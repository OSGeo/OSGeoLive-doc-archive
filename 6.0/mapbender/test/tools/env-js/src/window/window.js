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


