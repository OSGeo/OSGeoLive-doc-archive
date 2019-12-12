/**
 * Package: AddWmsFromTree
 *
 * Description:
 * Add a WMS from a container. The WMS are displayed in a customized tree 
 * (if one exists)
 * 
 * Files:
 *  - http/javascripts/mod_addWMSfromTree.js
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<app_id>',
 * > 'addWMSfromTree',2,1,'Add a WMS from a container. The WMS are displayed in a customized tree (if one exists)',
 * > 'Add WMS from tree','div','','',1,1,1,1,5,'overflow:scroll','','div',
 * > '../javascripts/mod_addWMSfromTree.js',
 * > '../extensions/jquery-ui-1.7.2.custom.min.js,../../lib/customTreeModel.js,../../lib/customTreeController.js',
 * > 'mapframe1','mapframe1','');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES ('<app_id>', 'addWMSfromTree', 
 * > 'addwms_showWMS', '<max_number_of_layers>', '0 : do not make layer visible; n > 0 : make visible if #layers < n' ,'var');
 * > 
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES ('<app_id>', 'addWMSfromTree', 
 * > 'applicationName', '<container_name>', 'Use the custom tree of this container' ,'var');
 * > 
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES ('<app_id>', 'body', 
 * > 'custom_tree_css', '../css/customTree.css', '' ,'var');
 *
 * Help:
 * http://www.mapbender.org/AddWmsFromTree
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * addwms_showWMS		- Integer. 	0 : do not make layer visible; 
 *						- 			n > 0 : make visible if #layers < n
 * applicationName		- String. Use the custom tree of this container
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

(function () {
	
	var addwms_showWMS = typeof options.addwms_showWMS !== "undefined" ? 
		options.addwms_showWMS : 0;
	var applicationName = options.applicationName;

	if (typeof applicationName === "undefined") {
		new Mb_exception("No applicationName given in " + options.id);
	}
	else {
	
		var loadWmsAndZoomCallback = function (opt) {
			if (typeof opt === "object" && opt.success) {
				
				var wmsId = parseInt(opt.wmsId, 10);
				var map = getMapObjByName(options.target[0]);
				var wms = map.getWmsById(wmsId);
				
				if (wms === null) {
					opt.msg = "Ein unbekannter Fehler ist aufgetreten.";
				}
				else {
					// activate
					if (typeof opt.visible === "number" && opt.visible === 1) {
						
						if (typeof addwms_showWMS === "number" 
							&& addwms_showWMS < wms.objLayer.length) {
							
							if (addwms_showWMS > 0) {
								try {
									
									
									var msg = "Der hinzugeladene Kartendienst " + 
										"verfügt über mehr als " + addwms_showWMS + 
										" Ebenen. Die Ebenen des Dienstes werden " + 
										"<b>nicht</b> aktiviert.";
									
									var $msg= $("<div>" + msg + "</div>");
									$msg.dialog({
										autoOpen: true,
										modal: false,
										width: 300,
										height: 200,
										pos: [100,50]
									});	
																						
									
								}
								catch (e) {
									new Mb_warning(e.message + ". " + msg);
								}
							}
							handleSelectedWms(map.elementName, wmsId, "visible", 0);
						}
						else {
							mb_restateLayers(map.elementName, wmsId);
						}
					}
					
					// zoom to bbox
					var bbox_minx, bbox_miny, bbox_maxx, bbox_maxy;
					for (var i = 0; i < wms.gui_epsg.length; i++) {
						if (map.epsg == wms.gui_epsg[i]) {
							bbox_minx = parseFloat(wms.gui_minx[i]);
							bbox_miny = parseFloat(wms.gui_miny[i]);
							bbox_maxx = parseFloat(wms.gui_maxx[i]);
							bbox_maxy = parseFloat(wms.gui_maxy[i]);
							if (bbox_minx === null || bbox_miny === null || bbox_maxx === null || bbox_maxy === null) {
								continue;
							}

							map.calculateExtent(new Extent(
								bbox_minx,
								bbox_miny,
								bbox_maxx,
								bbox_maxy
							));
							map.setMapRequest();
							break;
						}
					}
				}
			}
			loadWmsCallback(opt);

		};
	
		var loadWmsCallback = function (opt) {
			var msg = typeof opt.msg === "string" ? opt.msg : "";
			
			if (typeof opt !== "object" || !opt.success) {
				msg = "Ein unbekannter Fehler ist aufgetreten. ";
			} 
			else {
				var wmsId = parseInt(opt.wmsId, 10);
				var map = getMapObjByName(options.target[0]);
				var wms = map.getWmsById(wmsId);
				
				if (wms !== null) {
					msg = "Der folgende Dienst wurde zu 'Aktive Dienste' " + 
						"hinzugefügt:<br><br>";
					msg += "<b>" + wms.wms_title + "</b><br><br>";
				}
				else {
					msg = "Ein unbekannter Fehler ist aufgetreten. ";
				}
			}
			try {
				var $msg= $("<div>" + msg + "</div>");
				$msg.dialog({
					modal: false,
				});
			}
			catch (e) {
				new Mb_warning(e.message + ". " + msg);
			}
		};
	
		var additionalBehaviour = [
			{
				openTag: "img",
				attr: {
					src: "../img/tree_info.png",
					title: "Metadaten anzeigen"
				},
				css: {
					cursor: "pointer"
				},
				behaviour: {
					click: function (opt) {
					var $metadataPopup = $("<div><iframe frameborder=0 style='width:100%;height:100%;' src='../php/mod_layerMetadata.php?wmsid=" + opt.treeNode.wmsId + "'></iframe></div>");
					$metadataPopup.dialog({
							title : "Metadata",
							width : 450, 
							bgiframe: true,
							height :600,
							left : 250, 
							top : 100
						}).parent().css({position:"absolute"});
					}
				}
			},
			{
				openTag: "img",
				attr: {
					src: "../img/tree_zoom.png",
					title: "WMS zu aktiven Diensten hinzufügen und auf Ausschnitt zoomen"
				},
				css: {
					cursor: "pointer"
				},
				behaviour: {
					click: function (opt) {
						mod_addWMSById_ajax(opt.appId, opt.treeNode.wmsId, {
							zoomToExtent: 1,
							visible: 1,
							callback: loadWmsAndZoomCallback
						});
					}
				}
			},
			{
				openTag: "img",
				closeTag: "",
				attr: {
					src: "../img/tree_add_wms.png",
					title: "WMS zu aktiven Diensten hinzufügen"
				},
				css: {
					cursor: "pointer"
				},
				behaviour: {
					click: function (param) {
						mod_addWMSById_ajax(param.appId, param.treeNode.wmsId, {
							callback: function (opt) {
								if (typeof opt === "object" && opt.success) {
									var wmsId = parseInt(opt.wmsId, 10);
									var map = getMapObjByName(options.target[0]);
									handleSelectedWms(map.elementName, wmsId, "visible", 0);
									handleSelectedWms(map.elementName, wmsId, "querylayer", 0);
								}
								loadWmsCallback(opt);
							}
						});
					}
				}
			}
		];


		var myTree = new CustomTree({
			loadFromApplication: applicationName,
			id: options.id,
			draggable: false,
			droppable: false,
			skipRootNode: true,
			leafBehaviour: additionalBehaviour
		});
	}
})();
