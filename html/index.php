<?php
require_once("intern.php");
require_once("$intern/common.php");

if (empty($directory)) {
    $page['title'] = 'File index: ' . htmlspecialchars($directory);
    $page['heading'] = 'Download: '. htmlspecialchars($directory);
} else {
    $page['title'] = 'File index';
    $page['heading'] = 'Download';
}
require("$intern/header.php");

logToFile("index");
$filelist = list_files();
?>

<a href="gallery">Go to Gallery</a>

<h1><?= $page['heading'] ?></h1>


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
    <a title="download image" href="'.htmlspecialchars($file).'/file"><img src="style/save.png" width="16" height="16"/></a>
    <a title="view image" href="'.htmlspecialchars($file).'/view">'.$file.'</a>
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
    <a title="view file" href="'.htmlspecialchars($file).'/view"><img src="style/view.png" width="16" height="16"/></a>
    <a title="download file" href="'.htmlspecialchars($file).'/file">'.$file.'</a>
  </div>
</div>'."\n";
}
?>
</div>
<div class="clear"></div>


<h2>Subfolders</h2>
<div class="filelist">
<?php
if (!empty($directory))
    $filelist['folder'][] = '..';
foreach ($filelist['folder'] as $file) {
    $size = count_items($file);
    print
'<div class="file">
  <div class="size">'.$size.'</div>
  <div class="unit">Items</div>
  <div class="name">
    <a title="browse folder" href="'.htmlspecialchars($file).'/"><img src="style/folder.png" width="16" height="16"/></a>
    <a title="browse folder" href="'.htmlspecialchars($file).'/">'.$file.'</a>
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
