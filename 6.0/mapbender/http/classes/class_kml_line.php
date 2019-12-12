<?php
# $Id: class_kml_line.php 3092 2008-10-01 13:50:57Z christoph $
# http://www.mapbender.org/index.php/class_wmc.php
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

require_once(dirname(__FILE__)."/../classes/class_point.php");
require_once(dirname(__FILE__)."/../classes/class_kml_geometry.php");

/**
 * Represents a line string, consisting of an array of points.
 *  
 * @package KML 
 */
class KMLLine extends KMLGeometry {
	/**
	 * @param string the content of the geometry tag of a KML. Note: KML 2.2 uses a 
	 *               comma separated list, while KML OWS uses the GML syntax with 
	 *               blanks as separators 
	 */
	public function __construct ($geometryString, $epsg) {
		# KML 2.2
		if (preg_match("/,/", $geometryString)) {
			$pointArray = explode(" ", $geometryString);
			for ($i=0; $i < count($pointArray); $i++) {
				#
				# Some KMLs have a lot of whitespaces; this "if" is an
				# ugly hack to prevent adding empty points
				#
				if (preg_match("/,/", $pointArray[$i])) {
					$aPoint = explode(",", $pointArray[$i]);
					// KML only supperts EPSG 4326, so
					// the coordinates are transformed 
					$pt = new Mapbender_point($aPoint[0], $aPoint[1], $aPoint[2], $epsg);
					if (isset($epsg) && $epsg != 4326) {
						$pt->transform(4326);
					}
					$point = array("x" => $pt->x, "y" => $pt->y, "z" => $pt->z);
					array_push($this->pointArray, $point);
				}
			}
		}
		else {
			$pointArray = explode(" ", $geometryString);
			for ($i=0; $i < count($pointArray); $i+=3) {
				#
				# Some KMLs have a lot of whitespaces; this "if" is an
				# ugly hack to prevent adding empty points
				#
				if ($pointArray[$i] && $pointArray[$i+1]) {
					$pt = new Mapbender_point($pointArray[$i], $pointArray[$i+1], $pointArray[$i+2], $epsg);
					// KML only supperts EPSG 4326, so
					// the coordinates are transformed 
					if (isset($epsg) && $epsg != 4326) {
						$pt->transform(4326);
					}
					$point = array("x" => $pt->x, "y" => $pt->y, "z" => $pt->z);
					array_push($this->pointArray, $point);
				}
			}
		}
	}

	/**
	 * @return string a string representation of the object, currently geoJSON.
	 */
	public function __toString() {
		return $this->toGeoJSON();
	}

	/**
	 * @return string a geoJSON string representation of the object.
	 */
	public function toGeoJSON () {
		$numberOfPoints = count($this->pointArray);
		if ($numberOfPoints > 0) {
			$str = "";
			for ($i=0; $i < $numberOfPoints; $i++) {
				if ($i > 0) {
					$str .= ",";
				}
				if ($this->pointArray[$i]["z"]) {
					$str .= "[".$this->pointArray[$i]["x"].",".$this->pointArray[$i]["y"].",".$this->pointArray[$i]["z"]."]";
				}
				else {
					$str .= "[".$this->pointArray[$i]["x"].",".$this->pointArray[$i]["y"]."]";
				}
			}
			return "{\"type\": \"LineString\", \"coordinates\":[" . $str . "]}";
		}

		$e = new mb_exception("KMLLine: toGeoJSON: this line has no points.");
		return "";
	}

	/**
	 * @return array an array of points as associative array, coordinates as ["x"] and ["y"] and ["z"]
	 */
	public function getPointArray () {
		return $this->pointArray;
	}
	
	/**
	 * An array of points, with a point being an associative 
	 * array consisting of attributes "x" and "y" and "z"
	 */
	protected $pointArray = array();
}
?>
