<?php
require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";
require_once dirname(__FILE__) . "/../classes/class_wfs.php";
require_once(dirname(__FILE__)."/../classes/class_universal_wfs_factory.php");

$ajaxResponse = new AjaxResponse($_POST);

function abort ($message) {
	global $ajaxResponse;
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage($message);
	$ajaxResponse->send();
	die;
};

function getWfs ($wfsId = null) {
	$user = new User(Mapbender::session()->get("mb_user_id"));
	$wfsIdArray = $user->getOwnedWfs();

	if (!is_null($wfsId) && !in_array($wfsId, $wfsIdArray)) {
		abort(_mb("You are not allowed to access this WFS."));
	}
	return $wfsIdArray;
}

function getFeaturetype ($featuretypeId = null) {
	$user = new User(Mapbender::session()->get("mb_user_id"));
	$wfsIdArray = $user->getOwnedWfs();
	if (!is_array($wfsIdArray) || count($wfsIdArray) === 0) {
		abort(_mb("No metadata sets available."));
	}
	$wfsId = wfs::getWfsIdByFeaturetypeId($featuretypeId);
	if (is_null($wfsId) || !in_array($wfsId, $wfsIdArray)) {
		abort(_mb("You are not allowed to access this WFS " . $wfsId));
	}
	return;
}

switch ($ajaxResponse->getMethod()) {
	case "getWfs" :
		$wfsIdArray = getWfs();
		
		$wfsList = implode(",", $wfsIdArray);
		
		$sql = <<<SQL
	
SELECT wfs.wfs_id, wfs.wfs_title, wfs.wfs_timestamp, wfs_version
FROM wfs WHERE wfs_id IN ($wfsList);

SQL;
		$res = db_query($sql);
		$resultObj = array(
			"header" => array(
				"WFS ID",
				"Titel",
				"Timestamp",
				"Version"
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

	case "getWfsMetadata" :
		$wfsId = $ajaxResponse->getParameter("id");
		getWfs($wfsId);

		$sql = <<<SQL
	
SELECT wfs_id, wfs_abstract, wfs_title, fees, accessconstraints, 
individualname, positionname, providername, voice, 
facsimile, deliverypoint, city, 
administrativearea, postalcode, country, electronicmailaddress,
wfs_timestamp, wfs_timestamp_create, wfs_network_access, fkey_mb_group_id 
FROM wfs WHERE wfs_id = $wfsId;

SQL;

		$res = db_query($sql);
		$resultObj = array();
		$row = db_fetch_assoc($res);
		
		$resultObj['wfs_id'] = $row['wfs_id'];
		$resultObj['summary'] = $row['wfs_abstract'];
		$resultObj['title'] = $row['wfs_title'];
		$resultObj['fees'] = $row['fees'];
		$resultObj['accessconstraints'] = $row['accessconstraints'];
		$resultObj['individualName'] = $row['individualname'];
		$resultObj['positionName'] = $row['positionname'];
		$resultObj['providerName'] = $row['providername'];
		$resultObj['voice'] = $row['voice'];
		$resultObj['facsimile'] = $row['facsimile'];
		$resultObj['deliveryPoint'] = $row['deliverypoint'];
		$resultObj['city'] = $row['city'];
		$resultObj['administrativeArea'] = $row['administrativearea'];
		$resultObj['postalCode'] = $row['postalcode'];
		$resultObj['country'] = $row['country'];
		$resultObj['electronicMailAddress'] = $row['electronicmailaddress'];
		$resultObj['timestamp'] = $row['wfs_timestamp'] != "" ? date('d.m.Y', $row['wfs_timestamp']) : "";
		$resultObj['timestamp_create'] = $row['wfs_timestamp_create'] != "" ? date('d.m.Y', $row['wfs_timestamp_create']) : "";
		$resultObj['network_access'] = $row['wfs_network_access'];
		$resultObj['fkey_mb_group_id'] = $row['fkey_mb_group_id'];

		$keywordSql = <<<SQL
	
SELECT DISTINCT keyword FROM keyword, wfs_featuretype_keyword 
WHERE keyword_id = fkey_keyword_id AND fkey_featuretype_id IN (
	SELECT featuretype_id from wfs_featuretype, wfs 
	WHERE fkey_wfs_id = wfs_id AND wfs_id = $wfsId
) ORDER BY keyword

SQL;

		$keywordRes = db_query($keywordSql);
		$keywords = array();
		while ($keywordRow = db_fetch_assoc($keywordRes)) {
			$keywords[]= $keywordRow["keyword"];
		}

		$resultObj["wfs_keywords"] = implode(", ", $keywords);

		$termsofuseSql = <<<SQL
SELECT fkey_termsofuse_id FROM wfs_termsofuse WHERE fkey_wfs_id = $wfsId
SQL;

		$termsofuseRes = db_query($termsofuseSql);
		if ($termsofuseRes) {
			$termsofuseRow = db_fetch_assoc($termsofuseRes);
			$resultObj["wfs_termsofuse"] = $termsofuseRow["fkey_termsofuse_id"];
		}
		else {
			$resultObj["wfs_termsofuse"] = null;
		}

		$resultObj['wfs_network_access'] = $resultObj['wfs_network_access'] == 1 ? true : false;

		//get contact information from group relation
		//check if fkey_mb_group_id has been defined before - in service table
		if ($resultObj["fkey_mb_group_id"] == "" || !isset($resultObj["fkey_mb_group_id"])){
			$e = new mb_notice("fkey_mb_group_id is null or empty");
			//check if primary group is set 
			$user = new User;
			$userId = $user->id;
			$e = new mb_exception("user id:".$userId);
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
	
	case "getFeaturetypeMetadata" :
		$featuretypeId = $ajaxResponse->getParameter("id");
		getFeaturetype($featuretypeId);

		$sql = <<<SQL
	
SELECT featuretype_id, featuretype_name, featuretype_title, featuretype_abstract, featuretype_searchable 
FROM wfs_featuretype WHERE featuretype_id = $featuretypeId;

SQL;
		$res = db_query($sql);

		$resultObj = array();
		while ($row = db_fetch_assoc($res)) {
			foreach ($row as $key => $value) {
				$resultObj[$key] = $value;
			}
		}

		$sql = <<<SQL
SELECT fkey_md_topic_category_id
FROM wfs_featuretype_md_topic_category 
WHERE fkey_featuretype_id = $featuretypeId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["featuretype_md_topic_category_id"][]= $row["fkey_md_topic_category_id"];
		}

		$sql = <<<SQL
SELECT fkey_inspire_category_id 
FROM wfs_featuretype_inspire_category 
WHERE fkey_featuretype_id = $featuretypeId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["featuretype_inspire_category_id"][]= $row["fkey_inspire_category_id"];
		}

		$sql = <<<SQL
SELECT fkey_custom_category_id 
FROM wfs_featuretype_custom_category 
WHERE fkey_featuretype_id = $featuretypeId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["featuretype_custom_category_id"][]= $row["fkey_custom_category_id"];
		}

		$sql = <<<SQL
SELECT keyword FROM keyword, wfs_featuretype_keyword 
WHERE keyword_id = fkey_keyword_id AND fkey_featuretype_id = $featuretypeId
SQL;
		$res = db_query($sql);

		$resultObj["featuretype_keyword"] = array();
		while ($row = db_fetch_assoc($res)) {
			$resultObj["featuretype_keyword"][]= $row["keyword"];
		}

		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;
	case "getFeaturetypeByWfs" :
		$wfsId = $ajaxResponse->getParameter("id");
//		getWms($wmsId);

		$sql = <<<SQL
	
SELECT featuretype_id, featuretype_name, featuretype_title, featuretype_abstract 
FROM wfs_featuretype WHERE fkey_wfs_id = $wfsId ORDER BY featuretype_id;

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
				#"parent" => $row["layer_parent"] !== "" ? intval($row["layer_parent"]) : null,
				#"pos" => intval($row["layer_pos"]),
				"attr" => array (
					"featuretype_id" => intval($row["featuretype_id"]),
					"featuretype_name" => $row["featuretype_name"],
					"featuretype_title" => $row["featuretype_title"],
					"featuretype_abstract" => $row["featuretype_abstract"]
				)
			);
		}

		function addSubTree ($rows, $i, $left) {
			$nodeArray = array();
			$addNewNode = true;
			for ($j = $i; $j < count($rows); $j++) {
				$row = $rows[$j];
				$pos = $j;
				
				// first node of subtree
				if ($addNewNode) {
					$nodeArray[]= createNode($left, null, $row);
					$addNewNode = false;
				}
				else {
					$nodeArray[count($nodeArray)-1]["right"] = ++$left;
					$nodeArray[]= createNode(++$left, null, $row);
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
			$wfsId = intval($data->wfs->wfs_id);
		}
		catch (Exception $e) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("Invalid WFS ID."));
			$ajaxResponse->send();						
		}
		getWfs($wfsId);
		
		$wfsFactory = new UniversalWfsFactory();
		$wfs = $wfsFactory->createFromDb($wfsId, false);
		if (is_null($wfs)) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("Invalid WFS ID."));
			$ajaxResponse->send();	
		}
		
		$columns = array(
			"summary", 
			"title", 
			"fees", 
			"accessconstraints", 
			"individualName", 
			"positionName", 
			"voice", 
			"facsimile", 
			"providerName", 
			"deliveryPoint", 
			"city", 
			"administrativeArea", 
			"postalCode", 
			"country", 
			"electronicMailAddress",
			"wfs_termsofuse",
			"timestamp",
			"timestamp_create",
			"network_access",
			"fkey_mb_group_id",
			"uuid"
		);
		foreach ($columns as $c) {
			$value = $data->wfs->$c;
			if (!is_null($value)) {
				$wfs->$c = $value;
			}
		}
		
		try {
			$featuretypeId = intval($data->featuretype->featuretype_id);
		}
		catch (Exception $e) {
		}

		if ($featuretypeId) {
			$featuretype = $wfs->findFeatureTypeById($featuretypeId);
			if (!is_null($featuretype)) {
				
				$columns = array(
					"summary", 
					"title",
					"featuretype_keyword",
					"featuretype_md_topic_category_id",
					"featuretype_inspire_category_id",
					"featuretype_custom_category_id"
				);			
	
				foreach ($columns as $c) {
					if ($c === "summary") {
						$value = $data->featuretype->featuretype_abstract;
					}
					elseif ($c === "title") {
						$value = $data->featuretype->featuretype_title;
					}
					else {
						$value = $data->featuretype->$c;
					}	
					if ($c === "featuretype_keyword") {
						$featuretype->$c = explode(",", $value);
						foreach ($featuretype->$c as &$val) {
							$val = trim($val);
						}
					}
					elseif ($c === "featuretype_md_topic_category_id" 
						|| $c === "featuretype_inspire_category_id"
						|| $c === "featuretype_custom_category_id"
					) {
						if (!is_array($value)) {
							$featuretype->$c = array($value);
						}
						else {
							$featuretype->$c = $value;
						}
					}
					else {
						if (!is_null($value)) {
							$featuretype->$c = $value;
						}
					}
				}
			}
		}
		if ($wfs->network_access == "on") {
			$wfs->network_access = intval('1');
		} else {
			$wfs->network_access = intval('0');
		}
		$wfs->update();

		
		$ajaxResponse->setMessage("Updated WFS metadata for ID " . $wfsId);
		$ajaxResponse->setSuccess(true);		
		
		break;
	default: 
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("An unknown error occured."));
		break;
}

$ajaxResponse->send();
?>
