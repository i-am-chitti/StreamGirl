<?php

class Category {
    
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
                $sql .= "videocategories";
            }
            else if($entityType == 2) {
                $sql .= "albumcategories";
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

    public function getName() {
        return $this->sqlData["title"];
    }

    public function getThumbnail() {
        return $this->sqlData["thumbnail"];
    }

    public function getNumOfEntities() {
        return $this->sqlData["nEntities"];
    }

}


?>