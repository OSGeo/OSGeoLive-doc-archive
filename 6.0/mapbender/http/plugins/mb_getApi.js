var $getApi = $(this);

var GetApi = function (o) {
	
	Mapbender.events.init.register(function () {
		var setCRS = function (params) {
			if (!params.CRS) { 
				return;
			}
			
			options.$target.each(function () {
				var mapObj = $(this).mapbender();
				mapObj.setCrs({
					crs : params.CRS,
					callback : function () {
						setZOOM(params);
					}
				}); 
			});
	   	}
		
		var setZOOM = function (params) {
			if (!params.ZOOM) { 
				return;
			}
			
			options.$target.each(function () {
				var mapObj = $(this).mapbender();
				if (mapObj.id.match(/^overview/)) {
					return;
				}
		
				if (mapObj === null) {
					var e = new Mb_exception("mb_getApi: unknown map object");
					return;
				}
		
				var zoomParams = params.ZOOM.split(",");
				
				var epsg = "";
				if(zoomParams[zoomParams.length -1].match(/EPSG/)){
					epsg = zoomParams[zoomParams.length -1];
					zoomParams.pop();
				}
				
				if (zoomParams.length === 4) {
					var newExtent = new Mapbender.Extent(
						parseFloat(zoomParams[0]),
						parseFloat(zoomParams[1]),
						parseFloat(zoomParams[2]),
						parseFloat(zoomParams[3])
					);
					
					if (epsg) {
						mapObj.transformExtent({
							extent : newExtent,
							crs : epsg
						});
					}
					else {
						mapObj.calculateExtent(newExtent);
						mapObj.setMapRequest();
					}
				}
				
				//zoomParams contains 2 coordinates + scale (and optional EPSG param)
				if (zoomParams.length === 3) {
					mapObj.zoom(true, zoomParams[2], zoomParams[0], zoomParams[1], epsg);
				} 
			});
		}
		
		var setGEORSS = function (params) {
			if (!params.GEORSS) { 
				return;
			}
			
			// array of georss
			if(typeof params.GEORSS == 'object') {
				for (var i = 0 ; i < params.GEORSS.length ; i++) {
					options.$target.eq(0).georss({
						url : params.GEORSS[i]
					});
				}
			}
			// single georss
			else {
				options.$target.eq(0).georss({
					url : params.GEORSS
				});
			}
		};
		
		setCRS(getParams);
	    setZOOM(getParams);
	    setGEORSS(getParams);
	});

};

$getApi.mapbender(new GetApi(options));
