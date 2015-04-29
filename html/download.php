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


if ($serve_thumb && obsolete($thumb, $path)) {
    create_thumb($path, $thumb, $thumb_width, $thumb_height);
}

if (is_dir($path)) {
    system('gzip "' . escapeshellcmd() . '"');
    // to do: gzip compression
}

$finfo = new FInfo(FILEINFO_MIME)
    or fatal_error("failed $serve_action: ".$filename, "Opening fileinfo database failed");

$file = fopen($send, 'rb');
$size = filesize($send);
$type = $serve_inline ? "inline" : "attachment";
$time = filemtime($send);
$expire = 3600*24*31;

$resp = new http\Env\Response;
$body = new http\Message\Body($file);
$resp->setBody($body);
$resp->setHeader("Content-Length", $size);
$resp->setHeader("Last-Modified", date("r", $time));
$resp->setHeader("Expires", date("r", time()+$expire));
$resp->setContentDisposition([$type => ["filename" => $filename]]);
$resp->setContentType($finfo->file($send));
$resp->setCacheControl("Cache-Control: max-age=$expire");

$resp->send();

?>
