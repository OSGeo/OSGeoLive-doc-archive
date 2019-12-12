/**
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

/**
 * Triggered on load
 */
Mapbender.events.hideSplashScreen = new Mapbender.Event();

/**
 * Triggered after a map is requested.
 */
Mapbender.events.afterMapRequest = new Mapbender.Event();

/**
 * Triggered when Mapbender is loaded and after the map object has been initialised.
 */
Mapbender.events.init = new Mapbender.Event();

/**
 * Triggered before eventInit.
 */
Mapbender.events.beforeInit = new Mapbender.Event();

/**
 * Triggered after Mapbender is loaded and has been initialised.
 * Used to trigger initial map requests
 */
Mapbender.events.afterInit = new Mapbender.Event();

/**
 * Initializes the map object. Triggered when Mapbender is loaded. 
 */
Mapbender.events.initMaps = new Mapbender.Event();

/**
 * Switches the locale. Triggered by module switch_locale or onload(?) 
 */
Mapbender.events.localize = new Mapbender.Event();

/**
 * Triggered when the Gazetteer is ready and all of it evenst have been initialised.
 * Triggered by the gazetteer module.
 */
Mapbender.events.gazetteerReady = new Mapbender.Event();

/**
 * Triggered after a WMS has been loaded.
 */
Mapbender.events.afterLoadWms = new Mapbender.Event();


Mapbender.events.hideSplashScreen.register(function() {
	// remove the splash screen, show the application
	$("#loading_mapbender").remove();
	$(".hide-during-splash").removeClass("hide-during-splash");
});

Mapbender.events.afterInit.register(function () {
	// performs a map request for each map frame
	$(":maps").each(function () {
		$(this).mapbender(function () {
			this.setMapRequest();
		});
	});
});


//
//
// DEPRECATED, FOR BACKWARDS COMPATIBILITY
//
//

var eventAfterMapRequest = Mapbender.events.afterMapRequest;
var eventInit = Mapbender.events.init;
var eventBeforeInit = Mapbender.events.beforeInit;
var eventAfterInit = Mapbender.events.afterInit;
var eventInitMap = Mapbender.events.initMaps;
var eventLocalize = Mapbender.events.localize;
var eventAfterLoadWMS = Mapbender.events.afterLoadWms;

/**
 * Triggered after the map has been resized
 */
var eventResizeMap = new Mapbender.Event();

/**
 * Triggered after aall map images have been loaded.
 */
var eventAfterMapImagesReady = new Mapbender.Event();

/**
 * Triggered before a map is requested.
 */
var eventBeforeMapRequest = new Mapbender.Event();

/**
 * Triggered before a feature info is requested.
 */
var eventBeforeFeatureInfo = new Mapbender.Event();


var currentWmcExtensionData = {};
var restoredWmcExtensionData = {};

var mb_WfsReadSubFunctions = [];
var mb_WfsWriteSubFunctions = [];
var mb_l10nFunctions = [];

/**
 * deprecated wrapped function
 * @deprecated
 */
function mb_registerInitFunctions(stringFunction){
//	mb_InitFunctions[mb_InitFunctions.length] = stringFunction;
	eventInit.register(stringFunction);
}

/**
 * deprecated wrapped function
 * @deprecated
 */
function mb_registerPreFunctions(stringFunction){
//	mb_MapRequestPreFunctions[mb_MapRequestPreFunctions.length] = stringFunction;
	eventBeforeMapRequest.register(stringFunction);
}

/**
 * deprecated wrapped function
 * @deprecated
 */
function mb_registerFeatureInfoPreFunctions(stringFunction){
//	mb_FeatureInfoPreFunctions[mb_FeatureInfoPreFunctions.length] = stringFunction;
	eventBeforeFeatureInfo.register(stringFunction);
}

/**
 * deprecated wrapped function
 * @deprecated
 */
function mb_registerSubFunctions(stringFunction){
//	mb_MapRequestSubFunctions[mb_MapRequestSubFunctions.length] = stringFunction;
	eventAfterMapRequest.register(stringFunction);
}

/**
 * deprecated wrapped function
 * @deprecated
 */
function mb_registerloadWmsSubFunctions(stringFunction){
//	mb_loadWmsSubFunctions[mb_loadWmsSubFunctions.length] = stringFunction;
	eventAfterLoadWMS.register(stringFunction);
}

/**
 * deprecated wrapped function
 * @deprecated
 */
function mb_registerWfsReadSubFunctions(stringFunction){
	mb_WfsReadSubFunctions[mb_WfsReadSubFunctions.length] = stringFunction;
}

/**
 * deprecated wrapped function
 * @deprecated
 */
function mb_registerWfsWriteSubFunctions(stringFunction){
	mb_WfsWriteSubFunctions[mb_WfsWriteSubFunctions.length] = stringFunction;
}

/**
 * deprecated wrapped function
 * @deprecated
 */
function mb_registerL10nFunctions(stringFunction) {
	eventLocalize.register(stringFunction);
//	mb_l10nFunctions[mb_l10nFunctions.length] = stringFunction;
}

var mb_PanSubElements = [];
function mb_registerPanSubElement(elName){
	var ind = mb_PanSubElements.length;
	mb_PanSubElements[ind] = elName;
}

var mb_vendorSpecific = [];
function mb_registerVendorSpecific(stringFunction){
	mb_vendorSpecific[mb_vendorSpecific.length] = stringFunction;
}

/**
 * deprecated function for writing content within a tag via innerHTML
 * @deprecated
 */
function writeTag(frameName, elName, tagSource) {
	if (frameName && frameName !== "") {
		var el = window.frames[frameName].document.getElementById(elName);
		if (el !== null) {
			el.innerHTML = tagSource;
		}
	}
	else if(!frameName || frameName === ""){
		var node = document.getElementById(elName);
		if (node !== null) {
		   	node.innerHTML = tagSource;
		}
	}
}
