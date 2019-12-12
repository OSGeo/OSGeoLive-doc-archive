<?php

ob_start();

$download = array();

$download["dir"]  = "../tmp/";
$filename = basename(trim($_REQUEST["download"]));
if (!preg_match("/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9]+)$/", $filename) 
	|| !file_exists($download["dir"] . $filename)) {
	die("Invalid filename.");
}
$download["file"] = $download["dir"] . $filename;

if(!(bool)$download["file"]) {
	die("No filename given.");
}

/*
 * @security_patch fdl done
 * This allows filenames like ../../
 */
if(strpos($download["file"],"..") !== false) {
	die("Illegal filename given.");
}

if(!file_exists(implode($download)) || !is_readable(implode($download))) {
	die("An error occured.");
}

header("Pragma: private");
header("Cache-control: private, must-revalidate");
header("Content-Type: x-type/subtype");
header("Content-Disposition: attachment; filename=\"".$filename."\"");

readfile(implode($download));
?>