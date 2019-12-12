
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
