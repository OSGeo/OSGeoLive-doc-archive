<?php
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_wfs.php");
include_once(dirname(__FILE__)."/../extensions/JSON.php");

//db connection
$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);

$json = new Services_JSON();
$obj = $json->decode(stripslashes($_REQUEST['obj']));

//workflow:
switch($obj->action){
	case 'getServices':
		$obj->services = getServices($obj);
		sendOutput($obj);
	break;
	case 'getWfsConfData':
		$obj->wfsConf = getWfsConfData($obj->wfs);
		sendOutput($obj);
	break;
	case 'getAssignedGuis':
		$obj->assignedGuis = getAssignedGuis($obj);
		sendOutput($obj);
	break;
	case 'deleteSelectedConfs':
		deleteWfsConf($obj);
		sendOutput($obj);
	break;
	default:
		sendOutput("no action specified...");
}


/*
 * Get all services (ids and titles) where the current user is owner
 * 
 * @return mixed[] services the ids and titles of the services
 */
function getServices(){
	global $con;
	$services = array();
	$services['id'] = array();
	$services['title'] = array();
	$adm = new administration();
	$serviceList = $adm->getWfsByOwner(Mapbender::session()->get("mb_user_id"));
	if(count($serviceList) == 0){
		return false;	
	}
	$sql = "SELECT * FROM wfs WHERE wfs_id IN(".join(",",$serviceList).") ORDER BY wfs_title";
	$res = db_query($sql);
	while($row = db_fetch_array($res)){
		array_push($services['id'], $row['wfs_id']);
		array_push($services['title'], $row['wfs_title']);
	}
	return $services;
}

/*
 * Get all configurations of the selcted wfs if the current user is owner 
 * 
 * @return mixed[] 
 */
function getWfsConfData($wfsID){
	global $con;
	// re-check permission 
	$adm = new administration();
	$serviceList = $adm->getWfsByOwner(Mapbender::session()->get("mb_user_id"));
	if(in_array($wfsID, $serviceList)){
		$wfsConf = array();
		$wfsConf['id'] = array();
		$wfsConf['abstract'] = array();
		$sql = "SELECT * FROM wfs_conf WHERE fkey_wfs_id = $1 ORDER BY wfs_conf_abstract";
		$v = array($wfsID);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		$cnt = 0;
		while($row = db_fetch_array($res)){
			array_push($wfsConf['id'], $row['wfs_conf_id']);
			array_push($wfsConf['abstract'], $row['wfs_conf_abstract']);
			$cnt++;
		}
		if($cnt == 0){
			return false;	
		}
		else{
			return $wfsConf;
		}
	}
//	else if($wfsID==="gui_confs"){
//		$wfsConf = array();
//		$wfsConf['id'] = array();
//		$wfsConf['abstract'] = array();
//		$wfsConf['id'] = $adm->getWfsConfByPermission(Mapbender::session()->get("mb_user_id"));
//		$cnt = 0;
//		foreach($wfsConf['id'] as $wfscid){
//			$sql = "SELECT wfs_conf_abstract FROM wfs_conf WHERE wfs_conf_id = $1";
//			$v = array($wfscid);
//			$t = array('i');
//			$res = db_prep_query($sql,$v,$t);
//			while($row = db_fetch_array($res)){
//				array_push($wfsConf['abstract'], $row['wfs_conf_abstract']);
//			}	
//			$cnt++;
//		}
//		if($cnt == 0){
//			return false;	
//		}
//		else{
//			return $wfsConf;
//		}
//	}
	
}

/*
 * get all guis which are assigned to the selected wfs conf
 * @param 
 * @return mixed[] 
 */
function getAssignedGuis($obj){
	global $con;
	$guis = array();
	$wfsConf['id'] = array();
	$wfsConf['id'] = $obj->selectedConf;
	$confs = "";
	foreach($wfsConf['id'] as $wfsConfId){
		if($confs!=''){
			$confs .= ",";
		}
		$confs .= $wfsConfId;
	}

	if($confs === false){
		return false;	
	}
	$sql = "SELECT * FROM gui_wfs_conf WHERE fkey_wfs_conf_id IN (".$confs.")";
	$res = db_query($sql);
	if(!$res){
		$e = new mb_exception("Error: SQL: " . $sql . " -> WFS conf: " .$obj->selectedConf);
	}
	while($row = db_fetch_array($res)){
		array_push($guis, $row['fkey_gui_id']);
	}
	return $guis;
}

/*
 * deletes a WFS conf
 * 
 * @param object the un-encoded object 
 * @return boolean success
 */
function deleteWfsConf($obj){
	global $con;
	$wfsConf['id'] = array();
	$wfsConf['id'] = $obj->confs;
	foreach($wfsConf['id'] as $wfsConfId){
		$sql = "DELETE FROM gui_wfs_conf WHERE fkey_wfs_conf_id =$1";
		$v = array($wfsConfId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		
		$sql1 = "DELETE FROM wfs_conf WHERE wfs_conf_id = $1";
		$v1 = array($wfsConfId);
		$t1 = array('i');
		$res1 = db_prep_query($sql1,$v1,$t1);
	}
	$obj->success = true;
	return true;
}

/*
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


?>
