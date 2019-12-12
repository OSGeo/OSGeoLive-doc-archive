<?php
# $Id$
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

require_once(dirname(__FILE__) . "/../php/mb_validateSession.php");

?>
// ---------------------------------------------------------------------------------------------------------------
// --- usemap (begin) --------------------------------------------------------------------------------------------

function mod_usemap(wfs_name) {
	if (wfs_name == "") {
		usemap = "";
	}
	var ind = getMapObjIndexByName(mb_wfs_targets[0]);
	var myImg = window.frames[mb_wfs_targets[0]].document.getElementById("um_img").style; 
	myImg.width = mb_mapObj[ind].width;
	myImg.height = mb_mapObj[ind].height;

	for (var i = 0 ; i < mb_wfs_fetch.count() ; i ++) {
		if (mb_wfs_fetch.get(i).wfs_conf == wfs_name || wfs_name == "") {
		
			if (mb_wfs_fetch.get(i).geomType == geomType.polygon) {
				usemap += mod_usemap_polygon(i);
			}
			else if (mb_wfs_fetch.get(i).geomType == geomType.point) {
				usemap += mod_usemap_circle(i);
			}
			else if (mb_wfs_fetch.get(i).geomType == geomType.line) {
				usemap += mod_usemap_line(i);
			}
		}
	}
	writeUsemap(usemap);
}

function mod_usemap_circle(ind){
	var str = "";
	var coord = "";
	
	var title = "";
	for (var i = 0 ; i < mb_wfs_fetch.get(ind).e.count(); i++) {
		if (i>0) title += "&#10;";
		title += mb_wfs_fetch.get(ind).e.getName(i) + ": " + mb_wfs_fetch.get(ind).e.getValue(i);
	}

	for (var i = 0 ; i < mb_wfs_fetch.get(ind).count() ; i ++) {
		var p = mb_wfs_fetch.getPoint(ind, i, 0);
		var pos = realToMap(mb_wfs_targets[0],p);
		coord += pos.x + ", " + pos.y;
		
		str += "<AREA title='"+title+"' onmouseover='parent.mb_wfs_perform(\"over\",parent.mb_wfs_fetch.get("+ind+"))' ";
		str += "onmouseout='parent.mb_wfs_perform(\"out\",parent.mb_wfs_fetch.get("+ind+"))' shape='circle'  coords='";
		str += coord + ", " + mod_usemap_radius + "' href='#'>";
	}
	
	return str;
}

function mod_usemap_line_calculate (aGeometry, j, orientation, cnt) {
	var coord = "";

	var p1 = realToMap(mb_wfs_targets[0],aGeometry.get(j));
	var p2 = realToMap(mb_wfs_targets[0],aGeometry.get(j+orientation));

	var vec = p2.minus(p1);
	
	if (vec.x != 0 || vec.y != 0) {
		var n_vec;
		if (vec.x != 0) {
			if (vec.x > 0) n_vec = new Point((-vec.y)/vec.x, -1);
			else n_vec = new Point(vec.y/vec.x, 1);
		}
		else {
			if (vec.y > 0) n_vec = new Point(1,0);
			else n_vec = new Point(-1,0);
		}
		n_vec = n_vec.times(mod_usemap_line_tolerance).dividedBy(n_vec.dist(new Point(0,0)))

		lp = new Point(p1.x + n_vec.x, p1.y - n_vec.y);

		if (cnt > 0) coord += ", ";

		coord += parseInt(lp.x) + ", " + parseInt(lp.y);
		coord += ", " + parseInt(lp.x+vec.x) + ", " + parseInt(lp.y+vec.y);
	}
	return coord;
}

function mod_usemap_line(ind){
	var str = "";
	var title = "";
	for (var i = 0 ; i < mb_wfs_fetch.get(ind).e.count(); i++) {
		if (i>0) title += "&#10;";
		title += mb_wfs_fetch.get(ind).e.getName(i) + ": " + mb_wfs_fetch.get(ind).e.getValue(i);
	}
	for (var i = 0 ; i < mb_wfs_fetch.get(ind).count() ; i ++) {
		var coord = "";
		var cnt = 0;

		for (var j = 0 ; j < mb_wfs_fetch.getGeometry(ind,i).count() - 1  ; j ++) {
			var result = mod_usemap_line_calculate(mb_wfs_fetch.getGeometry(ind,i), j, 1, cnt);
			if (result != "") {
				coord += result;
				cnt++;
			}
		}
		
		for (var j = (mb_wfs_fetch.getGeometry(ind,i).count() - 1) ; j > 0 ; j--) {
			var result = mod_usemap_line_calculate(mb_wfs_fetch.getGeometry(ind,i), j, -1, cnt);
			if (result != "") {
				coord += result;
				cnt++;
			}
		}
		
		if (coord != "") {
			str += "<AREA title='"+title+"'";
			str += "onmouseover='parent.mb_wfs_perform(\"over\",parent.mb_wfs_fetch.get("+ind+"))' ";
			str += "onmouseout='parent.mb_wfs_perform(\"out\",parent.mb_wfs_fetch.get("+ind+"))' ";
			str += "shape='poly'  coords='";
			str += coord + "' href='#'>";
		}
		else {
			//display circle
			var pos = realToMap(mb_wfs_targets[0],mb_wfs_fetch.getPoint(ind,i,0));
			coord += pos.x + ", " + pos.y;
			
			str += "<AREA title='"+title+"' onmouseover='parent.mb_wfs_perform(\"over\",parent.mb_wfs_fetch["+ind+"])' ";
			str += "onmouseout='parent.mb_wfs_perform(\"out\",parent.mb_wfs_fetch.get("+ind+"))' shape='circle'  coords='";
			str += coord + ", " + mod_usemap_radius + "' href='#'>";
		}
	}
	
	return str;
}

function mod_usemap_polygon(ind){
	var str = "";
	var coord = "";
	var title = "";
	for (var i = 0 ; i < mb_wfs_fetch.get(ind).e.count(); i++) {
		if (i>0) title += "&#10;";
		title += mb_wfs_fetch.get(ind).e.getName(i) + ": " + mb_wfs_fetch.get(ind).e.getValue(i);
	}

	for (var i = 0 ; i < mb_wfs_fetch.get(ind).count() ; i ++) {
		var pos = realToMap(mb_wfs_targets[0],mb_wfs_fetch.getPoint(ind, i, 0));
		coord += pos.x + ", " + pos.y;
		
		for (var j = 1 ; j < mb_wfs_fetch.getGeometry(ind,i).count() ; j ++) {
			pos = realToMap(mb_wfs_targets[0],mb_wfs_fetch.getPoint(ind, i, j));
			coord += ", " + pos.x + ", " + pos.y;
		}
		
		str += "<AREA title='"+title+"' onmouseover='parent.mb_wfs_perform(\"over\",parent.mb_wfs_fetch.get("+ind+"))' ";
		str += "onmouseout='parent.mb_wfs_perform(\"out\",parent.mb_wfs_fetch.get("+ind+"))' shape='poly'  coords='";
		str += coord + "' href='#'>";
	}
	
	return str;
}

function writeUsemap(str) {
	writeTag(mb_wfs_targets[0], 'um', str);
}
// --- usemap (end) ----------------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------------------


var highlight_tag_id = "wfs_highlight_tag";
var mb_wfs_fetched = [];

var mb_wfs_objwin = null;
var mb_wfs_objwin_left = 800;
var mb_wfs_objwin_top = 200;
var mb_wfs_objwin_width = 200;
var mb_wfs_objwin_height = 200;
var mb_wfs_targetString = "<?php echo implode(",", $e_target); ?>";
var mb_wfs_targets = mb_wfs_targetString.split(",");
var mb_wfs_fillColor = "#ff0000";
var usemap = "";
var mod_usemap_radius = 10;
var mod_usemap_line_tolerance = 5;
var useCheckboxForHighlighting = false;

var mb_wfs_fetch = new GeometryArray();

var highlight;

try {if(generalHighlightZIndex){}}catch(e) {generalHighlightZIndex = 90;}
try {if(generalHighlightLineWidth){}}catch(e) {generalHighlightLineWidth = 2;}
try {if(useUsemap){}}catch(e) {useUsemap = 0;}

mb_registerInitFunctions('initHighlight()');

function initHighlight() {
	var styleObj = {"position":"absolute", "top":"0px", "left":"0px", "z-index":generalHighlightZIndex};
	highlight = new Highlight(mb_wfs_targets, highlight_tag_id, styleObj, generalHighlightLineWidth);
}
try {if(displayWfsResultList){}}catch(e) {displayWfsResultList = 0;};

if (displayWfsResultList == 1) {
	//mb_registerWfsReadSubFunctions(function(geom){mb_wfs_listMember(geom)});
}


if (parseInt(useUsemap) == 1) {
	mb_registerSubFunctions('mod_usemap("")');
}

if (useCheckboxForHighlighting) {
	eventInit.register(function() {
		mb_registerSubFunctions('highlight.paint()');
	});
}

/*
if (useExtentIsSet()) {
	mb_registerSubFunctions("mb_setwfsrequest_extent()");
}
function mb_setwfsrequest_extent() {

	if (useExtentIsSet()) {
		var ind = getMapObjIndexByName(mb_wfs_targets[0]);
		var pos_a = makeClickPos2RealWorldPos(mb_wfs_targets[0],0,0);
		var pos_b = makeClickPos2RealWorldPos(mb_wfs_targets[0],mb_mapObj[ind].width,mb_mapObj[ind].height);

		var x = [];
		var y = [];
		x[0] = pos_a[0];
		x[1] = pos_b[0];
		y[0] = pos_a[1];
		y[1] = pos_b[1];

		mb_setwfsrequest(mb_wfs_targets[0],'rectangle',x,y);
	}
}
*/

function mb_wfs_listMember(geomArray){
	mb_wfs_fetch.union(geomArray);
	var wfs_conf = get_complete_wfs_conf();
	var str = "<table>";
	for(var i=0; i<mb_wfs_fetch.count(); i++){
		var t = wfs_conf[mb_wfs_fetch.get(i).wfs_conf];
		for(var j=0; j<t['element'].length; j++){
			if(t['element'][j]['f_show'] > 0){
				var k = mb_wfs_fetch.get(i).e.getElementIndexByName(t['element'][j]['element_name']);
				//alert(k);
				if(k != -1){
					str += "<tr><td>";
					if (useCheckboxForHighlighting) {
						str += "<input type=checkbox id=highlightCheckbox" + i + " onChange='highlightGeometry(" + i + ")'></td><td>";
					}
					str += "<div";
					if (!useCheckboxForHighlighting) {
						str += " onmouseover='mb_wfs_perform(\"over\",mb_wfs_fetch.get("+i+"))' ";
						str += " onmouseout='mb_wfs_perform(\"out\",mb_wfs_fetch.get("+i+"))' ";
					}
					str += " onclick='mb_wfs_perform(\"click\",mb_wfs_fetch.get("+i+"))' ";
					str += ">" + mb_wfs_fetch.get(i).e.getValue(k)+ "</div></td></tr>";
				}
			}
		}
	}
	str += "</table>";
	mb_wfs_objwin.innerHTML = str;
//	if (parseInt(useUsemap) == 1) mod_usemap(wfs_name);
}

function mb_wfs_reset(){
	mb_wfs_fetch = new parent.GeometryArray();
	usemap = "";

	if(mb_wfs_objwin == null){
		var iframe=document.createElement('div');
		iframe.setAttribute("style","position:absolute;left:"+mb_wfs_objwin_left+"px;top:"+mb_wfs_objwin_top+"px;width:"+mb_wfs_objwin_width+"px;height:"+mb_wfs_objwin_height+"px");
		mb_wfs_objwin = document.body.appendChild(iframe);
		mb_wfs_objwin.id = "mb_wfs_objwin";
		mb_wfs_objwin.name = "mb_wfs_objwin";
		mb_wfs_objwin.style.position = 'absolute';
		mb_wfs_objwin.style.left = mb_wfs_objwin_left+"px";
		mb_wfs_objwin.style.top = mb_wfs_objwin_top+"px";
		mb_wfs_objwin.style.width = mb_wfs_objwin_width+"px";
		mb_wfs_objwin.style.height = mb_wfs_objwin_height+"px";
	}
	for(var i=0; i<mb_wfsreq; i++){
		if(document.getElementById("mb_wfs_win_"+mb_wfsreq)){
			document.removeChild("mb_wfs_win_"+mb_wfsreq);
		}
	}
	mb_wfsreq = 0;
	return true;
}

function get_complete_wfs_conf() {
	var wfs_conf = window.frames["wfs_conf"].get_wfs_conf();
	return wfs_conf;
}

function highlightGeometry(i) {
	var id = "highlightCheckbox"+i;
	if (document.getElementById(id).checked) {
		highlight.add(mb_wfs_fetch.get(i), '#00ff00');
		highlight.paint();
	}
	else {
		highlight.del(mb_wfs_fetch.get(i), '#00ff00');
		highlight.paint();
	}
}

function mb_wfs_perform(type,m, colour){
	if (typeof(colour) == "undefined") {
		colour = "#ff0000";
	}

	if(type=='over') {
		highlight.add(m, colour);
		highlight.paint();
	}
	else if (type == 'out') {
		highlight.del(m, colour);
		highlight.paint();
	}
	else if (type == 'clean') {
		highlight.clean();
		highlight.paint();
	}
	else
		if (type == 'click') {
			var wfs_conf = window.frames["wfs_conf"].get_wfs_conf();
			var tmp = m.getBBox();
			if (m.geomType == geomType.point) {
				var b = 1;
			}
			else {
				var b = 0;
			}
			if (typeof(m.wfs_conf) != "undefined") {
				b = parseFloat(wfs_conf[m.wfs_conf]['g_buffer']);
			}
			var buffer = new Point(b, b);
			var bbox_ll = tmp[0].minus(buffer);
			var bbox_ru = tmp[1].plus(buffer);
			Mapbender.modules[mb_wfs_targets[0]].calculateExtent(new Mapbender.Extent(bbox_ll, bbox_ru));
			highlight.del(m, colour);
			zoom(mb_wfs_targets[0], 'true', 1.0);
			highlight.add(m, colour);
			highlight.paint();
		}
}

function get_wfs_str(myconf, d, m, type, fid) {

	var featureTypeArray = myconf['featuretype_name'].split(':')
	var featureNS = featureTypeArray[0];

	var str = '<wfs:Transaction version="1.0.0" service="WFS" ';

	var ns_gml = false;	var ns_ogc = false;	var ns_xsi = false;	var ns_wfs = false;	var ns_featureNS = false;

	for (var q = 0 ; q < myconf['namespaces'].length ; q++) {

		if (myconf['namespaces'][q]['name'] == "gml"){
			 ns_gml = true;
			 str += 'xmlns:' + myconf['namespaces'][q]['name'] + '="' + myconf['namespaces'][q]['location'] + '" ';
		} else if (myconf['namespaces'][q]['name'] == "ogc") {
			ns_ogc = true;
			str += 'xmlns:' + myconf['namespaces'][q]['name'] + '="' + myconf['namespaces'][q]['location'] + '" ';
		} else if (myconf['namespaces'][q]['name'] == "xsi") {
			ns_xsi = true;
			str += 'xmlns:' + myconf['namespaces'][q]['name'] + '="' + myconf['namespaces'][q]['location'] + '" ';
		} else if (myconf['namespaces'][q]['name'] == "wfs") {
			ns_wfs = true;
			str += 'xmlns:' + myconf['namespaces'][q]['name'] + '="' + myconf['namespaces'][q]['location'] + '" ';
		} else if (myconf['namespaces'][q]['name'] == featureNS) {
			ns_featureNS = true;
			str += 'xmlns:' + myconf['namespaces'][q]['name'] + '="' + myconf['namespaces'][q]['location'] + '" '
			strForSchemaLocation = myconf['namespaces'][q]['location'];
		}
	}

	if (ns_gml == false) str += 'xmlns:gml="http://www.opengis.net/gml" ';
	if (ns_ogc == false) str += 'xmlns:ogc="http://www.opengis.net/ogc" ';
	if (ns_xsi == false) str += 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
	if (ns_featureNS == false) str += 'xmlns:"+featureNS+"="http://www.someserver.com/"+featureNS+"" ';
	if (ns_wfs == false) str += 'xmlns:wfs="http://www.opengis.net/wfs" ';

	str += 'xsi:schemaLocation="http://www.opengis.net/wfs';
	str += ' http://schemas.opengis.net/wfs/1.0.0/WFS-transaction.xsd';
	str += ' ' + strForSchemaLocation;
	str += ' '+ myconf['wfs_describefeaturetype'];
	//str += mb_getConjunctionCharacter(myconf['wfs_describefeaturetype']);
	//str += 'typename=' + myconf['featuretype_name'];
	str += '">';

	//
	// ---------------------------------------- SAVE -------------------------------------------------
	//
	if (type == "save") {
		str += '<wfs:Insert><'+ myconf['featuretype_name']+'>';
		for(var i=0; i<d.get(m).e.count(); i++){
			if(d.get(m).e.getValue(i) != "" && d.get(m).e.getName(i) != "fid"){
				var tmp = d.get(m).e.getName(i);
				str += '<' + tmp  + '><![CDATA[' + d.get(m).e.getValue(i) + ']]></' + tmp  + '>';
			}
		}
		for(var j=0; j<myconf['element'].length; j++){
			if(myconf['element'][j]['f_geom'] == 1){
				var el_geom = myconf['element'][j]['element_name'];
			}
		}
		str += '<' + el_geom + '>';
		if(d.get(m).geomType == geomType.point){
			str += '<gml:Point srsName="' + myconf['featuretype_srs'] + '">';
			str += '<gml:coordinates>';
			str += d.getPoint(m,0,0).x + "," + d.getPoint(m,0,0).y;
			str += '</gml:coordinates>';
			str += '</gml:Point>';
		}
		if(d.get(m).geomType == geomType.line){
			str += '<gml:MultiLineString srsName="' + myconf['featuretype_srs'] + '">';
			str += '<gml:lineStringMember><gml:LineString><gml:coordinates>';
			for(var k=0; k<d.getGeometry(m,0).count(); k++){
				if(k>0)	str += " ";
				str += d.getPoint(m,0,k).x + "," + d.getPoint(m,0,k).y;
			}
			str += '</gml:coordinates></gml:LineString></gml:lineStringMember>';
			str += '</gml:MultiLineString>';
		}
		if(d.get(m).geomType == geomType.polygon){
			str += '<gml:MultiPolygon srsName="' + myconf['featuretype_srs'] + '">';
			for (var k = 0; k < d.get(m).count(); k++) {
				str += '<gml:polygonMember><gml:Polygon><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>';
	
				for(var l = 0; l < d.getGeometry(m, k).count(); l++){
					if (l > 0) {
						str += " ";
					}	
					str += d.getPoint(m,k,l).x + "," + d.getPoint(m,k,l).y;
				}

				str += '</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>';
				
				if (d.getGeometry(m, k).innerRings) {
					for(var ii = 0; ii < d.getGeometry(m, k).innerRings.count(); ii++){
						str += '<gml:innerBoundaryIs><gml:LinearRing><gml:coordinates>';
						for(var l = 0; l < d.getGeometry(m, k).innerRings.get(ii).count(); l++){
							if (l > 0) {
								str += " ";
							}	
							str += d.getPoint(m,k,ii,l).x + "," + d.getPoint(m,k,ii,l).y;
						}
						str += '</gml:coordinates></gml:LinearRing></gml:innerBoundaryIs>';
					}
				}

				str += '</gml:Polygon></gml:polygonMember>';
			}
			str += '</gml:MultiPolygon>';
		}
		str += '</' + el_geom + '></'+ myconf['featuretype_name']+'></wfs:Insert>';
	}
	//
	// --------------------------------------- UPDATE ------------------------------------------------
	//
	else if (type == "update") {
		str += '<wfs:Update typeName="'+ myconf['featuretype_name']+'">';
		for(var i=0; i<d.get(m).e.count(); i++){
			if(d.get(m).e.getValue(i) != "" && d.get(m).e.getName(i) != "fid"){
				str += '<wfs:Property>';
				str += '<wfs:Name>'+d.get(m).e.getName(i)+'</wfs:Name>';
				str += '<wfs:Value><![CDATA['+d.get(m).e.getValue(i)+']]></wfs:Value>';
				str += '</wfs:Property>';
			}

			if(d.get(m).e.getName(i) != "fid") {
				str += '<wfs:Property>';
				str += '<wfs:Name>'+d.get(m).e.getName(i)+'</wfs:Name>';
				str += '</wfs:Property>';
			}
			
		}
		for(var j=0; j<myconf['element'].length; j++){
			if(myconf['element'][j]['f_geom'] == 1){
				var el_geom = myconf['element'][j]['element_name'];
			}
		}
		str += '<wfs:Property><wfs:Name>' + el_geom + '</wfs:Name><wfs:Value>';
		if(d.get(m).geomType == geomType.point){
			str += '<gml:Point srsName="' + myconf['featuretype_srs'] + '"><gml:coordinates>';
			str += d.getPoint(m,0,0).x + "," + d.getPoint(m,0,0).y;
			str += '</gml:coordinates></gml:Point>';
		}
		if(d.get(m).geomType == geomType.line){
			str += '<gml:MultiLineString srsName="' + myconf['featuretype_srs'] + '">';
			str += '<gml:lineStringMember><gml:LineString><gml:coordinates>';
			for(var k=0; k<d.getGeometry(m,0).count(); k++){
				if(k>0)	str += " ";
				str += d.getPoint(m,0,k).x + "," + d.getPoint(m,0,k).y;
			}
			str += '</gml:coordinates></gml:LineString></gml:lineStringMember>';
			str += '</gml:MultiLineString>';
		}
		if(d.get(m).geomType == geomType.polygon){
			str += '<gml:MultiPolygon srsName="' + myconf['featuretype_srs'] + '">';
			for (var l = 0; l < d.get(m).count(); l++) {
				str += '<gml:polygonMember><gml:Polygon><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>';
				for(var k=0; k<d.getGeometry(m,l).count(); k++){
					if(k>0)	str += " ";
					str += d.getPoint(m,l,k).x + "," + d.getPoint(m,l,k).y;
				}
				str += '</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>';
				if (d.getGeometry(m, l).innerRings) {
					for(var ii = 0; ii < d.getGeometry(m, l).innerRings.count(); ii++){
						str += '<gml:innerBoundaryIs><gml:LinearRing><gml:coordinates>';
						for(var p = 0; p < d.getGeometry(m, l).innerRings.get(ii).count(); p++){
							if (p > 0) {
								str += " ";
							}	
							str += d.getPoint(m,l,ii,p).x + "," + d.getPoint(m,l,ii,p).y;
						}
						str += '</gml:coordinates></gml:LinearRing></gml:innerBoundaryIs>';
					}
				}
				str += '</gml:Polygon></gml:polygonMember>';
			}
		}
		str += '</gml:MultiPolygon>';
		str += '</wfs:Value></wfs:Property>';
		str += '<ogc:Filter><ogc:FeatureId fid="'+fid+'"/></ogc:Filter>';
		str += '</wfs:Update>';
	}
	//
	// --------------------------------------- DELETE ------------------------------------------------
	//
	else if (type == "delete") {
		str += '<wfs:Delete typeName="'+ myconf['featuretype_name']+'">';
		for(var j=0; j<myconf['element'].length; j++){
			if(myconf['element'][j]['f_geom'] == 1){
				var el_geom = myconf['element'][j]['element_name'];
			}
		}
		str += '<ogc:Filter><ogc:FeatureId fid="'+fid+'"/></ogc:Filter>';
		str += '</wfs:Delete>';
	}

	str += '</wfs:Transaction>';
	return str;
}
