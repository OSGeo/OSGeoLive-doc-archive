//FIXME: "type"  shoudl be set by the dataobject recieved form the server
var MBconfObjectList = function(url,element){

// This Object displays availabe Configuration Objects in a given
// category in a HTML ul element
// and updates a corresponding editor with the Objects configuration 
// Options


var me = this;
this.type = "ObjectType";
this.element = element;
this.allEntries = {};
this.displayEntries = {};
this.editor = null;
this.url = url; //RPC endpoint for getting objects

};

MBconfObjectList.prototype.load = function(){
  var that = this;
  var options = {
    url: that.url, 
    method: "list",
    callback :(function(result,success,message){
      var users = result.list;
      that.type = result.type.display || that.type;
      that.allEntries = users;
      that.displayEntries = that.allEntries;
      that.display();
    })
    }

  var request = new Mapbender.Ajax.Request(options);
  request.send();
 
};


MBconfObjectList.prototype.display = function(search){
  if(!search) {search = "";}
  while(this.element.firstChild)
  {
      this.element.removeChild(this.element.firstChild);
  }
  $(this.element).html("");

  var listEntry = $("<li></li>");
  listEntry.addClass("listFilter");;

  var listFilter = $('<input type="text" >');
  listFilter.attr('title','filter users by name');
  listFilter.attr('value',search);
  listFilter.bind('change keyup',this.addOnFilterChangeHandler());

  
  //listEntry.appendChild(listFilter);
  listEntry.append(listFilter);
  $(this.element).append(listEntry);

  for(entry in this.displayEntries)
  {
    var name = this.displayEntries[entry].name;
    var value = this.displayEntries[entry].value;
    listEntry = $("<li></li>");
    listEntry.html(name);
    listEntry.bind('click', this.addOnClickHandler(this.displayEntries[entry]));
    $(this.element).append(listEntry);
  
  }
  listEntry = $('<li></li>');
  listEntry.addClass('listCommand');
  listEntry.html('Add ' + this.type);
  listEntry.bind('click',this.addOnClickHandler(value));
  $(this.element).append(listEntry);
  listFilter.focus();
};

MBconfObjectList.prototype.setEditor = function(newEditor){
  //TODO: check newEditor for validity
  this.editor = newEditor;
  //TODO: assuming the editor.setList also tries to set the
  // lists editor to itself, would cause some silly recursion.
  // how do I deal with this
  this.editor.setList(this);
};

MBconfObjectList.prototype.addOnClickHandler = function(key){
 if(this.editor === null || this.editor === undefined) { return (function(){});}
 var editor = this.editor;
 var handler =  (function(){
    editor.load(key.value);
 });
 return handler;
};

MBconfObjectList.prototype.addOnFilterChangeHandler = function(){
  var list = this;
  return (function(evt) {
    var e = evt;
    var needle = e.target.value;
    list.displayEntries = {};
    //TODO: optimize
    for(entry in list.allEntries)
    {
      if(list.allEntries[entry].value.indexOf(needle) != -1)
      {
        list.displayEntries[entry] = list.allEntries[entry]; 
      }
    }
    list.display(needle);
  });

};


var me = this;

var Tabs = $('<ul><li>The Admintabs</li></ul>');
Tabs.css("position","absolute");
Tabs.css("top","1em");
Tabs.css("left","1em");
var h = $(me).append(Tabs);

var AdminTabs = function() {

  
  var adminModules = [];
  this.register = function(moduleId) {
    for(var c = 0; c < adminModules.length;c++) 
    {
      if(adminModules[c] == moduleId) { return; }
    }
    adminModules.push(moduleId);
    
    initmodule(moduleId); 
  };

  /*  moduleId
   *  param @moduleId the Id of the module that should be initialized (same as the modules options.id)
   */
  var initmodule = function(moduleId) {

      var tab = $('<li><a href="#'+moduleId+'container">'+ Mapbender.modules[moduleId].caption +'</a></li>');
      Tabs.append(tab);
      
      var container = $("<div></div>");
      container.attr("id",moduleId + 'container');
      $(me).append(container);
      
      // append ul element to AdminArea and make it a list
      var listElement = $("<ul></ul>");
      listElement.addClass("mbList");
      container.append(listElement);
      var List = new MBconfObjectList('../javascripts/'+Mapbender.modules[moduleId].rpcEndpoint ,listElement);
      List.setEditor(Mapbender.modules[moduleId]);
      List.editor.setList(List);
      List.load();
      
      // make module a child of AdminArea
      $(container).append($('#' + moduleId+'frame'));


    };

//  $.getScript("../extensions/jquery-ui-1.7.1.w.o.effects.min.js",(function(){
//  WHEN jquery.ui.js is included directly, calling this without a timeout, creates a racecondition 
//  which is dependent on the interface elemens being loaded, we give it a timweout here and think about something
//  better,later
    setTimeout(function(){   $('#' + options.id).tabs();},1000);
//  }));

 };

Mapbender.modules[options.id] = new AdminTabs();
