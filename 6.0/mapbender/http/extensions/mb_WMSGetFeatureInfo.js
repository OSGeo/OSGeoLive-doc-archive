OpenLayers.Control.MapbenderWMSGetFeatureInfo = OpenLayers.Class(OpenLayers.Control.WMSGetFeatureInfo, {

    /**
     * Method: buildWMSOptions
     * Build an object with the relevant WMS options for the GetFeatureInfo request
     *
     * Parameters:
     * url - {String} The url to be used for sending the request
     * layers - {Array(<OpenLayers.Layer.WMS)} An array of layers
     * clickPosition - {<OpenLayers.Pixel>} The position on the map where the mouse
     *     event occurred.
     * format - {String} The format from the corresponding GetMap request
     */
    buildWMSOptions: function(url, layers, clickPosition, format) {
        var layerNames = [], styleNames = [], queryLayerNames = [];
        for (var i = 0, len = layers.length; i < len; i++) { 
            layerNames = layerNames.concat(layers[i].params.LAYERS);
            queryLayerNames = queryLayerNames.concat(layers[i].params.QUERY_LAYERS);
            styleNames = styleNames.concat(this.getStyleNames(layers[i]));
        }

        var params = OpenLayers.Util.extend({
            service: "WMS",
            version: layers[0].params.VERSION,
            request: "GetFeatureInfo",
            layers: layerNames,
            query_layers: queryLayerNames,
            styles: styleNames,
            bbox: this.map.getExtent().toBBOX(null,
                layers[0].reverseAxisOrder()),
            feature_count: this.maxFeatures,
            height: this.map.getSize().h,
            width: this.map.getSize().w,
            format: format,
            info_format: this.infoFormat
        }, (parseFloat(layers[0].params.VERSION) >= 1.3) ?
            {
                crs: this.map.getProjection(),
                i: clickPosition.x,
                j: clickPosition.y
            } :
            {
                srs: this.map.getProjection(),
                x: clickPosition.x,
                y: clickPosition.y
            }
        );
        OpenLayers.Util.applyDefaults(params, this.vendorParams);
        return {
            url: url,
            params: OpenLayers.Util.upperCaseObject(params),
            callback: function(request) {
                this.handleResponse(clickPosition, request);
            },
            scope: this
        };
    },
    request: function(clickPosition, options) {
        var layers = this.findLayers();
        if(layers.length == 0) {
            // Reset the cursor.
            OpenLayers.Element.removeClass(this.map.viewPortDiv, "olCursorWait");
            return;
        }

		var noQueryLayers = function (queryLayerNames) {
			for (var i = 0; i < queryLayerNames.params.QUERY_LAYERS.length; i++) {
				if (queryLayerNames.params.QUERY_LAYERS[i] !== "") {
					return false;
				}
			}
			return true;
		};
        
        options = options || {};
        if(this.drillDown === false) {
            var wmsOptions = this.buildWMSOptions(this.url, layers,
                clickPosition, layers[0].params.FORMAT); 
			if (noQueryLayers(wmsOptions)) {
	            OpenLayers.Element.removeClass(this.map.viewPortDiv, "olCursorWait");
				return;
			}
            var response = OpenLayers.Request.GET(wmsOptions);
    
            if (options.hover === true) {
                this.hoverRequest = response.priv;
            }
        } else {
            this._requestCount = 0;
            this._numRequests = 0;
            this.features = [];
            // group according to service url to combine requests
            var services = {}, url;
            for(var i=0, len=layers.length; i<len; i++) {
                var layer = layers[i];
                var service, found = false;
                url = layer.url instanceof Array ? layer.url[0] : layer.url;
                if(url in services) {
                    services[url].push(layer);
                } else {
                    this._numRequests++;
                    services[url] = [layer];
                }
            }

            var layers;
			var noRequest = true;
            for (var url in services) {
                layers = services[url];
                var wmsOptions = this.buildWMSOptions(url, layers, 
                    clickPosition, layers[0].params.FORMAT);
				if (noQueryLayers(wmsOptions)) {
					continue;
				}
				noRequest = false;
                OpenLayers.Request.GET(wmsOptions); 
            }
            if (noRequest) {
	            OpenLayers.Element.removeClass(this.map.viewPortDiv, "olCursorWait");
            }
        }
    },

    CLASS_NAME: "OpenLayers.Control.MapbenderWMSGetFeatureInfo"
});
