/**
 * Package: ol_layerSwitch
 *
 * Description:
 * An OpenLayers Layer Switch
 * 
 * Files:
 *  - http/plugins/ol_layerSwitch.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>',
 * > 'ol_layerSwitch',1,1,'An OpenLayers Layer Switch',
 * > 'OpenLayers Layer Switch','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'',
 * > '','div','../plugins/ol_layerSwitch.js','','ol','',
 * > 'http://www.mapbender.org/ol_layerSwitch');
 *
 * Help:
 * http://www.mapbender.org/ol_layerSwitch
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
	ol_map.addControl(new OpenLayers.Control.LayerSwitcher());	
});
