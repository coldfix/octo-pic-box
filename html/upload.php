<?php
require_once("intern.php");
$page['title'] = 'Upload';
include("$intern/header.php");

$file = basename($_FILES['file']['name']);
$path = $files.$file;

if (!is_public_name($file) && !file_exists($path)) {
    logToFile("upload-denied /$dirname/$file");
    echo "upload denied: $dirname/$file";
}
else if (move_uploaded_file($_FILES['file']['tmp_name'], $path) ) {
    logToFile("upload /$dirname/$file");
    echo "upload complete: $dirname/$file";
}
else {
    logToFile("upload-failed /$dirname/$file");
    echo "upload failed: $dirname/$file";
}

echo '<br/>
    <a href="'.$_POST['referer'].'">Return now</a>';

include("$intern/footer.php");
?>
