var me = this;


var UserEditor = function(){
  // chose the id to be of the form: options.id + <fieldname>
this.form = $('\n\
      <div id="'+options.id +'frame" class="mbFrame formContainer ConfEditor">\n\
      <h2 id="'+ options.id +'nameheader">UserNameGoesHere</h2>\n\
      <form method="post" id="'+ options.id +'form" >\n\
      <ul>\n\
        <li><label for="'+ options.id +'Name">Username</label><input id="'+ options.id +'name" type="text" /></li>\n\
        <li><label for="'+ options.id +'Password">Password</label><input id="'+ options.id +'password" type="password" /></li>\n\
        <li><label for="'+ options.id +'Description">Description</label><input id="'+ options.id +'description" type="text" /></li>\n\
        <li> <label for="'+ options.id +'owners">Owners</label><input type="hidden" id="'+ options.id +'owners" name="owners" /></li>\n\
        <li><label for="'+ options.id +'LoginCount">Number of Logins</label><input id="'+ options.id +'loginCount" type="text" /></li>\n\
        <li><label for="'+ options.id +'Email">Email</label><input id="'+ options.id +'email" type="text" /></li>\n\
        <li><label for="'+ options.id +'Phone">Phone</label><input id="'+ options.id +'phone" type="text" /></li>\n\
        <li><label for="'+ options.id +'Department">Department</label><input id="'+ options.id +'department" type="text" /></li>\n\
    </ul>\n\
  <p>\n\
  <input id="'+options.id +'save" type="button" value="save" onclick="Mapbender.modules[\''+ options.id  +'\'].save(); return false;" />\n\
  <input id="'+options.id +'delete" type="button" value="delete" onclick="Mapbender.modules[\''+ options.id  +'\'].remove(); return false;" />\n\
  </p>\n\
  </form>\n\
</div>');

  this.defaultFields = {};
  this.defaultFields.name =         { name: "name",         defaultValue: "New User", value: "", display: "User Name", type: "text"};
  this.defaultFields.password =     { name: "password",     defaultValue: "********", value: "", display: "Password", type: "text"};
  this.defaultFields.description =  { name: "description",  defaultValue: "", value: "", display: "Description", type: "text"};
  this.defaultFields.owner =        { name: "owner",        defaultValue: "", value: "", display: "Owner", type: "select"};
  this.defaultFields.loginCount =   { name: "loginCount",   defaultValue: "", value: "", display: "Login Count", type: "text"};
  this.defaultFields.email=         { name: "email",        defaultValue: "", value: "", display: "Email", type: "text"};
  this.defaultFields.phone=         { name: "phone",        defaultValue: "", value: "", display: "Phone", type: "text"};
  this.defaultFields.department=    { name: "department",   defaultValue: "", value: "", display: "Department", type: "text"};
  this.defaultFields.resolution=    { name: "resolution",   defaultValue: "", value: "", display: "Resolution", type: "text"};
  this.defaultFields.organization=  { name: "organization", defaultValue: "", value: "", display: "Organization", type: "text"};
  this.defaultFields.position=      { name: "position",     defaultValue: "", value: "", display: "position", type: "text"};
  this.defaultFields.phone1=        { name: "phone1",       defaultValue: "", value: "", display: "Phone Extra", type: "text"};
  this.defaultFields.fax=           { name: "fax",          defaultValue: "", value: "", display: "Fax", type: "text"};
  this.defaultFields.deliveryPoint= { name: "deliveryPoint",defaultValue: "", value: "", display: "Delivery Point", type: "text"};
  this.defaultFields.city=          { name: "city",         defaultValue: "", value: "", display: "City", type: "text"};
  this.defaultFields.postalCode=    { name: "postalCode",   defaultValue: "", value: "", display: "Postal Code", type: "text"};
  this.defaultFields.country=       { name: "country",      defaultValue: "", value: "", display: "Country", type: "text"};
  this.defaultFields.url=           { name: "url",          defaultValue: "", value: "", display: "URL", type: "text"};

  
  this.name = this.defaultFields.name.value;
  this.rpcEndpoint = "../javascripts/user.php"; 
  this.options = options;
  this.caption = "Users";

  $('#'+ options.id +'owners',this.form).tableedit({url: "../javascripts/user.php",editor: this});
  
  $(me).replaceWith(this.form);
  me = this.form;
  this.reset();

};

UserEditor.prototype = new ConfEditor();
UserEditor.prototype.constructor = UserEditor;

Mapbender.modules[options.id] = new UserEditor();
if(Mapbender.modules['AdminTabs'])
{
    Mapbender.modules['AdminTabs'].register(options.id);
}
