<?php

class Entity {
    
    private $con, $sqlData;

    //input could be data from dB(want to fetch all data from dB) or an entity id(already present somewhere in website and we want to create entity without details)
    public function __construct($con, $input, $entityType) {

        $this->con = $con;
        
        //entity is an array already containing data
        if(is_array($input)) {
            $this->sqlData = $input;
        }
        // got entity id in input
        else {
            
            $sql = "SELECT * FROM ";

            if($entityType == 1) {
                $sql .= "videos";
            }
            else if($entityType == 2) {
                $sql .= "albums";
            }

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

    public function getDescription() {
        return $this->sqlData["description"];
    }

    public function getName() {
        return $this->sqlData["title"];
    }

    public function getThumbnail() {
        return $this->sqlData["thumbnail"];
    }

    public function getPreview() {
        return $this->sqlData["previewPath"];
    }
    
    public function getFullVideo() {
        return $this->sqlData["filePath"];
    }

    public function getCategoryId() {
        return $this->sqlData["categoryId"];
    }

    public function getViews() {
        return $this->sqlData["views"];
    }

    public function getLikes() {
        return $this->sqlData["likes"];
    }

    public function getStarId() {
        return $this->sqlData["starId"];
    }

    public function incrementViews() {
        //increment video view
        $query = $this->con->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
        $query->bindValue(":id", $this->getId());
        $query->execute();


        $starId = $this->getStarId();
        if($starId != 0) {
            $query = $this->con->prepare("UPDATE stars SET views=views+1 WHERE id=:starId");
            $query->bindValue(":starId", $this->getStarId());
            $query->execute();
        }

    }

}


?>