<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_wms_factory.php";
require_once dirname(__FILE__) . "/../classes/class_wms.php";
require_once dirname(__FILE__) . "/../classes/class_connector.php";
require_once dirname(__FILE__) . "/../classes/class_administration.php";

/**
 * Creates WMS < 1.2 objects from a capabilities documents.
 * 
 * @return Wms
 */
class Wms_1_1_1_Factory extends WmsFactory {

	/**
	 * Creates WMS < 1.2 objects from a capabilities documents.
	 * 
	 * @return Wms
	 * @param $xml String
	 */
	public function createFromXml ($xml) {
	}
	
	public function createFromDb ($id) {
		$appId = func_num_args() >= 2 ? 
			func_get_arg(1) : null;

		$myWms = new wms();
		if (!is_null($appId)) {
			return parent::createFromDb($id, $myWms, $appId);
		}
		return parent::createFromDb($id, $myWms);
	}
	
	public function createLayerFromDb ($id) {
		$wmsId = func_num_args() >= 2 ? 
			func_get_arg(1) : null;

		$appId = func_num_args() >= 3 ? 
			func_get_arg(2) : null;

		//
		// get WMS of this layer
		//
		if (!is_null($appId)) {
			$myWms = $this->createFromDb($wmsId, $appId);
		}
		else {
			$myWms = $this->createFromDb($wmsId);
		}
		
		if (is_null($myWms)) {
			return null;
		}
		
		//
		// delete all layers apart from the one mentioned (but keep parents and children)
		//
		
		// Find layers that have both parents and children for testing:
		// SELECT DISTINCT q.layer_id, q.layer_pos, q.layer_parent FROM layer q, layer r WHERE r.layer_parent <> '' AND q.layer_pos = CAST(r.layer_parent AS numeric) and q.layer_parent = '0' and q.fkey_wms_id = r.fkey_wms_id
		$currentLayer = $myWms->getLayerById($id);

		$keep = array();

		$parents = $currentLayer->getParents();
		foreach ($parents as $parent) {
			$keep[]= $parent->layer_uid;
		}

		$keep[]= $currentLayer->layer_uid;

		$children = $currentLayer->getChildren();
		foreach ($children as $child) {
			$keep[]= $child->layer_uid;
		}

		//
		// 2. delete layers not for keeping
		//
		$i = 0;
		while ($i < count($myWms->objLayer)) {
			$l = $myWms->objLayer[$i];
			if (in_array($l->layer_uid, $keep)) {
				$i++;
				continue;
			}
			// delete layer
			array_splice($myWms->objLayer, $i, 1);
		}
		return $myWms;
	}
}
?>