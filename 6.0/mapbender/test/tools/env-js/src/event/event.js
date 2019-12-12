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


