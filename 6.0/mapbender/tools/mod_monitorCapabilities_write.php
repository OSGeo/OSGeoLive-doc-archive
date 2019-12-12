<?php
require_once(dirname(__FILE__)."/../lib/class_Monitor.php");
require_once(dirname(__FILE__)."/../http/classes/class_mb_exception.php");
/*
 * incoming parameters from command line
 */
if ($_SERVER["argc"] != 3) {
	echo _mb("Insufficient arguments! Monitoring aborted.");
	$e = new mb_exception("Insufficient arguments! Monitoring aborted.");
	die;
}

$reportFile = $_SERVER["argv"][1];

$autoUpdate = intval($_SERVER["argv"][2]);

$monitor = new Monitor($reportFile, $autoUpdate, dirname(__FILE__)."/tmp/");


$monitor->updateInXMLReport();
?>
