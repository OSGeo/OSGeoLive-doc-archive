<?php
# $Id: mod_displayWmc.php 8099 2011-09-05 10:05:00Z armin11 $
# http://www.mapbender.org/index.php/mod_displayWmc.php
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

$wmc_id = $_GET["wmc_id"];
$download = $_GET["download"];

if ($wmc_id){
	require_once(dirname(__FILE__)."/../classes/class_wmc.php");
	$wmc = new wmc();
	$wmc_gml = $wmc->getDocument($wmc_id);

	if ($wmc_gml){
		//Display WMC
 		// if "short open tags" is activated, the xml output is interpreted
		// as php, because the XML begins with "<?xml "
		if (ini_get("short_open_tag") == 1) {
			echo htmlentities($wmc_gml);
			$e = new mb_warning("'Allow short open tags' is 'On' in php.ini...you might want to turn it off to allow proper WMC display.'");
		}
		else {
			if ($download == 'true') {
				header('Content-disposition: attachment; filename=mapbender_wmc.xml');
				header("Content-type: application/xhtml+xml; charset=".CHARSET);
				echo $wmc_gml;
			} else {
				header("Content-type: application/xhtml+xml; charset=".CHARSET);
				echo $wmc_gml;
			}
		}
	}
	else{
		echo "Invalid document!";
	}
}
?>

