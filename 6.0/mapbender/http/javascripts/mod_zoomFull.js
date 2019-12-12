/**
 * Package: zoomFull
 *
 * Description:
 * Click button to zoom to the full extent of the BoundingBox.
 *
 * Files:
 *  - http/javascripts/mod_zoomFull.php
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment,
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width,
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file,
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','zoomFull',2,1,
 * > 'zoom to full extent button','Display complete map','img',
 * > '../img/button_blink_red/zoomFull_off.png','',335,60,24,24,2,'','','',
 * > 'mod_zoomFull.php','','mapframe1','',
 * >'http://www.mapbender.org/index.php/ZoomFull');
 *
 * Help:
 * http://www.mapbender.org/ZoomFull
 *
 * Maintainer:
 * http://www.mapbender.org/User:Verena_Diewald
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
	Mapbender.modules[options.target].zoomFull();
}).mouseover(function () {
	if (options.src) {
		this.src = options.src.replace(/_off/, "_over");
	}
}).mouseout(function () {
	if (options.src) {
		this.src = options.src;
	}
});