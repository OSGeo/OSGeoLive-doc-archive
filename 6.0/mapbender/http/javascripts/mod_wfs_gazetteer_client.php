<?php
# $Id: mod_wfs_gazetteer_client.php 8005 2011-07-26 21:27:54Z marc $
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

require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

$target = $_REQUEST["e_target"];
$isLoaded = $_REQUEST["isLoaded"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title>mod_wfs_gazetteer</title>

<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
/**
 * Package: gazetteerWFS
 *
 * Description:
 * A WFS gazetteer for pre configured WFS configurations.
 *
 * Files:
 *  - http/javascripts/mod_wfs_gazetteer_client.php
 *  - http/php/mod_wfs_gazetteer_server.php
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment,
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width,
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file,
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>','gazetteerWFS',
 * > 2,1,'a gazetteer for user in the mapbender user map','Search','iframe',
 * > '../javascripts/mod_wfs_gazetteer_client.php?sessionID&target=mapframe1,overview',
 * > 'frameborder = "0"',10,600,300,150,4,
 * > 'visibility:hidden; border: 1px solid #a19c8f;','','iframe','',
 * > 'geometry.js,requestGeometryConstructor.js,popup.js,../extensions/wz_jsgraphics.js',
 * > 'mapframe1,overview','','http://www.mapbender.org/GazetteerWFS');
 * >
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES ('<appId>', 'gazetteerWFS',
 * > 'wfsConfIdString', '<value>', 'comma seperated list of WFS conf ids' ,
 * > 'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'gazetteerWFS',
 * > 'wfs_spatial_request_conf_filename', 'wfs_additional_spatial_search.conf',
 * > 'location and name of the WFS configuration file for spatialRequest' ,
 * > 'php_var');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'gazetteerWFS',
 * > 'initializeOnLoad', '0', 'start gazetteer onload' ,'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'gazetteerWFS',
 * > 'enableSearchWithoutParams', '0',
 * > 'define that search can be started without any search params' ,
 * > 'var');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name,
 * > var_value, context, var_type) VALUES('<appId>', 'gazetteerWFS',
 * > 'removeSpatialRequestHighlight', '0', 'remove spatialrequest highlighting when firing search' ,'var');
 *
 * Help:
 * http://www.mapbender.org/GazetteerWFS
 *
 * Maintainer:
 * http://www.mapbender.org/User:Verena_Diewald
 *
 * Parameters:
 * wfsConfIdString - comma seperated list of WFS conf ids
 * wfs_spatial_request_conf_filename - location and name of the WFS
 * 			configuration file for spatialRequest
 * initializeOnLoad - start gazetteer onload
 * enableSearchWithoutParams - define that search can be started without any
 * 			search params
 * removeSpatialRequestHighlight - remove spatialrequest highlighting when firing search
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
<?php
	include '../include/dyn_js.php';
	include '../extensions/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js';
	include '../include/dyn_php.php';
	include(dirname(__FILE__) . "/../../conf/" . $wfs_spatial_request_conf_filename);

	echo "var targetString = '" . $target . "';";
	echo "var wfsConfIdString = '" . $wfsConfIdString . "';";
	echo "var e_id_css = '" . $e_id_css . "';";
?>

// Element var showResultInPopup
try {if(showResultInPopup){}}catch(e) {showResultInPopup = 1;}

//element var openLinkFromSearch for opening attribute link directly onclick of searchResult entry
try{
	if (openLinkFromSearch){}
}
catch(e){
	openLinkFromSearch =0;
}

// Element var to initialize the start of the gazetteer on load of the application
try {if(initializeOnLoad){}}catch(e) {initializeOnLoad = 0;}

// Element var to allow search without any search params
try {if(enableSearchWithoutParams){}}catch(e) {enableSearchWithoutParams = 0;}

//Element var to remove spatialrequest highlighting when firing search
try {if(removeSpatialRequestHighlight){}}catch(e) {removeSpatialRequestHighlight = 0;}

var targetArray = targetString.split(",");
var global_wfsConfObj;
var global_selectedWfsConfId;
var point_px = 10;
var resultGeom = null;
var frameName = e_id_css;
var inputNotEnough = [];

//start button management spatialRequest ////////
var button_point = "point";
var button_polygon = "polygon";
var button_rectangle = "rectangle";
var button_extent = "extent";

var activeButton = null;
var mod_wfs_spatialRequest_geometry = null;
var mod_wfs_spatialRequest_frameName = "";
var mod_wfs_spatialRequest_epsg;
var mod_wfs_spatialRequest_width;
var mod_wfs_spatialRequest_height;

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
var geomArray;

var buttonWfs_id = [];
var buttonWfs_on = [];
var buttonWfs_src = [];
var buttonWfs_title_off = [];
var buttonWfs_title_on = [];
var buttonWfs_x = [];
var buttonWfs_y = [];



function isEmpty(obj){
  for(var j in obj){ if(obj.hasOwnProperty(j)) { return false;}}
  return true;
}

// traverses a tree, throwing out
// all paths whose labels don't
// match the regular expression in filter
function filteredCopy(obj,filter){
  filter = filter || /.*/;
  obj    = obj    || {};

  var deleteStack = [];
  var subtree = null;
  var entry   = null;

  // visit each node, and check it's subtree for
  // nodes containing "filter"
  // if it doesn contain any of those, put the subtree index
  // on a list
  for(subtree in obj)
  {
    if(obj.hasOwnProperty(subtree))
    {
      var type = typeof(obj[subtree]);
      if(type == "object"){
        filteredSubtree =  filteredCopy(obj[subtree],filter);
        if(isEmpty(filteredSubtree))
        {
          // empty objects are leafnodes, so if they don't match
          // we mark for deletion
          if(!filter.test(subtree))
          {
            deleteStack.push(subtree);
          }
        }
      }else if (type == "string") {
        // strings are leafnodes, so if they don't match our regex
        // we add them to the list of thing we want removed
        if(!filter.test(subtree))
        {
          deleteStack.push(subtree);
        }
      }
    }
  }

  for( entry in deleteStack)
  {
    if(deleteStack.hasOwnProperty(entry)) {
      delete(obj[deleteStack[entry]])
    }
  }
  return obj;
}

function localizeGazetteer(){

  // make overlaytree containing only entries to be translated
  var inObj = $.extend(true,{},global_wfsConfObj);
  var labels = filteredCopy(inObj,/button_id$|label$/);

  var  req  =  new parent.Mapbender.Ajax.Request({
      url: "../php/mod_wfs_gazetteer_l10n.php",
      method: "translateServiceData" ,
      parameters: {data : labels},
      callback: function(obj, success, message){
          resultObj = $.extend(true,global_wfsConfObj,obj);
          reapplyWFSConfObject(resultObj);
      }
  });
  req.send();
}

function addButtonWfs(id, isOn, src, title, x, y) {
	buttonWfs_id.push(id);
	buttonWfs_on.push(isOn);
	buttonWfs_src.push(src);
	buttonWfs_title_off.push(title);
	buttonWfs_title_on.push(title);
	buttonWfs_x.push(x);
	buttonWfs_y.push(y);
}
// end of button management spatialRequest ///////////

var mapbenderInit = function () {
	try {
		parent.Mapbender.modules[frameName].receiveFeatureCollection = new parent.Mapbender.Event();
		parent.Mapbender.modules[frameName].events = {
			gazetteerReady: new parent.Mapbender.Event()
		};
		var ev = parent.Mapbender.modules[frameName].events;
		ev.receiveFeatureCollection = parent.Mapbender.modules[frameName].receiveFeatureCollection;

		/**
		 * Property: events.onWfsConfSelect
		 *
		 * Description:
		 * event is fired whenever a new WFS is selected
		 */
		ev.onWfsConfSelect = new parent.Mapbender.Event();

		/**
		 * Property: events.onFormReset
		 *
		 * Description:
		 * event is fired whenever the form is resetted
		 */
		ev.onFormReset = new parent.Mapbender.Event();


		// creates a Highlight object for the request geometry
		var styleProperties = {"position":"absolute", "top":"0px", "left":"0px", "z-index":70};
		requestGeometryHighlight = new parent.Highlight(targetArray, "requestGeometryHighlight", styleProperties, 2);
		parent.mb_registerSubFunctions("window.frames['" + frameName +"'].requestGeometryHighlight.paint()");


		initModWfsGazetteer();
		init_wfsSpatialRequest();
	}
	catch (exc) {
		alert(exc);
	}

	try {
		parent.Mapbender.modules.savewmc.setExtensionData({
			WFSCONFIDSTRING: wfsConfIdString
		});
	}
	catch (exc) {

	}

	try {
		parent.Mapbender.modules.loadwmc.events.loaded.register(function (obj) {
			if (obj.extensionData && obj.extensionData.WFSCONFIDSTRING) {
				wfsConfIdString = "";
				appendWfsConf(obj.extensionData.WFSCONFIDSTRING);
			}
		});
	}
	catch (exc) {
	}

	try {
		parent.$('#body').bind('addFeaturetypeConfs', function(event, obj) {
			if (obj.featuretypeConfObj) {
				var featuretypeConfIds = [];
				for (var i = 0 ; i < obj.featuretypeConfObj.length ; i ++) {
					featuretypeConfIds.push(obj.featuretypeConfObj[i].id);
				}
				appendWfsConf(featuretypeConfIds.join(","));
			}
		});
	}
	catch (exc) {
	}
};

if (parent.Mapbender.events.init.done === true) {
	mapbenderInit();
}
else {
	parent.Mapbender.events.init.register(mapbenderInit);
}

parent.Mapbender.events.localize.register(function() {
    localizeGazetteer();
});

// FIXME: need this in newStyle
//parent.eventLocalize.register(localizeGazetteer);

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
	var functionCall = "parent.mb_regButton_frame('initWfsButton', '"+frameName+"', "+j+")";
	var x = new Function ("", functionCall);
	x();
}

function initWfsButton(ind, pos) {
	parent.mb_button[ind] = document.getElementById(buttonWfs_id[pos]);
	parent.mb_button[ind].img_over = buttonWfs_imgdir + buttonWfs_src[pos].replace(/_off/,"_over");
	parent.mb_button[ind].img_on = buttonWfs_imgdir + buttonWfs_src[pos].replace(/_off/,"_on");
	parent.mb_button[ind].img_off = buttonWfs_imgdir + buttonWfs_src[pos];
	parent.mb_button[ind].img_out = buttonWfs_imgdir + buttonWfs_src[pos];
	parent.mb_button[ind].status = 0;
	parent.mb_button[ind].elName = buttonWfs_id[pos];
	parent.mb_button[ind].frameName = frameName;
	parent.mb_button[ind].go = new Function ("requestGeometryHighlight.clean(); wfsEnable(parent.mb_button["+ind+"], " + pos + ")");
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
			currentImg.style.margin = "5px";
			currentImg.onmouseover = new Function("wfsInitFunction("+i+")");
			var b = document.getElementById("displaySpatialButtons");
			if (b !== null) {
				b.appendChild(currentImg);
			}
		}
	}
}

function disableButtons() {
	var b = document.getElementById("displaySpatialButtons");
	if (b !== null) {
		removeChildNodes(b);
	}
}

function wfsEnable(obj) {
	var ind = parent.getMapObjIndexByName("mapframe1");
	var el = parent.mb_mapObj[ind].getDomElement();
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
	callRequestGeometryConstructor(obj.id,"mapframe1");
}

function callRequestGeometryConstructor(selectedType,target){
		if(document.getElementById("res")){
			document.getElementById("res").innerHTML ="";
			spatialRequestGeom = null;
		}
		if(document.getElementById("spatialResHint")){
			document.getElementById("spatialResHint").innerHTML = "";
		}
		spatialRequestType = selectedType;
		var geometryConstructor = new parent.RequestGeometryConstructor(target);
		geometryConstructor.getGeometry(selectedType,function(target,queryGeom){
			if(queryGeom !=''){
				var spatialRes = document.createElement("span");
				spatialRes.id = "spatialResHint";
				spatialRes.name = "spatialResHint";
				spatialRes.className = "spatialResHint";
				var b = document.getElementById("displaySpatialButtons");
				if (b !== null) {
					b.appendChild(spatialRes);
				}
				document.getElementById("spatialResHint").innerHTML = "<br><img src='"+spatialRequestIsSetImg+"'></img>"+spatialRequestIsSetMessage;
				spatialRequestGeom = queryGeom;
			}
			parent.mb_disableThisButton(selectedType);

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

		});
}

function wfsDisable(obj) {
	var ind = parent.getMapObjIndexByName("mapframe1");
	var el = parent.mb_mapObj[ind].getDomElement();
	el.onmousedown = null;
	el.ondblclick = null;
	el.onmousemove = null;
	parent.writeTag("","measure_display","");
	parent.writeTag("","measure_sub","");
	activeButton = null;
}

function openwindow(url) {
	window1 = window.open(url, "Information", "width=500,height=500,left=100,top=100,scrollbars=yes,resizable=no");
	window1.focus();
}
//----------------------------------------------------------------------------------

function appendWfsConf(newWfsConfIdString) {
	// merge with existing wfs conf ids
	if (wfsConfIdString !== "") {
		if (newWfsConfIdString !== "") {
			wfsConfIdString += "," + newWfsConfIdString;

			// rebuild form
			initModWfsGazetteer();
		}
	}
	else {
		wfsConfIdString = newWfsConfIdString;

		// rebuild form
		initModWfsGazetteer();
	}

}

function removeChildNodes(node) {
	if (node === null) {
		return;
	}
	try {
		while (node.childNodes.length > 0) {
		  var childNode = node.firstChild;
			node.removeChild(childNode);
		}
	}
	catch (exc) {

	}
}

/**
 * removes whitespaces and endlines before and after a string
 *
 */
function trimString (str) {
	return str.replace(/^\s+|\s+|\n+$/g, '');
}

function appendStyles() {
	//first of all remove all style tags from former wfs confs
	var styleNodes = document.getElementsByTagName("style");
	for (var i=0; i < styleNodes.length; i++) {
		var parentNodeOfStyle = styleNodes[i].parentNode;
		parentNodeOfStyle.removeChild(styleNodes[i]);
	}
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

//----------------------------------------------------------------------------------

function initModWfsGazetteer() {
    // empty nodes
	var nodesToEmpty = ["selectWfsConfForm", "wfsForm", "res", "wfsIcons"];
	while (nodesToEmpty.length > 0) {
		var currentId = nodesToEmpty.pop();
		var currentNode = document.getElementById(currentId);
		removeChildNodes(currentNode);
	}

	geomArray = new parent.GeometryArray();

    // Would be much nicer to have
    // initWFSConf() and reloadWFSConf
     parent.mb_ajax_json("../php/mod_wfs_gazetteer_server.php", {command:"getWfsConf",wfsConfIdString:wfsConfIdString}, function(json,status) {
        reapplyWFSConfObject(json, status);
        localizeGazetteer();
    });
}

function clearWFSGazetteer()
{
	// empty nodes
	var nodesToEmpty = ["selectWfsConfForm", "wfsForm", "res", "wfsIcons"];
	while (nodesToEmpty.length > 0) {
		var currentId = nodesToEmpty.pop();
		var currentNode = document.getElementById(currentId);
		removeChildNodes(currentNode);
	}
}

function reapplyWFSConfObject(json,status) {
	clearWFSGazetteer();
	global_wfsConfObj = json;
	var wfsCount = 0;
	for (var wfsConfId in global_wfsConfObj) {
		global_selectedWfsConfId = wfsConfId;
		if (typeof(global_wfsConfObj[wfsConfId] != 'function')) {
			wfsCount++;
		}
	}
	if (wfsCount === 0) {
		var e = new parent.Mb_exception("no wfs conf id available.");
	}
	else if (wfsCount === 1) {
		appendStyles();
		appendWfsForm();
	}
	else {
		appendWfsConfSelectBox();

	}
}

function setWfsInfo() {

	var bodyNode = document.getElementById("wfsIcons");
	removeChildNodes(bodyNode);
	var bulbNode = document.createElement("a");
	bulbNode.name = "wfsInfo";
	bulbNode.id = "wfsInfo";
	bodyNode.appendChild(bulbNode);

	// append bulb image
	removeChildNodes(bulbNode);
	var imgNode = document.createElement("img");
	imgNode.id = "wfsInfoImg";
	//imgNode.src = "../img/button_digitize/geomInfo.png";
	imgNode.src = "../img/tree_new/info.png";
	imgNode.border = 0;
	imgNode.title = '<?php echo _mb("show metadata");?>';
	bulbNode.appendChild(imgNode);
	bulbNode.href = "javascript:openwindow('../php/mod_featuretypeMetadata.php?wfs_conf_id=" + global_selectedWfsConfId.toString() + "');";
	bulbNode.style.visibility = "visible";

	// set image: remove this WFS
	var wfsRemoveNode = document.createElement("img");
	wfsRemoveNode.name = "wfsRemove";
	wfsRemoveNode.id = "wfsRemove";
	wfsRemoveNode.title ='<?php echo _mb("remove WFS Conf");?>';
	bodyNode.appendChild(wfsRemoveNode);
	//wfsRemoveNode.src = "../img/button_digitize/geomRemove.png";
	wfsRemoveNode.src = "../img/tree_new/delete_wms.png";
	wfsRemoveNode.style.visibility = 'visible';
	// Internet explorer
	if (parent.ie) {
		wfsRemoveNode.onclick = function() {
			var x = new Function ("", "delete global_wfsConfObj[global_selectedWfsConfId];setWfsConfIdString();initModWfsGazetteer();parent.mb_setWmcExtensionData({'WFSCONFIDSTRING':wfsConfIdString});");
			x();
		};
	}
	// Firefox
	else {
		wfsRemoveNode.onclick = function () {
			delete global_wfsConfObj[global_selectedWfsConfId];
			setWfsConfIdString();
			initModWfsGazetteer();
			parent.mb_setWmcExtensionData({"WFSCONFIDSTRING":wfsConfIdString});
		}
	}
	// set wfsGeomType image
	var wfsGeomTypeNode = document.createElement("img");
	wfsGeomTypeNode.name = "wfsGeomType";
	wfsGeomTypeNode.id = "wfsGeomType";
	bodyNode.appendChild(wfsGeomTypeNode);
	var wfsGeomType = "";
	for (var i=0; i < global_wfsConfObj[global_selectedWfsConfId].element.length; i++) {
		if (parseInt(global_wfsConfObj[global_selectedWfsConfId].element[i].f_geom)) {
			wfsGeomType = global_wfsConfObj[global_selectedWfsConfId].element[i].element_type;
		}
	}
	if (wfsGeomType.match(/Point/)) {
		wfsGeomTypeNode.src = "../img/button_digitize/point.png";
		wfsGeomTypeNode.style.visibility = 'visible';
		wfsGeomTypeNode.title = '<?php echo _mb("Point");?>';
	}
	else if (wfsGeomType.match(/Line/)) {
		wfsGeomTypeNode.src = "../img/button_digitize/line.png";
		wfsGeomTypeNode.title = '<?php echo _mb("Line");?>';
	}
	else if (wfsGeomType.match(/Polygon/)) {
		wfsGeomTypeNode.src = "../img/button_digitize/polygon.png";
		wfsGeomTypeNode.title = '<?php echo _mb("Polygon");?>';
	}
	else {
		wfsGeomTypeNode.style.display = "none";
		var e = new parent.Mb_exception("WFS gazetteer: geometry type unknown.");
	}
}

function setWfsConfIdString() {
	var str = [];
	for (var wfsConfId in global_wfsConfObj) {
		global_selectedWfsConfId = wfsConfId;
		if (typeof(global_wfsConfObj[wfsConfId] != 'function')) {
			str.push(wfsConfId);
		}
	}
	wfsConfIdString = str.join(",");
}

function appendWfsConfSelectBox() {
	var selectNode = document.createElement("select");
	selectNode.name = "wfs_conf_sel";
	var wfsFormNode = document.getElementById("selectWfsConfForm");
	if (parent.ie) {
		selectNode.onchange = function() {
			global_selectedWfsConfId = this.value;
			initializeOnLoad = 0;

			//close old dialogs
			parent.$('.resultList').dialog('close');
			parent.$('.infoPopup').dialog('close');

	    	appendStyles();
			appendWfsForm();
			parent.Mapbender.modules[frameName].events.onWfsConfSelect.trigger({
				wfsConfId: global_selectedWfsConfId
			});
		};
	}
	else{
	   selectNode.setAttribute("onchange", "parent.$('.resultList').dialog('close');parent.$('.infoPopup').dialog('close');global_selectedWfsConfId = this.value;initializeOnLoad=0;appendStyles();appendWfsForm();parent.Mapbender.modules[frameName].events.onWfsConfSelect.trigger({wfsConfId: global_selectedWfsConfId});");
	}
	var isSelected = false;
	for (var wfsConfId in global_wfsConfObj) {
		var optionNode = document.createElement("option");

		optionNode.value = wfsConfId;
		optionNode.innerHTML = global_wfsConfObj[wfsConfId].wfs_conf_abstract;

		if (!isSelected) {
			optionNode.selected = true;
			isSelected = true;
			global_selectedWfsConfId = wfsConfId;
		}
		selectNode.appendChild(optionNode);
	}

	var form = document.getElementById('selectWfsConfForm');
	form.appendChild(selectNode);

	appendStyles();
	appendWfsForm();
}

function appendWfsForm() {
	if(showWfsIcons) {
		setWfsInfo();
	}
	var form = document.getElementById("wfsForm");
	removeChildNodes(form);
	var resultDiv = document.getElementById("res");
	removeChildNodes(resultDiv);

	var divContainer = document.createElement("div");
	divContainer.className = global_wfsConfObj[global_selectedWfsConfId].g_label_id;

	divContainer.innerHTML = global_wfsConfObj[global_selectedWfsConfId].g_label;

	form.appendChild(divContainer);

	var wfsConfElementArray = global_wfsConfObj[global_selectedWfsConfId].element;

	for (var i = 0; i < wfsConfElementArray.length; i++){
		if (parseInt(wfsConfElementArray[i].f_search)) {
			var spanNode = document.createElement("span");
			spanNode.setAttribute("id", wfsConfElementArray[i].element_name+"Span");
			spanNode.className = wfsConfElementArray[i].f_label_id;
			spanNode.innerHTML = wfsConfElementArray[i].f_label;
			if(wfsConfElementArray[i].f_form_element_html.match(/\<select/)){
				var inputNode = document.createElement("span");
				inputNode.id = wfsConfElementArray[i].element_name+"Select";
				inputNode.innerHTML = wfsConfElementArray[i].f_form_element_html;
			}
			else if(wfsConfElementArray[i].f_form_element_html.match(/checkbox/)){
				var inputNode = document.createElement("span");
				inputNode.id = wfsConfElementArray[i].element_name+"Checkbox";
				inputNode.innerHTML = wfsConfElementArray[i].f_form_element_html;
			}
			else{
				var inputNode = document.createElement("input");
				inputNode.type = "text";
				inputNode.className = wfsConfElementArray[i].f_style_id;
				inputNode.id = wfsConfElementArray[i].element_name;
				if(wfsConfElementArray[i].f_form_element_html.match(/datepicker/)){
					inputNode.readOnly=true;
					inputNode.style.backgroundColor = "#D3D3D3";
					inputNode.title = "Use datepicker for selection of date";
				}
			}
			form.appendChild(spanNode);
			form.appendChild(inputNode);

			//build imgNode for datepicker image
			if(wfsConfElementArray[i].f_form_element_html.match(/datepicker/)){
				var imgNode = document.createElement("span");
				imgNode.id = wfsConfElementArray[i].element_name+"Img";
				imgNode.title = "Click here to open datepicker";
				imgNode.innerHTML = wfsConfElementArray[i].f_form_element_html;
				form.appendChild(imgNode);
			}
			form.appendChild(document.createElement("br"));
		}
	}
	var submitButton = document.createElement("input");
	submitButton.type = "submit";
	submitButton.id = "submitButton";
	submitButton.className = global_wfsConfObj[global_selectedWfsConfId].g_button_id;
	submitButton.value = global_wfsConfObj[global_selectedWfsConfId].g_button;

	form.appendChild(submitButton);

	var delFilterButton = document.createElement("input");
	delFilterButton.type = "button";
	delFilterButton.style.marginLeft = "5px";
	delFilterButton.className = "buttonDelFilter";
	delFilterButton.value = clearFilterButtonLabel;
	// Internet explorer
	if (parent.ie) {
		delFilterButton.onclick = function() {
			var x = new Function ("", "clearFilter();");
			x();
		};
	}
	// Firefox
	else {
		delFilterButton.onclick = function () {
			clearFilter();
		}
	}
	form.appendChild(delFilterButton);

	checkSrs();

	if(initializeOnLoad == 1) {
		return validate();
	}

	var ready = parent.Mapbender.modules[frameName].events.gazetteerReady; 
	if(ready.done !== true) {
		ready.done = true;
		ready.trigger();
	}
}

function checkSrs(){
	//check SRS
	var ind = parent.getMapObjIndexByName("mapframe1");
	var submit = document.getElementById("submitButton");
	var patternString = parent.mb_mapObj[ind].getSRS().toUpperCase();
	var pattern = new RegExp(patternString);

	if(global_wfsConfObj[global_selectedWfsConfId].featuretype_srs.match(pattern) == -1){
		var msg = '<?php echo _mb("Different EPSG of map and wfs featuretype, no spatial request possible!");?>';
		msg += parent.mb_mapObj[ind].getSRS()+"  -  "+global_wfsConfObj[global_selectedWfsConfId].featuretype_srs;
		alert(msg);

		//disable Submit Button
		if(submit)submit.disabled = true;
	}
	else{
		if(submit)submit.disabled = false;
	}
}

function clearFilter(){
	parent.Mapbender.modules[frameName].events.onFormReset.trigger({
		wfsConfId: global_selectedWfsConfId
	});
	var wfsConfElementArray = global_wfsConfObj[global_selectedWfsConfId].element;
	for (var i = 0; i < wfsConfElementArray.length; i++){
		if (parseInt(wfsConfElementArray[i].f_search)) {
			if(wfsConfElementArray[i].f_form_element_html.match(/checkbox/)){
				var elementArray = document.getElementsByName(wfsConfElementArray[i].element_name);
				for (var j = 0; j < elementArray.length; j++){
					elementArray[j].checked = "";
				}
				document.getElementById('checkAll').checked = "";
			}
			else{
				document.getElementById(wfsConfElementArray[i].element_name).value = "";
			}
		}
	}

	//remove geometry from spatialrequest, remove drawn rectangle or polygon and hint
	spatialRequestGeom = null;
	requestGeometryHighlight.clean();
	requestGeometryHighlight.paint();
	if(document.getElementById('spatialResHint')){
 		document.getElementById("spatialResHint").innerHTML = "";
 	}

	//close old dialogs
	parent.$('.resultList').dialog('close');
	parent.$('.infoPopup').dialog('close');

 	if(document.getElementById('spatialResHint')){
 		document.getElementById("spatialResHint").innerHTML = "";
 	}
 	document.getElementById("res").innerHTML = "";
}

function getNumberOfFilterParameters(){
	var cnt = 0;
	var el = global_wfsConfObj[global_selectedWfsConfId].element;
	inputNotEnough = [];
	for (var i = 0; i < el.length; i++){

		if( el[i]['f_search'] == 1){
			if(el[i]['f_form_element_html'].match(/\<select/)){
				var elementValue = document.getElementById(el[i]['element_name']).options[document.getElementById(el[i]['element_name']).selectedIndex].value;
    		}
    		else if(el[i]['f_form_element_html'].match(/checkbox/)){
				var elementArray = document.getElementsByName(el[i]['element_name']);
				var selectedVal = [];
				for (var j = 0; j < elementArray.length; j++){
					if (elementArray[j].checked == true){
						selectedVal.push(elementArray[j].value);
					}
				}
				var elementValue = selectedVal.join(",");
			}
			else{
				var elementValue = document.getElementById(el[i]['element_name']).value;
			}

			if (elementValue != '') {
				cnt++;
			}
			if(elementValue.length < el[i]['f_min_input']){
				inputNotEnough.push(el[i]['element_name']+"("+el[i]['f_min_input']+")");
			}
		}
	}

	if(inputNotEnough.length>0){
		alert("Mandatory fields: "+inputNotEnough.join(', '));
		return false;
	}

	if(enableSearchWithoutParams == 1) {
    	cnt = 2;
	}

//	if(spatialRequestGeom == null){
//		alert("Bitte räumliche Eingrenzung vornehmen.");
//		return false;
//	}

	return cnt;
}
function validate(){

	parent.Mapbender.modules[frameName].events.onWfsConfSelect.trigger({
		wfsConfId: global_selectedWfsConfId
	});
	if(geomArray.count()>0){
 		geomArray.empty();
 	}
	//close old dialogs
	parent.$('.resultList').dialog('close');
	parent.$('.infoPopup').dialog('close');

	if(removeSpatialRequestHighlight == 1) {
		requestGeometryHighlight.clean();
		requestGeometryHighlight.paint();
	}

	var filterParameterCount = getNumberOfFilterParameters();

	if(filterParameterCount == 0 && spatialRequestGeom == null && initializeOnLoad != 1){
	//if(filterParameterCount == 0 && spatialRequestGeom == null){
	//if(filterParameterCount == 0){
		//alert("Please specify at least one filter attribute.");
		return false;
	}
	else{
		if(inputNotEnough.length==0){
			var andConditions = [];

			var el = global_wfsConfObj[global_selectedWfsConfId].element;
			//var srs = global_wfsConfObj[global_selectedWfsConfId].featuretype_srs;
			var ind = parent.getMapObjIndexByName("mapframe1");
			var srs = parent.mb_mapObj[ind].getSRS();
			var ftName = global_wfsConfObj[global_selectedWfsConfId].featuretype_name;

			var pattern = /:[a-zA-Z0-9_]+/;
			var ftns = "";
			if (ftName.match(pattern)) {
				ftns = ftName.replace(pattern, ":");
			}

			for (var i = 0; i < el.length; i++) {
				if (el[i]['f_search'] == 1){
					var a = new Array();
					if(el[i]['f_form_element_html'].match(/\<select/)){
						var elementValue = document.getElementById(el[i]['element_name']).options[document.getElementById(el[i]['element_name']).selectedIndex].value;
						a.push(elementValue);
					}
	    			else if(el[i]['f_form_element_html'].match(/checkbox/)){
						var elementArray = document.getElementsByName(el[i]['element_name']);
						var selectedVal = [];
						for (var j = 0; j < elementArray.length; j++){
							if (elementArray[j].checked == true){
								selectedVal.push(elementArray[j].value);
							}
						}
						var elementValue = selectedVal;
						a = elementValue;
					}
					else{
						var elementValue = document.getElementById(el[i]['element_name']).value;
						a = elementValue.split(",");
					}
				}

				if (el[i]['f_search'] == 1 && elementValue != '') {
					var orConditions = "";
					for (var j=0; j < a.length; j++) {
						if(el[i]['f_operator']=='bothside'){
							orConditions += "<ogc:PropertyIsLike wildCard='*' singleChar='.' escape='!'>";
							orConditions += "<ogc:PropertyName>" + ftns + el[i]['element_name'] + "</ogc:PropertyName>";
							orConditions += "<ogc:Literal>*";
							if(el[i]['f_toupper'] == 1){
								orConditions += a[j].toUpperCase();
							}
							else{
								orConditions += a[j];
							}
							orConditions += "*</ogc:Literal>";
							orConditions += "</ogc:PropertyIsLike>";
					}
						else if(el[i]['f_operator']=='rightside'){
							orConditions += "<ogc:PropertyIsLike wildCard='*' singleChar='.' escape='!'>";
							orConditions += "<ogc:PropertyName>" + ftns + el[i]['element_name'] + "</ogc:PropertyName>";
							orConditions += "<ogc:Literal>";
							if(el[i]['f_toupper'] == 1){
								orConditions += a[j].toUpperCase();
							}
							else{
								orConditions += a[j];
							}
							orConditions += "*</ogc:Literal>";
							orConditions += "</ogc:PropertyIsLike>";
						}
						else if(el[i]['f_operator']=='greater_than'){
							orConditions += "<ogc:PropertyIsGreaterThan>";
							orConditions += "<ogc:PropertyName>" + ftns + el[i]['element_name'] + "</ogc:PropertyName>";
							orConditions += "<ogc:Literal>";
							if(el[i]['f_toupper'] == 1){
								orConditions += a[j].toUpperCase();
							}
							else{
								orConditions += a[j];
							}
							orConditions += "</ogc:Literal>";
							orConditions += "</ogc:PropertyIsGreaterThan>";
						}
						else if(el[i]['f_operator']=='less_than'){
							orConditions += "<ogc:PropertyIsLessThan>";
							orConditions += "<ogc:PropertyName>" + ftns + el[i]['element_name'] + "</ogc:PropertyName>";
							orConditions += "<ogc:Literal>";
							if(el[i]['f_toupper'] == 1){
								orConditions += a[j].toUpperCase();
							}
							else{
								orConditions += a[j];
							}
							orConditions += "</ogc:Literal>";
							orConditions += "</ogc:PropertyIsLessThan>";
						}
						else if(el[i]['f_operator']=='less_equal_than'){
							orConditions += "<ogc:PropertyIsLessThanOrEqualTo>";
							orConditions += "<ogc:PropertyName>" + ftns + el[i]['element_name'] + "</ogc:PropertyName>";
							orConditions += "<ogc:Literal>";
							if(el[i]['f_toupper'] == 1){
								orConditions += a[j].toUpperCase();
							}
							else{
								orConditions += a[j];
							}
							orConditions += "</ogc:Literal>";
							orConditions += "</ogc:PropertyIsLessThanOrEqualTo>";
						}
						else if(el[i]['f_operator']=='greater_equal_than'){
							orConditions += "<ogc:PropertyIsGreaterThanOrEqualTo>";
							orConditions += "<ogc:PropertyName>" + ftns + el[i]['element_name'] + "</ogc:PropertyName>";
							orConditions += "<ogc:Literal>";
							if(el[i]['f_toupper'] == 1){
								orConditions += a[j].toUpperCase();
							}
							else{
								orConditions += a[j];
							}
							orConditions += "</ogc:Literal>";
							orConditions += "</ogc:PropertyIsGreaterThanOrEqualTo>";
						}
						else if(el[i]['f_operator']=='equal'){
							orConditions += "<ogc:PropertyIsEqualTo>";
							orConditions += "<ogc:PropertyName>" + ftns + el[i]['element_name'] + "</ogc:PropertyName>";
							orConditions += "<ogc:Literal>";
							if(el[i]['f_toupper'] == 1){
								orConditions += a[j].toUpperCase();
							}
							else{
								orConditions += a[j];
							}
							orConditions += "</ogc:Literal>";
							orConditions += "</ogc:PropertyIsEqualTo>";
						}
						else{
							orConditions += "<ogc:PropertyIsLike wildCard='*' singleChar='.' escape='!'>";
							orConditions += "<ogc:PropertyName>" + ftns + el[i]['element_name'] + "</ogc:PropertyName>";
							orConditions += "<ogc:Literal>*";
							if(el[i]['f_toupper'] == 1){
								orConditions += a[j].toUpperCase();
							}
							else{
								orConditions += a[j];
							}
							orConditions += "*</ogc:Literal>";
							orConditions += "</ogc:PropertyIsLike>";
						}
					}
					if(a.length > 1){
						andConditions.push("<Or>" + orConditions + "</Or>");
					}
					else {
						andConditions.push(orConditions);
					}
				}
			}

			if(spatialRequestGeom!=null){
				var currentAndCondition = "";
				if(spatialRequestGeom.geomType == "polygon"){
					if(buttonPolygon.filteroption=='within'){
						currentAndCondition = "<Within><ogc:PropertyName>";
						for (var j=0; j < el.length; j++) {
							if(el[j]['f_geom']==1){
								currentAndCondition += ftns + el[j]['element_name'];								var elementName = el[j]['element_name'];
							}
						}
						currentAndCondition += "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\">";
						currentAndCondition += "<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
						for(var k=0; k<spatialRequestGeom.count(); k++){
							if(k>0)	currentAndCondition += " ";
							currentAndCondition += spatialRequestGeom.get(k).x+","+spatialRequestGeom.get(k).y;
						}
						currentAndCondition += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
						currentAndCondition += "</gml:Polygon></Within>";
					}
					else if(buttonPolygon.filteroption=='intersects'){
						currentAndCondition = "<Intersects><ogc:PropertyName>";
						for (var j=0; j < el.length; j++) {
							if(el[j]['f_geom']==1){
								currentAndCondition += ftns + el[j]['element_name'];								var elementName = el[j]['element_name'];
							}
						}
						currentAndCondition += "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\">";
						currentAndCondition += "<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
						for(var k=0; k<spatialRequestGeom.count(); k++){
							if(k>0)	currentAndCondition += " ";
							currentAndCondition += spatialRequestGeom.get(k).x+","+spatialRequestGeom.get(k).y;
						}
						currentAndCondition += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
						currentAndCondition += "</gml:Polygon></Intersects>";
					}
				}
				else if(spatialRequestGeom.geomType == "line"){
					var rectangle = [];
					rectangle = spatialRequestGeom.getBBox();

					if(buttonRectangle.filteroption=='within'){
						currentAndCondition = "<Within><ogc:PropertyName>";
						for (var j=0; j < el.length; j++) {
							if(el[j]['f_geom']==1){
								currentAndCondition += ftns + el[j]['element_name'];								var elementName = el[j]['element_name'];
							}
						}
						currentAndCondition += "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\">";
						currentAndCondition += "<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
						currentAndCondition += rectangle[0].x+","+rectangle[0].y;
						currentAndCondition += " ";
						currentAndCondition += rectangle[0].x+","+rectangle[1].y;
						currentAndCondition += " ";
						currentAndCondition += rectangle[1].x+","+rectangle[1].y;
						currentAndCondition += " ";
						currentAndCondition += rectangle[1].x+","+rectangle[0].y;
						currentAndCondition += " ";
						currentAndCondition += rectangle[0].x+","+rectangle[0].y;
						currentAndCondition += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
						currentAndCondition += "</gml:Polygon></Within>";
					}
					else if(buttonRectangle.filteroption=='intersects'){
						currentAndCondition = "<Intersects><ogc:PropertyName>";
						for (var j=0; j < el.length; j++) {
							if(el[j]['f_geom']==1){
								currentAndCondition += ftns + el[j]['element_name'];								var elementName = el[j]['element_name'];
							}
						}
						currentAndCondition += "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\">";
						currentAndCondition += "<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
						currentAndCondition += rectangle[0].x+","+rectangle[0].y;
						currentAndCondition += " ";
						currentAndCondition += rectangle[0].x+","+rectangle[1].y;
						currentAndCondition += " ";
						currentAndCondition += rectangle[1].x+","+rectangle[1].y;
						currentAndCondition += " ";
						currentAndCondition += rectangle[1].x+","+rectangle[0].y;
						currentAndCondition += " ";
						currentAndCondition += rectangle[0].x+","+rectangle[0].y;
						currentAndCondition += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
						currentAndCondition += "</gml:Polygon></Intersects>";
					}
				}
				else if(spatialRequestGeom.geomType == "point"){
					var tmp = spatialRequestGeom.get(0);
					var mapPos = parent.makeRealWorld2mapPos("mapframe1",tmp.x, tmp.y);
					var buffer = mb_wfs_tolerance/2;
					var mapPosXAddPix = mapPos[0] + buffer;
					var mapPosYAddPix = mapPos[1] +buffer;
					var mapPosXRemovePix = mapPos[0] - buffer;
					var mapPosYRemovePix = mapPos[1] - buffer;
					var realWorld1 = parent.makeClickPos2RealWorldPos("mapframe1",mapPosXRemovePix,mapPosYRemovePix);
					var realWorld2 = parent.makeClickPos2RealWorldPos("mapframe1",mapPosXAddPix,mapPosYRemovePix);
					var realWorld3 = parent.makeClickPos2RealWorldPos("mapframe1",mapPosXAddPix,mapPosYAddPix);
					var realWorld4 = parent.makeClickPos2RealWorldPos("mapframe1",mapPosXRemovePix,mapPosYAddPix);
					currentAndCondition = "<Intersects><ogc:PropertyName>";
					for (var j=0; j < el.length; j++) {
						if(el[j]['f_geom']==1){
							currentAndCondition += ftns + el[j]['element_name'];
						}
					}
					currentAndCondition += "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\"><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
					currentAndCondition += realWorld1[0] + "," + realWorld1[1] + " " + realWorld2[0] + "," + realWorld2[1] +  " ";
					currentAndCondition += realWorld3[0] + "," + realWorld3[1] + " " + realWorld4[0] + "," + realWorld4[1] + " " + realWorld1[0] + "," + realWorld1[1];
					currentAndCondition += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon></Intersects>";
				}
				if (currentAndCondition !== "") {
					andConditions.push(currentAndCondition);
				}

			}

			var andConditionString = andConditions.join("");
			if (andConditions.length > 1) {
				andConditionString = "<And>" + andConditionString + "</And>";
			}

			var filter = "<ogc:Filter>"+andConditionString+"</ogc:Filter>";

			document.getElementById("res").innerHTML = "<table><tr><td><img src='"+progressIndicatorImg+"'></td><td>"+progressIndicatorText+"</td></tr></table>";
			var parameters = {
				"command" : "getSearchResults",
				"wfs_conf_id" : global_selectedWfsConfId,
				"typename" : global_wfsConfObj[global_selectedWfsConfId].featuretype_name,
				"frame" : this.name,
				"filter" : filter,
				"backlink" : "",
				"destSrs" : srs
			};

			parent.mb_ajax_get("../php/mod_wfs_gazetteer_server.php", parameters, function (jsCode, status) {
				document.getElementById("res").innerHTML = "<table><tr><td>"+arrangeResultsText+"</td></tr></table>";
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
					try {
						if(typeof(jsCode) == 'string'){
							alert(jsCode);
							return false;
						}
						else if(typeof(jsCode) == 'object'){
							var geoObj = jsCode;
						}
						else{
							var geoObj = parent.$.parseJSON(jsCode);
						}
					}
					catch (exc) {
						document.getElementById("res").innerHTML = '';
						alert("Invalid data returned from service.");
						return false;
					}
		       		if (typeof geoObj === "undefined") {
						document.getElementById("res").innerHTML = '';
						alert("Invalid data returned from service.");
					}
					else if (jsCode) {
						if (typeof(geoObj) == 'object') {
			        		geomArray.importGeoJSON(geoObj);
			        		document.getElementById("res").innerHTML = '';

							parent.Mapbender.modules[frameName].receiveFeatureCollection.trigger({
								geoObj : geoObj, 
								ogcFilter : filter
							});

							var resultList = parent.Mapbender.modules.resultList;
							resultList.clear();
							resultList.setTitle(global_wfsConfObj[global_selectedWfsConfId].wfs_conf_abstract);
							resultList.setWFSconf(global_wfsConfObj[global_selectedWfsConfId]);
							resultList.addFeatureCollection(geoObj);
							resultList.show();
						}
						else {
							document.getElementById("res").innerHTML = '';
							parent.Mapbender.modules[frameName].receiveFeatureCollection.trigger(null);
						}
					}
		       		else {
						document.getElementById("res").innerHTML = '';
						alert("No results.");
					}
					initializeOnLoad = 0;
		       	}
			});
		}
		else{
			return false;
		}
	}
	//spatialRequestGeom = null;
	return false;
}

function callPick(obj){
	dTarget = obj;
	var dp = window.open('../tools/datepicker/datepicker.php?m=Jan_Feb_Mar_Apr_May_June_July_Aug_Sept_Oct_Nov_Dec&d=Mon_Tue_Wed_Thu_Fri_Sat_Sun&t=today','dp','left=200,top=200,width=230,height=210,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0');
	dp.focus();
	return false;
}

</script>
</head>
<body>
<form name='selectWfsConfForm' id='selectWfsConfForm'></form>
<div class='wfsIcons' name='wfsIcons' id='wfsIcons'></div>
<div class='spatialButtons' name='displaySpatialButtons' id='displaySpatialButtons'></div>
<form name='wfsForm' id='wfsForm' onsubmit='return validate()'>
</form>
<div id="uploader"></div>
<div class='resultDiv' name='res' id='res'></div>
</body>
</html>
