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


class GMLMultiPoint extends GmlGeometry {

	var $pointArray = array();

	public function __construct() {
		
	}
	
	public function addPoint ($x, $y) {
		array_push($this->pointArray, array("x" => $x, "y" => $y));
	}
	
	public function toGml2 () {
		$str = "<gml:MultiPoint srsName='$this->srs'>";
		foreach ($this->pointArray as $point) {
			$str .=	"<gml:pointMember><gml:Point><gml:coordinates>";
			$str .= $point["x"] . "," . $point["y"];
			$str .= "</gml:coordinates></gml:Point>";
		}
		$str .=	"</gml:pointMember></gml:MultiPoint>";
		return $str;		
	}
	
	public function toGml3 () {
		
		$str = "<gml:MultiPoint srsName='$this->srs'>";
		foreach ($this->pointArray as $point) {
			$str .=	"<gml:pointMember><gml:Point>";
			$str .= "<gml:pos>" . $point["x"] . " " . $point["y"] . "</gml:pos>";
			$str .= "</gml:Point></gml:pointMember>";
		}
		$str .=	"</gml:MultiPoint>";
		return $str;		
		
	}

	public function isEmpty () {
		return !(count($this->pointArray) > 0);
	}
	
	public function toGeoJSON () {
		$numberlineArray = count($this->pointArray);
		$str = "";
		if ($numberlineArray > 0) {
			$str .= "{\"type\": \"MultiPoint\", \"coordinates\":[";
			
			for ($cnt =0; $cnt < $numberlineArray; $cnt++){
				if ($cnt > 0) {
					$str .= ",";
				}
				if (in_array($this->srs, $this->latLonSrs)) {
					$str .= "[" . $this->pointArray[$cnt]["y"] . "," . 
						$this->pointArray[$cnt]["x"]."]";
				}
				else {
					$str .= "[" . $this->pointArray[$cnt]["x"] . "," . 
						$this->pointArray[$cnt]["y"]."]";
				}
			}
			$str .= "]}";
			
		}
		else {
			$e = new mb_exception("GMLMultiPoint: toGeoJSON: this multiPoint is null.");
		}
		return $str;
	}
}
?>