/**
 * Package: controlDigitizeDialog
 *
 * Description:
 * Allow saving a defined point or line geometry without  
 * inserting attribute data. Attribute window is not shown then. 
 * Attribute window can be opened checking the checkbox for attr data. 
 * 
 * Files:
 *  - http/plugins/mb_controlDigitizeDialog.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<app_id>',
 * > 'controlDigitizeDialog',4,1,'this module controls the digitize dialog window',
 * > '','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div',
 * >'../plugins/mb_controlDigitizeDialog.js','','digitize','','');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<app_id>', 'controlDigitizeDialog', 
 * > 'digitizeWfsConfIdLine', '3', 
 * > 'wfs conf id for saving lines without attribute data' ,'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<app_id>', 'controlDigitizeDialog', 
 * > 'digitizeWfsConfIdPoint', '4', 
 * > 'wfs conf id for saving points without attribute data' ,'var');
 * 
 *
 * Help:
 * http://www.mapbender.org/ControlDigitizeDialog.js
 *
 * Maintainer:
 * http://www.mapbender.org/User:Verena_Diewald
 * 
 * Parameters:
 * digitizeWfsConfIdLine     - wfs conf id for saving lines without attribute data
 * digitizeWfsConfIdPoint    - wfs conf id for saving points without attribute data
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
var getJsWfsConfIdByDbWfsConfId = function (wfsConf, id) {
	for (var i = 0; i < wfsConf.length; i++) {
		if (parseInt(wfsConf[i].wfs_conf_id, 10) === id) {
			return i;
		}
	}
	new Mapbender.Notice("No valid WFS conf found! Unable to link feature to WFS conf.");
	return null;
};

var checkIfSaveWithAttrDataIsSet = function () {
	var isChecked = window.frames.digitize.document.getElementById("attrDataCheck").checked;
	
	if (isChecked) {
		return true;
	}
	return false;
};

var saveGeometryInDb = function(obj) {
	var currentFeatureId = obj.feature.e.getElementValueByName("fid");
	var featureGIndex = -1;
	
	var method = "insert";
	if (obj.feature !== null && currentFeatureId !== false && currentFeatureId !== undefined) {
		method = "update";
		featureGIndex = obj.geometryIndex; 
		obj.feature.e.setElement("fid", currentFeatureId);
	}
	
	if(typeof options.digitizeWfsConfIdLine === "undefined" || 
			options.digitizeWfsConfIdLine == "" ||
			typeof options.digitizeWfsConfIdPoint === "undefined" || 
			options.digitizeWfsConfIdPoint == "") {
		return;
	}
	
	if(obj.feature.geomType == "line") {
		var digitizeWfsConfId = options.digitizeWfsConfIdLine;
	}
	else {
		var digitizeWfsConfId = options.digitizeWfsConfIdPoint;
		
	}
	
	var wfsConf = get_complete_wfs_conf();
	
	obj.feature.wfs_conf = getJsWfsConfIdByDbWfsConfId(wfsConf, digitizeWfsConfId);
	
	window.frames.digitize.dbGeom(method, featureGIndex, function () {});
};

Mapbender.events.init.register(function () {
	var attachEventsToDigitize = function () {
		
		var attrDataDiv = document.createElement("div");
		attrDataDiv.id = "attrDataDiv";
		attrDataDiv.name = "attrDataDiv";
		attrDataDiv.innerHTML = "<input type='checkbox' style='position: absolute; top: 30px; left: 0px;' id='attrDataCheck'>" +
								"<label for='attrDataCheck' style='position: absolute; top: 32px; left: 20px;'>mit Sachdaten</label>";
		window.frames.digitize.document.getElementById("digButtons").appendChild(attrDataDiv);
		
		Mapbender.modules.digitize.events.openDialog.register(function (obj) {
			
			var saveWithAttrData = checkIfSaveWithAttrDataIsSet();
			if (saveWithAttrData) {
				return true;
			}
			
			//saveGeometry directly
			var saveGeometry = saveGeometryInDb(obj);
			// false prevents the digitize module from 
			// opening the native dialog
			return false;
		});
	};

	var tryToAttachEventsToDigitize = function () {
		if (typeof Mapbender.modules.digitize.events === "undefined") {
			setTimeout(tryToAttachEventsToDigitize, 3000);
		}
		else {
			attachEventsToDigitize();
		}
	};
	
	tryToAttachEventsToDigitize();
	
});