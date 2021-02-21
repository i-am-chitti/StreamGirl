<?php

require_once("./includeClasses.php");

$metaInfo = MetaProvider::getMetaInfo($con, -1, null);  //index page
$title = $metaInfo["title"];
$metaDescription = $metaInfo["description"];
$metaKeywords = $metaInfo["keywords"];

require_once("./includes/header.php");

if(isset($_GET["error"])) {
    echo ErrorMessage::show($_GET["error"]);
}
else {
    echo ErrorMessage::show();
}

$views = new Container($con, null, null, null);
echo $views->showCategoryCards();

require_once("./includes/footer.php");

?>