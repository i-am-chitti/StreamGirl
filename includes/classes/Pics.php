<?php

class Pics {
    
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
            
            $sql = "SELECT * FROM pics WHERE id=:id";
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

    public function getPicPath() {
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

    public function getAlbumId() {
        return $this->sqlData["albumId"];
    }

    public function incrementViews() {
        $query = $this->con->prepare("UPDATE pics SET views=views+1 WHERE id=:id");
        $query->bindValue(":id", $this->getId());
        $query->execute();
    }

}

?>