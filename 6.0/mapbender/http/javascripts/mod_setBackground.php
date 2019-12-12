<?php
# $Id: mod_setBackground.php 8053 2011-08-03 15:04:05Z verenadiewald $
# http://www.mapbender.org/index.php/mod_setBackground.php
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
if(isset($_REQUEST["wms"])){
	$wms = $_REQUEST["wms"];
}
else{
	$wms = 0;
}
echo "var mod_setBackground_wms = ".$wms.";";
echo "var mod_setBackground_target = '".$e_target[0]."';";
?>
eventAfterLoadWMS.register(function () {
	mod_setBackground_init();
});

Mapbender.events.setBackgroundIsReady = new Mapbender.Event();

var mod_setBackground_active = false;
function mod_setBackground_init(){
	var map = Mapbender.modules[mod_setBackground_target];
	var setBackgroundSelectBox = document.setBackground.mod_setBackground_list;
	var ind = setBackgroundSelectBox.options[0].value;
	var cnt = 0;
	var selInd;

	setBackgroundSelectBox.options[setBackgroundSelectBox.length - 1] = null;
	for(var i=0; i<map.wms.length; i++){
		if(map.wms[i].gui_wms_visible == '0'){
			var title = map.wms[i].wms_title;
			var newO = new Option(title, i, false,false);

			setBackgroundSelectBox.options[setBackgroundSelectBox.length] = newO;
			if (ind == i) {
				selInd = cnt;
			}
			cnt++;
		}
	}
	
	if(Mapbender.modules['overview']) {
		Mapbender.modules['overview'].wms[0].gui_wms_visible = 1;
		setSingleMapRequest('overview',Mapbender.modules['overview'].wms[0].wms_id);
	}
	 
	if (cnt >0){
		map.wms[ind].gui_wms_visible = 2;
		setBackgroundSelectBox.selectedIndex = selInd;
		setSingleMapRequest(mod_setBackground_target,map.wms[ind].wms_id);
	}
	mod_setBackground_active = ind;

        Mapbender.events.setBackgroundIsReady.trigger();
}

function mod_setBackground_change(obj){	
	var map = Mapbender.modules[mod_setBackground_target];
	map.wms[mod_setBackground_active].gui_wms_visible = 0;
	map.wms[obj.value].gui_wms_visible = 2;
	
	mod_setBackground_active = obj.value;
	zoom(mod_setBackground_target,true, 1.0); 
}
