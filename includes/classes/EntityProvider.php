<?php

class EntityProvider {

    public static function getEntities($con, $type, $startFrom, $nResults) {

        $sql = "";
        $entityType = null; //either videos or album
        if($type >= 1 && $type <= 5) {
            $sql = "SELECT * FROM videos ";
            $entityType = 1;
        }
        else if($type >= 6 && $type <= 10) {
            $sql = "SELECT * FROM albums ";
            $entityType = 2;
        }
        
        if($type == 1 || $type == 6) $sql .= "ORDER BY uploadDate DESC"; //latest entities
        else if($type == 2 || $type == 7) $sql .= "ORDER BY views DESC"; //most viewed entities
        else if($type == 3 || $type == 8) $sql .= "ORDER BY likes DESC"; //most liked entities
        else if($type == 5 || $type == 10) $sql .= "WHERE isTopRated=1 ORDER BY views DESC"; //top rated entities
        else return;

        if($startFrom == null) {
            $sql .= " LIMIT :numResults";
        }
        else {
            $sql .= " LIMIT :startFrom, :nResults";
        }

        $query = $con->prepare($sql);

        if($startFrom == null) {
            $query->bindValue(":numResults", $nResults, PDO::PARAM_INT);
        }
        else {
            $query->bindValue(":startFrom", $startFrom, PDO::PARAM_INT);
            $query->bindValue(":nResults", $nResults, PDO::PARAM_INT);
        }
        $query->execute();

        $result = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Entity($con, $row, $entityType);
        }

        return $result;
    }

    public static function getCategoryEntities($con, $type, $categoryId, $startFrom, $nResults) {

        $sql = "";

        $entityType = null; //1 for videos and 2 for albums
        if($type == 4) {
            $sql = "SELECT * FROM videos ";
            $entityType = 1;
        }
        else if($type == 9) {
            $sql = "SELECT * FROM albums ";
            $entityType = 2;
        }        

        if($categoryId != null) {
            $sql .= "WHERE categoryId=:categoryId ";
        }

        $sql .= "ORDER BY views DESC ";

        if($startFrom == null) {
            $sql .= "LIMIT :numResults";
        }
        else {
            $sql .= "LIMIT :startFrom, :nResults";
        }

        $query = $con->prepare($sql);

        if($categoryId != null) {
            $query->bindValue(":categoryId", $categoryId);
        }

        if($startFrom == null) {
            $query->bindValue(":numResults", $nResults, PDO::PARAM_INT);
        }
        else {
            $query->bindValue(":startFrom", $startFrom, PDO::PARAM_INT);
            $query->bindValue(":nResults", $nResults, PDO::PARAM_INT);
        }

        $query->execute();

        $result = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Entity($con, $row, $entityType);
        }

        return $result;

    }

    public static function getTotalPageCount($con, $type, $categoryId, $resultsPerPage) {
        
        $sql ="SELECT COUNT(id) AS total FROM ";
        
        $videoOrAlbum = null; //1 for videos and 2 for albums
        if($type >= 1 && $type <= 5) {
            $videoOrAlbum = 1;
        }
        else if($type >= 6 && $type <= 10) {
            $videoOrAlbum = 2;
        }

        if($videoOrAlbum == 1) {
            //videos count
            $sql .= "videos";
        }
        else if($videoOrAlbum == 2) {
            $sql .= "albums";
        }

        if(($type == 4 || $type == 9) && $categoryId != null) {
            $sql .= " WHERE categoryId=:categoryId";
        }
        else if(($type == 5 || $type ==10) && $categoryId == null) {
            //top rated page count
            $sql .= " WHERE isTopRated=1";
        }

        $query = $con->prepare($sql);

        if($categoryId != null) {
            $query->bindValue(":categoryId", $categoryId);
        }

        $query->execute();
        
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $totalPages = ceil($row["total"] / $resultsPerPage);
        return $totalPages;
    }

    public static function addVideoToDatabase($con, $previewFilePath, $fullFilePath, $thumbnailFilePath, $categoryId, $videoTitle, $isTopRated, $videoDescription, $starId) {

        $sql = "INSERT INTO videos (title, previewPath, filePath, categoryId, thumbnail, isTopRated, `description`, starId) VALUES(:title, :previewPath, :filePath, :categoryId, :thumbnailPath, :isTopRated, :videoDescription, :starId)";
        $query = $con->prepare($sql);
        $query->bindValue(":title", $videoTitle);
        $query->bindValue(":previewPath", $previewFilePath);
        $query->bindValue(":filePath", $fullFilePath);
        $query->bindValue(":categoryId", $categoryId);
        $query->bindValue(":thumbnailPath", $thumbnailFilePath);
        $query->bindValue(":isTopRated", $isTopRated);
        $query->bindValue(":videoDescription", $videoDescription);
        $query->bindValue(":starId", $starId);

        $query->execute();

        $sql =  "UPDATE videocategories SET nEntities=nEntities+1 WHERE id=:categoryId";
        $query = $con->prepare($sql);
        $query->bindValue(":categoryId", $categoryId);
        $query->execute();

        $sql = "UPDATE stars SET nVideos=nVideos+1 WHERE id=:starId";
        $query = $con->prepare($sql);
        $query->bindValue(":starId", $starId);
        $query->execute();
    }

    public static function deleteVideoFromDatabase($con, $id) {

        $sql = "SELECT categoryId, starId FROM videos WHERE id=:id ";
        $query = $con->prepare($sql);
        $query->bindValue(":id", $id);
        $query->execute();

        $videoRow = $query->fetch(PDO::FETCH_ASSOC);
        $starId = $videoRow["starId"];
        $categoryId = $videoRow["categoryId"];

        $sql = "DELETE FROM videos WHERE id=:id";
        $query = $con->prepare($sql);
        $query->bindValue(":id", $id);
        $query->execute();

        if($starId != 0) {
            $sql = "UPDATE stars SET nVideos=nVideos-1 WHERE id=:starId";
            $query = $con->prepare($sql);
            $query->bindValue(":starId", $starId);
            $query->execute();
        }

        $sql = "UPDATE videocategories SET nEntities=nEntities-1 WHERE id=:categoryId";
        $query = $con->prepare($sql);
        $query->bindValue(":categoryId", $categoryId);
        $query->execute();

    }

    public static function deleteAlbumFromDatabase($con, $albumId) {
        $sql =  "SELECT categoryId FROM albums WHERE id=:id";
        $query = $con->prepare($sql);
        $query->bindValue(":id", $albumId);
        $query->execute();

        $categoryId = $query->fetch(PDO::FETCH_COLUMN);

        $sql = "DELETE FROM albums WHERE id=:id";
        $query = $con->prepare($sql);
        $query->bindValue(":id", $albumId);
        $query->execute();

        $sql = "UPDATE albumcategories SET nEntities=nEntities-1 WHERE id=:categoryId";
        $query = $con->prepare($sql);
        $query->bindValue(":categoryId", $categoryId);
        $query->execute();
    }

    public static function addAlbumToDatabase($con, $albumTitle, $albumDescription, $albumThumbnail, $albumCategory, $isTopRated) {

        $sql = "INSERT INTO albums (title, `description`, thumbnail, categoryId, isTopRated) VALUES(:title, :description, :thumbnail, :categoryId, :isTopRated)";
        $query = $con->prepare($sql);
        $query->bindValue(":title",$albumTitle);
        $query->bindValue(":description",$albumDescription);
        $query->bindValue(":thumbnail",$albumThumbnail);
        $query->bindValue(":categoryId",$albumCategory);
        $query->bindValue(":isTopRated",$isTopRated);
        $query->execute();

        $sql =  "UPDATE albumcategories SET nEntities=nEntities+1 WHERE id=:categoryId";
        $query = $con->prepare($sql);
        $query->bindValue(":categoryId", $albumCategory);
        $query->execute();

    }

    public static function addPicToDatabase($con, $picTitle, $picDescription, $picPath, $picCategory, $albumId) {

        $sql = "INSERT INTO pics (title, description, categoryId, filePath, albumId) VALUES(:title, :description, :categoryId, :filePath, :albumId)";

        $query = $con->prepare($sql);
        $query->bindValue(":title", $picTitle);
        $query->bindValue(":description", $picDescription);
        $query->bindValue(":categoryId", $picCategory);
        $query->bindValue(":filePath", $picPath);
        $query->bindValue(":albumId", $albumId);

        $query->execute();

    }

    public static function getSearchEntities($con, $term, $entityType) {

        $sql = "SELECT * FROM ";

        if($entityType == 1) {
            $sql .= "videos ";
        } 
        else if($entityType == 2) {
            $sql .= "albums ";
        }

        $sql .= "WHERE title LIKE CONCAT('%', :term, '%') ORDER BY RAND() LIMIT 30";

        $query = $con->prepare($sql);
        $query->bindValue(":term", $term);
        $query->execute();

        $entities = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $entities[] = new Entity($con, $row, $entityType);
        }
        return $entities;

    }

    public static function getVideosByStar($con, $starId, $startFrom, $nResults) {

        $sql = "SELECT * FROM videos WHERE starId=:starId ORDER BY views, likes DESC";

        if($startFrom == null) {
            $sql .= " LIMIT :numResults";
        }
        else {
            $sql .= " LIMIT :startFrom, :nResults";
        }

        $query = $con->prepare($sql);

        $query->bindValue(":starId", $starId);

        if($startFrom == null) {
            $query->bindValue(":numResults", $nResults, PDO::PARAM_INT);
        }
        else {
            $query->bindValue(":startFrom", $startFrom, PDO::PARAM_INT);
            $query->bindValue(":nResults", $nResults, PDO::PARAM_INT);
        }

        $query->execute();

        $starVideos = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $starVideos[] = new Entity($con, $row, 1); //1 for videos of a star
        }
        return $starVideos;

    }

    public static function doesVideoExist($con, $videoTitle, $previewVideo, $fullVideo, $videoDescription) {

        $baseSql = "SELECT id FROM videos ";

        $sql = $baseSql."WHERE title=:videoTitle";
        $query = $con->prepare($sql);
        $query->bindValue(":videoTitle", $videoTitle);
        $query->execute();

        if($query->rowCount() != 0) return 1; //video with same title exists

        $sql = $baseSql."WHERE previewPath=:previewPath";
        $query = $con->prepare($sql);
        $query->bindValue(":previewPath", $previewVideo);
        $query->execute();

        if($query->rowCount() != 0) return 2; //duplicate preview video link detected

        $sql = $baseSql."WHERE filePath=:filePath";
        $query = $con->prepare($sql);
        $query->bindValue(":filePath", $fullVideo);
        $query->execute();

        if($query->rowCount() != 0) return 3; //duplicate full video link detected

        $sql = $baseSql."WHERE description=:videoDescription";
        $query = $con->prepare($sql);
        $query->bindValue(":videoDescription", $videoDescription);
        $query->execute();

        if($query->rowCount() != 0) return 4; //duplicate video description detected

    }

}

?>