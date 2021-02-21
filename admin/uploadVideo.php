<?php
require_once("../includes/config.php");

if(!isset($_SESSION["contentUploader"]) || ($_SESSION["contentUploader"] != true)) {
    header("Location: ../index.php");
    exit;
}

require_once("../includes/classes/Category.php");
require_once("../includes/classes/CategoryProvider.php");

require_once("../includes/classes/Star.php");
require_once("../includes/classes/StarProvider.php");

$errorMessage = "";
$successMessage = "";

$categories = CategoryProvider::getCategory($con, 1, 1);
$stars = StarProvider::getAllStars($con);


if(isset($_POST["uploadContent"])) {

    $success_message = "";

    if(isset($_POST["videoTitle"]) && isset($_POST["thumbnail"]) && isset($_POST["previewVideo"]) && isset($_POST["fullVideo"]) &&
    isset($_POST["videoDescription"]) && isset($_POST["category"])) {

        $nInputs = count($_POST["videoTitle"]);

        $title_array = $_POST["videoTitle"];
        $thumbnail_array = $_POST["thumbnail"];
        $preview_array = $_POST["previewVideo"];
        $fullVideo_array = $_POST["fullVideo"];
        $description_array = $_POST["videoDescription"];
        $category_array = $_POST["category"];
        $star_array = $_POST["star"];
        $isTopRated_array = $_POST["isTopRated"];

        require_once("../includes/classes/EntityProvider.php");

        for($i=0; $i<$nInputs; $i++) {
            $flag = 1;

            $doesVideoExists = EntityProvider::doesVideoExist($con, $title_array[$i], $preview_array[$i], $fullVideo_array[$i], $description_array[$i]);

            switch($doesVideoExists) {
                case 1:
                    $errorMessage .= "<p class='errorText'>{$title_array[$i]} already exist in database</p><br>";
                    $flag = 0;
                break;
                case 2:
                    $errorMessage .= "<p class='errorText'>Preview link {$preview_array[$i]} already exist</p><br>";
                    $flag = 0;
                break;
                case 3:
                    $errorMessage .= "<p class='errorText'>Full video link {$fullVideo_array[$i]} already exist</p><br>";
                    $flag = 0;
                break;
                case 4:
                    $errorMessage .= "<p class='errorText'>Video description of {$title_array[$i]} already exists</p><br>";
                    $flag = 0;
            }

            if($flag != 0) {
                EntityProvider::addVideoToDatabase($con, $preview_array[$i], $fullVideo_array[$i], $thumbnail_array[$i], $category_array[$i],
                $title_array[$i], $isTopRated_array[$i], $description_array[$i], $star_array[$i]);
                $success_message .= "<p>{$title_array[$i]} successfully inserted.</p><br>";
            }
        }

    }

    $errorMessage .= "<p class='errorText'>No video was provided</p>";

    $successMessage = "<div class='alertSuccess'>
                        $success_message
                    </div>";

}

?>

<!DOCTYPE html>
<html>
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Welcome! Upload Video</title>

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
                        <h2 style="margin: 0;">Upload Video</h2>
                        <div class="formData" id="dynamic_video_container">

                            <label>Video Title</label>
                            <input type="text" name="videoTitle[]" required>

                            <label>Thumbnail</label>
                            <input type="url" name="thumbnail[]" required>

                            <label>Preview Video Embedded Link</label>
                            <input type="url" name="previewVideo[]" required>

                            <label>Full Video Direct Link</label>
                            <input type="url" name="fullVideo[]" required>

                            <label>Video Description</label>
                            <textarea name="videoDescription[]" rows="10" cols = "30" placeholder="Type something" required></textarea>

                            <label>Choose a category</label>
                            <select name="category[]">
                                <?php

                                    foreach($categories as $category) {
                                        echo "<option value='{$category->getId()}'>{$category->getName()}</option>\n                                ";
                                    }

                                ?>
                            </select>

                            <label>Choose A Star</label>
                            <select name="star[]">
                                <option selected value="0">Select a star</option>
                                <?php

                                    foreach($stars as $star) {
                                        echo "<option value='{$star->getId()}'>{$star->getName()}</option>";
                                    }

                                ?>
                            </select>

                            <label>Is top rated?</label>
                            <select name="isTopRated[]">
                                <option selected value="0">Select</option>
                                <option value="1">Top Rated</option>
                            </select>

                        </div>

                        <div style="width: 100%; display: inline-flex; justify-content: space-around; height: 50px; align-items: center;">
                                <label for="addInputBtn" style="color: #fff; font-size: 20px">Videos</label>
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
            var i=1;
            $("#addInputBtn").click(function() {
                i++;
                var inputInnerHtml = `<div class="dynamicFields" id="field_group`+i+`">

                    <div style='display: inline-flex; justify-content: space-between; align-items: center;'>
                        <h3>Video</h3>
                        <button type="button" class="removeFieldsBtn" id="`+ i +`"><i class="fa fa-trash"></i></button>
                    </div>

                    <label>Video Title</label>
                    <input type="text" name="videoTitle[]" required>

                    <label>Thumbnail</label>
                    <input type="url" name="thumbnail[]" required>

                    <label>Preview Video Embedded Link</label>
                    <input type="url" name="previewVideo[]" required>

                    <label>Full Video Direct Link</label>
                    <input type="url" name="fullVideo[]" required>

                    <label>Video Description</label>
                    <textarea name="videoDescription[]" rows="10" cols = "30" placeholder="Type something" required></textarea>

                    <label>Choose a category</label>
                    <select name="category[]">
                        <?php

                            foreach($categories as $category) {
                                echo "<option value='{$category->getId()}'>{$category->getName()}</option>\n                                ";
                            }

                        ?>
                    </select>

                    <label>Choose A Star</label>
                    <select name="star[]">
                        <option selected value="0">Select a star</option>
                        <?php

                            foreach($stars as $star) {
                                echo "<option value='{$star->getId()}'>{$star->getName()}</option>";
                            }

                        ?>
                    </select>

                    <label>Is top rated?</label>
                    <select name="isTopRated[]">
                        <option selected value="0">Select</option>
                        <option value="1">Top Rated</option>
                    </select>

                </div>`;
                $('#dynamic_video_container').append(inputInnerHtml);
            });

            $(document).on("click", ".removeFieldsBtn", function() {
                var button_id = $(this).attr("id");
                $("#field_group"+button_id+"").remove();
            });

        });
    </script>

</html>