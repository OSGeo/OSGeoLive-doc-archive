var $pan = $(this);

var PanApi = function (o) {
	var that = this;
	Mapbender.events.init.register(function () {
		options.$target.pan();
	});
};

$pan.mapbender(new PanApi(options));
