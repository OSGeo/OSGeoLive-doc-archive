/**
 * Package: mb_digitize_geometry
 *
 * Description:
 * A button that allows to draw a geometry on maps, for example polygons.
 *
 * For other geometries, change the element variable "geometryType", and
 * of course select another button image.
 *
 * Files:
 *  - http/plugins/mb_digitize_geometry.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment,
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width,
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file,
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>',
 * > 'mb_digitize_polygon',1,1,'Digitize a polygon geometry',
 * > 'Digitize a polygon geometry','img',
 * > '../img/button_digitize/polygon_off.png','',NULL ,NULL ,24,24,NULL ,'',
 * > '','','../plugins/mb_digitize_geometry.js','','mapframe1','','');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'mb_digitize_polygon',
 * > 'geometryType', 'polygon', '' ,'var');
 *
 * Help:
 * http://www.mapbender.org/mb_digitize_geometry
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 *
 * Parameters:
 * geometryType      - (String) "polygon", "line", "point" (default)
 * 						or "rectangle"
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var $digitize = $(this);

var DigitizeApi = function (o) {
	var that = this;
	var featureCollection = new GeometryArray();
	var defaults = {
		geometryType: "point"
	};
	var settings = $.extend(defaults, o);

	this.events = {
		/**
		 * Property: events.finished
		 *
		 * Description:
		 * Triggered after the geometry has been completed.
		 * obj.featureCollection contains the GeoJSON
		 */
		finished: new Mapbender.Event(),
		/**
		 * Property: events.aborted
		 *
		 * Description:
		 * Triggered if the geometry could not be created.
		 */
		aborted: new Mapbender.Event(),
		/**
		 * Property: events.added
		 *
		 * Description:
		 * Triggered after a point has been added to the geometry.
		 * obj.featureCollection contains the feature collection as GeoJSON
		 * obj.point contains the last point as Mapbender.Point
		 */
		added: new Mapbender.Event(),
		/**
		 * Property: events.mousemove
		 *
		 * Description:
		 * Triggered whenever a new point can be added to the geometry.
		 * obj.featureCollection contains the feature collection as GeoJSON
		 * obj.point contains the current mouse position as Mapbender.Point
		 */
		mousemove: new Mapbender.Event()
	};

	var mousemove = function (e) {
		var map = $(this).mapbender();
		var pt = map.getPos(e);
		var coord = map.convertPixelToReal(pt);

		// compute feature collection to be delegated
		var collection = new GeometryArray();
		collection.importGeoJSON(featureCollection.toString(), false);

		if (settings.geometryType === "point") {
			// if a point is digitized, we discard it, as we will
			// display the current position anyway
			collection = new GeometryArray();
		}
		else if (settings.geometryType === "rectangle") {
			var lastPoint = collection.getPoint(-1, -1, -1);
			if (lastPoint) {
				// if the second point of the rectangle is set, add
				// other points to complete the polygon
				var points = calculateRectanglePoints(lastPoint, coord);
				if(points === undefined) {
					return;
				}
				collection.getGeometry(-1, -1).updatePointAtIndex(points[0], 0);
				collection.getGeometry(-1, -1).addPoint(points[1]);
				collection.getGeometry(-1, -1).addPoint(points[2]);
				collection.getGeometry(-1, -1).addPoint(points[3]);
			}
		}
                else if (settings.geometryType === "circle") {
			var lastPoint = collection.getPoint(-1, -1, -1);
			if (lastPoint) {
				// if the second point of the circle is set, add
				// other points to complete the circle
				var points = calculateCirclePoints(lastPoint, coord);
				if(points === undefined) {
					return;
				}
				for (var i = 0; i < points.length; i++) {
                                    collection.getGeometry(-1, -1).addPoint(points[i]);
                                }
			}
		}
		else if (
			settings.geometryType === "polygon"
			|| settings.geometryType === "line"
		) {
			var numCollections = collection.count();
			if (numCollections > 0) {
				var numFeatures = collection.get(-1).count();
				if (numFeatures > 0) {
					var feature = collection.getGeometry(-1, -1);
					var ps = feature.count();
					// if geometry is a polygon, reopen it
					if (ps > 1 &&
						feature.geomType === Mapbender.geometryType.polygon
					) {
						collection.delPoint(-1, -1, -1);
					}
					// add current point to geometry
					feature.addPoint(coord);
				}
			}
		}

		// display current mouse position as point
		collection.addMember(Mapbender.geometryType.point);
		collection.get(-1).addGeometry();
		collection.getGeometry(-1, -1).addPoint(coord);

		that.events.mousemove.trigger({
			featureCollection: collection.toString()
		});
	};

	var calculateRectanglePoints = function (p1, p3) {
		var q1;
		var q2;
		var q3;
		var q4;

		//box in northeast direction
		if(p1.x < p3.x && p1.y < p3.y) {
			q1 = p1;
			q2 = new Mapbender.Point(p3.x, p1.y);
			q3 = p3;
			q4 = new Mapbender.Point(p1.x, p3.y);
		}
		//box in southwest direction
		else if(p1.x > p3.x && p1.y > p3.y) {
			q1 = p3;
			q2 = new Mapbender.Point(p1.x, p3.y);
			q3 = p1;
			q4 = new Mapbender.Point(p3.x, p1.y);
		}
		//box in southeast direction
		else if(p1.x < p3.x && p1.y > p3.y) {
			q1 = new Mapbender.Point(p1.x, p3.y);
			q2 = p3;
			q3 = new Mapbender.Point(p3.x, p1.y);
			q4 = p1;
		}
		//box in northwest direction
		else if(p1.x > p3.x && p1.y < p3.y) {
			q1 = new Mapbender.Point(p3.x, p1.y);
			q2 = p1;
			q3 = new Mapbender.Point(p1.x, p3.y);
			q4 = p3;
		}
		else {
			return;
		}

		return [
			q1,
			q2,
			q3,
			q4
		];
	};

        var calculateCirclePoints = function (p1, p2) {
            var centerPoint = p1;
            var secondPoint = p2;
            var radius = Math.sqrt(Math.pow(Math.abs(centerPoint.x - secondPoint.x),2)+Math.pow(Math.abs(centerPoint.y - secondPoint.y),2));

            var center = [centerPoint.x, centerPoint.y];
            var steps = 32;

            var circlePoints = [];
            for (var i = 0; i < steps + 1; i++) {
                var newX = (center[0] + radius * Math.cos(2 * Math.PI * i / steps));
                var newY = (center[1] + radius * Math.sin(2 * Math.PI * i / steps));
                circlePoints[i] = new Mapbender.Point(newX, newY);
            }
            return circlePoints;
	};

	var setPoint = function (e) {
		var map = $(this).mapbender();
		var pt = map.getPos(e);
		var coord = map.convertPixelToReal(pt);

		// do not add same point twice
		var lastPoint = featureCollection.getPoint(-1,-1,-1);
		if (lastPoint && coord.equals(featureCollection.getPoint(-1,-1,-1))) {
			// abort if rectangle
                        if (
                            settings.geometryType === "rectangle"
                            || settings.geometryType === "circle"
                        ) {
				featureCollection = new GeometryArray();
				button.stop();
			}
			return false;
		}

		// add point(s)
		if (settings.geometryType === "rectangle" && lastPoint) {
			// if the second point of the rectangle is set, add
			// other points to complete the polygon
			var points = calculateRectanglePoints(lastPoint, coord);

			if(points === undefined) {
				return;
			}
			featureCollection.getGeometry(-1, -1).updatePointAtIndex(points[0], 0);
			featureCollection.getGeometry(-1, -1).addPoint(points[1]);
			featureCollection.getGeometry(-1, -1).addPoint(points[2]);
			featureCollection.getGeometry(-1, -1).addPoint(points[3]);
		}
                else if (settings.geometryType === "circle" && lastPoint) {
                    	// if the second point of the circle is set, add
			// other points to complete the circle polygon
			var points = calculateCirclePoints(lastPoint, coord);

			if(points === undefined) {
				return;
			}

                        for (var i = 0; i < points.length; i++) {
                            featureCollection.getGeometry(-1, -1).addPoint(points[i]);
                        }
		}
		else {
			featureCollection.getGeometry(-1, -1).addPoint(coord);
		}

		// set SRS
		featureCollection.getGeometry(-1, -1).setEpsg(map.getSrs());

		that.events.added.trigger({
			point: coord,
			featureCollection: featureCollection.toString()
		});

		if (settings.geometryType === Mapbender.geometryType.point) {
			return button.stop();
		}
	};

	var finishFeature = function () {
            	if (featureCollection.count() > 0 &&
			featureCollection.getGeometry(-1, -1).close()
		) {
			button.stop();
            	}

	};

	var correctGeometryType = function (str) {
		if (str === "rectangle" || str === "extent" || str === "circle") {
			return Mapbender.geometryType.polygon;
		}
		return str;
	};

	var prevent = function(e){ e.preventDefault(); };

	var button = new Mapbender.Button({
		domElement: $digitize.get(0),
		over: o.src.replace(/_off/, "_over"),
		on: o.src.replace(/_off/, "_on"),
		off: o.src,
		name: o.id,
		go: function () {
			featureCollection = new GeometryArray();
			featureCollection.addMember(
				correctGeometryType(settings.geometryType)
			);
			featureCollection.get(-1).addGeometry();

			if (settings.geometryType === "rectangle" || settings.geometryType === "circle") {
				o.$target.bind("mousedown", setPoint);
				o.$target.bind("mouseup", setPoint);
				o.$target.bind("mouseup", finishFeature);
			}else if(settings.geometryType === "extent"){
				var map = Mapbender.modules[options.target];
				var extent = map.getExtentInfos();
				var points =  calculateRectanglePoints(extent.getSouthWest(), extent.getNorthEast());
				if(points === undefined) {
					return;
				}
				featureCollection.getGeometry(-1, -1).addPoint(points[0]);
				featureCollection.getGeometry(-1, -1).addPoint(points[1]);
				featureCollection.getGeometry(-1, -1).addPoint(points[2]);
				featureCollection.getGeometry(-1, -1).addPoint(points[3]);
				featureCollection.getGeometry(-1, -1).addPoint(points[0]);
				button.stop();

			}
			else {
				o.$target.bind("click", setPoint);
				o.$target.bind("dblclick", finishFeature);
			}
			o.$target.bind("mousemove", mousemove);
			$("body").bind("mousedown", prevent).bind("mouseover", prevent).bind("mousemove", prevent);

		},
		stop: function () {
			if (featureCollection.count() > 0) {
				if (!featureCollection.getGeometry(-1, -1).isValid()) {
					new Mapbender.Exception("Geometry could not be created.");
					that.events.aborted.trigger();
				}
				else {
					that.events.finished.trigger({
						featureCollection: featureCollection.toString()
					});
				}
				featureCollection.empty();
			}
			else {
				new Mapbender.Exception("Geometry could not be created.");
				that.events.aborted.trigger();
			}
			if (settings.geometryType === "rectangle" || settings.geometryType === "circle") {
				o.$target.unbind("mousedown", setPoint);
				o.$target.unbind("mouseup", setPoint);
				o.$target.unbind("mouseup", finishFeature);
			}
			else {
				o.$target.unbind("click", setPoint);
				o.$target.unbind("dblclick", finishFeature);
			}
			o.$target.unbind("mousemove", mousemove);
			$("body").unbind("mousedown", prevent).unbind("mouseover", prevent).unbind("mousemove", prevent);

		}
	});
};

$digitize.mapbender(new DigitizeApi(options));
