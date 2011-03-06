<?php
include('common.php');

$file = basename($_GET['file']);
$path = $files.$file;

if (!is_public_file($file) || !file_exists($path)) {
    logToFile("denied download: " . $file);
    die("Download of '$file' not allowed.");
}

logToFile("download: " . $file);

http_send_content_disposition($file, false);
http_send_content_type('application/octet-stream');
http_throttle(0.0, 40960);				// no delay currently
http_send_file($path);

?>
