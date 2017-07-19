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

  <div style="float: right">
    <a href="https://validator.w3.org/check?uri=referer"><img
      src="https://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a>
  </div>
  <div style="float:right">
    <a href="https://jigsaw.w3.org/css-validator/check/referer">
        <img style="border:0;width:88px;height:31px"
            src="https://jigsaw.w3.org/css-validator/images/vcss-blue"
            alt="Valid CSS!" />
    </a>
  </div>


