var Point = Mapbender.Point;

function init(){
	new Mb_warning("deprecated: old body onload init! Remove onload='init()' from the body element attributes.");
}

/**
 * @deprecated, use $("#module_id:maps").mapbender()
 */
var mb_mapObj = [];


var clickX;
var clickY;

// transparent GIF
var mb_trans = new Image(); 
mb_trans.src = "../img/transparent.gif";

/*
 * get the conjunction character of an URL
 * @param {String} onlineresource
 * @return the character & or ?
 * @type String
 */
var mb_getConjunctionCharacter = Mapbender.getConjunctionChar;


function mb_showHighlight(frameName,x,y){
	var map = getMapObjByName(frameName);
	if (map !== null) {
		var p = map.convertRealToPixel(new Point(x, y));
		
		var map_el = map.getDomElement();
		var $highlight = $("#" + map.elementName + "_highlight");
		if($highlight.size() === 0) {
			//create Box Elements
			$highlight = $("<div id='" + map.elementName+ "_highlight' style='position:absolute;top:-10px;left:-10px;width:14px;height:14px;z-index:100;visibility:visible'><img src='../img/redball.gif'/></div>");
			$(map_el).append($highlight);
		}
		$highlight.css("visibility", "visible");
	}
	mb_arrangeElement(map.frameName, map.elementName+ "_highlight" ,p.x-7, p.y-7);
}

function mb_hideHighlight(frameName) {
	var map = getMapObjByName(frameName);
	if (map !== null) {
		var map_el = map.getDomElement();
		mb_arrangeElement(map.frameName, map.elementName + "_highlight", -20, -20);
		$(map.elementName + "_highlight").css("visibility", "hidden");
	}
}

var cloneObject = Mapbender.cloneObject;

/**
 * @deprecated 
 * @param {Object} e
 * @param {Object} fName
 */
function mb_getMousePos(e,fName){

	var warning = new Mb_warning("The function mb_getMousePos is deprecated, use the map objects getMousePosition.");

	if(fName){
		if(ie){
			clickX = window.frames[fName].event.clientX;
			clickY = window.frames[fName].event.clientY;
		}
		else{
			clickX = e.pageX;
			clickY = e.pageY;
		}
	}
	else{
		if(ie){
			clickX = event.clientX;
			clickY = event.clientY;
		}
		else{
			clickX = e.pageX;
			clickY = e.pageY;
		}
	}
	var pos = [clickX,clickY];
	return pos;
}

function mb_arrangeElement(frameName, elName, left, top) {
	var el;
	if (typeof elName === "string") {
		if (frameName) {
			el = window.frames[frameName].document.getElementById(elName);
		}
		else {
			el = document.getElementById(elName);
		}
	}
	else if (typeof elName === "object") {
		el = elName;
	}
	else {
		return;
	}
	if (el !== null) {
		el.style.position = "absolute";
		el.style.top = String(top) + "px";
		el.style.left = String(left) + "px";
	}
}

var ie = $.browser.msie;
var n6 = document.getElementById&&!document.all?1:0;
var n4 = document.layers?1:0;

var mb_log = Mapbender.log;

var mb_timestamp = Mapbender.getTimestamp;


// Should be moved to a deprecated file
var Mb_notice = Mapbender.Notice;
var Mb_warning = Mapbender.Warning;
var Mb_exception = Mapbender.Exception;

var Extent = Mapbender.Extent;

/**
 * A wrapper for an AJAX request via GET 
 * 
 * @deprecated
 * @param {String} url the URL of a (presumably a server side) script.
 * @param {Object} param An object containing parameters, f.e. {name1:value1, name2:value2}
 * @param {Function} callback A function that is called when the server side script has been processed. The function is called with two parameters, result and status. Result is the output of the server side script (XML, HTML, whatever), status is a {String}, either "success" or "error". 
 */
function mb_ajax_get(url, param, callback) {
	try {
//		$.ajaxSetup({async:false}); 
		$.get(url, param, callback);
	}
	catch(e) {
		var error = new Mb_exception('map.php: mb_ajax_get:'+e);
	}
}	

/**
 * A wrapper for an AJAX request via POST 
 *
 * @deprecated
 * @param {String} url the URL of a (presumably a server side) script.
 * @param {Object} param An object containing parameters, f.e. {name1:value1, name2:value2}
 * @param {Function} callback A function that is called when the server side script has been processed. The function is called with two parameters, result and status. Result is the output of the server side script (XML, HTML, whatever), status is a {String}, either "success" or "error". 
 */
function mb_ajax_post(url, param, callback) {
	try {
//		$.ajaxSetup({async:false}); 
		$.post(url, param, callback);
	}
	catch(e) {
		var error = new Mb_exception('map.php: mb_ajax_post:'+e);
	}
}	
	
/**
 * A wrapper for an AJAX request via GET 
 *
 * @deprecated
 * @param {String} url the URL of a (presumably a server side) script.
 * @param {Object} param An object containing parameters, f.e. {name1:value1, name2:value2}
 * @param {Function} callback A function that is called when the server side script has been processed. The function is called with two parameters, result and status. Result is the output of the server side script (a JavaScript Object, not a String!), status is a {String}, either "success" or "error". 
 */
function mb_ajax_json(url, param, callback) {
	try {
//		window.frames['ajax'].$.ajaxSetup({async:false}); //TODO: find out why async doesn't work sometimes
		$.getJSON(url, param, callback);
	}
	catch(e) {
		var error = new Mb_exception('map.php: mb_ajax_json:'+e);
	}
}

/**
 * @ignore
 */
function mapToReal(frameName, aPoint) {
	var ind = getMapObjIndexByName(frameName);
	return mb_mapObj[ind].convertPixelToReal(aPoint);
}
/**
 * @ignore
 */
function realToMap(frameName, aPoint) {
	var ind = getMapObjIndexByName(frameName);
	return mb_mapObj[ind].convertRealToPixel(aPoint);
}

/**
 * @ignore
 */
function roundToDigits(aFloat, numberOfDigits) {
	return Math.round(aFloat*Math.pow(10, parseInt(numberOfDigits, 10)))/Math.pow(10, parseInt(numberOfDigits, 10));
}


/**
 * creates a style tag in the head of the document.
 *
 */
var StyleTag = function() {

	/**
	 * Creates the style tag in the head of the document. Something like a constructor. 
	 *
	 */
	var createStyleTag = function() {
		// TODO: Internet Explorer routine seems a little buggy
		if ($.browser.msie) {
			// create a Style Sheet object (IE only)
			//styleSheetObj=document.createStyleSheet();
			// get the DOM node of the style sheet object, set the type
			//styleObj=styleSheetObj.owningElement || styleSheetObj.ownerNode;
			//styleObj.setAttribute("type","text/css");
			
			// alternative way for IE: take existing styleSheet with index 0 instead of creating a new one
			styleSheetObj=document.styleSheets[0];
		}
		else {
			// create the style node, set the style
			styleObj=document.createElement("style");
			styleObj.setAttribute("type","text/css");
			// append the node to the head
			document.getElementsByTagName("head")[0].appendChild(styleObj);
		}
	};
	
	/**
	 * Adds a class className with the CSS in cssString
	 */
	this.addClass = function(className,cssString) {
		// TODO: Internet Explorer routine seems a little buggy
		if ($.browser.msie) {
			//add new style declaration to chosen styleSheet 
			var cssRules = cssString.split(";");
			for (i=0; i<cssRules.length-1; i++){
				styleSheetObj.addRule("."+className,cssRules[i]);
			}
		}
		else {
			// insert the content via createTextNode
			styleObj.appendChild(document.createTextNode("."+className+"{\n\t"+cssString+"\n}\n"));				
		}
	};
	
	var styleObj;
	var styleSheetObj; //IE only...
	
	createStyleTag();
};
