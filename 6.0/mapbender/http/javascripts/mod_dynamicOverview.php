<?php
# $Id: mod_dynamicOverview.php 4977 2009-11-12 11:04:51Z kmq $
# http://www.mapbender.org/index.php/Owsproxy
# Module maintainer Uli
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
include '../include/dyn_js.php';

echo "var mod_dynamicOverview_target = '".implode(",", $e_target)."';";

?>
/*
* element_var: mod_dynamicOverview_zoomFactor (proportion between main- and overview-map)
* element_var: mod_dynamicOverview_startExtent (startextent of the main mapframe, minx,miny,maxx,maxy commaseparated)
* element_var: mod_dynamicOverview_wmsIndex (wms by index to calculate the maxExtent)
* element_var: mod_dynamicOverview_useMaxExtent (consider the maxExtent for the wms in the given srs)
*/
try{
	if (mod_dynamicOverview_startExtent){}
}
catch(e){
	mod_dynamicOverview_startExtent = false;
}
try{
	if (mod_dynamicOverview_wmsIndex){ mod_dynamicOverview_wmsIndex = parseInt(mod_dynamicOverview_wmsIndex);}
}
catch(e){
	mod_dynamicOverview_wmsIndex = 0;
}
try{
	if (mod_dynamicOverview_useMaxExtent){}
}
catch(e){
	mod_dynamicOverview_useMaxExtent = false;
}

try { 
	if(mod_dynamicOverview_zoomFactor){}
}
catch(e){
	mod_dynamicOverview_zoomFactor = 10;
}

try { 
	if(mod_dynamicOverview_minScale){}
}
catch(e){
	mod_dynamicOverview_minScale = 10;
}

var mod_dynamicOverviewCount = 0;
var mod_dynamicOverviewSwitch = false;

eventBeforeMapRequest.register(function (obj) {
	mod_dynamicOverviewCalculateExtent(obj.map);
});

eventAfterMapRequest.register(function (obj) {
	mod_dynamicOverviewSetVisibility(obj.map);
});

function mod_dynamicOverviewCalculateExtent (map) {
	var arrayTargets = mod_dynamicOverview_target.split(",");
	var disty = false;
	var distx = false;

	var map0 = getMapObjByName(arrayTargets[0]);
	var map1 = getMapObjByName(arrayTargets[1]);
	
	//set extent for the main mapframe from configuration param element_var
	if (mod_dynamicOverview_startExtent && mod_dynamicOverviewCount == 0){
		mod_dynamicOverviewCount++;
		var arrayCoords = mod_dynamicOverview_startExtent.split(",");
		var minx = parseFloat(arrayCoords[0]);
		var miny = parseFloat(arrayCoords[1]);
		var maxx = parseFloat(arrayCoords[2]);
		var maxy = parseFloat(arrayCoords[3]);
		var newExtent = new Mapbender.Extent(minx, miny, maxx, maxy)
		map0.calculateExtent(newExtent);
	}

	// read params from main-mapframe
	if (map.elementName == map0.elementName) {

		// get center in coords:
		var coords = map0.extent.toString().split(",");
		var minx = parseFloat(coords[0]);
		var miny = parseFloat(coords[1]);
		var maxx = parseFloat(coords[2]);
		var maxy = parseFloat(coords[3]);
		distx = maxx - minx;
		disty = maxy - miny;
		var centerx = minx + distx/2;
		var centery = miny + disty/2;
		
		if(mod_dynamicOverview_zoomFactor){
			mod_dynamicOverview_zoomFactor = parseFloat(mod_dynamicOverview_zoomFactor);
			minx = centerx - ((distx/2)*mod_dynamicOverview_zoomFactor);
			miny = centery - ((disty/2)*mod_dynamicOverview_zoomFactor);
			maxx = centerx + ((distx/2)*mod_dynamicOverview_zoomFactor);
			maxy = centery + ((disty/2)*mod_dynamicOverview_zoomFactor);
			distx = maxx - minx;
		    disty = maxy - miny; 
		}
		
		// check and set maxExtent for orverview 
		if(mod_dynamicOverview_useMaxExtent){
			if(mod_dynamicOverview_useMaxExtent){
				var maxExtent = mod_dynamicOverview_useMaxExtent;
			}
			else{
				var maxExtent = mod_dynamicOverviewGetMaxExtent(wms[mod_dynamicOverview_wmsIndex], wms[0].gui_wms_epsg);
			}
			if(maxExtent){
				maxExtent = maxExtent.split(",");
				var maxMinx = parseFloat(maxExtent[0]);
				var maxMiny = parseFloat(maxExtent[1]);
				var maxMaxx = parseFloat(maxExtent[2]);
				var maxMaxy = parseFloat(maxExtent[3]);
				var maxDistx = maxMaxx - maxMinx;
				var maxDisty = maxMaxy - maxMiny;	
				
				if(distx && disty && (distx > maxDistx || disty > maxDisty)){
					minx = maxMinx;
					miny = maxMiny;
					maxx = maxMaxx;
					maxy = maxMaxy;
				}
			}	
		}
		//check and set minExtent for overview
		var newMinExt = mod_dynamicOverviewCheckDiagonal(map1, minx,miny, maxx, maxy);
		if(newMinExt){
			var minCoords = newMinExt.split(",");
			minx = 	parseFloat(minCoords[0]);	
			miny = 	parseFloat(minCoords[1]);
			maxx = 	parseFloat(minCoords[2]);
			maxy = 	parseFloat(minCoords[3]);
		}
		var newExtent = new Mapbender.Extent(minx, miny, maxx, maxy)
		map1.calculateExtent(newExtent);
		map1.zoom(true, 1.0);
	}
	else if(map.elementName == map1.elementName){
		//switch hidden wms to visible
		if(wms[mod_dynamicOverview_wmsIndex].gui_wms_visible != 1){
			mod_dynamicOverviewSwitch = wms[mod_dynamicOverview_wmsIndex].gui_wms_visible;
			wms[mod_dynamicOverview_wmsIndex].gui_wms_visible = 1;
		}
	}
}

function mod_dynamicOverviewCheckDiagonal(map, minx,miny, maxx, maxy){
	var r = false;
	var distx = maxx-minx;
	var disty = maxy-miny;
	var centerx = minx + distx/2;
	var centery = miny + distx/2;
	var xPerPix = distx/map.width;
	var yPerPix = disty/map.height;
	var d = Math.sqrt(Math.pow(xPerPix,2)+Math.pow(yPerPix,2));
	if(mod_dynamicOverview_minScale && mod_dynamicOverview_minScale > d){
		var newDistx = Math.sqrt(Math.pow((mod_dynamicOverview_minScale),2)/2)*map.width;
		minx = centerx - newDistx/2;
		maxx = centerx + newDistx/2;
		miny = centery - newDistx/2;
		maxy = centery + newDistx/2;
		r = minx + ","+ miny + "," + maxx + "," + maxy;
	}
	return r;
}

function mod_dynamicOverviewSetVisibility(map){
	var arrayTargets = mod_dynamicOverview_target.split(",");
	if(mod_dynamicOverviewSwitch && map.elementName == arrayTargets[1]){
		wms[mod_dynamicOverview_wmsIndex].gui_wms_visible = mod_dynamicOverviewSwitch;
		
	}	
}

function mod_dynamicOverviewGetMaxExtent(wms, srs){
	var re = false;
	for(var i=0; i<wms.gui_epsg.length; i++){
		if(srs == wms.gui_epsg[i]){
			var re = wms.gui_minx[i] +","+ wms.gui_miny[i]  +","+ wms.gui_maxx[i] +","+ wms.gui_maxy[i];
		}
	}
	return re;
}
