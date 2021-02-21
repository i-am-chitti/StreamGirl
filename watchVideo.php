<?php

if(!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

require_once("./includeClasses.php");

$entity = new Entity($con, $_GET["id"], 1); //1 for videos

if(!$entity->getName()) {
    header("Location: error?error=12");
    exit;
}

$metaInfo = MetaProvider::getMetaInfo($con, 12, $_GET["id"]);  //index page
$title = $metaInfo["title"];
$metaDescription = $metaInfo["description"];
$metaKeywords = $metaInfo["keywords"];

require_once("./includes/header.php");

$entity->incrementViews();

?>

<div class="watchContainer" style="padding-top: 90px;">
    
    <iframe src="<?php echo $entity->getPreview() ?>" frameborder="0" marginwidth="0" marginheight="0" 
        scrolling="NO" width="100%" height="460" allowfullscreen>
    </iframe>

    <div class="videoDetailsContainer">
        <div class="videoDetail">
            <h1 style="width: 65%;overflow: hidden; text-overflow: ellipsis; height: 50px;"><?php echo $entity->getName(); ?></h1>
            <a href="<?php echo $entity->getFullVideo() ?>" class="videoLinkButton">Download full video</a>
        </div>
        <div class="videoDetailIcons">
            <i class="fa fa-eye"></i>
            <span><?php echo $entity->getViews(); ?></span>
            <button onclick="likeVideo(<?php echo $entity->getId(); ?>)"><i class="fa fa-heart" id="likeBtn" style="margin-left: 20px;"></i></button>
            <span id="numOfLikes"><?php echo $entity->getLikes(); ?></span>
        </div>
        <div style="padding: 20px;"><?php echo $entity->getDescription(); ?></div> 
    </div>
</div>

<?php

$categoryId = $entity->getCategoryId();

$container = new Container($con, null, $categoryId, null);
echo $container->showCategory(4, $categoryId, null, 10, "You might also like");

require_once("./includes/footer.php");

?>