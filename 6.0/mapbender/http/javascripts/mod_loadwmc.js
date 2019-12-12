// set defaults
options.checkLayerIdExists = options.checkLayerIdExists ? true : false;
options.checkLayerIdValid = options.checkLayerIdValid ? true : false;
options.checkLayerPermission = options.checkLayerPermission ? true : false;
options.checkLayerAvailability = options.checkLayerAvailability ? true : false;
options.loadWmc = typeof options.loadWmc === "number" ? options.loadWmc : 1;
options.mergeWmc = typeof options.mergeWmc === "number" ? options.mergeWmc : 0;
options.appendWmc = typeof options.appendWmc === "number" ? options.appendWmc : 0;
options.publishWmc = typeof options.publishWmc === "number" ? options.publishWmc : 0;
options.showWmc = typeof options.showWmc === "number" ? options.showWmc : 1;
options.openLayers = typeof options.openLayers === "number" ? options.openLayers : 1;
options.openLayersUrl = typeof options.openLayersUrl === "number" ? options.openLayersUrl : 1;
options.deleteWmc = typeof options.deleteWmc === "number" ? options.deleteWmc : 1;
options.uploadWmc = typeof options.uploadWmc === "number" ? options.uploadWmc : 1;
options.listWmc = typeof options.listWmc === "number" ? options.listWmc : 1;
options.saveWmcTarget = typeof options.saveWmcTarget === "string" ? options.saveWmcTarget : "savewmc";
options.editWmc = typeof options.editWmc === "number" ? options.editWmc : 0;
options.showPublic = typeof options.showPublic === "number" ? options.showPublic : 0;

Mapbender.events.init.register(function () {
	if(Mapbender.modules[options.saveWmcTarget] && Mapbender.modules[options.saveWmcTarget].overwrite === 1 && options.editWmc == 1) {
		options.editWmc = 1;
	}
	else {
		options.editWmc = 0;
	}
	//if(Mapbender.modules.i18n){	
	//Mapbender.modules.i18n.queue(options.id, originalI18nObject, function (translatedObject) {
	//	if (typeof translatedObject !== "object") {
	//			return;
	//	}
	//	translatedI18nObject = translatedObject;
	//});
	//Mapbender.modules.i18n.localize(Mapbender.languageId);
//}
	
});

var originalI18nObject = {
	"labelList": "List",
	"labelUpload": "Upload",
	"labelWmcName": "WMC name",
	"labelLastUpdate": "last update",
	"labelCreated": "created",
	"labelLoad": "load",
	"labelMerge": "merge",
	"labelAppend": "append",
	"labelPublic": "public",
	"labelShow": "show/save",
	"labelDelete": "delete",
	"labelEditWmc": "edit",
	"labelOpenLayers": "OpenLayers",
	"labelOpenLayersUrl": "Link",
	"labelCurrentState": "currentState",
	"labelDeleteWmc": "delete this WMC",
	"confirmDelete": "Do you really want to delete WMC",
	"labelLoadWmc": "load this WMC",
	"labelMergeWmc": "merge this WMC",
	"labelAppendWmc": "append WMC",
	"messageLoadSuccess": "WMC has been loaded successfully.",
	"messageMergeSuccess": "WMC has been merged successfully.",
	"messageAppendSuccess":"WMC has been appended successfully.",
	"labelDisplayWmc": "display WMC XML",
	"labelWmcDocument": "WMC Document",
	"labelOpenLayersExport": "export to OpenLayers",
	"confirmLoadAnyway": "load Service anyway?"
};

var translatedI18nObject = Mapbender.cloneObject(originalI18nObject);

if(Mapbender.modules.i18n){	
	Mapbender.modules.i18n.queue(options.id, originalI18nObject, function (translatedObject) {
		if (typeof translatedObject !== "object") {
			return;
		}
		translatedI18nObject = translatedObject;
	});
	//Mapbender.modules.i18n.localize(Mapbender.languageId);
}

var $loadWmc = $(this);

var LoadWmcApi = function () {
	var that = this;
	var serverSideFileName = "../php/mod_loadwmc_server.php";
	var wmcTable;
	var $wmcPopup = null;
	var $wmcDisplayPopup = null;
	var $wmcOpenLayersPopup = null;
	var $wmcOpenLayersUrlPopup = null;
	var wmcPopupHtml = null;
	var wmcListTableInitialized = false;

	var LOAD_WMC_OPTIONS = {
		src: "../img/button_gray/wmc_load.png",
		title: translatedI18nObject.labelLoadWmc,
		method: "loadWmc",
		message: translatedI18nObject.messageLoadSuccess
	};

	var MERGE_WMC_OPTIONS = {
		src: "../img/button_gray/wmc_merge.png",
		title: translatedI18nObject.labelMergeWmc,
		method: "mergeWmc",
		message: translatedI18nObject.messageMergeSuccess
	};
	
	var APPEND_WMC_OPTIONS = {
		src: "../img/button_gray/wmc_append.png",
		title: translatedI18nObject.labelAppendWmc,
		method: "appendWmc",
		message: translatedI18nObject.messageAppendSuccess
	};
	
	var PUBLISH_WMC_OPTIONS = {
		title: translatedI18nObject.labelPublishWmc,
		method: "setWMCPublic"
	};
	
	var OPENLAYERS_WMC_OPTIONS = {
		src: "../img/OpenLayers.trac.png",
		title: translatedI18nObject.labelOpenLayersExport
	};
	
	var OPENLAYERSURL_WMC_OPTIONS = {
		src: "../img/osgeo_graphics/geosilk/link.png",
		title: translatedI18nObject.labelOpenLayersUrl
	};

	var DELETE_WMC_OPTIONS = {
		src: "../img/button_gray/del_disabled.png",
		title: translatedI18nObject.labelDeleteWmc,
		method: "deleteWmc"
	};
	
	var DISPLAY_WMC_OPTIONS = {
		src: "../img/button_gray/wmc_xml.png",
		title: translatedI18nObject.labelDisplayWmc
	};
	
	var EDIT_WMC_OPTIONS = {
		src: "../img/pencil.png",
		title: translatedI18nObject.labelEditWmc,
		method: "editWmc"
	};

	this.events = {
		loaded: new Mapbender.Event()
	};
	
	this.load = function (id) {
		loadMergeAppendCallback($.extend({}, LOAD_WMC_OPTIONS, {
			parameters: {
				id: id
			}
		}));
	};
	
	this.show = function () {
		// creates a new pop up (if it doesn't already exist)
		// the pop up allows you to load, append, merge, 
		// display and delete WMC documents
		if (tagName === "IMG") {
			if($wmcPopup) {
				$wmcPopup.dialog('destroy');
				$wmcPopup.remove();
			}
			wmcPopupHtml = getInitialDialogHtml(options.id);
			$wmcPopup = $(wmcPopupHtml);
			$wmcPopup.dialog({
				title: options.currentTitle,
				bgiframe: true,
				autoOpen: true,
				modal: false,
				width: 750,
				height: 500,
				pos: [100,50]
			}).parent().css({position:"absolute"});
		}
		else {
			$loadWmc.html(getInitialDialogHtml(options.id));
		}

		this.refreshList();			
	};
	
	this.hide = function () {
		if($wmcPopup && $wmcPopup.size() > 0) {
			$wmcPopup.dialog('destroy');
			$wmcPopup.remove();
		}
		this.hideWmcXml();
		this.hideOpenLayers();
	};
	
	this.showWmcXml = function (id) {
		this.hideWmcXml();
		var url = "../javascripts/mod_displayWmc.php?wmc_id=" + id + "&" + mb_session_name + "=" + mb_nr;
		var $wmcDisplayPopup = $('<div class="wmcDisplayPopup"><a href="'+ url + '&download=true' +'"><h3>Download</h3><img src="../img/gnome/document-save.png"/></a><br><br><iframe style="width:99%;height:99%;" src="' + url + '"></iframe></div>');
		$wmcDisplayPopup.dialog({
			title: translatedI18nObject.labelWmcDocument,
			bgiframe: true,
			autoOpen: true,
			modal: false,
			width: 600,
			height: 500,
			pos: [700,50]
		}).parent().css({position:"absolute"});
	};
	
	this.hideWmcXml = function () {
		if($('.wmcDisplayPopup').size() > 0) {
			$('.wmcDisplayPopup').dialog('destroy');
		}
	};
	
	this.showOpenLayers = function (id) {
		this.hideOpenLayers();
		var url = "../php/mod_wmc2ol.php?wmc_id=" + id + "&" + mb_session_name + "=" + mb_nr;
		var $wmcOpenLayersPopup = $('<div class="wmcOpenLayersPopup"><iframe style="width:99%;height:99%;" src="' + url + '" frameborder="0"></iframe></div>');
		$wmcOpenLayersPopup.dialog({
			title: translatedI18nObject.labelOpenLayers,
			bgiframe: true,
			autoOpen: true,
			modal: false,
			width: 600,
			height: 500,
			pos: [600,40]
		}).parent().css({position:"absolute"});
	};
	
	this.hideOpenLayers = function () {
		if($('.wmcOpenLayersPopup').size() > 0) {
			$('.wmcOpenLayersPopup').dialog('destroy');
		}
	};
	//following is added to give a window with an interated link which can be included in external applications
	this.showOpenLayersUrl = function (id) {
		this.hideOpenLayersUrl();
		var url = Mapbender.loginUrl;
		url = url.replace("frames/login.php", "");
		url = Mapbender.baseUrl + "/mapbender/";
		url = url + "php/mod_wmc2ol.php?wmc_id=" + id ;
		var $wmcOpenLayersUrlPopup = $('<div><input size="35" type="text" value="' + url + '"/></div>');
		$wmcOpenLayersUrlPopup.dialog({
			title: translatedI18nObject.labelOpenLayersUrl,
			bgiframe: true,
			autoOpen: true,
			modal: false,
			width: 350,
			height: 90,
			pos: [600,40]
		});
	};
	
	this.hideOpenLayersUrl = function () {
		if($('.wmcOpenLayersUrlPopup').size() > 0) {
			$('.wmcOpenLayersUrlPopup').dialog('destroy');
		}
	};
	//end of the link-handle popup	
	this.refreshList = function () {
		//alert("Options showPublic: "+options.showPublic);
		var req = new Mapbender.Ajax.Request({
			url: serverSideFileName,
			method: "getWmc",
			parameters: {
				showPublic: options.showPublic
			},
			callback: function(obj, result, message){
				if (!result) {
					new Mapbender.Exception(obj.message);
					return;
				}
				displayWmcList(obj, status);
			}
		});
		req.send();
	};
	
	this.showWmcSaveForm = function (id) {
		this.load(id);
		Mapbender.modules[options.saveWmcTarget].getExistingWmcData(function (obj) {
			for (var i = 0; i < obj.length; i++) {
				var wmc = obj[i];
				if (wmc.id === id) {
					$("#" + options.saveWmcTarget + "_wmc_id").val(wmc.id);
					$("#" + options.saveWmcTarget + "_wmctype").append("<option value='" + wmc.id + "' selected>" + wmc.title + "</option>");
					$("#" + options.saveWmcTarget + "_wmctype").attr("disabled","disabled");
					$("#" + options.saveWmcTarget + "_wmcname").val(wmc.title);
					$("#" + options.saveWmcTarget + "_wmcabstract").val(wmc.abstract);
					$("#" + options.saveWmcTarget + "_wmckeywords").val(wmc.keywords.join(","));
					$("input[id^='" + options.saveWmcTarget + "_wmcIsoTopicCategory_']").removeAttr("checked");
					for (var j = 0; j < wmc.categories.length; j++) {
						var cat = wmc.categories[j];
						$("#" + options.saveWmcTarget + "_wmcIsoTopicCategory_" + cat).attr("checked", "checked");
					}
				}
			}
			$("#" + options.saveWmcTarget + "_saveWMCForm").dialog('open');
		});
		Mapbender.modules[options.saveWmcTarget].events.saved.register(function () {
			//that.refreshList();
		});
	};
	
	var getInitialDialogHtml = function (id) {
	
		if (!options.listWmc && !options.uploadWmc) {
			return "";
		}
		var initialHtml = "";

		// add tabs if both modules are available
		if (options.listWmc && options.uploadWmc) {
			initialHtml += "<ul><li><a href='#" + id + "_wmclist'>" + 
				translatedI18nObject.labelList + 
				"</a></li><li><a href='#" + id + "_wmcUpload'>" + 
				translatedI18nObject.labelUpload + "</a></li></ul>";
		}

		initialHtml += "<div id='" + id + "_wmclist' >";
		
		// add listWMC if available
		var t = translatedI18nObject;
		if (options.listWmc) {
			initialHtml += "<table width='100%' id='" + id + "_wmclist_table'>" +
						"<thead><tr>" +
							"<th>" + t.labelWmcName + "</td>" +
							"<th>" + t.labelLastUpdate + "</td>" + 
							"<th>" + t.labelCreated + "</td>" + 
				(options.loadWmc ? "<th>" + t.labelLoad + "</td>" : "") + 
				(options.mergeWmc ? "<th>" + t.labelMerge + "</td>" : "") + 
				(options.appendWmc ? "<th>" + t.labelAppend + "</td>" : "") + 
				(options.publishWmc ? "<th>" + t.labelPublic + "</td>" : "") + 
				(options.showWmc ? "<th>" + t.labelShow + "</td>" : "") +
				(options.openLayers ? "<th>" + t.labelOpenLayers + "</td>" : "") + 
				(options.openLayersUrl ? "<th>" + t.labelOpenLayersUrl + "</td>" : "") + 
				(options.editWmc ? "<th>" + t.labelEditWmc + "</td>" : "") + 
				(options.deleteWmc ? "<th>" + t.labelDelete + "</td>" : "") + 
				"</tr></thead></table>";
		}
		
		initialHtml += "</div>";
		// add uploadWMC if available
		if (options.uploadWmc) {
			initialHtml += "<div id='" + id + "_wmcUpload' ></div>";
		}
		
		return "<div style='display:none' id='" + id + "_tabs'>" + initialHtml + "</div>";
	};
	
	/**
	 * args.method
	 * args.parameters
	 * args.message
	 */
	var executeJavaScript = function (args) {
		var req = new Mapbender.Ajax.Request({
			url: serverSideFileName,
			method: args.method,
			parameters: args.parameters,
			callback: function (obj, result, message) {
				if (!result) {
					new Mapbender.Warning(message);
					return;
				}
				try {
					if (args.method === "deleteWmc" || args.method === "setWMCPublic"){
						return;
					}
					if (obj.javascript && typeof(obj.javascript) == "object") {
						for (var j = 0; j < obj.javascript.length; j++) {
							//TODO: prohibit multiple maprequests when load wmc, cause on maprequests the wmc maybe stored to session :-(   
							//alert("Statement: " + obj.javascript[j]);
							//eventAfterLoadWMS.trigger(); -- load the each wms again and saves the wmc to session for each wms - prohibit this behaviour!!!! - Done by global lock_maprequest in class_wmc.php line 1220+!!
							//console.log("Statement: " + obj.javascript[j]);
							eval(obj.javascript[j]);
						}
						if (args.method === "loadWmc") {
							that.events.loaded.trigger({
								extensionData: restoredWmcExtensionData
							});
						}
					}	
					that.hide();
					new Mapbender.Notice(args.message);
				}
				catch (e) {
					new Mapbender.Exception(e.message);
				}
			}
		});
		req.send();
	};
	
	var loadMergeAppendCallback = function(args){
		if (constraintCheckRequired()) {
			checkConstraints(args, function(args){
				executeJavaScript(args);
			});
			return;
		}
		executeJavaScript(args);
	};

	var createTableCell = function (args, callback) {
		var $img = $("<img src='" + args.src + "' style='cursor:pointer' title='" + args.title + "'>");
		$img.click(function() {
			callback(args);
		});
		return $("<td></td>").append($img);
	};
	
	var appendBoolTableCell = function (args, callback) {
		var checked = args.isPublic ? 'checked="checked" ':'' ;
		var disabled = args.disabled ? 'disabled="disabled" ':'' ;
		var checkbox = $('<input type="checkbox" '+  checked  + disabled + ' />');
		checkbox.change(function(){
			args.parameters.isPublic = $(this).attr('checked') ? 1 : 0;
			callback(args);
		});
		return $("<td></td>").append(checkbox);
	};
	
	var createLoadWmcCell = function (wmc) {
		return createTableCell(
			$.extend({
				parameters: {
					id: wmc.id
				}
			}, LOAD_WMC_OPTIONS), 
			loadMergeAppendCallback
		);
	};
	
	var createMergeWmcCell = function (wmc) {
		//
		// WORKAROUND....cannot serialize map object,
		// as it contains a jQuery collection, which is
		// cyclic.
		// Removing the $target from the map object before
		// serialization, and re-appending it afterwards
		//
		var $target = [];
		for (var i = 0; i < mb_mapObj.length; i++) {
			$target.push(mb_mapObj[i].$target);
			delete mb_mapObj[i].$target;
		}
		
		var $cell = createTableCell(
			$.extend({
				parameters: {
					id: wmc.id,
					extensionData: currentWmcExtensionData !== null ? currentWmcExtensionData : null,
					mapObject: $.toJSON(mb_mapObj),
					generalTitle: translatedI18nObject.labelCurrentState
				}
			}, MERGE_WMC_OPTIONS), 
			loadMergeAppendCallback
		);

		//
		// reversal of above WORKAROUND
		//
		for (var i = 0; i < mb_mapObj.length; i++) {
			mb_mapObj[i].$target = $target[i];
		}
		return $cell;		
	};
	
	var createAppendWmcCell = function (wmc) {
		//
		// WORKAROUND....cannot serialize map object,
		// as it contains a jQuery collection, which is
		// cyclic.
		// Removing the $target from the map object before
		// serialization, and re-appending it afterwards
		//
		var $target = [];
		for (var i = 0; i < mb_mapObj.length; i++) {
			$target.push(mb_mapObj[i].$target);
			delete mb_mapObj[i].$target;
		}
		
		var $cell = createTableCell(
			$.extend({
				parameters: {
					id: wmc.id,
					extensionData: currentWmcExtensionData !== null ? currentWmcExtensionData : null,
					mapObject: $.toJSON(mb_mapObj),
					generalTitle: translatedI18nObject.labelCurrentState
				}
			}, APPEND_WMC_OPTIONS), 
			loadMergeAppendCallback
		);

		//
		// reversal of above WORKAROUND
		//
		for (var i = 0; i < mb_mapObj.length; i++) {
			mb_mapObj[i].$target = $target[i];
		}
		return $cell;
	};
	
	var createPublishWmcCell = function (wmc) {
		return appendBoolTableCell(
			$.extend({}, PUBLISH_WMC_OPTIONS, {
				isPublic: wmc.isPublic,
				disabled: wmc.disabled,
				parameters: {
					id: wmc.id
				}
			}), 
			executeJavaScript
		);
	};

	var createOpenLayersWmcCell = function (wmc) {
		return createTableCell(
			OPENLAYERS_WMC_OPTIONS, function(){
				that.showOpenLayers(wmc.id);
			}
		);	
	};

	var createOpenLayersUrlWmcCell = function (wmc) {
		return createTableCell(
			OPENLAYERSURL_WMC_OPTIONS, function(){
				that.showOpenLayersUrl(wmc.id);
			}
		);	
	};
	
	var createDisplayWmcCell = function (wmc) {
		return createTableCell(
			DISPLAY_WMC_OPTIONS,
			function(){
				that.showWmcXml(wmc.id);
			}
		);	
	};
	
	var createEditWmcCell = function (wmc) {
		return createTableCell(
			EDIT_WMC_OPTIONS,
			function(){
				that.showWmcSaveForm(wmc.id);
			}
		);	
	};
	
	var createDeleteWmcCell = function (wmc) {
		var $deleteTd = createTableCell(
			$.extend({}, DELETE_WMC_OPTIONS, {
				src: wmc.disabled ? "../img/button_gray/del_disabled.png" : "../img/button_gray/del.png",
				parameters: {
					id: wmc.id
				}
			}), 
			function(args){
				if (!wmc.disabled && confirm(Mapbender.sprintf(translatedI18nObject.confirmDelete, wmc.title))) {
					executeJavaScript(args);
					var aPos = wmcTable.fnGetPosition($deleteTd.get(0));
					wmcTable.fnDeleteRow(aPos[0]);
				}
			}
		);	
		return $deleteTd;
	};
	
	var displayWmcList = function (wmcObj, status) {
/*		
		if(wmcListTableInitialized === true) {
			wmcTable.fnClearTable();
			
			for (var i=0; i < wmcObj.wmc.length; i++) {
				(function () {
					
					var currentIndex = i;
					var currentWmc = wmcObj.wmc[currentIndex];
					
					var dataArray = [currentWmc.title, currentWmc.timestamp, currentWmc.timestamp_create];
					
					if (options.loadWmc) {
						dataArray.push(createLoadWmcCell(currentWmc));
					}
					if (options.mergeWmc) {
						dataArray.push(createMergeWmcCell(currentWmc));
					}
					if (options.appendWmc) {
						dataArray.push(createAppendWmcCell(currentWmc));
					}
					if (options.publishWmc) {
						dataArray.push(createPublishWmcCell(currentWmc));
					}
					if (options.showWmc) {
						dataArray.push(createDisplayWmcCell(currentWmc));
					}
					if (options.openLayers) {
						dataArray.push(createOpenLayersWmcCell(currentWmc));
					}
					if (options.editWmc) {
						dataArray.push(createEditWmcCell(currentWmc));
					}
					if (options.deleteWmc) {
						dataArray.push(createDeleteWmcCell(currentWmc));
					}
					wmcTable.fnAddData(dataArray);
					//$tr.appendTo($("tbody", wmcTable));
				})();
			}
		}
		else {
*/		
		// create table
		for (var i=0; i < wmcObj.wmc.length; i++) {
			(function () {
				var currentIndex = i;
				var currentWmc = wmcObj.wmc[currentIndex];
				
				var $tr = $("<tr></tr>").append("<td>" + currentWmc.title + "</td><td>" + 
					currentWmc.timestamp + "</td><td>" + 
					currentWmc.timestamp_create + "</td>"
				);
				
				if (options.loadWmc) {
					$tr.append(createLoadWmcCell(currentWmc));
				}
				if (options.mergeWmc) {
					$tr.append(createMergeWmcCell(currentWmc));
				}
				if (options.appendWmc) {
					$tr.append(createAppendWmcCell(currentWmc));
				}
				if (options.publishWmc) {
					$tr.append(createPublishWmcCell(currentWmc));
				}
				if (options.showWmc) {
					$tr.append(createDisplayWmcCell(currentWmc));
				}
				if (options.openLayers) {
					$tr.append(createOpenLayersWmcCell(currentWmc));
				}
				if (options.openLayersUrl) {
					$tr.append(createOpenLayersUrlWmcCell(currentWmc));
				}
				if (options.editWmc) {
					$tr.append(createEditWmcCell(currentWmc));
				}
				if (options.deleteWmc) {
					$tr.append(createDeleteWmcCell(currentWmc));
				}
				$tr.appendTo("#" + options.id + "_wmclist_table");
			})();
		}
/*			
		}
		

	
		
		
		if(wmcListTableInitialized === false) {
*/
			// create horizontal tabs
		// create horizontal tabs
		if (options.listWmc && options.uploadWmc) {
			$("#" + options.id + "_tabs").tabs().css("display", "block");
		}
		
		// create upload
		if (options.uploadWmc) {
			$("#" + options.id + "_wmcUpload").upload({
				callback: function(filename, success, message){
					if (!success) {
						new Mb_exception(message);
						alert(message);
					}
					executeJavaScript({
						method: "loadWmcFromFile",
						parameters: {
							filename: filename
						},
						message: translatedI18nObject.messageLoadSuccess
					});
				}
			});
		}
		
		// create datatables
		if (options.listWmc) {
			wmcTable = $("#" + options.id + "_wmclist_table").dataTable({
				"bPaginate": true,
				"bJQueryUI": true
			});
		}
/*			
			wmcListTableInitialized = true;
		}
		else {
			wmcTable.fnDraw();
		}
*/		
	};
	
	var constraintCheckRequired = function () {
		return options.checkLayerIdExists 
			|| options.checkLayerIdValid 
			|| options.checkLayerPermission 
			|| options.checkLayerAvailability;
	};
	
	var checkConstraints = function (args, callback) {
		var req = new Mapbender.Ajax.Request({
			url: serverSideFileName,
			method: "checkConstraints",
			parameters: {
				id: args.parameters.id,
				checkLayerIdExists: options.checkLayerIdExists,
				checkLayerIdValid: options.checkLayerIdValid,
				checkLayerPermission: options.checkLayerPermission,
				checkLayerAvailability: options.checkLayerAvailability
			},
			callback: function (obj, result, message) {
				
				var html = "";
				var constraintTypeArray = [];
				for (var constraintType in obj) {
					var caseObj = obj[constraintType];
	
					if (caseObj.wms.length === 0) {
						continue;
					}
	
					html += "<fieldset>" + caseObj.message + translatedI18nObject.confirmLoadAnyway + "<br><br>";
					
					for (var index in caseObj.wms) {
						var wms = caseObj.wms[index];
						html += "<label for='" + constraintType + "_" + wms.index + "'>" + 
							"<input id='" + options.id + "_" + constraintType + "_" + wms.index + "' " + 
							"type='checkbox' />" + wms.title  + "</label><br>";
					}
					html += "</fieldset><br>";
					constraintTypeArray.push(constraintType);
				}
	
				$("<div id='" + options.id + "_constraint_form' title='Warning'>" + 
					"<style> fieldset label { display: block; }</style>" + 
					"<form>" + html + "</form></div>").dialog(
					{
						bgiframe: true,
						autoOpen: false,
						height: 400,
						width: 500,
						modal: true,
						buttons: {
							"Continue": function () {
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
								args.parameters.skipWms = skipWmsArray;
								if (typeof callback === "function") {
									callback(args);
								}
								$(this).dialog('close').remove();
							},
							"Cancel": function(){
								$(this).dialog('close').remove();
							}
						}
					}
				);
	
				$("#" + options.id + "_constraint_form").dialog('open');
			}
		});
		req.send();
	};
	
	//
	// constructor
	//
	var tagName = $loadWmc.get(0).tagName.toUpperCase();
	if (tagName === "IMG") {
	
		$loadWmc.click(function () {
			that.show();
		}).mouseover(function () {
			if (options.src) {
				this.src = options.src.replace(/_off/, "_over");
			}
		}).mouseout(function () {
			if (options.src) {
				this.src = options.src;
			}
		});
	}
	else {
		that.show();
	}	
	
/*	try {
		
		// checks if element var loadFromSession exists
		if (loadFromSession === undefined) {
			loadFromSession = 0;
		}
		
		if (loadFromSession) {
			Mapbender.events.init.register(function () {
				load_wmc_session();
			});
		}
	}
	catch (exc) {
		new Mapbender.Exception(exc);
	}
*/
};

$loadWmc.mapbender(new LoadWmcApi());
