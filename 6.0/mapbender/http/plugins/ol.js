/**
 * Package: ol
 *
 * Description:
 * An OpenLayers Map, configured with WMS from Mapbender application settings
 * 
 * Files:
 *  - http/plugins/ol.js
 *
 * SQL:
 * > <SQL for element> 
 * > 
 * > <SQL for element var> 
 *
 * Help:
 * http://www.mapbender.org/ol
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

var ol_options = { 
	maxExtent: new OpenLayers.Bounds(-180, -90, 180, 90),
	units: "degrees",
	fractionalZoom: true,
	controls: []
};

OpenLayers.ImgPath = "../extensions/OpenLayers-2.9.1/img/";
var map = new OpenLayers.Map(options.id, ol_options);

map.mapbenderEvents = {
	'mapInstantiated': new Mapbender.Event(),
	'layersAdded': new Mapbender.Event(),
	// TODO: This one might be obsolete?
	'controlsAdded': new Mapbender.Event(),
	'mapReady': new Mapbender.Event()
};

var $openlayers = $(this);

$openlayers.mapbender(map);

var openlayers = $openlayers.mapbender();
openlayers.mapbenderEvents.mapReady.register(function () {
	if (!openlayers.getCenter()) {
		openlayers.zoomToMaxExtent({
			restricted: false
		});
	} 
});

// register the fiing of the first OL-event to the
// afterInit-event so that the different had time 
// to be registered elsewhere
Mapbender.events.afterInit.register(function(){
	// fire the mapInstantiated event
	openlayers.mapbenderEvents.mapInstantiated.trigger();
});
