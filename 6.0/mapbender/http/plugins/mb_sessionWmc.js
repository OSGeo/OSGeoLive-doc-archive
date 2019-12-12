/**
 * Package: sessionWmc
 *
 * Description:
 * Checks whether the GET API has loaded services and conflicts arose.
 * This module will display the conflicts and allows the user to resolve
 * these conflicts. Possible conflicts are: no permission, service not in
 * repository, service not up to date, etc.
 *
 * Files:
 *  - http/plugins/mb_sessionWmc.js
 *  - http/php/mod_sessionWmc_server.php
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','sessionWmc',2,1,'','Please confirm','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_sessionWmc.js','','','mapframe1','');
 *
 * Help:
 * http://www.mapbender.org/<wiki site name>
 *
 * Maintainer:
 * http://www.mapbender.org/User:<user>
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var originalI18nObject = {
	"confirmationQuestion": "Should this services be loaded regardless?",
	"goFurtherOn": "Continue",
	"confirmationTitle": "Confirmation"
};

var translatedI18nObject = Mapbender.cloneObject(originalI18nObject);
Mapbender.events.init.register(function () {
	if(Mapbender.modules.i18n){	
	Mapbender.modules.i18n.queue(options.id, originalI18nObject, function (translatedObject) {
		if (typeof translatedObject !== "object") {
			return;
		}
		translatedI18nObject = translatedObject;
	});
	//Mapbender.modules.i18n.localize(Mapbender.languageId);
}
	
});


var $sessionWmc = $(this);

var SessionWmcApi = function (o) {
	var that = this;
	var $dialog;

	var closeDialog = function () {
		if ($dialog === undefined || !$dialog.jquery) {
			return;
		}
		$dialog.dialog('close').remove();
	};

	var confirmConstraints = function (constraintTypeArray) {
		var skipWmsArray = [];
		for (var i in constraintTypeArray) {
			var currentConstraint = constraintTypeArray[i];
			var selector = options.id + "_" + currentConstraint + "_";
			var context = $("#" + options.id + "_constraint_form").get(0);
			$("input[id^='" + selector + "']", context).each(function () {
				if (!this.checked) {
					var regexp = new RegExp(selector);
					var id = parseInt(this.id.replace(regexp, ""), 10);
					skipWmsArray.push(id);
				}
			});
		}
		updateWmc(skipWmsArray);
	};

	var updateWmc = function (wmsIndices) {
		var req = new Mapbender.Ajax.Request({
			url: "../php/mod_sessionWmc_server.php",
			method: "updateWmc",
			parameters: {
				wmsIndices: wmsIndices
			},
			callback: function (obj, result, message) {
				if (!result) {
					$sessionWmc.text(message).dialog();
					return;
				}
				// execute JS code
				if (obj.js) {
					for (var j = 0; j < obj.js.length; j++) {
						eval(obj.js[j]);
					}
				}
				
				closeDialog();
			}
		});
		req.send();
	};

	this.deleteWmc = function () {
		var req = new Mapbender.Ajax.Request({
			url: "../php/mod_sessionWmc_server.php",
			method: "deleteWmc",
			parameters: {
			},
			callback: function (obj, result, message) {
				window.resetSession = true;
				//unset the get api params cause they will load the old state!
				var currentURL = location;
				var newHref = currentURL.protocol+'//'+currentURL.host+currentURL.pathname;
				window.location.href = newHref;
			}
		});
		req.send();
	};

	var displayConstraints = function (obj) {
		var html = "";
		var constraintTypeArray = [];
		var dialogHasContent = false;
		var wmsCount = 0;

		for (var constraintType in obj) {
			var caseObj = obj[constraintType];
			//check if terms of use has been set - then there are tou given in the message!
			if (o.displayTermsOfUse && constraintType === "wmcTou" && caseObj.message != "") {
				html += "<fieldset>"+caseObj.message+"</fieldset>";
				dialogHasContent = true;
				continue;
			}
			if (caseObj.wms && caseObj.wms.length === 0) {
				continue;
			}

			var permissionHtml = "";
			for (var index in caseObj.wms) {
				var wms = caseObj.wms[index];
				permissionHtml += "<label for='" + constraintType + "_" + wms.index + "'>" +
				(constraintType === "noPermission" ? "<li>" : "<input id='" + options.id + "_" + constraintType + "_" + wms.index + "' " + "type='checkbox' />") +
				wms.title  + "</label><br>";
				wmsCount++;
			}
			if (wmsCount > 0) {
				html += "<fieldset>" + caseObj.message +
					(constraintType === "noPermission" ?
						"<br><ul>" + permissionHtml + "</ul>" :
						" "+translatedI18nObject.confirmationQuestion+"<br><br>" +
						permissionHtml
					) + "</fieldset><br>";
				dialogHasContent = true;
			}
			constraintTypeArray.push(constraintType);
		}
		
		if (!dialogHasContent) {
			return;
		}

		$dialog = $("<div id='" + o.id + "_constraint_form' title='"+translatedI18nObject.confirmationTitle+"'>" +
			"<style> fieldset label { display: block; }</style>" +
			"<form>" + html + "</form></div>").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 400,
				width: 500,
				modal: true,
				buttons: {
					"Weiter": function () {
						confirmConstraints(constraintTypeArray);
					}
				},
				close: function () {
				}
			}
		);

		$dialog.dialog('open');
	};

	var checkConstraints = function () {
		var req = new Mapbender.Ajax.Request({
			url: "../php/mod_sessionWmc_server.php",
			method: "checkConstraints",
			parameters: {
			},
			callback: function (obj, result, message) {
				if (!result) {
					$sessionWmc.text(message).dialog();
					return;
				}
				displayConstraints(obj);
			}
		});
		req.send();
	};
	
	Mapbender.events.beforeInit.register(checkConstraints);
};

$sessionWmc.mapbender(new SessionWmcApi(options));
