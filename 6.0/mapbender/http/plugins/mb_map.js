/**
 * Package: mapframe1
 *
 * Description:
 * The main map in Mapbender
 * 
 * Files:
 *  - http/plugins/mb_map.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>','mapframe1',
 * > 1,1,'The main map in Mapbender','','div','','',220,105,625,400,2,
 * > 'overflow:hidden;background-color:#ffffff','','div',
 * > '../plugins/mb_map.js',
 * > '../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php',
 * > '','','http://www.mapbender.org/index.php/Mapframe');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'mapframe1', 
 * > 'skipWmsIfSrsNotSupported', '0', 
 * > 'if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility',
 * > 'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'mapframe1', 'slippy', 
 * > '0', 'Activates an animated, pseudo slippy map' ,'var');
 *
 * Help:
 * http://www.mapbender.org/Mapframe
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * skipWmsIfSrsNotSupported      - if set to 1, it skips the WMS request 
 * 									if the current SRS is not supported by 
 * 									the WMS; if set to 0, the WMS is always 
 * 									queried. 
 * 									Default is 0, because of backwards 
 * 									compatibility
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var $this = $(this);

Mapbender.events.initMaps.register(function () {
	
	$this.data("isMap", true);
	
	$this.html('<div id="' + options.id + '_maps" name="maps" ' + 
		'style="width: 0px; height: 0px; z-index:2;"></div>');

	$this.mapbender(new Mapbender.Map ({
		width: options.width,
		height: options.height,
		id: options.id,
		wms: wms
	}));

	$this.bind("mousedown", function (e) {
		e.preventDefault();
	}).bind("mouseover", function (e) {
		e.preventDefault();
	}).bind("mousemove", function (e) {
		e.preventDefault();
	});
	
	// for backwards compatibility
	mb_mapObj.push(Mapbender.modules[options.id]);

	var mapObject = Mapbender.modules[options.id];
	// set restricted extent
	mapObject.skipWmsIfSrsNotSupported = 
		options.skipWmsIfSrsNotSupported === 1 ? true : false;

	// pseudo slippy map
	mapObject.slippy = 
		options.slippy === 1 ? true : false;
/*	
	// set restricted extent
	if (typeof options.restrictedExtent !== "undefined") {
		mapObject.setRestrictedExtent(options.restrictedExtent);
	}
	else {
		new Mapbender.Notice("Restricted extent not set via element variable.");
	}
*/
});
