<?php

if(!isset($_GET["view"]) || $_GET["view"]>11) {
    header("Location: index.php");
    exit;
}

require_once("./includeClasses.php");

$metaInfo = null;
if(!isset($_GET["id"])) {
    $metaInfo = MetaProvider::getMetaInfo($con, $_GET["view"], null);
}
else {
    $metaInfo = MetaProvider::getMetaInfo($con, $_GET["view"], $_GET["id"]);
}

$title = $metaInfo["title"];
$metaDescription = $metaInfo["description"];
$metaKeywords = $metaInfo["keywords"];

require_once("./includes/header.php");

$currPage = 1;

if(isset($_GET["page"])) {
    $currPage = $_GET["page"];
}

$resultsPerPage = 20;

$startFrom = ($currPage - 1) * $resultsPerPage; 

$container = null;

$type = $_GET["view"];

if($type == "4" || $type == "9") {
    $container = new Container($con, $type, $_GET["id"], $resultsPerPage, $currPage);
    echo $container->showCategory($type, $_GET["id"] ,$startFrom, $resultsPerPage);
    echo $container->showCategory($type, $_GET["id"]+1, null, 10, "You might also like");
}
else if($type == "11") {

    if(isset($_GET["id"])) {
        $starId = $_GET["id"];
        $container = new Container($con, $type, $starId, $resultsPerPage, $currPage);
        echo $container->showStarVideos($starId, $startFrom, $resultsPerPage); // last parameter is for isScrollable 1=scrollable
    }
    else {
        $starId = null;
        $container = new Container($con, $type, $starId, $resultsPerPage, $currPage);
        echo $container->showStars($startFrom, $resultsPerPage, 0); // last parameter is for isScrollable
    }

}
else {
    $container = new Container($con, $type, null, $resultsPerPage, $currPage);
    echo $container->showByFilter($_GET["view"], $startFrom, $resultsPerPage);
}

require_once("./includes/footer.php");
?>