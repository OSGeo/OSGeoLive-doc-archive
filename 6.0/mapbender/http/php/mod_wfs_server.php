<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_wfs.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");
require_once(dirname(__FILE__) . "/../classes/class_universal_wfs_factory.php");

$json = new Mapbender_JSON();
$obj = $json->decode($_REQUEST['obj']);

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
	case 'getGuis':
		$obj->id = getGuis($obj);
		sendOutput($obj);
	break;
	case 'getAssignedConfs':
		$obj->assignedConfs = getAssignedConfs($obj);
		sendOutput($obj);
	break;
	case 'getUpdateUrl':
		$obj->url = getUpdateUrl($obj);
		sendOutput($obj);
	break;
		case 'add':
		addConfsToGui($obj);
		sendOutput($obj);
	break;
	case 'remove':
		removeConfsFromGui($obj);
		sendOutput($obj);
	break;
	case 'updateWfs':
		updateWfs($obj);
		sendOutput($obj);
	break;
	case 'deleteWfs':
		deleteWfs($obj);
		sendOutput($obj);
	break;
	case 'setOwsproxy':
		$ows = array();
		$ows['string'] = setOwsproxy($obj);
		$ows['action'] = "owsproxy";
		sendOutput($ows);
	break;
	case 'removeOwsproxy':
		$ows = array();
		$ows['string'] = removeOwsproxy($obj);
		$ows['action'] = "owsproxy";
		sendOutput($ows);
	break;
	case 'getOwsproxy':
		$ows = array();
		$ows['string'] = getOwsproxy($obj);
		$ows['action'] = "owsproxy";
		sendOutput($ows);
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
	$sql = "SELECT * FROM wfs WHERE wfs_id IN (";
	$v = $serviceList;
	$t = array();
	for ($i = 1; $i <= count($serviceList); $i++) {
		if ($i > 1) {
			$sql .= ", ";
		}
		$sql .= "$" . $i;
		array_push($t, "i");
	}
	$sql .= ") ORDER BY wfs_title";
	$res = db_prep_query($sql, $v, $t);
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
	else if($wfsID==="gui_confs"){
		$wfsConf = array();
		$wfsConf['id'] = array();
		$wfsConf['abstract'] = array();
		$wfsConf['id'] = $adm->getWfsConfByPermission(Mapbender::session()->get("mb_user_id"));
		$cnt = 0;
		foreach($wfsConf['id'] as $wfscid){
			$sql = "SELECT wfs_conf_abstract FROM wfs_conf WHERE wfs_conf_id = $1";
			$v = array($wfscid);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);
			while($row = db_fetch_array($res)){
				array_push($wfsConf['abstract'], $row['wfs_conf_abstract']);
			}
			$cnt++;
		}
		if($cnt == 0){
			return false;
		}
		else{
			return $wfsConf;
		}
	}
}
/*
 * Get all GUIs where the current user is owner. This are the GUIs where the user could publish his
 * wfs configurations
 *
 * @return mixed[]
 */
function getGuis(){
	$adm = new administration();
	$guiList = $adm->getGuisByOwner(Mapbender::session()->get("mb_user_id"),1);
	if(count($guiList) > 0){
		return $guiList;
	}
	return false;
}


/*
 * get all wfs_confs of the selected WFS which are assigned to the selected gui
 * @param
 * @return mixed[]
 */
function getAssignedConfs($obj){
	global $con;
	$assignedConfs = array();
	$confs = getWfsConfData($obj->selectedWfs);
	if($confs === false || is_null($confs)){
		return false;
	}
	$sql = "SELECT * FROM gui_wfs_conf WHERE fkey_gui_id = $1 AND fkey_wfs_conf_id IN (".join(",",$confs['id']).")";
	$v = array($obj->selectedGui);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
	if(!$res){
		$e = new mb_exception("Error: SQL: " . $sql . " -> Gui: " .$obj->selectedGui);
	}
	while($row = db_fetch_array($res)){
		array_push($assignedConfs, $row['fkey_wfs_conf_id']);
	}
	return $assignedConfs;
}


function addConfsToGui($obj){
	global $con;
	for($i=0; $i<count($obj->confs); $i++){
		$sql = "SELECT * FROM gui_wfs_conf WHERE fkey_gui_id = $1 AND fkey_wfs_conf_id = $2";
		$v = array($obj->gui,$obj->confs->$i);
		$t = array('s','i');
		$res = db_prep_query($sql,$v,$t);
		if(!$row = db_fetch_array($res)){
			$sql1 = "INSERT INTO gui_wfs_conf (fkey_gui_id,fkey_wfs_conf_id) VALUES ($1,$2)";
			$v1 = array($obj->gui, $obj->confs->$i);
			$t1 = array('s', 'i');
			$res1 = db_prep_query($sql1,$v1,$t1);
		}
	}
}

function removeConfsFromGui($obj){
	global $con;
	for($i=0; $i<count($obj->confs); $i++){
		$sql = "DELETE FROM gui_wfs_conf  WHERE fkey_gui_id = $1 AND fkey_wfs_conf_id = $2";
		$v = array($obj->gui, $obj->confs->$i);
		$t = array('s', 'i');
		$res = db_prep_query($sql,$v,$t);
	}
}
/*
 * updates an WFS
 *
 * @param object the un-encoded object
 * @return boolean success
 */
function updateWfs($obj){
	$id = $obj->wfs;
	$url = $obj->url;

	$wfsFactory = new UniversalWfsFactory();
	$myWfs = $wfsFactory->createFromUrl($url);
	$myWfs->id = $id;

	if(is_null($myWfs) || !$myWfs->update()){
		$obj->success = false;
	}
	else {
		$obj->success = true;
	}
	return true;
}
/*
 * deletes an WFS
 *
 * @param object the un-encoded object
 * @return boolean success
 */
function deleteWfs($obj){
	$id = $obj->wfs;
	
	$wfsFactory = new UniversalWfsFactory();
	$myWfs = $wfsFactory->createFromDb($id);
	if (is_null($myWfs) || !$myWfs->delete()) {
		$obj->success = false;
	}
	else {
		$obj->success = true;
	}
	return true;
}

/*
 * gets the specified url column from db
 *
 * @param object the un-encoded object
 * @return string requested url
 */

function getUpdateUrl($obj){
	global $con;
	$sql = "SELECT * FROM wfs WHERE wfs_id = $1;";
	$v = array($obj->wfs);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	while($row = db_fetch_array($res)){
		$ContentOfColumn = $row[$obj->column];
		if($obj->column == 'wfs_getcapabilities'){
			$n = new administration();
			$updateUrl = $n->checkURL($ContentOfColumn)."VERSION=".$row['wfs_version']."&REQUEST=GetCapabilities&SERVICE=WFS";
		}
		else{
			$updateUrl = $ContentOfColumn;
		}
			
		return $updateUrl;
	}
	return "";
}

function getOwsproxy($obj){
	$n = new administration();
	if($obj->wfs=="gui_confs")
		return false;
	return $n->getWfsOwsproxyString($obj->wfs);
}
function setOwsproxy($obj){
	$n = new administration();
	if($obj->wfs=="gui_confs")
		return false;
	return $n->setWfsOwsproxyString($obj->wfs,true);
}
function removeOwsproxy($obj){
	$n = new administration();
	if($obj->wfs=="gui_confs")
		return false;
	return $n->setWfsOwsproxyString($obj->wfs,false);
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
