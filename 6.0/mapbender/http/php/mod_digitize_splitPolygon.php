<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");

$json = new Mapbender_JSON();

$lineText = $_REQUEST["line"];
$polygonText = $_REQUEST["polygon"];


$floatPattern = "-?\d+(\.\d+)?";
$pointPattern = $floatPattern . " " . $floatPattern;
$ringPattern = "\(" . $pointPattern . "(, " . $pointPattern . ")*\)";
$polygonPattern = "\(" . $ringPattern . "(,( )*" . $ringPattern . ")*\)";
$singlePolygonPattern = "POLYGON( )*" . $polygonPattern;
$multiPolygonPattern = "MULTIPOLYGON( )\(" . $polygonPattern . "(,( )*" . $polygonPattern . ")*\)";$pattern = "/" . $multiPolygonPattern . "(;" . $multiPolygonPattern . ")+/";
$anyPolygonPattern = "(" . $singlePolygonPattern . ")|(" . $multiPolygonPattern . ")";
$linePattern = "LINESTRING \(" . $pointPattern . ",( )*" . $pointPattern . "(,( )*" . $pointPattern . ")*\)";

$pattern = "/" . $anyPolygonPattern . "/";
if (!preg_match($pattern, $polygonText)) {
	echo "not a polygon.";
	die();
}

$pattern = "/" . $linePattern . "/";
if (!preg_match($pattern, $lineText)) {
	echo "not a line.";
	die();
}


$sql = "SELECT astext(multi(geom)) FROM dump ((" . 
	"SELECT polygonize(geomunion(boundary('" . 
	$polygonText . "'::geometry),'" . 
	$lineText . "'::geometry))" . 
	"))" . 
	"WHERE contains('" . 
	$polygonText . "'::geometry, pointonsurface(geom)" . 
	")";

$res = db_query($sql);    

$polygonArray = array();
while ($row = db_fetch_array($res)) {
	array_push($polygonArray, $row[0]);
}

$data = array("geometries" => $polygonArray);

$output = $json->encode($data);

header("Content-type:application/x-json; charset=utf-8");
echo $output;
?>