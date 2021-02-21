<?php

class PicsProvider {

    public static function getPicsFromAlbum($con, $albumId) {

        $sql = "UPDATE albums SET views=views+1 WHERE id=:albumId";
        $query = $con->prepare($sql);
        $query->bindValue(":albumId", $albumId);
        $query->execute();

        $sql = "SELECT id FROM pics WHERE albumId=:albumId ORDER BY RAND()";
        $query = $con->prepare($sql);
        $query->bindValue(":albumId", $albumId);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function deletePicFromDatabase($con, $picId) {

        $sql = "DELETE FROM pics WHERE id=:picId";
        $query = $con->prepare($sql);
        $query->bindValue(":picId", $picId);
        $query->execute();
        
    }
}

?>