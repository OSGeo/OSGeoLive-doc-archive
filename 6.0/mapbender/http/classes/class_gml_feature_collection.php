<?php
# $Id: class_gml2.php 3099 2008-10-02 15:29:23Z nimix $
# http://www.mapbender.org/index.php/class_gml2.php
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
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_gml_feature.php");
require_once(dirname(__FILE__)."/../classes/class_bbox.php");


class FeatureCollection {
	var $type = "FeatureCollection";
	var $featureArray = array();
	
	public function __construct() {
		
	}
	
	public function addFeature ($aFeature) {
		array_push($this->featureArray, $aFeature);
	}
	
	public function toGeoJSON () {
		$str = "";
		$str .= "{\"type\": \"FeatureCollection\", \"features\": [";

		$len = count($this->featureArray); 
		if ($len > 0) {
			for ($i=0; $i < $len; $i++) {
				if ($i > 0) {
					$str .= ",";
				}	
				$str .= $this->featureArray[$i]->toGeoJSON();
			}
		}

		$str .= "]}";
		return $str;
	}
	
	/**
	 * 
	 * 
	 * 
	 * 
	 *
	 */
	public function getBbox () {
		if (!is_array($this->featureArray) || count($this->featureArray) === 0) {
			return null;
		}
		$bBoxArray = array();
		for ($i = 0; $i < count($this->featureArray);$i++) {
			$currentBbox = $this->featureArray[$i]->getBbox();
			if(!is_null($currentBbox)) {
				$bBoxArray[] = $currentBbox;
			}
		}
		return Mapbender_bbox::union($bBoxArray);
	}
	
	public function getFeatureMemberCount () {
		return count($this->featureArray);
	}
}
?>