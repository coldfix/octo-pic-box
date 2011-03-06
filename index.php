<a href="gallery.php">Go to Gallery</a>
<h2>Download</h2>

<?php
include('common.php');

logToFile("index");

$dir = opendir($files);
while ($file = readdir($dir)) {
    if (is_public_file($file))
        print '
<a href="download.php?file='.htmlspecialchars($file).'">'.$file.'</a> ('.get_filesize(filesize($files.$file)).')<br/>';
}

?>

<h2>Upload</h2>
<form action="upload.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="referer" value="index.php">
<table>
 <tr>
  <td>File:</td>
  <td><input type="file" name="file" size="40"></td>
  <td><input type="submit"></td>
 </tr>
</table>
</form>

