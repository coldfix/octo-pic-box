<?php

$root = '/data/development/httproot/view/';

function logToFile($msg)
{
    $fd = fopen(".log", "a");
    fwrite($fd, "[" . date("Y/m/d h:i:s", mktime()) . "] <". $_SERVER['REMOTE_ADDR']."> (".$_SERVER['REMOTE_HOST'].") ". $msg . "\n");
    fclose($fd);
}

function get_filesize ($dsize) {
    if (strlen($dsize) <= 9 && strlen($dsize) >= 7) {
        $dsize = number_format($dsize / (1024 * 1024),2);
        return "$dsize MiB";
    } elseif (strlen($dsize) >= 10) {
        $dsize = number_format($dsize / (1024 * 1024 * 1024),2);
        return "$dsize GiB";
    } else {
        $dsize = number_format($dsize / 1024,2);
        return "$dsize KiB";
    }
}

function is_public_file ($file)
{
    return strpos($file, '.') !== 0 && $file != 'index.php';
}



function create_thumb($image, $thumb, $thumb_width, $thumb_height)
{
    $info = pathinfo($image);
    $ext = strtolower($info['extension']);

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
    else if ($orig_width / $orig_height < $thumb_width / $thumb_height) {
		$thumb_width = $thumb_height * $orig_width / $orig_height;
    else { // if ($orig_$width / $orig_height == $thumb_width / $thumb_height) {
	}

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






if (isset($_POST['upload']))
{
    if (!is_public_file($_FILES['file']['name'])) {
        logToFile("upload denied: " . $_FILES['file']['name']);
		echo "upload denied: ".$_FILES['file']['name'];
    }
    else if (move_uploaded_file($_FILES['file']['tmp_name'], $root.$_FILES['file']['name']) ) {
        logToFile("upload: " . $_FILES['file']['name']);
		echo "upload complete: ".$_FILES['file']['name'];
    }
	else {
        logToFile("upload failed: " . $_FILES['file']['name']);
		echo "upload failed: ".$_FILES['file']['name'];
    }

    echo '<br/>
        <a href="index.php">Return now</a>';
}

else if (isset($_GET['file'])) {
    $file = basename($_GET['file']);

    if (!is_public_file($file) || !file_exists($file)) {
        logToFile("denied download: " . $file);
        die("Download of '$file' not allowed.");
    }
    logToFile("download: " . $file);

    $size = getimagesize($file);
	http_send_content_disposition($file, true);
    http_send_content_type($size['mime']);
	http_throttle(0.0, 40960);				// no delay currently
	http_send_file($file);
}

else {
    logToFile("view");

	echo "<h2>View</h2>\n"
		. "\n";

    $dir = opendir($root);
    while ($file = readdir($dir)) {
        if (is_public_file($file))
            print '
    <a href="?file='.htmlspecialchars($file).'">'.$file.'</a> ('.get_filesize(filesize($root.$file)).')<br/>';
    }

?>

<h2>Upload</h2>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="upload" value="1">
<table>
 <tr>
  <td>File:</td>
  <td><input type="file" name="file" size="40"></td>
  <td><input type="submit"></td>
 </tr>
</table>
</form>

<?php

}

?>
