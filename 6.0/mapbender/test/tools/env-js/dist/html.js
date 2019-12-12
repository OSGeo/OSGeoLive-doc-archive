/*
 * Envjs html.1.2.0.0 
 * Pure JavaScript Browser Environment
 * By John Resig <http://ejohn.org/> and the Envjs Team
 * Copyright 2008-2010 John Resig, under the MIT License
 * 
 * This file simply provides the global definitions we need to 
 * be able to correctly implement to core browser DOM HTML interfaces.
 */
var HTMLDocument,
    HTMLElement,
    HTMLCollection,
    HTMLAnchorElement,
    HTMLAreaElement,
    HTMLBaseElement,
    HTMLQuoteElement,
    HTMLBodyElement,
    HTMLButtonElement,
    HTMLCanvasElement,
    HTMLTableColElement,
    HTMLModElement,
    HTMLDivElement,
    HTMLFieldSetElement,
    HTMLFormElement,
    HTMLFrameElement,
    HTMLFrameSetElement,
    HTMLHeadElement,
    HTMLIFrameElement,
    HTMLImageElement,
    HTMLInputElement,
    HTMLLabelElement,
    HTMLLegendElement,
    HTMLLinkElement,
    HTMLMapElement,
    HTMLMetaElement,
    HTMLObjectElement,
    HTMLOptGroupElement,
    HTMLOptionElement,
    HTMLParamElement,
    HTMLScriptElement,
    HTMLSelectElement,
    HTMLStyleElement,
    HTMLTableElement,
    HTMLTableSectionElement,
    HTMLTableCellElement,
    HTMLTableRowElement,
    HTMLTextAreaElement,
    HTMLTitleElement,
    HTMLUnknownElement;
    
/*
 * Envjs html.1.2.0.0 
 * Pure JavaScript Browser Environment
 * By John Resig <http://ejohn.org/> and the Envjs Team
 * Copyright 2008-2010 John Resig, under the MIT License
 */

(function(){





/**
 * @author ariel flesler
 *    http://flesler.blogspot.com/2008/11/fast-trim-function-for-javascript.html 
 * @param {Object} str
 */
function __trim__( str ){
    return (str || "").replace( /^\s+|\s+$/g, "" );
    
};

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
};
/**
 * @class  HTMLDocument
 *      The Document interface represents the entire HTML or XML document.
 *      Conceptually, it is the root of the document tree, and provides 
 *      the primary access to the document's data.
 *
 * @extends Document
 */
HTMLDocument = function(implementation, ownerWindow, referrer) {
    Document.apply(this, arguments);
    this.referrer = referrer;
    this.baseURI = "about:blank";
    this.ownerWindow = ownerWindow;
};
HTMLDocument.prototype = new Document;

__extend__(HTMLDocument.prototype, {
    createElement: function(tagName){
        tagName = tagName.toUpperCase();
        // create Element specifying 'this' as ownerDocument
        // This is an html document so we need to use explicit interfaces per the 
        //TODO: would be much faster as a big switch
        switch(tagName){
            case "A":
                node = new HTMLAnchorElement(this);break;
            case "AREA":
                node = new HTMLAreaElement(this);break;
            case "BASE":
                node = new HTMLBaseElement(this);break;
            case "BLOCKQUOTE":
                node = new HTMLQuoteElement(this);break;
            case "Q":
                node = new HTMLQuoteElement(this);break;
            case "BODY":
                node = new HTMLBodyElement(this);break;
            case "BR":
                node = new HTMLElement(this);break;
            case "BUTTON":
                node = new HTMLButtonElement(this);break;
            case "CAPTION":
                node = new HTMLElement(this);break;
            case "COL":
                node = new HTMLTableColElement(this);break;
            case "COLGROUP":
                node = new HTMLTableColElement(this);break;
            case "DEL":
                node = new HTMLModElement(this);break;
            case "INS":
                node = new HTMLModElement(this);break;
            case "DIV":
                node = new HTMLDivElement(this);break;
            case "DL":
                node = new HTMLElement(this);break;
            case "FIELDSET":
                node = new HTMLFieldSetElement(this);break;
            case "FORM":
                node = new HTMLFormElement(this);break;
            case "FRAME":
                node = new HTMLFrameElement(this);break;
            case "H1":
                node = new HTMLHeadElement(this);break;
            case "H2":
                node = new HTMLHeadElement(this);break;
            case "H3":
                node = new HTMLHeadElement(this);break;
            case "H4":
                node = new HTMLHeadElement(this);break;
            case "H5":
                node = new HTMLHeadElement(this);break;
            case "H6":
                node = new HTMLHeadElement(this);break;
            case "HR":
                node = new HTMLElement(this);break;
            case "HTML":
                node = new HTMLElement(this);break;
            case "IFRAME":
                node = new HTMLIFrameElement(this);break;
            case "IMG":
                node = new HTMLImageElement(this);break;
            case "INPUT":
                node = new HTMLInputElement(this);break;
            case "LABEL":
                node = new HTMLLabelElement(this);break;
            case "LEGEND":
                node = new HTMLLegendElement(this);break;
            case "LI":
                node = new HTMLElement(this);break;
            case "LINK":
                node = new HTMLLinkElement(this);break;
            case "MAP":
                node = new HTMLMapElement(this);break;
            case "META":
                node = new HTMLObjectElement(this);break;
            case "OBJECT":
                node = new HTMLMapElement(this);break;
            case "OPTGROUP":
                node = new HTMLOptGroupElement(this);break;
            case "OPTION":
                node = new HTMLOptionElement(this);break;
            case "P":
                node = new HTMLParagraphElement(this);break;
            case "PARAM":
                node = new HTMLParamElement(this);break;
            case "PRE":
                node = new HTMLElement(this);break;
            case "SCRIPT":
                node = new HTMLScriptElement(this);break;
            case "SELECT":
                node = new HTMLSelectElement(this);break;
            case "STYLE":
                node = new HTMLStyleElement(this);break;
            case "TABLE":
                node = new HTMLTableElement(this);break;
            case "TBODY":
                node = new HTMLTableSectionElement(this);break;
            case "TFOOT":
                node = new HTMLTableSectionElement(this);break;
            case "THEAD":
                node = new HTMLTableSectionElement(this);break;
            case "TD":
                node = new HTMLTableCellElement(this);break;
            case "TH":
                node = new HTMLTableCellElement(this);break;
            case "TEXTAREA":
                node = new HTMLTextAreaElement(this);break;
            case "TITLE":
                node = new HTMLTitleElement(this);break;
            case "TR":
                node = new HTMLTableRowElement(this);break;
            case "UL":
                node = new HTMLElement(this);break;
            default:
                node = new HTMLUnknownElement(this);
        }
        // assign values to properties (and aliases)
        node.nodeName  = tagName;
        return node;
    },
    createElementNS : function (uri, local) {
        //print('createElementNS :'+uri+" "+local);
        if(!uri){
            return this.createElement(local);
        }else if ("http://www.w3.org/1999/xhtml" == uri) {
            return this.createElement(local);
        } else if ("http://www.w3.org/1998/Math/MathML" == uri) {
            return this.createElement(local);
        } else {
            return Document.prototype.createElementNS.apply(this,[uri, local]);
        }
    },
    get anchors(){
        return new HTMLCollection(this.getElementsByTagName('a'));
        
    },
    get applets(){
        return new HTMLCollection(this.getElementsByTagName('applet'));
        
    },
    get body(){ 
        var nodelist = this.getElementsByTagName('body');
        if(nodelist.length === 0){
            __stubHTMLDocument__(this);
            nodelist = this.getElementsByTagName('body');
        }
        return nodelist[0];
        
    },
    set body(html){
        return this.replaceNode(this.body,html);
    },

    get title(){
        var titleArray = this.getElementsByTagName('title');
        if (titleArray.length < 1)
            return "";
        return titleArray[0].textContent;
    },
    set title(titleStr){
        var titleArray = this.getElementsByTagName('title'),
            titleElem,
            headArray;
        if (titleArray.length < 1){
            // need to make a new element and add it to "head"
            titleElem = new HTMLTitleElement(this);
            titleElem.text = titleStr;
            headArray = this.getElementsByTagName('head');
    	    if (headArray.length < 1)
                return;  // ill-formed, just give up.....
            headArray[0].appendChild(titleElem);
        } else {
            titleArray[0].textContent = titleStr;
        }
    },

    get cookie(){
        return Cookies.get(this);
    },
    set cookie(cookie){
        return Cookies.set(this, cookie);
    },
    get location(){
        return this.baseURI;
    },
    set location(url){
        this.baseURI = url;
    },
    get domain(){
        var HOSTNAME = new RegExp('\/\/([^\:\/]+)'),
            matches = HOSTNAME.exec(this.baseURI);
        return matches&&matches.length>1?matches[1]:"";
    },
    set domain(value){
        var i,
            domainParts = this.domain.splt('.').reverse(),
            newDomainParts = value.split('.').reverse();
        if(newDomainParts.length > 1){
            for(i=0;i<newDomainParts.length;i++){
                if(!(newDomainParts[i] == domainParts[i])){
                    return;
                }
            }
            this.baseURI = this.baseURI.replace(domainParts.join('.'), value);
        }
    },
    get forms(){
      return new HTMLCollection(this.getElementsByTagName('form'));
    },
    get images(){
        return new HTMLCollection(this.getElementsByTagName('img'));
    },
    get lastModified(){ 
        /* TODO */
        return this._lastModified; 
    },
    get links(){
        return new HTMLCollection(this.getElementsByTagName('a'));
    },
	getElementsByName : function(name){
        //returns a real Array + the NodeList
        var retNodes = __extend__([],new NodeList(this, this.documentElement)),
          node;
        // loop through all Elements in the 'all' collection
        var all = this.all;
        for (var i=0; i < all.length; i++) {
            node = all[i];
            if (node.nodeType == Node.ELEMENT_NODE && 
                node.getAttribute('name') == name) {
                retNodes.push(node);
            }
        }
        return retNodes;
	},
	toString: function(){ 
	    return "[object HTMLDocument]"; 
    },
	get innerHTML(){ 
	    return this.documentElement.outerHTML; 
    },
    get URL(){ 
        return this.location;  
    },
    set URL(url){
        this.location = url;  
    }
});

var __stubHTMLDocument__ = function(doc){
    var html = doc.documentElement,
        head,
        body,
        children, i;    
    if(!html){
        //console.log('stubbing html doc');
        html = doc.createElement('html');
        doc.appendChild(html);
        head = doc.createElement('head');
        html.appendChild(head);
        body = doc.createElement('body');
        html.appendChild(body);
    }else{
        body = doc.documentElement.getElementsByTagName('body').item(0);
        if(!body){
            body = doc.createElement('body');
            html.appendChild(body);
        }
        head = doc.documentElement.getElementsByTagName('head').item(0);
        if(!head){
            head = doc.createElement('head');
            html.appendChild(head);
        }
    }
};

Aspect.around({ 
    target: Node,  
    method:"appendChild"
}, function(invocation) {
    var event,
        okay,
        node = invocation.proceed(),
        doc = node.ownerDocument;
    if((node.nodeType !== Node.ELEMENT_NODE)){
        //for now we are only handling element insertions.  probably we will need
        //to handle text node changes to script tags and changes to src 
        //attributes
        return node;
    }
    //console.log('appended html element %s %s %s', node.namespaceURI, node.nodeName, node);
    
    switch(doc.parsing){
        case true:
            //handled by parser if included
            break;
        case false:
            switch(node.namespaceURI){
                case null:
                    //fall through
                case "":
                    //fall through
                case "http://www.w3.org/1999/xhtml":
                    switch(node.tagName.toLowerCase()){
                        case 'script':
                            if((node.parentNode.nodeName+"".toLowerCase() == 'head')){
                                try{
                                    okay = Envjs.loadLocalScript(node, null);
                                    //console.log('loaded script? %s %s', node.uuid, okay);
                                    // only fire event if we actually had something to load
                                    if (node.src && node.src.length > 0){
                                        event = doc.createEvent('HTMLEvents');
                                        event.initEvent( okay ? "load" : "error", false, false );
                                        node.dispatchEvent( event, false );
                                    }
                                }catch(e){
                                    console.log('error loading html element %s %e', node, e.toString());
                                }
                            }
                            break;
                        case 'frame':
                        case 'iframe':
                            node.contentWindow = { };
                            node.contentDocument = new HTMLDocument(new DOMImplementation(), node.contentWindow);
                            node.contentWindow.document = node.contentDocument
                            node.contentDocument.addEventListener('DOMContentLoaded', function(){
                                event = node.contentDocument.createEvent('HTMLEvents');
                                event.initEvent("load", false, false);
                                node.dispatchEvent( event, false );
                            });
                            try{
                                if (node.src && node.src.length > 0){
                                    //console.log("getting content document for (i)frame from %s", node.src);
                                    Envjs.loadFrame(node, Envjs.uri(node.src));
                                    event = node.contentDocument.createEvent('HTMLEvents');
                                    event.initEvent("load", false, false);
                                    node.dispatchEvent( event, false );
                                }else{
                                    //I dont like this being here:
                                    //TODO: better  mix-in strategy so the try/catch isnt required
                                    try{
                                        if(Window){
                                            Envjs.loadFrame(node);
                                            //console.log('src/html/document.js: triggering frame load');
                                            event = node.contentDocument.createEvent('HTMLEvents');
                                            event.initEvent("load", false, false);
                                            node.dispatchEvent( event, false );
                                        }
                                    }catch(e){}
                                }
                            }catch(e){
                                console.log('error loading html element %s %e', node, e.toString());
                            }
                            break;
                        case 'link':
                            if (node.href && node.href.length > 0){
                                // don't actually load anything, so we're "done" immediately:
                                event = doc.createEvent('HTMLEvents');
                                event.initEvent("load", false, false);
                                node.dispatchEvent( event, false );
                            }
                            break;
                        case 'img':
                            if (node.src && node.src.length > 0){
                                // don't actually load anything, so we're "done" immediately:
                                event = doc.createEvent('HTMLEvents');
                                event.initEvent("load", false, false);
                                node.dispatchEvent( event, false );
                            }
                            break;
                        default:
                            break;
                    }//switch on name
                default:
                    break;
            }//switch on ns
            break;
        default: 
            console.log('element appended: %s %s', node+'', node.namespaceURI);
    }//switch on doc.parsing
    return node;

});


/**
 * @name HTMLEvents
 * @w3c:domlevel 2 
 * @uri http://www.w3.org/TR/2000/REC-DOM-Level-2-Events-20001113/events.html
 */
var HTMLEvents= function(){};
HTMLEvents.prototype = {
    onload: function(event){
        __eval__(this.getAttribute('onload')||'', this);
    },
    onunload: function(event){
        __eval__(this.getAttribute('onunload')||'', this);
    },
    onabort: function(event){
        __eval__(this.getAttribute('onabort')||'', this);
    },
    onerror: function(event){
        __eval__(this.getAttribute('onerror')||'', this);
    },
    onselect: function(event){
        __eval__(this.getAttribute('onselect')||'', this);
    },
    onchange: function(event){
        __eval__(this.getAttribute('onchange')||'', this);
    },
    onsubmit: function(event){
        if (__eval__(this.getAttribute('onsubmit')||'', this)) {
            this.submit();
        }
    },
    onreset: function(event){
        __eval__(this.getAttribute('onreset')||'', this);
    },
    onfocus: function(event){
        __eval__(this.getAttribute('onfocus')||'', this);
    },
    onblur: function(event){
        __eval__(this.getAttribute('onblur')||'', this);
    },
    onresize: function(event){
        __eval__(this.getAttribute('onresize')||'', this);
    },
    onscroll: function(event){
        __eval__(this.getAttribute('onscroll')||'', this);
    }
};


var __eval__ = function(script, node){
    if (!script == ""){
        // don't assemble environment if no script...
        try{
            eval(script);
        }catch(e){
            console.log('error evaluating %s', e);
        }               
    }
};



//HTMLDocument, HTMLFramesetElement, HTMLObjectElement
var  __load__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("load", false, false);
    element.dispatchEvent(event);
    return event;
};

//HTMLFramesetElement, HTMLBodyElement
var  __unload__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("unload", false, false);
    element.dispatchEvent(event);
    return event;
};

//HTMLObjectElement
var  __abort__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("abort", true, false);
    element.dispatchEvent(event);
    return event;
};

//HTMLFramesetElement, HTMLObjectElement, HTMLBodyElement 
var  __error__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("error", true, false);
    element.dispatchEvent(event);
    return event;
};

//HTMLInputElement, HTMLTextAreaElement
var  __select__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("select", true, false);
    element.dispatchEvent(event);
    return event;
};

//HTMLInputElement, HTMLSelectElement, HTMLTextAreaElement
var  __change__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("change", true, false);
    element.dispatchEvent(event);
    return event;
};

//HtmlFormElement
var __submit__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("submit", true, true);
    element.dispatchEvent(event);
    return event;
};

//HtmlFormElement
var  __reset__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("reset", false, false);
    element.dispatchEvent(event);
    return event;
};

//LABEL, INPUT, SELECT, TEXTAREA, and BUTTON
var __focus__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("focus", false, false);
    element.dispatchEvent(event);
    return event;
};

//LABEL, INPUT, SELECT, TEXTAREA, and BUTTON
var __blur__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("blur", false, false);
    element.dispatchEvent(event);
    return event;
};

//Window
var __resize__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("resize", true, false);
    element.dispatchEvent(event);
    return event;
};

//Window
var __scroll__ = function(element){
    var event = new Event('HTMLEvents');
    event.initEvent("scroll", true, false);
    element.dispatchEvent(event);
    return event;
};

/**
 * @name KeyboardEvents
 * @w3c:domlevel 2 
 * @uri http://www.w3.org/TR/2000/REC-DOM-Level-2-Events-20001113/events.html
 */
var KeyboardEvents= function(){};
KeyboardEvents.prototype = {
    onkeydown: function(event){
        __eval__(this.getAttribute('onkeydown')||'', this);
    },
    onkeypress: function(event){
        __eval__(this.getAttribute('onkeypress')||'', this);
    },
    onkeyup: function(event){
        __eval__(this.getAttribute('onkeyup')||'', this);
    }
};


var __registerKeyboardEventAttrs__ = function(elm){
    if(elm.hasAttribute('onkeydown')){ 
        elm.addEventListener('keydown', elm.onkeydown, false); 
    }
    if(elm.hasAttribute('onkeypress')){ 
        elm.addEventListener('keypress', elm.onkeypress, false); 
    }
    if(elm.hasAttribute('onkeyup')){ 
        elm.addEventListener('keyup', elm.onkeyup, false); 
    }
    return elm;
};

//HTMLInputElement, HTMLSelectElement, HTMLTextAreaElement
var  __keydown__ = function(element){
    var event = new Event('KeyboardEvents');
    event.initEvent("keydown", false, false);
    element.dispatchEvent(event);
};

//HTMLInputElement, HTMLSelectElement, HTMLTextAreaElement
var  __keypress__ = function(element){
    var event = new Event('KeyboardEvents');
    event.initEvent("keypress", false, false);
    element.dispatchEvent(event);
};

//HTMLInputElement, HTMLSelectElement, HTMLTextAreaElement
var  __keyup__ = function(element){
    var event = new Event('KeyboardEvents');
    event.initEvent("keyup", false, false);
    element.dispatchEvent(event);
};

/**
 * @name MaouseEvents
 * @w3c:domlevel 2 
 * @uri http://www.w3.org/TR/2000/REC-DOM-Level-2-Events-20001113/events.html
 */
var MouseEvents= function(){};
MouseEvents.prototype = {
    onclick: function(event){
        __eval__(this.getAttribute('onclick')||'', this);
    },
    ondblclick: function(event){
        __eval__(this.getAttribute('ondblclick')||'', this);
    },
    onmousedown: function(event){
        __eval__(this.getAttribute('onmousedown')||'', this);
    },
    onmousemove: function(event){
        __eval__(this.getAttribute('onmousemove')||'', this);
    },
    onmouseout: function(event){
        __eval__(this.getAttribute('onmouseout')||'', this);
    },
    onmouseover: function(event){
        __eval__(this.getAttribute('onmouseover')||'', this);
    },
    onmouseup: function(event){
        __eval__(this.getAttribute('onmouseup')||'', this);
    }  
};

var __registerMouseEventAttrs__ = function(elm){
    if(elm.hasAttribute('onclick')){ 
        elm.addEventListener('click', elm.onclick, false); 
    }
    if(elm.hasAttribute('ondblclick')){ 
        elm.addEventListener('dblclick', elm.ondblclick, false); 
    }
    if(elm.hasAttribute('onmousedown')){ 
        elm.addEventListener('mousedown', elm.onmousedown, false); 
    }
    if(elm.hasAttribute('onmousemove')){ 
        elm.addEventListener('mousemove', elm.onmousemove, false); 
    }
    if(elm.hasAttribute('onmouseout')){ 
        elm.addEventListener('mouseout', elm.onmouseout, false); 
    }
    if(elm.hasAttribute('onmouseover')){ 
        elm.addEventListener('mouseover', elm.onmouseover, false); 
    }
    if(elm.hasAttribute('onmouseup')){ 
        elm.addEventListener('mouseup', elm.onmouseup, false); 
    }
    return elm;
};


var  __click__ = function(element){
    var event = new Event('MouseEvents');
    event.initEvent("click", true, true, null, 0,
                0, 0, 0, 0, false, false, false, 
                false, null, null);
    element.dispatchEvent(event);
};
var  __mousedown__ = function(element){
    var event = new Event('MouseEvents');
    event.initEvent("mousedown", true, true, null, 0,
                0, 0, 0, 0, false, false, false, 
                false, null, null);
    element.dispatchEvent(event);
};
var  __mouseup__ = function(element){
    var event = new Event('MouseEvents');
    event.initEvent("mouseup", true, true, null, 0,
                0, 0, 0, 0, false, false, false, 
                false, null, null);
    element.dispatchEvent(event);
};
var  __mouseover__ = function(element){
    var event = new Event('MouseEvents');
    event.initEvent("mouseover", true, true, null, 0,
                0, 0, 0, 0, false, false, false, 
                false, null, null);
    element.dispatchEvent(event);
};
var  __mousemove__ = function(element){
    var event = new Event('MouseEvents');
    event.initEvent("mousemove", true, true, null, 0,
                0, 0, 0, 0, false, false, false, 
                false, null, null);
    element.dispatchEvent(event);
};
var  __mouseout__ = function(element){
    var event = new Event('MouseEvents');
    event.initEvent("mouseout", true, true, null, 0,
                0, 0, 0, 0, false, false, false, 
                false, null, null);
    element.dispatchEvent(event);
};

/**
* HTMLElement - DOM Level 2
*/
HTMLElement = function(ownerDocument) {
    Element.apply(this, arguments);
};
HTMLElement.prototype = new Element;
//TODO: Not sure where HTMLEvents belongs in the chain
//      but putting it here satisfies a lowest common 
//      denominator.
__extend__(HTMLElement.prototype, HTMLEvents.prototype);
__extend__(HTMLElement.prototype, {
	get className() { 
	    return this.getAttribute("class")||''; 
    },
	set className(value) { 
	    return this.setAttribute("class",__trim__(value)); 
    },
	get dir() { 
	    return this.getAttribute("dir")||"ltr"; 
    },
	set dir(val) { 
	    return this.setAttribute("dir",val); 
    },
	get id(){  
	    return this.getAttribute('id'); 
    },
	set id(id){  
	    this.setAttribute('id', id); 
    },
	get innerHTML(){  
	    var ret = "",
            i;
        
        // create string containing the concatenation of the string 
        // values of each child
        for (i=0; i < this.childNodes.length; i++) {
            if(this.childNodes[i]){
                if(this.childNodes[i].xhtml){
                    ret += this.childNodes[i].xhtml;
                }else if(this.childNodes[i].nodeType == Node.TEXT_NODE && i>0 && 
                    this.childNodes[i-1].nodeType == Node.TEXT_NODE){
                    //add a single space between adjacent text nodes
                    ret += " "+this.childNodes[i].xml;
                }else{
                    ret += this.childNodes[i].xml;
                }
            }
        }
        return ret;
    },
	get lang() { 
	    return this.getAttribute("lang"); 
    },
	set lang(val) { 
	    return this.setAttribute("lang",val); 
    },
	get offsetHeight(){
	    return Number((this.style["height"]||'').replace("px",""));
	},
	get offsetWidth(){
	    return Number((this.style["width"]||'').replace("px",""));
	},
	offsetLeft: 0,
	offsetRight: 0,
	get offsetParent(){
	    /* TODO */
	    return;
    },
	set offsetParent(element){
	    /* TODO */
	    return;
    },
	scrollHeight: 0,
	scrollWidth: 0,
	scrollLeft: 0, 
	scrollRight: 0,
	get style(){
        return this.getAttribute('style')||'';
	},
	get title() { 
	    return this.getAttribute("title"); 
    },
	set title(value) { 
	    return this.setAttribute("title", value);
    },
	get tabIndex(){
        var tabindex = this.getAttribute('tabindex');
        if(tabindex!==null){
            return Number(tabindex);
        } else {
            return 0;
        }
    },
    set tabIndex(value){
        if(value===undefined||value===null)
            value = 0;
        this.setAttribute('tabindex',Number(value));
    },
	get outerHTML(){ 
        //Not in the specs but I'll leave it here for now.
	    return this.xhtml; 
    },
    scrollIntoView: function(){
        /*TODO*/
        return;
    },
    toString: function(){
        return '[object HTMLElement]';
    },
    get xhtml() {
        // HTMLDocument.xhtml is non-standard
        // This is exactly like Document.xml except the tagName has to be 
        // lower cased.  I dont like to duplicate this but its really not
        // a simple work around between xml and html serialization via
        // XMLSerializer (which uppercases html tags) and innerHTML (which
        // lowercases tags)
        
        var ret = "",
            ns = "",
            name = (this.tagName+"").toLowerCase(),
            attrs,
            attrstring = "",
            i;

        // serialize namespace declarations
        if (this.namespaceURI){
            if((this === this.ownerDocument.documentElement) ||
                (!this.parentNode)||
                (this.parentNode && 
                (this.parentNode.namespaceURI !== this.namespaceURI)))
                ns = ' xmlns'+(this.prefix?(':'+this.prefix):'')+
                    '="'+this.namespaceURI+'"';
        }
        
        // serialize Attribute declarations
        attrs = this.attributes;
        for(i=0;i< attrs.length;i++){
            attrstring += " "+attrs[i].name+'="'+attrs[i].xml+'"';
        }
        
        if(this.hasChildNodes()){
            // serialize this Element
            ret += "<" + name + ns + attrstring +">";
            for(i=0;i< this.childNodes.length;i++){
                ret += this.childNodes[i].xhtml ?
                           this.childNodes[i].xhtml : 
                           this.childNodes[i].xml
            }
            ret += "</" + name + ">";
        }else{
            switch(name){
                case 'script':
                    ret += "<" + name + ns + attrstring +"></"+name+">";
                default:
                    ret += "<" + name + ns + attrstring +"/>";
            }
        }
        
        return ret;
    }
});


/*
* HTMLCollection - DOM Level 2
* Implementation Provided by Steven Wood
*/
HTMLCollection = function(nodelist, type){

    __setArray__(this, []);
    for (var i=0; i<nodelist.length; i++) {
        this[i] = nodelist[i];
        if('name' in nodelist[i]){
            this[nodelist[i].name] = nodelist[i];
        }
    }
    
    this.length = nodelist.length;

}

HTMLCollection.prototype = {
        
    item : function (idx) {
        var ret = null;
        if ((idx >= 0) && (idx < this.length)) { 
            ret = this[idx];                    
        }
    
        return ret;   
    },
    
    namedItem : function (name) {
        if(name in this){
            return this[name];
        }
        return null;
    }
};




	/*
 *  a set of convenience classes to centralize implementation of
 * properties and methods across multiple in-form elements
 *
 *  the hierarchy of related HTML elements and their members is as follows:
 *
 *
 *    HTMLInputCommon:  common to all elements
 *       .form
 *
 *    <legend>
 *          [common plus:]
 *       .align
 *
 *    <fieldset>
 *          [identical to "legend" plus:]
 *       .margin
 *
 *
 *  ****
 *
 *    <label>
 *          [common plus:]
 *       .dataFormatAs
 *       .htmlFor
 *       [plus data properties]
 *
 *    <option>
 *          [common plus:]
 *       .defaultSelected
 *       .index
 *       .label
 *       .selected
 *       .text
 *       .value   // unique implementation, not duplicated
 *
 *  ****
 *
 *    HTMLTypeValueInputs:  common to remaining elements
 *          [common plus:]
 *       .name
 *       .type
 *       .value
 *       [plus data properties]
 *
 *
 *    <select>
 *       .length
 *       .multiple
 *       .options[]
 *       .selectedIndex
 *       .add()
 *       .remove()
 *       .item()                                       // unimplemented
 *       .namedItem()                                  // unimplemented
 *       [plus ".onchange"]
 *       [plus focus events]
 *       [plus data properties]
 *       [plus ".size"]
 *
 *    <button>
 *       .dataFormatAs   // duplicated from above, oh well....
 *       [plus ".status", ".createTextRange()"]
 *
 *  ****
 *
 *    HTMLInputAreaCommon:  common to remaining elements
 *       .defaultValue
 *       .readOnly
 *       .handleEvent()                                // unimplemented
 *       .select()
 *       .onselect
 *       [plus ".size"]
 *       [plus ".status", ".createTextRange()"]
 *       [plus focus events]
 *       [plus ".onchange"]
 *
 *    <textarea>
 *       .cols
 *       .rows
 *       .wrap                                         // unimplemented
 *       .onscroll                                     // unimplemented
 *
 *    <input>
 *       .alt
 *       .accept                                       // unimplemented
 *       .checked
 *       .complete                                     // unimplemented
 *       .defaultChecked
 *       .dynsrc                                       // unimplemented
 *       .height
 *       .hspace                                       // unimplemented
 *       .indeterminate                                // unimplemented
 *       .loop                                         // unimplemented
 *       .lowsrc                                       // unimplemented
 *       .maxLength
 *       .src
 *       .start                                        // unimplemented
 *       .useMap
 *       .vspace                                       // unimplemented
 *       .width
 *       .onclick
 *       [plus ".size"]
 *       [plus ".status", ".createTextRange()"]

 *    [data properties]                                // unimplemented
 *       .dataFld
 *       .dataSrc

 *    [status stuff]                                   // unimplemented
 *       .status
 *       .createTextRange()

 *    [focus events]
 *       .onblur
 *       .onfocus

 */



var inputElements_dataProperties = {};
var inputElements_status = {};

var inputElements_onchange = {
    onchange: function(event){
        __eval__(this.getAttribute('onchange')||'', this)
    }
};

var inputElements_size = {
    get size(){
        return Number(this.getAttribute('size'));
    },
    set size(value){
        this.setAttribute('size',value);
    }
};

var inputElements_focusEvents = {
    blur: function(){
        __blur__(this);

        if (this._oldValue != this.value){
            var event = document.createEvent("HTMLEvents");
            event.initEvent("change", true, true);
            this.dispatchEvent( event );
        }
    },
    focus: function(){
        __focus__(this);
        this._oldValue = this.value;
    }
};


/*
* HTMLInputCommon - convenience class, not DOM
*/
var HTMLInputCommon = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLInputCommon.prototype = new HTMLElement;
__extend__(HTMLInputCommon.prototype, {
    get form(){
        var parent = this.parentNode;
        while(parent.nodeName.toLowerCase() != 'form'){
            parent = parent.parentNode;
        }
        return parent;
    },
    get accessKey(){
        return this.getAttribute('accesskey');
    },
    set accessKey(value){
        this.setAttribute('accesskey',value);
    },
    get access(){
        return this.getAttribute('access');
    },
    set access(value){
        this.setAttribute('access', value);
    },
    get disabled(){
        return (this.getAttribute('disabled')=='disabled');
    },
    set disabled(value){
        this.setAttribute('disabled', (value ? 'disabled' :''));
    }
});




/*
* HTMLTypeValueInputs - convenience class, not DOM
*/
var HTMLTypeValueInputs = function(ownerDocument) {
    
    HTMLInputCommon.apply(this, arguments);

    this._oldValue = "";
};
HTMLTypeValueInputs.prototype = new HTMLInputCommon;
__extend__(HTMLTypeValueInputs.prototype, inputElements_size);
__extend__(HTMLTypeValueInputs.prototype, inputElements_status);
__extend__(HTMLTypeValueInputs.prototype, inputElements_dataProperties);
__extend__(HTMLTypeValueInputs.prototype, {
    get defaultValue(){
        return this.getAttribute('defaultValue');
    },
    set defaultValue(value){
        this.setAttribute('defaultValue', value);
    },
    get name(){
        return this.getAttribute('name')||'';
    },
    set name(value){
        this.setAttribute('name',value);
    },
    get type(){
        return this.getAttribute('type');
    },
    set type(type){
        return this.setAttribute('type', type);
    },
    get value(){
        return this.getAttribute('value')||'';
    },
    set value(newValue){
        this.setAttribute('value',newValue);
    },
    setAttribute: function(name, value){
        if(name == 'value' && !this.defaultValue){
            this.defaultValue = value;
        }
        HTMLElement.prototype.setAttribute.apply(this, [name, value]);
    }
});


/*
* HTMLInputAreaCommon - convenience class, not DOM
*/
var HTMLInputAreaCommon = function(ownerDocument) {
    HTMLTypeValueInputs.apply(this, arguments);
};
HTMLInputAreaCommon.prototype = new HTMLTypeValueInputs;
__extend__(HTMLInputAreaCommon.prototype, inputElements_focusEvents);
__extend__(HTMLInputAreaCommon.prototype, inputElements_onchange);
__extend__(HTMLInputAreaCommon.prototype, {
    get readOnly(){
        return (this.getAttribute('readonly')=='readonly');
    },
    set readOnly(value){
        this.setAttribute('readonly', (value ? 'readonly' :''));
    },
    select:function(){
        __select__(this);

    }
});


/**
 * HTMLAnchorElement - DOM Level 2
 */
HTMLAnchorElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLAnchorElement.prototype = new HTMLElement;
__extend__(HTMLAnchorElement.prototype, {
	get accessKey() { 
	    return this.getAttribute("accesskey")||''; 
    },
	set accessKey(val) { 
	    return this.setAttribute("accesskey",val); 
    },
	get charset() { 
	    return this.getAttribute("charset")||''; 
    },
	set charset(val) { 
	    return this.setAttribute("charset",val); 
    },
	get coords() { 
	    return this.getAttribute("coords")||''; 
    },
	set coords(val) { 
	    return this.setAttribute("coords",val);
    },
	get href() { 
        var location = this.ownerDocument.location+'';
	    return (location?location.substring(0, location.lastIndexOf('/')):'')+
            (this.getAttribute("href")||'');
    },
	set href(val) { 
	    return this.setAttribute("href",val);
    },
	get hreflang() { 
	    return this.getAttribute("hreflang")||'';
    },
	set hreflang(val) { 
	    this.setAttribute("hreflang",val);
    },
	get name() { 
	    return this.getAttribute("name")||'';
    },
	set name(val) { 
	    this.setAttribute("name",val);
    },
	get rel() { 
	    return this.getAttribute("rel")||''; 
    },
	set rel(val) { 
	    return this.setAttribute("rel", val); 
    },
	get rev() { 
	    return this.getAttribute("rev")||'';
    },
	set rev(val) { 
	    return this.setAttribute("rev",val);
    },
	get shape() { 
	    return this.getAttribute("shape")||'';
    },
	set shape(val) { 
	    return this.setAttribute("shape",val);
    },
	get target() { 
	    return this.getAttribute("target")||'';
    },
	set target(val) { 
	    return this.setAttribute("target",val);
    },
	get type() { 
	    return this.getAttribute("type")||'';
    },
	set type(val) { 
	    return this.setAttribute("type",val);
    },
	blur:function(){
	    __blur__(this);
    },
	focus:function(){
	    __focus__(this);
    },
    toString: function(){
        return '[object HTMLAnchorElement]';
    }
});

/* 
 * HTMLAreaElement - DOM Level 2
 */
HTMLAreaElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLAreaElement.prototype = new HTMLElement;
__extend__(HTMLAreaElement.prototype, {
    get accessKey(){
        return this.getAttribute('accesskey');
    },
    set accessKey(value){
        this.setAttribute('accesskey',value);
    },
    get alt(){
        return this.getAttribute('alt');
    },
    set alt(value){
        this.setAttribute('alt',value);
    },
    get coords(){
        return this.getAttribute('coords');
    },
    set coords(value){
        this.setAttribute('coords',value);
    },
    get href(){
        return this.getAttribute('href');
    },
    set href(value){
        this.setAttribute('href',value);
    },
    get noHref(){
        return this.hasAttribute('href');
    },
    get shape(){
        //TODO
        return 0;
    },
    /*get tabIndex(){
        return this.getAttribute('tabindex');
    },
    set tabIndex(value){
        this.setAttribute('tabindex',value);
    },*/
    get target(){
        return this.getAttribute('target');
    },
    set target(value){
        this.setAttribute('target',value);
    },
    toString: function(){
        return '[object HTMLAreaElement]';
    }
});

			
/* 
* HTMLBaseElement - DOM Level 2
*/
HTMLBaseElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLBaseElement.prototype = new HTMLElement;
__extend__(HTMLBaseElement.prototype, {
    get href(){
        return this.getAttribute('href');
    },
    set href(value){
        this.setAttribute('href',value);
    },
    get target(){
        return this.getAttribute('target');
    },
    set target(value){
        this.setAttribute('target',value);
    }
});

	
/* 
* HTMLQuoteElement - DOM Level 2
*/
HTMLQuoteElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
__extend__(HTMLQuoteElement.prototype, HTMLElement.prototype);
__extend__(HTMLQuoteElement.prototype, {
    get cite(){
        return this.getAttribute('cite');
    },
    set cite(value){
        this.setAttribute('cite',value);
    }
});

/*
 * HTMLBodyElement - DOM Level 2
 */
HTMLBodyElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLBodyElement.prototype = new HTMLElement;
__extend__(HTMLBodyElement.prototype, {
    onload: function(event){
        __eval__(this.getAttribute('onload')||'', this)
    },
    onunload: function(event){
        __eval__(this.getAttribute('onunload')||'', this)
    }
});


/*
 * HTMLButtonElement - DOM Level 2
 */
HTMLButtonElement = function(ownerDocument) {
    HTMLTypeValueInputs.apply(this, arguments);
};
HTMLButtonElement.prototype = new HTMLTypeValueInputs;
__extend__(HTMLButtonElement.prototype, inputElements_status);
__extend__(HTMLButtonElement.prototype, {
    get dataFormatAs(){
        return this.getAttribute('dataFormatAs');
    },
    set dataFormatAs(value){
        this.setAttribute('dataFormatAs',value);
    }
});


/* 
* HTMLCanvasElement - DOM Level 2
*/
HTMLCanvasElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLCanvasElement.prototype = new HTMLElement;
__extend__(HTMLCanvasElement.prototype, {

    // TODO: obviously a big challenge

});

	
/* 
* HTMLTableColElement - DOM Level 2
*/
HTMLTableColElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLTableColElement.prototype = new HTMLElement;
__extend__(HTMLTableColElement.prototype, {
    get align(){
        return this.getAttribute('align');
    },
    set align(value){
        this.setAttribute('align', value);
    },
    get ch(){
        return this.getAttribute('ch');
    },
    set ch(value){
        this.setAttribute('ch', value);
    },
    get chOff(){
        return this.getAttribute('ch');
    },
    set chOff(value){
        this.setAttribute('ch', value);
    },
    get span(){
        return this.getAttribute('span');
    },
    set span(value){
        this.setAttribute('span', value);
    },
    get vAlign(){
        return this.getAttribute('valign');
    },
    set vAlign(value){
        this.setAttribute('valign', value);
    },
    get width(){
        return this.getAttribute('width');
    },
    set width(value){
        this.setAttribute('width', value);
    }
});


/* 
* HTMLModElement - DOM Level 2
*/
HTMLModElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLModElement.prototype = new HTMLElement;
__extend__(HTMLModElement.prototype, {
    get cite(){
        return this.getAttribute('cite');
    },
    set cite(value){
        this.setAttribute('cite', value);
    },
    get dateTime(){
        return this.getAttribute('datetime');
    },
    set dateTime(value){
        this.setAttribute('datetime', value);
    }
});

/*
* HTMLDivElement - DOM Level 2
*/
HTMLDivElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLDivElement.prototype = new HTMLElement;
__extend__(HTMLDivElement.prototype, {
    get align(){
        return this.getAttribute('align') || 'left';
    },
    set align(value){
        this.setAttribute('align', value);
    }
});


/**
 * HTMLLegendElement - DOM Level 2
 */
HTMLLegendElement = function(ownerDocument) {
    HTMLInputCommon.apply(this, arguments);
};
HTMLLegendElement.prototype = new HTMLInputCommon;
__extend__(HTMLLegendElement.prototype, {
    get align(){
        return this.getAttribute('align');
    },
    set align(value){
        this.setAttribute('align',value);
    }
});


/*
 * HTMLFieldSetElement - DOM Level 2
 */
HTMLFieldSetElement = function(ownerDocument) {
    HTMLLegendElement.apply(this, arguments);
};
HTMLFieldSetElement.prototype = new HTMLLegendElement;
__extend__(HTMLFieldSetElement.prototype, {
    get margin(){
        return this.getAttribute('margin');
    },
    set margin(value){
        this.setAttribute('margin',value);
    }
});

/* 
 * HTMLFormElement - DOM Level 2
 */
HTMLFormElement = function(ownerDocument){
    HTMLElement.apply(this, arguments);
    //TODO: on __elementPopped__ from the parser
    //      we need to determine all the forms default 
    //      values
};
HTMLFormElement.prototype = new HTMLElement;
__extend__(HTMLFormElement.prototype,{
    get acceptCharset(){ 
        return this.getAttribute('accept-charset');
    },
    set acceptCharset(acceptCharset){
        this.setAttribute('accept-charset', acceptCharset);
        
    },
    get action(){
        return this.getAttribute('action');
        
    },
    set action(action){
        this.setAttribute('action', action);
        
    },
    get elements() {
        return this.getElementsByTagName("*");
        
    },
    get enctype(){
        return this.getAttribute('enctype');
        
    },
    set enctype(enctype){
        this.setAttribute('enctype', enctype);
        
    },
    get length() {
        return this.elements.length;
        
    },
    get method(){
        return this.getAttribute('method');
        
    },
    set method(method){
        this.setAttribute('method', method);
        
    },
	get name() {
	    return this.getAttribute("name"); 
	    
    },
	set name(val) { 
	    return this.setAttribute("name",val); 
	    
    },
	get target() { 
	    return this.getAttribute("target"); 
	    
    },
	set target(val) { 
	    return this.setAttribute("target",val); 
	    
    },
    toString: function(){
        return '[object HTMLFormElement]';
    },
	submit:function(){
        //TODO: this needs to perform the form inputs serialization
        //      and submission
        //  DONE: see xhr/form.js
	    var event = __submit__(this);
	    
    },
	reset:function(){
        //TODO: this needs to reset all values specified in the form
        //      to those which where set as defaults
	    __reset__(this);
	    
    },
    onsubmit:HTMLEvents.prototype.onsubmit,
    onreset: HTMLEvents.prototype.onreset
});

/** 
 * HTMLFrameElement - DOM Level 2
 */
HTMLFrameElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
    // this is normally a getter but we need to be
    // able to set it to correctly emulate behavior
    this.contentDocument = null;
    this.contentWindow = null;
};
HTMLFrameElement.prototype = new HTMLElement;
__extend__(HTMLFrameElement.prototype, {
    
    get frameBorder(){
        return this.getAttribute('border')||"";
    },
    set frameBorder(value){
        this.setAttribute('border', value);
    },
    get longDesc(){
        return this.getAttribute('longdesc')||"";
    },
    set longDesc(value){
        this.setAttribute('longdesc', value);
    },
    get marginHeight(){
        return this.getAttribute('marginheight')||"";
    },
    set marginHeight(value){
        this.setAttribute('marginheight', value);
    },
    get marginWidth(){
        return this.getAttribute('marginwidth')||"";
    },
    set marginWidth(value){
        this.setAttribute('marginwidth', value);
    },
    get name(){
        return this.getAttribute('name')||"";
    },
    set name(value){
        this.setAttribute('name', value);
    },
    get noResize(){
        return this.getAttribute('noresize')||false;
    },
    set noResize(value){
        this.setAttribute('noresize', value);
    },
    get scrolling(){
        return this.getAttribute('scrolling')||"";
    },
    set scrolling(value){
        this.setAttribute('scrolling', value);
    },
    get src(){
        return this.getAttribute('src')||"";
    },
    set src(value){
        this.setAttribute('src', value);
    },
    toString: function(){
        return '[object HTMLFrameElement]';
    },
    onload: HTMLEvents.prototype.onload
});

/** 
 * HTMLFrameSetElement - DOM Level 2
 */
HTMLFrameSetElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLFrameSetElement.prototype = new HTMLElement;
__extend__(HTMLFrameSetElement.prototype, {
    get cols(){
        return this.getAttribute('cols');
    },
    set cols(value){
        this.setAttribute('cols', value);
    },
    get rows(){
        return this.getAttribute('rows');
    },
    set rows(value){
        this.setAttribute('rows', value);
    }
});

/** 
 * HTMLHeadElement - DOM Level 2
 */
HTMLHeadElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLHeadElement.prototype = new HTMLElement;
__extend__(HTMLHeadElement.prototype, {
    get profile(){
        return this.getAttribute('profile');
    },
    set profile(value){
        this.setAttribute('profile', value);
    },
    //we override this so we can apply browser behavior specific to head children
    //like loading scripts
    appendChild : function(newChild) {
        var newChild = HTMLElement.prototype.appendChild.apply(this,[newChild]);
        //TODO: evaluate scripts which are appended to the head
        //__evalScript__(newChild);
        return newChild;
    },
    insertBefore : function(newChild, refChild) {
        var newChild = HTMLElement.prototype.insertBefore.apply(this,[newChild]);
        //TODO: evaluate scripts which are appended to the head
        //__evalScript__(newChild);
        return newChild;
    },
    toString: function(){
        return '[object HTMLHeadElement]';
    },
});


/* 
 * HTMLIFrameElement - DOM Level 2
 */
HTMLIFrameElement = function(ownerDocument) {
    HTMLFrameElement.apply(this, arguments);
};
HTMLIFrameElement.prototype = new HTMLFrameElement;
__extend__(HTMLIFrameElement.prototype, {
	get height() { 
	    return this.getAttribute("height") || ""; 
    },
	set height(val) { 
	    return this.setAttribute("height",val); 
    },
	get width() { 
	    return this.getAttribute("width") || ""; 
    },
	set width(val) { 
	    return this.setAttribute("width",val); 
    },
    toString: function(){
        return '[object HTMLIFrameElement]';
    }
});
	
/** 
 * HTMLImageElement - DOM Level 2
 */
HTMLImageElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLImageElement.prototype = new HTMLElement;
__extend__(HTMLImageElement.prototype, {
    get alt(){
        return this.getAttribute('alt');
    },
    set alt(value){
        this.setAttribute('alt', value);
    },
    get height(){
        return this.getAttribute('height');
    },
    set height(value){
        this.setAttribute('height', value);
    },
    get isMap(){
        return this.hasAttribute('map');
    },
    set useMap(value){
        this.setAttribute('map', value);
    },
    get longDesc(){
        return this.getAttribute('longdesc');
    },
    set longDesc(value){
        this.setAttribute('longdesc', value);
    },
    get name(){
        return this.getAttribute('name');
    },
    set name(value){
        this.setAttribute('name', value);
    },
    get src(){
        return this.getAttribute('src');
    },
    set src(value){
        this.setAttribute('src', value);

        var event = document.createEvent();
        event.initEvent("load");
        this.dispatchEvent( event, false );
    },
    get width(){
        return this.getAttribute('width');
    },
    set width(value){
        this.setAttribute('width', value);
    },
    onload: function(event){
        __eval__(this.getAttribute('onload')||'', this)
    }
});
/**
 * HTMLInputElement - DOM Level 2
 */
HTMLInputElement = function(ownerDocument) {
    HTMLInputAreaCommon.apply(this, arguments);
};
HTMLInputElement.prototype = new HTMLInputAreaCommon;
__extend__(HTMLInputElement.prototype, {
    get alt(){
        return this.getAttribute('alt');
    },
    set alt(value){
        this.setAttribute('alt', value);
    },
    get checked(){
        return (this.getAttribute('checked')=='checked');
    },
    set checked(value){
        this.setAttribute('checked', (value ? 'checked' :''));
    },
    get defaultChecked(){
        return this.getAttribute('defaultChecked');
    },
    get height(){
        return this.getAttribute('height');
    },
    set height(value){
        this.setAttribute('height',value);
    },
    get maxLength(){
        return Number(this.getAttribute('maxlength')||'0');
    },
    set maxLength(value){
        this.setAttribute('maxlength', value);
    },
    get src(){
        return this.getAttribute('src');
    },
    set src(value){
        this.setAttribute('src', value);
    },
    get useMap(){
        return this.getAttribute('map');
    },
    get width(){
        return this.getAttribute('width');
    },
    set width(value){
        this.setAttribute('width',value);
    },
    click:function(){
        __click__(this);
    }
});



/** 
 * HTMLLabelElement - DOM Level 2
 */
HTMLLabelElement = function(ownerDocument) {
    HTMLInputCommon.apply(this, arguments);
};
HTMLLabelElement.prototype = new HTMLInputCommon;
__extend__(HTMLLabelElement.prototype, inputElements_dataProperties);
__extend__(HTMLLabelElement.prototype, {
    get htmlFor(){
        return this.getAttribute('for');
    },
    set htmlFor(value){
        this.setAttribute('for',value);
    },
    get dataFormatAs(){
        return this.getAttribute('dataFormatAs');
    },
    set dataFormatAs(value){
        this.setAttribute('dataFormatAs',value);
    }
});


/* 
* HTMLLinkElement - DOM Level 2
*/
HTMLLinkElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLLinkElement.prototype = new HTMLElement;
__extend__(HTMLLinkElement.prototype, {
    get disabled(){
        return this.getAttribute('disabled');
    },
    set disabled(value){
        this.setAttribute('disabled',value);
    },
    get charset(){
        return this.getAttribute('charset');
    },
    set charset(value){
        this.setAttribute('charset',value);
    },
    get href(){
        return this.getAttribute('href');
    },
    set href(value){
        this.setAttribute('href',value);
    },
    get hreflang(){
        return this.getAttribute('hreflang');
    },
    set hreflang(value){
        this.setAttribute('hreflang',value);
    },
    get media(){
        return this.getAttribute('media');
    },
    set media(value){
        this.setAttribute('media',value);
    },
    get rel(){
        return this.getAttribute('rel');
    },
    set rel(value){
        this.setAttribute('rel',value);
    },
    get rev(){
        return this.getAttribute('rev');
    },
    set rev(value){
        this.setAttribute('rev',value);
    },
    get target(){
        return this.getAttribute('target');
    },
    set target(value){
        this.setAttribute('target',value);
    },
    get type(){
        return this.getAttribute('type');
    },
    set type(value){
        this.setAttribute('type',value);
    },
    onload: function(event){
        __eval__(this.getAttribute('onload')||'', this)
    }
});


/** 
 * HTMLMapElement - DOM Level 2
 */
HTMLMapElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLMapElement.prototype = new HTMLElement;
__extend__(HTMLMapElement.prototype, {
    get areas(){
        return this.getElementsByTagName('area');
    },
    get name(){
        return this.getAttribute('name');
    },
    set name(value){
        this.setAttribute('name',value);
    }
});

/** 
 * HTMLMetaElement - DOM Level 2
 */
HTMLMetaElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLMetaElement.prototype = new HTMLElement;
__extend__(HTMLMetaElement.prototype, {
    get content(){
        return this.getAttribute('content');
    },
    set content(value){
        this.setAttribute('content',value);
    },
    get httpEquiv(){
        return this.getAttribute('http-equiv');
    },
    set httpEquiv(value){
        this.setAttribute('http-equiv',value);
    },
    get name(){
        return this.getAttribute('name');
    },
    set name(value){
        this.setAttribute('name',value);
    },
    get scheme(){
        return this.getAttribute('scheme');
    },
    set scheme(value){
        this.setAttribute('scheme',value);
    }
});


/**
 * HTMLObjectElement - DOM Level 2
 */
HTMLObjectElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLObjectElement.prototype = new HTMLElement;
__extend__(HTMLObjectElement.prototype, {
    get code(){
        return this.getAttribute('code');
    },
    set code(value){
        this.setAttribute('code',value);
    },
    get archive(){
        return this.getAttribute('archive');
    },
    set archive(value){
        this.setAttribute('archive',value);
    },
    get codeBase(){
        return this.getAttribute('codebase');
    },
    set codeBase(value){
        this.setAttribute('codebase',value);
    },
    get codeType(){
        return this.getAttribute('codetype');
    },
    set codeType(value){
        this.setAttribute('codetype',value);
    },
    get data(){
        return this.getAttribute('data');
    },
    set data(value){
        this.setAttribute('data',value);
    },
    get declare(){
        return this.getAttribute('declare');
    },
    set declare(value){
        this.setAttribute('declare',value);
    },
    get height(){
        return this.getAttribute('height');
    },
    set height(value){
        this.setAttribute('height',value);
    },
    get standby(){
        return this.getAttribute('standby');
    },
    set standby(value){
        this.setAttribute('standby',value);
    },
    /*get tabIndex(){
        return this.getAttribute('tabindex');
    },
    set tabIndex(value){
        this.setAttribute('tabindex',value);
    },*/
    get type(){
        return this.getAttribute('type');
    },
    set type(value){
        this.setAttribute('type',value);
    },
    get useMap(){
        return this.getAttribute('usemap');
    },
    set useMap(value){
        this.setAttribute('usemap',value);
    },
    get width(){
        return this.getAttribute('width');
    },
    set width(value){
        this.setAttribute('width',value);
    },
    get contentDocument(){
        return this.ownerDocument;
    }
});

			
/**
 * HTMLOptGroupElement - DOM Level 2
 */
HTMLOptGroupElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLOptGroupElement.prototype = new HTMLElement;
__extend__(HTMLOptGroupElement.prototype, {
    get disabled(){
        return this.getAttribute('disabled');
    },
    set disabled(value){
        this.setAttribute('disabled',value);
    },
    get label(){
        return this.getAttribute('label');
    },
    set label(value){
        this.setAttribute('label',value);
    },
});
	
/**
 * HTMLOptionElement - DOM Level 2
 */
HTMLOptionElement = function(ownerDocument) {
    HTMLInputCommon.apply(this, arguments);
};
HTMLOptionElement.prototype = new HTMLInputCommon;
__extend__(HTMLOptionElement.prototype, {
    get defaultSelected(){
        return this.getAttribute('defaultSelected');
    },
    set defaultSelected(value){
        this.setAttribute('defaultSelected',value);
    },
    get index(){
        var options = this.parent.childNodes;
        for(var i; i<options.length;i++){
            if(this == options[i])
                return i;
        }
        return -1;
    },
    get label(){
        return this.getAttribute('label');
    },
    set label(value){
        this.setAttribute('label',value);
    },
    get selected(){
        return (this.getAttribute('selected')=='selected');
    },
    set selected(value){
        if(this.defaultSelected===null && this.selected!==null){
            this.defaultSelected = this.selected;
        }
        var selectedValue = (value ? 'selected' : '');
        if (this.getAttribute('selected') == selectedValue) {
            // prevent inifinite loops (option's selected modifies 
            // select's value which modifies option's selected)
            return;
        }
        this.setAttribute('selected', selectedValue);
        if (value) {
            // set select's value to this option's value (this also 
            // unselects previously selected value)
            this.parentNode.value = this.value;
        } else {
            // if no other option is selected, select the first option in the select
            var i, anythingSelected;
            for (i=0; i<this.parentNode.options.length; i++) {
                if (this.parentNode.options[i].selected) {
                    anythingSelected = true;
                    break;
                }
            }
            if (!anythingSelected) {
                this.parentNode.value = this.parentNode.options[0].value;
            }
        }

    },
    get text(){
         return ((this.nodeValue === null) ||  (this.nodeValue ===undefined)) ?
             this.innerHTML :
             this.nodeValue;
    },
    get value(){
        return ((this.getAttribute('value') === undefined) || (this.getAttribute('value') === null)) ?
            this.text :
            this.getAttribute('value');
    },
    set value(value){
        this.setAttribute('value',value);
    }
});


/*
* HTMLParagraphElement - DOM Level 2
*/
HTMLParagraphElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLParagraphElement.prototype = new HTMLElement;
__extend__(HTMLParagraphElement.prototype, {
    toString: function(){
        return '[object HTMLParagraphElement]';
    }
});


/** 
 * HTMLParamElement - DOM Level 2
 */
HTMLParamElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLParamElement.prototype = new HTMLElement;
__extend__(HTMLParamElement.prototype, {
    get name(){
        return this.getAttribute('name');
    },
    set name(value){
        this.setAttribute('name',value);
    },
    get type(){
        return this.getAttribute('type');
    },
    set type(value){
        this.setAttribute('type',value);
    },
    get value(){
        return this.getAttribute('value');
    },
    set value(value){
        this.setAttribute('value',value);
    },
    get valueType(){
        return this.getAttribute('valuetype');
    },
    set valueType(value){
        this.setAttribute('valuetype',value);
    },
});

		
/** 
 * HTMLScriptElement - DOM Level 2
 */
HTMLScriptElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLScriptElement.prototype = new HTMLElement;
__extend__(HTMLScriptElement.prototype, {
    get text(){
        // text of script is in a child node of the element
        // scripts with < operator must be in a CDATA node
        for (var i=0; i<this.childNodes.length; i++) {
            if (this.childNodes[i].nodeType == Node.CDATA_SECTION_NODE) {
                return this.childNodes[i].nodeValue;
            }
        } 
        // otherwise there will be a text node containing the script
        if (this.childNodes[0] && this.childNodes[0].nodeType == Node.TEXT_NODE) {
            return this.childNodes[0].nodeValue;
 		}
        return this.nodeValue;

    },
    set text(value){
        this.nodeValue = value;
        Envjs.loadInlineScript(this);
    },
    get htmlFor(){
        return this.getAttribute('for');
    },
    set htmlFor(value){
        this.setAttribute('for',value);
    },
    get event(){
        return this.getAttribute('event');
    },
    set event(value){
        this.setAttribute('event',value);
    },
    get charset(){
        return this.getAttribute('charset');
    },
    set charset(value){
        this.setAttribute('charset',value);
    },
    get defer(){
        return this.getAttribute('defer');
    },
    set defer(value){
        this.setAttribute('defer',value);
    },
    get src(){
        return this.getAttribute('src');
    },
    set src(value){
        this.setAttribute('src',value);
    },
    get type(){
        return this.getAttribute('type');
    },
    set type(value){
        this.setAttribute('type',value);
    },
    onload: HTMLEvents.prototype.onload,
    onerror: HTMLEvents.prototype.onerror
});


/**
 * HTMLSelectElement - DOM Level 2
 */
HTMLSelectElement = function(ownerDocument) {
    HTMLTypeValueInputs.apply(this, arguments);

    this._oldIndex = -1;
};
HTMLSelectElement.prototype = new HTMLTypeValueInputs;
__extend__(HTMLSelectElement.prototype, inputElements_dataProperties);
__extend__(HTMLButtonElement.prototype, inputElements_size);
__extend__(HTMLSelectElement.prototype, inputElements_onchange);
__extend__(HTMLSelectElement.prototype, inputElements_focusEvents);
__extend__(HTMLSelectElement.prototype, {

    // over-ride the value setter in HTMLTypeValueInputs
    set value(newValue) {
        var options = this.options,
            i, index;
        for (i=0; i<options.length; i++) {
            if (options[i].value == newValue) {
                index = i;
                break;
            }
        }
        if (index !== undefined) {
            this.setAttribute('value', newValue);
            this.selectedIndex = index;
        }
    },
    get value() {
        var value = this.getAttribute('value');
        if (value === undefined || value === null) {
            var index = this.selectedIndex;
            return (index != -1) ? this.options[index].value : "";
        } else {
            return value;
        }
    },


    get length(){
        return this.options.length;
    },
    get multiple(){
        return this.getAttribute('multiple');
    },
    set multiple(value){
        this.setAttribute('multiple',value);
    },
    get options(){
        return this.getElementsByTagName('option');
    },
    get selectedIndex(){
        var options = this.options;
        for(var i=0;i<options.length;i++){
            if(options[i].selected){
                return i;
            }
        };
        return -1;
    },
    
    set selectedIndex(value) {
        var i;
        for (i=0; i<this.options.length; i++) {
            this.options[i].selected = (i == Number(value));
        }
    },
    get type(){
        var type = this.getAttribute('type');
        return type?type:'select-one';
    },

    add : function(){
        __add__(this);
    },
    remove : function(){
        __remove__(this);
    }
});



/** 
 * HTMLStyleElement - DOM Level 2
 */
HTMLStyleElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLStyleElement.prototype = new HTMLElement;
__extend__(HTMLStyleElement.prototype, {
    get disabled(){
        return this.getAttribute('disabled');
    },
    set disabled(value){
        this.setAttribute('disabled',value);
    },
    get media(){
        return this.getAttribute('media');
    },
    set media(value){
        this.setAttribute('media',value);
    },
    get type(){
        return this.getAttribute('type');
    },
    set type(value){
        this.setAttribute('type',value);
    }
});

/** 
 * HTMLTableElement - DOM Level 2
 * Implementation Provided by Steven Wood
 */
HTMLTableElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLTableElement.prototype = new HTMLElement;
__extend__(HTMLTableElement.prototype, {
    
        get tFoot() { 
        //tFoot returns the table footer.
        return this.getElementsByTagName("tfoot")[0];
    },
    
    createTFoot : function () {
        var tFoot = this.tFoot;
       
        if (!tFoot) {
            tFoot = document.createElement("tfoot");
            this.appendChild(tFoot);
        }
        
        return tFoot;
    },
    
    deleteTFoot : function () {
        var foot = this.tFoot;
        if (foot) {
            foot.parentNode.removeChild(foot);
        }
    },
    
    get tHead() { 
        //tHead returns the table head.
        return this.getElementsByTagName("thead")[0];
    },
    
    createTHead : function () {
        var tHead = this.tHead;
       
        if (!tHead) {
            tHead = document.createElement("thead");
            this.insertBefore(tHead, this.firstChild);
        }
        
        return tHead;
    },
    
    deleteTHead : function () {
        var head = this.tHead;
        if (head) {
            head.parentNode.removeChild(head);
        }
    },
 
    appendChild : function (child) {
        
        var tagName;
        if(child&&child.nodeType==Node.ELEMENT_NODE){
            tagName = child.tagName.toLowerCase();
            if (tagName === "tr") {
                // need an implcit <tbody> to contain this...
                if (!this.currentBody) {
                    this.currentBody = document.createElement("tbody");
                
                    Node.prototype.appendChild.apply(this, [this.currentBody]);
                }
              
                return this.currentBody.appendChild(child); 
       
            } else if (tagName === "tbody" || tagName === "tfoot" && this.currentBody) {
                this.currentBody = child;
                return Node.prototype.appendChild.apply(this, arguments);  
                
            } else {
                return Node.prototype.appendChild.apply(this, arguments);
            }
        }else{
            //tables can still have text node from white space
            return Node.prototype.appendChild.apply(this, arguments);
        }
    },
     
    get tBodies() {
        return new HTMLCollection(this.getElementsByTagName("tbody"));
        
    },
    
    get rows() {
        return new HTMLCollection(this.getElementsByTagName("tr"));
    },
    
    insertRow : function (idx) {
        if (idx === undefined) {
            throw new Error("Index omitted in call to HTMLTableElement.insertRow ");
        }
        
        var rows = this.rows, 
            numRows = rows.length,
            node,
            inserted, 
            lastRow;
        
        if (idx > numRows) {
            throw new Error("Index > rows.length in call to HTMLTableElement.insertRow");
        }
        
        var inserted = document.createElement("tr");
        // If index is -1 or equal to the number of rows, 
        // the row is appended as the last row. If index is omitted 
        // or greater than the number of rows, an error will result
        if (idx === -1 || idx === numRows) {
            this.appendChild(inserted);
        } else {
            rows[idx].parentNode.insertBefore(inserted, rows[idx]);
        }

        return inserted;
    },
    
    deleteRow : function (idx) {
        var elem = this.rows[idx];
        elem.parentNode.removeChild(elem);
    },
    
    get summary() {
        return this.getAttribute("summary");
    },
    
    set summary(summary) {
        this.setAttribute("summary", summary);
    },
    
    get align() {
        return this.getAttribute("align");
    },
    
    set align(align) {
        this.setAttribute("align", align);
    },
    
     
    get bgColor() {
        return this.getAttribute("bgColor");
    },
    
    set bgColor(bgColor) {
        return this.setAttribute("bgColor", bgColor);
    },
   
    get cellPadding() {
        return this.getAttribute("cellPadding");
    },
    
    set cellPadding(cellPadding) {
        return this.setAttribute("cellPadding", cellPadding);
    },
    
    
    get cellSpacing() {
        return this.getAttribute("cellSpacing");
    },
    
    set cellSpacing(cellSpacing) {
        this.setAttribute("cellSpacing", cellSpacing);
    },

    get frame() {
        return this.getAttribute("frame");
    },
    
    set frame(frame) { 
        this.setAttribute("frame", frame);
    },
    
    get rules() {
        return this.getAttribute("rules");
    }, 
    
    set rules(rules) {
        this.setAttribute("rules", rules);
    }, 
    
    get width() {
        return this.getAttribute("width");
    },
    
    set width(width) {
        this.setAttribute("width", width);
    }
    
});
	
/* 
* HTMLxElement - DOM Level 2
* - Contributed by Steven Wood
*/
HTMLTableSectionElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLTableSectionElement.prototype = new HTMLElement;
__extend__(HTMLTableSectionElement.prototype, {    
    
    appendChild : function (child) {
    
        // disallow nesting of these elements.
        if (child.tagName.match(/TBODY|TFOOT|THEAD/)) {
            return this.parentNode.appendChild(child);
        } else {
            return Node.prototype.appendChild.apply(this, arguments);
        }

    },
    
    get align() {
        return this.getAttribute("align");
    },

    get ch() {
        return this.getAttribute("ch");
    },
     
    set ch(ch) {
        this.setAttribute("ch", ch);
    },
    
    // ch gets or sets the alignment character for cells in a column. 
    set chOff(chOff) {
        this.setAttribute("chOff", chOff);
    },
     
    get chOff(chOff) {
        return this.getAttribute("chOff");
    },
     
    get vAlign () {
         return this.getAttribute("vAlign");
    },
    
    get rows() {
        return new HTMLCollection(this.getElementsByTagName("tr"));
    },
    
    insertRow : function (idx) {
        if (idx === undefined) {
            throw new Error("Index omitted in call to HTMLTableSectionElement.insertRow ");
        }
        
        var numRows = this.rows.length,
            node = null;
        
        if (idx > numRows) {
            throw new Error("Index > rows.length in call to HTMLTableSectionElement.insertRow");
        }
        
        var row = document.createElement("tr");
        // If index is -1 or equal to the number of rows, 
        // the row is appended as the last row. If index is omitted 
        // or greater than the number of rows, an error will result
        if (idx === -1 || idx === numRows) {
            this.appendChild(row);
        } else {
            node = this.firstChild;

            for (var i=0; i<idx; i++) {
                node = node.nextSibling;
            }
        }
            
        this.insertBefore(row, node);
        
        return row;
    },
    
    deleteRow : function (idx) {
        var elem = this.rows[idx];
        this.removeChild(elem);
    }

});


/** 
 * HTMLTableCellElement - DOM Level 2
 * Implementation Provided by Steven Wood
 */
HTMLTableCellElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLTableCellElement.prototype = new HTMLElement;
__extend__(HTMLTableCellElement.prototype, {
    
    // TODO :
    
});

/**
 * HTMLTextAreaElement - DOM Level 2
 */
HTMLTextAreaElement = function(ownerDocument) {
    HTMLInputAreaCommon.apply(this, arguments);
};
HTMLTextAreaElement.prototype = new HTMLInputAreaCommon;
__extend__(HTMLTextAreaElement.prototype, {
    get cols(){
        return this.getAttribute('cols');
    },
    set cols(value){
        this.setAttribute('cols', value);
    },
    get rows(){
        return this.getAttribute('rows');
    },
    set rows(value){
        this.setAttribute('rows', value);
    }
});


/** 
 * HTMLTitleElement - DOM Level 2
 */
HTMLTitleElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLTitleElement.prototype = new HTMLElement;
__extend__(HTMLTitleElement.prototype, {
    get text() {
        return this.innerText;
    },

    set text(titleStr) {
        this.textContent = titleStr; 
    }
});



/** 
 * HTMLRowElement - DOM Level 2
 * Implementation Provided by Steven Wood
 */
HTMLTableRowElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLTableRowElement.prototype = new HTMLElement;
__extend__(HTMLTableRowElement.prototype, {
    
    appendChild : function (child) {
    
       var retVal = Node.prototype.appendChild.apply(this, arguments);
       retVal.cellIndex = this.cells.length -1;
             
       return retVal;
    },
    // align gets or sets the horizontal alignment of data within cells of the row.
    get align() {
        return this.getAttribute("align");
    },
     
    get bgColor() {
        return this.getAttribute("bgcolor");
    },
         
    get cells() {
        var nl = this.getElementsByTagName("td");
        return new HTMLCollection(nl);
    },
       
    get ch() {
        return this.getAttribute("ch");
    },
     
    set ch(ch) {
        this.setAttribute("ch", ch);
    },
    
    // ch gets or sets the alignment character for cells in a column. 
    set chOff(chOff) {
        this.setAttribute("chOff", chOff);
    },
     
    get chOff(chOff) {
        return this.getAttribute("chOff");
    },
   
    get rowIndex() {
        var nl = this.parentNode.childNodes;
        for (var i=0; i<nl.length; i++) {
            if (nl[i] === this) {
                return i;
            }
        }
    },

    get sectionRowIndex() {
        var nl = this.parentNode.getElementsByTagName(this.tagName);
        for (var i=0; i<nl.length; i++) {
            if (nl[i] === this) {
                return i;
            }
        }
    },
     
    get vAlign () {
         return this.getAttribute("vAlign");
    },

    insertCell : function (idx) {
        if (idx === undefined) {
            throw new Error("Index omitted in call to HTMLTableRow.insertCell");
        }
        
        var numCells = this.cells.length,
            node = null;
        
        if (idx > numCells) {
            throw new Error("Index > rows.length in call to HTMLTableRow.insertCell");
        }
        
        var cell = document.createElement("td");

        if (idx === -1 || idx === numCells) {
            this.appendChild(cell);
        } else {
            

            node = this.firstChild;

            for (var i=0; i<idx; i++) {
                node = node.nextSibling;
            }
        }
            
        this.insertBefore(cell, node);
        cell.cellIndex = idx;
          
        return cell;
    },

    
    deleteCell : function (idx) {
        var elem = this.cells[idx];
        this.removeChild(elem);
    }

});


/** 
 * HTMLUnknownElement DOM Level 2
 */
HTMLUnknownElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLUnknownElement.prototype = new HTMLElement;
__extend__(HTMLUnknownElement.prototype,{
    toString: function(){
        return '[object HTMLUnknownElement]';
    }
});

/**
 * @author john resig & the envjs team
 * @uri http://www.envjs.com/
 * @copyright 2008-2010
 * @license MIT
 */

})();
