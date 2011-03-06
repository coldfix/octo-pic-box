<?php
include('common.php');

$file = basename($_FILES['file']['name']);

if (!is_public_file($file)) {
    logToFile("upload denied: ".$file);
    echo "upload denied: ".$file;
}
else if (move_uploaded_file($_FILES['file']['tmp_name'], $files.$file) ) {
    logToFile("upload: " . $file);
    echo "upload complete: ".$file;
}
else {
    logToFile("upload failed: " . $file);
    echo "upload failed: ".$file;
}

if (is_image_file($file))
    update_thumb($file);

echo '<br/>
    <a href="'.$_POST['referer'].'">Return now</a>';

?>
