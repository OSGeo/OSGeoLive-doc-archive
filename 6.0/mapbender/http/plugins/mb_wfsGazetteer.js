var $gazetteer = $(this);

// compatibilityevents:
// recaiveFeatureCollection
// onWfsConfSelect

$.get("../php/mod_wfs_gazetteer_server.php", {
	command:"getWfsConf",
	wfsConfIdString:options.wfsConf
}, function(json, status) {
	var wfsConf = $.parseJSON(json);
	$gazetteer.wfsGazetteer({
		target: options.$target,
		wfsConf: wfsConf[options.wfsConf],
		receivefeaturecollection : function(e,data){
			// trigger compatibility event for wfsGazeteer ? 	
			var resultList = Mapbender.modules.resultList;
			resultList.clear();
			resultList.setTitle(data.wfsConf.wfs_conf_abstract);
			resultList.setWFSconf(data.wfsConf);
			resultList.addFeatureCollection(data.featureCollection);
			resultList.show(); 
		}
	});

});

Mapbender.events.init.register(function () {
	options.$target.each(function(){
		if(this.id && Mapbender.modules[this.id]){
			Mapbender.modules[this.id].events.finished.register(function(obj){
				var fc = $.parseJSON(obj.featureCollection);
				$gazetteer.wfsGazetteer('option', 'geometry',fc.features[0]);
			});
		}
	});
	
	$gazetteer.bind("receivefeaturecollection", function () {
		if(options.activateLayer) {
			var activateLayer = options.activateLayer.split(",");
			var map = $(this + ":maps").mapbender();
			var wmsArray = map.wms;
			for(var i = 0; i < activateLayer.length; i++) {
				activateLayer[i] = $.trim(activateLayer[i]);
			}
			
			for (var i in wmsArray) {
				var currentWms = wmsArray[i];
				
				if(currentWms.gui_wms_visible == 1) {
					//first step: deactivate all WMS from tree
					handleSelectedWms(map.elementName, currentWms.wms_id, "visible", 0);
					handleSelectedWms(map.elementName, currentWms.wms_id, "querylayer", 0);
				} 
				
				for (var j in currentWms.objLayer) {
					var currentLayer = currentWms.objLayer[j];
					
					//second step: activate all layer from element var activateLayer
					if($.inArray(currentLayer.layer_name, activateLayer) != -1) {
						currentWms.handleLayer(currentLayer.layer_name, "visible", 1);
						currentWms.handleLayer(currentLayer.layer_name, "querylayer", 1);
					}
				}
			}
			map.setMapRequest();
			initWmsCheckboxen();
		}
	});
});
