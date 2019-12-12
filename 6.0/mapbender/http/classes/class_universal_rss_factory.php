<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_rss_factory.php";
require_once dirname(__FILE__) . "/../classes/class_georss_factory.php";

class UniversalRssFactory {

	public function createFromUrl ($url) {
		$dom = new DOMDocument();
		$dom->preserveWhitespace = false;
		$success = $dom->load($url);
		if (!$success) {
			new mb_exception(__FILE__ . ": createFromUrl(): Could not load " . $url);
			return null;
		}

		$nodeList = $dom->getElementsByTagName("rss");
		if ($nodeList->length > 0) {
			$node = $nodeList->item(0);
			if ($node->hasAttribute("xmlns:georss")) {
				$geoRssFactory = new GeoRssFactory();
				return $geoRssFactory->createFromUrl($url);
			}
		}
		$rssFactory = new RssFactory();
		return $rssFactory->createFromUrl($url);
	}
}

?>