<?php
$thumb_width = 450;
$thumb_height = 150;

$page = array('css' => array('style/style.css'));


if (!isset($directory))
    $directory = isset($_GET['dir']) ? $_GET['dir'].'/' : '';

if ($directory !== '' && !is_public_folder($directory))
{
    // show 404 ?
    $directory = '';
}

$files .= $directory;
$thumbs .= $directory;


//--------------------------------------------------
// logging
//--------------------------------------------------

function logToFile($msg)
{
    global $root;
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')
        return;

    $fd = fopen("$root/access.log", "a");
    fwrite($fd, "[" . date("Y/m/d h:i:s", mktime()) . "] <". $_SERVER['REMOTE_ADDR']."> (".$_SERVER['REMOTE_HOST'].") ". $msg . "\n");
    fclose($fd);
}

function fatal_error($log, $user)
{
    logToFile($log);
    die ($user);
}

function error404()
{
    require('error404.php');
    exit();
}

//--------------------------------------------------
// file functions
//--------------------------------------------------

function is_public_dirname($file)
{
    return $file === ''
        || (strpos('.\\/', $file[0]) === false
        && strpos($file, '/.') === false);
}

function is_public_name($file)
{
    return $file !== ''
        && is_public_dirname($file);
        // && strcspn($file, "/\\") == strlen($file)
        // && $file[0] != '.';
}

function is_public_file($file, $path = '')
{
    global $files;
    return 1 //is_public_dirname($path)
        && is_public_name(basename($file))
        && is_readable($files.$path.$file)
        && (is_file($files.$path.$file) || is_dir($files.$path.$file));
}

function is_public_folder($file)
{
    global $files;
    return is_public_dirname($file)
        && is_readable($files.$file)
        && is_dir($files.$file);
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

function get_filesize ($dsize, $unit = "")
{
    if ($unit == "")
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


function file_extension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function is_image_file($filename)
{
    $ext = file_extension($filename);
    return $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png';
}

function thumb_size($filename)
{
    global $files, $thumbs, $thumb_width, $thumb_height;
    if (is_readable($thumbs.$filename)) {
        list($w, $h) = getimagesize($thumbs.$filename);
        return array('width' => $w, 'height' => $h);
    }
    else {
        list($w, $h) = getimagesize($files.$filename);
        return compute_thumb_size($w, $h, $thumb_width, $thumb_height);
    }
}

function compute_thumb_size($orig_width, $orig_height, $thumb_width, $thumb_height)
{
	if ($orig_width / $orig_height > $thumb_width / $thumb_height)
		$thumb_height = $thumb_width * $orig_height / $orig_width;
    else if ($orig_width / $orig_height < $thumb_width / $thumb_height)
		$thumb_width = $thumb_height * $orig_width / $orig_height;
    return array('width' => $thumb_width, 'height' => $thumb_height);
}

function create_thumb($image, $thumb, $thumb_width, $thumb_height)
{
    // load image
    $ext = file_extension($image);
    if ($ext == 'jpg' || $ext == 'jpeg')
        $orig_img = imagecreatefromjpeg($image);
    else if ($ext == 'png')
        $orig_img = imagecreatefrompng($image);
    else
        return;

    // get image size
	$orig_width = imagesx($orig_img);
	$orig_height = imagesy($orig_img);
    list($thumb_width, $thumb_height) = array_values(compute_thumb_size(
            $orig_width, $orig_height, $thumb_width, $thumb_height ));

    // create a new temporary image, copy and resize old image into new image
	$thumb_img = imagecreatetruecolor($thumb_width, $thumb_height);
    imagecopyresampled($thumb_img, $orig_img, 0,0,0,0,
        $thumb_width, $thumb_height, $orig_width, $orig_height);

    // save thumbnail into a file
    if (!is_dir(dirname($thumb)))
        mkdir(dirname($thumb));
	if ($ext == 'png')
		imagepng($thumb_img, $thumb);
	else
		imagejpeg($thumb_img, $thumb);

	imagedestroy($thumb_img);
	imagedestroy($orig_img);
}

function count_items($folder)
{
    global $files;

    $count = 0;
    $dir = opendir($files.$folder);
    while ($file = readdir($dir)) {
        if (is_public_file($file, $folder.'/'))
            $count = $count + 1;
    }
    return $count;
}


function list_files()
{
    global $files;
    $directories = array();
    $image_files = array();
    $other_files = array();

    $dir = opendir($files);
    while ($file = readdir($dir)) {
        if (!is_public_file($file))
            continue;
        if (is_dir($files.$file))
            $directories[] = $file;
        else if (is_image_file($file))
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
        'file' => $sort(array_merge($image_files, $other_files)),
        'folder' => $sort($directories),
        'image' => $sort($image_files),
        'normal' => $sort($other_files)
    );
}


?>
