<?php

if(!isset($_GET["entity"])) {
    header("Location: entitySelect.php");
    exit;
}

$type = 0; // video category or album category

if($_GET["entity"] == "vid") {
    $type = 4;    
}
else if($_GET["entity"] == "album") {
    $type = 9;
}
else {
    header("Location: entitySelect.php");
    exit;
}

require_once("./includeClasses.php");

$metaInfo = MetaProvider::getMetaInfo($con, $type, null);  //index page
$title = $metaInfo["title"];
$metaDescription = $metaInfo["description"];
$metaKeywords = $metaInfo["keywords"];

require_once("./includes/header.php");

//IInd parameter = type  = null since all in one page. so no need to call EntityProvider::getTotalPageCount()
//IIIrd parameter = categoryId = null same all previous
$category = new Container($con, null, null, null);
echo $category->showAllCategories($type);

require_once("./includes/footer.php");

?>