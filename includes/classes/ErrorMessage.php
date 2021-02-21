<?php

class ErrorMessage {
    public static function show($code=0) {
        $text = "";
        switch($code) {
            case 1: $text = "Preview Link doesn't exists in database";
            break;
            case 2: $text = "Thumbnail Link doesn't exists in database";
            break;
            case 3: $text = "Full video link doesn't exist in database";
            break;
            case 4: $text = "Thumbnail File doesn't exist on server";
            break;
            case 5: $text = "Video file can't be deleted";
            break;
            case 6: $text = "Video Thumbnail File can't be deleted";
            break;
            case 7: $text = "Oops! Album not found";
            break;
            case 8: $text = "Oops! Pics are not found";
            break;
            case 9: $text = "Album Thumbnail Path doesn't exist in database";
            break;
            case 10: $text = "Album Thumbnail doesn't exist on server";
            break;
            case 11: $text = "Album Thumbnail can't be deleted but its all pics deleted";
            break;
            case 12: $text = "Requested video doesn't exist";
            break;
            case 13: $text = "No videos to show";
            break;
            case 18: $text = "Oops! Video Not Found";
            break;
            default: $text = "Something went wrong!";
        }
        echo "<div class='errorBanner'><i class='fa fa-exclamation-triangle' style='margin-right: 10px; margin-bottom:10px; color:red;'></i><br>
        {$text}
        <br>
        <button onclick='window.history.back()' style='margin-top: 10px;'><i class='fa fa-arrow-left' style='margin-right: 20px;'></i>Go Back</button>
        </div>";
    }
}

?>