/**
 * Package: Extent
 * 
 * Description:
 * An extent is also known as a bounding box. 
 * 
 * Files:
 *  - lib/extent.js
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

/** makeExtent is a workaround function to get printPDF functions working if
 * printPDF is shown in a popup
 */
Mapbender.makeExtent = function (minx, miny, maxx, maxy) {
    return new Mapbender.Extent(minx, miny, maxx, maxy);
};

/**
 * Constructor: Extent
 * 
 * Parameters (coordinates):
 * minx      - {Float} x-coordinate of south western point
 * miny      - {Float} y-coordinate of south western point
 * maxx      - {Float} x-coordinate of north eastern point
 * maxy      - {Float} y-coordinate of north eastern point
 * 
 * or parameters (points):
 * sw    - {Point} south western point
 * ne    - {Point} north eastern point
 */
Mapbender.Extent = function (minx, miny, maxx, maxy) {
	var that = this;

	var setMin = function (min) {
		that.min = min;
		that.minx = min.x;
		that.miny = min.y;
	};
	var setMax = function (max) {
		that.max = max;
		that.maxx = max.x;
		that.maxy = max.y;
	};
	
	var setExtent = function (extent) {
		that.extent = extent;
		that.extentx = extent.x;
		that.extenty = extent.y;
	};
	
	var setCenter = function (center) {
		that.center = center;
		that.centerx = center.x;
		that.centery = center.y;
	};
	
	/**
	 * Method: set
	 * 
	 * Description:
	 * Allows to set the center (one param) or the extent (two params)
	 * 
	 * Parameters (center)
	 * center	- {Point} center of bounding box
	 * 
	 * Parameters (center)
	 * sw		- {Point} south western point of bounding box
	 * ne		- {Point} north eastern point of bounding box
	 */
	this.set = function (min, max) {
		// only one parameter (center)
		if (typeof max === "undefined") {
			var newLowerLeft = min.minus(this.extent.dividedBy(2));
			var newUpperRight = min.plus(this.extent.dividedBy(2));
			setMin(newLowerLeft);
			setMax(newUpperRight);
			setCenter(min);
		}
		// two parameters (lower left and upper right ( = extent))
		else {
			setMin(min);
			setMax(max);
			setExtent(max.minus(min));
			setCenter((min.plus(max)).dividedBy(2));
		}
	};

	this.setCrs = function (options) {
		if (options.extent) {
			var sw = new Proj4js.Point(options.extent.min.x, options.extent.min.y);
			var ne = new Proj4js.Point(options.extent.max.x, options.extent.max.y);
		}
		else {
			var sw = new Proj4js.Point(that.min.x, that.min.y);
			var ne = new Proj4js.Point(that.max.x, that.max.y);
		}
		sw = Proj4js.transform(options.source, options.dest, sw);
		ne = Proj4js.transform(options.source, options.dest, ne);
		sw = new Mapbender.Point(sw.x, sw.y);
		ne = new Mapbender.Point(ne.x, ne.y);
		that.set(sw, ne);
	};
	
	var isPoint = function (param) {
		if (typeof param === "object" && param.constructor === Point) {
			return true;
		}
		return false;
	};
	
	var isExtent = function (param) {
		if (typeof param === "object" && param.constructor === Mapbender.Extent) {
			return true;
		}
		return false;
	};
	
	if (isExtent(minx)) {
		this.set(Mapbender.cloneObject(minx.min), Mapbender.cloneObject(minx.max));
	}
	else if (isPoint(minx) && isPoint(miny) && 
		typeof maxx === "undefined" && 
		typeof maxy === "undefined") {

		// input is "point, point"
		this.set(minx, miny);
	}
	else {
		// input is "coordinate, coordinate, coordinate, coordinate"
		// deprecated
		this.set(
			new Point(parseFloat(minx), parseFloat(miny)),
			new Point(parseFloat(maxx), parseFloat(maxy))
		);
	}
};

/**
 * Method: toString
 * 
 * Description:
 * Returns a comma-separated list of all four coordinates
 */
Mapbender.Extent.prototype.toString = function () {
	return this.min.x + "," + this.min.y + "," + this.max.x + "," + this.max.y;
};

Mapbender.Extent.prototype.getSouthWest = function () {
	return this.min;
};
	
Mapbender.Extent.prototype.getNorthEast = function () {
	return this.max;
};
