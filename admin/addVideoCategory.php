<?php

require_once("../includes/config.php");

if(!isset($_SESSION["contentUploader"]) || ($_SESSION["contentUploader"] != true)) {
    header("Location: ../index.php");
    exit;
}

$errorMessage = "";
$successMessage = "";

if(isset($_POST["addCategory"]) && isset($_POST["category"]) && isset($_FILES["thumbnail"])) {
    $categoryTitle = $_POST["category"];

    //check if star already exist
    $sql = "SELECT id FROM videoCategories WHERE title=:category";
    $query = $con->prepare($sql);
    $query->bindValue(":category", $categoryTitle);
    $query->execute();

    if($query->rowCount() != 0) {
        $errorMessage = "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Category Already Exists</p>";
    }
    else {
        //check profile dp file type
        $query = $con->prepare("SELECT AUTO_INCREMENT FROM information_schema.Tables where TABLE_SCHEMA='ail' and TABLE_NAME='videocategories'");
        $query->execute();
        $newCategoryId = $query->fetchColumn();
        $dpUploadDirPath = "entities/categories/videoCategories/". $newCategoryId .DIRECTORY_SEPARATOR;
        $baseUploadPath = "../".$dpUploadDirPath;

        $allowedTypes =array("jpg", "png", "jpeg");

        //max dp Size = 1MB
        $maxThumbnailSize =1 *1024 *1024;

        $category_thumbnail = $_FILES["thumbnail"];
        $category_thumbnail_tmpname = $category_thumbnail["tmp_name"];
        $category_thumbnail_name = $category_thumbnail["name"];
        $category_thumbnail_size = $category_thumbnail["size"];
        $category_thumbnail_ext = pathinfo($category_thumbnail_name, PATHINFO_EXTENSION);
        $category_thumbnail_upload_path = $baseUploadPath.$category_thumbnail_name;

        if(in_array(strtolower($category_thumbnail_ext), $allowedTypes)) {

            if($category_thumbnail_size > $maxThumbnailSize) {
                $errorMessage = "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Thumbnail file size exceeded</p>";
            }
            else {
                if(file_exists($category_thumbnail_upload_path)) {
                    $errorMessage= "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Thumbnail file already exist on server</p>";
                }
                else {

                    is_dir($baseUploadPath) || mkdir($baseUploadPath, 0777, true);

                    if(!move_uploaded_file($category_thumbnail_tmpname, $category_thumbnail_upload_path)) {
                        $errorMessage = "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Error uploading {$category_thumbnail_name}</p>";
                    }
                    else {
                        //success in moving file
                        require_once("../includes/classes/CategoryProvider.php");

                        CategoryProvider::addCategoryToDatabase($con, $categoryTitle, $dpUploadDirPath.$category_thumbnail_name, 1); // 1 for entity type video
                        $successMessage = "<div class='alertSuccess'>
                                                <p><i class='fa fa-check'></i> Category Added</p>
                                            </div>";
                    }
                }
            }

        }
        else {
            $errorMessage = "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Thumbnail file type not allowed</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Welcome! Add Video Category</title>

    <link href="/StreamGirl/assets/style/fontawesome-free-5.13.1-web/css/all.css" rel="stylesheet" type="text/css" />
    <link href="/StreamGirl/assets/style/fontawesome-free-5.13.1-web/css/v4-shims.min.css" rel="stylesheet" type="text/css" />
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

            <div class="settingsContainer column" style="width:100%;">
                <div class="uploadForm">
                    <form method="post" enctype="multipart/form-data">
                        <h2 style="margin: 0;">Add A Video Category</h2>
                        <div class="formData">

                            <label for="thumbnail">Thumbnail</label>
                            <input type="file" name="thumbnail" id="thumbnail" required>
                            <label for="category">Category Title</label>
                            <input type="text" name="category" id="category" required>
                        
                            <div class="alertError">
                                <?php echo $errorMessage ?>
                            </div>
                            <input type="submit" value="Upload" name="addCategory">
                                <?php echo $successMessage ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>