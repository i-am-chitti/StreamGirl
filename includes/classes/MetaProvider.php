<?php 

class MetaProvider {

    public static function getMetaInfo($con, $type, $id) {

        $description = "";
        $title = "";

        if($type == -1) {
            $title = "Error";
            $description = "Error";
        }
        else if($type == 0) {
            //index page meta description
            $title = "Full HD, Previews, Full Video Length";
            $description = "Wide variety of videos, albums, stars";
        }
        else if($type >= 1 && $type <= 5) {
            //mutliple videos page meta description
            $videoTitles = "";
            $sql = "SELECT title FROM videos LIMIT 10";
            $query = $con->prepare($sql);
            $query->execute();
            while($videoTitle = $query->fetch(PDO::FETCH_COLUMN)) {
                $videoTitles .= $videoTitle." | ";
            }
            switch($type) {
                case 1:
                    $title="Latest videos, Full HD ";
                    
                break;
                case 2:
                    $title = "Most viewed videos, Full HD";
                break;
                case 3:
                    $title = "Most liked videos, Full HD";
                break;
                case 4:
                    $title = "Video Category: ";
                    $sql = "SELECT title FROM videocategories";
                    if($id != null) {
                        $sql .= " WHERE id=:id";
                    }
                    $sql .= " LIMIT 5";
                    $query = $con->prepare($sql);
                    if($id != null) $query->bindValue(":id", $id);
                    $query->execute();
                    while($categoryTitle = $query->fetch(PDO::FETCH_COLUMN)) {
                        $title .= $categoryTitle." | ";
                    }
                break;
                case 5:
                    $title = "Top Rated Videos ";
                break;
            }
            $description = $title.$videoTitles;
        }
        else if($type >= 6 && $type <= 10) {
            //multiple albums page meta description 
            $albumTitles = "";
            $sql = "SELECT title FROM albums LIMIT 10";
            $query = $con->prepare($sql);
            $query->execute();
            while($albumTitle = $query->fetch(PDO::FETCH_COLUMN)) {
                $albumTitles .= $albumTitle." | ";
            }
            switch($type) {
                case 6:
                    $title="Latest albums, Full HD ";
                break;
                case 7:
                    $title = "Most viewed albums, Full HD ";
                break;
                case 8:
                    $title = "Most liked albums, Full HD ";
                break;
                case 9:
                    $title = "Album Category: ";
                    $sql = "SELECT title FROM albumcategories";
                    if($id != null) {
                        $sql .= " WHERE id=:id";
                    }
                    $sql .= " LIMIT 10";
                    $query = $con->prepare($sql);
                    if($id != null) $query->bindValue(":id", $id);
                    $query->execute();
                    while($categoryTitle = $query->fetch(PDO::FETCH_COLUMN)) {
                        $title .= $categoryTitle." | ";
                    }
                break;
                case 10:
                    $title = "Top Rated Albums ";
                break;
            }
            $description = $title.$albumTitles;

        }
        else if($type == 11) {
            //star page  meta descrption
            $title = "Popular Star: ";
            $sql = "SELECT title FROM stars";
            if($id != null) {
                $sql .= " WHERE id=:id";
            }
            $sql .= " LIMIT 5";
            $query = $con->prepare($sql);
            if($id != null) $query->bindValue(":id", $id);
            $query->execute();
            while($starTitle = $query->fetch(PDO::FETCH_COLUMN)) {
                $title .= $starTitle." | ";
            }
            $description = $title;
        }
        else if($type == 12) {
            //watch video page description
            if($id != null) {
                $sql = "SELECT title, `description` FROM videos WHERE id=:id";
                $query = $con->prepare($sql);
                $query->bindValue(":id", $id);
                $query->execute();
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $title = $row["title"];
                $description = $row["title"]." || ".$row["description"];
            }
        }
        else if($type == 13) {
            //watch album page description
            if($id != null) {
                $sql = "SELECT title, `description` FROM albums WHERE id=:id";
                $query = $con->prepare($sql);
                $query->bindValue(":id", $id);
                $query->execute();
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $title = $row["title"];
                $description = $row["title"]." || ".$row["description"];
            }
        }
        else if($type == 14) {
            //category select page description
            $title = "Select A Category: Videos || Albums";
            $description = "Select a category to view wide variety of videos";
        }
        else if($type == 15) {
            //search page description
            $title = "Search: Videos|Albums|Category|Stars";
            $description = "Search Videos, albums, category, stars";
        }
        $metaInfo["title"] = $title;
        $metaInfo["description"] = $description;
        $metaInfo["keywords"] = "Full HD, Full length videos, videos by category";
        return $metaInfo;
    }

}

?>