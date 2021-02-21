<?php 

require_once("../includes/config.php");
require_once("../includes/classes/Pics.php");

if(isset($_POST["picId"]) && isset($_POST["type"])) {
    
    $type = $_POST["type"];
    $pic = new Pics($con, $_POST["picId"]);
    $albumId = $pic->getAlbumId();

    if($type == 1) {

        $query = $con->prepare("UPDATE pics SET likes=likes+1 WHERE id=:picId");

        $query->bindValue(":picId", $_POST["picId"]);
        $query->execute();

        $query = $con->prepare("UPDATE albums SET likes=likes+1 WHERE id=:albumId");
        $query->bindValue(":albumId", $albumId);
        $query->execute();
        
    }

    else {

        $query = $con->prepare("UPDATE pics SET likes=likes-1 WHERE id=:picId");

        $query->bindValue(":picId", $_POST["picId"]);
        $query->execute();

        $query = $con->prepare("UPDATE albums SET likes=likes-1 WHERE id=:albumId");
        $query->bindValue(":albumId", $albumId);
        $query->execute();
        
        
    }
}
else {
    echo "Something went wrong!";
}

?>