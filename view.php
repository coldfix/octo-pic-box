<?php
include('common.php');

$file = basename($_GET['file']);
$path = $files.$file;

// if (!is_public_file($file) || !file_exists($path) || !is_image_file($file)) {
if (!is_public_file($file) || !file_exists($path)) {
    logToFile("denied view: " . $file);
    die("Viewing of '$file' not allowed.");
}
logToFile("view: " . $file);


$finfo = new finfo(FILEINFO_MIME); // return mime type ala mimetype extension

if (!$finfo)
    die("Opening fileinfo database failed");


// $size = getimagesize($path);
http_send_content_disposition($file, true);
// http_send_content_type($size['mime']);
http_send_content_type($finfo->file($path));
http_throttle(0.0, 40960);				// no delay currently
http_send_file($path);

?>
