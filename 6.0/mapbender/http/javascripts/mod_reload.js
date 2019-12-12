/**
 * Package: Reload
 * 
 * Description:
 * A button that reloads the whole window. 
 *
 * Files:
 *  - http/javascripts/mod_reload.js
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, 
 * > e_comment, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<gui_id>', 'reload',
 * > 2,1,'reload','img','../img/button_blink_red/reload_off.png', '',335,
 * > 60,24,24,1,'','','','mod_reload.js','','mapframe1','',
 * > 'http://www.mapbender.org/index.php/Reload');
 *
 * Help:
 * http://www.mapbender.org/index.php/Reload
 *
 * Maintainer: 
 * http://www.mapbender.org/User:Vera_Schulze
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
var Reload = function (options) {
	var that = this;
	
	var domElement = $("#" + options.id);
	domElement.src = options.src;
	if (!options.src) {
		new Mb_exception(options.id + " requires a src.");
	}
	else {
		$(domElement).mouseover(function () {
			domElement.src = options.src.replace(/_off/,"_over");
		}).mouseout(function () {
			domElement.src = options.src;
		}).click(function () {
		   that.reload(); 
		});
	};
	
	/**
	 * Method: reload
	 *
	 * Reloads the window.
	 */
	this.reload = function () {
		location.reload();
	};
};

var reload = new Reload(options);