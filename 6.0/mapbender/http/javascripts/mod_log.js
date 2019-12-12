/**
 * Package: Log
 *
 * Description:
 * Logs the accesses to the guis in the database or a logfile
 * The accesses will be written in the mapbender database on table mb_log or 
 * into LOG_DIR/mb_access_$DATE.log.
 * The log mode can be set via the element_var logtype (file or db). 
 * 
 * Files:
 *  - http/javascripts/mod_log.js
 *  - http/php/mod_log.php
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, 
 * > e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, 
 * > e_target, e_requires, e_url) VALUES ('<gui_id>', 'log', 2, 1, 
 * > 'log requests', 'div', '', '', 0, 0, NULL, NULL, NULL, '', '', 'div', 
 * > 'mod_log.js', '', 'mapframe1', '', '');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<gui_id>', 'log', 'logtype', 'file', 
 * > 'http://www.mapbender.org/Log' ,'php_var');
 *
 * Help:
 * http://www.mapbender.org/Log
 *
 * Maintainer:
 * http://www.mapbender.org/User:Melchior_Moos
 * 
 * Parameters:
 * logtype		- {String} log mode, file | db
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
Mapbender.events.init.register(function () {
	eventAfterMapRequest.register(function (options) {
		var parameters = {
			req : options.url,
			time_client : Mapbender.getTimestamp() 
		};
		var notification = new Mapbender.Ajax.Notification({
			method: "logRequest",
			url: "../php/mod_log.php",
			parameters: parameters
		});
		notification.send();
	});
});
