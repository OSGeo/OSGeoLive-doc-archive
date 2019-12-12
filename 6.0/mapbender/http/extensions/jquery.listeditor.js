// @param url the RPC endpoint to get the list for the selectbox from

jQuery.fn.listeditor = function(url,options) {
  var me = this;
  me.url = url || null;
  me.options = options || {};
  me.options.editor = options.editor || [];
  return me.each(function(){
    return new ListEditor($(this),url,options);
  });
 
};


var ListEditor = function(element,url,options) {
  var me = this;

  //check if this is already a listeditor and if so, exit

  if(element.next().is('table.listeditor')){ return this; };

  this.table = $('<table class="listeditor"></table>');
  this.select = $('<select class="listeditor"></select>');
  this.element = this.table;
  var addButton = $("<a>Add</a>").click(function(event){
    me.addEntry($(this).prev().val());
    event.preventDefault()
  }); 
  element.after(addButton).after(this.select).after(this.table);
  populateSelect(url,this.select);
};

ListEditor.prototype.addEntry = function(text){
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
};

ListEditor.prototype.val = function(text){
  var list = [];
  //TODO: this should be a singe selector
  var rows = $("tr",this.element);
  $("td:first",rows).each(function(){list.push(($(this).text()))}) ;
  return list;
};

var populateSelect = function(url,select) {
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
};
