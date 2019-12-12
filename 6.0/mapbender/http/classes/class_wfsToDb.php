<?php
# $Id: class_wfs.php 3094 2008-10-01 13:52:35Z christoph $
# http://www.mapbender.org/index.php/class_wfs.php
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

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/class_connector.php");
require_once(dirname(__FILE__)."/class_administration.php");
require_once(dirname(__FILE__)."/class_wfs.php");
require_once dirname(__FILE__) . "//class_Uuid.php";

class WfsToDb {

	/**
	 * Inserts a new or updates an existing WFS. Replaces the old wfs2db function.
	 * 
	 * @return Boolean
	 * @param $aWfs Wfs
	 */
	public function insertOrUpdate ($aWfs) {
		if (WfsToDb::exists($aWfs)) {
			return WfsToDb::update($aWfs);
		}
		return WfsToDb::insert($aWfs);
	}
	
	/**
	 * Inserts a new WFS into the database.
	 * 
	 * @return Boolean
	 * @param $aWfs Wfs
	 */
	public static function insert ($aWfs) {
		db_begin();
		$uuid = new Uuid();
		$sql = "INSERT INTO wfs (wfs_version, wfs_name, wfs_title, wfs_abstract, ";
		$sql .= "wfs_getcapabilities, wfs_getcapabilities_doc, wfs_upload_url, ";
		$sql .= "wfs_describefeaturetype, wfs_getfeature, wfs_transaction, fees, ";
		$sql .= "accessconstraints, wfs_owner, wfs_timestamp, uuid) ";
		$sql .= "VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14,$15)";
	
		$v = array(
			$aWfs->getVersion(), 
			$aWfs->name, 
			$aWfs->title, 
			$aWfs->summary, 
			$aWfs->getCapabilities, 
			$aWfs->getCapabilitiesDoc,
			$aWfs->uploadUrl, 
			$aWfs->describeFeatureType, 
			$aWfs->getFeature,
			$aWfs->transaction, 
			$aWfs->fees, 
			$aWfs->accessconstraints, 
			Mapbender::session()->get("mb_user_id"), 
			strtotime("now"),
			$uuid
		);
			
		$t = array('s', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 'i', 'i','s');
	
		$res = db_prep_query($sql, $v, $t);
	
		if (!$res) {
			$e = new mb_exception("Error while inserting WFS into database.");
			return false;
		}

		// set the WFS id
		$aWfs->id = db_insert_id($con, 'wfs', 'wfs_id');
		
		// Insert the feature types
		for ($i = 0; $i < count($aWfs->featureTypeArray); $i++) {
			$currentFeatureType = $aWfs->featureTypeArray[$i];
			if (!WfsToDb::insertFeatureType($currentFeatureType)) {
				db_rollback();
				return false;
			}
		}
		db_commit();
		return true;		
	}
	
	/**
	 * Updates an existing WFS in the database.
	 * 
	 * @return Boolean
	 * @param $aWfs Wfs
	 */
	public static function update ($aWfs) {
		db_begin();
		
		// update WFS
		$sql = "UPDATE wfs SET wfs_version = $1, wfs_name = $2, wfs_title = $3, ";
		$sql .= "wfs_abstract = $4, wfs_getcapabilities = $5, wfs_getcapabilities_doc = $6, ";
		$sql .= "wfs_upload_url = $7, wfs_describefeaturetype = $8, wfs_getfeature = $9, ";
		$sql .= "wfs_transaction = $10, fees = $11, accessconstraints = $12, ";
		$sql .= "individualname = $13, positionname = $14, providername = $15, ";
		$sql .= "city = $16, deliverypoint = $17, administrativearea = $18, ";
		$sql .= "postalcode = $19, voice = $20, facsimile = $21, ";
		$sql .= "electronicmailaddress = $22, country = $23, ";
		$sql .= "wfs_timestamp = $24, wfs_network_access = $25, fkey_mb_group_id = $26 ";
		$sql .= "WHERE wfs_id = $27";

		$v = array(
			$aWfs->getVersion(), 
			$aWfs->name, 
			$aWfs->title, 
			$aWfs->summary,
			$aWfs->getCapabilities, 
			$aWfs->getCapabilitiesDoc, 
			$aWfs->uploadUrl,
			$aWfs->describeFeatureType, 
			$aWfs->getFeature, 
			$aWfs->transaction,
			$aWfs->fees, 
			$aWfs->accessconstraints,
			$aWfs->individualName,
			$aWfs->positionName,
			$aWfs->providerName,
			$aWfs->city,
			$aWfs->deliveryPoint,
			$aWfs->administrativeArea,
			$aWfs->postalCode,
			$aWfs->voice,
			$aWfs->facsimile,
			$aWfs->electronicMailAddress,
			$aWfs->country, 
			strtotime("now"),
			$aWfs->network_access,
			$aWfs->fkey_mb_group_id,
			$aWfs->id
		);
			
		$t = array('s', 's', 's', 's', 's', 's', 's', 's' ,'s' ,'s' ,'s' ,'s','s' ,'s','s','s','s','s','s','s','s','s','s','i','i','i','i');
		$e = new mb_exception("UPDATING WFS " . $aWfs->id);
		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			$e = new mb_exception("Error while updating WFS in database.");
			db_rollback();
			return false;
		}
		
		# delete and refill wfs_termsofuse
		$sql = "DELETE FROM wfs_termsofuse WHERE fkey_wfs_id = $1 ";
		$v = array($aWfs->id);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		if(!$res){
			db_rollback();	
		}
		
		WfsToDb::insertTermsOfUse($aWfs);
		
		$featureTypeNameArray = array();
		for ($i = 0; $i < count($aWfs->featureTypeArray); $i++) {
			$currentFeatureType = $aWfs->featureTypeArray[$i];
			array_push($featureTypeNameArray, $currentFeatureType);
			if (WfsToDb::featureTypeExists($currentFeatureType)) {
				// update existing WFS feature types
				$e = new mb_exception("FT exists");
				if (!WfsToDb::updateFeatureType($currentFeatureType)) {
					db_rollback();
					return false;
				}
			}
			else {
				$e = new mb_exception("FT ne pas exists");
				// insert new feature types
				if (!WfsToDb::insertFeatureType($currentFeatureType)) {
					db_rollback();
					return false;
				}
			}
		}		
		
		// delete obsolete WFS feature types
		$v = array($aWfs->id);
		$t = array("i");
		$sql = "DELETE FROM wfs_featuretype WHERE fkey_wfs_id = $1";
		$sql_in = "";
		for ($i = 0; $i < count($featureTypeNameArray); $i++) {
			if ($i > 0) {
				$sql_in .= ", ";
			}
			$sql_in .= "$" . ($i+2);
			array_push($v, $featureTypeNameArray[$i]->name);
			array_push($t, "s");
		}
		if ($sql_in !== "") {
			$sql .=  " AND featuretype_name NOT IN (" . $sql_in . ")";
		}
		
		$res = db_prep_query($sql,$v,$t);
		if (!$res) {
			$e = new mb_exception("Error while deleting obsolete WFS feature types in database.");
			db_rollback();
			return false;
		}
		db_commit();
		return true;		
	}
	
	
	/**
	 * Checks if a WFS exists in the database. 
	 * 
	 * @return Boolean
	 * @param $aWfs Wfs
	 */
	public static function exists ($aWfs) {
		// temporary WFS do not have a numeric ID
		if (!is_numeric($aWfs->id)) {
			return false;
		}

		// if ID is numeric, check if it exists in the database
		$sql = "SELECT * FROM wfs WHERE wfs_id = $1;";
		$v = array($aWfs->id);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);	
		if ($row = db_fetch_array($res)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Delete a WFS from the database. Also sets the WFS object to null.
	 * 
	 * @return Boolean
	 * @param $aWfs Wfs
	 */
	public static function delete ($aWfs) {
		$sql = "DELETE FROM wfs WHERE wfs_id = $1";
		$v = array($aWfs->id);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		if ($res) {
			$aWfs = null;
			return true;
		}
		return false;
	}
	
	/**
	 * Inserts the terms of use for a WFS in the database.
	 * 
	 * @return Boolean
	 * @param $aWfs Wfs
	 */
	public static function insertTermsOfUse ($aWfs) {
		if (!is_numeric($aWfs->wfs_termsofuse)) {
			return;
		}
		$sql ="INSERT INTO wfs_termsofuse (fkey_wfs_id, fkey_termsofuse_id) ";
		$sql .= " VALUES($1,$2)";
		$v = array($aWfs->id,$aWfs->wfs_termsofuse);
		$t = array('i','i');
		$res = db_prep_query($sql,$v,$t);
		if(!$res){
			$e = new mb_exception("Error while inserting WFS termsofuse into the database.");
			return false;
		}
		return true;
	}

	//-----------------------------------------------------------------------	
	//
	// PRIVATE
	//
	//-----------------------------------------------------------------------	
	
	private static function insertFeatureTypeNamespace ($aWfsId, $aWfsFeatureTypeId, $aWfsFeatureTypeNamespace) {
		$sql = "INSERT INTO wfs_featuretype_namespace (fkey_wfs_id, " . 
				"fkey_featuretype_id, namespace, namespace_location) " . 
				"VALUES ($1, $2, $3, $4);"; 

		$v = array(
			$aWfsId, 
			$aWfsFeatureTypeId, 
			$aWfsFeatureTypeNamespace->name, 
			$aWfsFeatureTypeNamespace->value
		);
		$t = array("s", "s", "s", "s");
		$e = new mb_exception("INSERTING FT NS $aWfsId, FT: $aWfsFeatureTypeId, NS: $aWfsFeatureTypeNamespace->name");
		$res = db_prep_query($sql, $v, $t);

		if (!$res) {
			$e = new mb_exception("Error while inserting WFS feature type namespace into the database.");
			return false;
		}
		return true;
	}
	
	/**
	 * Inserts a new WFS feature type element into the database.
	 * 
	 * @return Boolean
	 * @param $aWfsFeatureTypeId Integer
	 * @param $aWfsFeatureTypeElement Object
	 */
	private static function insertFeatureTypeElement ($aWfsFeatureTypeId, $aWfsFeatureTypeElement) {
		$sql = "INSERT INTO wfs_element (fkey_featuretype_id, element_name, " . 
				"element_type) VALUES ($1, $2, $3)";
		
		$v = array(
			$aWfsFeatureTypeId, 
			$aWfsFeatureTypeElement->name, 
			$aWfsFeatureTypeElement->type
		);
		$t = array("i", "s", "s");
		
		$e = new mb_exception("INSERTING FT EL (FT: $aWfsFeatureTypeId, NS: $aWfsFeatureTypeElement->name");
		$res = db_prep_query($sql, $v, $t);
		
		if (!$res) {
			$e = new mb_exception("Error while inserting WFS feature type element into the database.");
			return false;
		}
		
		// set the WFS feature type element ID
		$aWfsFeatureTypeElement->id = db_insert_id("", "wfs_element", "element_id");

		//
		//
		//ADD THIS FEATURETYPE TO WFS CONFIGURATIONS THAT USE THIS FEATURETYPE
		//
		//
		$sql = "SELECT wfs_conf_id FROM wfs_conf WHERE fkey_featuretype_id = $1";
		$v = array($aWfsFeatureTypeId);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			// no configuration exists for this featuretype, 
			// which is fine
			$e = new mb_notice("No WFS conf found for this featuretype (Couldn't insert new feature type element in wfs_conf_element!)");
			return true;
		}
		while ($row = db_fetch_array($res)) {
			$wfsConfId = $row["wfs_conf_id"];
			
			// check if wfs conf element exists for this
			// featuretype element
			$sqlConfElement = "SELECT COUNT(wfs_conf_element_id) AS cnt FROM " . 
				"wfs_conf_element AS a, wfs_element AS b " . 
				"WHERE a.f_id = b.element_id AND " .
				"b.element_id = $1 AND a.fkey_wfs_conf_id = $2";
			$v = array($aWfsFeatureTypeElement->id, $wfsConfId);
			$t = array("i", "i");
			$resConfElement = db_prep_query($sqlConfElement, $v, $t);
			$rowConfElement = db_fetch_array($resConfElement);
			$count = $rowConfElement["cnt"];
			if ($count === "0") {
				$e = new mb_notice("Inserting this feature type element (" . 
					$aWfsFeatureTypeElement->id . ") into WFS conf ($wfsConfId)");
				
				// Insert featuretype element in wfs_conf_element
				$sqlInsertConfElement = "INSERT INTO wfs_conf_element ";
				$sqlInsertConfElement .= "(fkey_wfs_conf_id, f_id,f_search,f_pos, f_style_id,";
				$sqlInsertConfElement .= "f_toupper, f_label, f_label_id, f_show,";
				$sqlInsertConfElement .= "f_respos, f_form_element_html, f_edit,";
				$sqlInsertConfElement .= "f_mandatory, f_auth_varname, f_show_detail, f_operator,";
				$sqlInsertConfElement .= "f_detailpos, f_min_input) VALUES";

				$sqlInsertConfElement .= "($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)";

				$v = array($wfsConfId, $aWfsFeatureTypeElement->id,0,0,0,0,'',0,0,0,'',0,0,'',0,'',0,0);
				$t = array("i", "i","i", "i","i", "i","s","i","i","i","s","i","i","s","i","s","i","i");

				$resInsertConfElement = db_prep_query($sqlInsertConfElement, $v, $t);
				if (!$res) {
					$e = new mb_exception("Couldn't insert new feature type element in wfs_conf_element!");
					return false;
				}
			}
			else {
				$e = new mb_notice("This feature type element (" . 
					$aWfsFeatureTypeElement->id . ") already exists in WFS conf ($wfsConfId)");

			}
		}
		
		return true;
	}

	/**
	 * Updates an existing WFS feature type element in the database.
	 * 
	 * @return Boolean
	 * @param $aWfsFeatureTypeId Integer
	 * @param $aWfsFeatureTypeElement Object
	 */
	private static function updateFeatureTypeElement ($aWfsFeatureTypeId, $aWfsFeatureTypeElement) {
		$sql = "UPDATE wfs_element SET element_type = $1 " . 
				"WHERE element_id = $2 AND fkey_featuretype_id = $3";

		$v = array(
			$aWfsFeatureTypeElement->type, 
			$aWfsFeatureTypeElement->id, 
			$aWfsFeatureTypeId
		);
		$t = array("s", "i", "i");

		$e = new mb_exception("UPDATING FT EL (FT: $aWfsFeatureTypeId, NS: $aWfsFeatureTypeElement->name");
		$res = db_prep_query($sql, $v, $t);

		if (!$res) {
			$e = new mb_exception("Error while updating WFS feature type element in the database.");
			return false;
		}
		
		return true;
	}

	/**
	 * Inserts a new WFS feature type into the database.
	 * 
	 * @return Boolean
	 * @param $aWfsFeatureType WfsFeatureType
	 */
	private static function insertFeatureType ($aWfsFeatureType) {
		$uuid = new Uuid();
		$sql = "INSERT INTO wfs_featuretype (fkey_wfs_id, featuretype_name, " . 
				"featuretype_title, featuretype_abstract, featuretype_srs, uuid) " . 
				"VALUES($1, $2, $3, $4, $5, $6)";

		$v = array(
			$aWfsFeatureType->wfs->id,
			$aWfsFeatureType->name,
			$aWfsFeatureType->title,
			$aWfsFeatureType->summary,
			$aWfsFeatureType->srs,
			$uuid
		);
		$t = array('i','s','s','s','s','s');

		$e = new mb_exception("INSERTING FT (FT: $aWfsFeatureType->name)");
		$res = db_prep_query($sql,$v,$t);
		if (!$res) {
			$e = new mb_exception("Error while inserting WFS feature type into database.");
			return false;	
		}

		// save the id of each featuretype
		$aWfsFeatureType->id = db_insert_id("", "wfs_featuretype", "featuretype_id");

		// insert feature type elements
		for ($i = 0; $i < count($aWfsFeatureType->elementArray); $i++) {
			$element = $aWfsFeatureType->elementArray[$i];
			if (!WfsToDb::insertFeatureTypeElement($aWfsFeatureType->id, $element)) {
				return false;	
			}
		}
		
		// insert feature type namespaces
		for ($i = 0; $i < count($aWfsFeatureType->namespaceArray); $i++) {
			$namespace = $aWfsFeatureType->namespaceArray[$i];
			if (!WfsToDb::insertFeatureTypeNamespace($aWfsFeatureType->wfs->id, $aWfsFeatureType->id, $namespace)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Updates an existing WFS feature type in the database.
	 * 
	 * @return Boolean
	 * @param $aWfsFeatureType WfsFeatureType
	 */
	private static function updateFeatureType ($aWfsFeatureType) {

		$aWfsFeatureType->id = WfsToDb::getFeatureTypeId($aWfsFeatureType);

		$sql = "UPDATE wfs_featuretype SET ";
		$sql .= "featuretype_title = $1,";
		$sql .= "featuretype_abstract = $2,";
		$sql .= "featuretype_srs = $3 ";
		$sql .= "WHERE featuretype_id = $4";
		$v = array(
			$aWfsFeatureType->title,
			$aWfsFeatureType->summary,
			$aWfsFeatureType->srs,
			$aWfsFeatureType->id
		);
		$t = array('s','s','s','i');

		$e = new mb_exception("UPDATING FT (FT: $aWfsFeatureType->id)");

		$res = db_prep_query($sql,$v,$t);
		if (!$res) {
			$e = new mb_exception("Error while updating WFS feature type in database.");
			return false;
		}
		
		// update existing WFS feature type elements
		$featureTypeElementNameArray = array();
		for ($i = 0; $i < count($aWfsFeatureType->elementArray); $i++) {
			$currentElement = $aWfsFeatureType->elementArray[$i];
			array_push($featureTypeElementNameArray, $currentElement);
			if (WfsToDb::featureTypeElementExists($aWfsFeatureType, $currentElement->name)) {
				if (!WfsToDb::updateFeatureTypeElement($aWfsFeatureType->id, $currentElement)) {
					return false;
				}
			}
			else {
				if (!WfsToDb::insertFeatureTypeElement($aWfsFeatureType->id, $currentElement)) {
					return false;
				}
			}
		}		
		
		// delete obsolete WFS feature type elements
		$v = array($aWfsFeatureType->id);
		$t = array("i");
		$sql = "DELETE FROM wfs_element WHERE fkey_featuretype_id = $1";

		$sql_in = "";
		for ($i = 0; $i < count($featureTypeElementNameArray); $i++) {
			if ($i > 0) {
				$sql_in .= ", ";
			}
			$sql_in .= "$" . ($i+2);
			array_push($v, $featureTypeElementNameArray[$i]->name);
			array_push($t, "s");
		}
		if ($sql_in !== "")
		$sql .= " AND element_name NOT IN (" . $sql_in . ")";

		$res = db_prep_query($sql,$v,$t);
		if (!$res) {
			$e = new mb_exception("Error while deleting obsolete WFS feature type element in database.");
			return false;
		}

		// delete all namespaces of this WFS feature type
		$sql = "DELETE FROM wfs_featuretype_namespace WHERE ";
		$sql .= "fkey_wfs_id = $1 AND fkey_featuretype_id = $2";
		$v = array(
			$aWfsFeatureType->wfs->id, 
			$aWfsFeatureType->id
		);
		$t = array("i", "i");
		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			$e = new mb_exception("Error while deleting WFS feature type namespaces from the database.");
			return false;
		}		
		
		// insert feature type namespaces
		for ($i = 0; $i < count($aWfsFeatureType->namespaceArray); $i++) {
			$namespace = $aWfsFeatureType->namespaceArray[$i];
			if (!WfsToDb::insertFeatureTypeNamespace ($aWfsFeatureType->wfs->id, $aWfsFeatureType->id, $namespace)) {
				$e = new mb_exception("Error while inserting WFS feature type namespaces into the database.");
				return false;
			}
		}		
		
		// update categories for feature type
		$types = array("md_topic", "inspire", "custom");
		foreach ($types as $cat) {
			$sql = "DELETE FROM wfs_featuretype_{$cat}_category WHERE fkey_featuretype_id = $1";
			$v = array($aWfsFeatureType->id);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);
			if(!$res){
					$e = new mb_exception("Error while deleting old categories for WFS feature type in the database.");
					return false;
				}
			
			$attr = "featuretype_{$cat}_category_id";
			$k = $aWfsFeatureType->$attr;
			
			if($aWfsFeatureType->$attr && count($k) > 0) {
				for ($j = 0; $j < count($k); $j++) {
					if ($k[$j] != "") { 
						$sql = "INSERT INTO wfs_featuretype_{$cat}_category (fkey_featuretype_id, fkey_{$cat}_category_id) VALUES ($1, $2)";
						$v = array($aWfsFeatureType->id, $k[$j]);
						$t = array('i', 'i');
						$res = db_prep_query($sql,$v,$t);
						if(!$res){
							$e = new mb_exception("Error while inserting WFS feature type categories into the database.");
							return false;
						}
					}
				}
			}	
		}	
		
		
		// update keywords
		$sql = "DELETE FROM wfs_featuretype_keyword WHERE fkey_featuretype_id = $1";
		$v = array($aWfsFeatureType->id);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		
		$k = $aWfsFeatureType->featuretype_keyword;
//		var_dump($k);
		for($j=0; $j<count($k); $j++){
			$keyword_id = "";
			
			while ($keyword_id == "") {
				$sql = "SELECT keyword_id FROM keyword WHERE UPPER(keyword) = UPPER($1)";
				$v = array($k[$j]);
				$t = array('s');
				$res = db_prep_query($sql,$v,$t);
				$row = db_fetch_array($res);
			//print_r($row);
				if ($row) {
					$keyword_id = $row["keyword_id"];	
				}
				else {
					$sql_insertKeyword = "INSERT INTO keyword (keyword)";
					$sql_insertKeyword .= "VALUES ($1)";
					$v1 = array($k[$j]);
					$t1 = array('s');
					$res_insertKeyword = db_prep_query($sql_insertKeyword,$v1,$t1);
					if(!$res_insertKeyword){
						$e = new mb_exception("Error while inserting keywords into the database.");
						return false;	
					}
				}
			}

			// check if featuretype/keyword combination already exists
			$sql_fiKeywordExists = "SELECT * FROM wfs_featuretype_keyword WHERE fkey_featuretype_id = $1 AND fkey_keyword_id = $2";
			$v = array($aWfsFeatureType->id, $keyword_id);
			$t = array('i', 'i');
			$res_fiKeywordExists = db_prep_query($sql_fiKeywordExists, $v, $t);
			$row = db_fetch_array($res_fiKeywordExists);
			//print_r($row);
			if (!$row) {
				$sql1 = "INSERT INTO wfs_featuretype_keyword (fkey_keyword_id,fkey_featuretype_id)";
				$sql1 .= "VALUES ($1,$2)";
				$v1 = array($keyword_id,$aWfsFeatureType->id);
				$t1 = array('i','i');
				$res1 = db_prep_query($sql1,$v1,$t1);
				if(!$res1){
					$e = new mb_exception("Error while inserting wfs_featuretype_keywords into the database.");
					return false;
				}
			}
		}
		
		return true;		
	}
	
	/**
	 * Deletes an existing WFS feature type from the database.
	 * 
	 * @return Boolean
	 * @param $aWfsFeatureType WfsFeatureType
	 */
	private static function deleteFeatureType ($aWfsFeatureType) {
		$sql = "DELETE FROM wfs_featuretype WHERE featuretype_id = $1 AND fkey_wfs_id = $2";
		$v = array($aWfsFeatureType->id, $aWfsFeatureType->wfs->id);
		$t = array('i', 'i');

		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			$e = new mb_exception("Error while deleting WFS feature type from database.");
			return false;
		}
		return true;
	}
	
	/**
	 * Checks if a featuretype exists in the database. It selects the rows
	 * that match the WFS id and the featuretype name.
	 * 
	 * If the featuretype is found the featuretype id is returned.
	 * 
	 * @return Integer
	 * @param $aWfsFeatureType WfsFeatureType
	 */
	private static function getFeatureTypeId ($aWfsFeatureType) {
		$sql = "SELECT featuretype_id FROM wfs_featuretype WHERE " . 
			"fkey_wfs_id = $1 AND featuretype_name = $2";
		$v = array(
			$aWfsFeatureType->wfs->id,
			$aWfsFeatureType->name
		);
		$t = array("i", "s");
		$e = new mb_exception($sql . " " . print_r($v, true));
		$res = db_prep_query($sql, $v, $t);
		if ($row = db_fetch_array($res)) {
			return $row["featuretype_id"];
		}
		return null;
	}
	
	/**
	 * Checks if a featuretype exists in the database. 
	 * 
	 * @return Boolean
	 * @param $aWfsFeatureType WfsFeatureType
	 */
	private static function featureTypeExists ($aWfsFeatureType) {
		if (WfsToDb::getFeatureTypeId($aWfsFeatureType) !== null) {
			return true;
		}
		return false;
	}

	/**
	 * Gets the ID of a feature type element 
	 * 
	 * @return Integer
	 * @param $aWfsFeatureType WfsFeatureType
	 * @param $name WFS feature type element name
	 */
	private static function getFeatureTypeElementId ($aWfsFeatureType, $name) {
		$sql = "SELECT element_id FROM wfs_element WHERE " . 
			"fkey_featuretype_id = $1 AND element_name = $2";
		$v = array(
			$aWfsFeatureType->id,
			$name,
		);
		$t = array("i", "s");
		$res = db_prep_query($sql, $v, $t);
		if ($row = db_fetch_array($res)) {
			return $row["element_id"];
		}
		return null;
	}
	
	/**
	 * Checks if a featuretype element exists in the database. 
	 * 
	 * @return Boolean
	 * @param $aWfsFeatureType WfsFeatureType
	 * @param $name WFS feature type element name
	 */
	private static function featureTypeElementExists ($aWfsFeatureType, $name) {
		if (WfsToDb::getFeatureTypeElementId($aWfsFeatureType, $name) !== null) {
			return true;
		}
		return false;
	}
}
?>
