<?php
#$Id: mod_wfs_SpatialRequest.php 7768 2011-04-13 10:23:16Z verenadiewald $
#$Header: /cvsroot/mapbender/mapbender/http/javascripts/mod_wfs_spatialRequest.php,v 1.4 2006/03/08 15:26:26 c_baudson Exp $
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
$wfs_conf_filename = "wfs_default.conf";
include '../include/dyn_php.php';
$fname = dirname(__FILE__) . "/../../conf/" . $wfs_conf_filename;
if (file_exists($fname)) {
	/*
	 * @security_patch finc done
	 */
	include(secure($fname));
}
else {
	$e = new mb_exception("mod_wfs_SpatialRequest.php: Configuration file " . $wfs_conf_filename . " not found.");
}

include '../include/dyn_js.php';
echo "var mod_wfs_spatialRequest_target = '".$e_target[0]."';\n";
?>
//element var openLinkFromSearch for opening attribute link directly onclick of searchResult entry
var openLinkFromSearch = typeof openLinkFromSearch === "undefined" ? 0 : openLinkFromSearch;

var wfsAreaType_point = "point";
var wfsAreaType_polygon = "polygon";
var wfsAreaType_rectangle = "rectangle";
var wfsAreaType_extent = "extent";
var wfsAreaType_current = "";

var mod_wfs_spatialRequest_frameName = "";
var mod_wfs_spatialRequest_epsg;
var mod_wfs_spatialRequest_width;
var mod_wfs_spatialRequest_height;

var mod_wfs_spatialRequest_bg = "";
var mod_wfs_spatialRequest_pgsql = true;
var mod_wfs_spatialRequest_win = null;
var mod_wfs_spatialRequest_thema = false;

var button_point = "wfs_point";
var button_polygon = "wfs_polygon";
var button_rectangle = "wfs_rectangle";
var button_extent = "wfs_extent";
var button_dialogue = "wfs_dialogue";

var activeButton = null;
var mod_wfs_spatialRequestSubFunctions = [];

var buttonWfs_id = [];
var buttonWfs_on = [];
var buttonWfs_src = [];
var buttonWfs_title_off = [];
var buttonWfs_title_on = [];
var buttonWfs_x = [];
var buttonWfs_y = [];

var numberOfAjaxCalls = 0;
var numberOfFinishedAjaxCalls = 0;
var resultGeometryPopup;

/**
 * This Geometry contains the geometry of the optinal spatial constraint
 */
var requestGeom = null;

/**
 * Something like box, polygon, point, extent
 */
var spatialRequestType = null;

/**
 * This Geometry contains the result from the WFS request
 */
var geomArray;

var mod_digitize_elName = typeof mod_digitize_elName === "undefined" ? "digitize" : mod_digitize_elName;
var wfsResultToPopupDiv = typeof wfsResultToPopupDiv === "undefined" ? 0 : wfsResultToPopupDiv;
var buttonWfs_toDigitize_on = typeof buttonWfs_toDigitize_on === "undefined" ? 0 : buttonWfs_toDigitize_on;
var displaySrsWarning = typeof displaySrsWarning === "undefined" ? false : displaySrsWarning;

if (wfsResultToPopupDiv == 1) {
	mb_registerWfsReadSubFunctions(function (geom) {
		displayPopup(geom);
	});
}
else {
	if (buttonWfs_toDigitize_on == 1) {
		mb_registerWfsReadSubFunctions(function(geom, wfsConfId){
			if (buttonWfs_toDigitize_target && window.frames[buttonWfs_toDigitize_target]) {
				try {
					tab_open(buttonWfs_toDigitize_target);
				}
				catch (exc) {
					new Mb_warning("Tab open failed, pssibly because you do not have tabs in your application."); 
				}
				appendGeometryArrayToDigitize(geom);
			}
			else {
				var msg = "No digitizing module available. " + 
					"Check you WFS spatial request configuration.";
				var e = new Mb_warning(msg);
			}
		});
	}
}

var msgObj;

mb_registerInitFunctions("init_wfsSpatialRequest()");
//mb_registerL10nFunctions("init_wfsSpatialRequest()");

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
	mb_ajax_json("../php/mod_wfsSpatialRequest_messages.php", function(obj, status) {
		msgObj = obj;
		buttonWfs_id = [];
		buttonWfs_on = [];
		buttonWfs_src = [];
		buttonWfs_title_off = [];
		buttonWfs_title_on = [];
		buttonWfs_x = [];
		buttonWfs_y = [];
		addButtonWfs("wfs_rectangle", buttonRectangle.status, buttonRectangle.img, msgObj.buttonLabelRectangle, buttonRectangle.x, buttonRectangle.y);
		addButtonWfs("wfs_polygon", buttonPolygon.status, buttonPolygon.img, msgObj.buttonLabelPolygon, buttonPolygon.x, buttonPolygon.y);
		addButtonWfs("wfs_point", buttonPoint.status, buttonPoint.img, msgObj.buttonLabelPoint, buttonPoint.x, buttonPoint.y);
		addButtonWfs("wfs_extent", buttonExtent.status, buttonExtent.img, msgObj.buttonLabelExtent, buttonExtent.x, buttonExtent.y);
		addButtonWfs("wfs_dialogue", buttonDialogue.status, buttonDialogue.img, msgObj.buttonLabelDialogue, buttonDialogue.x, buttonDialogue.y);
		displayButtons();
	});
}
// ------------------------------------------------------------------------------------------
// ------------ button handling -------------------------------------------------------------

function wfsInitFunction (j) {
	var functionCall = "mb_regButton_frame('initWfsButton', null, "+j+")";
	var x = new Function ("", functionCall);
	x();
}

function displayButtons() {
	for (var i = 0 ; i < buttonWfs_id.length ; i ++) {
		if (parseInt(buttonWfs_on[i])==1) {
			var currentDiv = document.createElement("div");
			currentDiv.id = buttonWfs_id[i]+"Div";
			currentDiv.style.position = "absolute";
			currentDiv.style.left = buttonWfs_x[i] + "px";
			currentDiv.style.top = buttonWfs_y[i] + "px";
			currentDiv.style.zIndex = buttonWfs_zIndex;

			var currentImg = document.createElement("img");
			currentImg.id = buttonWfs_id[i];
			currentImg.name = buttonWfs_id[i];
			currentImg.title = buttonWfs_title_off[i];
			currentImg.src = buttonWfs_imgdir+buttonWfs_src[i];
			currentImg.onmouseover = new Function("wfsInitFunction("+i+")");

			currentDiv.appendChild(currentImg);
			document.getElementsByTagName('body')[0].appendChild(currentDiv);
		}
	}
}

function initWfsButton(ind, pos) {
	mb_button[ind] = document.getElementById(buttonWfs_id[pos]);
	mb_button[ind].img_over = buttonWfs_imgdir + buttonWfs_src[pos].replace(/_off/,"_over");
	mb_button[ind].img_on = buttonWfs_imgdir + buttonWfs_src[pos].replace(/_off/,"_on");
	mb_button[ind].img_off = buttonWfs_imgdir + buttonWfs_src[pos];
	mb_button[ind].status = 0;
	mb_button[ind].elName = buttonWfs_id[pos];
	mb_button[ind].fName = "";
	mb_button[ind].go = new Function ("wfsEnable(mb_button["+ind+"], " + pos + ")");
	mb_button[ind].stop = new Function ("wfsDisable(mb_button["+ind+"], " + pos + ")");
	var ind = getMapObjIndexByName(mod_wfs_spatialRequest_target);
	mod_wfs_spatialRequest_width = mb_mapObj[ind].width;
	mod_wfs_spatialRequest_height = mb_mapObj[ind].height;
	mod_wfs_spatialRequest_epsg = mb_mapObj[ind].epsg;
	mb_registerPanSubElement("measuring");
	
	geomArray = new GeometryArray();
}

function wfsEnable(obj) {
	var el = getMapDoc();

	$(el).unbind("mousedown")
		.unbind("mouseover")
		.unbind("mouseup")
		.unbind("mousemove");

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
	else if (obj.id == button_dialogue) {
		activeButton = obj;
		mod_wfs_SpatialRequest_dialog();
	}
	callRequestGeometryConstructor(obj.id,"mapframe1");
}

function callRequestGeometryConstructor(selectedType,target){
	spatialRequestType = selectedType;
	var geometryConstructor = new RequestGeometryConstructor(target);
	geometryConstructor.getGeometry(selectedType.replace(/wfs_/, ""),function(target,queryGeom){
		if(queryGeom !=''){
			requestGeom = queryGeom;
		}
		mb_disableThisButton(selectedType);
		
		// requestGeom is a Geometry, but for the highlight
		// a MultiGeometry is needed.
		var multiGeom;
		// a line represents a bbox...but highlight must be a polyon
		// (extent or box selection)
		if (requestGeom.geomType == geomType.line) {
			multiGeom = new MultiGeometry(geomType.polygon);
			newGeom = new Geometry(geomType.polygon);
			var p1 = requestGeom.get(0);
			var p2 = requestGeom.get(1);
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
			multiGeom = new MultiGeometry(requestGeom.geomType);
			multiGeom.add(requestGeom);
		}
		
		// add highlight of geometry
		//requestGeometryHighlight.add(multiGeom);
		//requestGeometryHighlight.paint();
	});
}

function getMapDoc () {
	var mapIndex = getMapObjIndexByName(mod_wfs_spatialRequest_target);
	var mapDomElement = mb_mapObj[mapIndex].getDomElement();
	var mapType = mapDomElement.tagName.toUpperCase();
	if (mapType == "IFRAME") {
		return mapDomElement.window.document;
	}
	return window.document;
}

function wfsDisable(obj) {
	var ind = getMapObjIndexByName("mapframe1");
	var el = mb_mapObj[ind].getDomElement();
	$(el).unbind("mousedown")
		.unbind("click")
		.unbind("mousemove");
	activeButton = null;
	writeTag("","measure_display","");
	writeTag("","measure_sub","");
	mb_setwfsrequest(mod_wfs_spatialRequest_target);
}

// ---------------------------------------------------------------------------------------------

function register_setExtRequestSubFunctions(stringFunction){
	mod_wfs_spatialRequestSubFunctions[mod_wfs_spatialRequestSubFunctions.length] = stringFunction;
}

function mod_wfs_SpatialRequest_dialog(){
	if(!mod_wfs_spatialRequest_win || mod_wfs_spatialRequest_win == null || mod_wfs_spatialRequest_win.closed == true){
		mod_wfs_spatialRequest_win = window.open("","mod_wfs_spatialRequest_win","width=200,height=150,resizable=yes");
		mod_wfs_spatialRequest_win.document.open("text/html");

		mod_wfs_spatialRequest_win.document.writeln('<script type="text/javascript" type="text/javascript">');
		mod_wfs_spatialRequest_win.document.writeln('function set(obj){');
		mod_wfs_spatialRequest_win.document.writeln('for(var i=0; i< document.getElementsByName("geom").length; i++){');
		mod_wfs_spatialRequest_win.document.writeln('if(document.getElementsByName("geom")[i].checked){');
		mod_wfs_spatialRequest_win.document.writeln('window.opener.mod_setExtRequest_geom = document.getElementsByName("geom")[i].value;');
		mod_wfs_spatialRequest_win.document.writeln('}');
		mod_wfs_spatialRequest_win.document.writeln('}');
		mod_wfs_spatialRequest_win.document.writeln('window.opener.wfsEnable(obj);');
		mod_wfs_spatialRequest_win.document.writeln('window.close();');
		mod_wfs_spatialRequest_win.document.writeln('return false;	');
		mod_wfs_spatialRequest_win.document.writeln('}');
		mod_wfs_spatialRequest_win.document.writeln('</script>');

		mod_wfs_spatialRequest_win.document.writeln("<form>");
		mod_wfs_spatialRequest_win.document.writeln("<input id='wfs_point' name='geom' type='radio' value='"+button_point+"' onclick='set(this)'> Punkt<br>");
		mod_wfs_spatialRequest_win.document.writeln("<input id='wfs_rectangle' name='geom' type='radio' value='"+button_rectangle+"' onclick='set(this)'> Rechteck<br>");
		mod_wfs_spatialRequest_win.document.writeln("<input id='wfs_polygon' name='geom' type='radio' value='"+button_polygon+"'onclick='set(this)'> Polygon<br>");
		mod_wfs_spatialRequest_win.document.writeln("<input id='wfs_extent' name='geom' type='radio' value='"+button_extent+"'onclick='set(this)'> Extent<br>");
		var checked = "";
		mod_wfs_spatialRequest_win.document.writeln("</form>");
		mod_wfs_spatialRequest_win.document.close();
	}
	else{
		mod_wfs_spatialRequest_win.focus();
	}
}

function mb_setwfsrequest(target){
	if(geomArray.count()>0){
 		geomArray.empty();
 	}
	
	//mb_wfs_reset();
	var ind = getMapObjIndexByName(target);
	var db_wfs_conf_id = [];
	js_wfs_conf_id = [];

	//remove old result dialogs
	$('.spatialResultPopup').dialog('close');
	$('.spatialResultDetailPopup').dialog('close');
	
	wfs_config = window.frames["wfs_conf"].get_wfs_conf();
	for (var i=0; i<mb_mapObj[ind].wms.length; i++){
		for(var ii=0; ii<mb_mapObj[ind].wms[i].objLayer.length; ii++){
			var o = mb_mapObj[ind].wms[i].objLayer[ii];
			if(o.gui_layer_wfs_featuretype != '' && o.gui_layer_visible == '1'){
				// db_wfs_conf_id entries have to be unique
				var exists = false;
				for (var iii = 0; iii < db_wfs_conf_id.length; iii++) {
					if (db_wfs_conf_id[iii] == o.gui_layer_wfs_featuretype) {
						exists = true;
						break;
					}
				}	
				if (!exists) {
					db_wfs_conf_id[db_wfs_conf_id.length] = o.gui_layer_wfs_featuretype;
				}
			}
		}
	}
	for(var i=0; i<db_wfs_conf_id.length; i++){
		for(var ii in wfs_config){
			if(wfs_config[ii]['wfs_conf_id'] == db_wfs_conf_id[i]) {

				// js_wfs_conf_id entries have to be unique
				var exists = false;
				for (var iii = 0; iii < js_wfs_conf_id.length; iii++) {
					var n = js_wfs_conf_id[iii];
					if (wfs_config[ii]['wfs_conf_id'] == wfs_config[n]['wfs_conf_id']) {
						exists = true;
						break;
					}
				}	
				if (!exists) {
					js_wfs_conf_id[js_wfs_conf_id.length] = ii;
				}
			}
		}
	}

	numberOfAjaxCalls =  js_wfs_conf_id.length;

	if(requestGeom.geomType==geomType.polygon){
		for(var i=0; i<js_wfs_conf_id.length; i++){
// I guess we should use the SRS of the map client, not the WFS? 
// The coordinates come from the current client (?)
//			var srs = wfs_config[js_wfs_conf_id[i]]['featuretype_srs'];
			var srs = mod_wfs_spatialRequest_epsg;
			var filter = "<ogc:Filter>";

			var ftName = wfs_config[js_wfs_conf_id[i]]['featuretype_name'];
			var pattern = /:[0-9a-zA-Z_]+/;
			var ftns = "";
			if (ftName.match(pattern)) {
				ftns = ftName.replace(pattern, ":");
			}
			if(buttonPolygon.filteroption=='within'){
				filter += "<Within><ogc:PropertyName>";
				for(var j=0; j<wfs_config[js_wfs_conf_id[i]]['element'].length; j++){
					if(wfs_config[js_wfs_conf_id[i]]['element'][j]['f_geom'] == 1){
						filter += ftns + wfs_config[js_wfs_conf_id[i]]['element'][j]['element_name'];
					}
				}
				filter += "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\">";
				filter += "<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
				for(var k=0; k<requestGeom.count(); k++){
					if(k>0)	filter += " ";
					filter += requestGeom.get(k).x+","+requestGeom.get(k).y;
				}
				filter += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
				filter += "</gml:Polygon></Within>";
			}
			else if(buttonPolygon.filteroption=='intersects'){
				filter += "<Intersects><ogc:PropertyName>";
				for(var j=0; j<wfs_config[js_wfs_conf_id[i]]['element'].length; j++){
					if(wfs_config[js_wfs_conf_id[i]]['element'][j]['f_geom'] == 1){
						filter += ftns + wfs_config[js_wfs_conf_id[i]]['element'][j]['element_name'];
					}
				}
				filter += "</ogc:PropertyName><gml:Polygon srsName='"+srs+"'>";
				filter += "<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
				for(var k=0; k<requestGeom.count(); k++){
					if(k>0)	filter += " ";
					filter += requestGeom.get(k).x+","+requestGeom.get(k).y;
				}
				filter += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
				filter += "</gml:Polygon></Intersects>";
			}

			filter += '</ogc:Filter>';
			mb_get_geom(filter, i, wfs_config[js_wfs_conf_id[i]]['featuretype_name'], js_wfs_conf_id[i], db_wfs_conf_id[i]);
		}
	}
	else if(requestGeom.geomType==geomType.line){
		var rectangle = requestGeom.getBBox();
		for(var i=0; i<js_wfs_conf_id.length; i++){
// I guess we should use the SRS of the map client, not the WFS? 
// The coordinates come from the current client (?)
//			var srs = wfs_config[js_wfs_conf_id[i]]['featuretype_srs'];
			var srs = mod_wfs_spatialRequest_epsg;
			var filter = "<ogc:Filter>";

			var ftName = wfs_config[js_wfs_conf_id[i]]['featuretype_name'];
			var pattern = /:[0-9a-zA-Z_]+/;
			var ftns = "";
			if (ftName.match(pattern)) {
				ftns = ftName.replace(pattern, ":");
			}

			if(buttonRectangle.filteroption=='within'){
				filter += "<Within><ogc:PropertyName>";
				for(var j=0; j<wfs_config[js_wfs_conf_id[i]]['element'].length; j++){
					if(wfs_config[js_wfs_conf_id[i]]['element'][j]['f_geom'] == 1){
						filter += ftns + wfs_config[js_wfs_conf_id[i]]['element'][j]['element_name'];
					}
				}
				filter += "</ogc:PropertyName><gml:Polygon srsName='"+srs+"'>";
				filter += "<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
				filter += rectangle[0].x+","+rectangle[0].y;
				filter += " ";
				filter += rectangle[0].x+","+rectangle[1].y;
				filter += " ";
				filter += rectangle[1].x+","+rectangle[1].y;
				filter += " ";
				filter += rectangle[1].x+","+rectangle[0].y;
				filter += " ";
				filter += rectangle[0].x+","+rectangle[0].y;
				filter += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
				filter += "</gml:Polygon></Within>";
			}
			else if(buttonRectangle.filteroption=='intersects'){
				filter += "<Intersects><ogc:PropertyName>";
				for(var j=0; j<wfs_config[js_wfs_conf_id[i]]['element'].length; j++){
					if(wfs_config[js_wfs_conf_id[i]]['element'][j]['f_geom'] == 1){
						filter += ftns + wfs_config[js_wfs_conf_id[i]]['element'][j]['element_name'];
					}
				}
				filter += "</ogc:PropertyName><gml:Polygon srsName='"+srs+"'>";
				filter += "<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
				filter += rectangle[0].x+","+rectangle[0].y;
				filter += " ";
				filter += rectangle[0].x+","+rectangle[1].y;
				filter += " ";
				filter += rectangle[1].x+","+rectangle[1].y;
				filter += " ";
				filter += rectangle[1].x+","+rectangle[0].y;
				filter += " ";
				filter += rectangle[0].x+","+rectangle[0].y;
				filter += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
				filter += "</gml:Polygon></Intersects>";
			}

			filter += "</ogc:Filter>";
			mb_get_geom(filter, i, wfs_config[js_wfs_conf_id[i]]['featuretype_name'], js_wfs_conf_id[i], db_wfs_conf_id[i]);
		}
	}
	else if(requestGeom.geomType == geomType.point){
		var tmp = requestGeom.get(0);
		var mapPos = makeRealWorld2mapPos("mapframe1",tmp.x, tmp.y);
		var buffer = mb_wfs_tolerance/2;
		var mapPosXAddPix = mapPos[0] + buffer;
		var mapPosYAddPix = mapPos[1] +buffer;
		var mapPosXRemovePix = mapPos[0] - buffer;
		var mapPosYRemovePix = mapPos[1] - buffer;
		var realWorld1 = makeClickPos2RealWorldPos("mapframe1",mapPosXRemovePix,mapPosYRemovePix);
		var realWorld2 = makeClickPos2RealWorldPos("mapframe1",mapPosXAddPix,mapPosYRemovePix);
		var realWorld3 = makeClickPos2RealWorldPos("mapframe1",mapPosXAddPix,mapPosYAddPix);
		var realWorld4 = makeClickPos2RealWorldPos("mapframe1",mapPosXRemovePix,mapPosYAddPix);
		
		
		for(var i=0; i<js_wfs_conf_id.length; i++){

// I guess we should use the SRS of the map client, not the WFS? 
// The coordinates come from the current client (?)
//			var srs = wfs_config[js_wfs_conf_id[i]]['featuretype_srs'];
			var ftName = wfs_config[js_wfs_conf_id[i]]['featuretype_name'];
			var pattern = /:[0-9a-zA-Z_]+/;
			var ftns = "";
			if (ftName.match(pattern)) {
				ftns = ftName.replace(pattern, ":");
			}
			var srs = mod_wfs_spatialRequest_epsg;
			var filter = "<ogc:Filter>";
			filter += "<Intersects><ogc:PropertyName>";
			for(var j=0; j<wfs_config[js_wfs_conf_id[i]]['element'].length; j++){
				if(wfs_config[js_wfs_conf_id[i]]['element'][j]['f_geom'] == 1){
					filter += ftns + wfs_config[js_wfs_conf_id[i]]['element'][j]['element_name'];
				}
			}
			filter += "</ogc:PropertyName><gml:Polygon srsName='"+srs+"'><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
			filter += realWorld1[0] + "," + realWorld1[1] + " " + realWorld2[0] + "," + realWorld2[1] +  " ";
			filter += realWorld3[0] + "," + realWorld3[1] + " " + realWorld4[0] + "," + realWorld4[1] + " " + realWorld1[0] + "," + realWorld1[1];
			filter += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon></Intersects></ogc:Filter>";
			mb_get_geom(filter, i, wfs_config[js_wfs_conf_id[i]]['featuretype_name'], js_wfs_conf_id[i], db_wfs_conf_id[i]);
		}
	}
//	highlight = new Highlight(mb_wfs_targets, highlight_tag_id, {"position":"absolute", "top":"0px", "left":"0px", "z-index":generalHighlightZIndex}, generalHighlightLineWidth);
	return true;
}

function mb_get_geom(filter, index, typename, js_wfs_conf_id, db_wfs_conf_id) {

	mb_ajax_post(
		"../" + wfsResultModulePath + wfsResultModuleFilename,
		{
			'filter' : filter,
			'typename' : typename,
			'js_wfs_conf_id' : js_wfs_conf_id, 
			'db_wfs_conf_id' : db_wfs_conf_id,
			'destSrs' : mod_wfs_spatialRequest_epsg
		},
		function(json,status){
			var geom = new GeometryArray();
			if (geom.importGeoJSON(json)) {
				for (var i = 0; i < geom.count(); i++) {
					geom.get(i).wfs_conf = parseInt(js_wfs_conf_id);
				}
			}
			checkIfAllAjaxCallsFinished(geom, db_wfs_conf_id);
		}
	);
}

function checkIfAllAjaxCallsFinished (geom, wfsConfId) {
	numberOfFinishedAjaxCalls++;
	if (typeof(geom) == 'object'){
		var mapIndex = getMapObjIndexByName(mod_wfs_spatialRequest_target);
		if (geom.count() === 0) {
			var e = new Mb_exception("Result set is empty.");
		}
		else {
		
			if (geom.get(0).getEpsg() !== mb_mapObj[mapIndex].epsg) {
				var e = new Mb_warning("SRS mismatch. Geometry is in " + geom.get(0).getEpsg() + ", map is in " + mb_mapObj[mapIndex].epsg + ".");
			}
			geomArray.union(geom);
		}
	}
	if (numberOfFinishedAjaxCalls == numberOfAjaxCalls) {
		numberOfFinishedAjaxCalls = 0;
		mb_execWfsReadSubFunctions(geomArray, wfsConfId);
	}
}

function useExtentIsSet () {
	return mod_wfs_spatialRequest_useExtent;
}

function isValidWfsConfIndex (wfsConf, wfsConfIndex) {
	return (typeof(wfsConfIndex) == "number" && wfsConfIndex >=0 && wfsConfIndex < wfsConf.length);
}

function Numsort (a, b) {
  return a < b;
}

function getListTitle (geom) {
	wfsConfId = geom.wfs_conf;
	wfsConf = get_complete_wfs_conf();
	if (isValidWfsConfIndex(wfsConf, wfsConfId)) {
		var resultArray = [];
		var resultName = "";
		for (var i = 0 ; i < wfsConf[wfsConfId]['element'].length ; i++) {
			if (wfsConf[wfsConfId]['element'][i]['f_show'] == 1 && geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']) !=false) {
				var pos = wfsConf[wfsConfId]['element'][i]['f_respos'];
				if (typeof(resultArray[pos]) != "undefined") {
					resultArray[pos] += " " + geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']);
				}
				else {
					resultArray[pos] = geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']);
				}
				resultName += geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']) + " ";
			}
		}
		resultArray.sort(Numsort);
		var resultName = resultArray.join(" ");
		if (resultName == "") {
			resultName = wfsConf[wfsConfId]['g_label'];
		}
		return resultName;
	}
	else {
		return msgObj.digitizeDefaultGeometryName;
	}
}

function appendGeometryArrayToDigitize(geom){
	var mapIndex = getMapObjIndexByName(mod_wfs_spatialRequest_target);
	if (!geom || geom.count() === 0) {
		return;
	}
	var proceed = true;
	if (geom.get(0).getEpsg() !== mb_mapObj[mapIndex].epsg) {
		var msg = "SRS mismatch. Geometry is in " + geom.get(0).getEpsg() + 
			", map is in " + mb_mapObj[mapIndex].epsg + ".";
		var e = new Mb_warning(msg);
		if (displaySrsWarning) {
			proceed = confirm(msg + " Proceed?");
		}
	}
	if (!proceed) {
		return;
	}
	try {
		window.frames[mod_digitize_elName].appendGeometryArray(geom);
		try {
			tab_open(mod_digitize_elName);
		}
		catch (exc) {
			new Mb_warning("Tab open failed, pssibly because you do not have tabs in your application."); 
		}
	}
	catch (e) {
		var e = new Mb_exception("The application element 'digitize' is missing.");
	}
}

function appendGeometryToDigitize(i){
	var mapIndex = getMapObjIndexByName(mod_wfs_spatialRequest_target);

	var proceed = true;
	if (geomArray.get(i).getEpsg() !== mb_mapObj[mapIndex].epsg) {
		var msg = "SRS mismatch. Geometry is in " + geomArray.get(0).getEpsg() + 
			", map is in " + mb_mapObj[mapIndex].epsg + ".";
		var e = new Mb_warning(msg);
		if (displaySrsWarning) {
			proceed = confirm(msg + " Proceed?");
		}
	}
	if (!proceed) {
		return;
	}
	var digitizeArray = new GeometryArray();
	
	digitizeArray.importGeoJSON(geomArray.get(i).toString());
	digitizeArray.get(0).wfs_conf = geomArray.get(i).wfs_conf;
	
	try {
		window.frames[mod_digitize_elName].appendGeometryArray(digitizeArray);
		try {
			tab_open(mod_digitize_elName);
		}
		catch (exc) {
			new Mb_warning("Tab open failed, pssibly because you do not have tabs in your application."); 
		}
	}
	catch (e) {
		var e = new Mb_exception("The application element 'digitize' is missing.");
	}
}

function createListOfGeometries(){
	var listOfGeom = "<table>\n";
	if (geomArray.count() > 0) {
		if(buttonWfs_toDigitize_on==1){
			listOfGeom += "<tr><td style='color:black;font-size:12px;'>edit all</td>\n";
			listOfGeom += "<td><img title='edit all' src='"+buttonWfs_toDigitize_src+"'  style='cursor:pointer' onclick='appendGeometryArrayToDigitize(geomArray);'></img>";
			listOfGeom += "</td>\n</tr>\n";
			listOfGeom += "<tr>\n<td>&nbsp;</td>\n</tr>\n";
		}
		for (var i = 0 ; i < geomArray.count(); i ++) {
			if (geomArray.get(i).get(-1).isComplete()) {
				listOfGeom += "\t<tr>\n\t\t<td style = 'color:blue;font-size:12px;cursor:pointer;'\n";
				listOfGeom += "\t\t\t onmouseover='mb_wfs_perform(\"over\",geomArray.get("+i+"), resultHighlightColour);' ";
				listOfGeom += " onmouseout='mb_wfs_perform(\"out\",geomArray.get("+i+"), resultHighlightColour)' ";
				listOfGeom += " onclick='mb_wfs_perform(\"click\",geomArray.get("+i+"), resultHighlightColour); showWfs("+i+");' ";
				var geomName = getListTitle(geomArray.get(i));
				//if (geomArray.get(i).geomType == geomType.polygon) {geomName += "(polygon)";}
				//else if (geomArray.get(i).geomType == geomType.line) {geomName += "(line)";}
				//else if (geomArray.get(i).geomType == geomType.point) {geomName += "(point)";}
				listOfGeom += ">" + geomName +"</td>";
				if(buttonWfs_toDigitize_on==1){
					listOfGeom += "<td><img title='edit geometry object' src='"+buttonWfs_toDigitize_src+"'  style='cursor:pointer' onclick='appendGeometryToDigitize("+i+");'></img></td>";
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
	
	var resultGeometryPopup = $('<div class="spatialResultPopup"></div>');
	resultGeometryPopup.append(createListOfGeometries());
	
	resultGeometryPopup.dialog({
		title : searchPopupTitle, 
		autoOpen : true, 
		draggable : true,
		width : searchPopupWidth,
		position : [searchPopupX,searchPopupY]
	});
}

function showWfs(geometryIndex) {
	$('.spatialResultDetailPopup').dialog('close');
	var wfsConfIndex = geomArray.get(geometryIndex).wfs_conf;
	var currentWfsConf = wfsConf[wfsConfIndex];

	var resultHtml = "";
	resultHtml += "<table style='background-color:#EEEEEE;'>\n";
	for (var i = 0 ; i <currentWfsConf.element.length; i ++) {
	    if(currentWfsConf.element[i].f_show_detail==1){
	    	if( geomArray.get(geometryIndex).e.getElementValueByName(currentWfsConf.element[i].element_name)!=false){
				resultHtml +="<tr><td>\n";
				resultHtml += currentWfsConf.element[i].f_label;
				resultHtml +="</td>\n";
				resultHtml += "<td>\n";
				var elementVal = geomArray.get(geometryIndex).e.getElementValueByName(currentWfsConf.element[i].element_name);
				if(currentWfsConf.element[i].f_form_element_html.indexOf("href")!=-1){
					var setUrl = currentWfsConf.element[i].f_form_element_html.replace(/href\s*=\s*['|"]\s*['|"]/, "href='"+elementVal+"' target='_blank'");
					if(setUrl.match(/><\/a>/)){
						var newLink	=	setUrl.replace(/><\/a>/, ">"+elementVal+"</a>");
					}
					else{
						var newLink = setUrl;
					}
					if(openLinkFromSearch=='1'){
						window.open(elementVal, elementVal,"width=500, height=400,left=100,top=100,scrollbars=yes");
					}
					resultHtml +=  newLink;
				}
				else{
					resultHtml += elementVal;
				}
				resultHtml += "</td></tr>\n";
			}
		}
	}
	resultHtml += "</table>\n";

	var getCenter =  geomArray.get(geometryIndex).getCenter();
	// getMapPos for positioning of new PopupDiv near object in mapframe1
	//var getMapPos = makeRealWorld2mapPos("mapframe1",getCenter.x, getCenter.y);
	
	var wfsPopup = $('<div class="spatialResultDetailPopup"></div>');
	wfsPopup.append(resultHtml);
	
	wfsPopup.dialog({
		title : detailPopupTitle, 
		autoOpen : true, 
		draggable : true,
		width : detailPopupWidth,
		position : [detailPopupX,detailPopupY]
	});
}
