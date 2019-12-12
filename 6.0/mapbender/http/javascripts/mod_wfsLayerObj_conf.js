function getWfsConfs(myGui,myWms,myLayer,myWfsConfId,layerButton) {
	$("#setWfsDialog").dialog('close').remove();
	var req = new parent.Mapbender.Ajax.Request({
		url: "../php/mod_wfsLayerObj_conf.php",
		method: "getWfsConfs",
		parameters : {
			gui : myGui,
			wms : myWms,
			layer : myLayer	
		},
		callback: (function(result,success,message){ 
			var selectHtml = "<select id='wfsConfSelect'>";
			selectHtml += "<option value=''><?php echo _mb("none"); ?></option>";
			for (var wfsConfId in result) {
				selectHtml += "<option value='" + result[wfsConfId].wfs_conf_id + "'";
				if(myWfsConfId != "" && myWfsConfId == result[wfsConfId].wfs_conf_id) {
					selectHtml += " selected='selected'";	
				}
				selectHtml += ">";
				selectHtml += result[wfsConfId].wfs_conf_abstract;
				selectHtml += "(ID: " + result[wfsConfId].wfs_conf_id + ")";
				selectHtml += "</option>";
			}
			selectHtml += "</select>";
			
			$("<div id='setWfsDialog'>" + selectHtml + "</div>").dialog({
				autoOpen: true,
				height: 170,
				width: 300,
				title: '<?php echo _mb("Set WFS for layer"); ?>',
				draggable: true,
				buttons: {
					"OK": function(){
						saveLayerWfsConnection($("#wfsConfSelect").val(), myGui, myWms, myLayer, layerButton);
					},
					"<?php echo _mb("Cancel"); ?>": function(){
						$(this).dialog('close').remove();
					}
				}
			});			
		})
	}); 
	req.send();
}

function saveLayerWfsConnection(wfsConf, gui, wms, layer, layerButton) {
	var req = new parent.Mapbender.Ajax.Request({
		url: "../php/mod_wfsLayerObj_conf.php",
		method: "saveLayerWfsConnection",
		parameters : {
			wfsConf : wfsConf,
			gui : gui,
			wms : wms,
			layer : layer	
		},
		callback: (function(result,success,message){ 
			if(success) {
				$("#setWfsDialog").dialog('close');
				if (wfsConf == "") {
					var layerButtonVal = "setWFS";
					
				}
				else {
					var layerButtonVal = "wfs " + wfsConf
					
					//$("#" + layerButton).attr("onclick", "getWfsConfs(gui,wms,layer,wfsConf,'buttonLayerWfs_' + layer)");
				}
				$("#" + layerButton).replaceWith("<input type='button' value='" + layerButtonVal + "' " + 
											"onclick='getWfsConfs(\"" + gui + "\"," + 
											wms + "," + layer + ",\"" + wfsConf + "\",\"buttonLayerWfs_" + layer + "\")' " + 
											"name='gui_layer_gaz' id='buttonLayerWfs_" + layer + "' class='button_wfs'>");
			}
			else {
				$("#setWfsDialog").append("<div>" + message + "</div>");
			} 
		})
	}); 
	req.send();
}