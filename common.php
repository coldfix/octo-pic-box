<?php

$root = '/data/development/httproot/download/';
$files = $root."files/";
$thumbs = $root."thumbs/";

$thumb_width = 350;
$thumb_height = 140;

$page = array('css' => array('style.css'));


function logToFile($msg)
{
    $fd = fopen(".log", "a");
    fwrite($fd, "[" . date("Y/m/d h:i:s", mktime()) . "] <". $_SERVER['REMOTE_ADDR']."> (".$_SERVER['REMOTE_HOST'].") ". $msg . "\n");
    fclose($fd);
}

function get_filesize_unit($dsize)
{
    if (strlen($dsize) >= 10)
        return "GiB";
    elseif (strlen($dsize) >= 7)
        return "MiB";
    else
        return "KiB";
}

function get_filesize ($dsize)
{
    $unit = get_filesize_unit($dsize);
    if ($unit == "GiB")
        return number_format($dsize / (1024 * 1024 * 1024), 2);
    elseif ($unit == "MiB")
        return number_format($dsize / (1024 * 1024), 2);
    elseif ($unit == "KiB")
        return number_format($dsize / 1024, 2);
    elseif ($unit == "Byte")
        return $dsize;
}

function is_public_file($file)
{
    return strpos($file, '.') !== 0;
    // return strpos($file, '.') !== 0 && $file != 'index.php';
}

function file_extension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function is_image_file($filename)
{
    $ext = file_extension($filename);
    return $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png';
}

function create_thumb($image, $thumb, $thumb_width, $thumb_height)
{
    $ext = file_extension($image);

    // load image
    if ($ext == 'jpg' || $ext == 'jpeg')
        $orig_img = imagecreatefromjpeg($image);
    else if ($ext == 'png')
        $orig_img = imagecreatefrompng($image);
    else {
        return;
    }

    // get image size
	$orig_width = imagesx($orig_img);
	$orig_height = imagesy($orig_img);

    // calculate thumbnail size
	if ($orig_width / $orig_height > $thumb_width / $thumb_height)
		$thumb_height = $thumb_width * $orig_height / $orig_width;
    else if ($orig_width / $orig_height < $thumb_width / $thumb_height)
		$thumb_width = $thumb_height * $orig_width / $orig_height;

    // create a new temporary image
	$thumb_img = imagecreatetruecolor($thumb_width, $thumb_height);

    // copy and resize old image into new image
	imagecopyresampled($thumb_img, $orig_img, 0,0,0,0, $thumb_width, $thumb_height, $orig_width, $orig_height);
    // imagecopyresized($thumb_img, $orig_img, 0,0,0,0, $thumb_width, $thumb_height, $orig_width, $orig_height);

    // save thumbnail into a file
	if ($ext == 'png')
		imagepng($thumb_img, $thumb);
	else
		imagejpeg($thumb_img, $thumb);

	imagedestroy($thumb_img);
	imagedestroy($orig_img);
}


function update_thumb($filename)
{
    global $thumbs, $files, $thumb_width, $thumb_height;
    if (!file_exists($thumbs.$filename) || filemtime($files.$filename) > filemtime($thumbs.$filename))
        create_thumb($files.$filename, $thumbs.$filename, $thumb_width, $thumb_height);
}


function list_files()
{
    global $files;
    $image_files = array();
    $other_files = array();

    $dir = opendir($files);
    while ($file = readdir($dir)) {
        if (!is_public_file($file))
            continue;
        if (is_image_file($file))
            $image_files[] = $file;
        else
            $other_files[] = $file;
    }

    $compare = function($file_a, $file_b) use ($files) {
        return filemtime($files.$file_a) - filemtime($files.$file_b); };
    $sort = function($array) use ($compare) {
        usort($array, $compare);
        return $array; };

    return array(
        'all' => $sort(array_merge($image_files, $other_files)),
        'image' => $sort($image_files),
        'normal' => $sort($other_files)
    );
}


?>
