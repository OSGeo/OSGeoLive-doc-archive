
/**
* DOMParser
*/

__defineParser__(function(e){
    console.log('Error loading html 5 parser implementation');
}, 'nu_validator_htmlparser_HtmlParser', '');

DOMParser = function(principle, documentURI, baseURI){};
__extend__(DOMParser.prototype,{
    parseFromString: function(xmlstring, mimetype){
        var xmldoc = new DOMImplementation().createDocument('','',null);
        return XMLParser.parseDocument(xmlstring, xmldoc, mimetype);
    }
});

XMLParser.parseDocument = function(xmlstring, xmldoc, mimetype){
    //console.log('XMLParser.parseDocument')
    var tmpdoc = new Document(new DOMImplementation()),
        parent,
        importedNode,
        tmpNode;
        
    if(mimetype && mimetype == 'text/xml'){
        tmpdoc.baseURI = 'http://envjs.com/xml';
        xmlstring = '<html><head></head><body>'+
            '<envjs_1234567890 xmlns="envjs_1234567890">'
                +xmlstring+
            '</envjs_1234567890>'+
        '</body></html>';
        Envjs.parseHtmlDocument(xmlstring, tmpdoc, false, null, null);  
        parent = tmpdoc.getElementsByTagName('envjs_1234567890')[0];
    }else{
        Envjs.parseHtmlDocument(xmlstring, tmpdoc, false, null, null);  
        parent = tmpdoc.documentElement;
    }
    
    while(xmldoc.firstChild != null){
        tmpNode = xmldoc.removeChild( xmldoc.firstChild );
        delete tmpNode;
    }
    while(parent.firstChild != null){
        tmpNode  = parent.removeChild( parent.firstChild );
        importedNode = xmldoc.importNode( tmpNode, true);
        xmldoc.appendChild( importedNode );
        delete tmpNode;
    }
    delete tmpdoc,
           xmlstring;
    return xmldoc;
};

var __fragmentCache__ = {};
HTMLParser.parseDocument = function(htmlstring, htmldoc){
    //console.log('HTMLParser.parseDocument %s', htmldoc.async);
    htmldoc.parsing = true;
    Envjs.parseHtmlDocument(htmlstring, htmldoc, htmldoc.async, null, null);  
    //Envjs.wait(-1);
    return htmldoc;
};
HTMLParser.parseFragment = function(htmlstring, fragment){
    //console.log('HTMLParser.parseFragment')
    // fragment is allowed to be an element as well
    var tmpdoc,
        parent,
        importedNode,
        tmpNode,
        length,
        i;
    
    if( htmlstring.length > 127 && htmlstring in __fragmentCache__){
        tmpdoc = __fragmentCache__[htmlstring];
    }else{
        //console.log('parsing html fragment \n%s', htmlstring);
        tmpdoc = new HTMLDocument(new DOMImplementation());
        Envjs.parseHtmlDocument(htmlstring,tmpdoc, false, null,null);
        if(htmlstring.length > 127 ){
            tmpdoc.normalizeDocument();
            __fragmentCache__[htmlstring] = tmpdoc;
            tmpdoc.cached = true;
        }else{
            tmpdoc.cached = false;
        }
    }
    
    parent = tmpdoc.body;
    while(fragment.firstChild != null){
        tmpNode = fragment.removeChild( fragment.firstChild );
        delete tmpNode;
    }
    if(tmpdoc.cached){
        length = parent.childNodes.length;
        for(i=0;i<length;i++){
            importedNode = fragment.importNode( parent.childNodes[i], true );
            fragment.appendChild( importedNode );  
        }
    }else{
        while(parent.firstChild != null){
            tmpNode  = parent.removeChild( parent.firstChild );
            importedNode = fragment.importNode( tmpNode, true);
            fragment.appendChild( importedNode );
            delete tmpNode;
        }
        delete tmpdoc,
               htmlstring;
    }
    
    return fragment;
};

var __clearFragmentCache__ = function(){
    __fragmentCache__ = {};
}

