/*
 * Envjs event.1.2.0.0 
 * Pure JavaScript Browser Environment
 * By John Resig <http://ejohn.org/> and the Envjs Team
 * Copyright 2008-2010 John Resig, under the MIT License
 * 
 * This file simply provides the global definitions we need to 
 * be able to correctly implement to core browser DOM Event interfaces.
 */
var Event,
    MouseEvent,
    UIEvent,
    KeyboardEvent,
    MutationEvent,
    DocumentEvent,
    EventTarget,
    EventException,
    //nonstandard but very useful for implementing mutation events 
    //among other things like general profiling
    Aspect;
/*
 * Envjs event.1.2.0.0 
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
 * @author john resig
 */
//from jQuery
function __setArray__( target, array ) {
    // Resetting the length to 0, then using the native Array push
    // is a super-fast way to populate an object with array-like properties
    target.length = 0;
    Array.prototype.push.apply( target, array );
};/**
 * Borrowed with love from:
 * 
 * jQuery AOP - jQuery plugin to add features of aspect-oriented programming (AOP) to jQuery.
 * http://jquery-aop.googlecode.com/
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Version: 1.1
 */
(function() {

	var _after	= 1;
	var _before	= 2;
	var _around	= 3;
	var _intro  = 4;
	var _regexEnabled = true;

	/**
	 * Private weaving function.
	 */
	var weaveOne = function(source, method, advice) {

		var old = source[method];

		var aspect;
		if (advice.type == _after)
			aspect = function() {
				var returnValue = old.apply(this, arguments);
				return advice.value.apply(this, [returnValue, method]);
			};
		else if (advice.type == _before)
			aspect = function() {
				advice.value.apply(this, [arguments, method]);
				return old.apply(this, arguments);
			};
		else if (advice.type == _intro)
			aspect = function() {
				return advice.value.apply(this, arguments);
			};
		else if (advice.type == _around) {
			aspect = function() {
				var invocation = { object: this, args: arguments };
				return advice.value.apply(invocation.object, [{ arguments: invocation.args, method: method, proceed : 
					function() {
						return old.apply(invocation.object, invocation.args);
					}
				}] );
			};
		}

		aspect.unweave = function() { 
			source[method] = old;
			pointcut = source = aspect = old = null;
		};

		source[method] = aspect;

		return aspect;

	};


	/**
	 * Private weaver and pointcut parser.
	 */
	var weave = function(pointcut, advice)
	{

		var source = (typeof(pointcut.target.prototype) != 'undefined') ? pointcut.target.prototype : pointcut.target;
		var advices = [];

		// If it's not an introduction and no method was found, try with regex...
		if (advice.type != _intro && typeof(source[pointcut.method]) == 'undefined')
		{

			for (var method in source)
			{
				if (source[method] != null && source[method] instanceof Function && method.match(pointcut.method))
				{
					advices[advices.length] = weaveOne(source, method, advice);
				}
			}

			if (advices.length == 0)
				throw 'No method: ' + pointcut.method;

		} 
		else
		{
			// Return as an array of one element
			advices[0] = weaveOne(source, pointcut.method, advice);
		}

		return _regexEnabled ? advices : advices[0];

	};

	Aspect = 
	{
		/**
		 * Creates an advice after the defined point-cut. The advice will be executed after the point-cut method 
		 * has completed execution successfully, and will receive one parameter with the result of the execution.
		 * This function returns an array of weaved aspects (Function).
		 *
		 * @example jQuery.aop.after( {target: window, method: 'MyGlobalMethod'}, function(result) { alert('Returned: ' + result); } );
		 * @result Array<Function>
		 *
		 * @example jQuery.aop.after( {target: String, method: 'indexOf'}, function(index) { alert('Result found at: ' + index + ' on:' + this); } );
		 * @result Array<Function>
		 *
		 * @name after
		 * @param Map pointcut Definition of the point-cut to apply the advice. A point-cut is the definition of the object/s and method/s to be weaved.
		 * @option Object target Target object to be weaved. 
		 * @option String method Name of the function to be weaved. Regex are supported, but not on built-in objects.
		 * @param Function advice Function containing the code that will get called after the execution of the point-cut. It receives one parameter
		 *                        with the result of the point-cut's execution.
		 *
		 * @type Array<Function>
		 * @cat Plugins/General
		 */
		after : function(pointcut, advice)
		{
			return weave( pointcut, { type: _after, value: advice } );
		},

		/**
		 * Creates an advice before the defined point-cut. The advice will be executed before the point-cut method 
		 * but cannot modify the behavior of the method, or prevent its execution.
		 * This function returns an array of weaved aspects (Function).
		 *
		 * @example jQuery.aop.before( {target: window, method: 'MyGlobalMethod'}, function() { alert('About to execute MyGlobalMethod'); } );
		 * @result Array<Function>
		 *
		 * @example jQuery.aop.before( {target: String, method: 'indexOf'}, function(index) { alert('About to execute String.indexOf on: ' + this); } );
		 * @result Array<Function>
		 *
		 * @name before
		 * @param Map pointcut Definition of the point-cut to apply the advice. A point-cut is the definition of the object/s and method/s to be weaved.
		 * @option Object target Target object to be weaved. 
		 * @option String method Name of the function to be weaved. Regex are supported, but not on built-in objects.
		 * @param Function advice Function containing the code that will get called before the execution of the point-cut.
		 *
		 * @type Array<Function>
		 * @cat Plugins/General
		 */
		before : function(pointcut, advice)
		{
			return weave( pointcut, { type: _before, value: advice } );
		},


		/**
		 * Creates an advice 'around' the defined point-cut. This type of advice can control the point-cut method execution by calling
		 * the functions '.proceed()' on the 'invocation' object, and also, can modify the arguments collection before sending them to the function call.
		 * This function returns an array of weaved aspects (Function).
		 *
		 * @example jQuery.aop.around( {target: window, method: 'MyGlobalMethod'}, function(invocation) {
		 *                alert('# of Arguments: ' + invocation.arguments.length); 
		 *                return invocation.proceed(); 
		 *          } );
		 * @result Array<Function>
		 *
		 * @example jQuery.aop.around( {target: String, method: 'indexOf'}, function(invocation) { 
		 *                alert('Searching: ' + invocation.arguments[0] + ' on: ' + this); 
		 *                return invocation.proceed(); 
		 *          } );
		 * @result Array<Function>
		 *
		 * @example jQuery.aop.around( {target: window, method: /Get(\d+)/}, function(invocation) {
		 *                alert('Executing ' + invocation.method); 
		 *                return invocation.proceed(); 
		 *          } );
		 * @desc Matches all global methods starting with 'Get' and followed by a number.
		 * @result Array<Function>
		 *
		 *
		 * @name around
		 * @param Map pointcut Definition of the point-cut to apply the advice. A point-cut is the definition of the object/s and method/s to be weaved.
		 * @option Object target Target object to be weaved. 
		 * @option String method Name of the function to be weaved. Regex are supported, but not on built-in objects.
		 * @param Function advice Function containing the code that will get called around the execution of the point-cut. This advice will be called with one
		 *                        argument containing one function '.proceed()', the collection of arguments '.arguments', and the matched method name '.method'.
		 *
		 * @type Array<Function>
		 * @cat Plugins/General
		 */
		around : function(pointcut, advice)
		{
			return weave( pointcut, { type: _around, value: advice } );
		},

		/**
		 * Creates an introduction on the defined point-cut. This type of advice replaces any existing methods with the same
		 * name. To restore them, just unweave it.
		 * This function returns an array with only one weaved aspect (Function).
		 *
		 * @example jQuery.aop.introduction( {target: window, method: 'MyGlobalMethod'}, function(result) { alert('Returned: ' + result); } );
		 * @result Array<Function>
		 *
		 * @example jQuery.aop.introduction( {target: String, method: 'log'}, function() { alert('Console: ' + this); } );
		 * @result Array<Function>
		 *
		 * @name introduction
		 * @param Map pointcut Definition of the point-cut to apply the advice. A point-cut is the definition of the object/s and method/s to be weaved.
		 * @option Object target Target object to be weaved. 
		 * @option String method Name of the function to be weaved.
		 * @param Function advice Function containing the code that will be executed on the point-cut. 
		 *
		 * @type Array<Function>
		 * @cat Plugins/General
		 */
		introduction : function(pointcut, advice)
		{
			return weave( pointcut, { type: _intro, value: advice } );
		},
		
		/**
		 * Configures global options.
		 *
		 * @name setup
		 * @param Map settings Configuration options.
		 * @option Boolean regexMatch Enables/disables regex matching of method names.
		 *
		 * @example jQuery.aop.setup( { regexMatch: false } );
		 * @desc Disable regex matching.
		 *
		 * @type Void
		 * @cat Plugins/General
		 */
		setup: function(settings)
		{
			_regexEnabled = settings.regexMatch;
		}
	};

})();



/**
 * 
 * // Introduced in DOM Level 2:
 * interface DocumentEvent {
 *   Event createEvent (in DOMString eventType) 
 *      raises (DOMException);
 * };
 */
DocumentEvent = function(){};
DocumentEvent.prototype.createEvent = function(eventType){
    //console.debug('createEvent(%s)', eventType); 
    switch (eventType){
        case 'Events':
            return new Event(); 
            break;
        case 'HTMLEvents':
            return new Event(); 
            break;
        case 'UIEvents':
            return new UIEvent();
            break;
        case 'MouseEvents':
            return new MouseEvent();
            break;
        case 'KeyEvents':
            return new KeyboardEvent();
            break;
        case 'KeyboardEvent':
            return new KeyboardEvent();
            break;
        case 'MutationEvents':
            return new MutationEvent();
            break;
        default:
            throw(new DOMException(DOMException.NOT_SUPPORTED_ERR));
    }
};

Document.prototype.createEvent = DocumentEvent.prototype.createEvent;

/**
 * @name EventTarget 
 * @w3c:domlevel 2 
 * @uri -//TODO: paste dom event level 2 w3c spc uri here
 */
EventTarget = function(){};
EventTarget.prototype.addEventListener = function(type, fn, phase){ 
    __addEventListener__(this, type, fn, phase); 
};
EventTarget.prototype.removeEventListener = function(type, fn){ 
    __removeEventListener__(this, type, fn); 
};
EventTarget.prototype.dispatchEvent = function(event, bubbles){ 
    __dispatchEvent__(this, event, bubbles); 
};

__extend__(Node.prototype, EventTarget.prototype);


var $events = [{}];

function __addEventListener__(target, type, fn, phase){
    phase = !!phase?"CAPTURING":"BUBBLING";
    if ( !target.uuid ) {
        target.uuid = $events.length+'';
        //console.log('event uuid %s %s', target, target.uuid);
    }
    if ( !$events[target.uuid] ) {
        $events[target.uuid] = {};
    }
    if ( !$events[target.uuid][type] ){
        $events[target.uuid][type] = {
            CAPTURING:[],
            BUBBLING:[]
        };
    }
    if ( $events[target.uuid][type][phase].indexOf( fn ) < 0 ){
        //console.log('adding event listener %s %s %s %s %s %s', target, target.uuid, type, phase, 
        //    $events[target.uuid][type][phase].length, $events[target.uuid][type][phase].indexOf( fn ));
        $events[target.uuid][type][phase].push( fn );
        //console.log('adding event listener %s %s %s %s %s %s', target, target.uuid, type, phase, 
        //    $events[target.uuid][type][phase].length, $events[target.uuid][type][phase].indexOf( fn ));
    }
};


function __removeEventListener__(target, type, fn, phase){

    phase = !!phase?"CAPTURING":"BUBBLING";
    if ( !target.uuid ) {
        target.uuid = $events.length+'';
    }
    if ( !$events[target.uuid] ) {
        $events[target.uuid] = {};
    }
    if ( !$events[target.uuid][type] ){
        $events[target.uuid][type] = {
            CAPTURING:[],
            BUBBLING:[]
        };
    }
    $events[target.uuid][type][phase] =
    $events[target.uuid][type][phase].filter(function(f){
        //console.log('removing event listener %s %s %s %s', target, type, phase, fn);
        return f != fn;
    });
};


function __dispatchEvent__(target, event, bubbles){

    //the window scope defines the $event object, for IE(^^^) compatibility;
    $event = event;

    if (bubbles == undefined || bubbles == null)
        bubbles = true;

    if (!event.target) {
        event.target = target;
    }
    
    //console.log('dispatching? %s %s %s', target, event.type, bubbles);
    if ( event.type && (target.nodeType || target === window )) {

        //console.log('dispatching event %s %s %s', target, event.type, bubbles);
        __captureEvent__(target, event);
        
        event.eventPhase = Event.AT_TARGET;
        if ( target.uuid && $events[target.uuid] && $events[target.uuid][event.type] ) {
            event.currentTarget = target;
            //console.log('dispatching %s %s %s %s', target, event.type, $events[target.uuid][event.type]['CAPTURING'].length);
            $events[target.uuid][event.type]['CAPTURING'].forEach(function(fn){
                //console.log('AT_TARGET (CAPTURING) event %s', fn);
                var returnValue = fn( event );
                //console.log('AT_TARGET (CAPTURING) return value %s', returnValue);
                if(returnValue === false){
                    event.stopPropagation();
                }
            });
            //console.log('dispatching %s %s %s %s', target, event.type, $events[target.uuid][event.type]['BUBBLING'].length);
            $events[target.uuid][event.type]['BUBBLING'].forEach(function(fn){
                //console.log('AT_TARGET (BUBBLING) event %s', fn);
                var returnValue = fn( event );
                //console.log('AT_TARGET (BUBBLING) return value %s', returnValue);
                if(returnValue === false){
                    event.stopPropagation();
                }
            });
        }
        if (target["on" + event.type]) {
            target["on" + event.type](event);
        }
        if (bubbles && !event.cancelled){
            __bubbleEvent__(target, event);
        }
    }else{
        throw new EventException(EventException.UNSPECIFIED_EVENT_TYPE_ERR);
    }
};

function __captureEvent__(target, event){
    var ancestorStack = [],
        parent = target.parentNode;
        
    event.eventPhase = Event.CAPTURING_PHASE;
    while(parent){
        if(parent.uuid && $events[parent.uuid] && $events[parent.uuid][event.type]){
            ancestorStack.push(parent);
        }
        parent = parent.parentNode;
    }
    while(ancestorStack.length && !event.cancelled){
        event.currentTarget = ancestorStack.pop();
        if($events[event.currentTarget.uuid] && $events[event.currentTarget.uuid][event.type]){
            $events[event.currentTarget.uuid][event.type]['CAPTURING'].forEach(function(fn){
                var returnValue = fn( event );
                if(returnValue === false){
                    event.stopPropagation();
                }
            });
        }
    }
};

function __bubbleEvent__(target, event){
    var parent = target.parentNode;
    event.eventPhase = Event.BUBBLING_PHASE;
    while(parent){
        if(parent.uuid && $events[parent.uuid] && $events[parent.uuid][event.type] ){
            event.currentTarget = parent;
            $events[event.currentTarget.uuid][event.type]['BUBBLING'].forEach(function(fn){
                var returnValue = fn( event );
                if(returnValue === false){
                    event.stopPropagation();
                }
            });
        }
        parent = parent.parentNode;
    }
};

/**
 * @class Event
 */
Event = function(options){
    // event state is kept read-only by forcing
    // a new object for each event.  This may not
    // be appropriate in the long run and we'll
    // have to decide if we simply dont adhere to
    // the read-only restriction of the specification
    var state = __extend__({
        bubbles : true,
        cancelable : true,
        cancelled: false,
        currentTarget : null,
        target : null,
        eventPhase : Event.AT_TARGET,
        timeStamp : new Date().getTime(),
        preventDefault : false,
        stopPropogation : false
    }, options||{} );
        
    return {
        get bubbles(){return state.bubbles;},
        get cancelable(){return state.cancelable;},
        get currentTarget(){return state.currentTarget;},
        set currentTarget(currentTarget){ state.currentTarget = currentTarget; },
        get eventPhase(){return state.eventPhase;},
        set eventPhase(eventPhase){state.eventPhase = eventPhase;},
        get target(){return state.target;},
        set target(target){ state.target = target;},
        get timeStamp(){return state.timeStamp;},
        get type(){return state.type;},
        initEvent: function(type, bubbles, cancelable){
            state.type=type?type:'';
            state.bubbles=!!bubbles;
            state.cancelable=!!cancelable;
        },
        preventDefault: function(){
            state.preventDefault = true;
        },
        stopPropagation: function(){
            if(state.cancelable){
                state.cancelled = true;
                state.bubbles = false;
            }
        },
        get cancelled(){
            return state.cancelled;
        },
        toString: function(){
            return '[object Event]';
        }
    };
};

__extend__(Event,{
    CAPTURING_PHASE : 1,
    AT_TARGET       : 2,
    BUBBLING_PHASE  : 3
});




/**
 * @name UIEvent
 * @param {Object} options
 */
UIEvent = function(options) {
    var state = __extend__({
        view : null,
        detail : 0
    }, options||{});
    return __extend__(new Event(state),{
        get view(){
            return state.view;
        },
        get detail(){
            return state.detail;
        },
        initUIEvent: function(type, bubbles, cancelable, windowObject, detail){
            this.initEvent(type, bubbles, cancelable);
            state.detail = 0;
            state.view = windowObject;
        }
    });
};
UIEvent.prototype = new Event;

var $onblur,
    $onfocus,
    $onresize;
    
    
/**
 * @name MouseEvent
 * @w3c:domlevel 2 
 * @uri http://www.w3.org/TR/2000/REC-DOM-Level-2-Events-20001113/events.html
 */
MouseEvent = function(options) {
    var state = __extend__({
        screenX: 0,
        screenY: 0,
        clientX: 0,
        clientY: 0,
        ctrlKey: false,
        metaKey: false,
        altKey:  false,
        metaKey: false,
        button: null,
        relatedTarget: null
    }, options||{});
    return __extend__(new Event(state),{
        get screenX(){
            return state.screenX;
        },
        get screenY(){
            return state.screenY;
        },
        get clientX(){
            return state.clientX;
        },
        get clientY(){
            return state.clientY;
        },
        get ctrlKey(){
            return state.ctrlKey;
        },
        get altKey(){
            return state.altKey;
        },
        get shiftKey(){
            return state.shiftKey;
        },
        get metaKey(){
            return state.metaKey;
        },
        get button(){
            return state.button;
        },
        get relatedTarget(){
            return state.relatedTarget;
        },
        initMouseEvent: function(type, bubbles, cancelable, windowObject, detail,
                screenX, screenY, clientX, clientY, ctrlKey, altKey, shiftKey, 
                metaKey, button, relatedTarget){
            this.initUIEvent(type, bubbles, cancelable, windowObject, detail);
            state.screenX = screenX;
            state.screenY = screenY;
            state.clientX = clientX;
            state.clientY = clientY;
            state.ctrlKey = ctrlKey;
            state.altKey = altKey;
            state.shiftKey = shiftKey;
            state.metaKey = metaKey;
            state.button = button;
            state.relatedTarget = relatedTarget;
        }
    });
};
MouseEvent.prototype = new UIEvent;

/**
 * Interface KeyboardEvent (introduced in DOM Level 3)
 */
KeyboardEvent = function(options) {
    var state = __extend__({
        keyIdentifier: 0,
        keyLocation: 0,
        ctrlKey: false,
        metaKey: false,
        altKey:  false,
        metaKey: false,
    }, options||{});
    return __extend__(new Event(state),{
        
        get ctrlKey(){
            return state.ctrlKey;
        },
        get altKey(){
            return state.altKey;
        },
        get shiftKey(){
            return state.shiftKey;
        },
        get metaKey(){
            return state.metaKey;
        },
        get button(){
            return state.button;
        },
        get relatedTarget(){
            return state.relatedTarget;
        },
        getModifiersState: function(keyIdentifier){

        },
        initMouseEvent: function(type, bubbles, cancelable, windowObject, 
                keyIdentifier, keyLocation, modifiersList, repeat){
            this.initUIEvent(type, bubbles, cancelable, windowObject, 0);
            state.keyIdentifier = keyIdentifier;
            state.keyLocation = keyLocation;
            state.modifiersList = modifiersList;
            state.repeat = repeat;
        }
    });
};
KeyboardEvent.prototype = new UIEvent;

KeyboardEvent.DOM_KEY_LOCATION_STANDARD      = 0;
KeyboardEvent.DOM_KEY_LOCATION_LEFT          = 1;
KeyboardEvent.DOM_KEY_LOCATION_RIGHT         = 2;
KeyboardEvent.DOM_KEY_LOCATION_NUMPAD        = 3;
KeyboardEvent.DOM_KEY_LOCATION_MOBILE        = 4;
KeyboardEvent.DOM_KEY_LOCATION_JOYSTICK      = 5;



//We dont fire mutation events until someone has registered for them
var __supportedMutations__ = /DOMSubtreeModified|DOMNodeInserted|DOMNodeRemoved|DOMAttrModified|DOMCharacterDataModified/;

var __fireMutationEvents__ = Aspect.before({
    target: EventTarget, 
    method: 'addEventListener'
}, function(target, type){
    if(type && type.match(__supportedMutations__)){
        //unweaving removes the __addEventListener__ aspect
        __fireMutationEvents__.unweave();
        // These two methods are enough to cover all dom 2 manipulations
        Aspect.around({ 
            target: Node,  
            method:"removeChild"
        }, function(invocation){
            var event,
                node = invocation.arguments[0];
            event = node.ownerDocument.createEvent('MutationEvents');
            event.initEvent('DOMNodeRemoved', true, false, node.parentNode, null, null, null, null);
            node.dispatchEvent(event, false);
            return invocation.proceed();
            
        }); 
        Aspect.around({ 
            target: Node,  
            method:"appendChild"
        }, function(invocation) {
            var event,
                node = invocation.proceed();
            event = node.ownerDocument.createEvent('MutationEvents');
            event.initEvent('DOMNodeInserted', true, false, node.parentNode, null, null, null, null);
            node.dispatchEvent(event, false); 
            return node;
        });
    }
});

/**
 * @name MutationEvent
 * @param {Object} options
 */
MutationEvent = function(options) {
    var state = __extend__({
        cancelable : false,
        timeStamp : 0,
    }, options||{});
    return __extend__(new Event(state),{
        get relatedNode(){
            return state.relatedNode;
        },
        get prevValue(){
            return state.prevValue;
        },
        get newValue(){
            return state.newValue;
        },
        get attrName(){
            return state.attrName;
        },
        get attrChange(){
            return state.attrChange;
        },
        initMutationEvent: function( type, bubbles, cancelable, 
                relatedNode, prevValue, newValue, attrName, attrChange ){
            state.relatedNode = relatedNode;
            state.prevValue = prevValue;
            state.newValue = newValue;
            state.attrName = attrName;
            state.attrChange = attrChange;
            switch(type){
                case "DOMSubtreeModified":
                    this.initEvent(type, true, false);
                    break;
                case "DOMNodeInserted":
                    this.initEvent(type, true, false);
                    break;
                case "DOMNodeRemoved":
                    this.initEvent(type, true, false);
                    break;
                case "DOMNodeRemovedFromDocument":
                    this.initEvent(type, false, false);
                    break;
                case "DOMNodeInsertedIntoDocument":
                    this.initEvent(type, false, false);
                    break;
                case "DOMAttrModified":
                    this.initEvent(type, true, false);
                    break;
                case "DOMCharacterDataModified":
                    this.initEvent(type, true, false);
                    break;
                default:
                    this.initEvent(type, bubbles, cancelable);
            }
        }
    });
};
MutationEvent.prototype = new Event;

// constants
MutationEvent.ADDITION = 0;
MutationEvent.MODIFICATION = 1;
MutationEvent.REMOVAL = 2;


/**
 * @name EventException
 */
EventException = function(code) {
  this.code = code;
};
EventException.UNSPECIFIED_EVENT_TYPE_ERR = 0;

/**
 * @author john resig & the envjs team
 * @uri http://www.envjs.com/
 * @copyright 2008-2010
 * @license MIT
 */

})();
