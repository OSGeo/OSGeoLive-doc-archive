<?php
require_once dirname(__FILE__) . "/../classes/class_json.php";

$json = new Mapbender_JSON();
$queryObj = $json->decode($_REQUEST['queryObj']);

if ($queryObj->sessionName && $queryObj->sessionId) {
	session_name($queryObj->sessionName);
	session_id($queryObj->sessionId);
	session_start();
	session_write_close();
}

require_once dirname(__FILE__) . "/../classes/class_user.php";
require_once dirname(__FILE__) . "/../classes/class_administration.php";

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

$resultObj = array();
$data = array();
$e = new mb_notice("command: " . $queryObj->command);

$user = new User();

switch($queryObj->command){

	case 'delete': 
		$applicationId = $queryObj->parameters->applicationId;

		// get all of the users applications
		$allowedApplicationArray = $user->getApplicationsByPermission(0);

		if (in_array($applicationId, $allowedApplicationArray)) {
			$sql = "DELETE FROM gui_treegde WHERE fkey_gui_id = $1";
			$v = array($applicationId);
			$t = array("s");
			$res = db_prep_query($sql, $v, $t);
			$resultObj["success"] = "Deletion successful. " . $sql . " (" . $applicationId . ")";
		}
		else {
			$resultObj["error"] = "Access denied to application " . $applicationId . ".";
		}
				
		break;
	case 'getApplications':
		// get all of the users applications
		$allowedApplicationArray = $user->getApplicationsByPermission(0);

		// get all of the users applications that contain treeGDE
		$sql = "SELECT DISTINCT gui_id FROM gui WHERE " . 
				"gui_id IN (";
		
		$v = array();
		$t = array();
		foreach ($allowedApplicationArray as $i => $application) {
			array_push($v, $application);
			array_push($t, "s");
			if ($i > 0) {
				$sql .= ",";
			}
			$sql .= "$" . ($i+1);
		}
		$sql .= ")";
		$res = db_prep_query($sql, $v, $t);
		$applicationArray = array();
		while ($row = db_fetch_array($res)) {
			array_push($applicationArray, $row[0]);
		}
		
		$data = array("applicationArray" => $applicationArray);
		$resultObj["data"] = $data;
		break;

	case 'getWmsByApplication':
		$applicationId = $queryObj->parameters->applicationId;

		// get all of the users applications
		$allowedApplicationArray = $user->getApplicationsByPermission(0);

		if (in_array($applicationId, $allowedApplicationArray)) {
			$sql = "SELECT b.wms_id AS id, b.wms_title AS title " . 
				"FROM gui_wms AS a, wms AS b " . 
				"WHERE a.fkey_wms_id = b.wms_id AND a.fkey_gui_id = $1";
			$v = array($applicationId);
			$t = array("s");
			$res = db_prep_query($sql,$v,$t);

			$wmsArray = array();
			while ($row = db_fetch_array($res)) {
				$wmsArray[$row["id"]] = $row["title"];
			}
			$data = array("wmsArray" => $wmsArray);
			$resultObj["data"] = $data;
			
		}
		else {
			$resultObj["data"] = array("wmsArray" => array());
		}		
		break;
	
	case 'getCustomTreeByApplication':
		$applicationId = $queryObj->parameters->applicationId;

		// get all of the users applications
		$allowedApplicationArray = $user->getApplicationsByPermission(0);

		if (in_array($applicationId, $allowedApplicationArray)) {
			$sql = "SELECT lft, rgt, my_layer_title, wms_id " . 
				"FROM gui_treegde " . 
				"WHERE fkey_gui_id = $1 ORDER BY lft";
			$v = array($applicationId);
			$t = array("s");
			$res = db_prep_query($sql,$v,$t);

			$nodeArray = array();
			
			//check if wms exists in gui
			$n = new administration();
			$applicationArray = array($applicationId);
			$mywms = $n->getWmsByOwnGuis($applicationArray);

			while ($row = db_fetch_array($res)) {
				
				$wmsIdArray = explode(",", $row["wms_id"]);
				$wmsArray = array();
				
				foreach ($wmsIdArray as $wmsId) {
					if (in_array($wmsId, $mywms)) {
						if (is_numeric($wmsId)) {
							$sqlWms = "SELECT wms_title FROM wms WHERE wms_id = $1";
							$vWms = array($wmsId);
							$tWms = array("i");
							$resWms = db_prep_query($sqlWms, $vWms, $tWms);
							$rowWms = db_fetch_array($resWms);
							$wmsArray[$wmsId] = $rowWms[0];
						}
					}
				}
				$currentNode = array(
					"left" => intval($row["lft"]),
					"right" => intval($row["rgt"]),
					"name" => $row["my_layer_title"],
					"wms" => $wmsArray
				);
				
				array_push($nodeArray, $currentNode);
			}
			$data = array("nodeArray" => $nodeArray);
			$resultObj["data"] = $data;
			
		}
		else {
			$resultObj["data"] = array("nodeArray" => array());
		}		
		break;

	case 'update':
		$elementArray = $queryObj->parameters->data->folderArray;		
		$applicationId = $queryObj->parameters->data->applicationId;		
		// get all of the users applications
		$allowedApplicationArray = $user->getApplicationsByPermission(0);

		if (in_array($applicationId, $allowedApplicationArray)) {

			$sql = "DELETE FROM gui_treegde WHERE fkey_gui_id = $1";
			$v = array($applicationId);
			$t = array("s");
			$res = db_prep_query($sql, $v, $t);
			
			$rowArray = array();
			for ($i = 0; $i < count($elementArray); $i++) {
		
				$currentElement = $elementArray[$i];
	
			$sql = "INSERT INTO gui_treegde (fkey_gui_id, lft, rgt, " . 
				"my_layer_title, wms_id) VALUES ($1, $2, $3, $4, $5)";
			$v = array(
				$applicationId, 
				$currentElement->left, 
				$currentElement->right, 
				$currentElement->name, 
				$currentElement->wms
			);
			$t = array("s", "i", "i", "s", "s");
			$res = db_prep_query($sql, $v, $t);
			$rowArray[]= $v;
		}
		$data = array("sql" => $sql, "data" => $rowArray);
		$resultObj["data"] = $data;
		$resultObj["success"] = "Elements have been updated in the database.";
		}
		break;
	

	// Invalid command
	default:
		$resultObj["error"] = "no action specified...";
}

sendOutput($resultObj);
?>
