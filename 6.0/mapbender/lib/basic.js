//
// custom jQuery selectors
//
$.expr[":"].maps = function (obj) {
	return ($(obj).data('isMap') === true);
};

$.expr[":"].mainMaps = function (obj) {
	return ($(obj).data('isMap') === true && $(obj).data('isOverview') !== true);
};

$.expr[":"].modules = function (obj) {
	return (typeof $(obj).mapbender() !== "undefined");
};

/**
 * Package: Mapbender
 *
 * Description:
 * This jQuery plugin grants access to the Mapbender API from a DOM element.
 * You can supply various arguments to achieve different things:
 * 
 * Passing no arguments will return the API object of the Mapbender element
 * associated with the DOM element, usage
 * 
 * > var api = $("#myelement").mapbender();
 * 
 * Passing an object serves as a setter: the object is the new API object 
 * associated with the DOM element, usage
 * 
 * > $("#myelement").mapbender(new CustomApiObject());
 * 
 * Passing a string serves as an attribute getter. It returns the value of
 * any public attribute of the API object, usage
 * 
 * > $("#myelement").mapbender("aSpecificAttribute")
 * 
 * Passing a function executes the function in the scope of the API object.
 * This is a shortcut to fetching the API object first, and then calling its
 * method. By this, you don't need to check whether the API object exists, 
 * if none exists, the function is simply not executed. Usage
 * 
 * > $("#myelement").mapbender(function () {
 * > 		// this refers to the API object!!
 * >		console.log(this.options);
 * > });
 * 
 * 
 * Files:
 *  - javascripts/core.php
 *  - lib/basic.js
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

$.fn.mapbender = function () {
	var id = this.attr("id");

	// Return the whole API object
	if (arguments.length === 0) {
		return Mapbender.modules[id];
	}
	// Add API functionality (parameter is API object)
	else if (arguments.length === 1 && typeof arguments[0] === "object") {
		var obj = arguments[0];
		Mapbender.modules[id] = $.extend(
			obj,
			Mapbender.modules[id]
		);
		return this.data("api", Mapbender.modules[id]);
		
	}
	// Get a value from an attribute
	else if (arguments.length === 1 && typeof arguments[0] === "string") {
		var module = this.data("api");

		if (typeof module === "undefined") {
			new Mb_exception("Module " + id + " is not present!");
			return null;
		}
		if (typeof module[arguments[0]] === "function") {
			module[arguments[0]]();
			return this;
		}
		return module[arguments[0]];
	}
	// Set an attribute
	else if (arguments.length === 2 && typeof arguments[0] === "string") {
		var module = this.data("api");

		if (typeof module === "undefined") {
			new Mb_exception("Module " + id + " is not present!");
			return null;
		}
		if (typeof module[arguments[0]] === "function") {
			if (typeof arguments[1] === "object") {
				module[arguments[0]].apply(arguments[1]);
			}
			else {
				module[arguments[0]].call(arguments[1]);
			}
			return this;
		}
		module[arguments[0]] = arguments[1];
		return this;
	}
	// Use API functionality
	else if (arguments.length === 1 && typeof arguments[0] === "function") {
		var closure = arguments[0];	
		
		return this.each(function () {
			var module = $(this).data("api");

			if (typeof module === "undefined") {
				new Mb_exception("Module " + id + " is not present!");
				return this;
			}
			return closure.call(module);
		});
	}
	else if (arguments.length > 2 && typeof arguments[0] === "string") {
		var module = this.data("api");

		if (typeof module === "undefined") {
			new Mb_exception("Module " + id + " is not present!");
			return null;
		}
		var closure = module[arguments[0]];
		if (typeof closure === "function") {
			var args = Array.prototype.slice.call(arguments);
			args.shift();
			closure.apply(module, args);
		}
		return this;		
	}
};

/**
 * Method: cookiesEnables
 * 
 * Description:
 * Checks whether cookies are enabled in the browser
 */

Mapbender.cookiesEnabled = function () {
    var dt = new Date();
    dt.setSeconds(dt.getSeconds() + 60);
    document.cookie = "cookietest=1; expires=" + dt.toGMTString();
    return document.cookie.indexOf("cookietest=") != -1;
};

/**
 * Method: cloneObject
 * 
 * Description:
 * Clone an object recursively. Make sure the object is not circular!
 * 
 * Parameters:
 * p      - an object
 */
Mapbender.cloneObject = function (p, c) {
	var d = c || {};
	for (var i in p) {
		if (typeof p[i] === 'object' && p[i] !== null) {
			d[i] = (p[i].constructor === Array) ? [] : {};
			Mapbender.cloneObject(p[i], d[i]);
		}
		else {
			d[i] = p[i];
		}
	}
	return d;
};

/**
 * Method: getConjunctionChar
 * 
 * Description:
 * returns the character necessary to append to a string 
 * in order to attach more GET parameters
 * 
 * Parameters:
 * url      - the online resource
 */
Mapbender.getConjunctionChar = function (url) {
	if (url.indexOf("?") > -1) { 
		var c = url.charAt(url.length - 1);
		if(c == "&" || c == "?") {
			return "";
		}
		return "&";
	}
	return "?";
};

Mapbender.phpjs = {
	"file_exists": function (url) {
	    // http://kevin.vanzonneveld.net
	    // +   original by: Enrique Gonzalez
	    // +      input by: Jani Hartikainen
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // %        note 1: This function uses XmlHttpRequest and cannot retrieve resource from different domain.
	    // %        note 1: Synchronous so may lock up browser, mainly here for study purposes. 
	    // *     example 1: file_exists('http://kevin.vanzonneveld.net/pj_test_supportfile_1.htm');
	    // *     returns 1: '123'
	    
	    var req = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	    if (!req) {throw new Error('XMLHttpRequest not supported');}
	      
	    // HEAD Results are usually shorter (faster) than GET
	    req.open('HEAD', url, false);
	    req.send(null);
	    if (req.status == 200){
	        return true;
	    }
	    
	    return false;
	}
};

Mapbender.sprintf = function () {
    // http://kevin.vanzonneveld.net
    // +   original by: Ash Searle (http://hexmen.com/blog/)
    // + namespaced by: Michael White (http://getsprink.com)
    // +    tweaked by: Jack
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Paulo Ricardo F. Santos
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: sprintf("%01.2f", 123.1);
    // *     returns 1: 123.10
    // *     example 2: sprintf("[%10s]", 'monkey');
    // *     returns 2: '[    monkey]'
    // *     example 3: sprintf("[%'#10s]", 'monkey');
    // *     returns 3: '[####monkey]'

    var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
    var a = arguments, i = 0, format = a[i++];

    // pad()
    var pad = function (str, len, chr, leftJustify) {
        if (!chr) {chr = ' ';}
        var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
        return leftJustify ? str + padding : padding + str;
    };

    // justify()
    var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
        var diff = minWidth - value.length;
        if (diff > 0) {
            if (leftJustify || !zeroPad) {
                value = pad(value, minWidth, customPadChar, leftJustify);
            } else {
                value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
            }
        }
        return value;
    };

    // formatBaseX()
    var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        prefix = prefix && number && {'2': '0b', '8': '0', '16': '0x'}[base] || '';
        value = prefix + pad(number.toString(base), precision || 0, '0', false);
        return justify(value, prefix, leftJustify, minWidth, zeroPad);
    };

    // formatString()
    var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
        if (precision != null) {
            value = value.slice(0, precision);
        }
        return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
    };

    // doFormat()
    var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
        var number;
        var prefix;
        var method;
        var textTransform;
        var value;

        if (substring == '%%') {return '%';}

        // parse flags
        var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false, customPadChar = ' ';
        var flagsl = flags.length;
        for (var j = 0; flags && j < flagsl; j++) {
            switch (flags.charAt(j)) {
                case ' ': positivePrefix = ' '; break;
                case '+': positivePrefix = '+'; break;
                case '-': leftJustify = true; break;
                case "'": customPadChar = flags.charAt(j+1); break;
                case '0': zeroPad = true; break;
                case '#': prefixBaseX = true; break;
            }
        }

        // parameters may be null, undefined, empty-string or real valued
        // we want to ignore null, undefined and empty-string values
        if (!minWidth) {
            minWidth = 0;
        } else if (minWidth == '*') {
            minWidth = +a[i++];
        } else if (minWidth.charAt(0) == '*') {
            minWidth = +a[minWidth.slice(1, -1)];
        } else {
            minWidth = +minWidth;
        }

        // Note: undocumented perl feature:
        if (minWidth < 0) {
            minWidth = -minWidth;
            leftJustify = true;
        }

        if (!isFinite(minWidth)) {
            throw new Error('sprintf: (minimum-)width must be finite');
        }

        if (!precision) {
            precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
        } else if (precision == '*') {
            precision = +a[i++];
        } else if (precision.charAt(0) == '*') {
            precision = +a[precision.slice(1, -1)];
        } else {
            precision = +precision;
        }

        // grab value using valueIndex if required?
        value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

        switch (type) {
            case 's': return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
            case 'c': return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
            case 'b': return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'o': return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'x': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'X': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
            case 'u': return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'i':
            case 'd':
                number = parseInt(+value, 10);
                prefix = number < 0 ? '-' : positivePrefix;
                value = prefix + pad(String(Math.abs(number)), precision, '0', false);
                return justify(value, prefix, leftJustify, minWidth, zeroPad);
            case 'e':
            case 'E':
            case 'f':
            case 'F':
            case 'g':
            case 'G':
                number = +value;
                prefix = number < 0 ? '-' : positivePrefix;
                method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
                textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
                value = prefix + Math.abs(number)[method](precision);
                return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
            default: return substring;
        }
    };

    return format.replace(regex, doFormat);
};

/**
 * dummy. Is only used so that xgettext finds translatable content in JS files.
 */
Mapbender._mb = function (text) {
	return text;
};


/**
 * form filling and serializing
 */
$.fn.easyform = function (obj) {
	if (typeof obj !== "string" && typeof obj !== "object") {
		return this;
	}
	var data = typeof obj === "object" && obj.data ? obj.data : {};
	var command = typeof obj === "string" ? obj : "";

	switch (command) {
		case "serialize":
			var f = $(this).serializeArray();
			var r = {};
			for (var i in f) {
				var n = r[f[i].name];
				if (n === undefined) {
					r[f[i].name] = $(f[i]).val();
				}
				else if (n.length && typeof n === "object") {
					r[f[i].name].push($(f[i]).val());
				}
				else {
					r[f[i].name] = [n, $(f[i]).val()];
				}
			}
			return r;
		case "fill":
			data = arguments.length > 1 && 
				typeof arguments[1] === "object" ? arguments[1] : {};
			break;
		case "reset":
			this.each(function () {
				this.reset();
			});
			break;
			
	}
	
	return this.each(function () {
		if (data) {
			for (var i in data) {
				if (!this[i]) {
					continue;
				}
				var $node = $(this[i]);
				if ($node.size() === 1) {
					if ($node.get(0).tagName.toUpperCase() === "SELECT") {
						
						$node.find("option").each(function () {
							var $opt = $(this);
							var value = data[i];
							if (!value || !value.length) {
								value = [value];
							}
							for (var j in value) {
								if ($opt.val() === value[j]) {
									$opt.attr("selected", "selected");
								}
							}
						});
					}
					else if ($node.get(0).tagName.toUpperCase() === "INPUT" && $node.attr("type") === "checkbox") {
						$node.attr("checked", data[i] ? true : false);
					}
					else {
						$node.val(data[i]);
					}
				}
			}
			return;
		}
		
	});
};
