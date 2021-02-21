<?php

class Star {
    
    private $con, $sqlData;

    //input could be data from dB(want to fetch all data from dB) or an entity id(already present somewhere in website and we want to create entity without details)
    public function __construct($con, $input) {

        $this->con = $con;
        
        //entity is an array already containing data
        if(is_array($input)) {
            $this->sqlData = $input;
        }
        // got entity id in input
        else {
            
            $sql = "SELECT * FROM stars";

            $sql .= " WHERE id=:id";
            $query = $this->con->prepare($sql);
            $query->bindValue(":id", $input);
            $query->execute();
            
            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
            
        }
    }

    public function getId() {
        return $this->sqlData["id"];
    }

    public function getName() {
        return $this->sqlData["title"];
    }

    public function getThumbnail() {
        return $this->sqlData["thumbnail"];
    }

    public function getViews() {
        return $this->sqlData["views"];
    }

    public function getLikes() {
        return $this->sqlData["likes"];
    }

    public function getNumOfVideos() {
        return $this->sqlData["nVideos"];
    }

    public function incrementViews() {
        $query = $this->con->prepare("UPDATE stars SET views=views+1 WHERE id=:id");
        $query->bindValue(":id", $this->getId());
        $query->execute();
    }

    public function incrementLikes() {
        $query = $this->con->prepare("UPDATE stars SET likes=likes+1 WHERE id=:id");
        $query->bindValue(":id", $this->getId());
        $query->execute();
    }

    public function incrementNumOfVideos() {
        $query = $this->con->prepare("UPDATE stars SET nVideos=nVideos+1 WHERE id=:id");
        $query->bindValue(":id", $this->getId());
        $query->execute();
    }

}


?>