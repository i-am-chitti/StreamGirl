<?php

require_once("./includeClasses.php");

$metaInfo = MetaProvider::getMetaInfo($con, 14, null);  //index page
$title = $metaInfo["title"];
$metaDescription = $metaInfo["description"];
$metaKeywords = $metaInfo["keywords"];

require_once("./includes/header.php");

$container = new Container($con, null, null, null);

echo $container->showCategoryCards();

require_once("./includes/footer.php");

?>