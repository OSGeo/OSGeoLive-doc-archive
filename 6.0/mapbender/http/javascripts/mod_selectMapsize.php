<?php
# $Id: mod_selectMapsize.php 4577 2009-08-29 14:12:12Z marc $
# http://www.mapbender.org/index.php/mod_selectMapsize.php
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
echo "var mod_selectMapsize_target = '".$e_target[0]."';";
?>

function mod_selectMapsize(obj){
	var ind = getMapObjIndexByName(mod_selectMapsize_target);     
	var p = obj.value.split(",");
	var w = parseInt(p[0]) ;
	var h = parseInt(p[1]);
	var pos = makeClickPos2RealWorldPos(mod_selectMapsize_target,w,h);
	var coords = mb_mapObj[ind].extent.toString().split(",");
	mb_mapObj[ind].extent = new Mapbender.Extent(
		coords[0],
		pos[1],
		pos[0],
		coords[3]
	); 
	mb_mapObj[ind].width = w;
	mb_mapObj[ind].height = h;
	document.getElementById(mod_selectMapsize_target).style.width = mb_mapObj[ind].width;
	document.getElementById(mod_selectMapsize_target).style.height = mb_mapObj[ind].height;
	document.getElementById("mapframe1").style.width = mb_mapObj[ind].width;
	document.getElementById("mapframe1").style.height = mb_mapObj[ind].height;
	setMapRequest(mod_selectMapsize_target);
}
