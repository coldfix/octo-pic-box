<?php
include('common.php');

$file = basename($_GET['file']);
$path = $files.$file;

if (!is_public_file($file) || !file_exists($path) || !is_image_file($file)) {
    logToFile("denied view: " . $file);
    die("Viewing of '$file' not allowed.");
}
logToFile("view: " . $file);

$size = getimagesize($path);
http_send_content_disposition($file, true);
http_send_content_type($size['mime']);
http_throttle(0.0, 40960);				// no delay currently
http_send_file($path);

?>
