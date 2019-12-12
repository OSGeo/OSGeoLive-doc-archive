/**
 * Package: Ajax
 * 
 * Description:
 * An AJAX wrapper for client server interaction via JSON RPC
 * 
 * Files:
 *  - lib/ajax.js
 *
 * Help:
 * <none>
 * 
 * Maintainer:
 * http://www.mapbender.org/Christoph_Baudson
 * 
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
Mapbender.Ajax = {
	/**
	 * Property: requestCount
	 * 
	 * Description:
	 * counts the number of requests that have been made
	 */
	requestCount: 0,
	Messages: {
		fatalError: "A fatal error occured.",
		idMismatchError: "The ID of the response is not equal to the ID of the request."
	}
};

/**
 * Package: Notification
 * 
 * Description:
 * An AJAX notification (it doesn't expect a reply from the server)
 * 
 * Files:
 *  - lib/ajax.js
 *
 * Help:
 * <none>
 * Maintainer:
 * http://www.mapbender.org/Christoph_Baudson
 * 
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
/**
 * Constructor: Mapbender.Ajax.Request
 * 
 * Parameters:
 * options.url		- (String) The URL or the server side script
 * options.type		- *[optional]* (String) "POST" (default) or "GET"
 * options.log		- *[optional]* (Boolean) logs the request 
 * 						as Mapbender.Notice  (default: false)
 * options.method	- *[optional]* (String) The name of the method that 
 * 						is called on the server side (default: "")
 * options.parameters	- *[optional]* (Object) arguments being passed to 
 * 							the method given above
 * <deprecated variable name>  - *[deprecated]* <type and description>
 */
Mapbender.Ajax.Notification = function (options) {
	var conjunctionCharacter = function (url) {
		if (url.indexOf("?") === -1) {
			return "?";
		}
		return "&";
	};

	if (typeof options === "object") {
		var url = (options.url) ? options.url : "";
		url += conjunctionCharacter(url) + Mapbender.sessionName + "=" + Mapbender.sessionId;
		var type = (options.type) ? options.type.toUpperCase() : "POST";
		var log = (options.log) ? options.log : false;
		var method = (options.method) ? options.method : "";
		var async = (typeof options.async === "boolean") ? options.async : true;
		var parameters = (options.parameters) ? options.parameters : {};
	}
	 
	this.send = function () {
		$.ajaxSetup({
			async: async,
			cache: false
		});
		switch (type.toUpperCase()) {
			case "POST" :
				$.post(url, getParameters(), function () {
				});
				$.ajaxSetup({
					async: true
				});
				break;
			case "GET" :
				$.get(url, getParameters(), function () {
				});
				$.ajaxSetup({
					async: true
				});
				break;
			default:
				new Mapbender.Exception("Invalid type (" + type + 
					") in Mapbender.Ajax.Notification.");
				return false;
		}
		return true;
	};

	var getParameters = function () {
		return {
			"method": method,
			"params": $.toJSON(parameters),
			"id": null
//			,
//			"sessionName": Mapbender.sessionName,
//			"sessionId": Mapbender.sessionId
		};
	};
};

/**
 * Package: Request
 * 
 * Description:
 * An AJAX request (it expects a reply from the server)
 * 
 * Files:
 *  - lib/ajax.js
 *
 * Help:
 * <none>
 * 
 * Maintainer:
 * http://www.mapbender.org/Christoph_Baudson
 * 
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
/**
 * Constructor: Mapbender.Ajax.Request
 * 
 * Parameters:
 * options.url			- (String) The URL to the server side script
 * options.type			- *[optional]* (String) "POST" (default) or "GET"
 * options.log			- *[optional]* (Boolean) logs the request 
 * 							as Mapbender.Notice  (default: false)
 * options.method		- *[optional]* (String) The name of the method that 
 * 							is called on the server side (default: "")
 * options.parameters	- *[optional]* (Object) arguments being passed to 
 * 							the method given above
 * options.callback			- (Function) Will be called after the request
 * 							is finished
 * options.scope		- *[optional]* (Object) the callback will 
 * 							be executed within the scope of this object
 */
Mapbender.Ajax.Request = function (options) {
	var conjunctionCharacter = function (url) {
		if (url.indexOf("?") === -1) {
			return "?";
		}
		return "&";
	};

	if (typeof options === "object") {
		var url = (options.url) ? options.url : "";
		url += conjunctionCharacter(url) + Mapbender.sessionName + "=" + Mapbender.sessionId;

		var type = (options.type) ? options.type.toUpperCase() : "POST";
		var log = (options.log) ? options.log : false;
		var method = (options.method) ? options.method : "";
		var parameters = (options.parameters) ? options.parameters : {};
		var callback = (options.callback) ? options.callback : null;
		var scope = (options.scope) ? options.scope : null;
		var async = (typeof options.async === "boolean") ? options.async : true;
	}
	else {
		new Mapbender.Exception("No options specified in " + 
			"Mapbender.Ajax.Notification.");
		return;
	}

	// A unique identifier for this Ajax request
	var id = ++Mapbender.Ajax.requestCount;
	
	// The result object, coming from the server
	var result = {};

	// A message coming from the server
	var message = "";

	// Did the request succeed?
	var success = false;
	
	// Did an internal error occur
	var internalError = false;
	
	// Did the session expire
	var sessionExpired = false;
	
	// error code of the reply
	var errorCode;
	
	// Checks if the response is valid.
	var receive = function (json, status) {
		if (!json) {
			message = Mapbender.Ajax.Messages.fatalError;
			new Mapbender.Warning(message);
			internalError = true;
			return;
		}
		var resultObj = typeof json === "object" ? json : $.parseJSON(json);
		// some severe error has occured
		if (typeof(resultObj) != "object" || status != "success") {
			message = Mapbender.Ajax.Messages.fatalError;
			new Mapbender.Warning(message);
			internalError = true;
			return;
		}
		if (resultObj.error !== null) {
			// the ajax request reports failure  
			if (resultObj.error.message) {
				message = resultObj.error.message;
			}
			else {
				message = Mapbender.Ajax.Messages.fatalError;
			}
			if (resultObj.error.code == -1) {
//				internalError = true;
			}
			else if (resultObj.error.code == -2) {
				sessionExpired = true;
			}
			new Mapbender.Warning(message);
			errorCode = resultObj.error.code;
			return;
		}

		// the ajax request reports success
		if (resultObj.result && typeof(resultObj.result) == "object" &&
			resultObj.result.data && typeof(resultObj.result.data) == "object") {

			if (id != resultObj.id) {
				message = Mapbender.Ajax.Messages.idMismatchError;
				new Mapbender.Warning(message);
				internalError = true;
				return;
			}

			success = true;

			if (resultObj.result.message) {
				message = resultObj.result.message;
				status = resultObj.result.message;
			}
			result = resultObj.result.data;
		}
		return;
	};

	/**
	 * Method: send
	 * 
	 * Description:
	 * Sends the request to the server side
	 */
 	this.send = function () {
		internalError = false;
		sessionExpired = false;
		success = false;
		
		var callbackWrapper = function (json, status) {
			receive(json, status);
			if (sessionExpired) {
				alert(message);
				window.location.reload();
				return; 
			}
			if (!internalError) {
				new Mapbender.Notice("REQUEST #" + id + ": " + url + "\n\n" + 
					"RESULT: " + $.toJSON(result) + "\n\n" + 
					"SUCCESS: " + success + "\n\n" + 
					"MESSAGE: " + message);
				
				if (typeof callback === null) {
					new Mapbender.Warning("No callback function in " + 
						"Mapbender.Ajax.Request.");
					return;
				}
				if (scope !== null) {
					callback.apply(scope, [result, success, message, errorCode]);
				}
				else {
					callback(result, success, message, errorCode);
				}
			}
		};
		$.ajaxSetup({
			async: async
		});
		switch (type) {
			case "POST" :
				$.post(url, getParameters(), callbackWrapper);
				$.ajaxSetup({
					async: true
				});
				break;
			case "GET" :
				$.get(url, getParameters(), callbackWrapper);
				$.ajaxSetup({
					async: true
				});
				break;
			default:
				return false;
		}
		return true;
	};

	var getParameters = function () {
		return {
			"method": method,
			"params": $.toJSON(parameters),
			"id": id
//			,
//			"sessionName": Mapbender.sessionName,
//			"sessionId": Mapbender.sessionId
		};
	};
    return this;
};
Mapbender.Ajax.Request.prototype = new Mapbender.Ajax.Notification();
