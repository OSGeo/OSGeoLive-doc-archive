/**
 * Package: jq_jgrowl
 *
 * Description:
 * The container for the jQuery plugin jGrowl
 * 
 * Files:
 *  - http/javascripts/jq_ui_effects.js
 *  - http/extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.effects.*
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','jq_jgrowl',1,1,'',
 * > '','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','jq_jgrowl.js',
 * > '../extensions/jGrowl-1.2.4/jquery.jgrowl_minimized.js','','',
 * > 'http://stanlemon.net/projects/jgrowl.html');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'body', 'jq_jgrowl', 
 * > '../extensions/jGrowl-1.2.4/jquery.jgrowl.css', '' ,'file/css');
 *
 * Help:
 * http://www.mapbender.org/jq_jgrowl
 * http://stanlemon.net/projects/jgrowl.html
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

Mapbender.events.init.register(function () {
	$.fn.jGrowl.prototype.defaults.position = "bottom-right";
});

function alert (msg) {
	$.jGrowl(msg, {
		glue: "after"
	});
}
