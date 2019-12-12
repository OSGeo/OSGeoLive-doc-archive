/* 
* $Id: geometry.js 8120 2011-09-08 17:01:26Z kmq $
* COPYRIGHT: (C) 2001 by ccgis. This program is free software under the GNU General Public
* License (>=v2). Read the file gpl.txt that comes with Mapbender for details. 
*/
// http://www.mapbender.org/index.php/GeometryArray.js


var nameGeometryArray = "GeometryArray";
var nameMultiGeometry = "MultiGeometry";
var nameGeometry = "Geometry";

Mapbender.geometryType = {
	polygon: "polygon",
	line: "line",
	point: "point"
};

/**
 * @class A class representing geometry types "polygon", "line" and "point".
 *
 * @constructor
 */
function GeomType(){
	/**
	 * An identifier for polygons. If you want to check if a {@link MultiGeometry}
	 * or {@link Geometry} is a polygon, write
	 * if (someGeometry.geomType == geomType.polygon
	 * 
	 * @type String
	 */
	this.polygon = "polygon";

	/**
	 * An identifier for polygons. If you want to check if a {@link MultiGeometry}
	 * or {@link Geometry} is a line, write
	 * if (someGeometry.geomType == geomType.line
	 * 
	 * @type String
	 */
	this.line = "line";

	/**
	 * An identifier for polygons. If you want to check if a {@link MultiGeometry}
	 * or {@link Geometry} is a point, write
	 * if (someGeometry.geomType == geomType.point
	 * 
	 * @type String
	 */
	this.point = "point";
}
var geomType = new GeomType();


/**
 * @class a {@link GeometryArray} is a {@link List} of {@link MultiGeometry} objects
 *
 * @ extends List
 * @ requires MultiGeometry 
 * @ requires Geometry
 * @ requires Point
 * @ constructor
 */
function GeometryArray(){

	/*
	 * creates a new, empty Multigeometry and adds it to this GeometryArray
	 *
	 * @param {String} geomType a {@link GeomType}
	 */
	this.addMember = function(geomType){
		this.add(new MultiGeometry(geomType));
	};

	this.addCopy = function (feature) {
		this.importGeoJSON(feature.toString());
		this.get(-1).wfs_conf = feature.wfs_conf;
	};
		
	/**
	 * @ignore
	 */
	this.name = nameGeometryArray;
	
	/**
	 * A {@link List} of {@link MultiGeometry} objects.
	 * 
	 * @type List
	 */
	this.list = [];
}

GeometryArray.prototype = new List();
	
/**
 * gets the j-th {@link Geometry} object of the i-th {@link MultiGeometry} object
 *
 * @param {Integer} i index of the MultiGeometry
 * @param {Integer} j index of the Geometry
 * @type Geometry 
 */
GeometryArray.prototype.getGeometry = function(i,j){
	var tmp = this.get(i);
	if (tmp) {
		return tmp.get(j);
	}
	return false;
};

/**
 * gets the k-th Point of the j-th {@link Geometry} object of the i-th {@link MultiGeometry} object
 *
 * @param {Integer} i index of the MultiGeometry
 * @param {Integer} j index of the Geometry
 * @param {Integer} k index of the Point
 * @type Point
 * @returns the Point object at the given indices
 */
GeometryArray.prototype.getPoint = function(i, j, k, l){
	var mg = this.get(i);
	if (mg) {
		var g = mg.get(j);
		if (g) {
			if (l === undefined) {
				return g.get(k);
			}
			else {
				var r = g.innerRings.get(k);
				if (r) {
					return r.get(l);
				}
			}
		}
	}
	return false;
};

GeometryArray.prototype.clone = function () {
	var geomArray = new GeometryArray();
	return geomArray.union(this, false);
};

/**
 * appends a geometry array to an existing geometryArray
 *
 * @param {GeometryArray} geom GeometryArray
 */
GeometryArray.prototype.union = function(geom, uniqueFid){
	var oldLen = this.count();
	var i, j;
	var mygeom = geom;
	//
	// only add geometries that are not already present
	//
	if (typeof uniqueFid === "boolean" && uniqueFid) {
		mygeom = geom.clone();
		
		for (i = 0; i < this.count(); i++) {
			var existingFid = this.get(i).e.getElementValueByName("fid");
			var len = mygeom.count() - 1;
			for (j = len; j >= 0; j--) {
				var fid = mygeom.get(j).e.getElementValueByName("fid");
				if (fid && fid === existingFid) {
					mygeom.del(j);
					continue;
				}
			}
		}
	}

	this.importGeoJSON(mygeom.toString());
	j = 0;
	for (i = oldLen; i < this.count(); i++) {
		if (typeof mygeom.get(j).wfs_conf !== "undefined") {
			this.get(i).wfs_conf = mygeom.get(j).wfs_conf;
		}
		j++;
	}
	return this;

};

/**
 * gets an Array of indices; the {@link MultiGeometry} objects at these indices are equal to geom
 *
 * @type Integer[]
 * @param {MultiGeometry} geom 
 * @returns an Array of indices
 */
GeometryArray.prototype.findMultiGeometry = function(geom) {
	var a = [];
	for (var i=0; i < this.count(); i++) {
		if (this.get(i).equals(geom)) {a.push(i);}
	}
	return a;
};
	
/**
 * deletes the j-th {@link Geometry} object of the i-th {@link MultiGeometry} object
 *
 * @param {Integer} i index of the MultiGeometry
 * @param {Integer} j index of the Geometry
 */
GeometryArray.prototype.delGeometry = function(i,j){
	if (this.get(i).del(j) === false) {
		this.del(i);
	}
};
	
/**
 * deletes the k-th {@link Point} of the j-th {@link Geometry} object of the i-th {@link MultiGeometry} object
 *
 * @param {Integer} i index of the MultiGeometry
 * @param {Integer} j index of the Geometry
 * @param {Integer} k index of the Point
 */
GeometryArray.prototype.delPoint = function (i, j, k, l) {
	var res = this.get(i).delPoint(j, k, l);
	if (res === false) {
		this.del(i);
	}
};
	
/**
 * closes the current {@link MultiGeometry}. Calls method close of the {@link Geometry} class.
 *
 */
GeometryArray.prototype.close = function(i){
	var index;
	if (arguments.length > 0) {
		index = this.getIndex(i);
		if (index === false) {
			return null;
		}
	}
	else {
		index = -1;
	}
	
	if (!this.get(index).get(-1).close()) {
		this.delGeometry(index, -1);
	}
	else {
		if (this.get(index).get(-1).count() === 0) {
			this.get(index).del(-1);
		}
		if (this.get(index).count() === 0) {
			this.del(index);
		}
	}
	
};

/**
 * deletes all {@link Point} objects of this {@link GeometryArray} that equal point
 *
 * @param {Point} point
 */
GeometryArray.prototype.delAllPointsLike = function(point){
	var finished = false;
	var i, j, k, l;
	while (finished === false){
		finished = true;
		for (i = 0 ; finished === true && i < this.count() ; i++){
			for (j = 0 ; finished === true && j < this.get(i).count() ; j++){

				var currentGeometry = this.get(i).get(j);
				if (currentGeometry.geomType == geomType.polygon && currentGeometry.innerRings) {
					for (k = 0; finished === true && k < currentGeometry.innerRings.count(); k++) {
						for (l = 0; finished === true && l < currentGeometry.innerRings.get(k).count(); l++) {
							if (this.getPoint(i, j, k, l).equals(point)) {
								this.delPoint(i, j, k, l);
								finished = false;
							}
						}
					}
				}
				if (!finished) {
					break;
				}

				for (k = 0 ; finished === true && k < this.get(i).get(j).count() ; k++){
					if (this.getPoint(i,j,k).equals(point)){
						this.delPoint(i,j,k);
						finished = false;
					}
				}
			} 
		}
	}
};
	
/**
 * updates all {@link Point} objects of this {@link GeometryArray} that equal oldP to newP
 *
 * @param {Point} oldP
 * @param {Point} newP
 */
GeometryArray.prototype.updateAllPointsLike = function(oldP, newP){
	for (var i = 0; i < this.count(); i++){
		this.get(i).updateAllPointsLike(oldP, newP);
	}
};

GeometryArray.prototype.placemarkToString = function (placemarkId) {
	var i;
	var str = "{\"type\": \"Feature\", \"geometry\": ";

	// get geometries with placemarkId
	var geometriesFromPlacemark = [];
	for (i = 0, len = this.count(); i < len; i++) {
		if (this.get(i).isFromKml() && this.get(i).e.getElementValueByName("Mapbender:placemarkId") == placemarkId) {
			geometriesFromPlacemark.push(i);
		}
	}	

	if (geometriesFromPlacemark.length > 1) {
		str += "{\"type\": \"GeometryCollection\", \"geometries\": [";
		for (i = 0; i < geometriesFromPlacemark.length; i++) {
			if (i > 0) {
				str += ",";
			}	
			str += this.get(geometriesFromPlacemark[i]).placemarkToString();
		}
		str += "]}";

		// metadata is the same for all indices...get from index 0
		var propString = this.get(geometriesFromPlacemark[0]).e.toString();
		if (propString) {
			str += "," + propString;
		}
	}
	else if (geometriesFromPlacemark.length === 1) {
		str += this.get(geometriesFromPlacemark[0]).placemarkToString();
	}

	str += "}";
	return str;
};

GeometryArray.prototype.getBBox = function(){
	var q = this.get(0).get(0).get(0);
	var min = Mapbender.cloneObject(q);
	var max = Mapbender.cloneObject(q);
	for(var i=0; i < this.count();i++){
		var pos = this.get(i).getBBox();
		if (pos[0].x < min.x) {min.x = pos[0].x;}
		if (pos[1].x > max.x) {max.x = pos[1].x;}
		if (pos[1].y > max.y) {max.y = pos[1].y;}
		if (pos[0].y < min.y) {min.y = pos[0].y;}
	}
	return [min, max];
};

GeometryArray.prototype.importGeometryFromText = function (text, srs) {
	var i, j, m, currentPoint;
	// remove whitespace after commata
	text  = text.replace(/,\s*/, ',');
	
	var tmpArray = text.split("(");
	// trim whitespace
	var geometryType = tmpArray[0].replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	
	

	switch (geometryType) {
		case "MULTIPOLYGON":
			var text = text.replace(/\)/g, "");
			var sepArray = text.split("(((");
			var polyArray = sepArray[1].split(",((");
			
			this.addMember(geomType.polygon);
			for (i = 0; i < polyArray.length; i++) {
				var ringArray = polyArray[i].split(",(");
				for (j = 0; j < ringArray.length; j++) {
					var coordinatesArray = ringArray[j].split(",");
					if (j === 0) {
						// add outer ring
						this.get(-1).addGeometry();
						for (m = 0; m < -1 + coordinatesArray.length; m++) {
							currentPoint = coordinatesArray[m].split(" ");
							this.getGeometry(-1, -1).addPointByCoordinates(parseFloat(currentPoint[0]), parseFloat(currentPoint[1]));
							this.getGeometry(-1,-1).setEpsg(srs);
						}
						this.close();
					}		
					else {
						// add inner ring
						var ring = new Geometry(geomType.polygon);
						for (m = 0; m < -1 + coordinatesArray.length; m++) {
							currentPoint = coordinatesArray[m].split(" ");
							ring.addPointByCoordinates(parseFloat(currentPoint[0]), parseFloat(currentPoint[1]));
						}
						ring.close();
						this.getGeometry(-1,-1).addInnerRing(ring);				
						this.getGeometry(-1,-1).setEpsg(srs);
					}
				}
			}
			break;
		case "MULTILINESTRING":
			var text = text.replace(/\)/g, "");
			var sepArray = text.split("((");
			var lineArray = sepArray[1].split(",(");
			
			this.addMember(geomType.line);
			for (var i = 0; i < lineArray.length; i++) {
				var coordinatesArray = lineArray[i].split(",");
				this.get(-1).addGeometry();
				for (var m = 0; m < coordinatesArray.length; m++) {
					var currentPoint = coordinatesArray[m].split(" ");
					this.getGeometry(-1, -1).addPointByCoordinates(parseFloat(currentPoint[0]), parseFloat(currentPoint[1]));
					this.getGeometry(-1,-1).setEpsg(srs);
				}
				this.close();
			}
			break;

		case "LINESTRING":
			// generalize the Linestring to a multilinestring, because that's how Mapbender handles this type of thing
			// Not generalizing Points, because I am not testing it now and something would later break and I'd have to Rage again
			// It's probably possible though, to do it by just copying this case and converting all "linestring" to "point" 
			var multilinestring = text.replace('(','((');
			var multilinestring = multilinestring.replace(')','))');
			var multilinestring = multilinestring.replace("LINESTRING","MULTILINESTRING");
			this.importGeometryFromText(multilinestring,srs);
			break;
		break;

		default:
			throw "Can't Import geometries of Type '" + geometryType + "'";
	}
};

GeometryArray.prototype.importPoint = function(currentGeometry, featureEpsg){
	var coordinates = currentGeometry.coordinates;

	this.addMember(geomType.point);
	
	this.get(-1).addGeometry();
	this.getGeometry(-1,-1).addPointByCoordinates(coordinates[0], coordinates[1], coordinates[2]);
	this.getGeometry(-1,-1).setEpsg(featureEpsg);
	this.close();
};

GeometryArray.prototype.importLine = function(currentGeometry, featureEpsg){
	var coordinates = currentGeometry.coordinates;

	this.addMember(geomType.line);
	this.get(-1).addGeometry();
	for (var m = 0; m < coordinates.length; m++) {
		var currentPoint = coordinates[m];
		this.getGeometry(-1,-1).addPointByCoordinates(currentPoint[0], currentPoint[1], currentPoint[2]);
	}
	this.getGeometry(-1,-1).setEpsg(featureEpsg);
	this.close();
};

GeometryArray.prototype.importMultiLine = function(currentGeometry, featureEpsg){
	var coordinates = currentGeometry.coordinates;

	this.addMember(geomType.line);
	for (var m = 0; m < coordinates.length; m++) {
		this.get(-1).addGeometry();
		var currentLine = coordinates[m];
		for (var n = 0; n < currentLine.length; n++) {
			var currentPoint = currentLine[n];
			this.getGeometry(-1,-1).addPointByCoordinates(currentPoint[0], currentPoint[1], currentPoint[2]);
		}
		this.getGeometry(-1,-1).setEpsg(featureEpsg);
	}
	this.close();
};

GeometryArray.prototype.importMultiPoint = function(currentGeometry, featureEpsg){
	var coordinates = currentGeometry.coordinates;

	this.addMember(geomType.point);
	for (var m = 0; m < coordinates.length; m++) {
		this.get(-1).addGeometry();
		var currentPoint = coordinates[m];
		this.getGeometry(-1,-1).addPointByCoordinates(currentPoint[0], currentPoint[1], currentPoint[2]);
		this.getGeometry(-1,-1).setEpsg(featureEpsg);
	}
	this.close();
};

GeometryArray.prototype.importPolygon = function(currentGeometry, featureEpsg){
	var coordinates = currentGeometry.coordinates;
	
	this.addMember(geomType.polygon);
	for (var m = 0; m < coordinates.length; m++) {
		var currentRing = coordinates[m];
		
		if (m === 0) { 
			this.get(-1).addGeometry();
			for (var p = 0; p < currentRing.length; p++) {
				var currentPoint = currentRing[p];
				this.getGeometry(-1, -1).addPointByCoordinates(currentPoint[0], currentPoint[1], currentPoint[2]);
			}    
		}    
		else {
			var ring = new Geometry(geomType.polygon);
			for (var p = 0; p < currentRing.length; p++) {
				var currentPoint = currentRing[p];
				ring.addPointByCoordinates(currentPoint[0], currentPoint[1], currentPoint[2]);
			}
			ring.close();
			this.getGeometry(-1,-1).addInnerRing(ring);
		}
		this.getGeometry(-1,-1).setEpsg(featureEpsg);
	}
	this.close();
};

GeometryArray.prototype.importMultiPolygon = function(currentGeometry, featureEpsg){
	var coordinates = currentGeometry.coordinates;
	this.addMember(geomType.polygon);
	for (var m = 0; m < coordinates.length; m++) {
		
		this.get(-1).addGeometry();
		var currentPolygon = coordinates[m];
		
		for (var n = 0; n < currentPolygon.length; n++) {
			var currentRing = currentPolygon[n];
			
			if (n === 0) {
				for (var p = 0; p < currentRing.length; p++) {
					var currentPoint = currentRing[p];
					this.getGeometry(-1, -1).addPointByCoordinates(currentPoint[0], currentPoint[1], currentPoint[2]);
				}
				this.getGeometry(-1, -1).close();
			}
			else {
				var ring = new Geometry(geomType.polygon);
				for (var p = 0; p < currentRing.length; p++) {
					var currentPoint = currentRing[p];
					ring.addPointByCoordinates(currentPoint[0], currentPoint[1], currentPoint[2]);
				}
				ring.close();
				this.getGeometry(-1,-1).addInnerRing(ring);
			}
		}
		this.getGeometry(-1,-1).setEpsg(featureEpsg);
	}
	this.close();
};

GeometryArray.prototype.importFeature = function(currentFeature){
	var isFeature = (currentFeature.type == "Feature") ? true : false;

	// add geometry ...
	if (currentFeature.geometry && isFeature) {
		var featureEpsg = "EPSG:4326";
		if (!currentFeature.crs || currentFeature.crs.type !== "name" || !currentFeature.crs.properties.name) {
			var e = new Mb_warning("SRS not set or unknown in GeoJSON. Using 'EPSG:4326'.");
		}
		else {
			featureEpsg = currentFeature.crs.properties.name;
		}

		//
		// GEOMETRY
		//
		var currentGeometry = currentFeature.geometry;
		var geometrytype = currentGeometry.type;
		switch (geometrytype) {
			case "Point":
				this.importPoint(currentGeometry, featureEpsg);
				break;
			
			case "MultiPoint":
				this.importMultiPoint(currentGeometry, featureEpsg);
				break;
			
			case "LineString":
				this.importLine(currentGeometry, featureEpsg);
				break;
				
			case "MultiLineString":
				this.importMultiLine(currentGeometry, featureEpsg);
				break;
			
			case "Polygon":
				this.importPolygon(currentGeometry, featureEpsg);
				break;
				
			case "MultiPolygon":
				this.importMultiPolygon(currentGeometry, featureEpsg);
				break;

			case "GeometryCollection":
				var exc = new Mb_exception("Geometry: GeometryCollections are not yet supported");
				break;
		}
	}
	
	if (currentFeature.properties && currentFeature.geometry !== "") {
		var properties = currentFeature.properties;
		// GeometryCollections are NOT YET IMPLEMENTED
		if (geometrytype != "GeometryCollection") {
			for (var l in properties) {
				if (typeof(properties[l]) != "function") {
					this.get(-1).e.setElement(l, properties[l]);
				}
			}
			if (currentFeature.id) {
				this.get(-1).e.setElement("fid", currentFeature.id);
			}
		}
	}
};
GeometryArray.prototype.importGeoJSON = function (geoJSON) {
	// you can pass either geoJSON or the evaluated geoJSON string
	// for backwards compatibility
	if (typeof(geoJSON) == 'string') {
		var geoJSON = $.parseJSON(geoJSON);
	}

	//
	// FEATURE COLLECTION
	//
	var isFeatureCollection = (geoJSON.type == "FeatureCollection") ? true : false;
	switch (geoJSON.type) {
		case "FeatureCollection" :
			var featureArray = geoJSON.features;
			for (var j = 0; j < featureArray.length; j++) {
				var currentFeature = featureArray[j];
				this.importFeature(currentFeature);
			}
			break;
		case "Feature" :
			this.importFeature(geoJSON);
			break;
	}
	return true;
};

GeometryArray.prototype.featureToString = function (i) {
	var str = "{\"type\": \"FeatureCollection\", \"features\": [";
	str += this.get(i).toString();
	str += "]}";
	return str;
};


GeometryArray.prototype.toString = function () {
	var str = "{\"type\": \"FeatureCollection\", \"features\": [";

	// separate: geometries that are from a KML and those which are not
	var multiGeometriesFromKml = [];
	var multiGeometriesNotFromKml = [];
	for (var i = 0, len = this.count(); i < len; i++) {
		if (this.get(i).isFromKml()) {
			var placemarkId = this.get(i).e.getElementValueByName("Mapbender:placemarkId");

			// only add placemark ids once!
			var isFound = false;
			for (var j = 0; j < multiGeometriesFromKml && isFound === false; j++) {
				if (multiGeometriesFromKml == placemarkId) {
					isFound = true;
				}
			}
			if (!isFound) {
				multiGeometriesFromKml.push(placemarkId);
			}
		}
		else {
			multiGeometriesNotFromKml.push(i);
		}
	}

	// add geometries not from KML
	for (var i = 0, len = multiGeometriesNotFromKml.length; i < len; i++) {
		if (i > 0) {
			str += ",";
		}		
		str += this.get(multiGeometriesNotFromKml[i]).toString();
	}
	
	// add geometries from KML
	if (multiGeometriesNotFromKml.length > 0 && multiGeometriesFromKml.length > 0) {
		str += ",";
	}

	for (var i=0; i < multiGeometriesFromKml.length; i++) {
		if (i > 0) {
			str += ",";
		}	
		str += this.placemarkToString();
	}

	str += "]}";
	return str;
};
	
/**
 * @class a MultiGeometry is a List of Geometry objects
 *
 * @ extends List
 * @ requires Geometry
 * @ requires Point
 * @ constructor
 * @ param {String} geomType a geomType
 */
function MultiGeometry(geomType){

	/*
	 * creates a new, empty {@link Geometry} object and adds it to this {@link MultiGeometry}
	 *
	 */
	this.addGeometry = function(){
		this.add(new Geometry(this.geomType));
	};
	
	/**
	 * deletes the {@link Geometry} object at index i; -1 refers to the last {@link Geometry object in the list
	 * overwrites the del function of {@link List}.
	 *
	 * @param {Integer} i index
	 */
	this.del = function(i){
		i = this.getIndex(i);
		if (i !== false){
			var tmpLength = this.count() - 1;
			for (var z = i; z < tmpLength ; z ++){
				this.list[z] = this.list[z+1];
			}
			this.list.length -= 1;
			if (this.list.length === 0) {return false;}
		}
		return true;
	};

	this.list = [];
	this.e = new Wfs_element();
	this.geomType = geomType;
	this.name = nameMultiGeometry;
}

MultiGeometry.prototype = new List();

/**
 * updates all {@link Point} objects of this {@link MultiGeometry} that equal oldP to newP
 *
 * @param {Point} oldP
 * @param {Point} newP
 */
MultiGeometry.prototype.updateAllPointsLike = function(oldP, newP){
	for (var i = 0; i < this.count(); i++) {
		this.get(i).updateAllPointsLike(oldP, newP);
	}
};

/**
 * gets the bounding box of this {@link MultiGeometry} as an Array of 2 points
 *
 * @return the bounding box
 * @type Array of two Point objects
 */
MultiGeometry.prototype.getBBox = function(){
	var q = this.get(0).get(0);
	var min = Mapbender.cloneObject(q);
	var max = Mapbender.cloneObject(q);
	for(var i=0; i<this.count();i++){
		var pos = this.get(i).getBBox();
		if (pos[0].x < min.x) {min.x = pos[0].x;}
		if (pos[1].x > max.x) {max.x = pos[1].x;}
		if (pos[1].y > max.y) {max.y = pos[1].y;}
		if (pos[0].y < min.y) {min.y = pos[0].y;}
	}
	return [min, max];
};

/**
 * gets the bounding box of this {@link MultiGeometry} as a polygon of four points
 *  
 * @return the bounding box
 * @type MultiGeometry
 */
MultiGeometry.prototype.getBBox4 = function() {
	var bbox = this.getBBox();
	var realBox = new MultiGeometry(geomType.polygon);
	realBox.addGeometry(geomType.polygon);
	realBox.get(-1).addPointByCoordinates(bbox[0].x, bbox[0].y);
	realBox.get(-1).addPointByCoordinates(bbox[0].x, bbox[1].y);
	realBox.get(-1).addPointByCoordinates(bbox[1].x, bbox[1].y);
	realBox.get(-1).addPointByCoordinates(bbox[1].x, bbox[0].y);
	realBox.get(-1).close();
	return realBox;
};

/**
 * gets the center of the bounding box of this {@link MultiGeometry}.
 *
 * @return the center of the bounding box
 * @type Point
 */
MultiGeometry.prototype.getCenter = function(){
	var tmp = this.getBBox();
	var x = parseFloat(tmp[0].x) + parseFloat((tmp[1].x - tmp[0].x)/2);
	var y = parseFloat(tmp[0].y) + parseFloat((tmp[1].y - tmp[0].y)/2);
	return new Point(x,y);
};

/**
 * gets the total number of {@link Point} objects of this {@link MultiGeometry}.
 *
 * @return number of points
 * @type Integer
 */
MultiGeometry.prototype.getTotalPointCount = function(){ 
	var c = 0;
	for (var i = 0 ; i < this.count(); i++)	{
		c += this.get(i).count();
	}
	return c;
};

/**
 * gets the total number of {@link Point} objects of this {@link MultiGeometry}.
 *
 * @return number of points
 * @type Integer
 */
MultiGeometry.prototype.getPoint = function(j,k){
	return this.get(j).get(k);
};

/**
 * compares this {@link MultiGeometry} object with the {@link MultiGeometry} object multigeom.
 *
 * @param {MultiGeometry} multigeom another multigeometry
 * @return true if he multigeometries match; else false 
 * @type Boolean
 */
MultiGeometry.prototype.equals = function(multigeom) {
	if (this.geomType != multigeom.geomType) {return false;}
	if (this.count() != multigeom.count()) {return false;}
	if (this.getTotalPointCount() != multigeom.getTotalPointCount()) {return false;}
	for (var i=0; i<this.count(); i++) {
		if (!this.get(i).equals(multigeom.get(i))) {return false;}
	}
	return true;
};

/**
 * deletes the j-th {@link Point} object of the i-th {@link Geometry} object of this {@link MultiGeometry} object.
 *
 * @param {Integer} i geometry index
 * @param {Integer} j point index
 * @return true if the deletion succeded; else false.
 * @type Boolean
 */
MultiGeometry.prototype.delPoint = function(i, j, k){
	var res;
	if (k === undefined) {
		res = this.get(i).del(j);
		if (res === false) {
			return this.del(i);
		}
	}
	else {
		res = this.get(i).innerRings.get(j).del(k);
		if (res === false) {
			this.get(i).innerRings.del(j);
		}
	}
	return true;
};

MultiGeometry.prototype.isFromKml = function () {
	if (this.e.getElementValueByName("Mapbender:kml")) {
		return true;
	}
	return false;
};

MultiGeometry.prototype.toText = function () {
	var text = "";
	var numOfGeom = this.count();
	if (numOfGeom >= 1) {
		if (this.geomType == geomType.polygon) {
			if (numOfGeom > 1) {
				text += "MULTIPOLYGON (";
				for (var i = 0; i < numOfGeom; i++) {
					if (i > 0) {
						text += ", ";
					}
					var currentPolygon = this.get(i);
					text += "(" + currentPolygon.toText() + ")";
				}
				text += ")";
			}
			else {
				text += "POLYGON (" + this.get(0).toText() + ")";
			}
		}
		else if (this.geomType == geomType.line) {
			text += "LINESTRING (";

			var currentLine = this.get(0);
			for (var j = 0; j < currentLine.count(); j++) {
				if (j > 0) {
					text += ", ";
				}

				var currentPoint = currentLine.get(j);
				text += currentPoint.x + " " + currentPoint.y;
			}

			text += ")";
		}
		else if(this.geomType == geomType.point){
			text = this.get(0).toText();
		}
		
	}
	return text;		
};

MultiGeometry.prototype.toString = function () {
	var str = this.toStringWithoutProperties();
	
	// properties
	var propString = this.e.toString();
	if (propString) {
		str += "," + propString;
	}

	var fid = this.e.getElementValueByName("fid");
	if (fid) {
		str += ", \"id\":\"" + fid + "\"";
	}

	str += "}";
	
	return str;
};

MultiGeometry.prototype.placemarkToString = function () {
	var str = "";
	// geometries
	for (var i = 0, len = this.count(); i < len; i++) {
		if (i > 0) {
			str += ",";
		}		
		str += this.get(i).toString();
	}
	return str;
};

MultiGeometry.prototype.isRectangle = function () {
	if (this.geomType !== Mapbender.geometryType.polygon || this.count() !== 1 || this.get(0).count() !== 5) {
		return false;
	}
	var p1 = this.getPoint(0, 0);
	var p2 = this.getPoint(0, 1);
	var p3 = this.getPoint(0, 2);
	var p4 = this.getPoint(0, 3);

	if (p1.y === p2.y && p1.x === p4.x && p3.y === p4.y && p3.x === p2.x) {
		return true;
	}
	return false;
};

MultiGeometry.prototype.toStringWithoutProperties = function () {
	var str = "{\"type\": \"Feature\", ";

	var epsg = this.getEpsg();
	if (epsg) {
		str += "\"crs\": {\"type\": \"name\", \"properties\": {\"name\": \"" + epsg + "\"}}, ";
	}
	str += "\"geometry\": {";

	var len = this.count(); 
	
	switch (this.geomType) {
		case geomType.polygon:
			if (len > 1) {
				str += "\"type\": \"MultiPolygon\", ";
			}
			else {
				str += "\"type\": \"Polygon\", ";
			}
			break;
		case geomType.line:
			if (len > 1) {
				str += "\"type\": \"MultiLineString\", ";
			}
			else {
				str += "\"type\": \"LineString\", ";
			}
			break;
		case geomType.point:
			if (len > 1) {
				str += "\"type\": \"MultiPoint\", ";
			}
			else {
				str += "\"type\": \"Point\", ";
			}
			break;
	}

	str += "\"coordinates\": ";
	// geometries
	if (len > 1) {
		str += "[";
		for (var i = 0; i < len; i++) {
			if (i > 0) {
				str += ",";
			}		
			str += this.get(i).toString();
		}
		str += "]";
	}
	else if (len === 1) {
		str += this.get(0).toString();
	}
	else {
		str += "[]";
	}

	str += "}";

// this closing curly bracket is added in toString()
//	str += "}";
	
	return str;
};

/**
 * @return the EPSG code of this geometry.
 * @type integer
 */
MultiGeometry.prototype.getEpsg = function () {
	if (this.count() > 0) {
		return this.get(0).getEpsg();
	}
	return false;
};


function InnerRings () {
	this.list = [];
};

InnerRings.prototype = new List();

/**
 * @class a Geometry is a List of Point objects. If it is a polygon, the last point has 
 * to equal the first point.
 *
 * @extends List
 * @requires Point
 * @constructor
 * @param {String} a string representing a geometry type, see @see GeomType.
 */
function Geometry(aGeomtype){

	/**
	 * deletes the {@link Point} object at index i; -1 refers to the last 
	 * {@link Point} object in the list. Overwrites the del function of 
	 * {@link List}.
	 *
	 * @param {Integer} i index
	 * @return false if deletion is not yet finished. It is cascaded to 
	 *         {@link MultiGeometry}. True if the deletion is finished.
	 */
	this.del = function(i){
		i = this.getIndex(i);
		if (i !== false) {
			var tmpLength = this.count()-1;
			
			for (var z = i; z < tmpLength ; z ++){
				this.list[z] = this.list[z+1];
			}
			this.list.length -= 1;
		
			if (this.geomType == geomType.polygon){
				if (i == tmpLength && this.complete) {
					this.list[0] = this.list[tmpLength-1];
				}
				else if (i === 0) {
					this.list[tmpLength-1] = this.list[0];
				}
				if (this.list.length == 1){
					return false;
				}
			}
			updateDist();
			if(this.list.length === 0) {return false;}
			return true;
		}
		return false;
	};

	/**
	 * adds a {@link Point} object to this {@link Geometry} object.
	 *
	 * @param {Float} x x value of the point
	 * @param {Float} y y value of the point
	 */	
	this.addPointByCoordinates = function(x,y,z){
		var newPoint = new Point(x,y,z);
		this.add(newPoint);
		updateDist();
	};

	/**
	 * adds a {@link Point} object to this {@link Geometry} object.
	 *
	 * @param {Point} aPoint another point
	 */	
	this.addPoint = function(aPoint){
		this.add(new Point(aPoint.x, aPoint.y, aPoint.z));
		updateDist();
	};

	/**
	 * inserts a {@link Point} object at index i of this {@link Geometry} object.
	 *
	 * @param {Point} p another point
	 * @param {Integer} i index
	 */	
	this.addPointAtIndex = function(p,i){
		i = this.getIndex(i);
		if (i !== false){
			for(var z = this.count(); z > i; z--){
				this.list[z] = this.list[z-1];
			}
			this.list[i] = new Point(p.x, p.y, p.z);
			updateDist();
		}
	};
	
	/**
	 * Overwrites the {@link Point) object at index i with the {@link Point} object p.
	 *
	 * @private
	 * @param {Point} p another point
	 * @param {Integer} i index
	 */	
	this.updatePointAtIndex = function(p, i){
		i = this.getIndex(i);
		if ((i === 0 || i == this.count()-1) && this.geomType == geomType.polygon){
			this.list[0] = p;
			this.list[this.count()-1] = p;
		}
		else {this.list[i] = p;}
		updateDist();
	};
	
	/**
	 * Updates the {@link Geometry#dist} and {@link Geometry#totaldist}
	 *
	 * @private
	 */	
	var updateDist = function(){
		dist[0] = 0;		
		totaldist[0] = 0;		
		for (var i = 1 ; i < that.count(); i++){
			dist[i] = that.get(i-1).dist(that.get(i));
			totaldist[i] = totaldist[i-1] + dist[i];
		}
	};
	/**
	 * gets the distance between two points i and i+1
	 *
	 * @param {Integer} i index
	 * @param {Integer} numberOfDigits round to numberOfDigits (optional)
	 * @return the distance
	 * @type Float
	 */	
	this.getDist = function(i, numberOfDigits) {
		var indexA = this.getIndex(i);
		var indexB = this.getIndex(i+1);
		if (indexA === false || indexB === false) {
			return 0;
		} 
		if (typeof(numberOfDigits) == "number") {
			return roundToDigits(dist[indexB], numberOfDigits);
		}
		return dist[indexB];
		
	};
	/**
	 * gets the distance between the last and last but one point of this {@link Geometry}.
	 *
	 * @param {Integer} numberOfDigitis round to numberOfDigits (optional)
	 * @return the distance
	 * @type Float
	 */	
	this.getCurrentDist = function(numberOfDigits) {
		if (typeof(numberOfDigits) == "number") {
			return roundToDigits(dist[this.count()-1], numberOfDigits);
		}
		return dist[this.count()-1];
		
	};
	/**
	 * gets the total distance between two points i and i+1
	 *
	 * @param {Integer} i index
	 * @param {Integer} numberOfDigits round to numberOfDigits (optional)
	 * @return the distance
	 * @type Float
	 */	
	this.getAggregatedDist = function(i, numberOfDigits) {
		var indexA = this.getIndex(i);
		var indexB = this.getIndex(i+1);
		if (indexA === false || indexB === false) {
			return 0;
		} 
		if (typeof(numberOfDigits) == "number") {
			return roundToDigits(totaldist[indexB], numberOfDigits);
		}
		return totaldist[indexB];
		
	};
	/**
	 * gets the length of the outer rim of this {@link Geometry}.
	 *
	 * @param {Integer} numberOfDigitis round to numberOfDigits (optional)
	 * @return the distance
	 * @type Float
	 */	
	this.getTotalDist = function(numberOfDigits) {
		if (typeof(numberOfDigits) == "number") {
			return roundToDigits(totaldist[this.count()-1], numberOfDigits);
		}
		return totaldist[this.count()-1];
	};
	/**
	 * closes this {@link Geometry}. 
	 *
	 * @return true if the geometry could be closed; otherwise false
	 * @type Boolean
	 */	
	this.close = function(){
		complete = true;
		if (this.geomType == geomType.polygon){
//			if (this.count() > 2){
				if (this.count() > 0 && !this.get(0).equals(this.get(-1))) {
					this.addPoint(this.get(0));
				}
//			}
//			else {return false;}
		}
		if (this.geomType == geomType.line){
//			if (this.count() < 2){return false;}
		}
		if (this.geomType == geomType.point){
			if (this.count() === 0){return false;}
		}
		return true;
	};

	this.isValid = function(){
		if (this.geomType === geomType.polygon){
			return (this.count() > 3 && this.get(0).equals(this.get(-1))) ? true : false;
		}
		if (this.geomType === geomType.line){
			return (this.count() < 2) ? false : true;
		}
		if (this.geomType == geomType.point){
			return (this.count() === 0) ? false : true;
		}
		return false;
	};
	
	this.reopen = function () {
		if (!complete) {
			return false;
		}
		complete = false;
		if (this.geomType == geomType.polygon){
			this.del(-1);
		}
		return true;
	};
	
	/**
	 * checks if this {@link Geometry} has been closed. 
	 *
	 * @return true if the geometry is closed; otherwise false
	 * @type Boolean
	 */	
	this.isComplete = function() { 
		return complete;
	};
	
	/**
	 * Sets the EPSG of this geometry.
	 * 
	 * @param {Integer} someEpsg the EPSG of this geometry.
	 * @return true if the EPSG could be set; else false
	 * @type boolean
	 */
	this.setEpsg = function (someEpsg) {
		// TODO: how to check if EPSG code is correct?
		epsg = someEpsg;
		return true;

//		var e = new Mb_exception("EPSG code not valid ("+someEpsg+")");
//		return false;
	};
	
	/**
	 * @return the EPSG code of this geometry.
	 * @type integer
	 */
	this.getEpsg = function () {
		return epsg;
	};
	
	this.list = [];
	var dist = [];
	var totaldist = [];
	var complete = false;

	var epsg;

	var that = this;

	this.geomType = aGeomtype;
	this.name = nameGeometry;

	// add these members if the geometry is a polygon
	if (this.geomType == geomType.polygon) {
		this.innerRings = new InnerRings();
		this.addInnerRing = function (somePolygon) {
			this.innerRings.add(somePolygon);
		};
		this.delInnerRing = function (index) {
			this.innerRings.del(index);
		};
	}
}

Geometry.prototype = new List();

Geometry.prototype.toText = function () {
	var text = "";
	switch (this.geomType) {
		case geomType.polygon:
			text += "(";
			for (var j = 0; j < this.count(); j++) {
				if (j > 0) {
					text += ", ";
				}
				var currentPoint = this.get(j);
				text += currentPoint.x + " " + currentPoint.y;
			}
			text += ")";
			if (this.innerRings && this.innerRings.count() > 0) {
				for (var k = 0; k < this.innerRings.count(); k++) {
					text += ", ";
					text += this.innerRings.get(k).toText();
				}				
			}
			break;
		case geomType.point:
			var point = this.get(0);
			if(typeof(!point.z)) {
				return "POINT(" +point.x + " " + point.y + ")";
			}
			else {
				return "POINT Z (" + point.x + " " + point.y + " " + point.z + ")";
			}
			break;
	}
	return text;
};
/**
 * gets the bounding box of this {@link Geometry}
 *
 * @return the bounding box (array of two Point objects)
 * @type Point[]
 */
Geometry.prototype.getBBox = function(){
	var q = this.get(0);
	var min = Mapbender.cloneObject(q);
	var max = Mapbender.cloneObject(q);
	
	for (var j=0; j<this.count(); j++){
		var pos = this.get(j);
		if (pos.x < min.x) {min.x = pos.x;}
		else if (pos.x > max.x) {max.x = pos.x;}
		if (pos.y < min.y) {min.y = pos.y;}
		else if (pos.y > max.y) {max.y = pos.y;}
	}
	if (this.geomType == geomType.polygon) {
		for (var i = 0; i < this.innerRings.count(); i++) {
			var currentRing = this.innerRings.get(i);
			for (var j=0; j<currentRing.count(); j++){
				var pos = currentRing.get(j);
				if (pos.x < min.x) {min.x = pos.x;}
				else if (pos.x > max.x) {max.x = pos.x;}
				if (pos.y < min.y) {min.y = pos.y;}
				else if (pos.y > max.y) {max.y = pos.y;}
			}
		}
	}
	
	return [min, max];
};

/**
 * updates all {@link Point} objects of this {@link Geometry} that equal oldP to newP
 *
 * @param {Point} oldP
 * @param {Point} newP
 */
Geometry.prototype.updateAllPointsLike = function(oldP, newP){
	var len = this.count();
	for (var i = 0; i < len ; i++){
		if (oldP.equals(this.get(i))){
			if (i>0 && newP.equals(this.get(i-1))){
				this.del(i);
				len--;
				i--;
			}
			else {this.updatePointAtIndex(newP, i);}
		}
	}
	if (this.geomType == geomType.polygon) {
		for (var j = 0; j < this.innerRings.count(); j++) {
			var len = this.innerRings.get(j).count();
			for (var i = 0; i < len ; i++){
				if (oldP.equals(this.innerRings.get(j).get(i))){
					if (i>0 && newP.equals(this.innerRings.get(j).get(i-1))){
						this.innerRings.get(j).del(i);
						len--;
						i--;
					}
					else {this.innerRings.get(j).updatePointAtIndex(newP, i);}
				}
			}
			
		}
	}
};

/**
 * compares this {@link Geometry} object with the {@link Geometry} object geom point by point.
 *
 * @param {Geometry} geom another geometry
 * @return true if he geometries match; else false 
 * @type Boolean
 */
Geometry.prototype.equals = function(geom) {
	if (this.geomType != geom.geomType) {return false;}
	if (this.count() != geom.count()) {return false;}
	for (var i=0; i < this.count(); i++) {
		if (!this.get(i).equals(geom.get(i))) {return false;}
	}
	if (!this.innerRings && !geom.innerRings) {
		// no inner rings; fine
	}
	else if (this.innerRings && geom.innerRings) {
		if (this.innerRings.count() != geom.innerRings.count()) {
			return false;
		}
		for (var j = 0; j < this.innerRings.count(); j++) {
			if (!this.innerRings.get(j).equals(geom.innerRings.get(j))) {
				return false;
			}
		}
	}
	else {
		// inner ring mismatch
		return false;
	}	
	return true;
};

/**
 * creates a polygon geometry object which form a buffer around the line geometry
 * 
 * @param {float} real world units to buffer around the line
 * @param {float} (optional) units to buffer around the line in Y direction
 * 
 * @return linebuffer polygon
 * @type Geometry
 */
Geometry.prototype.bufferLine = function(bufferX, bufferY){
	if(typeof(bufferY)=='undefined')
		bufferY = bufferX;
	if (this.geomType != geomType.line || this.count() < 2) {
		return false;
	}
	
	var ret = new Geometry(geomType.polygon);
	
	//get vector from point 0 to point 1
	last_vec = this.get(1).minus(this.get(0));

	//get 90° rotated vector
	last_vec_o = new Point(-last_vec.y, last_vec.x);

	//resize vectors with apropriate linebuffer length
	last_vec_o = last_vec_o.dividedBy(last_vec_o.dist(new Point(0,0)));
	last_vec_o.x*=bufferX; last_vec_o.y*=bufferY;
	last_vec = last_vec.dividedBy(last_vec.dist(new Point(0,0)));
	last_vec.x*=bufferX; last_vec.y*=bufferY;
		
	//add first pointsets
	ret.list.unshift(this.get(0).plus(last_vec_o).minus(last_vec));
	ret.list.push(this.get(0).minus(last_vec_o).minus(last_vec));
		
	for(var i=1;i<this.count()-1;i++){
		//get vector from point n to point n+1
		vec = this.get(i+1).minus(this.get(i));
		//get orthogonal (90° rotated) vector		
		vec_o = new Point(-vec.y, vec.x);

		//resize vectors to linebuffer length
		vec_o = vec_o.dividedBy(vec_o.dist(new Point(0,0)));
		vec_o.x*=bufferX; vec_o.y*=bufferY;
		vec = vec.dividedBy(vec.dist(new Point(0,0)));
		vec.x*=bufferX; vec.y*=bufferY;
			
		//if direction is the same continue
		if(vec.equals(last_vec)) {
			continue;
		}
			
		// calculate directed angle between the two vectors by 
		// calculating the argument diffenrences between complex numbers
		// arg(x + i*y) (because law of cosine can onlycalculate undirected angle)
		var angle = (Math.atan2(vec.x,vec.y)-Math.atan2(last_vec.x,last_vec.y))
		//ensure that angle is -180<=angle<=180
		if(angle<-Math.PI)angle=2*Math.PI+angle;
		if(angle>+Math.PI)angle=2*Math.PI-angle;
		
		//calculate the distance between the next points on boundary
		//and the line point
		//the point will be in the direction of angle/2 relative to last_vec_o
		//since cosine is adjacent side / hypothenuse and we know that 
		//the adjacent side is lineBuffer the hypothenus (our distance) is
		var ndist = 1/(Math.cos(angle/2))
		//direction of next points on boundary
		var int_vec = vec_o.plus(last_vec_o);
		//resize direction vector to our distance
		int_vec = int_vec.times(ndist/int_vec.dist(new Point(0,0)));
		int_vec.x*=bufferX; int_vec.y*=bufferY;
		
		//look if we have an outer sharp corner (>90°)
		if(angle>Math.PI/2){
			//push cutted edge points
			ret.list.unshift(this.get(i).plus(last_vec_o).plus(last_vec));
			ret.list.unshift(this.get(i).plus(vec_o).minus(vec));
		}
		else{
			//push inner/light edge
			ret.list.unshift(this.get(i).plus(int_vec));
		}

		//look if we have an inner sharp corner (<-90°)
		if(angle<-Math.PI/2){
			//push cutted edge points
			ret.list.push(this.get(i).minus(last_vec_o).plus(last_vec));
			ret.list.push(this.get(i).minus(vec_o).minus(vec));
		}
		else{
			//push inner/light edge
			ret.list.push(this.get(i).minus(int_vec));
		}
			
		//copy for next point
		last_vec = vec;
		last_vec_o = vec_o;
	}
	//add last pointsets
	ret.list.unshift(this.get(i).plus(last_vec_o).plus(last_vec));
	ret.list.push(this.get(i).minus(last_vec_o).plus(last_vec));
	
	ret.close();

	return ret;	
};

Geometry.prototype.toString = function () {
	var str = "";
	if (this.geomType == geomType.polygon) {
		str += "[[";
		for (var i = 0; i < this.count(); i++) {
			if (i > 0) {
				str += ", ";
			}
			str += this.get(i).toString();
		}
		if (this.count() > 0 && !this.get(0).equals(this.get(i-1))) {
			str += ", " + this.get(0).toString();
		}

		if (typeof(this.innerRings) == "object" && this.innerRings.count() > 0) {
			for (var j = 0; j < this.innerRings.count(); j++) {
				var currentRing = this.innerRings.get(j);

				str += "],[";
				for (var i = 0; i < currentRing.count(); i++) {
					if (i > 0) {
						str += ", ";
					}
					str += currentRing.get(i).toString();
				}
				if (currentRing.count() > 0 && !currentRing.get(0).equals(currentRing.get(i-1))) {
					str += ", " + currentRing.get(0).toString();
				}
			}
		}
		str += "]]";
	}
	else if (this.geomType == geomType.line) {
		str += "[";
		for (var i = 0; i < this.count(); i++) {
			if (i > 0) {
				str += ", ";
			}
			str += this.get(i).toString();
		}
		str += "]";
	}
	else if (this.geomType == geomType.point) {
		str += this.get(0).toString();
	}
	
	return str;
};



/**
 * @class an array of elements, each consisting of a name/value pair
 *
 * @ constructor
 */
function Wfs_element(){

	/**
	 * returns the number of elements of this {@link Wfs_element} object.
	 *
	 * @return the number of elements
	 * @type Integer
	 */
	this.count = function(){
		return this.name.length;
	};

	/**
	 * returns the name of the element at index i.
	 *
	 * @param {Integer} i index
	 * @return the name
	 * @type String
	 */
	this.getName = function(i){ 
		if (this.isValidElementIndex(i)) {return this.name[i];}
		return false;
	};
	
	/**
	 * returns the value of the element at index i.
	 *
	 * @param {Integer} i index
	 * @return the value
	 */
	this.getValue = function(i){ 
		if (this.isValidElementIndex(i)) {return this.value[i];}
		return false;
	};

	/**
	 * appends a new element with a given name. If an element with this name exists, it is overwritten.
	 *
	 * @param {String} aName the name of the new element
	 * @param {String} aValue the value of the new element
	 */
	this.setElement = function(aName, aValue){ 
		var i = this.getElementIndexByName(aName);
		if (i === false) {i = this.count();}
		this.name[i] = aName;
		this.value[i] = aValue;
	};

	/**
	 * removes an element with a given name. If an element with this name exists, it is removed.
	 *
	 * @param {String} aName the name of the element to delete
	 */
	this.delElement = function(aName){
		var i = this.getElementIndexByName(aName);
		if (i !== false) {
			this.name.splice(i, 1);
			this.value.splice(i, 1);
		}
	};
	
	/**
	 * checks if an index is valid
	 *
	 * @private
	 * @param {Integer} i an index
	 * @return true if the index is valid; otherwise false
	 * @type Boolean
	 */
	this.isValidElementIndex = function(i){ 
		if (i>=0 && i<this.name.length) {return true;}
		var e = new Mb_exception("class Wfs_element: function isValidElementIndex: illegal element index");
		return false;
	};
	
	this.name  = [];
	this.value = [];
}

/**
 * gets the index of the element with a given name.
 *
 * @param {String} elementName a name
 * @return the index of the element; if no element with this name exists, false
 * @type Integer, Boolean
 */
Wfs_element.prototype.getElementIndexByName = function(elementName){
	for (var j = 0, len = this.count() ; j < len ; j++){
		if (this.getName(j) == elementName) {return j;}
	}
	return false;
};

/**
 * gets the value of the element with a given name.
 *
 * @param {String} elementName a name
 * @return the value of the element; if no element with this name exists, false
 * @type String, Boolean
 */
Wfs_element.prototype.getElementValueByName = function(elementName){
	var i = this.getElementIndexByName(elementName);
	if (i === false) {return false;}
	return this.getValue(i);
};

Wfs_element.prototype.toString = function () {
	var str = "";
	if (this.count() > 0) {
		str += "\"properties\": {";
		
		for (i = 0, len = this.count(); i < len; i++) {
			if (i > 0) {
				str += ",";
			}		
			var key = this.getName(i);
			var value = this.getValue(i);
			str += "\"" + key + "\":\"" + (value+'').replace(/([\\"'])/g, "\\$1").replace(/\u0000/g, "\\0").replace(/\n/g, "\\n").replace(/\r/g, "\\r") + "\"";
		}
		str += "}";
	}
	return str;
};

/**
 * @class a {@link Canvas} contains a {@link DivTag} that holds graphics rendered by {@link jsGraphics}
 *
 * @constructor
 * @requires DivTag
 * @requires jsGraphics
 * @requires GeometryArray
 * @requires MultiGeometry
 * @requires Geometry
 * @param {String} aMapFrame name of the target mapframe
 * @param {String} aTagName name of the target div tag
 * @param {String} aStyle style of the div tag
 * @param {Integer} aLineWidth the line width of the jsGraphics output
 */
function Canvas(aMapframe, aTagName, aStyle, aLineWidth) {
	
	/**
	 * draws the geometry of the canvas
	 *
	 * @param {String} t geometry type (@see GeomType)
	 * @param {MultiGeometry} g a MultiGeometry object
	 * @param {String} col a color
	 * @private
	 */
 	this.drawGeometry = function(t,g,col){
 		var mapframeWidth = map.width;
		var mapframeHeight = map.height;
		var node = null;
	
		if(t == geomType.point) {
			var poiIcon = g.e.getElementValueByName("Mapbender:icon");
			var poiOffsetX = parseInt(g.e.getElementValueByName("Mapbender:iconOffsetX"), 10);
			var poiOffsetY = parseInt(g.e.getElementValueByName("Mapbender:iconOffsetY"), 10);

			var poiWidth = parseInt(g.e.getElementValueByName("Mapbender:iconWidth"), 10);
			var poiHeight = parseInt(g.e.getElementValueByName("Mapbender:iconHeight"), 10);

			for(var i=0, ilen = g.count(); i < ilen; i++){
				var currentGeom = g.get(i);
				var p = realToMap(mapframe, currentGeom.get(0));
				var px = p.x;
				var py = p.y;
				var radius = diameter/2;
				if ((px - radius < mapframeWidth && px + radius > 0 &&
					py - radius < mapframeHeight && py + radius > 0) ||
					(p.dist(new Point(0,0)) < radius || 
					 p.dist(new Point(mapframeWidth, mapframeHeight)) < radius ||
					 p.dist(new Point(0,mapframeHeight)) < radius || 
					 p.dist(new Point(mapframeWidth, 0)) < radius
					)
				) {
					// if the point contains a link to an icon, display the icon
					if (poiIcon) {
						if (isNaN(poiWidth) || isNaN(poiHeight)) {
							node = displayIcon(poiIcon, px, py, poiOffsetX, poiOffsetY);
						}
						else {
							node = displayIcon(poiIcon, px, py, poiOffsetX, poiOffsetY, poiWidth, poiHeight);
						}
					}
					else {
						node = drawCircle(px-1, py-1, diameter,col);
					}
				}
			}
		}
		else if(t == geomType.line || t==geomType.polygon) {
			if (typeof Raphael !== "undefined") {
				var path = "";
				//				if (t == geomType.line) {
				for (var i = 0, ilen = g.count(); i < ilen; i++) {
					var currentGeom = g.get(i);
					var previousPoint = realToMap(mapframe, currentGeom.get(0));
					var segment = "";
					for (var j = 1, jlen = currentGeom.count(); j < jlen; j++) {
						(function(){
							var currentPoint = realToMap(mapframe, currentGeom.get(j));
							
							var pq = calculateVisibleDash(previousPoint, currentPoint, mapframeWidth, mapframeHeight);
							if (pq) {
								if (segment === "") {
									segment += "M" + pq[0].x + " " + pq[0].y;
								}
								else {
									if (!previousPoint.equals(pq[0])) {
										if (t == geomType.polygon) {
											segment += "L" + pq[0].x + " " + pq[0].y;
										}
										else {
											segment += "M" + pq[0].x + " " + pq[0].y;
										}
									}
								}
								segment += " L" + pq[1].x + " " + pq[1].y;
							}
							previousPoint = currentPoint;
						})();
					}
					if (t == geomType.polygon) {
						segment += " Z";
					}
					path += segment;
				}
				//				}
				var canvasPath = canvas.path(path);
				node = canvasPath.node;
				if (t == geomType.polygon) {
					canvasPath.attr({
						"fill": col,
						"stroke": col,
						"stroke-width": lineWidth,
						"fill-opacity": 0.1
					});
				}
				else {
					canvasPath.attr({
						"stroke": col,
						"stroke-width": lineWidth
					});
				}
			}
			else {	
				for(var i=0, ilen = g.count(); i < ilen; i++){
					var currentGeom = g.get(i);
					// paint inner rings
					if (t==geomType.polygon && currentGeom.innerRings.count() > 0) {
						for (var k = 0; k < currentGeom.innerRings.count(); k++) {
							var currentRing = currentGeom.innerRings.get(k);
							var previousPoint = realToMap(mapframe, currentRing.get(0));
							for (var j=1, jlen = currentRing.count(); j < jlen; j++) {
								(function () {
									var currentPoint = realToMap(mapframe, currentRing.get(j));
									
									var pq = calculateVisibleDash(previousPoint, currentPoint, mapframeWidth, mapframeHeight);
									if (pq) {
										drawLine([pq[0].x-1, pq[1].x-1], [pq[0].y-1, pq[1].y-1], col);
									}
									previousPoint = currentPoint;
								})();
							}
							
						}					
					}
					// paint line or outer ring
					var previousPoint = realToMap(mapframe, currentGeom.get(0));
					for (var j=1, jlen = currentGeom.count(); j < jlen; j++) {
						(function () {
							var currentPoint = realToMap(mapframe, currentGeom.get(j));
							
							var pq = calculateVisibleDash(previousPoint, currentPoint, mapframeWidth, mapframeHeight);
							if (pq) {
								drawLine([pq[0].x-1, pq[1].x-1], [pq[0].y-1, pq[1].y-1], col);
							}
							previousPoint = currentPoint;
						})();
					}
				}
			}
		}
		else {
			var e = new Mb_exception("class Canvas: function drawGeometry: unknown geomType " + t);
		}
	return node;
	};

	/**
	 * checks if the MultiGeometry's bounding box width and height is smaller than minWidth
	 *
	 * @private
	 * @param {MultiGeometry} g a MultiGeometry object
	 */
	this.isTooSmall = function(g){
		// TODO switch between dot, original, circle
//		return false;

		var tmp = g.getBBox();
		var min = realToMap(mapframe,tmp[0]);
		var max = realToMap(mapframe,tmp[1]);
		if((Math.abs(max.x - min.x) < minWidth) && (Math.abs(max.y - min.y) < minWidth)) {
			return true;
		}
		return false;
	};
	
	/**
	 * gets the jsGraphics.
	 *
	 * @private
	 * @return the jsGraphics
	 * @type jsGraphics
	 */
	this.getCanvas = function(){
		return canvas;
	};
	
	this.setDiameter = function (px) {
		diameter = px;
	};
	
	/**
	 * draws a circle with {@link jsGraphics}.
	 *
	 * @private
	 * @param {Float} x x value of the center
	 * @param {Float} y y value of the center
	 * @param {Float} diameter diameter of the circle
	 * @param {String} color the color of the circle in hex format
	 */
	var drawCircle = function(x, y, diameter, color) {
		if (typeof Raphael !== "undefined") {
			var c = canvas.circle(x, y, diameter/2);
			node = c.node;
			c.attr({
				"fill": color,
				"stroke": color,
				"stroke-width": lineWidth,
				"fill-opacity": 0.5
			});		
		}
		else {
			canvas.setColor(color);
			canvas.drawEllipse(x-diameter/2,y-diameter/2,diameter,diameter);
		}
		return node;
	};

	/**
	 * draws a polyline with {@link jsGraphics}.
	 *
	 * @private
	 * @param {Array} x_array array of x values
	 * @param {Array} y_array array of y values
	 * @param {String} color the color of the polyline in hex format
	 */
	var drawLine = function(x_array, y_array, color) {
		if (typeof Raphael !== "undefined") {
		}
		else {
			canvas.setColor(color);
			canvas.drawPolyline(x_array, y_array);
		}
	};

	/**
	 * Displays an icon in the mapframe
	 * 
	 * @private
	 * @param {String} url link to the image
	 * @param {Float} x x coordinate within the map frame
	 * @param {Float} y y coordinate within the map frame
	 */
	var displayIcon = function (url, x, y, offsetX, offsetY, width, height) {
		var node = null;
		if (typeof offsetX !== "number" || isNaN(offsetX)) {
			offsetX = -40;
		}

		if (typeof offsetY !== "number" || isNaN(offsetY)) {
			offsetY = -40;
		}
		var newImgTop = y + offsetY;
		var newImgLeft = x + offsetX;

		if (typeof Raphael !== "undefined") {
			if (width !== undefined && height !== undefined) {
				var img = canvas.image(url, newImgLeft, newImgTop, width, height);
				node = img.node;
			}
			else {
                                //append tmp img to body to get img width and height for IE
                                var $tmpImg = $("body").append('<img style="visibility:hidden;" id="tmpImg" src="'+url+'" />');
                                var $img = $('<img src="'+url+'" />');
                                var width = $("#tmpImg").attr('width')||19;
                                var height = $("#tmpImg").attr('height')||34;
                                //remove tmp img
                                $("#tmpImg").remove();
                                var img = canvas.image(url, newImgLeft, newImgTop, width, height);
				node = img.node;
				
			}
		}
		else {
			var $img = $("<img title='mapSymbol' class='mapSymbol' src='" + 
			url + "' style='position:absolute;top:" + newImgTop + "px;left:" + newImgLeft + 
			"px;" + (width !== undefined ? "width:" + width + "px;" : "") +
			(height !== undefined ? "height:" + height + "px;" : "") +
			";z-index:100;display:none'/>")
			$(that.canvasDivTag.getTag()).html($img).children("img").show();
			node = $img.get(0);
		}
		return node;
	};
	
	/**
	 * This is the {@link DivTag} that contains the output by {@link jsGraphics}.
	 * 
	 * @type DivTag
	 */
	var mapframe = aMapframe;
	var map = Mapbender.modules[mapframe];
	if (!map) {
		new Mb_exception(mapframe + " not found by geometry.js.");
		return;
	}
	if (map.getDomElement().frameName) {
		this.canvasDivTag = new DivTag(aTagName, mapName, aStyle);
	}
	else {
		this.canvasDivTag = new DivTag(aTagName, "", aStyle, map.getDomElement());
	}

	var that = this;

	var diameter = 8;
	var minWidth = 8;
	var lineWidth = aLineWidth || 2;
	var style = aStyle;
	if (typeof Raphael !== "undefined") {
		var canvas = Raphael(this.canvasDivTag.getTag(), map.getWidth(), map.getHeight());
		map.events.dimensionsChanged.register(function (obj) {
			canvas.setSize(obj.width, obj.height);
		});
		
//		$(this.canvasDivTag.getTag()).bind("mousedown", function (e) {
//			e.preventDefault();
//		}).bind("mouseover", function (e) {
//			e.preventDefault();
//		}).bind("mousemove", function (e) {
//			e.preventDefault();
//		});		
	}
	else {
		var canvas = new jsGraphics(aTagName, map.getDomElement().frameName ? window.frames[mapframe] : window);
		canvas.setStroke(lineWidth);
	}
	if (!map.isOverview) {
		mb_registerPanSubElement(aTagName);
	}
}

/**
 * cleans the canvas by emptying the canvas {@link DivTag}.
 */
Canvas.prototype.clean = function () {
	if (typeof Raphael !== "undefined") {
		this.getCanvas().clear();
	}
	else {
		this.canvasDivTag.clean();
	}
};

/**
 * paints all geometries.
 *
 * @param {GeometryArray} gA the geometries that will be drawn
 */
Canvas.prototype.paint = function(gA) {
	var nodes = [];
	var node = null;
	for (var q = 0; q < gA.count(); q++) {
		var m = gA.get(q);
		var t = m.geomType;
		var col = m.color;
		if (t == geomType.point) {
			node =this.drawGeometry(t,m,col);
			nodes.push(node);
		}
		else {
			if (this.isTooSmall(m)){
				var newMember = new MultiGeometry(geomType.point);
				newMember.addGeometry();
				newMember.get(-1).addPoint(m.getCenter());
				node = this.drawGeometry(geomType.point,newMember,col);
				nodes.push(node);
			}
			else{
				if(t == geomType.line) {
					node = this.drawGeometry(t,m, col);
					nodes.push(node);
				}
				else if(t == geomType.polygon) {
					node = this.drawGeometry(t,m,col);
					nodes.push(node);
				}
				else {
					var e = new Mb_exception("class Canvas: function paint: unknown geomType" + t);				
				}
			}
		}
	}
	if (typeof Raphael === "undefined") {
		this.getCanvas().paint();
	}
	return nodes;
};

/**
 * @class a {@link Highlight} object is {@link jsGraphics} rendering of a {@link GeometryArray} in various mapframes.
 *
 * @constructor
 * @requires Canvas
 * @requires GeometryArray
 * @param {Array} aTargetArray an array of Strings referring to mapframes
 * @param {String} aTagName the name of the div tags
 * @param {Object} aStyle the style of the div tags
 * @param {Integer} the line width of the jsGraphics lines
 */
function Highlight(aTargetArray, aTagName, aStyle, aLineWidth, printable) {
	var nodes = [];
	/**
	 * removes a {@link MultiGeometry} object from the geometry Array
	 *
	 * @param {MultiGeometry} m a MultiGeometry
	 * @param {String} color a color
	 */	
	this.del = function(m, color) {
		var newMultiGeom;
		if (m.name == nameMultiGeometry) {
			newMultiGeom = m;
		}
		else if (m.name == nameGeometry) {
			var newMultiGeom = new MultiGeometry(m.geomType);
			newMultiGeom.add(m);
		}

		var a = gA.findMultiGeometry(newMultiGeom);
		var del = false;
		for (var i=0; i<a.length && del === false; i++) {
			if (gA.get(a[i]).color == color) {
				gA.del(a[i]);
				del = true;
			}
		}
	};

	/**
	 * adds a {@link MultiGeometry} object to the geometry Array
	 *
	 * @param {MultiGeometry} m a MultiGeometry
	 * @param {String} color the color of the highlight
	 */	
	this.add = function(m, color) {

		if (m.name == nameMultiGeometry) {
			gA.addCopy(m);
		}
		else if (m.name == nameGeometry) {
			var newMultiGeom = new MultiGeometry(m.geomType);
			newMultiGeom.add(m);
			gA.addCopy(newMultiGeom);
		}
		if (typeof(color) != 'undefined') {gA.get(-1).color = color;} 
		else {gA.get(-1).color = lineColor;}
	};
	
	this.hide = function () {
		for (var i=0; i < canvas.length; i++) {
			if (typeof(canvas[i]) == "object") {canvas[i].clean();}
		}
	};


	/**
	 * removes all MultiGeometries.
	 *
	 */	
	this.clean = function() {
		if (gA.count() > 0) {
			gA = new GeometryArray();
			this.paint();
		}
        if (printable){
            window.GlobalPrintableGeometries = null;
        }
	};

	/**
	 * displays the highlight
	 *
	 */	
	this.paint = function() {
        if (printable){
            window.GlobalPrintableGeometries = gA;
        }
		this.hide();
		for (var i=0; i < canvas.length; i++) {
			if (typeof(canvas[i]) == "object") {canvas[i].clean();}
		}
		for (var i=0; i<targets.length; i++){
			if (typeof(canvas[i]) == 'undefined') {
				canvas[i] = new Canvas(targets[i], tagname + i, style, lineWidth);
			}
			nodes = canvas[i].paint(gA);
		}
	};

	this.setDiameter = function (radius) {
		for (var i = 0; i < targets.length; i++) {
			if (typeof(canvas[i]) != "undefined") {
				canvas[i].setDiameter(radius);
			}
		}
	}

	this.setMouseOver = function (callback) {
		for (var i=0; i<targets.length; i++){
			if (typeof(canvas[i]) !== 'undefined') {
				$(canvas[i].canvasDivTag.getTag()).mouseover(function (e) {
					callback(e);
				});
			}
		}
	};
	
	this.setMouseOut = function (callback) {
		for (var i=0; i<targets.length; i++){
			if (typeof(canvas[i]) !== 'undefined') {
				$(canvas[i].canvasDivTag.getTag()).mouseout(function (e) {
					callback(e);
				});
			}
		}
	};
	
	this.setMouseClick = function (callback) {
		for (var i=0; i<targets.length; i++){
			if (typeof(canvas[i]) !== 'undefined') {
				$(canvas[i].canvasDivTag.getTag()).click(function (e) {
					callback(e);
				});
			}
		}
	};

		
	this.getNodes = function(){
		return nodes;
	};
	
	var lineWidth = aLineWidth;
	var tagname = 'mod_gaz_draw'+aTagName;
	var style = aStyle;
	var targets = aTargetArray; 
	var canvas = []; 
	var gA = new GeometryArray(); 
	var lineColor = "#ff0000";
	this.paint();
}

// ----------------------------------------------------------------------------------------------------
// Snapping
// ----------------------------------------------------------------------------------------------------
/**
 * @class a {@link Snapping} object stores is {@link jsGraphics} rendering of a {@link GeometryArray} in various mapframes.
 *
 * @constructor
 * @requires GeometryArray
 * @requires Highlight
 * @param {String} aTarget name of the mapframe where snapping occurs
 * @param {String} aTolerance Snapping is activated if the mouse is 
 *                 within aTolerance pixel distance to the reference point.
 * @param {String} aColor apparently deprecated?
 * @param {Integer} aZIndex the z-Index of the {@link jsGraphics} generated by {@link Highlight}.
 */
function Snapping(aTarget, aTolerance, aColor, aZIndex){

	/**
	 * draws a circle to highlight the snapped point.
	 * 
	 * @param {Point} center the snapped point.
	 * @param {Integer} radius radius of the circular highlight.
	 */
	this.draw = function(center,radius){ 
		mG = new MultiGeometry(geomType.point);
		mG.addGeometry();
		mG.get(-1).addPoint(center);
		highlight.add(mG,snappingColor);
		highlight.paint();
	};
	this.getTolerance = function() {
		return tolerance;
	};
	this.getTarget = function() {
		return target;
	};
	this.cleanHighlight = function() {
		return highlight.clean();
	};
	this.addPoint = function(aPoint) {
		coord.add(aPoint);
	};
	this.removePoint = function (aPoint) {
		var len = coord.count() - 1;
		for (var i = len; i >= 0; i--) {
			if (coord.get(i).equals(aPoint)) {
				coord.del(i);
			}
		}
	};
	this.getPointCount = function() {
		return coord.count();
	};
	this.getPoint = function(i) {
		return coord.get(i);
	};
	this.resetPoints = function() {
		coord.empty();
	};
	this.getNearestNeighbour = function(){
		if (min_i != -1) {return this.getPoint(min_i);}
		return false;
	};
	this.setIndexOfNearestNeighbour = function(i){
		min_i = i;
	};
	this.resetIndexOfNearestNeighbour = function(){
		min_i = -1;
	};
	
	this.toString = function () {
		return coord.list.join(",");
	};
	
	/**
	 * @private
	 */
	var tolerance = (typeof(aTolerance) == 'undefined') ? 10 : aTolerance;

	/**
	 * @private
	 */
	var zIndex = (typeof(aZIndex) == 'undefined') ? 50 : aZIndex;

	/**
	 * @private
	 */
	var coord = new List(); 
	coord.list = [];

	/**
	 * @private
	 */
	var min_i = -1;

	/**
	 * @private
	 */
	var target = aTarget;

	/**
	 * @private
	 */
	var snappingColor = aColor;
	
	/**
	 * @private
	 */
	var lineWidth = 2;

	/**
	 * @private
	 */
	var style = {"position":"absolute", "top":"0px", "left":"0px", "z-index":zIndex};

	/**
	 * @private
	 */
	var highlight = new Highlight([target], "snapping"+Math.round(Math.random()*Math.pow(10,10)), style, lineWidth);
}

Snapping.prototype.check = function(currPoint){
	var minDist = false;
	if (this.getPointCount() === 0) {
		return;
	}
	
	for (var i = 0 ; i < this.getPointCount() ; i++) {

		var currDist = currPoint.dist(realToMap(this.getTarget(), this.getPoint(i)));
		if (minDist === false || currDist < minDist) {
			minDist = currDist;
			if (minDist < this.getTolerance()) {this.setIndexOfNearestNeighbour(i);}
		}
	}
	if (this.getPointCount() > 0 && minDist > this.getTolerance()) {
		this.resetIndexOfNearestNeighbour();
	}
	this.cleanHighlight();
	if (this.isSnapped()) {
		this.draw(this.getNearestNeighbour(), this.getTolerance());
	}
};

/**
 * Stores the points which will have the snapping property. 
 * 
 * @param {GeometryArray} geom all points of geom will be stored. May also be a 
 *                             {@link MultiGeometry} or {@link Geometry}.
 * @param {Point} point this point is excluded. Useful when moving a point of a 
 *                      geometry; you don't want to snap against the point you
 *                      move. Optional.
 */
Snapping.prototype.store = function(geom, point){
	this.resetPoints();
	this.resetIndexOfNearestNeighbour();

	for (var i = 0 ; i < geom.count(); i++){
		if (geom.name == nameGeometryArray || geom.name == nameMultiGeometry){
			for (var j = 0 ; j < geom.get(i).count() ; j++){
				if (geom.get(i).name == nameMultiGeometry){
					// inner rings
					if (geom.get(i).get(j).geomType == geomType.polygon && geom.get(i).get(j).innerRings && geom.get(i).get(j).innerRings.count() > 0) {
						for (var l = 0; l < geom.get(i).get(j).innerRings.count(); l++) {
							var currentRing = geom.get(i).get(j).innerRings.get(l);
							for (var k = 0 ; k < currentRing.count() ; k++){
								if ((currentRing.isComplete() === true && typeof(point) == 'undefined') || (typeof(point) != 'undefined' && !currentRing.get(k).equals(point))){
									this.add(currentRing.get(k));
								}
							}
						}
					}
					// lines, points, outer rings
					for (var k = 0 ; k < geom.get(i).get(j).count() ; k++) {
						//if ((geom.get(i).get(j).isComplete() === true && typeof(point) == 'undefined') || (typeof(point) != 'undefined' && !geom.get(i).get(j).get(k).equals(point))){
							this.add(geom.getPoint(i, j, k));
						//}
					}
				}
				else {
					if ((geom.get(i).isComplete() === true && typeof(point) == 'undefined') || (typeof(point) != 'undefined' && !geom.get(i).get(j).get(k).equals(point))){
						this.add(geom.getPoint(i, j));
					}
				}
			}
		}
		else {
			if (typeof(point) != 'undefined' && !geom.get(i).get(j).get(k).equals(point)){
				this.add(geom.get(i));
			}
		}
	}
};

/**
 * Determines whether a point is within snapping distance to the mouse cursor
 * 
 * @return true if a point is within snapping distance; else false
 * @type Boolean
 */
Snapping.prototype.isSnapped = function(){ 
	if (this.getNearestNeighbour() !== false) {return true;}
	return false;
};

/**
 * Returns the point that is within snapping distance and closest to the mouse cursor.
 * 
 * @return the point (if there is any); else false
 * @type Point
 */
Snapping.prototype.getSnappedPoint = function(){
	return this.getNearestNeighbour();
};

/**
 * Adds the point to the stored points with snapping property.
 * 
 * @param {Point} point which receives snapping property.
 */
Snapping.prototype.add = function(aPoint){ 
	this.addPoint(aPoint);
};

/**
 * Removes the highlight.
 */
Snapping.prototype.clean = function(){
	this.cleanHighlight();
};



// ----------------------------------------------------------------------------------------------------
// misc. functions
// ----------------------------------------------------------------------------------------------------

/**
 * @ignore
 */
function calculateVisibleDash (a, b, width, height) {
	var p0 = new Mapbender.Point(a);
	var p1 = new Mapbender.Point(b);
	if (p0.x > p1.x) {
		var switched = true; 
		var p_temp = p0; 
		p0 = p1; 
		p1 = p_temp; p_temp = null;
	}
	var p = p0; var q = p1; var m; var ix; var iy;
	if (p1.x != p0.x) {
		m = -(p1.y-p0.y)/(p1.x-p0.x); 
		if (p0.x < width && p1.x > 0 && !(p0.y < 0 && p1.y < 0) && !(p0.y > height && p1.y > height) ) {
			if (p0.x < 0) {
				iy = p0.y - m*(0-p0.x);
				if (iy > 0 && iy < height) {p = new Point(0, iy);}
				else if (iy > height) {
				    ix = p0.x+((p0.y - height)/m);
				    if (ix > 0 && ix < width) {p = new Point(ix, height);} else {return false;}
				}
				else if (iy < 0) {
				    ix = p0.x+(p0.y/m);
				    if (ix > 0 && ix < width) {p = new Point(ix, 0);} else {return false;}
				}
				else {return false;}
			}
			else if (p0.y >= 0 && p0.y <= height) {p = p0;}
			else if (p0.y < 0) {
			    ix = p0.x+(p0.y/m);
			    if (ix > 0 && ix < width) {p = new Point(ix, 0);} else {return false;}
			}
			else if (p0.y > height && m > 0) {
			    ix = p0.x+((p0.y - height)/m);
			    if (ix > 0 && ix < width) {p = new Point(ix, height);} else {return false;}
			}
			else {return false;}
			if (p1.x > width) {
				iy = p1.y - m*(width-p1.x);
				if (iy > 0 && iy < height) {q = new Point(width, iy);}
				else if (iy < 0) {
				    ix = p0.x+(p0.y/m);
				    if (ix > 0 && ix < width) {q = new Point(ix, 0);} else {return false;}
				}
				else if (iy > height) {
				    ix = p0.x+((p0.y - height)/m);
				    if (ix > 0 && ix < width) {q = new Point(ix, height);} else {return false;}
				}
				else {return false;}
			}
			else if (p1.y >= 0 && p1.y <= height) {q = p1;}
			else if (p1.y < 0) {
			    ix = p1.x+(p1.y/m);
			    if (ix > 0 && ix < width) {q = new Point(ix, 0);} else {return false;}
			}
			else if (p1.y > height) {
			    ix = p1.x+((p1.y- height)/m);
			    if (ix > 0 && ix < width) {q = new Point(ix, height);} else {return false;}
			}
		}
		else {return false;}
	}
	else {
		if (!(p0.y < 0 && p1.y < 0) && !(p0.y > height && p1.y > height)) {
			if (p0.y < 0) {p = new Point(p0.x, 0);}
			else if (p0.y > height) {p = new Point(p0.x, height);}
			else {p = p0;}
			if (p1.y < 0) {q = new Point(p0.x, 0);}
			else if (p1.y > height) {q = new Point(p0.x, height);}
			else {q = p1;}
		}
		else {return false;}
	}
	if (switched) {
		return [new Point(Math.round(q.x), Math.round(q.y)), new Point(Math.round(p.x), Math.round(p.y))];
	}
	return [new Point(Math.round(p.x), Math.round(p.y)), new Point(Math.round(q.x), Math.round(q.y))];

}

/**
 * @ignore
 */
function objString (a){
	var z = "";
	
	for (attr in a) {
		var b = a[attr];
		if (typeof(b) == "object") {z += objString(b);}
		else {z += attr + " " + b + "\n";}
	}	
	return z;
}
