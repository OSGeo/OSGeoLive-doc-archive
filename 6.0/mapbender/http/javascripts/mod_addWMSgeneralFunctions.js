function mb_mapObjaddWMS(obj){
	var cnt_layers = 0;
	var cnt_querylayers = 0;
	var styles = "";
	var layers = "";
	var querylayers = "";
	var ind = getMapObjIndexByName(obj);
	//is the id valid?
//	for( var i=0; i<(wms.length-1); i++){
//		if(parseInt(wms[i].wms_id, 10) >= parseInt(wms[wms.length-1].wms_id, 10)){
//			wms[wms.length-1].wms_id = parseInt(mb_mapObj[ind].wms[i].wms_id, 10) + 1;
//		}
//	} 
	

	mb_mapObj[ind].wms[mb_mapObj[ind].wms.length] = wms[wms.length-1];
	mb_mapObj[ind].layers[mb_mapObj[ind].layers.length] = layers;
	mb_mapObj[ind].styles[mb_mapObj[ind].styles.length] = styles;
	mb_mapObj[ind].querylayers[mb_mapObj[ind].querylayers.length] = querylayers;  
	var extArray = mb_mapObj[ind].extent.toString().split(",");
	var newExt = new Extent(
		parseFloat(extArray[0]),
		parseFloat(extArray[1]),
		parseFloat(extArray[2]),
		parseFloat(extArray[3])
	);
	mb_mapObj[ind].setSrs({
		srs: mb_mapObj[ind].epsg,
		extent: new Extent(
			parseFloat(newExt.minx),
			parseFloat(newExt.miny),
			parseFloat(newExt.maxx),
			parseFloat(newExt.maxy)
		),
		displayWarning: true
	});
	return true; 
}

function mod_addWMS_load(caps, param) {

	var options = {
		caps: caps,
		noHtml: 1
	};
	
	options[mb_session_name] = mb_nr;
	
	$.get("../php/mod_createJSObjFromXML.php", options, function (js, status) {
		var opt = {};
		
		if (typeof param !== "undefined") {
			opt = {
				callback: typeof param.callback === "function" ? param.callback : function(){
				},
				options: {
					visible: typeof param.visible === "number" ? param.visible : 0,
					zoomToExtent: typeof param.zoomToExtent === "number" ? param.zoomToExtent : 0
				}
			};
		}
		mod_addWms_general(js, opt);
	});
	
}

function mod_addLayer_load(caps, layer_name, param){
	var options = {
		caps: caps,
		layerName: layer_name,
		noHtml: 1
	};
	
	options[mb_session_name] = mb_nr;
	
	$.get("../php/mod_createJSLayerObjFromXML.php", options, function (js, status) {

		var opt = {};
		
		if (typeof param !== "undefined") {
			opt = {
				callback: typeof param.callback === "function" ? param.callback : function () {},
				options: {
					visible: typeof param.visible === "number" ? param.visible : 0,
					zoomToExtent: typeof param.zoomToExtent === "number" ? 
						param.zoomToExtent : 0
				}
			};
		}
		mod_addWms_general(js, opt);
	});
}

function mod_addWMSById_load(gui_id, wms_id){
	window.frames.loadData.document.location.href = "../php/mod_createJSObjFromDBByWMS.php?wms_id=" + wms_id + "&gui_id=" + gui_id;
}

var mod_addWms_general = function (js, param) {
	var ind = getMapObjIndexByName('mapframe1');
	var map = mb_mapObj[ind];

	var success = false;
	if (js) {
		var oldWmsCount = wms.length;
		eval(js);
		var newWmsCount = wms.length;
		if (newWmsCount > oldWmsCount) {
			success = true;
			mb_mapObjaddWMS('mapframe1');
			var lastwms = map.wms[map.wms.length - 1];
			if (param && param.options && typeof param.options.zoomToExtent === "number" && param.options.zoomToExtent === 1) {
				// zoom to bbox
				var bbox_minx, bbox_miny, bbox_maxx, bbox_maxy;
				for (var i = 0; i < lastwms.gui_epsg.length; i++) {
					if (map.epsg == lastwms.gui_epsg[i]) {
						bbox_minx = parseFloat(lastwms.gui_minx[i]);
						bbox_miny = parseFloat(lastwms.gui_miny[i]);
						bbox_maxx = parseFloat(lastwms.gui_maxx[i]);
						bbox_maxy = parseFloat(lastwms.gui_maxy[i]);
						if (bbox_minx === null || bbox_miny === null || bbox_maxx === null || bbox_maxy === null) {
							continue;
						}
						
						map.calculateExtent(new Mapbender.Extent(bbox_minx, bbox_miny, bbox_maxx, bbox_maxy));
						map.setMapRequest();
						break;
					}
				}
			}	
		}
	}
	if (typeof param === "object" 
		&& typeof param.callback === "function" 
		&& typeof param.options === "object"
		) {
		param.options.success = success;
		param.callback(param.options);
	}
	mb_execloadWmsSubFunctions({
		wms: map.wms.length > 0 ? map.wms[map.wms.length - 1] : null
	});
};

function mod_addWMSById_ajax (gui_id, wms_id, param) {
	var options = {
		wms_id: wms_id,
		gui_id: gui_id,
		noHtml: 1
	};
	
	// abort if WMS is already loaded
	var map = getMapObjByName('mapframe1');
	var wms = map.getWmsById(wms_id);

	var originalI18nObject = {
			"messageHint": "Hint",
			"messageMsg": "The selected service is already activated in your application and will not be included again:"
	};	

	var translatedI18nObject = Mapbender.cloneObject(originalI18nObject);	
	
	if (wms !== null) {
		try {
				
			var $msg = $('<div>' + translatedI18nObject.messageMsg + '<br /><b>' + wms.wms_title + '</b></div>');
			$msg.dialog({
					title: translatedI18nObject.messageHint,
					bgiframe: true,
					autoOpen: true,
					modal: false,
					width: 300,
					height: 200,
					pos: [100,50]
				});			
		}
		catch (e) {
			new Mb_warning(e.message + ". " + translatedI18nObject.messageMsg);
		}
		return;		
	}

	
	options[mb_session_name] = mb_nr;
	
	$.get("../php/mod_createJSObjFromDBByWMS.php", options, function (js, status) {
		var opt = {};
		
		if (typeof param !== "undefined") {
			opt = {
				callback: typeof param.callback === "function" ? param.callback : function(){
				},
				options: {
					wmsId: wms_id,
					appId: gui_id,
					visible: typeof param.visible === "number" ? param.visible : 0,
					zoomToExtent: typeof param.zoomToExtent === "number" ? param.zoomToExtent : 0
				}
			};
		}
		mod_addWms_general(js, opt);
	});
	
	Mapbender.events.localize.register(function(){
		Mapbender.modules.i18n.queue(options.id, originalI18nObject, function(translatedI18nObject){
			$('.labelLoadError').text(translatedI18nObject.labelLoadError); 
			$('.labelUrlBox').text(translatedI18nObject.labelUrlBox); 
		});
	});
}



function mod_addWMS_refresh(){
	mb_mapObjaddWMS('mapframe1');
	mb_execloadWmsSubFunctions();
	zoom('mapframe1', true, 0.999);
}
