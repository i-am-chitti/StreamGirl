<?php

class SearchResultsProvider {

    private $con;

    public function __construct($con) {

        $this->con = $con;
        
    }

    public function getResults($inputText) {

        $videoEntities = EntityProvider::getSearchEntities($this->con, $inputText, 1);
        $albumEntities = EntityProvider::getSearchEntities($this->con, $inputText, 2);

        $videoCategories = CategoryProvider::getSearchCategories($this->con, $inputText, 1);
        $albumCategories = CategoryProvider::getSearchCategories($this->con, $inputText, 2);

        $starEntities = StarProvider::getSearchStars($this->con, $inputText);

        $html = "<div class='previewCategories noScroll'>";

        $html .= $this->getResultHtml($videoEntities, 5);  //type can be any between 1 to 5

        $html .= $this->getResultHtml($albumEntities, 10);  //type can be any between 6 to 10

        $html .= $this->getResultHtml($videoCategories, 4);  //video categories type

        $html .= $this->getResultHtml($albumCategories, 9);  //album categories type

        $html .= $this->getResultHtml($starEntities, 11); //type for stars

        $html .= "</div>";

        return $html;

    }

    private function getResultHtml($entities, $type) {
        if(sizeof($entities) == 0) {
            return;
        }

        $title = "<div class='searchResultTitle'>";
        
        $entitiesHtml = "";

        $provider = new Provider($this->con);

        if($type == 5 || $type == 10) { 

            if($type == 5) {
                $title .= "<i class='fa fa-video-camera' style='font-size: 50px;'></i><br>Videos";
            }
            else {
                $title .= "<i class='fa fa-picture-o' style='font-size: 50px;'></i><br>Albums";
            } 

            foreach ($entities as $entity) {
                $entitiesHtml .= $provider->createEntityPreviewSquare($entity, $type);
            }
        }
        else if($type == 4 || $type == 9) { //4 for video categories and 9 for album categories

            if($type == 4) {
                $title .= "<i class='fa fa-video-camera' style='font-size: 50px;'></i><br>Videos Categories";
            }
            else {
                $title .= "<i class='fa fa-picture-o' style='font-size: 50px;'></i><br>Album Categories";
            }

            foreach ($entities as $entity) {
                $entitiesHtml .= $provider->createCategoryEntitySquare($entity, $type);
            }
        }
        else if($type == 11) {
            $title .= "<i class='fa fa-female' style='font-size: 50px;'></i><br>Stars";
            foreach($entities as $entity) {
                $entitiesHtml .= $provider->createProfileDpSquare($entity, $type);
            }
        }

        $title .= "</div>";

        return "<div class='category'>
                    $title
                    <div class='entities'>
                        $entitiesHtml
                    </div>

                </div>";
    }

}

?>