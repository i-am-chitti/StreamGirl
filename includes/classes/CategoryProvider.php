<?php

class CategoryProvider {
    
    public static function getCategory($con, $entityType, $isAll) { //isAll is for nEntities >0 or not
        $sql = "SELECT * FROM ";

        if($entityType == 1) { //video categories
            $sql .= "videocategories";
        }
        else if($entityType == 2) { //album categories
            $sql .= "albumcategories";
        }

        if(!$isAll) {
            $sql .= " WHERE nEntities > 0";
        }

        $query = $con->prepare($sql);
        $query->execute();

        $categories = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($con, $row, $entityType);
        }

        return $categories;
    }

    public static function getSearchCategories($con, $term, $entityType) {

        $sql = "SELECT * FROM ";

        if($entityType == 1) {
            $sql .= "videocategories ";
        } 
        else if($entityType == 2) {
            $sql .= "albumcategories ";
        }

        $sql .= "WHERE title LIKE CONCAT('%', :term, '%') ORDER BY RAND() LIMIT 10";

        $query = $con->prepare($sql);
        $query->bindValue(":term", $term);
        $query->execute();

        $categories = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($con, $row, $entityType);
        }
        return $categories;

    }

    public static function addCategoryToDatabase($con, $categoryTitle, $thumbnailPath, $entityType) {
            $sql = "INSERT INTO ";
            if($entityType ==1) {
                $sql .= "videocategories";
            }
            else if($entityType == 2) {
                $sql .= "albumcategories";
            }

            $sql .= " (title, thumbnail) VALUES (:categoryTitle, :categoryThumbnailPath)";
            $query = $con->prepare($sql);
            $query->bindValue(":categoryTitle", $categoryTitle);
            $query->bindValue(":categoryThumbnailPath", $thumbnailPath);
            $query->execute();
    }
}

?>