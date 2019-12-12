/**
 * Package: sql
 *
 * Description:
 * Exports the current application as SQL
 * 
 * Files:
 *  - http/plugins/mb_sql.js
 *  - http/plugins/mb_sql_server.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('test_sql','sql',1,1,'','Export to SQL','a','','href=''#''',900,20,NULL ,NULL ,NULL ,'','Export to SQL','a','../plugins/mb_sql.js','','','jq_ui_dialog','');
 *
 * Help:
 * http://www.mapbender.org/<wiki site name>
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

var $sql = $(this);

var SqlApi = function (o) {
	$sql.bind("click", function () {
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_sql_server.php",
			method: "sql",
			parameters: {
				applicationId: Mapbender.application
			},
			callback: function (obj, result, message) {
				if (!result) {
					alert(message);
					return;
				}
				$("<textarea />").attr({
					"title": "SQL of application '" + Mapbender.application + "'"
				}).text(obj.sql).dialog();
			}
		});
		req.send();
	});
};

$sql.mapbender(new SqlApi(options));
