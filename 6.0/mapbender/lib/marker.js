/**
 * Package: Marker
 * 
 * Description:
 * A very basic marker class. Put a (custom) marker at a given location
 * 
 * Files:
 *  - lib/marker.js
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */


/**
 * Constructor: Mapbender.Marker
 * 
 * Parameters:
 * p - a <Mapbender.Point> to specify the location
 * map - a <Mapbender.Map> on which the point is rendered
 * options.img.url - *[optional]* a link to a custom marker image
 * options.img.offset - *[optional]* a <Mapbender.Point> specifying the offset,
 * 		default is (0,0). Usually these values are below zero because the image
 *      has to be translated to top and left
 * options.img.width - *[optional]* use this to resize the marker image width 
 *      (height must also be specified)
 * options.img.height - *[optional]* use this to resize the marker image height
 *      (width must also be specified)
 */
Mapbender.Marker = function (p, map) {
	var options = {};
	if (arguments.length > 2 && typeof arguments[2] === "object") {
		options = arguments[2];
	}

	// override default marker image
	if (typeof options.img === "object" && options.img.url) {
		if (typeof options.img.offset !== "object" || 
			options.img.offset.x === undefined ||
			options.img.offset.y === undefined
		) {
			options.img.offset = new Point(0, 0);
		}
	}
	// default marker image
	else {
		options.img = {
			url: "../img/marker/red.png",
			offset: new Point(-10, -34)
		};
	}

	
	var id = map.elementName + "_" + parseInt(Math.random()*100000,10);
	var h = new Highlight(
		[map.elementName],
		id,
		{
			position:"absolute",
			top:"0px",
			left:"0px",
 			zIndex: "100"
		},
		1
	);
	var g = new MultiGeometry(geomType.point);
	g.addGeometry(geomType.point);
	g.get(-1).add(p);

	g.e.setElement("Mapbender:icon", options.img.url);
	g.e.setElement("Mapbender:iconOffsetX", options.img.offset.x);
	g.e.setElement("Mapbender:iconOffsetY", options.img.offset.y);
	g.e.setElement("Mapbender:iconZIndex", options.img.offset.y);

	if (options.img.width !== undefined && options.img.height !== undefined) {
		g.e.setElement("Mapbender:iconWidth", options.img.width);
		g.e.setElement("Mapbender:iconHeight", options.img.height);
	}
	
	h.add(g);
	h.paint();

	map.events.afterMapRequest.register(function () {
		h.paint();
	});

	/**
	 * Method: remove
	 * 
	 * Description:
	 * Remove the marker from the map.
	 */
	this.remove = function () {
		h.clean();
	};
};
