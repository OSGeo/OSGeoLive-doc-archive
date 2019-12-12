
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

