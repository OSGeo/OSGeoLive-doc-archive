<?php 
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_conf.php");

$ajaxResponse = new AjaxResponse($_POST);

$myGui = $ajaxResponse->getParameter('gui');
$myWms = $ajaxResponse->getParameter('wms');
$myLayer = $ajaxResponse->getParameter('layer');
$myWfsConf = $ajaxResponse->getParameter('wfsConf');

try{
	switch($ajaxResponse->getMethod()) {
		case "getWfsConfs":
			$result = getWfsConfs($myGui);
			$ajaxResponse->setSuccess(true);
			$ajaxResponse->setResult($result);
		break;
		case "saveLayerWfsConnection":
			$result = saveLayerWfsConnection($myWfsConf, $myGui, $myLayer);
			if($result === true) {
				$ajaxResponse->setSuccess(true);
			}
			else {
				$ajaxResponse->setSuccess(false);
				$ajaxResponse->setMessage("An error occured performing UPDATE");
			}
		break;
		default:
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage("method invalid");
	}	
}
catch(Exception $E) {
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage("An error occured");
	$e = new mb_exception("mod_wfsLayerObj_conf.php: ".$E->getMessage());
}
$ajaxResponse->send();

function getWfsConfs($myGui) {
	$user = new User($_SESSION["mb_user_id"]);

	// get all WFS conf IDs for this application
	$availableWfsConfIds = $user->getWfsConfByPermission($myGui);
	
	$wfsConfObj = new WfsConf();
	$result = $wfsConfObj->load($availableWfsConfIds);	
	
	return $result;
}

function saveLayerWfsConnection($myWfsConf, $myGui, $myLayer) {
	$sql = "UPDATE gui_layer SET gui_layer_wfs_featuretype = $1 ";
	$sql .= "WHERE fkey_gui_id = $2 AND fkey_layer_id = $3";
    $v = array($myWfsConf,$myGui,$myLayer);
	$t = array('s','s','i');
	$res = db_prep_query($sql,$v,$t);
	
	if($res) {
		return true;
	}
	else {
		return false;
	}
}
?>