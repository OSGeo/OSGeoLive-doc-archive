<?php

require_once dirname(__FILE__) . "/../php/mb_validateSession.php";
require_once dirname(__FILE__) . "/classes/factoryClasses.php";
require_once dirname(__FILE__) . "/../include/dyn_php.php";

$pf = new mbPdfFactory();

$confFile = basename($_REQUEST["printPDF_template"]);
if (!preg_match("/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9]+)$/", $confFile) || 
	!file_exists($_REQUEST["printPDF_template"])) {

	$errorMessage = _mb("Invalid configuration file");
	echo htmlentities($errorMessage, ENT_QUOTES, CHARSET);
	$e = new mb_exception($errorMessage);
	die;
}

$pdf = $pf->create($_REQUEST["printPDF_template"]);

//element vars of print
$pdf->unlinkFiles = $unlink;
$pdf->logRequests = $logRequests;
$pdf->logType = $logType;

try {
    $pdf->render();
	$pdf->save();
}
catch (Exception $e) {
	new mb_exception($e->getMessage());
	die($e->getMessage());
}

print $pdf->returnAbsoluteUrl();
?>
