/**
 * Package: Event
 * 
 * Description:
 *  An event. What happens, when the event occurs, depends on which functions have been
 * registered with the event. 
 * 
 * usage:
 * 
 * > // create a new Event
 * > var eventAfterMapRequest = new MapbenderEvent(); 
 * >
 * > // register a function with that event
 * > eventAfterMapRequest.register(function () {
 * > 	...
 * > })
 * >
 * > // trigger the event
 * > eventAfterMapRequest.trigger();
 * 
 * Files:
 *  - lib/event.js
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
 * Constructor: Mapbender.Event
 */
var MapbenderEvent = Mapbender.Event = function () {
	
	// public methods
	/**
	 * Method: register
	 * 
	 * Description:
	 * A function that needs to be executed, when the event occurs, has to be 
	 * registered via this function.
	 * 
	 * Parameters:
	 * closure - a function (or a string for backwards compatibility) which
	 * 			is called (evaluated) when the trigger method is called. 
	 * scope - *[optional]* if given, the function is not simply executed, but
	 * 			called with JavaScript's call function
	 * > func.call(scope, argumentObj)
	 */
	this.register = function(aFunction, scope) {

		var mpbnFunction = new MapbenderFunction(aFunction, scope);
		functionArray.push(mpbnFunction);
		propertiesObj.count = functionArray.length;
	};

	/**
	 * Method: unregister
	 * 
	 * Description:
	 * Exclude a previously registered function from the event permanently
	 * 
	 * Parameters:
	 * closure - a function (or a string for backwards compatibility)
	 */
	this.unregister = function(aFunction) {
		for (var i = 0, len = functionArray.length; i < len; i++) {
			if (functionArray[i].getFunction() === aFunction) {
				for (var j = i + 1; j < len; j++) {
					functionArray[j-1] = functionArray[j];
				}
				delete functionArray[len - 1];
				len = len - 1;
			}
		}
		functionArray.length = len;
		propertiesObj.count = functionArray.length;
	};

	/**
	 * Method: isRegistered
	 * 
	 * Description:
	 * Checks if a function is already registered with this event. This
	 * can be used to avoid duplicate registers.
	 * 
	 * Parameters:
	 * closure - a function (or a string for backwards compatibility)
	 */
	this.isRegistered = function (aFunction) {
		for (var i = 0, len = functionArray.length; i < len; i++) {
			if (functionArray[i].getFunction() === aFunction) {
				return true;
			}
		}
		return false;
	};
	
	/**
	 * Method: trigger
	 * 
	 * Description:
	 * This function triggers the event. 
	 * 
	 * Parameters:
	 * properties - an object containing the arguments to be passed to 
	 * 			the registered functions
	 * operator - *[optional]* a string that specifies how the return 
	 * 			values of the individual registered functions shall be 
	 * 			combined. Available operators are "AND", "OR" and "CAT"
	 * 			(string concatenation). The default return value is the 
	 * 			return value of the last registered function.
	 */
	this.trigger = function(properties, booleanOperator) {
		if (!(functionArray.length > 0)) {
			return true;
		}
		//
		// check arguments
		//
		// properties
		if (typeof(properties) != "object") {
			// maybe properties is missing, and so 
			// properties represents booleanOperator
			if (typeof(booleanOperator) == "undefined") {
				booleanOperator = properties;
				properties = undefined;
			}
			else {
//				var e = new Mb_exception("MapbenderEvent.trigger: invalid properties: %s", properties);
			}
		}		

		// booleanOperator
		if (typeof(booleanOperator) == "string") {
			if (booleanOperator != "AND" && booleanOperator != "OR") {
//				var e = new Mb_exception("MapbenderEvent.trigger: invalid booleanOperator: %s", booleanOperator);
			}
		}		
		else if (typeof(booleanOperator) != "undefined") {
//			var e = new Mb_exception("MapbenderEvent.trigger: invalid booleanOperator, must be a string, but is %s", typeof(booleanOperator));
		}
		
		var result;

		// the optional boolean operator allows to combine the return values of the functions
		// into a single result value.
		switch (booleanOperator) {
			case "AND":
				result = true;
				break;
			case "OR":
				result = false;
				break;
			case "CAT":
				result = "";
				break;
			default:
				result = true;
				break;
		}

		if (log) {
			var e = new Mb_notice("functions (after sort): " + functionArray.join(","));
		}

		for (var i = 0; i < functionArray.length; i++) {
			// executes the function at position i
			// and determines the return value based on the (optional) boolean operator
			switch (booleanOperator) {
				case "AND":
					result = result && functionArray[i].execute(properties);
					break;
				case "OR":
					result = result || functionArray[i].execute(properties);
					break;
				case "CAT":
					result += functionArray[i].execute(properties);
					break;
				default:
					result = functionArray[i].execute(properties);
					break;
			}
		}
		return result;
	};	
	
	this.getProperties = function () {
		return propertiesObj;
	};

	// private
	/**
	 * these functions will be executed once the event is triggered
	 */
	var functionArray = [];
	
	var propertiesObj = {};
	propertiesObj.count = 0;
	var log = false;
};

/**
 * A MapbenderFunction is a function with a priority.
 */
var MapbenderFunction = function (aFunction, aScope) {
	
	// public
	/**
	 * Returns the function itself
	 */
	this.getFunction = function () {
		return func;
	};
	
	/**
	 * Executes the function
	 */
	this.execute = function (argumentObj) {
		if (typeof(func) == "function" || typeof(func) == "object") {
			if (scope) {
				return func.call(scope, argumentObj);
			}
			return func(argumentObj);
		}

		// this branch is for backwards compatibility with the 
		// pre-2.5 event system that is based on strings.
		else {
			var argumentNames = [];
			var argumentValues = [];
			for (var i in argumentObj) {
				if (typeof(argumentObj[i]) == "number" || typeof(argumentObj[i]) == "boolean") {
					argumentNames.push(i);
					argumentValues.push(argumentObj[i]);
				}
				else if (typeof(argumentObj[i]) == "string") {
					argumentNames.push(i);
					argumentValues.push("'" + argumentObj[i] + "'");
				}
			}
			var str = "";
			str += "(function (" + argumentNames.join(", ") + ") {";
			str += "return " + aFunction;
			str += "}";
			str += "(" + argumentValues.join(", ") + "));";
			var returnValue = eval(str);
			return returnValue;
		}	
	};
	
	// private
	var func = aFunction;
	var scope = aScope;
};
