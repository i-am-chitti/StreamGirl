<?php

if(!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

require_once("./includeClasses.php");

$albumEntity = new Entity($con, $_GET["id"], 2); //2 for albums

$albumTitle = $albumEntity->getName();
if(empty($albumTitle)) {
    header("Location: error?error=7");
    exit;
}

$metaInfo = MetaProvider::getMetaInfo($con, 13, $_GET["id"]);  //index page
$title = $metaInfo["title"];
$metaDescription = $metaInfo["description"];
$metaKeywords = $metaInfo["keywords"];

require_once("./includes/header.php");

$picsProvider = new Provider($con);
$picsHtml = $picsProvider->getPicsByAlbumId($albumEntity->getId());

echo "<div class='swiper-container' style='padding-top: 90px;'>
        <div class='swiper-wrapper'>
            $picsHtml
        </div>
    </div>";

$categoryId = $albumEntity->getCategoryId();

$container = new Container($con, null, $categoryId, null);
echo $container->showCategory(9, $categoryId, null, 15, "You might also like");

?>


<script>
    //swiper js
    var swiper = new Swiper('.swiper-container', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        coverflowEffect: {
        rotate: 10,
        stretch: 0,
        depth: 200,
        modifier: 1,
        slideShadows: true,
        },
        pagination: {
        el: '.swiper-pagination',
        },
    });
</script>
