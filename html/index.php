<?php
require_once("intern.php");

if (empty($dirname)) {
    $page['title'] = 'File index';
    $page['heading'] = 'Download';
} else {
    $page['title'] = 'File index: ' . htmlspecialchars($dirname);
    $page['heading'] = 'Download: '. htmlspecialchars($dirname);
}
require("$intern/header.php");

$filelist = list_files();
?>

<a href="<?= content('.','gallery') ?>">Go to Gallery</a> |
<a href="<?= content('.','index') ?>">Reload</a>



<h1><?= $page['heading'] ?></h1>


<h2>Pictures</h2>
<table class="filelist">
<?php
foreach ($filelist['image'] as $file) {
    $size = filesize($files.$file);
    print
'<tr class="file">
  <td class="size">'.get_filesize($size).'</td>
  <td class="unit">'.get_filesize_unit($size).'</td>
  <td class="action">
    <a title="download image" href="'.content($file,'download').'"><img src="'.uri('style/save.png').'" width="16" height="16" alt="save"/></a>
  </td>
  <td class="name">
    <a title="view image" href="'.content($file,'view').'">'.$file.'</a>
  </td>
</tr>'."\n";
}
?>
</table>


<h2>Other files</h2>
<table class="filelist">
<?php
foreach ($filelist['normal'] as $file) {
    $size = filesize($files.$file);
    $hl = is_highlightable($file);
    print
'<tr class="file">
  <td class="size">'.get_filesize($size).'</td>
  <td class="unit">'.get_filesize_unit($size).'</td>
  <td class="action">';
    if ($hl)
      print '
    <a title="highlight file" href="'.content($file,'view-src').'"><img src="'.uri('style/highlight.png').'" width="16" height="16" alt="source"/></a>';
    print '
    <a title="view file" href="'.content($file,'view').'"><img src="'.uri('style/view.png').'" width="16" height="16" alt="view"/></a>
  </td>
  <td class="name">
    <a title="download file" href="'.content($file,'download').'">'.$file.'</a>
  </td>
</tr>'."\n";
}
?>
</table>


<h2>Folders</h2>
<table class="filelist">
<?php
if (!empty($dirname))
    array_unshift($filelist['folder'], '..');
    // $filelist['folder'][] = '..';
array_unshift($filelist['folder'], '.');

foreach ($filelist['folder'] as $file) {
    $size = count_items($file);
    print
'<tr class="file">
  <td class="size">'.$size.'</td>
  <td class="unit">Items</td>
  <td class="action">
    <a title="browse folder" href="'.content($file,'index').'"><img src="'.uri('style/folder.png').'" width="16" height="16" alt="browse"/></a>
  </td>
  <td class="name">
    <a title="browse folder" href="'.content($file,'index').'">'.$file.'</a>
  </td>
</tr>'."\n";
}
?>
</table>


<?php
if (is_writeable(rtrim($files,'/'))) {
?>

<h1>Upload</h1>
<form action="<?= content('.','upload') ?>" method="post" enctype="multipart/form-data" accept-charset="utf-8">
<table>
 <tr>
  <td><input type="hidden" name="referer" value="<?= content('.', 'index') ?>"/>File:</td>
  <td><input type="file" name="file" size="40"/></td>
  <td><input type="submit"/></td>
 </tr>
</table>
</form>

<?php
}
else
{
?>

<div class="gray">
<h1>Upload</h1>
Write protected directory.
</div>

<?php
}

require("$intern/footer.php");
?>
