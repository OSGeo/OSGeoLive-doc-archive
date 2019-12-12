<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__)."/../core/globalSettings.php";

/**
 * Normalizes the input data specified at http://www.mapbender.org/GET-Parameter
 */
class GetApi {
	private $layers = array();
	private $featuretypes = array();
	private $geoRSSFeeds = array();
	private $wmc = array();
	
	/**
	 * @param array $input
	 */
	public function __construct ($input) {
		if (!is_array($input)) {
			return null;
		}
		foreach ($input as $key => $value) {
			switch ($key) {
				case "WMC":
					$this->wmc = $this->normalizeWmcInput($value);
					break;
				case "LAYER":
					$this->layers = $this->normalizeLayerInput($value);
					break;
				case "FEATURETYPE":
					$this->featuretypes = $this->normalizeFeaturetypeInput($value);
					break;
				case "GEORSS":
					$this->geoRSSFeeds = $this->normalizeGeoRSSInput($value);
					break;
			}
		}
	}
	
	/**
	 * Returns an array of layer metadata
	 * @return array
	 */
	public function getLayers () {
		return $this->layers;	
	}

	/**
	 * Returns an array of featuretype metadata
	 * @return array
	 */
	public function getFeaturetypes () {
		return $this->featuretypes;
	}

	/*
	 * 
	 *
	 */
	public function getGeoRSSFeeds(){
		return $this->geoRSSFeeds;

	}

	/**
	 * Returns an array of wmc
	 * @return array
	 */
	public function getWmc () {
		return $this->wmc;
	}

	// for possible inputs see http://www.mapbender.org/GET-Parameter#WMC
	private function normalizeWmcInput ($input) {
		// assume WMC=12,13,14
		$inputArray = explode(",", $input);
		$input = array();
		$i = 0;
		foreach ($inputArray as $id) {
			if (is_numeric($id)) {
				$input[$i++]["id"] = $id;
			}
		}

// check if each layer has at least an id, if not, delete
		$i = 0;
		while ($i < count($input)) {
			if (!is_array($input[$i]) || !isset($input[$i]["id"]) || !is_numeric($input[$i]["id"])) {
				array_splice($input, $i, 1);
				continue;
			}
			$input[$i]["id"] = intval($input[$i]["id"]);
			$i++;
		}
		return $input;
	}

	// for possible inputs see http://www.mapbender.org/GET-Parameter#LAYER
	// for test cases, see http://www.mapbender.org/Talk:GET-Parameter#LAYER
	private function normalizeLayerInput ($input) {
		if (is_array($input)) {
			$keys = array_keys($input);
			$isSingleLayer = false;
			foreach ($keys as $key) {
				if (!is_numeric($key)) {
					$isSingleLayer = true;
					break;
				}
			}
			// LAYER[id]=12&LAYER[application]=something
			if ($isSingleLayer) {
				$input[0] = array();
				foreach ($keys as $key) {
					if (!is_numeric($key)) {
						$input[0][$key] = $input[$key];
						unset($input[$key]);
					}
				}
			}
			else {
				for ($i = 0; $i < count($input); $i++) {
					// assume LAYER[]=12&LAYER[]=13
					if (is_numeric($input[$i])) {
						$id = $input[$i];
						$input[$i] = array("id" => $id);
					}
					// else assume LAYER[0][id]=12&LAYER[0][application]=something
				}
			}
		}
		else {
			// assume LAYER=12,13,14
			$inputArray = explode(",", $input);
			$input = array();
			$i = 0;
			foreach ($inputArray as $id) {
				if (is_numeric($id)) {
					$input[$i++]["id"] = $id;
				}
			}
		}
		// check if each layer has at least an id, if not, delete
		$i = 0;
		while ($i < count($input)) {
			if (!is_array($input[$i]) || !isset($input[$i]["id"]) || !is_numeric($input[$i]["id"])) {
				array_splice($input, $i, 1);
				continue;
			}
			$input[$i]["id"] = intval($input[$i]["id"]);
			$i++;
		}
		return $input;
	}

	private function normalizeFeaturetypeInput ($input) {
		if (is_array($input)) {
			$keys = array_keys($input);
			$isSingleFeaturetype = false;
			foreach ($keys as $key) {
				if (!is_numeric($key)) {
					$isSingleFeaturetype = true;
					break;
				}
			}
			// FEATURETYPE[id]=12&FEATURETYPE[active]=something
			if ($isSingleFeaturetype) {
				$input[0] = array();
				foreach ($keys as $key) {
					if (!is_numeric($key)) {
						$input[0][$key] = $input[$key];
						unset($input[$key]);
					}
				}
			}
			else {
				for ($i = 0; $i < count($input); $i++) {
					// assume FEATURETYPE[]=12&FEATURETYPE[]=13
					if (is_numeric($input[$i])) {
						$id = $input[$i];
						$input[$i] = array("id" => $id);
					}
				}
			}
		}
		else {
			// assume FEATURETYPE=12,13,14
			$inputArray = explode(",", $input);
			$input = array();
			$i = 0;
			foreach ($inputArray as $id) {
				if (is_numeric($id)) {
					$input[$i++]["id"] = $id;
				}
			}
		}
		// check if each featuretype has at least an id, if not, delete
		$i = 0;
		while ($i < count($input)) {
			if (!is_array($input[$i]) || !isset($input[$i]["id"]) || !is_numeric($input[$i]["id"])) {
				array_splice($input, $i, 1);
				continue;
			}
			$input[$i]["id"] = intval($input[$i]["id"]);
			$i++;
		}
		return $input;
	}

	private function normalizeGeoRSSInput($input){
		return is_array($input) ? $input : array($input);
	}
}

?>
