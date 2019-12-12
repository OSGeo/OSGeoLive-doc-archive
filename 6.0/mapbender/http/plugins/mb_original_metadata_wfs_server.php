<?php
require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";
require_once dirname(__FILE__) . "/../classes/class_wfs.php";

$ajaxResponse = new AjaxResponse($_POST);

function abort ($message) {
	global $ajaxResponse;
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage($message);
	$ajaxResponse->send();
	die;
};

function getWfsMetadataFromXml($capabilitiesDoc) {
	$xmlDoc = new DOMDocument();
	$xmlDoc->encoding = CHARSET;
	$xmlDoc->preserveWhiteSpace = false;
	$xmlDoc->loadXML($capabilitiesDoc);
	
	//define default resultObj
	$resultObj = array(
		"original_title" => "",
		"original_summary" => "",
		"original_accessconstraints" => "",
		"original_fees" => "",
//		"original_wfs_keywords" => "",
		"original_positionName" => "",
		"original_electronicMailAddress" => "",
		"original_facsimile" => "",
		"original_voice" => "",
		"original_individualName" => "",
		"original_providerName" => "",
		"original_deliveryPoint" => "",
		"original_city" => "",
		"original_administrativeArea" => "",
		"original_postalCde" => "",
		"original_country" => ""
	);
	
	$service_node = $xmlDoc->getElementsByTagName('Service'); 
	foreach ($service_node as $node) {
		$children = $node->childNodes;
		foreach($children as $child) {
			$e = new mb_exception(strtoupper($child->nodeName)."------".$child->nodeValue);
			if (strtoupper($child->nodeName) == "TITLE"){
				$resultObj["original_title"] = $child->nodeValue;
			}
			if (strtoupper($child->nodeName) == "ABSTRACT"){
				$resultObj["original_summary"] = $child->nodeValue;
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
						$resultObj["original_positionName"] = $contact_child_node->nodeValue;
					}
					if (strtoupper($contact_child_node->nodeName) == "CONTACTELECTRONICMAILADDRESS"){
						$resultObj["original_electronicMailAddress"] = $contact_child_node->nodeValue;
					}
					if (strtoupper($contact_child_node->nodeName) == "CONTACTFACSIMILETELEPHONE"){
						$wms_fax = $contact_child_node->nodeValue;	
						$resultObj["original_facsimile"] = $contact_child_node->nodeValue;
					}
					if (strtoupper($contact_child_node->nodeName) == "CONTACTVOICETELEPHONE"){
						$wms_telephone = $contact_child_node->nodeValue;
						$resultObj["original_voice"] = $contact_child_node->nodeValue;
					}
					
					//"ContactInformation"-childnode ContactpersonPrimary
					if (strtoupper($contact_child_node->nodeName) == "CONTACTPERSONPRIMARY"){
						$contactpersoninfo_list = $contact_child_node->childNodes;
						foreach($contactpersoninfo_list as $contactperson_child) {
							if (strtoupper($contactperson_child->nodeName) == "CONTACTPERSON"){
								$resultObj["original_individualName"] = $contactperson_child->nodeValue;
							}
							if (strtoupper($contactperson_child->nodeName) == "CONTACTORGANIZATION"){
								$resultObj["original_providerName"] = $contactperson_child->nodeValue;
							}
						}
					}
					
					//"ContactInformation"-childnode ContactAddress	
					if (strtoupper($contact_child_node->nodeName) == "CONTACTADDRESS"){
						$contactaddress_list = $contact_child_node->childNodes;
						foreach($contactaddress_list as $contactaddress_child) {
							if (strtoupper($contactaddress_child->nodeName) == "ADDRESS"){
								$resultObj["original_deliveryPoint"] = $contactaddress_child->nodeValue;
							}
							if (strtoupper($contactaddress_child->nodeName) == "CITY"){
								$resultObj["original_city"] = $contactaddress_child->nodeValue;
							}
							if (strtoupper($contactaddress_child->nodeName) == "STATEORPROVINCE"){
								$resultObj["original_administrativeArea"] = $contactaddress_child->nodeValue;
							}
							if (strtoupper($contactaddress_child->nodeName) == "POSTCODE"){
								$resultObj["original_postalCode"] = $contactaddress_child->nodeValue;
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

function getfeaturetypeMetadataFromXml($capabilitiesDoc,$featuretypeName) {
	$xmlDoc = new DOMDocument();
	$xmlDoc->encoding = CHARSET;
	$xmlDoc->preserveWhiteSpace = false;
	$xmlDoc->loadXML($capabilitiesDoc);
	
	//define default resultObj
	$resultObj = array(
		"original_featuretype_title" => "",
		"original_featuretype_abstract" => "",
		"original_featuretype_keyword" => ""
	);
	
	$layer_nodes = $xmlDoc->getElementsByTagName('Featuretype');
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
			$resultObj["original_featuretype_title"] = $layerChild->nodeValue;
		}
		if (strtoupper($layerChild->nodeName) == "ABSTRACT") {
			$resultObj["original_featuretype_abstract"] = $layerChild->nodeValue;
		}
		//"Service"-childnode KeywordList	
		if (strtoupper($layerChild->nodeName) == "KEYWORDLIST") {
			$keywords_list = $layerChild->childNodes;
			$keywords = array();
			foreach ($keywords_list as $keywordlist_child_node) {																		
				$keyword = $keywordlist_child_node->nodeValue;						
				array_push($keywords, $keyword);
			}
			$resultObj["original_featuretype_keyword"] = implode(", ", $keywords); 		
		}
	}
	return $resultObj;
}

switch ($ajaxResponse->getMethod()) {
	case "getOriginalMetadata" :
		$wfsId = $ajaxResponse->getParameter("id");
		$featuretypeName = $ajaxResponse->getParameter("featuretypeName");
		$sql = <<<SQL
	
SELECT wfs_getcapabilities_doc FROM wfs WHERE wfs_id = $wfsId;

SQL;
		$res = db_query($sql);
		$row = db_fetch_array($res);
		$wfs_getcapabilities_doc = $row["wfs_getcapabilities_doc"];
		
		if($featuretypeName != "") {
			$resultObj = getFeaturetypeMetadataFromXml($wfs_getcapabilities_doc,$featuretypeName);
		}
		else {
			$resultObj = getWfsMetadataFromXml($wfs_getcapabilities_doc);
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