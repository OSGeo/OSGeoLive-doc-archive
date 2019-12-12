<?php

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");

$ajaxResponse = new AjaxResponse($_POST);

switch($ajaxResponse->getMethod())
{
	case "createWPSRequest":
		$templatefilePath = realpath(dirname(__FILE__)."/../../resources/wps_template.xml");
		$parameters = $ajaxResponse->getParameter('attributes');
		try{
			$result = createWPSRequest($parameters,$templatefilePath);
			$ajaxResponse->setSuccess(true);
			$ajaxResponse->setResult($result);
		}catch(Exception $E){
			$ajaxResponse->setSuccess(true);
			$ajaxResponse->setMessage($E->getMessage);
		}
	break;

	default:
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage("method invalid");
				
}
$ajaxResponse->send();

/*
* @params parameters an assoc array containing parameters with wich to create a WPS request
* @return a WPS XML document
*/
function createWPSRequest($parameters,$templatefilePath)
{
	// check and give parameters default values
	// need this collection to be subscriptable
	$parray['interpolationMethod'] = isset($parameters->interpolationMethod) ? $parameters->interpolationMethod : "automatic";
	$parray['calculationTime'] = isset($parameters->calculationTime) ? $parameters->calculationTime : "120000";
	$parray['predictionTypes'] = isset($parameters->predictionTypes) ? $parameters->predictionTypes : "Mean";
	$parray['propabilityLimit'] = isset($parameters->propabilityLimit) ? $parameters->propabilityLimit : "35i.4";
	$parray['featureCollectionURL'] = isset($parameters->featureCollectionURL) ? $parameters->featureCollectionURL : ""; //FIXME:
	$parray['wfsURL'] = isset($parameters->wfsURL) ? $parameters->wfsURL : ""; //FIXME
	$parray['featureType'] = isset($parameters->featureType) ? $parameters->featureType : ""; //FIXME
	$parray['time'] = isset($parameters->time) ? $parameters->time : date("c") ; //default to current time
	$parray['wpsURL'] = isset($parameters->wpsURL) ? $parameters->wpsURL : "";
	$parray['outlierDetection'] = isset($parameters->outlierDetection) ? $parameters->outlierDetection : "true";
	$parray['clipping'] = isset($parameters->clipping) ? $parameters->clipping : "true";
	$parray['colorschema'] = isset($parameters->colorschema) ? $parameters->colorschema : "";
	$parray['imageFormat'] = isset($parameters->imageFormat) ? $parameters->imageFormat : "image/jpeg";
	$parray['bboxSRS'] = isset($parameters->bboxSRS) ? $parameters->bboxSRS : "";
	$parray['bbox'] = isset($parameters->bbox) ? $parameters->bbox : "";
	$parray['width'] = isset($parameters->width) ? $parameters->width : "";
	$parray['height'] = isset($parameters->height) ? $parameters->height : "";

	try {
		$WMCDoc = DOMDocument::load($templatefilePath);
	} 
	catch (Exception $E) {
		new mb_exception("WMC XML is broken.");
		throw new Exception("Could not load WPS Template XML");
	}   
	if(!$WMCDoc){
		throw new Exception("Could not load WPS Template XML");
	}

	
	$xpath = new DOMXPath($WMCDoc);
	$xpath->registerNamespace("xlink","http://www.w3.org/1999/xlink");
	$xpath->registerNamespace("ows", "http://www.opengis.net/ows/1.1");
	$xpath->registerNamespace("wps","http://www.opengis.net/wps/1.0.0");

	$OWS_IdentifierList = $xpath->query("/wps:Execute/wps:DataInputs/wps:Input/ows:Identifier");
	$result = "";
	foreach($OWS_IdentifierList as  $OWS_Identifier)
	{
		//FIXME: this requires that our data is checked above
		$WPS_LiteralDataList = $xpath->query("../wps:Data/wps:LiteralData",$OWS_Identifier);
		$WPS_LiteralData = $WPS_LiteralDataList->item(0);
		$WPS_LiteralData->nodeValue = $parray[$OWS_Identifier->nodeValue];
	}

	$result = $WMCDoc->saveXML();
	return $result;
}

?>
