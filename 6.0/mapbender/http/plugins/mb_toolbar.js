var $toolbar = $(this);

var ToolbarApi = function (o) {
	var that = this;
	
	var $list = $("<ul />").appendTo($toolbar);
	o.$target.each(function () {
		if (this === undefined) {
			return;
		}
		$list.append($(this).wrap("<li />").parent());
	});
};

$toolbar.mapbender(new ToolbarApi(options));
