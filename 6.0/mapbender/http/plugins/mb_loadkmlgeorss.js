/*
 * Package: load_georsskml
 *
 * Description:
 * This element enables you to load GeoRSS or KML temporary to a Mapbender application. 
 * The features will be displayed in the mapframe. The result will also be displayed in a result table. 
 * The module enables you to load more than one GeoRSS/KML. Every resultset will be displayed in a result table.
 * As long as the result table is open. The features are shown in the map.
 * 
 * Files:
 *  - ../http/plugins/mb_loadkmlgeorss.js>
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, 
 * > e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, 
 * > e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('<application_name>',
 * >'load_georsskml',2,1,'','Load GeoRSS or KML','img','../img/georss_logo_off.png','',750,10,
 * > 24,24,NULL ,'','','','../plugins/mb_loadkmlgeorss.js','','','','');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<application_name>', 'load_georsskml', 'buffer', '100', 
 * > 'how much space to leave around a feature when zooming to it ' ,'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<application_name>', 'load_georsskml', 'position', '', 
 * > 'position where the result frame is displayed, see the [http://docs.jquery.com/UI/Dialog |jquery UI documentation] for possible values' ,'var');
 *
 * Help:
 * http://www.mapbender.org/Loadkmlgeorss
 *
 * Maintainer:
 * http://www.mapbender.org/User:Karim_Malhas
 * 
 * Parameters:
 * buffer    - *[optional]* var, how much space to leave around a feature when zooming to it
 * position  - *[optional]* var, position where the result frame is displayed
 * 
 *
 * Requires:
 * <>
 * <>
 * 
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
options.buffer = options.buffer || 0.10;
options.position = options.position || 'center';
var originalI18nObject = {
	"labelLoadError" : "Could not load Document",
	"labelName":"Name",
	"labelUntitled":"Untitled",
	"labelUrlBox": "Paste URL here",
	"sProcessing":   "Processing...",
	"sLengthMenu":   "Show _MENU_ entries",
	"sZeroRecords":  "No matching records found",
	"sInfo":         "SLowing _START_ to _END_ of _TOTAL_ entries",
	"sInfoEmpty":    "Showing 0 to 0 of 0 entries",
	"sInfoFiltered": "(filtered from _MAX_ total entries)",
	"sInfoPostFix":  "",
	"sSearch":       "Search:",
	"sUrl":          "",
	"oPaginate": {
		"sFirst":    "First",
		"sPrevious": "Previous",
		"sNext":     "Next",
		"sLast":     "Last"
	 }


};

var translatedI18nObject = Mapbender.cloneObject(originalI18nObject);

var that = this;
that.feeds = [];
that.$popup = $('<div></div>').dialog({autoOpen : false, height: 500, width: 500});

$('<div id="'+ options.id +'_dialog"><label>GeoRSS <input type="radio" name="type" value="GeoRSS" checked="checked"/></label>'+
	 '<label>KML<input type="radio" name="type" value="KML"></label><br />'+
	 '<label class="labelUrlBox">'+ translatedI18nObject.labelUrlBox  +'</label> <input id="' + options.id +'_url" type="text" /></div>').dialog({
                bgiframe: true,
                autoOpen: false,
                height: 240,
                width: 400,
                buttons: { 
					"OK" : function(){
						var url = $('#' + options.id + '_url').val();
						$('#' + options.id + '_url').val("");
						var $dialog = $(this);
						$dialog.dialog("close");
				
						var format = $("input:checked",$dialog).val();
						var endpointURL = "";
						if( format == "GeoRSS"){
							endpointURL = "../php/geoRSSToGeoJSON.php";
						}else{
							endpointURL = "../php/kmlToGeoJSON.php";
						}
						$.ajax({ url: endpointURL,
							data: {url: url},
							type: "POST",
							dataType: "json",
							success: function(data,textStatus,xhr){
						
							if(data.errorMessage){
								$("<div class='labelLoadError'>"+ translatedI18nObject.labelLoadError +"<div>").dialog({ buttons: {"OK":function(){ $(this).dialog("close"); } } } );
								return;
							}

							var $map = $('#mapframe1').mapbender();
							var markers = [];
							var title = "";
							$("table", $dialog).remove();
							var $table = $("<table><thead><tr><th class='labelName'>"+ translatedI18nObject.labelName  +"</th></tr></thead><tbody></tbody></table>");

							if(data.features){
								// we always transform _from_ 4326 geoRSS and KML use this as their default
								var projSrc = new Proj4js.Proj('EPSG:4326');
								var projDest = new Proj4js.Proj($map.epsg);
								var markeroptions = {width: "19px", height: "34px"};
								var g = null;
								var map = $('#mapframe1').mapbender();
								//title = feature.properties.title || feature.properties.name || translatedI18nObject.labelUntitled;

							
								
								var geomArray = new GeometryArray();
								var highlightArray = [];
								geomArray.importGeoJSON(data);
								for(var i =0; i < geomArray.count(); i++){
									var h = new Highlight(['mapframe1'], "mapframe1_" + parseInt(Math.random()*100000,10),{
										"position":"absolute",
										"top": "0px",
										"left": "0px",
										"z-index": "80" },1);
									g = geomArray.get(i);
									icon = g.e.getElementValueByName("Mapbender:icon");
									title = g.e.getElementValueByName("title");
									name = g.e.getElementValueByName("name");
									
									if(name != "false" && name !== false){
										title = name;
									}			
									if(icon == "false" || icon === false){
										g.e.setElement("Mapbender:iconOffsetX", -10);
										g.e.setElement("Mapbender:iconOffsetY", -34);
										g.e.setElement("Mapbender:icon","../img/marker/red.png");
									}

									description = g.e.getElementValueByName("description");
									$row = $("<tr><td>"+ title  +"</td></tr>");
									$row.css("cursor","pointer");
									$row.click((function(title,description){
										return function(){
											$("*",that.$popup).remove();
											description = description.replace('<a ','<a target="_new" ');
											that.$popup.append($("<div><h1>"+title+"</h1><p>"+description +"</p></div>")).dialog('open');
										};
									})(title,description));
									$("tbody",$table).append($row);
									h.add(g);
									highlightArray.push(h);

									title = "";
									name = "";
								}
								for(var i in highlightArray){
									highlightArray[i].paint();
								}
								map.events.afterMapRequest.register(function () {
									for(var i in highlightArray){
										highlightArray[i].paint();
									}
								});

								that.feeds[url] = {
									geomArray: geomArray,
									highlightArray: highlightArray
								};


							}

							var $tableDialog = $("<div></div>").dialog({
								width: "450",
								height: "500",
								position: options.position,
								beforeclose: (function(url){
									return function(){
										delete that.feeds[url];
										for(var i in highlightArray){
											highlightArray[i].clean();
										}
									};
									})(url),
								buttons: {"Close": function(){
										$(this).dialog('close');
										$(this).dialog('destroy');
									}
								}
							});
							$tableDialog.append($table);
							
							$table.dataTable({"bJQueryUI": true ,
								"oLanguage":{
									"sUrl":"../extensions/dataTables-1.5/lang/"+Mapbender.languageId +".txt"
									} });
							

							$dialog.dialog('close');
							},
							error: function(xhr, ajaxOptions,error){
								$("<div class='labelLoadError'>"+ translatedI18nObject.labelLoadError +"</div>").dialog({ buttons: {"OK":function(){ $(this).dialog("close");  }} });	
							}
						});
					},
					"Cancel": function(){
						$(this).dialog("close");
					}
				}
});
Mapbender.events.localize.register(function(){
	Mapbender.modules.i18n.queue(options.id, originalI18nObject, function(translatedI18nObject){
		$('.labelLoadError').text(translatedI18nObject.labelLoadError); 
		$('.labelUrlBox').text(translatedI18nObject.labelUrlBox); 
	});
});

Mapbender.events.init.register(function () {
	$(that).click(function(){
		$('#'+options.id+'_dialog').dialog('open');
	});
	$('#mapframe1').click(function(e){
		var map = $('#mapframe1').mapbender();
		var pos = map.getMousePosition(e);		
		var clickPoint =  map.convertPixelToReal(new Point(pos.x,pos.y));
		var feed = null;
		var requestGeometries = [];
		// This uses two methods to determine wether a clickposition is on a geometry
		// - Points are represented as icons, so we check if the click is on an icon
		// - Polygons don't have a dom Element when not using Rapheljs, so we go ask postgis
		// after that's finished the results are merged and displayed in a box
		var pointGeometries = {};	
		var g,h,nodes = null;
		for (var i in that.feeds){
			feed = that.feeds[i] ;
			requestGeometries = [];

			for(var j = 0; j < feed.geomArray.count(); j++){
				g = feed.geomArray.get(j);
			 	h = feed.highlightArray[j];	
			 	nodes = h.getNodes();
				if(g.geomType == geomType.point){
					// we only add one point per highlight so we can assume there's only one node
					if(!nodes[0]){ continue;}
					var rect = nodes[0].getBoundingClientRect(); 
					if(e.clientX >= rect.left && e.clientX <= rect.right &&
					   e.clientY >= rect.top  && e.clientY <= rect.bottom){
						// we just need the keys to exist
						// theywill be merged with the ones coming from the
						// server
						pointGeometries[j] = true;
					}
					
				}else{
					requestGeometries.push(g.toText());
				}
			}
			var req = new Mapbender.Ajax.Request({
			url: "../php/intersection.php",
			method: "intersect",
			parameters: {
				clickPoint:	clickPoint.toText(),
				geometries: requestGeometries
				},
			callback: (function(geomArray,pointGeometries){ return function(result, success, message){
				if(!success){
					return;
				}
				// this is basically an onclick handler, !intersects means
				// the click didn't happen on the polygon
				$.extend(result.geometries,pointGeometries);
				if(!result.geometries || result.geometries.length <1){
					return;
				}

				
				$("*",that.$popup).remove();
				var $tabs = $("<ul></ul>");
				// this iterates over an object where the keys are _not_ the incremential
				// basically a sparse array. therefore I cannot be used to count the entries in the object
				// this is why j is used
				var j = 0;
				for(i in result.geometries){
					var g = geomArray.get(i);
					title = g.e.getElementValueByName("title");
					name  = g.e.getElementValueByName("name");
					if(typeof(name) == "string"){
						title = name != "false" ? name : title;
						if (icon == "false"){
							g.e.setElement("Mapbender:icon","../img/marker/red.png");
						}
					}else{
						//sane browsers go here
						title = name != false ? name : title;
						if (icon === false){
							g.e.setElement("Mapbender:icon","../img/marker/red.png");
						}
					}
					description = g.e.getElementValueByName("description");
					$tabs.append('<li><a href="#rsspopup_'+ i +'">'+ title + '</a></li>');
					that.$popup.append('<div id="rsspopup_'+ i +'"><h1>'+ title +'</h1><p>'+ description +'</p></h1>');
					j++;
				}
				if(j > 1){
					var $tabcontainer = $("<div><div>");
					$tabcontainer.append($tabs);
					$tabcontainer.append($('div',that.$popup));	
					that.$popup.append($tabcontainer);
					$tabcontainer.tabs();
				}
				that.$popup.dialog('open');
			

			}})(feed.geomArray, pointGeometries)
			});
			req.send();
			requestGeometries = [];
			pointGeometries = {};
		}
	});
});

