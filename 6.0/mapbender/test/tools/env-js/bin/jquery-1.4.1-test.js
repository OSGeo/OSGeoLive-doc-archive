//debugger;
load("dist/env.rhino.js");

Envjs({
    //let it load the script from the html
    scriptTypes: {
        "text/javascript"   :true
    },
    afterScriptLoad:{
        'data/testrunner.js': function(){
            console.log('loaded test runner');
            //hook into qunit.log
            var count = 0;
            QUnit.log = function(result, message){
                console.log('(' + (count++) + ')[' + 
                    ((!!result) ? 'PASS' : 'FAIL') + '] ' + message);
            };
            //hook into qunit.done
            QUnit.done = function(pass, fail){
                console.log('Writing Results to File');
                jQuery('script').each(function(){
                    this.type = 'text/envjs';
                });
                Envjs.writeToFile(
                    document.documentElement.outerHTML, 
                    Envjs.location('Envjs.jQuery.1.4.1.html')
                );
            };
            
            //allow jquery to run ajax
            isLocal = true;
            jQuery.ajaxSetup({async : false});
            
            //we are breaking becuase our inheritence pattern causes infinite
            //recursion somewhere in jsDump;
            QUnit.jsDump = {
                parse: function(thing){
                    return thing+'';
                }
            }
            
            
            var unsafeStop = stop,
                unsafeStart = start,
                isStopped = null;

            var config_timeout;
            stop = function(timeout){
                if(isStopped === null || isStopped === false){
                    console.log('pausing...');
                    isStopped = true;
                    unsafeStop.call(this);
                    timeout = ( timeout && timeout > 0 ) ? timeout : 10000;
                    start();
                    Envjs.wait()
                }
            };
            start = function(){
                if(isStopped === null || isStopped === true ){
                    console.log('restarting');
                    isStopped = false;
                    if(config_timeout) {
                        clearTimeout(config_timeout);
                        config_timeout = undefined;
                    }
                    unsafeStart.call(this);
                }
            };
            //we know some ajax calls will fail becuase
            //we are not running against a running server
            //for php files
            var handleError = jQuery.handleError;
            jQuery.handleError = function(){
                ok(false, 'Ajax may have failed while running locally');
                try{
                    handleError(arguments);
                }catch(e){
                    console.log(e);
                }
                //allow tests to gracefully continue
                start();
            };
            //allow unanticipated xhr error with no ajax.handleError 
            //callback (eg jQuery.getScript) to exit gracefully
            Envjs.onInterrupt = function(){
                console.log('thread interupt: gracefully continuing test');
                start();
            };
            
           
            Envjs.onScriptLoadError = function(script){
                Envjs.error("failed to load script \n"+script.text);    
                ok(false, 'Ajax may have failed to load correct script while running locally');
                //allow tests to gracefully continue
                start();
            };
        }
    }
});

window.document.async = false;
window.location = 'test/vendor/jQuery/1.4.1/test/index.html';
Envjs.wait();

/*
 * I was able to attach a memory analyzer to Envjs 1.2.x running jQuery 1.4.1 test/index.html and found there where some giant strings being generated:

Found 74 occurrences of char[] with at least 10 instances having identical content. Total size is 40,131,624 bytes.

Top elements include:

499 x "Document": function( a, b ){
  &nb... (39,888 bytes)
497 x "Node": function( a ){
   &nbs... (39,880 bytes)
523 x "baseURI": "file:///opt/tomcat/webapps/env-js/test... (192 bytes)
523 x "nodeName": "#document" (64 bytes)
526 x "namespaceURI": null (56 bytes)

I was able to track this down to QUnit.jsDump.parsers.function which adding a logging statement shows a  infinite loop does actually occur at that point.

The cause seems to be a gross inheritence pattern that I let propagate through Envjs dom/html modules, for example:

Element = function(ownerDocument) {
    this.Node  = Node;
    this.Node(ownerDocument);
   
    this.attributes = new NamedNodeMap(this.ownerDocument, this);
};
Element.prototype = new Node;

This pattern is just an artifact of the http://xmljs.sourceforge.net/ project which we incorporated  bits of a long time ago, and in particular the fact that this.Node = Node; is used instead of just Node.prototype.constructor.apply(this, arguments) I'm already refactoring and am confident that we wont see the jsDump issue after that, but I thought you might like to know about the possibility of the issue.

you can recreate it for the time being in envjs trunk with ./bin/test-jquery.js
*/
