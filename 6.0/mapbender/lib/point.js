/**
 * Package: Point
 * 
 * Description:
 * A class representing a two- (or three-) dimensional point.
 * 
 * Files:
 *  - lib/point.js
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

/**
 * Constructor: Point
 * 
 * Description:
 * just pass a Point in order to clone it.
 * 
 * Parameters:
 * x      - the x coordinate
 * y      - the y coordinate
 * z      - *[optional]* the z coordinate
 */
Mapbender.Point = function (x, y, z){
	if (typeof x === "object" && x.constructor === Mapbender.Point) {
		this.x = x.x;
		this.y = x.y;
		this.z = x.z;
		return;
	}

	this.x = parseFloat(x);
	this.y = parseFloat(y);
	this.z = parseFloat(z);
};

/**
 * computes the distance between a {@link Point} p and this {@link Point}
 *
 * @param {Point} p the distance between this {@link Point} and the {@link Point} p is computed.
 * @return {Float} the distance between the two {@link Point} objects.
 */
Mapbender.Point.prototype.dist = function(p){
	return Math.sqrt(Math.pow(this.y-p.y,2) + Math.pow(this.x-p.x,2)) ;
};

/**
 * checks if the coordinates of this {@link Point} match the coordinates of a {@link Point} p
 *
 * @param {Point} p 
 * @return {Boolean} true if the two points are equal; elso false
 */
Mapbender.Point.prototype.equals = function(p){
	if (this.x == p.x && this.y == p.y) {return true;}
	return false;
};

/**
 * subtracts a {@link Point} p from this {@link Point}
 *
 * @param {Point} p 
 * @return a new {@link Point} with the difference of the two points
 */
Mapbender.Point.prototype.minus = function(p){
	return new Mapbender.Point(this.x-p.x, this.y-p.y);
};

/**
 * adds this {@link Point} to a {@link Point} p
 *
 * @param {Point} p 
 * @return a new {@link Point} with the sum of the two points
 */
Mapbender.Point.prototype.plus = function(p){
	return new Mapbender.Point(this.x+p.x, this.y+p.y);
};

/**
 * divides this {@link Point} by a scalar c
 *
 * @param {Float} c divisor
 * @return a new {@link Point} divided by c
 */
Mapbender.Point.prototype.dividedBy = function(c){
	if (c !== 0) {
		return new Mapbender.Point(this.x/c, this.y/c);
	}
	var e = new Mapbender.Exception("Point.dividedBy: Division by zero");
	return false;
};

/**
 * multiplies this {@link Point} by a scalar c
 *
 * @param {Float} c factor
 * @return a new {@link Point} multiplied by c
 */
Mapbender.Point.prototype.times = function(c){
	return new Mapbender.Point(this.x*c, this.y*c);
};

/**
 * rounds the coordinates to numOfDigits digits
 *
 * @param numOfDigits the coordinate will be rounded to numOfDigits digits
 * @return a new {@link Point} rounded to numOfDigits digits
 * @type Point
 */
Mapbender.Point.prototype.round = function(numOfDigits){
	var roundToDigits = function (aFloat, numberOfDigits) {
		var d = parseInt(numberOfDigits, 10);
		return Math.round(aFloat * Math.pow(10, d))	/ Math.pow(10, d);
	};
	
	return new Mapbender.Point(roundToDigits(this.x, numOfDigits), roundToDigits(this.y, numOfDigits));
};

/**
 * @returns a {String} representation of this Point
 * @type String
 */
Mapbender.Point.prototype.toString = function(){
	if (typeof(this.z == "undefined")) {
		return "[" + this.x + ", " + this.y + "]";
	}
	else {
		return "[" + this.x + ", " + this.y + ", " + this.z + "]";
	}
};

Mapbender.Point.prototype.toText = function(){
	if (typeof(this.z == "undefined")) {
		return "POINT(" + this.x + " " + this.y + ")";
	}
	else {
		return "POINT Z (" + this.x + " " + this.y + " " + this.z + ")";
	}
}

/**
 * transforms {@link Point} coords from source crs to dest crs
 * @param {String} source crs
 * @param {String} dest crs
 * @return a new {@link Point} transformed into dest crs
 */
Mapbender.Point.prototype.transform = function(source, dest, callback){
	var source = new Proj4js.Proj(source);
	var dest = new Proj4js.Proj(dest);
	var that = this;
	
	var intervalId = setInterval(function() {
		if(source.readyToUse && dest.readyToUse) {
			clearInterval(intervalId);
			var p = new Proj4js.Point(that.x, that.y);
			p = Proj4js.transform(source, dest, p);
			p = new Mapbender.Point(p.x, p.y);
			callback(p);
        } 
	}, 200);
};
