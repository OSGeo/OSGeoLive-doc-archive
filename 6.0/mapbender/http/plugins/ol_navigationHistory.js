/**
 * Package: ol_navigation
 *
 * Description:
 * An OpenLayers NavigationHistory Control
 * 
 * Files:
 *  - http/plugins/ol_navigationHistory.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>',
 * > 'ol_layerSwitch',1,1,'An OpenLayers NavigationHistory Control',
 * > 'OpenLayers NavigationHistory','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'',
 * > '','div','../plugins/ol_navigationHistory.js','','ol','',
 * > 'http://www.mapbender.org/ol_navigationHistory');
 *
 * Help:
 * http://www.mapbender.org/ol_navigationHistory
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
 
var $navigationHistory = $(this);

var ol_map = Mapbender.modules[options.target[0]];
ol_map.mapbenderEvents.layersAdded.register(function () {
	var nav = new OpenLayers.Control.NavigationHistory({
		div: $navigationHistory.get(0)
	});
	ol_map.addControl(nav);
	
	Mapbender.events.afterInit.register(function () {
		nav.clear();
	});
	$navigationHistory.mapbender({
		control: nav,
		buttons: [nav.next, nav.previous] 
	});
});

