<?php

# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");

$json = new Mapbender_JSON();
$startPoint = null;
$endPoint = null;
$points = array();
$lineArray = array();

function isLinestring($string) {
	$floatPattern = "-?\d+(\.\d+)?";
	$pointPattern = $floatPattern . " " . $floatPattern;
	$linePattern = "LINESTRING \(" . $pointPattern . ",( )*" . $pointPattern . "(,( )*" . $pointPattern . ")*\)";
	
	if(preg_match("/" . $linePattern . "/", $string)) {
		return true;
	}
	return false;
}


if(!isLinestring($_REQUEST["line1"]) OR !isLinestring($_REQUEST["line2"])) {
	die("not a line.");
}

$line1Text = $_REQUEST["line1"];
$line2Text = $_REQUEST["line2"];


// find intersection points
$intersection_sql =  sprintf("SELECT ST_AsText(multipoint.geom) as point, "
							." ST_AsText(ST_StartPoint('%s'::geometry)) AS startpoint,"
							." ST_AsText(ST_EndPoint('%s'::geometry)) AS endpoint "
							." FROM ST_Dump((SELECT ST_AsText(ST_Intersection('%s'::geometry,'%s'::geometry)))) AS multipoint"
							." ORDER BY ST_Line_Locate_Point('%s'::geometry,ST_AsText(multipoint.geom)::geometry) ASC;"
							,$line1Text,$line1Text,$line1Text,$line2Text,$line1Text);

$res = db_query($intersection_sql);    

// add first and last points to the array of intersectionpoints
while ($row = db_fetch_array($res)) {
	$startPoint = $row['startpoint'];
	$endPoint = $row['endpoint'];
	$points[] = $row['point'];
}



// if the two lines don't intersect, we just do nothing, and return the first line
if(count($points) == 0){
	$lineArray[] = $line1Text;
}else{
	$points = array_merge(array($startPoint),$points,array($endPoint));

	// go through the point array in pairs, cut into segment, and add each segment onto resultArray
	for($i = 0; $i < count($points)-1; $i++){

		$pointStartText = $points[$i];
		$pointEndText = $points[$i+1];
		
		if($i == count($points) -2 && $startPoint == $endPoint) {
			$nthSegment_sql = sprintf("SELECT ST_AsText(ST_multi(geom)) AS substring FROM "
				." ST_Dump((SELECT ST_AsText(ST_FORCE_COLLECTION(ST_Line_Substring("
					."'%s'::geometry,"
					."ST_Line_Locate_Point('%s'::geometry,'%s'::geometry),"
					."1.0"
				.")))));",
				$line1Text,$line1Text,$pointStartText);		
		} else {
			$nthSegment_sql = sprintf("SELECT ST_AsText(ST_multi(geom)) AS substring FROM "
				." ST_Dump((SELECT ST_AsText(ST_FORCE_COLLECTION(ST_Line_Substring("
					."'%s'::geometry,"
					."ST_Line_Locate_Point('%s'::geometry,'%s'::geometry),"
					."ST_Line_Locate_Point('%s'::geometry,'%s'::geometry)"
				.")))));",
				$line1Text,$line1Text,$pointStartText,$line1Text,$pointEndText);
		}
		
		$res = db_query($nthSegment_sql);    
		
		if($row = db_fetch_array($res)) {
			$lineArray[] = $row['substring'];
		}
	}
}

// OUTPUT
header("Content-type:application/x-json; charset=utf-8");
echo $json->encode(array("geometries" => $lineArray));
?>
