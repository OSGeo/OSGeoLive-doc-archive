<?php
# $Id: mod_savewmc.php 264 2006-05-12 11:07:19Z vera_schulze 
# http://www.mapbender.org/index.php/mod_savewmc.php
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
echo "mod_savewmc_target = '".$e_target[0]."';";
?>

var mod_savewmc_img = new Image(); mod_savewmc_img.src = "<?php  echo $e_src;  ?>";
//var mod_savewmc_img_over = new Image(); mod_savewmc_img_over.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";

function mod_saveWmcKml(obj){
	current_user = "<?php echo Mapbender::session()->get("mb_user_id"); ?>";
	current_password = "<?php echo Mapbender::session()->get("mb_user_password");?>";
	current_gui = "<?php echo $gui_id;?>";
	
	alert('Please set the meeting point by clicking the map.');
	
	var ind = getMapObjIndexByName(mod_savewmc_target);
	
	var el = frames[mod_savewmc_target].document;
	el.onmousedown = mod_getMousePos;
	el.onmouseup = null;
	el.onmousemove = null;
	el.onmouseover = null;
}

function mod_getMousePos(e) {
	mb_getMousePos(e,mod_savewmc_target);
	var q = new Point(clickX,clickY);
	var realWorldPos = mapToReal(mod_savewmc_target,q);

	var el = frames[mod_savewmc_target].document;
	el.onmousedown = null;
	//alert(realWorldPos);
	//document.sendData.target = "_blank";
	//document.sendData.action = "../javascripts/mod_insertWmcIntoDb.php";
	//document.sendData.data.value = user + "____" + generalTitle + "____" + wmc + "____" + id;
	//document.sendData.submit();

	saveWindow = open("", "save", "width=400, height=300, resizable, dependent=yes, scrollbars=yes");
	saveWindow.document.open("text/html");
/*
	var cssLink = saveWindow.document.createElement("link");
	cssLink.setAttribute("href", wfsCssUrl); 
	cssLink.setAttribute("type", "text/css"); 
	cssLink.setAttribute("rel", "stylesheet"); 
	var cssTmp = saveWindow.document.getElementsByTagName("head")[0];
	cssTmp.appendChild(cssLink);
*/
	var wmc_title = "meetingpoint";
	var icon = "http://wms1.ccgis.de/mapbender_dev/img/pin.png";
	
	var str = "";
	var onclick = "wmcid=window.opener.mod_savewmc('"+wmc_title+"');";
	onclick += "document.sendData.action = '../javascripts/mod_insertKmlIntoDb.php?<?php echo SID;?>';";
	onclick += "document.sendData.data.value = wmcid + '____" + realWorldPos.x + "____" + realWorldPos.y + "____"+ icon + "____<?php echo $gui_id;?>';";
	onclick += "document.sendData.submit();" 
	str += "<form name='sendData' method='post'>\n";
	str += "<table>\n";
	str += "<tr>\n";
	str += "<td>Name</td>\n";
	str += "<td><input type='text' name='name'></td>\n";
	str += "</tr>\n";
	str += "<tr>\n";
	str += "<td>Strasse</td>\n";
	str += "<td><input type='text' name='street'></td>\n";
	str += "</tr>\n";
	str += "<tr>\n";
	str += "<td>PLZ</td>\n";
	str += "<td><input type='text' name='postcode'></td>\n";
	str += "</tr>\n";
	str += "<tr>\n";
	str += "<td>Ort</td>\n";
	str += "<td><input type='text' name='city'></td>\n";
	str += "</tr>\n";
	str += "<tr>\n";
	str += "<td>Webseite</td>\n";
	str += "<td><input type='text' name='website'></td>\n";
	str += "</tr>\n";
	str += "<tr>\n";
	str += "<td>Beschreibung</td>\n";
	str += "<td><textarea name='description'></textarea></td>\n";
	str += "</tr>\n";
	str += "<tr>\n";
	str += "<td colspan=2><input type='button' value='URL generieren' name='generateUrl' onclick=\""+onclick+"\"></td>\n";
	str += "</tr>\n";
	str += "</table>\n";
	str += "<input type=hidden name=data>\n";
	str += "</form>";
	saveWindow.document.write(str);
	saveWindow.document.close();
}