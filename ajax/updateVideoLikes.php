<?php 

require_once("../includes/config.php");

if(isset($_POST["videoId"]) && isset($_POST["type"])) {

    $sql = "";
    if($_POST["type"] == 1) {
        $sql ="UPDATE videos SET likes=likes+1 WHERE id=:id";
    }
    else {
        $sql = "UPDATE videos SET likes=likes-1 WHERE id=:id";
    }

    $query = $con->prepare($sql);
    $query->bindValue(":id", $_POST["videoId"]);
    $query->execute();
    
}
else {
    echo "Something went wrong!";
}

?>