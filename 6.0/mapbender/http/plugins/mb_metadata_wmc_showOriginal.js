var $showOriginalDiv = $(this);
var $originalMetadataForm = $("<form></form>").appendTo($showOriginalDiv);
var $originalMetadataPopup = $("<div></div>");

var ShowOriginalApi = function() {
	var that = this;
	var featuretypeName;
	
	this.events = {
		replaceMetadata : new Mapbender.Event()
	};
	
	var replaceMetadata = function (clickedAttr, attrVal) {
		var returnObj = {};
		returnObj[clickedAttr] = attrVal;
		that.events.replaceMetadata.trigger({
			data : returnObj
		});
		//remove background-color of replaced attr
		$("#" + clickedAttr).removeClass("differentFromOriginal");
	};
	
	var replaceAllMetadata = function (obj) {
		var returnObj = {};
		for(metadataAttr in obj) {
			var attr = metadataAttr.split("original_");
			returnObj[attr[1]] = obj[metadataAttr];
		}	
		that.events.replaceMetadata.trigger({
			data : returnObj
		});
		//remove background-color of all replaced attr
		$(".differentFromOriginal").removeClass("differentFromOriginal");
	};
	
	var mergeOriginalWithCurrentMetadata = function (originalObj,currentObj) {
		var differenceFound = false;
		for(metadataAttr in currentObj) {
			var origMetadataAttr = "original_" + metadataAttr;
			
			//some attr are excluded from check
			if(metadataAttr != "wmc_id") {
				if(!originalObj[origMetadataAttr]) {
					originalObj[origMetadataAttr] = "";
				}
				
				if(currentObj[metadataAttr] == originalObj[origMetadataAttr]) {
					$("#" + origMetadataAttr).parent().hide();
				}
				else if(currentObj[metadataAttr] == "" && originalObj[origMetadataAttr] == "") {
					$("#" + origMetadataAttr).parent().hide();
				}
				else {
					//mark all attr in main form which are different
					$("#" + metadataAttr).addClass("differentFromOriginal");
					(function () {
						var clickedAttr = metadataAttr;
						var attrValue = $("#" + origMetadataAttr).val();
						$("#" + origMetadataAttr).parent().append("<input type='button' value='Replace' id='replaceRecord_"+origMetadataAttr+"' />");
						$("#replaceRecord_" +origMetadataAttr).addClass("ui-state-default ui-corner-all");
						$("#replaceRecord_" +origMetadataAttr).bind("click", function () {
							replaceMetadata(clickedAttr, attrValue);
						});
					})();
					var differenceFound = true;
				}
			}
		}
		if(differenceFound === false) {
			$originalMetadataPopup.dialog("close");
			var noDifferenceMsg = "No difference found.";
			$("<div>" + noDifferenceMsg + "</div>").dialog(
				{
					title : "Show original metadata", 
					bgiframe: true,
					autoOpen: true,
					modal: false,
					position : [600, 75],
					buttons: {
						"ok": function(){
							$(this).dialog('close').remove();
						}
					}
				}
			);
		}
	};
	
	var getOriginalMetadata = function (currentId, currentData) {
		// get original metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_original_metadata_wmc_server.php",
			method: "getOriginalMetadata",
			parameters: {
				"id": currentId
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				fillForm(obj);
				mergeOriginalWithCurrentMetadata(obj,currentData);
			}
		});
		req.send();	
	};
	
	var fillForm = function (obj) {
		$originalMetadataForm.easyform("reset");
		$originalMetadataForm.easyform("fill", obj);
		$originalMetadataPopup.append($originalMetadataForm);
		$originalMetadataPopup.dialog({
			title : "Show original metadata", 
			autoOpen : false, 
			draggable : true,
			modal : true,
			width : 600,
			position : [600, 75],
			buttons: {
				"close": function() {
					$(this).dialog('close');
				},
				"replace all metadata": function() {
					replaceAllMetadata(obj);
					$(this).dialog('close');
				}
			},
			close: function() {
				$(".differentFromOriginal").removeClass("differentFromOriginal");
			}
		});
		$originalMetadataPopup.dialog("open");
		
	};
	
	this.init = function (obj) {
		$originalMetadataPopup.dialog("close");
		$originalMetadataForm.load("../plugins/mb_metadata_wmc_showOriginal.html", function () {
			getOriginalMetadata(obj.wmcId, obj.wmcData);
		});
	}
};

$showOriginalDiv.mapbender(new ShowOriginalApi());