/**
 * Package: ol_mousePosition
 *
 * Description:
 * An OpenLayers MousePosition
 * 
 * Files:
 *  - http/plugins/ol_mousePosition.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>',
 * > 'ol_mousePosition',1,1,'An OpenLayers MousePosition',
 * > 'OpenLayers MousePosition','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,
 * > '','','div','../plugins/ol_mousePosition.js','','ol','',
 * > 'http://www.mapbender.org/ol_mousePosition');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES ('<appId>', 'body', 
 * > 'ol_mousePosition', '.olControlMousePosition
 * > {
 * > 	background-color:white; 
 * >	width:220px;
 * >	height:15px;
 * >	border:solid gray 1px;
 * >	border-bottom:none;
 * >	left:0px;
 * >	bottom:2px;
 * >	padding-left:8px;
 * > }', '' ,'text/css');
 *
 * Help:
 * http://www.mapbender.org/ol_mousePosition
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
	ol_map.addControl(new OpenLayers.Control.MousePosition());	
});

