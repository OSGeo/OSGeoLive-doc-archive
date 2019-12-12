<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");

$filename = basename($_GET["f"]);
if (!preg_match("/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9]+)$/", $filename)) {
	$errorMessage = _mb("Invalid filename.");
	echo htmlentities($errorMessage, ENT_QUOTES, CHARSET);
	$e = new mb_exception($errorMessage);
	die;
}

if (isset($filename) && $filename != "" && file_exists(TMPDIR."/".$filename)) {
	$filenameWithPath = TMPDIR."/".$filename;
	header("Pragma: private");
	header("Cache-control: private, must-revalidate");
	header("Content-Type: application/pdf");
	header("Content-Disposition: attachment; filename=\"".$filename."\"");	
	
    ob_clean();
    flush();
    readfile($filenameWithPath);
    exit;
}
else {
	echo "not found";
}
?>