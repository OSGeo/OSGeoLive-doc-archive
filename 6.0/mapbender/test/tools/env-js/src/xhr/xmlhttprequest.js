
/**
 * 
 * @class XMLHttpRequest
 * @author Originally implemented by Yehuda Katz
 * 
 */

// this implementation can be used without requiring a DOMParser
// assuming you dont try to use it to get xml/html documents
var domparser;

XMLHttpRequest = function(){
	this.headers = {};
	this.responseHeaders = {};
    this.aborted = false;//non-standard
};

// it would be nice if these were part of the standard but
// they are not.
XMLHttpRequest.UNSENT = 0;
XMLHttpRequest.OPEN = 0;
XMLHttpRequest.HEADERS_RECEIVED = 0;
XMLHttpRequest.LOADING = 0;
XMLHttpRequest.DONE = 4;

XMLHttpRequest.prototype = {
	open: function(method, url, async, user, password){ 
		this.readyState = 1;
		this.async = (async === false)?false:true;
		this.method = method || "GET";
		this.url = Envjs.uri(url);
		this.onreadystatechange();
	},
	setRequestHeader: function(header, value){
		this.headers[header] = value;
	},
	send: function(data, parsedoc/*non-standard*/){
		var _this = this;
        parsedoc = !!parsedoc;
		function makeRequest(){
            Envjs.connection(_this, function(){
                if (!_this.aborted){
                    var doc = null,
                        domparser;
                    // try to parse the document if we havent explicitly set a 
                    // flag saying not to and if we can assure the text at least
                    // starts with valid xml
                    if ( parsedoc && _this.responseText.match(/^\s*</) ) {
                        domparser = domparser||new DOMParser();
                        try {
                            //Envjs.debug("parsing response text into xml document");
                            doc = domparser.parseFromString(_this.responseText+"");
                        } catch(e) {
                            //Envjs.error('response XML does not appear to be well formed xml', e);
                            console.log('parseerror \n%s', e);
                            doc = document.implementation.createDocument('','error',null);
                            doc.appendChild(doc.createTextNode(e+''));
                        } 
                    }else{
                        //Envjs.warn('response XML does not appear to be xml');
                    }
                    _this.__defineGetter__("responseXML", function(){
                        return doc;
                    });
                }
			}, data);

            if (!_this.aborted){
                _this.onreadystatechange();
            }
		};

		if (this.async){
		    //Envjs.debug("XHR sending asynch;");
            //TODO: what we really need to do here is rejoin the 
            //      current thread and call onreadystatechange via
            //      setTimeout so the callback is essentially applied
            //      at the end of the current callstack
			Envjs.runAsync(makeRequest);
		}else{
		    //Envjs.debug("XHR sending synch;");
			makeRequest();
		}
	},
	abort: function(){
        this.aborted = true;
	},
	onreadystatechange: function(){
		//Instance specific
	},
	getResponseHeader: function(header){
      //$debug('GETTING RESPONSE HEADER '+header);
	  var rHeader, returnedHeaders;
		if (this.readyState < 3){
			throw new Error("INVALID_STATE_ERR");
		} else {
			returnedHeaders = [];
			for (rHeader in this.responseHeaders) {
				if (rHeader.match(new RegExp(header, "i")))
					returnedHeaders.push(this.responseHeaders[rHeader]);
			}
            
			if (returnedHeaders.length){ 
                //$debug('GOT RESPONSE HEADER '+returnedHeaders.join(", "));
                return returnedHeaders.join(", "); 
            }
		}
        return null;
	},
	getAllResponseHeaders: function(){
	  var header, returnedHeaders = [];
		if (this.readyState < 3){
			throw new Error("INVALID_STATE_ERR");
		} else {
			for (header in this.responseHeaders){
				returnedHeaders.push( header + ": " + this.responseHeaders[header] );
			}
		}return returnedHeaders.join("\r\n");
	},
	async: true,
	readyState: 0,
	responseText: "",
	status: 0,
    statusText: ""
};
