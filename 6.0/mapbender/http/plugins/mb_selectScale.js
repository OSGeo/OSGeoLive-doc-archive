var $selectScale = $(this);

var SelectScaleApi = function () {
	var that = this;
	
	this.set = function (scale) {
        // IE call this with empty scale for some convoluted reason
        if(!scale){ return; }
        options.$target.mapbender().repaintScale(null,null,scale);
	};

	var init = function () {
		$selectScale.change(function () {
			that.set(this.value);
		});
		
		Mapbender.events.init.register(function () {
			options.$target.mapbender(function () {
				var map = this;
				map.events.afterMapRequest.register(function () {
					var scale = map.getScale();
					$selectScale.children("option").eq(0)
						.text("1 : " + scale)
						.attr("selected", "selected");
				});
			});
		});
	};
	init();
};

$selectScale.mapbender(new SelectScaleApi());
