<?php
$intern = "../intern";
require_once("$intern/common.php");
$page['title'] = 'File index';
require("$intern/header.php");

logToFile("index");
$filelist = list_files();
?>

<a href="gallery">Go to Gallery</a>

<h1>Download</h1>

<h2>Pictures</h2>
<div class="filelist">
<?php
foreach ($filelist['image'] as $file) {
    $size = filesize($files.$file);
    print
'<div class="file">
  <div class="size">'.get_filesize($size).'</div>
  <div class="unit">'.get_filesize_unit($size).'</div>
  <div class="name">
    <a title="download image" href="file/'.htmlspecialchars($file).'"><img src="style/save.png" width="16" height="16"/></a>
    <a title="view image" href="view/'.htmlspecialchars($file).'">'.$file.'</a>
  </div>
</div>'."\n";
}
?>
</div>
<div class="clear"></div>

<h2>Other files</h2>
<div class="filelist">
<?php
foreach ($filelist['normal'] as $file) {
    $size = filesize($files.$file);
    print
'<div class="file">
  <div class="size">'.get_filesize($size).'</div>
  <div class="unit">'.get_filesize_unit($size).'</div>
  <div class="name">
    <a title="view file" href="view/'.htmlspecialchars($file).'"><img src="style/view.png" width="16" height="16"/></a>
    <a title="download file" href="file/'.htmlspecialchars($file).'">'.$file.'</a>
  </div>
</div>'."\n";
}
?>
</div>
<div class="clear"></div>


<h1>Upload</h1>
<form action="upload" method="post" enctype="multipart/form-data">
<table>
 <tr>
  <td><input type="hidden" name="referer" value="./"/>File:</td>
  <td><input type="file" name="file" size="40"/></td>
  <td><input type="submit"/></td>
 </tr>
</table>
</form>

<?php
require("$intern/footer.php");
?>
