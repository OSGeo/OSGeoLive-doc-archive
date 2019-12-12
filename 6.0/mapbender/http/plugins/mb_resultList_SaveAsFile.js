var WFSSaveAsFile = function(data){
	var req = new parent.Mapbender.Ajax.Request({
		url: "../php/mod_echo.php",
		method:"createFile",
		parameters: {
			data: data.geoJSON
		},
		callback : function(result, success, message){
			if(success) {
				$('#resultdownloader').remove();
				var hiddenIframe = $(document.body).append('<iframe name="resultdownloader" id="resultdownloader"></iframe>');
				$('#resultdownloader').css(
					'display','none'
				).attr('src',result.url);
				window.frames["resultdownloader"].location.href = 
					"../php/mod_download.php?download=" + result.url;
			}
			else {
				alert("could not create file on server: "+message);

			}

		}
			
	});
	req.send();

}

Mapbender.modules[options.target].addGlobalButton({"title":"Suchergebnis speichern", "classes":"buttonMargin", "callback": WFSSaveAsFile});