<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_rss_factory.php";
require_once dirname(__FILE__) . "/../classes/class_georss.php";

class GeoRssFactory extends RssFactory {

	protected function createRss () {
		$rss = new GeoRss();
		return $rss;
	}

	protected function createRssItem () {
		$rssItem = new GeoRssItem();
		return $rssItem;
	}
	
	protected function parseItem ($node, $item) {
		
		$item = parent::parseItem($node, $item);
		
		foreach ($node->childNodes as $childNode) {
			switch ($childNode->tagName) {
				case "georss:box":
					$coordinateString = trim($childNode->nodeValue);
					$coordinateArray = explode(" ", $coordinateString);
					
					if (count($coordinateArray) === 4) {
						$bbox = new Mapbender_bbox(
							floatval($coordinateArray[0]),
							floatval($coordinateArray[1]),
							floatval($coordinateArray[2]),
							floatval($coordinateArray[3]),
							"EPSG:4326"
						);
						$item->setBbox($bbox);
					}
					break;
			}
		}
		return $item;
	}
}

?>