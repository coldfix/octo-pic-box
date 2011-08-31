<?php
require_once("intern.php");

if (empty($directory)) {
    $page['title'] = 'Gallery: ' . htmlspecialchars($directory);
    $page['heading'] = 'Gallery: '. htmlspecialchars($directory);
} else {
    $page['title'] = 'Gallery';
    $page['heading'] = 'Gallery';
}
require("$intern/header.php");

logToFile("gallery");
$filelist = list_files();
?>

<a href="<?= content('.','index') ?>">Go to Index</a>

<h1><?= $page['heading'] ?></h1>

<?php
foreach ($filelist['image'] as $file) {
    list($w, $h) = array_values(thumb_size($file));
    print '
<div style="display: inline-block; margin: 4px; border: 1px solid grey; text-align: center; width: '.$w.'px; height: '.$h.'px;">
 <a href="'.content($file,'view').'">
  <img width="'.$w.'" height="'.$h.'" src="'.content($file,'thumb').'" alt="'.htmlspecialchars($file).'"/>
 </a>
</div>';
}
?>


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
    <a title="browse folder" href="'.content($file,'gallery').'"><img src="'.uri('style/folder.png').'" width="16" height="16"/></a>
    <a title="browse folder" href="'.content($file,'gallery').'">'.$file.'</a>
  </div>
</div>'."\n";
}
?>
</div>
<div class="clear"></div>


<h1>Upload</h1>
<form action="<?= content('.','upload') ?>" method="post" enctype="multipart/form-data">
<table>
 <tr>
  <td><input type="hidden" name="referer" value="gallery"/>File:</td>
  <td><input type="file" name="file" size="40"/></td>
  <td><input type="submit"/></td>
 </tr>
</table>
</form>

<?php
require("$intern/footer.php");
?>
