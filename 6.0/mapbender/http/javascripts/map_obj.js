/**
 * Package: Map
 *
 * Description:
 * This is the central module for displaying the composite map overlay.
 *
 * Help:
 * http://www.mapbender.org/Mapframe
 */
/**
 * Constructor: Map
 *
 * Parameters:
 * frameName		- *[deprecated]* the name of the iframe, for backwards
 * 						compatibility only, use "" for maps in DIVs
 * elementName		- the ID of the DOM node
 * width			- width of the map in pixel
 * height			- height of the map in pixel
 * wms_index		- restrict the WMS in the map to the WMS with this ID
 * 						(useful for overview maps)
 */
Mapbender.Map = function (options) {

	var defaults = {
		width: 400,
		height: 300,
		id: "mapframe1",
		wms: [],
		wmsIndexOverview: null
	};
	
	var o = $.extend({}, defaults, options || {});

    var mapMsgObj;
	var extentHasChanged = false;
	var srsHasChanged = false;

	this.slippy = false;
	
    eventLocalize.register(function(){
        localizeMap();
    });
    
/*    eventInit.register(function(){
        localizeMap();
    });*/
    
    var localizeMap = function(){
        $.post("../php/mod_map_messages.php", {
		"sessionName": Mapbender.sessionName,
		"sessionId": Mapbender.sessionId
        }, function(obj, status){
            mapMsgObj = $.parseJSON(obj);
        });
    };
    
    this.history = new Mapbender.History();
	this.history.events.beforeAdd.register(function () {
		for (var i = that.history.getCurrentIndex() + 1; i <= that.history.count(); i++) {
	        $("#" + that.elementName + "_request_" + i).remove();
		}
	});
    
    /**
     * Method: setExtent
     *
     * Description:
     * set the extent of the wms
     *
     * Parameters:
     * minx		- x of south west
     * miny		- y of south west
     * maxx		- x of north east
     * maxy		- y of north east
     */
    this.setExtent = function(minx, miny, maxx, maxy){
		if (typeof this.extent !== "undefined") {
            this.oldExtent = new Extent(this.extent);

	        // pixel coordinates of new extent in old extent
	        var oldpixll, oldpixur;
			if (arguments.length === 1) {
				oldpixll = this.convertRealToPixel(minx.min);
				oldpixur = this.convertRealToPixel(minx.max);
				this.oldExtentPix = new Extent(oldpixll.x, oldpixll.y, oldpixur.x, oldpixur.y);
			}
			else if (arguments.length === 4) {
				oldpixll = this.convertRealToPixel(new Point(parseFloat(minx), parseFloat(miny)));
				oldpixur = this.convertRealToPixel(new Point(parseFloat(maxx), parseFloat(maxy)));
				this.oldExtentPix = new Extent(oldpixll.x, oldpixll.y, oldpixur.x, oldpixur.y);
			}

        }
		else {
			this.oldExtentPix = new Extent(0, this.getHeight(), this.getWidth(), 0);
		}
        

        if (arguments.length === 1) {
            this.extent = arguments[0];
        }
        else if (arguments.length === 4) {
            if (!this.extent) {
                this.extent = new Mapbender.Extent(arguments[0], arguments[1], arguments[2], arguments[3]);
            }
            else {
                this.extent.set(new Point(arguments[0], arguments[1]), new Point(arguments[2], arguments[3]));
            }
        }
		extentHasChanged = true;
    };
    
    /*	
     this.restrictedExtent = function (extent) {
     this.restrictedExtent = extent;
     };
     */
    /**
     * get the width of the mapObj
     *
     * @member Map
     * @return width of the mapObj
     * @type integer
     */
    this.getWidth = function(){
		var w = $(this.getDomElement()).innerWidth();
		if (w !== 0) {
			return w;
		}
        return parseInt(this.width, 10);
    };
    
    /**
     * set the width of the mapObj
     *
     * @param {integer} widht the width of the mapObj
     */
    this.setWidth = function(width){
        this.width = parseInt(width, 10);

        $("#" + this.elementName).width(this.width);
		
		this.events.dimensionsChanged.trigger({
			width: this.width,
			height: this.height
		});
    };
    
	this.setDimensions = function (w, h) {
		this.width = parseInt(w, 10);
        this.height = parseInt(h, 10);

        $("#" + this.elementName)
			.width(this.width)
			.height(this.height);

		this.calculateExtent(this.extent);
		this.setMapRequest();
		
		this.events.dimensionsChanged.trigger({
			width: this.width,
			height: this.height
		});
		
	};
    /**
     * get the height of the mapObj
     *
     * @member Map
     * @return width of the mapObj
     * @type integer
     */
    this.getHeight = function(){
		var h = $(this.getDomElement()).innerHeight();
		if (h !== 0) {
			return h;
		}
        return parseInt(this.height, 10);
    };
    
    this.getDomElement = function(){
        return document.getElementById(this.elementName);
    };
    
    /**
     * set the height of the mapObj
     *
     * @param {integer} height the height of the mapObj
     */
    this.setHeight = function(height){
        this.height = parseInt(height, 10);

        $("#" + this.elementName).height(this.height);
		
		this.events.dimensionsChanged.trigger({
			width: this.width,
			height: this.height
		});		
    };
    
    // using the normalized jQuery event
	this.getPos = function (e) {
		var $dom = $(this.getDomElement());
/*		if ( $.browser.msie ) {
			return new Mapbender.Point(
				e.pageX - $dom.offset().left + $(window).scrollLeft(),
				e.pageY - $dom.offset().top + $(window).scrollTop()
			);
		}
		else {
*/		
			return new Mapbender.Point(
				e.pageX - $dom.offset().left,
				e.pageY - $dom.offset().top
			);
//		}
	};
	
  	this.getMousePosition = function (event) {
		return this.getPos(event);
	};
    
    /**
     * converts the extent of the mapobject so that the maximum	extent will be displayed
     */
    this.calculateExtent = function (ext) {
    	var relation_px_x = this.getWidth() / this.getHeight();
        var relation_px_y = this.getHeight() / this.getWidth();
        var relation_bbox_x = ext.extentx / ext.extenty;
        var oldMin = ext.getSouthWest();
        var oldMax = ext.getNorthEast();

		if (relation_bbox_x <= relation_px_x) {
            ext.set(new Point(
				ext.centerx - relation_px_x * ext.extenty / 2,
				oldMin.y
			), new Point(
				ext.centerx + relation_px_x * ext.extenty / 2,
				oldMax.y	
			));
        }
        if (relation_bbox_x > relation_px_x) {
            ext.set(new Point(
				oldMin.x,
				ext.centery - relation_px_y * ext.extentx / 2
			), new Point(
				oldMax.x,
				ext.centery + relation_px_y * ext.extentx / 2	
			));
        }
        
        this.setExtent(ext);
        return ext;
    };
    
    var ignoredWms;
    
    var undoIgnoreWms = function(){
        ignoredWms = [];
    };
    
    var ignoreWms = function(wms){
        ignoredWms.push({
            id: wms.wms_id,
            title: wms.wms_title
        });
    };
    
    var isIgnoredWms = function(wms){
        for (var j = 0; j < ignoredWms.length; j++) {
            if (ignoredWms[j].id === wms.wms_id) {
                return true;
            }
        }
        return false;
    };
    
    var getIgnoredWms = function(){
        return ignoredWms;
    };
    
    this.transformExtent = function (options) {
    	if (typeof options !== 'object') {
    		return false;
    	}	
    	
    	if (this.epsg !== options.crs) {
	    	var source = new Proj4js.Proj(options.crs);
			var dest = new Proj4js.Proj(this.epsg);
	    	
			var intervalId = setInterval(function() {
				if(source.readyToUse && dest.readyToUse) {
					clearInterval(intervalId);
					var o = {
						source : source,
						dest : dest,
						extent : options.extent
					};
					
					that.extent.setCrs(o);
					that.calculateExtent(that.extent);
		        	that.setMapRequest();
		        } 
			}, 200);
    	}
    };
    
    this.setCrs = function (options) {
    	if (typeof options !== 'object') {
    		return false;
    	}
    	
    	if (this.epsg !== options.crs) {
    		var source = new Proj4js.Proj(this.epsg);
    		var dest = new Proj4js.Proj(options.crs);
    		
    		var intervalId = setInterval(function() {
    			if(source.readyToUse && dest.readyToUse) {
    				var o = {
    					source : source,
    					dest : dest
    				};
    				clearInterval(intervalId);
    				setCrsMap(o);
    				setCrsWms(o);
    				checkSupportedWms({
        				srs : options.crs
        			});
    	    		that.epsg = options.crs;
    	    		srsHasChanged = true;
    	    		that.calculateExtent(that.extent);
    	        	that.setMapRequest();
    	        	options.callback();
    	        } 
    		}, 200);
    	}
    };
    
    var setCrsMap = function (options) {
    	that.extent.setCrs(options);
    };
    
    var setCrsWms = function (options) {
    	for (j = 0; j < that.wms.length; j++) {
    		that.wms[j].setCrs(options);
		}
    };
    
    this.setSrs = function(options){
    	if (typeof options.srs !== "string") {
            new Mb_exception("Mapbender.Map.setSrs: SRS is not a string: " + options.srs);
            return null;
        }
        if (!options.extent || options.extent.constructor !== Mapbender.Extent) {
            new Mb_exception("Mapbender.Map.setSrs: Extent is not a Mapbender.Extent: " + options.extent);
            return null;
        }
        if (this.epsg !== options.srs) {
            // actually set the new values and return extent
        	checkSupportedWms(options);
            this.epsg = options.srs;
            srsHasChanged = true;
            return this.calculateExtent(options.extent);
        }
		return this.calculateExtent(options.extent);
    };
    
    var checkSupportedWms = function (o) {
        var defaultOptions = {
        	displayWarning : false,
        	srs : null
        };
        var options = $.extend(defaultOptions, o);
    	// check which WMS support the new SRS
        undoIgnoreWms();
        for (var i = 0; i < that.wms.length; i++) {
            var found = false;
            for (var j = 0; j < that.wms[i].gui_epsg.length; j++) {
                if (options.srs === that.wms[i].gui_epsg[j] && that.wms[i].gui_epsg_supported[j]) {
                    found = true;
                    break;
                }
            }
            if (!found) {
                ignoreWms(that.wms[i]);
            }
        }
        var ignoredWms = getIgnoredWms();
        
        // ...and optionally display a message
        if (options.displayWarning && ignoredWms.length > 0) {
        
            var msg = mapMsgObj.srsNotSupported + ": <br><br>";
            
            for (var key in ignoredWms) {
                msg += "<b>" + ignoredWms[key].title + "</b><br>";
            }
            try {
            	
            	var $msg= $("<div>" + msg + "</div>");
            	$msg.dialog({
                    modal: false
                });
            } 
            catch (e) {
                new Mb_warning(e.message + ". " + msg);
            }
        }
    }; 
    
    
    // If options.wmsIndexOverview is set (=map is overview), only this
    // WMS is being pointed to.
    this.setWms = function (options) {
        this.wms = [];
        for (var i = 0; i < options.wms.length; i++) {
            var isValidWms = options.wmsIndexOverview === null || 
				options.wmsIndexOverview === i;
            if (isValidWms) {
                // MAJOR CHANGE!
                // formerly, this was a reference!
                // Now, this is a copy!
                // Each map object has its own set of WMS.
                this.wms.push($.extend(true, {
					mapURL: false
				}, options.wms[i]));
            }
        }
    };
    
    this.initializeWms = function(){
        var cnt_layers;
        var cnt_querylayers;
        var styles;
        var layers;
        var querylayers = "";
        for (i = 0; i < this.wms.length; i++) {
            cnt_layers = 0;
            cnt_querylayers = 0;
            styles = "";
            layers = "";
            querylayers = "";
            
            for (var ii = 0; ii < this.wms[i].objLayer.length; ii++) {
                // layer is visible and not root layer
                if (this.wms[i].objLayer[ii].gui_layer_visible == 1 && ii > 0) {
                    if (cnt_layers > 0) {
                        layers += ",";
                        styles += ",";
                    }
                    layers += encodeURIComponent(this.wms[i].objLayer[ii].layer_name);
                    styles += "";
                    cnt_layers++;
                }
                // layer is queryable and not root layer
                if (this.wms[i].objLayer[ii].gui_layer_querylayer == 1 && ii > 0) {
                    if (cnt_querylayers > 0) {
                        querylayers += ",";
                    }
                    querylayers += this.wms[i].objLayer[ii].layer_name;
                    cnt_querylayers++;
                }
            }
            this.layers[i] = layers;
            this.styles[i] = styles;
            this.querylayers[i] = querylayers;
        }
    };
    /**
     * get the extent of the mapObj
     *
     * @member Map
     * @return extent of the mapObj as commaseparated minx,minx,maxx,maxy
     * @type string
     */
    this.getExtent = function(){
        var ext = this.extent;
        if (ext) {
        	return ext.toString();
        }
        return "";
    };

    /**
     * get the switched extent of the mapObj
     *
     * @member Map
     * @return extent of the mapObj as commaseparated minx,minx,maxx,maxy
     * @type string
     */    
    this.getExtentSwitch = function(){
        var ext = this.extent;
	var tmpExtent;
        var tmp;
        if (ext) {
	tmpExtent = ext.toString().split(",");
        tmp = tmpExtent[0];
        tmpExtent[0] = tmpExtent[1];
        tmpExtent[1] = tmp;
        tmp = tmpExtent[2];
        tmpExtent[2] = tmpExtent[3];
        tmpExtent[3] = tmp;
        	return tmpExtent.toString();
        }
        return "";
    };

    /**
     * get the extent as minx, maxx, miny, maxy
     *
     * @return extent and additional informations of the mapObj
     * @type Object
     */
    this.getExtentInfos = function(){
        var c = this.getExtent().split(",");
		if (typeof c !== "object" || c.length !== 4) { 
			return null; 
		} 
		var ext = new Mapbender.Extent(c[0], c[1], c[2], c[3]);
        return ext;
    };
    
    /**
     * Sets the list of layers, styles and querylayers for a specified WMS
     */
    this.restateLayers = function(wms_id){
        for (var i = 0; i < this.wms.length; i++) {
            if (this.wms[i].wms_id == wms_id) {
                var currentWms = this.wms[i];
                var cnt_layers = 0;
                var cnt_querylayers = 0;
                var layers = "";
                var styles = "";
                var querylayers = "";
                for (var ii = 0; ii < currentWms.objLayer.length; ii++) {
                    var currentLayer = currentWms.objLayer[ii];
                    if (currentLayer.gui_layer_visible == 1 && !currentLayer.has_childs) {
                        if (cnt_layers > 0) {
                            layers += ",";
                            styles += ",";
                        }
                        layers += encodeURIComponent(currentLayer.layer_name);
                        styles += "";
                        cnt_layers++;
                    }
                    if (currentLayer.gui_layer_querylayer == 1 && !currentLayer.has_childs) {
                        if (cnt_querylayers > 0) {
                            querylayers += ",";
                        }
                        querylayers += currentLayer.layer_name;
                        cnt_querylayers++;
                    }
                }
                this.layers[i] = layers;
                this.querylayers[i] = querylayers;
                this.styles[i] = styles;
            }
        }
        //		this.setExtent(ext.minx,ext.miny,ext.maxx,ext.maxy);
    };
    
    /**
     *
     * @param {Object} direction
     */
    this.pan = function(direction){
        var arrayBBox = this.getExtent().split(",");
        var minx = parseFloat(arrayBBox[0]);
        var miny = parseFloat(arrayBBox[1]);
        var maxx = parseFloat(arrayBBox[2]);
        var maxy = parseFloat(arrayBBox[3]);
        var xtentx = maxx - minx;
        var xtenty = maxy - miny;
        var factor = 0.5;
        
        switch (direction.toUpperCase()) {
            case "NW":
                minx -= (xtentx * factor);
                maxx -= (xtentx * factor);
                miny += (xtenty * factor);
                maxy += (xtenty * factor);
                break;
            case "N":
                miny += (xtenty * factor);
                maxy += (xtenty * factor);
                break;
            case "NE":
                minx += (xtentx * factor);
                maxx += (xtentx * factor);
                miny += (xtenty * factor);
                maxy += (xtenty * factor);
                break;
            case "E":
                minx += (xtentx * factor);
                maxx += (xtentx * factor);
                break;
            case "SE":
                minx += (xtentx * factor);
                maxx += (xtentx * factor);
                miny -= (xtenty * factor);
                maxy -= (xtenty * factor);
                break;
            case "S":
                miny -= (xtenty * factor);
                maxy -= (xtenty * factor);
                break;
            case "SW":
                minx -= (xtentx * factor);
                maxx -= (xtentx * factor);
                miny -= (xtenty * factor);
                maxy -= (xtenty * factor);
                break;
            case "W":
                minx -= (xtentx * factor);
                maxx -= (xtentx * factor);
                break;
        }
        this.setExtent(minx, miny, maxx, maxy);
        //		this.restrictedExtent;
        this.setMapRequest();
        
    };
    
    this.setCenter = function(aPoint){
        this.extent.set(aPoint);
        this.calculateExtent(this.extent);
        this.setMapRequest();
    };
    
    this.zoomFull = function(){
//        if (this.restrictedExtent) {
//            this.calculateExtent(this.restrictedExtent);
//            this.setMapRequest();
//        }
//        else {
            if (typeof this.wms !== "object" || this.wms.length === 0) {
            	return;
            }
            for (var i = 0; i < this.wms[0].gui_epsg.length; i++) {
                if (this.epsg == this.wms[0].gui_epsg[i]) {
                    var bbox_minx = parseFloat(this.wms[0].gui_minx[i]);
                    var bbox_miny = parseFloat(this.wms[0].gui_miny[i]);
                    var bbox_maxx = parseFloat(this.wms[0].gui_maxx[i]);
                    var bbox_maxy = parseFloat(this.wms[0].gui_maxy[i]);
                    
                    var wmsExtent = new Mapbender.Extent(bbox_minx, bbox_miny, bbox_maxx, bbox_maxy);
                    this.calculateExtent(wmsExtent);
                    this.setMapRequest();
                    break;
                }
            }
//       }
    };
    
	this.calculateExtentAfterZoom = function (in_, factor, x, y) {
        factor = parseFloat(factor);
        if (!in_) {
            factor = 1 / factor;
        }
        
        var extent = that.getExtentInfos();
        var distx = extent.maxx - extent.minx;
        var disty = extent.maxy - extent.miny;
        
        if (typeof x === "object" && x.constructor === Mapbender.Point) {
            y = x.y;
            x = x.x;
        }
        
		var centerx, centery;
        if (x && y) {
            centerx = parseFloat(x);
            centery = parseFloat(y);
        }
        else {
            centerx = extent.minx + distx / 2;
            centery = extent.miny + disty / 2;
        }
        
        var new_distx = distx / factor;
        var new_disty = disty / factor;
        var minx = centerx - new_distx / 2;
        var miny = centery - new_disty / 2;
        var maxx = centerx + new_distx / 2;
        var maxy = centery + new_disty / 2;
        // Check if ext is within restricted extent
        // If not, calculate a new extent according
        // to restricted extent.
        /*
         var relation_px_x = this.getWidth() / this.getHeight();
         var relation_px_y = this.getHeight() / this.getWidth();
         if ( this.restrictedExtent ) {
         if ( minx  < this.restrictedExtent.minx ) {
         minx = this.restrictedExtent.minx;
         maxx = minx + (relation_px_x * new_disty);
         }
         if ( miny < this.restrictedExtent.miny ) {
         miny = this.restrictedExtent.miny;
         maxy = miny + (relation_px_y * new_distx);
         }
         if ( maxx > this.restrictedExtent.maxx ) {
         maxx = this.restrictedExtent.maxx;
         minx = maxx - (relation_px_x * new_distx);
         }
         if ( maxy > this.restrictedExtent.maxy ) {
         maxy = this.restrictedExtent.maxy;
         miny = maxy - (relation_px_y * new_disty);
         }
         }
         */
		return new Mapbender.Extent(minx, miny, maxx, maxy);		
	};
	
    /**
     * zoom the map with a zoomfactor and optional to x,y coords
     *
     * @param {boolean} in_ in = true, out = false
     * @param {float} factor the zoomfactor 1 equals 100%
     * @param {float} x center to x-position
     * @param {float} y center to y-position
     */
    this.zoom = function(in_, factor, x, y, crs){
    	if (crs) {
    		if (this.epsg !== crs) {
	    		var zoomPoint = new Mapbender.Point(x, y);
	    		var newPoint = zoomPoint.transform(crs, this.epsg, function (p) {
	    			that.setExtent(that.calculateExtentAfterZoom(in_, factor, p.x, p.y));
		            that.setMapRequest();
	    		});
	    	}
    	}
    	else {
    		this.setExtent(this.calculateExtentAfterZoom(in_, factor, x, y));
            this.setMapRequest();
    	}
	};
    
    this.convertPixelToReal = function(aPoint, referenceExtent){
    	var arrayBBox;
		if (arguments.length === 2) {
    		arrayBBox = referenceExtent.toString().split(",");
    	}
    	else {
	        arrayBBox = this.getExtent().split(",");
			if (typeof arrayBBox !== "object" || arrayBBox.length !== 4) { 
				return new Mapbender.Point(0,0); 
			} 
     	}
        var minX = parseFloat(arrayBBox[0]);
        var minY = parseFloat(arrayBBox[1]);
        var maxX = parseFloat(arrayBBox[2]);
        var maxY = parseFloat(arrayBBox[3]);
        var xtentx = maxX - minX;
        var xtenty = maxY - minY;
        var deltaX = xtentx / this.getWidth();
        var deltaY = xtenty / this.getHeight();
        
 		var digitsX = Math.round(Math.log(deltaX)/Math.log(10));
		var digitsY = Math.round(Math.log(deltaY)/Math.log(10));
		var roundX = Math.pow(10, -digitsX);
		var roundY = Math.pow(10, -digitsY);
		
		var posX = parseFloat(minX + (aPoint.x / this.getWidth()) * xtentx);
		var posY = parseFloat(maxY - (aPoint.y / this.getHeight()) * xtenty);
		posX = Math.round(posX * roundX) / roundX;
		posY = Math.round(posY * roundY) / roundY;
		newX = posX.toFixed(Math.abs(digitsX));
		newY = posY.toFixed(Math.abs(digitsY));
		var pt = new Point(newX, newY);
		return pt;

    };
    
    /**
     * Convert real world coordinates to pixel coordinates
     */
    this.convertRealToPixel = function(aPoint, referenceExtent){
    	var arrayBBox;
		if (arguments.length === 2) {
    		arrayBBox = referenceExtent.toString().split(",");
    	}
    	else {
	        arrayBBox = this.getExtent().split(",");
    	}
        var minX = parseFloat(arrayBBox[0]);
        var minY = parseFloat(arrayBBox[1]);
        var maxX = parseFloat(arrayBBox[2]);
        var maxY = parseFloat(arrayBBox[3]);
        return new Point(Math.round((aPoint.x - minX) * this.getWidth() / (maxX - minX)), Math.round((maxY - aPoint.y) * this.getHeight() / (maxY - minY)));
    };
    
    /**
     * get the srs of the mapObj
     *
     * @return srs as epsg:number
     * @type string
     */
    this.getSRS = function(){
        return this.epsg;
    };
    
    this.getSrs = function(){
        return this.epsg;
    };
    
    /**
     * Return the map URL of the WMS at index i
     * @param {Object} currentWmsIndex
     */
    this.getMapUrl = function(ii, extent){
        var currentWms = this.wms[ii];
	var tmpExtent;
	tmpExtent = extent.toString().split(",");
        tmp = tmpExtent[0];
        tmpExtent[0] = tmpExtent[1];
        tmpExtent[1] = tmp;
        tmp = tmpExtent[2];
        tmpExtent[2] = tmpExtent[3];
        tmpExtent[3] = tmp;
        var validLayers = (arguments.length === 3 && typeof arguments[2] === "number") ? 
			currentWms.getLayers(this, arguments[2]) : currentWms.getLayers(this);
        if (validLayers.length === 0) {
            return false;
        }
		var validLayersEncoded = [];
        for (var i = 0; i < validLayers.length; i++) {
            validLayersEncoded[i] = encodeURIComponent(validLayers[i]);
        }
        var layerNames = validLayersEncoded.join(",");
        
        url = currentWms.wms_getmap;
        url += mb_getConjunctionCharacter(currentWms.wms_getmap);
        
        if (currentWms.wms_version == "1.0.0") {
            url += "WMTVER=" + currentWms.wms_version + "&REQUEST=map&";
        }
        else {
            url += "VERSION=" + currentWms.wms_version + "&REQUEST=GetMap&SERVICE=WMS&";
        }
        
        url += "LAYERS=" + layerNames + "&";
        url += "STYLES=";
        for (var j = 0; j < validLayers.length; j++) {
            if (j > 0) {
                url += ",";
            }
            
            if (currentWms.getCurrentStyleByLayerName(validLayers[j]) !== false &&
            typeof currentWms.getCurrentStyleByLayerName(validLayers[j]) !== "undefined") {
                url += encodeURIComponent(currentWms.getCurrentStyleByLayerName(validLayers[j]));
            }
        }
        url += "&";
        if (currentWms.wms_version != "1.3.0") {
		url += "SRS=" + this.epsg + "&";
        }else{
		url += "CRS=" + this.epsg + "&";
	}
	tmp_epsg = this.epsg;
	tmp_epsg = tmp_epsg.replace(/EPSG:/g,'');
	tmp_epsg = tmp_epsg.replace(/CRS:/g,'');  
        //if (currentWms.wms_version == "1.3.0" &&  tmp_epsg >= 4000 && tmp_epsg < 5000){
        if(currentWms.wms_version == "1.3.0" && epsg_axis_order.indexOf(parseInt(tmp_epsg))>= 0){ 
                url += "BBOX=" + tmpExtent.toString() + "&";
        }else{
                url += "BBOX=" + extent.toString() + "&";
        }
        url += "WIDTH=" + this.getWidth() + "&";
        url += "HEIGHT=" + this.getHeight() + "&";
        url += "FORMAT=" + currentWms.gui_wms_mapformat + "&";
        url += "BGCOLOR=0xffffff&";
        
        if (currentWms.gui_wms_mapformat.search(/gif/i) > -1 ||
        currentWms.gui_wms_mapformat.search(/png/i) > -1) {
            url += "TRANSPARENT=TRUE&";
        }
        
        url += "EXCEPTIONS=" + currentWms.gui_wms_exceptionformat + "&";
        
        // add vendor-specific
		for (var v = 0; v < mb_vendorSpecific.length; v++) {
			var functionName = 'setMapRequest';
			var currentWms_wms_title = currentWms.wms_title;
			var vendorSpecificString = eval(mb_vendorSpecific[v]);
			// if eval doesn't evaluate a function, the result is undefined.
			// Sometimes it is necessary not to evaluate a function, for
			// example if you want to change a variable from the current
			// scope (see mod_addSLD.php) 
			if (typeof(vendorSpecificString) != "undefined") {
				url += vendorSpecificString + "&";
				
				// not sure what this is, but it's evil
				try {
					if (currentWms.wms_title == removeLayerAndStylesAffectedWMSTitle) {
						url = url.replace(/LAYERS=[^&]*&/, '');
						url = url.replace(/STYLES=[^&]*&/, '');
					}
				}
				catch (exc) {
					new Mb_warning(exc.message);
				}
			}
		}
        // add Filter
        if (currentWms.wms_filter !== "") {
            url += "&SLD=" + currentWms.wms_filter + "?id=" + mb_styleID + "&";
        }
        // add sld
        if (currentWms.gui_wms_sldurl !== "") {
            url += "&SLD=" + escape(currentWms.gui_wms_sldurl) + "&";
        }
        //remove the last ampersant (&) of the mapurl
        url = url.substr(0, url.length - 1);
        
        return url;
    };
    
    /**
     * get all featureInfoRequests
     *
     * @member Map
     * @param float x the x-value of the click position in pixel
     * @param float y the y-value of the click position in pixel
     * @return array of all featureInfoRequests of this map object
     * @type string[]
     */
    this.getFeatureInfoRequests = function(clickPoint, ignoreWms){
        var allRequests = [];
        //loop through all wms to get the FeatureInfoRequests
        for (var i = 0; i < this.wms.length; i++) {
            var ignoreThisWms = false;
            if (typeof ignoreWms !== "undefined" &&
            ignoreWms.constructor === Array) {
            
                for (var j = 0; j < ignoreWms.length; j++) {
                    if (ignoreWms[j] == this.wms[i].wms_id) {
                        ignoreThisWms = true;
                        break;
                    }
                }
            }
            if (!ignoreThisWms) {
                var currentRequest = this.wms[i].getFeatureInfoRequest(this, clickPoint);
                if (currentRequest) {
                    allRequests.push(currentRequest);
                }
            }
        }
        if (allRequests.length > 0) {
            return allRequests;
        }
        return false;
    };

	var calculateDistanceGeographic = function (a, b) {
		var lon_from = (a.x * Math.PI) / 180;
		var lat_from = (a.y * Math.PI) / 180;
		var lon_to = (b.x * Math.PI) / 180;
		var lat_to = (b.y * Math.PI) / 180;
		return Math.abs(6371229 * Math.acos(
			Math.sin(lat_from) * Math.sin(lat_to) + 
			Math.cos(lat_from) * Math.cos(lat_to) * 
			Math.cos(lon_from - lon_to)
		));
	};
    
    /**
     * calculation of the mapscale
     *
     * @member Map
     * @return scale
     * @type integer
     */
    this.getScale = function(){
        var scale;
		var bbox = this.getExtent().split(","); 
		if (typeof bbox !== "object" || bbox.length !== 4) { 
			return null; 
		} 
		if (this.epsg == "EPSG:4326") {
			var xtenty = calculateDistanceGeographic(
				new Mapbender.Point(
					this.extent.center.x,
					this.extent.min.y
				),
				new Mapbender.Point(
					this.extent.center.x,
					this.extent.max.y
				)
			);
		}
		else {
			var xtenty = parseFloat(bbox[3]) - parseFloat(bbox[1]);
		}       
		scale = (xtenty / this.getHeight()) * (mb_resolution * 100);
        return parseInt(Math.round(scale), 10);
    };
    
    /**
     *
     */
    this.checkScale = function(wmsIndex){
        var thisLayer = this.layers[wmsIndex].split(",");
        var thisScale = this.getScale();
        var str_layer = "";
        var cnt_layer = 0;
        for (var i = 0; i < this.wms[wmsIndex].objLayer.length; i++) {
            var currentLayer = this.wms[wmsIndex].objLayer[i];
            var myLayername = encodeURIComponent(currentLayer.layer_name);
            
            var myMinscale = currentLayer.gui_layer_minscale;
            var myMaxscale = currentLayer.gui_layer_maxscale;
            
            for (var ii = 0; ii < thisLayer.length; ii++) {
                if (thisLayer[ii] == myLayername && !currentLayer.has_childs) {
                    if (myMinscale !== 0 && thisScale < myMinscale) {
                        continue;
                    }
                    if (myMaxscale !== 0 && thisScale > myMaxscale) {
                        continue;
                    }
                    if (cnt_layer > 0) {
                        str_layer += ",";
                    }
                    str_layer += thisLayer[ii];
                    cnt_layer++;
                }
            }
        }
        var str_layerstyles = [];
        str_layerstyles[0] = str_layer;
        return str_layerstyles;
        
    };
    
    this.repaintScale = function(x, y, scale){
        if (x === null && y === null) {
            x = this.extent.center.x;
            y = this.extent.center.y;
        }
		if (this.epsg == "EPSG:4326") {
	        var minx = parseFloat(x);
	        var miny = this.extent.min.y;
	        var maxx = minx + 0.01;
			var xtenty = scale * this.getHeight() / (mb_resolution * 100);
	        var maxy = miny + xtenty/6371229;
		}
		else {
	        var minx = parseFloat(x) - (this.getWidth() / (mb_resolution * 100 * 2) * scale);
	        var miny = parseFloat(y) - (this.getHeight() / (mb_resolution * 100 * 2) * scale);
	        var maxx = parseFloat(x) + (this.getWidth() / (mb_resolution * 100 * 2) * scale);
	        var maxy = parseFloat(y) + (this.getHeight() / (mb_resolution * 100 * 2) * scale);
			
		}

        this.repaint(new Point(minx, miny), new Point(maxx, maxy));
    };
    
    this.repaint = function(min, max){
        if (typeof min !== "undefined" && typeof max !== "undefined") {
            this.extent = this.calculateExtent(new Extent(min, max));
        }
        this.setMapRequest();
    };
    
    this.setSingleMapRequest = function(wms_id){
		if (typeof wms_id === "object") {
			this.setMapRequest(wms_id);
		}
		else {
			this.setMapRequest([wms_id]);
		}
    };
    
    this.mb_setFutureObj = function(mod_back_cnt){
        var cnt = this.mb_MapFutureObj.length;
		var i;
        this.mb_MapFutureObj[cnt] = {};
        this.mb_MapFutureObj[cnt].reqCnt = mod_back_cnt;
        this.mb_MapFutureObj[cnt].width = this.getWidth();
        this.mb_MapFutureObj[cnt].height = this.getHeight();
        this.mb_MapFutureObj[cnt].epsg = this.epsg;
        this.mb_MapFutureObj[cnt].extent = this.extent;
        this.mb_MapFutureObj[cnt].layers = [];
        
        for (i = 0; i < this.layers.length; i++) {
            this.mb_MapFutureObj[cnt].layers[i] = this.layers[i];
        }
        
        this.mb_MapFutureObj[cnt].styles = [];
        
        for (i = 0; i < this.styles.length; i++) {
            this.mb_MapFutureObj[cnt].styles[i] = this.styles[i];
        }
        
        this.mb_MapFutureObj[cnt].querylayers = [];
        
        for (i = 0; i < this.querylayers.length; i++) {
            this.mb_MapFutureObj[cnt].querylayers[i] = this.querylayers[i];
        }
    };
    
    var drawMaps = function(idArray, index){
		var numRequests = that.history.count();
		var requestCnt = index;
		var i;
		
		// existing history item
		if (requestCnt + 1 < numRequests && numRequests > 1 || $("#" + that.elementName + "_request_" + requestCnt).size() > 0) {
	        $("#" + that.elementName + "_request_" + requestCnt).hide();
			for (i = 0; i < requestCnt; i++) {
		        $("#" + that.elementName + "_request_" + i).hide();
			}
			for (i = requestCnt + 1; i <= numRequests; i++) {
		        $("#" + that.elementName + "_request_" + i).hide();
			}
			$("#" + that.elementName + "_request_" + requestCnt + " div img").css({
				visibility: "visible"
			});
		}
		// new history item
		else {
	        var newMapRequest = "";
	        for (i = 0; i < idArray.length; i++) {
	            var currentWms = that.wms[idArray[i]];
/*
				if (that.slippy && currentWms.gui_wms_visible === 2) {
					// request larger background image
					var bgExtent = this.calculateExtentAfterZoom(false, 2.0, that.extent.center.x, that.extent.center.y);
					var bgSwPix = that.convertRealToPixel(bgExtent.min);
					var bgNePix = that.convertRealToPixel(bgExtent.max);
					
					var left = bgSwPix.x;
					var top = bgNePix.y;
					var width = bgNePix.x - bgSwPix.x;
					var height = bgSwPix.y - bgNePix.y;
					var html = getLayerHtmlCode(idArray[i], bgExtent, width, height, top, left, requestCnt);
				}
*/				
				newMapRequest += getLayerHtmlCode(
					idArray[i], 
					that.extent, 
					that.getWidth(), 
					that.getHeight(),
					0,
					0,
					requestCnt
				);
	        }
			var $currentRequest = $(
				"<div id='" + that.elementName + "_request_" + (requestCnt) + 
				"'></div>").hide().css({
					position: "absolute",
					top: "0px",
					left: "0px"
				})
			if (newMapRequest !== "") {
				$currentRequest.append(newMapRequest);	
			}
			
	        $("#" + that.elementName + "_maps").append($currentRequest);
			for (i = 0; i < requestCnt; i++) {
				// setting the visibility to hidden is a workaround to fix the ... that is Internet Explorer
		        $("#" + that.elementName + "_request_" + i).hide().css("visibility","hidden").each(function () {
					$(this).children().each(function () {
						this.style.zIndex = this.style.zIndex - 1;
					});
				});
			}
		}
		// for the reason for setting the visibility, see above
		$("#" + that.elementName + "_request_" + index).show().css("visibility","visible");
		
    };
    
    var displayMaps = function(index, wmsArray){
		var ret = eventBeforeMapRequest.trigger({
            map: that
        }, "AND");
        if (ret === false) {
            return true;
        }
        var myMapId = [];
        var mapUrlArray = [];
        var visibleWms = [];
		var restrictedWmsArray = typeof wmsArray === "object" && wmsArray.length ?
			wmsArray : [];
        for (var ii = 0; ii < that.wms.length; ii++) {
			var currentWms = that.wms[ii];
            that.restateLayers(currentWms.wms_id);
			if (restrictedWmsArray.length > 0) {
				var found = true;
				for (var j = 0; j < restrictedWmsArray.length; j++) {
					if (restrictedWmsArray[j] === currentWms.wms_id) {
						found = false;
						break;
					}
				}
				if (!found) {
					continue;
				}
			}
			
            try {
                if (that.skipWmsIfSrsNotSupported && isIgnoredWms(currentWms)) {
                    new Mb_notice(currentWms.wms_title + " is ignored.");
                    continue;
                }
            } 
            catch (e) {
                new Mb_warning(e.message);
            }
            if (!(currentWms.gui_wms_visible > 0)) {
                new Mb_notice(currentWms.title + " is not visible, will not be requested.");
                continue;
            }
            myMapId.push(that.elementName + "_request_" + index + "_map_" + ii);
            mapUrlArray.push(that.getMapUrl(ii, that.extent));
            visibleWms.push(ii);
        }
        
        drawMaps(visibleWms, index);
        
        eventAfterMapRequest.trigger({
            map: that,
            myMapId: myMapId.join(","),
            url: mapUrlArray
        });
        that.afterMapRequest.trigger({
            map: that,
            myMapId: myMapId.join(","),
            url: mapUrlArray
        });
        
    };
    
	var deactivatePreviousMapImg = function (selector) {
		$(selector).css({
			visibility: "hidden"
		});
	};
	
	this.moveMap = function (x, y) {
		var newX = x;
		var newY = y;
		var index = this.history.getCurrentIndex();

		// do not reset the map
		if (typeof newX !== "undefined") {
			$("#" + this.elementName + "_request_" + index).css({
				top: String(newY) + "px",
				left: String(newX) + "px"
			});
		}
		
		// reset pan sub elements only
		// TO DO: this looks awkward when animation is turned on
		if (typeof newX === "undefined") {
			newX = 0;
			newY = 0;			
		}
		for(var i = 0; i < mb_PanSubElements.length; i++) {
			mb_arrangeElement("", mb_PanSubElements[i], newX, newY);
		} 
	};
	
	var hideOutOfScaleWms = function (previousWmsIndices, currentWmsIndices, index) {
		$(previousWmsIndices).each(function () {
			if ($.inArray(parseInt(this, 10), currentWmsIndices) === -1) {
				var selector = "#" + that.elementName + "_request_" + (index-1) + "_map_" + parseInt(this, 10);
				deactivatePreviousMapImg(selector);
			}
		});
	};
	
	var animateMaps = function (index) {
		
		//
		// show new map
		// 
		
		if (that.slippy && extentHasChanged && !srsHasChanged) {
			var previousRequest = $("#" + that.elementName + "_request_" + (index-1));
			var wasPanned = parseInt(previousRequest.css("top"), 10) !== 0 ||
				parseInt(previousRequest.css("left"), 10) !== 0;

			var newWidth = that.oldExtentPix.max.x - that.oldExtentPix.min.x;
			var newHeight = that.oldExtentPix.min.y - that.oldExtentPix.max.y;
			var newTop = that.oldExtentPix.max.y;
			var newLeft = that.oldExtentPix.min.x;

			//
			// abort if no animation is required
			//
			if (newLeft === 0 && newTop === 0 && newHeight === that.getHeight() && newWidth === that.getWidth()) {
				return;
			}

			//
			// check which WMS need to be deactivated because they are out of scale
			//
			
			// indices of wms in old map
			var prevMapImg = $("#" + that.elementName + "_request_" + (index-1) + " div img");
			var previousWmsIndices = [];
			prevMapImg.each(function () {
				var id = parseInt(this.id.split("_").pop(), 10);
				previousWmsIndices.push(id);
			});

			// indices of wms in new map
			var currentMapImg = $("#" + that.elementName + "_request_" + index + " div img");
			var currentWmsIndices = [];
			currentMapImg.each(function () {
				var id = parseInt(this.id.split("_").pop(), 10);
				currentWmsIndices.push(id);
			});


			
			//
			// show previous maps for animation
			//
			$("#" + that.elementName + "_request_" + (index - 1)).show();

			//
			// hide new images until complete, but perform pan animation
			//
			var currentMapImg = $("#" + that.elementName + "_request_" + index + " div img");
			if (currentMapImg.size() === 0) {
				hideOutOfScaleWms(previousWmsIndices, currentWmsIndices, index);
			}
			else {
				currentMapImg.css({
					visibility: "hidden"
				}).load(function () {
					this.style.visibility = "visible";
					
					$(this).data("loaded", true);
					if ($(this).data("loaded") && (wasPanned || $(this).data("animationFinished"))) {
						hideOutOfScaleWms(previousWmsIndices, currentWmsIndices, index);
						
						var tmpId = this.id;
						var pattern = /_request_([0-9]+)_/;
						var selector = "#" + tmpId.replace(pattern, "_request_" + (index-1) + "_");
						deactivatePreviousMapImg(selector);
					}
				});
			}
		

			if (wasPanned) {
				return;
			}

			//
			// animate new images (zoom)
			//
			if (newWidth !== that.getWidth() || newHeight !== that.getHeight()) {

				currentMapImg.css({
					width: newWidth,
					height: newHeight				
				}).animate(
					{
						width: that.getWidth() + "px",
						height: that.getHeight() + "px"
					}, "normal", "linear", function () {

						$(this).data("animationFinished", true);
						if ($(this).data("loaded") && ($(this).data("animationFinished"))) {
							hideOutOfScaleWms(previousWmsIndices, currentWmsIndices, index);

							var tmpId = this.id;
							var pattern = /_request_([0-9]+)_/;
							var selector = "#" + tmpId.replace(pattern, "_request_" + (index-1) + "_");
							deactivatePreviousMapImg(selector);
						}
					}
				);
			}

			//
			// animate new images (pan & zoom)
			//
			$("#" + that.elementName + "_request_" + index).css({
				position: "absolute",
				top: newTop,
				left: newLeft
			}).animate(
				{
					left: "0px",
					top: "0px"
				}, "normal", "linear", function () {
					$(this).children().children().each(function () {
						$(this).data("animationFinished", true);
						if ($(this).data("loaded") && $(this).data("animationFinished")) {
							hideOutOfScaleWms(previousWmsIndices, currentWmsIndices, index);

							var tmpId = this.id;
							var pattern = /_request_([0-9]+)_/;
							var selector = "#" + tmpId.replace(pattern, "_request_" + (index-1) + "_");
							deactivatePreviousMapImg(selector);
						}
					});
				}
			);
	

			//
			// animate previous request div and images
			//
			
			// calculate old extent's pixel pos in new extent
			var oldLLPix = that.convertRealToPixel(that.oldExtent.min);
			var oldURPix = that.convertRealToPixel(that.oldExtent.max);
			
			var left = oldLLPix.x;
			var top = oldURPix.y;
			var width = oldURPix.x - oldLLPix.x;
			var height = oldLLPix.y - oldURPix.y;
			
			// zoom
			if (width !== that.getWidth() || height !== that.getHeight()) {
				// zoom
				var oldMapImg = $("#" + that.elementName + "_request_" + (index-1) + " div img");
				oldMapImg.css({
					position: "absolute"
				}).animate(
					{
						width: width + "px",
						height: height + "px"
					}, 
					"normal",
					"linear"
				);
			}

			// pan & zoom
			$("#" + that.elementName + "_request_" + (index - 1)).animate(
				{
					left: left + "px",
					top: top + "px"
				}, 
				"normal", 
				"linear"
			);
		}
	};
	
    this.setMapRequest = function(wmsArray){
		if (!this.wms || this.wms.length === 0) { 
			return; 
		}         
 	
 		// initialize history
        (function(){
			var extent = new Extent(that.extent);
			if (typeof that.oldExtent === "undefined") {
				that.setExtent(extent, false);
				displayMaps(0);
				if (typeof wmsArray === "undefined") {
					animateMaps(0);
				}
			}
			else {
	            var oldExtent = new Extent(that.oldExtent);
	            that.history.addItem({
	                forward: function(setExtent){
						var index = that.history.getCurrentIndex();
	                    if (typeof setExtent === "undefined" || setExtent === true) {
							that.setExtent(extent);
						}
	        			displayMaps(index + 1);
						if (typeof wmsArray === "undefined") {
							animateMaps(index + 1);
						}
	                },
	                back: function(){
						var index = that.history.getCurrentIndex();
						that.setExtent(oldExtent);
			            displayMaps(index);
						if (typeof wmsArray === "undefined") {
							animateMaps(index);
						}
	                }
	            });
			}
		})();
		
		this.history.forward(false);
		extentHasChanged = false;
		srsHasChanged = false;
    };
    
    var getLayerHtmlCode = function(ii, extent, width, height, top, left, requestCnt){
        var currentWms = that.wms[ii];
        
        var myDivId = that.elementName + "_request_" + requestCnt + "_div_" + ii;
        var myMapId = that.elementName + "_request_" + requestCnt + "_map_" + ii;
        var ts = mb_timestamp();
        
        //disable Layer which are out of scale
        var validLayers = that.checkScale(ii);
        var layerNames = validLayers.toString();
        
        var newMapURL = false;
        var opacityString = "";
        
        if (that.layers[ii] !== "" && layerNames !== '') {
            // get map URL
            newMapURL = that.getMapUrl(ii, that.extent);
            
            var currentOpacity = currentWms.gui_wms_mapopacity;
            if (currentOpacity != 1) {
                opacityString += "opacity:" + currentOpacity + "; ";
                opacityString += "Filter: Alpha(Opacity=" + currentOpacity * 100 + "); ";
                opacityString += "-moz-opacity:" + currentOpacity + " ; ";
                opacityString += "-khtml-opacity:" + currentOpacity;
            }
        }
        
        var imageString = "";
        if (newMapURL) {
            imageString = "<img id='" + myMapId + "' name='mapimage' ";
            imageString += "src='" + newMapURL + "' ";
            imageString += "width='" + width + "' ";
            imageString += "height='" + height + "' ";
            imageString += "border='0'>";
        }
        
        var newMapRequest = "<div id='" + myDivId + "' ";
        newMapRequest += "style=\"position:absolute; top:" + top + "px; left:" + left + "px; ";
        //newMapRequest += "style=\"";
        newMapRequest += "z-index:" + (2 * ii + 20) + ";" + opacityString + "\">";
        newMapRequest += imageString;
        newMapRequest += "</div>";
        
        that.mapURL[ii] = newMapURL;
        currentWms.mapURL = newMapURL;
        
        if (Mapbender.log && currentWms.mapURL) {
            var tmp = eval(Mapbender.log + "('" +
            newMapURL +
            "','" +
            ts +
            "')");
        }
        
        return newMapRequest;
    };
    
    this.getWmsIdByTitle = function(title){
        for (var i = 0; i < this.wms.length; i++) {
            if (this.wms[i].wms_title == title) {
                return this.wms[i].wms_id;
            }
        }
        return null;
    };
    
    this.getWmsIndexById = function(wms_id){
        for (var i = 0; i < this.wms.length; i++) {
            if (this.wms[i].wms_id == wms_id) {
                return i;
            }
        }
        return null;
    };
    
    this.getWmsById = function(wms_id){
        for (var i = 0; i < this.wms.length; i++) {
            if (this.wms[i].wms_id == wms_id) {
                return this.wms[i];
            }
        }
        return null;
    };
    
    this.removeWms = function(wmsIndex){
        var wms_ID = null;
        var i;
        var new_wmsarray = [];
        var new_layerarray = [];
        var new_querylayerarray = [];
        var new_stylesarray = [];
        var new_mapURLarray = [];
        
        for (i = 0; i < this.wms.length; i++) {
            if (i != wmsIndex) {
                new_wmsarray.push(this.wms[i]);
                new_layerarray.push(this.layers[i]);
                new_querylayerarray.push(this.querylayers[i]);
                new_stylesarray.push(this.styles[i]);
                new_mapURLarray.push(this.mapURL[i]);
            }
            else {
                wms_ID = this.wms[i].wms_id;
            }
        }
        this.wms = new_wmsarray;
        this.layers = new_layerarray;
        this.querylayers = new_querylayerarray;
        this.styles = new_stylesarray;
        this.mapURL = new_mapURLarray;
        
        var another_new_wmsarray = [];
        
        for (i = 0; i < window.wms.length; i++) {
            if (window.wms[i].wms_id != wms_ID) {
                another_new_wmsarray.push(window.wms[i]);
            }
        }
        window.wms = another_new_wmsarray;
    };
    
    /**
     * move a wms or layer
     *
     * @param int wms_id id of wms to move
     * @param int layer_id id of layer to move
     * @return true of successful
     * @type boolean
     */
    this.move = function(wms_id, layer_id, moveUp){
        var i, j;
        for (i = 0; i < this.wms.length; i++) {
            if (wms_id == this.wms[i].wms_id) {
                break;
            }
        }
        
        //check if only one wms is affected?
        if (layer_id && layer_id != this.wms[i].objLayer[0].layer_id) {
            return this.wms[i].moveLayer(layer_id, moveUp);
        }
        
        //else swap wms
        j = i + (moveUp ? -1 : 1);
        if (!(i != j && i >= 0 && i < this.wms.length && j >= 0 && j < this.wms.length)) {
            return false;
        }
        
        upper = this.wms[i];
        this.wms[i] = this.wms[j];
        this.wms[j] = upper;
        var upperLayers = this.layers[i];
        var upperStyles = this.styles[i];
        var upperQuerylayers = this.querylayers[i];
        this.layers[i] = this.layers[j];
        this.styles[i] = this.styles[j];
        this.querylayers[i] = this.querylayers[j];
        this.layers[j] = upperLayers;
        this.styles[j] = upperStyles;
        this.querylayers[j] = upperQuerylayers;
        this.events.afterMoveWms.trigger();
        return true;
    };

	/* DEPRECATED! */    
	this.getMousePos = function(e){
		var pt = this.getMousePosition(e);
		// set the globals for backwards compatibility
		if (pt !== null) {
			clickX = pt.x;
			clickY = pt.y;
		}
		return pt;
    };
    
    var that = this;
    // private
    this.width = options.width;
    // private
    this.height = options.height;
    this.type = "DIV";
    this.elementName = options.id;
    this.mapURL = [];
    var domElement = this.getDomElement();
	if (this.width) {
	    domElement.style.width = this.width;
	}
	else {
		this.width = this.getWidth();
	}
	if (this.height) {
		domElement.style.height = this.height + "px";
	}
	else {
		this.height = this.getHeight();
	}

    ignoredWms = [];
    this.layers = [];
    this.styles = [];
    this.querylayers = [];
    this.geom = "";
    this.gml = "";
    this.wms = [];
    
    var bbox_minx, bbox_miny, bbox_maxx, bbox_maxy;
    
    /**
     * Triggered after the map has been resized
     */
    this.eventResizeMap = new MapbenderEvent();
    
    //
    // Add pointers to WMS objects which are in this map.
    //
    this.setWms(o);
    //
    // set list of visible layers, active querylayers 
    // and styles for each WMS in this map
    //
    this.initializeWms();
    
	if (typeof this.wms !== "object" || this.wms.length === 0) {
		var errorMsg = "There are no WMS in map '" + this.elementName + "'"; 
		new Mapbender.Exception(errorMsg); 
		$(domElement).css({ 
			"border": "1px solid red", 
			"color": "red" 
		}).children().text(errorMsg); 
	}     
	else {
	    for (var i = 0; i < this.wms[0].gui_epsg.length; i++) {
	        if (this.wms[0].gui_wms_epsg == this.wms[0].gui_epsg[i]) {
	            bbox_minx = parseFloat(this.wms[0].gui_minx[i]);
	            bbox_miny = parseFloat(this.wms[0].gui_miny[i]);
	            bbox_maxx = parseFloat(this.wms[0].gui_maxx[i]);
	            bbox_maxy = parseFloat(this.wms[0].gui_maxy[i]);
	            break;
	        }
	    }
	    
	    this.setSrs({
	        srs: this.wms[0].gui_wms_epsg,
	        extent: new Mapbender.Extent(bbox_minx, bbox_miny, bbox_maxx, bbox_maxy)
	    });
	}
    
    
    this.afterMapRequest = new Mapbender.Event();
	this.events = {
		afterMapRequest: this.afterMapRequest,
		afterMoveWms: new Mapbender.Event(),
		dimensionsChanged: new Mapbender.Event()		
	};
    
};

Mapbender.Map.prototype.getWfsConfIds = function(wfs_config){
    var db_wfs_conf_id = [];
    var js_wfs_conf_id = [];
    var i, ii;
    //search configurations that are selected (and in scale)
    for (i = 0; i < this.wms.length; i++) {
        for (ii = 0; ii < this.wms[i].objLayer.length; ii++) {
            var o = this.wms[i].objLayer[ii];
            if (o.gui_layer_wfs_featuretype != '' && o.gui_layer_querylayer == '1') {
                if (!checkscale || o.checkScale(this)) {
                    db_wfs_conf_id[db_wfs_conf_id.length] = o.gui_layer_wfs_featuretype;
                }
            }
        }
    }
    for (i = 0; i < db_wfs_conf_id.length; i++) {
        for (ii = 0; ii < wfs_config.length; ii++) {
            if (wfs_config[ii].wfs_conf_id == db_wfs_conf_id[i]) {
                js_wfs_conf_id[js_wfs_conf_id.length] = ii;
                break;
            }
        }
    }
    return js_wfs_conf_id;
};
