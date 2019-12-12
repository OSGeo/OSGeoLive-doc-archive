<?php
# $Id$
# http://www.mapbender.org/index.php/gazetteerMetadata
# Copyright (C) 2002 CCGIS 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

$user_id = Mapbender::session()->get("mb_user_id");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta name="author" content="V. Diewald">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<meta name="DC.Rights" content="WhereGroup GmbH & Co.KG, Bonn">
<title>Metadata search</title>
<?php
include_once(dirname(__FILE__) . "/../include/dyn_css.php");
?>

<script type="text/javascript">
<?php
$sql = "SELECT e_target FROM gui_element WHERE e_id = $1 AND fkey_gui_id = $2";
$v = array($e_id, $gui_id);
$t = array('s', "s");
$res = db_prep_query($sql,$v,$t);

$targetArray = explode(",", db_result($res,0,"e_target"));
echo "var myTarget = '".$targetArray[0]."';";
include_once(dirname(__FILE__) . "/../include/dyn_php.php");
echo "var searchColumnsWms = '" . $searchColumnsWms . "';";
echo "var searchColumnsLayer = '" . $searchColumnsLayer . "';";
?>
</script>

<style type="text/css">
<!--
	body{
		font-family : Arial, Helvetica, sans-serif;
		font-size : 12px;
		font-weight : bold;
		color: #808080;
	}

	a:link{
		font-family : Arial, Helvetica, sans-serif;
		text-decoration : none;
		color: #808080;
		font-size : 12px;
		font-weight : normal;
	}
	a:visited{
		font-family : Arial, Helvetica, sans-serif;
		text-decoration : none;
		color: #808080;
		font-size : 12px;
		font-weight : normal;
	}
	a:hover{
		font-family : Arial, Helvetica, sans-serif;
		color: #808080;
		text-decoration : none;
		font-weight : normal;
	}
	a:active{
		font-family : Arial, Helvetica, sans-serif;
		color: #808080;
		text-decoration : none;
		font-weight : normal;
	}

	.textfield{
		border : 2 solid #D3D3D3;
		font-family : Arial, Helvetica, sans-serif;
		font-size : 12px;
		font-weight : normal;
		color: #000000;
		width: 120px;
	}
	
	.result{
		position: absolute;
		top: 40px;
		left: 0px;
	}
-->
</style>
<script type="text/javascript">

function i18nInit(){
        parent.Mapbender.events.localize.register(function(){
                window.location.reload();
        });
}


function init(){
        if (parent.Mapbender.events.init.done){
                i18nInit();
        }
        else{
                parent.Mapbender.events.init.register(function(){
                        i18nInit();
                });
        }
}

var loadWmsAndZoomCallback = function (opt) {
	if (typeof opt === "object" && opt.success) {
		
		var map = parent.getMapObjByName(myTarget);
		var wms = map.wms[map.wms.length - 1];
		
		if (wms === null) {
			opt.msg = "<?php echo _mb("An error occured."); ?>";
		}
		else {
			var wmsId = wms.wms_id;

			// activate
			if (typeof opt.visible === "number" && opt.visible === 1) {
				if (typeof addwms_showWMS === "number" 
					&& addwms_showWMS < wms.objLayer.length) {
					
					if (addwms_showWMS > 0) {
						try {
							var msg = "<?php echo _mb("The added Service has more than"); ?> " + addwms_showWMS + 								" <?php echo _mb("layer. The layer of the MapService will NOT be activated."); ?>";
								
							parent.Mapbender.Modules.dialogManager.openDialog({
								content: msg,
								modal: false,
								effectShow: 'puff'
							});
						}
						catch (e) {
							new parent.Mb_warning(e.message + ". " + msg);
						}
					}
				}
				else {
					parent.handleSelectedWms(map.elementName, wmsId, "visible", 1);
					parent.mb_restateLayers(map.elementName, wmsId);
				}
			}
			
			// zoom to bbox
			var bbox_minx, bbox_miny, bbox_maxx, bbox_maxy;
			for (var i = 0; i < wms.gui_epsg.length; i++) {
				if (map.epsg == wms.gui_epsg[i]) {
					bbox_minx = parseFloat(wms.gui_minx[i]);
					bbox_miny = parseFloat(wms.gui_miny[i]);
					bbox_maxx = parseFloat(wms.gui_maxx[i]);
					bbox_maxy = parseFloat(wms.gui_maxy[i]);
					if (bbox_minx === null || bbox_miny === null || bbox_maxx === null || bbox_maxy === null) {
						continue;
					}

					map.calculateExtent(new parent.Extent(
						bbox_minx,
						bbox_miny,
						bbox_maxx,
						bbox_maxy
					));
					map.setMapRequest();
					break;
				}
			}
		}
	}
	loadWmsCallback(opt);

};

var loadWmsCallback = function (opt) {
	var msg = typeof opt.msg === "string" ? opt.msg : "";
	
	if (typeof opt !== "object" || !opt.success) {
		msg = "<?php echo _mb("An error occured."); ?>";
	} 
	else {
		var map = parent.getMapObjByName(myTarget);
		var wms = map.wms[map.wms.length - 1];
		
		if (wms !== null) {
			msg = "<?php echo _mb("The following Service has been added"); ?>:<br><br>";
			msg += "<b>" + wms.wms_title + "</b><br><br>";
		}
		else {
			msg = "<?php echo _mb("An error occured."); ?>";
		}
	}
	try {
		parent.Mapbender.Modules.dialogManager.openDialog({
			content: msg,
			modal: false,
			effectShow: 'puff'
		});
	}
	catch (e) {
		new parent.Mb_warning(e.message + ". " + msg);
	}
};



function openKeywordPopup () {
	if(parent.$('.keywordIframe').size() > 0) {
		parent.$('.keywordIframe').dialog('destroy');
	}
	var $keywordPopup = parent.$('<div class="keywordIframe"><iframe style="width:99%;height:99%;" src="../php/mod_SelectKeyword.php"></iframe></div>');
	$keywordPopup.dialog({
		title: "<?php echo _mb("Select a keyword"); ?>",
		bgiframe: true,
		autoOpen: true,
		modal: false,
		width: 550,
		height: 450,
		pos: [300,100]
	}).parent().css({position:"absolute"});
}

function openMetadataPopup (layerId) {
	if(parent.$('.metadataIframe').size() > 0) {
		parent.$('.metadataIframe').dialog('destroy');
	}
	var $metadataPopup = parent.$('<div class="metadataIframe"><iframe style="width:100%;height:98%;" src="../php/mod_layerMetadata.php?id=' + layerId + '"></iframe></div>');
	$metadataPopup.dialog({
		title : "<?php echo _mb("Metadata"); ?>",
		bgiframe: true,
		autoOpen: true,
		modal: false,
		width: 450,
		height: 600,
		pos: [400,100]
	}).parent().css({position:"absolute"});
}

function validate(){

   if(document.form1.search.value.length < 1){
      alert("<?php echo _mb("Please insert a keyword!"); ?>");
      document.form1.search.focus();
      return false;
   }
   else{   
		document.getElementById("resultDivTag").innerHTML = "<table><tr><td><img src='../img/indicator_wheel.gif'></td><td><?php echo _mb("Searching"); ?>...</td></tr></table>";
		var ind = parent.getMapObjIndexByName('mapframe1');
		
		parent.mb_ajax_json(
			"../php/mod_gazetteerMetadata_search.php", 
			{
				"search" : document.form1.search.value,
				"srs" : parent.mb_mapObj[ind].epsg,
				"searchColumnsWms" : searchColumnsWms,
				"searchColumnsLayer" : searchColumnsLayer
			}, 
			function(jsonObj, status){
				document.getElementById("resultDivTag").innerHTML = displayTable(jsonObj);
			}
		);
		return false;
   }
}


function displayTable(obj) {
	var text = "<table>";
	for (var attr in obj) {
		var resultObj = obj[attr];
		if (typeof(resultObj) != 'function') {
			text += "<tr><td style='padding-left:0px padding-right:0px' valign='top'>";
			var imgUrl = "";
			var onclickFunction = "";
			if (typeof(resultObj.layer_name) !== "undefined") {
				imgUrl = "../img/button_gray/metadata_layer.gif";
				onclickFunction = "mod_addWMSLayerfromfilteredList(\"" + 
					resultObj.wms_getcapabilities + "\",\"" + 
					resultObj.wms_version + "\", \"" + 
					resultObj.layer_name+"\", {" + 
					"zoomToExtent: 0, " + 
					"visible: 0, " + 
					"callback: loadWmsCallback" + 
					"});";
			}
			else {
				imgUrl = "../img/button_gray/metadata_wms.gif";
				onclickFunction = "mod_addWMSfromfilteredList(\"" + 
					resultObj.wms_getcapabilities + "\",\"" + 
					resultObj.wms_version+"\", {" + 
					"zoomToExtent: 0, " + 
					"visible: 0, " + 
					"callback: loadWmsCallback" + 
					"});";
			}
			text += "<img name='add_wms' style='cursor: pointer;' src='" + imgUrl + "' ";
			text += "border='0' title='<?php echo _mb("Load"); ?>' ";
			text += "onclick='" + onclickFunction + "'>";

			if (typeof(resultObj.layer_name) !== "undefined") {
				imgUrl = "../img/tree_new/zoom.png";
				onclickFunction = "mod_addWMSLayerfromfilteredList(\"" + 
					resultObj.wms_getcapabilities + "\",\"" + 
					resultObj.wms_version + "\", \"" + 
					resultObj.layer_name+"\", {" + 
					"zoomToExtent: 1, " + 
					"visible: 1, " + 
					"callback: loadWmsAndZoomCallback" + 
					"});";
			}
			else {
				imgUrl = "../img/tree_new/zoom.png";
				onclickFunction = "mod_addWMSfromfilteredList(\"" + 
					resultObj.wms_getcapabilities + "\",\"" + 
					resultObj.wms_version+"\", {" + 
					"zoomToExtent: 1, " + 
					"visible: 1, " + 
					"callback: loadWmsAndZoomCallback" + 
					"});";
			}
			text += "<img src='../img/tree_zoom.png' ";
			text += "title='<?php echo _mb("Add WMS and zoom to extent"); ?>' ";
			text += "onclick='" + onclickFunction + "'>";
			text += "</td><td>";
			text += "<a href='#' ";
			text += "onclick='openMetadataPopup(" + resultObj.layer_id + ");' title='Info'>"; 
			text += resultObj.title+"</a>";	
			text += "</td></tr>";
		}
	}
	if(obj.length === 0) {
		text += "<tr><td>0 bewertete Treffer. Keine Suchergebnisse gefunden.</td></tr>";
	}
	text += "</table>";
	return text;
}


function handleLayer(sel_lay, wms_title){
    
	//var wms_title = document.forms[0].wmsTitle.value

	var x = new Array();

    x[0] = sel_lay;

    var y = new Array();
    
    if (backlink =='parent'){
		var wms_ID = parent.parent.getWMSIDByTitle('mapframe1',wms_title);
	}
	else{
		var wms_ID = parent.getWMSIDByTitle('mapframe1',wms_title);
	}

    y[0] = wms_ID;
    
	//alert(wms_title + " -- X "+ x + "wms_id" + wms_ID);
	
	if (backlink =='parent'){
		parent.parent.handleSelectedLayer_array('mapframe1',y,x,'querylayer',1);
		parent.parent.handleSelectedLayer_array('mapframe1',y,x,'visible',1);
	}
	else{
		parent.handleSelectedLayer_array('mapframe1',y,x,'querylayer',1);
		parent.handleSelectedLayer_array('mapframe1',y,x,'visible',1);		
	}

}

function mod_addWMSfromfilteredList(pointer_name, version, options){

	pointer_name=pointer_name + parent.mb_getConjunctionCharacter(pointer_name);
	if (version == '1.0.0'){
		var cap = pointer_name + "REQUEST=capabilities&WMTVER=1.0.0";
		var load = cap;
	}
	else if (version == '1.1.0'){
		var cap = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.0";
		var load = cap;
	}
	else if (version == '1.1.1'){
		var cap = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.1";
		var load = cap;
	}  
	if(load){
		parent.mod_addWMS_load(load, options);
	}
}

function mod_addWMSLayerfromfilteredList(pointer_name,version,layer_name, options){
	
	pointer_name=pointer_name + parent.mb_getConjunctionCharacter(pointer_name);
	var load = null;
	if (version == '1.0.0'){
		load = pointer_name + "REQUEST=capabilities&WMTVER=1.0.0";
	}
	else if (version == '1.1.0'){
		load = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.0";
	}
	else if (version == '1.1.1'){
		load = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.1";
	}  
	//alert (load);

	if (typeof load === "string") {
		if (load.charAt(0) == '/' && load.charAt(1) == 'c'){
			parent.mod_addLayer_load('http://localhost' + load, layer_name, options);
		}
		else {
			parent.mod_addLayer_load(load, layer_name, options);
		}  
	}
}

</script>
</head>
<body leftmargin="2" topmargin="0" bgcolor="#ffffff" onload='init();'>
<form name='form1' target='result' onsubmit='return validate();'>
<p>
<input class='textfield' id='metadataSearchString' name='search' type='text' style='width:110px'>

<img src="../img/add.png" style="cursor: pointer;vertical-align:middle;" title="<?php echo _mb("keywords") ?>" onclick="openKeywordPopup();">
<input type='submit' name='send' value='ok'>
</p>
</form>
<div id='resultDivTag' class='result'></div>
</body>
</html>
