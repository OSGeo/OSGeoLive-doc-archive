<?php
# $Id: mod_setBBOX1.php 5778 2010-03-16 10:03:28Z verenadiewald $
# http://www.mapbender.org/index.php/mod_setBBOX1.php
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
echo "var targetString = '".$e_target[0]."';";
?>
eventInit.register(function () {
	var targetArray = targetString.split(",");
	var bboxString = "<?php echo Mapbender::session()->get("mb_myBBOX") ?>";
	var srs = "<?php echo Mapbender::session()->get("mb_myBBOXEpsg") ?>";
	var performTransformation = 
		("<?php 
			echo Mapbender::session()->get("mb_myBBOXEpsg_transform") 
		?>" === "1") ? true : false;
		
		

	//
	// the user wants to set an SRS that is different from the 
	// application's SRS!
	// OR
	// the user wants to set a bounding box specified in 
	// another SRS to be displayed in the current SRS
	//
	if (typeof Mapbender.modules[targetArray[0]] === "object" 
		&& srs !== "" && srs !== Mapbender.modules[targetArray[0]].epsg) {

		if (typeof Mapbender.modules.changeEPSG !== "object") {
			new Mb_exception("setBBOX requires changeEPSG!");
			return;
		}
		if (performTransformation || bboxString === "") {
			Mapbender.modules.changeEPSG.setSrs({
				srs: srs,
				callback: function () {
					setBbox(targetArray, bboxString);
				}
			});
		}
		else {
			var bboxArray = bboxString.split(",");
			Mapbender.modules.changeEPSG.setSrs({
				srs: srs,
				recalculateExtentOnly : {
					target : targetArray[0],
					extent : new Mapbender.Extent(
						bboxArray[0],
						bboxArray[1],
						bboxArray[2],
						bboxArray[3]
					)
				}
			});
			
		}
	}
	//
	// the user doesn't want to change the SRS.
	//
	else {
		setBbox(targetArray, bboxString);
	}
});

var setBbox = function (targetArray, bboxString) { 

	for (var i = 0; i < targetArray.length; i++) {
		var currentTarget = targetArray[i];
		
		var mapObj = getMapObjByName(currentTarget);
		if (mapObj === null) {
			var e = new Mb_exception("setBBOX: unknown map object: " + currentTarget);
			continue;
		}

		if (bboxString === "") {
			var e = new Mapbender.Notice("setBBOX: no bounding box found.");
			continue;
		}
		else {
			var coord = bboxString.split(","); 
			var newExtent = new Mapbender.Extent(
				parseFloat(coord[0]),
				parseFloat(coord[1]),
				parseFloat(coord[2]),
				parseFloat(coord[3])
			);
		}

		// if the restrictedExtent attribute exists, it has been 
		// configured by the user in the element variable.
		// This is an indicator, that the administrator wants to
		// set the restricted extent coming from request variables.
		if (mapObj.restrictedExtent) {
			mapObj.setRestrictedExtent(newExtent);
		}
		mapObj.calculateExtent(newExtent);		
		mapObj.setMapRequest();		
	}
};

