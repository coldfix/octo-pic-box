<?php
$thumb_width = 450;
$thumb_height = 150;

$page = array('css' => array('style/style.css'));


$base_address = dirname($_SERVER['SCRIPT_NAME']);

if (!isset($dirname))
    $dirname = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    // $dirname = isset($_GET['dir']) ? $_GET['dir'].'/' : '';


$dirname = trim($dirname, "./");
if ($dirname !== '') {
    if (is_public_folder($dirname))
        $filename = '';
    else if (is_public_file($dirname)) {
        $filename = basename($dirname);
        $dirname = dirname($dirname);
    }
    else
        error404();
}

if ($dirname !== '') {
    $files .= "$dirname/";
    $thumbs .= "$dirname/";
    $highlights .= "$dirname/";
}



//--------------------------------------------------
// logging
//--------------------------------------------------

function logToFile($msg)
{
    global $root, $intern;
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')
        return;
    $remote_host = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : "";
    $date = date("Y/m/d h:i:s", mktime());
    $remote_addr = $_SERVER['REMOTE_ADDR'];
    $logstr = sprintf("[%s] <%s> (%s) %s\n", $date, $remote_addr, $remote_host, $msg);
    file_put_contents("$intern/access.log", $logstr, FILE_APPEND);
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

function obsolete($target, $dep)
{
    return !file_exists($target) || filemtime($dep) > filemtime($target);
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

function create_highlight($source, $highlight)
{
  $ret = 0;
  system("source-highlight -i " . escapeshellarg($source)
                         . " -o " . escapeshellarg($highlight)
                         . " -f html -d -q", $ret);
  return $ret == 0;
}

function is_highlightable($file)
{
  global $files, $highlights;
  return file_exists($highlights.$file.".html") ||
        create_highlight($files.$file, $highlights.$file.".html");
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
  if ($ext == 'jpg' || $ext == 'jpeg')
		imagejpeg($thumb_img, $thumb);
  else if ($ext == 'png')
		imagepng($thumb_img, $thumb);

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



function uri($relative)
{
    global $base_address;
    return htmlspecialchars($base_address.'/'.$relative);
}

function content($file, $action)
{
    global $dirname;
    if ($file == '..')
        return uri(implode('/',array($action,dirname($dirname))));
    else if ($file == '.')
        return uri(implode('/', array($action,$dirname)));
    else if ($dirname == '')
        return uri(implode('/', array($action,$file)));
    else
        return uri(implode('/', array($action,$dirname,$file)));
}


/*
 * enumerations
 */


function print_filelist($files, $fmtsize, $fmtunit, $mklink)
{
    global $dirname;
    print '
<h2>Subfolders</h2>
<div class="filelist">';
    if (!empty($dirname))
        $filelist['folder'][] = '..';
    foreach ($filelist['folder'] as $file) {
        $size = count_items($file);
        print
'<div class="file">
  <div class="size">'.$size.'</div>
  <div class="unit">Items</div>
  <div class="name">
    <a title="browse folder" href="'.htmlspecialchars($file).'/gallery"><img src="style/folder.png" width="16" height="16"/></a>
    <a title="browse folder" href="'.htmlspecialchars($file).'/gallery">'.$file.'</a>
  </div>
</div>'."\n";
    }
    print '
</div>
<div class="clear"></div>';
}



?>
