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
require_once(dirname(__FILE__)."/../classes/class_bbox.php");


class GMLLine extends GmlGeometry {

	var $pointArray = array();

	public function __construct() {
		
	}

	public function addPoint ($x, $y) {
		array_push($this->pointArray, array("x" => $x, "y" => $y));
	}
	
	public function toGml2 () {
		$str = "<gml:MultiLineString srsName=\"$this->srs\">" . 
			"<gml:lineStringMember><gml:LineString><gml:coordinates>";

		$ptArray = array();
		foreach ($this->pointArray as $point) {
			$ptArray[] = $point["x"] . "," . $point["y"];
		}
		$str .= implode(" ", $ptArray);

		$str .= "</gml:coordinates></gml:LineString></gml:lineStringMember>";
		$str .= "</gml:MultiLineString>";		
		return $str;
	}
	
	public function toGml3 () {
		$str = "<gml:MultiCurve srsName=\"$this->srs\">" . 
			"<gml:curveMember><gml:LineString>";

		$ptArray = array();
		foreach ($this->pointArray as $point) {
			$ptArray[] = "<gml:pos>" . $point["x"] . " " . $point["y"] . "</gml:pos>";
		}
		$str .= implode("", $ptArray);

		$str .= "</gml:LineString></gml:curveMember>";
		$str .= "</gml:MultiCurve>";		
		return $str;
	}

	public function isEmpty () {
		return !(count($this->pointArray) > 0);
	}

	public function toGeoJSON () {
		$numberOfPoints = count($this->pointArray);
		$str = "";
		if ($numberOfPoints > 0) {
			$str .= "{\"type\": \"LineString\", \"coordinates\":[";
			for ($i=0; $i < $numberOfPoints; $i++) {
				if ($i > 0) {
					$str .= ",";
				}
				if (in_array($this->srs, $this->latLonSrs)) {
					$str .= "[".$this->pointArray[$i]["y"].",".$this->pointArray[$i]["x"]."]";
				}
				else {
					$str .= "[".$this->pointArray[$i]["x"].",".$this->pointArray[$i]["y"]."]";
				}
			}
			$str .= "]}";
		}
		else {
			$e = new mb_exception("GMLLine: toGeoJSON: this point is null.");
		}
		return $str;
	}
	
	public function getBbox () {
		$bboxArray = array();
		for ($i = 0; $i < count($this->pointArray); $i++) {
			$p = new Mapbender_point(
				$this->pointArray[$i]["x"],
				$this->pointArray[$i]["y"],
				$this->srs	
			);
			$bboxArray[]= new Mapbender_bbox($p, $p, $this->srs);
		} 
		return Mapbender_bbox::union($bboxArray);
	}
}
?>