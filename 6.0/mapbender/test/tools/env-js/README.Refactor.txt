
= envjs : pilot fish =

    The goal for refactoring 1.1.rcX to 1.2.X was primarily to isolate and 
    organize the code into more independent areas, provide behavior driven testing
    with tests that run on firefox for each module and it's dependencies.

== src folder ==

    The source files for this project are organized by the conventions described 
    below.  All final sources are included here, including the massaged parser.
    Platform developers are welcome to use the src/env/ folder to consider a new 
    platform.
    
== specs folder ==

    The 'specifications' are our best attempt at isolating some DOM Spec into 
    something we can measure via an existing implementation, namely Firefox, and
    which also allow us to pass the same tests in a Platform.

== a couple code conventions ==

 * Page width <= 80
 * 'Modules' are isolated as
    var A,B,C;
    (function(){
        A = ...;
        B = ...;
        C = ...;
    })();
 * Modules depend on each other in some order.  Many modules provide mix-ins to 
   enhance interfaces exposed in other modules.  events.js for example, provide
   dom 2 events for the dom.js module, adding addEventListener etc to the dom.  
   Here is the general hierarchy as proposed:
    
   dom.js |->event.js |->html.js |->timer.js |->parser.js |->xhr.js |->window.js   
   
   all together we also include what we believe is a platform specific module 
   that describes the interfaces that must be implemented in a platform specific
   api, and this lives in src/env/(core|rhino).  
 * Variable naming should be short but complete words.
 * Module level internal functions should be prefixed and appended with __. 
   For example __example__.
 * Build targets
        > ant   //(does all the following in order)
        > ant env-platforms 
        > ant console-specs
        > ant dom-specs
        > ant event-specs
        > ant html-specs
        > ant timer-specs
        > ant parser-specs
        > ant xhr-specs
        > ant window-specs
   
== contributing tests with patches ==
    
    Each module has a spec in env-js/test/spec.  Most tests will run whether you 
    load them in the file:, http:, or https:, though once you get to xhr.js 
    the tests will fail for the 'file:' protocol in firefox because of 
    permissions.  To run xhr.js and window.js specs, copy settings.js to 
    local_settings.js and run a local server to satisfy those urls included in
    the spec.js;
    
    
== platforms ==

 Out of the box we have support for native platforms with rhino.  A spidermonkey
 port is also being widely used, and we hope to integrate into the main branch 
 or as a github plugin.
 

     
    
    
    