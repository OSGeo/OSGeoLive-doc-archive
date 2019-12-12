<?php
require_once(dirname(__FILE__) . "/../php/mb_validateSession.php");
require_once(dirname(__FILE__) . "/../classes/class_user.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");

/**
 * encodes and delivers the data
 * 
 * @param object the un-encoded object 
 */
function sendOutput($out){
	global $json;
	$output = $json->encode($out);
	header("Content-Type: text/x-json");
	echo $output;
}


$json = new Mapbender_JSON();
$queryObj = $json->decode($_REQUEST['queryObj']);
$resultObj = array();

$e = new mb_exception("command: " . $queryObj->command);

$userId = Mapbender::session()->get("mb_user_id");

switch($queryObj->command){

	// gets available WMCs
	case 'update':
		$elementArray = $queryObj->parameters->data;		
		for ($i = 0; $i < count($elementArray); $i++) {
			$currentElement = $elementArray[$i];
			$id = $currentElement->id;
			$top = $currentElement->top;
			$left = $currentElement->left;
			$width = $currentElement->width;
			$height = $currentElement->height;
			$app = $queryObj->parameters->applicationId;
			$sql = "UPDATE gui_element SET e_left = $1, e_top = $2, " .
					"e_width = $3, e_height = $4 " .  
					"WHERE e_id = $5 AND fkey_gui_id = $6"; 
			$v = array($left, $top, $width, $height, $id, $app);
			$t = array("i", "i", "i", "i", "s", "s");
			$res = db_prep_query($sql, $v, $t);
			$e = new mb_notice("updating element '" . $id . "'");
		}
		$resultObj["success"] = "Elements have been updated in the database.";
	break;
	

	// Invalid command
	default:
		$resultObj["error"] = "no action specified...";
}

sendOutput($resultObj);
?>