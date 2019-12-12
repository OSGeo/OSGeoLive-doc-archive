
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


