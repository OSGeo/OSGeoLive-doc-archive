/**
 * Package: ol_scaleLine
 *
 * Description:
 * An OpenLayers ScaleLine
 * 
 * Files:
 *  - http/plugins/ol_scaleLine.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>',
 * > 'ol_scale',1,0,'An OpenLayers ScaleLine','OpenLayers scaleLine',
 * > 'div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div',
 * > '../plugins/ol_scaleLine.js','','ol','',
 * > 'http://www.mapbender.org/ol_scaleline');
 *
 * Help:
 * http://www.mapbender.org/ol_scaleLine
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
var ol_map = Mapbender.modules[options.target[0]];
ol_map.mapbenderEvents.layersAdded.register(function () {
	var handleMeasurements = function(event) {
		var geometry = event.geometry;
		var units = event.units;
		var order = event.order;
		var measure = event.measure;
		var out = "";
		if(order == 1) {
			out += "measure: " + measure.toFixed(3) + " " + units;
		} else {
			out += "measure: " + measure.toFixed(3) + " square" + units;
		}
//		console.log( out );
        };
	var deactivateConflictingControls = function() {
//		console.log('deactivating');
	};

	// style the sketch fancy
	var sketchSymbolizers = {
		"Point": {
			pointRadius: 4,
			graphicName: "square",
			fillColor: "white",
			fillOpacity: 1,
			strokeWidth: 1,
			strokeOpacity: 1,
			strokeColor: "#333333"
		},
		"Line": {
			strokeWidth: 3,
			strokeOpacity: 1,
			strokeColor: "#666666",
			strokeDashstyle: "dash"
		},
		"Polygon": {
			strokeWidth: 2,
			strokeOpacity: 1,
			strokeColor: "#666666",
			fillColor: "white",
			fillOpacity: 0.3
		}
	};
	var style = new OpenLayers.Style();
	style.addRules([
		new OpenLayers.Rule({symbolizer: sketchSymbolizers})
	]);
	var styleMap = new OpenLayers.StyleMap({"default": style});
	
	var measureLine = new OpenLayers.Control.Measure(
		OpenLayers.Handler.Path, {
			persist: true,
			handlerOptions: {
				layerOptions: {styleMap: styleMap}
			},
			type: 
		}
	);
	measureLine.events.on({
		"measure": handleMeasurements,
		"measurepartial": handleMeasurements,
		"activate": deactivateConflictingControls
	});
	
	ol_map.addControl( measureLine );	

	var panel = new OpenLayers.Control.Panel({
		position: new OpenLayers.Pixel(100, 0)	
	});
        panel.addControls([measureLine]);
        ol_map.addControl(panel);
});
