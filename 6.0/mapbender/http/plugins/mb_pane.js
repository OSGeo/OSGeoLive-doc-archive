/**
 * Package: <Application element name>
 *
 * Description:
 * <A description>
 * 
 * Files:
 *  - <path and filename, like http/javascripts/mod_zoomIn1.php>
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','pane',1,1,'','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'height:100%;width:100%','','div','../plugins/mb_pane.js','','','jq_layout','http://layout.jquery-dev.net');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<appId>', 'pane', 'north', 'id of target element', '' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<appId>', 'pane', 'south', 'id of target element', '' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<appId>', 'pane', 'east', 'id of target element', '' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<appId>', 'pane', 'west', 'id of target element', '' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<appId>', 'pane', 'center', 'id of target element', '' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<appId>', 'pane', 'layoutOptions', '{
	applyDefaultStyles: true
}', '' ,'var');

 *
 * Help:
 * http://www.mapbender.org/<wiki site name>
 *
 * Maintainer:
 * http://www.mapbender.org/User:<user>
 * 
 * Parameters:
 * <normal element var name>      - <type and description>
 * <optional element var name>    - *[optional]* <type and description>
 * <deprecated element var name>  - *[deprecated]* <type and description>
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var $pane = $(this);

var PaneApi = function (o) {
	var that = this;
	
	this.events = {
		resize: new Mapbender.Event()
	};
	
	var paneOptions = {};
	var paneTypes = ["center", "west", "east", "north", "south"];
	
	for (var i in paneTypes) {
		var type = paneTypes[i];
		if (o[type]) {
			var $currentPane = $("#" + o[type]);
			if (!$currentPane.is("div") && !$currentPane.is("iframe")) {
				$currentPane = $currentPane.wrap("<div></div>").parent();
			}
			$currentPane.appendTo($pane).addClass(o.id + "-" + type).addClass("pane");
		}
	}

	var resizeSubPanes = function (name, element, state, options, layoutName) {
		try {
			var w = element.innerWidth()-options.spacing_open;
			var h = element.innerHeight()-options.spacing_open;
			element.mapbender("setDimensions", w, h);
		}
		catch (e) {
			new Mapbender.Notice("Mapframe1 not present.");
		}
		
		try {
			element.mapbender("updateSize", element);
		}
		catch (e) {
			new Mapbender.Notice("OpenLayers not present.");
		}
		that.events.resize.trigger();
	};
	
	var myOptions = $.extend({
		applyDefaultStyles: false,
		center__paneSelector:	"." + o.id + "-center",	
		west__paneSelector:		"." + o.id + "-west",
		east__paneSelector:		"." + o.id + "-east",
		north__paneSelector:		"." + o.id + "-north",
		south__paneSelector:		"." + o.id + "-south",
		center__onresize: resizeSubPanes,
		north__onresize: resizeSubPanes,
		south__onresize: resizeSubPanes,
		east__onresize: resizeSubPanes,
		west__onresize: resizeSubPanes
//		onclose: resizeSubPanes,
//		onshow: resizeSubPanes,
//		onhide: resizeSubPanes,
	}, o.layoutOptions);
	var layout = $pane.layout(myOptions);
	layout.resizeAll();
	
	this.resizeAll = function () {
		new Mapbender.Warning("Resizing panes of " + o.id);
		layout.resizeAll();
	};
	
	Mapbender.events.afterInit.register(function () {
		that.resizeAll();
	});
};

$pane.mapbender(new PaneApi(options));
