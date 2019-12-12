<?php
# $Id: mod_scaleSel.php 6684 2010-08-04 07:43:36Z kmq $
# http://www.mapbender.org/index.php/mod_scaleSel.php
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
var mod_scaleSelect_target = options.target;
var that = this;

var mod_scaleSelectChange = function(){
	mod_scaleSelect(this);
	$('body').focus();
};

eventAfterMapRequest.register(function (obj) {
	mod_scaleSelect_val(obj.map.elementName);
});

function mod_scaleSelect(obj){
	var myMapObj = Mapbender.modules[mod_scaleSelect_target];
	var ind = obj.selectedIndex;
	myMapObj.repaintScale(null,null,obj.options[ind].value);
}
function mod_scaleSelect_val(frameName){
	if(frameName == mod_scaleSelect_target){
		var myMapObj = Mapbender.modules[mod_scaleSelect_target];
		var scale = myMapObj.getScale();
		document.getElementById("scaleSelect").options[0].text = "1 : " + scale;
		document.getElementById("scaleSelect").options[0].selected = true;
	}
}

$(that).bind('change', mod_scaleSelectChange);
