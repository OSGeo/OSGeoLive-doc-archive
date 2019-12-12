/**
 * Package: wmsTimeInterval
 *
 * Description:
 * Find an interval with two datepickers and attach it to getMap requests
 * of WMS in your application
 * 
 * Files:
 *  - http/plugins/mb_wmsTimeInterval.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, 
 * > e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, 
 * > e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, 
 * > e_url) VALUES('<gui_id>','wmsTimeInterval',1,1,'select an interval for WMS-T',
 * > '','div','','',700,40,NULL ,NULL ,2,'','','div',
 * > '../plugins/mb_wmsTimeInterval.js','','','','');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<gui_id>', 'wmsTimeInterval', 'wms', '<wmsId>', 
 * > 'An array in JSON notation, containing the IDs of the WMS that the time parameter is passed to' ,
 * > 'var');
 *
 * Help:
 * http://www.mapbender.org/wmsTimeInterval
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * wms      - a single numerical WMS id (like 407), or 
 * 				an array of numerical WMS in JSON notation (like [407,912])
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
else {
	// invalid configuration
	options.wms = [];
}

var $datepickerFrom, $datepickerTo;

var getDate = function ($datepicker) {
	var dateString = $.datepicker.formatDate('yy-mm-dd', 
		$datepicker.datepicker("getDate")
	);
	var dateArray = dateString.split("-");
	var h = $("#" + $datepicker.attr("id").replace(/_datepicker/, "_h")).val();
	var m = $("#" + $datepicker.attr("id").replace(/_datepicker/, "_m")).val();
	
	var date = new Date();
	date.setYear(parseInt(dateArray[0], 10));
	date.setMonth(parseInt(dateArray[1], 10) - 1);
	date.setDate(parseInt(dateArray[2], 10));
	date.setHours(parseInt(h, 10));
	date.setMinutes(parseInt(m, 10));
	date.setSeconds(0);
	date.setMilliseconds(0);
	return date;
	
};

var formatDate = function (date) {
	var mon = date.getMonth();
	var day = date.getDate();
	var hours = date.getHours();
	var min = date.getMinutes();

	return date.getFullYear() + "-" + (mon<10?"0"+mon:mon) + "-" + 
		(day<10?"0"+day:day) + "T" + (hours<10?"0"+hours:hours) + ":" + 
		(min<10?"0"+min:min) + ":00Z";
};

Mapbender.events.init.register(function () {

	var createDialogHtml = function (prefix) {
		var html = "<form><fieldset>";
		html += "<div id='" + prefix + "_time'><select id='" + prefix + "_h'>";
		for (var i = 0; i < 24; i++) {
			var val = (i<10?"0":"") + i;
			html += "<option value='" + val + "'>" + val + "</option>";
		}
		html += "</select>";
		html += " : <select id='" + prefix + "_m'>";
		for (var i = 0; i < 6; i++) {
			var val = (i*10<10?"0":"") + i*10;
			html += "<option value='" + val + "'>" + val + "</option>";
		}
		html += "</select></div>";
		html += "<div id='" + prefix + "_datepicker'></div>";
		html += "</fieldset></form>";
		return "<div id='" + prefix + "_select' title='Select date'>" + 
			"<style> fieldset label { display: block; }</style>" + 
			html + "</div>";
	};
	
	var isEarlier = function (date1, date2) {
		if (date1.getTime() < date2.getTime()) {
			return true;
		}
		return false;
	};
	
	var updateInput = function ($input, $datepicker) {
		var date = getDate($datepicker);
		var result = $datepicker.data("dateChanged").trigger({date: date});
		if (result) {
			$input.val(formatDate(date));
			Mapbender.modules[options.id].updated.trigger({
				time: formatDate(getDate($datepickerFrom)) + "/" + 
					formatDate(getDate($datepickerTo))
			});
			return true;
		}
		return false;
	};
	
	var initDialog = function ($dialog, $datepicker, $input) {
		$dialog.dialog({
			bgiframe: true,
			autoOpen: false,
			height: 320,
			width: 300,
			modal: true,
			close: function () {
				$datepicker.datepicker("setDate", $datepicker.data("currentDate"));
			},
			buttons: {
				"Continue": function(){
					if (updateInput($input, $datepicker)) {
						$datepicker.data("currentDate", getDate($datepicker));
						$(this).dialog('close');
						return;
					}
					alert("invalid date selected.");
				},
				"Cancel": function(){
					$datepicker.datepicker("setDate", $datepicker.data("currentDate"));
					$(this).dialog('close');
				}
			}
		});
	};
	
	//
	// initialize input fields (this is where the timestamp is)
	//
	var getInputHtmlCode = function (input) {
		return "<input readonly='readonly' id='" + options.id + "_" + 
			input + "' type='text'></input>";
	};

	var $inputFrom = $(getInputHtmlCode("from"));
	var $inputTo = $(getInputHtmlCode("to"));
	$this.append($inputFrom).append($inputTo);
	
	//
	// initialize datepicker 
	//	
	var $dialogFrom = $(createDialogHtml($inputFrom.attr("id")));
	var $dialogTo = $(createDialogHtml($inputTo.attr("id")));
	$("body").append($dialogFrom).append($dialogTo);

	$datepickerFrom = $("#" + $inputFrom.attr("id") + "_datepicker");
	$datepickerFrom.datepicker({
		dateFormat: 'yy-mm-dd',
		defaultDate: "-1d"
	});
	$datepickerTo = $("#" + $inputTo.attr("id") + "_datepicker");
	$datepickerTo.datepicker({
		dateFormat: 'yy-mm-dd'
	});
	
	//
	// bind events to datepickers
	//
	$datepickerFrom.data("dateChanged", new Mapbender.Event());
	$datepickerFrom.data("dateChanged").register(function () {
		// from must be earlier than to
		if (!isEarlier(getDate($datepickerFrom), getDate($datepickerTo))) {
			return false;
		}
		// adjust date for to
		$datepickerTo.datepicker('option', 'minDate', getDate($datepickerFrom));
		return true;
	});
	
	$datepickerTo.data("dateChanged", new Mapbender.Event());
	$datepickerTo.data("dateChanged").register(function () {
		// from must be earlier than to
		if (!isEarlier(getDate($datepickerFrom), getDate($datepickerTo))) {
			return false;
		}
		// adjust date for to
		$datepickerFrom.datepicker('option', 'maxDate', getDate($datepickerTo));
		return true;
	});

	//
	// initialize dialog
	//
	initDialog($dialogFrom, $datepickerFrom, $inputFrom);
	initDialog($dialogTo, $datepickerTo, $inputTo);
	
	//
	// set defaults in input fields
	//
	updateInput($inputFrom, $datepickerFrom);
	updateInput($inputTo, $datepickerTo);
	
	//
	// add click behaviour to button
	//
	$inputFrom.click(function () {
		$datepickerFrom.data("currentDate", getDate($datepickerFrom));
		$dialogFrom.dialog('open');
	});
	$inputTo.click(function () {
		$datepickerTo.data("currentDate", getDate($datepickerTo));
		$dialogTo.dialog('open');
	});
});

//
// create an API function that is called by the add vendor specific code
//
var Api = function (options) {
	this.addTimeToWms = function (currentWms, functionName) {
		if (functionName !== "setMapRequest" &&
			functionName !== "setSingleMapRequest") {
			return "";
		}
		if (typeof $datepickerFrom === "undefined" || 
			typeof $datepickerTo === "undefined") {
			return "";
		}
		for (var i = 0; i < options.wms; i++) {
			if (options.wms[i] === parseInt(currentWms.wms_id, 10)) {
				return "TIME=" + 
					formatDate(getDate($datepickerFrom)) + "/" + 
					formatDate(getDate($datepickerTo));
			}
		}
		return "";
	};
	this.updated = new Mapbender.Event();
};

Mapbender.modules[options.id] = $.extend(new Api(options), Mapbender.modules[options.id]);

//
// register the API function with add vendor specific
//
mb_registerVendorSpecific("Mapbender.modules." + options.id + ".addTimeToWms(currentWms, functionName);");
