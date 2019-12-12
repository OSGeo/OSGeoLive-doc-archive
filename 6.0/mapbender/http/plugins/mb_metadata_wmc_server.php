<?php
require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";
require_once dirname(__FILE__) . "/../classes/class_wmc.php";
require_once(dirname(__FILE__)."/../classes/class_wmc_factory.php");

$ajaxResponse = new AjaxResponse($_POST);

function abort ($message) {
	global $ajaxResponse;
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage($message);
	$ajaxResponse->send();
	die;
};

function getWmc ($wmcId = null) {
	$user = new User(Mapbender::session()->get("mb_user_id"));
	$wmcIdArray = $user->getWmcByOwner();
	//getAccessibleWmcs();

	if (!is_null($wmcId) && !in_array($wmcId, $wmcIdArray)) {
		abort(_mb("You are not allowed to access this WMC."));
	}
	return $wmcIdArray;
}

switch ($ajaxResponse->getMethod()) {
	case "getWmc" :
		$wmcIdArray = getWmc();
		$wmcList = implode(",", $wmcIdArray);
		$sql = <<<SQL
	
SELECT mb_user_wmc.wmc_serial_id as wmc_id, mb_user_wmc.wmc_title, mb_user_wmc.wmc_timestamp, wmc_load_count.load_count FROM mb_user_wmc 
LEFT JOIN wmc_load_count ON wmc_load_count.fkey_wmc_serial_id = mb_user_wmc.wmc_serial_id WHERE wmc_serial_id IN ($wmcList);

SQL;
		$res = db_query($sql);
		$resultObj = array(
			"header" => array(
				"WMC ID",
				"Titel",
				"Timestamp",
				"Load Count",
				""
			), 
			"data" => array()
		);

		while ($row = db_fetch_row($res)) {
			// convert NULL to '', NULL values cause datatables to crash
			$walk = array_walk($row, create_function('&$s', '$s=strval($s);'));
			$link = "<a class='cancelClickEvent' target='_blank' href='../php/mod_showMetadata.php?".
					"languageCode=".Mapbender::session()->get("mb_lang")."&resource=wmc&id=".$row[0]."'>"._mb("Metadata")."</a>";
			array_push($row, $link);
			$resultObj["data"][]= $row;
		}
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;

	case "getWmcMetadata" :
		$wmcId = $ajaxResponse->getParameter("id");
		
		$sql = <<<SQL
	
SELECT wmc_serial_id as wmc_id, abstract, wmc_title, 
wmc_timestamp, wmc_timestamp_create, wmc_public 
FROM mb_user_wmc WHERE wmc_serial_id = $wmcId;

SQL;

		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		
		$resultObj = array();
		$resultObj['wmc_id'] = $row['wmc_id'];
		$resultObj['wmc_abstract'] = $row['abstract'];
		$resultObj['wmc_title'] = $row['wmc_title'];
		$resultObj['wmc_timestamp'] = $row['wmc_timestamp'] != "" ? date('d.m.Y', $row['wmc_timestamp']) : "";
		$resultObj['wmc_timestamp_create'] = $row['wmc_timestamp_create'] != "" ? date('d.m.Y', $row['wmc_timestamp_create']) : "";
		$resultObj['public'] = $row['wmc_public'] == 1 ? true : false;
		$resultObj['linkHref'] = "../php/mod_showMetadata.php?languageCode=".Mapbender::session()->get("mb_lang")."&resource=wmc&id=".$row['wmc_id'];	

		$keywordSql = <<<SQL
	
SELECT DISTINCT keyword FROM keyword, wmc_keyword 
WHERE keyword_id = fkey_keyword_id AND 
fkey_wmc_serial_id = $wmcId ORDER BY keyword

SQL;

		$keywordRes = db_query($keywordSql);
		$keywords = array();
		while ($keywordRow = db_fetch_assoc($keywordRes)) {
			$keywords[]= $keywordRow["keyword"];
		}

		$resultObj["wmc_keyword"] = implode(", ", $keywords);

		$sql = <<<SQL
SELECT fkey_md_topic_category_id
FROM wmc_md_topic_category 
WHERE fkey_wmc_serial_id = $wmcId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["isoTopicCats"][]= $row["fkey_md_topic_category_id"];
		}

		$sql = <<<SQL
SELECT fkey_inspire_category_id 
FROM wmc_inspire_category 
WHERE fkey_wmc_serial_id = $wmcId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["inspireCats"][]= $row["fkey_inspire_category_id"];
		}

		$sql = <<<SQL
SELECT fkey_custom_category_id 
FROM wmc_custom_category 
WHERE fkey_wmc_serial_id = $wmcId
SQL;
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$resultObj["customCats"][]= $row["fkey_custom_category_id"];
		}
		
		$ajaxResponse->setResult($resultObj);
		$ajaxResponse->setSuccess(true);
		break;
		
	case "save":
		global $firephp;
		$data = $ajaxResponse->getParameter("data");
		
		try {
			$wmcId = intval($data->wmc->wmc_id);
		}
		catch (Exception $e) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("Invalid WMC ID."));
			$ajaxResponse->send();						
		}
		$wmcFactory = new WmcFactory();
		$wmc = $wmcFactory->createFromDb($wmcId);

		if (is_null($wmc)) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("Invalid WMC ID."));
			$ajaxResponse->send();	
		}
		
		$columns = array(
			"wmc_abstract", 
			"wmc_title",
			"wmc_keyword",
			"isoTopicCats",
			"inspireCats",
			"customCats",
			"public"
		);
		
		foreach ($columns as $c) {
			$value = $data->wmc->$c;
			if ($c === "wmc_keyword") {
				$wmc->$c = explode(",", $value);
				foreach ($wmc->$c as &$val) {
					$val = trim($val);
				}
				if(!$value) {
					$wmc->$c = array();
				}
			}
			elseif ($c === "isoTopicCats" 
				|| $c === "inspireCats"
				|| $c === "customCats"
			) {
				if (!is_array($value)) {
					if(!$value) {
						$wmc->$c = array();
					}
					else {
						$wmc->$c = array($value);
					}
				}
				else {
					$wmc->$c = $value;
				}
			}
			elseif ($c === "public") {
				$public = $value == "on"  ? true : false;
				$wmc->setPublic($public);
			}
			else {
				if (!is_null($value)) {
					$wmc->$c = $value;
				}
			}
		}
		
		//$firephp->log($wmc->public);
		$overwrite = 1;
		$wmc->insert($overwrite);
		
		$ajaxResponse->setMessage("Updated WMC metadata for ID " . $wmcId);
		$ajaxResponse->setSuccess(true);		
		
		break;
	default: 
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("An unknown error occured."));
		break;
}

$ajaxResponse->send();
?>
