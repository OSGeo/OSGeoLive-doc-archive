$.fn.upload = function (args) {
	if (typeof $.fn.upload.initialized === "undefined") {
		$.fn.upload.initialized = [];
	}
	return this.each(function () {
		if (this.id === "upload") {
			return;
		}

		var options = args || {};
		var $this = $(this);
		var that = this;
		var id = this.id;

		var exists = false;
		$($.fn.upload.initialized).each(function () {
			if (that === this) {
				exists = true;
			}
		});
		if (exists) {
			return;
		}
		
		$.fn.upload.initialized.push(this);

		var time = 0;
		var timeout = options.timeout || 7000;
		var timeinterval = options.interval || 200;
		// we want the default to be true, and we want the user to be able to write {displaySubmit: false}
		var displaySubmit = options.displaySubmit === undefined ? true : !!options.displaySubmit;
		var url = options.url || "../plugins/jq_upload.php";
		var width = options.width || 30;

		var startUpload = function () {
			var t = setInterval(function () {
				var returnValue = window.frames[id + "_target"].id;
				time += timeinterval;

				if (typeof returnValue !== "undefined") {
					clearInterval(t);
					time = 0;
					
					var returnValueArray = returnValue.split("___");
					var filename = returnValueArray[returnValueArray.length - 3];
					var origFilename = window.frames[id+"_target"].filename;
					var msg = "";
					var success = false;
					if (returnValue.match(/_finished/)) {
						window.frames[id + "_target"].id = undefined;
						if (typeof options.callback === "function") {
							var msgArray = returnValue.split("___");
							msg = msgArray.pop();
							options.callback({filename:filename,origFilename: origFilename}, true, msg);						
						}
					}
					else if (returnValue.match(/_cancelled/)) {
						window.frames[id + "_target"].id = undefined;
						var msgArray = returnValue.split("___");
						msg = msgArray.pop();
						if (typeof options.callback === "function") {
							options.callback(null, false, msg);						
						}
					}
					$("#" + id + "_submit").removeAttr("disabled");
					return;
				} 
				if (time >= timeout) {
					clearInterval(t);
					time = 0;

					msg = "File upload failed, timeout reached (" + 
						timeout + " ms)";
					new parent.window.opener.parent.Mb_exception(msg);		

					if (typeof options.callback === "function") {
						options.callback(null, false, msg);						
					}
					$("#" + id + "_submit").removeAttr("disabled");
					return;
				}
			}, timeinterval);
		};

		var $form = $(
			"<form style='margin:0px' action='"+ url  +"?id=" + id + "' method='post' " + 
			"enctype='multipart/form-data' target='" + id + "_target'>" + 
			"</form>"
		).submit(function () {
			var filename = $("input[name=myfile]", $this).val();
			if (filename !== "") {
				$("#" + id + "_submit").attr("disabled", "disabled");
				startUpload();
			}
			return true;
		});
		$form.append(
			"<span id='" + id + "_form' align='center'>" + 
				"<input name='myfile' type='file' size='"+width+"' />" + 

				(displaySubmit ? "<input id='" + id + "_submit' type='submit' value='Upload' />": "") + 

			"</span>"
		);
		var iframeStr = "<iframe name='" + id + "_target' id='" + id + "_target' src='../html/mod_blank.html' " + 
			"style='width:0;height:0;border:0px solid #fff;'></iframe>";

		$("body").eq(0).append(iframeStr);
		$this.append($form);
	});
};
