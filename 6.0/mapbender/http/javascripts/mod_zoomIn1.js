/**
 * Package: ZoomIn1
 *
 * Description:
 * Click button, which minimizes the real world bounding box of the visible map section, doubles the scale (halfes the scale number). Image size is not affected. 
 *
 * Files:
 *  - http/javascripts/mod_zoomIn1.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, 
 * > e_comment, e_title, e_element, e_src, e_attributes, e_left, 
 * > e_top, e_width, e_height, e_z_index, e_more_styles, e_content, 
 * > e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
 * > VALUES('gui', 'zoomIn1', 2, 1, 'zoomIn button','Zoom in',
 * > 'img','../img/button_gray/zoomIn2_off.png','',220,10,24,24,1,
 * > '','','','mod_zoomIn1.js','','mapframe1','',
 * > 'http://www.mapbender.org/index.php/ZoomIn');
 *
 * Help:
 * http://www.mapbender.org/ZoomIn
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
	if (!options.target 
		|| !Mapbender.modules[options.target]
		|| typeof Mapbender.modules[options.target].zoom !== "function") {
		return;
	}
	Mapbender.modules[options.target].zoom(true, 2.0);
}).mouseover(function () {
	if (options.src) {
		this.src = options.src.replace(/_off/, "_over");
	}
}).mouseout(function () {
	if (options.src) {
		this.src = options.src;
	}
});
