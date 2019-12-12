
var ConfObject = function(params) {
  this.options = params.options || null;
  this.fields = params.fields || {};
  this.url = params.url || "";
  this.name = params.name || null;
};

ConfObject.prototype.load = function(key) {
  var me = this; 
  var options = {
    url           :  me.url,
    method        : "load",
    parameters    : {
      name    : key
      },
    callback:(function(result,success,message){
      if(result.data.error){
        throw("Could not load ConfObject, server said: " + message);
        return;
      }
      for(key in result.data)
      {
        me.fields[key].value = result.data[key];
      }
      me.editor.display();
    })
}
  var req = Mapbender.Ajax.Request(options);
  req.send();
};

ConfObject.prototype.remove = function(){
  var me = this; 
  var req = Mapbender.Ajax.Request({
    url           :  me.url,
    method        : "remove",
    parameters    : {
      name    : me.name
      },
    callback      : function(result,success,message){
      me.editor.list.load();
    }
  });
  req.send();
};

ConfObject.prototype.update = function(){
  var me = this;
  var lightFields = {};
  for(field in me.fields)
  {
    lightFields[field] = me.fields[field].value;
  }
  var options = {
    url           :  me.url,
    method        : "update",
    parameters    : {
      name    : me.name,
      fields  : lightFields
      },
    callback      : function(result,success,message){
      if(result.error){
        alert("Could not load ConfObject, server said: " + message);
        return;
      }
      me.name = result.name ||me.name;
      me.editor.list.load();
      me.editor.display();
     }
    };
  var req =  Mapbender.Ajax.Request(options);
  req.send();
};

ConfObject.prototype.create = function(){
  var me = this;

  var lightfields = {}
  for(field in me.fields)
  {
    lightfields[field] = me.fields[field].value;
  }

  me.name = me.fields.name.value;
  var options = {
    url           :  me.url,
    method        : "create",
    parameters    : {
      name    : me.name,
      fields  : lightfields
      },
    callback      : function(result,success,message){
      me.editor.list.load();
      if(result.error){
        alert("Could not create ConfObject, server said: " + message);
        return;
      }
     }
    };
  var req = Mapbender.Ajax.Request(options);
  req.send();
};

ConfObject.prototype.setEditor = function(newEditor) {
  this.editor = newEditor || null;
};

