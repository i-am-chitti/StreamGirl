<?php

class Provider {

    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function createEntityPreviewSquare($entity, $type) {

        //check if square is for video or pic
        $fileType = 1; //for videos
        if($type >= 6 && $type <= 10) {
            $fileType = 2; //for pics
        }

        $id = $entity->getId();
        $thumbnail = $entity->getThumbnail();
        $name = $entity->getName();
        $views = $entity->getViews();
        $likes = $entity->getLikes();
        $title = str_replace(" ", "-", $name);

        $previewHtml = "<div class='previewContainer small'>
                        <div class='previewContent'>
                            <img src='$thumbnail' alt='$name'>
                            <div class='iconContainer'>
                                <i class='fa fa-eye iconOnImage'> {$views}</i>
                                <i class='fa fa-heart iconOnImage'> {$likes}</i>
                            </div>
                        </div>";
        
        if(isset($_SESSION["contentUploader"]) && $_SESSION["contentUploader"] == true) {
            $previewHtml .= "<div style='padding: 20px 0;'>";
            
            if($fileType == 1) {
                $previewHtml .= "<a href='watchVideo.php?id=$id&title=$title' style='padding-left: 0; padding-right: 0;'>$name</a>";
            }
            else if($fileType == 2) {
                $previewHtml .= "<a href='watchPics.php?id=$id&title=$title' style='padding-left: 0; padding-right: 0;'>$name</a>";
            }

            $previewHtml .= "<a href='admin/deleteEntity.php?id={$id}&entityType={$fileType}' target='_blank' style='color: #fff; float: right; margin-right: 5px; padding: 0;'><i class='fa fa-trash'></i></a>
                            </div>";
        }
        else {
            if($fileType == 1) {
                $previewHtml .= "<a href='watchVideo.php?id=$id&title=$title'>
                                    $name
                                </a>";
            }
            else if($fileType == 2) {
                $previewHtml .= "<a href='watchPics.php?id=$id&title=$title'>
                                    $name
                                </a>";
            }
        }
        $previewHtml .= "</div>";
        return $previewHtml;
    }

    public function getPicsByAlbumId($albumId) {
        $picIds = PicsProvider::getPicsFromAlbum($this->con, $albumId);

        //cross check
        if(count($picIds) < 1) {
            header("Location: error?error=8");
            exit;
        }

        $picsInnerHtml = "";

        foreach($picIds as $picId) {

            $pic = new Pics($this->con, $picId);
            $pic->incrementViews();
            
            $picActionsHtml = $this->getPicActionsHtml($pic);

            $picsInnerHtml .= "<div class='swiper-slide'>
                                    <div class='picContainer'>
                                        <img src='{$pic->getPicPath()}' alt='{$pic->getName()}'>
                                        <h3 class='picDetails'>{$pic->getName()}</h3>
                                    </div>
                                    $picActionsHtml
                                    <div class='picDescription'>{$pic->getDescription()}</div>
                                </div>";
        }

        return $picsInnerHtml;
    }

    private function getPicActionsHtml($pic) {

        return "<div class='picAction'>
                    <div>
                        <i class='fa fa-eye' style='color: #fff;'></i>
                        <span>". $pic->getViews() ."</span>
                    </div>
                    <div>
                        <button onclick='changeLikes({$pic->getId()})'><i class='fa fa-heart' id='likeBtn".$pic->getId()."' style='color: #fff;'></i></button>
                        <span id='noOfLikes".$pic->getId()."'>". $pic->getLikes() ."</span>
                    </div>
                </div>";
    }

    public function createCategoryEntitySquare($categoryEntity, $type) {

        $categoryName = $categoryEntity->getName();
        $categoryTitle = str_replace(" ", "-", $categoryName);

        return "<div class='previewContainer small'>
                    <div class='previewContent'>
                        <img src={$categoryEntity->getThumbnail()} alt={$categoryName}>
                    </div>
                    <a href='view.php?view={$type}&id={$categoryEntity->getId()}&title={$categoryTitle}'>{$categoryName}</a>
                </div>";
    }

    public function createProfileDpSquare($starEntity, $type) {
        $starId = $starEntity->getId();
        $starName = $starEntity->getName();
        $starDp = $starEntity->getThumbnail();
        $starTitle = str_replace(" ", "-", $starName);

        return "<div class='previewContainer small'>
                    <div class='previewContent'>
                        <img src={$starDp} alt={$starName}>
                    </div>
                    <a href='view.php?view={$type}&id={$starId}&title={$starTitle}'>{$starName}</a>
                </div>";
    }
}

?>