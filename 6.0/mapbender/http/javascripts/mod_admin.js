var Mapbender = Mapbender || {};

function object(o){
  function F() {}
  F.prototype = o;
  return new F();
}

Mapbender.Admin = {};

 /*
  * Alist of  registered modules
 */
Mapbender.Admin.modules = {};

/*
 *  Represents a Configuration Module. All Available Modules have to be registered here 
 */
Mapbender.Admin.Module = function(options){
  
  options = options || {};

  /*
   * The title displayed to the user
  */
  this.title = options.title || "";

  /*
   * The relative path to the File that contains the
   * derived MBconfigObject, and MBConfigObjectEditor
  */
  this.path = options.path || "";

  /*
   * The path where to send RPCs
  */
  this.endpoint = options.endpoint || "";

  /*
   * wether the Module should active in the current page
  */
  this.active = options.active || false;

  this.class = options.class || "";

  this.load = function()
  {

  };

};
Mapbender.Admin.Module.prototype.constructor = Mapbender.Admin.Module;


//TODO: move these into their respective files
Mapbender.Admin.modules.User   = new  Mapbender.Admin.Module({title: "User",
                          path: "mod_user.php", 
                          endpoint : "user.php",
                          class: "MBUserEditor",
                          confObject: Mapbender.Admin.confUser});

Mapbender.Admin.modules.Group  =   new  Mapbender.Admin.Module({title: "Groups",
                          path: "mod_group.php", 
                          endpoint: "group.php",
                          class: "MBGroupEditor"});




/*
 * Scans the current Page for any modules to be activated
*/
Mapbender.Admin.scan = function(){
  
  for(module in Mapbender.Admin.modules)
  {
    if(!Mapbender.Admin.modules.hasOwnProperty(module)) { continue; }
    var selector = "." + Mapbender.Admin.modules[module].class;
    var elements = $(selector);
    for(var i =0; i < elements.length; i++)
    {
     elements[i].style.backgroundColor = "red";

    }
    
  }

};

