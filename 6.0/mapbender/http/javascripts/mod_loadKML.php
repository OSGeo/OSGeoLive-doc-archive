<?php
# $Id: mod_loadKML.php 2413 2008-04-23 16:21:04Z christoph $
# http://www.mapbender.org/index.php/mod_loadwmc.php
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
require_once(dirname(__FILE__)."/../classes/class_kml.php");

echo "var loadKmlTarget = '".$e_target[0]."';\n";

?>
var kmlHasLoaded = new MapbenderEvent();


var loadKmlImg = new Image(); 
loadKmlImg.src = "<?php echo $e_src; ?>";

var loadKmlImgOver = new Image(); 
loadKmlImgOver.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";

var mpbn_loadKml = function () {
	windowKml = window.open("../php/mb_listKMLs.php?<?php echo $urlParameters;?>","displayKml","width=500, height=600, scrollbars=yes, dependent=yes");
};

var mpbn_loadKmlInit = function (obj) {
	obj.src = loadKmlImgOver.src;
	obj.onmouseover = new Function("mpbn_loadKmlOver(this)");
	obj.onmouseout = new Function("mpbn_loadKmlOut(this)")
};

var mpbn_loadKmlOver = function (obj) {
	obj.src = loadKmlImgOver.src;
}

var mpbn_loadKmlOut = function (obj) {
	obj.src = loadKmlImg.src;
}