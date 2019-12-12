/**
 * Package: printPDF
 *
 * Description:
 * Mapbender print PDF with PDF templates module.
 * 
 * Files:
 *  - http/plugins/mb_print.php
 *  - http/print/classes
 *  - http/print/printFactory.php
 *  - http/print/printPDF_download.php
 *  - lib/printbox.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','printPDF',
 * > 2,1,'pdf print','Print','div','','',1,1,2,2,5,'',
 * > '<div id="printPDF_working_bg"></div><div id="printPDF_working"><img src="../img/indicator_wheel.gif" style="padding:10px 0 0 10px">Generating PDF</div><div id="printPDF_input"><form id="printPDF_form" action="../print/printFactory.php"><div id="printPDF_selector"></div><div class="print_option"><input type="hidden" id="map_url" name="map_url" value=""/><input type="hidden" id="legend_url" name="legend_url" value=""/><input type="hidden" id="overview_url" name="overview_url" value=""/><input type="hidden" id="map_scale" name="map_scale" value=""/><input type="hidden" name="measured_x_values" /><input type="hidden" name="measured_y_values" /><br /></div><div class="print_option" id="printPDF_formsubmit"><input id="submit" type="submit" value="Print"><br /></div></form><div id="printPDF_result"></div></div>',
 * > 'div','../plugins/mb_print.php',
 * > '../../lib/printbox.js,../extensions/jquery-ui-1.7.2.custom/development-bundle/external/bgiframe/jquery.bgiframe.js,../extensions/jquery.form.min.js',
 * > 'mapframe1','','http://www.mapbender.org/index.php/Print');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'printPDF', 
 * > 'mbPrintConfig', '{"Standard": "mapbender_template.json"}', '' ,'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'printPDF',
 * > 'unlink', 'true', 'delete print pngs after pdf creation' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'printPDF',
 * > 'logRequests', 'false', 'log wms requests for debugging' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'printPDF',
 * > 'logType', 'file', 'log mode can be set to file or db' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'printPDF',
 * > 'timeout', '90000', 'define maximum milliseconds to wait for print request finished' ,'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'body',
 * > 'print_css', '../css/print_div.css', '' ,'file/css');
 * 
 * Help:
 * http://www.mapbender.org/PrintPDF_with_template
 *
 * Maintainer:
 * http://www.mapbender.org/User:Michael_Schulz
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * mbPrintConfig      - *[optional]* object with name and filename of template,
 * 							like 	{
 * 										"Standard": "a_template.json",
 * 										"Different": "another_template.json"
 * 									}
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var myTarget = options.target ? options.target[0] : "mapframe1";
var myId = options ? options.id : "printPDF";

var mbPrintConfig = options.mbPrintConfig;

/* the array of json print config files */

if (typeof mbPrintConfig === "object") {
	mbPrintConfigFilenames = [];
	mbPrintConfigTitles = [];
	for (var i in mbPrintConfig) {
		mbPrintConfigFilenames.push(mbPrintConfig[i]);
		mbPrintConfigTitles.push(i);
	}
}
if (typeof mbPrintConfigFilenames === "undefined") {
	mbPrintConfigFilenames = ["mapbender_template.json"];
}

if (typeof mbPrintConfigTitles === "undefined") {
	mbPrintConfigTitles = ["Default"];
}


var mbPrintConfigPath = "../print/";


/* ------------- printbox addition ------------- */

var PrintPDF = function (options) {

	var that = this;
	
	/**
 	 * Property: actualConfig
	 * 
	 * object, holds the actual configuration after loading the json file
	 */
	var actualConfig;

	/**
 	 * constructor
	 */
	eventInit.register(function () {
		mod_printPDF_init();
	});
	
	/**
 	 * Property: printBox
	 * 
	 * the movable printframe
	 */	
	var printBox = null;
	
	eventAfterMapRequest.register(function () {
		if (printBox !== null) {
			printBox.repaint();
		}
	});
	/**
 	 * Method: createPrintBox
	 * 
	 * creates a printBox in the current view, calculates the scale 
	 * (tbd. if not set from the config) so that the printbox fits in the mapframe.
	 * Width and height are taken from the configuration.
	 */
	this.createPrintBox = function () {
		size = "A4";
		//document.form1.size.value = size;
		format = "portrait";
		var w, h;
		//validate();
		var map = getMapObjByName(myTarget);
		var map_el = map.getDomElement();
		var jqForm = $("#"+myId+"_form");
		var $scaleInput = $("#scale");

		if (printBox !== null) {
        	destroyPrintBox();
			jqForm[0].scale.value = "";
			jqForm[0].coordinates.value = "";
			jqForm[0].angle.value = "";
		}
		else {
        	printBox = new Mapbender.PrintBox({
				target : myTarget,
				printWidth : getPDFMapSize("width")/10,
				printHeight : getPDFMapSize("height")/10,
				scale : $scaleInput.size() > 0 && !isNaN(parseInt($scaleInput.val(), 10)) ? 
					parseInt($scaleInput.val(), 10) : 
					Math.pow(10, Math.floor(Math.log(map.getScale())/Math.LN10)),
				afterChangeAngle : function (obj) {
					if (typeof(obj) == "object") {
						if (typeof(obj.angle) == "number") {
                            if(typeof(jqForm[0].angle) != "undefined") {
								jqForm[0].angle.value = obj.angle;
	                        }
						}
						if (obj.coordinates) {
                           	if(typeof(jqForm[0].coordinates) != "undefined") {
								jqForm[0].coordinates.value = String(obj.coordinates);
                            }
						}
					}
				},
				afterChangeSize : function (obj) {
					if (typeof(obj) == "object") {
						if (obj.scale) {
    						if($("#scale").is("input")) {
    							jqForm[0].scale.value = parseInt(obj.scale / 10, 10) * 10;
    						}
    						else {
    							$("#scale .addedScale").remove();
    							$("#scale").append("<option selected class='addedScale' value='"+parseInt(obj.scale / 10, 10) * 10+"'>1 : " + parseInt(obj.scale / 10, 10) * 10 + "</option>");
    						}
						}
						if (obj.coordinates) {
	                        if(typeof(jqForm[0].coordinates) != "undefined") {
								jqForm[0].coordinates.value = String(obj.coordinates);
                            }
						}
					}
				}
			});
			printBox.paintPoints();
			printBox.paintBox();

		}
	};

	/**
 	 * Method: getPDFMapSize
	 * 
	 * checks the actual config for the size w/h values.
	 *
	 * Parameters:
	 * key      - string, the key which value to retrieve (currently width or height)
	 */	
	var getPDFMapSize = function (key) {
		for (var page in actualConfig.pages) {
			for (var pageElement in actualConfig.pages[page].elements) {
				if (actualConfig.pages[page].elements[pageElement].type == "map") {
					return actualConfig.pages[page].elements[pageElement][key];
				}
			}
		} 
	};

	/**
 	 * Method: destroyPrintBox
	 * 
	 * removes an existing printBox.
	 */	
	var destroyPrintBox = function () {
		if (printBox) {
			printBox.destroy();
                        printBox = null;
		}
	};
	
	/**
 	 * Method: mod_printPDF_init
	 * 
	 * initializes the print modules, generates template chooser and loads first configuration.
	 */	
	var mod_printPDF_init = function () {
		/* first we'd need to build the configuration selection */
		buildConfigSelector();	
		/* second we'd need to read the json configuration */
		that.loadConfig(mbPrintConfigFilenames[0]);
		/* than we need the translation of the print button */
		$("#submit").val("<?php echo htmlentities(_mb("print"), ENT_QUOTES, "UTF-8");?>");

                //show printBox for first entry in printTemplate selectbox
                $("."+myId+"-dialog").bind("dialogopen", function () {
                    printObj.createPrintBox();
                });

                //destroy printBox if printDialog is closed
                $("."+myId+"-dialog").bind("dialogclose", function () {
                    destroyPrintBox();
                });
	};

	/**
 	 * Method: loadConfig
	 * 
	 * GETs the config, build corresponding form, remove an existing printBox
	 */		
	this.loadConfig = function (configFilename, callback) {
        	// the dataType to $.get is given explicitely, because there were instances of Mapbender that were returning 
		// either json or a string, which trips up $.parseJSON which was being used in the callback
		$.get(mbPrintConfigPath + configFilename, function(json, status){
		    actualConfig = json;
		    buildForm(); 
		    hookForm();
			if (typeof callback === "function") {
				printBox = null;
                                callback();
                        }
		},"json");
		destroyPrintBox();
                
	};

	/**
 	 * Method: hookForm
	 * 
	 * utility method to connect the form plugin to the print form.
	 */	
	var hookForm = function () {
		var o = {
	   		url:			'../print/printFactory.php?e_id='+myId,
	   		type:			'post',
	   		dataType:		'json',
	   		beforeSubmit: 	validate,
	   		success:    	showResult,
                        timeout:        options.timeout ? options.timeout : 90000,
                        error:          function() {
                                showHideWorking("hide");
                                alert("An error occured or timeout of " + Math.round(options.timeout/1000) + " seconds reached. Print was aborted.");
                        }
		};
		$("#"+myId+"_form").ajaxForm(o);
	};
	
	/**
	 * Change status of the working elements. These should begin with "$myId_working" 
	 *
	 * @param {String} newStatus either "hide or "show"
	 */
	var showHideWorking = function (newStatus) {
		if (newStatus=="hide") {
	    	$("[id^='"+myId+"_working']").hide();
		}
	    else {
	    	$("[id^='"+myId+"_working']").show();	
		}
	};
	
	/**
	 * update form values helper function
	 *
	 */
	var updateFormField = function (formData, key, value) {
		for(var j=0; j < formData.length; j++){
			if (formData[j].name == key) {
				formData[j].value = value;
				break;
			}
		}	
	};

	var getCurrentResolution = function (type) {

		// default resolution is 72 dpi
		var dpi = 72;

		// set resolution according to map configuration in template
		for (var i in actualConfig.pages) {
			var page = actualConfig.pages[i];
			for (var j in page.elements) {
				var el = page.elements[j];
				if (type === el.type && typeof el.res_dpi === "number") {
					dpi = el.res_dpi;
				}
			}
		}
		// set resolution according to resolution select box (if present)

		// check if hq print is requested
		var resolutionControl = null;
		for (var i in actualConfig.controls) {
			var c = actualConfig.controls[i];
			try {
				for (var j in c.pageElementsLink) {
					if (c.pageElementsLink[j] === "res_dpi") {
						resolutionControl = typeof c.id === "string" &&
							c.id.length > 0 ? $("#" + c.id) : null;
					}
				}
			}
			catch (e) {
			}
		}
		if (resolutionControl !== null && resolutionControl.size() === 1) {
			dpi = resolutionControl.val();
		}
		return parseInt(dpi, 10);
	};

	var replaceMapFileForHighQualityPrint = function (currentMapUrl, type) {
		var dpi = getCurrentResolution(type);
		// replace map file with hq map file (if configured)
		var hqmapfiles = $.isArray(options.highqualitymapfiles) ?
			options.highqualitymapfiles : [];
		for (var i = 0; i < hqmapfiles.length; i++) {
			var exp = new RegExp(hqmapfiles[i].pattern);
			if (hqmapfiles[i].pattern && typeof currentMapUrl === "string" && currentMapUrl.match(exp)) {
				// check if mapping in current resolution exists
				var resolutions = hqmapfiles[i].replacement;
				var resolutionExists = false;
				for (var r in resolutions) {
					if (parseInt(r, 10) === dpi) {
						resolutionExists = true;
					}
				}
				if (resolutionExists) {
					// replace with hqmapfile
					var hqmapfile = resolutions[dpi];
					currentMapUrl = currentMapUrl.replace(exp, hqmapfile);
				}
			}
		}
		return currentMapUrl;
	};

	/**
	 * Validates and updates form data values.
	 * Adds the elements before the submit button.
	 *
	 * @see jquery.forms#beforeSubmitHandler
	 */
	var validate = function (formData, jqForm, params) {
		showHideWorking("show");
		
		// map urls
		var ind = getMapObjIndexByName(myTarget);
		var f = jqForm[0];
		f.map_url.value = '';
		
		var scale = f.scale.value || mb_mapObj[ind].getScale();
		scale = parseInt(scale, 10);
		
		var legendUrlArray = [];
		f.overview_url.value = '';
		for(var i=0; i < mb_mapObj[ind].wms.length; i++){
			var currentWms = mb_mapObj[ind].wms[i];
			if(mb_mapObj[ind].wms[i].gui_wms_visible > 0){
				if (mb_mapObj[ind].wms[i].mapURL != false && mb_mapObj[ind].wms[i].mapURL != 'false' && mb_mapObj[ind].wms[i].mapURL != ''){   
					if(f.map_url.value != ""){
						f.map_url.value += '___';
					}      
					var currentMapUrl = mb_mapObj[ind].getMapUrl(i, mb_mapObj[ind].getExtentInfos(), scale);
					
					currentMapUrl = replaceMapFileForHighQualityPrint(currentMapUrl, "map");
					f.map_url.value += currentMapUrl

					var wmsLegendObj = [];
					
					var layers = currentWms.getLayers(mb_mapObj[ind], scale);
					for (var j = 0; j < layers.length; j++) {
						var currentLayer = currentWms.getLayerByLayerName(layers[j]);
						// TODO: add only visible layers
						var isVisible = (currentLayer.gui_layer_visible === 1);
						var hasNoChildren = (!currentLayer.has_childs);
						if (isVisible && hasNoChildren){
							var layerLegendObj = {};
							layerLegendObj.name = currentLayer.layer_name;
							layerLegendObj.title = currentWms.getTitleByLayerName(currentLayer.layer_name);
							var layerStyle = currentWms.getCurrentStyleByLayerName(currentLayer.layer_name);
							if (layerStyle === false || layerStyle === "") {
								layerStyle = "default";
							}
							layerLegendObj.legendUrl = currentWms.getLegendUrlByGuiLayerStyle(currentLayer.layer_name, layerStyle);
							if (layerLegendObj.legendUrl !== false) {
								wmsLegendObj.push(layerLegendObj);
							}
						}
					}
					if (wmsLegendObj.length > 0) {
						var tmpObj = {};
						tmpObj[currentWms.wms_currentTitle] = wmsLegendObj;
						legendUrlArray.push(tmpObj);
					
					}
				}
			}
		}
		var legendUrlArrayJson =  $.toJSON(legendUrlArray);
		updateFormField(formData, "legend_url", legendUrlArrayJson);
		updateFormField(formData, "map_url", f.map_url.value);
		updateFormField(formData, "scale", scale);
		
		//overview_url
		var ind_overview = getMapObjIndexByName('overview');
		if(mb_mapObj[ind_overview].mapURL != false ){
		    var overviewUrl = mb_mapObj[ind_overview].mapURL;
			overviewUrl = $.isArray(overviewUrl) ? overviewUrl[0] : overviewUrl;

		    f.overview_url.value = replaceMapFileForHighQualityPrint(overviewUrl, "overview");

			updateFormField(formData, "overview_url", f.overview_url.value);
		}
		
		updateFormField(formData, "map_scale", mb_getScale(myTarget));
		// write the measured coordinates
		if (typeof(mod_measure_RX) !== "undefined") {
			var tmp_x = '';
			var tmp_y = '';
			for(i = 0; i < mod_measure_RX.length; i++) {
				if(tmp_x != '') {
					tmp_x += ',';
				}
				tmp_x += mod_measure_RX[i];
			}
			for(i = 0; i < mod_measure_RY.length; i++) {
				if(tmp_y != '') {
					tmp_y += ',';
				}
				tmp_y += mod_measure_RY[i];
			}
			updateFormField(formData, "measured_x_values", tmp_x);
			updateFormField(formData, "measured_y_values", tmp_y);
		}

                //write the permanent highlight image, if defined

                var markers = [];
                var pixelpos= null;
                var realpos= [null,null];
                var feature = null;

                if(typeof GlobalPrintableGeometries != "undefined"){

                    for(var idx = 0; idx < GlobalPrintableGeometries.count(); idx++ ) {
                        feature = GlobalPrintableGeometries.get(idx);
                        realpos = feature.get(0).get(0);
                        path =  feature.e.getElementValueByName("Mapbender:icon");
                        // The offsets are set to 40, meaning that images will need to be 80 x 80 with the tip of the marker- pixel being in the middle
                        markers.push({
                            position: [realpos.x,realpos.y],
                            path: path,
                            width: 40 * 2,
                            height: 40 * 2,
                            offset_x: 40,
                            offset_y: 40 
                        });
                    }
                    var permanentImage = JSON.stringify(markers);
                    updateFormField(formData, "mypermanentImage", permanentImage);

                }
		
		if (f.map_url.value!="") {
			//return true;
		} else {
		    showHideWorking("hide");
			return false;
		}
	};

	/**
 	 * Method: showResult
	 * 
	 * load the generated PDF from the returned URL as an attachment, 
	 * that triggers a download popup or is displayed in PDF plugin.
	 */		
	var showResult = function (res, text) {
		if (text == 'success') {
			var $downloadFrame = $("#" + myId + "_frame");
			if ($downloadFrame.size() === 0) {
				$downloadFrame = $(
					"<iframe id='" + myId + "_frame' name='" + 
					myId + "_frame' width='0' height='0' style='display:none'></iframe>"
				).appendTo("body");
			}
			if ($.browser.msie) {
 				$('<div></div>')
					.attr('id', 'ie-print')
					.append($('<p>Ihr PDF wurde erstellt und kann nun heruntergeladen werden:</p>'))
					.append($('<a>Zum Herunterladen hier klicken</a>')
						.attr('href', stripslashes(res.outputFileName))
						.click(function() { $(this).parent().dialog('destroy');	}))
					.appendTo('body')
					.dialog({
						title: 'PDF-Druck'
					});
			}
			else {
				window.frames[myId + "_frame"].location.href = 
					stripslashes(res.outputFileName);
			}
			showHideWorking("hide");
                        //remove printbox after successful print
                        destroyPrintBox();
		}
		else {
			/* something went wrong */
			$("#"+myId+"_result").html(text);
		}
	};
	
	/**
	 * Generates form elements as specified in the config controls object.
	 * Adds the elements before the submit button.
	 * 
	 * @param {Object} json the config object in json
	 */
	var buildForm = function () {
		$(".print_option_dyn").remove();
		var str = "";
		for (var item in actualConfig.controls) {
			var element = actualConfig.controls[item];
			var element_id = myId + "_" + element.id;
			if (element.type != "hidden") {
				str += '<div class="print_option_dyn">\n';	
				str += '<label class="print_label" for="'+element.id+'">'+element.label+'</label>\n';
			} else {
				str += '<div class="print_option_dyn" style="display:none;">\n';	
			}
			
			if(element.maxCharacter) {
				var maxLength = 'maxlength="'+element.maxCharacter+'"';
				
			}
			else {
				var maxLength = "";
			}
			switch (element.type) {
				case "text":
					str += '<input type="'+element.type+'" name="'+element.id+'" id="'+element.id+'" size="'+element.size+'" '+maxLength+'><br>\n';
					break;
				case "hidden":
					str += '<input type="'+element.type+'" name="'+element.id+'" id="'+element.id+'">\n';
					break;				
				case "textarea":
					str += '<textarea id="'+element.id+'" name="'+element.id+'" size="'+element.size+'" '+maxLength+'></textarea><br>\n';
					break;				
				case "select":
					str += '<select id="'+element.id+'" name="'+element.id+'" size="1">\n';
					for (var option_index in element.options) {
						option = element.options[option_index];
						str += '<option value="'+option.value+'">'+option.label+'</option>\n';
					}
					str += '</select><br>\n';
					break;
			}
			str += '</div>\n';
		}
		if (str) {
			$('textarea[maxlength]').live('keyup change', function() {
              	var str = $(this).val()
              	var mx = parseInt($(this).attr('maxlength'))
              	if (str.length > mx) {
                 	$(this).val(str.substr(0, mx))
                 	return false;
              	}
            });
			$("#" + myId + "_formsubmit").before(str);
			if($("#scale").is("input")) {
    			$("#scale").keydown(function (e) {
    				if (e.keyCode === 13) {
    					return false;
    				}
    			}).keyup(function (e) {
    				if (e.keyCode === 13) {
    					return false;
    				}
                                    
                                    var scale = parseInt(this.value, 10);
    				if (isNaN(scale) || typeof printBox === "undefined") {
    					return true;
    				}
    
                                    if(scale < 10) {
                                        return true;
                                    }
    				printBox.setScale(scale);
    				return true;
    			});
			}
			else {
				$("#scale").change(function (e) {
                var scale = parseInt(this.value, 10);
				if (isNaN(scale) || typeof printBox === "undefined") {
					return true;
				}

                if(scale < 10) {
	                return true;
	            }
					printBox.setScale(scale);
					return true;
				});
			}

			$("#angle").keydown(function (e) {
				if (e.keyCode === 13) {
					return false;
				}
			}).keyup(function (e) {
				if (e.keyCode === 13) {
					return false;
				}
				var angle = parseInt(this.value, 10);
				if (isNaN(angle) || typeof printBox === "undefined") {
					return true;
				}
				printBox.setAngle(angle);
				return true;
			});
		}
	};
	
	/**
	 * Generates the configuration select element from the gui element vars
	 * mbPrintConfigFilenames and mbPrintConfigTitles
	 */
	var buildConfigSelector = function () {
		var str = "";
		str += '<label class="print_label" for="printPDF_template">Vorlage</label>\n';
		str += '<select id="printPDF_template" name="printPDF_template" size="1">\n';
		for (var i = 0; i < mbPrintConfigFilenames.length; i++) {
			str += '<option value="'+mbPrintConfigFilenames[i]+'">'+mbPrintConfigTitles[i]+'</option>\n';
		}
		str += '</select><img id="printPDF_handle" src="../print/img/shape_handles.png" title="<?php echo htmlentities(_mb("use print box"), ENT_QUOTES, "UTF-8");?>">\n';
		if (str) {
			$("#printPDF_selector").append(str).find("#printPDF_template").change(function () {
				printObj.loadConfig(mbPrintConfigFilenames[this.selectedIndex], function () {
				    printObj.createPrintBox()
				});			
			});
                       
			$("#printPDF_handle").click(function () {
				printObj.createPrintBox();
			});
			$("#printPDF_working").bgiframe({ 
				src: "BLOCKED SCRIPT'&lt;html&gt;&lt;/html&gt;';",
				width: 200,
				height: 200
			});
		}
	};
	
	var stripslashes = function ( str ) {
	    return (str+'').replace(/\0/g, '0').replace(/\\([\\'"])/g, '$1');
	};

};

var printObj = new PrintPDF(options);

