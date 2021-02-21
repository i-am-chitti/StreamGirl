<?php

require_once("./includeClasses.php");

$metaInfo = MetaProvider::getMetaInfo($con, 0, null);  //index page
$title = $metaInfo["title"];
$metaDescription = $metaInfo["description"];
$metaKeywords = $metaInfo["keywords"];

require_once("./includes/header.php");

$views = new Container($con, null, null, null);

echo $views->showAll();

echo $views->showCategoryCards();

require_once("./includes/footer.php");

?>