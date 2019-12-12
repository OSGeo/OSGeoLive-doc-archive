<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_rss.php");
require_once(dirname(__FILE__)."/../classes/class_georss_item.php");

/**
 * Creates an RSS Feed.
 */
class GeoRss extends Rss {
	
	protected function createRssItem () { 
		return new GeoRssItem();
	}
	
	protected function getNamespaceString () {
		return parent::getNamespaceString() . 
			' xmlns:georss="http://www.georss.org/georss"';
	}	
}
?>
