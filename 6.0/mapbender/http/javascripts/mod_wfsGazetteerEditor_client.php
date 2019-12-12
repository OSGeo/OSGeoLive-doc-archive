<?php 
# $Id: mod_wfsGazetteerEditor_client.php 1414 2008-01-17 08:55:06Z diewald $
# maintained by http://www.mapbender.org/index.php/User:Verena Diewald
# http://www.mapbender.org/index.php/WFS_gazetteer
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
require_once(dirname(__FILE__)."/../../core/globalSettings.php");

$gui_id = Mapbender::session()->get("mb_user_gui");
$target = $_REQUEST["e_target"];
$e_id_css = $_REQUEST["e_id_css"];
$isLoaded = $_REQUEST["isLoaded"];


$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db($DB,$con);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='<?php echo CHARSET;?>'">
<title>mod_wfsGazetteerEditor</title>
<STYLE TYPE="text/css">
<!--
div.mainDiv {
	width: 330px;	 
}

input.op {
	width: 32px;	 	
}

div.helptext {
	visibility: hidden;
	display: none;
	position: absolute;
	top: 5%;
	left: 5%;
	width: 90%;
	padding: 5px;
	color: #000;
	background-color: #CCC;
	border: 1px solid #000;
	z-index:1000;
}

div.helptext p {
	margin: 0 ;
}

div.helptext a.close {
	display: block;
	margin: 5px auto;
	text-align: center;
}

a img {
	vertical-align: middle;
	border: 0;
}
-->
</STYLE>
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
<?php 
	include '../include/dyn_js.php';
	include '../include/dyn_php.php';
	include("../../conf/" . $wfs_spatial_request_conf_filename);

	echo "var targetString = '" . $target . "';";
	echo "var e_id_css = '" . $e_id_css . "';";

	require(dirname(__FILE__)."/../javascripts/wfsFilter.js");

?>
// Element var maxHighlightedPoints
try{
	if (maxHighlightedPoints){
		maxHighlightedPoints = Number(maxHighlightedPoints);

		if (isNaN(maxHighlightedPoints)) {
			var e = new parent.Mb_warning("mod_wfsGazetteerEditor_client.php: Element var maxHighlightedPoints must be a number.");
		}
	}
}
catch(e){
	maxHighlightedPoints = 0;
	var e = new parent.Mb_warning("mod_wfsGazetteerEditor_client.php: Element var maxHighlightedPoints is not set, see 'edit element vars'.");
}

var otherFrame = parent.window.frames[e_id_css];
<?php 

//var global_selectedWfsConfId = otherFrame.global_selectedWfsConfId;
$wfsConfId = $_REQUEST['wfsConfId'];
echo "var global_selectedWfsConfId = '$wfsConfId';\n";
?>
//var global_wfsConfObj = otherFrame.global_wfsConfObj;

var targetArray = targetString.split(",");
var global_resultHighlight;
var requestGeometryHighlight;
var point_px = 10;
var resultGeom = null;
var cw_fillcolor = "#cc33cc";
var frameName = e_id_css + "_";
var inputNotEnough = [];

var button_point = "point";
var button_polygon = "polygon";
var button_rectangle = "rectangle";
var button_extent = "extent";
var mb_wfs_tolerance = 8;
var activeButton = null;
var mod_wfs_spatialRequest_geometry = null;
var mod_wfs_spatialRequest_frameName = "";
var mod_wfs_spatialRequest_epsg;
var mod_wfs_spatialRequest_width;
var mod_wfs_spatialRequest_height;
var buttonWfs_id = [];
var buttonWfs_on = [];
var buttonWfs_src = [];
var buttonWfs_title_off = [];
var buttonWfs_title_on = [];
var buttonWfs_x = [];
var buttonWfs_y = [];


/**
 * This Geometry contains the geometry of the optinal spatial constraint
 */
var spatialRequestGeom = null;

/**
 * Something like box, polygon, point, extent
 */
var spatialRequestType = null;

/**
 * This Geometry contains the result from the WFS request
 */
var geomArray = null;

var frameIsReady = function () {
	var req = parent.Mapbender.Ajax.Request({
		url: 	"../php/mod_wfs_conf_server.php",
		method:	"getWfsConfsFromId",
		parameters: {
			wfsConfIdString: global_selectedWfsConfId 
		},
		callback: function(result,success,message){
		console.debug("b");
		console.debug(result);

			// the rest of the script doesn't expect an array, but an object, so the result must be converted
			global_wfsConfObj = {};
			for(var i in result){
				if(!result.hasOwnProperty(i)){ continue;}
				global_wfsConfObj[result[i].id] = result[i];

			}
			init_wfsSpatialRequest();
			appendWfsForm();
			appendStyles();
		
			setWfsInfo();
			
			// creates a Highlight object for the request geometry
			var styleProperties = {"position":"absolute", "top":"0px", "left":"0px", "z-index":100};
			requestGeometryHighlight = new parent.Highlight(targetArray, "requestGeometryHighlight", styleProperties, 2);
			parent.mb_registerSubFunctions("window.frames['" + frameName +"'].requestGeometryHighlight.paint()");
		}
	});
//	req.send();
	parent.mb_ajax_json("../php/mod_wfs_gazetteer_server.php", {command:"getWfsConf",wfsConfIdString: global_selectedWfsConfId }, function(json,status){
		global_wfsConfObj = json;
		init_wfsSpatialRequest();
		appendWfsForm();
		appendStyles();
	
		setWfsInfo();
		
		// creates a Highlight object for the request geometry
		var styleProperties = {"position":"absolute", "top":"0px", "left":"0px", "z-index":100};
		requestGeometryHighlight = new parent.Highlight(targetArray, "requestGeometryHighlight", styleProperties, 2);
		parent.mb_registerSubFunctions("window.frames['" + frameName +"'].requestGeometryHighlight.paint()");
	});

}

function showHelptext(helptextId) {
	hideHelptext();
	document.getElementById('helptext' + helptextId).style.visibility = 'visible';
	document.getElementById('helptext' + helptextId).style.display    = 'block';
}

function hideHelptext(helptextId) {
	if(helptextId) {
		document.getElementById('helptext' + helptextId).style.visibility = 'hidden';
		document.getElementById('helptext' + helptextId).style.display    = 'none';
	}

	var helptext = document.getElementsByTagName('div');
	
	for(var i = 0; i < helptext.length; i++) {
		if(helptext[i].className === 'helptext') {
			helptext[i].style.visibility = 'hidden';
			helptext[i].style.display    = 'none';
		}
	}
}

function trim(string) {
	return string.replace(/^\s+/, '').replace(/\s+$/, '');
}

function removeChildNodes(node) {
	if (node) {
		while (node.childNodes.length > 0) {
			var childNode = node.firstChild;
			node.removeChild(childNode);
		}
	}
}

/**
 * removes whitespaces and endlines before and after a string
 *
 */ 
function trimString (str) {
	return str.replace(/^\s+|\s+|\n+$/g, '');
}

function openwindow(Adresse) {
	Fenster1 = window.open(Adresse, "<?php echo _mb("Informations"); ?>", "width=500,height=500,left=100,top=100,scrollbars=yes,resizable=no");
	Fenster1.focus();
}

function setWfsInfo() {
	//
	// append bulb image
	//
	var bulbNode = document.getElementById("wfsInfo"); 	
	var imgNode = document.createElement("img");
	imgNode.id = "wfsInfoImg";
	imgNode.src = "../img/button_blue/getArea_off.png";
	imgNode.border = 0;
	bulbNode.appendChild(imgNode);
	bulbNode.href = "javascript:openwindow('../php/mod_showMetadata.php?resource=wfs-conf&id=" + global_selectedWfsConfId.toString() + "');";
	bulbNode.style.display = "inline";
	
	//
	// set image (pre configured or editor)
	//
	var wfsPreConfiguredOrEditor = document.getElementById("wfsPreConfiguredOrEditor");
	var preConfigured = false;
	for (var i=0; i < global_wfsConfObj[global_selectedWfsConfId].element.length; i++) {
		if (parseInt(global_wfsConfObj[global_selectedWfsConfId].element[i].f_search)) {
			preConfigured = true;
			break;
		}
	}
	if (preConfigured) {
		wfsPreConfiguredOrEditor.src = "../img/gnome/icn_suchmodul.png";
		wfsPreConfiguredOrEditor.title = "<?php echo _mb("Moduletype: Search"); ?>";
	}
	else {
		wfsPreConfiguredOrEditor.src = "../img/gnome/document-save.png";
		wfsPreConfiguredOrEditor.title = "<?php echo _mb("Moduletype: Download"); ?>";
	}
	wfsPreConfiguredOrEditor.style.display = 'inline';
	
	//
	// set wfsGeomType image
	//
	var wfsGeomTypeNode = document.getElementById("wfsGeomType");
	var wfsGeomType = "";
	for (var i=0; i < global_wfsConfObj[global_selectedWfsConfId].element.length; i++) {
		if (parseInt(global_wfsConfObj[global_selectedWfsConfId].element[i].f_geom)) {
			wfsGeomType = global_wfsConfObj[global_selectedWfsConfId].element[i].element_type;
		}
	}
	if (wfsGeomType.match(/Point/)) {
		wfsGeomTypeNode.src = "../img/button_digitize_blue_red/point.png";
		wfsGeomTypeNode.style.display = 'inline';
		wfsGeomTypeNode.title = '<?php echo _mb("Geometrytype: point"); ?>';
	}
	else if (wfsGeomType.match(/Line/)) {
		wfsGeomTypeNode.src = "../img/button_digitize_blue_red/line.png";
		wfsGeomTypeNode.style.display = 'inline';
		wfsGeomTypeNode.title = '<?php echo _mb("Geometrytype: line"); ?>';
	}
	else if (wfsGeomType.match(/Polygon/)) {
		wfsGeomTypeNode.src = "../img/button_digitize_blue_red/polygon.png";
		wfsGeomTypeNode.style.display = 'inline';
		wfsGeomTypeNode.title = '<?php echo _mb("Geometrytype: area"); ?>';
	}
	else {
		var e = new parent.Mb_exception("WFS gazetteer: geometry type unknown.");		
	}
}
/*
 * ---------------------------------------------------
 * BUTTON HANDLING
 * ---------------------------------------------------
 */
function addButtonWfs(id, isOn, src, title, x, y) {
	buttonWfs_id.push(id);
	buttonWfs_on.push(isOn);
	buttonWfs_src.push(src);
	buttonWfs_title_off.push(title);
	buttonWfs_title_on.push(title);
	buttonWfs_x.push(x);
	buttonWfs_y.push(y);
}

function init_wfsSpatialRequest() {
	buttonWfs_id = [];
	buttonWfs_on = [];
	buttonWfs_src = [];
	buttonWfs_title_off = [];
	buttonWfs_title_on = [];
	buttonWfs_x = [];
	buttonWfs_y = [];
	addButtonWfs("rectangle", buttonRectangle.status, buttonRectangle.img, buttonRectangle.title, buttonRectangle.x, buttonRectangle.y);
	addButtonWfs("polygon", buttonPolygon.status, buttonPolygon.img, buttonPolygon.title, buttonPolygon.x, buttonPolygon.y);
	addButtonWfs("point", buttonPoint.status, buttonPoint.img, buttonPoint.title, buttonPoint.x, buttonPoint.y);
	addButtonWfs("extent", buttonExtent.status, buttonExtent.img, buttonExtent.title, buttonExtent.x, buttonExtent.y);
	displayButtons();
}

function wfsInitFunction (j) {
	parent.mb_regButton_frame('initWfsButton',frameName,j);
} 

function initWfsButton(ind, pos) {
	parent.mb_button[ind] = document.getElementById(buttonWfs_id[pos]);
	parent.mb_button[ind].img_over = buttonWfs_imgdir + buttonWfs_src[pos].replace(/_off/,"_over");
	parent.mb_button[ind].img_on = buttonWfs_imgdir + buttonWfs_src[pos].replace(/_off/,"_on");
	parent.mb_button[ind].img_off = buttonWfs_imgdir + buttonWfs_src[pos];
	parent.mb_button[ind].status = 0;
	parent.mb_button[ind].elName = buttonWfs_id[pos];
	parent.mb_button[ind].frameName = frameName;
	parent.mb_button[ind].go = new Function ("requestGeometryHighlight.clean();wfsEnable(parent.mb_button["+ind+"], " + pos + ")");
	parent.mb_button[ind].stop = new Function ("wfsDisable(parent.mb_button["+ind+"], " + pos + ")");
	var ind = parent.getMapObjIndexByName("mapframe1");
	mod_wfs_spatialRequest_width = parent.mb_mapObj[ind].width;
	mod_wfs_spatialRequest_height = parent.mb_mapObj[ind].height;
	mod_wfs_spatialRequest_epsg = parent.mb_mapObj[ind].epsg;
	parent.mb_registerPanSubElement("measuring");
}	

function displayButtons() {
	for (var i = 0 ; i < buttonWfs_id.length ; i ++) {
		if (parseInt(buttonWfs_on[i])==1) {
			var currentImg = document.createElement("img");
			currentImg.id = buttonWfs_id[i];
			currentImg.name = buttonWfs_id[i];
			currentImg.title = buttonWfs_title_off[i];
			currentImg.src = buttonWfs_imgdir+buttonWfs_src[i];
			currentImg.style.position = "absolute";
			currentImg.style.top = buttonWfs_y[i];
			currentImg.style.left = buttonWfs_x[i];
			currentImg.onmouseover = new Function("wfsInitFunction("+i+")");
			
			document.getElementById("displaySpatialButtons").appendChild(currentImg);
		}
	}
}

function disableButtons() {
	removeChildNodes(document.getElementById("displaySpatialButtons"));
}

function wfsEnable(obj) {
   	var el = parent.window.document;
   	el.onmouseover = null;
   	el.onmousedown = null;
   	el.onmouseup = null;
   	el.onmousemove = null;
   	
	if (obj.id == button_point) {
		if (activeButton == null) {
			activeButton = obj;
		}
	}
	if (obj.id == button_polygon) {
		if (activeButton == null) {
			activeButton = obj;
		}
	}
	else if (obj.id == button_rectangle){
		if (activeButton == null) {
			activeButton = obj;
		}
	}
	else if (obj.id == button_extent){
		if (activeButton == null) {
			activeButton = obj;
		}
	}
	callRequestGeometryConstructor(obj, "mapframe1");
}


function wfsDisable(obj) {
	var el = parent.window.document; 
	el.onmousedown = null;
	el.ondblclick = null;
	el.onmousemove = null;
	parent.writeTag("mapframe1","measure_display","");
	parent.writeTag("mapframe1","measure_sub","");
	activeButton = null;
}

/*
 * ---------------------------------------------------
 * Request geometry 
 * --------------------------------------------------- 
 */

function callRequestGeometryConstructor(pressedButton,target){
	var selectedType = pressedButton.id;

	spatialRequestType = selectedType;
	var geometryConstructor = new parent.RequestGeometryConstructor(target);
	geometryConstructor.getGeometry(selectedType,function(target,queryGeom){
		//
		// callback function; called when query geometry has been 
		// constructed by user.
		//
		if (queryGeom !='') {
			spatialRequestGeom = queryGeom;

			// disable the selected button
			parent.mb_disableThisButton(pressedButton.elName);

			// spatialRequestGeom is a Geometry, but for the highlight
			// a MultiGeometry is needed.
			var multiGeom;
			// a line represents a bbox...but highlight must be a polyon
			// (extent or box selection)
			if (spatialRequestGeom.geomType == parent.geomType.line) {
				multiGeom = new parent.MultiGeometry(parent.geomType.polygon);
				newGeom = new parent.Geometry(parent.geomType.polygon);
				var p1 = spatialRequestGeom.get(0);
				var p2 = spatialRequestGeom.get(1);
				newGeom.addPoint(p1);
				newGeom.addPointByCoordinates(p1.x, p2.y);
				newGeom.addPoint(p2);
				newGeom.addPointByCoordinates(p2.x, p1.y);
				newGeom.close();
				multiGeom.add(newGeom);
			}
			// standard case
			// (polygon and point selection)
			else {
				multiGeom = new parent.MultiGeometry(spatialRequestGeom.geomType);
				multiGeom.add(spatialRequestGeom);
			}

			// add highlight of geometry
			requestGeometryHighlight.add(multiGeom);
			requestGeometryHighlight.paint();

			// add button for geometry deletion
			var deleteGeomButton = document.getElementById("deleteRequestGeometry");
			deleteGeomButton.style.display = "block";
			deleteGeomButton.onclick = function () {
				spatialRequestGeom = null;
				this.style.display = "none";
				requestGeometryHighlight.clean();
				requestGeometryHighlight.paint();
			}
		}
	});
}

//----------------------------------------------------------------------------------


/*
 * Appends styles to the pre-configured WFS form
 */
function appendStyles() {
	var styleObj;
	var rule = global_wfsConfObj[global_selectedWfsConfId].g_style + global_wfsConfObj[global_selectedWfsConfId].g_res_style;
	if (parent.ie) {
		var styleSheetObj=document.createStyleSheet();
		styleObj=styleSheetObj.owningElement || styleSheetObj.ownerNode;
		styleObj.setAttribute("type","text/css");
		ruleArray = rule.split("}");
		for (var i=0; i < ruleArray.length - 1; i++) {
			var currentRule = trimString(ruleArray[i]);
			var nameValueArray = currentRule.split("{");
			var name = nameValueArray[0];
			var value = nameValueArray[1];
			styleSheetObj.addRule(name,value);
		}
	}
	else {
		styleObj=document.createElement("style");
		styleObj.setAttribute("type","text/css");
		document.getElementsByTagName("head")[0].appendChild(styleObj);
		styleObj.appendChild(document.createTextNode(rule+"\n"));		
	}
}

function isSearchPreconfigured () {
	var wfsConfElementArray = global_wfsConfObj[global_selectedWfsConfId].element;
	for (var i = 0; i < wfsConfElementArray.length; i++){
		if (parseInt(wfsConfElementArray[i].f_search)) {
			return true;
		}
	}
	return false;
}

function appendWfsForm() {
	var form = document.getElementById("wfsForm");
	removeChildNodes(form);

	if(!isSearchPreconfigured()){
		/*
		 * Appends the WFS editor
		 */
		document.getElementById("mainDiv").style.display = "block";
		fillLeftList();
	}
	else{
		
		/*
		 * Appends the pre-configured WFS form
		 */
		var divContainer = document.createElement("div");
		divContainer.className = global_wfsConfObj[global_selectedWfsConfId].g_label_id;
		divContainer.innerHTML = global_wfsConfObj[global_selectedWfsConfId].g_label;
		
		form.appendChild(divContainer);
	
		var wfsConfElementArray = global_wfsConfObj[global_selectedWfsConfId].element;
			
		for (var i = 0; i < wfsConfElementArray.length; i++){
			if (parseInt(wfsConfElementArray[i].f_search)) {
				var spanNode = document.createElement("span");
				spanNode.setAttribute("id", "ttttt");
				spanNode.className = wfsConfElementArray[i].f_label_id;
				spanNode.innerHTML = wfsConfElementArray[i].f_label;
				var inputNode = document.createElement("input");
				inputNode.type = "text";
				inputNode.className = wfsConfElementArray[i].f_style_id;
				inputNode.id = wfsConfElementArray[i].element_name;
				
				if(wfsConfElementArray[i].f_helptext && wfsConfElementArray[i].f_helptext.length > 0) {
					var helptextNode    = document.createElement('span');
					
					var helptextDisplay = document.createElement('div');
					helptextDisplay.id        = 'helptext' + i;
					helptextDisplay.className = 'helptext';
					
					var helptext = document.createElement('p');
					helptext.innerHTML  = wfsConfElementArray[i].f_helptext.replace(/(http:\/\/\S*)/g,'<a href="$1" target="blank">$1<\/a>');
					helptext.innerHTML += '<a href="#" class="close" onclick="hideHelptext(' + i + ')">close</a>';
					helptextDisplay.appendChild(helptext);
					
					helptextNode.innerHTML = ' <a href="#" onclick="showHelptext(' + i + ')"><img src="../img/help.png" width="16" height="16" alt="?" /></a> ';
				}
				
				form.appendChild(spanNode);
				form.appendChild(inputNode);
				if(wfsConfElementArray[i].f_helptext.length > 0) { form.appendChild(helptextNode); }
				if(wfsConfElementArray[i].f_helptext.length > 0) { form.appendChild(helptextDisplay); }
				form.appendChild(document.createElement("br"));
			}
		}

		var submitButton = document.createElement("input");
		submitButton.type = "submit";
		submitButton.id = "wfsForm_Submit";
		submitButton.className = global_wfsConfObj[global_selectedWfsConfId].g_button_id;
		submitButton.value = global_wfsConfObj[global_selectedWfsConfId].g_button;
		
		form.appendChild(submitButton);
	}
	
	checkSrs();
}
function checkSrs(){
	//check SRS
	var ind = parent.getMapObjIndexByName("mapframe1");
	var submit = document.getElementById("wfsForm_Submit");
	var submit_attr = document.getElementById("attrPanel_Submit");
	if(global_wfsConfObj[global_selectedWfsConfId].featuretype_srs!=parent.mb_mapObj[ind].getSRS()){
		var msg = "<?php echo _mb("The coordinate reference system (crs) of the objects differ from the crs of the viewer. A query will not be possible. Please change the crs of the viewer first."); ?>\n";
		msg += parent.mb_mapObj[ind].getSRS()+" und "+global_wfsConfObj[global_selectedWfsConfId].featuretype_srs;
		alert(msg);
			
		//disable Submit Button
		if(submit)submit.disabled = true;
		if(submit_attr)submit_attr.disabled = true;
			
	}
	else{
		//disable Submit Button
		if(submit)submit.disabled = false;
		if(submit_attr)submit_attr.disabled = false;
	}
}


//------------------------------------------------- start Editor functions



function fillLeftList() {
	deleteSelectOptions(document.attrPanel.leftList);
	deleteSelectOptions(document.attrPanel.rightList);
	deleteFilterText();
	
	var wfsConf = global_wfsConfObj[global_selectedWfsConfId];
	for (var i = 0 ; i < wfsConf.element.length ; i++) {
		// do not include geometry column
		if (wfsConf.element[i].f_geom != 1) {
			var lLOption = document.createElement("option");
			lLOption.id = "lLOPtion"+i+"_"+wfsConf.element[i].element_type;
			lLOption.value = i;
			lLOption.innerHTML = wfsConf.element[i].element_name;
			document.getElementById("leftList").appendChild(lLOption);
		}
	}
}

 function fillRightList() {
 	var chosenLayer = global_wfsConfObj[global_selectedWfsConfId].featuretype_name;
	var layerAttr=document.getElementById("leftList");
	var chosenAttr =layerAttr.options[layerAttr.selectedIndex].text;
	var wfs_getfeature = global_wfsConfObj[global_selectedWfsConfId].wfs_getfeature;
	
	deleteSelectOptions(document.attrPanel.rightList);
	
	document.getElementById("progressWheel").innerHTML = "<table><tr><td><img src='../img/indicator_wheel.gif'></td><td>Get features...</td></tr></table>";
		
	var properties = {"command":"getFeature", "wfsFeatureTypeName":chosenLayer, "wfsGetFeatureAttr":chosenAttr, "wfsGetFeature":wfs_getfeature};
	parent.$.post("../javascripts/mod_wfsGazetteerEditor_server.php", properties, function (result, status) {		
		if (status == "success") {
		   var arrFeatureColumnValues = eval('(' + result + ')');
      	   var i = 0;
      	   for( var i=0; i<arrFeatureColumnValues.length; i++){
      	   		removeChildNodes(document.getElementById("progressWheel"));
      	   		if(arrFeatureColumnValues[i]!=''){
	    	    	var rLOption = document.createElement("option");
	   				rLOption.id = "rLOption"+i;
	   				rLOption.value = i;
	   				rLOption.innerHTML = arrFeatureColumnValues[i];
	   				document.getElementById("rightList").appendChild(rLOption);
   				}
 			}
		}
	});
 }

/**
 * writeLeftValueInString()
 * @param {}  
 * Doppelklick auf Listeneintrag (Links) => Listenwert wir in Textarea geschrieben
 */
function writeLeftValueInString(){
 	var layerAttr=document.getElementById("leftList");
	var chosenAttr = layerAttr.options[layerAttr.selectedIndex].text;
	if(layerAttr.options[layerAttr.selectedIndex].id.match("int") || layerAttr.options[layerAttr.selectedIndex].id.match("float")){
		controlOperators("int");
	}
	else if(layerAttr.options[layerAttr.selectedIndex].id.match("string")){
		controlOperators("string");
	}
	else if(layerAttr.options[layerAttr.selectedIndex].id.match("date")){
		controlOperators("date");
	}
	else{
		controlOperators("all");
	}
	var attrTxt = "[" + chosenAttr + "]";
	insertAtCursor(document.attrPanel.attrRequestText, attrTxt);

}

function hideOperators(){
	document.getElementById('greaterThan').style.visibility = "hidden";
	document.getElementById('lessThan').style.visibility = "hidden";
	document.getElementById('lessThanOrEqualTo').style.visibility = "hidden";
	document.getElementById('greaterThanOrEqualTo').style.visibility = "hidden";
	document.getElementById('and').style.visibility = "hidden";
	document.getElementById('like').style.visibility = "hidden";
	document.getElementById('equal').style.visibility = "hidden";
	document.getElementById('notEqual').style.visibility = "hidden";
}

function controlOperators(opType){
	document.getElementById('greaterThan').style.visibility = "visible";
	document.getElementById('lessThan').style.visibility = "visible";
	document.getElementById('lessThanOrEqualTo').style.visibility = "visible";
	document.getElementById('greaterThanOrEqualTo').style.visibility = "visible";
	document.getElementById('and').style.visibility = "visible";
	document.getElementById('like').style.visibility = "visible";
	document.getElementById('equal').style.visibility = "visible";
	document.getElementById('notEqual').style.visibility = "visible";
	if(opType=='int'){
		document.getElementById('like').style.visibility = "hidden";
	}
	else if(opType=='string'){
		document.getElementById('greaterThan').style.visibility = "hidden";
		document.getElementById('lessThan').style.visibility = "hidden";
		document.getElementById('lessThanOrEqualTo').style.visibility = "hidden";
		document.getElementById('greaterThanOrEqualTo').style.visibility = "hidden";
	}
	else if(opType=='date'){
		// do something
	}
	else{
		// do something
	}
}

/**
 * writeRightValueInString()
 * @param {}  
 * Doppelklick auf Listeneintrag (Rechts) => Listenwert wir in Textarea geschrieben
 */
function writeRightValueInString(){
 	var chosenVal =document.getElementById("rightList").options[document.getElementById("rightList").selectedIndex].text;
	insertAtCursor(document.attrPanel.attrRequestText,chosenVal);
}


/**
* insertOperator
* @param {type} param 
*/
 function insertOperator(value) {
	var wert = value;  	
 	insertAtCursor(document.attrPanel.attrRequestText, wert);
 }

/**
 * insertAtCursor
 * @param {Textfeld, String} param 
 * Fuegt an die Cursorstelle einen Text(String) ein
 */
function insertAtCursor(myField, myValue) {
//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
	}
//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		+ myValue
		+ myField.value.substring(endPos, myField.value.length);
	} else {
	myField.value += myValue;
	}
}

/**
 * deleteSelectOptions()
 * @param {}  
 * 
 */
 
function deleteSelectOptions(field){
	var numOfOpt = field.length;
	for (i=0; i <numOfOpt; i++){
		field.options[field.length-1] = null;
	}
}

/**
 * deleteFilterText()
 * @param {type}
 * Button loescht Eintrag im Textfeld (attrRequestText)  
 */
 function deleteFilterText() {
 	document.attrPanel.attrRequestText.value="";
 }
 
 
 //-------------------------------------------------------------------------
 
function createWfsFilter () {
	var wfsFilter = new WfsFilter();

	/*
	 * Add filter from WFS editor
	 */
 	
	if (!isSearchPreconfigured()) {
		var filter = document.attrPanel.attrRequestText.value;
	
		if (!wfsFilter.parse(filter)) {
			alert("Keine gültige Filterbedingung gesetzt.");
			return false;
		}
	}
	

	/*
	 * Add filter from pre configured searches
	 */
	var filterParameterCount = getNumberOfFilterParameters();
	var el = global_wfsConfObj[global_selectedWfsConfId].element;
	
	if (filterParameterCount != 0) {
		for (var i = 0; i < el.length; i++) {
			if (el[i]['f_search'] == 1) {
		
				var a = document.getElementById(el[i]['element_name']).value.split(",");
				wfsFilter.addPreConfigured(el[i]['element_name'], a, el[i]['f_toupper'], el[i]['f_operator']);
			}
		}
	} 
	/*
	 * Add filter from request geometry
	 */
	if (spatialRequestGeom != null) {
	
		// get SRS
		var srs = global_wfsConfObj[global_selectedWfsConfId].featuretype_srs;
		
		// get geometry column name
		var geometryColumnName = "";
		for (var j = 0; j < el.length; j++) {
			if (el[j].f_geom == 1) {
				geometryColumnName = el[j].element_name;
			}
		}
		
		// get filter option
		var filterOption = "";
		var selectedButton;
		if (spatialRequestGeom.geomType == parent.geomType.polygon) {
			selectedButton = buttonPolygon;
		}
		else 
			if (spatialRequestGeom.geomType == parent.geomType.line) {
				selectedButton = buttonRectangle;
			}
		
		if (selectedButton.filteroption == 'within') {
			filterOption = "Within";
		}
		else 
			if (selectedButton.filteroption == 'intersects') {
				filterOption = "Intersects";
			}
		
		// add spatial filter
		wfsFilter.addSpatial(spatialRequestGeom, geometryColumnName, filterOption, srs);
	}		
	return wfsFilter.toString();
}
 
/**
 * The filter is calculated from the WFS editor or the pre-comfigured
 * search form, with optional request geometry 
 */
 function makeRequest() {
	// hide the result and detail popup
 	if (typeof(resultGeometryPopup) != "undefined") {
 		resultGeometryPopup.dialog('close');
 	}
 	if (typeof(wfsPopup) != "undefined") {
 		wfsPopup.dialog('close');
 	}
	
	// empty the result geometry
// geomArray not used any more
//	if(geomArray != null && geomArray.count()>0){
// 		geomArray.empty();
// 	}
	
	// set the Highlight object for the result geometry
	var styleProperties = {"position":"absolute", "top":"0px", "left":"0px", "z-index":100};
	global_resultHighlight = new parent.Highlight(targetArray, "wfsGazetteerEditorHighlight", styleProperties, 2);

	/*
	 * Send WFS request
	 */
	document.getElementById("progressWheel").innerHTML = "<table><tr><td><img src='../img/indicator_wheel.gif'></td><td>Searching...</td></tr></table>";

	var u = global_wfsConfObj[global_selectedWfsConfId].wfs_getfeature + parent.mb_getConjunctionCharacter(global_wfsConfObj[global_selectedWfsConfId].wfs_getfeature);
	u += "REQUEST=getFeature&Typename="+global_wfsConfObj[global_selectedWfsConfId].featuretype_name+"&Version=1.0.0&service=WFS";
	u += "&filter=";
	
	var exportToShape = document.getElementById("exportToShape").checked;
	
	var parameters = {
		command:"getSearchResults", 
		"wfs_conf_id":global_selectedWfsConfId, 
		"frame":this.name, 
		"url":u, 
		"filter":createWfsFilter(), 
		"backlink":"",
		"exportToShape":exportToShape
	};
	
	if (!isSearchPreconfigured()) {
		parent.mb_ajax_get("../javascripts/mod_wfsGazetteerEditor_server.php", parameters, function (jsCode, status) {
			if(status=='success'){
				for (var i=0; i < parent.wms.length; i++) {
					for (var j=0; j < parent.wms[i].objLayer.length; j++) {
						var currentLayer = parent.wms[i].objLayer[j];
						var wms_id = parent.wms[i].wms_id; 
						if (currentLayer.gui_layer_wfs_featuretype == global_selectedWfsConfId) {
							var layer_name = currentLayer.layer_name; 
							parent.handleSelectedLayer_array(targetArray[0],[wms_id],[layer_name],'querylayer',1); 
							parent.handleSelectedLayer_array(targetArray[0],[wms_id],[layer_name],'visible',1);					
						}
					}
				}
				var geoObj = jsCode; //eval('(' + jsCode + ')');	
	       		if (!exportToShape) {
	       			if (jsCode) {
			        	if (typeof(geoObj) == 'object') {
							displayPopup(geoObj);
						}
						else {
							displayPopup();
						}
					}
		       		else {
						alert("<?php echo _mb("No result"); ?>");
					}
	       		}
	       		else {
	       			if (geoObj) {
						var htmlCodeDownload = "<iframe style='visibility:visible' src='../tmp/"+geoObj.filename + "'></iframe>";
						var downloadPopup = new parent.mb_popup("Download", htmlCodeDownload, 1, 1, -10, 1);
						downloadPopup.show();
						downloadPopup.hide();
					}
		       		else {
						alert("<?php echo _mb("An error occured when start download."); ?>");
		       		}
				}
				document.getElementById("progressWheel").innerHTML = "";
			}
		});
	}
	else{
		if(inputNotEnough.length==0){
			parent.mb_ajax_get("../javascripts/mod_wfsGazetteerEditor_server.php", parameters, function (jsCode, status) {
				if(status=='success'){
					for (var i=0; i < parent.wms.length; i++) {
						for (var j=0; j < parent.wms[i].objLayer.length; j++) {
							var currentLayer = parent.wms[i].objLayer[j];
							var wms_id = parent.wms[i].wms_id; 
							if (currentLayer.gui_layer_wfs_featuretype == global_selectedWfsConfId) {
								var layer_name = currentLayer.layer_name; 
								parent.handleSelectedLayer_array(targetArray[0],[wms_id],[layer_name],'querylayer',1); 
								parent.handleSelectedLayer_array(targetArray[0],[wms_id],[layer_name],'visible',1);					
							}
						}
					}
					var geoObj = jsCode; //eval('(' + jsCode + ')');	
		       		if (!exportToShape) {
		       			if (jsCode) {
				        	if (typeof(geoObj) == 'object') {
								displayPopup(geoObj);
							}
							else {
								displayPopup();
							}
						}
			       		else {
							alert("Kein Ergebnis.");
						}
		       		}
		       		else {
		       			if (jsCode) {
							var htmlCodeDownload = "<iframe style='visibility:visible' src='../geoportal/download.php?download=" + geoObj.filename + "'></iframe>";
							var downloadPopup = new parent.mb_popup("Download", htmlCodeDownload, 1, 1, -10, 1);
							downloadPopup.show();
							//downloadPopup.hide();
						}
			       		else {
							alert("Beim Export ist ein Fehler aufgetreten.");
			       		}
					}
					document.getElementById("progressWheel").innerHTML = "";
				}
			});	
		}
		else{
			return false;
		}
	}
	return false;
 }


function getListTitle(geom){
	var wfsConf = global_wfsConfObj[global_selectedWfsConfId];
	var resultObj = {};
	for (var i = 0 ; i < wfsConf.element.length ; i++) {
		var currentElement = wfsConf.element[i];
		if (currentElement.f_show == 1 && geom.e.getElementValueByName(currentElement.element_name) != false) {
			var pos = currentElement.f_respos;
			if (pos > 0) {
				resultObj[pos] = geom.e.getElementValueByName(currentElement.element_name);
			}
		}
	}
	return resultObj;
}

function createListOfGeometries(){
	var listOfGeom = "<table style='background-color:#EEEEEE;'>\n";
	if (geomArray != null && geomArray.count() > 0) {
		for (var i = 0 ; i < geomArray.count(); i ++) {
			if (geomArray.get(i).get(-1).isComplete()) {
				listOfGeom += "\t<tr>\n\t\t ";
				var resultElObj = getListTitle(geomArray.get(i));
				for (var wfsConfEl in resultElObj) {
					if(resultElObj[wfsConfEl]!=''){
						listOfGeom += "<td style='cursor:pointer;\n";
						if ((i % 2) === 0) {
							listOfGeom += "color:black'";
						}
						else {
							listOfGeom += "color:black'";
						}
						listOfGeom += "\t\t\t onmouseover=\"window.frames['"+frameName+"'].setResult('over',"+i+")\" ";
						listOfGeom += " onmouseout=\"window.frames['"+frameName+"'].setResult('out',"+i+")\" ";
						listOfGeom += " onclick=\"window.frames['"+frameName+"'].setResult('click',"+i+"); window.frames['"+frameName+"'].showWfs("+i+");\" ";
						listOfGeom += ">"+ resultElObj[wfsConfEl] +"</td>";
					}
				}	
				listOfGeom += "\t\t</tr>\n"; 
			}
		}
	}
	listOfGeom += "</table>\n";
	return listOfGeom; 
}

function displayPopup(geom){
	geomArray = geom;

	if(!parent.$("#resultList")){
		return;
	}
	var resultList = parent.$('#resultList').mapbender();
	resultList.clear();
	resultList.setTitle(global_wfsConfObj[global_selectedWfsConfId].wfs_conf_abstract);
	resultList.setWFSconf(global_wfsConfObj[global_selectedWfsConfId]);
	resultList.addFeatureCollection(geom);
	resultList.show();
	return;

	var contentHtml = "<?php echo _mb("No result"); ?>";
	if (geomArray != null && geomArray.count() > 0){
		contentHtml = createListOfGeometries();
	}

	removeChildNodes(document.getElementById("progressWheel"));	

	if (typeof(resultGeometryPopup) == "undefined") {
		resultGeometryPopup = parent.$("<div>"+ contentHtml +"</div>").dialog({
			title: searchPopupTitle,
			width:searchPopupWidth,
			height: searchPopupHeight,
			position: [ searchPopupX,searchPopupY],
			onclose: function(){
				resultList.hide();
			}
		}); 
	}
	else {
		resultGeometryPopup.html(contentHtml);
	}
	resultGeometryPopup.dialog('open');
} 


function showWfs(geometryIndex) {
	var wfsConf = global_wfsConfObj[global_selectedWfsConfId];
	var wfsElement = geomArray.get(geometryIndex).e;
	
	var resultHtml = "";
	resultHtml += "<table style='background-color:#EEEEEE;'>\n";
	var details = 0;
	for (var i = 0 ; i <wfsConf.element.length; i ++) {
		if(wfsConf.element[i].f_show_detail == 1){
			resultHtml +="<tr><td>\n"; 
			resultHtml += wfsConf.element[i].element_name;
			resultHtml +="</td>\n"; 
			resultHtml += "<td>\n";
			var elementVal = wfsElement.getElementValueByName(wfsConf.element[i].element_name); 
			resultHtml += elementVal;
			resultHtml += "</td></tr>\n";
			details = 1;
		}
	}
//	if(details != 1){
//		resultHtml +="<tr><td><?php echo _mb("No information available."); ?></td></tr>\n";
//	}
	resultHtml += "</table>\n";
	
	if(details == 1){
		if (typeof(wfsPopup) == "undefined") {
			wfsPopup = parent.$("<div>"+resultHtml+"</div>").dialog({
				title: detailPopupTitle,
				width: detailPopupWidth,
				height: detailPopupWidth,
				position: [detailPopupX,detailPopupY]
			});
		}
		else {
			wfsPopup.html(resultHtml);
		}
		wfsPopup.dialog('open');	
	}
}
//------------------------------------------------- end Editor functions


function getNumberOfFilterParameters(){
	var cnt = 0;
	var el = global_wfsConfObj[global_selectedWfsConfId].element;
	inputNotEnough = [];
	
	for (var i = 0; i < el.length; i++){
		if( el[i]['f_search'] == 1){
			if (document.getElementById(el[i]['element_name']).value != '') {
				cnt++;
			}
			if(document.getElementById(el[i]['element_name']).value.length < el[i]['f_min_input']){
				inputNotEnough.push(el[i]['element_name']+"("+el[i]['f_min_input']+")");
			}
		}
	}
	if(inputNotEnough.length>0){
		alert("Pflichtfelder: "+inputNotEnough.join(', '));
		document.getElementById("progressWheel").innerHTML = "";
		return false;
	}
	
	return cnt;
}
/*
* event -> {over || out || click}
* geom -> commaseparated coordinates x1,y1,x2,y2 ...
*/
function setResult(event, index){
	var currentGeom = geomArray.get(index);
	if (maxHighlightedPoints > 0 && currentGeom.getTotalPointCount() > maxHighlightedPoints) {
		currentGeom = currentGeom.getBBox4();
	}
	if (event == "over") {
		global_resultHighlight.add(currentGeom, cw_fillcolor);
		global_resultHighlight.paint();
	}
	else if (event == "out"){
		global_resultHighlight.del(currentGeom, cw_fillcolor);
		global_resultHighlight.paint();
	}
	else if (event == "click"){
		global_resultHighlight.del(currentGeom, cw_fillcolor);
		var bbox = currentGeom.getBBox();
		var bufferFloat = parseFloat(global_wfsConfObj[global_selectedWfsConfId].g_buffer);
		var buffer = new parent.Point(bufferFloat,bufferFloat);
		bbox[0] = bbox[0].minus(buffer);
		bbox[1] = bbox[1].plus(buffer);
		parent.mb_calculateExtent(targetArray[0], bbox[0].x, bbox[0].y, bbox[1].x, bbox[1].y);
		parent.zoom(targetArray[0], 'true', 1.0);
		global_resultHighlight.add(currentGeom, cw_fillcolor);
		global_resultHighlight.paint();
	}
	return true;
} 
</script>
</head>
<body leftmargin='0' topmargin='10' bgcolor='#ffffff' onload='frameIsReady()'> <!-- onload='initModWfsGazetteer();init_wfsSpatialRequest();'  -->
	<!-- WFS conf info -->
	<img src = "" name='wfsPreConfiguredOrEditor' id='wfsPreConfiguredOrEditor' style='display:none'>
	<img src = "" name='wfsGeomType' id='wfsGeomType' style='display:none'>
	<a name='wfsInfo' title='<?php echo _mb("Show information"); ?>' id='wfsInfo' style='display:none'></a><form name='attrPanel' id='attrPanel' onsubmit='return makeRequest();'>


	<div class='mainDiv' id='mainDiv' style="display:none">
	<b><?php echo _mb("Set spatial filter"); ?></b>
	<div name='displaySpatialButtons' id='displaySpatialButtons' style='width:180px'></div>
	<input type="button" id="deleteRequestGeometry" name="deleteRequestGeometry" value="löschen" style="position:absolute;top:70px;left:120px;display:none">
	<br><br>
	<b><?php echo _mb("Set attribute filter"); ?></b>
	  <table>
	  	<tr valign='top'>
	  		<td width='30%'>&nbsp;</td><td width='35%'>&nbsp;</td><td width='35%' style='font-size:12px;'><?php echo _mb("Examples (first 20 entries):"); ?></td>
	  	</tr>
	    <tr valign='top'>
	      	<td width='30%'>
	      		<select name='leftList' id='leftList' onChange='fillRightList();' onDblClick='writeLeftValueInString();' size='8' style='width:120px;'></select>
			</td>
			<td width='35%'>
				<input id='greaterThan' class='op' onClick='insertOperator(value);' type='button' value='>>' name='op1'>
				<input id='greaterThanOrEqualTo' class='op' onClick='insertOperator(value);' type='button' value='>=' name='op2'><br>
				<input id='lessThan' class='op' onClick='insertOperator(value);' type='button' value='<<' name='op3'>
				<input id='lessThanOrEqualTo' class='op' onClick='insertOperator(value);' type='button' value='<=' name='op4'><br>
				<input id='equal' class='op' onClick='insertOperator(value);' type='button' value='==' name='op5'>
				<input id='notEqual' class='op' onClick='insertOperator(value);' type='button' value='<>' name='op6'><br>
				<input id='like' class='op' onClick='insertOperator(value);' type='button' value=' LIKE ' name='op7'>
				<input id='and' class='op' onClick='insertOperator(value);' type='button' value=' AND ' name='op8'><br>
			</td>
			<td width='35%'>
				<select name='rightList' id='rightList' onDblClick='writeRightValueInString();' size='8' style='width:120px;'>
				</select>
			</td>
		</tr> 
	  </table>
	  
	<br>
	<table>
	    <tr>
	      	<td style='width:80%;height:40' colspan=4>	
	      		<textarea cols='48' rows='5' name='attrRequestText' id='attrRequestText' ></textarea>
			</td>
	    </tr> 
	    <tr >
	      	<td>	      		
	      		 <input type='submit' id='attrPanel_Submit' value='<?php echo _mb("Start query"); ?>'>
	      	</td>	      		
			<td>
				<input onClick='deleteFilterText();' type='button' value='<?php echo _mb("Reset filter"); ?>'>
			</td>
	      	<td>	      		
				<div name='displayCheckbox' id='displayCheckbox' style='width:180px'>
					<input type='checkbox' name='exportToShape' id='exportToShape'><?php echo _mb("Export data"); ?>
				</div>
	      	</td>	      		
	    </tr> 
	  </table>
	</div>
</form>
<form name='wfsForm' id='wfsForm' onsubmit='return makeRequest()'></form>
<div name='progressWheel' id='progressWheel' style='width:180px'></div>
</body>
</html>
