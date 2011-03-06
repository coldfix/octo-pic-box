<?php
include('common.php');
$page['title'] = 'File index';
include('header.php');

logToFile("index");
$filelist = list_files();
?>

<a href="gallery.php">Go to Gallery</a>

<h1>Download</h1>

<h2>Pictures</h2>
<table class="filelist">
<?php

foreach ($filelist['image'] as $file) {
    print '
<tr>
  <td class="filesize">'.get_filesize(filesize($files.$file)).'</td>
  <td class="filename">
    <a href="view.php?file='.htmlspecialchars($file).'">'.$file.'</a>
  </td>
</tr>';
}
?>
</table>

<h2>Other files</h2>
<table class="filelist">
<?php
foreach ($filelist['normal'] as $file) {
    print '
<tr>
  <td class="filesize">'.get_filesize(filesize($files.$file)).'</td>
  <td class="filename">
    <a href="download.php?file='.htmlspecialchars($file).'">'.$file.'</a>
  </td>';
}
?>
</table>

<h1>Upload</h1>
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

<?php
include('footer.php');
?>
