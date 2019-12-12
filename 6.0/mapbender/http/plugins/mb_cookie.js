/**
 * Package: cookie
 *
 * Description:
 * Sets a cookie after each map request. The cookie contains the 
 * current settings of the map, similar to WMC.
 * 
 * Files:
 *  - http/plugins/mb_cookie.js
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>','cookie',20,1,
 * > '','','div','','',-1,-1,1,1,NULL ,'','','div','../plugins/mb_cookie.js',
 * > '','mapframe1','','');
 *
 * Help:
 * http://www.mapbender.org/Cookie
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

var mod_mapCookie_target             = options.target[0];
var mod_mapCookie_cookieLifetimeDays = 365;
var mod_mapCookie_cookieName         = 'stored_map_state';

Mapbender.events.init.register(function () {
	mod_mapCookie_restoreMapState();

	Mapbender.modules[mod_mapCookie_target].afterMapRequest.register(function () {
		mod_mapCookie_storeMapState();
	});
});

function mod_mapCookie_restoreMapState() {
	if(!document.cookie) {
		return;
	}

	var currentMapState = Mapbender.modules[mod_mapCookie_target];
	var cookieData      = document.cookie.split(';');
	var cookieExists = false;
	var i, j;
	
	for (i = 0; i < cookieData.length; i++) {
		var cookieName = new RegExp(mod_mapCookie_cookieName + "=");
		
		if(!cookieData[i].match(cookieName)) {
			continue;
		}
		cookieExists = true;
		var json = cookieData[i].replace(cookieName,'');
		var storedMapState = $.parseJSON(json);
		if (!storedMapState) {
			return;
		}
		currentMapState.epsg   = storedMapState.epsg;
		
		var currentWmsLayers = [];
		for (j = 0; j < currentMapState.wms.length; j++) {
			for(var k = 0; k < currentMapState.wms[j].objLayer.length; k++) {
				currentWmsLayers.push(currentMapState.wms[j].objLayer[k]);
			}
		}
	
		for (j = 0; j < currentWmsLayers.length; j++) {
			for(k = 0; k < storedMapState.layers.length; k++) {
				var storedWmsTitle           = storedMapState.layers[k][0];
				var storedLayerId            = storedMapState.layers[k][1];
				var storedLayerName          = storedMapState.layers[k][2];
				var storedLayerVisibility    = storedMapState.layers[k][3];
				var storedLayerQueryablility = storedMapState.layers[k][4];
				
				if(currentWmsLayers[j].layer_id == storedLayerId) {
					currentWmsLayers[j].gui_layer_visible    = storedLayerVisibility;
					currentWmsLayers[j].gui_layer_querylayer = storedLayerQueryablility;
				}
			}
		}

		var index = getMapObjIndexByName(mod_mapCookie_target);     
		var dimensions = storedMapState.mapsize.split(",");
		var width      = parseInt(dimensions[0], 10);
		var height     = parseInt(dimensions[1], 10);

		var coordArray = storedMapState.extent.split(",");
		var newExtent = new Mapbender.Extent(
			coordArray[0],
			coordArray[1],
			coordArray[2],
			coordArray[3]
		);
		currentMapState.setWidth(width);
		currentMapState.setHeight(height);
		currentMapState.calculateExtent(newExtent);

		
	}

	if (!cookieExists) {
		return;
	}		

	for (i = 0; i < currentMapState.wms.length; i++) {
		mb_restateLayers(mod_mapCookie_target,currentMapState.wms[i].wms_id);
	}

//	eventAfterLoadWMS.trigger();
	for (i = 0; i < mb_mapObj.length; i++) {
		mb_mapObj[i].setMapRequest();
	}
}

function mod_mapCookie_storeMapState() {
	var currentMapState     = Mapbender.modules[mod_mapCookie_target];
	var cookieData          = {};
	var cookieExpires       = new Date();

	cookieData.epsg   = currentMapState.epsg;
	cookieData.extent = currentMapState.getExtent();
	cookieData.layers = [];
	
	for(var i = 0; i < currentMapState.wms.length; i++) {
		for(var j = 0; j < currentMapState.wms[i].objLayer.length; j++) {
			var currentLayers = [];
			currentLayers.push(
				currentMapState.wms[i].wms_title,
				currentMapState.wms[i].objLayer[j].layer_id,
				currentMapState.wms[i].objLayer[j].layer_name,
				currentMapState.wms[i].objLayer[j].gui_layer_visible,
				currentMapState.wms[i].objLayer[j].gui_layer_querylayer
			);
			cookieData.layers.push(currentLayers);
		}
	}

	cookieData.mapsize = currentMapState.getWidth() + "," + currentMapState.getHeight();

	cookieExpires.setTime(
		cookieExpires.getTime() + 
		(parseInt(mod_mapCookie_cookieLifetimeDays, 10) * 24 * 60 * 60 * 1000)
	);

	document.cookie = mod_mapCookie_cookieName + 
		'=' + $.toJSON(cookieData) + ';expires=' + 
		cookieExpires.toGMTString() + ';';
}
