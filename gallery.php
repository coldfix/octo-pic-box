<a href="index.php">Go to Index</a></h2>
<h2>Gallery</h2>

<?php
include('common.php');

logToFile("gallery");

$dir = opendir($files);
while ($file = readdir($dir)) {
    if (!is_public_file($file) || !is_image_file($file))
        continue;
    update_thumb($file);

    $size = getimagesize($thumbs.$file);
    $w = $size[0];
    $h = $size[1];
    print '
<div style="display: inline-block; margin: 4px; border: 1px solid grey; text-align: center; width: '.$w.'px; height: '.$h.'px;">
 <a href="view.php?file='.htmlspecialchars($file).'">
  <img width="'.$w.'" height="'.$h.'" alt="" src="thumbs/'.htmlspecialchars($file).'" alt="'.htmlspecialchars($file).'"/>
 </a>
</div>';
}

?>

<h2>Upload</h2>
<form action="upload.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="referer" value="gallery.php">
<table>
 <tr>
  <td>File:</td>
  <td><input type="file" name="file" size="40"></td>
  <td><input type="submit"></td>
 </tr>
</table>
</form>

