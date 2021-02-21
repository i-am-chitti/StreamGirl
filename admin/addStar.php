<?php

require_once("../includes/config.php");

if(!isset($_SESSION["contentUploader"]) || ($_SESSION["contentUploader"] != true)) {
    header("Location: ../index.php");
    exit;
}

$errorMessage = "";
$successMessage = "";

if(isset($_POST["addStar"]) && isset($_POST["starName"]) && isset($_FILES["thumbnail"])) {
    $starName = $_POST["starName"];

    //check if star already exist
    $sql = "SELECT id FROM stars WHERE title=:starName";
    $query = $con->prepare($sql);
    $query->bindValue(":starName", $starName);
    $query->execute();

    if($query->rowCount() != 0) {
        $errorMessage = "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Star Already exists in database</p>";
    }
    else {
        //check profile dp file type
        $query = $con->prepare("SELECT AUTO_INCREMENT FROM information_schema.Tables where TABLE_SCHEMA='ail' and TABLE_NAME='stars'");
        $query->execute();
        $newStarId = $query->fetchColumn();
        $dpUploadDirPath = "entities/stars/". $newStarId .DIRECTORY_SEPARATOR;
        $baseUploadPath = "../".$dpUploadDirPath;

        $allowedTypes =array("jpg", "png", "jpeg");

        //max dp Size = 1MB
        $maxDpSize =1 *1024 *1024;

        $starDp = $_FILES["thumbnail"];
        $starDp_tmpname = $starDp["tmp_name"];
        $starDp_name = $starDp["name"];
        $starDp_size = $starDp["size"];
        $starDp_ext = pathinfo($starDp_name, PATHINFO_EXTENSION);
        $starDp_upload_path = $baseUploadPath.$starDp_name;

        if(in_array(strtolower($starDp_ext), $allowedTypes)) {

            if($starDp_size > $maxDpSize) {
                $errorMessage = "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Dp file size exceeded</p>";
            }
            else {
                if(file_exists($starDp_upload_path)) {
                    $errorMessage= "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Dp already exist on server</p>";
                }
                else {

                    is_dir($baseUploadPath) || mkdir($baseUploadPath, 0777, true);

                    if(!move_uploaded_file($starDp_tmpname, $starDp_upload_path)) {
                        $errorMessage = "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Error uploading {$starDp_name}</p>";
                    }
                    else {
                        //success in moving file
                        require_once("../includes/classes/StarProvider.php");

                        StarProvider::addStarToDatabase($con, $starName, $dpUploadDirPath.$starDp_name);
                        $successMessage = "<div class='alertSuccess'>
                                                <p><i class='fa fa-check'></i> Star Added</p>
                                            </div>";
                    }
                }
            }

        }
        else {
            $errorMessage = "<p class='errorText'><i class='fa fa-exclamation-triangle'></i>Dp file type not allowed</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Welcome! Add A Star</title>

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
                        <h2 style="margin: 0;">Add An Star</h2>
                        <div class="formData">

                            <label for="thumbnail">Thumbnail</label>
                            <input type="file" name="thumbnail" id="thumbnail" required>
                            <label for="starName">Star Name</label>
                            <input type="text" name="starName" id="starName" required>
                        
                            <div class="alertError">
                                <?php echo $errorMessage ?>
                            </div>
                            <input type="submit" value="Upload" name="addStar">
                                <?php echo $successMessage ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>