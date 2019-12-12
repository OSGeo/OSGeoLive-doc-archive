/**
 * Package: toggleModule
 *
 * Description:
 * activate a module after the application has loaded (started).
 * Just define the "id" of the module you like to trigger under the target field.
 *
 * Files:
 *  - http/javascripts/mod_toggleModule.php
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<gui_id>','toggleModule',1,1,'',
 * > '','div','','',1,1,1,1,2,'','','div','mod_toggleModule.php','','pan1','','');
 *
 * Help:
 * http://www.mapbender.org/ToggleModule
 *
 * Maintainer:
 * http://www.mapbender.org/User:Marc_Manns
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
 
function mod_toggleModule_init(){
	$("#" + options.target).trigger('click');
}

Mapbender.events.afterInit.register(function (){
	mod_toggleModule_init();
});