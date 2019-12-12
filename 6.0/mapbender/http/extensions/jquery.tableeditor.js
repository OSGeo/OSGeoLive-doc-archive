var TableEditor = {


_init : function() {
  
  var me = this;
  this.table = $('<table class="listeditor"></table>');
  this.select = $('<select class="listeditor"></select>');
  var addButton = $("<a>Add</a>").click(function(event){
    me.addEntry($(this).prev().val());
    event.preventDefault()
  }); 
  this.element.after(addButton).after(this.select).after(this.table);
  this.element = this.table;

  this._populateSelect(this.options.url,this.select);

},

addEntry: function(text){

  var rows = $("tr",this.element);
  for(var i = 0;i<rows.length;i++)
  {
    
     if($("td:first",rows[i]).text() == $(this.select).val())
     {
       // list already contains this entry
       return;
     }
   }
   this.element.append("<tr><td>"+ text +"</td></tr>");
},

val : function(){

  var entries = [];

  $("tr",this.element).each(function(){ alert("oh hia");  });

  return entries;
},

_populateSelect : function(url,select) {
  var me = this;
  options = { url: url,
              method: 'list',
              callback: (function (result,success, message){
                for( entry in result.list)
                {
                  var selectOption = $('<option value="'+ result.list[entry].name +'">'+ result.list[entry].name +'</option>');
                  select.append(selectOption);
                }

              })};
  var req = Mapbender.Ajax.Request(options);
  req.send();
}

  

};

$.widget("ui.tableedit",TableEditor);
$.ui.tableedit.getter = "val";
//$.extend($.ui.tableedit);
