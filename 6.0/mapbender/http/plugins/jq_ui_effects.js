/**
 * Package: jq_ui_effects
 *
 * Description:
 * A collection of jQuery UI effects
 * 
 * Files:
 *  - http/plugins/jq_ui_effects.js
 *  - http/extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.effects.*
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>','jq_ui_effect',
 * > 1,1,'','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','',
 * > 'jq_ui_effects.php',
 * > '../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.effects.core.js',
 * > '','','');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'blind', 
 * > '0', '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'bounce', 
 * > '0', '1 = effect active' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'clip', 
 * > '0', '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'drop', 
 * > '0', '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'explode', 
 * > '0', '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'fold', 
 * > '0', '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'highlight', 
 * > '0', '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'pulsate', 
 * > '0', '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'scale', 
 * > '0', '1 = effect active' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'shake', 
 * > '0', '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'slide', 
 * >'0', '1 = effect active' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'jq_ui_effect', 'transfer', 
 * > '1', '1 = effect active' ,'php_var');
 *
 * Help:
 * http://jqueryui.com/docs/effect/
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

 return this;