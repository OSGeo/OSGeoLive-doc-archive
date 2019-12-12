/**
 * Package: mb_metadata_wfs_edit
 *
 * Description:
 *
 * Files:
 *
 * SQL:
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

var $metadataEdit = $(this);
var $metadataForm = $("<form>No WFS selected.</form>").appendTo($metadataEdit);

var MetadataEditApi = function (o) {
	var that = this;
	var validator;
	var formReady = false;
	var wfsId;
	
	this.events = {
		showOriginalMetadata : new Mapbender.Event(),
		submit: new Mapbender.Event()
	};

	this.valid = function () {
		if (validator && validator.numberOfInvalids() > 0) {
			$metadataForm.valid();
			return false;
		}
		return true;
	};
	
	this.serialize = function (callback) {
		$metadataForm.submit();
		var data = null;
		if (this.valid()) {
			data = {
				wfs: $metadataForm.easyform("serialize")
			};
		}
		if ($.isFunction(callback)) {
			callback(data);
		}
		return data !== null ? data.wfs : data;
	};
	
	// second optional parameter formData
	var fillForm = function (obj) {
		
		if (arguments.length >= 2) {
			$metadataForm.easyform("reset");
			$metadataForm.easyform("fill", arguments[1]);
			that.valid();
			return;
		}
		
		// get metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_wfs_server.php",
			method: "getWfsMetadata",
			parameters: {
				"id": obj
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				$metadataForm.easyform("reset");
				$metadataForm.easyform("fill", obj);
				that.valid();
			}
		});
		req.send();		
	};

	this.fillMdContact = function(obj) {
		// get mdContact from server per fkey_mb_group_id
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "getContactMetadata",
			parameters: {
				"id": obj
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				//fill form on a not so easy way ;-)
				for (var key in obj) {
					if (key == 'mb_group_title' || key == 'mb_group_address' || key == 'mb_group_postcode' || key == 'mb_group_city' || key == 'mb_group_logo_path' || key == 'mb_group_email' || key == 'mb_group_voicetelephone'){
						document.getElementById(key).value = obj[key];
					}
				}
			}
		});
		req.send();
	}	

	this.fill = function (obj) {
		$metadataForm.easyform("fill", obj);
	};
	
	var showOriginalMetadata = function () {
		that.events.showOriginalMetadata.trigger({
			data : {
				wfsId : wfsId,
				wfsData : $metadataForm.easyform("serialize")
			}
		});
	};
	
	this.init = function (obj) {
		wfsId = obj;
		
		var formData = arguments.length >= 2 ? arguments[1] : undefined;
		
		if (!formReady) {
			$metadataForm.load("../plugins/mb_metadata_wfs_edit.php", function () {
				$metadataForm.find(".help-dialog").helpDialog();
				$metadataForm.find(".original-metadata-wfs").bind("click", function() {
					showOriginalMetadata();
				});				
				validator = $metadataForm.validate({
					submitHandler: function () {
						return false;
					}
				});
				if (formData !== undefined) {
					fillForm(obj, formData);
				}
				else {
					fillForm(obj);
				}
				formReady = true;
			});
			return;
		}
		fillForm(obj);
	};
	
	Mapbender.events.localize.register(function () {
		that.valid();
		var formData = $metadataForm.easyform("serialize");
		formReady = false;
		that.init(wfsId, formData);
	});
	Mapbender.events.init.register(function () {
		that.valid();
	});
};

$metadataEdit.mapbender(new MetadataEditApi(options));
