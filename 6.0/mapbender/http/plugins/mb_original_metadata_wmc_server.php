<?php
require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";
require_once dirname(__FILE__) . "/../classes/class_wmc.php";

$ajaxResponse = new AjaxResponse($_POST);

function abort ($message) {
	global $ajaxResponse;
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage($message);
	$ajaxResponse->send();
	die;
};

function getWmcMetadataFromXml($wmcDoc) {
	$xmlDoc = new DOMDocument();
	$xmlDoc->encoding = CHARSET;
	$xmlDoc->preserveWhiteSpace = false;
	$xmlDoc->loadXML($wmcDoc);
	
	//define default resultObj
	$resultObj = array(
		"original_wmc_title" => "",
		"original_wmc_abstract" => "",
		"original_wmc_keyword" => ""
	);
	
	$general_node = $xmlDoc->getElementsByTagName('General'); 
	foreach ($general_node as $node) {
		$children = $node->childNodes;
		foreach($children as $child) {
			if (strtoupper($child->nodeName) == "TITLE"){
				$resultObj["original_wmc_title"] = $child->nodeValue;
			}
			if (strtoupper($child->nodeName) == "ABSTRACT"){
				$resultObj["original_wmc_abstract"] = $child->nodeValue;
			}

			//childnode KeywordList	
			if (strtoupper($child->nodeName) == "WMC:KEYWORDLIST"){
				$keywords_list = $child->childNodes;
				$keywords = array();
				foreach ($keywords_list as $keywordlist_child_node) {																		
					$keyword = $keywordlist_child_node->nodeValue;						
					array_push($keywords, $keyword);
				}
				$resultObj["original_wmc_keyword"] = implode(", ", $keywords); 		
			}
		}
	}
	return $resultObj;
}

switch ($ajaxResponse->getMethod()) {
	case "getOriginalMetadata" :
		$wmcId = $ajaxResponse->getParameter("id");
		$sql = <<<SQL
	
SELECT wmc FROM mb_user_wmc WHERE wmc_serial_id = $wmcId;

SQL;
		$res = db_query($sql);
		$row = db_fetch_array($res);
		$wmc_doc = $row["wmc"];
		
		$resultObj = getWmcMetadataFromXml($wmc_doc);
		
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		
		break;
	default: 
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("An unknown error occured."));
		break;
}

$ajaxResponse->send();
?>