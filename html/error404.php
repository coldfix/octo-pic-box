<?php
$intern = "../intern";
header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
// header("HTTP/1.0 404 Not Found");
header("Status: 404 Not Found");

require_once("$intern/common.php");
$page['title'] = '404 Not Found';
$page['css'] = array();
require("$intern/header.php");

?>

<h1>404 Not Found</h1>
<div>The requested URL <?= $_SERVER['REQUEST_URI'] ?> was not found on this server.</div>
<hr>
<address>Apache/2.2.16 (Ubuntu) Server at localhost Port 80</address>

<?php
include ("$intern/footer.php");
?>
