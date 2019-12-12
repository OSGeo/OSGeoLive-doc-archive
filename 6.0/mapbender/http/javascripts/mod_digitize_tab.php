<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__) . "/../php/mb_validateSession.php");

$e_target = $_GET["e_target"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Digitize</title>
<?php
$digitize_conf_filename = "digitize_default.conf";
include '../include/dyn_css.php';
?>
<script type='text/javascript' src='../extensions/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js'></script>
<script type='text/javascript'>
/**
 * Package: digitize
 *
 * Description:
 * Allows the user to digitize polygons, lines and points.
 * 
 * Files:
 *  - http/javascripts/mod_digitize_tab.php
 *  - http/php/mod_digitize_messages.php
 *  - http/css/digitize.css
 *  - conf/digitize_default.conf
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>','digitize',
 * > 2,1,'Digitize tool.','Digitize','iframe',
 * > '../javascripts/mod_digitize_tab.php?sessionID','frameborder = "0" ',
 * > 1,1,1,1,5,'','','iframe','','geometry.js','mapframe1','mapframe1',
 * > 'http://www.mapbender.org/Digitize');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'digitize', 'cssUrl', 
 * > '../css/digitize.css', 'url to the style sheet of the mapframe' ,
 * > 'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'digitize', 
 * > 'digitize_conf_filename', 'digitize_default.conf', '' ,'php_var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'digitize', 
 * > 'text css', 
 * > 'digitizeGeometryList {position:absolute; top:50px; left:0px;} .digitizeGeometryListItem {color:#000000; font-size:10px;} body {font-family: Arial, Helvetica, sans-serif; font-size:12px; color:#ff00ff; background-color:#ffffff; margin-top: 0px; margin-left:0px;} .button {height:18px; width:32px;}', 
 * > 'text css' ,'text/css');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'digitize', 
 * > 'wfsCssUrl', '../css/mapbender.css', 'var' ,'var');
 *
 * Help:
 * http://www.mapbender.org/Digitize
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * cssUrl - path/filename of CSS which specifies the design of digitize 
 * 			objects in the map do not change the default file, create your 
 * 			own css file from the default file
 * text css - CSS text for the geometry list
 * wfsCssUrl - path/filename of CSS which specifies the design of the popup
 * 			which appears when a feature is saved or updated
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

//
// Buttons
//
function addButtonDig(id, isOn, src, titleOff, titleOn, x, y) {
	buttonDig_id.push(id);
	buttonDig_on.push(isOn);
	buttonDig_src.push(src);
	buttonDig_title_off.push(titleOff);
	buttonDig_title_on.push(titleOn);
	buttonDig_x.push(x);
	buttonDig_y.push(y);
}

function htmlspecialchars(p_string) {
	p_string = p_string.replace(/&/g, '&amp;');
	p_string = p_string.replace(/</g, '&lt;');
	p_string = p_string.replace(/>/g, '&gt;');
	p_string = p_string.replace(/"/g, '&quot;');
	//	p_string = p_string.replace(/'/g, '&#039;');
	return p_string;
};
   
//default definition of image directory for digitize buttons, might
//be overwritten with digitize conf data
var buttonDig_imgdir = "../img/button_digitize/";
var buttonDig_id = [];
var buttonDig_on = [];
var buttonDig_src = [];
var buttonDig_title_off = [];
var buttonDig_title_on = [];
var buttonDig_x = [];
var buttonDig_y = [];	

var mapWindow;
var mapDomElement;
var mapType = "";

var DigitizeHistory = function () {
	var historyItemArray = [];
	var currentIndex = 0;
	
	this.addItem = function (obj) {
		if (typeof obj == "object" 
		&& obj.back && typeof obj.back === "function"
		&& obj.forward && typeof obj.forward === "function"
		) {
			for (var i = currentIndex; i < historyItemArray.length; i++) {
				delete historyItemArray[i];
			}
			historyItemArray.length = currentIndex;
			historyItemArray.push({
				back: obj.back,
				forward: obj.forward
			});
			return true;
		}
		return false;
	};
	
	this.back = function () {
		if (currentIndex > 0) {
			currentIndex --;
			historyItemArray[currentIndex].back();
			executeDigitizeSubFunctions();
			return true;
		}
		return false;
	};
	
	this.forward = function () {
		if (currentIndex < historyItemArray.length) {
			historyItemArray[currentIndex].forward();
			currentIndex ++;
			executeDigitizeSubFunctions();
			return true;
		}
		return false;
	};
};

var digitizeHistory = new DigitizeHistory();

var registerAnotherFunction = function () {
	var mapIndex = parent.getMapObjIndexByName(mod_digitize_target);
	mapDomElement = parent.mb_mapObj[mapIndex].getDomElement();
	mapType = mapDomElement.tagName.toUpperCase();
	if (mapType == "DIV") {
		mapWindow = parent.window;
	}
	else if (mapType == "IFRAME") {
		mapWindow = mapDomElement.window;
	}
	else {
		new parent.Mb_warning("Could not set CSS for map in digitizing module.");
	}

	eventCloseGeometry = new parent.Mapbender.Event();
	eventCloseGeometry.register(function (obj) {
		d.close(obj.index);
	});
	if (openMetadataEditorAfterNewGeometryHasBeenCreated) {
		eventCloseGeometry.register(function (obj) {
			if (typeof obj !== "object") {
				return;
			}
			if (typeof obj.index !== "number") {
				return;
			}
			showWfs(obj.index);
		});
	}
	
	/**
	 * Property: events
	 * 
	 * Description:
	 * Your callback functions receive an object with the 
	 * following attributes
	 * - geometryIndex: index of feature in current feature collection
	 * - feature: the <Mapbender.MultiGeometry>
	 */
	parent.Mapbender.modules[mod_digitize_elName].events = {
		/**
		 * Property: events.closeGeometry
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired after a new geometry has been 
		 * digitized. 
		 */
		closeGeometry: eventCloseGeometry,
		/**
		 * Property: events.beforeUpdateOrInsert
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired before a feature is updated
		 * or inserted by WFS-T. 
		 */
		beforeUpdateOrInsert: new parent.Mapbender.Event(),
		/**
		 * Property: events.beforeUpdate
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired before a feature is updated
		 * by WFS-T. 
		 */
		beforeUpdate: new parent.Mapbender.Event(),
		/**
		 * Property: events.beforeInsert
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired before a feature is inserted 
		 * by WFS-T. 
		 */
		beforeInsert: new parent.Mapbender.Event(),
		/**
		 * Property: events.openDialog
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired before the user opens the dialog
		 * for inserting or updating. Returning false in your callback prevents
		 * the default dialog from popping up.
		 */
		openDialog: new parent.Mapbender.Event(),
		/**
		 * Property: events.clickDelete
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired when the user clicks the button to 
		 * delete features by WFS-T
		 */
		clickDelete: new parent.Mapbender.Event(),
		/**
		 * Property: events.geometryInserted
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired after a feature has been inserted 
		 * by WFS-T
		 */
		geometryInserted: new parent.Mapbender.Event(),
		/**
		 * Property: events.afterWfs
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired after a feature has been inserted,
		 * updated or deleted by WFS-T
		 */
		afterWfs: new parent.Mapbender.Event(),
		/**
		 * Property: events.mergeLines
		 * 
		 * Description:
		 * This <Mapbender.Event> is fired when 2 lines are merged to a single line,
		 */
		 mergeLines: new parent.Mapbender.Event()
	};

	eventCloseGeometry.register(function () {
		_currentGeomIndex = -1;
	});

	parent.Mapbender.modules[mod_digitize_elName].cancelAjaxRequest = false;
	parent.Mapbender.modules[mod_digitize_elName].cancelAjaxRequestMessage = "An error occured.";

	parent.Mapbender.modules[mod_digitize_elName].dataCheck = false;
};


<?php
echo "var mod_digitize_target = '".$e_target."';";
$digitizeConfFilenameAndPath = dirname(__FILE__) . "/../../conf/" . $digitize_conf_filename;
if ($digitize_conf_filename && file_exists($digitizeConfFilenameAndPath)) {
	/*
	 * @security_patch finc done
	 */
	include(secure($digitizeConfFilenameAndPath));
}
?>
if (typeof snapping === "undefined") {
	snapping = true;
}

var wfsWindow;	
var wfsConf = [];
var d;
var mod_digitize_width;
var mod_digitize_height;
var mod_digitizeEvent = false;
var nonTransactionalHighlight;

var button_point;
var button_line;
var button_polygon;
var button_line_continue = "lineContinue";
var button_move = "dragBasePoint";
var button_insert = "setBasePoint";
var button_delete = "delBasePoint";
var button_clear = "clear";
var button_split = "digitizeSplit";
var button_merge = "digitizeMerge";
var button_difference = "digitizeDifference";
var button_line_merge = "mergeLine";
var _currentGeomIndex = -1;

var digitizeDivTag;

var GeometryArray;
var MultiGeometry = parent.MultiGeometry;
var Geometry;
var Point;
var geomType;

var msgObj;
var featureTypeElementFormId = "featureTypeElementForm";

try {if(mod_digitize_elName){}}catch(e) {mod_digitize_elName = "digitize";}
try {if(nonTransactionalEditable){}}catch(e) {nonTransactionalEditable = false;}
try {if(updatePointGeometriesInstantly){}}catch(e) {updatePointGeometriesInstantly = false;}
try {if(addCloneGeometryButton){}}catch(e) {addCloneGeometryButton = false;}

if (typeof featuresMustHaveUniqueId === "undefined") {
	var featuresMustHaveUniqueId = false;
}

if (typeof allowUndoPolygonBySnapping === "undefined") {
	var allowUndoPolygonBySnapping = false;
}

if (typeof openMetadataEditorAfterNewGeometryHasBeenCreated === "undefined") {
	var openMetadataEditorAfterNewGeometryHasBeenCreated = false;
}


var eventCloseGeometry;

function toggleTabs(tabId) {
	if(!initialTab) {
		return;
	}

	var tabHeaders = wfsWindow.document.getElementsByTagName('a');
	var tabs       = wfsWindow.document.getElementsByTagName('div');
	
	for(var i = 0; i < tabHeaders.length; i++) {
		if(tabHeaders[i].id.indexOf('tabheader') != -1) {
			tabHeaders[i].className = 'tabheader';
		}
	}
	
	for(var i = 0; i < tabs.length; i++) {
		if(tabs[i].className === 'tabcontent') {
			tabs[i].style.visibility = 'hidden';
			tabs[i].style.display    = 'none';
		}
	}
	
	wfsWindow.document.getElementById('tabheader_' + tabId).className += ' active';
	
	wfsWindow.document.getElementById('tab_' + tabId).style.visibility = 'visible';
	wfsWindow.document.getElementById('tab_' + tabId).style.display    = 'block';
	
	return false;
}

function showHelptext(helptextId) {
	hideHelptext();
	wfsWindow.document.getElementById('helptext' + helptextId).style.visibility = 'visible';
	wfsWindow.document.getElementById('helptext' + helptextId).style.display    = 'block';

	return false;
}

function hideHelptext(helptextId) {
	if(helptextId) {
		wfsWindow.document.getElementById('helptext' + helptextId).style.visibility = 'hidden';
		wfsWindow.document.getElementById('helptext' + helptextId).style.display    = 'none';
	}

	var helptext = wfsWindow.document.getElementsByTagName('div');
	
	for(var i = 0; i < helptext.length; i++) {
		if(helptext[i].className === 'helptext') {
			helptext[i].style.visibility = 'hidden';
			helptext[i].style.display    = 'none';
		}
	}

	return false;
}
function getMousePosition(e) {
	var map = parent.getMapObjByName(mod_digitize_target);
	var pos =  map.getMousePosition(e);

	// http://bugs.jquery.com/ticket/7319
	if( $.browser.msie ) {
		var doc = parent.$(e.target.ownerDocument);
		pos.x = pos.x + doc.scrollLeft();
		pos.y = pos.y + doc.scrollTop();
	}

	return pos;
}


function initializeDigitize () {
	d = new parent.GeometryArray();
	GeometryArray = parent.GeometryArray;
	Geometry = parent.Geometry;
	Point = parent.Point;
	geomType = parent.geomType;
	button_point = parent.geomType.point; //"Point";
	button_line = parent.geomType.line; //"Line";
	button_polygon = parent.geomType.polygon; //"Polygon";
}

/**
 * Append geometries from KML when KML has been loaded
 */
function appendGeometryArrayFromKML () {
	try {
		parent.kmlHasLoaded.register(function(properties){
			d = new parent.GeometryArray();
			d.importGeoJSON(properties);		
//			d = parent.geoJsonToGeometryArray(properties);
			executeDigitizeSubFunctions();
		});
	}
	catch (e) {
		var exc = new parent.Mb_warning(e);
	}
}


// ------------------------------------------------------------------------------------------------------------------------
// --- polygon, line, point insertion (begin) ----------------------------------------------------------------------------------------------

function appendGeometryArray(obj) {
	executeDigitizePreFunctions();
	d.union(obj, featuresMustHaveUniqueId);
	executeDigitizeSubFunctions();
}

function mod_digitize_go(e){
	// track mouse position
	var currentPos = getMousePosition(e);
	s.check(currentPos);
}

function mod_digitize_timeout(){
	var el = mapDomElement;
	$(el).unbind("mousedown")
		.unbind("mouseup")
		.unbind("mousemove");
}

var isLastLinePointSnapped = function (newPoint) {
	return d.get(_currentGeomIndex).geomType === parent.geomType.line 
		&& d.getGeometry(_currentGeomIndex,-1).count() > 1 
		&& d.getGeometry(_currentGeomIndex,-1).get(-1).equals(newPoint);
};

var innerPointSnapped = function (newPoint) {
	if (d.count() === 0) {
		return null;
	}
	var start = 1;
	if (d.getGeometry(_currentGeomIndex, -1).count() < 3) {
		start = 0;
	}
	for (var i = start; i < d.getGeometry(_currentGeomIndex, -1).count(); i++) {
		if (d.getGeometry(_currentGeomIndex,-1).get(i).equals(newPoint)) {
			return i;
		}
	}
	return null;
};

var isFirstPolygonPointSnapped = function (newPoint) {
	return d.get(_currentGeomIndex).geomType == parent.geomType.polygon 
		&& d.getGeometry(_currentGeomIndex,-1).count() >= 3 
		&& d.getGeometry(_currentGeomIndex,-1).get(0).equals(newPoint);
};

var editingPolygonAndThreePointsHaveBeenInserted = function () {
	return d.get(_currentGeomIndex).geomType == parent.geomType.polygon && d.getGeometry(_currentGeomIndex,-1).count() == 2;
};

var editingLineAndTwoPointsHaveBeenInserted = function () {
	return d.get(_currentGeomIndex).geomType == parent.geomType.line && d.getGeometry(_currentGeomIndex,-1).count() >= 1;				
};

function mod_digitize_start(e){
	if (mod_digitizeEvent !== button_point
		&& mod_digitizeEvent !== button_line
		&& mod_digitizeEvent !== button_line_continue
		&& mod_digitizeEvent !== button_polygon) {

		alert(msgObj.errorMessageNoGeometrySelected);
		return false;
	}
	
	(function () {
		//
		// get the last point the user digitized
		//
		var realWorldPos;
		var isSnapped = s.isSnapped();
		if (isSnapped) {
			realWorldPos = s.getSnappedPoint(); 
			s.clean();
		}
		else {
			var currentPos = getMousePosition(e);
			realWorldPos = parent.mapToReal(mod_digitize_target,currentPos);
		}

		var geometryType = mod_digitizeEvent;
		var currentPoint = realWorldPos;
		var currentEpsg = parent.mb_mapObj[parent.getMapObjIndexByName(mod_digitize_target)].epsg;
		var currentGeomIndex = _currentGeomIndex;

		if (mod_digitizeEvent === button_line_continue) {
			if (isSnapped) {
				// find corresponding line
				for (var i = 0; i < d.count(); i++) {
					var lastPointSnapped = false;
					var firstPointSnapped = false;
					if (d.get(i).geomType !== parent.geomType.line) {
						continue;
					}
					if (d.getPoint(i, -1, -1) === realWorldPos) {
						lastPointSnapped = true;
					}
					else if (d.getPoint(i, -1, 0) === realWorldPos) {
						firstPointSnapped = true;
					}
					else {
						continue;
					}
					if (firstPointSnapped) {
						// reverse line!
						// we can only add points to the end of the line, not insert them at the beginning
						var oldLine = parent.Mapbender.cloneObject(d.getGeometry(i, -1));
						var newLine = d.getGeometry(i, -1);
						var len = oldLine.count();
						for (var j = len-1; j >= 0; j--) {
							newLine.updatePointAtIndex(oldLine.get(j), len-j-1);
						}
					}
					// enable snapping to all points except 
					// the ones from this line
					s.resetPoints();
					if (snapping) {
						s.store(d);
						for (var j = 0; j < d.get(i).count(); j++) {
							var currentLine = d.getGeometry(i, j);
							for (var k = 0; k < currentLine.count(); k++) {
								s.removePoint(currentLine.get(k));
							}
						}
					}


					// delete last point, will be added again below, 
					// as the event has changed to "button_line"
					d.getGeometry(i, -1).del(-1);
					d.getGeometry(i, -1).reopen();
					currentGeomIndex = i;
					_currentGeomIndex = i;
					
					parent.mb_enableButton(d.get(i).geomType);
					break;
				}
			}
			// check if event is still "line continue", 
			// as it might have changed
			if (mod_digitizeEvent === button_line_continue) {
				return;
			}
		}
		//
		// A new geometry has to be created
		//
		if (d.count() === 0 || (d.get(currentGeomIndex).count()> 0 && d.getGeometry(currentGeomIndex, -1).isComplete())) {
			digitizeHistory.addItem({
				// remove the entire multigeometry
				back: function () {
					s.resetPoints();
					d.del(currentGeomIndex);
					if (snapping) {
						s.store(d);
					}
				},
				// add the multigeometry to the geometry array
				forward: function () {
					if (snapping) {
						s.store(d);
					}
					parent.mb_enableButton(geometryType);
					d.addMember(geometryType);
					d.get(currentGeomIndex).addGeometry();
					d.getGeometry(currentGeomIndex,-1).setEpsg(currentEpsg);
					d.getGeometry(currentGeomIndex,-1).addPoint(realWorldPos);
					if (geometryType == parent.geomType.point){
						eventCloseGeometry.trigger({
							index: currentGeomIndex,
							geometry: d.get(currentGeomIndex)
						});
						parent.mb_disableThisButton(mod_digitizeEvent);
					}
				}
			});
		}
		//
		// a point is added to an existing multigeometry
		//
		else {
			var innerPointIndex = innerPointSnapped(realWorldPos);
			//
			// editing polygon and first point is snapped -> close polygon
			//
			if (isFirstPolygonPointSnapped(realWorldPos)) {
				digitizeHistory.addItem({
					back: function () {
						d.getGeometry(currentGeomIndex, -1).reopen();
						parent.mb_enableButton(d.get(currentGeomIndex).geomType);
						// activate button
					},
					forward: function () {
						// close the polygon
						eventCloseGeometry.trigger({
							index: currentGeomIndex,
							geometry: d.get(currentGeomIndex)
						});
						parent.mb_disableThisButton(mod_digitizeEvent);
					}
				});
			}
			//
			// editing line and last point is snapped -> close line
			//
			else if (isLastLinePointSnapped(realWorldPos)) {
				digitizeHistory.addItem({
					back: function () {
						d.getGeometry(currentGeomIndex, -1).reopen();
						parent.mb_enableButton(d.get(currentGeomIndex).geomType);
					},
					forward: function () {
						// close the polygon
						eventCloseGeometry.trigger({
							index: currentGeomIndex,
							geometry: d.get(currentGeomIndex)
						});
						parent.mb_disableThisButton(mod_digitizeEvent);
					}
				});
			}
			//
			// another point is snapped (undo for polygons!)
			//
			else if (innerPointIndex !== null && allowUndoPolygonBySnapping && geometryType == parent.geomType.polygon) {
				while (d.getGeometry(currentGeomIndex, -1).count() > innerPointIndex) {
					digitizeHistory.back();
				}
				// avoids the history.forward()!
				return;
			}
			//
			// just add the point
			//
			else {
				if (editingPolygonAndThreePointsHaveBeenInserted()) {
					digitizeHistory.addItem({
						back: function () {
							s.removePoint(d.getPoint(currentGeomIndex, -1, 0));
							if (allowUndoPolygonBySnapping && geometryType == parent.geomType.polygon) {
								s.removePoint(d.getPoint(currentGeomIndex, -1, -1));
							}
							d.getGeometry(currentGeomIndex, -1).del(-1);
						},
						forward: function () {
							d.getGeometry(currentGeomIndex,-1).addPoint(realWorldPos);
							s.add(d.getPoint(currentGeomIndex, -1, 0));
							if (allowUndoPolygonBySnapping && geometryType == parent.geomType.polygon) {
								s.add(d.getPoint(currentGeomIndex, -1, -1));
							}
						}
					});
				}
				else if (editingLineAndTwoPointsHaveBeenInserted()) {
					digitizeHistory.addItem({
						back: function () {
							s.removePoint(d.getPoint(currentGeomIndex, -1, -1));
							s.add(d.getPoint(currentGeomIndex, -1, -2));
							d.getGeometry(currentGeomIndex, -1).del(-1);
						},
						forward: function () {
							d.getGeometry(currentGeomIndex,-1).addPoint(realWorldPos);
							s.removePoint(d.getPoint(currentGeomIndex, -1, -2));
							s.add(d.getPoint(currentGeomIndex, -1, -1));
						}
					});
				}
				else {
					digitizeHistory.addItem({
						back: function () {
							if (allowUndoPolygonBySnapping && geometryType == parent.geomType.polygon) {
								s.removePoint(d.getPoint(currentGeomIndex, -1, -1));
							}
							if (geometryType === parent.geomType.line) {
								d.getGeometry(currentGeomIndex, -1).del(-1);
							}
							else {
								d.getGeometry(currentGeomIndex, -1).del(-1);
							}
						},
						forward: function () {
							d.getGeometry(currentGeomIndex, -1).addPoint(realWorldPos);
							if (allowUndoPolygonBySnapping && geometryType == parent.geomType.polygon) {
								s.add(d.getPoint(currentGeomIndex, -1, -1));
							}
						}
					});
				}
			}
		}
		digitizeHistory.forward();
	})();

	return true;
}
// --- polygon, line, point insertion (begin) ----------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------------------------------
// --- basepoint handling (begin) -----------------------------------------------------------------------------------------

var basepointObject = false;
var basepointMemberIndex = null;
var basepointGeometryIndex = null;
var basepointRingIndex = null;
var basepointPointIndex = null;
var basepointDragActive = false;

function handleBasepoint(obj,memberIndex, geometryIndex, ringIndex, pointIndex){

	if (!(
		mod_digitizeEvent == button_move || 
		mod_digitizeEvent == button_insert || 
		mod_digitizeEvent == button_delete)
	) { 
		return false; 
	}
	
	basepointObject = obj;
	basepointMemberIndex = memberIndex;
	basepointGeometryIndex = geometryIndex;

	if (pointIndex == undefined) {
		pointIndex = ringIndex;
		basepointRingIndex = undefined;
	}
	else {
		basepointRingIndex = ringIndex;
	}
	basepointPointIndex = pointIndex;
	
    // The eventhandlers get unbound before being bound again to ensure
    // they are only called once - see http://trac.osgeo.org/mapbender/ticket/879 
	if(mod_digitizeEvent == button_move){
		mod_digitize_timeout();
		basepointObject.style.cursor = 'move';
		parent.$(basepointObject).unbind("mousedown", parent.frames[mod_digitize_elName].selectBasepoint);
		parent.$(basepointObject).bind("mousedown", parent.frames[mod_digitize_elName].selectBasepoint);
	}

	if(mod_digitizeEvent == button_delete){
		mod_digitize_timeout();
		basepointObject.style.cursor = 'crosshair';
		parent.$(basepointObject).unbind("mousedown", parent.frames[mod_digitize_elName].deleteBasepoint);
		parent.$(basepointObject).bind("mousedown", parent.frames[mod_digitize_elName].deleteBasepoint);
	}
}

function convertLinepointToBasepoint(obj, memberIndex, geomIndex, ringIndex, pointIndex){
	if(!(mod_digitizeEvent == button_insert)){ return false; }
	
	if(mod_digitizeEvent == button_insert){
		mod_digitize_timeout();
		obj.style.cursor = 'crosshair';
		$(obj).unbind("click").click(function (e){
			insertBasepoint(e);
			return false;
		});

		basepointObject = obj;
		basepointMemberIndex = memberIndex;
		basepointGeometryIndex = geomIndex;
		basepointRingIndex = ringIndex;
		basepointPointIndex = pointIndex;
	}
}

function insertBasepoint(e){
	var i = basepointMemberIndex;
	var j = basepointGeometryIndex;
	var k = basepointRingIndex;
	var l = basepointPointIndex;
	
	var currentPos = getMousePosition(e);

	var ind = parent.getMapObjIndexByName(mod_digitize_target);
	var p = parent.mb_mapObj[ind].convertPixelToReal(new Point(currentPos.x, currentPos.y));

	if (k == undefined) {
		d.getGeometry(i,j).addPointAtIndex(p, l);
	}
	else {
		d.getGeometry(i,j).innerRings.get(k).addPointAtIndex(p, l);
	}

	executeDigitizeSubFunctions();
}

function deleteBasepoint(){
	var i = basepointMemberIndex;
	var j = basepointGeometryIndex;
	var k = basepointRingIndex;
	var l = basepointPointIndex;

	if (k != undefined) {
		d.delAllPointsLike(d.getPoint(i, j, k, l));
	}
	else {
		d.delAllPointsLike(d.getPoint(i, j, l));
	}

	executeDigitizeSubFunctions();
}

function selectBasepoint(e){
	if(!basepointDragActive && mod_digitizeEvent == button_move){
		basepointDragActive = true;
		if (snapping) {
			s.store(d, d.getPoint(basepointMemberIndex, basepointGeometryIndex, basepointPointIndex));
		}
		// replace basepoint by transparent blob
		basepointObject.style.width = mod_digitize_width + "px";
		basepointObject.style.height = mod_digitize_height + "px";
		basepointObject.style.left = "0px";
		basepointObject.style.top = "0px";
			
		if (parent.ie) {
			// ie cannot handle backgroundColor = 'transparent'
			basepointObject.style.background = "url(../img/transparent.gif)";
		}
		else{
			basepointObject.style.backgroundColor = 'transparent';
		}

		parent.$(basepointObject).bind("mouseup", releaseBasepoint);
		parent.$(basepointObject).bind("mousemove", dragBasepoint);
	}
}

function dragBasepoint(e){
	if(basepointDragActive){
		var currentPos = getMousePosition(e);
		var res = s.check(currentPos);

	}
}
	
function updateAllPointsOfNonTransactionalLike(oldP, newP){ 
	for (var i = 0; i < d.count(); i++) {
		if (isTransactional(d.get(i))) {
			d.get(i).updateAllPointsLike(oldP, newP);
		}
	}
}

	
function releaseBasepoint(e){
	
	var i = basepointMemberIndex;
	var j = basepointGeometryIndex;
	var k = basepointRingIndex;
	var l = basepointPointIndex;
	basepointDragActive = false;
	
	var currentPos = getMousePosition(e);
	var basepointDragEnd = currentPos;
	parent.$(basepointObject).unbind("mousedown");
	var ind = parent.getMapObjIndexByName(mod_digitize_target);
	var p = parent.mb_mapObj[ind].convertPixelToReal(new Point(basepointDragEnd.x, basepointDragEnd.y));

	var oldPoint;
	if (k == undefined) {
		oldPoint = parent.Mapbender.cloneObject(d.getPoint(i,j,l));
	} 
	else {
		oldPoint = parent.Mapbender.cloneObject(d.getPoint(i,j,k,l));
	}
	if (s.isSnapped()) {
		var snappedPoint = parent.Mapbender.cloneObject(s.getSnappedPoint());
		if (!nonTransactionalEditable) {
			updateAllPointsOfNonTransactionalLike(oldPoint, snappedPoint);
		}
		else {
			d.updateAllPointsLike(oldPoint, snappedPoint);
		}
		s.clean();
	}
	else {
		if (!nonTransactionalEditable) {
			updateAllPointsOfNonTransactionalLike(oldPoint, p);
		}
		else {
			d.updateAllPointsLike(oldPoint, p);
		}
	}
	basepointMemberIndex = null;
	basepointGeometryIndex = null;
	basepointPointIndex = null;	
		
	executeDigitizeSubFunctions();

	var isPoint = d.get(i).geomType === parent.geomType.point;
	var hasFid = d.get(i).e.getElementValueByName("fid") !== false;
	if (mod_digitizeEvent == button_move && 
		updatePointGeometriesInstantly && 
		isPoint && 
		hasFid) {

		dbGeom("update", i, function () {
			d.del(i);
		});
	}		
}
// --- basepoint handling (end) -----------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------
	
// ------------------------------------------------------------------------------------------------------------------------
// --- registered functions (begin) ---------------------------------------------------------------------------------------

function registerDigitizePreFunctions(stringFunction){
	mod_digitizePreFunctions[mod_digitizePreFunctions.length] = stringFunction;
}

function registerDigitizeSubFunctions(stringFunction){
	mod_digitizeSubFunctions[mod_digitizeSubFunctions.length] = stringFunction;
}

function executeDigitizeSubFunctions(){
	for(var i=0; i<mod_digitizeSubFunctions.length; i++){
		eval(mod_digitizeSubFunctions[i]);
	}
}

function executeDigitizePreFunctions(){
	for(var i=0; i<mod_digitizePreFunctions.length; i++){
		eval(mod_digitizePreFunctions[i]);
	}
}

function completeInitialization() {
	registerAnotherFunction();
	initializeDigitize();
	setStyleForTargetFrame();
	checkDigitizeTag();
	initialiseSnapping();
//		appendGeometryArrayFromKML();
	if (!nonTransactionalEditable) {
		initialiseHighlight();
	}
	initialiseMeasure();
	getMessages();
}

function registerFunctions(){
	
	mod_digitizePreFunctions = [];
	mod_digitizeSubFunctions = [];
	registerDigitizePreFunctions("updateExtent()");
	registerDigitizePreFunctions("drawDashedLine()");
	registerDigitizeSubFunctions("updateListOfGeometries()");
	registerDigitizeSubFunctions("drawDashedLine()");

	if (parent.Mapbender.events.init.done) {
		completeInitialization();
	}
	else {
		parent.Mapbender.events.init.register(completeInitialization);
	}
	parent.eventLocalize.register(function() {
		getMessages();
	});

	parent.eventAfterMapRequest.register(function () {
		updateExtent();
	});
	parent.mb_registerWfsWriteSubFunctions(function(){parent.zoom(mod_digitize_target, true, 0.999);});
}

function checkDigitizeTag(){
	var digitizeTagName = "digitizeDiv";
	var digitizeTagStyle;
	

	if (mapType == "DIV") {
		
		digitizeTagStyle = {"z-index":digitizeTransactionalZIndex, "font-size":"10px"};
		digitizeDivTag = new parent.DivTag(digitizeTagName, "", digitizeTagStyle, mapDomElement);
	}	 
	else {
		digitizeTagStyle = {"position":"absolute", "top":"0px", "left":"0px", "z-index":digitizeTransactionalZIndex, "font-size":"10px"};
		digitizeDivTag = new parent.DivTag(digitizeTagName, mod_digitize_target, digitizeTagStyle);
	}
	parent.mb_registerPanSubElement(digitizeTagName);
	parent.mb_registerSubFunctions("window.frames['"+ mod_digitize_elName + "'].drawDashedLine()");
}


function setStyleForTargetFrame(){
	var cssLink = mapWindow.document.createElement("link");
	var cssHead = mapWindow.document.getElementsByTagName("head")[0]; 
	cssLink.setAttribute("href", cssUrl); 
	cssLink.setAttribute("type", "text/css"); 
	cssLink.setAttribute("rel", "stylesheet"); 
	cssHead.appendChild(cssLink);
}

function initialiseSnapping(){
	s = new parent.Snapping(mod_digitize_target, snappingTolerance, snappingColor, snappingHighlightZIndex);
}
function initialiseHighlight(){
	nonTransactionalHighlight = new parent.Highlight([mod_digitize_target], "nonTransactional", {"position":"absolute", "top":"0px", "left":"0px", "z-index":digitizeNonTransactionalZIndex}, nonTransactionalLineWidth);
}
function initialiseMeasure(){
	if (mapType == "DIV") {
		measureDivTag = new parent.DivTag(measureTagName, "", measureTagStyle);
	}
	else {
		measureDivTag = new parent.DivTag(measureTagName, measureTagTarget, measureTagStyle);
	}
	parent.mb_registerSubFunctions("window.frames['"+ mod_digitize_elName + "'].updateMeasureTag()");
}
// --- registered functions (end) -----------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------


function updateMeasureTag () {
	if (d.count() > 0 ) {
		if (d.get(_currentGeomIndex).count() > 0) {
			if (d.getGeometry(_currentGeomIndex, -1).count() > 0) {
				if (mod_digitizeEvent == button_line || mod_digitizeEvent == button_polygon) {
					var measureString = "";
					measureString += msgObj.measureTagLabelCurrent + d.getGeometry(_currentGeomIndex, -1).getCurrentDist(measureNumberOfDigits) + "<br>";
					measureString += msgObj.measureTagLabelTotal + d.getGeometry(_currentGeomIndex, -1).getTotalDist(measureNumberOfDigits);
					measureDivTag.write(measureString);
					return true;
				}
			}
		}
	}
	measureDivTag.clean();
}



// ------------------------------------------------------------------------------------------------------------------------
// --- button handling (begin) --------------------------------------------------------------------------------------------

function displayButtons(){
	for (var i = 0 ; i < buttonDig_id.length ; i ++) {
		if (parseInt(buttonDig_on[i])==1) {
			var divTag = document.createElement("div");
			divTag.setAttribute("id", "div_" + buttonDig_id[i]);
// FIREFOX 
			document.getElementById("digButtons").appendChild(divTag);

//IE WORKAROUND, WORKS ALSO FOR FIREFOX
			var tagContent = "<div style='position:absolute; top:"+buttonDig_y[i]+"px; left:"+buttonDig_x[i]+"px;'><img name=\""+buttonDig_id[i]+"\" onmouseover=\"parent.mb_regButton_frame('initDigButton', mod_digitize_elName, "+i+");\" id=\""+buttonDig_id[i]+"\" title=\""+buttonDig_title_off[i]+"\" src=\""+buttonDig_imgdir+buttonDig_src[i]+"\"></div>";
			parent.writeTag(mod_digitize_elName,"div_" + buttonDig_id[i],tagContent);
		}
	}
}

function updateButtons() {
	for (var i = 0 ; i < buttonDig_id.length ; i ++) {
		if (parseInt(buttonDig_on[i])==1) {
			var currentButton = document.getElementById(buttonDig_id[i]);
			var currentStatus = buttonDig_id[i].status;
			var currentTitle = "";
			switch (buttonDig_id[i]) {
				case "point":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelPointOn;
					}
					else {
						currentTitle = msgObj.buttonLabelPointOff;
					}
					break;
				case "line":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelLineOn;
					}
					else {
						currentTitle = msgObj.buttonLabelLineOff;
					}
					break;
				case "lineContinue":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelLineContinueOn;
					}
					else {
						currentTitle = msgObj.buttonLabelLineContinueOff;
					}
					break;
				case "polygon":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelPolygonOn;
					}
					else {
						currentTitle = msgObj.buttonLabelPolygonOff;
					}
					break;
				case "dragBasePoint":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelMoveBasepointOn;
					}
					else {
						currentTitle = msgObj.buttonLabelMoveBasepointOff;
					}
					break;
				case "setBasePoint":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelInsertBasepointOn;
					}
					else {
						currentTitle = msgObj.buttonLabelInsertBasepointOff;
					}
					break;
				case "delBasePoint":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelDeleteBasepointOn;
					}
					else {
						currentTitle = msgObj.buttonLabelDeleteBasepointOff;
					}
					break;
				case "clear":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelClearListOn;
					}
					else {
						currentTitle = msgObj.buttonLabelClearListOff;
					}
					break;
				case "digitizeSplit":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelSplitOn;
					}
					else {
						currentTitle = msgObj.buttonLabelSplitOff;
					}
					break;
				case "digitizeMerge":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelMergeOn;
					}
					else {
						currentTitle = msgObj.buttonLabelMergeOff;
					}
					break;
				case "digitizeDifference":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelDifferenceOn;
					}
					else {
						currentTitle = msgObj.buttonLabelDifferenceOff;
					}
					break;
				case "mergeLine":
					if (currentStatus == 1) {
						currentTitle = msgObj.buttonLabelMergeLineOn;
					}
					else {
						currentTitle = msgObj.buttonLabelMergeLineOff;
					}
					break;	
			}
			currentButton.title = currentTitle;
		}
	}
}

function initDigButton(ind, pos){
	parent.mb_button[ind] = document.getElementById(buttonDig_id[pos]);
	parent.mb_button[ind].img_over = buttonDig_imgdir + buttonDig_src[pos].replace(/_off/,"_over");
	parent.mb_button[ind].img_on = buttonDig_imgdir + buttonDig_src[pos].replace(/_off/,"_on");
	parent.mb_button[ind].img_off = buttonDig_imgdir + buttonDig_src[pos];
	parent.mb_button[ind].title_on = buttonDig_title_on[pos];
	parent.mb_button[ind].title_off = buttonDig_title_off[pos];
	parent.mb_button[ind].status = 0;
	parent.mb_button[ind].elName = buttonDig_id[pos];
	parent.mb_button[ind].fName = "";
	parent.mb_button[ind].go = new Function ("digitizeEnable(parent.mb_button["+ind+"])");
	parent.mb_button[ind].stop = new Function ("digitizeDisable(parent.mb_button["+ind+"])");
}	

function digitizeEnable(obj) {
	if (obj.id == button_move || obj.id == button_insert || obj.id == button_delete) {
		mod_digitizeEvent = obj.id;
		executeDigitizePreFunctions();
	}
	else if (obj.id == button_point || obj.id == button_line || 
			obj.id == button_polygon || obj.id == button_clear || 
			obj.id == button_split || obj.id == button_merge ||
			obj.id == button_difference || obj.id == button_line_continue ||
			obj.id == button_line_merge){
					
		var el = mapDomElement;
		$(el).mousemove(function (e) {
			mod_digitize_go(e);
		}).mousedown(function (e) {
			mod_digitize_start(e);
		});


		mod_digitizeEvent = obj.id;

		//get a first snapping point for digitizing
		if (mod_digitizeEvent == button_point || mod_digitizeEvent == button_line || mod_digitizeEvent == button_polygon || mod_digitizeEvent == button_insert ) {
			s.resetPoints();
			if (snapping) {
				s.store(d);
			}
		}
		
		if (mod_digitizeEvent == button_point || mod_digitizeEvent == button_line || mod_digitizeEvent == button_polygon || mod_digitizeEvent == button_insert ) {
			//
			// complete a previously unfinished geometry
			//
			(function () {
				var currentGeometryDoesNotCorrespondToTheCurrentButton = 
					(d.count() > 0 && d.get(_currentGeomIndex).count() > 0 &&
					!d.get(_currentGeomIndex).get(-1).isComplete() &&
					mod_digitizeEvent !== d.get(_currentGeomIndex).geomType);
				var currentGeometryType = mod_digitizeEvent;
				
				if (currentGeometryDoesNotCorrespondToTheCurrentButton) {
					var currentEpsg = parent.mb_mapObj[parent.getMapObjIndexByName(mod_digitize_target)].epsg;
					digitizeHistory.addItem({
						back: function(){
							s.resetPoints();
							d.del(_currentGeomIndex);
							if (snapping) {
								s.store(d);
							}
							d.getGeometry(_currentGeomIndex, -1).reopen();
						},
						forward: function(){
							d.close(-1);
							if (snapping) {
								s.store(d);
							}
							d.addMember(currentGeometryType);
							d.get(_currentGeomIndex).addGeometry();
							d.getGeometry(_currentGeomIndex,-1).setEpsg(currentEpsg);
						}
					});
					digitizeHistory.forward();
				}
			})();
	
		}

		executeDigitizePreFunctions();
		if (obj.id == button_polygon) {
			// close previous open polygons
			if (d.count() > 0 && d.get(_currentGeomIndex).count() > 0 && !d.get(_currentGeomIndex).get(-1).isComplete()) {
				if (d.get(_currentGeomIndex).geomType !== parent.geomType.polygon) {
//					d.close();
//					executeDigitizeSubFunctions();
				}
				else {
					s.add(d.getPoint(-1, -1, 0));
				}
			}
		}
		else if (obj.id == button_line) {
			if (d.count() > 0 && d.get(_currentGeomIndex).count() > 0 && !d.get(_currentGeomIndex).get(-1).isComplete()) {
				if (d.get(_currentGeomIndex).geomType != parent.geomType.line) {
//					d.close();
//					executeDigitizeSubFunctions();
				}
				else {
//					s.add(d.getPoint(-1, -1, -1));
				}
			}
		}
		else if (obj.id == button_line_continue) {
			s.resetPoints();
			for (var i = 0; i < d.count(); i++) {
				if (d.get(i).geomType !== parent.geomType.line) {
					continue;
				}
				s.add(d.getPoint(i, -1, 0));
				s.add(d.getPoint(i, -1, -1));
			}
		}
		else if (obj.id == button_clear) {
			var clear = confirm(msgObj.messageConfirmDeleteAllGeomFromList);
			if (clear) {
				d = new parent.GeometryArray();
				s.resetPoints();
				parent.mb_disableThisButton(mod_digitizeEvent);
				digitizeHistory = new DigitizeHistory();
			}
		}
		else if (obj.id == button_merge) {
			var applicable = (d.count() > 1);

			var polygonTextArray = [];
			for (var i = 0; i < d.count(); i++) {
				if (d.get(i).geomType != parent.geomType.polygon) {
					applicable = false;
					polygonTextArray = [];
					break;
				}
				polygonTextArray.push(d.get(i).toText());
			}

			if (!applicable) {
				alert(msgObj.messageErrorMergeNotApplicable);
				parent.mb_disableThisButton(mod_digitizeEvent);
				return false;
			}
			
			parent.mb_ajax_post("../php/mod_digitize_mergePolygon.php", {polygons: polygonTextArray.join(";")}, function(json, status) {
				var response = json;
				var polygon = response.polygon;
				var mapIndex = parent.getMapObjIndexByName(mod_digitize_target);
				d.importGeometryFromText(polygon, parent.mb_mapObj[mapIndex].epsg);

				// remove the original polygons
				var len = d.count();
				for (var i = 0; i < len-1; i++) {
					d.del(0);
				}
				parent.mb_disableThisButton(mod_digitizeEvent);
			});
		}
		else if (obj.id == button_split) {
			var applicable = (d.count() == 2) && 
				(
					d.get(0).geomType == parent.geomType.polygon ||
					d.get(0).geomType == parent.geomType.line
				) && (d.get(1).geomType == parent.geomType.line);
			if (!applicable) {
				alert(msgObj.messageErrorSplitNotApplicable);
				parent.mb_disableThisButton(mod_digitizeEvent);
				return false;
			}
			
			var splitCallback = function (json, status) {
				var response = json;
				var resultArray = response.geometries;
				var wfsConfId = d.get(0).wfs_conf;
				var mapIndex = parent.getMapObjIndexByName(mod_digitize_target);
				for (var i in resultArray) {
					d.importGeometryFromText(resultArray[i], parent.mb_mapObj[mapIndex].epsg);
					d.get(-1).wfs_conf = wfsConfId;
					var wfsProperties = d.get(0).e;

					for (var j = 0; j < wfsProperties.count(); j++) {
						if (i > 0 && wfsProperties.getName(j) === "fid") {
							continue;
						}
						d.get(-1).e.setElement(
							wfsProperties.getName(j), 
							wfsProperties.getValue(j)
						);
					}
				}
				// remove the original geometry and the temporary line
				d.del(0);
				d.del(0);
				parent.mb_disableThisButton(mod_digitizeEvent);
			};

			if (d.get(0).geomType == parent.geomType.polygon) {
				var polygonText = d.get(0).toText();
				var lineText = d.get(1).toText();
				
				parent.mb_ajax_post("../php/mod_digitize_splitPolygon.php", {
					polygon: polygonText, 
					line: lineText
				}, splitCallback);
			}
			else {
				var line1text = d.get(0).toText();
				var line2text = d.get(1).toText();
				
				parent.mb_ajax_post("../php/mod_digitize_splitLine.php", {
					line1: line1text, 
					line2: line2text
				}, splitCallback);
			}
		}
		else if (obj.id == button_difference) {
			var applicable = (d.count() == 2) && 
							(d.get(0).geomType == parent.geomType.polygon) &&
							(d.get(1).geomType == parent.geomType.polygon);
			if (!applicable) {
				alert(msgObj.messageErrorDifferenceNotApplicable);
				parent.mb_disableThisButton(mod_digitizeEvent);
				return false;
			}
			
			var polygon1Text = d.get(0).toText();
			var polygon2Text = d.get(1).toText();
			
			parent.mb_ajax_post("../php/mod_digitize_differencePolygon.php", {polygon1: polygon1Text, polygon2: polygon2Text}, function(json, status) {
				var response = json;
				var polygonArray = response.polygons;
				var wfsConfId = d.get(0).wfs_conf;
				var wfsProperties = d.get(0).e;
				var mapIndex = parent.getMapObjIndexByName(mod_digitize_target);
				for (var i in polygonArray) {
					d.importGeometryFromText(polygonArray[i], parent.mb_mapObj[mapIndex].epsg);
					d.get(-1).wfs_conf = wfsConfId;
					for (var i = 0; i < wfsProperties.count(); i++) {
						if (wfsProperties.getName(i) === "fid") {
							continue;
						}
						d.get(-1).e.setElement(wfsProperties.getName(i), wfsProperties.getValue(i));
					}
				}
				// remove the original and the temporary polygon
				d.del(0);
				d.del(0);
				parent.mb_disableThisButton(mod_digitizeEvent);
			});
		}
		else if (obj.id == button_line_merge) {
			//var applicable = (d.count() > 1);
			
/*			var lineTextArray = [];
			for (var i = 0; i < d.count(); i++) {
				if (d.get(i).geomType != parent.geomType.line) {
					applicable = false;
					lineTextArray = [];
					break;
				}
				lineTextArray.push(d.get(i).toText());
			}
*/
			var applicable = (d.count() == 2) && 
							(d.get(0).geomType == parent.geomType.line) &&
							(d.get(1).geomType == parent.geomType.line);

			if (!applicable) {
				alert(msgObj.messageErrorMergeLineNotApplicable);
				parent.mb_disableThisButton(mod_digitizeEvent);
				return false;
			}

			var line1Text = d.get(0).toText();
			var line2Text = d.get(1).toText();
				
			parent.mb_ajax_post("../php/mod_digitize_mergeLines.php", {line1: line1Text, line2: line2Text}, function(json, status) {
				var response = json;
				var line = response.line;
				if(line == "") {
					alert(msgObj.errorMessageInvalidLineGeometries);
					parent.mb_disableThisButton(mod_digitizeEvent);
					return false;
				}
				var wfsConfId = d.get(-1).wfs_conf;
				var wfsProperties = d.get(-1).e;
				var mapIndex = parent.getMapObjIndexByName(mod_digitize_target);
				
				d.importGeometryFromText(line, parent.mb_mapObj[mapIndex].epsg);
				d.get(-1).wfs_conf = wfsConfId;
				for (var i = 0; i < wfsProperties.count(); i++) {
					d.get(-1).e.setElement(wfsProperties.getName(i), wfsProperties.getValue(i));
				}

				var len = d.count();
				var obsoleteFeatureArray = [];
				wfsConf = parent.get_complete_wfs_conf();
				for (var i = 0; i < len-2; i++) {
					if(typeof wfsConf == 'object' && typeof wfsConf[d.get(i).wfs_conf] == 'object') {	
						var featureCollection = new GeometryArray();
						featureCollection.importGeoJSON(d.get(i).toString());
						obsoleteFeatureArray.push({
							geoJson : featureCollection.toString(),
							wfsConfId : wfsConf[d.get(i).wfs_conf]['wfs_conf_id']    
						});
					}
				}
				var mergedFeatureId = d.get(-1).e.getElementValueByName("fid");

				// remove the original lines
				var len = d.count();
				//for (var i = len-1 ; i >= 1; i--) {
				for (var i = 0; i < len-1; i++) {
					d.del(0);
				}
				parent.mb_disableThisButton(mod_digitizeEvent);
				
				var res = true;	
				res = parent.Mapbender.modules.digitize.events.mergeLines.trigger({
					mergedFeatureId: mergedFeatureId,
					obsoleteFeature: obsoleteFeatureArray
					
				}, "AND");
				
				if (res === false) {
					return;	
				}
				
				if(mergedFeatureId !== false) {
					parent.Mapbender.modules.digitize.events.afterWfs.register(function (obj) {
						for(var j = 0; j < obsoleteFeatureArray.length; j++) {
							if(obj.type == 'update' && obj.feature.e.getElementValueByName("fid") == mergedFeatureId) {
								parent.mb_ajax_post(
									"../extensions/geom2wfst.php", 
									{
										'geoJson' : obsoleteFeatureArray[j].geoJson,
										'method' : "delete",
										'wfs_conf_id' : obsoleteFeatureArray[j].wfsConfId
									}, 
									function(json,status){
										parent.zoom(mod_digitize_target, true, 0.999);				
									}
								);	
							}
						}	
					});
				}
			});
		}	
	}
	else {
		alert("unknown type: " + obj.id);
	}
}

function digitizeDisable(obj) {
	mod_digitize_timeout();
	executeDigitizeSubFunctions();
	mod_digitizeEvent = false;
}
// --- button handling (end) ----------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------------------------------
// --- display (begin) -----------------------------------------------------------------------------------------

function updateExtent() {
	var anInd = parent.getMapObjIndexByName(mod_digitize_target);
	var change = false;
	if (typeof(mod_digitize_width) == 'undefined' || mod_digitize_width != parent.mb_mapObj[anInd].width) {
		mod_digitize_width = parent.mb_mapObj[anInd].width;
		change = true;
	}
	if (typeof(mod_digitize_height) == 'undefined' || mod_digitize_height != parent.mb_mapObj[anInd].height) {
		mod_digitize_height = parent.mb_mapObj[anInd].height;
		change = true;
	}
	if (typeof(mod_digitize_epsg) == 'undefined' || mod_digitize_epsg != parent.mb_mapObj[anInd].epsg) {
		mod_digitize_epsg = parent.mb_mapObj[anInd].epsg;
		change = true;
	}
//	if (change) {
//		drawDashedLine();
//	}
}

function drawDashedLine(){
	if (!nonTransactionalEditable) {
		nonTransactionalHighlight.clean();
	}
	var smP = "";
	smP += "<div class='t_img'>";
	smP += "<img src='"+parent.mb_trans.src+"' width='"+mod_digitize_width+"' height='0'></div>";
	smP += "<div class='t_img'>";
	smP += "<img src='"+parent.mb_trans.src+"' width='0' height='"+mod_digitize_height+"'></div>";

	if (!nonTransactionalEditable) {
		nonTransactionalHighlight.clean();
	}
	var smPArray = [];
	smPArray[smPArray.length] = "<div class='t_img'>"
			+ "<img src='"+parent.mb_trans.src+"' width='"+mod_digitize_width+"' height='0'></div>"
			+ "<div class='t_img'>"
			+ "<img src='"+parent.mb_trans.src+"' width='0' height='"+mod_digitize_height+"'></div>";
	
	var mapObj = parent.mb_mapObj[parent.getMapObjIndexByName(mod_digitize_target)];
	var width = mapObj.width;
	var height = mapObj.height;
	var arrayBBox = mapObj.extent.toString().split(",")
	var minX = parseFloat(arrayBBox[0]);
	var minY = parseFloat(arrayBBox[1]);
	var maxX = parseFloat(arrayBBox[2]);
	var maxY = parseFloat(arrayBBox[3]);
	var cx = width/(maxX - minX);
	var cy = height/(maxY - minY);
	var isMoveOrInsertOrDelete = mod_digitizeEvent == button_move || mod_digitizeEvent == button_insert || mod_digitizeEvent == button_delete;
	var minDist = 6;

	for(var i=0, lenGeomArray = d.count(); i < lenGeomArray; i++){
		var currentGeomArray = d.get(i);

		if (!nonTransactionalEditable && !isTransactional(currentGeomArray)) {
			nonTransactionalHighlight.add(currentGeomArray, nonTransactionalColor);
		}
		else {
			for(var j=0, lenGeom = currentGeomArray.count(); j < lenGeom ; j++){
				var currentGeometry = d.getGeometry(i,j);
				var isPolygon = currentGeomArray.geomType == parent.geomType.polygon;
				var isLine = currentGeomArray.geomType == parent.geomType.line;
				var isComplete = currentGeometry.isComplete();
				var lastPaintedPoint = false;

				for(var k = 0, lenPoint = currentGeometry.count(); k < lenPoint; k++){
					var currentPoint = currentGeometry.get(k);
					var totalDistMeasureTag = "";
					var currentPointMap = new Point(Math.round((currentPoint.x - minX)*cx), Math.round((maxY - currentPoint.y)*cy));
//					var isTooCloseToPrevious = lastPaintedPoint && (k > 0) && Math.abs(currentPointMap.x-lastPaintedPoint.x) <= minDist && Math.abs(currentPointMap.y-lastPaintedPoint.y) <= minDist;
//					if (!isTooCloseToPrevious) {
						var currentPointIsVisible = currentPointMap.x > 0 && currentPointMap.x < width && currentPointMap.y > 0 && currentPointMap.y < height;
						if (currentPointIsVisible) {
							if (!isComplete && ((k == 0 && isPolygon) || (k == lenPoint-1 && isLine))) {
								smPArray[smPArray.length] = "<div class='bp' style='top:"+
									(currentPointMap.y-2)+"px;left:"+(currentPointMap.x-2)+"px;z-index:"+
									parseInt(digitizeTransactionalZIndex+10, 10)+";background-color:"+linepointColor+"'";
								if(measuring) {
									if(isLine && k != 0) {
										totalDistMeasureTag = "<p class='measure'>"+currentGeometry.getAggregatedDist(k-1, 2)+"</p>";
									}
								}	
								
							}
							else {
								smPArray[smPArray.length] = "<div class='bp' style='top:"+(currentPointMap.y-2)+"px;left:"+(currentPointMap.x-2)+"px;z-index:"+parseInt(digitizeTransactionalZIndex+10, 10)+";'";
								if(measuring) {
									if(isLine && k == 0) {
										//nothing
										totalDistMeasureTag = "";
									}
									else {
										totalDistMeasureTag = "<p class='measure'>"+currentGeometry.getAggregatedDist(k-1, 2)+"</p>";
									}
								}
								
							}
							if(k==0 && isPolygon && !isComplete){
								smPArray[smPArray.length] = " title='"+msgObj.closePolygon_title+"' ";
								
							}
							if(isMoveOrInsertOrDelete) {
								smPArray[smPArray.length] = "onmouseover='window.frames[\""+mod_digitize_elName+"\"].handleBasepoint(this,"+i+","+j+","+k+")' ;";
							}
							smPArray[smPArray.length] = ">";
							if (isPolygon || isLine) {
								smPArray[smPArray.length] = totalDistMeasureTag;
							}	
							smPArray[smPArray.length] = "</div>";
							lastPaintedPoint = currentPointMap;
							
						}
						if (k > 0) {
							points = parent.calculateVisibleDash(currentPointMap, previousPointMap, width, height);
							if (points != false) {
								smPArray[smPArray.length] = evaluateDashes(points[0], points[1], i, j, k);
							}
						}
//					}
					var previousPointMap = currentPointMap;
				}
				if (isPolygon && currentGeometry.innerRings.count() > 0) {
					// draw inner rings

					for (var l = 0, lenRings = currentGeometry.innerRings.count(); l < lenRings; l++) {
						var currentRing = currentGeometry.innerRings.get(l);
						var lastPaintedPoint = false;
						
						for (var m = 0, lenPoint = currentRing.count(); m < lenPoint; m++) {
							var currentPoint = currentRing.get(m);
							var currentPointMap = new Point(Math.round((currentPoint.x - minX) * cx), Math.round((maxY - currentPoint.y) * cy));
							
							//					var isTooCloseToPrevious = lastPaintedPoint && (k > 0) && Math.abs(currentPointMap.x-lastPaintedPoint.x) <= minDist && Math.abs(currentPointMap.y-lastPaintedPoint.y) <= minDist;
							//					if (!isTooCloseToPrevious) {
							var currentPointIsVisible = currentPointMap.x > 0 && currentPointMap.x < width && currentPointMap.y > 0 && currentPointMap.y < height;
							if (currentPointIsVisible) {
								if (!isComplete && ((k == 0 && isPolygon) || (k == lenPoint - 1 && isLine))) {
									smPArray[smPArray.length] = "<div class='bp' style='top:" +
									(currentPointMap.y - 2) +
									"px;left:" +
									(currentPointMap.x - 2) +
									"px;z-index:" +
									parseInt(digitizeTransactionalZIndex+10, 10) +
									";background-color:" +
									linepointColor +
									"'";
								}
								else {
									smPArray[smPArray.length] = "<div class='bp' style='top:" + (currentPointMap.y - 2) + "px;left:" + (currentPointMap.x - 2) + "px;z-index:" + parseInt(digitizeTransactionalZIndex+10, 10) + ";'";
								}
								if (m == 0 && isPolygon && !isComplete) {
									smPArray[smPArray.length] = " title='" + msgObj.closePolygon_title + "' ";
								}
								if (isMoveOrInsertOrDelete) {
									smPArray[smPArray.length] = " onmouseover='window.frames[\"" + mod_digitize_elName + "\"].handleBasepoint(this," + i + "," + j + "," + l + "," + m + ")' ;";
								}
								smPArray[smPArray.length] = "></div>";
								lastPaintedPoint = currentPointMap;
							}
							if (m > 0) {
								points = parent.calculateVisibleDash(currentPointMap, previousPointMap, width, height);
								if (points != false) {
									smPArray[smPArray.length] = evaluateDashes(points[0], points[1], i, j, l, m);
								}
							}
							//					}
							var previousPointMap = currentPointMap;
						}
					}
				}
			}
		}
	}
	if (!nonTransactionalEditable) {
		nonTransactionalHighlight.paint();
	}

	digitizeDivTag.write(smPArray.join(""));
}

function evaluateDashes(start, end, memberIndex, geomIndex, ringIndex, pointIndex){
	if (pointIndex == undefined) {
		pointIndex = ringIndex;
		ringIndex = undefined;
	}
	
	var strArray = [];
	var delta = new parent.Point(end.x - start.x, end.y - start.y);
	var lastGeomIsComplete = d.getGeometry(-1,-1).isComplete(); 
	 
	var vecLength = start.dist(end);
	var n = Math.round(vecLength/dotDistance);
	if (n > 0) {
		var step = delta.dividedBy(n);
	}
	var lineCenter = Math.round(n/2);
	
	for(var i=1; i < n; i++){
		var x = Math.round(start.x + i * step.x) - 2;
		var y = Math.round(start.y + i * step.y) - 2;
		if(x >= 0 && x <= mod_digitize_width && y >= 0 && y <= mod_digitize_height){
			if (memberIndex == d.count()-1 && !lastGeomIsComplete) {
				strArray[strArray.length] = "<div class='lp' style='top:"+y+"px;left:"+x+"px;z-index:"+digitizeTransactionalZIndex+";background-color:"+linepointColor+"' ";
			}
			else {
				strArray[strArray.length] = "<div class='lp' style='top:"+y+"px;left:"+x+"px;z-index:"+digitizeTransactionalZIndex+";' onmousedown='return false;' ";
			}
			if(mod_digitizeEvent == button_insert) {
				strArray[strArray.length] = "onmouseover='window.frames[\""+mod_digitize_elName+"\"].convertLinepointToBasepoint(this,"+memberIndex+","+geomIndex+","+ringIndex+","+pointIndex+")'";
			}
			strArray[strArray.length] = ">";
			if(measuring) {
				if(i == lineCenter) {
					strArray[strArray.length] = "<p class='measure'>"+d.getGeometry(memberIndex, geomIndex).getDist(pointIndex-1, 2)+"</p>";
				}
			}
			strArray[strArray.length] = "</div>";
		}
	}
	return strArray.join("");
}

function isTransactional(geom) {
//	alert(typeof(geom.wfs_conf) + " " + geom.wfs_conf + " " + wfsConf.length);
	if (typeof(geom.wfs_conf) == 'number') {
		if (geom.wfs_conf >= 0 && geom.wfs_conf < wfsConf.length) {			
			var isTransactionalGeom = (wfsConf[geom.wfs_conf]['wfs_transaction'] != "" && wfsConf[geom.wfs_conf]['fkey_featuretype_id'] != "");
			if (isTransactionalGeom) {
				return true;
			}
			else{
				return false;
			}
		}
	}
	else if (typeof(geom.wfs_conf) == 'undefined') {
		return true;
	}
}

function isValidWfsConfIndex (wfsConf, wfsConfIndex) {
	return (typeof(wfsConfIndex) == "number" && wfsConfIndex >=0 && wfsConfIndex < wfsConf.length);
}

function getName (geom) {
	wfsConfId = geom.wfs_conf;
	wfsConf = parent.get_complete_wfs_conf();
	if (isValidWfsConfIndex(wfsConf, wfsConfId)) {
		var resultName = "";
		for (var i = 0 ; i < wfsConf[wfsConfId]['element'].length ; i++) {
			if (wfsConf[wfsConfId]['element'][i]['f_show'] == 1) {
				var attrValue = geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']);
				if(attrValue === false) {
					resultName += "";
				}
				else{
					resultName += geom.e.getElementValueByName(wfsConf[wfsConfId]['element'][i]['element_name']) + " ";					
				}	
			}
		}
		if (resultName == "") {
			resultName = wfsConf[wfsConfId]['g_label'];
		}
		return resultName;
	}
	else if (geom.e.getElementValueByName("name")) {
		return geom.e.getElementValueByName("name");
	}
	else {
		return msgObj.digitizeDefaultGeometryName;
	}
}

function updateListOfGeometries(){
	var listOfGeom = "<ul>";
	if (d.count() > 0) {
		wfsConf = parent.get_complete_wfs_conf();
		//for (var i = 0 ; i < d.count(); i ++) {
		for (var i = d.count()-1 ; i >= 0; i--) {
//			if (d.get(i).get(-1).isComplete() && (nonTransactionalEditable || isTransactional(d.get(i)))) {
			if ((nonTransactionalEditable || isTransactional(d.get(i)))) {
	
				// for the geometries from a kml, there is another save dialogue
				if (d.get(i).isFromKml()) {
					// if the kml is in the db (id = id in database)
					if (d.get(i).e.getElementValueByName("Mapbender:id")) {
						// button: geometry information, update kml
						listOfGeom += "<li>";
						listOfGeom += "<img src = '"+buttonDig_imgdir+buttonDig_wfs_src+"' title='"+msgObj.buttonDig_wfs_title+"' onclick='showWfsKml("+i+")'>";
					}
				}
				else {
					// button: geometry information, save, update, delete
					listOfGeom += "<li>";
					if (d.get(i).get(-1).isComplete() && wfsExistsForGeom(d.get(i), wfsConf)) {
						listOfGeom += "<img src = '"+buttonDig_imgdir+buttonDig_wfs_src+"' title='"+msgObj.buttonDig_wfs_title+"' onclick='showWfs("+i+")'>";
					}

					// button: remove this geometry
					listOfGeom += "<img src = '"+buttonDig_imgdir+buttonDig_remove_src+"' title='"+msgObj.buttonDig_remove_title+"' onclick='parent.mb_disableThisButton(mod_digitizeEvent);d.del("+i+");executeDigitizeSubFunctions();'>";

					// button clone this geometry
					if (d.get(i).get(-1).isComplete() && addCloneGeometryButton === true) {
						listOfGeom += "<img src = '"+buttonDig_imgdir+buttonDig_clone_src+"' title='"+msgObj.buttonDig_clone_title+"' onclick='d.addCopy(d.get("+i+"));d.get(-1).e.delElement(\"fid\");eventCloseGeometry.trigger({index: d.count() - 1, geometry: d.get(-1)});executeDigitizeSubFunctions();'>";
					}
				}
					
				// button: remove geometry from database
				if (d.get(i).e.getElementValueByName('fid')) {
					listOfGeom += "<img src = '"+buttonDig_imgdir+buttonDig_removeDb_src + 
						"' title='"+msgObj.buttonDig_removeDb_title + 
						"' onclick=\"deleteFeature("+i+")\">";
				}
				
				listOfGeom += "<div class='digitizeGeometryListItem' onmouseover='parent.mb_wfs_perform(\"over\",d.get("+i+"),\""+geomHighlightColour+"\");' ";
				listOfGeom += " onmouseout='parent.mb_wfs_perform(\"clean\",d.get("+i+"),\""+geomHighlightColour+"\")' ";
				listOfGeom += " onclick='parent.mb_wfs_perform(\"click\",d.get("+i+"),\""+geomHighlightColour+"\");' ";
				var geomName = getName(d.get(i)); 
				var currentGeomType;
				if (d.get(i).geomType == parent.geomType.polygon) {
					currentGeomType = msgObj.messageDescriptionPolygon;
				}
				else if (d.get(i).geomType == parent.geomType.line) {
					currentGeomType = msgObj.messageDescriptionLine;
				}
				else if (d.get(i).geomType == parent.geomType.point) {
					currentGeomType = msgObj.messageDescriptionPoint;
				}
				var multi = "";
				if (d.get(i).count() > 1) {
					multi = "multi";
				}
				listOfGeom += ">" + htmlspecialchars(geomName) +" (" + multi + currentGeomType + ")</div>";
				
				// multigeometries
				listOfGeom += "<ul>";
				for (var j = 0; j < d.get(i).count(); j++) {
					var currentGeom = d.get(i).get(j);
					if (d.get(i).count() > 1 || (d.get(i).geomType == geomType.polygon && 
						d.get(i).get(j).innerRings && 
						d.get(i).get(j).innerRings.count() > 0)) {
						listOfGeom += "<li>";
						listOfGeom += "<img src = '"+buttonDig_imgdir+buttonDig_remove_src+"' title='"+msgObj.buttonDig_remove_title+"' onclick='parent.mb_disableThisButton(mod_digitizeEvent);d.get("+i+").del(" + j + ");executeDigitizeSubFunctions();'>";
						listOfGeom += "<div class='digitizeGeometryListItem' onmouseover='parent.mb_wfs_perform(\"over\",d.get("+i+").get("+j+"),\""+geomHighlightColour+"\");' ";
						listOfGeom += " onmouseout='parent.mb_wfs_perform(\"clean\",d.get("+i+").get("+j+"),\""+geomHighlightColour+"\")' ";
						listOfGeom += " onclick='parent.mb_wfs_perform(\"click\",d.get("+i+").get("+j+"),\""+geomHighlightColour+"\");' ";
						listOfGeom += ">" + currentGeomType + "#" + (j+1) +"</div></li>";
					}
					if (d.get(i).geomType == geomType.polygon && 
						d.get(i).get(j).innerRings && 
						d.get(i).get(j).innerRings.count() > 0) {
						listOfGeom += "<ul>";
						for (var k = 0; k < d.get(i).get(j).innerRings.count(); k++) {
							var currentRing = d.get(i).get(j).innerRings.get(k);
							listOfGeom += "<li>";
							listOfGeom += "<img src = '"+buttonDig_imgdir+buttonDig_remove_src+"' title='"+msgObj.buttonDig_remove_title+"' onclick='parent.mb_disableThisButton(mod_digitizeEvent);d.get("+i+").get(" + j + ").innerRings.del(" + k + ");executeDigitizeSubFunctions();'>";
							listOfGeom += "<div class='digitizeGeometryListItem' onmouseover='parent.mb_wfs_perform(\"over\",d.getGeometry("+i+","+j+").innerRings.get(" + k + "),\""+geomHighlightColour+"\");' ";
							listOfGeom += " onmouseout='parent.mb_wfs_perform(\"clean\",d.getGeometry("+i+","+j+").innerRings.get(" + k + "),\""+geomHighlightColour+"\")' ";
							listOfGeom += " onclick='parent.mb_wfs_perform(\"click\",d.getGeometry("+i+","+j+").innerRings.get(" + k + "),\""+geomHighlightColour+"\");' ";
							listOfGeom += ">inner ring #" + (k+1) +"</div></li>";
							
						}
						listOfGeom += "</ul>";
					}
				}
				listOfGeom += "</ul>";
				listOfGeom += "</li>";
			}
		}
	}
	listOfGeom += "<ul>";
	parent.writeTag(mod_digitize_elName,"listOfGeometries",listOfGeom);
}
// --- display (end) -----------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------------------------------
// --- wfs window (begin) -----------------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------------------------------
// --- wfs window form check (begin) -----------------------------------------------------------------------------------------

function formCorrect(doc, formId) {
	var isCorrect = true;
	var errorMessage = "";
	var result;
	var form = doc.getElementById(formId);
	
	result = mandatoryFieldsNotEmpty(doc, form);
	isCorrect = isCorrect && result.isCorrect;
	errorMessage += result.errorMessage;

	//select box is now checked within function mandatoryFieldsNotEmpty
	//result = validBoxEntrySelected(form);
	//isCorrect = isCorrect && result.isCorrect;
	//errorMessage += result.errorMessage;

	result = dataTypeIsCorrect(doc, form);
	isCorrect = isCorrect && result.isCorrect;
	errorMessage += result.errorMessage;

	return {"isCorrect":isCorrect, "errorMessage":errorMessage};
}

function validBoxEntrySelected(form){
	var isCorrect = true;
	var errorMessage = "";
	var name = "";
	for (var i = 0; i < form.childNodes.length && isCorrect; i++) {
		if (form.childNodes[i].nodeName.toUpperCase() == "SELECT") {
			name += form.childNodes[i].name;
			if (parseInt(form.childNodes[i].selectedIndex) == 0 && $(form.childNodes[i]).hasClass("mandatory")) {
				var msg = name + ": " + msgObj.messageSelectAnOption + "\n"
				var categ = form.childNodes[i].getAttribute("category");
				if (typeof(categ) != "undefined") {
					msg = categ + " => " + msg;
				}
				return {"isCorrect":false, "errorMessage":msg};
			}
		}
		else if (form.childNodes[i].hasChildNodes()) {
			var res = validBoxEntrySelected(form.childNodes[i]);
			errorMessage = res.errorMessage + errorMessage;
			isCorrect = res.isCorrect;
			
		}
	}
	return {"isCorrect":isCorrect, "errorMessage":errorMessage};
}

function mandatoryFieldsNotEmpty(doc, node){
	var isCorrect = true;
	var errorMessage = "";
	
	var $nodeArray = $(".mandatory", doc);
	for (var i = 0; i < $nodeArray.size() && isCorrect; i++) {
		var $currentNode = $nodeArray.eq(i);
		var tagName = $currentNode.get(0).nodeName.toUpperCase();
		if (tagName == "INPUT" && $currentNode.val() == "") {
			isCorrect = false;
			errorMessage += "'"+ $currentNode.attr("name") +"': "+ msgObj.messageErrorFieldIsEmpty +"\n";
		}
		
		if (tagName == "SELECT" && parseInt($currentNode.get(0).selectedIndex) == 0) {
			isCorrect = false;
			errorMessage += "'"+ $currentNode.attr("name") +"': "+ msgObj.messageErrorFieldIsEmpty +"\n";
		}
	}
	return {"isCorrect":isCorrect, "errorMessage":errorMessage};
}

function isInteger(str) {
	if (str.match(/^[0-9]{0,8}$/) || str == "" ) { //will be better ;-)
		return true;
	}
	return false;
}

function isFloat(str) {
	if (isInteger(str)) {
		return true;
	}
	if (str.match(/^\d+\.\d+$/)) {
		return true;
	}
	return false;
}

function replaceCommaByDecimalPoint(str) {
	var patternString = ",";
	var pattern = new RegExp(patternString);
	while (str.match(pattern)) {
		str = str.replace(pattern, ".");
	}
	return str;
}

function dataTypeIsCorrect(doc, node){
	var isCorrect = true;
	var errorMessage = "";
	
	nodeArray = doc.getElementsByName("datatype");
	for (var i = 0; i < nodeArray.length ; i++) {
		if (nodeArray[i].nodeName.toUpperCase() == "INPUT" && nodeArray[i].type == "hidden" && nodeArray[i].id.substr(0,9) == "datatype_") {
			var nodeId = nodeArray[i].id.substr(9);
			var nodeValue = doc.getElementById(nodeId).value;
			
			if (nodeArray[i].value == "int") {
				if (!isInteger(nodeValue)) {
					isCorrect = false;
					errorMessage += "'"+doc.getElementById(nodeId).name+"': "+ msgObj.messageErrorNotAnInteger + "\n";
				}
			}
			else if (nodeArray[i].value == "double" || nodeArray[i].value == "float") {
				nodeValue = replaceCommaByDecimalPoint(nodeValue);
				if (!isFloat(nodeValue)) {
					isCorrect = false;
					errorMessage += "'"+doc.getElementById(nodeId).name+"': "+ msgObj.messageErrorNotAFloat + "\n";
				}
				else {
					doc.getElementById(nodeId).value = nodeValue;
				}
			}
		}
	}
	return {"isCorrect":isCorrect, "errorMessage":errorMessage};
}
// --- wfs window form check (end) -----------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------------

function getAvailableWfsForGeom(geom, wfsConf) {	
	var wfsConfIndices = [];
	
	for (var attr in wfsConf) {
		var isTrans = (wfsConf[attr]['wfs_transaction'] != "");
		if (!isTrans) {
			continue;
		}
		/*
		if (isValidWfsConfIndex(wfsConf, parseInt(geom.wfs_conf))) {
			if (parseInt(geom.wfs_conf) == parseInt(attr)) {
				wfsConfIndices.push(attr);
			}
		}
		else {
		*/
			for (var elementIndex = 0; elementIndex < wfsConf[attr]['element'].length ; elementIndex++) {
				var isGeomColumn = (parseInt(wfsConf[attr]['element'][elementIndex]['f_geom']) == 1); 
				if (isGeomColumn) {
					var isMultiPolygon = (
						geom.geomType == parent.geomType.polygon && 
						(
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'MultiPolygonPropertyType' ||
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'GeometryPropertyType' ||
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'MultiSurfacePropertyType'
						)
					);
					var isPolygon = (
						geom.geomType == parent.geomType.polygon && 
						geom.count() == 1 && 
						(
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'PolygonPropertyType' ||
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'GeometryPropertyType' ||
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'SurfacePropertyType'
						)
					);
					var isMultiLine = (
						geom.geomType == parent.geomType.line && 
						(
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'MultiLineStringPropertyType' ||
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'GeometryPropertyType' ||
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'MultiCurvePropertyType'
						)
					);
					var isLine = (
						geom.geomType == parent.geomType.line && 
						geom.count() == 1 && 
						(
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'LineStringPropertyType' ||
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'GeometryPropertyType' ||
						wfsConf[attr]['element'][elementIndex]['element_type'] == 'CurvePropertyType'
						)
					);
					var isPoint = (geom.geomType == parent.geomType.point && wfsConf[attr]['element'][elementIndex]['element_type'] == 'PointPropertyType');
					var isMultiPoint = (geom.geomType == parent.geomType.point && wfsConf[attr]['element'][elementIndex]['element_type'] == 'MultiPointPropertyType');
//					alert(isMultiPolygon + " " + isPolygon + " " + isMultiLine + " " + isLine + " " + isPoint);
					if (isMultiPolygon || isPolygon || isMultiLine || isLine || isMultiPoint || isPoint || wfsConf[attr]['element'][elementIndex]['element_type'] == 'GeometryPropertyType') {
						
						wfsConfIndices.push(attr);
					}
				}
			}
		//}	
	}
	return wfsConfIndices;
}
function wfsExistsForGeom(geom, wfsConf) {
	wfsConfIndices = getAvailableWfsForGeom(geom, wfsConf);
//	alert(wfsConfIndices.join(","));
	if (wfsConfIndices.length > 0) {
		return true;
	}
	return false;
}


function showWfsKml (geometryIndex) {
	wfsKmlWindow = open("", "wfsattributes", "width="+wfsWindowWidth+", height="+wfsWindowHeight+", resizable, dependent=yes, scrollbars=yes");
	wfsKmlWindow.document.open("text/html");
	wfsKmlWindow.document.writeln("<html><head><meta http-equiv='Content-Type' content='text/html; charset=<?php echo CHARSET;?>'></head><body><div id='linkToKml'></div><div id='elementForm'></div></body></html>");
	wfsKmlWindow.document.close();
	
	str = "<form id = 'wmsKmlForm' onsubmit='return false;'><table>";

	var properties = d.get(geometryIndex).e;
	var propertyCount = properties.count();	
	for (var i = 0; i < propertyCount; i++) {
		var key = properties.getName(i);
		var value = properties.getValue(i);
		var expr = /Mapbender:/;
		if (!key.match(expr)) {
			str += "\t\t<tr>\n";
			str += "\t\t\t<td>\n\t\t\t\t<div>" + key + "</div>\n\t\t\t</td>\n";
			str += "\t\t\t<td>\n";
			str += "\t\t\t\t<input id = 'wmskml_" + i + "' name='" + key + "' type='text' size=20 value = '" + value + "'>\n";
			str += "\t\t\t</td>\n\t\t</tr>\n";
		}
	}	

	var updateOnClickText = "this.disabled=true;window.opener.updateKmlInDb("+geometryIndex+", 'update');";
	var deleteOnClickText = "var deltrans = confirm('This geometry will be removed from the KML.');";
	deleteOnClickText += "if (deltrans){";
	deleteOnClickText += "this.disabled=true;window.opener.updateKmlInDb("+geometryIndex+", 'delete')}";
	
	str += "\t\t\t<td><input type='button' name='updateButton' value='Update' onclick=\""+updateOnClickText+"\"/></td>\n";
// delete button not yet implemented
//	str += "\t\t\t<td><input type='button' name='deleteButton' value='Delete' onclick=\""+deleteOnClickText+"\"/></td>\n";
	str += "\t\t\t<td><input type='button' name='abortButton' value='Abort' onclick=\"window.close();\" /></td>\n";

	str += "\t\t</tr>\n";
	str += "\t</table>\n";
	str += "</form>\n";

	wfsKmlWindow.document.getElementById("elementForm").innerHTML = str;
}


function stripslashes (str) {
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +      fixed by: Mick@el
    // +   improved by: marrtins    
	// +   bugfixed by: Onno Marsman
    // +   improved by: rezna
    // +   input by: Rick Waldron
    // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +   input by: Brant Messenger (http://www.brantmessenger.com/)    
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: stripslashes('Kevin\'s code');
    // *     returns 1: "Kevin's code"
    // *     example 2: stripslashes('Kevin\\\'s code');
    // *     returns 2: "Kevin\'s code"    
	return (str+'').replace(/\\(.?)/g, function (s, n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':                
				return '\u0000';
            case '':
                return '';
            default:
                return n1;        
		}
    });
}

function deleteFeature (geometryIndex) {
	var res = true;	
	res = parent.Mapbender.modules[mod_digitize_elName].events.clickDelete.trigger({
		geometryIndex: geometryIndex,
		feature: d.get(geometryIndex)
	});
	if (res === false) {
		return;	
	}
	var deltrans = confirm(msgObj.messageConfirmDeleteGeomFromDb);
	if (deltrans) {
		dbGeom('delete', geometryIndex);
	};

}

//
// this method opens a new window and displays the attributes in wfs_conf
//
function showWfs(geometryIndex) {
	var res = true;	
	res = parent.Mapbender.modules[mod_digitize_elName].events.openDialog.trigger({
		geometryIndex: geometryIndex,
		feature: d.get(geometryIndex)
	}, "AND");
	if (res === false) {
		return;	
	}

	wfsConf = parent.get_complete_wfs_conf();

	if(typeof wfsWindow != 'undefined') {
		wfsWindow.close();
	}
	
	wfsWindow = open("", "wfsattributes", "width="+wfsWindowWidth+", height="+wfsWindowHeight+", resizable, dependent=yes, scrollbars=yes");
	wfsWindow.document.open("text/html");
	setTimeout(function () {
		wfsWindow.focus();
	}, 100);
	
	var str = "";
	var strStyle = "";
	var defaultIndex = -1;

	str += "<form id='wfs'>\n";

	//
	// 1. add select box 
	//

	var onChangeText = "document.getElementById('elementForm').innerHTML = ";
	onChangeText += "window.opener.buildElementForm(this.value, " + geometryIndex + ");";
	onChangeText += "window.opener.setWfsWindowStyle(this.value);";

	var datePickerText = "$('.hasdatepicker').each(function () { " + 
		"var data = $.parseJSON(window.opener.stripslashes($(this).attr('data'), true));" +
		"var defaults = {};" +
		"var settings = $.extend({}, defaults, data);" + 
		"$(this).datepicker(settings);" + 
		"});";
	
	onChangeText += datePickerText;

	
	var uploaderText = 
		"deleteUploadedFile = function (domNode) {" + 
			"$(domNode).siblings('input').val('');" + 
			"$(domNode).siblings('a').remove();" + 
			"$(domNode).remove();" + 
		"};" + 
		"var linkAndDeleteButtonHtml = '<a target=\\\'_blank\\\' title=\\\'show uploaded file\\\' " + 
			"href=\\\'#\\\'>Show file</a>" + 
			"<img src=\\\'../img/button_digitize/geomRemove.png\\\' " + 
			"title=\\\'Delete uploaded file\\\' alt=\\\'Delete uploaded file\\\' " +  
			"onclick=\\\'deleteUploadedFile(this);\\\' " + 
			"style=\\\'cursor:pointer\\\'/>';" + 
		"$('.upload').each(function () { " + 
		"var $this = $(this).upload({ "+
			"callback: function(result,stat,msg){"+
				"if(stat !== true){alert(msg);}"+
				"else{" + 
					"$this.prev().val(result.filename);" +
					"if ($this.siblings('a').size() === 0) {" + 
						"$this.parent().prepend(linkAndDeleteButtonHtml);" + 
					"}\n" +
					"$this.siblings('a').attr('href', result.filename);" + 
				"}" + 
			"}" + 
		"});" + 
	"});";
	
	onChangeText += uploaderText;

        var targetObject = parent.getMapObjByName("mapframe1");
	var mapExtentObj = targetObject.extent;
        var mapExtent = mapExtentObj.minx + ","+ mapExtentObj.miny +","+ mapExtentObj.maxx + ","+ mapExtentObj.maxy;

	var mapSelectBoxText = "$('.hasMapdependentSelect').each(function () { \n" +
		"var data = $.parseJSON(window.opener.stripslashes($(this).attr('data'), true));\n" +
		"data.bbox='"+ mapExtent + "';\n"+
		"var defaults = {};\n" +
		"var settings = $.extend({}, defaults, data);\n" +
		"$(this).mapdependentSelect(settings);\n" +
		"});\n";
	onChangeText  += mapSelectBoxText;

	str += "\t<select name='wfs' size='" + wfsConf.length + "'";
	str += " onChange=\""+ onChangeText +"\"";
	str += ">\n\t\t";

	var wfsConfIndices = getAvailableWfsForGeom(d.get(geometryIndex), wfsConf);
	var selected = false;
	// set the current wfs_conf as the selected
	// if wfs_conf is not yet set (for example when creating a new feature, just select the first one)
	var selectedIndex = d.get(geometryIndex).wfs_conf || wfsConfIndices[0];
	for (var i = 0; i < wfsConfIndices.length ; i++) {
		for (var j in wfsConf[i].element){

		}
		str += "<option value='" + wfsConfIndices[i] + "'";
		if (wfsConfIndices[i] == selectedIndex ) {
			str += " selected";
			selected = true;
			defaultIndex = parseInt(wfsConfIndices[i]);
		}
		str += ">" + wfsConf[wfsConfIndices[i]]['wfs_conf_abstract'];
		str += "</option>\n\t\t";
	}

	
	str += "</select>\n\t\t</form>\n\t";
	
	var elForm = "";
	if (defaultIndex != -1) {
		elForm = buildElementForm(defaultIndex, geometryIndex);

		var headStr = "<html><head><meta http-equiv='Content-Type' content='text/html; charset=<?php echo CHARSET;?>'><style type='text/css'>" + wfsConf[defaultIndex]['g_style'] + "</style>";
		headStr += '<link rel="stylesheet" type="text/css" href="../extensions/jquery-ui-1.7.2.custom/development-bundle/themes/base/ui.all.css" />';
		headStr += '<style type="text/css">'
		headStr += 'a.tabheader {float:left; margin: 0 3px 0 0;padding: 1px 5px;text-decoration: none;color: #999;background-color: #F5F5F5;border: 1px solid #999;border-bottom: 0; }';
		headStr += 'a.tabheader.active {float:left; color: #666;background-color: transparent;border-color: #666;border-bottom: 0px solid #FFF;cursor: default; }';
		headStr += 'div.tabcontent {clear:left; visibility: hidden;display: none;margin: 1px 0 5px 0;padding: 5px;border: 1px solid #666; }';
		headStr += 'div.helptext { visibility: hidden;display: none;position: absolute;top: 5%;left: 5%;width: 85%;padding: 5px;color: #000;background-color: #EEEEEE;border: 1px solid #000;-moz-border-radius: 10px;-webkit-border-radius: 10px;}';
		headStr += 'div.helptext p { margin: 0 ; }';
		headStr += 'div.helptext p a.close { display: block;margin: 5px auto;text-align: center; }';
		headStr += 'a img { vertical-align: middle;border: 0; }';
		headStr += '.mandatory { border:1px solid red; }';
		headStr += '</style>';
		headStr += '</head><body onload="window.opener.toggleTabs(\''+initialTab+'\'); ' + datePickerText + uploaderText + mapSelectBoxText + '">';
		wfsWindow.document.write(headStr);
	}
	else {
		var headStr = "<html><head><style type='text/css'></style></head><body>";
		wfsWindow.document.write(headStr);
	}
	str += "<div id='elementForm'>\n" + elForm + "</div>";
	str += "<script type='text/javascript' src='../extensions/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js'><\/script>";
	str += "<script type='text/javascript' src='../extensions/jquery-ui-1.7.1.w.o.effects.min.js'><\/script>";
	str += "<script type='text/javascript' src='../plugins/jq_upload.js'><\/script>";
	str += "<script type='text/javascript' src='../extensions/jqjson.js'><\/script>";
        str += "<script type='text/javascript' src='../plugins/mapdependentSelectBox.js'><\/script>";
	str += "</body></html>";
	wfsWindow.document.write(str);
	wfsWindow.document.close();

//	toggleTabs(initialTab);

}

function setWfsWindowStyle(wfsConfIndex) {
	wfsWindow.document.getElementsByTagName("style")[0].innerHTML = wfsConf[wfsConfIndex]['g_style'];
}

function prepareSelectBox (formElementHtml, categoryName, isMandatory, elementLabel, elementValue, styleId) {
	var classString = (styleId == '0') ? "" : styleId;
	var patternString = "<select";
	var pattern = new RegExp(patternString);
	// set category
	if (categoryName) {
		formElementHtml = formElementHtml.replace(pattern, patternString + " category='" + categoryName + "' ");
	}

	if (isMandatory) {
		// set border if mandatory
		classString += " mandatory";
	}
	classString = (classString !== "") ? " class='"+classString+"' " : " ";
	formElementHtml = formElementHtml.replace(pattern, patternString + classString);

	// set name of select box to elementlabel
	patternString = "name\s*=\s*\\*'\w+\\*'";
	pattern = new RegExp(patternString);
	if (pattern.test(formElementHtml)) {
		formElementHtml = formElementHtml.replace(pattern, "name='" + elementLabel + "'");
	}
	else {
		patternString = "<select";
		pattern = new RegExp(patternString);
		formElementHtml = formElementHtml.replace(pattern, "<select name='" + elementLabel + "'");
	}
	
	// preselect the correct entry of the box
	patternString = "option( )+value( )*=( )*('|\")"+elementValue+"('|\")";
	pattern = new RegExp(patternString);
	var patternStringForReplace = "option value = '"+elementValue+"'";
	formElementHtml = formElementHtml.replace(pattern, patternStringForReplace+" selected");
	return formElementHtml;
}

function prepareMapdependentSelect (formElementHtml, categoryName, isMandatory, elementLabel, elementValue, styleId) {
	var classString = (styleId == '0') ? "" : styleId;
	var patternString = "<select";
	var pattern = new RegExp(patternString);
	// set category
	if (categoryName) {
		formElementHtml = formElementHtml.replace(pattern, patternString + " category='" + categoryName + "' ");
	}

	if (isMandatory) {
		// set border if mandatory
		classString += "hasMapdependentSelect mandatory";
	}
	classString = (classString !== "") ? " class='"+classString+"' " : " ";
	formElementHtml = formElementHtml.replace(pattern, patternString + classString);

	// set name of select box to elementlabel
	patternString = "name\s*=\s*\\*'\w+\\*'";
	pattern = new RegExp(patternString);
	if (pattern.test(formElementHtml)) {
		formElementHtml = formElementHtml.replace(pattern, "name='" + elementLabel + "'");
	}
	else {
		patternString = "<select";
		pattern = new RegExp(patternString);
		formElementHtml = formElementHtml.replace(pattern, "<select name='" + elementLabel + "'");
	}

	// preselect the correct entry of the box
	// not so easy because this can happen after the WFSsearch returns results
	// so give it an extra attribute which can be parsed bythe javascript side of things

	formElementHtml = formElementHtml.replace(pattern,patternString + " elementvalue='"+elementValue + "' ");
	return formElementHtml;
}

function prepareDatepicker (formElementHtml, categoryName, isMandatory, elementLabel, elementValue, styleId) {
	var classString = (styleId == '0') ? "" : styleId;
	var patternString = "<input";
	var pattern = new RegExp(patternString);
		
	// set category
	if (categoryName) {
		formElementHtml = formElementHtml.replace(pattern, patternString + " category='" + categoryName + "' ");
	}

	if (isMandatory) {
		// set border if mandatory
		classString += " mandatory";
	}
	classString = (classString !== "") ? " class='"+classString+" hasdatepicker' " : " ";
	formElementHtml = formElementHtml.replace(pattern, patternString + classString);

	// set name of select box to elementlabel
	patternString = "name\s*=\s*\\*('|\")\w+\\*('|\")";
	pattern = new RegExp(patternString);
	if (pattern.test(formElementHtml)) {
		formElementHtml = formElementHtml.replace(pattern, "name='" + elementLabel + "'");
	}
	else {
		patternString = "<input";
		pattern = new RegExp(patternString);
		formElementHtml = formElementHtml.replace(pattern, "<input name='" + elementLabel + "'");
	}
	
	// preselect the correct entry of the box
	patternString = "<input";
	pattern = new RegExp(patternString);
	formElementHtml = formElementHtml.replace(pattern, patternString + " value='"+elementValue+"'");

	return formElementHtml;
}

function prepareTextArea (formElementHtml, categoryName, isMandatory, elementLabel, elementValue, styleId) {
	var classString = (styleId == '0') ? "" : styleId;
	var patternString = "<textarea";
	var pattern = new RegExp(patternString);
	// set category
	if (categoryName) {
		formElementHtml = formElementHtml.replace(pattern, patternString + " category='" + categoryName + "' ");
	}

	if (isMandatory) {
		// set border if mandatory
		classString += " mandatory";
	}
	classString = (classString !== "") ? " class='"+classString+"' " : " ";
	formElementHtml = formElementHtml.replace(pattern, patternString + classString);

	// set name of select box to elementlabel
	patternString = "name\s*=\s*\\*('|\")\w+\\*('|\")";
	pattern = new RegExp(patternString);
	if (pattern.test(formElementHtml)) {
		formElementHtml = formElementHtml.replace(pattern, "name='" + elementLabel + "'");
	}
	else {
		patternString = "<textarea";
		pattern = new RegExp(patternString);
		formElementHtml = formElementHtml.replace(pattern, "<textarea name='" + elementLabel + "'");
	}
	
	// preselect the correct entry of the box
	patternString = "<\/textarea>";
	pattern = new RegExp(patternString);
	formElementHtml = formElementHtml.replace(pattern, elementValue + patternString);

	return formElementHtml;
}

function prepareUploadField (formElementHtml, categoryName, isMandatory, elementLabel, elementValue, styleId) {
	//var elementValue = "../tmp/1-14bbb3d6987e16.png";
	var classString = (styleId == '0') ? "" : styleId;
	var patternString = "<div";
	var pattern = new RegExp(patternString);
		
	// set category
	if (categoryName) {
		formElementHtml = formElementHtml.replace(pattern, patternString + " category='" + categoryName + "' ");
	}

	classString = (classString !== "") ? " class='"+classString+"' " : " ";
	formElementHtml = formElementHtml.replace(pattern, patternString + classString);
	
	// preselect the correct entry of the box
	if(elementValue !== "") {
		formElementHtml = "<a title='show uploaded file' target='_blank' href='" + 
			elementValue + "'>Show file</a><img title='Delete uploaded file' " +
			"alt='Delete uploaded file' style='cursor:pointer;' " +
			"onclick='$(this).siblings(\"input\").val(\"\");" + 
			"$(this).siblings(\"a\").remove();$(this).remove();' " + 
			"src='../img/button_digitize/geomRemove.png'/>" + 
			formElementHtml;
	}
	patternString = "class\s*=\s*['\"]hiddenUploadField['\"]";
	pattern = new RegExp(patternString);
	replaceString = "class='hiddenUploadField' " + " value='"+elementValue+"'";
	formElementHtml = formElementHtml.replace(pattern, replaceString);
	
	return formElementHtml;
}

// Returns a form with the elements of a selected WFS grouped in tabs
// (if the original WFS is the selected WFS, the values are set too)
var initialTab = false;

// returns a form with the elements of a selected wfs
// (if the original wfs is the selected wfs, the values are set too)
function buildElementForm(wfsConfIndex, memberIndex){
	var featureTypeMismatch = false;
	if (parseInt(d.get(memberIndex).wfs_conf) != parseInt(wfsConfIndex)) {featureTypeMismatch = true;}
	var str = "";
	var hasGeometryColumn = false;
	var featureTypeArray = wfsConf[wfsConfIndex];
	var memberElements;
	var fid = false;

	if (!featureTypeMismatch) {
		memberElements = d.get(memberIndex).e;
		fid = memberElements.getElementValueByName('fid');
	}
	
	if (typeof(featureTypeArray["element"]) !== "undefined") {
		featureTypeElementArray = featureTypeArray["element"];


		// Check if there are categories given and
		// build the form in tabs if necessary
		var elementCategories = [];
		for(var i = 0; i < featureTypeElementArray.length; i++){
			var categoryName         = featureTypeElementArray[i].f_category_name;
			var categoryNameIsUnique = true;
			
			if(categoryName.length === 0) { continue; }
			
			for(var j = 0; j < elementCategories.length; j++) {
				if(elementCategories[j] == categoryName) {
					categoryNameIsUnique = false;
				}
			}
			
			if(categoryNameIsUnique) {
				elementCategories.push(categoryName);
			}
		}

		str += "<form id='"+featureTypeElementFormId+"'>\n\t";
		
		var hasCategories = (elementCategories.length > 0);
		if (hasCategories) {
			elementCategories.sort();
			
			initialTab = elementCategories[0];
			str +='<table><tr><td>';
			for (var currentCategory = 0; currentCategory < elementCategories.length; currentCategory++) {
				str += '<a href="#" id="tabheader_' + elementCategories[currentCategory] + '" class="tabheader" onclick="return window.opener.toggleTabs(\'' + elementCategories[currentCategory] + '\')">' + elementCategories[currentCategory] + '</a>';
			}
			str +='</td></tr></table>';
		}
			
		for (var currentCategory = 0; currentCategory < elementCategories.length || !hasCategories; currentCategory++) {
			if (hasCategories) {
				str += '<div id="tab_' + elementCategories[currentCategory] + '" class="tabcontent">';
			}
			str += '<table>';

			//
			// 2. add rows to form 
			//
			for (var i = 0 ; i < featureTypeElementArray.length ; i++) {
				var featureTypeElement = featureTypeElementArray[i];

				var elementName = featureTypeElement['element_name'];
				var elementType = featureTypeElement['element_type'];
				var isEditable = (parseInt(featureTypeElement['f_edit']) == 1); 
				var isMandatory = (parseInt(featureTypeElement['f_mandatory']) == 1); 
				var isGeomColumn = (parseInt(featureTypeElement['f_geom']) == 1); 

				if(hasCategories && featureTypeElement.f_category_name != elementCategories[currentCategory] && !isGeomColumn) {
					continue;
				}
			

				var elementLabelExists = (featureTypeElement['f_label'] != "");
				var elementLabel = ""; 
				if (elementLabelExists) {
					elementLabel = featureTypeElement['f_label'];
				}
				var elementLabelStyle = featureTypeElement['f_label_id'];

				if (!isGeomColumn) {
					if (isEditable) {
						str += "\t\t<tr>\n";
						if(elementLabelExists) {
							str += "\t\t\t<td>\n\t\t\t\t<div class = '"+elementLabelStyle+"'>" + elementLabel + "</div>\n\t\t\t</td>\n";
							str += "\t\t\t<td>\n";
						}
						else {
							str += '<td colspan="2">';
						}

						var elementValue = "";
						if (!featureTypeMismatch) {
							for (var j = 0 ; j < memberElements.count() ; j ++) {
								if (memberElements.getName(j) == featureTypeElement['element_name']) {
									elementValue = memberElements.getValue(j);
								}
							}
						}
						var formElementHtml = featureTypeElement['f_form_element_html']; 
						if (!formElementHtml) {
							var classString = (styleId == '0') ? "" : featureTypeElement['f_style_id'];
							if (parseInt(featureTypeElement['f_mandatory']) == 1) {
								classString += " mandatory";
							}

							classString = (classString !== "") ? " class='"+classString+"' " : " ";
							
							str += "\t\t\t\t<input id = 'datatype_mb_digitize_form_" + elementName + "' name='datatype' type='hidden' value = '" + elementType + "'>\n";
							if (!hasCategories) {
								str += "\t\t\t\t<input id = 'mb_digitize_form_" + elementName + "' name='" + elementLabel + "' type='text' "+classString+" size=20 value = '" + elementValue + "'>\n";
							}
							else {
								str += "\t\t\t\t<input category='"+elementCategories[currentCategory]+"' id = 'mb_digitize_form_" + elementName + "' name='" + elementLabel + "' type='text' "+classString+" size=20 value = '" + elementValue + "'>\n";
							}
						}
						else {
							while (formElementHtml.match(/\\/)) {
								formElementHtml = formElementHtml.replace(/\\/, "");
							} 

							var isMandatory = (parseInt(featureTypeElement['f_mandatory']) == 1);

							var patternString = "<select";
							pattern = new RegExp(patternString);
							var styleId = featureTypeElement['f_style_id'];
							if (pattern.test(formElementHtml)) {
								formElementHtml = prepareSelectBox(formElementHtml, "", isMandatory, elementLabel, elementValue, styleId);
							}
							var patternString = "hasdatepicker";
							pattern = new RegExp(patternString);
							if (pattern.test(formElementHtml)) {
								formElementHtml = prepareDatepicker(formElementHtml, "", isMandatory, elementLabel, elementValue, styleId);
							}
							var patternString = "<textarea";
							pattern = new RegExp(patternString);
							if (pattern.test(formElementHtml)) {
								formElementHtml = prepareTextArea(formElementHtml, "", isMandatory, elementLabel, elementValue, styleId);
							}
							var patternString = "upload";
							pattern = new RegExp(patternString);
							if (pattern.test(formElementHtml)) {
								formElementHtml = prepareUploadField(formElementHtml, "", isMandatory, elementLabel, elementValue, styleId);
							}
                                                        var patternString = "hasMapdependentSelect";
							pattern = new RegExp(patternString);
							if (pattern.test(formElementHtml)) {
								formElementHtml = prepareMapdependentSelect(formElementHtml, "", isMandatory, elementLabel, elementValue, styleId);
							}
							str += formElementHtml;
						}
						
						if(featureTypeElement.f_helptext.length > 0) {
							str += ' <a href="#" onclick="return window.opener.showHelptext(' + i + ')"><img src="../img/help.png" width="16" height="16" alt="?" /></a> ';
							str += '<div id="helptext' +i+ '" class="helptext">';
							str += '<p>';
							str += featureTypeElement.f_helptext.replace(/(http:\/\/\S*)/g,'<a href="$1" target="blank">$1<\/a>');
							str += '<a href="#" class="close" onclick="return window.opener.hideHelptext(' + i + ')">close</a>';
							str += '</p>';
							str += '</div>';
						}
						
						str += "\t\t\t</td>\n\t\t</tr>\n";
					}
				}
				else {
					hasGeometryColumn = true;
				}
			}

			str += '</table>';
			if (hasCategories) {
				str += '</div>';
			}

			// if no categories exist, the for loop would be 
			// infinite without this break
			if (!hasCategories) {
				break;
			}
		}

		//
		// 3. add buttons "save", "update", "delete"
		//
		str += "<table>";
		var isTransactional = (featureTypeArray['wfs_transaction']); 
		if (isTransactional) {
			str += "\t\t<tr>\n";

			var options = ["insert", "update", "delete", "abort"];
			for (var i = 0 ; i < options.length ; i++) {
				var onClickText = "this.disabled=true;var result = window.opener.formCorrect(document, '"+featureTypeElementFormId+"');";
				onClickText += 	"if (result.isCorrect) {";
				onClickText += 		"window.opener.dbGeom('"+options[i]+"', "+memberIndex+"); ";
//				onClickText +=      "window.close();";
				onClickText += 	"}";
				onClickText += 	"else {";
				onClickText += 		"alert(result.errorMessage);this.disabled=false;"
				onClickText += 	"}";
				
				if (options[i] == "insert" && hasGeometryColumn && (!fid || showSaveButtonForExistingGeometries)) {
					str += "\t\t\t<td><input type='button' name='saveButton' value='"+msgObj.buttonLabelSaveGeometry+"' onclick=\""+onClickText+"\" /></td>\n";
				}
				
				if (!featureTypeMismatch && fid) {
					if (options[i] == "update" && hasGeometryColumn) {
						str += "\t\t\t<td><input type='button' name='updateButton' value='"+msgObj.buttonLabelUpdateGeometry+"' onclick=\""+onClickText+"\"/></td>\n";
					}
					if (options[i] == "delete"){ 
						var deleteOnClickText = "var deltrans = confirm('"+msgObj.messageConfirmDeleteGeomFromDb+"');";
						deleteOnClickText += "if (deltrans){";
						deleteOnClickText += onClickText + "}";
						str += "\t\t\t<td><input type='button' name='deleteButton' value='"+msgObj.buttonLabelDeleteGeometry+"' onclick=\""+deleteOnClickText+"\"/></td>\n";
					}
				}
				if (options[i] == "abort") {
					str += "\t\t\t<td><input type='button' name='abortButton' value='"+msgObj.buttonLabelAbort+"' onclick=\"window.close();\" /></td>\n";
				}
			}
			str += "\t\t</tr>\n";
		}
		str += "\t</table>\n";
		str += "<input type='hidden' id='fid' value='"+fid+"'>";
//			str += "<input type='text' name='mb_wfs_conf'>";
		str += "</form>\n";
	}
	return str;
}

function dbGeom(type, m, callback, dbWfsConfId) {
	if (typeof dbWfsConfId !== "undefined") {
		d.get(m).wfs_conf = getJsWfsConfIdByDbWfsConfId(wfsConf, dbWfsConfId);
		d.get(m).e = new parent.Wfs_element();
	}

	var hasFid = d.get(m).e.getElementValueByName("fid") !== false;

	if (!hasFid) {
		if (typeof(wfsWindow) != 'undefined' && !wfsWindow.closed) {
			d.get(m).wfs_conf = parseInt(wfsWindow.document.forms[0].wfs.options[wfsWindow.document.forms[0].wfs.selectedIndex].value);
			d.get(m).e = new parent.Wfs_element();
		}
	}
	else {
		wfsConf = parent.get_complete_wfs_conf();
	}
	var myconf = wfsConf[d.get(m).wfs_conf];
	
	var mapObjInd = parent.getMapObjIndexByName(mod_digitize_target);

	var proceed = true;
	var patternString = parent.mb_mapObj[mapObjInd].epsg.toUpperCase();
	var pattern = new RegExp(patternString);

//	if(!myconf['featuretype_srs'].match(pattern)){
//		proceed = confirm(msgObj.errorMessageEpsgMismatch + parent.mb_mapObj[mapObjInd].epsg + " / "+ myconf['featuretype_srs'] + ". Proceed?");
//	}
//	if (proceed) {
		var fid = false;
		var errorMessage = "";
		if (typeof(wfsWindow) != 'undefined' && !wfsWindow.closed && (type === "insert" || type === "update")) {
			myform = wfsWindow.document.getElementById(featureTypeElementFormId);
		
			for (var i=0; i<myform.length; i++){
				if (myform.elements[i].id == "fid") {
					fid = myform.elements[i].value;
					if (fid == "false") {
						fid = false;
					}
					else {
						d.get(m).e.setElement('fid', fid);
					}
				}
				
				//else if (myform.elements[i].type == 'text' { //merging geoportal.rlp
				else if (myform.elements[i].type == "text" || 
						myform.elements[i].tagName.toUpperCase() == "TEXTAREA" ||
						myform.elements[i].className == "hiddenUploadField"){
					if (myform.elements[i].id) {
						var elementId = String(myform.elements[i].id).replace(/mb_digitize_form_/, "");
						var $dataTypeNode =  $(myform.elements[i]).prev("input");
						// if featuretype element is numeric, do not send empty fields
						if ($dataTypeNode.size() === 1 && -1 !== $.inArray($dataTypeNode.attr("value"), ["int", "double", "float", "decimal"])) {
							if (myform.elements[i].value !== "") {
								d.get(m).e.setElement(elementId, myform.elements[i].value);
							}
						}
						else {
							d.get(m).e.setElement(elementId, myform.elements[i].value);
						}					
					}
					else {
						errorMessage = msgObj.messageErrorFormEvaluation;
					}
				}
				// selectbox
				else if (typeof(myform.elements[i].selectedIndex) == 'number') {
					if (myform.elements[i].id) {
						var elementId = String(myform.elements[i].id).replace(/mb_digitize_form_/, "");
						d.get(m).e.setElement(elementId, myform.elements[i].options[myform.elements[i].selectedIndex].value);
					}
					else {
						errorMessage = msgObj.messageErrorFormEvaluation;
					}
				}
			}
		}
		else {
			fid = d.get(m).e.getElementValueByName('fid');
		}
//		str = parent.get_wfs_str(myconf, d, m, type, fid);

		if (type === "insert" || type === "update") {
			var module = parent.Mapbender.modules[mod_digitize_elName]; 
			module.events.beforeUpdateOrInsert.trigger({
				wfsConf: myconf,
				geometryIndex: m,
				feature: d.get(m),
				'method' : type
			});
			if (type === "insert") {
				module.events.beforeInsert.trigger({
					wfsConf: myconf,
					geometryIndex: m,
					feature: d.get(m)
				});
			}
			if (type === "update") {
				module.events.beforeUpdate.trigger({
					geometryIndex: m,
					feature: d.get(m)
				});
			}

			// can be set to true from outside to stop save action
			// default is false, so nothing happens here
			if (module.dataCheck) {
				return;
			}
			
			if (module.cancelAjaxRequest) {
				alert(module.cancelAjaxRequestMessage);
				module.cancelAjaxRequest = false;
				module.cancelAjaxRequestMessage = "An error occured.";
				return;
			}
		}
		
		// Extract the current, possibly new WfsConf
                var newWfsConfId = d.get(m).wfs_conf
		if (typeof(wfsWindow) != 'undefined' && !wfsWindow.closed) {
                    newWfsConfId = parseInt(wfsWindow.document.getElementById('wfs').wfs.value, 10);
		}
		var newWfsConf = wfsConf[newWfsConfId];
		newWfsConfId = newWfsConf['wfs_conf_id'];

		// Check each feature attribute if it is part of the WfsConf element type. If not, delete.
		var e = d.get(m).e;
		var elementsToDelete = [];
		for(var i in e.name) {
			// Never delete fid attribute
                        if(e.name[i] === "fid") {
                            continue;
                        }
                        
                        var validElement = false;
			for(var j in newWfsConf.element) {
				if(e.name[i] == newWfsConf.element[j]['element_name'] && newWfsConf.element[j]['f_edit'] === "1") {
					validElement = true;
					break;
				}
			}
			if(!validElement) {
				elementsToDelete.push(i);
			}
		}
		
		// Delete. As the arrays shrink, the indices into the arrays are shrunk, too
		for(var i in elementsToDelete) {
			e.name.splice(elementsToDelete[i] - i, 1);
			e.value.splice(elementsToDelete[i] - i, 1);
		}
		
		var geoJson = d.featureToString(m);

		parent.mb_ajax_post(
			"../extensions/geom2wfst.php", 
			{
				'geoJson' : geoJson,
				'method' : type,
				'wfs_conf_id' : newWfsConfId
			}, 
			function(json,status){
				var result = typeof(json) == 'object' ? json :  eval('('+json+')');
				var success = result.success;
				var fid = result.fid;
				wfsSubWrite(m, type, status, success, fid, callback);
			}
		);
//	}
}

function getJsWfsConfIdByDbWfsConfId (wfsConf, id) {
	for (var i = 0; i < wfsConf.length; i++) {
		if (parseInt(wfsConf[i].wfs_conf_id, 10) === id) {
			return i
		}
	}
	return null;
}

function wfsSubWrite(m, type, status, success, fid, callback) {
	if (status == "success" && success) {
		if (type == 'insert' && fid) {
			d.get(m).e.setElement("fid", fid);
		}
		if (type == 'delete') {
			parent.mb_disableThisButton(mod_digitizeEvent);
			d.del(m);
		}

		var wfsWriteMessage = msgObj.messageSuccessWfsWrite;
	}
	else {
		var wfsWriteMessage = msgObj.messageErrorWfsWrite;
	} 

	parent.Mapbender.modules[mod_digitize_elName].events.afterWfs.trigger({
		feature: (type === 'delete') ? null : d.get(m),
		type: type
	});
	
	parent.mb_execWfsWriteSubFunctions();

	if (updatePointGeometriesInstantly && 
		status == "success" && 
		success && 
		typeof callback === "function") {
			callback();		
	}

	if (typeof(wfsWindow) != 'undefined' && !wfsWindow.closed) {
		if (status !== "success" || !success) {
			wfsWindow.alert(wfsWriteMessage);
		}
		else {
			parent.Mapbender.modules[mod_digitize_elName].events.geometryInserted.trigger({
				fid: fid,
				geometryIndex: m,
				feature: d.get(m),
				type: type
			});
			new parent.Mb_notice(wfsWriteMessage);
		}
		window.setTimeout("wfsWindow.close()",0);
	}
	else {
		if (status !== "success" || !success) {
			alert(wfsWriteMessage);
		}
		else {
			parent.Mapbender.modules[mod_digitize_elName].events.geometryInserted.trigger({
				fid: fid,
				geometryIndex: m,
				feature: d.get(m)
			});
			new parent.Mb_notice(wfsWriteMessage);
		}
	}
	executeDigitizeSubFunctions();
}

function getMultiGeometryIdsByPlacemarkId (placemarkId) {
	var multiGeometryIdArray = [];
	for (var i = 0; i < d.count(); i++) {
		var currentPlacemarkId = d.get(i).e.getElementValueByName("Mapbender:placemarkId");
		if (currentPlacemarkId && currentPlacemarkId == placemarkId) {
			multiGeometryIdArray.push(i);	
		}
	}	
	return multiGeometryIdArray;
}

function updateKmlInDb (geometryIndex, command) {
	var properties = d.get(geometryIndex).e;
	var placemarkId = properties.getElementValueByName("Mapbender:placemarkId");
	
	var multiGeometryIdArray = getMultiGeometryIdsByPlacemarkId(placemarkId);

	if (typeof(wfsKmlWindow) != 'undefined' && !wfsKmlWindow.closed) {

		// update properties from form
		myform = wfsKmlWindow.document.getElementById("wmsKmlForm");
	
		for (var i=0; i < myform.length; i++){
			if (myform.elements[i].type == 'text' ){
				if (myform.elements[i].id) {
					var key = myform.elements[i].name;
					var value = myform.elements[i].value;
					
					// update all geometries with the same placemark id
					for (var j = 0; j < multiGeometryIdArray.length; j++) {
						var currentProperties = d.get(j).e; 
						currentProperties.setElement(key, value);
					}
				}
			}
		}
		var kmlId = properties.getElementValueByName("Mapbender:id");
	
		parent.mb_ajax_post("../php/mod_updateKmlInDb.php", {command:command, kmlId:kmlId, placemarkId:placemarkId, geoJSON:d.placemarkToString(placemarkId)}, function(obj, status) {
			if (obj === "1") {
				wfsKmlWindow.alert("KML updated.");
				var link = wfsKmlWindow.document.createElement("a");
				link.href = "../php/mod_displayKML.php?kmlId=" + kmlId;
				link.target = "_blank";
				link.innerHTML = "KML";
				wfsKmlWindow.document.getElementById('elementForm').innerHTML = "";
				wfsKmlWindow.document.getElementById('linkToKml').appendChild(link);
			}
			else {
				wfsKmlWindow.alert("Error, KML could not be updated. Check your error log.");
			}
		});
	}
}


// --- wfs window (begin) -----------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------------

function getMessages() {
	parent.mb_ajax_json("../php/mod_digitize_messages.php", function(obj, status) {
		msgObj = obj;
		applyMessages();
	});
}

function applyMessages() {
	updateMeasureTag();
	updateListOfGeometries();
	updateButtons();
}

	</script>
	</head>
	<body onload="registerFunctions();displayButtons();">
<!-- 		<img id="digitizeBack" style="position:absolute;top:28;left:84" src="../img/button_digitize/back_on.png" title="" onclick="digitizeHistory.back()" name="digitizeBack"/>
		<img id="digitizeForward" style="position:absolute;top:28;left:112" src="../img/button_digitize/forward_on.png" title="" onclick="digitizeHistory.forward()" name="digitizeForward"/>
 -->
		<div id='digButtons'></div>
		<div style='position:absolute;top:60px;left:5px' id='listOfGeometries' class='digitizeGeometryList'></div>
	</body>
</html>
