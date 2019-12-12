<?php
# $Id: mod_back.php 5500 2010-02-12 10:12:45Z verenadiewald $
# http://www.mapbender.org/index.php/Back
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

require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

echo "var mod_back_map = '".$e_target[0]."';";
echo "var mod_back_overview = '".$e_target[1]."';";

?>
var mod_back_img_off = new Image(); 
mod_back_img_off.src = "<?php  echo preg_replace("/_off_disabled/","_off",$e_src);  ?>";
var mod_back_img_off_disabled = new Image(); 
mod_back_img_off_disabled.src = "<?php  echo $e_src;  ?>";
var mod_back_img_over = new Image(); 
mod_back_img_over.src = "<?php  echo preg_replace("/_off_disabled/","_over",$e_src);  ?>";
var mod_back_img_previous = null;
var mod_back_cnt = -1;

$('#<?php echo $e_id;?>').mouseover(function () {
	mod_back_over(this);
}).mouseout(function() {
	mod_back_out(this);
}).click(function() {
	mod_back_set();
});

eventAfterMapRequest.register(function (obj) {
	mb_setHistoryObj(obj.map.elementName);
	mod_back_check(obj.map.elementName);
});


function mod_back_check(frameName){
	if(frameName == mod_back_map){
		var ind = getMapObjIndexByName(frameName);
		mod_back_cnt++;
		if(mb_mapObj[ind].mb_MapHistoryObj.length > 1){
			document.getElementById("back").src =  mod_back_img_off.src;
		}
		else{
			document.getElementById("back").src =  mod_back_img_off_disabled.src;
			mod_back_img_previous = document.getElementById("back").src;
		}
	}
}
function mod_back_over(obj){
	mod_back_img_previous = document.getElementById("back").src;
	if(document.getElementById("back").src ==  mod_back_img_off.src){
		document.getElementById("back").src = mod_back_img_over.src;
	}
}

function mod_back_out(obj){
	document.getElementById("back").src  = mod_back_img_previous;
}

function mod_back_set(){
	if(mod_back_img_previous ==  mod_back_img_off.src){
		var ind = getMapObjIndexByName(mod_back_map);
		if(mb_mapObj[ind].mb_MapFutureObj){
			mb_mapObj[ind].mb_setFutureObj(mod_back_cnt);
		}
		var cnt = mb_mapObj[ind].mb_MapHistoryObj.length - 2;
		if(mb_mapObj[ind].epsg != mb_mapObj[ind].mb_MapHistoryObj[cnt].epsg){
			var oind = getMapObjIndexByName(mod_back_overview);
			for(var i=0; i < mb_mapObj[oind].mb_MapHistoryObj.length; i++){
				if(mb_mapObj[oind].mb_MapHistoryObj[i].epsg == mb_mapObj[ind].mb_MapHistoryObj[cnt].epsg){
					mb_mapObj[oind].epsg = mb_mapObj[oind].mb_MapHistoryObj[i].epsg;
					mb_mapObj[oind].extent = new Mapbender.Extent(mb_mapObj[oind].mb_MapHistoryObj[i].extent.min, mb_mapObj[oind].mb_MapHistoryObj[i].extent.max);
					setMapRequest(mod_back_overview);
					break;
				}
			}
		}
		document.getElementById(mod_back_map).style.width = mb_mapObj[ind].mb_MapHistoryObj[cnt].width;
		document.getElementById(mod_back_map).style.height = mb_mapObj[ind].mb_MapHistoryObj[cnt].height;     
		mb_mapObj[ind].width = mb_mapObj[ind].mb_MapHistoryObj[cnt].width;
		mb_mapObj[ind].height = mb_mapObj[ind].mb_MapHistoryObj[cnt].height;     
		mb_mapObj[ind].epsg = mb_mapObj[ind].mb_MapHistoryObj[cnt].epsg;
		var extentMin = mb_mapObj[ind].mb_MapHistoryObj[cnt].extent.min;
		var extentMax = mb_mapObj[ind].mb_MapHistoryObj[cnt].extent.max;
		var buffer = parseFloat(0.000000001);
		extentMin.x = extentMin.x - buffer;
		extentMin.y = extentMin.y - buffer;
		extentMax.x = extentMax.x - buffer;
		extentMax.y = extentMax.y - buffer;
		mb_mapObj[ind].extent = new Mapbender.Extent(extentMin, extentMax);
		mb_mapObj[ind].extent = new Mapbender.Extent(mb_mapObj[ind].mb_MapHistoryObj[cnt].extent.min, mb_mapObj[ind].mb_MapHistoryObj[cnt].extent.max);
		mb_mapObj[ind].layers = mb_mapObj[ind].mb_MapHistoryObj[cnt].layers;
		mb_mapObj[ind].styles = mb_mapObj[ind].mb_MapHistoryObj[cnt].styles;
		mb_mapObj[ind].querylayers = mb_mapObj[ind].mb_MapHistoryObj[cnt].querylayers;
		mb_mapObj[ind].mb_MapHistoryObj.length = (mb_mapObj[ind].mb_MapHistoryObj.length - 2);
		setMapRequest(mod_back_map);
	}
}
function mb_setHistoryObj(frameName){
	var ind = getMapObjIndexByName(frameName);
	if(mb_mapObj[ind].mb_MapHistoryObj == null){
		mb_mapObj[ind].mb_MapHistoryObj = [];
	}
	var cnt = mb_mapObj[ind].mb_MapHistoryObj.length;
	mb_mapObj[ind].mb_MapHistoryObj[cnt] = new Object();
	mb_mapObj[ind].mb_MapHistoryObj[cnt].width = mb_mapObj[ind].width;
	mb_mapObj[ind].mb_MapHistoryObj[cnt].height = mb_mapObj[ind].height;
	mb_mapObj[ind].mb_MapHistoryObj[cnt].epsg = mb_mapObj[ind].epsg;
	// must create a new Mapbender.Extent object!
	mb_mapObj[ind].mb_MapHistoryObj[cnt].extent = new Mapbender.Extent(mb_mapObj[ind].extent.min, mb_mapObj[ind].extent.max);
	mb_mapObj[ind].mb_MapHistoryObj[cnt].layers = new Array();
	for(var i=0; i<mb_mapObj[ind].layers.length;i++){
		mb_mapObj[ind].mb_MapHistoryObj[cnt].layers[i] = mb_mapObj[ind].layers[i];
	}
	mb_mapObj[ind].mb_MapHistoryObj[cnt].styles = [];
	for(var i=0; i<mb_mapObj[ind].styles.length;i++){
		mb_mapObj[ind].mb_MapHistoryObj[cnt].styles[i] = mb_mapObj[ind].styles[i];
	}
	mb_mapObj[ind].mb_MapHistoryObj[cnt].querylayers = [];
	for(var i=0; i<mb_mapObj[ind].querylayers.length;i++){
		mb_mapObj[ind].mb_MapHistoryObj[cnt].querylayers[i] = mb_mapObj[ind].querylayers[i];
	}
}
