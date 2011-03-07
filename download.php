<?php
include('intern/common.php');

if (!isset($_GET['file']) || empty($_GET['file'])) {
    die ('No file requested.');
}

$serve_thumb = isset($_GET['thumb']) && $_GET['thumb'];
$serve_inline = isset($_GET['inline']) && $_GET['inline'];
$serve_action = $serve_thumb ? 'thumb' : ($serve_inline ? 'view' : 'download');

$file = basename($_GET['file']);
$path = $files.$file;
$thumb = $thumbs.$file;
$send = $serve_thumb ? $thumb : $path;


if (!is_public_file($file) || !file_exists($path)
    || ($serve_thumb && !is_image_file($file) ))
{
    logToFile("denied $serve_action: " . $file);
    die("Access to '$file' not allowed.");
}
logToFile("$serve_action: " . $file);

if ($serve_thumb && (!file_exists($thumb) || filemtime($path) > filemtime($thumb)))
    create_thumb($path, $thumb, $thumb_width, $thumb_height);


$finfo = new FInfo(FILEINFO_MIME)
    or die("Opening fileinfo database failed");

// HttpResponse::setGzip(true);
HttpResponse::setContentDisposition($file, $serve_inline);
// HttpResponse::guessContentType($send);
HttpResponse::setContentType($finfo->file($send));

HttpResponse::setFile($send); // auto calculates ETag and LastModified
HttpResponse::setCacheControl('public', 3600*24, false);
HttpResponse::setCache(true);

HttpResponse::setThrottleDelay(0.0);
HttpResponse::send();

?>
