
var $metadataLayerPreview = $(this);
$metadataLayerPreview.css('border', 'white');

var MetadataLayerPreviewApi =  function (o){

	var that = this;
	options = o || {};
	options.map = options.map || "";
	options.buttons = options.buttons || [];

	this.wmsId = null;

	var changeEpsgAndZoomToExtent = function (layer, map) {
		layer.gui_layer_visible = 1;
		var len = layer.layer_epsg.length;
		if (len === 0) {
			// could not zoom to extent
			return;
		}
		for (var j = 0;  j < len; j++) {
			var currentEpsg = layer.layer_epsg[j];
			if (currentEpsg.epsg === map.epsg) {
				var newExtent = new Mapbender.Extent(
					currentEpsg.minx,
					currentEpsg.miny,
					currentEpsg.maxx,
					currentEpsg.maxy
				);
				map.calculateExtent(newExtent);
				return;
			}
		}
		// current SRS is not supported, switch to a supported SRS
		var newEpsg = layer.layer_epsg[0];
		var newExtent = new Mapbender.Extent(
			newEpsg.minx,
			newEpsg.miny,
			newEpsg.maxx,
			newEpsg.maxy
		);
		map.setSrs({
			srs: newEpsg.epsg,
			extent: newExtent
		});
	};

	// enable layer,. disabling all others
	this.layer = function(layer){
		var layername = layer.layer_name;
	
		if(layername === undefined){
			return 'currentlayername';
		}
		var map = $('#'+options.map).mapbender();
		var wms = map.wms[map.wms.length -1];
		for (var i in wms.objLayer){
			if(wms.objLayer[i].layer_name == layername){
				changeEpsgAndZoomToExtent(wms.objLayer[i], map);
			}
			else{
				wms.objLayer[i].gui_layer_visible = 0;
			}	
		}
		map.restateLayers(wms.wms_id);
        map.setMapRequest();
	};

	// set wms, throwing out all others
	this.wms = function(wmsid){
		if(wmsid === undefined){
			return 'currentwmsid';
		}	
		
		$('#' + options.map).each(function () {
			var map = $(this).mapbender();
			if (!map) {
				return;
			}
			var wms = map.wms;
			for(var i = wms.length -1 ; i > 0; i--){
				delete wms[i];
				wms.length--;
			}
			mod_addWMSById_ajax('', wmsid, {
				zoomToExtent: 1
			});
		});
	};

	this.init = function(obj){
	
		if (this.init.done !== true) {
			var $map = $('#' + options.map);
			$map.css('position','relative');
			$map.css('display','');
			$map.css('top','');
			$map.css('left','');
			$('img',$map).css({
				'height': $map.css('height'),
				'width': $map.css('width')
			});
	
			var $target = $('#map');
			$target.append($map);
	
			$(options.toolbarUpper).each(function () {
				$("#" + this).css({
					"position": "relative",
					"display":"",
					"top":"",
					"left":""
				}).appendTo($("#toolbar_upper"));
			});
			$(options.toolbarLower).each(function () {
				$("#" + this).css({
					"position": "relative",
					"display":"",
					"top":"",
					"left":""
				}).appendTo($("#toolbar_lower"));
			});
			this.init.done = true;
		}
		
		if(typeof parseInt(obj, 10) == 'number') {
			that.wms(obj);
		}
		
	};
};

$metadataLayerPreview.mapbender(new MetadataLayerPreviewApi(options));
