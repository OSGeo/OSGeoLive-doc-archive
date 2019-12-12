<?php
# $Id: mod_digitize_mergePolygon.php 4691 2009-09-25 10:39:48Z christoph $
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

$line1 = $_REQUEST["line1"];
$line2 = $_REQUEST["line2"];

#$lineList = $_REQUEST["lines"];

$floatPattern = "-?\d+(\.\d+)?";
$pointPattern = $floatPattern . " " . $floatPattern;
$linePattern = "LINESTRING \(" . $pointPattern . ",( )*" . $pointPattern . "(,( )*" . $pointPattern . ")*\)";

$pattern = "/" . $linePattern . "/";

#if (!preg_match($pattern, $lineList)) {
#	echo "not a line.";
#	die();
#}

if (!preg_match($pattern, $line1)) {
	echo "Line 1 not a line.";
	die();
}

if (!preg_match($pattern, $line2)) {
	echo "Line 2 not a line.";
	die();
}

#$lineArray = explode(";", $lineList);

//check for valid lines for merging (2 lines)
$sql = "SELECT "; 
$sql .= "ST_StartPoint(ST_GeomFromText('" . $line1 . "')) = ST_StartPoint(ST_GeomFromText('" . $line2 . "')) as a, ";
$sql .= "ST_StartPoint(ST_GeomFromText('" . $line1 . "')) = ST_EndPoint(ST_GeomFromText('" . $line2 . "')) as b, ";
$sql .= "ST_EndPoint(ST_GeomFromText('" . $line1 . "')) = ST_StartPoint(ST_GeomFromText('" . $line2 . "')) as c, ";
$sql .= "ST_EndPoint(ST_GeomFromText('" . $line1 . "')) = ST_EndPoint(ST_GeomFromText('" . $line2 . "')) as d";

$res = db_query($sql); 
$row = db_fetch_array($res);

if($row['a'] == 't' || $row['b'] == 't' || $row['c'] == 't' || $row['d'] == 't') {
	$sql = "SELECT ST_AsText(multi(st_linemerge(st_collect_garray(ARRAY[";
	$sql .= "ST_GeomFromText('" . $line1 . "'), ST_GeomFromText('" . $line2 . "')";
/*	for ($i = 0; $i < count($lineArray); $i++) {
		if ($i > 0) {
			$sql .= ", ";
		}
		$sql .= "ST_GeomFromText('" . $lineArray[$i] . "')";
	}
*/		
	$sql .= "])))) as a";
	
	#echo $sql;
	$res = db_query($sql);    
	
	$lineArray = array();
	$row = db_fetch_array($res);
	
	$data = array("line" => $row[0]);
	
}
else {
	$data = array("line" => "");
}

$output = $json->encode($data);

header("Content-type:application/x-json; charset=utf-8");
echo $output;
?>