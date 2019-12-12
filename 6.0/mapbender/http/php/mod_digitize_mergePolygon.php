<?php
# $Id: mod_digitize_mergePolygon.php 7353 2010-12-22 09:45:24Z christoph $
# http://www.mapbender.org/index.php/DeleteWMS
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

require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");

$json = new Mapbender_JSON();

$polygonList = $_REQUEST["polygons"];

$floatPattern = "-?\d+(\.\d+)?";
$pointPattern = $floatPattern . " " . $floatPattern;
$ringPattern = "\(" . $pointPattern . "(, " . $pointPattern . ")*\)";
$polygonPattern = "\(" . $ringPattern . "(,( )*" . $ringPattern . ")*\)";
$singlePolygonPattern = "POLYGON( )*" . $polygonPattern;
$multiPolygonPattern = "MULTIPOLYGON( )\(" . $polygonPattern . "(,( )*" . $polygonPattern . ")*\)";$pattern = "/" . $multiPolygonPattern . "(;" . $multiPolygonPattern . ")+/";
$anyPolygonPattern = "(" . $singlePolygonPattern . ")|(" . $multiPolygonPattern . ")";
$pattern = "/" . $anyPolygonPattern . "(;" . $anyPolygonPattern . ")*/";

if (!preg_match($pattern, $polygonList)) {
	echo "not a polygon.";
	die();
}

$polygonArray = explode(";", $polygonList);

$sql = "SELECT astext(multi(st_union(geom))) FROM (";

for ($i = 0; $i < count($polygonArray); $i++) {
	if ($i > 0) {
		$sql .= " UNION ";
	}
	$sql .= "(SELECT '" . $polygonArray[$i] . "'::geometry AS geom) ";
}
	
$sql .= ") as a";
$res = db_query($sql);    

$polygonArray = array();
$row = db_fetch_array($res);

$data = array("polygon" => $row[0]);

$output = $json->encode($data);

header("Content-type:application/x-json; charset=utf-8");
echo $output;
?>
