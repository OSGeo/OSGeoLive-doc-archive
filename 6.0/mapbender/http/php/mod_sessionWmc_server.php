<?php
require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_user.php");
require_once(dirname(__FILE__) . "/../classes/class_wmc.php");
require_once(dirname(__FILE__) . "/../classes/class_wmcToXml.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");
require_once(dirname(__FILE__) . "/../classes/class_administration.php");

$ajaxResponse = new AjaxResponse($_POST);
$json = new Mapbender_JSON();
$currentUser = new User();

$wmc = new wmc();

$resultObj = array();

switch ($ajaxResponse->getMethod()) {

	case 'checkConstraints':
		$resultObj = Mapbender::session()->get("wmcConstraints");
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;
	case 'deleteWmc':
		if (Mapbender::session()->exists("mb_wmc")) {
			Mapbender::session()->delete("mb_wmc");
			$ajaxResponse->setMessage(_mb("WMC in session reset."));
			$ajaxResponse->setSuccess(true);
		} else {
			$ajaxResponse->setMessage(_mb("No WMC in session found."));
			$ajaxResponse->setResult($resultObj);
			$ajaxResponse->setSuccess(false);
		}
		break;
	case 'updateWmc':
		if (
			!Mapbender::session()->exists("wmcGetApi") ||
			!is_a(Mapbender::session()->get("wmcGetApi"), "wmc")
		) {
			$ajaxResponse->setMessage(_mb("No WMC in session."));
			$ajaxResponse->setSuccess(true);
			break;
		}
		$wmc = Mapbender::session()->get("wmcGetApi");
		$skipWms = $ajaxResponse->getParameter("wmsIndices");
		$skipWms = is_array($skipWms) ? $skipWms : array();
		$js = $wmc->toJavaScript($skipWms);
		$resultObj = array(
			"js" => $js
		);
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;
	// Invalid command
	default:
		$ajaxResponse->setMessage(_mb("No method specified."));
		$ajaxResponse->setSuccess(false);		
}

$ajaxResponse->send();
?>
