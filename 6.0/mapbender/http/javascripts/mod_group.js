var me = this;


var GroupEditor = function(){
  // chose the id to be of the form: options.id + <fieldname>
  this.form = $('\n\
      <div id="'+options.id +'frame" class="mbFrame formContainer ConfEditor">\n\
      <h2 id="'+ options.id +'nameheader">GroupNameGoesHere</h2>\n\
      <form method="post" id="'+ options.id +'form" >\n\
      <ul>\n\
        <li><label for="'+ options.id +'name">Name</label><input id="'+ options.id +'name" type="text" /></li>\n\
        <li><label for="'+ options.id +'description">Description</label><input id="'+ options.id +'description" type="text" /></li>\n\
        <li><label for="'+ options.id +'owner">Owner</label><select id="'+ options.id +'owner" type="text"> </select></li>\n\
      </ul>\n\
      <p>\n\
      <input id="'+options.id +'save" type="button" value="save" onclick="Mapbender.modules[\''+ options.id  +'\'].save(); return false;" />\n\
      <input id="'+options.id +'delete" type="button" value="delete" onclick="Mapbender.modules[\''+ options.id  +'\'].remove(); return false;" />\n\
      </p>\n\
      </form>\n\
      </div>');

  this.defaultFields = {};
  this.defaultFields.name  = { name: "name", defaultValue : "New Group", value: "", display: "Group Name",  type: "text"};
  this.defaultFields.owner = { name: "owner",       defaultValue : "", value:"", display: "Owner",       type: "select"};
  this.defaultFields.description = { name: "description",       defaultValue : "", value:"", display: "Description",  type: "text"};
  
  this.name = this.defaultFields.name.value;
  this.rpcEndpoint = "../javascripts/group.php"; 
  this.options = options;

  this.caption = "Groups";

  
  $(me).replaceWith(this.form);
  me = this.form;
  this.reset();

};



GroupEditor.prototype = new ConfEditor();
GroupEditor.prototype.constructor = GroupEditor;

Mapbender.modules[options.id] = new GroupEditor();
if(Mapbender.modules['AdminTabs'])
{
  Mapbender.modules['AdminTabs'].register(options.id);
}
