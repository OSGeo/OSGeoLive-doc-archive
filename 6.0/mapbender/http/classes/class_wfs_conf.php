<?php
# $Id: class_wfs_conf.php 7517 2011-02-02 18:28:18Z kmq $
# http://www.mapbender.org/index.php/class_wfs_conf.php
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
require_once(dirname(__FILE__)."/../classes/class_user.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");

class WfsConf {
	var $confArray = array();

	/**
	 * Gets the configuration from the database
	 */
	function __construct () {

	}
	
	function __toString () {
		$json = new Mapbender_JSON();
		return $json->encode($this->confArray);
	}
	
	/**
	 * Loads WFS conf data from the database
	 * 
	 * @return Object WFS conf data.
	 * @param $idOrIdArray Object May be an integer or an array of integers representing WFS conf IDs.
	 */
	public function load ($idOrIdArray) {
		// Check parameter and set idArray
		if (isset($idOrIdArray)){
			
			// parameter is a number	
			if (!is_array($idOrIdArray) && is_numeric($idOrIdArray)) {
				$idOrIdArray = array(intval($idOrIdArray));
			}

			// parameter is an array of numbers
			if (is_array($idOrIdArray)) {
				$idArray = array();
				foreach ($idOrIdArray as $id) {
					if (!is_numeric($id)) {
						$e = new mb_exception("Wfs_conf: constructor: wrong parameter: ".$id." is not a number.");
						return array();
					}
					array_push($idArray, intval($id));
				}

				// If a user ID is given, remove the ones the user has no access to
				if (Mapbender::session()->get("mb_user_id")) {
					$user = new User(Mapbender::session()->get("mb_user_id"));
					$idArray = array_intersect($idArray, $user->getWfsConfByPermission());
				}

				return $this->getWfsConfFromDb($idArray);
			}
			// parameter is invalid
			else {
				$e = new mb_exception("Wfs_conf: constructor: parameter must be number or an array of numbers.");
				return array();
			}
		}
		else {
			$e = new mb_exception("Wfs_conf: constructor: parameter is not valid");
			return null;
		}
	}
	

	// --------------------------- private -----------------------------------

	/**
	 * Gets the database content for a number of WFS configurations given by their IDs.
	 * 
	 * @return Array
	 * @param $idArray Array an array of integer values representing WFS conf IDs.
	 */
	private static function getWfsConfFromDbByArray ($idArray) {
		$rowArray = array();
        foreach ($idArray as $id) {
        		$sql = "SELECT * FROM wfs_conf ";
                $sql .= "JOIN wfs ON wfs_conf.fkey_wfs_id = wfs.wfs_id ";
                $sql .= "WHERE wfs_conf.wfs_conf_id = $1 LIMIT 1";
        
                $v = array($id);
                $t = array("i");
                $res = db_prep_query($sql, $v, $t);
                $row = db_fetch_array($res);
                array_push($rowArray, $row);
        }
        return $rowArray;
	}
	
	public static function getGeomColumnNameByConfId ($confId) {
		$elArray = self::getWfsConfElementFromDb($confId);
		foreach ($elArray as $element) {
			if ($element["f_geom"] == "1") {
				return $element["element_name"];
			}
		}
		return null;
	}
	
	/**
	 * Gets the database content of a WFS conf element given by a WFS conf ID.
	 * 
	 * @return Array
	 * @param $id Integer the WFS conf ID.
	 */
	private static function getWfsConfElementFromDb ($id) {
		$sql = "SELECT * FROM wfs_conf_element ";
		$sql .= "JOIN wfs_element ON wfs_conf_element.f_id = wfs_element.element_id ";
		$sql .= "WHERE wfs_conf_element.fkey_wfs_conf_id = $1 ";
		#filtered on client side
		#$sql .= "AND wfs_conf_element.f_search = 1 ";
		$sql .= "ORDER BY wfs_conf_element.f_pos";
		$v = array($id);
		$t = array('i');
		$res = db_prep_query($sql, $v, $t);
	
		$elementArray = array();
		while ($row = db_fetch_array($res)) {
			$currentElement = array("element_name" => $row["element_name"],
									"element_type" => $row["element_type"],
									"f_search" => $row["f_search"],
									"f_style_id" => $row["f_style_id"],
									"f_toupper" => $row["f_toupper"],
									"f_label" => $row["f_label"],
									"f_label_id" => $row["f_label_id"],
									"f_geom" => $row["f_geom"],
									"f_show" => $row["f_show"],
									"f_mandatory" => $row["f_mandatory"],
									"f_respos" => $row["f_respos"],
									"f_min_input" => $row["f_min_input"],
									"f_form_element_html" => $row["f_form_element_html"],
									"f_auth_varname" => $row["f_auth_varname"],
									"f_detailpos" => $row["f_detailpos"],
									"f_operator" => $row["f_operator"],
									"f_show_detail" => $row["f_show_detail"],
									"f_helptext" => $row["f_helptext"] == null? "":$row["f_helptext"]
									);
			array_push($elementArray, $currentElement);
		}
		return $elementArray;
	}

	/**
	 * Gets the database content of a WFS feature type given by a WFS ID and a featuretype ID.
	 * 
	 * @return Array
	 * @param $wfsId Integer the WFS ID.
	 * @param $featuretypeId Integer the WFS featuretype ID.
	 */
	private static function getWfsFeatureTypeFromDb($wfsId, $featuretypeId) {
		$sql = "SELECT * FROM wfs_featuretype WHERE fkey_wfs_id = $1 AND featuretype_id = $2";
		$v = array($wfsId, $featuretypeId);
		$t = array("i", "i");
	
		$res = db_prep_query($sql, $v, $t);

		$currentRow = array();
		
		if($row = db_fetch_array($res)){
			$currentRow["featuretype_name"] = $row["featuretype_name"];
			$currentRow["featuretype_srs"] = $row["featuretype_srs"];
		}
	
		return $currentRow;
	}
	
	/**
	 * get WFS conf data from database
	 */
	public function getWfsConfFromDB ($idArray) {
		
		// if a user has access to some WFS confs...
		if (count($idArray) > 0) {

			// get WFS conf data from DB
			$rowArray = self::getWfsConfFromDbByArray($idArray);
			
			for ($i=0; $i < count($rowArray); $i++) {
	
				// WFS conf data				
				$currentRow = array("g_label" => $rowArray[$i]["g_label"], 
									"wfs_conf_abstract" => $rowArray[$i]["wfs_conf_abstract"],
			                        "g_label_id" => $rowArray[$i]["g_label_id"],
									"g_style" => $rowArray[$i]["g_style"],
									"g_button" => $rowArray[$i]["g_button"],
									"g_button_id" => $rowArray[$i]["g_button_id"],
									"g_buffer" => $rowArray[$i]["g_buffer"],
									"g_res_style" => $rowArray[$i]["g_res_style"],
									"g_use_wzgraphics" => $rowArray[$i]["g_use_wzgraphics"],
									"wfs_id" => $rowArray[$i]["fkey_wfs_id"],
									"featuretype_id" => $rowArray[$i]["fkey_featuretype_id"],
									"wfs_getfeature" => $rowArray[$i]["wfs_getfeature"],
									"wfs_describefeaturetype" => $rowArray[$i]["wfs_describefeaturetype"],
									"wfs_transaction" => $rowArray[$i]["wfs_transaction"],
									"wfs_conf_id" => $rowArray[$i]["wfs_conf_id"],
									"wfs_conf_type" => $rowArray[$i]["wfs_conf_type"],
									"element" => $elementArray
									);

				// get WFS conf element data of current WFS conf
				$id = $rowArray[$i]["wfs_conf_id"];
				$currentRow["element"] = self::getWfsConfElementFromDb($id);

				// get WFS featuretype data of current WFS conf
				$wfsId = $rowArray[$i]["fkey_wfs_id"];
				$featuretypeId = $rowArray[$i]["fkey_featuretype_id"];
				$currentRow = array_merge($currentRow , self::getWfsFeatureTypeFromDb($wfsId, $featuretypeId));

				$this->confArray[$id] = $currentRow;
			}
			return $this->confArray;
		}
		else {
			$e = new mb_warning("class_wfs_conf.php: getWfsConfFromDB: You don't have access to any WFS confs. Check EDIT WFS.");
			return array();
		}
	}
	
}

/**
 * @deprecated
 */
class wfs_conf{
	
	var $wfs_id;
	var $wfs_name;
	var $wfs_title;
	var $wfs_abstract;
	var $wfs_getcapabilities;
	var $wfs_describefeaturetype;
	var $wfs_getfeature;
	
	var $features;
	var $elements;
	var $namespaces;
		

	function getallwfs($userid){
		$this->wfs_id = array();
		$this->wfs_name = array();
		$this->wfs_title = array();
		$this->wfs_abstract = array();
		
		global $DBSERVER,$DB,$OWNER,$PW;
		$con = db_connect($DBSERVER,$OWNER,$PW);
		db_select_db($DB,$con);
		if($userid){
		 	$sql = "SELECT * FROM wfs WHERE wfs_owner = $1";
			$v = array($userid);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);
		}
		else{
		 	$sql = "SELECT * FROM wfs";
			$res = db_query($sql);
		}
		
		$cnt = 0;
		while ($row = db_fetch_array($res)){
			$this->wfs_version[$cnt] = $row["wfs_version"];
			$this->wfs_id[$cnt] = $row["wfs_id"];
			$this->wfs_name[$cnt] = $row["wfs_name"];
			$this->wfs_title[$cnt] = $row["wfs_title"];
			$this->wfs_abstract[$cnt] = $row["wfs_abstract"];
			$this->wfs_getcapabilities[$cnt] = $row["wfs_getcapabilities"];
			$this->wfs_describefeaturetype[$cnt] = $row["wfs_describefeaturetype"];
			$this->wfs_getfeature[$cnt] = $row["wfs_getfeature"];
			$cnt++;
		}	
	}
	function getfeatures($wfsid){
		$this->features = new features($wfsid);
	}
	function getelements($feature){
		$this->elements = new elements($feature);
	}
	function getnamespaces($feature){
		$this->namespaces = new namespaces($feature);
	}
}
class features extends wfs_conf{
	
	var $featuretype_id;
	var $featuretype_name;
	var $featuretype_title;
	var $featuretype_srs;
	
	function features($id){		
		
		$featuretype_id = array();
		$featuretype_name = array();
		$featuretype_title = array();
		$featuretype_srs = array();
		
		global $DBSERVER,$DB,$OWNER,$PW;
		$con = db_connect($DBSERVER,$OWNER,$PW);
		db_select_db($DB,$con);
		$sql = "SELECT * FROM wfs_featuretype WHERE fkey_wfs_id = $1";
		$v = array($id);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);
		$cnt = 0;
		while ($row = db_fetch_array($res)){
			$this->featuretype_id[$cnt] = $row["featuretype_id"];
			$this->featuretype_name[$cnt] = $row["featuretype_name"];
			$this->featuretype_title[$cnt] = $row["featuretype_title"];
			$this->featuretype_srs[$cnt] = $row["featuretype_srs"];
			$cnt++;
		}	
	}	
}
class elements extends wfs_conf{
	
	var $element_id;
	var $element_name;
	var $element_type;
	
	function elements($fid){
		
		$element_id = array();
		$element_name = array();
		$element_type = array();
		
		global $DBSERVER,$DB,$OWNER,$PW;
		$con = db_connect($DBSERVER,$OWNER,$PW);
		db_select_db($DB,$con);
		$sql = "SELECT * FROM wfs_element WHERE fkey_featuretype_id = $1";
		$v = array($fid);
		$t = array("s");
		$res = db_prep_query($sql, $v, $t);
		$cnt = 0;
		while ($row = db_fetch_array($res)){
			$this->element_id[$cnt] = $row["element_id"];
			$this->element_name[$cnt] = $row["element_name"];
			$this->element_type[$cnt] = $row["element_type"];
			$cnt++;
		}
	}
}
class namespaces extends wfs_conf{
	
	var $namespace_name;
	var $namespace_location;
	
	function namespaces($fid){
		
		$namespace_name = array();
		$namespace_location = array();
		
		global $DBSERVER,$DB,$OWNER,$PW;
		$con = db_connect($DBSERVER,$OWNER,$PW);
		db_select_db($DB,$con);
		$sql = "SELECT * FROM wfs_featuretype_namespace WHERE fkey_featuretype_id = $1";
		$v = array($fid);
		$t = array("s");
		$res = db_prep_query($sql, $v, $t);
		$cnt = 0;
		while ($row = db_fetch_array($res)){
			$this->namespace_name[$cnt] = $row["namespace"];
			$this->namespace_location[$cnt] = $row["namespace_location"];
			$cnt++;
		}
	}
}
?>
