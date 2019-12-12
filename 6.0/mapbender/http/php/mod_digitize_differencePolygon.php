<?php
# $Id: mod_digitize_splitPolygon.php 2905 2008-09-03 12:40:37Z christoph $
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

$polygon1Text = $_REQUEST["polygon1"];
$polygon2Text = $_REQUEST["polygon2"];


$floatPattern = "-?\d+(\.\d+)?";
$pointPattern = $floatPattern . " " . $floatPattern;
$ringPattern = "\(" . $pointPattern . "(, " . $pointPattern . ")*\)";
$polygonPattern = "\(" . $ringPattern . "(,( )*" . $ringPattern . ")*\)";
$singlePolygonPattern = "POLYGON( )*" . $polygonPattern;
$multiPolygonPattern = "MULTIPOLYGON( )\(" . $polygonPattern . "(,( )*" . $polygonPattern . ")*\)";$pattern = "/" . $multiPolygonPattern . "(;" . $multiPolygonPattern . ")+/";
$anyPolygonPattern = "(" . $singlePolygonPattern . ")|(" . $multiPolygonPattern . ")";

$pattern = "/" . $anyPolygonPattern . "/";
if (!preg_match($pattern, $polygon1Text)) {
	echo "Polygon 1 not a polygon.";
	die();
}

if (!preg_match($pattern, $polygon2Text)) {
	echo "Polygon 2 not a polygon.";
	die();
}


$sql = "SELECT " . 
	"overlaps(" . 
	"'" . $polygon1Text . "'::geometry," . 
	"'" . $polygon2Text . "'::geometry" . 
	") AS o," . 
	"intersects(" . 
	"'" . $polygon1Text . "'::geometry," . 
	"'" . $polygon2Text . "'::geometry" . 
	") AS i," . 
	"contains(" . 
	"'" . $polygon1Text . "'::geometry," . 
	"'" . $polygon2Text . "'::geometry" . 
	") AS c," . 
	"touches(" . 
	"'" . $polygon1Text . "'::geometry," . 
	"'" . $polygon2Text . "'::geometry" . 
	") AS t," . 
	"within(" . 
	"'" . $polygon1Text . "'::geometry," . 
	"'" . $polygon2Text . "'::geometry" . 
	") AS w"; 
$res = db_query($sql);    

$row = db_fetch_array($res);

if ($row["i"] == "t") {
	// calculate difference
	$sql = "SELECT astext(multi(difference(" . 
		"'" . $polygon1Text . "'::geometry," . 
		"'" . $polygon2Text . "'::geometry" . 
		")))";
		
}
else {
	// calculate union
	$sql = "SELECT astext(multi(st_union(" . 
		"'" . $polygon1Text . "'::geometry," . 
		"'" . $polygon2Text . "'::geometry" . 
		")))";
}
$res = db_query($sql);    

$polygonArray = array();
while ($row = db_fetch_array($res)) {
	array_push($polygonArray, $row[0]);
}

$data = array("polygons" => $polygonArray);

$output = $json->encode($data);

header("Content-type:application/x-json; charset=utf-8");
echo $output;
?>