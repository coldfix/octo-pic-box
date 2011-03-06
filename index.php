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
<div class="filelist">
<?php
foreach ($filelist['image'] as $file) {
    print '
<div class="file">
  <div class="size">'.get_filesize(filesize($files.$file)).'</div>
  <div class="name">
    <a href="view.php?file='.htmlspecialchars($file).'">'.$file.'</a>
  </div>
</div>';
}
?>
</div>
<div class="clear"></div>

<h2>Other files</h2>
<div class="filelist">
<?php
foreach ($filelist['normal'] as $file) {
    print '
<div class="file">
  <div class="size">'.get_filesize(filesize($files.$file)).'</div>
  <div class="name">
    <a href="download.php?file='.htmlspecialchars($file).'">'.$file.'</a>
  </div>
</div>';
}
?>
</div>
<div class="clear"></div>

<h1>Upload</h1>
<form action="upload.php" method="post" enctype="multipart/form-data">
<table>
 <tr>
  <td><input type="hidden" name="referer" value="index.php"/>File:</td>
  <td><input type="file" name="file" size="40"/></td>
  <td><input type="submit"/></td>
 </tr>
</table>
</form>

<?php
include('footer.php');
?>
