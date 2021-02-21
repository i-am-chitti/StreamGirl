<?php

session_start();

if(isset($_SESSION["contentUploader"]) && ($_SESSION["contentUploader"] == true)) {
    session_destroy();
    header("Location: ./");
    exit;
}

if(isset($_POST["collectData"])) {

    $username = $_POST["entryName"];
    $password = $_POST["entryKey"];

    if($username == "chitti" && $password == "1234") {
        $_SESSION["contentUploader"] = true;
        header("Location: ../index.php");
    }
    else {
        header("Location: ../error");
        exit;
    }

}

?>

<!DOCTYPE html>
<html>
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Admin Login</title>
        
        <link href="./assets/style.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <div class="container">
            <h3>Login</h3>
            <div class="formSection">
                <form method="POST">
                    <div class="formField">
                        <span>Username</span>
                        <input type="text" name="entryName" required>
                    </div>
                    <div class="formField">
                        <span>Password</span>
                        <input type="password" name="entryKey" required>
                    </div>
                    <button type="submit" name="collectData" class="submitBtn">Submit</button>
                </form>
            </div>
        </div>
    </body>
</html>