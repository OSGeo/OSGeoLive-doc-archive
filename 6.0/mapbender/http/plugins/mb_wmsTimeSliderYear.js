/**
 * Package: wmsTimeSliderYear
 *
 * Description:
 * sets the TIME parameter for all activated WMS (years only)
 * 
 * Files:
 *  - http/plugins/mb_wmsTimeSliderYear.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>',
 * > 'wmsTimeSliderYear',5,1,'sets the TIME parameter for all activated WMS (years only)',
 * > '','div','','',500,700,500,10,NULL ,'','','div',
 * > '../plugins/mb_wmsTimeSliderYear.js','','mapframe1','jq_ui_slider','');
 * >
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','jq_ui_slider',
 * > 5,1,'slider from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,
 * > NULL ,NULL ,'','','','',
 * > '../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.slider.js',
 * > '','jq_ui','');
 * >
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','jq_ui',1,1,
 * > 'The jQuery UI core','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','',
 * > '','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.core.js',
 * > '','','');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'jq_ui', 'css', 
 * > '../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css', 
 * > '' ,'file/css');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'wmsTimeSliderYear', 
 * > 'yearDefault', '2007', 'default slider setting' ,'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'wmsTimeSliderYear', 'yearMax', 
 * > '2007', 'upper end of the time interval' ,'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<appId>', 'wmsTimeSliderYear', 'yearMin', 
 * > '1995', 'lower end of the time interval' ,'var');
 *
 * Help:
 * http://www.mapbender.org/wmsTimeSliderYear
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * yearMin      - lower end of the time interval
 * yearMax      - lower end of the time interval
 * yearDefault  - *[optional]* default slider setting 
 *                (if not set yearMax is used)
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var $this = $(this);

if (typeof options.wms === "object") {
	// default, like [407, 912]
}
else if (typeof options.wms === "number") {
	// just one wms
	options.wms = [options.wms];
}
else if (typeof options.wms === "string") {
	// just one wms
	options.wms = options.wms.split(",");
	for (var i in options.wms) {
		options.wms[i] = parseInt(options.wms[i], 10);
	}
}
else {
	// invalid configuration
	options.wms = [];
}

if (typeof options.yearMin !== "number" || typeof options.yearMax !== "number"
) {
	new Mb_exception("No interval given in " + options.id);
	return;
}

if (typeof options.yearDefault !== "number") {
	options.yearDefault = options.yearMax;
}

var sliderId = options.id + "_slider";
var textId = options.id + "_text";
$this.append("<div id='" + textId + "' /><div id='" + sliderId + "' />");

Mapbender.events.init.register(function () {
	$("#" + sliderId).slider({
		min: options.yearMin,
		max: options.yearMax,
		value: options.yearDefault,
		step: 1,
		change: function (event, ui) {
			Mapbender.modules[options.id].updated.trigger({
				year: ui.value
			});
		},
		slide: function (event, ui) {
			Mapbender.modules[options.id].slided.trigger({
				year: ui.value
			});
		}
	});
	
	Mapbender.modules[options.id].slided.trigger({
		year: options.yearDefault
	});
});


var formatDate = function (date) {
	var mon = date.getMonth();
	var day = date.getDate();
	var hours = date.getHours();
	var min = date.getMinutes();

	return date.getFullYear() + "-" + (mon<10?"0"+mon:mon) + "-" + 
		(day<10?"0"+day:day) + "T" + (hours<10?"0"+hours:hours) + ":" + 
		(min<10?"0"+min:min) + ":00Z";
};

//
// create an API function that is called by the add vendor specific code
//
var Api = function (options) {
	var currentDate = new Date(options.yearDefault, 0, 1);

	this.addTimeToWms = function (currentWms, functionName) {
		if (functionName !== "setMapRequest" &&
			functionName !== "setSingleMapRequest") {
			return "";
		}
		if (currentWms.gui_wms_visible !== 1) {
			return "";
		}
		for (var i = 0; i < options.wms; i++) {
			if (options.wms[i] === parseInt(currentWms.wms_id, 10)) {
				return "TIME=" + formatDate(currentDate);
			}
		}
		// if not set, add TIME parameter to all WMS
		if (options.wms.length === 0) {
			return "TIME=" + formatDate(currentDate);
		}
	};
	this.slided = new Mapbender.Event();
	this.slided.register(function (obj) {
		currentDate = new Date(obj.year, 1, 1);
		$("#" + textId).text(obj.year);
	});
	this.updated = new Mapbender.Event();
	this.updated.register(function (obj) {
		currentDate = new Date(obj.year, 1, 1);
		$("#" + textId).text(obj.year);
		Mapbender.modules[options.target[0]].setMapRequest();
	});
};

Mapbender.modules[options.id] = $.extend(new Api(options), Mapbender.modules[options.id]);

//
// register the API function with add vendor specific
//
mb_registerVendorSpecific("Mapbender.modules." + options.id + ".addTimeToWms(currentWms, functionName);");

