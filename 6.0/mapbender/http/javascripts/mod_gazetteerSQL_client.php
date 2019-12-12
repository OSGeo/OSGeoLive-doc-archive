<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title>Gazetteer</title>
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">

var targetFrameArray = [];
<?php
echo "var e_id = '". $e_id . "';";
for ($i = 0; $i < count($e_target); $i++) {
	echo "targetFrameArray.push('".$e_target[$i]."');";
}
?>
<!--
// --- begin: expected element vars ---
// var scale 
// var numberOfResults
// var profile = "adresse" | "alk" | "alb" | "adresse2"

if (typeof(scale) == 'undefined') {
	var scale = 2000;
	var e = new parent.Mb_warning("mod_gazetteerSQL: element var scale is missing.");
}
if (typeof(numberOfResults) == 'undefined') {
	var numberOfResults = 0;
	var e = new parent.Mb_warning("mod_gazetteerSQL: element var numberOfResults is missing.");
}
if (typeof(tooManyResultsString) == 'undefined') {
	var tooManyResultsString = "Too many results. Please specify your query.";
	var e = new parent.Mb_warning("mod_gazetteerSQL: element var tooManyResultsString is missing.");
}
if (typeof(profile) == 'undefined' || (profile != "alb" && profile != "alk" && profile != "adresse" && profile != "adresse2")) {
	profile = "adresse"
	var e = new parent.Mb_exception("mod_gazetteerSQL: element var profile is missing.");
}

// --- end: expected element vars ---


var generalPreFunctions = [];
var generalSubFunctions = [];
var onloadSubFunctions = [];
var communesSubFunctions = [];
var streetSubFunctions = [];
var numberSubFunctions = [];
var districtSubFunctions = [];
var parcelSubFunctions = [];
var ownerSubFunctions = [];

registerFunction(generalPreFunctions, "disableForm();");
registerFunction(generalSubFunctions, "enableForm();");
if (profile == "alb") {
	registerFunction(onloadSubFunctions, "updateCommunes();");
	registerFunction(communesSubFunctions, "updateOwner();");
	registerFunction(ownerSubFunctions, "updateOwner();");
}
else if (profile == "alk") {
	registerFunction(onloadSubFunctions, "updateCommunes();");
	registerFunction(communesSubFunctions, "updateDistricts();");
	registerFunction(parcelSubFunctions, "updateParcels();");
}
else if (profile == "adresse") {
	registerFunction(onloadSubFunctions, "updateCommunes();");
	registerFunction(communesSubFunctions, "updateStreets();");
	registerFunction(streetSubFunctions, "updateNumbers();");
}
else if (profile == "adresse2") {
	registerFunction(onloadSubFunctions, "updateStreets();");
	registerFunction(streetSubFunctions, "updateNumbers();");
}

function executeFunctions(arrayOfFunctionStrings) {
	for (var i = 0; i < arrayOfFunctionStrings.length; i++) {
		eval(arrayOfFunctionStrings[i]);
	}
}

function registerFunction(functionStringArray, functionString) {
	functionStringArray.push(functionString);
}

function disableForm() {
	document.getElementById('selectCommune').disabled = true;
	document.getElementById('selectStreet').disabled = true;
	document.getElementById('selectDistrict').disabled = true;
	document.getElementById('inputParcel1').disabled = true;
	document.getElementById('inputParcel2').disabled = true;
	document.getElementById('inputParcelButton').disabled = true;
	document.getElementById('inputOwner').disabled = true;
	document.getElementById('inputOwnerButton').disabled = true;
	document.getElementById("divResults").innerHTML = searchImage;
}

function enableForm() {
	document.getElementById('selectCommune').removeAttribute("disabled");
	document.getElementById('selectStreet').removeAttribute("disabled");
	document.getElementById('selectDistrict').removeAttribute("disabled");
	document.getElementById('inputParcel1').removeAttribute("disabled");
	document.getElementById('inputParcel2').removeAttribute("disabled");
	document.getElementById('inputParcelButton').removeAttribute("disabled");
	document.getElementById('inputOwner').removeAttribute("disabled");
	document.getElementById('inputOwnerButton').removeAttribute("disabled");
	document.getElementById("divResults").innerHTML = "";
}

var highlight;
var houseLocation;
var parcelLocation;
var searchImage = "<table><tr><td><img src='../img/indicator_wheel.gif'></td><td>Searching...</td></tr></table>";
var phpUrl = "../php/mod_gazetteerSQL_server.php";

parent.mb_registerInitFunctions("window.frames['"+e_id+"'].initHighlight()");
parent.mb_registerInitFunctions("window.frames['"+e_id+"'].executeFunctions(window.frames['"+e_id+"'].onloadSubFunctions)");


// - BEGIN -------- HIGHLIGHTING AND ZOOMING ------------------------------------------

function zoomToLocation(aPoint) {
	parent.mb_repaintScale(targetFrameArray[0], aPoint.x, aPoint.y, scale)
}

function initHighlight() {
	var generalHighlightZIndex = 100;
	var generalHighlightLineWidth = 3;
	var styleObj = {"position":"absolute", "top":"0px", "left":"0px", "z-index":generalHighlightZIndex};
	highlight = new parent.Highlight(targetFrameArray, e_id, styleObj, generalHighlightLineWidth);
}

function zoomToHouseNumber(houseNumber) {
	zoomToLocation(houseLocation[houseNumber]);
	removeHighlight();
	highlightHouseNumber(houseNumber);
}

function zoomToParcel(parcelId) {
	zoomToLocation(parcelLocation[parcelId]);
	removeHighlight();
	highlightParcel(parcelId);
}

function highlightHouseNumber(houseNumber) {
	var mG = new parent.MultiGeometry(parent.geomType.point);
	mG.addGeometry();
	mG.get(-1).addPoint(houseLocation[houseNumber]);
	highlight.add(mG);
	highlight.paint();
}

function highlightParcel(parcelId) {
	var mG = new parent.MultiGeometry(parent.geomType.point);
	mG.addGeometry();
	mG.get(-1).addPoint(parcelLocation[parcelId]);
	highlight.add(mG);
	highlight.paint();
}

function removeHighlight() {
	highlight.clean();
}

// - END -------- HIGHLIGHTING AND ZOOMING ------------------------------------------



function removeChildNodes(node) {
	while (node.childNodes.length > 0) {
		var childNode = node.firstChild;
		node.removeChild(childNode);
	}
}

function getSize(result) {
	if (typeof(result) == "array") {
		return result.length;
	}
	else if (typeof(result) == "object") {
		var c = 0;
		for (var attr in result) {
			c++;
		}
		return c;
	}
	return 1;
}

function updateCommunes() {
	executeFunctions(generalPreFunctions);
	parent.mb_ajax_json(phpUrl, {"command":"getCommunes"}, function (json, status) {
		executeFunctions(generalSubFunctions);

		removeChildNodes(document.getElementById('selectCommune'));

		for (var communeId in json.communes) {
			if (typeof(json.communes[communeId]) != 'function') {
				var currentNode = document.createElement("option");
			
				if (document.getElementById('selectCommune').childNodes.length == 0) {
					currentNode.selected = "selected";
				}
				currentNode.value = communeId;
				currentNode.innerHTML = json.communes[communeId];
				document.getElementById('selectCommune').appendChild(currentNode);
			}
		}
		executeFunctions(communesSubFunctions);
	});
}

function updateStreets() {
	executeFunctions(generalPreFunctions);
	var communeId = document.getElementById('selectCommune').value;
	
	parent.mb_ajax_json(phpUrl, {"command":"getStreets", "communeId":communeId}, function (json, status) {
		executeFunctions(generalSubFunctions);

		removeChildNodes(document.getElementById('selectStreet'));

		for (var streetId in json.streets) {
			if (typeof(json.streets[streetId]) != 'function') {
				var currentNode = document.createElement("option");
	
				if (document.getElementById('selectStreet').childNodes.length == 0) {
					currentNode.selected = "selected";
				}
				
				currentNode.value = json.streets[streetId];
				currentNode.innerHTML = json.streets[streetId];
				document.getElementById('selectStreet').appendChild(currentNode);
			}
		}
		executeFunctions(streetSubFunctions);
	});
}

function updateDistricts() {
	executeFunctions(generalPreFunctions);

	var communeId = document.getElementById('selectCommune').value;
	
	parent.mb_ajax_json(phpUrl, {"command":"getDistricts", "communeId":communeId}, function (districtObject, status) {
		executeFunctions(generalSubFunctions);

		removeChildNodes(document.getElementById('selectDistrict'));

		for (var districtId in districtObject.districts) {
			if (typeof(districtObject.districts[districtId]) != 'function') {
				var currentNode = document.createElement("option");
	
				currentNode.value = districtId;
	
				if (document.getElementById('selectDistrict').childNodes.length == 0) {
					currentNode.selected = "selected";
				}
				
				currentNode.value = districtObject.districts[districtId];
				currentNode.innerHTML = districtObject.districts[districtId];
				document.getElementById('selectDistrict').appendChild(currentNode);
			}
		}
		executeFunctions(districtSubFunctions);
	});
}

function updateNumbers() {
	executeFunctions(generalPreFunctions);

	var streetName = document.getElementById('selectStreet').value;
	var communeId = document.getElementById('selectCommune').value;

	parent.mb_ajax_json(phpUrl, {"command":"getNumbers", "communeId":communeId, "streetName":streetName, "numberOfResults":numberOfResults}, function (json, status) {
		executeFunctions(generalSubFunctions);
		houseLocation = {};
		var resultString = "";
		if (getSize(json.houseNumbers) > 0) {
			if (json.limited === true) {
				resultString += tooManyResultsString;
			}
			for (var houseNumber in json.houseNumbers) {
				if (typeof(json.houseNumbers[houseNumber]) != 'function') {
					houseLocation[houseNumber] = new parent.Point(json.houseNumbers[houseNumber].x, json.houseNumbers[houseNumber].y);
					resultString += "<b style=\"cursor:pointer\" onclick=\"zoomToHouseNumber('"+houseNumber+"')\" onmouseover=\"highlightHouseNumber('"+houseNumber+"')\" onmouseout=\"removeHighlight()\">"+houseNumber+"</b>&nbsp;&nbsp; ";
				}
			}
		}
		else {
			resultString += noResultsString;
		}
		document.getElementById("divResults").innerHTML = resultString;
		executeFunctions(numberSubFunctions);
	});
}

function updateParcels() {
	executeFunctions(generalPreFunctions);
	
	var districtId = document.getElementById('selectDistrict').value;
	var inputParcel1 = document.getElementById('inputParcel1').value;
	var inputParcel2 = document.getElementById('inputParcel2').value;


	parent.mb_ajax_json(phpUrl, {"command":"getLandparcelsByDistrict", "districtId":districtId, "parcelNumber1":inputParcel1, "parcelNumber2":inputParcel2, "numberOfResults":numberOfResults}, function (json, status) {
		executeFunctions(generalSubFunctions);

		parcelLocation = {};
		var resultString = "";
		if (getSize(json.landparcels) > 0) {
			if (json.limited === true) {
				resultString += tooManyResultsString;
			}
			resultString += "<ol>";
			for (var parcelId in json.landparcels) {
				if (typeof(json.landparcels[parcelId]) != 'function') {
					parcelLocation[parcelId] = new parent.Point(json.landparcels[parcelId].x, json.landparcels[parcelId].y);
					resultString += "<li style=\"cursor:pointer\" onclick=\"zoomToParcel('"+parcelId+"')\" onmouseover=\"highlightParcel('"+parcelId+"')\" onmouseout=\"removeHighlight()\">"+parcelId+"</li>";
				}
			}
			resultString += "</ol>";
		}
		else {
			resultString += noResultsString;
		}
		document.getElementById("divResults").innerHTML = resultString;
		executeFunctions(numberSubFunctions);
	});
}

function updateOwner() {
	var ownerQueryString = document.getElementById('inputOwner').value;
	var communeId = document.getElementById('selectCommune').value;

	document.getElementById("divResults").innerHTML = "";
	document.getElementById('selectCommune').removeAttribute("disabled");
	document.getElementById('inputOwner').removeAttribute("disabled");
	document.getElementById('inputOwnerButton').removeAttribute("disabled");
	
	if (ownerQueryString != "") {
		executeFunctions(generalPreFunctions);
		parent.mb_ajax_json(phpUrl, {"command":"getLandparcelsByOwner", "communeId":communeId, "ownerQueryString":ownerQueryString, "numberOfResults":numberOfResults}, function (json, status) {
			executeFunctions(generalSubFunctions);

			parcelLocation = {};
			var resultString = "";
			if (getSize(json.landparcels) > 0) {
				if (json.limited === true) {
					resultString += tooManyResultsString;
				}
				resultString += "<ol>";
				for (var i=0; i < json.landparcels.length; i++) {
					var parcelId = json.landparcels[i].landparcelId;
					parcelLocation[parcelId] = new parent.Point(json.landparcels[i].x, json.landparcels[i].y);
					resultString += "<li style=\"cursor:pointer\" onclick=\"zoomToParcel('"+parcelId+"')\" onmouseover=\"highlightParcel('"+parcelId+"')\" onmouseout=\"removeHighlight()\">"+json.landparcels[i].owner+ " (" + parcelId+")</li>";
				}
				resultString += "</ol>";
			}
			else {
				resultString += noResultsString;
			}
			document.getElementById("divResults").innerHTML = resultString;
			executeFunctions(numberSubFunctions);
	
		});
	}
}
// -->
</script>
</head>
<body>
<form>
<select class='selectCommune' id='selectCommune' onchange='executeFunctions(communesSubFunctions)'></select>
<select class='selectStreet' id='selectStreet' onchange='executeFunctions(streetSubFunctions);' size=5 disabled></select>
<select class='selectDistrict' id='selectDistrict' onchange='executeFunctions(districtSubFunctions);' size=5 disabled></select>
<div id='divParcel' class='divParcel'>
Flur: <input type='input' class='inputParcel1' id='inputParcel1' disabled></select>
Flstz: <input type='input' class='inputParcel2' id='inputParcel2' disabled></select>
<input type='button' id='inputParcelButton' value='?' onclick='executeFunctions(parcelSubFunctions);'>
</div>
<div id='divOwner' class='divOwner'>
Eigent&uuml;mer: <input type='input' class='inputOwner' id='inputOwner' disabled></select>
<input type='button' id='inputOwnerButton' value='?' onclick='executeFunctions(ownerSubFunctions);'>
</div>
</form>
<div class='divResults' id='divResults'></div>
</body>
</html>