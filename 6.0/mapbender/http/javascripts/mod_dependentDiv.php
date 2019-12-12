<?php
# $Id: mod_dependentDiv.php 6673 2010-08-02 13:52:19Z christoph $
# http://www.mapbender.org/index.php/mod_dependentDiv.php
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
include '../include/dyn_js.php';

echo "var mod_dependentDiv_target = '".$e_target[0]."';";
?>
var dependentDiv_offsetLeft = typeof dependentDiv_offsetLeft === "undefined" ? 1 : dependentDiv_offsetLeft;
var dependentDiv_offsetTop = typeof dependentDiv_offsetTop === "undefined" ? 10 : dependentDiv_offsetTop;

eventAfterMapRequest.register(function () {
	mod_dependentDiv();
});

function mod_dependentDiv(){
	var obj = document.getElementById(mod_dependentDiv_target).style;
	var thisObj = document.getElementById('dependentDiv').style; 
	thisObj.left = (parseInt(obj.left, 10) + dependentDiv_offsetLeft) + "px";
	thisObj.top = (parseInt(obj.top, 10) + parseInt(obj.height, 10) +  dependentDiv_offsetTop) + "px";
	thisObj.width = (parseInt(obj.width, 10) + (2*dependentDiv_offsetTop)) + "px";
}
