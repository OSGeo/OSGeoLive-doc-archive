<?php
require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";
require_once dirname(__FILE__) . "/../classes/class_wms.php";
require_once dirname(__FILE__) . "/../classes/class_Uuid.php";

$ajaxResponse = new AjaxResponse($_POST);

function abort ($message) {
	global $ajaxResponse;
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage($message);
	$ajaxResponse->send();
	die();
};

function getWms ($wmsId = null) {
	$user = new User(Mapbender::session()->get("mb_user_id"));
	$wmsIdArray = $user->getOwnedWms();

	if (!is_null($wmsId) && !in_array($wmsId, $wmsIdArray)) {
		abort(_mb("You are not allowed to access this WMS."));
	}
	return $wmsIdArray;
}

function getLayer ($layerId = null) {
	$user = new User(Mapbender::session()->get("mb_user_id"));
	$wmsIdArray = $user->getOwnedWms();
	if (!is_array($wmsIdArray) || count($wmsIdArray) === 0) {
		abort(_mb("No metadata sets available."));
	}
	$wmsId = wms::getWmsIdByLayerId($layerId);
	if (is_null($wmsId) || !in_array($wmsId, $wmsIdArray)) {
		abort(_mb("You are not allowed to access WMS " . $wmsId));
	}
	return;
}

switch ($ajaxResponse->getMethod()) {
	case "getWms" :
		$wmsIdArray = getWms();
		
		$wmsList = implode(",", $wmsIdArray);
		$sql = <<<SQL
	
SELECT wms.wms_id, wms.wms_title, to_timestamp(wms.wms_timestamp),to_timestamp(wms.wms_timestamp_create), wms_version, m.status_comment, wms_id
FROM wms LEFT JOIN mb_wms_availability AS m
ON wms.wms_id = m.fkey_wms_id 
WHERE wms_id IN ($wmsList);

SQL;
		$res = db_query($sql);
		$resultObj = array(
			"header" => array(
				_mb("WMS ID"),
				_mb("title"),
				_mb("last change"),
				_mb("creation"),
				_mb("version"),
				_mb("status"),
				_mb("wms id")
			), 
			"data" => array()
		);

		while ($row = db_fetch_row($res)) {
			// convert NULL to '', NULL values cause datatables to crash
			$walk = array_walk($row, create_function('&$s', '$s=strval($s);'));
			$resultObj["data"][]= $row;
		}
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;

	case "getWmsMetadata" :
		$wmsId = $ajaxResponse->getParameter("id");
		getWms($wmsId);

		$wms = new wms();
		$wms->createObjFromDBNoGui($wmsId);//here the owsproxyurls will be read out - to make previews with proxy urls

		$fields = array(
			"wms_id", 
			"wms_abstract", 
			"wms_title", 
			"fees", 
			"accessconstraints", 
			"contactperson", 
			"contactposition", 
			"contactvoicetelephone", 
			"contactfacsimiletelephone", 
			"contactorganization", 
			"address", 
			"city", 
			"stateorprovince", 
			"postcode", 
			"country", 
			"contactelectronicmailaddress",
			"wms_timestamp", 
			"wms_timestamp_create",
			"wms_network_access",
			"fkey_mb_group_id"
		);

		$resultObj = array();
		foreach ($fields as $field) {
			if ($field == "wms_timestamp" || $field == "wms_timestamp_create") {
				if ($wms->$field != "") {
	
					$resultObj[$field] = date('d.m.Y', $wms->$field);
					
				}
			}
			else {
				$resultObj[$field] = $wms->$field;
				//$e = new mb_exception("mb_metadata_server: resultObject[".$field."]=".$wms->$field);	
			}
		}
		
		// layer searchable
		$resultObj["layer_searchable"] = array();
		foreach ($wms->objLayer as $layer) {
			if (intval($layer->layer_searchable) === 1) {
				$resultObj["layer_searchable"][] = intval($layer->layer_uid);
			}
		}
		
		$keywordSql = <<<SQL
	
SELECT DISTINCT keyword FROM keyword, layer_keyword 
WHERE keyword_id = fkey_keyword_id AND fkey_layer_id IN (
	SELECT layer_id from layer, wms 
	WHERE fkey_wms_id = wms_id AND wms_id = $wmsId
) ORDER BY keyword

SQL;

		$keywordRes = db_query($keywordSql);
		$keywords = array();
		while ($keywordRow = db_fetch_assoc($keywordRes)) {
			$keywords[]= $keywordRow["keyword"];
		}

		$resultObj["wms_keywords"] = implode(", ", $keywords);

		$termsofuseSql = <<<SQL
SELECT fkey_termsofuse_id FROM wms_termsofuse WHERE fkey_wms_id = $wmsId
SQL;

		$termsofuseRes = db_query($termsofuseSql);
		if ($termsofuseRes) {
			$termsofuseRow = db_fetch_assoc($termsofuseRes);
			$resultObj["wms_termsofuse"] = $termsofuseRow["fkey_termsofuse_id"];
		}
		else {
			$resultObj["wms_termsofuse"] = null;
		}
		
		$resultObj['wms_network_access'] = $resultObj['wms_network_access'] == 1 ? true : false;
		
		//get contact information from group relation
		//check if fkey_mb_group_id has been defined before - in service table
		if ($resultObj["fkey_mb_group_id"] == "" || !isset($resultObj["fkey_mb_group_id"])){
			$e = new mb_notice("fkey_mb_group_id is null or empty");
			//check if primary group is set 
			$user = new User;
			$userId = $user->id;
			//$e = new mb_exception("user id:".$userId);
			$sql = <<<SQL
	
SELECT fkey_mb_group_id, mb_group_name, mb_group_title, mb_group_address, mb_group_email, mb_group_postcode, mb_group_city, mb_group_logo_path, mb_group_voicetelephone FROM (SELECT fkey_mb_group_id FROM mb_user_mb_group WHERE fkey_mb_user_id = $1 AND mb_user_mb_group_type = 2) AS a LEFT JOIN mb_group ON a.fkey_mb_group_id = mb_group.mb_group_id

SQL;
			$v = array($userId);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);
			$row = array();
			if ($res) {
				$row = db_fetch_assoc($res);
				$resultObj["fkey_mb_group_id"] = $row["fkey_mb_group_id"];
				$resultObj["mb_group_title"] = $row["mb_group_title"];
				$resultObj["mb_group_address"] = $row["mb_group_address"];
				$resultObj["mb_group_email"] = $row["mb_group_email"];
				$resultObj["mb_group_postcode"] = $row["mb_group_postcode"];
				$resultObj["mb_group_city"] = $row["mb_group_city"];
				$resultObj["mb_group_logo_path"] = $row["mb_group_logo_path"];
				$resultObj["mb_group_voicetelephone"] = $row["mb_group_voicetelephone"];
			}
		} else {
			//get current fkey_mb_group_id and the corresponding data
			$sql = <<<SQL
	
SELECT mb_group_name, mb_group_title, mb_group_address, mb_group_email, mb_group_postcode, mb_group_city, mb_group_logo_path, mb_group_voicetelephone FROM mb_group WHERE mb_group_id = $1

SQL;
			$v = array($resultObj["fkey_mb_group_id"]);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);
			$row = array();
			if ($res) {
				$row = db_fetch_assoc($res);
				$resultObj["mb_group_title"] = $row["mb_group_title"];
				$resultObj["mb_group_address"] = $row["mb_group_address"];
				$resultObj["mb_group_email"] = $row["mb_group_email"];
				$resultObj["mb_group_postcode"] = $row["mb_group_postcode"];
				$resultObj["mb_group_city"] = $row["mb_group_city"];
				$resultObj["mb_group_logo_path"] = $row["mb_group_logo_path"];
				$resultObj["mb_group_voicetelephone"] = $row["mb_group_voicetelephone"];
			}
			else {
				$resultObj["fkey_mb_group_id"] = null;
			}
		}
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);

		break;
	
	case "getLayerMetadata" :
		$layerId = $ajaxResponse->getParameter("id");
		getLayer($layerId);

		$sql = <<<SQL
	
SELECT layer_id, layer_name, layer_title, layer_abstract, layer_searchable 
FROM layer WHERE layer_id = $layerId;

SQL;
		$res = db_query($sql);

		$resultObj = array();
		while ($row = db_fetch_assoc($res)) {
			foreach ($row as $key => $value) {
				$resultObj[$key] = $value;
				$e = new mb_notice("plugins/mb_metadata_server.php: get ".$value." for ".$key);
			}
		}

		$sql = <<<SQL
SELECT fkey_md_topic_category_id 
FROM layer_md_topic_category 
WHERE fkey_layer_id = $layerId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["layer_md_topic_category_id"][]= $row["fkey_md_topic_category_id"];
		}

		$sql = <<<SQL
SELECT fkey_inspire_category_id 
FROM layer_inspire_category 
WHERE fkey_layer_id = $layerId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["layer_inspire_category_id"][]= $row["fkey_inspire_category_id"];
		}

		$sql = <<<SQL
SELECT fkey_custom_category_id 
FROM layer_custom_category 
WHERE fkey_layer_id = $layerId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["layer_custom_category_id"][]= $row["fkey_custom_category_id"];
		}

		$sql = <<<SQL
SELECT keyword FROM keyword, layer_keyword 
WHERE keyword_id = fkey_keyword_id AND fkey_layer_id = $layerId
SQL;
		$res = db_query($sql);

		$resultObj["layer_keyword"] = array();
		while ($row = db_fetch_assoc($res)) {
			$resultObj["layer_keyword"][]= $row["keyword"];
		}
		//get MetadataURLs from md_metadata table
		$sql = <<<SQL
SELECT metadata_id, uuid, link, linktype, md_format, origin FROM mb_metadata 
INNER JOIN (SELECT * from ows_relation_metadata 
WHERE fkey_layer_id = $layerId ) as relation ON 
mb_metadata.metadata_id = relation.fkey_metadata_id WHERE mb_metadata.origin IN ('capabilities','external','metador','upload')
SQL;
		$res = db_query($sql);
		$resultObj["md_metadata"]->metadata_id = array();
		$resultObj["md_metadata"]->uuid = array();
		$resultObj["md_metadata"]->origin = array();
		$resultObj["md_metadata"]->linktype = array();
		$resultObj["md_metadata"]->link = array();
		$i = 0;
		while ($row = db_fetch_assoc($res)) {
			$resultObj["md_metadata"]->metadata_id[$i]= $row["metadata_id"];
			$resultObj["md_metadata"]->link[$i]= $row["link"];
			$resultObj["md_metadata"]->uuid[$i]= $row["uuid"];
			$resultObj["md_metadata"]->origin[$i]= $row["origin"];
			$resultObj["md_metadata"]->linktype[$i]= $row["linktype"];
			$i++;
		}
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;
	case "getLayerByWms" :
		$wmsId = $ajaxResponse->getParameter("id");
//		getWms($wmsId);

		$sql = <<<SQL
	
SELECT layer_id, layer_pos, layer_parent, layer_name, layer_title, layer_abstract, layer_searchable 
FROM layer WHERE fkey_wms_id = $wmsId ORDER BY layer_pos;

SQL;
		$res = db_query($sql);

		$rows = array();
		while ($row = db_fetch_assoc($res)) {
			$rows[] = $row;
		}
		$left = 1;

		function createNode ($left, $right, $row) {
			return array(
				"left" => $left,
				"right" => $right,
				"parent" => $row["layer_parent"] !== "" ? intval($row["layer_parent"]) : null,
				"pos" => intval($row["layer_pos"]),
				"attr" => array (
					"layer_id" => intval($row["layer_id"]),
					"layer_name" => $row["layer_name"],
					"layer_title" => $row["layer_title"],
					"layer_abstract" => $row["layer_abstract"],
					"layer_searchable" => intval($row["layer_searchable"])
				)
			);
		}

		function addSubTree ($rows, $i, $left) {
			$nodeArray = array();
			$addNewNode = true;
			for ($j = $i; $j < count($rows); $j++) {
				$row = $rows[$j];
				$pos = intval($row["layer_pos"]);
				$parent = $row["layer_parent"] !== "" ? intval($row["layer_parent"]) : null;
				
				// first node of subtree
				if ($addNewNode) {
					$nodeArray[]= createNode($left, null, $row);
					$addNewNode = false;
				}
				else {
					// new sub tree
					if ($parent === $nodeArray[count($nodeArray)-1]["pos"]) {
						$addedNodeArray = addSubTree($rows, $j, ++$left);
						
						$nodeArray[count($nodeArray)-1]["right"] = 
							$nodeArray[count($nodeArray)-1]["left"] + 
							2 * count($addedNodeArray) + 1;
						
						$left = $nodeArray[count($nodeArray)-1]["right"] + 1;

						$nodeArray = array_merge($nodeArray, $addedNodeArray);
						$j += count($addedNodeArray) - 1;
						$addNewNode = true;
						
					}
					// siblings
					elseif ($parent === $nodeArray[count($nodeArray)-1]["parent"]) {
						$nodeArray[count($nodeArray)-1]["right"] = ++$left;
						$nodeArray[]= createNode(++$left, null, $row);
					}
				}
			}
			if (is_null($nodeArray[count($nodeArray)-1]["right"])) {
				$nodeArray[count($nodeArray)-1]["right"] = ++$left;
			}
			return $nodeArray;
		}
		

		$nodeArray = addSubTree($rows, 0, 1);
		$resultObj = array(
			"nestedSets" => $nodeArray
		);
		
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		

		break;
	case "save":
		$data = $ajaxResponse->getParameter("data");
		
		try {
			$wmsId = intval($data->wms->wms_id);
		}
		catch (Exception $e) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("Invalid WMS ID."));
			$ajaxResponse->send();						
		}
		getWms($wmsId);
		
		$wms = new wms();
		$wms->createObjFromDBNoGui($wmsId,false);//here the original urls will be used - cause the object will used to update the wms table
		
		$columns = array(
			"wms_abstract", 
			"wms_title", 
			"fees", 
			"accessconstraints", 
			"contactperson", 
			"contactposition", 
			"contactvoicetelephone", 
			"contactfacsimiletelephone", 
			"contactorganization", 
			"address", 
			"city", 
			"stateorprovince", 
			"postcode", 
			"country", 
			"contactelectronicmailaddress",
			"wms_termsofuse",
			"wms_network_access",
			"fkey_mb_group_id"
		);
		foreach ($columns as $c) {
			$value = $data->wms->$c;
			if (!is_null($value)) {
				$wms->$c = $value;
			}
		}

		if (is_array($data->wms->layer_searchable)) {
			foreach ($wms->objLayer as &$layer) {
				$layer->layer_searchable = 0;//why
				$e = new mb_notice("mb_metadata_server.php: Check layer with id ".$layer->layer_uid." to be searchable");
				for ($i = 0; $i < count($data->wms->layer_searchable); $i++) {
					//$e = new mb_exception("mb_metadata_server.php: Layer with id ".$id." found to be searchable");
					$id = $data->wms->layer_searchable[$i];
					$e = new mb_notice("mb_metadata_server.php: Layer with id ".$id." found to be searchable");
					if ($id == intval($layer->layer_uid)) {
						$e = new mb_notice("mb_metadata_server.php: Layer identical - update it in wms object");
						$layer->layer_searchable = 1;					
					} else {
						continue; //with next 
					}
					unset($id);
					//$layer->layer_searchable = 1;
					//break;
				}
			}
		}

		try {
			$layerId = intval($data->layer->layer_id);
		}
		catch (Exception $e) {
		  	$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("Could not read layer ID ".$data->layer->layer_id));
			$ajaxResponse->send();						
		}
		
		if ($layerId) {
			$e = new mb_notice("Got following layer id from wms metadata editor client: ".$layerId);
			try {
				$layer = &$wms->getLayerReferenceById($layerId);
			}
			catch (Exception $e) {
				$ajaxResponse->setSuccess(false);
				$ajaxResponse->setMessage(_mb("Could not get layer with ID ".$layerId));
				$ajaxResponse->send();						
			}
			$columns = array(
				"layer_abstract",
				"layer_title",
				"layer_keyword",
				"layer_md_topic_category_id",
				"layer_inspire_category_id",
				"layer_custom_category_id"
			);

			foreach ($columns as $c) {
				$value = $data->layer->$c;
				$e = new mb_notice("plugins/mb_metadata_server.php: layer entry for ".$c.": ".$data->layer->$c);
				if ($c === "layer_keyword") {
					$layer->$c = explode(",", $value);
					foreach ($layer->$c as &$val) {
						$val = trim($val);
					}
				}
				elseif ($c === "layer_md_topic_category_id"
					|| $c === "layer_inspire_category_id"
					|| $c === "layer_custom_category_id"
				) {
					if (!is_array($value)) {
						$layer->$c = array($value);
					}
					else {
						$layer->$c = $value;
					}
				}
				else {
					if (!is_null($value)) {
						$layer->$c = $value;
					}
				}
			}
		}
		if ($wms->wms_network_access == "on") {
			$wms->wms_network_access = intval('1');
		} else {
			$wms->wms_network_access = intval('0');
		}
		//try {
			$wms->updateObjInDB($wmsId);
		//}
		//catch (Exception $e) {
		//	$ajaxResponse->setSuccess(false);
		//	$ajaxResponse->setMessage(_mb("Could not update wms object in database!"));
		//	$ajaxResponse->send();						
		//}
		
		
		$ajaxResponse->setMessage("Updated WMS metadata for ID " . $wmsId);
		$ajaxResponse->setSuccess(true);		
		
		break;
	case "getContactMetadata" :
		$mbGroupId = $ajaxResponse->getParameter("id");
		$sql = <<<SQL
	
SELECT mb_group_name, mb_group_title, mb_group_address, mb_group_email, mb_group_postcode, mb_group_city, mb_group_logo_path, mb_group_voicetelephone FROM mb_group WHERE mb_group_id = $1

SQL;
		$v = array($mbGroupId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		$row = array();
		if ($res) {
			$row = db_fetch_assoc($res);
			$resultObj["fkey_mb_group_id"] = $mbGroupId;
			$resultObj["mb_group_name"] = $row["mb_group_name"];
			$resultObj["mb_group_title"] = $row["mb_group_title"];
			$resultObj["mb_group_address"] = $row["mb_group_address"];
			$resultObj["mb_group_email"] = $row["mb_group_email"];
			$resultObj["mb_group_postcode"] = $row["mb_group_postcode"];
			$resultObj["mb_group_city"] = $row["mb_group_city"];
			$resultObj["mb_group_logo_path"] = $row["mb_group_logo_path"];
			$resultObj["mb_group_voicetelephone"] = $row["mb_group_voicetelephone"];	
		}
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;

	case "getLayerMetadataAddon" :
		$layerId = $ajaxResponse->getParameter("layerId");
		$metadataId = $ajaxResponse->getParameter("metadataId");
		$sql = <<<SQL

SELECT * from mb_metadata where metadata_id = $1

SQL;
		$v = array($metadataId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		$row = array();
		if ($res) {
			$row = db_fetch_assoc($res);
			$resultObj["metadata_id"]= $row['metadata_id']; //serial
			$resultObj["uuid"] = $row["uuid"]; //char
			$resultObj["origin"] = $row["origin"]; //char
			$resultObj["link"] = $row["link"]; //char
			$resultObj["linktype"] = $row["linktype"]; //char
			$resultObj["title"] = $row["title"]; //char -- prefill from layer
			$resultObj["abstract"] = $row["abstract"]; //char - prefill from layer
			$resultObj["format"] = $row["format"]; //char
			$resultObj["ref_system"] = $row["ref_system"]; //char
			$resultObj["spatial_res_type"] = $row["spatial_res_type"]; //integer
			$resultObj["spatial_res_value"] = $row["spatial_res_value"]; //char
			$resultObj["inspire_charset"] = $row["inspire_charset"]; //char
			$resultObj["lineage"] = $row["lineage"]; //text
			$resultObj["tmp_reference_1"] = $row["tmp_reference_1"]; //text
			$resultObj["tmp_reference_2"] = $row["tmp_reference_2"]; //text
			$export2csw = $row["export2csw"]; //boolean
			$resultObj["update_frequency"] = $row["update_frequency"]; //text
			switch ($export2csw) {
				case "t" :
					$resultObj["export2csw"] = true;
					break;
				case "f" :
					$resultObj["export2csw"] = false;
					break;
				default:
				break;	
			}
			$inspire_top_consistence = $row["inspire_top_consistence"]; //boolean
			switch ($inspire_top_consistence) {
				case "t" :
					$resultObj["inspire_top_consistence"] = true;
					break;
				case "f" :
					$resultObj["inspire_top_consistence"] = false;
					break;
				default:
				break;	
			}					
			
		}
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;
	case "updateLayerMetadataAddon" :
		$layerId = $ajaxResponse->getParameter("layerId");
		$metadataId = $ajaxResponse->getParameter("metadataId");
		$data = $ajaxResponse->getParameter("data");
		if ($data->export2csw) {
			$data->export2csw = 't';
		} else {
			$data->export2csw = 'f';
		}
		if ($data->inspire_top_consistence) {
			$data->inspire_top_consistence = 't';
		} else {
			$data->inspire_top_consistence = 'f';
		}
		$sql = <<<SQL

UPDATE mb_metadata SET link = $2, title = $3, abstract = $4, format = $5, ref_system = $6, export2csw = $7, inspire_top_consistence = $8, tmp_reference_1 = $9, tmp_reference_2 = $10, lineage = $11, spatial_res_type = $12, spatial_res_value = $13, inspire_charset = $14, changedate = now(), update_frequency = $15 WHERE metadata_id = $1

SQL;
		$v = array($metadataId, $data->link, $data->title, $data->abstract, $data->format, $data->ref_system, $data->export2csw, $data->inspire_top_consistence, $data->tmp_reference_1, $data->tmp_reference_2, $data->lineage, $data->spatial_res_type, $data->spatial_res_value, $data->inspire_charset, $data->update_frequency);
		$t = array('i','s','s','s','s','s','b','b','s','s','s','s','s','s','s');
		try {
			$res = db_prep_query($sql,$v,$t);
		}
		catch (Exception $e){
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("Could not update metadata object in database!"));
			$ajaxResponse->send();
			die;	
		}
		$ajaxResponse->setMessage("Metadata updated!");
		$ajaxResponse->setSuccess(true);
		break;
	case "insertLayerMetadataAddon" :
		$layerId = $ajaxResponse->getParameter("layerId");
		$data = $ajaxResponse->getParameter("data");
		//normaly the link is only set if a link has been created
		//if a record has been created, the link element will be empty 
		//use this to distinguish between the to different inserts!
		//this insert should also push one entry in the ows_relation_metadata table! - after the insert into md_metadata
		//origin
		if ($data->kindOfMetadataAddOn == 'link') {
			//generate metador entry
			$origin = 'external';
		} else {
			$origin = 'metador';
		}
		//export
		if ($data->export2csw == "on") {
			$data->export2csw = 't';
		} else {
			$data->export2csw = 'f';
		}
		//consistance
		if ($data->inspire_top_consistence == "on") {
			$data->inspire_top_consistence = 't';
		} else {
			$data->inspire_top_consistence = 'f';
		}
		//generate a uuid for the record:
		$uuid = new Uuid();
		//initialize database objects
		$link = '';
		$title = '';
		$abstract = '';
		$format = '';	
		$ref_system = '';
		$export2csw = 'f';
		$inspire_top_consistence = 'f';
		$tmp_reference_1 = '';
		$tmp_reference_2 = '';
		$lineage = '';
		$spatial_res_type = '';
		$spatial_res_value = '';
		$inspire_charset = '';
		$update_frequency = '';
		//read out json objects 
		if (isset($data->link)) {
			$link = $data->link;
		}
		if (isset($data->export2csw)) {
			$export2csw = $data->export2csw;
		} else {
			$export2csw = 'f';
		}
		if (isset($data->title)) {
			$title = $data->title;
		}
		if (isset($data->abstract)) {
			$abstract = $data->abstract;
		}
		if (isset($data->format)) {
			$format = $data->format;
		}
		if (isset($data->ref_system)) {
			$ref_system = $data->ref_system;
		}
		if (isset($data->inspire_top_consistence)) {
			$inspire_top_consistence = $data->inspire_top_consistence;
		}
		if (isset($data->tmp_reference_1)) {
			$tmp_reference_1 = $data->tmp_reference_1;
		}
		if ($tmp_reference_1 == "") {
			$tmp_reference_1 = "2000-01-01";
		}
		if (isset($data->tmp_reference_2)) {
			$tmp_reference_2 = $data->tmp_reference_2;
		}
		if ($tmp_reference_2 == "") {
			$tmp_reference_2 = "2000-01-01";
		}
		if (isset($data->lineage)) {
			$lineage = $data->lineage;
		}
		if (isset($data->spatial_res_type)) {
			$spatial_res_type = $data->spatial_res_type;
		}
		if (isset($data->spatial_res_value)) {
			$spatial_res_value = $data->spatial_res_value;
		}
		if (isset($data->inspire_charset)) {
			$inspire_charset = $data->inspire_charset;
		}
		if (isset($data->update_frequency)) {
			$update_frequency = $data->update_frequency;
		}
		$randomid = new Uuid();
		//Check if origin is external and export2csw is activated!
		if ($origin == 'external' && $export2csw == 't' ) {
			//harvest link from location, parse the content for datasetid and push xml into data column
			//load metadata from link TODO: function from class_wms - generate a class for metadata management and include it here and in class_wms
			$metadataConnector = new connector();
			$metadataConnector->set("timeOut", "5");
			$metaData = $metadataConnector->load($link);
			//$e = new mb_exception($metaData);
			if (!$metaData) {
    				abort(_mb("Could not load metadata from source url!"));
			}

			//***
			//write metadata to temporary file:
			$randomFileId = new Uuid();
				
			$tmpMetadataFile = fopen(TMPDIR.'/link_metadata_file_'.$randomFileId.'.xml', 'w');
			fwrite($tmpMetadataFile, $metaData);
			fclose($tmpMetadataFile);
			$e = new mb_exception("File which has been written: link_metadata_file_".$randomFileId.".xml");
			//read out objects from xml structure
			if (file_exists(TMPDIR.'/link_metadata_file_'.$randomFileId.'.xml')) {
				$iso19139Xml=simplexml_load_file(TMPDIR.'/link_metadata_file_'.$randomFileId.'.xml');
				//$metaData = file_get_contents(TMPDIR.'/link_metadata_file_'.$randomFileId.'.xml');
			} else {
				abort(_mb("Temporary file could not be parsed!"));
			}

			//$metaData = $metadataConnector->file;
			//parse metadata
			/*try {
				$iso19139Xml =  new SimpleXMLElement($metaData);
			}
			catch (Exception $e) {
				abort(_mb("Parsing ISO19139 XML failed!"));
			}*/
			if ($iso19139Xml != false) {
				//get elements for database from xml by using xpath
				//uuid
				$uuid = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString');
				//createdate
				$createdate = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:dateStamp/gco:Date');
				//changedate
				$changedate = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:dateStamp/gco:Date');
				//TODO: check if this is set, maybe DateTime must be searched instead?
				//title
				$title = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString');
				//dataset identifier - howto model into md_metadata?
				//check where datasetid is defined - maybe as RS_Identifier or as MD_Identifier see http://inspire.jrc.ec.europa.eu/documents/Metadata/INSPIRE_MD_IR_and_ISO_v1_2_20100616.pdf page 18
				$code = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:identifier/gmd:RS_Identifier/gmd:code/gco:CharacterString');
				$codeSpace = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:identifier/gmd:RS_Identifier/gmd:codeSpace/gco:CharacterString');
				$e = new mb_notice("class_wms.php: code: ".$code[0]);
				$e = new mb_notice("class_wms.php: codeSpace: ".$codeSpace[0]);
				if (isset($codeSpace[0]) && isset($code[0]) && $codeSpace[0] != '' && $code[0] != '') {
						
					$datasetid = $codeSpace[0]."#".$code[0];
					$e = new mb_exception("class_wms.php: datasetid: ".$datasetid);
				} else {
						
					$datasetid = 'undefined';# in sense of INSPIRE TODO control MD_Identifier too!
					$e = new mb_exception("class_wms.php: datasetid: ".$datasetid);
				}
				//abstract
				$abstract = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString');
				//searchtext -- use keywords!
				$keywords = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword/gco:CharacterString');
				//type 
				$type = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:hierarchyLevel/gmd:MD_ScopeCode');
				//tmp_reference_1
				$tmp_reference_1 = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod/gml:beginPosition');
				//tmp_reference_2
				$tmp_reference_2 = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod/gml:endPosition');		//spatial_res_type
				//spatial_res_value
				//ref_system
				//format
				//inspire_charset
				//inspire_top_consistence
				//responsible_party
				//fees
				//"constraints"	
				//fill database with elements:

				$sql = <<<SQL

INSERT INTO mb_metadata (link, uuid, origin, title, abstract, format, ref_system, export2csw, inspire_top_consistence, tmp_reference_1, tmp_reference_2, lineage, spatial_res_type, spatial_res_value, inspire_charset, createdate, datasetid, randomid, data, harvestresult) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, now(), $16, $17, $18, 1)

SQL;
				$v = array($link, $uuid[0], $origin, $title[0], $abstract[0], $format, $ref_system, $export2csw,$inspire_top_consistence,$tmp_reference_1[0],$tmp_reference_2[0],$lineage,$spatial_res_type,$spatial_res_value,$inspire_charset, $datasetid, $randomid, $metaData);
				$t = array('s','s','s','s','s','s','s','b','b','s','s','s','s','s','s','s','s','s');
		
				try {
					$res = db_prep_query($sql,$v,$t);
				}
				catch (Exception $e){
					abort(_mb("Insert of harvested metadata into database failed!"));
				}
			
			} else {
				abort(_mb("Problem with parsing the XML structure with SimpleXML! Record was not inserted into database! Ask your administrator."));
				//give back error message - cause parsing has problems
			}
		} else { //fill only links into db - do not try to load data
			$sql = <<<SQL

INSERT INTO mb_metadata (link, uuid, origin, title, abstract, format, ref_system, export2csw, inspire_top_consistence, tmp_reference_1, tmp_reference_2, lineage, spatial_res_type, spatial_res_value, inspire_charset, createdate, randomid, update_frequency) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, now(), $16, $17)

SQL;
			$v = array($link, $uuid, $origin, $title, $abstract, $format, $ref_system, $export2csw,$inspire_top_consistence,$tmp_reference_1,$tmp_reference_2,$lineage,$spatial_res_type,$spatial_res_value,$inspire_charset, $randomid, $update_frequency);
			$t = array('s','s','s','s','s','s','s','b','b','s','s','s','s','s','s','s','s');
		
			try {
				$res = db_prep_query($sql,$v,$t);
			}
			catch (Exception $e){
				abort(_mb("Insert of link into database failed!"));
			}
		}
		//set relation into relation table
		//get metadata_id of record which have been inserted before
		$sql = <<<SQL

SELECT metadata_id FROM mb_metadata WHERE randomid = $1

SQL;
		//maybe there are more than one results - which should be used??? case of creating new linkage with old metadata TODO TODO
		$v = array($randomid);
		$t = array('s');
		try {
			$res = db_prep_query($sql,$v,$t);
		}
		catch (Exception $e){
			abort(_mb("Cannot get metadata record with following uuid from database: ".$uuid));
		}
		if ($res) {
			$row = db_fetch_assoc($res);
			$metadata_id = $row['metadata_id'];
		}
		$sql = <<<SQL

		INSERT INTO ows_relation_metadata (fkey_metadata_id, fkey_layer_id) VALUES ($1, $2)

SQL;
		$v = array($metadata_id, $layerId);
		$t = array('i','i');
		try {
			$res = db_prep_query($sql,$v,$t);
		}
		catch (Exception $e){
				abort(_mb("Cannot write relation between ows and metadata to database!"));
		}
		if ($dbInsertFailed != true) {	
			$ajaxResponse->setMessage("Metadata object inserted into md_metadata!");
			$ajaxResponse->setSuccess(true);
		}				
		break;
	case "deleteLayerMetadataAddon" :
		$metadataId = $ajaxResponse->getParameter("metadataId");
		$sql = <<<SQL

DELETE FROM mb_metadata WHERE metadata_id = $1

SQL;
		$v = array($metadataId);
		$t = array('i');
		try {
			$res = db_prep_query($sql,$v,$t);
		}
		catch (Exception $e){
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("Could not delete metadata from database!"));
			$ajaxResponse->send();
			die;	
		}
		$ajaxResponse->setMessage("Metadata deleted!");
		$ajaxResponse->setSuccess(true);
		break;
	case "importLayerXmlAddon" :
//this is similar to insert the metadata from external link, but came from internal file from tmp folder which has been updated before
		$layerId = $ajaxResponse->getParameter("layerId");
		$filename = $ajaxResponse->getParameter("filename");
		//normaly the link is only set if a link has been created
		//if a record has been created, the link element will be empty 
		//use this to distinguish between the to different inserts!
		//this insert should also push one entry in the ows_relation_metadata table! - after the insert into md_metadata
		//origin
		//generate metador entry
		$origin = 'upload';
		//generate a uuid for the record:
		$uuid = new Uuid();
		//initialize database objects
		$link = '';
		$title = '';
		$abstract = '';
		$format = '';	
		$ref_system = '';
		$export2csw = 't';
		$inspire_top_consistence = 'f';
		$tmp_reference_1 = '';
		$tmp_reference_2 = '';
		$lineage = '';
		$spatial_res_type = '';
		$spatial_res_value = '';
		$inspire_charset = '';
		$randomid = new Uuid();	
		$e = new mb_exception("File to load: ".$filename);
		//read out objects from xml structure
		/*if (file_exists($filename)) {
			try {	
				
				$iso19139Xml = simplexml_load_file($filename);
			}
			catch (Exception $e) {
				abort(_mb("Loading ISO19139 XML failed!"));
			}
		}
		else {
			abort(_mb("File not found: ".$filename." !"));
		}*/
		$metaData = file_get_contents($filename);
		if (!$metaData){
			abort(_mb("Reading file ".$filename." failed!"));
		}
		
		try {	
			$iso19139Xml =  new SimpleXMLElement($metaData);
		}
		catch (Exception $e) {
			abort(_mb("Parsing ISO19139 XML failed!"));
		}
		//get elements for database from xml by using xpath
		//uuid
		$uuid = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString');
		//createdate
		$createdate = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:dateStamp/gco:Date');
		//changedate
		$changedate = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:dateStamp/gco:Date');
		//TODO: check if this is set, maybe DateTime must be searched instead?
		//title
		$title = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString');
		//dataset identifier - howto model into md_metadata?
		//check where datasetid is defined - maybe as RS_Identifier or as MD_Identifier see http://inspire.jrc.ec.europa.eu/documents/Metadata/INSPIRE_MD_IR_and_ISO_v1_2_20100616.pdf page 18
		$code = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:identifier/gmd:RS_Identifier/gmd:code/gco:CharacterString');
		$codeSpace = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:identifier/gmd:RS_Identifier/gmd:codeSpace/gco:CharacterString');
		$e = new mb_notice("plugins/mb_metadata_server.php: code: ".$code[0]);
		$e = new mb_notice("plugins/mb_metadata_server.php: codeSpace: ".$codeSpace[0]);
		if (isset($codeSpace[0]) && isset($code[0]) && $codeSpace[0] != '' && $code[0] != '') {		
			$datasetid = $codeSpace[0]."#".$code[0];
			$e = new mb_notice("class_wms.php: datasetid: ".$datasetid);
		} else {		
			$datasetid = 'undefined';# in sense of INSPIRE TODO control MD_Identifier too!
			$e = new mb_notice("class_wms.php: datasetid: ".$datasetid);
		}
		//abstract
		$abstract = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString');
		//searchtext -- use keywords!
		$keywords = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword/gco:CharacterString');
		//type 
		$type = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:hierarchyLevel/gmd:MD_ScopeCode');
		//tmp_reference_1
		$tmp_reference_1 = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod/gml:beginPosition');
		//tmp_reference_2
		$tmp_reference_2 = $iso19139Xml->xpath('/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:temporalElement/gmd:EX_TemporalExtent/gmd:extent/gml:TimePeriod/gml:endPosition');	//spatial_res_type
		//spatial_res_value
		//ref_system
		//format
		//inspire_charset
		//inspire_top_consistence
		//responsible_party
		//fees
		//"constraints"	
		//fill database with elements:
		$sql = <<<SQL

INSERT INTO mb_metadata (link, uuid, origin, title, abstract, format, ref_system, export2csw, inspire_top_consistence, tmp_reference_1, tmp_reference_2, lineage, spatial_res_type, spatial_res_value, inspire_charset, createdate, datasetid, randomid, data, harvestresult) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, now(), $16, $17, $18, 1)

			
SQL;
			
		$xml = $iso19139Xml->asXML();
		$v = array($link, $uuid[0], $origin, $title[0], $abstract[0], $format, $ref_system, $export2csw,$inspire_top_consistence,$tmp_reference_1[0],$tmp_reference_2[0],$lineage,$spatial_res_type,$spatial_res_value,$inspire_charset, $datasetid, $randomid, $xml);
		$t = array('s','s','s','s','s','s','s','b','b','s','s','s','s','s','s','s','s','s');
		
		try {
				$res = db_prep_query($sql,$v,$t);
		}
		catch (Exception $e){
			abort(_mb("Insert of harvested metadata into database failed!"));
		}
	//set relation into relation table
	//get metadata_id of record which have been inserted before
	$sql = <<<SQL

SELECT metadata_id FROM mb_metadata WHERE randomid = $1

SQL;
	//maybe there are more than one results - which should be used??? case of creating new linkage with old metadata TODO TODO
	$v = array($randomid);
	$t = array('s');
	try {
		$res = db_prep_query($sql,$v,$t);
	}
	catch (Exception $e){
		abort(_mb("Cannot get metadata record with following uuid from database: ".$uuid));
	}
	if ($res) {
		$row = db_fetch_assoc($res);
		$metadata_id = $row['metadata_id'];
	}
	$sql = <<<SQL

	INSERT INTO ows_relation_metadata (fkey_metadata_id, fkey_layer_id) VALUES ($1, $2)

SQL;
	$v = array($metadata_id, $layerId);
	$t = array('i','i');
	try {
		$res = db_prep_query($sql,$v,$t);
	}
	catch (Exception $e){
			abort(_mb("Cannot write relation between ows and metadata to database!"));
	}
	if ($dbInsertFailed != true) {	
		$ajaxResponse->setMessage("Metadata object inserted into md_metadata!");
		$ajaxResponse->setSuccess(true);
	}				
	break;		

	default: 
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("An unknown error occured."));
		break;
}

$ajaxResponse->send();
?>
