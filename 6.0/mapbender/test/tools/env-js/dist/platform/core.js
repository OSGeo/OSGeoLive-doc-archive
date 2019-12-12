/*
 * Envjs core-env.1.2.0.0 
 * Pure JavaScript Browser Environment
 * By John Resig <http://ejohn.org/> and the Envjs Team
 * Copyright 2008-2010 John Resig, under the MIT License
 */

var Envjs = function(){
    var i,
        name
        override = function(){
            for(i=0;i<arguments.length;i++){
                for ( name in arguments[i] ) {
                    var g = arguments[i].__lookupGetter__(name), 
                        s = arguments[i].__lookupSetter__(name);
                    if ( g || s ) {
                        if ( g ) Envjs.__defineGetter__(name, g);
                        if ( s ) Envjs.__defineSetter__(name, s);
                    } else
                        Envjs[name] = arguments[i][name];
                }
            }
        };
    if(arguments.length === 1 && typeof(arguments[0]) == 'string'){
        window.location = arguments[0];
    }else if (arguments.length === 1 && typeof(arguments[0]) == "object"){
        override(arguments[0])
    }else if(arguments.length === 2){
        override(arguments[1]);
        window.location = arguments[0];
    }
    return;
};

//eg "Mozilla"
Envjs.appCodeName  = "Envjs";
//eg "Gecko/20070309 Firefox/2.0.0.3"
Envjs.appName      = "Resig/20070309 PilotFish/1.2.0.1";


/*
 * Envjs core-env.1.2.0.0 
 * Pure JavaScript Browser Environment
 * By John Resig <http://ejohn.org/> and the Envjs Team
 * Copyright 2008-2010 John Resig, under the MIT License
 */

(function(){





/**
 * Writes message to system out
 * @param {String} message
 */
Envjs.log = function(message){};

/**
 * Constants providing enumerated levels for logging in modules
 */
Envjs.DEBUG = 1;
Envjs.INFO = 2;
Envjs.WARN = 3;
Envjs.ERROR = 3;
Envjs.NONE = 3;

/**
 * Writes error info out to console
 * @param {Error} e
 */
Envjs.lineSource = function(e){};




/**
 * describes which script src values will trigger Envjs to load
 * the script like a browser would
 */
Envjs.scriptTypes = {
    "text/javascript"   :false,
    "text/envjs"        :true
};
    
/**
 * will be called when loading a script throws an error
 * @param {Object} script
 * @param {Object} e
 */
Envjs.onScriptLoadError = function(script, e){
    console.log('error loading script %s %s', script, e);
};


/**
 * load and execute script tag text content
 * @param {Object} script
 */
Envjs.loadInlineScript = function(script){
    var tmpFile;
    tmpFile = Envjs.writeToTempFile(script.text, 'js') ;
    load(tmpFile);
};


/**
 * Executes a script tag
 * @param {Object} script
 * @param {Object} parser
 */
Envjs.loadLocalScript = function(script){
    console.debug("loading script %s", script);
    var types, 
        src, 
        i, 
        base,
        filename;
    
    if(script.type){
        types = script.type.split(";");
        for(i=0;i<types.length;i++){
            if(Envjs.scriptTypes[types[i]]){
                //ok this script type is allowed
                break;
            }
            if(i+1 == types.length)
                return false;
        }
    }else{
        try{
            //handle inline scripts
            if(!script.src)
                Envjs.loadInlineScript(script);
             return true;
        }catch(e){
            //Envjs.error("Error loading script.", e);
            Envjs.onScriptLoadError(script, e);
            return false;
        }
    }
        
        
    if(script.src){
        //$env.info("loading allowed external script :" + script.src);
        //lets you register a function to execute 
        //before the script is loaded
        if(Envjs.beforeScriptLoad){
            for(src in Envjs.beforeScriptLoad){
                if(script.src.match(src)){
                    Envjs.beforeScriptLoad[src](script);
                }
            }
        }
        base = "" + script.ownerDocument.location;
        //filename = Envjs.uri(script.src.match(/([^\?#]*)/)[1], base );
        //console.log('base %s', base);
        filename = Envjs.uri(script.src, base);
        try {                      
            load(filename);
            //console.log('loaded %s', filename);
        } catch(e) {
            console.log("could not load script %s \n %s", filename, e );
            Envjs.onScriptLoadError(script, e);
            return false;
        }
        //lets you register a function to execute 
        //after the script is loaded
        if(Envjs.afterScriptLoad){
            for(src in Envjs.afterScriptLoad){
                if(script.src.match(src)){
                    Envjs.afterScriptLoad[src](script);
                }
            }
        }
    }
    return true;
};
    
/**
 * synchronizes thread modifications
 * @param {Function} fn
 */
Envjs.sync = function(fn){};

/**
 * sleep thread for specified duration
 * @param {Object} millseconds
 */
Envjs.sleep = function(millseconds){};
    
/**
 * Interval to wait on event loop when nothing is happening
 */
Envjs.WAIT_INTERVAL = 100;//milliseconds


/**
 * resolves location relative to base or window location
 * @param {Object} path
 * @param {Object} base
 */
Envjs.uri = function(path, base){};
    
    
/**
 * Used in the XMLHttpRquest implementation to run a
 * request in a seperate thread
 * @param {Object} fn
 */
Envjs.runAsync = function(fn){};


/**
 * Used to write to a local file
 * @param {Object} text
 * @param {Object} url
 */
Envjs.writeToFile = function(text, url){};


/**
 * Used to write to a local file
 * @param {Object} text
 * @param {Object} suffix
 */
Envjs.writeToTempFile = function(text, suffix){};

/**
 * Used to delete a local file
 * @param {Object} url
 */
Envjs.deleteFile = function(url){};

/**
 * establishes connection and calls responsehandler
 * @param {Object} xhr
 * @param {Object} responseHandler
 * @param {Object} data
 */
Envjs.connection = function(xhr, responseHandler, data){};


    
    
/**
 * Makes an object window-like by proxying object accessors
 * @param {Object} scope
 * @param {Object} parent
 */
Envjs.proxy = function(scope, parent, aliasList){};

Envjs.javaEnabled = false;   

Envjs.tmpdir         = ''; 
Envjs.os_name        = ''; 
Envjs.os_arch        = ''; 
Envjs.os_version     = ''; 
Envjs.lang           = ''; 
Envjs.platform       = '';//how do we get the version
    
/**
 * 
 * @param {Object} frameElement
 * @param {Object} url
 */
Envjs.loadFrame = function(frame, url){
    try {
        if(frame.contentWindow){
            //mark for garbage collection
            frame.contentWindow = null; 
        }
        
        //create a new scope for the window proxy
        //platforms will need to override this function
        //to make sure the scope is global-like
        frame.contentWindow = (function(){return this;})();
        new Window(frame.contentWindow, window);
        
        //I dont think frames load asynchronously in firefox
        //and I think the tests have verified this but for
        //some reason I'm less than confident... Are there cases?
        frame.contentDocument = frame.contentWindow.document;
        frame.contentDocument.async = false;
        if(url){
            //console.log('envjs.loadFrame async %s', frame.contentDocument.async);
            frame.contentWindow.location = url;
        }
    } catch(e) {
        console.log("failed to load frame content: from %s %s", url, e);
    }
};

/**
 * @author john resig & the envjs team
 * @uri http://www.envjs.com/
 * @copyright 2008-2010
 * @license MIT
 */

})();
