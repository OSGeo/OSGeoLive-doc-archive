var $wizard = $(this);

var WizardApi = function (o) {
	var that = this;

	this.events = {
		onBeforeClickLink: new Mapbender.Event(),
		onClickLink: new Mapbender.Event()
	};

	Mapbender.events.init.register(function () {
		if (o.$target.size() > 0) {
			$wizard.append(o.$target).wizard({
				fade: true,
				startWith: o.$target.eq(0),
				onClickLink: function ($targets) {
					var args = [{
							path: $targets
					}];
					for (var i = 1; i < arguments.length; i++) {
						args.push(arguments[i]);
					}
					that.events.onClickLink.trigger.apply(this, args);
				},
				onBeforeClickLink: function (evt, $link) {
					var proceed = that.events.onBeforeClickLink.trigger({
						evt: evt,
						$link: $link
					}, "AND");
					return proceed;
				}
			});
		}
	});
};

$wizard.mapbender(new WizardApi(options));
