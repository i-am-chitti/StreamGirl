<?php

class Container {
    
    private $con, $numOfPages, $currentPage;

    //here $type ranges (1-11) for vids or albums and $filterId would be null except for $type=4
    public function __construct($con, $type, $filterId, $resultsPerPage, $currPage=1) {
        $this->con = $con;

        if($resultsPerPage != null) {
            if($type == 11) { //get stars page count
                $this->numOfPages = StarProvider::getTotalPageCount($con, $filterId, $resultsPerPage);
            }
            else {
                $this->numOfPages = EntityProvider::getTotalPageCount($con, $type, $filterId, $resultsPerPage);
            }
        }
        else {
            $this->numOfPages = 1;
        }

        $this->currentPage = $currPage;
    }

    public function showAll() {
        $html = "";
        $html = $this->getVideoHtml(); //get sliding videos filtered by latest, most viewed, liked

        //remove below if not to show slidable album categories
        $html .= $this->showAllCategories(9); //showing slidable album categories

        //show slidable stars
        $html .= $this->showStars(null, 10); //15 popular stars to be shown

        return $html;
    }

    public function getVideoHtml() {
        $allHtml = "";
        //get slidable videos
        $allHtml = "<div class='previewCategories'>
                        <h3 class='entityText'><i class='fa fa-video-camera' style='font-size: 50px;'></i><span style='padding: 0 20px;'>Videos</span></h3>";

        //creating limited latest videos release
        $latestVideos = EntityProvider::getEntities($this->con, 1, null, 10); //1 for latest videos entities
        if(count($latestVideos) != 0) {
            $allHtml .= $this->createSquare($latestVideos, 1);
        }

        //creating limited most viewed videos
        $mostViewedVideos = EntityProvider::getEntities($this->con, 2, null, 10);  //2 for most viewed videos
        if(count($mostViewedVideos) != 0) {
            $allHtml .= $this->createSquare($mostViewedVideos, 2);
        }

        //creating limited most liked videos
        $mostLikedVideos = EntityProvider::getEntities($this->con, 3, null, 10); //3 for most liked videos
        if(count($mostLikedVideos) != 0) {
            $allHtml .= $this->createSquare($mostLikedVideos, 3);
        }

        //creating top rated videos
        $topRatedVideos = EntityProvider::getEntities($this->con, 5, null, 10); //top rated content videos
        if(count($topRatedVideos) != 0) {
            $allHtml .= $this->createSquare($topRatedVideos, 5);
        }

        // 6 for latest albums
        //$latestAlbums = EntityProvider::getEntities($this->con, 6, null, 10);
        //$allHtml .= $this->createSquare($latestAlbums, 6);

        // 7 for most viewed albums
        //$mostViewedAlbums = EntityProvider::getEntities($this->con, 7, null, 10);
        //$allHtml .= $this->createSquare($mostViewedAlbums, 7);

        // 8 for most liked albums
        //$mostLikedAlbums = EntityProvider::getEntities($this->con, 8, null, 10);
        //$allHtml .= $this->createSquare($mostLikedAlbums, 8);

        // 10 for top rated albums
        //$topRatedAlbums = EntityProvider::getEntities($this->con, 10, null, 10);
        //$allHtml .= $this->createSquare($topRatedAlbums, 10);

        return $allHtml.
                    "</div>";
    }

    public function createSquare($entities, $type) {
        $title ="";
        if($type == 1) {
            $title = "Latest Release Videos";
        }
        else if($type == 2) {
            $title = "Most Viewed Videos";
        }
        else if($type == 3) {
            $title = "Most Liked Videos";
        }
        else if($type == 5) {
            $title = "Top Rated Videos";
        }
        else if($type == 6) {
            $title = "Latest Albums";
        }
        else if($type == 7) {
            $title = "Most Viewed Albums";
        }
        else if($type == 8) {
            $title = "Most Liked Albums";
        }
        else if($type == 10) {
            $title = "Top Rated Albums";
        }
        else if($type == 11) {
            $title = "Stars";
        }

        $entitiesHtml = "";

        $previewProvider = new Provider($this->con);

        foreach($entities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity, $type);
        }

        $innerHtml = "<div class='category'>
                    <a href='view.php?view={$type}'>
                        <h3>$title</h3>
                    </a>

                    <div class='entities'>
                        $entitiesHtml
                    </div>

                </div>";
        
        $pageNavHtml = $this->getPageNavHtml($type);

        return $innerHtml.$pageNavHtml;
    }

    private function getPageNavHtml($type, $categoryId=null, $title=null) {

        $pageNavHtml = "";
        if($this->numOfPages > 1) {
            $pageNavHtml .= "<div class='pageNav'>";
            if($this->currentPage > 1) {
                $previousPage = $this->currentPage - 1;
                $pageNavHtml .= "<a href='view.php?view={$type}";
                if($categoryId != null) {
                    $pageNavHtml .= "&id={$categoryId}&title={$title}";
                }
                $pageNavHtml .= "&page={$previousPage}' class='goToPage'>
                <i class='fa fa-arrow-left'></i>
                </a>";
            }

            $pageNavHtml .= "<span>{$this->currentPage}</span>";

            if($this->currentPage < $this->numOfPages) {
                $nextPage = $this->currentPage + 1;
                $pageNavHtml .= "<a href='view.php?view={$type}";
                if($categoryId != null) {
                    $pageNavHtml .= "&id={$categoryId}&title={$title}";
                }
                $pageNavHtml .= "&page={$nextPage}' class='goToPage'>
                <i class='fa fa-arrow-right'></i>
                </a>";
            }
            $pageNavHtml .= "</div>";
        }

        return $pageNavHtml;
    }

    public function showByFilter($type, $startFrom, $nResults) {
        $html = "<div class='previewCategories noScroll'>";
        $html .= $this->getHtmlByFilter($type, $startFrom, $nResults);
        return $html."</div>";
    }

    public function getHtmlByFilter($type, $startFrom, $nResults) {
        $allHtml = "";
        $entities = EntityProvider::getEntities($this->con, $type, $startFrom, $nResults);
        $allHtml = $this->createSquare($entities, $type);
        return $allHtml;
    }

    public function showAllCategories($type) {
        
        $sql = "SELECT * FROM ";
        if($type == 4) {
            $sql .= "videocategories";
        }
        else if($type == 9) {
            $sql .= "albumcategories";
        }
        $query = $this->con->prepare($sql);
        $query->execute();

        $html = "<div class='previewCategories'>";

        if($type == 4) {
        $html .= "<h3 class='entityText'><i class='fa fa-video-camera' style='font-size: 50px;'></i><span style='padding: 0 20px;'>Videos</span></h3>";
        }
        else if($type == 9) {
            $html .= "<h3 class='entityText'><i class='fa fa-picture-o' style='font-size: 50px;'></i><span style='padding: 0 20px;'>Albums</span></h3>";
        }

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html.=$this->getCategoryHtml($row, null, $type, null, 20);   
        }

        return $html."</div>";
    }

    public function showCategory($type, $categoryId, $startFrom, $nResults, $title=null) {

        $categoryRecomedation = 0;
        if($title == "You might also like") {
            $categoryRecomedation = 1;
        }

        if($categoryRecomedation) {
            $sql = "SELECT MAX(id) FROM ";
            if($type == 4) {
                $sql .= "videocategories";
            }
            else if($type == 9) {
                $sql .= "albumcategories";
            }
            $query = $this->con->prepare($sql);
            $query->execute();
            $maxCategoryId = $query->fetch(PDO::FETCH_COLUMN);

            if($categoryId == $maxCategoryId) {
                $categoryId = $maxCategoryId - 1;
            }
        }

        $sql = "SELECT * FROM ";
        if($type == 4) {
            $sql .= "videocategories";
        }
        else if($type == 9) {
            $sql .= "albumcategories";
        }
        $sql .= " WHERE id=:id";

        $query = $this->con->prepare($sql);

        $query->bindValue(":id", $categoryId);
        
        $query->execute();

        if($query->rowCount() == 0) {
            return;
        }

        

        $html = "<div class='previewCategories";

        if($categoryRecomedation){
            $html .= "' style='padding: 20px;'>";
        }
        else {
            $html .= " noScroll'>";
        }

        $row = $query->fetch(PDO::FETCH_ASSOC);
        if($row["nEntities"] == 0 && !$categoryRecomedation) {
            if($type == 4) {
                header("Location: errorPage.php?error=18");
                exit;
            }
            else if($type == 9) {
                header("Location: errorPage.php?error=7");
                exit;
            }
        }
        $html .= $this->getCategoryHtml($row, $title, $type, $startFrom, $nResults, $categoryRecomedation);  
        

        return $html.
                "</div>";
    }

    private function getCategoryHtml($sqlData, $title, $type, $startFrom, $nResults, $categoryRecomedation=0) {
        
        $categoryId = $sqlData["id"];
        $title = $title == null ? $sqlData["title"] : $title;

        $categoryEntities = EntityProvider::getCategoryEntities($this->con, $type, $categoryId, $startFrom, $nResults);

        if(sizeof($categoryEntities) == 0) {
            return;
        }
        
        $entitiesHtml = "";

        $previewProvider = new Provider($this->con);

        foreach ($categoryEntities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity, $type); 
        }

        $categoryTitle = str_replace(" ", "-", $title);

        $innerHtml = "<div class='category'>";
        if($categoryRecomedation) {
            $innerHtml .= "<h3 style='padding: 10px; color: #fff;'>$title</h3>";
        }
        else $innerHtml .= "<a href='view.php?view={$type}&id=$categoryId&title=$categoryTitle'>
                                <h3>$title</h3>
                            </a>";

        $innerHtml .= "<div class='entities'>
                            $entitiesHtml
                        </div>
                    </div>";

        if($categoryRecomedation) {
            return $innerHtml;
        }
        else {

            $pageNavHtml = $this->getPageNavHtml($type, $categoryId, $categoryTitle);

            return $innerHtml.$pageNavHtml;
        }
    }

    public function showCategoryCards() {
        $albumContainerHtml = $this->getAlbumCardHtml();
        $videoContainerHtml = $this->getVideosCardHtml();

        return "<div class='previewCategories'>
                    $videoContainerHtml
                    $albumContainerHtml
                </div>";
    }

    private function getAlbumCardHtml() {
        $albumObjs = CategoryProvider::getCategory($this->con, 2, null); //entityType for albums and null for nEntities>0
        
        if(sizeof($albumObjs) != 0) {
            $albumContainerHtml = "<div class='category'>
                                        <a href='categories.php?entity=album' style='text-align: center; padding: 30px;'>
                                            <i class='fa fa-picture-o' style='font-size: 50px;'></i>
                                            <h3 style='font-size: 20px;'>Albums Categories</h3>
                                        </a>
                                    <div class='entities'>";
            
            $provider = new Provider($this->con);

            foreach($albumObjs as $albumObj) {
                $albumContainerHtml .= $provider->createCategoryEntitySquare($albumObj, 9); //type for album category
            }

            return $albumContainerHtml."</div>
                                            </div>";
        }
        else return;
    }

    private function getVideosCardHtml() {
        $videoObjs = CategoryProvider::getCategory($this->con, 1, null); //entityType for videos and null for nEntities>0
        
        if(sizeof($videoObjs) != 0) {
            $videoContainerHtml = "<div class='category'>
                                        <a href='categories.php?entity=vid' style='text-align: center; padding: 30px;'>
                                            <i class='fa fa-video-camera' style='font-size: 50px;'></i>
                                            <h3 style='font-size: 20px;'>Videos Categories</h3>
                                        </a>
                                    <div class='entities'>";
            
            $provider = new Provider($this->con);

            foreach($videoObjs as $albumObj) {
                $videoContainerHtml .= $provider->createCategoryEntitySquare($albumObj, 4); //type for video category
            }

            return $videoContainerHtml."</div>
                                            </div>";
        }
        else return;
    }

    public function showStars($startFrom, $nResults, $isScrollable=1) {
        $html = "<div class='previewCategories";
        if($isScrollable) {
            $html .= "'>";
        }
        else {
            $html .= " noScroll'>";
        }
        
        $html .= "<h3 class='entityText'><i class='fa fa-female' style='font-size: 50px;'></i><span style='padding: 0 20px;'>Stars</span></h3>";
        
        $stars = StarProvider::getStars($this->con, null, $startFrom, $nResults); //get all stars

        $starsHtml = "";

        if(sizeof($stars) == 0) {
            return;
        }

        $provider = new Provider($this->con);

        foreach($stars as $star) {
            $starsHtml .= $provider->createProfileDpSquare($star, 11);
        }

        $html .= "<div class='category'>
                    <a href='view.php?view=11'><h3>Popular Stars</h3></a>
                    <div class='entities'>
                        $starsHtml
                    </div>
                </div>";

        $pageNavHtml = $this->getPageNavHtml(11);

        return $html.$pageNavHtml."</div>";
    }

    public function showStarVideos($starId, $startFrom, $resultsPerPage) {

        $star = new Star($this->con, $starId);

        $starVideosHtml = "";

        $provider = new Provider($this->con);

        $starVideos = EntityProvider::getVideosByStar($this->con, $starId, $startFrom, $resultsPerPage);

        if(sizeof($starVideos) == 0) {
            //redirect to error page since either there are no videos or page number exceeded
            header("Location: /StreamGirl/errorPage.php?error=13");
            exit;
        }

        foreach($starVideos as $starVideo) {
            $starVideosHtml .= $provider->createEntityPreviewSquare($starVideo, 1); //II parameter is for videos type, can be 1-5
        }

        $allHtml = "<div class='previewCategories noScroll'>
                        <h3 class='entityText'><i class='fa fa-video-camera' style='font-size: 50px;'></i><span style='padding: 0 20px;'>{$star->getName()} Videos</span></h3>
                        <div class='category'>
                            <div class='entities'>
                                $starVideosHtml
                            </div>
                        </div>
                    </div>";

        $pageNavHtml = $this->getPageNavHtml(11, $starId);

        return $allHtml.$pageNavHtml;
    }

}

?>