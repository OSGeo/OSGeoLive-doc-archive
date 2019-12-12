jQuery.fn.mapdependentSelect = function(o){
	return this.each(function(){
		var that = $(this);
		var options = o;
		options.url = options.url || "../html/mod_blank.html";
		var requestData = {BBOX:options.bbox};
		jQuery.post(
			options.url,
			requestData,
			function(data, status, xhr){
				$('option',that).remove();
				that.append('<option value="" selected="selected">...</option>');
				for(var i = 0; i < data.length; i++){
					that.append('<option value="'+ data[i].val +'">'+ data[i].show +'</option>');
				}
				// elementValue is set if we are working on an exitising feature
				var elementValue = that.attr('elementvalue');
				if(elementValue){
					$('option[value="'+elementValue +'"]',that).attr('selected','selected');
				}
			return true;
			},
			"json"
		);
	});
};
	
