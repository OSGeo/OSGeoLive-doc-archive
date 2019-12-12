<?PHP
# $Id$
# http://www.mapbender.org/index.php/MeetingPoint
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>meetingPoint</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
<!--
<?php
include('../include/dyn_js.php');
echo "var mod_meetingPoint_target = '" . $_REQUEST["e_target"] . "';";
echo "var meetingPoint_write_to = 'meetingPoint';";
?>

// causes an error, read for discussion:  http://stackoverflow.com/questions/1007340/javascript-function-aliasing-doesnt-seem-to-work
// window.alert = parent.alert;

try{
	if (meetingPoint_export_subject){}
}
catch(e){
	meetingPoint_export_subject = '<?php echo _mb("Your meeting point. Follow the link!") ?>';
}

try{
	if (meetingPoint_export_url){}
}
catch(e){
	meetingPoint_export_url = '';
}

try{
	if (meetingPoint_export_format){}
}
catch(e){
	meetingPoint_export_format = 'prompt';
}

try{
	if (meetingPoint_max_characters){}
}
catch(e){
	meetingPoint_max_characters = 100;
}

try{
	if (meetingPoint_image){}
}
catch(e){
	meetingPoint_image = "../img/button_digitize/point_off.png";
}

try{
	if (meetingPoint_icon){}
}
catch(e){
	meetingPoint_icon = '../img/redball.gif';
}

try{
	if (meetingPoint_width){}
}
catch(e){
	meetingPoint_width = 14;
}

try{
	if (meetingPoint_height){}
}
catch(e){
	meetingPoint_height = 14;
}

try{
	if (meetingPoint_style){}
}
catch(e){
	meetingPoint_style = 'background-color:white;font-weight: bold;color:black;font-family:Arial;';
}

try{
	if (meetingPoint_please_click){}
}
catch(e){
	meetingPoint_please_click = '<?php echo _mb("Please click for the meeting point position!") ?>';
}

var mod_meetingPointRealPoint = null;
//parent.mb_registerSubFunctions("window.frames['meetingPoint'].mod_meetingPoint_draw()");

parent.eventAfterMapRequest.register(function () {
	mod_meetingPoint_draw();
});

var ie = document.all?1:0;
var mod_meetingPoint_win = null;
var mod_meetingPoint_elName = "meetingPoint";
var mod_meetingPoint_frameName = "meetingPoint";
var mod_meetingPoint_button = "my_getCoords";
var mod_meetingPoint_img_on = new Image(); mod_meetingPoint_img_on.src = meetingPoint_image.replace(/_off/,"_on") ;
var mod_meetingPoint_img_off = new Image(); mod_meetingPoint_img_off.src = meetingPoint_image;
var mod_meetingPoint_img_over = new Image(); mod_meetingPoint_img_over.src = meetingPoint_image.replace(/_off/,"_over") ;
var mod_meetingPoint_fix = "";


function init_mod_meetingPoint(ind){
	parent.mb_button[ind] = window.document.getElementById(mod_meetingPoint_button);
	parent.mb_button[ind].img_over = mod_meetingPoint_img_over.src;
	parent.mb_button[ind].img_on = mod_meetingPoint_img_on.src;
	parent.mb_button[ind].img_off = mod_meetingPoint_img_off.src;
	parent.mb_button[ind].status = 0;
	parent.mb_button[ind].elName = mod_meetingPoint_button;
	parent.mb_button[ind].go = new Function ("mod_meetingPoint_run()");
	parent.mb_button[ind].stop = new Function ("mod_meetingPoint_disable()");
}

var mod_meetingPoint_getMousePosition = function (e) {
	var mapObject = parent.getMapObjByName(mod_meetingPoint_target);
	var clickPos = mapObject.getMousePosition(e);
	mod_meetingPointRealPoint = mapObject.convertPixelToReal(clickPos);
	mod_meetingPoint_write(mod_meetingPointRealPoint.x, mod_meetingPointRealPoint.y);
	mod_meetingPoint_draw();	
	parent.mb_disableThisButton(mod_meetingPoint_button);
};

function mod_meetingPoint_run(){   
	if (document.forms[0].mytext.value === "") {
		alert("<?php echo _mb("Please define a text!") ?>");
		parent.mb_disableThisButton(mod_meetingPoint_button);
		return;
	}
	var mapObject = parent.getMapObjByName(mod_meetingPoint_target);
	if (mapObject) {
		parent.$(mapObject.getDomElement()).bind("click", mod_meetingPoint_getMousePosition);
	}
}

function mod_meetingPoint_disable(){
	var mapObject = parent.getMapObjByName(mod_meetingPoint_target);
	if (mapObject) {
		parent.$(mapObject.getDomElement()).unbind("click", mod_meetingPoint_getMousePosition);
	}
}

function mod_meetingPoint_write(x,y){	
   document.forms[0].x.value =x;
   document.forms[0].y.value =y;	   
}

function mod_meetingPoint_hide(){
	var mapObject = parent.getMapObjByName(mod_meetingPoint_target);
	var map_el = mapObject.getDomElement();
	parent.$(parent.document.getElementById(mapObject.elementName + "_meetingPoint_permanent")).empty();
}


function mod_meetingPoint_draw(){
    var splitext  = document.forms[0].mytext.value;
    document.forms[0].mytext.value = splitext.substring(0, meetingPoint_max_characters);
    	
	var mapObject = parent.getMapObjByName(mod_meetingPoint_target);
	if (mapObject) {
		if (mod_meetingPointRealPoint !== null) {
			//alert(document.forms[0].x.value +" -- "+ document.forms[0].y.value + " - " );
	
			var meetingPointClickPos = mapObject.convertRealToPixel(mod_meetingPointRealPoint);
			var tagSource = "";
			tagSource += "<div style='visibility:visible;z-index:105;position:absolute;left:"+
				(meetingPointClickPos.x- Math.round(0.5*meetingPoint_width))+"px;top:"+
				(meetingPointClickPos.y-Math.round(0.5*meetingPoint_height))+"px'>";
			tagSource += "<img src='"+meetingPoint_icon+"' />";
			tagSource += "<div class='ui-widget-content ui-corner-all' style='padding:3px'><span style='white-space:nowrap;'>"+document.forms[0].mytext.value.replace("\n", "<br>")+"</span></div>";
			tagSource += "</div>";

			var map_el = mapObject.getDomElement();
			if (!map_el.ownerDocument.getElementById(mapObject.elementName + "_meetingPoint_permanent")) {
		
				//create Box Elements
				var $div = parent.$("<div id='" + mapObject.elementName + "_meetingPoint_permanent'></div>");
				$div.css({
					position: "absolute",
					top: "0px",
					left: "0px"
				});
				parent.$(map_el).append($div);
				parent.mb_registerPanSubElement(mapObject.elementName + "_meetingPoint_permanent")
			}
			parent.$(parent.document.getElementById(mapObject.elementName + "_meetingPoint_permanent")).html(tagSource);
		}
	}
}

/* Check max character value*/

function checkMaxCharacters() {
	var stringLength = document.forms[0].mytext.value.length;
	if (stringLength > meetingPoint_max_characters) {
		document.forms[0].mytext.value = document.forms[0].mytext.value.slice(0, meetingPoint_max_characters) ;
		alert("<?php echo _mb("Input too long. Maximum of allowed characters is"); ?> " + meetingPoint_max_characters);
	}
}

function setMaxCharacterTitle() {
	var maxCharacterString = "<?php echo _mb("Max characters") ?>: " + meetingPoint_max_characters;
//	document.getElementById("mytext").title = maxCharacterString;
	document.getElementById("mytext").setAttribute("title",maxCharacterString);
//	document.forms[0].mytext.title = maxCharacterString;
}

-->
</script>

<script language='JavaScript'>
<!--
<?php
echo "var used_charset = '".CHARSET ."';";
?>

function validate(){
	var mycheck = true;
	var checkObj= document.forms[0].mytext;
	if(checkObj.value == '') {
		alert ("<?php echo _mb("Please define a text!") ?>");
		checkObj.focus();
		mycheck=false;
		return false;
	}

	var checkObj= document.forms[0].x;
	if(checkObj.value == '') {
		alert (meetingPoint_please_click);
		mycheck = false;
	}

	if(mycheck == true){
		if(meetingPoint_export_url != "") {
			my_meetingPoint = meetingPoint_export_url + "?";
		}
		else {
			my_meetingPoint = document.forms[0].myurl.value + "?";
		}	
		my_meetingPoint += "name=" + encodeURIComponent(document.forms[0].myuser.value);
		my_meetingPoint += "&password=" + encodeURIComponent(document.forms[0].mypw.value);
		my_meetingPoint += "&mb_user_myGui=" + encodeURIComponent(document.forms[0].mygui.value);
		
		var ind = parent.getMapObjIndexByName('mapframe1');
		var coord = parent.mb_mapObj[ind].extent.toString().split(",");
		
		my_meetingPoint += "&mb_myBBOX=" + parseFloat(coord[0]) + ",";
			my_meetingPoint +=  parseFloat(coord[1]) + ",";
			my_meetingPoint +=  parseFloat(coord[2]) + ",";
			my_meetingPoint +=  parseFloat(coord[3]) ;
		
		my_meetingPoint += "&mb_myPOI=";
		
		var splitext  = document.forms[0].mytext.value;
		document.forms[0].mytext.value = splitext.substring(0, meetingPoint_max_characters);
		
		my_meetingPoint += encodeURIComponent(document.forms[0].mytext.value);
		
		my_meetingPoint += "___" + document.forms[0].x.value + "___";
		my_meetingPoint += document.forms[0].y.value;
		
		if(meetingPoint_export_format == 'email') {
			createEmail(my_meetingPoint,meetingPoint_export_subject);
		}
		else {			
			var div = parent.$("<div/>").attr("title", "Treffpunkt").text(meetingPoint_export_subject);
			var textarea = parent.$("<textarea />").attr({
				"rows": 5,
				"cols": 30
			}).text(my_meetingPoint);
			div.append("<br /><br />");
			div.append(textarea);
			div.dialog();
		//			prompt(meetingPoint_export_subject,my_meetingPoint);			
		}
	}
}

function emptyfields(){
	document.forms[0].mytext.value ='';
	document.forms[0].x.value ='';
	document.forms[0].y.value =  '';
	mod_meetingPoint_hide();
}

function createEmail (url,subject) {
	var email = "mailto:"
	email  += "";
	email  += "?subject=";
	email  += subject;
	email  += "&body=";
	email  += escape(url);

	var win = window.open(email, 'email', 'top=120,left=120');
	win.close();
}

function goBack(where){
	document.location.href=where;
}

function init(){
	parent.mb_regButton_frame("init_mod_meetingPoint","meetingPoint",null);
}

-->
</script>

</head>
<body onload="setMaxCharacterTitle()">

<form action="" >

<input class='strinput' type="hidden" name='myurl' value='<?php  echo LOGIN;  ?>'>

<input class='strinput' type="hidden" name='mygui' value='<?php  echo Mapbender::session()->get("mb_user_gui");  ?>'>

<input class='strinput' type="hidden" name='myuser' value='<?php  echo Mapbender::session()->get("mb_user_name");  ?>'>

<input class='strinput' type="hidden" name='mypw' value='<?php  echo Mapbender::session()->get("mb_user_password");  ?>'>
<!--<input class='strinput' type="textarea" size=2 name='mytext' value='' "maxlength=70" title="max. 70 Zeichen">-->
<textarea class='strinput' rows="3" name='mytext' id='mytext' onmouseover='checkMaxCharacters()' title='' onkeyup='checkMaxCharacters()'></textarea>
<br><br>
<img  id='my_getCoords' name='my_getCoords' onclick="mod_meetingPoint_run()" onmouseover ="parent.mb_regButton_frame('init_mod_meetingPoint','meetingPoint',null)"  title="Treffpunkt setzen"  src = '../img/button_digitize/point_off.png'>
<br><br>
<input class="okbutton" name="Send"   type="button" value="ok" onclick="validate();">
<input class="ibutton" type="button" value="<?php echo _mb("cancel") ?>" onClick="emptyfields();">
<br>
<input class="coord" type="hidden" name='x' value='' readonly>
<input class="coord" type="hidden" name='y' value='' readonly>
<br>
</form>
</body>
</html>
