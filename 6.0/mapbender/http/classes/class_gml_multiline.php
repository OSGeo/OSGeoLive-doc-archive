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


class GMLMultiLine extends GmlGeometry {

	var $lineArray = array();

	public function __construct() {
		
	}
	
	public function addPoint ($x, $y, $i) {
            if (!is_array($this->lineArray[$i])) {
                $this->lineArray[$i] = array();
            }
            array_push($this->lineArray[$i], array("x" => $x, "y" => $y));
	}
	
	public function toGml2 () {
		$str = "<gml:MultiLineString srsName='$this->srs'>";
		foreach ($this->lineArray as $line) {
			$str .=	"<gml:lineStringMember><gml:LineString><gml:coordinates>";
			$ptArray = array();
			foreach ($line as $point) {
				$ptArray[] = $point["x"] . "," . $point["y"];
			}
			$str .= implode(" ", $ptArray);
			$str .= "</gml:coordinates></gml:LineString></gml:lineStringMember>";
		}
		$str .=	"</gml:MultiLineString>";
		return $str;		
	}
	
	public function toGml3 () {
		$str = "<gml:MultiCurve srsName='$this->srs'>";
		foreach ($this->lineArray as $line) {
			$str .=	"<gml:curveMember><gml:LineString>";
			$ptArray = array();
			foreach ($line as $point) {
				$ptArray[] = "<gml:pos>" . $point["x"] . " " . $point["y"] . "</gml:pos>";
			}
			$str .= implode("", $ptArray);
			$str .= "</gml:LineString></gml:curveMember>";
		}
		$str .=	"</gml:MultiCurve>";
		return $str;		
		
	}

	public function isEmpty () {
		return !(count($this->lineArray) > 0);
	}
	
	public function toGeoJSON () {
		$numberlineArray = count($this->lineArray);
		$str = "";
		if ($numberlineArray > 0) {
			$str .= "{\"type\": \"MultiLineString\", \"coordinates\":[";
			
			for ($cnt =0; $cnt < $numberlineArray; $cnt++){
				if ($cnt > 0) {
						$str .= ",";
					}
					$str .="[";
			
				for ($i=0; $i < count($this->lineArray[$cnt]); $i++) {
					if ($i > 0) {
						$str .= ",";
					}
					if (in_array($this->srs, $this->latLonSrs)) {
						$str .= "[".$this->lineArray[$cnt][$i]["y"].",".$this->lineArray[$cnt][$i]["x"]."]";
					}
					else {
						$str .= "[".$this->lineArray[$cnt][$i]["x"].",".$this->lineArray[$cnt][$i]["y"]."]";
					}
				}
				$str .="]";
			}
			$str .= "]}";
			
		}
		else {
			$e = new mb_exception("GMLMultiLine: toGeoJSON: this multiLine is null.");
		}
		return $str;
	}
}
?>