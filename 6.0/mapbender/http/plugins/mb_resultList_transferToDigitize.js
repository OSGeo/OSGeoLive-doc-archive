/**
 * Package: resultList_transferToDigitize
 *
 * Description:
 * A button for a mapbender result list to transfer search results to digitize
 * 
 * Files:
 *  - http/plugins/mb_resultList_transferToDigitize.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, 
 * > e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, 
 * > e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('<app_id>',
 * > 'resultList_transferToDigitize',2,1,'transfer marked search results to digitize','','div','','',NULL,NULL,NULL,NULL,NULL,
 * > '','','','../plugins/mb_resultList_transferToDigitize.js','','resultList','',
 * > 'http://www.mapbender.org/ResultList'); 
 * 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_transferToDigitize', 'digitizeId', 'digitize', 
 * > 'references the ID of the digitize module' ,'var');
 *
 * Help:
 * http://www.mapbender.org/ResultList_transferToDigitize
 *
 * Maintainer:
 * http://www.mapbender.org/User:Verena_Diewald
 * 
 * Parameters:
 * digitizeId - references the ID of the digitize module
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var originalI18nObject = {
	labelButton: "edit"	
};

var translatedI18nObject = cloneObject(originalI18nObject);

Mapbender.events.localize.register(function () {
	Mapbender.modules.i18n.queue(options.id, originalI18nObject, function (translatedObject) {
		if (typeof translatedObject !== "object") {
			return;
		}
		translatedI18nObject = translatedObject;
	});
});

Mapbender.events.init.register(function () {
	var resultList = Mapbender.modules[options.target[0]];
	var editButton = resultList.addGlobalButton({
		title: translatedI18nObject.labelButton, 
		classes: 'buttonMargin',
		callback: function (data) {
			tab_open(options.digitizeId);


			var digitizeArray = new GeometryArray();

			var wfsConf = get_complete_wfs_conf();
			
			var getJsWfsConfIdByDbWfsConfId = function (wfsConf, id) {
				for (var i = 0; i < wfsConf.length; i++) {
					if (parseInt(wfsConf[i].wfs_conf_id, 10) === id) {
						return i;
					}
				}
				return null;
			};
			
			for (var i in data.selectedRows) {
				digitizeArray.importGeoJSON(data.selectedRows[i].toString());
				digitizeArray.get(-1).wfs_conf = 
					getJsWfsConfIdByDbWfsConfId(wfsConf, parseInt(data.WFSConf.wfs_conf_id));
			}
			window.frames[options.digitizeId].appendGeometryArray(digitizeArray);
			tab_open(options.digitizeId);
			resultList.hide();
		}
	});	
	resultList.events.wfsConfSet.register(function (obj) {
		var isTransactionalGeom = obj.wfsConf.wfs_transaction != "" && obj.wfsConf.wfs_transaction !== null;
		if (!isTransactionalGeom) {
			editButton.hide();
		}
		else {
			editButton.show();			
		}
	});
});
