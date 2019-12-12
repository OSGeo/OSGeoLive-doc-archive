<?php
# $Id: mod_wfs_gazetteer_client.php 1044 2007-10-10 08:30:29Z baudson $
# maintained by http://www.mapbender.org/index.php/User:Verena Diewald
# http://www.mapbender.org/index.php/WFS_gazetteer
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
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
$gui_id = Mapbender::session()->get("mb_user_gui");

$target = $_REQUEST["target"];
//$e_id_css = $_REQUEST["e_id_css"];

$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db($DB,$con);

//include '../include/dyn_css.php';
?>

function setWfsConfString() {
<?php
echo "\tvar gazetteerFrameId = '" . $e_id . "';\n";
// if services were selected in search, append them to the wfs gazetteer
echo "\tvar gazetteerWfsConfFromPortal = '" . $_REQUEST["portal_services_wfs"] . "';\n";
?>

// now done somewhere else
//	var gazetteerWfsConfFromWMC = mb_getWmcExtensionData("wfsConfIdString");

	//just initialise the gazetteer.
//	if (!gazetteerWfsConfFromPortal && !gazetteerWfsConfFromWMC) {
//		console.log("no WFS conf from WMC and Portal");
//	}
	// append WFS conf from WMC
//	else if (!gazetteerWfsConfFromPortal && gazetteerWfsConfFromWMC) {
//		console.log("WFS conf from WMC");
//		window.frames[gazetteerFrameId].appendWfsConf(gazetteerWfsConfFromWMC);	
//	}
	// append WFS conf from portal
//	else if (gazetteerWfsConfFromPortal && !gazetteerWfsConfFromWMC) {
//		console.log("WFS conf from Portal");
//		window.frames[gazetteerFrameId].appendWfsConf(gazetteerWfsConfFromPortal);	
//	}
	// append WFS conf from portal and WMC
//	else if (gazetteerWfsConfFromPortal && gazetteerWfsConfFromWMC) {
//		console.log("WFS conf from WMC and Portal");
//		window.frames[gazetteerFrameId].appendWfsConf(gazetteerWfsConfFromPortal + "," + gazetteerWfsConfFromWMC);	
//	}

	if (gazetteerWfsConfFromPortal && gazetteerWfsConfFromPortal != null) {
		window.frames[gazetteerFrameId].appendWfsConf(gazetteerWfsConfFromPortal);	
	}
}

mb_registerloadWmcSubFunctions("setWfsConfString()");
