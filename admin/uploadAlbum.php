<?php
require_once("../includes/config.php");

if(!isset($_SESSION["contentUploader"]) || ($_SESSION["contentUploader"] != true)) {
    header("Location: ../index.php");
    exit;
}

require_once("../includes/classes/CategoryProvider.php");
require_once("../includes/classes/Category.php");

$errorMessage = "";
$successMessage = "";

$categories = CategoryProvider::getCategory($con, 2, 1);

if(isset($_POST["uploadContent"])) {

    $albumFlag = 1;
    
    if(!isset($_FILES["picFiles"]) || !isset($_POST["picTitle"]) || !isset($_POST["picDescription"])  || !isset($_POST["picCategory"])) {
        $errorMessage = "Upload at least one pic";
        $albumFlag = 0;
    }
    else {
        $nInputs = count($_POST["picTitle"]);

        $isAlbumTopRated = 0;
        if(isset($_POST["isTopRated"])) {
            $isAlbumTopRated = 1;
        }

        $query = $con->prepare("SELECT AUTO_INCREMENT FROM information_schema.Tables where TABLE_SCHEMA='ail' and TABLE_NAME='albums'");
        $query->execute();
        $newAlbumId = $query->fetchColumn();
        $albumDirPath = "entities/albums/".$newAlbumId. DIRECTORY_SEPARATOR;
        $baseUploadDirPath = "../". $albumDirPath;

        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif'); 

        //1MB max file size
        $maxPicSize = 1 * 1024 * 1024;

        $picFilesArray = $_FILES["picFiles"];

        if($nInputs > 0) {
            //make album directory
            is_dir($baseUploadDirPath) || mkdir($baseUploadDirPath, 0777, true);
        }
        else {
            $errorMessage = "<p class='errorText'>No pics were provided</p>";
            $albumFlag = 0;
        }

        $albumThumnail = $_FILES["thumbnail"];
        $album_thumbnail_tmpname = $albumThumnail["tmp_name"];
        $album_thumbnail_name = $albumThumnail["name"];
        $album_thumbnail_size = $albumThumnail["size"];
        $album_ext_thumbnail = pathinfo($album_thumbnail_name, PATHINFO_EXTENSION);
        $album_upload_path = $baseUploadDirPath.$album_thumbnail_name;
        if(in_array(strtolower($album_ext_thumbnail), $allowedTypes)) {
            if($album_thumbnail_size > $maxPicSize) {
                $errorMessage = "<p class='errorText'>Error: {$album_thumbnail_name} size is larger than allowed</p><br>";
                $albumFlag = 0;
            }
            else {
                if(file_exists($album_upload_path)) {
                    $errorMessage .= "<p class='errorText'>Error: {$album_thumbnail_name} again selected to upload</p><br>";
                    $albumFlag = 0;
                }
                else {
                    if(!move_uploaded_file($album_thumbnail_tmpname, $album_upload_path)) {
                        $errorMessage .= "<p class='errorText'>Error Uploading {$pic_name}</p><br>";
                        $albumFlag = 0;
                    }
                }
            }
        }

        if($albumFlag == 1) {

            require_once("../includes/classes/EntityProvider.php");
            //no errors while creating an album, so start adding pics

            $nPicsUploaded = 0;

            for($i=0; $i<$nInputs; $i++) {

                $picFlag = 1;

                $picTitle = $_POST["picTitle"][$i];
                $picCategory = $_POST["picCategory"][$i];
                $picDescription = $_POST["picDescription"][$i];

                $pic_tmpname = $picFilesArray['tmp_name'][$i]; 
                $pic_name = $picFilesArray['name'][$i]; 
                $pic_size = $picFilesArray['size'][$i]; 
                $pic_ext = pathinfo($pic_name, PATHINFO_EXTENSION);
                
                //pic upload path
                $pic_upload_path = $baseUploadDirPath.$pic_name;

                if(!in_array(strtolower($pic_ext), $allowedTypes)) {
                    $errorMessage .= "<p class='errorText'>Error: {pic_name} has not valid type</p><br>";
                    $picFlag = 0;
                }
                else {
                        if($pic_size > $maxPicSize) {
                            $errorMessage = "<p class='errorText'>Error: {$pic_name} size is larger than allowed</p><br>";
                            $picFlag = 0;
                        }
                        
                        if(file_exists($pic_upload_path)) {
                            $errorMessage .= "<p class='errorText'>Error: {$pic_name} again selected to upload</p><br>";
                            $picFlag = 0;
                        }

                        //move file to upload directory
                        if(!move_uploaded_file($pic_tmpname, $pic_upload_path)) {
                            $errorMessage .= "<p class='errorText'>Error Uploading {$pic_name}</p><br>";
                            $picFlag = 0;
                        }
                        else {
                            $nPicsUploaded++;
                            EntityProvider::addPicToDatabase($con, $picTitle, $picDescription, $albumDirPath.$pic_name, $picCategory, $newAlbumId);
                        } 
                } 
            }

            if($nPicsUploaded > 0) {
                EntityProvider::addAlbumToDatabase($con, $_POST["albumTitle"], $_POST["albumDescription"], $albumDirPath.$album_thumbnail_name, $_POST["albumCategory"], $isAlbumTopRated);
            }
        }
        else {
            $errorMessage .= "<p class='errorText'>Error: Creating Album</p>";
        }

        if($albumFlag == 1) {
            $successMessage = "<div class='alertSuccess'>
                                    Some or all pics are uploaded. Verify with errors.
                            </div>";
        }
        
    }
}

?>
<!DOCTYPE html>
<html>
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Welcome! Upload An Album</title>

        <link href="/StreamGirl/assets/style/fontawesome-free-5.13.1-web/css/all.css" rel="stylesheet" type="text/css" />
        <link href="/StreamGirl/assets/style/fontawesome-free-5.13.1-web/css/v4-shims.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/style.css" rel="stylesheet" type="text/css">

        <script src="/StreamGirl/assets/js/jquery-3.5.1.slim.min.js"></script>

        <style>
            body {
                background-color: #000;
                margin: 0;
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
                        <h2 style="margin: 0;">Upload An Album</h2>

                        <div class="formData" id="dynamic_album">

                            <label for="albumTitle">Album Title</label>
                            <input type="text" name="albumTitle" id="albumTitle" placeholder="Album Name" required>
                            
                            <label for="albumDescription">Album Description</label>
                            <textarea name="albumDescription" rows="10" cols = "30" placeholder="Type something" required></textarea>
                            
                            <label for="categories">Choose Album Category</label>
                            <select name="albumCategory" id="categories">
                                <?php
                                    foreach($categories as $category) {
                                        echo "<option value='{$category->getId()}'>{$category->getName()}</option>";
                                    }
                                ?>
                            </select>

                            <label for="thumbnail">Album Thumbnail</label>
                            <input type="file" name="thumbnail" id="thumbnail" required>

                            <div style="display: inline-flex; align-items: center;padding: 20px 0;">
                                <input type="checkbox" id="isTopRated" name="isTopRated" style="width: 30px; height: 30px;">
                                <label for="isTopRated">Top Rated</label>
                            </div>

                        </div>
                        
                        <div style="width: 100%; display: inline-flex; justify-content: space-around; height: 50px; align-items: center;">
                                <label for="addInputBtn" style="color: #fff; font-size: 20px">Add Album</label>
                                <button class="addInputsBtn" id="addInputBtn" type="button">Add</button>
                        </div>

                        <div class="alertError">
                            <?php echo $errorMessage ?>
                        </div>

                        <input type="submit" value="Upload" name="uploadContent" style="margin: 0 10px; height: 50px;">

                        <?php echo $successMessage ?>
                        
                    </form>
                </div>
            </div>
        </div>
    </body>

    <script>
        $(document).ready(function(){
            var i=0;
            $("#addInputBtn").click(function() {
                i++;
                var inputInnerHtml = `<div class="dynamicFields" id="field_group`+i+`">
                <div style='display: inline-flex; justify-content: space-between; align-items: center;'>
                    <h3>Pic</h3>
                    <button type="button" class="removeFieldsBtn" id="`+ i +`"><i class="fa fa-trash"></i></button>
                </div>
                <label for="pic">Select A Pic</label>
                <input type="file" name="picFiles[]" id="pic" required>
                <label for="picTitle">Pic Title</label>
                <input type="text" name="picTitle[]" id="picTitle" required>
                <label for="pic_description">Pic Description</label>
                <textarea id="pic_description" name="picDescription[]" rows="10" cols = "30" placeholder="Type something" required></textarea>
                <label for="categories">Choose a category</label>
                <select name="picCategory[]" id="categories">
                     <?php
                        foreach($categories as $category) {
                            echo "<option value='{$category->getId()}'>{$category->getName()}</option>";
                        }
                     ?>
                </select>
                </div>`;
                $('#dynamic_album').append(inputInnerHtml);
            });

            $(document).on("click", ".removeFieldsBtn", function() {
                var button_id = $(this).attr("id");
                $("#field_group"+button_id+"").remove();
            });

        });
    </script>

</html>