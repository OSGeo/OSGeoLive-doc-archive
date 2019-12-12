<?php
# $Id: mod_setPOI2Scale.php 7741 2011-04-04 13:36:50Z verenadiewald $ 
# http://www.mapbender.org/index.php/mod_setPOI2Scale.php
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
echo "var mod_setPOI2Scale_target = '".$e_target[0]."';";
include(dirname(__FILE__) . "/../include/dyn_js.php");

?>

try{
	if (mod_setPOI2Scale_defScale){}
}
catch(e){
	mod_setPOI2Scale_defScale = 5000;
}

eventAfterLoadWMS.register(function () {
	mod_setPOI2Scale();
});

function mod_setPOI2Scale(){
	var my_target = mod_setPOI2Scale_target.split(",");
	var myPOI = "<?php echo addslashes(Mapbender::session()->get("mb_myPOI2SCALE")); ?>";
	if(myPOI && myPOI != ""){
		var coord = myPOI.split(",");
		if(coord.length == 2){
			coord[2] = mod_setPOI2Scale_defScale; 	
		}
		for(var i=0; i<my_target.length; i++){		
			if(myPOI != ""){							
				mb_repaintScale(my_target[i], coord[0], coord[1], coord[2]);			
			}
		}
	}
}
