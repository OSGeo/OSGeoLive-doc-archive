<?php
# $Id: class_wfs_conf.php 3510 2009-02-03 10:36:01Z christoph $
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

/**
 * This is the configuration of a WFS featuretype element. It belongs
 * to a configuration of a WFS featuretype.
 */
class WfsConfigurationElement {
	var $id;
	var $name;
	var $type;
	var $search;
	var $styleId;
	var $toUpper;
	var $label;
	var $labelId;
	var $geom;
	var $show;
	var $mandatory;
	var $respos;
	var $minInput;
	var $formElementHtml;
	var $authVarname;
	var $detailPos;
	var $operator;
	var $showDetail;
	var $helptext;
	var $category;
	
	/**
	 * Creates a WFS featuretype element configuration from an object.
	 * The object presumably comes from a client-side JSON.
	 * 
	 * @return WfsConfigurationElement the WFS featuretype element configuration
	 * @param $obj Object
	 */
	public static function createFromObject ($obj) {
		$element = new WfsConfigurationElement();
		$element->id = intval($obj->id);
		$element->name = $obj->name;
		$element->type = $obj->type;
		$element->search = intval($obj->search);
		$element->pos = intval($obj->pos);
		$element->styleId = $obj->styleId;
		$element->toUpper = intval($obj->toUpper);
		$element->edit = intval($obj->edit);
		$element->label = $obj->label;
		$element->labelId = $obj->labelId;
		$element->geom = intval($obj->geom);
		$element->show = intval($obj->show);
		$element->mandatory = intval($obj->mandatory);
		$element->respos = intval($obj->respos);
		$element->minInput = intval($obj->minInput);
//		$element->formElementHtmlTemplate = $row["f_html_template"];
		$element->formElementHtml = $obj->formElementHtml;
		$element->authVarname = $obj->authVarname;
		$element->detailPos = intval($obj->detailPos);
		$element->operator = $obj->operator;
		$element->showDetail = intval($obj->showDetail);
		$element->helptext = $obj->helptext;
		$element->category = $obj->category;
		return $element;
	}

	/**
	 * Inserts a new WFS featuretype element configuration into the database.
	 * 
	 * @return Integer the ID of the new row
	 * @param $wfsConfId Integer The WFS featuretype configuration ID
	 * @param $el WfsConfigurationElement
	 */
	public static function insertIntoDb ($wfsConfId, $el) {
		$sql = file_get_contents("../../sql/insert_wfs_conf_element.sql");
		$v = array(
			$wfsConfId,	$el->id, $el->geom,	$el->search, $el->pos,
			$el->styleId, $el->toUpper,	$el->label,	$el->labelId,
			$el->show, $el->respos, $el->formElementHtml, $el->edit,
			$el->mandatory,	$el->authVarname, $el->operator,
			$el->showDetail, $el->detailPos, $el->minInput, $el->helptext, $el->category
		);
		$t = array(
			"i", "i", "i", "i", "i", "s", "i", "s", "s", "i", 
			"i", "s", "i", "i", "s", "s", "i", "i", "i", "s", "s"
		);
		
		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			return false;
		}
		return true;
	}

	/**
	 * Updates a WFS featuretype element configuration in the database.
	 * 
	 * @return Boolean true if succeeded; else false
	 * @param $el WfsConfigurationElement
	 */
	public static function updateInDb ($wfsConfId, $el) {
		$sql = file_get_contents("../../sql/update_wfs_conf_element.sql");
		$v = array(
			$el->geom, $el->search, $el->pos, $el->styleId, $el->toUpper,
			$el->label,	$el->labelId, $el->show, $el->respos, 
			$el->formElementHtml, $el->edit, $el->mandatory,
			$el->authVarname, $el->operator, $el->showDetail, $el->detailPos,  
			$el->minInput, $el->helptext, $el->category, $el->id, $wfsConfId
		);
		$t = array(
			"i", "i", "i", "s", "i", "s", "s", "i", "i", 
			"s", "i", "i", "s", "s", "i", "i", "i", "s", "s", "i", "i"
		);
		
		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			return false;
		}
		return true;
	}
}	


// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------


/**
 * The configuration of a WFS featuretype.
 */
class WfsConfiguration {
	
	var $id;
	var $type;
	var $wfsId;
	var $featureTypeId;
	var $featureTypeName;
	var $label; 
	var $labelId;
	var $style;
	var $button;
	var $buttonId;
	var $buffer;
	var $resStyle;
	var $elementArray = array();

	function __construct () {
	}
	
	public function getGeometryColumnName () {
		foreach ($this->elementArray as $element) {
			if ($element->geom) {
				return $element->name;
			}
		}
		$e = new mb_warning("This WFS conf doesn't have a geometry column.");
		return null;
	}

        public function getGeometryColumnType () {
		foreach ($this->elementArray as $element) {
			if ($element->geom) {
				return $element->type;
			}
		}
		$e = new mb_warning("This WFS conf doesn't have a geometry column.");
		return null;
	}
	
	/**
	 * Finds the featuretype element which stores the authentication data.
	 * 
	 * @return String 
	 */
	public function getAuthElement () {
		foreach ($this->elementArray as $element) {
			if (!empty($element->authVarname)) {
				#$validname = preg_match('/^\$_[a-zA-z]+(\[\"[a-zA-Z_]+\"\])?$/', $element->authVarname);

				#if ($validname === 1) {
					return $element;
				#}
				#else {
				#	$e = new mb_exception("Found auth element, but variable name is not valid: " . $element->authVarname);
				#}
			}
		}
		return null;
	}

	/**
	 * Checks if the user currently logged in is allowed to access
	 * the WFS configuration
	 * 
	 * @return Boolean
	 */
	private function accessAllowed () {
		if (Mapbender::session()->get("mb_user_id")) {
			$user = new User(Mapbender::session()->get("mb_user_id"));

			$allowedWfsConfIds = $user->getWfsConfByPermission();

			$idArray = array_intersect(array($this->id), $allowedWfsConfIds);

			if (count($idArray) === 1) {
				return true;
			}
		}
		$e = new mb_exception("User '" . Mapbender::session()->get("mb_user_id") . "' is not allowed to access Wfs conf " . $this->id . ".");
		return false;
	}
	
	/**
	 * Creates a new WFS configuration form an object,
	 * presumably originating from a client side JSON
	 * generated from a form.
	 * 
	 * @return Integer ID of the new object in the database
	 * @param $obj Object
	 */
	public static function createFromObject ($obj) {		
		$wfsConf = new WfsConfiguration();

		//
		// use case: existing WFS conf
		//
		if ($obj->id !== null) {
			$wfsConf->id = intval($obj->id);
			if (!$wfsConf->accessAllowed()) {
				return null;
			}
		}
		//
		// use case: new WFS conf
		//
		else {
			// TO DO: check if access is allowed!
			// (use the new WFS classes for this)
			$wfsConf->wfsId = intval($obj->wfsId);
			$wfsConf->featureTypeId = intval($obj->featuretypeId);
		}
	 
		$wfsConf->type = intval($obj->type);
		$wfsConf->label = $obj->label;
		$wfsConf->labelId = $obj->labelId;
		$wfsConf->button = $obj->button;
		$wfsConf->buttonId = $obj->buttonId;
		$wfsConf->style = $obj->style;
		$wfsConf->buffer = floatval($obj->buffer);
		$wfsConf->resStyle = $obj->resStyle; 
		$wfsConf->abstr = $obj->abstr;
		$wfsConf->description = $obj->description;

		for ($i = 0; $i < count($obj->elementArray); $i++) {
			$element = WfsConfigurationElement::createFromObject($obj->elementArray[$i]);
			$wfsConf->elementArray[]= $element;
		}
		return $wfsConf;
	}	

	/**
	 * Update an existing WFS configuration in the database.
	 * 
	 * @return Boolean true if update succeeds; else false
	 * @param $wfsConf WfsConfiguration the WFS configuration
	 */
	public static function updateInDb ($wfsConf) {
		if (!$wfsConf->accessAllowed()) {
			return false;
		}
		db_begin();

		$sql = file_get_contents("../../sql/update_wfs_conf.sql");
		$v = array(
			$wfsConf->abstr, $wfsConf->label, $wfsConf->labelId, 
			$wfsConf->button, $wfsConf->buttonId, $wfsConf->style, 
			$wfsConf->buffer, $wfsConf->resStyle, $wfsConf->description, 
			$wfsConf->type, $wfsConf->id
		);
		$t = array(
			"s", "s", "s", "s", "s", "s", "d", "s", "s", "i", "i"
		);
		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			$e = new mb_exception("WFS Conf update failed.");
			db_rollback();
			return false;
		}
		
		//
		// Update WFS featuretype conf elements
		//
		for ($i = 0; $i < count($wfsConf->elementArray); $i++) {
			$el = $wfsConf->elementArray[$i];
			$success = WfsConfigurationElement::updateInDb($wfsConf->id, $el);
			if (!$success) {
				db_rollback();
				return null;
			}
		}		

		db_commit();
		return true;
	}
	
	/**
	 * Inserts a new WFS featuretype configuration into the database.
	 * 
	 * @return Integer the ID of the new WFS configuration
	 * @param $wfsConf WfsConfiguration
	 */
	public static function insertIntoDb ($wfsConf) {
		if (!$wfsConf->wfsId || !$wfsConf->featureTypeId) {
			return null;
		}

		db_begin();
		
		$sql = file_get_contents("../../sql/insert_wfs_conf.sql");
		$v = array(
			$wfsConf->abstr, $wfsConf->wfsId, $wfsConf->featureTypeId,
			$wfsConf->label, $wfsConf->labelId, $wfsConf->button, 
			$wfsConf->buttonId, $wfsConf->style, $wfsConf->buffer, 
			$wfsConf->resStyle, $wfsConf->description, $wfsConf->type
		);
		$t = array(
			"s", "i", "i", "s", "s", "s", "s", "s", "d", "s", "s", "i"
		);
		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			$e = new mb_exception("WFS Conf insert failed.");
			db_rollback();
			return null;
		}
		
		//
		// get ID of this WFS conf
		//
		$sql = "SELECT max(wfs_conf_id) AS max_id FROM wfs_conf";
		$res = db_query($sql);
		if (!$res) {
			db_rollback();
			return null;
		}
		$row = db_fetch_array($res);
		$id = $row["max_id"];
		if (!$id) {
			db_rollback();
			return null;
		}

		//
		// insert WFS conf elements
		//
		for ($i = 0; $i < count($wfsConf->elementArray); $i++) {
			$el = $wfsConf->elementArray[$i];
			$success = WfsConfigurationElement::insertIntoDb($id, $el);
			if (!$success) {
				db_rollback();
				return null;
			}
		}		

		db_commit();
		return $id;
	}
	

	
	/**
	 * Creates an object from the database.
	 * Maybe we could have a factory for this later...let's
	 * keep it simple for now
	 * 
	 * @return WfsConfiguration
	 * @param $id Integer
	 */
	public static function createFromDb ($id) {
		if (!is_numeric($id)) {
			return null;	
		}
		$wfsConf = new WfsConfiguration();
		$wfsConf->id = intval($id);
		
		if (!$wfsConf->accessAllowed()) {
			return null;
		}
		
		$sql = <<<SQL
SELECT * FROM wfs_conf JOIN wfs ON wfs_conf.fkey_wfs_id = wfs.wfs_id 
WHERE wfs_conf.wfs_conf_id = $1 LIMIT 1
SQL;

        $v = array($wfsConf->id);
        $t = array("i");
        $res = db_prep_query($sql, $v, $t);
        $row = db_fetch_array($res);
		
		$wfsConf->abstr = $row["wfs_conf_abstract"];
		$wfsConf->description = $row["wfs_conf_description"];
		$wfsConf->label = $row["g_label"];
		$wfsConf->labelId = $row["g_label_id"];
		$wfsConf->style = $row["g_style"];
		$wfsConf->button = $row["g_button"];
		$wfsConf->buttonId = $row["g_button_id"];
		$wfsConf->buffer = $row["g_buffer"];
		$wfsConf->resStyle = $row["g_res_style"];
		$wfsConf->wfsId = intval($row["fkey_wfs_id"]);
		$wfsConf->type = intval($row["wfs_conf_type"]);
		$wfsConf->featureTypeId = intval($row["fkey_featuretype_id"]);

		$sql = <<<SQL
SELECT featuretype_name FROM wfs_featuretype WHERE featuretype_id = $1 LIMIT 1
SQL;

        $v = array($wfsConf->featureTypeId);
        $t = array("i");
        $res = db_prep_query($sql, $v, $t);
        $row = db_fetch_array($res);
		
		$wfsConf->featureTypeName = $row["featuretype_name"];

		$sql = <<<SQL
SELECT * FROM wfs_conf_element JOIN wfs_element 
ON wfs_conf_element.f_id = wfs_element.element_id 
WHERE wfs_conf_element.fkey_wfs_conf_id = $1 
ORDER BY wfs_conf_element.f_id
SQL;
		$v = array($wfsConf->id);
		$t = array('i');
		$res = db_prep_query($sql, $v, $t);
	
		
		while ($row = db_fetch_array($res)) {
			$element = new WfsConfigurationElement();

			$element->id = intval($row["element_id"]);
			$element->name = $row["element_name"];
			$element->type = $row["element_type"];
			$element->search = intval($row["f_search"]);
			$element->pos = intval($row["f_pos"]);
			$element->edit = intval($row["f_edit"]);
			$element->styleId = $row["f_style_id"];
			$element->toUpper = intval($row["f_toupper"]);
			$element->label = $row["f_label"];
			$element->labelId = $row["f_label_id"];
			$element->geom = intval($row["f_geom"]);
			$element->show = intval($row["f_show"]);
			$element->mandatory = intval($row["f_mandatory"]);
			$element->respos = intval($row["f_respos"]);
			$element->minInput = intval($row["f_min_input"]);
//			$element->formElementHtmlTemplate = $row["f_html_template"];
			$element->formElementHtml = $row["f_form_element_html"];
			$element->authVarname = stripslashes($row["f_auth_varname"]);
			$element->detailPos = intval($row["f_detailpos"]);
			$element->operator = $row["f_operator"];
			$element->showDetail = intval($row["f_show_detail"]);
			$element->helptext = $row["f_helptext"];
			$element->category = $row["f_category_name"];

			$wfsConf->elementArray[]= $element;
		}

		$wfsConf->id = intval($id);
		return $wfsConf;
	}
}
?>
