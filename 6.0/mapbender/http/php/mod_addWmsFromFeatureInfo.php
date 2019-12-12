<?php
# $Id: mod_addWmsFromFeatureInfo.php 7200 2010-12-11 10:56:35Z apour $
# http://www.mapbender.org/index.php/addWMSFromFeatureInfo.php
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

/*  
 * @security_patch irv open 
 */ 
security_patch_log(__FILE__,__LINE__); 
import_request_variables("PG"); 
include(dirname(__FILE__).'/../include/dyn_js.php');

echo "var mod_target = '".$e_target[0]."';";
?>
function mb_swapWmsByIndex(mapObj_ind, indexA, indexB) {
	if (indexA != indexB && indexA >= 0 && indexA < mb_mapObj[mapObj_ind].wms.length && indexB >= 0 && indexB < mb_mapObj[mapObj_ind].wms.length) {
		upper = mb_mapObj[mapObj_ind].wms[indexA];
		mb_mapObj[mapObj_ind].wms[indexA] = mb_mapObj[mapObj_ind].wms[indexB];
		mb_mapObj[mapObj_ind].wms[indexB] = upper;
		var upperLayers = mb_mapObj[mapObj_ind].layers[indexA];
		var upperStyles = mb_mapObj[mapObj_ind].styles[indexA];
		var upperQuerylayers = mb_mapObj[mapObj_ind].querylayers[indexA];
		mb_mapObj[mapObj_ind].layers[indexA] = mb_mapObj[mapObj_ind].layers[indexB];
		mb_mapObj[mapObj_ind].styles[indexA] = mb_mapObj[mapObj_ind].styles[indexB];
		mb_mapObj[mapObj_ind].querylayers[indexA] = mb_mapObj[mapObj_ind].querylayers[indexB];
		mb_mapObj[mapObj_ind].layers[indexB] = upperLayers;
		mb_mapObj[mapObj_ind].styles[indexB] = upperStyles;
		mb_mapObj[mapObj_ind].querylayers[indexB] = upperQuerylayers;
		return true;
	}
	else {
		return false;
	}
}


function mb_wmsMoveByIndex(mapObj_ind, fromIndex, toIndex) {
	if (fromIndex != toIndex && fromIndex >= 0 && fromIndex < mb_mapObj[mapObj_ind].wms.length && toIndex >= 0 && toIndex < mb_mapObj[mapObj_ind].wms.length) {
		var changed = false;
		var i;
		var result;
		if (fromIndex > toIndex) {
			for (i = fromIndex; i > toIndex ; i--) {
				result = mb_swapWmsByIndex(mapObj_ind, i-1, i);
				if (result === true) {
					changed = true;
				}
			}
		}
		else {
			for (i = fromIndex; i < toIndex ; i++) {
				result = mb_swapWmsByIndex(mapObj_ind, i, i+1);
				if (result === true) {
					changed = true;
				}
			}
		}
		return changed;
	}
	else {
		return false;
	}
}

function addWmsFromFeatureInfo(pointer_name, version) {
	mb_registerloadWmsSubFunctions("addWmsFromInfo_pos()");
	var mywms = pointer_name; 
	if(mywms.indexOf("?") > -1){pointer_name += "&";}
	if(mywms.indexOf("?") == -1){pointer_name += "?";}
	if (version == '1.0.0'){
		var cap = pointer_name + "REQUEST=capabilities&WMTVER=1.0.0";
		var load = cap;
	}
	else if (version == '1.1.0'){
		var cap = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.0";
		var load = cap;
	}
	else if (version == '1.1.1'){
		var cap = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.1";
		var load = cap;
	}
	if(load){
		//if the mapfile WMS ONLINE RESOURCE is set relative, without any host
		if(load.charAt(0) == '/' && load.charAt(1) == 'c') {
			mod_addWMS_load('http://localhost' + load);
        	}
		else{
			mod_addWMS_load(load);
		}
	}
}
function addWmsFromInfo_pos(){
	if (mod_addWmsFromFeatureInfo_position > 0 && mod_addWmsFromFeatureInfo_position < mb_mapObj[getMapObjIndexByName(mod_target)].wms.length-1) {
		mb_wmsMoveByIndex(getMapObjIndexByName(mod_target), mb_mapObj[getMapObjIndexByName(mod_target)].wms.length-1, mod_addWmsFromFeatureInfo_position-1);
	}
	eventAfterLoadWMS.unregister("addWmsFromInfo_pos()");
}