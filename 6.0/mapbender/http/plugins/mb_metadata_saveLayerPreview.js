
//options.src = options.src || "img/button_gray/wmc_save_on. png";
var $saveLayerPreview = $(this);

var SaveLayerPreviewApi = function () {
	var that = this;
	var layer;
	this.setLayer = function (l) {
		layer = l;
	};

	this.save = function () {
		options.$target.each(function () {
			var map = $(this).mapbender();
			var wms = map.wms[map.wms.length-1];
			var url = wms.mapURL;
			var wmsId = wms.wms_id;
			var layerId = layer.layer_id;
			if (!layer || !layer.layer_name) {
				alert("No layer defined or layer is not named and cannot be requested!");
				return;
			}
			var layerId = layer.layer_id;
			var layerName = layer.layer_name;
			
			var layerParent = wms.checkLayerParentByLayerName(layerName); 
			var layerTitle = wms.getTitleByLayerName(layerName);
			var layerStyle = wms.getCurrentStyleByLayerName(layerName);
			var legendUrl = false;
			if (layerStyle === false) {
				legendUrl = wms.getLegendUrlByGuiLayerStyle(layerName,"default");	
			}
			else {
				legendUrl = wms.getLegendUrlByGuiLayerStyle(layerName,layerStyle);
			}
			
			var req = new Mapbender.Ajax.Request({ 
				url : "../plugins/mb_metadata_layerPreview.php",
				method: "saveLayerPreview",
				parameters : { 
					mapurl: url, 
					wmsId: wmsId,
					layerId: layerId, 
					legendUrl: legendUrl, 
					layerName: layerName
				},
				callback: function(result, success, message){
					alert("Preview saved.");
					if(status){
					}
					else{
					}
				}
			});
			req.send();
			
		});
	};
	
	this.init = function () {
		$saveLayerPreview.click(function () {
			that.save();
		
		}).mouseover(function () {
			if (options.src) { this.src = options.src.replace(/_off/, "_over"); }
		}).mouseout(function () {
			if (options.src) { this.src = options.src; }
		});
		
	};
	
	this.init();
};

$saveLayerPreview.mapbender(new SaveLayerPreviewApi());

