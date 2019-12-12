/**
 * Package: mb_tabs_horizontal
 *
 * Description:
 *
 * Puts existing elements into horizontal tabs, using jQuery UI tabs. 
 * List the elements comma-separated under target, and make sure they 
 * have a title.
 *  
 * Files:
 * - http/plugins/mb_tabs_horizontal.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>',
 * > 'mb_tabs_horizontal',3,1,
 * > 'Puts existing elements into horizontal tabs, using jQuery UI tabs. List the elements comma-separated under target, and make sure they have a title.',
 * > '','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'',
 * > '<ul></ul><div class=''ui-layout-content''></div>','div',
 * > '../plugins/mb_tabs_horizontal.js','','','jq_ui_tabs','');
 * 
 * Help:
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

var $tabs = $(this);

var HorizontalTabsApi = function (o) {
	var that = this;
	
	this.create = function () {
		o.$target.each(function () {
			var $currentTabEntry = $(this);
			var tabId = $tabs.attr("id") + "_" + this.id;
			
			$tabs.find("ul").append(
				"<li><a href='#" + tabId + "'>" + 
				$currentTabEntry.mapbender("currentTitle") + 
				"</a></li>"
			)
			.end()
			.find("div.ui-layout-content")
			.append("<div id='" + tabId + "'/>")
			.find("#" + tabId)
			.append($currentTabEntry);
		});
	
		if (o.$target.size() > 0) {
			$tabs.tabs({
				select: function (event, ui) {
					that.events.selected.trigger({
						ui: ui
					});
				}
			});
		}
	};
	
	this.select = function (id) {
		var index = $.inArray(id, o.target);
		if (index === -1) {
			return;
		}
		$tabs.tabs("select", index);
	};
	
	this.events = {
		"selected" : new Mapbender.Event()
	};
	
	this.create();
};

$tabs.mapbender(new HorizontalTabsApi(options));
