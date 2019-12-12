/**
 * Package: zoomBar
 *
 * Description:
 * This module adds a slider to your application which can be used for navigation. The slider provides defined zoom level which can be individually 
 * configured by the element variable level. A second element variable defaultLevel defines the level which shall be takken when the applicatio-n starts.
 * 
 * Files:
 *  - /http/plugins/mb_zoomBar.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, 
 * > e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES
 * > ('template_basic_zoomBar','mapframe1_zoomBar',1,1,'Finally, a stupid zoom bar','Zoom to scale','div','','',65,80,NULL ,NULL ,100,'','','div',
 * > '../plugins/mb_zoomBar.js','','mapframe1','mapframe1, jq_ui_slider','');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES
 * > ('template_basic_zoomBar', 'mapframe1_zoomBar', 'level', '[2500,5000,10000,50000,100000,500000,1000000,10000000,50000000]', 
 * > 'define an array of levels for the slider (element_var has to be defined)' ,'var');
 * 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES
 * > ('template_basic_zoomBar', 'mapframe1_zoomBar', 'defaultLevel', '4', 'define the default level for application start' ,'var');
 * >
 * >
 * Help:
 * http://www.mapbender.org/zoomBar
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * level    	     - define an array of levels for the slider (parameter has to be defined)
 * defaultLevel      -  define the default level for application start, optional, if not defined the last level is taken
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

// Must define levels as array, like [250, 1000, 5000, 10000, 100000]
if (typeof options.level !== "object" || !options.level.length) {
	new Mapbender.Exception("No levels defined for zoomBar.");
	return;
}

// Must define defaultLevel, index in array level
if (typeof options.defaultLevel !== "number" 
	|| options.defaultLevel < 0 
	|| options.defaultLevel >= options.level.length) 
{
	options.defaultLevel = options.level.length - 1;
}

var $zoomBar = $(this);

var ZoomBarApi = function () {
	var that = this;
	var skipMapRequest = false;

	this.events = {
		updated: new Mapbender.Event(),
		slided: new Mapbender.Event()
	};
	
	var findClosestScaleIndex = function (someScale) {
		var len = options.level.length;
		if (someScale < options.level[0]) {
			return options.level.length - 1;
		}
		if (someScale >= options.level[len - 1]) {
			return 0;
		}
		for (var i = 0; i < len-1; i++) {
			if (someScale >= options.level[i] && someScale < options.level[i+1]) {
				return options.level.length - 1 - i;
			}
		}
		return 0;
	};
	
	// repaint with current scale after the slider has been moved
	this.events.updated.register(function (obj) {
		$zoomBar.attr("title", "1:" + obj.scale);
		options.$target.mapbender(function () {
			this.repaintScale(null, null, obj.scale);
		});
	});
	
	// create slider
	$zoomBar.slider({
		orientation: "vertical",
		min: 0,
		max: options.level.length-1,
		step: 1,
		value: options.defaultLevel,
		change: function (event, ui) {
			var currentScale = options.$target.mapbender().getScale();
			var scale = options.level[options.level.length - ui.value-1]; 
			$zoomBar.attr("title", "1:" + scale);
			
			if (skipMapRequest) {
				return;
			}
			that.events.updated.trigger({
				scale: scale
			});
			return false;
		},
		slide: function (event, ui) {
			var scale = options.level[options.level.length-1-ui.value];
			that.events.slided.trigger({
				scale: scale
			});
		}
	});

	// Set default scale on afterInit
	Mapbender.events.afterInit.register(function () {
		$zoomBar.appendTo(options.$target);
		
		var initialScale = options.level[options.level.length-1-options.defaultLevel];
		var closestScaleIndex = findClosestScaleIndex(initialScale);
		$zoomBar.slider("value", closestScaleIndex);

		options.$target.mapbender().events.afterMapRequest.register(function () {
			var currentScale = options.$target.mapbender().getScale();
			var closestScaleIndex = findClosestScaleIndex(currentScale);
			skipMapRequest = true;
			$zoomBar.slider("value", closestScaleIndex);
			skipMapRequest = false;
		});

	});
	
};

$zoomBar.mapbender(new ZoomBarApi());
