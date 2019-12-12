
options.$target.each(function () {
	var map = $(this).mapbender();
	if (map && map.zoomToExtent) {
		var coordinates = '<?php echo Mapbender::session()->get("mb_myBBOX") ?>';
		var c = coordinates.split(",");
		if (c.length === 4) {
			var b =	new OpenLayers.Bounds();
			b.extend(new OpenLayers.LonLat(
				parseFloat(c[0], 10),
				parseFloat(c[1], 10)
			));
			b.extend(new OpenLayers.LonLat(
				parseFloat(c[2], 10),
				parseFloat(c[3], 10)
			));
			map.mapbenderEvents.mapReady.register(function () {
				map.zoomToExtent(b);
			});
		}
	}			
});
