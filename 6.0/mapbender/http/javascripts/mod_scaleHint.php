<?php
# $Id: mod_scaleHint.php 8109 2011-09-07 07:51:32Z verenadiewald $
# http://www.mapbender.org/index.php/mod_scaleHint.php
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

echo "var mod_scaleHint_target = '".$e_target[0]."';";
echo "var mod_scaleHint_min = '".$e_target[1]."';";
echo "var mod_scaleHint_max = '".$e_target[2]."';"; 

?>

eventBeforeMapRequest.register(function (obj) {
	mod_scaleHint_init(obj.map.elementName);
});

function mod_scaleHint_init(frameName){
	if(frameName == mod_scaleHint_target){
		var ind = getMapObjIndexByName(frameName);
		var scale = mb_mapObj[ind].getScale();
                if(parseInt(scale,10) < parseInt(mod_scaleHint_min, 10) || parseInt(scale, 10) > parseInt(mod_scaleHint_max, 10)){
                	if(parseInt(scale, 10) < parseInt(mod_scaleHint_min, 10)){
				var newScale = parseInt(mod_scaleHint_min, 10);
			}
			if(parseInt(scale, 10) > parseInt(mod_scaleHint_max, 10)){
				var newScale = parseInt(mod_scaleHint_max, 10);
			}
			var ind = getMapObjIndexByName(frameName);
			var arrayBBox = mb_mapObj[ind].extent.toString().split(",");
			var x = parseFloat(arrayBBox[0]) + ((parseFloat(arrayBBox[2]) - parseFloat(arrayBBox[0]))/2);
			var y = parseFloat(arrayBBox[1]) + ((parseFloat(arrayBBox[3]) - parseFloat(arrayBBox[1]))/2);
			var minx = parseFloat(x) - (mb_mapObj[ind].width / (mb_resolution * 100 *2) * newScale);
			var miny = parseFloat(y) -  (mb_mapObj[ind].height / (mb_resolution * 100 *2) * newScale);
			var maxx = parseFloat(x) + (mb_mapObj[ind].width / (mb_resolution * 100 *2) * newScale);
			var maxy = parseFloat(y) +  (mb_mapObj[ind].height / (mb_resolution * 100 *2) * newScale);
			mb_mapObj[ind].extent = new Mapbender.Extent(
				minx,
				miny,
				maxx,
				maxy
			);
		}
	}
	return true;
}
