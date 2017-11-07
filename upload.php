<?php
$settings = array();
$settings['install_dir'] = 'gallery/';

require $_SERVER["DOCUMENT_ROOT"] . $settings['install_dir'] . 'settings.php';

session_start();
session_cache_limiter("private_no_expire");
if ((!isset($_SESSION["password"])) || ($_SESSION["password"] != $password)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ((isset($_POST["password"])) && (md5($_POST["password"]) == $password)) {
            $_SESSION["password"] = md5($_POST["password"]);
        } else {
            echo 'Wrong password?';exit();
        }
    } else {
        echo '<!doctype html><html><head><title>Login</title></head>
    <body><h1>Login</h1><p>You must be logged in to view this page.</p>
   <form action="" method="POST">
    <input type="password" placeholder="Password" name="password">
    <input type="submit" class="button">
   </form>
</body></html>';exit();
    }
}

$upload_category = $_POST['category'];

$target_dir = $upload_category . "/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $message = "<p>This file is not a picture...</p>";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    $message = "<p>A file with that name already exists...</p>";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 10000000) {
    $message = "<p>The file was to big. Try to resize it before uploading.</p>";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    $message = "<p>You can only upload: JPG, JPEG, PNG og GIF filer.</p>";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "The file was not uploaded";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $message = "<p>". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</p>";
        $message .= '<p><a href="admin.php" class="button">Upload another file</a></p>';
        $message .= '<p><a href="index.php?category='.$upload_category.'">Go to: '.$upload_category.'</a></p>';
        session_start();
        $_SESSION["selected_category"] = $upload_category;
    } else {
        $message ="<p>Error uploading file.</p>";
    }
}

require $_SERVER["DOCUMENT_ROOT"] . $settings['install_dir'] . 'templates/default/upload_template.php';





