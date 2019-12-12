<?php
# $Id: mod_setBackground_all.php 2413 2008-04-23 16:21:04Z christoph $
# http://www.mapbender.org/index.php/mod_setBackground_all.php
# 
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
echo "var mod_setBackground_all_target = '".$e_target[0]."';";
?>

var mod_setBackground_all_wms = new Array();
function mod_setBackground_all_init(obj){
	var ind = getMapObjIndexByName(mod_setBackground_all_target);	
	var cnt = 0;
	if(obj.checked == true){	
		for(var i=0; i<mb_mapObj[ind].wms.length; i++){
			if(wms[i].gui_wms_visible == '0'){
				mod_setBackground_all_wms[cnt] = mb_mapObj[ind].wms[i].wms_id;
				mb_mapObj[ind].wms[i].gui_wms_visible = "1";
				cnt++;						
			}
		}
	}
	else{
		for(var i=0; i<mb_mapObj[ind].wms.length; i++){
			for(var ii = 0; ii<mod_setBackground_all_wms.length; ii++){
				if(mb_mapObj[ind].wms[i].wms_id == mod_setBackground_all_wms[ii]){
					mb_mapObj[ind].wms[i].gui_wms_visible = "0";
				}		
			}
		}
	}
	zoom(mod_setBackground_all_target,true, 0.99);
}
