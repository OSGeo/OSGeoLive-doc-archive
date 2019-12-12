$.widget("mapbender.mb_measure", {
	options: {
		opacity: 0.6,
		measurePointDiameter: 10,
		polygonFillSnapped: "#FC3",
		polygonFillDefault: "#FFF",
		polygonStrokeWidthDefault: 1,
		polygonStrokeWidthSnapped: 5,
		lineStrokeDefault: "#C9F",
		lineStrokeSnapped: "#F30",
		lineStrokeWidthDefault: 3,
		lineStrokeWidthSnapped: 5,
		pointFillSnapped: "#F90",
		pointFillDefault: "#CCF",
                pointStrokeDefault: "#FC3",
                pointStrokeSnapped: "#F30",
                pointStrokeWidthDefault: 2
	},
	_measurePoints: [],
	_map: undefined,
	_srs: undefined,
	_currentDistance: 0,
        _currentAngle: 0,
	_totalDistance: 0,
	_polygonIsInvalid: false,
	_currentPolygonIsInvalid: false,
	_canvas: undefined,
	_isPolygon: function (pos) {

		var len = this._measurePoints.length;

		var max = pos ? len - 1 : len - 2;

		if (pos && len < 2 || !pos && len < 3) {
			return false;
		}

		var posLocal = pos ? pos : this._measurePoints[len - 1];

		var p0 = this._measurePoints[0].pos;
		var pn = this._measurePoints[max].pos;
		for (var i = 0; i < max; i++)  {
			var pi = this._measurePoints[i].pos;
			var pj = this._measurePoints[i + 1].pos;

			if (i > 0 && this._lineIntersect(
				pi.x, pi.y, pj.x, pj.y,
				p0.x, p0.y, posLocal.x, posLocal.y)
			) {
				return false;
			}
			if (this._lineIntersect(
				pi.x, pi.y, pj.x, pj.y,
				pn.x, pn.y, posLocal.x, posLocal.y)
			) {
				this._currentPolygonIsInvalid = true;
				return false;
			}
		}
		this._currentPolygonIsInvalid = false;
		return true;
	},
	_lineIntersect: function ( x1, y1, x2, y2, x3, y3, x4, y4 ) {
		var isOnSegment = function (xi, yi, xj, yj, xk, yk) {
			// changed <= to < so the segments are allowed to touch!
			 return (xi < xk || xj < xk) &&
					(xk < xi || xk < xj) &&
			        (yi < yk || yj < yk) &&
					(yk < yi || xk < yj);
		};

		var computeDirection = function (xi, yi, xj, yj, xk, yk) {
			var a = (xk - xi) * (yj - yi);
			var b = (xj - xi) * (yk - yi);
			return a < b ? -1 : a > b ? 1 : 0;
		};

		var e1 = computeDirection(x3, y3, x4, y4, x1, y1);
		var e2 = computeDirection(x3, y3, x4, y4, x2, y2);
		var e3 = computeDirection(x1, y1, x2, y2, x3, y3);
		var e4 = computeDirection(x1, y1, x2, y2, x4, y4);
		return (((e1 > 0 && e2 < 0) || (e1 < 0 && e2 > 0)) &&
			((e3 > 0 && e4 < 0) || (e3 < 0 && e4 > 0))) ||
			(e1 === 0 && isOnSegment(x3, y3, x4, y4, x1, y1)) ||
			(e2 === 0 && isOnSegment(x3, y3, x4, y4, x2, y2)) ||
			(e3 === 0 && isOnSegment(x1, y1, x2, y2, x3, y3)) ||
			(e4 === 0 && isOnSegment(x1, y1, x2, y2, x4, y4));
	},
	_toRad: function (deg) {
		return deg * Math.PI/180;
	},
	//
	// calculate area
	//
	_calculateAreaMetric: function (pos) {
		if (this._measurePoints.length < 2) {
			return null;
		}
		this._measurePoints.push(pos);
		var part, area = 0;
		var p0 = this._measurePoints[0].pos, pi, pj;
		for (var i = 0; i < this._measurePoints.length - 1; i++)  {
			pi = this._measurePoints[i].pos;
			pj = this._measurePoints[i + 1].pos;
			part = (pi.y + pj.y) * (pi.x - pj.x) / 2;
			area += part;
		}
		part = (pj.y + p0.y) * (pj.x - p0.x) / 2;
		area += part;
		this._measurePoints.pop();
		return Math.abs(area);
	},
	_calculateAreaGeographic: function (pos) {
		if (this._measurePoints.length < 2) {
			return null;
		}

		// add current point and first point
		this._measurePoints.push({
			pos: pos
		});
		this._measurePoints.push(this._measurePoints[0]);

        var area = 0.0;
        var p1, p2;
        for(var i=0; i<this._measurePoints.length-1; i++) {
            p1 = this._measurePoints[i].pos;
            p2 = this._measurePoints[i+1].pos;
			var c = this._toRad(p2.x - p1.x) *
                    (2 + Math.sin(toRad(p1.y)) +
                    Math.sin(toRad(p2.y)));
            area += c;
        }
        area = area * 6378137.0 * 6378137.0 / 2.0;

		// remove current point and first point
		this._measurePoints.pop();
		this._measurePoints.pop();

		return Math.abs(area);
	},
	_calculateArea: function (pos) {
		switch (this._map.getSrs()) {
			case "EPSG:4326":
				return this._calculateAreaGeographic(pos);
			default:
				return this._calculateAreaMetric(pos);
		}
		return null;
	},
	//
	// calculate distance
	//
	_calculateDistanceGeographic: function (a, b) {
		var lon_from = this._toRad(a.x);
		var lat_from = this._toRad(a.y);
		var lon_to = this._toRad(b.x);
		var lat_to = this._toRad(b.y);
		return Math.abs(6371229 * Math.acos(
			Math.sin(lat_from) * Math.sin(lat_to) +
			Math.cos(lat_from) * Math.cos(lat_to) *
			Math.cos(lon_from - lon_to)
		));
	},
	_calculateDistanceMetric: function (a, b) {
		return Math.abs(Math.sqrt(
			Math.pow(Math.abs(b.x - a.x), 2) +
			Math.pow(Math.abs(b.y - a.y), 2)
		));
	},
	_calculateDistance: function (a, b) {
		if (a !== null && b !== null) {
			switch (this._map.getSrs()) {
				case "EPSG:4326":
					return this._calculateDistanceGeographic(a, b);
				default:
					return this._calculateDistanceMetric(a, b);
			}
		}
		return null;
	},
        _calculateAngle: function (a, b, c) {
		if (a !== null && b !== null && c !== null) {
                        function angleAt(a,b,c){
                            var vectorAB = [b.x- a.x,b.y-a.y];
                            //var vectorBC = [c.x- b.x,c.y-b.y];
                            var vectorBC = [b.x- c.x,b.y-c.y];
                            var cosalpha = scalarProduct(vectorAB,vectorBC)/
                                (vectorlen(vectorAB)*vectorlen(vectorBC));

                            var acosalpha = Math.acos(cosalpha);
                            return (acosalpha/Math.PI)*180;
                        }

                        function scalarProduct(va,vb){
                                return va[0]*vb[0] + va[1]*vb[1];
                        }

                        function vectorlen(v) {
                                return  Math.sqrt(v[0]*v[0] + v[1]*v[1]);
                        }

                        var angle = angleAt(a,b,c);
                        return angle;
		}
		return null;
	},
	_isPointSnapped: function (p1, p2) {
		return p1.dist(p2) <= this.options.measurePointDiameter/2;
	},
	_isFirstPointSnapped: function (p) {
		if (this._measurePoints.length > 0 ) {
			var pos0 = this._measurePoints[0].mousePos;
			if (this._measurePoints.length > 2 && this._isPointSnapped(pos0, p)) {
				return true;
			}
		}
		return false;
	},
	_isLastPointSnapped: function (p) {
		if (this._measurePoints.length > 0 ) {
			var posn = this._measurePoints[this._measurePoints.length - 1].mousePos;
			if (this._measurePoints.length > 1 && this._isPointSnapped(posn, p)) {
				return true;
			}
		}
		return false;
	},
	_draw: function (pos, drawOptions) {
		this._canvas.clear();

		var str_path = "";

		if (pos && drawOptions && !drawOptions.highlightFirst) {
			this._measurePoints.push(pos);
		}

		var len = this._measurePoints.length;
		if (len > 0) {
			for (var k=0; k < len; k++) {
				var pk = this._measurePoints[k].pos;
				var q = this._measurePoints[k].mousePos;

				str_path += (k === 0) ? 'M' : 'L';
				str_path += q.x + ' ' + q.y;

				if (drawOptions.highlightFirst && k === len - 1) {
					var p0 = this._measurePoints[0].mousePos;
					str_path += 'L' + p0.x + ' ' + p0.y;
					
				}
				if (drawOptions && drawOptions.highlightLast && k === len - 1) {
					continue;
				}

				if (drawOptions && drawOptions.drawPoints &&
					(k === 0 && !this._polygonIsInvalid || k >= len - 2 && !drawOptions.highlightFirst)) {

					var circle = this._canvas.circle(q.x, q.y, this.options.measurePointDiameter);

					if (k === 0) {
						circle.attr({
							fill: drawOptions && drawOptions.highlightFirst ?
								this.options.pointFillSnapped : this.options.pointFillDefault,
                                                        "fill-opacity": this.options.opacity,
							stroke: drawOptions.highlightFirst || drawOptions.highlightLast ?
                                                                this.options.pointStrokeSnapped: this.options.pointStrokeDefault,
                                                        "stroke-width": this.options.pointStrokeWidthDefault
						});
					}
					else {
						circle.attr({
							fill: drawOptions && drawOptions.highlightLast && k === len - 2 ?
								this.options.pointFillSnapped : this.options.pointFillDefault,
                                                        "fill-opacity": this.options.opacity,
							stroke: drawOptions.highlightFirst || drawOptions.highlightLast ?
                                                                this.options.pointStrokeSnapped: this.options.pointStrokeDefault,
                                                        "stroke-width": this.options.pointStrokeWidthDefault
						});
					}
				}
			}
		}
		if (pos && drawOptions && !drawOptions.highlightFirst) {
			this._measurePoints.pop();
		}


		if (this._isPolygon(this._measurePoints, pos) && drawOptions && !drawOptions.highlightLast && !this.polygonIsInvalid) {
			var poly = this._canvas.path(str_path + 'Z');
			poly.attr({
				fill: drawOptions.highlightFirst ?
					this.options.polygonFillSnapped : this.options.polygonFillDefault,
				stroke: drawOptions.highlightFirst || drawOptions.highlightLast ?
					this.options.lineStrokeSnapped: this.options.lineStrokeDefault,
				"stroke-width": drawOptions.highlightFirst ?
					this.options.polygonStrokeWidthSnapped : this.options.polygonStrokeWidthDefault,
				opacity: this.options.opacity
			});
		}

		var line = this._canvas.path(str_path);
		line.attr({
			stroke: drawOptions && (drawOptions.highlightFirst || drawOptions.highlightLast) ?
				this.options.lineStrokeSnapped : this.options.lineStrokeDefault,
			"stroke-width": drawOptions && drawOptions.highlightLast ?
				this.options.lineStrokeWidthSnapped : this.options.lineStrokeWidthDefault
		});
		line.toFront();
	},
	_measure: function (e) {
		var mousePos = this._map.getMousePosition(e);
		var firstPointSnapped = this._isFirstPointSnapped(mousePos)
			&& !this._polygonIsInvalid;
		var lastPointSnapped = this._isLastPointSnapped(mousePos);

		var measureData = {
			pos: {
				mousePos: mousePos,
				pos: firstPointSnapped ?
					this._measurePoints[0].pos : lastPointSnapped ?
						this._measurePoints[this._measurePoints.length - 1].pos :
						this._map.convertPixelToReal(mousePos)
			}
		};

		// 
		// calculate distance
		//
		var len = this._measurePoints.length;
		var previousPoint = len > 0 ?
			this._measurePoints[len - 1].pos : null;

		this._currentDistance = this._calculateDistance(
			previousPoint,
			measureData.pos.pos
		);

		//
		// calculate total distance and perimeter
		//
		if (len > 0) {
			measureData.currentDistance = this._currentDistance;
			this._totalDistance = this._currentDistance;

			if (!firstPointSnapped) {
				measureData.totalDistance = this._totalDistance ;
			}

			if (len > 1) {
				this._totalDistance = this._measurePoints[len - 1].totalDistance + this._currentDistance;
				if (!firstPointSnapped) {
					measureData.totalDistance = this._totalDistance;
				}
				if (!lastPointSnapped) {
					measureData.perimeter = measureData.totalDistance + this._calculateDistance(
						this._measurePoints[0].pos,
						measureData.pos.pos
					);
				}

                                this._currentAngle = this._calculateAngle(
                                    this._measurePoints[len-2].pos,
                                    previousPoint,
                                    measureData.pos.pos
                                );
                                measureData.currentAngle  = this._currentAngle;
			}
		}

		//
		// calculate area
		//
		if (this._isPolygon(this._measurePoints, measureData.pos) && !this._polygonIsInvalid) {
			this._currentArea = this._calculateArea(measureData.pos);
			if (!lastPointSnapped) {
				measureData.area = this._currentArea;
			}
		}

		this._trigger("update", null, measureData);

		this._draw(measureData.pos, {
			highlightFirst: firstPointSnapped,
			highlightLast: lastPointSnapped,
			drawPoints: true
		});
	},
	_reinitialize: function (e) {
		this.element
			.unbind("click", $.proxy(this, "_reinitialize"))
		this._trigger("reinitialize", e);
		return false;
	},
	_addLastPoint: function (e) {
		this._trigger("lastpointadded", e);

		this.element.unbind("click", this._addPoint)
			.unbind("mousemove", this._measure)
			.css("cursor", "auto")
			.bind("click", $.proxy(this, "_reinitialize"));
	},
	_addPoint: function (e) {
		var mousePos = this._map.getMousePosition(e);

		var len = this._measurePoints.length;

		var data = {
			pos: {
				mousePos: mousePos,
				pos: this._map.convertPixelToReal(mousePos)
			}
		};

		if (this._totalDistance) {
			data.pos.totalDistance = this._totalDistance;
		}

		this._trigger("pointadded", e, data);

		var firstPointSnapped = this._isFirstPointSnapped(mousePos);
		var lastPointSnapped = this._isLastPointSnapped(mousePos);

		this._isPolygon(this._measurePoints, data.pos);
		if (this._currentPolygonIsInvalid) {
			this._polygonIsInvalid = true;
		}
		this._currentPolygonIsInvalid = false;

		if (lastPointSnapped || firstPointSnapped) {
			this._draw(data.pos, {
				highlightFirst: firstPointSnapped,
				highlightLast: lastPointSnapped,
				drawPoints: false
			});
			this._addLastPoint(e);
			this._measurePoints.closedPolygon = firstPointSnapped;
			this._measurePoints.closedLine = lastPointSnapped;
		}
		else {
			this._measurePoints.push(data.pos);

			this._totalDistance += this._currentDistance;
			this._currentDistance = 0;

			lastPointSnapped = this._isLastPointSnapped(mousePos);
			this._draw(data.pos, {
				highlightFirst: firstPointSnapped,
				highlightLast: lastPointSnapped,
				drawPoints: true
			});
		}
		return true;
	},
	_redraw: function () {
		if (!$(this.element).data("mb_measure")) {
			return;
		}
		var len = this._measurePoints.length;
		if (len === 0) {
			return;
		}
		for (var i = 0; i < len; i++) {
			var p = this._measurePoints[i];
			p.mousePos = this._map.convertRealToPixel(p.pos);
		}
		if (this._measurePoints.closedPolygon) {
			this._draw(undefined, {
				highlightFirst: true,
				highlightLast: false,
				drawPoints: false
			});
		}
		else if (this._measurePoints.closedLine) {
			this._draw(undefined, {
				highlightFirst: false,
				highlightLast: true,
				drawPoints: false
			});
		}
		else {
			this._draw(undefined, {
				highlightFirst: false,
				highlightLast: false,
				drawPoints: false
			});
		}
	},
	_init: function () {
		if (this._measurePoints.closedLine || this._measurePoints.closedPolygon) {
			this._measurePoints = [];
			this._canvas.clear();
		}
		this.element
			.bind("click", $.proxy(this, "_addPoint"))
			.bind("mousemove", $.proxy(this, "_measure"))
			.css("cursor", "crosshair");

	},
	_create: function () {
		this._measurePoints = [];
		
		// ":maps" is a Mapbender selector which
		// checks if an element is a Mapbender map
		this.element = this.element.filter(":maps");

		if (!this.element.jquery || this.element.size() === 0) {
			$.error("This widget must be applied to a Mapbender map.");
		}

		this._map = this.element.mapbender();
		this._map.events.afterMapRequest.register($.proxy(this._redraw, this));
		this._srs = this._map.getSrs();

		this._$canvas = $("<div id='measure_canvas' />").css({
			"z-index": 1000,
			"position": "absolute"
		}).appendTo(this.element);
		this._canvas = Raphael(this._$canvas.get(0), this._map.getWidth(), this._map.getHeight());
		mb_registerPanSubElement($(this._canvas.canvas).parent().get(0));
	},
	// the measured geometry will be available, the events will be deleted
	deactivate: function () {
		this.element
			.unbind("click", this._addPoint)
			.unbind("mousemove", this._measure)
			.unbind("click", this._reinitialize)
			.css("cursor", "default");
	},
	// delete everything
	destroy: function () {
		this.deactivate();
		this._canvas.clear();
		this._measurePoints = [];
		this._$canvas.remove();
		this._map.events.afterMapRequest.unregister($.proxy(this._redraw, this));

		$.Widget.prototype.destroy.apply(this, arguments); // default destroy
		$(this.element).data("mb_measure", null);
	}
});
