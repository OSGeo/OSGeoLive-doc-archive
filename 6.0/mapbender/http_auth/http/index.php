<?php
require(dirname(__FILE__) . "/../../conf/mapbender.conf");
require(dirname(__FILE__) . "/../../http/classes/class_administration.php");
require(dirname(__FILE__) . "/../../http/classes/class_connector.php");
require_once(dirname(__FILE__) . "/../../http/classes/class_mb_exception.php");
require(dirname(__FILE__) . "/../../owsproxy/http/classes/class_QueryHandler.php");

//database connection
$db = db_connect($DBSERVER,$OWNER,$PW);
db_select_db(DB,$db);

$imageformats = array("image/png","image/gif","image/jpeg", "image/jpg");

//control if digest auth is set, if not set, generate the challenge with getNonce()
if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.REALM.
           '",qop="auth",nonce="'.getNonce().'",opaque="'.md5(REALM).'"');
    die('Text to send if user hits Cancel button');
}

//read out the header in an array
$requestHeaderArray = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);

//error if header could not be read
if (!($requestHeaderArray)) {
	echo 'Following Header information cannot be validated - check your clientsoftware!<br>';
	echo $_SERVER['PHP_AUTH_DIGEST'].'<br>';
	die();
}

//get mb_username and email out of http_auth username string
$userIdentification = explode(';',$requestHeaderArray['username']);
$mbUsername = $userIdentification[0];
$mbEmail = $userIdentification[1];

$userInformation = getUserInfo($mbUsername,$mbEmail);

if ($userInformation[0] == '-1') {
	die('User with name: '.$mbUsername.' and email: '.$mbEmail.' not known to security proxy!');
}

if ($userInformation[1]=='') { //check if digest exists in db - if no digest exists it should be a null string!
	die('User with name: '.$mbUsername.' and email: '.$mbEmail.' has no digest - please set a new password and try again!');
}

//first check the stale!
if($requestHeaderArray['nonce'] == getNonce()) {
                        // Up-to-date nonce received
                        $stale = false;
                    } else {
                        // Stale nonce received (probably more than x seconds old)
                        $stale = true;
			//give another chance to authenticate
    			header('HTTP/1.1 401 Unauthorized');
    			header('WWW-Authenticate: Digest realm="'.REALM.'",qop="auth",nonce="'.getNonce().'",opaque="'.md5(REALM).'" ,stale=true');	
                    }
// generate the valid response to check the request of the client
$A1 = $userInformation[1];
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$requestHeaderArray['uri']);
$valid_response = $A1.':'.getNonce().':'.$requestHeaderArray['nc'];
$valid_response .= ':'.$requestHeaderArray['cnonce'].':'.$requestHeaderArray['qop'].':'.$A2;

$valid_response=md5($valid_response);

if ($requestHeaderArray['response'] != $valid_response) {//the user have to authenticate new - cause something in the authentication went wrong
    die('Authentication failed - sorry, you have to authenticate once more!'); 
}
//if we are here - authentication has been done well!
//let's do the proxy things (came from owsproxy.php):
$postdata = $HTTP_RAW_POST_DATA;
$layerId = $_REQUEST['layer_id'];
$query = new QueryHandler();

// an array with keys and values toLoserCase -> caseinsensitiv
$reqParams = $query->getRequestParams();

$n = new administration();

$wmsId = getWmsIdByLayerId($layerId);
$owsproxyString = $n->getWMSOWSstring($wmsId);

if (!$owsproxyString) {
	die('The requested resource does not exists or the routing through mapbenders owsproxy is not activated!');
}
//get authentication infos if they are available in wms table! if not $auth = false
$auth = $n->getAuthInfoOfWMS($wmsId);

if ($auth['auth_type']==''){
	unset($auth);
}

$e = new mb_exception("REQUEST to HTTP_AUTH: ".strtolower($reqParams['request']));

//what the proxy does
switch (strtolower($reqParams['request'])) {

	case 'getcapabilities':
		$arrayOnlineresources = checkWmsPermission($wmsId,$userInformation[0]);
		$query->setOnlineResource($arrayOnlineresources['wms_getcapabilities']);
		//$request = preg_replace("/(.*)frames\/login.php/", "$1php/wms.php?layer_id=".$layerId, LOGIN);
		if (isset($_SERVER["HTTPS"])){
			$urlPrefix = "https://";
		} else {
			$urlPrefix = "http://";
		}
		$request = $urlPrefix.$_SERVER['HTTP_HOST']."/mapbender/php/wms.php?layer_id=".$layerId;
		$requestFull .= $request.'&REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS';
		if(isset($auth)){
			getCapabilities($request,$requestFull,$auth);
		}
		else {
			getCapabilities($request,$requestFull);
		}
		break;
	case 'getfeatureinfo':
		$arrayOnlineresources = checkWmsPermission($wmsId,$userInformation[0]);
		$query->setOnlineResource($arrayOnlineresources['wms_getfeatureinfo']);
		$layers = checkLayerPermission($wmsId,$reqParams['layers'],$userInformation[0]);
		if ($layers == '' ) {
		throwE("GetFeatureInfo permission denied on layer with id".$layerId);
		die();
		}
		$request = $query->getRequest();
		if(isset($auth)){
			getFeatureInfo($request,$auth);
		}
		else {
			getFeatureInfo($request);
		}
		break;
	case 'getmap':
		$arrayOnlineresources = checkWmsPermission($wmsId,$userInformation[0]);
		$query->setOnlineResource($arrayOnlineresources['wms_getmap']);
		$layers = checkLayerPermission($wmsId,$reqParams['layers'],$userInformation[0]);
		if ($layers == '' ) {
			throwE("GetMap permission denied on layer with id ".$layerId);
		die();
		}
		$query->setParam("layers",urldecode($layers));
		$request = $query->getRequest();
		#log proxy requests
		if($n->getWmsLogTag($wmsId)==1) {
			#do log to db
			#TODO read out size of bbox and calculate price
		        #get price out of db
			$price=intval($n->getWmsPrice($wmsId));
			$n->logWmsProxyRequest($wmsId,$userInformation[0],$request,$price);
		}
		if(isset($auth)){
			getImage($request,$auth);
		}
		else {
			getImage($request);
		}
		break;
	case 'getlegendgraphic':
		$url = getLegendUrl($wmsId);
		$e = new mb_exception("URL for getlegendgraphic: ");
		if(isset($auth)){	
			getImage($url,$auth);
		}
		else {
			getImage($url);
		}
		break;
	default:
echo 'Your are logged in as: <b>' .$requestHeaderArray['username'].'</b> and requested the layer with id=<b>'.$layerId.'</b> but your request is not a valid OWS request';
}
//functions for http_auth 
//**********************************************************************************************

// function to parse the http auth header
function http_digest_parse($txt)
{
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);	
    $data = array();
    $keys = implode('|', array_keys($needed_parts));
    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }
    return $needed_parts ? false : $data;
}
// function to get relevant user information from mb db
function getUserInfo($mbUsername,$mbEmail) {
	$result = array();
	$sql = "SELECT mb_user_id, mb_user_digest FROM mb_user where mb_user_name = $1 AND mb_user_email= $2";
	$v = array($mbUsername, $mbEmail);
	$t = array("s","s");
	$res = db_prep_query($sql, $v, $t);
	if(!($row = db_fetch_array($res))){
		$result[0] = "-1";
	}
	else {
		$result[0] = $row['mb_user_id'];
		$result[1] = $row['mb_user_digest'];
	}
	return $result;
}

function getNonce() {
	global $nonceLife;
	$time = ceil(time() / $nonceLife) * $nonceLife;
	return md5(date('Y-m-d H:i', $time).':'.$_SERVER['REMOTE_ADDR'].':'.NONCEKEY);
}

//**********************************************************************************************
//functions of owsproxy/http/index.php
//**********************************************************************************************
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
	global $reqParams;
	if (!$reqParams['width'] || !$reqParams['height']) { //width or height are not set by ows request - maybe for legendgraphics
		$width = 300;
		$height = 20;
	}
	$image = imagecreate($width,$height);
	$transparent = ImageColorAllocate($image,155,155,155); 
	ImageFilledRectangle($image,0,0,$width,$height,$transparent);
	imagecolortransparent($image, $transparent);
	$text_color = ImageColorAllocate ($image, 233, 14, 91);
	for($i=0; $i<count($e); $i++){
		ImageString ($image, 3, 5, $i*20, $e[$i], $text_color);
	}
	responseImage($image);
}
function throwText($e){
	echo join(" ", $e);
}
function responseImage($im){
	global $reqParams;
	$format = $reqParams['format'];
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
	$e = new mb_exception("owsproxy: Try to fetch FeatureInfoRequest: ".$url);
	header("Content-Type: ".$reqParams['info_format']);
	if (func_num_args() == 2) { //new for HTTP Authentication
		$auth = func_get_arg(1);
		echo getDocumentContent($url,$auth);
	}
	else
	{
		echo getDocumentContent($url);
	}
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
		$e = new mb_exception("Gefundene URL ".$i.": ".$req);
		#$notice = new mb_notice("owsproxy id:".$req);
		$id = registerURL($req);
		$extReq = setExternalRequest($id);
		$e = new mb_exception("MD5 URL ".$id."-Externer Link: ".$extReq);
		$content = str_replace($req,$extReq,$content);
	}
	return $content;
}

function setExternalRequest($id){
	global $reqParams,$query;
	$extReq = "http://".$_SESSION['HTTP_HOST'] ."/owsproxy/". $reqParams['sid'] ."/".$id."?request=external";
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
			
			$content = getDocumentContent($cUrl,false);
			#$content = matchUrls($content); //In the case of http_auth - this is not possible cause we cannot save them in the header - maybe we could create a special session to do so later on? 			
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
		$e = new mb_exception("Is noch net drin!");
		$id = md5($url);
		$e = new mb_exception("ID: ".$id."  URL: ".$url." will be written to session");	
		array_push($_SESSION["owsproxyUrls"]["url"],$url);
		array_push($_SESSION["owsproxyUrls"]["id"], $id);
	}
	else{
		$e = new mb_exception("It was found! Search content and return ID!");
		for($i=0; $i<count($_SESSION["owsproxyUrls"]["url"]); $i++){
		$e = new mb_exception("Content ".$i." : proxyurl:".$_SESSION["owsproxyUrls"]["url"][$i]." - new: ".$url);
		if($url == $_SESSION["owsproxyUrls"]["url"][$i]){
			$e = new mb_exception("Identical! ID:".$_SESSION["owsproxyUrls"]["id"][$i]." will be used");
			$id = $_SESSION["owsproxyUrls"]["id"][$i];
			}			
		}
	}
	return $id;
}

function getCapabilities($request,$requestFull){
	global $arrayOnlineresources;
	global $layerId;
	header("Content-Type: application/xml");
	if (func_num_args() == 3) { //new for HTTP Authentication
		$auth = func_get_arg(2);
		$content = getDocumentContent($requestFull,$auth);
	}
	else
	{
		$content = getDocumentContent($requestFull);
	}

	$new = "href=\"".HTTP_AUTH_PROXY ."/". $layerId."?";
        $pattern = "#href=\"".OWSPROXY."/[a-z0-9]{32}\/[a-z0-9]{32}\?#m";
	$content = preg_replace($pattern,$new,$content);

	#TODO: maybe do this by parsing xml rather then regexpr cause they are hungry ;-) - but fast 

	$new = "href=\"".HTTP_AUTH_PROXY ."/". $layerId."?$1\"";
	$pattern = "#href=\"".str_replace('?','\?',str_replace('/','\/',$request))."\"#";
	$content = preg_replace($pattern,$new,$content);

	echo $content;
}

/**
 * gets the original url of the requested legend graphic
 * 
 * @param string owsproxy md5
 * @return string url to legend graphic
 */
function getLegendUrl($wmsId){
	global $reqParams;
	//get the url
	$sql = "SELECT layer_style.legendurl ";
	$sql .= "FROM layer_style JOIN layer ";
	$sql .= "ON layer_style.fkey_layer_id = layer.layer_id ";
	$sql .= "WHERE layer.layer_name = $2 AND layer.fkey_wms_id = $1 ";
	$sql .= "AND layer_style.name = $3 AND layer_style.legendurlformat = $4";
	if ($reqParams['style']==''){
		$style='default';
	}
	else {
		$style='';
	}
	$v = array($wmsId, $reqParams['layer'], $style, $reqParams['format']);
	$t = array("i", "s", "s", "s");
	
	$res = db_prep_query($sql, $v, $t);
	if($row = db_fetch_array($res))
		return $row["legendurl"];
	else{
		throwE(array("No legendurl available."));
		die();
	}
}
/**
 * validated access permission on requested wms
 * 
 * @param wmsId integer, userId - integer
 * @return array array with detailed information about requested wms
 */
function checkWmsPermission($wmsId,$userId){
	global $con, $n;
	$myguis = $n->getGuisByPermission($userId,true);
	$mywms = $n->getWmsByOwnGuis($myguis);

	$sql = "SELECT * FROM wms WHERE wms_id = $1";
	$v = array($wmsId);
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

function checkLayerPermission($wms_id,$l,$userId){
	global $n, $owsproxyService;
	$e = new mb_notice("owsproxy: checkLayerpermission: wms: ".$wms_id.", layer: ".$l.' user_id: '.$userId);
	$myl = split(",",$l);
	$r = array();
	foreach($myl as $mysl){
		if($n->getLayerPermission($wms_id, $mysl, $userId) === true){
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
//**********************************************************************************************
//extra functions TODO: push them in class_administration.php 

/**
     * selects the wms id for a given layer id.
     *
     * @param <integer> the layer id
     * @return <string|boolean> either the id of the wms as integer or false when none exists
     */
	function getWmsIdByLayerId($id){
		$sql = "SELECT fkey_wms_id FROM layer WHERE layer_id = $1";
		$v = array($id);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		$row = db_fetch_array($res);
		if ($row) return $row["fkey_wms_id"]; else return false;
	}


?>
