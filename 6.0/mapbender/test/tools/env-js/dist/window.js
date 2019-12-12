
/**
 * @todo: document
 */
var Window,
    Screen,
    History,
    Navigator,
    __this__ = this;


/*
 * Envjs window.1.2.0.0 
 * Pure JavaScript Browser Environment
 * By John Resig <http://ejohn.org/> and the Envjs Team
 * Copyright 2008-2010 John Resig, under the MIT License
 */

(function(){





/**
 * @author john resig
 */
// Helper method for extending one object with another.  
function __extend__(a,b) {
    for ( var i in b ) {
        var g = b.__lookupGetter__(i), s = b.__lookupSetter__(i);
        if ( g || s ) {
            if ( g ) a.__defineGetter__(i, g);
            if ( s ) a.__defineSetter__(i, s);
        } else
            a[i] = b[i];
    } return a;
};
/**
 * @todo: document
 */

__extend__(HTMLFrameElement.prototype,{

    get contentDocument(){
        return this.contentWindow?
            this.contentWindow.document:
            null;
    },
    set src(value){
        var event;
        this.setAttribute('src', value);
        if (this.parentNode && value && value.length > 0){
            //console.log('loading frame %s', value);
            Envjs.loadFrame(this, Envjs.uri(value));
            
            //console.log('event frame load %s', value);
            event = this.ownerDocument.createEvent('HTMLEvents');
            event.initEvent("load", false, false);
            this.dispatchEvent( event, false );
        }
    }
    
});

/*
*	history.js
*
*/

History = function(owner){
	var $current = 0,
        $history = [null],
        $owner = owner;
	
    return {
		get length(){ 
            return $history.length;
        },
		back : function(count){
			if(count){
				go(-count);
			}else{
                go(-1);
            }
		},
        get current(){
            return this.item($current);
        },
        get previous(){
            return this.item($current-1);
        },
		forward : function(count){
			if(count){
				go(count);
			}else{go(1);}
		},
		go : function(target){
			if(typeof target == "number"){
				target = $current + target;
				if(target > -1 && target < $history.length){
					if($history[target].type == "hash"){
                        if($owner.location){
						    $owner.location.hash = $history[target].value;
                        }
					}else{
                        if($owner.location){
						    $owner.location = $history[target].value;
                        }
					}
					$current = target;
				}
			}else{
				//TODO: walk through the history and find the 'best match'?
			}
		},
        item: function(index){
            if(index < history.length)
                return $history[index];
            else
                return null;
        },
        
        add: function(newLocation, type){
            //not a standard interface, we expose it to simplify 
            //history state modifications
            if(newLocation !== $history[$current]){
                $history.slice(0, $current);
                $history.push({
                    type: type||"href",
                    value: value
                });
            }
        }
	};
};


	

/*
 *	navigator.js
 *  Browser Navigator    
 */
Navigator = function(){

	return {
		get appCodeName(){
			return Envjs.appCodeName;
		},
		get appName(){
			return Envjs.appName;
		},
		get appVersion(){
			return Envjs.version +" ("+ 
			    this.platform +"; "+
			    "U; "+//?
			    Envjs.os_name+" "+Envjs.os_arch+" "+Envjs.os_version+"; "+
			    Envjs.lang+"; "+
			    "rv:"+Envjs.revision+
			  ")";
		},
		get cookieEnabled(){
			return true;
		},
		get mimeTypes(){
			return [];
		},
		get platform(){
			return Envjs.platform;
		},
		get plugins(){
			return [];
		},
		get userAgent(){
			return this.appCodeName + "/" + this.appVersion + " " + this.appName;
		},
		javaEnabled : function(){
			return Envjs.javaEnabled;	
		}
	};
};


/**
 * Screen
 * @param {Object} __window__
 */

Screen = function(__window__){

    var $availHeight  = 600,
        $availWidth   = 800,
        $colorDepth   = 16,
        $pixelDepth   = 24,
        $height       = 600,
        $width        = 800;
        $top          = 0;
        $left         = 0;
        $availTop     = 0;
        $availLeft    = 0;
        
    __extend__( __window__, {
        moveBy : function(dx,dy){
            //TODO - modify $locals to reflect change
        },
        moveTo : function(x,y) {
            //TODO - modify $locals to reflect change
        },
        /*print : function(){
            //TODO - good global to modify to ensure print is not misused
        };*/
        resizeBy : function(dw, dh){
            __window__resizeTo($width + dw, $height + dh);
        },
        resizeTo : function(width, height){
            $width = (width <= $availWidth) ? width : $availWidth;
            $height = (height <= $availHeight) ? height : $availHeight;
        },
        scroll : function(x,y){
            //TODO - modify $locals to reflect change
        },
        scrollBy : function(dx, dy){
            //TODO - modify $locals to reflect change
        },
        scrollTo : function(x,y){
            //TODO - modify $locals to reflect change
        }
    });   
    
    return {
        get top(){
            return $top;
        },
        get left(){
            return $left;
        },
        get availTop(){
            return $availTop;
        },
        get availLeft(){
            return $availleft;
        },
        get availHeight(){
            return $availHeight;
        },
        get availWidth(){
            return $availWidth;
        },
        get colorDepth(){
            return $colorDepth;
        },
        get pixelDepth(){
            return $pixelDepth;
        },
        get height(){
            return $height;
        },
        get width(){
            return $width;
        }
    };
};

//These descriptions of window properties are taken loosely David Flanagan's
//'JavaScript - The Definitive Guide' (O'Reilly)

/**
 * Window
 * @param {Object} scope
 * @param {Object} parent
 * @param {Object} opener
 */
Window = function(scope, parent, opener){
    
    __initStandardObjects__(scope, parent);
    
    // the window property is identical to the self property and to this obj
    var proxy = new Envjs.proxy(scope, parent);
    scope.__proxy__ = proxy;
    scope.__defineGetter__('window', function(){
        return scope;
    });
    
    
    var $uuid = new Date().getTime()+'-'+Math.floor(Math.random()*1000000000000000); 
    __windows__[$uuid] = scope;
    //console.log('opening window %s', $uuid);
    
    // every window has one-and-only-one .document property which is always
    // an [object HTMLDocument].  also, only window.document objects are
    // html documents, all other documents created by the window.document are
    // [object XMLDocument]
    var $htmlImplementation =  new DOMImplementation();
    $htmlImplementation.namespaceAware = true;
    $htmlImplementation.errorChecking = false;
    
    // read only reference to the Document object
    var $document = new HTMLDocument($htmlImplementation, scope);

    //The version of this application
    var $version = "0.1";
    
    //This should be hooked to git or svn or whatever
    var $revision = "0.0.0.0";
    
    // A read-only reference to the Window object that contains this window
    // or frame.  If the window is a top-level window, parent refers to
    // the window itself.  If this window is a frame, this property refers
    // to the window or frame that contains it.
    var $parent = parent;
    
    /**> $cookies - see cookie.js <*/
    // read only boolean specifies whether the window has been closed
    var $closed = false;
    
    // a read/write string that specifies the default message that 
    // appears in the status line 
    var $defaultStatus = "Done";
    
    // IE only, refers to the most recent event object - this maybe be 
    // removed after review
    var $event = null;
    
    // a read-only reference to the History object
    var $history = new History();
    
    // a read-only reference to the Location object.  the location object does 
    // expose read/write properties
    var $location = new Location('about:blank', $document, $history);
    
    // The name of window/frame. Set directly, when using open(), or in frameset.
    // May be used when specifying the target attribute of links
    var $name = null;
    
    // a read-only reference to the Navigator object
    var $navigator = new Navigator();
    
    // a read/write reference to the Window object that contained the script 
    // that called open() to open this browser window.  This property is valid 
    // only for top-level window objects.
    var $opener = opener?opener:null;
    
    // read-only properties that specify the height and width, in pixels
    var $innerHeight = 600, $innerWidth = 800;
    
    // Read-only properties that specify the total height and width, in pixels, 
    // of the browser window. These dimensions include the height and width of 
    // the menu bar, toolbars, scrollbars, window borders and so on.  These 
    // properties are not supported by IE and IE offers no alternative 
    // properties;
    var $outerHeight = $innerHeight, 
        $outerWidth = $innerWidth;
    
    // Read-only properties that specify the number of pixels that the current 
    // document has been scrolled to the right and down.  These are not 
    // supported by IE.
    var $pageXOffset = 0, $pageYOffset = 0;
    
    // a read-only reference to the Screen object that specifies information  
    // about the screen: the number of available pixels and the number of 
    // available colors.
    var $screen = new Screen(scope);
   
    // read only properties that specify the coordinates of the upper-left 
    // corner of the screen.
    var $screenX = 1, 
        $screenY = 1;
    var $screenLeft = $screenX, 
        $screenTop = $screenY;
    
    // a read/write string that specifies the current status line.
    var $status = '';
    
    __extend__(scope, EventTarget.prototype);

    return __extend__( scope, {
        get closed(){
            return $closed;
        },
        get defaultStatus(){
            return $defaultStatus;
        },
        set defaultStatus(defaultStatus){
            $defaultStatus = defaultStatus;
        },
        get document(){ 
            return $document;
        },
        set document(doc){ 
            $document = doc;
        },
        /*
        deprecated ie specific property probably not good to support
        get event(){
            return $event;
        },
        */
        get frames(){
        return new HTMLCollection($document.getElementsByTagName('frame'));
        },
        get length(){
            // should be frames.length,
            return this.frames.length;
        },
        get history(){
            return $history;
        },
        get innerHeight(){
            return $innerHeight;
        },
        get innerWidth(){
            return $innerWidth;
        },
        get clientHeight(){
            return $innerHeight;
        },
        get clientWidth(){
            return $innerWidth;
        },
        get location(){
            return $location;
        },
        set location(uri){
            uri = Envjs.uri(uri);
            //new Window(this, this.parent, this.opener);
            if($location.href == uri){
                $location.reload();
            }else if($location.href == 'about:blank'){
                $location.assign(uri);
            }else{
                $location.replace(uri);
            }
        },
        get name(){
            return $name;
        },
        set name(newName){ 
            $name = newName; 
        },
        get navigator(){
            return $navigator;
        }, 
        get opener(){
            return $opener;
        },
        get outerHeight(){
            return $outerHeight;
        },
        get outerWidth(){
            return $outerWidth;
        },
        get pageXOffest(){
            return $pageXOffset;
        },
        get pageYOffset(){
            return $pageYOffset;
        },
        get parent(){
            return $parent;
        },
        get screen(){
            return $screen;
        },
        get screenLeft(){
            return $screenLeft;
        },
        get screenTop(){
            return $screenTop;
        },
        get screenX(){
            return $screenX;
        },
        get screenY(){
            return $screenY;
        },
        get self(){
            return scope;
        },
        get status(){
            return $status;
        },
        set status(status){
            $status = status;
        },
        // a read-only reference to the top-level window that contains this window.
        // If this window is a top-level window it is simply a reference to itself.  
        // If this window is a frame, the top property refers to the top-level 
        // window that contains the frame.
        get top(){
            return __top__(scope)
        },
        get window(){
            return proxy;
        },
        toString : function(){
          return '[Window]';
        },
        getComputedStyle : function(element, pseudoElement){
            if(CSS2Properties){
                return element?
                    element.style:new CSS2Properties({cssText:""});
            }
        },
        open: function(url, name, features, replace){
            if (features)
                console.log("'features argument not yet implemented");
            var _window = {},
                open;
            if(replace && name){
                for(open in __windows__){
                    if(open.name === name)
                        _window = open;
                }
            }
            new Window(_window, _window, this);
            if(name)
                _window.name = name;
            _window.document.async = false;
            _window.location.assign(Envjs.uri(url));
            return _window;
        },
        close: function(){
            delete __windows__[$uuid];
        },
        alert : function(message){
            Envjs.alert(message);
        },
        confirm : function(question){
            Envjs.confirm(question);
        },
        prompt : function(message, defaultMsg){
            Envjs.prompt(message, defaultMsg);
        },
        onload: function(){},
        onunload: function(){},
        get uuid(){
            return $uuid;
        }
    });

};

var __top__ = function(_scope){
    var _parent = _scope.parent;
    while(_scope && _parent && _scope !== _parent){
        if(_parent === _parent.parent)break;
        _parent = _parent.parent;
        //console.log('scope %s _parent %s', scope, _parent);
    }
    return _parent || null;
}

var __windows__ = {};

var __initStandardObjects__ = function(scope, parent){
    
    var __Array__;
    if(!scope.Array){
        __Array__ = function(){
            return new parent.top.Array();
        };
        __extend__(__Array__.prototype, parent.top.Array.prototype);
        scope.__defineGetter__('Array', function(){
            return  __Array__;
        });
    }
    
    var __Object__;
    if(!scope.Object){
        __Object__ = function(){
            return new parent.top.Object();
        };
        __extend__(__Object__.prototype, parent.top.Object.prototype);
        scope.__defineGetter__('Object', function(){
            return  __Object__;
        });
    }
    

    var __Date__;
    if(!scope.Date){
        __Date__ = function(){
            return new parent.top.Date();
        };
        __extend__(__Date__.prototype, parent.top.Date.prototype);
        scope.__defineGetter__('Date', function(){
            return  __Date__;
        });
    }
    
    var __Number__;
    if(!scope.Number){
        __Number__ = function(){
            return new parent.top.Number();
        };
        __extend__(__Number__.prototype, parent.top.Number.prototype);
        scope.__defineGetter__('Number', function(){
            return  __Number__;
        });
    }
     
};

//finally pre-supply the window with the window-like environment
console.log('Default Window');
new Window(__this__, __this__);



/**
 * @author john resig & the envjs team
 * @uri http://www.envjs.com/
 * @copyright 2008-2010
 * @license MIT
 */

})();
