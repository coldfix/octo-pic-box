<?php
require_once("intern.php");
$page['title'] = 'Upload';
include("$intern/header.php");

$file = basename($_FILES['file']['name']);
$path = $files.$file;

if (!is_public_name($file) && !file_exists($path)) {
    echo "upload denied: $dirname/$file";
}
else if (move_uploaded_file($_FILES['file']['tmp_name'], $path) ) {
    echo "upload complete: $dirname/$file";
}
else {
    echo "upload failed: $dirname/$file";
}

echo '<br/>
    <a href="'.$_POST['referer'].'">Return now</a>';

include("$intern/footer.php");
?>
