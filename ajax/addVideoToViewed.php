<?php 

require_once("../includes/config.php");

if(isset($_POST["videoId"])) {

    $query = $con->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
    $query->bindValue(":id", $_POST["videoId"]);
    $query->execute();
    
}
else {
    header("Location: ../error");
    exit;
}

?>