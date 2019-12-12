var $submit = $(this);

var MetadataSubmitApi = function () {
	var that = this;
	var formData = {};
	
	var serializeCallback = function (data) {
		if (data === null) {
			formData = null;
			return;
		}
		if (formData !== null) {
			formData = $.extend(formData, data);
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

		if (formData === null) {
			return;
		}

		// get metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_wmc_server.php",
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
