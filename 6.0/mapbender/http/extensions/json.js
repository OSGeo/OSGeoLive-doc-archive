/*
    json.js
    2007-01-10

    This file adds these methods to JavaScript:

		toJSONString(obj)
        arrayToJSONString(obj)
        booleanToJSONString(obj)
        dateToJSONString(obj)
        numberToJSONString(obj)
        objectToJSONString(obj)
        stringToJSONString(obj)
            These methods produce a JSON text from a JavaScript value.
            It must not contain any cyclical references. Illegal values
            will be excluded.

            The default conversion for dates is to an ISO string. You can
            add a toJSONString method to any date object to get a different
            representation.

        parseJSON(string, filter)
            This method parses a JSON text to produce an object or
            array. It can throw a SyntaxError exception.

            The optional filter parameter is a function which can filter and
            transform the results. It receives each of the keys and values, and
            its return value is used instead of the original value. If it
            returns what it received, then structure is not modified. If it
            returns undefined then the member is deleted.

            Example:

            // Parse the text. If a key contains the string 'date' then
            // convert the value to a date.

            myData = text.parseJSON(function (key, value) {
                return key.indexOf('date') >= 0 ? new Date(value) : value;
            });

    It is expected that these methods will formally become part of the
    JavaScript Programming Language in the Fourth Edition of the
    ECMAScript standard in 2007.
*/
function arrayToJSONString(ao){
	var a = ['['], b, i, l = ao.length, v;
	
	function p(s) {
		if (b) {
			a.push(',');
		}
		a.push(s);
		b = true;
	}

	for (i = 0; i < l; i += 1) {
		v = ao[i];
		switch (typeof v) {
		case 'undefined':
		case 'function':
		case 'unknown':
			break;
		case 'object':
			if (v) {
				p(toJSONString(v));
			} else {
				p("null");
			}
			break;
		default:
			p(toJSONString(v));
		}
	}
	a.push(']');
	return a.join('');		
}
function boolToJSONString(bo) {
	return String(bo);
};

function dateToJSONString(dao) {
	function f(n) {
		return n < 10 ? '0' + n : n;
	}

	return '"' + dao.getFullYear() + '-' +
		f(dao.getMonth() + 1) + '-' +
		f(dao.getDate()) + 'T' +
		f(dao.getHours()) + ':' +
		f(dao.getMinutes()) + ':' +
		f(dao.getSeconds()) + '"';
};
   
function numberToJSONString(no) {
	return isFinite(no) ? String(no) : "null";
};

function objectToJSONString(ob) {
	
	var a = ['{'], b, i, v;

	function p(s) {
		if (b) {
			a.push(',');
		}
		a.push(toJSONString(i), ':', s);
		b = true;
	}

	for (i in ob) {
		if (ob.hasOwnProperty(i)) {
			v = ob[i];
			switch (typeof v) {
			case 'undefined':
			case 'function':
			case 'unknown':
				break;
			case 'object':
				if (v) {
					p(toJSONString(v));
				} else {
					p("null");
				}
				break;
			default:
				p(toJSONString(v));
			}
		}
	}
	a.push('}');
	return a.join('');
};

function stringToJSONString(so){
	var m = {
		'\b': '\\b',
		'\t': '\\t',
		'\n': '\\n',
		'\f': '\\f',
		'\r': '\\r',
		'"' : '\\"',
		'\\': '\\\\'
	};	
	if (/["\\\x00-\x1f]/.test(so)) {
		return '"' + so.replace(/([\x00-\x1f\\"])/g, function(a, b) {
			var c = m[b];
			if (c) {
				return c;
			}
			c = b.charCodeAt();
			return '\\u00' +
				Math.floor(c / 16).toString(16) +
				(c % 16).toString(16);
		}) + '"';
	}
	return '"' + so + '"';
}

function toJSONString(o){
	switch(typeof o){
	case 'undefined':
	case 'function':
	case 'unknown':
		break;
	case 'object':
		if (o.constructor == Array){
			return arrayToJSONString(o);
		}else if(o.constructor == Date){
			return dateToJSONString(o);
		}else{
			return objectToJSONString(o);
		}
	case 'number':
		return numberToJSONString(o);
	case 'string':
		return stringToJSONString(o);
	case 'boolean':
		return boolToJSONString(o);
	}
}

function parseJSON(so, filter){
	var m = {
		'\b': '\\b',
		'\t': '\\t',
		'\n': '\\n',
		'\f': '\\f',
		'\r': '\\r',
		'"' : '\\"',
		'\\': '\\\\'
	};	
	try {
		if (/^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/.
				test(so)) {
			var j = eval('(' + so + ')');
			if (typeof filter === 'function') {
				function walk(k, v) {
					if (v && typeof v === 'object') {
						for (var i in v) {
							if (v.hasOwnProperty(i)) {
								v[i] = walk(i, v[i]);
							}
						}
					}
					return filter(k, v);
				}
				return walk('', j);
			}
			return j;
		}
	} catch (e) {
	}
	throw new SyntaxError("parseJSON");        
}