<?php
# http://www.mapbender.org/index.php/Monitor_Capabilities
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
require_once(dirname(__FILE__)."/../classes/class_gml2.php");
define("GML_HIGHLIGHT_Z_INDEX",1000);
$gml_string = Mapbender::session()->get("GML");

if ($gml_string) {
	$gml = new gml2();
	$gml->parse_xml($gml_string);
	$bbox = $gml->bbox;
	echo "Mapbender.events.afterInit.register(highlight_init);\n";
	echo "function highlight_init() {\n";
	echo "var mf = new Array(";
	for ($i=0; $i<count($e_target); $i++) {
		if ($i>0) echo ", ";
		echo "'".$e_target[$i]."'";
	}
	echo ");\n";
	echo "hl = new Highlight(mf, 'GML_rendering', {'position':'absolute', 'top':'0px', 'left':'0px', 'z-index':" . GML_HIGHLIGHT_Z_INDEX . "});\n";
	echo $gml->exportMemberToJS(0, false);
	echo "hl.add(q);\n";
	echo "hl.paint();\n";
	echo "mb_registerSubFunctions('hl.paint()');\n";
	echo "}\n";
	$e = new mb_notice("renderGML: GML: " . Mapbender::session()->get("GML") . "; EPSG: " . Mapbender::session()->get("epsg") . "; BBOX: " . implode(", ", $bbox));
	Mapbender::session()->set("GML",NULL);

	$e = new mb_notice("renderGML: deleting GML...");
	
}
else {
	$e = new mb_notice("renderGML: no GML.");
}
?>
