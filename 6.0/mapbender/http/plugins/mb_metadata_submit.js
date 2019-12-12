var $submit = $(this);

var MetadataSubmitApi = function () {
	var that = this;
	var formData = {};
	
	var serializeCallback = function (data) {
		if (data === null) {
			// if data is null, the form didn't validate!
			// formData is set to null, which will prevent server interaction
			formData = null;
			return;
		}
		if (formData !== null) {
			if (formData.wms && data.wms) {
				formDatawms = $.extend(formData.wms, data.wms);
			}
			else if (formData.layer && data.layer) {
				formData.layer = $.extend(formData.layer, data.layer);
			}
			else {
				formData = $.extend(formData, data);
			}
		}
	};
	
	this.enable = function () {
		$submit.find("input[type='submit']").removeAttr("disabled");
	};
	
	this.submit = function () {
		formData = {};
		this.events.submit.trigger({
			callback: serializeCallback
		});

		// The form didn't validate
		if (formData === null) {
			alert("Please complete or correct the data in the form.");
			return;
		}

		// get metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "save",
			parameters: {
				"data": formData
			},
			callback: function (obj, result, message) {
				if (!result) {
					$("<div></div>").text(!message ? "An error occured." : message).dialog({
						modal: true
					});
					return;
				}
				$("<div></div>").text(message).dialog({
					modal: true
				});

			}
		});
		req.send();			
	};
	
	this.events = {
		submit: new Mapbender.Event()
	};
	
	var init = function () {
		$submit.find("input[type='submit']").bind("click", function () {
			that.submit();
		});
	};

	init();
};

$submit.mapbender(new MetadataSubmitApi());
