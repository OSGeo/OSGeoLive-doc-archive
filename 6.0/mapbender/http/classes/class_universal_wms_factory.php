<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/class_administration.php";
require_once dirname(__FILE__) . "/class_ows_factory.php";
require_once dirname(__FILE__) . "/class_wms_factory.php";
require_once dirname(__FILE__) . "/class_wms_1_1_1_factory.php";

/**
 * 
 * @return 
 * @param $xml String
 */
class UniversalWmsFactory extends WmsFactory {
	
	/**
	 * Parses the capabilities document for the WMS 
	 * version number and returns it.
	 * 
	 * @return String
	 * @param $xml String
	 */
	private function getVersionFromXml ($xml) {
		// of course to be refactored. Up to now, the same factory 
		// handles just 1.1.1 and below
		return "1.1.1 or older";
/*
		$admin = new administration();
		$values = $admin->parseXml($xml);
		
		foreach ($values as $element) {
			if($this->sepNameSpace(strtoupper($element[tag])) == "WFS_CAPABILITIES" && $element[type] == "open"){
				return $element[attributes][version];
			}
		}
		throw new Exception("WFS version could not be determined from XML.");
*/
	}

	/**
	 * Creates a WMS object by parsing its capabilities document. 
	 * 
	 * The WMS version is determined by parsing 
	 * the capabilities document up-front.
	 * 
	 * @return Wms
	 * @param $xml String
	 */
	public function createFromXml ($xml) {
		try {
			$version = $this->getVersionFromXml($xml);

			switch ($version) {
				case "1.1.1 or older":
					$factory = new Wms_1_1_1_Factory();
					break;
				default:
					throw new Exception("Unknown WMS version " . $version);
					break;
			}
			return $factory->createFromXml($xml);
		}
		catch (Exception $e) {
			new mb_exception($e);
			return null;
		}
	}

	private function getVersionByWmsId ($id) {
		$sql = "SELECT wms_version FROM wms WHERE wms_id = $1";
		$v = array($id);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);
		$row = db_fetch_array($res);
		if ($row) {
			return $row["wms_version"];
		}
		return null;
	}
	
	private function getFactory ($version) {
		switch ($version) {
			case "1.0.0":
				return new Wms_1_1_1_Factory();
				break;
			case "1.1.0":
				return new Wms_1_1_1_Factory();
				break;
			case "1.1.1":
				return new Wms_1_1_1_Factory();
				break;
			default:
				throw new Exception("Unknown WMS version " . $version);
				break;
		}
		return null;
	}
	
	public function createFromDb ($id, $appId = null) {
		try {
			$version = $this->getVersionByWmsId($id);
			if (!is_null($version)) {

				$factory = $this->getFactory($version);
				if (!is_null($factory)) {
					if (!is_null($appId)) {
						return $factory->createFromDb($id, $appId);
					}
					return $factory->createFromDb($id);
				}
				return null;
			}
		}
		catch (Exception $e) {
			new mb_exception($e);
			return null;
		}
	}
	
	public function createLayerFromDb ($id, $appId = null) {
		$wmsId = intval(wms::getWmsIdByLayerId($id));
		$version = $this->getVersionByWmsId($wmsId);
		if (!is_null($version)) {

			$factory = $this->getFactory($version);
			if (!is_null($factory)) {
				if (!is_null($appId)) {
					return $factory->createLayerFromDb($id, $wmsId, $appId);
				}
				return $factory->createLayerFromDb($id, $wmsId);
			}
			return null;
		}
	}
}
?>