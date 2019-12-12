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

class GMLPoint extends GmlGeometry {

	var $point;

	public function __construct() {
		
	}

	public function setPoint ($x, $y) {
#		echo "x: " . $x . " y: " . $y . "\n";
		$this->point = array("x" => $x, "y" => $y);
	}
	
	public function toGml2 () {
		$str = "<gml:Point srsName='$this->srs'><gml:coordinates>";
		$str .= $this->point["x"] . "," . $this->point["y"];
		$str .= "</gml:coordinates></gml:Point>";
		return $str;		
	}
	
	public function toGml3 () {
		$str = "<gml:Point srsName='$this->srs'><gml:pos>";
		$str .= $this->point["x"] . " " . $this->point["y"];
		$str .= "</gml:pos></gml:Point>";
		return $str;		
	}

	public function isEmpty () {
		return ($this->point ? false : true);
	}

	public function toGeoJSON () {
		$str = "";
		if ($this->point) {
			$str .= "{\"type\": \"Point\", \"coordinates\":";
			if (in_array($this->srs, $this->latLonSrs)) {
				$str .= "[".$this->point["y"].",".$this->point["x"]."]";
			}
			else {
				$str .= "[".$this->point["x"].",".$this->point["y"]."]";
			}
			$str .= "}";
		}
		else {
			$e = new mb_exception("GMLPoint: toGeoJSON: this point is null.");
		}
		return $str;
	}
	
	public function getBbox () {
		$p = new Mapbender_point(
			$this->point["x"],
			$this->point["y"],
			$this->srs	
		);
		return new Mapbender_bbox($p, $p, $this->srs);
	}
}
?>