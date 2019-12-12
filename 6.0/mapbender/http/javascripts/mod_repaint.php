/**
 * Package: repaint
 *
 * Description:
 * Repaints a map. The maps must be specified comma-seperated under target.
 * 
 * Files:
 *  - http/javascripts/mod_repaint.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<gui_id>','repaint',2,1,
 * > 'refresh a map object','Repaint',
 * > 'img','../img/button_blink_red/repaint_off.png','',360,60,24,24,1,'','',
 * > '','mod_repaint.js','','mapframe1','','');
 *
 * Help:
 * http://www.mapbender.org/Repaint
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

$(this).click(function () {
	if (!options.target) {
		return;
	}
	if (!Mapbender.modules[options.target]) {
		return;
	}
	Mapbender.modules[options.target].zoom(true, 0.999);
}).mouseover(function () {
	if (options.src) {
		this.src = options.src.replace(/_off/, "_over");
	}
}).mouseout(function () {
	if (options.src) {
		this.src = options.src;
	}
});
