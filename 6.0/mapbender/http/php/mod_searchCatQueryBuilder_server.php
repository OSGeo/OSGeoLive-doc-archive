<?php
# $Id$
# http://www.mapbender.org/index.php/class_wms
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

require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_csw.php"); 
require_once(dirname(__FILE__)."/../classes/class_cswrecord.php"); 
require_once(dirname(__FILE__)."/../classes/class_administration.php"); 
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
//require_once(dirname(__FILE__)."/../classes/class_connector.php");

/*
 * get catalogs for gui
 * get get/post url for catalog
 * construct query on url via get/xml
 * make queries sequentially
 * parse responses
 * show responses with cat-name in results	
 */

$DEBUG_ = false;

//Init Variables
$userId = $_SESSION["mb_user_id"];
$command = $_REQUEST["command"];
$guiId = $_REQUEST["guiID"];
$simplesearch = $_REQUEST['search'];
$getrecords_type = $_REQUEST['getrecordsmedia'];
$getrecords_query = $_REQUEST['getrecordsquery'];

$admin = new administration();
$guiIdArray = $admin->getGuisByPermission($userId, false);

$resultObj = array();
$resultObj['cats'] = array();

if($DEBUG_)
array_push($resultObj['cats'],array("id"=>'id',"title"=>'test'));

if ($command == "getrecordssimple") {
	//$resultObj["cats"] = array();
	if($DEBUG_)
	array_push($resultObj['cats'],array("id"=>'id2',"title"=>$getrecords_type));	
	$catalogIDs = array();
	
	switch(mb_strtolower($getrecords_type)){
		
		case 'get':
			$guicats = get_catalogs_for_gui($guiId);
			if($DEBUG_)
			$guicats = array('13');
			$catalogIDs = get_catalogs_by_supported_type($guicats,'getrecords','get');
			//$catalogIDs = get_catalogs_by_supported_type(array(9,10),'getrecords','get');
			//$catalogIDs['7']= 'ac';		
			if($DEBUG_)
				array_push($resultObj['cats'],array("id"=>'id35'.$guiId,"title"=>$guicats[0]));
			break;
		case 'post':
			
			$catalogIDs = get_catalogs_by_supported_type(get_catalogs_for_gui($guiId),'getrecords','post');
			break;
		case 'soap':
			$catalogIDs = get_catalogs_by_supported_type(get_catalogs_for_gui($guiId),'getrecords','soap');
			break;
		default:
			$catalogIDs = get_catalogs_by_supported_type(get_catalogs_for_gui($guiId),'getrecords','get');
		#$e = new mb_exception("catalogids: ".print_r($catalogIDs['getrecords']));
	}
	
	//main controller
	//loop for each catalog
	#$error = var_dump($catalogIDs['getrecordbyid']);
	#$e = new mb_exception("catalogids: ".$error);
	foreach($catalogIDs as $catalog_id=>$url){
		#$e = new mb_exception("url: ".$url['getrecords']);
		//$cat_obj = new csw();
		//$cat_obj->createCatObjFromDB($catalogs);
		//list($getrecordsurl,$getrecordsxml) = getrecords_get_build_query($url,$simplesearch);
		list($getrecordsurl,$getrecordsxml) = getrecords_build_query($url['getrecords'],$getrecords_type,$command);
		if($DEBUG_)
		array_push($resultObj['cats'],array("id"=>$catalog_id,"title"=>$getrecordsurl));
		//array_push($resultObj['cats'],array("title"=>$catalog_id.'url',"abstractt"=>$getrecordsurl));
		//Create Record Objects
		$e = new mb_exception('getrecordbyid'.$url['getrecordbyid']);
		$RecordObj = new cswrecord();		
		//$RecordObj->getrecords_exception;
		$RecordObj->createCSWRecordFromXML($getrecordsurl,$getrecordsxml);
		if ($RecordObj->getrecords_exception == false & $RecordObj->getrecords_status == true){
			//Populate JSON for each summary record for each catalog
			//Loop for each Summary Record
			foreach ($RecordObj->SummaryRecordsArray as $SummaryRecordObj){
				$title = $SummaryRecordObj->getTitle();
				$abstract = $SummaryRecordObj->getAbstract();
				$identifier = $SummaryRecordObj->getIdentifier();
				#new mb_exception("mod_searchCatQueryBuilder.php: Identifier: ".$identifier);
				array_push($resultObj['cats'],array("title"=>$title,"abstractt"=>$abstract, "identifier"=>$identifier,"url"=>$url['getrecordbyid']));
			}
		}
		else {
			$e = new mb_exception("php/mod_searchCatQueryBuilder_server.php: CAT getrecords returned an ows:exception!");
		    array_push($resultObj['cats'],array("title"=>"OWS Exception","abstractt"=>$RecordObj->getrecords_exception_text));
		}
	}
}

/**
 * Build Query
 * @param $getrecords_url
 * @return array URL and XML
 */
function getrecords_build_query($getrecords_url,$type,$command){
	//CHECK FOR COMMAND, GET POST. HANDLE THINGS HERE
	$url = null;
	$xml = null;
	$request = 'GetRecords';
	$version = '2.0.2';
	$resulttype = 'results';
	$typename = 'csw:Record';
	$service='CSW';
	global $simplesearch;
	switch(strtolower($type)){
		case 'get':
			$url = $getrecords_url.'?request='.$request.'&service='.$service.'&ResultType='.$resulttype.'&TypeNames='.$typename.'&version='.$version;
			if($command=='getrecordssimple'){
				
				//Simple GetRecords via GET
				$tmpurl = "csw:AnyText Like '%$simplesearch%'";
				$suburl = urlencode($tmpurl);
				$aurl = "%$simplesearch%";
				$aurl = urlencode($aurl);
				$tmpurl = "csw:AnyText%20Like%20%27$aurl%27";
				$url .= (isset($simplesearch) && $simplesearch!="")?'&constraintlanguage=CQLTEXT&constraint='.$tmpurl:'';
				$e = new mb_exception("mod_searchCatQueryBuilder_server:url:".$url);
	            $e = new mb_exception("mod_searchCatQueryBuilder_server:xml:".$xml);
			}
			else {
				//Advanced GetRecords via GET
				$url = getrecords_advanced_get($getrecords_url);
				$e = new mb_exception("mod_searchCatQueryBuilder_server:url:".$url);
			}
			break;
		case 'post':
			$url = $getrecords_url;
			$xml = build_getrecords_xml();
			$e = new mb_exception("mod_searchCatQueryBuilder_server:url:".$url);
			break;
		case 'soap':
			break;
		default:
			break;
	}
	
	return array($url,$xml);
	
}	

function getrecords_advanced_get($url){
	
	$adv_title = $_REQUEST['adv_title'];
	$adv_subject = $_REQUEST['adv_keyword'];
	$adv_abstract = $_REQUEST['adv_abstract'];
	$latmin = $_REQUEST['latmin'];
	$latmax = $_REQUEST['latmax'];
	$lonmin = $_REQUEST['lonmin'];
	$lonmax = $_REQUEST['lonmax'];
	
	$url .= '&constraintlanguage=CQLTEXT&constraint=';
	$query = '';
	$query .= (isset($adv_title) && $adv_title!="")?"dc:Title Like %$adv_title%":'';
	$query .= (isset($adv_abstract) && $adv_abstract!="")?"AND dc:Abstract Like %$adv_abstract%":'';
	$query .= (isset($adv_subject) && $adv_subject!="")?"AND dc:Subject Like %$adv_subject%":'';
	
	$url = $url.urlencode($query);
	$e = new mb_exception("mod_searchCatQueryBuilder.php: url: ".$url);
	return $url;
}

/**
 * 
 * @param $gui_id
 * @param $getrecords_type
 * @return array list of cats
 */
function get_catalogs_for_gui($gui_id){
	$sql = "select fkey_cat_id from gui_cat where fkey_gui_id = $1";
	$v = array($gui_id);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
	$list_of_cat = array();
	while($row = db_fetch_array($res)){
		array_push($list_of_cat,$row['fkey_cat_id']);
	}
	return $list_of_cat;
}

/**
 * http://geomatics.nlr.nl/excat/csw?request=GetRecords&service=CSW&version=2.0.2&ResultType=results&TypeName=csw:Record&TYPENAMES=csw:dataset
 * @param $url
 * @param $search
 * @return unknown_type
 */
function getrecords_get_build_query($url,$search){

	$request = 'GetRecords';
	$version = '2.0.2';
	$resulttype = 'results';
	$typename = 'csw:Record';
	$service='CSW';

	$url_encode = $url.'?request='.$request.'&service='.$service.'&ResultType='.$resulttype.'&TypeNames='.$typename.'&version='.$version;
	//$url_encode = urlencode($url_encode);
	
	return array($url_encode,null);
}

/**
 * Get catalog URL which support fetch mode for operation type
 * @param $cat_array array of catalogs for guis
 * @param $operation_type getrecords,describerecords..
 * @param $fetch_mode get|post|soap
 * @return array of supported catalog ids
 */
function get_catalogs_by_supported_type($cat_array,$operation_type,$fetch_mode){
	
				//array_push($resultObj['cats'],array("id"=>'id5',"title"=>$operation_type));
	$cat_supported = array();
	
	$sql = "select fk_cat_id,param_value from cat_op_conf where param_name=$1 and param_type=$2";
	$v = array($fetch_mode,$operation_type);	
	$t = array('s','s');
	$res = db_prep_query($sql,$v,$t);
	
	
	while($row = db_fetch_array($res)){
		//array_push($list_of_cat,$row['fkey_cat_id']);
		if($DEBUG_)
				array_push($resultObj['cats'],array("id"=>'id4',"title"=>$row));
		if(in_array($row['fk_cat_id'],$cat_array,true)){
			//array_push($cat_supported,$row['fk_cat_id']);
			$cat_supported[$row['fk_cat_id']]['getrecords'] = $row['param_value'];
			//get url entry for getrecordbyid:
			$sql2 = "select fk_cat_id,param_value from cat_op_conf where fk_cat_id=$1 and param_type=$2 and param_name='get'";
	        $v2 = array($row['fk_cat_id'],'getrecordbyid');	
	        $t2 = array('i','s');
	        $res2 = db_prep_query($sql2,$v2,$t2);
	        while($row2 = db_fetch_array($res2)){
	            $e = new mb_exception("mod_searchCatQueryBuilder.php: getrecordbyidurl:  ".$row2['param_value']);
				$getrecordbyidurl = $row2['param_value'];
	        }
			$cat_supported[$row['fk_cat_id']]['getrecordbyid'] = $getrecordbyidurl;
			
		}
	}
	return $cat_supported;
}

/**
 * Build XML query for getrecords
 * @return string xml file
 * @todo: get values dynamically
 */
function build_getrecords_xml() {
	$xml = '<?xml version="1.0" encoding="UTF-8"?> ';
	$xml .= '<csw:GetRecords ';
	#$xml .= '<GetRecords ';
	$xml .= 'service="CSW" ';
	$xml .= 'version="2.0.2" ';
	$xml .= 'maxRecords="5" ';
	$xml .= 'startPosition="1" ';
	$xml .= 'resultType="results" ';
	$xml .= 'outputFormat="application/xml" ';
	$xml .= 'outputSchema="http://www.opengis.net/cat/csw/2.0.2" ';
	$xml .= 'xmlns="http://www.opengis.net/cat/csw/2.0.2" ';
  	$xml .= 'xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" ';
  	$xml .= 'xmlns:ogc="http://www.opengis.net/ogc" ';
  	$xml .= 'xmlns:ows="http://www.opengis.net/ows" ';
  	$xml .= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
  	$xml .= 'xmlns:dct="http://purl.org/dc/terms/" ';
  	$xml .= 'xmlns:gml="http://www.opengis.net/gml" ';
  	$xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
	$xml .= 'xsi:schemaLocation="http://www.opengis.net/cat/csw/2.0.2/CSW-discovery.xsd"> ';
	$xml .= '> ';
  	$xml .= '<csw:Query typeNames="csw:Record">';
  	$xml .= '<csw:ElementSetName>summary</csw:ElementSetName>';
	$xml .= '</csw:Query>';
	$xml .= '</csw:GetRecords>';
	#$xml .= '</GetRecords>';
	//parse xml for validating it before sending
	$data = stripslashes($xml);
	$dataXMLObject = new SimpleXMLElement($xml);
	$xml = $dataXMLObject->asXML();
	
	return $xml;
	
}


function getrecords_post ($url, $postData) {
	 	$connection = new connector();
        $connection->set("httpType", "post");
        $connection->set("httpContentType", "text/xml");
        $connection->set("httpPostData", $postData);

        $e = new mb_notice("CAT REQUEST: " . $url . "\n\n" . $postData);
        $data = $connection->load($url);
        if (!$data) {
            $e = new mb_exception("CAT getrecords returned no result: " . $url . "\n" . $postData);
            return null;
        }
        return $data;
}



$json = new Mapbender_JSON();
$output = $json->encode($resultObj);
echo $output;

?>