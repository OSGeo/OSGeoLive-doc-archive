<?php
# $Id: class_gml2.php 3099 2008-10-02 15:29:23Z nimix $
# http://www.mapbender.org/index.php/class_gml2.php
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
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_gml_geometry.php");

class GMLMultiPolygon extends GmlGeometry {

	var $polygonArray = array();
	var $innerRingArray = array();

	public function __construct() {
		
	}

	public function addPointToRing ($i, $j, $x, $y) {
		while (count($this->innerRingArray) <= $i) {
			array_push($this->innerRingArray, array());
		}
		while (count($this->innerRingArray[$i]) <= $j) {
			array_push($this->innerRingArray[$i], array());
		}
		array_push($this->innerRingArray[$i][$j], array("x" => $x, "y" => $y));
	}
	
	public function addPoint ($x, $y, $i) {
		while (count($this->polygonArray) <= $i) {
			array_push($this->polygonArray, array());
		}
		array_push($this->polygonArray[$i], array("x" => $x, "y" => $y));
	}

	public function toGml2 () {
		$str = "<gml:MultiPolygon srsName='$this->srs'>";
		for ($i = 0; $i < count($this->polygonArray); $i++) {
			$str .= "<gml:polygonMember><gml:Polygon>" . 
				"<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";

			$currentExteriorRing = $this->polygonArray[$i];
			
			$ptArray = array();
			for ($j = 0; $j < count($currentExteriorRing); $j++) {
				$point = $currentExteriorRing[$j];
				$ptArray[] = $point["x"] . "," . $point["y"];
			}
			$str .= implode(" ", $ptArray);
			$str .= "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
			
			// interior rings exist
			if (count($this->innerRingArray) > $i && count($this->innerRingArray[$i]) > 0) {

				for ($j = 0; $j < count($this->innerRingArray[$i]); $j++) {
					$currentInteriorRing = $this->innerRingArray[$i][$j];
					$str .= "<gml:innerBoundaryIs><gml:LinearRing><gml:coordinates>";
					$ptArray = array();
					for ($k = 0; $k < count($currentInteriorRing); $k++) {
						$point = $currentInteriorRing[$k];
						$ptArray[] = $point["x"] . "," . $point["y"];
					}
					$str .= implode(" ", $ptArray);
					$str .= "</gml:coordinates></gml:LinearRing></gml:innerBoundaryIs>";
				}

			}
			$str .= "</gml:Polygon></gml:polygonMember>";
		}
		$str .= "</gml:MultiPolygon>";

		return $str;		
	}
	
	public function toGml3 () {
		$str = "<gml:MultiSurface srsName='$this->srs'>";
		for ($i = 0; $i < count($this->polygonArray); $i++) {
			$str .= "<gml:surfaceMember><gml:Polygon>" . 
				"<gml:exterior><gml:LinearRing>";

			$currentExteriorRing = $this->polygonArray[$i];
			
			$ptArray = array();
			for ($j = 0; $j < count($currentExteriorRing); $j++) {
				$point = $currentExteriorRing[$j];
				$ptArray[] = "<gml:pos>" . $point["x"] . " " . $point["y"] . "</gml:pos>";
			}
			$str .= implode("", $ptArray);
			$str .= "</gml:LinearRing></gml:exterior>";
			
			// interior rings exist
			if (count($this->innerRingArray) > $i && count($this->innerRingArray[$i]) > 0) {

				for ($j = 0; $j < count($this->innerRingArray[$i]); $j++) {
					$currentInteriorRing = $this->innerRingArray[$i][$j];
					$str .= "<gml:interior><gml:LinearRing>";
					$ptArray = array();
					for ($k = 0; $k < count($currentInteriorRing); $k++) {
						$point = $currentInteriorRing[$k];
						$ptArray[] = "<gml:pos>" . $point["x"] . " " . $point["y"] . "</gml:pos>";
					}
					$str .= implode("", $ptArray);
					$str .= "</gml:LinearRing></gml:interior>";
				}

			}
			$str .= "</gml:Polygon></gml:surfaceMember>";
		}
		$str .= "</gml:MultiSurface>";

		return $str;		
		
	}

	public function isEmpty () {
		return !(count($this->polygonArray) > 0);
	}
	
	public function toGeoJSON () {
		$numberPolygonArray = count($this->polygonArray);
		$str = "";
		if ($numberPolygonArray > 0) {
			$str .= "{\"type\": \"MultiPolygon\", \"coordinates\":[";
			
			for ($cnt =0; $cnt < $numberPolygonArray; $cnt++){
				if ($cnt > 0) {
					$str .= ",";
				}
				$str .= "[";

				$str .= "[";
				for ($i=0; $i < count($this->polygonArray[$cnt]); $i++) {
					if ($i > 0) {
						$str .= ",";
					}
					if (in_array($this->srs, $this->latLonSrs)) {
						$str .= "[".$this->polygonArray[$cnt][$i]["y"].",".$this->polygonArray[$cnt][$i]["x"]."]";
					}
					else {
						$str .= "[".$this->polygonArray[$cnt][$i]["x"].",".$this->polygonArray[$cnt][$i]["y"]."]";
					}
				}
				$str .= "]";
				
				for ($i=0; $i < count($this->innerRingArray[$cnt]); $i++) {
					$str .= ",[";
					for ($j=0; $j < count($this->innerRingArray[$cnt][$i]); $j++) {
						if ($j > 0) {
							$str .= ",";
						}
						if (in_array($this->srs, $this->latLonSrs)) {
							$str .= "[".$this->innerRingArray[$cnt][$i][$j]["y"].",".$this->innerRingArray[$cnt][$i][$j]["x"]."]";
						}
						else {
							$str .= "[".$this->innerRingArray[$cnt][$i][$j]["x"].",".$this->innerRingArray[$cnt][$i][$j]["y"]."]";
						}
					}
					$str .= "]";
				}
				$str .= "]";
			}
			$str .= "]}";
			
		}
		else {
			$e = new mb_exception("GMLMultiPolygon: toGeoJSON: this multiLine is null.");
		}
		return $str;
	}
	
	public function getBbox () {
		$bboxArray = array();
        for ($i = 0; $i < count($this->polygonArray); $i++) {
        	if($this->polygonArray[$i]["x"] && $this->polygonArray[$i]["y"]) {
            	$p = new Mapbender_point(
                	$this->polygonArray[$i]["x"],
                    $this->polygonArray[$i]["y"],
                    $this->srs
                );
                $bboxArray[]= new Mapbender_bbox($p, $p, $this->srs);
           	}
     	}
        return Mapbender_bbox::union($bboxArray);
   	}
	
}
?>
