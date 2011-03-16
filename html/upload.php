<?php
$intern = "../intern";
require_once("$intern/common.php");
$page['title'] = 'Upload';
include("$intern/header.php");

$file = basename($_FILES['file']['name']);
$path = $files.$file;

if (!is_public_name($file) && !file_exists($path)) {
    logToFile("upload denied: ".$file);
    echo "upload denied: ".$file;
}
else if (move_uploaded_file($_FILES['file']['tmp_name'], $path) ) {
    logToFile("upload: " . $file);
    echo "upload complete: ".$file;
}
else {
    logToFile("upload failed: " . $file);
    echo "upload failed: ".$file;
}

echo '<br/>
    <a href="'.$_POST['referer'].'">Return now</a>';

include("$intern/footer.php");
?>
