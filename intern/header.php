<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <title><?= $page["title"] ?></title>
 <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<?php
foreach ($page["css"] as $css)
	echo ' <link rel="stylesheet" href="'.uri($css).'" type="text/css" />';

echo '
 <link rel="shortcut icon" href="'.uri('favicon.ico').'" />'
?>
</head>
<body>
<div id="surface">
 <div id="page">
  <div id="content">


