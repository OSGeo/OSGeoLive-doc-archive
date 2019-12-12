<?php 
# $Id: mod_wfs_gazetteer_client.php 1414 2008-01-17 08:55:06Z diewald $
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
$e_target = $_REQUEST["e_target"];
$e_id_css = $_REQUEST["e_id_css"];

$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db($DB,$con);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='<?php echo CHARSET;?>'">	
<title>mod_wfs_gazetteer</title>

<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
<?php 
	echo "var targetString = '" . $e_target . "';";
	echo "var wfsConfIdString = '" . $wfsConfIdString . "';";
?>

// Element var maxHighlightedPoints
try{
	if (maxHighlightedPoints){
		maxHighlightedPoints = Number(maxHighlightedPoints);

		if (isNaN(maxHighlightedPoints)) {
			var e = new parent.Mb_warning("mod_wfs_gazetteer_client.php: Element var maxHighlightedPoints must be a number.");
		}
	}
}
catch(e){
	maxHighlightedPoints = 0;
	var e = new parent.Mb_warning("mod_wfs_gazetteer_client.php: Element var maxHighlightedPoints is not set, see 'edit element vars'.");
}


// Helper functions
/**
 * Deletes all nodes under a specified node. 
 */
function removeChildNodes(node) {
	try {
		while (node.childNodes.length > 0) {
			var childNode = node.firstChild;
			node.removeChild(childNode);
		}
	}
	catch(e) {
//		console.log(e);
	}
}

/**
 * removes whitespaces and endlines before and after a string
 */ 
function trimString (str) {
	return str.replace(/^\s+|\s+|\n+$/g, '');
}

// ----------------------------

var targetArray = targetString.split(",");
var global_wfsConfObj;
var global_selectedWfsConfId;
var searchPopup;

// removed because initWfs is done on load wmc, which itself is done on init
//parent.mb_registerInitFunctions("window.frames['"+this.name+"'].initModWfsGazetteer()");
parent.mb_registerloadWmcSubFunctions("window.frames['"+this.name+"'].appendWfsConf(restoredWmcExtensionData.wfsConfIdString);window.frames['"+this.name+"'].initModWfsGazetteer()");

function openwindow(Adresse) {
	Fenster1 = window.open(Adresse, "Informationen", "width=500,height=500,left=100,top=100,scrollbars=yes,resizable=no");
	Fenster1.focus();
}

function appendWfsConf(newWfsConfIdString) {
	// merge with existing wfs conf ids
	if (wfsConfIdString !== "" && typeof(wfsConfIdString) != "undefined") {
		if (newWfsConfIdString !== "" && typeof(newWfsConfIdString) != "undefined") {
			wfsConfIdString += "," + newWfsConfIdString;
		}
	}
	else {
		if (newWfsConfIdString !== "" && typeof(newWfsConfIdString) != "undefined") {
			wfsConfIdString = newWfsConfIdString;
		}
		else {
			wfsConfIdString = "";
		}
	}
}

function fixConfString() {
	if (wfsConfIdString && typeof(wfsConfIdString) == "string") {
		var confIdArray = wfsConfIdString.split(",");
		var newIdArray = [];
		for (var i = 0; i < confIdArray.length; i++) {
			var current = parseInt(confIdArray[i]);
			if (!isNaN(current) && typeof(current) == "number") {
				var found = false;
				for (var j = 0; j < newIdArray.length && !found; j++) {
					if (current == newIdArray[j]) {
						found = true;
					}
				}
				if (!found) {
					newIdArray.push(current);
				}
			}
		}
		wfsConfIdString = newIdArray.join(",");
	}
	else {
		wfsConfIdString = "";
	}
}
function initModWfsGazetteer() {
	fixConfString();
	deleteWfsInfo()

	// delete WFS conf select box entries
	removeChildNodes(document.getElementById("wfs_conf_sel"));
	removeChildNodes(document.getElementById("wfs_messages"));
	
	var url = "../geoportal/mod_wfsGazetteerEditor_server.php";
	var properties = {command:"getWfsConf", wfsConfIdString:wfsConfIdString};

	parent.mb_ajax_json(url , properties, function(json, status) {
		global_wfsConfObj = json;
		var wfsCount = 0;
		for (var wfsConfId in global_wfsConfObj) {
			global_selectedWfsConfId = wfsConfId; 
			if (typeof(global_wfsConfObj[wfsConfId] != 'function')) {
				wfsCount++;
			}
		}
		// If no WFS is available, display an error message...
		if (wfsCount === 0) {
			
			var msges = document.getElementById("wfs_messages");
			var textNode = document.createTextNode("Kein WFS verfügbar. Über die Suche können Sie Dienste hinzuladen.");
			msges.appendChild(textNode);			

			var selectbox = document.getElementById("wfs_conf_sel");
			selectbox.style.display = "none";

			hideWfsInfo();
			
			var e = parent.Mb_exception("no wfs conf id available.");
			
		}
		// ...else, display a Select Box with available WFS.
		else {
			appendWfsConfSelectBox();
			setWfsInfo();	
		}
		parent.mb_setWmcExtensionData({"wfsConfIdString":wfsConfIdString});
	});
}

function deleteWfsInfo () {
	// delete WFS conf info
	removeChildNodes(document.getElementById("wfsInfo"));
}

function hideWfsInfo () {
	var wfsPreConfiguredOrEditor = document.getElementById("wfsPreConfiguredOrEditor");
	wfsPreConfiguredOrEditor.style.display = 'none';
	var wfsGeomTypeNode = document.getElementById("wfsGeomType");
	wfsGeomTypeNode.style.display = "none";
	var wfsInfoNode = document.getElementById("wfsInfo");
	wfsInfoNode.style.display = "none";
	var wfsRemoveNode = document.getElementById("wfsRemove");
	wfsRemoveNode.style.display = "none";
	var wfsSubmitNode = document.getElementById("wfsSubmit");
	wfsSubmitNode.style.display = "none";
	return;	
}

function setWfsInfo() {
	//
	// append bulb image
	//
	var bulbNode = document.getElementById("wfsInfo"); 	
	removeChildNodes(bulbNode);
	var imgNode = document.createElement("img");
	imgNode.id = "wfsInfoImg";
	imgNode.src = "../geoportal/img/info.png";
	imgNode.border = 0;
	bulbNode.appendChild(imgNode);
	bulbNode.href = "javascript:openwindow('../geoportal/mod_featuretypeMetadata.php?wfs_conf_id=" + global_selectedWfsConfId.toString() + "');";
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
		wfsPreConfiguredOrEditor.src = "../geoportal/img/modul_suche.png";
		wfsPreConfiguredOrEditor.title = "Modultyp: Suche";
	}
	else {
		wfsPreConfiguredOrEditor.src = "../geoportal/img/modul_download.png";
		wfsPreConfiguredOrEditor.title = "Modultyp: Download";
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
		wfsGeomTypeNode.src = "../geoportal/img/wfs_pkt.gif";
		wfsGeomTypeNode.style.display = 'inline';
		wfsGeomTypeNode.title = 'Geometrietyp: Punkt';
	}
	else if (wfsGeomType.match(/Line/)) {
		wfsGeomTypeNode.src = "../geoportal/img/wfs_l.gif";
		wfsGeomTypeNode.style.display = 'inline';
		wfsGeomTypeNode.title = 'Geometrietyp: Linie';
	}
	else if (wfsGeomType.match(/Polygon/)) {
		wfsGeomTypeNode.src = "../geoportal/img/wfs_p.gif";
		wfsGeomTypeNode.style.display = 'inline';
		wfsGeomTypeNode.title = 'Geometrietyp: Fläche';
	}
	else {
		var e = new parent.Mb_exception("WFS gazetteer: geometry type unknown.");		
	}
	
	//
	// set image: remove this WFS
	//
	var wfsRemoveNode = document.getElementById("wfsRemove");
	wfsRemoveNode.src = "../geoportal/img/modul_loeschen.png";
	wfsRemoveNode.style.display = 'inline';
	wfsRemoveNode.onclick = function () {
		delete global_wfsConfObj[global_selectedWfsConfId];
		setWfsConfIdString();
		initModWfsGazetteer();			
		parent.mb_setWmcExtensionData({"wfsConfIdString":wfsConfIdString});
	}
	
	var wfsSubmitNode = document.getElementById("wfsSubmit");
	wfsSubmitNode.style.display = "display";

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
	var selectNode = document.getElementById("wfs_conf_sel");
	selectNode.style.display = "block";
	selectNode.onchange = function () {
		global_selectedWfsConfId = this.value;
		setWfsInfo();
	};
	
	var isSelected = false;
	for (var wfsConfId in global_wfsConfObj) {
		var optionNode = document.createElement("option");
		
		optionNode.value = wfsConfId;
		optionNode.innerHTML = global_wfsConfObj[wfsConfId].g_label;

		if (!isSelected) {
			optionNode.selected = true;
			isSelected = true;
			global_selectedWfsConfId = wfsConfId;
		}
		selectNode.appendChild(optionNode);
	}
}

function displayPopup(){
	var e_id = "<?php echo $e_id_css; ?>";
	var url = "../geoportal/mod_wfsGazetteerEditor_client.php?" +
		"e_target=<?php echo $e_target; ?>" +
		"&e_id_css=" + e_id;

	if (typeof(searchPopup) == "undefined") {
		searchPopup = new parent.mb_popup({title:global_wfsConfObj[global_selectedWfsConfId].g_label,
			url:url, width:430, height:400, top:50, left:50, frameName:e_id + "_",
			minTop:"document",minLeft:"document",maxRight:"document",maxBottom:"document",destroy:false});
	}
	else{
		searchPopup.setUrl(url);
		searchPopup.setTitle(global_wfsConfObj[global_selectedWfsConfId].g_label);
	}
	searchPopup.show();
}

function openWfsEditor() {
	displayPopup();
	return false;
}

</script>
</head>
<body leftmargin='0' topmargin='10'  bgcolor='#ffffff'>

	<!-- WFS conf selector -->
	<form name='selectWfsConfForm' id='selectWfsConfForm' onsubmit='return openWfsEditor();'>
		
		<select id='wfs_conf_sel' name='wfs_conf_sel' style='display:none'>
		</select>

		<!-- WFS conf info -->
		<img src = "" name='wfsPreConfiguredOrEditor' id='wfsPreConfiguredOrEditor' style='display:none'>
		<img src = "" name='wfsGeomType' id='wfsGeomType' style='display:none'>
		<img src = "" title='Modul l&ouml;schen' name='wfsRemove' id='wfsRemove' style='display:none'>
		<a name='wfsInfo' title='Informationen anzeigen' id='wfsInfo' style='display:none'></a>

		<!-- opens the WFS editor -->
		<br>
		<input id='wfsSubmit' type='submit' value='Modul laden'>
	</form>
	<p id = "wfs_messages">
	</p>
</body>
</html>
