<?php
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_stripRequest.php");
require_once(dirname(__FILE__)."/../classes/class_weldMaps2JPEG.php");

$ajaxResponse  = new AjaxResponse($_REQUEST);

$wmcId =  $ajaxResponse->getParameter("wmcId");
$mapUrls = $ajaxResponse->getParameter("mapUrls");

if (!$wmcId) {
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage('wmcId not set');
	$ajaxResponse->send();
}

if(!$mapUrls){
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage('mapURLs not set');
	$ajaxResponse->send();
}

switch ($ajaxResponse->getMethod()) {
	case "saveWmcPreview":
		//check if all urls have been send - sometimes only false is send - then delete this entry!
		//loop
		$mapUrlsNew = array();
		$problemUrls = array();
		for($i=0; $i<count($mapUrls); $i++){
			if ($mapUrls[$i] != 'false') {
				$mapUrlsNew[] = $mapUrls[$i];
			} else {
				$problemUrls[] = $i;
			}
		}
		$listOfProblemUrls = implode($problemUrls,",");
		$mapUrls = $mapUrlsNew;			
		$img = new weldMaps2JPEG(implode("___",$mapUrls), PREVIEW_DIR."/".$wmcId."_wmc_preview.jpg");
		if(!$img) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb('Preview could not be created'));
			$ajaxResponse->send();
		} 
		else {
			if (count($problemUrls) > 0) {
				$ajaxResponse->setSuccess(true);
				$ajaxResponse->setMessage(_mb('Preview saved'));
				$ajaxResponse->send();
			} else {
				$ajaxResponse->setSuccess(true);
				$ajaxResponse->setMessage(_mb('Preview saved - but following service urls are not included cause the firewall prevent this!').": ".$listOfProblemUrls);
				$ajaxResponse->send();
			}
		}

		break;

	default:
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("invalid method"));
}
$ajaxResponse->send();
?>
