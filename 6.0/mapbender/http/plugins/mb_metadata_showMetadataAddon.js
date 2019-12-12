var $showMetadataAddonDiv = $(this);
var $metadataAddonForm = $("<form></form>").appendTo($showMetadataAddonDiv);
//var $metadataUploadForm = $("<form></form>").appendTo($showMetadataAddonDiv);

var $metadataAddonPopup = $("<div></div>");
var $metadataUploadPopup = $("<div></div>");

var ShowMetadataAddonApi = function() {
	var that = this;
	var layerId;
	var metadataId;
	//Function, which pulls the metadata out off the mapbender registry and give a possibility to edit the record or link
	this.valid = function () {
		if (validator && validator.numberOfInvalids() > 0) {
			$metadataAddonForm.valid();
			return false;
		}
		return true;
	};

	this.getAddedMetadata = function(metadataId, layerId){
		// get metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "getLayerMetadataAddon",
			parameters: {
				"layerId": layerId,
				"metadataId": metadataId
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				$metadataAddonForm.easyform("reset");
				$metadataAddonForm.easyform("fill", obj);
				
				//enable link element to edit link!
				$("#link").removeAttr("disabled");

				switch (obj.origin) {
					case "external":
						$("#metadataUrlEditor").css("display","block");
						$("#link_editor").css("display","block");
    						break;
  					case "metador":
						$("#metadataUrlEditor").css("display","block");
						$("#simple_metadata_editor").css("display","block");
    						break;
  					case "capabilities":
						$("#metadataUrlEditor").css("display","block");
    						$("#simple_metadata_editor").css("display","block");
    						break;			
					default:
    						break;
				}
				//select the right list entries:
				$(".format_selectbox").val(obj.format); 				$(".charset_selectbox").val(obj.inspire_charset);
				$(".ref_system_selectbox").val(obj.ref_system);
				$(".cyclic_selectbox").val(obj.update_frequency);
				$(".radioRes").filter('[value='+obj.spatial_res_type+']').attr('checked', true);

			}
		});
		req.send();	
	}		
	this.insertAddedMetadata = function(layerId, data){
		// push metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "insertLayerMetadataAddon",
			parameters: {
				"layerId": layerId,
				"data": data
			},
			callback: function (obj, result, message) {
				$("<div></div>").text(message).dialog({
					modal: true
				});
				//update layer form to show edited data
				that.fillLayerForm(layerId);
			}
		});
		req.send();	
	}


	this.updateAddedMetadata = function(metadataId, layerId, data){
		// push metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "updateLayerMetadataAddon",
			parameters: {
				"layerId": layerId,
				"metadataId": metadataId,
				"data": data
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				$("<div></div>").text(message).dialog({
					modal: true
				});
				//update layer form to show edited data
				that.fillLayerForm(layerId);
			}
		});
		req.send();	
	}	
	//function to fill layer form with changed metadata entries TODO: this function is defined in mb_metadata_layer.js before but it cannot be called - maybe s.th. have to be changed
	this.fillLayerForm = function (layerId) {
		// get metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "getLayerMetadata",
			parameters: {
				"id": layerId
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				//delete metadataURL entries
				$('.metadataEntry').remove();
				//fill MetadataURLs into metadata_selectbox_id
				that.fillMetadataURLs(obj);
			}
		});
		req.send();		
	};

	//function generate updated metadataUrl entries TODO: this function is defined in mb_metadata_layer.js before but it cannot be called - maybe s.th. have to be changed
	this.fillMetadataURLs = function (obj) {
		layerId = obj.layer_id;
		//for size of md_metadata records:
		for (i=0;i<obj.md_metadata.metadata_id.length;i++) {
			if (obj.md_metadata.origin[i] == "capabilities") {
				$("<tr class='metadataEntry'><td>"+obj.md_metadata.metadata_id[i]+"</td><td><img src='../img/osgeo_graphics/geosilk/server_map.png' title='capabilities'/></td><td><a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id="+obj.md_metadata.uuid[i]+"' target='_blank'>"+obj.md_metadata.uuid[i]+"</a></td><td><a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id="+obj.md_metadata.uuid[i]+"&validate=true' target='_blank'>validate</a></td><td></td></tr>").appendTo($("#metadataTable"));
			}
			if (obj.md_metadata.origin[i] == "external") {
				$("<tr class='metadataEntry'><td>"+obj.md_metadata.metadata_id[i]+"</td><td><img src='../img/osgeo_graphics/geosilk/link.png' title='linkage'/><td><a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id="+obj.md_metadata.uuid[i]+"' target='_blank'>"+obj.md_metadata.uuid[i]+"</a></td><td><a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id="+obj.md_metadata.uuid[i]+"&validate=true' target='_blank'>validate</a></td><td><img  class='' title='edit' src='../img/pencil.png' onclick='initMetadataAddon("+obj.md_metadata.metadata_id[i]+","+layerId+",false);return false;'/></td><td><img class='' title='delete' src='../img/cross.png' onclick='deleteAddedMetadata("+obj.md_metadata.metadata_id[i]+","+layerId+");return false;'/></td></tr>").appendTo($("#metadataTable"));
			}
			if (obj.md_metadata.origin[i] == "upload") {
				$("<tr class='metadataEntry'><td>"+obj.md_metadata.metadata_id[i]+"</td><td><img src='../img/button_blue_red/up.png' title='uploaded data'/><td><a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id="+obj.md_metadata.uuid[i]+"' target='_blank'>"+obj.md_metadata.uuid[i]+"</a></td><td><a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id="+obj.md_metadata.uuid[i]+"&validate=true' target='_blank'>validate</a></td><td><img class='' title='delete' src='../img/cross.png' onclick='deleteAddedMetadata("+obj.md_metadata.metadata_id[i]+","+layerId+");return false;'/></td></tr>").appendTo($("#metadataTable"));
			}
			if (obj.md_metadata.origin[i] == "metador") {
				$("<tr class='metadataEntry'><td>"+obj.md_metadata.metadata_id[i]+"</td><td><img src='../img/gnome/edit-select-all.png' title='metadata'/><td><a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id="+obj.md_metadata.uuid[i]+"' target='_blank'>"+obj.md_metadata.uuid[i]+"</a></td><td><a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id="+obj.md_metadata.uuid[i]+"&validate=true' target='_blank'>validate</a></td><td><img  class='' title='edit' src='../img/pencil.png' onclick='initMetadataAddon("+obj.md_metadata.metadata_id[i]+","+layerId+",false);return false;'/></td><td><img class='' title='delete' src='../img/cross.png' onclick='deleteAddedMetadata("+obj.md_metadata.metadata_id[i]+","+layerId+");return false;'/></td></tr>").appendTo($("#metadataTable"));
			}
		}
		$("<img class='metadataEntry' title='new' src='../img/add.png' onclick='initMetadataAddon("+obj.md_metadata.metadata_id[i]+","+layerId+",true);return false;'/>").appendTo($("#metadataTable"));
	}

		
	deleteAddedMetadata = function(metadataId, layerId){
		// push metadata from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "deleteLayerMetadataAddon",
			parameters: {
				"metadataId": metadataId
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				//delete metadataURL entries
				$('.metadataEntry').remove();
				//fill MetadataURLs into metadata_selectbox_id
				//update layer form to show edited data
				that.fillLayerForm(layerId);
				$("<div></div>").text(message).dialog({
					modal: true
				});
			}
		});
		req.send();	
	}		

	
	this.showForm = function (metadataId, layerId, isNew) {
		$metadataAddonPopup.append($metadataAddonForm);
		$metadataAddonPopup.dialog({
			title : "Metadata Addon Editor", 
			autoOpen : false, 
			draggable : true,
			modal : true,
			width : 600,
			position : [600, 75],
			buttons: {
				"close": function() {
					$(this).dialog('close');
				},
				"save": function() {
					//get data from form
					//supress validation for the link only way
					//example $("#myform").validate().element( "#myselect" );
					//$("#myform").validate({
					// ignore: ".ignore"
					//})
					if ($("#addonChooser").css("display") == "block") {
						//don't allow saving but do something else
						return;
						
					}
					if ($("#simple_metadata_editor").css("display") == "block") {
						//validate form before send it!
						if ($metadataAddonForm.valid() != true) {
							alert("Form not valid - please check your input!"); //TODO use translations and make a php file from this
							return;
						}
					}
					var formData = $metadataAddonForm.easyform("serialize");
					if (!isNew) {
						that.updateAddedMetadata(metadataId, layerId, formData);
					} else {
						that.insertAddedMetadata(layerId, formData);
					}
					$(this).dialog('close');
				}
			},
			close: function() {
				//what to do when the dialog is closed
				
			}
		});
		$metadataAddonPopup.dialog("open");
	};
	
	initUploadForm = function (layerId) {
		$metadataAddonPopup.dialog("close");
		initXmlImport(layerId);
		that.fillLayerForm(layerId);
	}


	this.init = function (metadataId, layerId, isNew) {
		$metadataAddonPopup.dialog("close");
		$metadataAddonForm.load("../plugins/mb_metadata_addon.php", function () {
			//push infos to help dialogs
			$metadataAddonForm.find(".help-dialog").helpDialog();
			//initialize datepicker
			$('.hasdatepicker').datepicker({dateFormat:'yy-mm-dd', yearRange: '1900:2050', buttonImageOnly: true, changeYear: true,
constraintInput: true});
			//first get json
			if (!isNew) {
				that.getAddedMetadata(metadataId, layerId);
			
			} else {
				//show chooser
				$("#metadataUrlEditor").css("display","block"); 
				$("#addonChooser").css("display","block");
				$("#uploadImage").attr('onclick', 'initUploadForm('+layerId+')');
			}
			that.showForm(metadataId, layerId, isNew);
		});
		//upload form
		
		//$metadataUploadPopup.append($metadataUploadForm);
	}
	initMetadataAddon = function(metadataId, layerId, isNew) {
		//close old window and load form
		that.init(metadataId, layerId, isNew);	
		//fill form
	}
};

$showMetadataAddonDiv.mapbender(new ShowMetadataAddonApi());
