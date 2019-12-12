//[  { featureTypeId: 24, fields: [ "hausnummer", "schlüssel" ] },   ...]

var API = function(options){
	var parameters = [];
	var currentFeatureTypeId = null;

	this.getParamater = function(wms){
		var layer = null;
		var resultString = "";
		var i = 0;
		for(i in wms.objLayer){
			layer = wms.objLayer[i];
			if(layer.gui_layer_wfs_featuretype == ""){ continue;}

			if(layer.gui_layer_wfs_featuretype == currentFeatureTypeId){
				// currentFeatureType is valid for this WMS
				var j = 0;
				for(j in parameters){
					resultString += encodeURIComponent(parameters[j].key) + "=" +encodeURIComponent(parameters[j].value) + "&";
				}
			}else{
				//default to NULL to satisfy Mapfiles
				var k = 0;
				for(k in options.featuretype_properties){
					if(layer.gui_layer_wfs_featuretype == options.featuretype_properties[k].ftId){
						var j = 0;
						for(j in options.featuretype_properties[k].fields){
							resultString += encodeURIComponent(options.featuretype_properties[k].fields[j]) + "=NULL&";
						}
					}
				}
			}
		}	
		return resultString;
	};
	
	Mapbender.modules[options.target[0]].rowclick.register(function(row){
		var me = Mapbender.modules[options.target[0]];
		var modelIndex = $(row).data("modelindex");

		// check if the currentFeatureTypeId is configured in the options
		// and if so, get the values
		var key ="";
		var value="";
		var i = 0;
		for(i in options.featuretype_properties){
			if(options.featuretype_properties[i].ftId == currentFeatureTypeId){
				parameters  = [];
				var j = 0;
				for(j in options.featuretype_properties[i].fields){
					key = options.featuretype_properties[i].fields[j];
					value = me.model.getFeatureProperty(modelIndex, key);
					parameters.push({ key: key, value: value});
				}	

			}
		}

		var feature = me.model.getFeature(modelIndex);
		var bbox = feature.getBBox();
		var bufferFloat = parseFloat(me.WFSConf.g_buffer);
		var buffer = new Point(bufferFloat,bufferFloat);
		bbox[0] = bbox[0].minus(buffer);
		bbox[1] = bbox[1].plus(buffer);
		var map = Mapbender.modules[me.options.target[0]];
		map.calculateExtent( new Mapbender.Extent(bbox[0], bbox[1]));
		map.setMapRequest();

	});
	
	Mapbender.modules[options.target[1]].events.onWfsConfSelect.register(function(data){
		currentFeatureTypeId = data.wfsConfId;
		var key ="";
		var value="";
		var i = 0;
		for(i in options.featuretype_properties){
			parameters  = [];
			if(options.featuretype_properties[i].ftId == currentFeatureTypeId){
				var j = 0;
				for(j in options.featuretype_properties[i].fields){
					key = options.featuretype_properties[i].fields[j];
					value = "NULL";
					parameters.push({ key: key, value: value});
				}	

			}
		}
	});

	Mapbender.modules[options.target[1]].events.onFormReset.register(function(){
		currentFeatureTypeId = null;
		var me = Mapbender.modules[options.target[0]];
		var map = Mapbender.modules[me.options.target[0]];
		map.setMapRequest();
	});
	
};

Mapbender.events.gazetteerReady.register(function(){
	Mapbender.modules[options.id] = $.extend(new API(options), Mapbender.modules[options.id]);
	mb_registerVendorSpecific("Mapbender.modules." +options.id + ".getParamater(currentWms, functionName)");
});
