<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");

$message = $_POST['text'];
$level = $_POST['level'];

if ($level == "warning") {
	$e = new mb_warning($message);
}
else if ($level == "notice") {
	$e = new mb_notice($message);
}
else if ($level == "error") {
	$e = new mb_exception($message);
}
else {
	$e = new mb_exception($message);
}
echo $type . " '" . $message . "' thrown.";
?>