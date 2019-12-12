

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
    
    