<?php

$errorMessages = null;

if(!isset($_GET["id"]) && isset($_GET["entityType"])) {
    header("Location: ../error");
    exit;
}

else {
    
    require_once("../includes/config.php");
    require_once("../includes/classes/ErrorMessage.php");

    require_once("../includes/classes/Entity.php");
    require_once("../includes/classes/EntityProvider.php");

    $entity = new Entity($con, $_GET["id"], $_GET["entityType"]);

    if($_GET["entityType"] == 1) {
        //video delete
        $previewPath = null;
        $thumbnailPath = null;
        $previewFilePath = $entity->getPreview();
        $thumbnailPath = $entity->getThumbnail();
        $fullVideoPath = $entity->getFullVideo();
        
        if(!$previewFilePath || !$thumbnailPath || !$fullVideoPath) {
            if(!$previewFilePath) {
                header("Location: ../error?error=1");
                exit;
            }
            else if(!$thumbnailPath) {
                header("Location: ../error?error=2");
                exit;
            }
            else {
                header("Location: ../error?error=3");
                exit;
            }
        }
        else {
            
            EntityProvider::deleteVideoFromDatabase($con, $entity->getId());
            echo "<script>window.close();</script>";

        }
    }
    else if($_GET["entityType"] == 2) {
        //album delete
        $album_thumbnail_filePath = null;
        $album_thumbnail_filePath = $entity->getThumbnail();
        if(!$album_thumbnail_filePath) {
            header("Location: ../error?error=9");
            exit;
        }
        else {
            $album_thumbnail_filePath = realpath("../".$album_thumbnail_filePath);

            $doesAlbumExistOnServer = file_exists($album_thumbnail_filePath);

            if(!$doesAlbumExistOnServer) {
                header("Location: ../error?error=10");
                exit;
            }
            else {
                //first deleting all pics, if any error occurs, it won't delete album thumbnail

                require_once("../includes/classes/Pics.php");
                require_once("../includes/classes/PicsProvider.php");

                $albumId = $entity->getId();

                $picIds = PicsProvider::getPicsFromAlbum($con, $albumId);

                $errorMessages = array();

                $nPicsDeleted = 0;
                $picFlag = 1;

                foreach($picIds as $picId) {

                    $picFlag = 1;

                    $pic = new Pics($con, $picId);

                    $picFilePath = null;
                    $picFilePath = $pic->getPicPath();
                    if(!$picFilePath) {
                        $errorMessages[] = "<h3>Error: {$picId} doesn't exist on database</h3>";
                        $picFlag = 0;
                    }
                    else {

                        $picFilePath = realpath("../".$picFilePath);
                        $doesPicExistsOnServer = file_exists($picFilePath);

                        if(!$doesPicExistsOnServer) {
                            $errorMessages[] = "<h3>Error: Pic with id={$picId} doesn't exist on server</h3>";
                            $picFlag = 0;
                        }
                        else {
                            $picDeletion = unlink($picFilePath);
                            if(!$picDeletion) {
                                $errorMessages[] = "<h3>Error: Pic with id={$id} couldn't be deleted</h3>";
                                $picFlag = 0;
                            }
                            else {
                                //clean up database
                                PicsProvider::deletePicFromDatabase($con, $picId);
                                $nPicsDeleted++;
                            }
                        }
                    }
                }

                if($nPicsDeleted == count($picIds)) {
                    // deleted all pics from album successfully, proceed to deleted album itself
                    $album_thumbnail_delete = unlink($album_thumbnail_filePath);
                    if(!$album_thumbnail_delete) {
                        $errorMessages[] = "<h3>Error: Album's thumbnail with id={$albumId} couldn't be deleted</h3>";
                    }
                    else {
                        $albumDir = realpath("../entities/albums/".$albumId); // blank directory
                        $albumDir_delete = rmdir($albumDir);
                        if(!$albumDir_delete) {
                            $errorMessages[] = "<h3>Error: Album's directory with id={$albumId} couldn't be deleted but all pics and thumbnail are deleted</h3>";
                        }
                        //successfully deleted album files from server, now clean up dB
                        EntityProvider::deleteAlbumFromDatabase($con, $albumId);
                        echo "<script>window.close();</script>";
                    }
                }

            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Delete Album Error</title>

        <link href="./assets/style.css" rel="stylesheet" type="text/css">

        <style>
            body {
                background-color: #000;
            }            
        </style>

    </head>

    <body>
        <div class="wrapper">
            <?php
                include_once("../includes/navBar.php");
            ?>
            <div class="">
                <?php 
                    foreach($errorMessages as $errorMessage) {
                        echo $errorMessage;
                    }
                ?>
            </div>
        </div>
    </body>
</html>