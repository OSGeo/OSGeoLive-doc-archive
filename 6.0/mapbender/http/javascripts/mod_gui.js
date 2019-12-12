var me = this;


var GuiEditor = function(){
  // chose the id to be of the form: options.id + <fieldname>
this.form = $('\n\
      <div id="'+options.id +'frame" class="mbFrame formContainer ConfEditor">\n\
      <h2 id="'+ options.id +'nameheader">GuiNameGoesHere</h2>\n\
      <form method="post" id="'+ options.id +'form" >\n\
      <ul>\n\
        <li><label for="'+ options.id +'name">Name</label><input id="'+ options.id +'name" type="text" /></li>\n\
        <li><label for="'+ options.id +'description">Description</label><input id="'+ options.id +'description" type="text" /></li>\n\
        <li><label for="'+ options.id +'public">Public</label><input id="'+ options.id +'public" name="public"  type="checkbox" /></li>\n\
        <li>\n\
          <label for="'+ options.id +'owners">Owners</label><input type="hidden" id="'+ options.id +'owners" name="owners" />\n\
        </li>\n\
        <li>\n\
          <label for="'+ options.id +'groups">Groups</label><input type="hidden" id="'+ options.id +'groups" name="owners" />\n\
        </li>\n\
    </ul>\n\
  <p>\n\
  <input id="'+options.id +'save" type="button" value="save" onclick="Mapbender.modules[\''+ options.id  +'\'].save(); return false;" />\n\
  <input id="'+options.id +'delete" type="button" value="delete" onclick="Mapbender.modules[\''+ options.id  +'\'].remove(); return false;" />\n\
  </p>\n\
  </form>\n\
</div>');
  
  var meGuiEditor = this;

  
  this.defaultFields = {};
  this.defaultFields.name =         { name: "name",         defaultValue: "New Gui", value: "", display: "User Name", type: "text"};
  this.defaultFields.description =  { name: "description",  defaultValue: "", value: "", display: "Description", type: "text"};
  this.defaultFields.public=        { name: "public",        defaultValue: true, value: "", display: "Public", type: "bool"};
  this.defaultFields.owners=        { name: "owners",        defaultValue: [], value: [], display: "Owners", type: "multiselect"};
  this.defaultFields.groups=        { name: "groups",        defaultValue: [], value: [], display: "Groups", type: "multiselect"};
  

  
  this.name = this.defaultFields.name.value;
  this.rpcEndpoint = "../javascripts/gui.php"; 
  this.options = options;
  
  this.caption = "GUI";
  
  $('#'+ options.id +'owners',this.form).tableedit({url: "../javascripts/user.php",editor: this});
  $('#'+ options.id +'groups',this.form).listeditor("../javascripts/group.php",{editor: this});
  
  
  $(me).replaceWith(this.form);
          
  me = this.form;
  this.reset();

};


GuiEditor.prototype = new ConfEditor();
GuiEditor.prototype.constructor = GuiEditor;

Mapbender.modules[options.id] = new GuiEditor();
if(Mapbender.modules['AdminTabs'])
{
    Mapbender.modules['AdminTabs'].register(options.id);
}
