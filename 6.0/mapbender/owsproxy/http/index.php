<?php
# $Id: index.php 8219 2011-11-30 09:58:53Z verenadiewald $
# http://www.mapbender.org/index.php/Owsproxy
# Module maintainer Uli
# Copyright (C) 2002 CCGIS 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

require(dirname(__FILE__) . "/../../conf/mapbender.conf");
require(dirname(__FILE__) . "/../../http/classes/class_administration.php");
require(dirname(__FILE__) . "/../../http/classes/class_connector.php");
require_once(dirname(__FILE__) . "/../../http/classes/class_mb_exception.php");
require(dirname(__FILE__) . "/./classes/class_QueryHandler.php");

/***** conf *****/
$imageformats = array("image/png","image/gif","image/jpeg", "image/jpg");
$width = 400;
$height = 400;
/***** conf *****/

$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);

$postdata = $HTTP_RAW_POST_DATA;

$owsproxyService = $_REQUEST['wms']; //ToDo: change this to 'service' in the apache url-rewriting
$query = new QueryHandler();

// an array with keys and values toLoserCase -> caseinsensitiv
$reqParams = $query->getRequestParams();

$notice = new mb_notice("owsproxy id:".$query->getOwsproxyServiceId());

// check session
session_regenerate_id();
session_destroy();
session_id($_REQUEST["sid"]);
session_start();
if(!$_SESSION['mb_user_id']){
	$notice = new mb_notice("Permission denied");
	throwE("Permission denied");
	die();
}
$n = new administration;
//if($_SESSION['mb_user_ip'] != $_SERVER['REMOTE_ADDR']){
//	throwE(array("No session data available.","Permission denied.","Please authenticate."));
//	die();	
//}

$wmsId = $n->getWmsIdFromOwsproxyString($query->getOwsproxyServiceId());
#$notice = new mb_notice("wmsid:".$wmsId);
//get authentication infos if they are available in wms table! if not $auth = false
$auth = $n->getAuthInfoOfWMS($wmsId);
#$mb_exception = new mb_exception("auth: ".$auth['username']);
if ($auth['auth_type']==''){
	unset($auth);
}

/*************  workflow ************/
$n = new administration();
switch (strtolower($reqParams['request'])) {
	case 'getcapabilities':
		$arrayOnlineresources = checkWmsPermission($query->getOwsproxyServiceId());
		$query->setOnlineResource($arrayOnlineresources['wms_getcapabilities']);
		$request = $query->getRequest();
		if(isset($auth)){
			getCapabilities($request,$auth);
			#$mb_exception = new mb_exception("auth: ".$auth['auth_type']);
		}
		else {
			getCapabilities($request);
		}
		break;
	case 'getfeatureinfo':
		$arrayOnlineresources = checkWmsPermission($query->getOwsproxyServiceId());
		$query->setOnlineResource($arrayOnlineresources['wms_getfeatureinfo']);
		$request = $query->getRequest();
		if(isset($auth)){
			getFeatureInfo($request,$auth);
		}
		else {
			getFeatureInfo($request);
		}
		break;
	case 'getmap':
		$arrayOnlineresources = checkWmsPermission($owsproxyService);
		$query->setOnlineResource($arrayOnlineresources['wms_getmap']);
		$layers = checkLayerPermission($arrayOnlineresources['wms_id'],$reqParams['layers']);
		if($layers===""){
			throwE("Permission denied");
			die();
		}
		$query->setParam("layers",urldecode($layers));//the decoding of layernames dont make problems - but not really good names will be requested also ;-)
		$request = $query->getRequest();
		#log proxy requests
		if($n->getWmsLogTag($arrayOnlineresources['wms_id'])==1) {
			#do log to db
			#TODO read out size of bbox and calculate price
		        #get price out of db
			$price=intval($n->getWmsPrice($arrayOnlineresources['wms_id']));
			$n->logWmsProxyRequest($arrayOnlineresources['wms_id'],$_SESSION['mb_user_id'],$request,$price);
		}
		if(isset($auth)){
#$mb_exception = new mb_exception("auth: ".$auth['auth_type']);
			getImage($request,$auth);
		}
		else {
			getImage($request);
		}
		break;
	case 'map':
		$arrayOnlineresources = checkWmsPermission($owsproxyService);
		$query->setOnlineResource($arrayOnlineresources['wms_getmap']);
		$layers = checkLayerPermission($arrayOnlineresources['wms_id'],$reqParams['layers']);
		if($layers===""){
			throwE("Permission denied");
			die();
		}
		$query->setParam("layers",urldecode($layers));
		$request = $query->getRequest();
		if(isset($auth)){
			getImage($url,$auth);
		}
		else {
			getImage($url);
		}
		break;	
	case 'getlegendgraphic':
		$url = getLegendUrl($query->getOwsproxyServiceId());
		if(isset($auth)){
			getImage($url,$auth);
		}
		else {
			getImage($url);
		}
		break;
	case 'external':
		getExternalRequest($query->getOwsproxyServiceId());
		break; 
	case 'getfeature':
		$arrayFeatures = array($reqParams['typename']);
		$arrayOnlineresources = checkWfsPermission($query->getOwsproxyServiceId(), $arrayFeatures);
		$query->setOnlineResource($arrayOnlineresources['wfs_getfeature']);
		$request = $query->getRequest();
		$request = stripslashes($request);
		getFeature($request);
		break;
	// case wfs transaction (because of raw POST the request param is empty)
	case '':
		$arrayFeatures = getWfsFeaturesFromTransaction($HTTP_RAW_POST_DATA);
		$arrayOnlineresources = checkWfsPermission($query->getOwsproxyServiceId(), $arrayFeatures);
		$query->setOnlineResource($arrayOnlineresources['wfs_transaction']);
		$request = $query->getRequest();
		doTransaction($request, $HTTP_RAW_POST_DATA);
		break;
	default:
		
}
/*********************************************************/
function throwE($e){
	global $reqParams, $imageformats;
	if(in_array($reqParams['format'],$imageformats)){
		throwImage($e);
	}
	else{
		throwText($e);	
	}
}

function throwImage($e){
	global $width,$height;
	$image = imagecreate($width,$height);
	$transparent = ImageColorAllocate($image,155,155,155); 
	ImageFilledRectangle($image,0,0,$width,$height,$transparent);
	imagecolortransparent($image, $transparent);
	$text_color = ImageColorAllocate ($image, 233, 14, 91);
	if (count($e) > 1){
		for($i=0; $i<count($e); $i++){
			ImageString ($image, 3, 5, $i*20, $e[$i], $text_color);
		}
	} else {
		ImageString ($image, 3, 5, $i*20, $e, $text_color);
	}
	responseImage($image);
}
function throwText($e){
	echo join(" ", $e);
}
function responseImage($im){
	global $reqParams;
	$format = $reqParams['format'];
	$format="image/gif";
	if($format == 'image/png'){header("Content-Type: image/png");}
	if($format == 'image/jpeg' || $format == 'image/jpg'){header("Content-Type: image/jpeg");}
	if($format == 'image/gif'){header("Content-Type: image/gif");}
 
	if($format == 'image/png'){imagepng($im);}
	if($format == 'image/jpeg' || $format == 'image/jpg'){imagejpeg($im);}
	if($format == 'image/gif'){imagegif($im);}	
}
function completeURL($url){
	global $reqParams;
	$mykeys = array_keys($reqParams);
	for($i=0; $i<count($mykeys);$i++){
		if($i > 0){ $url .= "&"; }
		$url .= $mykeys[$i]."=".urlencode($reqParams[$mykeys[$i]]);
	}
	return $url;
}

/**
 * fetch and returns an image to client
 * 
 * @param string the original url of the image to send
 */

function getImage($or){
	global $reqParams;
	header("Content-Type: ".$reqParams['format']);
	#log the image_requests to database
	#log the following to table mb_proxy_log
	#timestamp,user_id,getmaprequest,amount pixel,price - but do this only for wms to log - therefor first get log tag out of wms!
	#
	#
	if (func_num_args() == 2) { //new for HTTP Authentication
		$auth = func_get_arg(1);
		echo getDocumentContent($or,$auth);
	}
	else
	{
		echo getDocumentContent($or);
	}

}

/**
 * fetchs and returns the content of the FeatureInfo Response
 * 
 * @param string the url of the FeatureInfoRequest
 * @return string the content of the FeatureInfo document
 */
function getFeatureInfo($url){
	global $reqParams;
	//$e = new mb_notice("owsproxy: Try to fetch FeatureInfoRequest: ".$url);
	header("Content-Type: ".$reqParams['info_format']);
	
	if (func_num_args() == 2) { //new for HTTP Authentication
		$auth = func_get_arg(1);
		$content = getDocumentContent($url,$auth);
	}
	else {
		$content = getDocumentContent($url);
	}
	$content = matchUrls($content);
	echo $content;
}

/**
 * fetchs and returns the content of WFS GetFeature response
 * 
 * @param string the url of the GetFeature request
 * @return echo the content of the GetFeature document
 */
function getFeature($url){
	global $reqParams;
	
	header("Content-Type: ".$reqParams['info_format']);

	header("Content-Type: ".$info_format);
	$content = getDocumentContent($url);
	$content = matchUrls($content);
	echo $content;
}

/**
 * simulates a post request to host
 * 
 * @param string host to send the request to
 * @param string port of host to send the request to
 * @param string method to send data (should be "POST")
 * @param string path on host
 * @param string data to send to host
 * @return string hosts response
 */

function sendToHost($host,$port,$method,$path,$data){
	$buf = '';
    if (empty($method)) $method = 'POST';
    $method = mb_strtoupper($method);
    $fp = fsockopen($host, $port);
    fputs($fp, "$method $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
    fputs($fp,"Content-type: application/xml\r\n");
    fputs($fp, "Content-length: " . strlen($data) . "\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    if ($method == 'POST') fputs($fp, $data);
    while (!feof($fp)) $buf .= fgets($fp,4096);
    fclose($fp);
    return $buf;
}

/**
 * get wfs featurenames that are touched by a tansaction request defined in XML $data
 * 
 * @param string XML that contains the tansaction request
 * @return array array of touched feature names
 */

function getWfsFeaturesFromTransaction($data){
	new mb_notice("owsproxy.getWfsFeaturesFromTransaction.data: ".$data);
	if(!$data || $data == ""){
		return false;
	}
	$features = array();
	$values = NULL;
	$tags = NULL;
	$parser = xml_parser_create();
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	xml_parse_into_struct($parser,$data,$values,$tags);

	$code = xml_get_error_code ($parser);
	if ($code) {
		$line = xml_get_current_line_number($parser);
		$col = xml_get_current_column_number($parser);
		$mb_exception = new mb_exception("OWSPROXY invalid Tansaction XML: ".xml_error_string($code) .  " in line " . $line. " at character ". $col);
		die();
	}
	xml_parser_free($parser);
	
	$insert = false;
	$insertlevel = 0;
	foreach ($values as $element) {
		//features touched by insert
		if(strtoupper($element[tag]) == "WFS:INSERT" && $element[type] == "open"){
			$insert = true;
			$insertlevel = $element[level];
		}
		if($insert && $element[level] == $insertlevel + 1 && $element[type] == "open"){
			array_push($features, $element[tag]);
		}
		if(strtoupper($element[tag]) == "WFS:INSERT" && $element[type] == "close"){
			$insert = false;
		}
		//updated features
		if(strtoupper($element[tag]) == "WFS:UPDATE" && $element[type] == "open"){
			array_push($features, $element[attributes]["typeName"]);
		}
		//deleted features
		if(strtoupper($element[tag]) == "WFS:DELETE" && $element[type] == "open"){
			array_push($features, $element[attributes]["typeName"]);
		}
	}
	return $features;
}

/**
 * sends the data of WFS Transaction and echos the response
 * 
 *  @param string url to send the WFS Transaction to
 *  @param string WFS Transaction data
 */

function doTransaction($url, $data){
	$arURL = parse_url($url);
	$host = $arURL["host"];
	$port = $arURL["port"]; 
	if($port == '') $port = 80;	

	$path = $arURL["path"];
	$method = "POST";
	$result = sendToHost($host,$port,$method,html_entity_decode($path),$data);
	
	//delete header from result
	$result = mb_eregi_replace("^[^<]*", "", $result);
	$result = mb_eregi_replace("[^>]*$", "", $result);
	
	echo $result;
}

function matchUrls($content){
	if(!session_is_registered("owsproxyUrls")){
		$_SESSION["owsproxyUrls"] = array();
		$_SESSION["owsproxyUrls"]["id"] = array();
		$_SESSION["owsproxyUrls"]["url"] = array();
	}
	$pattern = "/[\"|\'](https*:\/\/[^\"|^\']*)[\"|\']/";
	preg_match_all($pattern,$content,$matches);
	for($i=0; $i<count($matches[1]); $i++){
		$req = $matches[1][$i];
		$notice = new mb_notice("owsproxy found URL ".$i.": ".$req);
		#$notice = new mb_notice("owsproxy id:".$req);
		$id = registerURL($req);
		$extReq = setExternalRequest($id);
		$notice = new mb_notice("MD5 URL ".$id." - external link: ".$extReq);
		$content = str_replace($req,$extReq,$content);
	}
	return $content;
}

function setExternalRequest($id){
	global $reqParams,$query;
//	$extReq = "http://".$_SESSION['HTTP_HOST'] ."/owsproxy/". $reqParams['sid'] ."/".$id."?request=external";
	$extReq = OWSPROXY ."/". $reqParams['sid'] ."/".$id."?request=external";
	return $extReq;
}
function getExternalRequest($id){
	for($i=0; $i<count($_SESSION["owsproxyUrls"]["url"]); $i++){
		if($id == $_SESSION["owsproxyUrls"]["id"][$i]){
			$cUrl = $_SESSION["owsproxyUrls"]["url"][$i];
			$query_string = removeOWSGetParams($_SERVER["QUERY_STRING"]);
			if($query_string != ''){
				$cUrl .= getConjunctionCharacter($cUrl).$query_string;
			}	
			$metainfo = get_headers($cUrl,1);
			// just for the stupid InternetExplorer
			header('Pragma: private');
			header('Cache-control: private, must-revalidate');
			
			header("Content-Type: ".$metainfo['Content-Type']);
			
			$content = getDocumentContent($cUrl);
			#$content = matchUrls($content);			
			echo $content; 
		}	
	} 
}
function removeOWSGetParams($query_string){
	$r = preg_replace("/.*request=external&/","",$query_string);
	#return $r;
	return "";
}
function getConjunctionCharacter($url){
	if(strpos($url,"?")){ 
		if(strpos($url,"?") == strlen($url)){ 
			$cchar = "";
		}else if(strpos($url,"&") == strlen($url)){
			$cchar = "";
		}else{
			$cchar = "&";
		}
	}
	if(strpos($url,"?") === false){
		$cchar = "?";
	} 
	return $cchar;  
}
function registerUrl($url){	
	if(!in_array($url,$_SESSION["owsproxyUrls"]["url"])){
		$id = md5($url);
		array_push($_SESSION["owsproxyUrls"]["url"],$url);
		array_push($_SESSION["owsproxyUrls"]["id"], $id);
	}
	else{
		for($i=0; $i<count($_SESSION["owsproxyUrls"]["url"]); $i++){
			if($url == $_SESSION["owsproxyUrls"]["url"][$i]){
				$id = $_SESSION["owsproxyUrls"]["id"][$i];
			}			
		}
	}
	return $id;
}
function getCapabilities($url){
	global $arrayOnlineresources;
	global $sid,$wms;
	$t = array(htmlentities($arrayOnlineresources["wms_getcapabilities"]),htmlentities($arrayOnlineresources["wms_getmap"]),htmlentities($arrayOnlineresources["wms_getfeatureinfo"]));
	$new = OWSPROXY ."/". $sid ."/".$wms."?";
	$r = str_replace($t,$new,$arrayOnlineresources["wms_getcapabilities_doc"]);
	header("Content-Type: application/xml");
	echo $r;
}

/**
 * gets the original url of the requested legend graphic
 * 
 * @param string owsproxy md5
 * @return string url to legend graphic
 */
function getLegendUrl($wms){
	global $reqParams;
	
	//get wms id
	$sql = "SELECT * FROM wms WHERE wms_owsproxy = $1";
	$v = array($wms);
	$t = array("s");
	$res = db_prep_query($sql, $v, $t);	
	if($row = db_fetch_array($res))
		$wmsid = $row["wms_id"];
	else{
		throwE(array("No wms data available."));
		die();	
	}
	
	//get the url
	$sql = "SELECT layer_style.legendurl ";
	$sql .= "FROM layer_style JOIN layer ";
	$sql .= "ON layer_style.fkey_layer_id = layer.layer_id ";
	$sql .= "WHERE layer.layer_name = $2 AND layer.fkey_wms_id = $1 ";
	$sql .= "AND layer_style.name = $3 AND layer_style.legendurlformat = $4";
	
	$v = array($wmsid, $reqParams['layer'], $reqParams['style'], $reqParams['format']);
	$t = array("i", "s", "s", "s");
	
	$res = db_prep_query($sql, $v, $t);
	if($row = db_fetch_array($res))
		return $row["legendurl"];
	else{
		throwE(array("No legend available."));
		die();
	}
}
/**
 * validated access permission on requested wms
 * 
 * @param string OWSPROXY md5
 * @return array array with detailed information about requested wms
 */
function checkWmsPermission($wms){
	global $con, $n;
	$myguis = $n->getGuisByPermission($_SESSION["mb_user_id"],true);
	$mywms = $n->getWmsByOwnGuis($myguis);

	$sql = "SELECT * FROM wms WHERE wms_owsproxy = $1";
	$v = array($wms);
	$t = array("s");
	$res = db_prep_query($sql, $v, $t);
	$service = array();
	if($row = db_fetch_array($res)){
		$service["wms_id"] = $row["wms_id"];
		$service["wms_getcapabilities"] = $row["wms_getcapabilities"];	
		$service["wms_getmap"] = $row["wms_getmap"];
		$service["wms_getfeatureinfo"] = $row["wms_getfeatureinfo"];
		$service["wms_getcapabilities_doc"] = $row["wms_getcapabilities_doc"];
	}
	if(!$row || count($mywms) == 0){
		throwE(array("No wms data available."));
		die();	
	}
	
	if(!in_array($service["wms_id"], $mywms)){
		throwE(array("Permission denied."," -> ".$service["wms_id"], implode(",", $mywms)));
		die();
	}
	return $service;
}
/**
 * validates the access permission by getting the appropriate wfs_conf
 * to each feature requested and check the wfs_conf permission
 * 
 * @param string owsproxy md5
 * @param array array of requested featuretype names
 * @return array array with detailed information on reqested wfs
 */
function checkWfsPermission($wfsOws, $features){
	global $con, $n;
	$myconfs = $n->getWfsConfByPermission($_SESSION["mb_user_id"]);
	
	//check if we know the features requested
	if(count($features) == 0){
		throwE(array("No wfs_feature data available."));
		die();
	}
	
	//get wfs
	$sql = "SELECT * FROM wfs WHERE wfs_owsproxy = $1";
	$v = array($wfsOws);
	$t = array("s");
	$res = db_prep_query($sql, $v, $t);
	$service = array();
	if($row = db_fetch_array($res)){
		$service["wfs_id"] = $row["wfs_id"];
		$service["wfs_getcapabilities"] = $row["wfs_getcapabilities"];	
		$service["wfs_getfeature"] = $row["wfs_getfeature"];
		$service["wfs_describefeaturetype"] = $row["wfs_describefeaturetype"];
		$service["wfs_transaction"] = $row["wfs_transaction"];
		$service["wfs_getcapabilities_doc"] = $row["wfs_getcapabilities_doc"];
	}
	else{
		throwE(array("No wfs data available."));
		die();	
	}
	
	foreach($features as $feature){
	
		//get appropriate wfs_conf
		$sql = "SELECT wfs_conf.wfs_conf_id FROM wfs_conf ";
		$sql.= "JOIN wfs_featuretype ";
		$sql.= "ON wfs_featuretype.featuretype_id = wfs_conf.fkey_featuretype_id ";
		$sql.= "WHERE wfs_featuretype.featuretype_name = $2 ";
		$sql.= "AND wfs_featuretype.fkey_wfs_id = $1";
		$v = array($service["wfs_id"], $feature);
		$t = array("i","s");
		$res = db_prep_query($sql, $v, $t);
		if(!($row = db_fetch_array($res))){
			$notice = new mb_notice("Permissioncheck failed no wfs conf for wfs ".$service["wfs_id"]." with feturetype ".$feature);
			throwE(array("No wfs_conf data for featuretype ".$feature));
			die();	
		}
		$conf_id = $row["wfs_conf_id"];
		
		//check permission
		if(!in_array($conf_id, $myconfs)){
			$notice = new mb_notice("Permissioncheck failed:".$conf_id." not in ".implode(",", $myconfs));
			throwE(array("Permission denied."," -> ".$conf_id, implode(",", $myconfs)));
			die();
		}
	}

	return $service;
}

function checkLayerPermission($wms_id,$l){
	global $n, $owsproxyService;
//	$notice = new mb_notice("owsproxy: checkLayerpermission: wms: ".$wms_id.", layer: ".$l);
	$myl = split(",",$l);
	$r = array();
	foreach($myl as $mysl){
		if($n->getLayerPermission($wms_id, $mysl, $_SESSION["mb_user_id"]) === true){
			array_push($r, $mysl);
		}		
	}
	$ret = implode(",",$r);
	return $ret;
}
function getDocumentContent($url){
	if (func_num_args() == 2) { //new for HTTP Authentication
       	$auth = func_get_arg(1);
		$d = new connector($url, $auth);
	}
	else {
		$d = new connector($url);
	}

	return $d->file;
}
?>
