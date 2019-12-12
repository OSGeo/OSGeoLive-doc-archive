
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

