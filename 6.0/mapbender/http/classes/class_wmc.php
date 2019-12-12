<?php
# $Id: class_wmc.php 8432 2012-07-06 10:19:22Z astrid_emde $
# http://www.mapbender.org/index.php/class_wmc.php
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

require_once(dirname(__FILE__) . "/../classes/class_wms.php");
require_once(dirname(__FILE__) . "/../classes/class_wfs_conf.php");
require_once(dirname(__FILE__) . "/../classes/class_layer_monitor.php");
require_once(dirname(__FILE__) . "/../classes/class_point.php");
require_once(dirname(__FILE__) . "/../classes/class_bbox.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");
require_once(dirname(__FILE__) . "/../classes/class_map.php");
require_once(dirname(__FILE__) . "/../classes/class_administration.php");
require_once(dirname(__FILE__) . "/../classes/class_wmcToXml.php");
require_once(dirname(__FILE__) . "/../classes/class_user.php");

/**
 * Implementation of a Web Map Context Document, WMC 1.1.0
 *
 * Use cases:
 *
 * Instantiation (1) create a WMC object from a WMC XML document
 * 		$myWmc = new wmc();
 * 		$myWmc->createFromXml($xml);
 *
 *    If you want to create a WMC object from a WMC in the database
 *    	$xml = wmc::getDocument($wmcId);
 * 		$myWmc = new wmc();
 * 		$myWmc->createFromXml($xml);
 *
 *
 * Instantiation (2) create a WMC from the client side
 * 		$myWmc = new wmc();
 * 		$myWmc->createFromJs($mapObject, $generalTitle, $extensionData);
 *
 * 	  	(creates a WMC from the JS data and then creates an object from that WMC)
 *
 * Output (1) (do Instantiation first) Load a WMC into client
 * 		This will return an array of JS statements
 *
 * 		$myWmc->toJavaScript();
 *
 * Output (2) (do Instantiation first) Merge with another WMC, then load
 *
 * 		$myWmc->merge($anotherWmcXml);
 * 		$myWmc->toJavaScript();
 *
 */
class wmc {
/**
 * Representing the main map in a map application
 * @var Map
 */
	var $mainMap;

	/**
	 * Representing an (optional) overview map in a map application
	 * @var Map
	 */
	var $overviewMap;

	/**
	 * @var Array
	 */
	var $generalExtensionArray = array();

	/**
	 * The XML representation of this WMC.
	 * @var String
	 */
	var $xml;

	// constants
	var $saveWmcAsFile = false;
	var $extensionNamespace = "mapbender";
	var $extensionNamespaceUrl = "http://www.mapbender.org/context";

	// set in constructor
	var $wmc_id;
	var $userId;
	var $timestamp;
	var $public;

	// set during parsing
	var $wmc_version;
	var $wmc_name;
	var $wmc_title;
	var $wmc_abstract;
	var $wmc_srs;
	var $wmc_extent;
	var $wmc_keyword = array();
	var $wmc_contactposition;
	var $wmc_contactvoicetelephone;
	var $wmc_contactemail;
	var $wmc_contactfacsimiletelephone;
	var $wmc_contactperson;
	var $wmc_contactorganization;
	var $wmc_contactaddresstype;
	var $wmc_contactaddress;
	var $wmc_contactcity;
	var $wmc_contactstateorprovince;
	var $wmc_contactpostcode;
	var $wmc_contactcountry;
	var $wmc_logourl;
	var $wmc_logourl_format;
	var $wmc_logourl_type;
	var $wmc_logourl_width;
	var $wmc_logourl_height;
	var $wmc_descriptionurl;
	var $wmc_descriptionurl_format;
	var $wmc_descriptionurl_type;

	var $inspireCats;
	var $isoTopicCats;
	var $customCats;

	public function __construct () {
		$this->userId = Mapbender::session()->get("mb_user_id");
		$this->timestamp = time();
	}

	// ---------------------------------------------------------------------------
	// INSTANTIATION
	// ---------------------------------------------------------------------------

	/**
	 * Parses the XML string and instantiates the WMC object.
	 *
	 * @param $xml String
	 */
	public function createFromXml ($xml) {
		return $this->createObjFromWMC_xml($xml);
	}

	/**
	 * Loads a WMC from the database.
	 *
	 * @param integer $wmc_id the ID of the WMC document in the database table "mb_user_wmc"
	 */
	function createFromDb($wmcId) {
		$doc = wmc::getDocument($wmcId);
		if ($doc === false) {
			return false;
		}
		$this->createObjFromWMC_xml($doc);
		$sql = "SELECT wmc_timestamp, wmc_title, wmc_public, srs, minx, miny, maxx, maxy " .
			"FROM mb_user_wmc WHERE wmc_serial_id = $1 AND (fkey_user_id = $2 OR wmc_public = 1)";
		$v = array($wmcId, Mapbender::session()->get("mb_user_id"));
		$t = array("i", "i");

		$res = db_prep_query($sql,$v,$t);
		if(db_error()) { return false; }
		if($row = db_fetch_row($res)) {
			$this->wmc_id = $wmcId;
			$this->timestamp = $row[0];
			$this->title = $row[1];
			$this->public = $row[2];
			$this->wmc_srs = $row[3];
			$this->wmc_extent->minx = $row[4];
			$this->wmc_extent->miny = $row[5];
			$this->wmc_extent->maxx = $row[6];
			$this->wmc_extent->maxy = $row[7];
			return true;
		}
		return false;
	}

	public function createFromApplication ($appId) {
	// get the map objects "overview" and "mapframe1"
		$this->mainMap = map::selectMainMapByApplication($appId);
		$this->overviewMap = map::selectOverviewMapByApplication($appId);

		// a  wWFS is basically just a vectorlayer, and a WFSconf is just a configured WFS,
		// so it makes sense to attach the WFSCONFIDstring to to the mapobject
		// this clearly needs a better solution though...
		try{
			$ev = new ElementVar($appId,"mapframe1","wfsConfIdString");
			$this->generalExtensionArray['WFSCONFIDSTRING'] = $ev->value;
		}catch(Exception $E){
			// ... exceprtions are a terribkle way to do this, but I am not going to rewrite the ElementVar class
			$this->generalExtensionArray['WFSCONFIDSTRING'] = "";
		}

		$this->createXml();
		$this->saveAsFile();
	}

	/**
	 * Creates a WMC object from a JS map object {@see map_obj.js}
	 *
	 * @param object $mapObject a map object
	 * @param integer $user_id the ID of the current user
	 * @param string $generalTitle the desired title of the WMC
	 * @param object $extensionData data exclusive to Mapbender, which will be
	 * 								mapped into the extension part of the WMC
	 */
	public function createFromJs($mapObject, $generalTitle,$extensionData, $id=null) {

		if (count($mapObject) > 2) {
			$e = new mb_exception("Save WMC only works for two concurrent map frames (overview plus main) at the moment.");
		}

		// set extension data
		$this->generalExtensionArray = $extensionData;

		if ($id) {
		//set id
			$this->wmc_id = $id;
		}

		// set title
		$this->wmc_title = $generalTitle;

		// create the map objects
		for ($i = 0; $i < count($mapObject); $i++) {
			$currentMap = new Map();
			$currentMap->createFromJs($mapObject[$i]);

			if (isset($mapObject[$i]->isOverview)) {
				$this->overviewMap = $currentMap;
			}
			else {
				$this->mainMap = $currentMap;
			}
		}


		// create XML
		$this->createXml();

		$this->saveAsFile();
		return true;
	}

	// ---------------------------------------------------------------------------
	// DATABASE FUNCTIONS
	// ---------------------------------------------------------------------------
	public function getPublicWmcIds () {
		$sql = "SELECT wmc_serial_id FROM mb_user_wmc ";
		$sql .= "WHERE wmc_public = 1 GROUP BY wmc_serial_id";
		$res_wmc = db_query($sql);

		$wmcArray = array();
		while($row = db_fetch_array($res_wmc)) {
			array_push($wmcArray, $row["wmc_serial_id"]);
		}
		return $wmcArray;
	}

	public function getAccessibleWmcs ($user) {
		$wmcArray = array();

		// get WMC ids
		$wmcOwnerArray = $user->getWmcByOwner();

		$publicWmcIdArray = $this->getPublicWmcIds();

		return array_keys( array_flip(array_merge($wmcOwnerArray, $publicWmcIdArray)));
	}

	public function selectByUser ($user, $showPublic=0) {
		$wmcArray = array();

		// get WMC ids
		$wmcOwnerArray = $user->getWmcByOwner();
		if ($showPublic==1) {
			$publicWmcIdArray = self::getPublicWmcIds();
			$wmcIdArray = array_keys( array_flip(array_merge($wmcOwnerArray, $publicWmcIdArray)));
		} else {
			$publicWmcIdArray = array();
			$wmcIdArray=$wmcOwnerArray;
		}
		// get WMC data
		$v = array();
		$t = array();
		$wmcIdList = "";

		for ($i = 0; $i < count($wmcIdArray); $i++) {
			if ($i > 0) {
				$wmcIdList .= ",";
			}
			$wmcIdList .= "$".($i+1);
			array_push($v, $wmcIdArray[$i]);
			array_push($t, 's');
		}

		if ($wmcIdList !== "") {
			$sql = "SELECT DISTINCT wmc_serial_id, wmc_title, wmc_timestamp, wmc_timestamp_create, wmc_public, abstract FROM mb_user_wmc ";
			$sql .= "WHERE wmc_serial_id IN (" . $wmcIdList . ") ";
			$sql .=	"ORDER BY wmc_timestamp DESC";

			$res = db_prep_query($sql, $v, $t);
			while($row = db_fetch_assoc($res)) {
				$currentResult = array();
				$currentResult["id"] = $row["wmc_serial_id"];
				$currentResult["abstract"] = $row["abstract"];
				$currentResult["title"] = administration::convertIncomingString($row["wmc_title"]);
				$currentResult["timestamp"] = date("M d Y H:i:s", $row["wmc_timestamp"]);
				$currentResult["timestamp_create"] = date("M d Y H:i:s", $row["wmc_timestamp_create"]);
				$currentResult["isPublic"] = $row["wmc_public"] == 1? true: false;
				$currentResult["disabled"] = ((
					in_array($currentResult["id"], $publicWmcIdArray) &&
					!in_array($currentResult["id"], $wmcOwnerArray)) || $user->isPublic()) ?
					true : false;

				// get categories
				$currentResult["categories"] = $this->getCategoriesById($currentResult["id"], $user);
				$currentResult["keywords"] = $this->getKeywordsById($currentResult["id"], $user);
				array_push($wmcArray, $currentResult);
			}
		}
		return $wmcArray;
	}

	private function getKeywordsById ($id, $user) {
		$wmcArray = $this->getAccessibleWmcs($user);
		if (!in_array($id, $wmcArray)) {
			return array();
		}

		$keywordArray = array();

		$sql = "SELECT DISTINCT k.keyword FROM keyword AS k, wmc_keyword AS w " .
			"WHERE w.fkey_keyword_id = k.keyword_id AND w.fkey_wmc_serial_id = $1";
		$v = array($id);
		$t = array("s");
		$res = db_prep_query($sql, $v, $t);
		while ($row = db_fetch_array($res)) {
			$keywordArray[]= $row["keyword"];
		}
		
		return $keywordArray;
	}

	private function getCategoriesById ($id, $user) {
		$wmcArray = $this->getAccessibleWmcs($user);
		if (!in_array($id, $wmcArray)) {
			return array();
		}

		$categoryArray = array();

		$sql = "SELECT DISTINCT t.md_topic_category_id FROM " .
			"md_topic_category AS t, wmc_md_topic_category AS w " .
			"WHERE w.fkey_md_topic_category_id = t.md_topic_category_id " .
			"AND w.fkey_wmc_serial_id = $1";
		$v = array($id);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);
		while ($row = db_fetch_array($res)) {
			$categoryArray[]= $row["md_topic_category_id"];
		}
		return $categoryArray;
	}

	private function compareWms ($a, $b) {
		if ($a["id"] === $b["id"]) return 0;
		return -1;
	}
	public function getAllWms () {
		$wmsArray = $this->mainMap->getWmsArray();
		$resultObj = array();
		$usedIds = array();
		for ($i = 0; $i < count($wmsArray); $i++) {
			if (in_array($wmsArray[$i]->wms_id, $usedIds)) {
				continue;
			}
			$resultObj[]= array(
				"title" => $wmsArray[$i]->wms_title,
				"id" => is_null($wmsArray[$i]->wms_id) ? null : intval($wmsArray[$i]->wms_id),
				"index" => $i
			);
			$usedIds[]= $wmsArray[$i]->wms_id;
		}
		return $resultObj;
	}

	public function getWmsWithoutId () {
		$wmsArray = $this->getAllWms();
		$resultObj = array();

		for ($i = 0; $i < count($wmsArray); $i++) {
			if (is_numeric($wmsArray[$i]["id"])) {
				continue;
			}
			$resultObj[]= array(
				"title" => $wmsArray[$i]["title"],
				"id" => $wmsArray[$i]["id"],
				"index" => $i
			);
		}
		return $resultObj;
	}

	public function getWmsWithId () {
		return array_values(array_udiff(
			$this->getAllWms(),
			$this->getWmsWithoutId(),
			array("wmc", "compareWms")
		));
	}

	public function getValidWms () {
		$inv = $this->getInvalidWms();
		$withId = $this->getWmsWithId();
		return array_values(array_udiff(
			$withId,
			$inv,
			array("wmc", "compareWms")
		));
	}

	public function getInvalidWms () {
		$resultObj = array();

		$wmsArray = $this->getWmsWithId();
		for ($i = 0; $i < count($wmsArray); $i++) {

			$sql = "SELECT COUNT(wms_id) FROM wms WHERE wms_id = $1";
			$v = array($wmsArray[$i]["id"]);
			$t = array('i');
			$res = db_prep_query($sql, $v, $t);
			$layerRow = db_fetch_row($res);
			if (intval($layerRow[0]) === 0) {
				$resultObj[]= array(
					"title" => $wmsArray[$i]["title"],
					"id" => intval($wmsArray[$i]["id"]),
					"index" => $wmsArray[$i]["index"]
				);
			}
		}
		return $resultObj;
	}

	public function getWmsWithPermission ($user) {
		$wmsArray = $this->getValidWms();
		$resultObj = array();

		for ($i = 0; $i < count($wmsArray); $i++) {
			$currentWmsId = intval($wmsArray[$i]["id"]);

			if ($user->isWmsAccessible($currentWmsId)) {
				$resultObj[]= array(
					"title" => $wmsArray[$i]["title"],
					"id" => intval($currentWmsId),
					"index" => $wmsArray[$i]["index"]
				);
			}
		}
		return $resultObj;
	}

	public function getWmsWithoutPermission ($user) {
		return array_values(array_udiff(
		$this->getValidWms(),
		$this->getWmsWithPermission($user),
		array("wmc", "compareWms")
		));
	}

	public function getAvailableWms ($user) {
		return array_values(array_udiff(
		$this->getWmsWithPermission($user),
		$this->getUnavailableWms(),
		array("wmc", "compareWms")
		));
	}

	public function getUnavailableWms ($user) {
		$wmsArray = $this->getWmsWithPermission($user);
		$resultObj = array();

		for ($i = 0; $i < count($wmsArray); $i++) {
			$currentWmsId = $wmsArray[$i]["id"];

			$sql = "SELECT last_status FROM mb_wms_availability WHERE fkey_wms_id = $1";
			$v = array($currentWmsId);
			$t = array("i");
			$res = db_prep_query($sql, $v, $t);
			$statusRow = db_fetch_row($res);
			$status = intval($statusRow[0]);
			if (isset($status) && $status == -1) {
				$resultObj[]= array(
					"title" => $wmsArray[$i]["title"],
					"id" => $currentWmsId,
					"index" => $wmsArray[$i]["index"]
				);
			}
		}
		return $resultObj;
	}

	public function updateUrlsFromDb () {
		$query_mbWMSId = "/wmc:ViewContext/wmc:LayerList/wmc:Layer/wmc:Extension/mapbender:wms_id";
		try {
			$WMCDoc = DOMDocument::loadXML($this->toXml());
		}
		catch (Exception $E) {
			new mb_exception("WMC XML is broken.");
		}

		$xpath = new DOMXPath($WMCDoc);
		$xpath->registerNamespace("wmc","http://www.opengis.net/context");
		$xpath->registerNamespace("mapbender","http://www.mapbender.org/context");
		$xpath->registerNamespace("xlink","http://www.w3.org/1999/xlink");

		$WMSIdList = $xpath->query($query_mbWMSId);
		foreach($WMSIdList as $WMSId) {
			$id =  $WMSId->nodeValue;
			$sql = "SELECT wms_timestamp,wms_getmap,wms_getlegendurl, wms_owsproxy " .
				"FROM wms WHERE wms_id = $1";// AND " .
				//"wms_owsproxy <> NULL AND wms_owsproxy <> ''";
			$v = array($id);
			$t = array("t");

			$res = db_prep_query($sql,$v,$t);
			if (db_error()) {
				true;
			} //FIMXE: PROPER ERROR MESSAGE

			if($row = db_fetch_array($res)) {
				//check if ows_proxy is set
				if (isset($row["wms_owsproxy"]) && $row["wms_owsproxy"] != '') {
					//set relevant wms urls to owsproxy urls
					$wmsowsproxy = $row["wms_owsproxy"];
					$owsproxyurl = OWSPROXY."/".session_id()."/".$wmsowsproxy."?";
					$wmsGetMapUrl = $owsproxyurl;
					$wmsGetLegendUrl = $owsproxyurl;
					//in the case of owsproxy the urls should be exchanged every time!
					$MapResources = $xpath->query("../../wmc:Server/wmc:OnlineResource",$WMSId);
					foreach($MapResources as $MapResource) {
						$MapResource->setAttribute("xlink:href",$wmsGetMapUrl);
					}

					$LegendResources = $xpath->query("../../wmc:StyleList/wmc:Style/wmc:LegendURL/wmc:OnlineResource",$WMSId);
					foreach ($LegendResources as $LegendResource) {
							$e = new mb_exception("class_wmc.php: old getlegendurl (WMC): ".$LegendResource->getAttribute("xlink:href"));
							$e = new mb_exception("class_wmc.php: new getlegendurl (OWSPROXY): ".$wmsGetLegendUrl);
							//get param part of getlegend url from xml
							$arURL = parse_url($LegendResource->getAttribute("xlink:href"));
							$query = $arURL["query"];
							$url = $wmsGetLegendUrl . $query;
							$LegendResource->setAttribute("xlink:href",$url);
					}
				} else { //service is not secured - exchange urls only when they may have changed
					$wmsGetMapUrl = $row["wms_getmap"];
					$wmsGetLegendUrl = $row["wms_getlegendurl"];
					
					//in cases when no owsproxy is defined exchange only if timestamp are not in sync
					$wms_timestamp = $row["wms_timestamp"];
					//if ($this->timestamp < $wms_timestamp) {//TODO: check if such a distiction is really needful - the timestamp of a wmc changes if some metadata changes - therefor it is needed - exchange all urls by default! 
					if (false) {
						// wmc is fresh, life is good
						$e = new mb_notice("class_wmc.php: the wms metadata has not been changed - no url will be exchanged!");
					}
					else {
						$MapResources = $xpath->query("../../wmc:Server/wmc:OnlineResource",$WMSId);
						$e = new mb_notice("class_wmc.php: the wms is newer than the wmc -urls will be exchanged!");
						foreach($MapResources as $MapResource) {
							$MapResource->setAttribute("xlink:href",$wmsGetMapUrl);
						}

						//$LegendResources = $xpath->query("../../wmc:StyleList/wmc:Style/wmc:LegendURL/wmc:OnlineResource",$WMSId);
						
						/*foreach ($LegendResources as $LegendResource) {
							$e = new mb_exception("class_wmc.php: old getlegendurl (WMC): ".$LegendResource->getAttribute("xlink:href"));
							$e = new mb_exception("class_wmc.php: new getlegendurl (DB): ".$wmsGetLegendUrl);
							//get param part of getlegend url from xml
							$arURL = parse_url($LegendResource->getAttribute("xlink:href"));
							$query = $arURL["query"];
							$url = $wmsGetLegendUrl . $query;
							$LegendResource->setAttribute("xlink:href",$url);
						}*/
					}
				}
				
			}
		}
		$updatedWMC = $WMCDoc->saveXML();
		$this->update_existing($updatedWMC,$wmsId);
		return $updatedWMC;
	}

	/**
	 * Stores this WMC in the database. The WMC has to be instantiated first, see above.
	 *
	 * @return mixed[] an assoc array with attributes "success" (boolean) and "message" (String).
	 */
	public function insert ($overwrite) {
		$result = array();

		if ($this->userId && $this->xml && $this->wmc_title) {
			try {
				$user = new user($this->userId);
			}
			catch (Exception $E) {
				$errMsg = "Error while saving WMC document " . $this->wmc_title . "': Invalid UserId";
				$result["success"] = false;
				$result["message"] = $errMsg;
				$e = new mb_exception("mod_insertWMCIntoDB: " . $errMsg);
				return $result;
			}

			$overwrite  = ($user->isPublic())? false: $overwrite;

			//put keywords into Document
			try {
				$WMCDoc = DOMDocument::loadXML($this->toXml());
			}
			catch (Exception $E) {
				new mb_exception("WMC XML is broken.");
			}

			$xpath = new DOMXPath($WMCDoc);
			$xpath->registerNamespace("wmc","http://www.opengis.net/context");
			$xpath->registerNamespace("mapbender","http://www.mapbender.org/context");
			$xpath->registerNamespace("xlink","http://www.w3.org/1999/xlink");

			$query_KeywordList = "/wmc:ViewContext/wmc:General/wmc:KeywordList";
			$query_general = "/wmc:ViewContext/wmc:General";
			$DocKeywordLists = $xpath->query($query_KeywordList);
			// we just use a single <general> element

			$NewKeywordList = $WMCDoc->createElementNS('http://opengis.net/context', 'wmc:KeywordList');
			$WMCDoc->appendChild($NewKeywordList);
			
			foreach($this->wmc_keyword as $keyword) {
				$Keyword = $WMCDoc->createElementNS('http://opengis.net/context', 'wmc:Keyword', $keyword);
				$NewKeywordList->appendChild($Keyword);
			}

			$generalList = $xpath->query($query_general);
			$general = $generalList->item(0);
			
			if($DocKeywordLists->item(0)) {
				$tmpNode = $WMCDoc->importNode($DocKeywordLists->item(0),true);
				$general->replaceChild($NewKeywordList,$tmpNode);
			}
			else {
				$tmpNode = $WMCDoc->importNode($NewKeywordList,true);
				$general->appendChild($tmpNode);
			}

			$this->xml  = $WMCDoc->saveXML();

			db_begin();

			if($overwrite) {

				$findsql = "SELECT fkey_user_id,wmc_title,wmc_timestamp, wmc_serial_id FROM mb_user_wmc WHERE fkey_user_id = $1 AND wmc_serial_id = $2 ORDER BY wmc_timestamp DESC LIMIT 1;";
				$v = array($this->userId, $this->wmc_id);
				$t = array("i","i");

				$res = db_prep_query($findsql,$v,$t);
				if (db_error()) {
					$errMsg = "Error while saving WMC document '" . $this->wmc_title . "': " . db_error();
					$result["success"] = false;
					$result["message"] = $errMsg;
					$e = new mb_exception("mod_insertWMCIntoDB: " . $errMsg);
					return $result;
				}

				if($row = db_fetch_row($res)) {
					$sql = "UPDATE mb_user_wmc SET wmc = $1, wmc_timestamp = $2, abstract = $3, srs = $4, minx = $5, miny = $6,".
						" maxx = $7, maxy = $8, wmc_title = $9 WHERE fkey_user_id = $10 AND wmc_serial_id=$11 AND wmc_timestamp = $12;";
					$v = array($this->xml, time(), $this->wmc_abstract, $this->wmc_srs, $this->wmc_extent->minx, $this->wmc_extent->miny,
						$this->wmc_extent->maxx, $this->wmc_extent->maxy ,administration::convertOutgoingString($this->wmc_title), $this->userId, $this->wmc_id,$row[2]);
					$t = array("s", "s","s","s","i","i","i","i", "s", "i", "i","s");
					$res = db_prep_query($sql, $v, $t);
					// need the database Id
					$wmc_DB_ID = $row[3];
					$delsqlCustomTopic = "DELETE FROM wmc_custom_category WHERE fkey_wmc_serial_id = $1;";
					$delvCustomTopic = array($wmc_DB_ID);
					$deltCustomTopic = array("s");
					db_prep_query($delsqlCustomTopic, $delvCustomTopic,$deltCustomTopic);

					$delsqlInspireTopic = "DELETE FROM wmc_inspire_category WHERE fkey_wmc_serial_id = $1;";
					$delvInspireTopic= array($wmc_DB_ID);
					$deltInspireTopic = array("s");
					db_prep_query($delsqlInspireTopic, $delvInspireTopic,$deltInspireTopic);
					
					$delsql = "DELETE FROM wmc_md_topic_category WHERE fkey_wmc_serial_id = $1;";
					$delv = array($wmc_DB_ID);
					$delt = array("s");
					db_prep_query($delsql, $delv,$delt);
					
					$delkwsql = "DELETE FROM wmc_keyword WHERE fkey_wmc_serial_id = $1;";
					$delkwv = array($wmc_DB_ID);
					$delkwt = array("s");
					db_prep_query($delkwsql, $delkwv,$delkwt);
				}
				else {
					$sql = "SELECT max(wmc_serial_id) AS i FROM mb_user_wmc";
					$res = db_query($sql);
					$row = db_fetch_assoc($res);
					$wmc_DB_ID_new = intval($row["i"])+1;

					$sql = "INSERT INTO mb_user_wmc (" .
						"wmc_id, fkey_user_id, wmc, wmc_title, wmc_public, wmc_timestamp, wmc_timestamp_create, " .
						"abstract, srs, minx, miny, maxx, maxy, wmc_serial_id".
						") VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14);";
					$v = array(time(), $this->userId, $this->xml, administration::convertOutgoingString($this->wmc_title), $this->isPublic()?1:0,time(),time(),
						$this->wmc_abstract, $this->wmc_srs, $this->wmc_extent->minx,  $this->wmc_extent->miny, $this->wmc_extent->maxx, $this->wmc_extent->maxy, $wmc_DB_ID_new);
					$t = array("s", "i", "s", "s", "i", "s","s", "s","s","i","i","i", "i", "i");
					$res = db_prep_query($sql, $v, $t);
					
					//$sql = "SELECT max(wmc_serial_id) AS i FROM mb_user_wmc";
					//$res = db_query($sql);
					//$row = db_fetch_assoc($res);
					//$wmc_DB_ID = intval($row["i"]);
				}
			}
			//if overwrite = false
			else {
				$sql = "SELECT max(wmc_serial_id) AS i FROM mb_user_wmc";
				$res = db_query($sql);
				$row = db_fetch_assoc($res);
				$wmc_DB_ID_new = intval($row["i"])+1;

				$sql = "INSERT INTO mb_user_wmc (" .
					"wmc_id, fkey_user_id, wmc, wmc_title, wmc_public, wmc_timestamp, wmc_timestamp_create, " .
					"abstract, srs, minx, miny, maxx, maxy, wmc_serial_id".
					") VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14);";
				//$e = new mb_exception($sql);
				$v = array(time(), $this->userId, $this->xml, administration::convertOutgoingString($this->wmc_title), $this->isPublic()?1:0, time(),time(),
					$this->wmc_abstract, $this->wmc_srs, $this->wmc_extent->minx,  $this->wmc_extent->miny, $this->wmc_extent->maxx, $this->wmc_extent->maxy, $wmc_DB_ID_new);
				$t = array("s", "i", "s", "s", "i", "s","s", "s","s","i","i","i", "i", "i");
				$res = db_prep_query($sql, $v, $t);
				
				
			}

			if (db_error()) {
				$errMsg = "Error while saving WMC document '" . $this->wmc_title . "': " . db_error();
				$result["success"] = false;
				$result["message"] = $errMsg;
				$e = new mb_exception("mod_insertWMCIntoDB: " . $errMsg);
			}
			else {
			// because the wmc id is created each time a wmc is instantiated $this->wmc_id has nothing to do with the database wmc_id
			// this is some duct tape to fix it :-(
			// see also above where wmc_DB_ID is defined if we need to update
				if(!isset($wmc_DB_ID_new)) { $wmc_DB_ID_new = $this->wmc_id; }

				// update keywords
				foreach($this->wmc_keyword as $keyword) {

				// if a keyword does not yet exist, create it
					$keywordExistsSql = "SELECT keyword FROM keyword WHERE keyword = $1";
					$keywordCreateSql = "INSERT INTO keyword (keyword) VALUES($1);";
					$v = array($keyword);
					$t = array("s");
					$res = db_prep_query($keywordExistsSql,$v,$t);
					if(db_num_rows($res) == 0) {
						$res = db_prep_query($keywordCreateSql,$v,$t);
						if($a = db_error()) {
						}
					}
					
					$keywordsql = <<<SQL
INSERT INTO wmc_keyword (fkey_keyword_id,fkey_wmc_serial_id)
	SELECT keyword.keyword_id,$1 FROM keyword
	WHERE keyword = $2 AND keyword.keyword_id NOT IN (
		SELECT fkey_keyword_id FROM wmc_keyword WHERE fkey_wmc_serial_id = $3
	)
SQL;
					$v = array($wmc_DB_ID_new, $keyword,$wmc_DB_ID_new);
					$t = array("s","s","s");
					$res = db_prep_query($keywordsql, $v, $t);
					if($a = db_error()) {
					}
				}
				
				// update iso topic categories
				$this->isoTopicCats = $this->isoTopicCats? $this->isoTopicCats: array();
				foreach($this->isoTopicCats as $catId) {

					$catSql = "INSERT INTO wmc_md_topic_category (fkey_wmc_serial_id, fkey_md_topic_category_id) VALUES ($1,$2)";
					$v = array($wmc_DB_ID_new, $catId);
					$t = array("s","s");
					$res = db_prep_query($catSql, $v, $t);

				}
				
				// update inspire categories
				$this->inspireCats = $this->inspireCats? $this->inspireCats: array();
				foreach($this->inspireCats as $catId) {

					$catSql = "INSERT INTO wmc_inspire_category (fkey_wmc_serial_id, fkey_inspire_category_id) VALUES ($1,$2)";
					$v = array($wmc_DB_ID_new, $catId);
					$t = array("s","s");
					$res = db_prep_query($catSql, $v, $t);

				}
				
				// update custom categories
				$this->customCats = $this->customCats? $this->customCats: array();
				foreach($this->customCats as $catId) {

					$catSql = "INSERT INTO wmc_custom_category (fkey_wmc_serial_id, fkey_custom_category_id) VALUES ($1,$2)";
					$v = array($wmc_DB_ID_new, $catId);
					$t = array("s","s");
					$res = db_prep_query($catSql, $v, $t);

				}

				$result["success"] = true;
				$msg = "WMC document '" . $this->wmc_title . "' has been saved.";
				$result["message"] = $msg;
				$e = new mb_notice("mod_insertWMCIntoDB: WMC  '" . $this->wmc_title . "' saved successfully.");
			}
		}
		else {
			$result["success"] = false;
			$errMsg = "missing parameters (user_id: ".$this->userId.", title: " . $this->wmc_title . ")";
			$result["message"] = $errMsg;
			$e = new mb_exception("mod_insertWMCIntoDB: " . $errMsg .")");
		}
		db_commit();
		return $result;
	}


    /*
    * overwrites an exact version of a wmc in the database
    */
	public function update_existing($xml,$id) {
		$sql = "UPDATE mb_user_wmc SET wmc = $1 WHERE wmc_serial_id = $2";
		$v = array($xml,$id);
		$t = array("s","i");
		$res = db_prep_query($sql,$v,$t);
		if(db_error()) { $e = new mb_exception("There was an error saving an updated WMC"); }
	}

	/**
	 * deletes a {@link http://www.mapbender.org/index.php/WMC WMC}
	 * entry specified by wmc_id and user_id
	 *
	 * @param	integer		the user_id
	 * @param	string		the wmc_id
	 * @return	boolean		Did the query run successful?
	 */
	public static function delete ($wmcId, $userId) {
		if (!isset($userId) || $userId === null) {
			$userId = Mapbender::session()->get("mb_user_id");
		}

		try {
			$user = new user($userId);
		} catch (Exception $E) {
			return $false;
		}

		if($user->isPublic()) {
			return $false;
		}

		$sql = "DELETE FROM mb_user_wmc ";
		$sql .= "WHERE fkey_user_id = $1 AND wmc_serial_id = $2";
		$v = array($userId, $wmcId);
		$t = array('i', 's');
		$res = db_prep_query($sql, $v, $t);
		if ($res) {
			return true;
		}
		return false;
	}

	/**
	 * Returns a WMC document
	 * @return String|boolean The document if it exists; else false
	 * @param $id String the WMC id
	 */
	public static function getDocument ($id) {
		$sql = "SELECT wmc FROM mb_user_wmc WHERE wmc_serial_id = $1 AND " .
			"(fkey_user_id = $2 OR wmc_public = 1)";
		$v = array($id, Mapbender::session()->get("mb_user_id"));
		$t = array('s', 'i');
		$res = db_prep_query($sql,$v,$t);
		$row = db_fetch_array($res);
		if ($row) {
			return $row["wmc"];
		}
		return false;
	}

	/**
	 * Returns a WMC document
	 * @return String|boolean The document if it exists; else false
	 * @param $id String the WMC id
	 */
	public static function getDocumentByTitle ($title) {
		$sql = "SELECT wmc FROM mb_user_wmc WHERE wmc_title = $1 AND " .
			"(fkey_user_id = $2 OR wmc_public = 1)";
		$v = array($title, Mapbender::session()->get("mb_user_id"));
		$t = array('s', 'i');
		$res = db_prep_query($sql,$v,$t);
		$row = db_fetch_array($res);
		if ($row) {
			return $row["wmc"];
		}
		return false;
	}

    /*
    * sets the WMC's public flag
    * @param $public boolean wether access should be public
    */
	public function setPublic($public) {
		$currentUser = new User(Mapbender::session()->get("mb_user_id"));
		if ($currentUser->isPublic()) {
			return false;
		}
		$wmcId = $this->wmc_id;
		$public = $public ? 1 :0;
		$sql = "UPDATE mb_user_wmc SET wmc_public = $1 WHERE wmc_serial_id = $2 AND fkey_user_id = $3;";
		$v = array($public,$wmcId, $currentUser->id);
		$t = array("i","s","i");
		$res = db_prep_query($sql,$v,$t);
		if(db_error()) {
			return false;
		}
		return true;
	}
    /*
    * increments the wmc_load_count if it has been set before
    * @param $wmc_id wmc_serial_id
    */
	public function incrementWmcLoadCount() {
		$wmcId = $this->wmc_id;
		//check for public else return false
		if ($this->isPublic()) {
			//check if a load_count has been set before
			//if not been set, set it to 1
			//else increment it
			$sql = "SELECT load_count FROM wmc_load_count where fkey_wmc_serial_id = $1;";
			$v = array($wmcId);
			$t = array("i");
			$res = db_prep_query($sql,$v,$t);
			if(db_error()) {
				return false;
			}
			$row = db_fetch_array($res);
			if ($row) {
				$e = new mb_notice("class_wmc: incrementWmcLoadCount found entry increment should be performed");
				$count = $row['load_count'];
				$count++;
				$sql = "UPDATE wmc_load_count SET load_count = $2 WHERE fkey_wmc_serial_id = $1;";
				$v = array($wmcId,$count);
				$t = array("i","i");
				$res = db_prep_query($sql,$v,$t);
			} else {
				$e = new mb_exception("class_wmc: incrementWmcLoadCount dont found entry - new should be set to 1");
				$sql = "INSERT INTO wmc_load_count (fkey_wmc_serial_id,load_count) VALUES ($1, $2);";
				$v = array($wmcId,1);
				$t = array("i","i");
				$res = db_prep_query($sql,$v,$t);
			}
			return true;
		}
		return false;
	}
    /*
    * test if the given wmc is public
    *
    */
	public function isPublic() {
		$wmcId = $this->wmc_id;
		$sql = "SELECT wmc_serial_id FROM mb_user_wmc ";
		$sql .= "WHERE wmc_serial_id = $1 AND wmc_public = 1;";
		$v = array($wmcId);
		$t = array("i");
		$res = db_prep_query($sql,$v,$t);
		$row = db_fetch_array($res);
		if (isset($row['wmc_serial_id']) && $row['wmc_serial_id'] != '') {
			$e = new mb_notice("class_wmc: isPublic is true");
			return true;
		}
		$e = new mb_notice("class_wmc: isPublic is false");
		return false;
	}

	// ---------------------------------------------------------------------------
	// GETTER FUNCTIONS
	// ---------------------------------------------------------------------------

	/**
	 * @return string the title of the WMC.
	 */
	public function getTitle() {
		return $this->wmc_title;
	}

	private function getLayerWithoutIdArray () {
		$layerWithoutWmsIdArray = array();
		$layerWithoutLayerIdArray = array();

		// check if WMS IDs exist
		$wmsArray = $this->mainMap->getWmsArray();
		for ($i = 0; $i < count($wmsArray); $i++) {
			$currentWms = $wmsArray[$i];
			if (!is_numeric($currentWms[$currentId])) {
				array_push($layerWithoutWmsIdArray, $currentId);
			}
		}

		// check if layer IDs exist
		for ($i = 0; $i < count($layerIdArray); $i++) {
			$currentId = $layerIdArray[$i];
			if (!is_numeric($this->wmc_layer_id[$currentId])) {
				array_push($layerWithoutLayerIdArray, $currentId);
			}
		}
		$noIdArray = array_unique(array_merge($layerWithoutWmsIdArray, $layerWithoutLayerIdArray));
		return $noIdArray;
	}


	// ---------------------------------------------------------------------------
	// OUTPUT FUNCTIONS
	// ---------------------------------------------------------------------------

	/**
	 * Wrapper function, returns XML at the moment
	 * @return String
	 */
	public function __toString() {
		return $this->toXml();
	}

	/**
	 * Returns the XML document if available
	 *
	 * @return String The XML document; if unavailable, null is returned.
	 */
	public function toXml () {
	//		if (!$this->xml) {
		$this->createXml();
		//		}
		return $this->xml;
	}

	private function incrementLoadCount ($wms) {
	// counts how often a layer has been loaded
		$monitor = new Layer_load_count();
		foreach ($wms->objLayer as $l) {
			$monitor->increment($l->layer_uid);
		}
	}

	public function extentToJavaScript() {
		return $this->mainMap->extentToJavaScript();
	}

	public function wmsToJavaScript() {
		$wmsArray = $this->mainMap->getWmsArray();

		$wmcJsArray = array();
		for ($i = 0; $i < count($wmsArray); $i++) {
			$currentWms = $wmsArray[$i];

			$wmcJsArray[] = $currentWms->createJsObjFromWMS_();
			$this->incrementLoadCount($currentWms);
		}
		return $wmcJsArray;
	}

	public function featuretypeConfToJavaScript() {
		$wfsConfIds = $this->generalExtensionArray['WFSCONFIDSTRING'];
		//new mb_notice("app AAAA idstr $wfsConfIds");
		$featuretypeConfs = array();
		$featuretypeConfArray = is_string($wfsConfIds) ?
			explode(",", $wfsConfIds) : array();
		for ($i = 0; $i < count($featuretypeConfArray); $i++) {
			$featuretypeConf = WfsConf::getWfsConfFromDb($featuretypeConfArray[$i]);
			array_push($featuretypeConfs,$featuretypeConf);
		}
		$featuretypeConfObj = new Mapbender_JSON();
		$featuretypeConfObj = $featuretypeConfObj->encode($featuretypeConfs);
		return $featuretypeConfObj;
	}

	public function removeWms ($wmsIndexArray) {
		$wmsArray = $this->mainMap->getWmsArray();

		// remove WMS from overview map
		if (!is_null($this->overviewMap)) {
			$ovIndices = array();
			$ovWmsArray = $this->overviewMap->getWmsArray();
			for ($i = 0; $i < count($ovWmsArray); $i++) {
				for ($j = 0; $j < count($wmsArray); $j++) {
					if ($ovWmsArray[$i]->equals($wmsArray[$j]) && in_array($j, $wmsIndexArray)) {
						$ovIndices[]= $i;
						break;
					}
				}
			}
			$this->overviewMap->removeWms($ovIndices);
		}

		// remove WMS from main map
		$this->mainMap->removeWms($wmsIndexArray);
	}

	/**
	 * Returns an array of JavaScript statements
	 *
	 * @return String[]
	 */
	public function toJavaScript () {
		$skipWmsArray = array();
		if (func_num_args() === 1) {
			if (!is_array(func_get_arg(0))) {
				throw new Exception("Invalid argument, must be array.");
			}
			$skipWmsArray = func_get_arg(0);
		}

		// will contain the JS code to create the maps
		// representing the state stored in this WMC
		$wmcJsArray = array();

		// set general extension data
		if (count($this->generalExtensionArray) > 0) {
			$json = new Mapbender_JSON();
			array_push($wmcJsArray, "restoredWmcExtensionData = " . $json->encode($this->generalExtensionArray) . ";");
		}

		// reset WMS data
		array_push($wmcJsArray, "wms = [];");
		array_push($wmcJsArray, "wms_layer_count = 0;");

		// add WMS for main map frame
		$wmsArray = $this->mainMap->getWmsArray();

		// find the WMS in the main map which is equal to the WMS
		// in the overview map
		$overviewWmsIndex = null;
		$ovWmsArray = array();
		if ($this->overviewMap !== null) {
			$ovWmsArray = $this->overviewMap->getWmsArray();
			$overviewWmsIndex = 0;
			for ($i = 0; $i < count($ovWmsArray); $i++) {
				for ($j = 0; $j < count($wmsArray); $j++) {
					if ($ovWmsArray[$i]->equals($wmsArray[$j]) && !in_array($j, $skipWmsArray)) {
						$overviewWmsIndex = $j;
						$wmsIndexOverview = $i;
						break;
					}
				}
			}
		}

		// for all wms...
		for ($i = 0; $i < count($wmsArray); $i++) {
			if (in_array($i, $skipWmsArray)) {
				continue;
			}
			array_push($wmcJsArray, $wmsArray[$i]->createJsObjFromWMS_());
			$this->incrementLoadCount($wmsArray[$i]);
		}

		// delete existing map objects...
		//		array_push($wmcJsArray, "mb_mapObj = [];");

		// .. and add the overview map (if exists) and set map request
		if ($this->overviewMap !== null) {
			$wmcJsArray = array_merge(
				$wmcJsArray,
				$this->overviewMap->toJavaScript(
				"{wms:wms,wmsIndexOverview:" . $overviewWmsIndex . "}"
				)
			);
		}

		// .. and add main map ..
		$wmcJsArray = array_merge(
			$wmcJsArray,
			$this->mainMap->toJavaScript(
			"{wms:wms,wmsIndexOverview:null}"
			)
		);

		// set visibility of ov map WMS (may be different from main)
		if ($this->overviewMap !== null) {
			for ($i = 0; $i < count($ovWmsArray[$wmsIndexOverview]->objLayer); $i++) {
				$visStr = "try { Mapbender.modules['".$this->overviewMap->getFrameName().
					//					"'].wms[" .$wmsIndexOverview . "].handleLayer(" .
					// The above doesn't work.
					// But there is only one WMS in the overview anyway! The index 0 is hard wired for now.
					"'].wms[0].handleLayer(" .
					"'" . $ovWmsArray[$wmsIndexOverview]->objLayer[$i]->layer_name . "', " .
					"'visible', " .
					($ovWmsArray[$wmsIndexOverview]->objLayer[$i]->gui_layer_visible ? 1 : 0) . ")} catch (e) {};";
				array_push($wmcJsArray, $visStr);
			}
			array_push($wmcJsArray, "try { Mapbender.modules['".$this->overviewMap->getFrameName().
				"'].restateLayers(" . $ovWmsArray[$wmsIndexOverview]->wms_id . ");} catch (e) {};");
		}

		// .. request the map
		
		array_push($wmcJsArray, "lock_maprequest = true;");
		array_push($wmcJsArray, "eventAfterLoadWMS.trigger();"); //TODO: Why? Reload tree? Other way to do this?
		array_push($wmcJsArray, "lock_maprequest = false;");
		array_push($wmcJsArray, "Mapbender.modules['".$this->mainMap->getFrameName().
			"'].setMapRequest();");
		if ($this->overviewMap !== null) {
			array_push($wmcJsArray, "try {Mapbender.modules['".$this->overviewMap->getFrameName().
				"'].setMapRequest()} catch (e) {};");
		}
		//?initializeWms()
		//eventAfterLoadWMS.register(reloadTree);
		return $wmcJsArray;
	}

	// ------------------------------------------------------------------------
	// manipulation
	// ------------------------------------------------------------------------
	/**
	 * Merges this WMC with another WMC.
	 * The settings of the other WMC overwrite the settings of this WMC.
	 *
	 * @return void
	 * @param $xml2 Object
	 */
	public function merge ($xml2) {
		$someWmc = new wmc();
		$someWmc->createFromXml($xml2);

		$this->mainMap->merge($someWmc->mainMap);
		if (isset($this->overviewMap) && isset($someWmc->overviewMap)) {
			$this->overviewMap->merge($someWmc->overviewMap);
		}
	}

	/**
	 * Appends the layers of another WMC to this WMC.
	 *
	 * @return void
	 * @param $xml2 Object
	 */
	public function append ($xml2) {
		$someWmc = new wmc();
		$someWmc->createFromXml($xml2);

		$this->mainMap->append($someWmc->mainMap);
		if (isset($this->overviewMap) && isset($someWmc->overviewMap)) {
		// There is only one WMS in the overview map; merge, not append
			$this->overviewMap->merge($someWmc->overviewMap);
		}
	}

	/**
	 * Adds a WMS to this WMC
	 *
	 * @return
	 */
	public function appendWmsArray ($wmsArray) {
		return $this->mainMap->appendWmsArray($wmsArray);
	}

	/**
	 * Merges a WMS into this WMC
	 *
	 * @return
	 */
	public function mergeWmsArray ($wmsArray) {
		if (func_num_args() > 1) {
			$options = func_get_arg(1);
			return $this->mainMap->mergeWmsArray($wmsArray, $options);
		}
		return $this->mainMap->mergeWmsArray($wmsArray);
	}

	// ---------------------------------------------------------------------------
	// private functions
	// ---------------------------------------------------------------------------

	/**
	 * Loads a WMC from an actual WMC XML document.
	 * Uses WMS class.
	 *
	 * @param string $data the data from the XML file
	 */
	protected function createObjFromWMC_xml($data) {
	// store xml
		$this->xml = $data;
		//$wmcXml = simplexml_load_string(mb_utf8_encode($data));
		//if ($wmcXml) {
		//	$e = new mb_exception("class_wmc.php: parsing wmc successfully");
		//}
		
		$values = administration::parseXml($data);
		if (!$values) {
			throw new Exception("WMC document could not be parsed.");
		}
		//
		// Local variables that indicate which section of the WMC
		// is currently parsed.
		//
		$extension = false;		$general = false;		$layerlist = false;
		$layer = false;  		$formatlist = false;	$dataurl = false;
		$metadataurl = false; 	$stylelist = false;

		//
		// reset WMC data
		//
		$this->mainMap = new Map();
		$this->overviewMap = null;
		$this->generalExtensionArray = array();

		$layerlistArray = array();
		$layerlistArray["main"] = array();
		$layerlistArray["overview"] = array();

		foreach ($values as $element) {
			$tag = strtoupper(administration::sepNameSpace($element[tag]));
			$tagLowerCase = administration::sepNameSpace($element[tag]);
			$type = $element[type];
			$attributes = $element[attributes];
			$value = mb_utf8_decode(html_entity_decode($element[value]));

			if ($tag == "VIEWCONTEXT" && $type == "open") {
				$this->wmc_id = $attributes["id"];
				$this->wmc_version = $attributes["version"];
			}
			if ($tag == "GENERAL" && $type == "open") {
				$general = true;
			}
			if ($tag == "LAYERLIST" && $type == "open") {
				$layerlist = true;
			}
			if ($general) {
				if ($tag == "WINDOW") {
					$this->mainMap->setWidth($attributes["width"]);
					$this->mainMap->setHeight($attributes["height"]);
				}
				if ($tag == "BOUNDINGBOX") {
					$bbox = new Mapbender_bbox($attributes["minx"], $attributes["miny"], $attributes["maxx"], $attributes["maxy"], $attributes["SRS"]);
					$this->mainMap->setExtent($bbox);
				}
				if ($tag == "NAME") {
					$this->wmc_name = $value;
				}
				if ($tag == "TITLE") {
					$this->wmc_title = $value;
				}
				if ($tag == "ABSTRACT") {
					$this->wmc_abstract = $value;
				}
				if ($tag == "CONTACTINFORMATION" && $type == "open") {
					$contactinformation = true;
				}
				if ($contactinformation) {
					if ($tag == "CONTACTPOSITION") {
						$this->wmc_contactposition = $value;
					}
					if ($tag == "CONTACTVOICETELEPHONE") {
						$this->wmc_contactvoicetelephone = $value;
					}
					if ($tag == "CONTACTFACSIMILETELEPHONE") {
						$this->wmc_contactfacsimiletelephone = $value;
					}
					if ($tag == "CONTACTELECTRONICMAILADDRESS") {
						$this->wmc_contactemail = $value;
					}
					if ($tag == "CONTACTPERSONPRIMARY" && $type == "open") {
						$contactpersonprimary = true;
					}
					if ($contactpersonprimary) {
						if ($tag == "CONTACTPERSON") {
							$this->wmc_contactperson = $value;
						}
						if ($tag == "CONTACTORGANIZATION") {
							$this->wmc_contactorganization = $value;;
						}
						if ($tag == "CONTACTPERSONPRIMARY" && $type == "close") {
							$contactpersonprimary = false;
						}
					}
					if ($tag == "CONTACTADDRESS" && $type == "open") {
						$contactaddress = true;
					}
					if ($contactaddress) {
						if ($tag == "ADDRESSTYPE") {
							$this->wmc_contactaddresstype = $value;
						}
						if ($tag == "ADDRESS") {
							$this->wmc_contactaddress = $value;
						}
						if ($tag == "CITY") {
							$this->wmc_contactcity = $value;
						}
						if ($tag == "STATEORPROVINCE") {
							$this->wmc_contactstateorprovince = $value;
						}
						if ($tag == "POSTCODE") {
							$this->wmc_contactpostcode = $value;
						}
						if ($tag == "COUNTRY") {
							$this->wmc_contactcountry = $value;
						}
						if ($tag == "CONTACTADDRESS" && $type == "close") {
							$contactaddress = false;
						}
					}
				}
				if ($tag == "LOGOURL" && $type == "open") {
					$logourl = true;
					$this->wmc_logourl_width = $attributes["width"];
					$this->wmc_logourl_height = $attributes["height"];
					$this->wmc_logourl_format = $attributes["format"];
				}
				if ($logourl) {
					if ($tag == "LOGOURL" && $type == "close") {
						$logourl = false;
					}
					if ($tag == "ONLINERESOURCE") {
						$this->wmc_logourl_type = $attributes["xlink:type"];
						$this->wmc_logourl = $attributes["xlink:href"];
					}
				}
				if ($tag == "DESCRIPTIONURL" && $type == "open") {
					$descriptionurl = true;
					$this->wmc_descriptionurl_format = $attributes["format"];
				}
				if ($descriptionurl) {
					if ($tag == "DESCRIPTIONURL" && $type == "close") {
						$descriptionurl = false;
					}
					if ($tag == "ONLINERESOURCE") {
						$this->wmc_descriptionurl_type = $attributes["xlink:type"];
						$this->wmc_descriptionurl = $attributes["xlink:href"];
					}
				}
				if ($tag == "KEYWORDLIST" && $type == "open") {
					$keywordlist = true;
				}
				if ($keywordlist) {
					if ($tag == "KEYWORDLIST" && $type == "close") {
						$keywordlist = false;
						$cnt_keyword = -1;
					}
					if ($tag == "KEYWORD") {
						$cnt_keyword++;
						$this->wmc_keyword[$cnt_keyword] = $value;
					}
				}
				if ($tag == "EXTENSION" && $type == "close") {
					$generalExtension = false;
					//
					// After the general extension tag is closed,
					// we have all necessary information to CREATE
					// the map objects that are contained in this
					// WMC.
					//
					$this->setMapData();
				}
				if ($generalExtension) {
					if ($value !== "") {
						if (isset($this->generalExtensionArray[$tag])) {
							if (!is_array($this->generalExtensionArray[$tag])) {
								$firstValue = $this->generalExtensionArray[$tag];
								$this->generalExtensionArray[$tag] = array();
								array_push($this->generalExtensionArray[$tag], $firstValue);
							}
							array_push($this->generalExtensionArray[$tag], $value);
						}
						else {
							$this->generalExtensionArray[$tag] = $value;
						}
					}
				}
				if ($tag == "EXTENSION" && $type == "open") {
					$generalExtension = true;
				}
				if ($tag == "GENERAL" && $type == "close") {
					$general = false;
				}
			}
			if ($layerlist) {
				if ($tag == "LAYERLIST" && $type == "close") {
					$layerlist = false;
				}
				if ($tag == "LAYER" && $type == "open") {
				//
				// The associative array currentLayer holds all
				// data of the currently processed layer.
				// The data will be set in the classes' WMS
				// object when the layer tag is closed.
				//
					$currentLayer = array();

					$currentLayer["queryable"] = $attributes["queryable"];
					if ($attributes["hidden"] == "1") {
						$currentLayer["visible"] = 0;
					}
					else {
						$currentLayer["visible"] = 1;
					}
					$currentLayer["format"] = array();
					$currentLayer["style"] = array();
					$layer = true;
				}
				if ($layer) {
					if ($tag == "LAYER" && $type == "close") {

					//
					// After a layer tag is closed,
					// we have all necessary information to CREATE
					// a layer object and append it to the WMS object
					//
						if (isset($currentLayer["extension"]["OVERVIEWHIDDEN"])) {
							array_push($layerlistArray["overview"], $currentLayer);
						}
						$modifiedLayer = $currentLayer;
						unset($modifiedLayer["extension"]["OVERVIEWHIDDEN"]);
						array_push($layerlistArray["main"], $modifiedLayer);
						$layer = false;
					}
					if ($formatlist) {
						if ($tag == "FORMAT") {
							array_push($currentLayer["format"], array("current" => $attributes["current"], "name" => $value));
							if ($attributes["current"] == "1") {
								$currentLayer["formatIndex"] = count($currentLayer["format"]) - 1;
							}
						}
						if ($tag == "FORMATLIST" && $type == "close") {
							$formatlist = false;
						}
					}
					elseif ($metadataurl) {
						if ($tag == "ONLINERESOURCE") {
							$currentLayer["metadataurl"] = $attributes["xlink:href"];
						}
						if ($tag == "METADATAURL" && $type == "close") {
							$metadataurl = false;
						}
					}
					elseif ($dataurl) {
						if ($tag == "ONLINERESOURCE") {
							$currentLayer["dataurl"] = $attributes["xlink:href"];
						}
						if ($tag == "DATAURL" && $type == "close") {
							$dataurl = false;
						}
					}
					elseif ($stylelist) {
						if ($style) {
							$index = count($currentLayer["style"]) - 1;
							if ($tag == "STYLE" && $type == "close") {
								$style = false;
							}
							if ($tag == "SLD" && $type == "open") {
								$sld = true;
							}
							if ($sld) {
								if ($tag == "SLD" && $type == "close") {
									$sld = false;
								}
								if ($tag == "ONLINERESOURCE") {
									$currentLayer["style"][$index]["sld_type"] = $attributes["xlink:type"];
									$currentLayer["style"][$index]["sld_url"] = $attributes["xlink:href"];
								}
								if ($tag == "TITLE") {
									$currentLayer["style"][$index]["sld_title"] = $value;
								}
							}
							else {
								if ($tag == "NAME") {
									$currentLayer["style"][$index]["name"] = $value ? $value : "default";
								}
								if ($tag == "TITLE") {
									$currentLayer["style"][$index]["title"] = $value ? $value : "default";
								}
								if ($legendurl) {
									if ($tag == "LEGENDURL" && $type == "close") {
										$legendurl = false;
									}
									if ($tag == "ONLINERESOURCE") {
										$currentLayer["style"][$index]["legendurl_type"] = $attributes["xlink:type"];
										$currentLayer["style"][$index]["legendurl"] = $attributes["xlink:href"];
									}
								}
								if ($tag == "LEGENDURL" && $type == "open") {
									$legendurl = true;
									$currentLayer["style"][$index]["legendurl_width"] = $attributes["width"];
									$currentLayer["style"][$index]["legendurl_height"] = $attributes["height"];
									$currentLayer["style"][$index]["legendurl_format"] = $attributes["format"];
								}
							}
						}
						if ($tag == "STYLE" && $type == "open") {
							$style = true;
							array_push($currentLayer["style"], array("current" => $attributes["current"]));
							if ($attributes["current"] == "1") {
								$currentLayer["styleIndex"] = count($currentLayer["style"]) - 1;
							}
						}
						if ($tag == "STYLELIST" && $type == "close") {
							$stylelist = false;
						}
					}
					else {
						if ($tag == "SERVER" && $type == "open") {
							$server = true;
							$currentLayer["service"] = $attributes["service"];
							$currentLayer["version"] = $attributes["version"];
							$currentLayer["wms_title"] = $attributes["title"];
						}
						if ($server) {
							if ($tag == "SERVER" && $type == "close") {
								$server = false;
							}
							if ($tag == "ONLINERESOURCE") {
								$currentLayer["url"] = $attributes["xlink:href"];
							}
						}
						if ($tag == "NAME") {
							$currentLayer["name"] = $value;
						}
						if ($tag == "TITLE") {
							$currentLayer["title"] = $value;
						}
						if ($tag == "ABSTRACT") {
							$currentLayer["abstract"] = $value;
						}
						if ($tag == "SRS") {
							$currentLayer["epsg"] = explode(" ", $value);
						}
						if ($tag == "EXTENSION" && $type == "close") {
							$extension = false;
						}
						if ($extension == true) {
						//							if ($value !== "") {
							if (isset($currentLayer["extension"][$tag])) {
								if (!is_array($currentLayer["extension"][$tag])) {
									$firstValue = $currentLayer["extension"][$tag];
									$currentLayer["extension"][$tag] = array();
									array_push($currentLayer["extension"][$tag], $firstValue);
								}
								array_push($currentLayer["extension"][$tag], $value);
							}
							else {
								$currentLayer["extension"][$tag] = $value;
							}
						//							}
						}
						if ($tag == "EXTENSION" && $type == "open") {
							$currentLayer["extension"] = array();
							$extension = true;
						}
						if ($tag == "METADATAURL" && $type == "open") {
							$metadataurl = true;
						}
						if ($tag == "DATAURL" && $type == "open") {
							$dataurl = true;
						}
						if ($tag == "FORMATLIST" && $type == "open") {
							$formatlist = true;
						}
						if ($tag == "STYLELIST" && $type == "open") {
							$stylelist = true;
						}
					}
				}
			}
		}

		// set WMS data

		$layerlistCompleteArray = array_merge($layerlistArray["main"], $layerlistArray["overview"]);

		for ($i = 0; $i < count($layerlistCompleteArray); $i++) {
			$this->setLayerData($layerlistCompleteArray[$i]);
		}

		$wmsArr = $this->mainMap->getWmsArray();
		for ($i = 0; $i < count($wmsArr); $i++) {
			$wmsArr[$i]->updateAllOwsProxyUrls();
		}
		return true;
	}

	/**
	 * Saves the current WMC in the log folder.
	 *
	 * @return string the filename of the WMC document.
	 */
	private function saveAsFile() {
		if ($this->saveWmcAsFile) {
			$filename = "wmc_" . date("Y_m_d_H_i_s") . ".xml";
			$logfile = "../tmp/" . $filename;

			if($h = fopen($logfile,"a")) {
				$content = $this->xml;
				if(!fwrite($h,$content)) {
					$e = new mb_exception("class_wmc.php: failed to write wmc.");
					return false;
				}
				fclose($h);
			}
			$e = new mb_notice("class_wmc: saving WMC as file " . $filename . "; You can turn this behaviour off in class_wmc.php");
			return $filename;
		}
		return null;
	}

	/**
	 * Called during WMC parsing; sets the data of a single layer.
	 *
	 * @return
	 * @param $currentLayer Array an associative array with layer data
	 */
	private function setLayerData ($currentLayer) {
		$currentMap = $this->mainMap;
		$currentMapIsOverview = false;

		if (isset($currentLayer["extension"]["OVERVIEWHIDDEN"])) {
			$currentMap = $this->overviewMap;
			$currentMapIsOverview = true;
		}

		if (is_null($currentMap)) {
			$e = new mb_exception('class_wmc.php: setLayerData: $currentMap is null. Aborting.');
			return null;
		}

		$wmsArray = $currentMap->getWmsArray();

		//
		// check if current layer belongs to an existing WMS.
		// If yes, store the index of this WMS in $wmsIndex.
		// If not, set the value to null.
		//
		$wmsIndex = null;

		// find last WMS with the same online resource
		for ($i = count($wmsArray) - 1; $i >= 0; $i--) {
			if (isset($currentLayer["url"]) &&
				$currentLayer["url"] == $wmsArray[$i]->wms_getmap) {
				$wmsIndex = $i;
				break;
			}
		}

		// Even if this WMS has been found before it could still
		// be a duplicate! We would have to create a new WMS and
		// not append this layer to that WMS.
		// For the overview layer we never add a new wms.
		// check if this layer is an overview layer. If yes, skip this layer.
		if ($wmsIndex !== null && !$currentMapIsOverview) {

		// check if this WMS has a layer equal to the current layer.
		// If yes, this is a new WMS. If not, append this layer
		// to the existing WMS.
			$matchingWmsLayerArray = $this->wmsArray[$wmsIndex]->objLayer;

			for ($i = 0; $i < count($matchingWmsLayerArray); $i++) {
				if ($matchingWmsLayerArray[$i]->layer_name == $currentLayer["name"]) {

				// by re-setting the index to null, a new WMS will be
				// added below.
					$wmsIndex = null;
					break;
				}
			}
		}

		// if yes, create a new WMS ...
		if ($wmsIndex === null) {
			$wmsIndex = 0;
			$wms = new wms();

			//
			// set WMS data
			//
			$wms->wms_id = $currentLayer["extension"]["WMS_ID"]; // TO DO: how about WMS without ID?
			$wms->wms_version = $currentLayer["version"];
			$wms->wms_title = $currentLayer["wms_title"];
			$wms->wms_abstract = $currentLayer["abstract"];
			$wms->wms_getmap = $currentLayer["url"];
			$wms->wms_getfeatureinfo = $currentLayer["url"]; // TODO : Add correct data

			$styleIndex = $currentLayer["styleIndex"];
			$wms->wms_getlegendurl = $currentLayer["style"][$styleIndex]["legendurl"];

			$wms->wms_filter = ""; // TODO : Add correct data

			$formatIndex = $currentLayer["formatIndex"];
			$wms->gui_wms_mapformat = $currentLayer["format"][$formatIndex]["name"];

			$wms->gui_wms_featureinfoformat = "text/html"; // TODO : Add correct data
			if($currentLayer["version"] == '1.3.0' || $currentLayer["version"] == '1.0.0'){
				$wms->gui_wms_exceptionformat = "XML"; // TODO : Add correct data
			}else{
				$wms->gui_wms_exceptionformat = "application/vnd.ogc.se_xml"; // TODO : Add correct data
			}
			$wms->gui_wms_epsg = $this->mainMap->getEpsg();
			$wms->gui_wms_visible = $currentLayer["extension"]["WMS_VISIBLE"];
			$wms->gui_wms_opacity = $currentLayer["extension"]["GUI_WMS_OPACITY"];
			$wms->gui_wms_sldurl = $currentLayer["style"][$styleIndex]["sld_url"];
			$wms->wms_srs = $currentLayer["epsg"];
			$wms->gui_epsg = $currentLayer["epsg"];
			//
			// set data formats
			//
			for ($i = 0; $i < count($currentLayer["format"]); $i++) {
				array_push($wms->data_type, "map");
				array_push($wms->data_format, $currentLayer["format"][$i]["name"]);
			}

			// add WMS
			array_push($wmsArray, $wms);

			// the index of the WMS we just added
			$wmsIndex = count($wmsArray) - 1;
		}

		// add layer to existing WMS ...
		$currentWms = $wmsArray[$wmsIndex];
		$currentWms->newLayer($currentLayer, null);
		$currentMap->setWmsArray($wmsArray);
		return true;
	}

	/**
	 * Called during WMC parsing; sets the maps within a WMC.
	 *
	 * @return
	 */
	private function setMapData () {
		if ($this->generalExtensionArray["OV_WIDTH"] &&
			$this->generalExtensionArray["OV_HEIGHT"] &&
			$this->generalExtensionArray["OV_FRAMENAME"] &&
			$this->generalExtensionArray["OV_MINX"] &&
			$this->generalExtensionArray["OV_MINY"] &&
			$this->generalExtensionArray["OV_MAXX"] &&
			$this->generalExtensionArray["OV_MAXY"] &&
			$this->generalExtensionArray["OV_SRS"]) {

			$this->overviewMap = new Map();
			$this->overviewMap->setWidth(
				// this should not be an array, but sometimes it is.
				// I can't find the reason at the moment, consider
				// this a workaround
				is_array($this->generalExtensionArray["OV_WIDTH"]) ?
				$this->generalExtensionArray["OV_WIDTH"][0] :
				$this->generalExtensionArray["OV_WIDTH"]
			);
			$this->overviewMap->setHeight(
				// this should not be an array, but sometimes it is.
				// I can't find the reason at the moment, consider
				// this a workaround
				is_array($this->generalExtensionArray["OV_HEIGHT"]) ?
				$this->generalExtensionArray["OV_HEIGHT"][0] :
				$this->generalExtensionArray["OV_HEIGHT"]
			);
			$this->overviewMap->setFrameName(
				// this should not be an array, but sometimes it is.
				// I can't find the reason at the moment, consider
				// this a workaround
				is_array($this->generalExtensionArray["OV_FRAMENAME"]) ?
				$this->generalExtensionArray["OV_FRAMENAME"][0] :
				$this->generalExtensionArray["OV_FRAMENAME"]
			);
			$this->overviewMap->setIsOverview(true);

			if (is_array($this->generalExtensionArray["OV_SRS"])) {
				$this->generalExtensionArray["OV_SRS"] = $this->generalExtensionArray["OV_SRS"][0];
				$this->generalExtensionArray["OV_MINX"] = $this->generalExtensionArray["OV_MINX"][0];
				$this->generalExtensionArray["OV_MINY"] = $this->generalExtensionArray["OV_MINY"][0];
				$this->generalExtensionArray["OV_MAXX"] = $this->generalExtensionArray["OV_MAXX"][0];
				$this->generalExtensionArray["OV_MAXY"] = $this->generalExtensionArray["OV_MAXY"][0];
			}
			$bbox = new Mapbender_bbox($this->generalExtensionArray["OV_MINX"], $this->generalExtensionArray["OV_MINY"], $this->generalExtensionArray["OV_MAXX"], $this->generalExtensionArray["OV_MAXY"], $this->generalExtensionArray["OV_SRS"]);
			$this->overviewMap->setExtent($bbox);
		}
		if ($this->generalExtensionArray["EPSG"] &&
			$this->generalExtensionArray["MINX"] &&
			$this->generalExtensionArray["MINY"] &&
			$this->generalExtensionArray["MAXX"] &&
			$this->generalExtensionArray["MAXY"]) {

			$mainEpsgArray = array();
			$mainMinXArray = array();
			$mainMinYArray = array();
			$mainMaxXArray = array();
			$mainMaxYArray = array();
			if (!is_array($this->generalExtensionArray["EPSG"])) {
				$mainEpsgArray[0] = $this->generalExtensionArray["EPSG"];
				$mainMinXArray[0] = $this->generalExtensionArray["MINX"];
				$mainMinYArray[0] = $this->generalExtensionArray["MINY"];
				$mainMaxXArray[0] = $this->generalExtensionArray["MAXX"];
				$mainMaxYArray[0] = $this->generalExtensionArray["MAXY"];
			}
			else {
				$mainEpsgArray = $this->generalExtensionArray["EPSG"];
				$mainMinXArray = $this->generalExtensionArray["MINX"];
				$mainMinYArray = $this->generalExtensionArray["MINY"];
				$mainMaxXArray = $this->generalExtensionArray["MAXX"];
				$mainMaxYArray = $this->generalExtensionArray["MAXY"];
			}

			for ($i=0; $i < count($mainEpsgArray); $i++) {
				$box = new Mapbender_bbox(
					floatval($mainMinXArray[$i]), floatval($mainMinYArray[$i]),
					floatval($mainMaxXArray[$i]), floatval($mainMaxYArray[$i]),
					$mainEpsgArray[$i]
				);
				$this->mainMap->addZoomFullExtent($box);
			}
		}

		if ($this->generalExtensionArray["MAIN_FRAMENAME"]) {
			$this->mainMap->setFrameName(
				// this should not be an array, but sometimes it is.
				// I can't find the reason at the moment, consider
				// this a workaround
				is_array($this->generalExtensionArray["MAIN_FRAMENAME"]) ?
				$this->generalExtensionArray["MAIN_FRAMENAME"][0] :
				$this->generalExtensionArray["MAIN_FRAMENAME"]
			);
		}
		else {
			$this->mainMap->setFrameName("mapframe1");
		}
		unset($this->generalExtensionArray["OV_WIDTH"]);
		unset($this->generalExtensionArray["OV_HEIGHT"]);
		unset($this->generalExtensionArray["OV_MINX"]);
		unset($this->generalExtensionArray["OV_MINY"]);
		unset($this->generalExtensionArray["OV_MAXX"]);
		unset($this->generalExtensionArray["OV_MAXY"]);
		unset($this->generalExtensionArray["OV_SRS"]);
		unset($this->generalExtensionArray["OV_FRAMENAME"]);
		unset($this->generalExtensionArray["MINX"]);
		unset($this->generalExtensionArray["MINY"]);
		unset($this->generalExtensionArray["MAXX"]);
		unset($this->generalExtensionArray["MAXY"]);
		unset($this->generalExtensionArray["EPSG"]);
		unset($this->generalExtensionArray["MAIN_FRAMENAME"]);
		return true;
	}

	/**
	 * Creates a WMC document (XML) from the current object
	 *
	 * @return String XML
	 */
	private function createXml() {
		$wmcToXml = new WmcToXml($this);
		$this->xml = $wmcToXml->getXml();
	}
} 

/**
 * @deprecated
 */
function mb_utf8_encode ($str) {
//	if(CHARSET=="UTF-8") return utf8_encode($str);
	return $str;
}

/**
 * @deprecated
 */
function mb_utf8_decode ($str) {
//	if(CHARSET=="UTF-8") return utf8_decode($str);
	return $str;
}
?>
