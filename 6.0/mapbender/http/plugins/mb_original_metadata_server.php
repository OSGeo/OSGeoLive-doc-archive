<?php
require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";
require_once dirname(__FILE__) . "/../classes/class_wms.php";

$ajaxResponse = new AjaxResponse($_POST);

function abort ($message) {
	global $ajaxResponse;
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage($message);
	$ajaxResponse->send();
	die;
};

function getWmsMetadataFromXml($capabilitiesDoc) {
	$xmlDoc = new DOMDocument();
	$xmlDoc->encoding = CHARSET;
	$xmlDoc->preserveWhiteSpace = false;
	$xmlDoc->loadXML($capabilitiesDoc);
	
	//define default resultObj
	$resultObj = array(
		"original_wms_title" => "",
		"original_wms_abstract" => "",
		"original_accessconstraints" => "",
		"original_fees" => "",
//		"original_wms_keywords" => "",
		"original_contactposition" => "",
		"original_contactelectronicmailaddress" => "",
		"original_contactfacsimiletelephone" => "",
		"original_contactvoicetelephone" => "",
		"original_contactperson" => "",
		"original_contactorganization" => "",
		"original_address" => "",
		"original_city" => "",
		"original_stateorprovince" => "",
		"original_postcode" => "",
		"original_country" => ""
	);
	
	$service_node = $xmlDoc->getElementsByTagName('Service'); 
	foreach ($service_node as $node) {
		$children = $node->childNodes;
		foreach($children as $child) {
			if (strtoupper($child->nodeName) == "TITLE"){
				$resultObj["original_wms_title"] = $child->nodeValue;
			}
			if (strtoupper($child->nodeName) == "ABSTRACT"){
				$resultObj["original_wms_abstract"] = $child->nodeValue;
			}
			if (strtoupper($child->nodeName) == "ACCESSCONSTRAINTS"){
				$resultObj["original_accessconstraints"] = $child->nodeValue;
			}
			if (strtoupper($child->nodeName) == "FEES"){
				$resultObj["original_fees"] = $child->nodeValue;
			}

/*			
			//"Service"-childnode KeywordList	
			if (strtoupper($child->nodeName) == "KEYWORDLIST"){
				$keywords_list = $child->childNodes;
				$keywords = array();
				foreach ($keywords_list as $keywordlist_child_node) {																		
					$keyword = $keywordlist_child_node->nodeValue;						
					array_push($keywords, $keyword);
				}
				$resultObj["original_wms_keywords"] = implode(", ", $keywords); 		
			}
*/				
			//"Service"-childnode ContactInformation	
			if (strtoupper($child->nodeName) == "CONTACTINFORMATION"){
				$contactinfo_list = $child->childNodes;
				foreach ($contactinfo_list as $contact_child_node) {
					if (strtoupper($contact_child_node->nodeName) == "CONTACTPOSITION"){
						$wms_contactposition = $contact_child_node->nodeValue;
						$resultObj["original_contactposition"] = $contact_child_node->nodeValue;
					}
					if (strtoupper($contact_child_node->nodeName) == "CONTACTELECTRONICMAILADDRESS"){
						$resultObj["original_contactelectronicmailaddress"] = $contact_child_node->nodeValue;
					}
					if (strtoupper($contact_child_node->nodeName) == "CONTACTFACSIMILETELEPHONE"){
						$wms_fax = $contact_child_node->nodeValue;	
						$resultObj["original_contactfacsimiletelephone"] = $contact_child_node->nodeValue;
					}
					if (strtoupper($contact_child_node->nodeName) == "CONTACTVOICETELEPHONE"){
						$wms_telephone = $contact_child_node->nodeValue;
						$resultObj["original_contactvoicetelephone"] = $contact_child_node->nodeValue;
					}
					
					//"ContactInformation"-childnode ContactpersonPrimary
					if (strtoupper($contact_child_node->nodeName) == "CONTACTPERSONPRIMARY"){
						$contactpersoninfo_list = $contact_child_node->childNodes;
						foreach($contactpersoninfo_list as $contactperson_child) {
							if (strtoupper($contactperson_child->nodeName) == "CONTACTPERSON"){
								$resultObj["original_contactperson"] = $contactperson_child->nodeValue;
							}
							if (strtoupper($contactperson_child->nodeName) == "CONTACTORGANIZATION"){
								$resultObj["original_contactorganization"] = $contactperson_child->nodeValue;
							}
						}
					}
					
					//"ContactInformation"-childnode ContactAddress	
					if (strtoupper($contact_child_node->nodeName) == "CONTACTADDRESS"){
						$contactaddress_list = $contact_child_node->childNodes;
						foreach($contactaddress_list as $contactaddress_child) {
							if (strtoupper($contactaddress_child->nodeName) == "ADDRESS"){
								$resultObj["original_address"] = $contactaddress_child->nodeValue;
							}
							if (strtoupper($contactaddress_child->nodeName) == "CITY"){
								$resultObj["original_city"] = $contactaddress_child->nodeValue;
							}
							if (strtoupper($contactaddress_child->nodeName) == "STATEORPROVINCE"){
								$resultObj["original_stateorprovince"] = $contactaddress_child->nodeValue;
							}
							if (strtoupper($contactaddress_child->nodeName) == "POSTCODE"){
								$resultObj["original_postcode"] = $contactaddress_child->nodeValue;
							}
							if (strtoupper($contactaddress_child->nodeName) == "COUNTRY"){
								$resultObj["original_country"] = $contactaddress_child->nodeValue;
							}		
						}	
					}		
				}
			}						
		}
	}
	return $resultObj;
}

function getLayerMetadataFromXml($capabilitiesDoc,$layerName) {
	$xmlDoc = new DOMDocument();
	$xmlDoc->encoding = CHARSET;
	$xmlDoc->preserveWhiteSpace = false;
	$xmlDoc->loadXML($capabilitiesDoc);
	
	//define default resultObj
	$resultObj = array(
		"original_layer_title" => "",
		"original_layer_abstract" => "",
		"original_layer_keyword" => ""
	);
	
	$layer_nodes = $xmlDoc->getElementsByTagName('Layer');
	foreach ($layer_nodes as $node) {
		$children = $node->childNodes;
		foreach($children as $child) {
			if (strtoupper($child->nodeName) == "NAME"){
				if($child->nodeValue == $layerName) {
					$layerNode = $node;
				}	
			}
		}
	}
	
	$layerChildren = $layerNode->childNodes;
	foreach($layerChildren as $layerChild) {
		if (strtoupper($layerChild->nodeName) == "TITLE") {
			$resultObj["original_layer_title"] = $layerChild->nodeValue;
		}
		if (strtoupper($layerChild->nodeName) == "ABSTRACT") {
			$resultObj["original_layer_abstract"] = $layerChild->nodeValue;
		}
		//"Service"-childnode KeywordList	
		if (strtoupper($layerChild->nodeName) == "KEYWORDLIST") {
			$keywords_list = $layerChild->childNodes;
			$keywords = array();
			foreach ($keywords_list as $keywordlist_child_node) {																		
				$keyword = $keywordlist_child_node->nodeValue;						
				array_push($keywords, $keyword);
			}
			$resultObj["original_layer_keyword"] = implode(", ", $keywords); 		
		}
	}
	return $resultObj;
}

switch ($ajaxResponse->getMethod()) {
	case "getOriginalMetadata" :
		$wmsId = $ajaxResponse->getParameter("id");
		$layerName = $ajaxResponse->getParameter("layerName");
		$sql = <<<SQL
	
SELECT wms_getcapabilities_doc FROM wms WHERE wms_id = $wmsId;

SQL;
		$res = db_query($sql);
		$row = db_fetch_array($res);
		$wms_getcapabilities_doc = $row["wms_getcapabilities_doc"];
		
		if($layerName != "") {
			$resultObj = getLayerMetadataFromXml($wms_getcapabilities_doc,$layerName);
		}
		else {
			$resultObj = getWmsMetadataFromXml($wms_getcapabilities_doc);
		}
		
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