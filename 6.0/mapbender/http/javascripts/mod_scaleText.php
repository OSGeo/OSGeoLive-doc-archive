<?php
# $Id: mod_scaleText.php 4274 2009-07-01 15:05:08Z christoph $
# http://www.mapbender.org/index.php/mod_scaleText.php
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
echo "var mod_scaleText_target = '".$e_target[0]."';";
?>
function mod_scaleText(){
	mod_scaleText_val(mod_scaleText_target);
	return false;
}
function mod_scaleText_val(frameName){
	var scale = document.getElementById("scaleText").elements[0];
	if(scale.value.search(/\D/) != -1 || scale.value == ""){
		scale.value = "";
		return;
	}   
	var ind = getMapObjIndexByName(frameName);
	var arrayBBox = mb_mapObj[ind].extent.toString().split(",");
	var x = parseFloat(arrayBBox[0]) + ((parseFloat(arrayBBox[2]) - parseFloat(arrayBBox[0]))/2);
	var y = parseFloat(arrayBBox[1]) + ((parseFloat(arrayBBox[3]) - parseFloat(arrayBBox[1]))/2);

	var minx = parseFloat(x) - (mb_mapObj[ind].width / (mb_resolution * 100 *2) * scale.value);
	var miny = parseFloat(y) -  (mb_mapObj[ind].height / (mb_resolution * 100 *2) * scale.value);
	var maxx = parseFloat(x) + (mb_mapObj[ind].width / (mb_resolution * 100 *2) * scale.value);
	var maxy = parseFloat(y) +  (mb_mapObj[ind].height / (mb_resolution * 100 *2) * scale.value);     
	mb_mapObj[ind].extent = new Mapbender.Extent(
		minx,
		miny,
		maxx,
		maxy
	);
	setMapRequest(frameName);
	scale.value = "";
}
