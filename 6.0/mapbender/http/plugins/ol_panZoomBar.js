/**
 * Package: ol_panZoomBar
 *
 * Description:
 * An OpenLayers panZoomBar
 * 
 * Files:
 *  - http/plugins/ol_panZoomBar.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>',
 * > 'ol_panZoomBar',1,0,'An OpenLayers PanZoomBar','OpenLayers Layer Switch',
 * > 'div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div',
 * > '../plugins/ol_panZoomBar.js','','ol','',
 * > 'http://www.mapbender.org/ol_panZoomBar');

 *
 * Help:
 * http://www.mapbender.org/ol_panZoomBar
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
var ol_map = Mapbender.modules[options.target[0]];
ol_map.mapbenderEvents.layersAdded.register(function () {
	ol_map.addControl(new OpenLayers.Control.PanZoomBar());	
});
