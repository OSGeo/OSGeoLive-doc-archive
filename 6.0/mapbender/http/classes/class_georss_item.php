<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_georss.php";
require_once dirname(__FILE__) . "/../classes/class_bbox.php";

class GeoRssItem extends RssItem {
	private $bbox;
	
	public function setBbox ($bbox) {
		if (is_a($bbox, "Mapbender_bbox")) {
			$this->bbox = $bbox;		
			return true;
		}
		new mb_exception(__FILE__ . 
			": setBbox(): parameter not a Mapbender_bbox!");
		return false;
	}
	
	public function getBbox () {
		return $this->bbox;
	}
	
	protected function getItemString () {
		$str = parent::getItemString();
		if (is_a($this->bbox, "Mapbender_bbox")) {
			$str .= "<georss:box>" . 
					$this->bbox->min->x . " " . 
					$this->bbox->min->y . " " . 
					$this->bbox->max->x . " " . 
					$this->bbox->max->y . 
	            "</georss:box>\n";
		}
		return $str;
	}
}
?>