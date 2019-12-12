<?php
/**
 * Package: AddWMSfromfilteredList_ajax
 *
 * Description:
 * Shows all WMS services contained in an authorized GUI
 * Shows list of all available WMS with abstract description
 * Adds selected WMS to current application
 * checkbox to ctivate layer on load, checkbox to zoom to WMS extent on load
 *
 * Files:
 *  - http/javascripts/mod_addWMSfromfilteredList_ajax.php
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, 
 * > e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, 
 * > e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, 
 * > e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES(
 * > '<app_id>','addWMSfromfilteredList_ajax',2,1,'add a WMS to the running application from a filtered list',
 * > 'Adding WMS from filtered list','img','../img/button_gray/add_filtered_list_off.png',
 * > '',620,60,24,24,1,'','','','mod_addWmsFromFilteredList_button.php',
 * > 'mod_addWMSgeneralFunctions.js,popup.js','treeGDE,mapframe1','loadData',
 * > 'http://www.mapbender.org/index.php/Add_WMS_from_filtered_list');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<app_id>', 'addWMSfromfilteredList_ajax', 'capabilitiesInput', 
 * > '1', 'load wms by capabilities url' ,'var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('<app_id>', 'addWMSfromfilteredList_ajax', 'option_dball',
 * > '1', '1 enables option "load all configured wms from db"' ,'var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value,
 * > context, var_type) VALUES('<app_id>', 'addWMSfromfilteredList_ajax', 'option_dbgroup',
 * > '0', '1 enables option "load configured wms by group"' ,'var');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value,
 * > context, var_type) VALUES('<app_id>', 'addWMSfromfilteredList_ajax', 'option_dbgui', 
 * > '0', '1 enables option "load configured wms by application"' ,'var');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES
 * > ('<app_id>', 'addWMSfromfilteredList_ajax', 'addwms_showWMS', '4', '' ,'var');
 *
 * Help:
 * http://www.mapbender.org/Add_WMS_from_filtered_list_%28AJAX%29
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 *
 * Parameters:
 * addwms_showWMS 		- x
 * capabilitiesInput - show input field to load wms by capabilities url
 * option_dball		- load all configured wms from db
 * option_dbgroup		- load configured wms by group
 * option_dbgui		- load configured wms by application
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");

$sql = "SELECT e_target FROM gui_element WHERE e_id = $1 AND fkey_gui_id = $2";
$v = array($e_id, $gui_id);
$t = array("s", "s");
$res = db_prep_query($sql, $v, $t);
$row = db_fetch_array($res);
$e_target = explode(",", ((string) $row["e_target"]));

$sql_css = "SELECT var_value FROM gui_element_vars WHERE var_name = 'jq_ui_theme' AND fkey_gui_id = $1";
$v_css = array($gui_id);
$t_css = array("s");
$res_css = db_prep_query($sql_css, $v_css, $t_css);
if ($res_css) {
	$row_css = db_fetch_array($res_css);
	$theme = $row_css["var_value"];
}
if (!$theme) {
	$theme = "../extensions/jquery-ui-1.8.1.custom/css/custom-theme/jquery-ui-1.8.5.custom.css";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<?php printf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\" />",CHARSET);	?>
	<title>Add WMS</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $theme;?>" />
	<script type='text/javascript' src='../extensions/jquery-ui-1.8.1.custom/js/jquery-1.4.2.min.js'></script>
	<script type='text/javascript' src='../extensions/jquery-ui-1.8.1.custom/js/jquery-ui-1.8.1.custom.min.js'></script>
	<script type="text/javascript">
	<?php
	include '../include/dyn_js.php';
	?>
	<!--
	// Set default for element variables if they are undefined
	option_dball      = (typeof(option_dball) !== 'undefined')      ? option_dball      : 1;
	option_dbgroup    = (typeof(option_dbgroup) !== 'undefined')    ? option_dbgroup    : 0;
	option_dbgui      = (typeof(option_dbgui) !== 'undefined')      ? option_dbgui      : 0;
	capabilitiesInput = (typeof(capabilitiesInput) !== 'undefined') ? capabilitiesInput : 1;
	gui_list          = (typeof(gui_list) !== 'undefined')          ? gui_list          : '';
	addwms_showWMS = 0;
	addwms_zoomToExtent = 0;

	var guis = gui_list.split(',');

	if(gui_list === '') {
		guis = [];
	}

	var global_source = 'capabilities';  // [capabilities,db]
	var phpUrl        = '../php/mod_addWMSfromfilteredList_server.php?<?php echo $urlParameters;?>';

	// Load service

	function mod_addWMSfromDB(gui_id,wms_id) {
		mod_addWMSById_load(gui_id,wms_id);
	}

	var loadWmsAndZoomCallback = function (opt) {
		if (typeof opt === "object" && opt.success) {

			var wmsId = parseInt(opt.wmsId, 10);
			var map = parent.getMapObjByName('<?php echo $e_target[1]; ?>');
			var wms = map.wms[map.wms.length - 1];

			if (wms === null) {
				opt.msg = "<?php echo _mb("An unknown error occured.");?>";
			}
			else {
				// activate
				if (typeof opt.visible === "number" && opt.visible === 1) {
					if (typeof addwms_showWMS === "number"
						&& addwms_showWMS > 0 && addwms_showWMS < wms.objLayer.length) {

						try {
							var msg = "<?php echo _mb("The map service contains too many layers. The layers of this service will not be activated");?>";

							$("<div></div>").text(msg).dialog();
						}
						catch (e) {
							new parent.Mb_warning(e.message + ". " + msg);
						}
					}
					else {
						var wmsId = wms.wms_id;
						parent.handleSelectedWms(map.elementName, wmsId, "visible", 1)
					}
				}

				if (typeof opt.zoomToExtent === "number" && opt.zoomToExtent === 1) {
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

							map.calculateExtent(new parent.Extent(bbox_minx, bbox_miny, bbox_maxx, bbox_maxy));
							map.setMapRequest();
							break;
						}
					}
				}
			}
		}
		loadWmsCallback(opt);

	};

	var loadWmsCallback = function (opt) {
		var msg = typeof opt.msg === "string" ? opt.msg : "";

		if (typeof opt !== "object" || !opt.success) {
			msg = '<?php echo _mb("An unknown error occured.") ?>';
		}
		else {
			var map = parent.getMapObjByName('<?php echo $e_target[1]; ?>');
			var wms = map.wms[map.wms.length - 1];

			if (wms !== null) {
				msg = '<?php echo _mb("The following Service has been added to the application") ?>' + ': ';
				msg += wms.wms_title;
			}
			else {
				msg = '<?php echo _mb("An unknown error occured.") ?>';
			}
		}
		$("<div></div>").text(msg).dialog();
	};


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

	function mod_addWmsfromURL(){
		var capabilities = document.getElementById('CapURL').value;
		var zoomToExtent = document.getElementById('addwms-zoom').checked ? 1 : 0;
		var showLayers = document.getElementById('addwms-show').checked ? 1 : 0;

		parent.mod_addWMS_load(capabilities, {
			zoomToExtent: zoomToExtent,
			visible: showLayers,
			callback: loadWmsAndZoomCallback
		});
	}

	// Retrieve data

	function setSource(sourceValue) {
		global_source = sourceValue;
	}

	function getGroups() {
		imageOn();
		parent.mb_ajax_json(phpUrl, {"command":"getGroups"}, function (json, status) {
			imageOff();
			displayGroups(json.group);
		});
	}

	function getGUIs() {
		imageOn();
		parent.mb_ajax_json(phpUrl, {"command":"getGUIs"}, function (json, status) {
			imageOff();
			displayGUIs(json.gui);
		});
	}

	function getWMSByGUI(guiId) {
		if(guiId=="")
			return getAllWMS();
		imageOn();
		parent.mb_ajax_json(phpUrl, {"command":"getWMSByGUI", "guiId":guiId}, function (json, status) {
			imageOff();
			displayWMS(json.wms, guiId);
		});
	}

	function getWMSByGroup(groupId) {
		imageOn();
		parent.mb_ajax_json(phpUrl, {"command":"getWMSByGroup", "groupId":groupId}, function (json, status) {
			imageOff();
			displayWMS(json.wms);
		});
	}

	function getAllWMS() {
		imageOn();
		parent.mb_ajax_json(phpUrl, {"command":"getAllWMS"}, function (json, status) {
			imageOff();
			displayWMS(json.wms);
		});
	}

	// -----------------  Display results --------------------

	function removeChildNodes(node) {
		while (node.childNodes.length > 0) {
			var childNode = node.firstChild;
			node.removeChild(childNode);
		}
	}

	function setTableHeader(text,titleLeft,titleRight) {
		document.getElementById('resultString').innerHTML = text;
		document.getElementById('titleLeft').innerHTML    = titleLeft;
		document.getElementById('titleRight').innerHTML   = titleRight;

		removeChildNodes(document.getElementById('resultTableBody'));
	}

	function addTableRow(leftText,rightText,onClick) {
		var resultTableBoy        = document.getElementById('resultTableBody');
		var leftTableCell         = document.createElement('td');
		var rightTableCell        = document.createElement('td');
		var leftTableCellContent  = document.createElement('strong');
		var rightTableCellContent = document.createElement('em');
		var tableRow              = document.createElement('tr');

		leftTableCellContent.innerHTML  = leftText;
		rightTableCellContent.innerHTML = rightText;

		leftTableCell.appendChild(leftTableCellContent);
		rightTableCell.appendChild(rightTableCellContent);
		tableRow.appendChild(leftTableCell);
		tableRow.appendChild(rightTableCell);

		tableRow.onclick = function () {
			eval(onClick);
		}

		if(resultTableBoy.childNodes.length % 2 !== 0) {
			tableRow.className += tableRow.className + ' alternate';
		}

		resultTableBoy.appendChild(tableRow);
	}

	function imageOn() {
		document.getElementById("progressIndicator").style.visibility = "visible";
		document.getElementById("progressIndicator").style.display = "block";
		document.getElementById("resultTable").style.visibility = "hidden";
		document.getElementById("resultTable").style.display = "none";
		document.getElementById("resultString").style.visibility = "hidden";
		document.getElementById("resultString").style.display = "none";
	}

	function imageOff() {
		document.getElementById("progressIndicator").style.visibility = "hidden";
		document.getElementById("progressIndicator").style.display = "none";
		document.getElementById("resultTable").style.visibility = "visible";
		document.getElementById("resultTable").style.display = "block";
		document.getElementById("resultString").style.visibility = "visible";
		document.getElementById("resultString").style.display = "block";
	}

	function noResult() {
		document.getElementById("resultTable").style.visibility = 'hidden';
		document.getElementById("resultString").innerHTML = noResultText;
	}

	function setDefaults () {
		if (typeof addwms_showWMS === "number" && addwms_showWMS > 0) {
			document.getElementById("addwms-show").checked = "checked";
		}
		if (typeof addwms_zoomToExtent === "number"
			&& addwms_zoomToExtent === 1) {

			document.getElementById("addwms-zoom").checked = "checked";
		}
	}

	function setButtons() {
		var containerCapabilities = document.getElementById('container_capabilities');
		var containerButtons      = document.getElementById('container_buttons');
		var optionButton          = false;

		// If only one is active load list imidiately
		if(
			parseInt(option_dbgui) +
			parseInt(option_dbgroup) +
			parseInt(option_dball)
		=== 1) {
			if(option_dball === 1){
				optionButton = document.getElementById('button_dbAll');
			}
			if(option_dbgroup === 1) {
				optionButton = document.getElementById('button_dbGroup');
			}
			if(option_dbgui === 1) {
				optionButton = document.getElementById('button_dbGui');
			}

			if(optionButton) {
				optionButton.onclick();
				containerButtons.parentNode.removeChild(containerButtons);

				return;
			}
		}

		if(option_dball === 0) {
			optionButton = document.getElementById('button_dbAll');
			optionButton.parentNode.removeChild(optionButton);
		}
		if(option_dbgroup === 0) {
			optionButton = document.getElementById('button_dbGroup');
			optionButton.parentNode.removeChild(optionButton);
		}
		if(option_dbgui === 0) {
			optionButton = document.getElementById('button_dbGui');
			optionButton.parentNode.removeChild(optionButton);
		}

		if(capabilitiesInput === 0) {
			optionButton = document.getElementById('capabilitiesForm');
			optionButton.parentNode.removeChild(optionButton);
			containerCapabilities.parentNode.removeChild(containerCapabilities);
		}
	}

	function displayGroups (groupArray) {
		if (groupArray.length > 0) {
			setTableHeader(selectGroupText, groupNameText, groupAbstractText);

			for (var i = 0; i < groupArray.length; i++) {
				var onClick = "getWMSByGroup('" + groupArray[i].id + "')";
				addTableRow(groupArray[i].name, groupArray[i].description, onClick);
			}
		}
		else {
			noResult();
		}
	}

	function displayGUIs (guiArray) {
		if (guiArray.length > 0) {
			setTableHeader(selectGuiText, guiNameText, guiAbstractText);

			for (var i = 0; i < guiArray.length; i++) {
				var onClick = "getWMSByGUI('" + guiArray[i].id + "')";
				if(guis.length>0){
					for(var j=0; j < guis.length; j++){
						if(guiArray[i].id==guis[j]){
							addTableRow(guiArray[i].name, guiArray[i].description, onClick);
							break;
						}
					}
				}
				else
					addTableRow(guiArray[i].name, guiArray[i].description, onClick);
			}
		}
		else {
			noResult();
		}
	}

	function displayWMS (wmsArray, guiId) {
		if (wmsArray.length > 0) {
			setTableHeader(selectWmsText, wmsNameText, wmsAbstractText);

			for (var i = 0; i < wmsArray.length; i++) {

				if (global_source == "db" && typeof(guiId) !== "undefined" ) {
					var onClick = "parent.mod_addWMSById_ajax(" +
						"'" + guiId + "', '" + wmsArray[i].id + "', {" +
							"callback: function (opt) {" +
								"if (typeof opt === 'object' && opt.success) {" +
									"var wmsId = parseInt(opt.wmsId, 10);" +
									"var map = parent.getMapObjByName('<?php echo $e_target[1]; ?>');" +
									"parent.handleSelectedWms(map.elementName, wmsId, 'visible', 0);" +
									"parent.handleSelectedWms(map.elementName, wmsId, 'querylayer', 0);" +
								"}" +
								"loadWmsAndZoomCallback(opt);}});";
				}
				else {
					var onClick = "mod_addWMSfromfilteredList(\"" +
					wmsArray[i].getCapabilitiesUrl + "\",\"" +
					wmsArray[i].version+"\", {" +
					"zoomToExtent: 0, " +
					"visible: 0, " +
					"callback: loadWmsAndZoomCallback" +
					"});";
				}
				addTableRow(wmsArray[i].title, wmsArray[i].abstract, onClick);
			}
		}
		else {
			noResult();
		}
	}
	-->
	</script>
	<?php include("../include/dyn_css.php"); ?>
	<script type="text/javascript">
	var wmsNameText = '<?php echo _mb("WMS name");?>';
	var wmsAbstractText = '<?php echo _mb("WMS abstract");?>';
	var selectWmsText = '<?php echo _mb("Please select a WMS") . ":";?>';
	var selectGuiText = '<?php echo _mb("Please select an application") . ":";?>';
	var selectGroupText = '<?php echo _mb("Please select a group") . ":";?>';
	var groupAbstractText = '<?php echo _mb("group abstract");?>';
	var groupNameText = '<?php echo _mb("group name");?>';
	var guiAbstractText = '<?php echo _mb("application abstract");?>';
	var guiNameText = '<?php echo _mb("application name");?>';
	var noResultText = '<?php echo _mb("no result");?>';
	</script>
	<link rel="stylesheet" type="text/css" href="../css/addwms.css"/>
</head>

<body onLoad="setButtons();setDefaults();">
<h1><?php echo _mb("Add WMS"); ?></h1>
<p><?php echo _mb("Enter a Capabilities-URL of a WMS or select one or more WMS from list."); ?></p>
<p><em><?php echo _mb("Notice: Be aware of the scale hints. Possibly you need to zoom in to display the added service."); ?></em></p>

<form id="capabilitiesForm" name="addURLForm" method="post" action="">
<fieldset id="container_capabilities">
<legend>Capabilities</legend>
	<p>
		<label for="CapURL"><?php echo _mb("Capabilities-URL"); ?>:</label>
		<input type="text" id="CapURL" name="CapURL" />
		<input type="button" id="addCapURL" name="addCapURL" value="<?php echo _mb("Add WMS"); ?>" onclick="mod_addWmsfromURL();" /><br />
	</p>
	<p style="padding:0 0 4px 0;">
		<input style="margin-left: 12em;" type="checkbox" id="addwms-show" />
		<label style='display:inline;float:none;cursor:pointer' for="addwms-show"><?php echo _mb("activate layers"); ?></label>
	</p>
	<p style="padding:0 0 4px 0;">
		<input style="margin-left: 12em;" type="checkbox" id="addwms-zoom" />
		<label style='display:inline;float:none;cursor:pointer' for="addwms-zoom"><?php echo _mb("zoom to extent"); ?></label>
	</p>
</fieldset>
</form>

<form id="addWMSForm" name="addWMSForm" method="post" action="">
<fieldset id="container_buttons">
<legend>WMS list(s)</legend>
	<p>
		<input type="button" name="button_dbAll"   id="button_dbAll"   value="<?php echo _mb("List all WMS"); ?>" onclick="setSource('db');getWMSByGUI(gui_list)">
		<input type="button" name="button_dbGroup" id="button_dbGroup" value="<?php echo _mb("List WMS by Group"); ?>"   onclick="setSource('db');getGroups()">
		<input type="button" name="button_dbGui"   id="button_dbGui"   value="<?php echo _mb("List WMS by Application"); ?>"     onclick="setSource('db');getGUIs()">
	</p>
</fieldset>
</form>

<p id="progressIndicator" name="progressIndicator">
	<img src="../img/indicator_wheel.gif" />
	<?php echo _mb("Loading"); ?> ...
</p>

<h2 id="resultString" name="resultString"></h2>

<table id="resultTable" name="resultTable">
	<thead>
		<tr>
			<th id="titleLeft" name="titleLeft"></th>
			<th id="titleRight" name="titleRight"></th>
		</tr>
	</thead>
	<tbody id="resultTableBody" name="resultTableBody">
	</tbody>
</table>
</body>

</html>
