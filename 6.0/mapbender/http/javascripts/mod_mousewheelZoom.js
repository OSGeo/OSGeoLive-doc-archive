/**
 * Package: mousewheelZoom
 *
 * Description:
 * Zoom in/out with the mousewheel. Specify maps in target field.
 * 
 * Files:
 *  - http/javascripts/mod_mousewheelZoom.js
 *  - http/extensions/jquery.mousewheel.min.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<gui_id>','mousewheelZoom',
 * > 2,1,'adds mousewheel zoom to map module (target)','Mousewheel zoom',
 * > 'div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div',
 * > 'mod_mousewheelZoom.js','../extensions/jquery.mousewheel.min.js',
 * > 'mapframe1','','');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<gui_id>', 'mousewheelZoom', 'factor', '2', 
 * > 'The factor by which the map is zoomed on each mousewheel unit' ,'var');
 *
 * Help:
 * http://www.mapbender.org/MousewheelZoom
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * factor    - *[optional]* {Float} The factor by which the map is zoomed on 
 * 					each mousewheel unit. Default is 2.
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

 
if (options.factor === undefined) {
	options.factor = 2;
}

eventInit.register(function () {

	for (var i in options.target) {
		(function () {
		
			var currentTarget = options.target[i];
	
			var mapTimeout;
			var sum_delta = 0;
			var lastTimestamp;
			var lastScrollPosition;
			var zoomMousewheel = options.factor;
		
			var mapObject = Mapbender.modules[currentTarget];
			mapObject.mousewheelZoom = function () {
				var currentTime = new Date();
			
				if (currentTime.getTime() - lastTimestamp > 200) {
					if (lastScrollPosition !== null) {
						var pos = mapObject.convertPixelToReal(lastScrollPosition);
			
						if (sum_delta > 0) {
							var extentAfterZoom = mapObject.calculateExtentAfterZoom(
								true, 
								Math.pow(zoomMousewheel, sum_delta), 
								pos.x, 
								pos.y
							);
						}
						else {
							var extentAfterZoom = mapObject.calculateExtentAfterZoom(
								false, 
								Math.pow(zoomMousewheel, -sum_delta), 
								pos.x, 
								pos.y
							);
						}

						var newPos = mapObject.convertRealToPixel(
							pos,
							extentAfterZoom
						);
						var diff = newPos.minus(lastScrollPosition);
						
						var newSouthEast = mapObject.convertPixelToReal(
							(new Point(0, mapObject.getHeight())).plus(diff),
							extentAfterZoom
						);
						var newNorthWest = mapObject.convertPixelToReal(
							(new Point(mapObject.getWidth(), 0)).plus(diff),
							extentAfterZoom
						);
						var newExtent = new Mapbender.Extent(newSouthEast, newNorthWest);
						mapObject.setExtent(newExtent);
						mapObject.setMapRequest();
					}			
					sum_delta = 0;
					clearTimeout(mapTimeout);
				}
				else {
					var that = this;
					mapTimeout = setTimeout(function () {
							that.mousewheelZoom();	
						}, 
						100
					);
				}
			};
		
			//
			// add mousewheel behaviour
			//
			$("#" + currentTarget).mousewheel(function (e, delta) {
				if (sum_delta == 0) {
					mapTimeout = setTimeout(function () {
							lastScrollPosition = mapObject.getMousePosition(e);
							mapObject.mousewheelZoom();	
						}, 
						100);
				}
				sum_delta = sum_delta + (delta);
				var currentTime = new Date();
				lastTimestamp = currentTime.getTime();
				
				return false;
			});
		}());
	}
});