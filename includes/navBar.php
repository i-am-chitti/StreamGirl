<?php

$isAdmin = false;

if(isset($_SESSION["contentUploader"]) && ($_SESSION["contentUploader"] == true)) {
    $isAdmin= true;
}

?>

<div class="topBar">
    
    <div class="logoContainer">
        <a href="/StreamGirl/index.php">
        <img src="/StreamGirl/assets/img/logo.png" alt="logo">
        </a>
    </div>

    <ul class="navLinks">

        <li><a href="/StreamGirl/index.php">Home</a></li>
        <li><a href="/StreamGirl/entitySelect.php">Category</a></li>
        <?php
            if($isAdmin) {
                echo "<li><a href='/StreamGirl/admin/uploadVideo.php'>Upload Video</a></li>
                        <li><a href='/StreamGirl/admin/uploadAlbum.php'>Upload Album</a></li>
                        <li><a href='/StreamGirl/admin/addStar.php'><i class='fa fa-plus'></i> Star</a></li>
                        <li><a href='/StreamGirl/admin/addVideoCategory.php'><i class='fa fa-plus'></i> Video Category</a></li>
                        <li><a href='/StreamGirl/admin/addAlbumCategory.php'><i class='fa fa-plus'></i> Album Category</a></li>";
            }
        ?>

    </ul>

    <div class="mobile">
        <label for="toggle">&#9776;</label>
        <input type="checkbox" id="toggle">
        <div class="menu">
            <a href="/StreamGirl/index.php">Home</a>
            <a href="/StreamGirl/entitySelect.php">Category</a>
            <?php
                if($isAdmin) {
                    echo "<a href='/StreamGirl/admin/uploadVideo.php'>Upload Video</a>
                            <a href='/StreamGirl/admin/uploadAlbum.php'>Upload Album</a>
                            <a href='/StreamGirl/admin/addStar.php'><i class='fa fa-plus'></i> Star</a>
                            <a href='/StreamGirl/admin/addVideoCategory.php'><i class='fa fa-plus'></i> Video Category</a>
                            <a href='/StreamGirl/admin/addAlbumCategory.php'><i class='fa fa-plus'></i> Album Category</a>";
                }
            ?>
            <div style="margin-top:0; display: inline-flex; width: 100%; justify-content: space-around;">
                <a href="/StreamGirl/search.php" style="width: 33%;"><i class="fas fa-search"></i></a>
                <?php
                    if($isAdmin) {
                        echo "<a href='/StreamGirl/admin/logout.php'><i class='fas fa-sign-out-alt'></i></a>";
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="rightItems">
        <a href="/StreamGirl/search.php"><i class="fas fa-search"></i></a>
        <?php
            if($isAdmin) {
                echo "<a href='/StreamGirl/admin/logout.php'><i class='fas fa-sign-out-alt'></i></a>";
            }
        ?> 
    </div>

</div>