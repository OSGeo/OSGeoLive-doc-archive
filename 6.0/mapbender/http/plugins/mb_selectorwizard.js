var $wizard = $(this);

var WizardApi = function (o) {
	var that = this;

	this.events = {
		onBeforeClickLink: new Mapbender.Event(),
		onClickLink: new Mapbender.Event(),
		onNavigate : new Mapbender.Event()
	};
	
	$select = $('select',$wizard);
	
	Mapbender.events.init.register(function () {
		if (o.$target.size() > 0) {
			$wizard.append(o.$target).wizard({
				fade: false,
				startWith: o.$target.eq(0),
				onClickLink: function ($targets) {
					that.events.onClickLink.trigger({
						path: $targets
					});
				},
				onBeforeClickLink: function (evt, $link) {
					var proceed = that.events.onBeforeClickLink.trigger({
						evt: evt,
						$link: $link
					}, "AND");
					return proceed;
				},
				onNavigate: function($target){
					$select.val($target.attr('id'));
				}
			});
		}
	});

	o.$target.each(function(){
		$option = $('<option></option>').attr('value',this.id).text($(this).attr('title'));
		$select.append($option);
	});
	$select.change(function(){
		$wizard.wizard('to',$('#' + $(this).val()));
	});
			
	//TODO: this allows a user to use a html <button> elemnt  to create spatial buttons
	// but the implementation needs work
	$('button.mb-ui-searchContainer-point', $wizard).each(function(e){
		$(this).replaceWith($('#mb_digitize_point'));
	});
	$('button.mb-ui-searchContainer-line', $wizard).each(function(e){
		$(this).replaceWith($('#mb_digitize_line'));
	});
	$('button.mb-ui-searchContainer-polygon', $wizard).each(function(e){
		$(this).replaceWith($('#mb_digitize_polygon'));
	});
	$('button.mb-ui-searchContainer-rectangle', $wizard).each(function(e){
		$(this).replaceWith($('#mb_digitize_rectangle'));
	});
	$('button.mb-ui-searchContainer-extent', $wizard).each(function(e){
		$(this).replaceWith($('#mb_digitize_extent'));
	});

	
};

$wizard.mapbender(new WizardApi(options));
