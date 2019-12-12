/**
 * Package: i18n
 *
 * Description:
 * Internationalization module, collects data from all elements
 * and sends them to the server in a single POST request.
 * The strings are translated via gettext only.
 * 
 * Files:
 *  - http/plugins/mb_i18n.js
 *  - http/plugins/mb_i18n_server.php
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('gui','i18n',1,1,
 * > 'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.',
 * > 'Internationalization','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','',
 * > 'div','../plugins/mb_i18n.js','','','',
 * > 'http://www.mapbender.org/Gettext');
 *
 * Help:
 * http://www.mapbender.org/Gettext
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


var I18n = function () {
	var translationObj = {};
	
	this.queue = function (elementId, obj, callback) {
		var t = translationObj[elementId] = {};
		t.data = obj;
		t.callback = callback;
	};
	this.localize = function (locale) {
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_i18n_server.php",
			method: "translate",
			parameters: {
				locale: locale,
				data: translationObj
			},
			callback: function (obj, result, message) {
				if (!result) {
					new Mb_exception(message);
					return;
				}
				Mapbender.locale = obj.locale;
				for (var serverId in obj.data) {
					// Processing translation for each element
					new Mb_notice(
						"Processing translation of " + serverId + "..."
					);
					for (var clientId in translationObj) {
						if (clientId !== serverId) {
							continue;
						}
						var t = translationObj[clientId];
						if (typeof t.callback === "function") {
							t.callback(obj.data[serverId].data);
							new Mb_notice(
								"Processing translation of " + serverId + "...done"
							);
							break;
						}
					}
				}
				translationObj = {};
				//Mapbender.events.localize.trigger();
			}
		});
		req.send();
	};
};

Mapbender.modules[options.id] = $.extend(new I18n(options), Mapbender.modules[options.id]);
Mapbender.events.init.register(function () {
	Mapbender.modules.i18n.localize(Mapbender.languageId);
});
