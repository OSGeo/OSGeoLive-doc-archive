<?php
# $Id: mod_toggleModule.php 2238 2008-03-13 14:24:56Z christoph $
# http://www.mapbender.org/index.php/mod_toggleModule.php
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
	$e = new mb_exception("tooltip.php: Configuration file " . $wfs_conf_filename . " not found.");
}

echo "var tooltipTarget ='".$e_target[0]."';";

include '../include/dyn_js.php';
?>
//tolerance when we ask wfs
var mb_wfs_tolerance = 8;

var mapframeOffset = {
	x : parseInt(document.getElementById(tooltipTarget).style.left),
	y : parseInt(document.getElementById(tooltipTarget).style.top)
};


//initialize Element Vars

//destination frame for the request (creates Popup if empty)
if(typeof(tooltip_destinationFrame)==='undefined')
	var tooltip_destinationFrame = "";
if(typeof(tooltip_timeDelay)==='undefined')
	var tooltip_timeDelay = 1000;
if(typeof(tooltip_styles)==='undefined')
	var tooltip_styles = "";
if(typeof(tooltip_width)==='undefined')
	var tooltip_width = 270;
if(typeof(tooltip_height)==='undefined')
	var tooltip_height = 200;
if(typeof(tooltip_styles_detail)==='undefined')
	var tooltip_styles_detail = "";
if(typeof(tooltip_disableWms)==='undefined')
	var tooltip_disableWms = "0";
if(typeof(tooltip_disableWfs)==='undefined')
	var tooltip_disableWfs = "0";
try{
	var no_result_text = eval(tooltip_noResultArray);
}catch(e){
	var no_result_text = ["Kein Ergebnis.",'<body onLoad="javascript:window.close()">'];
}
var mouseMoves=0;
var tooltipWin=null;
var point;
var tooltipWfsRequestCount = 0;
var tooltipWmsRequestCount = 0;
var numberOfFinishedWfsRequests = 0;
var numberOfFinishedWmsRequests = 0;
var visibleRequest = 0;
var tooltipMsg = {'title':"<?php echo _mb("Information");?>"};

function mod_tooltipInit(){
        var ind = getMapObjIndexByName(tooltipTarget);
	var myMapObj = mb_mapObj[ind];
        var domNode = myMapObj.getDomElement();

        $(domNode).bind("mousemove", function (e) {
            var tooltip_map = getMapObjByName(tooltipTarget);
            point = tooltip_map.getMousePosition(e);
            mod_tooltip_run(point);
        });
        $(domNode).bind("mouseout", function() {
            mouseMoves=0;
        });

		if (tooltipWin) {
			tooltipWin.hide();
                        hideProgressDisplay();
		}
		buildProgressDisplay();

                $(".printPDF-dialog").bind("dialogopen", function () {
                    if (tooltipWin) {
                        tooltipWin.destroy();
                    }
                    hideProgressDisplay();
                });


}

eventInit.register(mod_tooltipInit); //initialize tooltips!

Mapbender.events.afterMapRequest.register(function () {
    var ind = getMapObjIndexByName(tooltipTarget);
	var myMapObj = mb_mapObj[ind];
        var domNode = myMapObj.getDomElement();

        $(domNode).bind("mousemove", function (e) {
            var tooltip_map = getMapObjByName(tooltipTarget);
            point = tooltip_map.getMousePosition(e);
            mod_tooltip_run(point);
        });
        $(domNode).bind("mouseout", function() {
            mouseMoves=0;
        });

		if (tooltipWin) {
			tooltipWin.hide();
                        hideProgressDisplay();
		}
		buildProgressDisplay();

                $(".printPDF-dialog").bind("dialogopen", function () {
                    if (tooltipWin) {
                        tooltipWin.destroy();
                    }
                    hideProgressDisplay();
                });

});

function mod_tooltip_run(point){
        mouseMoves++;
	var currentMouseMoves = mouseMoves;
	setTimeout(function () {
			if(point !== null && mouseMoves == currentMouseMoves) {
				if($("#container_printbox").length > 0) {
                                    return;
                                }
                                else {
                                    fireRequests(point);
                                    eventTooltipWmsRequestsStarted.trigger({clickX:point.x, clickY:point.y});
                                }
			}
		}
		,tooltip_timeDelay
	);
}

// Tooltip mit Zustandsanzeiger (Fortschritt der Abfrage) erzeugen
var progressDisplay = false;

function buildProgressDisplay() {
	var progressDisplayImg = document.createElement('img');

	progressDisplayImg.id               = 'progress_display';
	progressDisplayImg.src              = '../img/progress.gif';
	progressDisplayImg.style.visibility = 'hidden';
	progressDisplayImg.style.position   = 'absolute';
	progressDisplayImg.style.zIndex     = 999999;

	document.getElementById(tooltipTarget + '_maps').appendChild(progressDisplayImg);

	progressDisplay = progressDisplayImg;

	document.onmouseout = hideProgressDisplay;
}

// Tooltip mit Zustandsanzeiger (Fortschritt der Abfrage) anzeigen
function showProgressDisplay(point) {
	if(progressDisplay === false) {
		buildProgressDisplay();
	}

	progressDisplay.style.visibility = 'visible';

	progressDisplay.style.top  = (point.y + 5) + 'px';
	progressDisplay.style.left = (point.x + 5) + 'px';
}

// Tooltip mit Zustandsanzeiger (Fortschritt der Abfrage) verbergen
function hideProgressDisplay() {
	if(progressDisplay) {
		progressDisplay.style.visibility = 'hidden';
	}
}

var exisitingBalloonId = false;

function fireRequests(obj){
	var ind = getMapObjIndexByName(tooltipTarget);
	var point_geom = new Geometry(geomType.point);
	point_geom.addPoint(mapToReal(tooltipTarget,point));
	visibleRequest = 0;

	if(tooltip_disableWms != '1') {
		//FeatureInfo requests
		urls = mb_mapObj[ind].getFeatureInfoRequests(point);
		tooltipWmsRequestCount = urls.length;
                if (urls.length > 0) {
                    showProgressDisplay(point);
                }
		numberOfFinishedWmsRequests	= 0;
		for(var j=0;j < urls.length;j++){
			mb_ajax_post("../extensions/ext_featureInfoTunnel.php", {url:urls[j]},
				checkFeatureInfoResults);
		}
	}

	if(tooltip_disableWfs != '1') {
		//WFS requests
		requests = getWfsRequests(tooltipTarget, point_geom, true);
		tooltipWfsRequestCount = requests.length;
                if (requests.length > 0) {
                    showProgressDisplay(point);
                }
		numberOfFinishedWfsRequests = 0;
		resultGeomArray = new GeometryArray();
		for(var j=0;j< requests.length;j++){
			(function () {
				var currentRequest = requests[j];
				mb_ajax_post("../" + wfsResultModulePath + wfsResultModuleFilename,currentRequest,function(js_code,status){
					var geom = new GeometryArray();
					if (js_code && geom.importGeoJSON(js_code)) {
						if (typeof(currentRequest) === "object" && typeof(currentRequest.js_wfs_conf_id) !== "undefined") {
							for (var i = 0; i < geom.count(); i++) {
								geom.get(i).wfs_conf = parseInt(currentRequest.js_wfs_conf_id);
							}
						}
					}
					checkWfsResultsFinished(geom);
				});
			}());
		}
	}
}

function checkFeatureInfoResults(js_code,status){
	numberOfFinishedWmsRequests++;

        if(isLastResult()){
		eventTooltipWmsRequestsFinished.trigger();
	}

	//check if there are results
	if(js_code == ""){
		if(!isFirstResult())
			displayResultDoc("");
                        hideProgressDisplay();
		return;
	}

	for(var k=0;k < no_result_text.length;k++){
		if(js_code.indexOf(no_result_text[k])!==-1){
			if(!isFirstResult()) {
				displayResultDoc("");
                                hideProgressDisplay();
			}
			return;
		}
	}

        hideProgressDisplay();
	//output code
	displayResultDoc(js_code);
}

eventTooltipWfsRequestsFinished = new MapbenderEvent();

eventTooltipWfsRequestsFinished.register(function(obj) {
	var resultGeomArray = obj.geomArray;
	if(resultGeomArray.count()>0){
		//generate and output result
		if(resultGeomArray.count()>1)
			var html = createSimpleWfsResultHtml(resultGeomArray);
		else
			var html = createDetailedWfsResultHtml(resultGeomArray);
		displayResultDoc(html);
                hideProgressDisplay();
	}
	else if(!isFirstResult())
		displayResultDoc("");
                hideProgressDisplay();
});

function checkWfsResultsFinished(g){
	//check if all wfs requests arrived
	numberOfFinishedWfsRequests++;
	if (typeof(g) == 'object'){
		resultGeomArray.union(g);
	}
	if (numberOfFinishedWfsRequests == tooltipWfsRequestCount) {
		eventTooltipWfsRequestsFinished.trigger({
			"geomArray" : resultGeomArray
		});
	}
}

function isFirstResult(){
	return visibleRequest == 0;
}

function isLastResult(){
	return (numberOfFinishedWfsRequests == tooltipWfsRequestCount && numberOfFinishedWmsRequests == tooltipWmsRequestCount);
}

function displayResultDoc(html){
	if(exisitingBalloonId) {
		$('#' + exisitingBalloonId).hide();
		$('#balloon_' + exisitingBalloonId).remove();
	}

        //test if we have a fixed destination and create popup otherwise
	if(tooltip_destinationFrame=="") {
		return showBalloonFrame(html);
	}

	//put the frame there
	$("#"+tooltip_destinationFrame).each(function(){
	    var oDoc = this.contentWindow || this.contentDocument;
	    if (oDoc.document) {
	        oDoc = oDoc.document;
		}
		if(isFirstResult())
			oDoc.open();
		oDoc.write(html);
		if(isLastResult())
			oDoc.close();
	});
	visibleRequest++;
        hideProgressDisplay();
}

eventTooltipWmsRequestsStarted = new MapbenderEvent();
eventTooltipWmsRequestsStarted.register(function () {
//	new Mb_notice("STARTED " + tooltipTarget);
//	$("body", window.frames[tooltipTarget].document).css({"cursor": "pointer"});
});
eventTooltipWmsRequestsStarted.register(fireRequests);

eventTooltipWmsRequestsFinished = new MapbenderEvent();
eventTooltipWmsRequestsFinished.register(function () {
//	new Mb_notice("FINISHED " + tooltipTarget);
//	$("body", window.frames[tooltipTarget].document).css({"cursor": "default"});
});

function showBalloonFrame(html){
        hideProgressDisplay();

	if(isFirstResult()){
		//calculate Position

		x=point.x+parseInt(document.getElementById(tooltipTarget).style.left, 10);
		y=point.y+parseInt(document.getElementById(tooltipTarget).style.top, 10);

		//hide old Popup
		if(tooltipWin&&tooltipWin.isVisible())
			tooltipWin.destroy();

		//create Popup and append document
		tooltipWin = new mb_popup({html:'<iframe allowTransparency="true" id="tooltipWin" name="tooltipWin" src="about:blank"/>',title:tooltipMsg.title,width:tooltip_width,height:tooltip_height,balloon:true,left:x,top:y});
		//open document
		tooltipWin.open();
	}
	tooltipWin.write(html);

	if(isLastResult()){
		tooltipWin.close();
	}

	//finally display popup
	tooltipWin.show();
	visibleRequest++;

	// destroy the popup if the mouse leaves the popup
	$("#balloon_"+tooltipWin.id).mousemove(function(e){
		var tooltip_map = getMapObjByName(tooltipTarget);
                point = tooltip_map.getMousePosition(e);
		mod_tooltip_run(point);
	}).mouseout(function() {
		//tooltipWin.destroy();
                mouseMoves=0;
	});
}

function getWfsRequests(target, geom, checkscale, filteroption){
	//get all configurations
	wfs_config = get_complete_wfs_conf();
	var ind = getMapObjIndexByName(target);
	var db_wfs_conf_id = [];
	var js_wfs_conf_id = [];

	//search configurations that are selected (and in scale)
	for (var i=0; i < mb_mapObj[ind].wms.length; i++){
		for(var ii=0; ii < mb_mapObj[ind].wms[i].objLayer.length; ii++){
			var o = mb_mapObj[ind].wms[i].objLayer[ii];
			if(o.gui_layer_wfs_featuretype != '' && o.gui_layer_querylayer == '1'){
				if(!checkscale || o.checkScale(mb_mapObj[ind]))
					db_wfs_conf_id[db_wfs_conf_id.length] = o.gui_layer_wfs_featuretype;
			}
		}
	}
	for(var i=0; i < db_wfs_conf_id.length; i++){
		for(var ii=0; ii < wfs_config.length; ii++){
			if(wfs_config[ii]['wfs_conf_id'] == db_wfs_conf_id[i]){
				js_wfs_conf_id[js_wfs_conf_id.length] = ii;
				break;
			}
		}
	}

	//build requests
	var requests = [];

	for(var i=0;i < js_wfs_conf_id.length; i++){
		//build url
		var url = wfs_config[js_wfs_conf_id[i]]['wfs_getfeature'];
		url += mb_getConjunctionCharacter(wfs_config[js_wfs_conf_id[i]]['wfs_getfeature']);
		url += "service=wfs&request=getFeature&version=1.0.0";
		url += "&typename="+ wfs_config[js_wfs_conf_id[i]]['featuretype_name'];
		url += "&filter=";

		//search for geometry column
		var geometryCol;
		for(var j=0; j < wfs_config[js_wfs_conf_id[i]]['element'].length; j++){
			if(wfs_config[js_wfs_conf_id[i]]['element'][j]['f_geom'] == 1){
				geometryCol = wfs_config[js_wfs_conf_id[i]]['element'][j]['element_name'];
			}
		}

		//get filter
		var filter = new WfsFilter();
		filter.addSpatial(geom, geometryCol, filteroption, wfs_config[js_wfs_conf_id[i]]['featuretype_srs'], target);

		requests.push({'url':url,'filter':filter.toString(), 'typename':wfs_config[js_wfs_conf_id[i]]['featuretype_name'],'js_wfs_conf_id':js_wfs_conf_id[i], 'db_wfs_conf_id':db_wfs_conf_id[i]});
	}

	return requests;
}

function createSimpleWfsResultHtml(_geomArray){
	var geometryIndex = 0;
	wfsConf = get_complete_wfs_conf();
	var html = '<html><head><style type="text/css">';
	html += tooltip_styles;
	html += "</style></head><body><table>\n";

	for (var i = 0 ; i < _geomArray.count(); i ++) {
		if (_geomArray.get(i).get(-1).isComplete()) {
			html += "\t<tr class='list_"+(i%2?"uneven":"even")+"'>\n\t\t<td \n";
			html += "\t\t\t onmouseover='parent.setResult(\"over\","+i+");' ";
			html += " onmouseout='parent.setResult(\"out\","+i+")' ";
			html += " onclick='parent.setResult(\"click\","+i+");' ";
			var geomName = getWfsListEntry(_geomArray.get(i));
			html += ">" + geomName +"</td>";
			html += "\t\t</tr>\n";
		}
	}

	html += "</table></body>\n";
	return html;
}

function createDetailedWfsResultHtml(_geomArray){
	var geometryIndex = 0;
	var cnt = 0;
	wfsConf = get_complete_wfs_conf();
	var html = '<html><head><style type="text/css">';
	html += tooltip_styles_detail;
	html += "</style></head><body><table>\n";

	var wfsConfIndex = _geomArray.get(geometryIndex).wfs_conf;
	var currentWfsConf = wfsConf[wfsConfIndex];
	for (var i = 0 ; i <currentWfsConf.element.length; i ++) {
	    if(currentWfsConf.element[i].f_show_detail==1){
	    	if( _geomArray.get(geometryIndex).e.getElementValueByName(currentWfsConf.element[i].element_name)!=false){
				html +="<tr class='list_"+(cnt%2?"uneven":"even")+"'><td>\n";
				html += currentWfsConf.element[i].f_label;
				html +="</td>\n";
				html += "<td>\n";
				var elementVal = _geomArray.get(geometryIndex).e.getElementValueByName(currentWfsConf.element[i].element_name);
				if(currentWfsConf.element[i].f_form_element_html.indexOf("href")!=-1){
					var setUrl = currentWfsConf.element[i].f_form_element_html.replace(/href\s*=\s*['|"]\s*['|"]/, "href='"+elementVal+"' target='_blank'");
					if(setUrl.match(/><\/a>/)){
						var newLink	=	setUrl.replace(/><\/a>/, ">"+elementVal+"</a>");
					}
					else{
						var newLink = setUrl;
					}
					html +=  newLink;
				}
				else{
					html += elementVal;
				}
				html += "</td></tr>\n";
				cnt++;
			}
		}
	}

	html += "</table></body>\n";
	return html;
}


function getWfsListEntry (geom) {
	wfsConfId = geom.wfs_conf;
	wfsConf = window.frames["wfs_conf"].get_wfs_conf();
	if (typeof(wfsConfId) == "number" && wfsConfId >=0 && wfsConfId < wfsConf.length) {
		var resultArray = [];
		for (var i = 0 ; i < wfsConf[wfsConfId]['element'].length ; i++) {
			if (wfsConf[wfsConfId]['element'][i]['f_show'] == 1 && geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']) !=false) {
				var pos = wfsConf[wfsConfId]['element'][i]['f_respos'];
				if (typeof(resultArray[pos]) != "undefined") {
					resultArray[pos] += " " + geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']);
				}
				else {
					resultArray[pos] = geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']);
				}
			}
		}
		var resultName = resultArray.join(" ");
		if (resultName == "") {
			resultName = wfsConf[wfsConfId]['g_label'];
		}
		return resultName;
	}
	else {
		return false;
	}
}

/*
* event -> {over || out || click}
* geom -> commaseparated coordinates x1,y1,x2,y2 ...
*/
function setResult(event, index){
	var currentGeom = resultGeomArray.get(index);
	var resultHighlight = new parent.Highlight(targetArray, "tooltipHighlight", {"position":"absolute", "top":"0px", "left":"0px", "z-index":100}, 2);
	var cw_fillcolor = "#cc33cc";

	if (event == "over") {
		resultHighlight.add(currentGeom, cw_fillcolor);
		resultHighlight.paint();
	}
	else if (event == "out"){
		resultHighlight.del(currentGeom, cw_fillcolor);
		resultHighlight.paint();
	}
	else if (event == "click"){
		resultHighlight.del(currentGeom, cw_fillcolor);
		var bbox = currentGeom.getBBox();
		//parent.mb_calculateExtent(tooltipTarget, bbox[0].x, bbox[0].y, bbox[1].x, bbox[1].y);
		//parent.zoom(tooltipTarget, 'true', 1.0);
		resultHighlight.add(currentGeom, cw_fillcolor);
		resultHighlight.paint();
	}
	return true;
}
