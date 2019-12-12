<?php
require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");
require_once(dirname(__FILE__) . "/../classes/class_tou.php");

//ajax wrapper for class_tou.php

$ajaxResponse = new AjaxResponse($_POST);
$json = new Mapbender_JSON();
$touObject = new tou();

//$currentUser = new User();
//$wmc = new wmc();

$resultObj = array();
//obj structure in session for acceptedTou (see class_tou.php):
//acceptedTou {
//		wms [100,101,112],
//		wfs [12,34]
//		}
switch ($ajaxResponse->getMethod()) {

	case 'checkAcceptedTou':
		$result = $touObject->check($ajaxResponse->getParameter("serviceType"),$ajaxResponse->getParameter("serviceId"));
		$ajaxResponse->setResult($result['accepted']); //1 or 0
		$ajaxResponse->setMessage(_mb($result['message']));
		$ajaxResponse->setSuccess(true);
		break;
	case 'setAcceptedTou':
		$serviceType = $ajaxResponse->getParameter("serviceType");
		$serviceId = $ajaxResponse->getParameter("serviceId");
		$result = $touObject->set($ajaxResponse->getParameter("serviceType"),$ajaxResponse->getParameter("serviceId"));	
		$ajaxResponse->setResult($result['setTou']); //1 or 0
		$ajaxResponse->setMessage(_mb($result['message']));
		$ajaxResponse->setSuccess(true);
		break;
	// Invalid command
	default:
		$ajaxResponse->setMessage(_mb("No method specified."));
		$ajaxResponse->setSuccess(false);		
}

$ajaxResponse->send();
?>
