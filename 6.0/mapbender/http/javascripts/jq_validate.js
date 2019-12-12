(function () {
	var originalMessages = $.extend({}, jQuery.validator.messages);

	Mapbender.events.localize.register(function () {

		// use english as fallback
		jQuery.extend(jQuery.validator.messages, originalMessages);

		// overwrite existing message with localized
		$.ajaxSetup.async = false;
		var url = "../extensions/jquery-validate/localization/" + 
			"messages_" + Mapbender.locale.substr(0, 2) + ".js";
		$.get(url , function (js) {
			if (js) {
				eval(js);
			}
		});		
		$.ajaxSetup.async = true;
		
	});
})();
