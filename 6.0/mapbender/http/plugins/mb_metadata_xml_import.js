var $xmlImport = $(this);
var importXmlLayerId;

var XmlImportApi = function () {
	var that = this;
	var type;
	
	this.events = {
		"uploadComplete" : new Mapbender.Event()
	};
	
	this.events.uploadComplete.register(function () {
		$xmlImport.dialog("close");
	});
	

	
	var importUploadedFile = function(filename, layerId, callback){
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "importLayerXmlAddon",
			parameters: {
				filename: filename,
				layerId: layerId
			},
			callback: function (obj, result, message, errorCode) {
				
				if (!result) {
					switch (errorCode) {
						case -1002:
							alert("file: "+filename+"has problems: "+message);
							break;
						default:
							alert(message);
							return;
					}
				}

				alert(message);
				$xmlImport.dialog("close");
				that.fillLayerForm(layerId);
				if ($.isFunction(callback)) {
					callback(obj.id);
				}
			}
		});
		req.send();
	};

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

	
	$xmlImport.upload({
		size: 10,
		timeout: 20000,
		url: "../plugins/jq_upload.php",
		callback: function(result,stat,msg){
			if(!result){ 
				alert(msg);
				return;
			}
	        var uploadResultName = result.filename;
	        var uploadResultOrigName = result.origFilename;
	        
	        importUploadedFile(result.filename, that.importXmlLayerId, function (id) {
		        that.events.uploadComplete.trigger({
					"type": type,
					"id": id
				});
			});
	        
    	}
	}).dialog({
		title: 'XML Import',
		autoOpen: false,
		modal: true,
		width: 580
	});


	this.init = function (obj) {
		type = obj.type;
		$xmlImport.dialog("open");
	};

	initXmlImport = function (layerId) {
		that.importXmlLayerId = layerId;
		$xmlImport.dialog("open");
		return true;
	};
	
};

$xmlImport.mapbender(new XmlImportApi());
