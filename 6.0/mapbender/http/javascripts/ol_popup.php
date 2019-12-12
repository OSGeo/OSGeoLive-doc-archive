/**
 * Package: ol_popup
 *
 * Description:
 * Various OpenLayers popups
 * 
 * Files:
 *  - http/javascripts/ol_popup.php
 *
 * SQL:
 *
 * Help:
 * http://www.mapbender.org/ol_popup
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters (PHP):
 * framed          - set to 1 to activate this effect, 0 to disable
 * framedCloud     - set to 1 to activate this effect, 0 to disable
 * anchored        - set to 1 to activate this effect, 0 to disable
 * anchoredBubble  - set to 1 to activate this effect, 0 to disable
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

<?php
	
	include '../include/dyn_php.php';
	
	if ($anchored || $framed || $framedCloud) {
		include OPENLAYERS_PATH . "lib/OpenLayers/Popup/Anchored.js";
	}
	
	if ($framed || $framedCloud) {
		include OPENLAYERS_PATH . "lib/OpenLayers/Popup/Framed.js";
	}
	
	if ($framedCloud) {
		include OPENLAYERS_PATH . "lib/OpenLayers/Popup/FramedCloud.js";
	}
	
	if ($anchoredBubble) {
		include OPENLAYERS_PATH . "lib/OpenLayers/Popup/AnchoredBubble.js";
	}
	
?>