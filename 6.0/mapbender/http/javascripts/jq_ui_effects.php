/**
 * Package: jq_ui_effects
 *
 * Description:
 * A collection of jQuery UI effects
 * 
 * Files:
 *  - http/javascripts/jq_ui_effects.js
 *  - http/extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.effects.*
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','jq_ui_effects',
 * > 1,1,'','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','',
 * > 'jq_ui_effects.php',
 * > '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.effects.core.js',
 * > '','','');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'body', 'jq_ui_effect_transfer', 
 * > '.ui-effects-transfer { z-index:1003; border: 2px dotted gray; } ', 
 * > '' ,'text/css');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'blind', '0', 
 * > '1 = effect active' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'bounce', '0', 
 * > '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'clip', '0', 
 * > '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'drop', '0', 
 * > '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'explode', '0', 
 * > '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'fold', '0', 
 * > '1 = effect active' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'highlight', '0', 
 * > '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'pulsate', '0', 
 * > '1 = effect active' ,'php_var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'scale', '0', 
 * > '1 = effect active' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'shake', '0', 
 * > '1 = effect active' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'slide', '0', 
 * > '1 = effect active' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui_effects', 'transfer', '1', 
 * > '1 = effect active' ,'php_var');
 *
 * Help:
 * http://www.mapbender.org/jq_ui_effects
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters (PHP):
 * blind      - set to 1 to activate this effect, 0 to disable
 * bounce     - set to 1 to activate this effect, 0 to disable
 * clip       - set to 1 to activate this effect, 0 to disable
 * drop       - set to 1 to activate this effect, 0 to disable
 * explode    - set to 1 to activate this effect, 0 to disable
 * fold       - set to 1 to activate this effect, 0 to disable
 * highlight  - set to 1 to activate this effect, 0 to disable
 * pulsate    - set to 1 to activate this effect, 0 to disable
 * scale      - set to 1 to activate this effect, 0 to disable
 * shake      - set to 1 to activate this effect, 0 to disable
 * slide      - set to 1 to activate this effect, 0 to disable
 * transfer   - set to 1 to activate this effect, 0 to disable
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

<?php
	$uiPath = dirname(__FILE__) . '/' . 
		"../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/";

	include '../include/dyn_php.php';
	
	if ($blind) {
		include $uiPath . "jquery.effects.blind.min.js";
	}
	
	if ($bounce) {
		include $uiPath . "jquery.effects.bounce.min.js";
	}
	
	if ($clip) {
		include $uiPath . "jquery.effects.clip.min.js";
	}
	
	if ($drop) {
		include $uiPath . "jquery.effects.drop.min.js";
	}
	
	if ($explode) {
		include $uiPath . "jquery.effects.explode.min.js";
	}
	
	if ($fold) {
		include $uiPath . "jquery.effects.fold.min.js";
	}
	
	if ($highlight) {
		include $uiPath . "jquery.effects.highlight.min.js";
	}
	
	if ($pulsate) {
		include $uiPath . "jquery.effects.pulsate.min.js";
	}
	
	if ($scale) {
		include $uiPath . "jquery.effects.scale.min.js";
	}
	
	if ($shake) {
		include $uiPath . "jquery.effects.shake.min.js";
	}
	
	if ($slide) {
		include $uiPath . "jquery.effects.slide.min.js";
	}
	
	if ($transfer) {
		include $uiPath . "jquery.effects.transfer.min.js";
	}
	
?>