<?php

class StarProvider {

    public static function getTotalPageCount($con, $starId, $resultsPerPage) {
        
        $sql ="";

        if($starId != null) {
            $sql ="SELECT nVideos AS total FROM stars WHERE id=:starId";
        }
        else {
            $sql ="SELECT COUNT(id) AS total FROM stars";
        }
        
        $query = $con->prepare($sql);

        if($starId != null) {
            $query->bindValue(":starId", $starId);
        }

        $query->execute();
        
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $totalPages = ceil($row["total"] / $resultsPerPage);
        return $totalPages;
    }

    public static function getStars($con, $starId, $startFrom, $nResults) {
        $sql = "SELECT * FROM stars";

        if($starId != null) {
            $sql .= " WHERE id=:starId";
        }

        $sql .= " ORDER BY views DESC";

        if($startFrom == null) {
            $sql .= " LIMIT :numResults";
        }
        else {
            $sql .= " LIMIT :startFrom, :nResults";
        }

        $query = $con->prepare($sql);
        
        if($starId != null) {
            $query->bindValue(":starId", $starId);
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
            $result[] = new Star($con, $row);
        }

        return $result;
    }

    public static function getAllStars($con) {
        $sql = "SELECT * FROM stars";
        $query = $con->prepare($sql);
        $query->execute();

        $stars = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $stars[] = new Star($con, $row);
        }
        return $stars;
    }

    public static function addStarToDatabase($con, $starName, $starDp) {
        $sql = "INSERT INTO stars (title, thumbnail) VALUES(:starName, :starDp)";
        $query = $con->prepare($sql);
        $query->bindValue(":starName", $starName);
        $query->bindValue(":starDp", $starDp);
        $query->execute();
    }

    public static function getSearchStars($con, $term) {

        $sql = "SELECT * FROM stars ";

        $sql .= "WHERE title LIKE CONCAT('%', :term, '%') ORDER BY RAND() LIMIT 30";

        $query = $con->prepare($sql);
        $query->bindValue(":term", $term);
        $query->execute();

        $stars = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $stars[] = new Star($con, $row);
        }
        return $stars;

    }
}

?>