<?php
require_once("intern.php");

if (empty($filename))
    error404();

$serve_thumb = isset($_GET['thumb']) && $_GET['thumb'];
$serve_inline = isset($_GET['inline']) && $_GET['inline'];
$serve_highlight = isset($_GET['highlight']) && $_GET['highlight'];

$serve_action = $serve_inline ? 'view' : 'download';
$serve_action .= $serve_thumb ? '-thumb' : ($serve_highlight ? "-highlight" : "");

$path = $files.$filename;
$thumb = $thumbs.$filename;
$highlight = $highlights.$filename.".html";

if ($serve_highlight && obsolete($highlight, $path)) {
  if (!create_highlight($path, "$dirname/$filename", $highlight)) {
    $serve_highlight = false;
  }
}

$send = $serve_thumb ? $thumb : ($serve_highlight ? $highlight : $path);

if ($serve_highlight) {
  $filename .= ".html";
}

if ($serve_thumb && !is_image_file($filename))
    error404();
logToFile("$serve_action /$dirname/$filename");


if ($serve_thumb && obsolete($thumb, $highlight)) {
    create_thumb($path, $thumb, $thumb_width, $thumb_height);
}

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
