<?php
include('common.php');

if (!isset($_GET['file']) || empty($_GET['file'])) {
    print "<pre>";
    print_r($_SERVER);
    print "</pre>";
    die ('No file requested.');
}

$serve_thumb = isset($_GET['thumb']) && $_GET['thumb'];
$serve_inline = isset($_GET['inline']) && $_GET['inline'];

$file = basename($_GET['file']);
$path = $files.$file;
$thumb = $thumbs.$file;

$action = $serve_thumb ? 'thumb' : ($serve_inline ? 'view' : 'download');


if (!is_public_file($file) || !file_exists($path)
    || ($serve_thumb && !is_image_file($file) ))
{
    logToFile("denied $action: " . $file);
    die("Access to '$file' not allowed.");
}
logToFile("$action: " . $file);

$finfo = new FInfo(FILEINFO_MIME); // return mime type ala mimetype extension
if (!$finfo)
    die("Opening fileinfo database failed");

if ($serve_thumb)
    update_thumb($file);


http_send_content_disposition($file, $serve_inline);
http_send_content_type($finfo->file($thumb));
http_throttle(0.0, 40960);				// no delay currently
http_send_file($thumb);
?>
