var WfsConfInterface = function (options) {

	var ALL_WFS_CONF_ATTRIBUTES = {
		"geom" : {
			"type" : "radio"	
		}, 
		"search" : {
			"type" : "checkbox"
		}, 
		"pos" : {
			"type" : "text",
			"def" : 0,
			"size" : 1
		}, 
		"minInput" : {
			"type" : "select",
			"def" : {
				"0" : "...", 
				"1" : "1", 
				"2" : "2", 
				"3" : "3", 
				"4" : "4", 
				"5" : "5"
			}
		}, 
		"styleId" : {
			"type" : "text",
			"def" : 0,
			"size" : 1
		},
		"toUpper" : {
			"type" : "checkbox"
		}, 
		"label" : {
			"type" : "text",
			"size" : 2
		},
		"labelId" : {
			"type" : "text",
			"def" : 0,
			"size" : 1
		}, 
		"show" : {
			"type" : "checkbox"
		}, 
		"respos" : {
			"type" : "text",
			"def" : 0,
			"size" : 1
		},
		"showDetail" : {
			"type" : "checkbox"
		}, 
		"detailPos" : {
			"type" : "text",
			"def" : 0,
			"size" : 1
		}, 
		"mandatory" : {
			"type" : "checkbox"
		},
		"edit" : {
			"type" : "checkbox"
		}, 
		"formElementHtml" : {
			"type" : "htmlTemplate"
		}, 
		"authVarname" : {
			"type" : "text",
			"size" : 8
		}, 
		"operator" : {
			"type" : "select",
			"def" : {
				"0" : "...", 
				"bothside" : "%...%", 
				"rightside" : "...%", 
				"equal" : "equal", 
				"greater_than" : ">", 
				"less_than" : "<"
			}
		}, 
		"helptext" : {
			"type" : "textarea"
		},	
		"category" : {
			"type" : "text",
			"size" : 4
		}
	};

	var WFS_CONF_FORM_OPERATORS = {
		"int" : ["equal", "greater_than", "less_than"],
		"float" : ["equal", "greater_than", "less_than"],
		"string" : ["equal", "bothside", "rightside"],
		"date" : ["equal", "bothside", "rightside"],
		"all" : ["equal", "bothside", "rightside", "greater_than", "less_than"]
	};

	var WFS_CONF_FORM_HTML_TEMPLATES = {
		"textarea" : {
			"title" : "Textarea",
			"code" : "<textarea cols='' rows='' id=''></textarea>"
		},
		"datepicker" : {
			"title" : "Datepicker",
			"code" : "<input type='text' id='' class='hasdatepicker' />"
		},
		"selectbox" : {
			"title" : "Selectbox",
			"code" : "<select id=''>\n" + 
				"<option>...</option>\n" + 
				"<option value=''></option>\n" + 
				"<option value=''></option>\n" + 
				"<option value=''></option>\n" + 
				"</select>"
		},	
		"checkbox" : {
			"title" : "Checkbox",
			"code" : "<input type='checkbox' id='' value='1'>"
		},
		"upload" : {
			"title" : "Upload",
			"code" : "<input type='hidden' id='' class='hiddenUploadField' /><div id='_upload' class='upload' />"
		}
	};
	
	var WFS_CONF_FORM = "";
	var WFS_CONF_TYPE_NAME_SUFFIX = "selectwfs_conf_type";
	var WFS_CONF_TYPES = [
		{
			"id" : 2,
			"value" : "download",
			"title" : "Download",
			"icon" : "someicon.png",
			"columns" : [
				"geom", "pos", "show", "respos", "showDetail", "detailPos", "helptext", "label"
			]
		},
		{
			"id" : 0,
			"value" : "search",
			"title" : "Search",
			"icon" : "someicon.png",
			"columns" : [
				"geom", "search", "pos", "minInput", "styleId", "toUpper", 
				"label", "labelId", "show", "respos", "showDetail", "detailPos", 
				"formElementHtml", "authVarname", "operator", "helptext"
			]
		},
		{
			"id" : 1,
			"value" : "digitize",
			"title" : "Digitize",
			"icon" : "someicon.png",
			"columns" : [
				"geom", "pos", "styleId", "label", "labelId", "show", 
				"respos", "mandatory", "edit", "formElementHtml", 
				"authVarname", "helptext",	"category"
			]
		}
	];


	// -------------------------------
	// DATA
	// -------------------------------

	var wfsArray = [];
	var wfsConfArray = [];

	// get available WFS ("new" mode)
	var getWfsFromDb = function (options) {
		var req = new Mapbender.Ajax.Request({
			method : "getWfs",
			parameters : {},
			url: "../php/mod_wfs_conf_server.php",
			callback: function(result, success, message) {
				if (result !== null) {
					wfsArray = result;
				}
				if (typeof options !== "object") {
					return;
				}
				if (typeof options.callback === "function") {
					options.callback(wfsArray);
				}
			} 
		});
		req.send();	
	};
	
	// get available WFS configurations ("edit" mode)
	var getWfsConfsFromDb = function (options) {
		var req = new Mapbender.Ajax.Request({
			method : "getWfsConfs",
			parameters : {},
			url: "../php/mod_wfs_conf_server.php",
			callback: function (result, success, message) {
				if (result !== null) {
					wfsConfArray = result;
				}
				if (typeof options !== "object") {
					return;
				}
				if (typeof options.callback === "function") {
					options.callback(wfsConfArray);
				}
			}
		});
		req.send();	
	};
	
	var updateWfsConfInDb = function (wfsConf) {
		var req = new Mapbender.Ajax.Request({
			method : "updateWfsConf",
			parameters: {
				"wfsConf": wfsConf
			},
			url: "../php/mod_wfs_conf_server.php",
			callback: function(result, success, message) {
				isNotBusy.trigger();
				alert(message);
			}
		});
		isBusy.trigger();
		req.send();	
	};

	var insertWfsConfIntoDb = function (wfsConf) {
		var req = new Mapbender.Ajax.Request({
			method : "insertWfsConf",
			parameters: {
				"wfsConf": wfsConf
			},
			url: "../php/mod_wfs_conf_server.php",
			callback: function(result, success, message) {
				if (success) {
					$("#wfs_conf_id").val(result.id);
	
					// update WFS conf array (add the latest entry)
					getWfsConfsFromDb({
						"callback": function(wfsConfArray) {
							isNotBusy.trigger();
							alert(message);
						}
					});
				}
				else {
					isNotBusy.trigger();
					alert(message);
				}
			}
		});
		isBusy.trigger();
		req.send();	
	};

	var getWfsById = function (id) {
		// find WFS in WFS array
		for (var i = 0; i < wfsArray.length; i++) {
			if (wfsArray[i].id === id) {
				return wfsArray[i];
			}
		}
		return null;
	};

	var getWfsConfById = function (id) {
		// find WFS Conf in WFS Conf array
		for (var i = 0; i < wfsConfArray.length; i++) {
			if (wfsConfArray[i].id === id) {
				return wfsConfArray[i];
			}
		}
		return null;
	};
	
	var getWfsConfIndex = function (id) {
		// find WFS Conf in WFS Conf array
		for (var i = 0; i < wfsConfArray.length; i++) {
			if (wfsConfArray[i].id === id) {
				return i;
			}
		}
		return null;
	};
	

	//
	// FORMS
	//
	// there are four major components to this interface
	//
	// 1) WfsSelectBox: 
	//		WFS select box (use case "new WFS conf")

	var fillWfsSelectBox = function (wfsArray) {
	
		var $select = $("#new_select_wfs_select");
		$select.empty();
		var optionString = "<option>...</option>";
		for (var i = 0; i < wfsArray.length; i++) {
			optionString += "<option value='" + wfsArray[i].id + "'>" +
				wfsArray[i].id + " " + wfsArray[i].title + "</option>";
		}
		$select.append(optionString);
		$select.change(function(){
			var wfsId = this.value;
			var wfs = getWfsById(wfsId);
			if (this.selectedIndex !== 0 && wfs !== null) {
				onSelectWfs(wfs);
			}
			return false;
		});
		$select.removeAttr("disabled");
		var currentWfs = $select.children("[selected]").val();
	};


	// 2) WfsMetadata: 
	//		WFS metadata with featuretype select box and type selector
	//		(use case: "new WFS conf")

	var getWfsConfType = function (id) {
		var $checkedRadioButtons = $("#" + id + " input:checked");
		if ($checkedRadioButtons.length) {
			return $checkedRadioButtons[0].value;
		}
		var $firstRadioButtons = $("#" + id + " input");
		if ($firstRadioButtons.length) {
			return $firstRadioButtons[0].value;
		}
		return "download";
	};
	
	var wfsConfTypeIsSelected = function () {
		var $checkedRadioButtons = $("#new_wfs_metadata_radio_conftype input:checked");
		return ($checkedRadioButtons.size() > 0);
	};
	
	var getWfsConfFeaturetype = function () {
		return $("#new_select_featuretype_select").get(0).value;
	};
	
	var wfsConfFeaturetypeIsSelected = function () {
		return ($("#new_select_featuretype_select").get(0).selectedIndex > 0);
	};
	
	var createWfsConfTypeSelectBox = function (name) {
		html = "";
		for (var i = 0; i < WFS_CONF_TYPES.length; i++) {
			html += "<input type='radio' name='" + name + "' " + 
				"value='" + WFS_CONF_TYPES[i].value + "'> " + 
				WFS_CONF_TYPES[i].title + "<br>";
		}		
		return html;
	};
	
	var fillWfsMetadataForm = function (wfs) {
	
		$('#new_wfs_metadata_id').html(wfs.id);
		$('#new_wfs_metadata_name').html(wfs.name);
		$('#new_wfs_metadata_title').html(wfs.title);
		$('#new_wfs_metadata_abstract').html(wfs.abstr);
		$('#new_wfs_metadata_getcapabilities').html(wfs.getCapabilities);
		$('#new_wfs_metadata_describefeaturetype').html(wfs.describeFeaturetype);
		$('#new_wfs_metadata_getfeature').html(wfs.getFeature);
	
		//
		// fill featuretype select box and add behaviour
		// 
		$('#new_select_featuretype_select > option:gt(0)').remove();
	
		var $select = $('#new_select_featuretype_select');
		var optionString = "";
		for (var i = 0; i < wfs.featuretypeArray.length; i++) {
			var ft = wfs.featuretypeArray[i];
			optionString += "<option value='" + ft.id + "'>" + ft.name + "</option>";		
		}
		$select.append(optionString);
	
		$select.change(function () {
			if (!wfsConfFeaturetypeIsSelected()) {
				alert("Please select a featuretype!");
				return;
			} 
			if (!wfsConfTypeIsSelected()) {
				alert("Please select a WFS conf type!");
				return;
			}
			
			// find featuretype in featuretype array
			var index = null;
			var ftId = getWfsConfFeaturetype();
			for (var i = 0; i < wfs.featuretypeArray.length; i++) {
				if (wfs.featuretypeArray[i].id === ftId) {
					index = i;
					break;
				}
			}
			// WFS not found in WFS array
			if (index === null) {
				return;
			}
			var ft = wfs.featuretypeArray[index];
			onSelectFeaturetype(ft);
		});
		
		//
		// WFS conf type selection
		//
		var html = createWfsConfTypeSelectBox("new_" + WFS_CONF_TYPE_NAME_SUFFIX);
		$('#new_wfs_metadata_radio_conftype').html(html);

		var $radioButtons = $("input[name='new_" + WFS_CONF_TYPE_NAME_SUFFIX + "']");
		$radioButtons.eq(0).attr("checked", "checked");
		
		$radioButtons.click(function () {
			onSelectWfsConfType(this.value, "new_");
		});
	
		// show form
		$("#new_select_featuretype").css("display", "block");
	};

	// 3) WfsConfSelectBox: 
	//		WFS conf select box and type selector (use case: "edit WFS conf")

	var fillWfsConfSelectBox = function (wfsConfArray) {
		var $select = $("#edit_select_wfs_conf_select");
		$select.empty();
		var optionString = "<option>...</option>";		
		for (var i = 0; i < wfsConfArray.length; i++) {
			optionString += "<option value='" + wfsConfArray[i].id + "'>" + 
				wfsConfArray[i].id + " " + wfsConfArray[i].abstr + 
				"</option>";
		}
		$select.append(optionString);
		$select.change(function () {
			if (this.selectedIndex !== 0) {
				var wfsConfId = parseInt(this.value);
				var wfsConf = getWfsConfById(wfsConfId);
				onSelectWfsConf(wfsConf);
			}
		});
		$select.removeAttr("disabled");
		var currentWfsConf = $select.children("[selected]").val();

		//
		// WFS conf type selection
		//
		var html = createWfsConfTypeSelectBox("edit_" + WFS_CONF_TYPE_NAME_SUFFIX);
		$('#edit_wfs_metadata_radio_conftype').html(html);

		var $radioButtons = $("input[name='edit_" + WFS_CONF_TYPE_NAME_SUFFIX + "']");
		$radioButtons.eq(0).attr("checked", "checked");
		
		$radioButtons.click(function () {
			onSelectWfsConfType(this.value, "edit_");
		});
	};

	// 4) WfsConfForm:
	//		WFS featuretype and featuretype element configuration form 
	//		(both use cases)
	
	var fillWfsConfFormElement = function (column, $td, value) {
		var wfsConfType = ALL_WFS_CONF_ATTRIBUTES[column];

		switch (wfsConfType.type) {
			case "text" :
				if (value !== "") {
					$td.children(":first").val(value);
				}
				break;
			case "radio" :
				if (value === 1) {
					$td.children(":first").attr("checked", "checked");
				}
				break;
			case "checkbox" :
				if (value) {
					$td.children(":first").attr("checked", "checked");
				}
				break;
			case "textarea" :
				if (value !== "") {
					$td.children("DIV.helptext").children("textarea").val(value);
				}
				break;
			case "htmlTemplate" :
				if (value !== "") {
					$td.children("DIV.helptext").children("textarea").val(value);
				}
				break;
			case "select" :
				if (value !== "") {
					var $select = $td.children(":first");
					$select.children("option").each(function () {
						if (this.value == value) {
							$(this).attr("selected", "selected");
						}
					});
				}
				break;
		}		
	};
	
	var createWfsConfFormElement = function (column) {
		var wfsConfType = ALL_WFS_CONF_ATTRIBUTES[column];
		var html = "";

		switch (wfsConfType.type) {
			case "text":
				html += "<input type='text' ";
				if (typeof wfsConfType.def !== "undefined") {
					html += "value='" + wfsConfType.def + "' ";
				}
				else {
					html += "value='' ";
				}
				var size = wfsConfType.size;
				if (typeof wfsConfType.size === "undefined") {
					size = 4;
				}
				html += "size=" + wfsConfType.size + " ";
				html += ">";
				break;
			case "radio":
				html += "<input type='radio' name='wfs_conf_" + column + "' ";
				if (typeof wfsConfType.def !== "undefined" 
					&& wfsConfType.def === "checked") {
					html += "checked='checked'";
				}
				html += ">";
				break;
			case "checkbox":
				html += "<input type='checkbox' ";
				if (typeof wfsConfType.def !== "undefined" 
					&& wfsConfType.def === "checked") {
					html += "checked='checked'";
				}
				html += ">";
				break;
			case "textarea":
				html += "<div name='helptext' class='helptext'>";
				html += "<strong>Helptext<em></em>:</strong>";
				html += "<textarea cols='15' rows='1'></textarea><br />";
				html += "<input type='button' value='OK' />";
				html += "</div>";
				html += "<input type='button' value='Set' />";
				break;
			case "htmlTemplate":
				html += "<input type='button' value='Set' />";
				html += "<div name='helptext' class='helptext htmltemplate'>";
				html += "<strong>HTML Template<em></em>:</strong>";
				html += "<select>";
				html += "<option>...</option>";
				for (var attr in WFS_CONF_FORM_HTML_TEMPLATES) {
					var template = WFS_CONF_FORM_HTML_TEMPLATES[attr];
					html += "<option value='" + attr + "'>" + template.title + "</option>";
				}
				html += "</select><br />";
				html += "<textarea cols='15' rows='1'></textarea><br />";
				html += "<input type='button' value='OK' />";
				html += "</div>";
				break;
			case "select":
				html += "<select>";
				if (typeof wfsConfType.def === "object") {
					for (var key in wfsConfType.def) {
						html += "<option value='" + key + "'>" + wfsConfType.def[key] + "</option>";
					}
				}
				html += "</select>";
				break;
		}
		return html;
	};
	
	var getWfsConfFromHtml = function (prefix) {
			//
			// get the configuration of all featuretype elements
			//
			var ftElements = [];
			
			$("#" + prefix + "wfs_conf_form table:gt(0) tr:gt(0)").each(function () {
				var currentFtElement = {
					"id" : parseInt($(this).children("TD.wfs_conf_id").text(), 10),
					"NAME" : $(this).children("TD.wfs_conf_name").get(0).firstChild.data,
					"type" : $(this).children("TD.wfs_conf_name").children("div").text()
				};
				for (var attr in ALL_WFS_CONF_ATTRIBUTES) {
					var currentColumn = ALL_WFS_CONF_ATTRIBUTES[attr];
					var $td = $(this).children("TD.wfs_conf_" + attr);

					switch (currentColumn.type) {
						case "text" :
							currentFtElement[attr] = $td.children(":first").val();
							break;
						case "radio" :
							currentFtElement[attr] = $td.children("input:checked").size();
							break;
						case "checkbox" :
							currentFtElement[attr] = $td.children("input:checked").size();
							break;
						case "textarea" :
							currentFtElement[attr] = $td.children("DIV.helptext").children("textarea").val();
							break;
						case "htmlTemplate" :
							currentFtElement[attr] = $td.children("DIV.helptext").children("textarea").val();
							break;
						case "select" :
							currentFtElement[attr] = $td.children(":first").val();
							break;
					}
				}
				ftElements.push(currentFtElement);
			});	

			// WFS conf id (use case: edit wfs conf)
			var wfsConfId = parseInt($('#wfs_conf_id').val(), 10);
			if (isNaN(wfsConfId)) {
				wfsConfId = null;
			}

			// WFS id (use case: new wfs conf)
			var wfsId = null;
			if (wfsConfId === null) {
				wfsId = parseInt($("#new_select_wfs_select").val(), 10);
			}

			// WFS featuretype id (use case: new wfs conf)
			var wfsFeaturetypeId = null;
			if (wfsConfId === null) {
				wfsFeaturetypeId = parseInt($("#new_select_featuretype_select").val(), 10);
			}

			// WFS conf type
			var wfsConfType = null;
			var typeString = getWfsConfType(prefix + "wfs_metadata_radio_conftype");
			for (var i in WFS_CONF_TYPES) {
				if (WFS_CONF_TYPES[i].value === typeString) {
					wfsConfType = WFS_CONF_TYPES[i].id;
				}
			}

			//
			// return the configuration of the featuretype 
			//
			return {
				"id": wfsConfId,
				"wfsId" : wfsId,
				"featuretypeId" : wfsFeaturetypeId,
				"type" : wfsConfType,
				"abstr": $('#wfs_conf_title').val(),
				"description": $('#wfs_conf_description').val(),
				"label": $('#wfs_conf_label').val(),
				"labelId": $('#wfs_conf_label_id').val(),
				"button": $('#wfs_conf_button').val(),
				"buttonId": $('#wfs_conf_button_id').val(),
				"style": $('#wfs_conf_style').val(),
				"buffer": $('#wfs_conf_buffer').val(),
				"resStyle": $('#wfs_conf_resultstyle').val(),
				"elementArray": ftElements
			};
		
	};
	
	var fillWfsConfForm = function (options) {
		if (typeof options !== "object") {
			return;
		}
		
		var prefix, ft = null, wfsConf = null, wfsConfType = null, elementArray = null;

		if (typeof options.ft !== "undefined") {
			ft = options.ft;
			elementArray = ft.elementArray;
			prefix = "new_";
		}
		if (typeof options.wfsConf !== "undefined") {
			wfsConf = options.wfsConf;
			elementArray = wfsConf.elementArray;
			prefix = "edit_";
		}

		wfsConfType = getWfsConfType(prefix + "wfs_metadata_radio_conftype");

		if (wfsConfType === null || elementArray === null) {
			alert("An error occured!");
			return;
		}

		//
		// set WFS Conf metadata
		//
		if (wfsConf !== null) {
			$("#wfs_conf_id").val(wfsConf.id);
			$("#wfs_conf_title").val(wfsConf.abstr);
			$("#wfs_conf_description").val(wfsConf.description);
			$("#wfs_conf_label").val(wfsConf.label);
			$("#wfs_conf_label_id").val(wfsConf.labelId);
			$("#wfs_conf_button").val(wfsConf.button);
			$("#wfs_conf_button_id").val(wfsConf.buttonId);
			$("#wfs_conf_style").val(wfsConf.style);
			$("#wfs_conf_buffer").val(wfsConf.buffer);
			$("#wfs_conf_resultstyle").val(wfsConf.resStyle);
		}	
	
		//
		// create WFS conf element form
		//

//		setTimeout(function () {
			
			var table = "";
			for (var i = 0; i < elementArray.length; i++) {
				var el = elementArray[i];
	
				var row = "";
				row += "<td class='wfs_conf_id'>" + el.id + "</td>";
				row += "<td class='wfs_conf_name'>" + el.name + "<br><div style='font-size:10'>" + 
						el.type + "</div></td>";
				
				for (var column in ALL_WFS_CONF_ATTRIBUTES) {
					row += "<td style='display:none' class='wfs_conf_" + column + "'>" + 
						createWfsConfFormElement(column) + 
						"</td>";
				}
				table += "<tr id='" + prefix + "wfs_conf_form_tr_" + el.id + "'>" + row + "</tr>";
			}
			$("#" + prefix + "wfs_conf_form table:gt(0)").append(table);
//		}, 0);
//		console.timeEnd("create WFS conf element form");

//		console.time("pre-fill WFS conf element form");
		//
		// pre-fill WFS conf element form
		//
//		setTimeout(function () {

			if (wfsConf !== null) {
				for (var i = 0; i < elementArray.length; i++) {
					var el = elementArray[i];
					var $tr = $("#" + prefix + "wfs_conf_form_tr_" + el.id);
					for (var attr in el) {
						for (var column in ALL_WFS_CONF_ATTRIBUTES) {
							if (attr === column) {
								var $td = $tr.children("TD.wfs_conf_" + column);
								fillWfsConfFormElement(column, $td, el[attr]);
								break;
							}
						}
					}
				}		
			}
//		}, 0);
//		console.timeEnd("pre-fill WFS conf element form");
		
//		console.time("behaviour");
		// ----------------------------------------
		// add behaviour
		// ----------------------------------------
//		setTimeout(function () {

			var $helptext = $("DIV.helptext");
			//
			// Edit button of textarea
			//
			$helptext.siblings("input").click(function () {
				// close all open helptexts
				$helptext.hide().siblings("input").removeAttr("disabled");			
				// open this helptext
				$(this).siblings('div').show().siblings('input').attr('disabled', 'disabled');
			}).each(function () {
				// if a text is present, change the button 
				// value from "Set" to "Edit"
				var $this = $(this);
				var text = $this.siblings("div").children("textarea").val();
				if (text !== "") {
					$this.val("Edit");
				}
			});
	
			//
			// OK button of textarea
			//
			$helptext.children("input").click(function () {
				$helptext.hide().siblings("input").removeAttr("disabled");
				var $this = $(this);
				if ($this.siblings("textarea").val() !== "") {
					$this.parent().siblings("input").val("Edit");
				}	
				else {
					$this.parent().siblings("input").val("Set");
				}		
			});
	
			//
			// select box for HTML templates
			//
			var $htmlTemplate = $("DIV.htmltemplate");
			$htmlTemplate.each(function () {
	
				// if the confirm message after the onchange event
				// is not confirmed, the old index needs to be preserved
				var oldSelectedIndex = 0;	
				
				$(this).children("select").click(function () {
					oldSelectedIndex = this.selectedIndex;
				}).change(function () {
					var $textarea = $(this).siblings("textarea");
		
					if (this.selectedIndex === 0) {
						if ($textarea.val() === "" || confirm("Delete existing HTML template?")) {
							$textarea.val("");
						}
						else {
							// action canceled
							this.selectedIndex = oldSelectedIndex;
						}
						return;
					}
		
					for (var attr in WFS_CONF_FORM_HTML_TEMPLATES) {
						if (attr !== this.value) {
							continue;
						}
						if ($textarea.val() === "" || confirm("Overwrite existing HTML template?")) {
							var template = WFS_CONF_FORM_HTML_TEMPLATES[attr];
							$textarea.val(template.code);
						}
						else {
							// action canceled
							this.selectedIndex = oldSelectedIndex;
						}
						break;
					}
				});
			});
	
			//
			// enable / disable operator column 
			// if search column is set / not set
			//
			$("TD.wfs_conf_search").children("input").each( function () {
				var $this = $(this);
				var oldSelectedIndex = 0;
	
				var $operatorSelect = $this.parent().siblings("TD.wfs_conf_operator").children("select");
				if (!$this.attr("checked")) {
					$operatorSelect.attr("disabled", "disabled");
				}
	
				$this.click(function () {
					var $this = $(this);
					var $operatorSelect = $this.parent().siblings("TD.wfs_conf_operator").children("select");
					if ($this.attr("checked")) {
						$operatorSelect.removeAttr("disabled");
						$operatorSelect.get(0).selectedIndex = oldSelectedIndex;
					}
					else {
						oldSelectedIndex = $operatorSelect.get(0).selectedIndex;
						$operatorSelect.get(0).selectedIndex = 0;
						$operatorSelect.attr("disabled", "disabled");
					}
				});
			});
	
			//
			// delete obsolete entries in operator column
			//
			$("TD.wfs_conf_operator").children("select").each(function () {
				var type = $(this).parent().siblings("TD.wfs_conf_name").children("div").text();
				
				for (var currentType in WFS_CONF_FORM_OPERATORS) {
					if (type !== currentType) {
						continue;
					}
					
					var optionArray = WFS_CONF_FORM_OPERATORS[currentType];
					$(this).children("option:gt(0)").each(function () {
						var found = false;
						for (var i in optionArray) {
							if (optionArray[i] === this.value) {
								found = true;
								break;
							}
						}
						if (!found) {
							$(this).remove();
						}
					});
				}
			});
	//		console.timeEnd("behaviour");
	
	//		console.time("show and hide columns");
	
			//
			// show and hide appropriate columns	
			//
			onSelectWfsConfType(wfsConfType, prefix);
	//		console.timeEnd("show and hide columns");
	
			//
			// save WFS conf
			//
			$("#wfs_conf_save").click(function () {
	
				var wfsConf = getWfsConfFromHtml(prefix);
				if (wfsConf.id !== null) {
					updateWfsConfInDb(wfsConf);
					var index = getWfsConfIndex(wfsConf.id);
					if (index !== null) {
						wfsConfArray[index] = wfsConf;
					}
				}
				else {
					insertWfsConfIntoDb(wfsConf);
				}
				return;
			});
		
			$("#" + prefix + "wfs_conf_form").css("display", "block");		
//		}, 0);
	};
	
	var loadWfsConfForm = function (options) {
		if (typeof options !== "object") {
			return;
		}

		var setHtml = function (data) {
			$("#" + options.target).html(data);		

			$("#" + options.target + " th").each(function () {
				var text = $(this).text();
				$(this).html("<img alt='" + text + "' src='../php/createImageFromText.php?text=" + text + "&angle=90'>");
			});
		};
		
		$("#" + options.empty).empty();
		
		// load the HTML via AJAX and cache it
		if (!WFS_CONF_FORM) {
			$.get("../php/mod_wfs_conf_featuretype_elements.html", {}, function(html){
				if (html) {
					WFS_CONF_FORM = html;
					setHtml(WFS_CONF_FORM);
					options.callback();
				}
				else {
					alert("Unable to read WFS conf HTML file!");
					return;
				}
			});
			return;
		}
	
		//
		// HTML has already been loaded
		//
		// remove existing featuretype elements first
//		$("#" + options.target + " table:gt(0) tr:gt(0)").remove();
		setHtml(WFS_CONF_FORM);
		options.callback();
	};

	// ---------------------------
	// FORM BEHAVIOUR
	// ---------------------------

	var onSelectWfs = function (wfs) {
		setTimeout(function () {
			$("#new_wfs_conf_form").empty();
			fillWfsMetadataForm(wfs);
		}, 0);
	};
	
	var onSelectFeaturetype = function (ft) {
		isBusy.trigger();
		setTimeout(function () {
			loadWfsConfForm({
				"callback" : function () {
					fillWfsConfForm({
						"ft": ft
					});
					isNotBusy.trigger();
				},
				"target" : "new_wfs_conf_form",
				"empty" : "edit_wfs_conf_form"
			});
		}, 0);
	};
	
	var onSelectWfsConf = function (wfsConf) {
		isBusy.trigger();
		setTimeout(function () {
			for (var i in WFS_CONF_TYPES) {
				if (WFS_CONF_TYPES[i].id === wfsConf.type) {
					var selector = "input[name='edit_" + 
						WFS_CONF_TYPE_NAME_SUFFIX + "']" + 
						"[value='" + WFS_CONF_TYPES[i].value + "']";
					$(selector).attr("checked", "checked");
					break;
				}
			}
	
			loadWfsConfForm({
				"callback" : function () {
					fillWfsConfForm({
						"wfsConf": wfsConf
					});
					isNotBusy.trigger();
				},
				"target" : "edit_wfs_conf_form",
				"empty" : "new_wfs_conf_form"
			});
		}, 0);
	};
	
	/**
	 * Hides and shows columns in the WFS conf form
	 * according to the selected WFS conf type
	 * 
	 * @param {String} wfsConfType
	 */
	var onSelectWfsConfType = function (wfsConfType, prefix) {
		var numberOfFeatureTypeElements = $("#" + prefix + "wfs_conf_form table:gt(0) tr:gt(0)").size();

		// no featuretype elements in list, abort
		if (numberOfFeatureTypeElements === 0) {
			return;
		}
		isBusy.trigger();
		setTimeout(function () {
			//
			// hide all columns except id and name
			//
			var $table = $("#" + prefix + "wfs_conf_form table:gt(0)");
			$("th", $table).hide();
			$("td", $table).hide();
			$(".wfs_conf_id", $table).show();
			$(".wfs_conf_name", $table).show();
			
			//
			// display columns of current WFS conf type
			//
			for (var i = 0; i < WFS_CONF_TYPES.length; i++) {
				if (WFS_CONF_TYPES[i].value !== wfsConfType) {
					continue;
				}
				for (var j = 0; j < WFS_CONF_TYPES[i].columns.length; j++) {
					var currentColumn = WFS_CONF_TYPES[i].columns[j];
					$(".wfs_conf_" + currentColumn, $table).show();
				}
				break;
			}
			isNotBusy.trigger();
		}, 0);
	};
	
	//
	// constructor
	//
	
	var isBusy = new parent.MapbenderEvent();
	var isNotBusy = new parent.MapbenderEvent();
	
	isBusy.register(function () {
		$("#ajaxoverlay").css("display", "block");
	});

	isNotBusy.register(function () {
		$("#ajaxoverlay").css("display", "none");
	});

	if (typeof options.$container === "undefined" || options.$container.size() === 0) {
		alert("WFS conf: Could not find container.");
		return;
	}
	var tabsHtml = "<ul>" + 
		"<li><a href='mod_wfs_conf_new.html'>" + 
		"Create new WFS configuration" + 
		"</a></li>" + 
		"<li><a href='mod_wfs_conf_edit.html'>" + 
		"Edit existing WFS configuration" + 
		"</a></li>" + 
		"</ul>";

	options.$container.html(tabsHtml).tabs({
		"load" : function (event, ui) {
			// new conf
			if (ui.index === 0 && wfsArray.length > 0) {
				fillWfsSelectBox(wfsArray);
			}
			// edit conf
			else if (ui.index === 1 && wfsConfArray.length > 0) {
				fillWfsConfSelectBox(wfsConfArray);
			}
		}
	});

	isBusy.trigger();
	setTimeout(function () {
		getWfsFromDb({
			"callback": function(wfsArray) {
				fillWfsSelectBox(wfsArray);
				isNotBusy.trigger();
			}
		});
		getWfsConfsFromDb({
			"callback": function(wfsConfArray){
				fillWfsConfSelectBox(wfsConfArray);
				isNotBusy.trigger();
			}
		}, 0);
	});
};
