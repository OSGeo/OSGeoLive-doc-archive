/**
 * Package: doubleclickZoom
 *
 * Description:
 * Adds behaviour to maps specified in the target field.
 * Zoom in on left double click, zoom out on right double click.
 * Deactivates the browser contextmenu!
 * 
 * Files:
 *  - http/javascripts/mod_doubleclickZoom.js
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<gui_id>', 
 * > 'doubleclickZoom',2,1,'adds doubleclick zoom to map module (target). 
 * > Deactivates the browser contextmenu!!!','Doubleclick zoom','div', 
 * > '','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div',
 * > 'mod_doubleclickZoom.js','','mapframe1','','');
 *
 * Help:
 * http://www.mapbender.org/DoubleclickZoom
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

eventInit.register(function () {

	for (var i in options.target) {
		(function () {
		
			var currentTarget = options.target[i];
	
			var mapObject = Mapbender.modules[currentTarget];
			
			var zoomTo = function (lastScrollPosition, zoomIn) {
				var pos = mapObject.convertPixelToReal(lastScrollPosition);
				var extentAfterZoom = mapObject.calculateExtentAfterZoom(
					zoomIn, 
					2.0, 
					pos.x, 
					pos.y
				);
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
			};
			
			//
			// Zoom out on right double click
			//
			mapObject.doubleClickZoomOut = function (lastScrollPosition) {
				zoomTo(lastScrollPosition, false);
			};
			
			//
			// Zoom in on left double click
			//
			mapObject.doubleClickZoomIn = function (lastScrollPosition) {
				zoomTo(lastScrollPosition, true);
			};
		
			//
			// add doubleclick behaviour
			//
			$("#" + currentTarget).dblclick(function (e) {
				mapObject.doubleClickZoomIn(mapObject.getMousePosition(e));
			}).bind('contextmenu',function(){ 
				return false 
			}).mouseup(function(e){
				var rightclick = (e.which) ? (e.which == 3) : (e.button == 2);
				var t = $(this);
				if (rightclick) {
					if (t.data('rightclicked')) {
						mapObject.doubleClickZoomOut(mapObject.getMousePosition(e));
		
						t.data('rightclicked',false);
						clearTimeout(t.data('rcTimer'));
					} else {
						t.data('rightclicked',true);
						t.data('rcTimer',setTimeout((function(t){ 
							return function(){ 
								t.data('rightclicked', false); 
							} 
						})(t), 800));
					};
				};
			}); 
		}());
	}
});