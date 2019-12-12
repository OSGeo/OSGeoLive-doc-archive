Mapbender.PrintBox = function (options) {
	if (!options) {
		options = {};
	}

	var target = options.target || "mapframe1";
	var map = getMapObjByName(target);
//	var map = Mapbender.modules[target];
	var map_el = map.getDomElement();

	// Default is portrait, A4, unit seems to be cm
	var printWidth = options.printWidth || 21;
	var printHeight = options.printHeight || 29.7;

	// initialised in setScale()
	var boxWidth, boxHeight; 

	var scale = options.scale || 100000;

	// behaviour
	var afterChangeAngle = options.afterChangeAngle || function (obj) {};
	var afterChangeSize = options.afterChangeSize || function (obj) {};
	
	// styles
	var opacity = options.boxOpacity || 1;
	var boxColour = options.boxColour || "#9999FF";
	var frameColour = options.frameColour || "#000000";
	var pointColour = options.pointColour || "#DD0000";
	var circleColour = options.circleColour || "#DD0000";
	var circleWidth = options.circleWidth || 4;

	// attributes
	this.id = "printbox";
	var angle = 0;
	var totalAngle = 0;

	// the four points of the box as pixel coordinates (incl rotation),
	// with (0,0) as center. This is important for angle calculations.
	var pointArray = [];

	// The pointArray is moved by the center vector.
	// default: place box in the center of the map
	//var center = options.center || new Point(map.width/2,map.height/2);
        var center = options.center || new Point(map.getWidth()/2,map.getHeight()/2);

	// the center in real world coordinates
	var centerMap = null;

	// the four points of the box as pixel coordinates (NO ROTATION)
	var startPointPixArray = [];

	// the four points of the box as real world coordinates (NO ROTATION)
	var startPointMapArray = [];
	var that = this;

	// if the box is smaller than this, the circle will not be drawn
	var MIN_BOX_WIDTH_OR_HEIGHT = 10;

	// if the box is larger than this, the box will not be filled
	var MAX_BOX_WIDTH_OR_HEIGHT = 800;

	this.toString = function () {
		var str = "";
		str += "Center: " + getCenter() + "\n";
		str += "Radius: " + radius + "\n";
		str += "StartRadius: " + startRadius + "\n";
		str += "Pixelpos: " + String(pointArray) + "\n";
		str += "StartPixelpos: " + String(startPointPixArray) + "\n";
		str += "Mappos: " + String(startPointMapArray) + "\n";
		return str;
	};
	
	var initBehaviour = function () {
		initMoveBehaviour();
		initResizeBehaviour();
		initRotateBehaviour();
	};

	var initRotateBehaviour = function () {
		$circleCanvas.css("z-index", "110").mousedown(function (e) {
			circleCanvas.clear();	
	
			var newCenter = getCenter();
	
			var mouseMoveStart = map.getMousePos(e);
			var vectorA = pointArray[0].minus(newCenter);
			
			var currentPos = map.getMousePos(e);
			var vectorCurrent = currentPos.minus(newCenter);
			
			angle = Math.ceil(getAngle(vectorA, vectorCurrent));
			
			$(document).mousemove(function (e) {
				var currentPos = map.getMousePos(e);
				var vectorCurrent = currentPos.minus(newCenter);
				var currentAngle = Math.ceil(getAngle(vectorA, vectorCurrent));
				var diffAngle = currentAngle - angle;
				if (Math.abs(diffAngle) >= 1) {
					angle = currentAngle;
					totalAngle = ((totalAngle + diffAngle) +360 )% 360;
					that.rotate(totalAngle);
				}
				return false;
			}).mouseup(function (e) {
				angle = 0;
				$(document).unbind("mousemove");
				$(document).unbind("mouseup");
				afterChangeAngle({
					angle: totalAngle,
					coordinates: that.getStartCoordinates()
				});
				that.paintBox();
				return false;
			});	
			return false;
		}).css("cursor", "move");
	};

	var initMoveBehaviour = function () {
		$boxCanvas.mousedown(function (e) {
			circleCanvas.clear();	
	
			var mouseMoveStart = map.getMousePos(e);
	
			var containerStart = new Point(
				parseInt($container.css("left"), 10),
				parseInt($container.css("top"), 10)
			);

			var diff;
	
			$(document).mousemove(function (e) {
				diff = (map.getMousePos(e)).minus(mouseMoveStart);
	
				$container.css({
					"top": (containerStart.y + diff.y) + "px",
					"left": (containerStart.x + diff.x) + "px"
				});
				return false;
	
			}).mouseup(function (e) {
				$(document).unbind("mousemove");
				$(document).unbind("mouseup");
				recalculateMapPositions();
				that.rotate(totalAngle);
				that.paintBox();
				return false;
			});	
			return false;
		});
	};

	var initResizeBehaviour = function () {
		$pointCanvas.css("z-index", "120").mousedown(function (e) {
			circleCanvas.clear();	

			var vectorA = getCenter();
	
			resizeRatio = 1;
			mouseMoveStart = map.getMousePos(e);
			$(document).mousemove(function (e) {
				var newRadius = vectorA.dist(map.getMousePos(e));
				var resizeRatio = newRadius / radius;
				if (resizeRatio < 0.98 || resizeRatio > 1.02) {
					for (var i = 0; i < pointArray.length; i++) {
						pointArray[i].x *= resizeRatio;
						pointArray[i].y *= resizeRatio;
						startPointPixArray[i].x *= resizeRatio;
						startPointPixArray[i].y *= resizeRatio;
					}
					radius *= resizeRatio;
					that.paintPoints();
				}
				return false;
			});
			$(document).mouseup(function (e) {
				$(document).unbind("mousemove");
				$(document).unbind("mouseup");

				recalculateMapPositions();
				recalculatePixPositions();
				afterChangeSize({
					scale: that.getScale(),
					coordinates: that.getStartCoordinates()
				});
				that.rotate(totalAngle);
				that.paintBox();
				return false;
			});
			return false;
		}).css("cursor", "move");
	};

	var setCenter = function (inputCenter) {
		center = inputCenter.minus(
			new Point(
				parseInt($container.css("left"), 10), 
				parseInt($container.css("top"), 10)
			)
		);
	};
	
	var getCenter = function () {
		var c = center.plus(
			new Point(
				parseInt($container.css("left"), 10), 
				parseInt($container.css("top"), 10)
			)
		);
		return c;
	};

	/**
	 * Calculates the angle (-180 < angle <= 180) between two vectors.
	 * 
	 * @param {Point} a
	 * @param {Point} b
	 */
	var getAngle = function (a, b) {
		var undirectedAngle = 180 * Math.acos(
				(a.x * b.x + a.y * b.y) 
				/
				(
					Math.sqrt(
						a.x * a.x
					+ 
					 	a.y * a.y
					) * 
					Math.sqrt(
						b.x * b.x
					+ 
						b.y * b.y
					)
				)
			) / Math.PI;

		if ((a.x*b.y - a.y*b.x) > 0) {
			return -1 * undirectedAngle;
		}
		return undirectedAngle;
		
	};
	
	/**
	 * To be replaced by the map objects native getMousePosition
	 * 
	 * @param {Event} e
	 
	var getMousePos = function (e) {
		if ($.msie) {
			return new Point(event.clientX, event.clientY);
		}
		return new Point(e.pageX, e.pageY);
	};
	 */

	var recalculateMapPositions = function () {
		for (var i = 0; i < pointArray.length; i++) {
			startPointMapArray[i] = convertPixelToMap(startPointPixArray[i].plus(getCenter()));
		}
		centerMap = convertPixelToMap(getCenter());
		
	};

	var recalculatePixPositions = function () {
		setCenter(convertMapToPixel(centerMap));
		for (var i = 0; i < startPointMapArray.length; i++) {
			pointArray[i] = convertMapToPixel(startPointMapArray[i]).minus(getCenter());
			startPointPixArray[i] = convertMapToPixel(startPointMapArray[i]).minus(getCenter());
		}
		radius = pointArray[0].dist(new Point(0,0));
		startRadius = radius;
		boxWidth = pointArray[2].x - pointArray[0].x;
		boxHeight = pointArray[0].y - pointArray[2].y;
	};

	var initPoints = function () {
		var w = parseInt((boxWidth/2), 10);
		var h = parseInt((boxHeight/2), 10);

		pointArray[0] = new Point(-w,  h);
		pointArray[1] = new Point( w,  h);
		pointArray[2] = new Point( w, -h);
		pointArray[3] = new Point(-w, -h);

		startPointPixArray[0] = (new Point(-w,  h));
		startPointPixArray[1] = (new Point( w,  h));
		startPointPixArray[2] = (new Point( w, -h));
		startPointPixArray[3] = (new Point(-w, -h));

		radius = pointArray[0].dist(new Point(0,0));
		startRadius = radius;

		recalculateMapPositions();

		scale = that.getScale();
	};

	var switchBoxDimensions = function () {
		setBoxDimensions(boxHeight, boxWidth);
		afterChangeSize({
			scale: that.getScale(),
			coordinates: that.getStartCoordinates()
		});
	};

	this.setPortrait = function () {
		this.setAngle(0);
		if (boxWidth > boxHeight) {
			switchBoxDimensions();
		}
	};
	
	this.setLandscape = function () {
		this.setAngle(0);
		if (boxWidth < boxHeight) {
			switchBoxDimensions();
		}
	};
	
	this.setPrintWidthAndHeight = function (width, height) {
		var currentScale = this.getScale();
		printWidth = width;
		printHeight = height;
		this.setScale(currentScale);
	};
	
	var convertMapToPixel = function (aPoint) {
		var pArray = makeRealWorld2mapPos(map.elementName, aPoint.x, aPoint.y);
		return new Point(pArray[0], pArray[1]);
	};
	
	var convertPixelToMap = function (aPoint) {
		var pArray = makeClickPos2RealWorldPos(map.elementName, aPoint.x, aPoint.y);
		return new Point(pArray[0], pArray[1]);
	};
	
	/**
	 * Sets the box width and box height (calculated in setScale)
	 * 
	 * @param {Integer} inputWidth
	 * @param {Integer} inputHeight
	 */
	var setBoxDimensions = function (inputWidth, inputHeight) {
		boxWidth = inputWidth;
		boxHeight = inputHeight;
		
		initPoints();
		that.rotate(totalAngle);

		afterChangeSize({
			scale: that.getScale(),
			coordinates: that.getStartCoordinates()
		});

		that.paintBox();
	};

	/**
	 * Returns an array of two points, the lower left and upper right of the initial box
	 */
	this.getStartCoordinates = function () {
		var a = startPointMapArray[0];
		var b = startPointMapArray[2];
		if (!a || !b) {
			return null;
		}
		var returnString =  a.x + "," + a.y + "," + b.x + "," + b.y;
		return returnString;
	};

	/**
	 * Returns the current scale of the print box
	 */
	this.getScale = function () {
/*
		var coords = this.getStartCoordinates();
		var coordsArray = coords.split(",");		

		var ext = mapObj.getExtentInfos();
		var extMinX = ext.minx;
		var extMaxX = ext.maxx;
		
		var x = (ext.minx + ext.maxx)/2;
		var y = (ext.miny + ext.maxy)/2;
		var scale1 = (x - coordsArray[0]) * (mb_resolution * 100 *2) / mapObj.width;
		var scale2 = (coordsArray[2] - x) * (mb_resolution * 100 *2) / mapObj.width;
		scale = Math.round(scale1/2 + scale2/2);
		return scale;
*/
		var coords = this.getStartCoordinates();
		var coordsArray = coords.split(",");		
	
		xtentx =  coordsArray[2] - coordsArray[0];
		scale = parseInt(Math.ceil(xtentx / (printWidth / 100)), 10);
		return scale;
	};

	/**
	 * Repaints the Box with the current scale. Can be called from outside, 
	 * for example after zoom in.
	 */
	this.repaint = function () {
		recalculatePixPositions();
		this.rotate(totalAngle);
		this.paintBox();
	};
	
	/**
	 * Sets the current scale, and repaints the box
	 * 
	 * @param {Integer} inputScale
	 */
	this.setScale = function (inputScale) {
		if (typeof(inputScale) == "number") {
/*			
			var arrayBBox = mapObj.extent.split(",");
			x = parseFloat(arrayBBox[0]) + ((parseFloat(arrayBBox[2]) - parseFloat(arrayBBox[0]))/2);
			y = parseFloat(arrayBBox[1]) + ((parseFloat(arrayBBox[3]) - parseFloat(arrayBBox[1]))/2);
			
			var minx = parseFloat(x) - (mapObj.width / (mb_resolution * 100 *2) * inputScale);
			var miny = parseFloat(y) -  (mapObj.height / (mb_resolution * 100 *2) * inputScale);
			var maxx = parseFloat(x) + (mapObj.width / (mb_resolution * 100 *2) * inputScale);
			var maxy = parseFloat(y) +  (mapObj.height / (mb_resolution * 100 *2) * inputScale);

			var newMinPos = makeRealWorld2mapPos(mapObj.frameName, minx, miny);
			var newMaxPos = makeRealWorld2mapPos(mapObj.frameName, maxx, maxy);
			var newBoxWidth = newMaxPos[0] - newMinPos[0];
			var newBoxHeight = newBoxWidth * (printHeight / printWidth);
*/
			var mapWidthInM = printWidth / 100;
			var realWidthInM = inputScale * mapWidthInM;
			var mapHeightInM = printHeight / 100;
			var realHeightInM = inputScale * mapHeightInM;
			
			var coords = this.getStartCoordinates();
			if (coords !== null) {
				var coordsArray = coords.split(",");
				var oldMin = new Point(parseFloat(coordsArray[0]), parseFloat(coordsArray[1]));
				var oldMax = new Point(parseFloat(coordsArray[2]), parseFloat(coordsArray[3]));
				centerMap = (oldMin.times(0.5)).plus(oldMax.times(0.5));
				
			}
			else {
				centerMap = convertPixelToMap(getCenter());
			}
			
			var newMin = new Point(centerMap.x - 0.5 * realWidthInM, centerMap.y - 0.5 * realHeightInM);
			var newMax = new Point(centerMap.x + 0.5 * realWidthInM, centerMap.y + 0.5 * realHeightInM);

			startPointMapArray[0] = new Point(newMin.x, newMin.y);
			startPointMapArray[1] = new Point(newMax.x, newMin.y);
			startPointMapArray[2] = new Point(newMax.x, newMax.y);
			startPointMapArray[3] = new Point(newMin.x, newMax.y);

			this.getStartCoordinates();
			var newMinPos = convertMapToPixel(newMin);
			var newMaxPos = convertMapToPixel(newMax);
			boxWidth = newMaxPos.x - newMinPos.x;
			boxHeight = newMinPos.y - newMaxPos.y;

			var w = parseInt(0.5 * boxWidth, 10);
			var h = parseInt(0.5 * boxHeight, 10);

			pointArray[0] = new Point(-w,  h);
			pointArray[1] = new Point( w,  h);
			pointArray[2] = new Point( w, -h);
			pointArray[3] = new Point(-w, -h);
	
			startPointPixArray[0] = (new Point(-w,  h));
			startPointPixArray[1] = (new Point( w,  h));
			startPointPixArray[2] = (new Point( w, -h));
			startPointPixArray[3] = (new Point(-w, -h));
	
			radius = pointArray[0].dist(new Point(0,0));
			startRadius = radius;
	
			this.rotate(totalAngle);
	
	
			afterChangeSize({
				scale: that.getScale(),
				coordinates: that.getStartCoordinates()
			});
	
			that.paintBox();

			return true;
		}
		return false;
	};

	/**
	 * Sets the angle of the box to a specific angle.
	 * 
	 * @param {Integer} angle
	 */
	this.setAngle = function (angle) {
		if (typeof(angle) == "number" && angle >= -360) {
			totalAngle = (360 + angle) % 360;
			this.rotate(totalAngle);
			this.paintBox();
			afterChangeAngle({
				angle: totalAngle,
				coordinates: that.getStartCoordinates()
			});
			return true;
		}
		return false;
	};


	//
	//
	// VIEW
	//
	//

	/**
	 * Rotates the box by a given degree (0 <= degree < 360), 
	 * and paints the corner points.
	 * 
	 * @param {Integer} degree
	 */
	this.rotate = function (degree) {
		var rotationAngle = (Math.PI * parseFloat(degree))/180;
		var resizeRatio = radius / startRadius;

		for (var i = 0; i < pointArray.length; i++) {
			var p = (convertMapToPixel(startPointMapArray[i])).minus(getCenter());
			var newx = p.x * Math.cos(rotationAngle) + p.y * Math.sin(rotationAngle);
			var newy = p.x * -Math.sin(rotationAngle) + p.y * Math.cos(rotationAngle);
			pointArray[i] = (new Point(newx, newy)).times(resizeRatio);
		}
		afterChangeAngle({
			angle: degree,
			coordinates: this.getStartCoordinates()
		});
		this.paintPoints();
	};

	/**
	 * Paints the four corner points of the print box.
	 */
	this.paintPoints = function () {

		switchActiveCanvas();
		var c = center;
		for (var i = 0; i < pointArray.length; i++) {
			activeCanvas.fillEllipse(
				pointArray[i].x + c.x - 4,
				pointArray[i].y + c.y - 4,
				8,
				8
			);
		}
		activeCanvas.paint();
		passiveCanvas.clear();
	};

	var boxTooBig = function () {
		if (boxWidth > MAX_BOX_WIDTH_OR_HEIGHT || boxHeight > MAX_BOX_WIDTH_OR_HEIGHT) {
			return true;
		}
		return false;
	};

	var boxTooSmall = function () {
		if (boxWidth < MIN_BOX_WIDTH_OR_HEIGHT || boxHeight < MIN_BOX_WIDTH_OR_HEIGHT) {
			return true;
		}
		return false;
	};

	/**
	 * Paints the box itself. Plus the circle.
	 */
	this.paintBox = function () {
		var r = Math.round(0.75 * radius);
		var c = center;
		circleCanvas.clear();	
		if (!boxTooSmall() && !boxTooBig()) {
			circleCanvas.drawEllipse(c.x-r, c.y-r, 2*r, 2*r);
		}
		else {
			new Mb_warning("The print box is too small or too big. The rotate circle is not shown.");
		}
		circleCanvas.paint();

		boxCanvas.clear();

                //don't use a filledPolygon any more to move the print box, because
                //IE can't display the fill opacity, use instead a center point for moving print box
                //if (!boxTooBig()) {
			/*boxCanvas.fillPolygon([
				pointArray[0].x + c.x,
				pointArray[1].x + c.x,
				pointArray[2].x + c.x,
				pointArray[3].x + c.x
			],
			[
				pointArray[0].y + c.y,
				pointArray[1].y + c.y,
				pointArray[2].y + c.y,
				pointArray[3].y + c.y
			]);*/

                    boxCanvas.setColor(pointColour);
                    boxCanvas.fillEllipse(
                            c.x - 4,
                            c.y - 4,
                            10,
                            10
                    );
		//}
		//else {
		//	new Mb_warning("The print box is too big. The box is not filled.");
		//}

               
		
		// frame
		boxCanvas.setColor(frameColour);	
		for (var i = 0; i < pointArray.length; i++) {
			var indexA = i % 4;
			var a = pointArray[indexA].plus(center);
			var indexB = (i + 1) % 4;
			var b = pointArray[indexB].plus(center);
			boxCanvas.drawLine(a.x, a.y, b.x, b.y);
		}
		
		boxCanvas.setColor(boxColour);	
		boxCanvas.paint();
	};
	
	/**
	 * Clears all canvases, to be performed onunload.
	 */
	this.destroy = function () {
		circleCanvas.clear();	
		boxCanvas.clear();
		activeCanvas.clear();
		passiveCanvas.clear();
		$("#" + this.id).remove();
                $("#container_" + this.id).remove();
	};

	var switchActiveCanvas = function () {
		if (canvasNr == 1) {
			canvasNr = 2;
			activeCanvas = jg[2];
			passiveCanvas = jg[1];
		}
		else {
			canvasNr = 1;
			activeCanvas = jg[1];
			passiveCanvas = jg[2];
		}
	};


	var $container = $("<div id='" + this.id + "' style='position:relative;top:0px;left:0px;" + 
		"'></div>");
		
	var $superContainer = $("<div id='container_" + this.id + "' style='position:absolute;z-index:1000;'></div>");
	$superContainer.append($container);
	//$("#"+map.elementName).append($superContainer);
	$(map_el).append($superContainer);
	
	var canvasName = [
		this.id + "_canvas_box",
		this.id + "_canvas_points1",
		this.id + "_canvas_points2",
		this.id + "_canvas_circle"
	];

	var jg = [];
	
	var canvasNr = 1;

	for (var i = 0; i < canvasName.length; i++) {
		$container.append(
			$("<div id='" + canvasName[i] + "'></div>")
		);
		jg[i] = new jsGraphics(canvasName[i]);
	}

	$circleCanvas = $("#" + canvasName[3]);
	$pointCanvas = $("#" + canvasName[1] +  ", #" + canvasName[2]);
	$boxCanvas = $("#" + canvasName[0]);
	$boxCanvas.css({
		"opacity" : opacity, 
		"filter" : "alpha(opacity=" + (opacity * 100) + ")",
                "cursor" : "move"
	});
		
	var boxCanvas = jg[0];
	boxCanvas.setColor(boxColour);	
	var activeCanvas = jg[1];
	activeCanvas.setColor(pointColour);
	var passiveCanvas = jg[2];
	passiveCanvas.setColor(pointColour);
	var circleCanvas = jg[3];
	circleCanvas.setColor(circleColour);
	circleCanvas.setStroke(circleWidth);	

	var mouseMoveStart = [];	

	var radius = 0;
	var startRadius = 0;

	// "constructor" functions
	initBehaviour();

	this.setScale(scale);
	mb_registerPanSubElement($superContainer.get(0).id);

};