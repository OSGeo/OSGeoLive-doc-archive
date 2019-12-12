
var $saveWmcPreview = $(this);

var SaveWmcPreviewApi = function () {
	var that = this;
	var wmcId;
	this.setWmc = function (wmc) {
		wmcId = wmc;
	};

	this.save = function () {
		var map = $("#mapframe1").mapbender();
		var mapUrls = [];
		for (var i = 0; i < map.wms.length; i++) {
			mapUrls[i] = map.wms[i].mapURL;
		}
		
		var req = new Mapbender.Ajax.Request({ 
			url : "../plugins/mb_metadata_wmcPreview.php",
			method: "saveWmcPreview",
			parameters : { 
				wmcId : wmcId,
				mapUrls : mapUrls
			},
			callback: function(result, success, message) {
				alert(message);
			}
		});
		req.send();
	};
	
	this.init = function () {
		$saveWmcPreview.click(function () {
			that.save();
		}).mouseover(function () {
			if (options.src) { this.src = options.src.replace(/_off/, "_over"); }
		}).mouseout(function () {
			if (options.src) { this.src = options.src; }
		});
		
	};
	
	this.init();
};

$saveWmcPreview.mapbender(new SaveWmcPreviewApi());

