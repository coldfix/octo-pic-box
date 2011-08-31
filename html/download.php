<?php
require_once("intern.php");

if (empty($filename))
    error404();

$serve_thumb = isset($_GET['thumb']) && $_GET['thumb'];
$serve_inline = isset($_GET['inline']) && $_GET['inline'];
$serve_action = $serve_thumb ? 'thumb' : ($serve_inline ? 'view' : 'download');

$path = $files.$filename;
$thumb = $thumbs.$filename;
$send = $serve_thumb ? $thumb : $path;


if ($serve_thumb && !is_image_file($filename))
    error404();
logToFile("$serve_action /$filename");

if ($serve_thumb && (!file_exists($thumb) || filemtime($path) > filemtime($thumb)))
    create_thumb($path, $thumb, $thumb_width, $thumb_height);

if (is_dir($path)) {
    system('gzip "' . escapeshellcmd() . '"');
    // to do: gzip compression
}

$finfo = new FInfo(FILEINFO_MIME)
    or fatal_error("failed $serve_action: ".$filename, "Opening fileinfo database failed");

HttpResponse::setGzip(true);
HttpResponse::setContentDisposition($filename, $serve_inline);
// HttpResponse::guessContentType($send); // needs libmagick
HttpResponse::setContentType($finfo->file($send));

HttpResponse::setFile($send); // auto calculates ETag and LastModified
// HttpResponse::setCacheControl('public', 3600*24, false);
HttpResponse::setCacheControl('public', 3600*24*31, true);
HttpResponse::setCache(true);

HttpResponse::setThrottleDelay(0.0);
HttpResponse::send();

?>
