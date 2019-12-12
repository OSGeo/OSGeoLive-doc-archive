
//Since we're running in rhino I guess we can safely assume
//java is 'enabled'.  I'm sure this requires more thought
//than I've given it here
Envjs.javaEnabled = true;   

Envjs.tmpdir         = java.lang.System.getProperty("java.io.tmpdir"); 
Envjs.os_name        = java.lang.System.getProperty("os.name"); 
Envjs.os_arch        = java.lang.System.getProperty("os.arch"); 
Envjs.os_version     = java.lang.System.getProperty("os.version"); 
Envjs.lang           = java.lang.System.getProperty("user.lang"); 
Envjs.platform       = "Rhino ";//how do we get the version
    

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
        frame.contentWindow = __context__.initStandardObjects();
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
 * Makes an object window-like by proxying object accessors
 * @param {Object} scope
 * @param {Object} parent
 */
Envjs.proxy = function(scope, parent){

    try{   
        if(scope+'' == '[object global]'){
            __context__.initStandardObjects(scope);
            //console.log('succeeded to init standard objects %s %s', scope, parent);
        }
    }catch(e){
        console.log('failed to init standard objects %s %s \n%s', scope, parent, e);
    }
    
    var _scope = scope;
        _parent = parent||null,
        _this = this,
        _undefined = Packages.org.mozilla.javascript.Scriptable.NOT_FOUND,
        _proxy = new Packages.org.mozilla.javascript.ScriptableObject({
            getClassName: function(){
                return 'envjs.platform.rhino.Proxy';
            },
            has: function(nameOrIndex, start){
                var has;
                //print('proxy has '+nameOrIndex+" ("+nameOrIndex['class']+")");
                if(nameOrIndex['class'] == java.lang.String){
                    switch(nameOrIndex+''){
                        case '__iterator__':
                            return _proxy.__iterator__;
                            break;
                        default:
                            has = (nameOrIndex+'') in _scope;
                            //print('has as string :'+has);
                            return has;
                    }
                }else{
                    //print('has not');
                    return false;
                }
            },
            put: function(nameOrIndex,  start,  value){
                //print('put '+ value);
                _scope[nameOrIndex+''] = value;
            },
            get: function(nameOrIndex, start){
                //print('proxy get '+nameOrIndex+" ("+nameOrIndex['class']+")");
                var value;
                if(nameOrIndex['class'] == java.lang.String){
                    //print("get as string");
                    value = _scope[nameOrIndex+''];
                    if(value+'' === "undefined"){
                        return _undefined;
                    }else{
                        return value;
                    }
                } else {
                    //print('get not');
                    return _undefined;
                }
            },
            'delete': function(nameOrIndex){
                //console.log('deleting %s', nameOrIndex);
                delete _scope[nameOrIndex+''];
            },
            get parentScope(){
                //console.log('get proxy parentScope');
                return _parent;
            },
            set parentScope(parent){
                //console.log('set proxy parentScope');
                _parent = parent;
            },
            get topLevelScope(){
                //console.log('get proxy topLevelScope');
                return _parent;
            },
            equivalentValues: function(value){
                return (value == _scope || value == this );
            },
            equals: function(value){
                return (value === _scope || value === this );
            }
        });
        
    
            
    return _proxy;
    
};
