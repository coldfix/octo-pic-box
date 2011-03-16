<?php
$intern = "../intern";
require_once("$intern/common.php");
$page['title'] = 'Gallery';
require("$intern/header.php");

logToFile("gallery");
$filelist = list_files();
?>

<a href="./">Go to Index</a>

<h1>Gallery</h1>

<?php
foreach ($filelist['image'] as $file) {
    list($w, $h) = array_values(thumb_size($file));
    print '
<div style="display: inline-block; margin: 4px; border: 1px solid grey; text-align: center; width: '.$w.'px; height: '.$h.'px;">
 <a href="view/'.htmlspecialchars($file).'">
  <img width="'.$w.'" height="'.$h.'" src="thumb/'.htmlspecialchars($file).'" alt="'.htmlspecialchars($file).'"/>
 </a>
</div>';
}
?>


<h1>Upload</h1>
<form action="upload" method="post" enctype="multipart/form-data">
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