/**
 * Package: mb_metadata_featuretype
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

var $metadataFeaturetype = $(this);
var $metadataForm = $("<form>No featuretype selected.</form>").appendTo($metadataFeaturetype);

var MetadataLayerApi = function (o) {
	var that = this;
	var validator;
	var formReady = false;
	var wfsId;
	var featuretypeId;
	
	var disabledFields = [
		"featuretype_custom_category_id", 
		"featuretype_inspire_category_id", 
		"featuretype_md_topic_category_id", 
		"featuretype_keyword", 
		"featuretype_abstract", 
		"featuretype_title"
	];

	this.events = {
		initialized: new Mapbender.Event(),
		submit: new Mapbender.Event(),
		showOriginalFeaturetypeMetadata : new Mapbender.Event()
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
				featuretype: $metadataForm.easyform("serialize")
			};
		}
		if ($.isFunction(callback)) {
			callback(data);
		}
		return data !== null ? data.featuretype : data;
	};

	this.fillForm = function (obj) {
		$(disabledFields).each(function () {
			$("#" + this).removeAttr("disabled");
		});

		featuretypeId = obj.featuretype_id;

		$metadataForm.easyform("reset");
		
		// get metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_wfs_server.php",
			method: "getFeaturetypeMetadata",
			parameters: {
				"id": featuretypeId
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				$metadataForm.easyform("fill", obj);
				that.valid();
				that.enableResetButton();
			}
		});
		req.send();		
	};
	
	this.enableResetButton = function () {
		$("#resetIsoTopicCats").click(function () {
			$("#featuretype_md_topic_category_id option").removeAttr("selected");
		});
		$("#resetCustomCats").click(function () {
			$("#featuretype_custom_category_id option").removeAttr("selected");
		});
		$("#resetInspireCats").click(function () {
			$("#featuretype_inspire_category_id option").removeAttr("selected");
		});
	}
	
	this.fill = function (obj) {
		$metadataForm.easyform("fill", obj);
	};
	
	var showOriginalFeaturetypeMetadata = function () {
		that.events.showOriginalFeaturetypeMetadata.trigger({
			data : {
				wfsId : wfsId,
				featuretypeData : $metadataForm.easyform("serialize")
			}
		});
	};
	

	this.init = function (obj) {
		delete featuretypeId;
		$metadataForm.easyform("reset");

		wfsId = obj;
		
		var formData = arguments.length >= 2 ? arguments[1] : undefined;

		if (!formReady) {
			$metadataForm.load("../plugins/mb_metadata_featuretype.php", function () {
				$metadataForm.find(".help-dialog").helpDialog();

				$metadataForm.find(".original-metadata-featuretype").bind("click", function() {
					showOriginalFeaturetypeMetadata();
				});	

				validator = $metadataForm.validate({
					submitHandler: function () {
						return false;
					}
				});

				that.events.initialized.trigger({
					wfsId: wfsId
				});
				formReady = true;
				
				// select layer in tree if set
				
				// init map
				
				// fill layer form if set
			});
			return;
		}
		$(disabledFields).each(function () {
			$("#" + this).attr("disabled", "disabled");
		});
		that.events.initialized.trigger({
			wfsId: wfsId
		});
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

$metadataFeaturetype.mapbender(new MetadataLayerApi(options));
