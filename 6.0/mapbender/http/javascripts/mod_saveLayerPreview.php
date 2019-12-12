<?php
# $Id: mod_savewmc.php 264 2006-05-12 11:07:19Z vera_schulze 
# http://www.mapbender.org/index.php/mod_savewmc.php
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
include(dirname(__FILE__) . "/../include/dyn_js.php");

echo "mod_savewmc_target = '".$e_target[0]."';";
?>
var mod_saveLayerPreview_img = new Image(); mod_saveLayerPreview_img.src = "<?php  echo $e_src;  ?>";

function mod_saveLayerPreview(){
//	document.sendData.target = "_blank";
	document.sendData.action = "../javascripts/mod_insertLayerPreviewIntoDb.php";
	document.sendData.data.value = mb_mapObj[0].mapURL[0] + "____" + wms[0].wms_getlegendurl;
	document.sendData.submit();
}


