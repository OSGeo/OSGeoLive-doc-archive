

var ConfEditor = function(){
};

ConfEditor.prototype.load = function(key){
  var objectParams = {options: this.options,
                      fields: this.defaultFields,
                      url: this.rpcEndpoint,
                      name: key};
  this.confObject = new ConfObject(objectParams);
  this.confObject.setEditor(this);
  
  // if key is not specified the default values are used
  // and the save method is set to be an alias for create
  if(!key)
  {
    for(field in this.confObject.fields)
    {
      this.confObject.fields[field].value = this.confObject.fields[field].defaultValue;
    }
    ConfEditor.prototype.save = ConfEditor.prototype.create;
    this.display();
    return;
  }
  
  //else we try to load the object by the specified key
  try{
    this.confObject.load(key);
  }catch(E){
    alert("ConfEditor: unable to Load : "+ key +". ("+ E +")"); 
  }
};

ConfEditor.prototype.remove = function(){
  if(this.confObject)
  {
    //TODO: do something nicer
    try{
      this.confObject.remove();
    }catch(E){
      alert(E);
    }
  }else{
    //TODO: notify user
    alert("can't remove: no user loaded");
  }
};

ConfEditor.prototype.commit = function(){

  if(this.confObject.name){
  try{
    this.getValues(); 
    this.confObject.update();
  }catch(E){
    alert("ConfEditor: unable to Commit : ("+ E +")"); 
  }
  }else{
    //TODO: notify user
    alert("cant save : no user loaded");
  }
};

ConfEditor.prototype.create = function(){
  
  this.getValues();
  try{
    this.confObject.create();
  }catch(E){
    alert("ConfEditor: unable to Create: ("+ E +")");
  }
  this.refresh();
  ConfEditor.prototype.save = ConfEditor.prototype.commit;
};

ConfEditor.prototype.save = ConfEditor.prototype.commit;

ConfEditor.prototype.refresh = function() {
  if(this.confObject){
    this.confObject.load(this.confObject.name);
  }else{
    alert("can't refresh :  no user loaded");
  }
};

ConfEditor.prototype.display = function(){
  
  $('#' + this.options.id + 'nameheader').text(this.confObject.name);
  
  //FIXME: this needs to differentiate between the different possible fieldtypes
  for(field in this.confObject.fields)
  {
    field = this.confObject.fields[field];
    switch(field.type)
    {
      case 'text':
        try{
            $('#' + this.options.id + field.name).val(field.value);
        }catch(E){
          alert("Error trying to set " +E);
        }
        break;

      case 'bool':
        if(field.value === true)
        {
          $('#' + this.options.id + field.name).val(true);
        }else{
          $('#' + this.options.id + field.name).val(false);
        }
        break;

      case 'select':
         $('#' + this.options.id + field.name +' > [value='+field.value +']').selected();
        break;
      
      case 'multiselect':
        var element =  $('#' + this.options.id + field.name);
        for(i in field.value)
        {
          element.listeditor().addEntry(i); alert('success');
        }
        break;
        
    }
  }
};

ConfEditor.prototype.reset = function(){
  //see comments in display();
  var objectParams = {options: this.options, fields: this.defaultFields,  url: this.rpcEndpoint};
  this.confObject = this.confObject || new ConfObject(objectParams);
  for(field in this.confObject.fields)
  {
    try{
      $('#' + this.options.id + this.confObject.fields[field].name).val(this.confObject.fields[field].defaultValue);
    }catch(E){
      alert("Error trying to set " +E);
    }
  }
};

ConfEditor.prototype.getValues = function(){
    for(field in this.confObject.fields)
    {
      field = this.confObject.fields[field];
      switch(field.type)
      {
        case 'text':
          try{
            field.value = $('#' + this.options.id + field.name ).val();
          }catch(E){
            alert("Error trying to set " +E);
          }
          break;

        case 'bool':
          var selector = '#' + this.options.id + field.name + ':checked '
          var el =  $(selector);
          //FIXME: need the value of the checkbox
          field.value = $('#' + this.options.id + field.name ).is('checked');

          break;
        case 'select':
          field.value = $('#' + this.options.id + field.name).val();
          break;

        case 'multiselect':
          var test = $('#' + this.options.id + field.name);
          var values = test.listeedit('val');
          field.value = values;
          break;
      }
    }

}

ConfEditor.prototype.setList = function(newList) {
  this.list = newList || null;
};

