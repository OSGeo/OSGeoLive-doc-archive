(function ($) {
	
	//An abstract class, logs JavaScript events like errors, warnings etc.
	var Mb_log = function () {
		var that = this;
		
		var levelArray = global_log_levels.split(",");
		var log_level = global_mb_log_level;
		var log_js = global_mb_log_js;
		
		var indexOfLevel = function(aLevel) {
			for (var i = 0; i < levelArray.length; i++) {
				if (aLevel == levelArray[i]) {
					return i;
				}
			}
			return false;
		};
		var isValidLevel = function(aLevel) {
			var isNotOff = typeof(log_js) != 'undefined' && log_js != "off";
			var levelIndex = indexOfLevel(aLevel);
			var isAppropriate = (typeof(levelIndex)=='number' && levelIndex <= indexOfLevel(log_level));
			return (isNotOff && isAppropriate);
		};
		this.throwException = function (message, level) {
			if (isValidLevel(level)) {
				if (log_js == "on") {
					try {
						mb_ajax_post('../php/mb_js_exception.php', {level:level,text:message});
					}
					catch(e) {
						//alert(e + ": " + message);
					}
				}
				else if (log_js == "alert") {
					alert(message);
				}
				else if (log_js == "console" && 
					typeof window.console !== "undefined") {
						
					if ($.browser.msie) {
						try {
							mb_ajax_post('../php/mb_js_exception.php', {
								level:level,
								text:message
							});
						}
						catch(e) {
							//alert(e + ": " + message);
						}
						
					}
					else {
						if (level == "warning") {
							console.warn("%s", message);
						}
						else if (level == "error") {
							console.error("%s", message);
						}
						else {
							console.log("%s", message);
						}
					}
				}
			}
		};
	};	
	
	/**
	 * Class: Exception
	 * 
	 * Description:
	 * Logs an exception in the log file, in a console window or as an alert
	 * 
	 * Files:
	 *  - lib/exception.js
	 *
	 * Help:
	 * <none>
	 * 
	 * License:
	 * Copyright (c) 2009, Open Source Geospatial Foundation
	 * This program is dual licensed under the GNU General Public License 
	 * and Simplified BSD license.  
	 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
	 */
	/**
	 * Constructor: Exception
	 * 
	 * Parameters:
	 * message	- A message that is being logged
	 */
	Mapbender.Exception = function (message) {
		var level = "error";
		this.throwException(message, level);
	};
	
	Mapbender.Exception.prototype = new Mb_log();
	
	/**
	 * Class: Warning
	 * 
	 * Description:
	 * Logs an warning in the log file, in a console window or as an alert
	 * 
	 * Files:
	 *  - lib/exception.js
	 *
	 * Help:
	 * <none>
	 * 
	 * License:
	 * Copyright (c) 2009, Open Source Geospatial Foundation
	 * This program is dual licensed under the GNU General Public License 
	 * and Simplified BSD license.  
	 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
	 */
	/**
	 * Constructor: Warning
	 * 
	 * Parameters:
	 * message	- A message that is being logged
	 */
	Mapbender.Warning = function (message) {
		var level = "warning";
		this.throwException(message, level);
	};
	
	Mapbender.Warning.prototype = new Mb_log();
	 
	/**
	 * Package: Notice
	 * 
	 * Description:
	 * Logs a notice in the log file, in a console window or as an alert
	 * 
	 * Files:
	 *  - lib/exception.js
	 *
	 * Help:
	 * <none>
	 * 
	 * License:
	 * Copyright (c) 2009, Open Source Geospatial Foundation
	 * This program is dual licensed under the GNU General Public License 
	 * and Simplified BSD license.  
	 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
	 */
	/**
	 * Constructor: Notice
	 * 
	 * Parameters:
	 * message	- A message that is being logged
	 */
	Mapbender.Notice = function (message) {
		var level = "notice";
		this.throwException(message, level);
	};
	
	Mapbender.Notice.prototype = new Mb_log();
	
	$.error = function (msg) {
		new Mapbender.Exception(msg);
	};

})(jQuery);
