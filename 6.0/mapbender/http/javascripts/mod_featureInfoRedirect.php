<?php
# $Id: mod_featureInfoRedirect.php 6673 2010-08-02 13:52:19Z christoph $
# http://www.mapbender.org/index.php/mod_featureInfoRedirect.php
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
?>

/*
* sticky IFRAME, right from the main mapframe "mapframe1"
*/

eventAfterMapRequest.register(function () {
	mod_featureInfoRedirect_position();
});
eventBeforeFeatureInfo.register(function (obj) {
	mod_featureInfoRedirect_set(obj.fName);
});

function mod_featureInfoRedirect_set(fName){
	var ind = getMapObjIndexByName("mapframe1");
	var res = new Array();
	for(var i=0; i<mb_mapObj[ind].wms.length; i++){
		if(mb_mapObj[ind].querylayers[i] != ""){
			res[res.length] = mb_mapObj[ind].wms[i].wms_id; 
		}
	}  

	var newWin = document.getElementById("FeatureInfoRedirect").style;
	var width = parseInt(newWin.width);
	var height = parseInt(newWin.height) / res.length;

	window.frames["FeatureInfoRedirect"].document.open("text/html");
	for(i=0; i<res.length; i++){
		var top = i * height;      
		window.frames["FeatureInfoRedirect"].document.write("<iframe src='' id='"+res[i]+"' style='position:absolute;top:"+top+"px;left:0px;width:"+width+"px;height:"+height+"px' frameborder='0'></iframe>");
	}
	window.frames["FeatureInfoRedirect"].document.close();
}

function mod_featureInfoRedirect_position(){
	var leftOffset = 10;
	var borderOffset = 10;
	var width = 450;

	var newWin = document.getElementById("FeatureInfoRedirect").style;
	var mapframe = document.getElementById("mapframe1").style;

	newWin.left = (parseInt(mapframe.left, 10) + parseInt(mapframe.width, 10) + leftOffset) + "px";
	newWin.top = (parseInt(mapframe.top, 10) - borderOffset) + "px";
	newWin.width = width + "px";
	newWin.height = (parseInt(mapframe.height, 10) + 2 * borderOffset) + "px";

	var resWin = window.frames["FeatureInfoRedirect"].document.getElementsByTagName("iframe");

	for(var i=0; i<resWin.length; i++){
		resWin[i].style.height = (parseInt(newWin.height, 10) / resWin.length) + "px";
	}
}