<?php
/**
 * $Id: class_kml_point.php 3092 2008-10-01 13:50:57Z christoph $
 * 
 * @link 		http://www.mapbender.org/index.php/class_wmc.php
 * @copyright 	2002 CCGIS 
 * @license		http://opensource.org/licenses/gpl-license.php
 * 				This program is free software; you can redistribute it and/or modify
 * 				it under the terms of the GNU General Public License as published by
 * 				the Free Software Foundation; either version 2, or (at your option)
 * 				any later version.
 *
 * 				This program is distributed in the hope that it will be useful,
 * 				but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 				MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 				GNU General Public License for more details.
 * 
 * 				You should have received a copy of the GNU General Public License
 * 				along with this program; if not, write to the Free Software
 * 				Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_kml_geometry.php");

/**
 * Represents a point, consisting of a single point geometry.
 * 
 * @package KML 
 */
class KMLPoint extends KMLGeometry {
	
	/**
	 * @param	string	the content of the geometry tag of a KML. Note: KML 2.2 uses a 
	 *               	comma separated list, while KML OWS uses the GML syntax with 
	 *               	blanks as separators 
	 */
	public function __construct ($geometryString, $epsg) {
		//TODO: parameter validation and exception handling
		
		// KML 2.2
		if (preg_match("/,/", $geometryString)) {
			$aPoint = explode(",", $geometryString);
			// ignore altitude
			$pt = new Mapbender_point($aPoint[0], $aPoint[1], $aPoint[2], $epsg);
		}
		else {
			$aPoint = explode(" ", $geometryString);
			// ignore altitude
			$pt = new Mapbender_point($aPoint[0], $aPoint[1], $aPoint[2], $epsg);
		}

		// KML only supperts EPSG 4326, so
		// the coordinates are transformed 
		if (isset($epsg) && $epsg != 4326) {
			$pt->transform(4326);
		}
		$this->point = array("x" => $pt->x, "y" => $pt->y, "z" => $pt->z);
	}

	/**
	 * @return	string	a string representation of the object, currently geoJSON.
	 */
	public function __toString() {
		return $this->toGeoJSON();
	}

	/**
	 * @return 	string	a geoJSON string representation of the object.
	 */
	public function toGeoJSON () {
		if ($this->point !== null) {
			if ($this->point["z"]) {
				return "{\"type\": \"Point\", \"coordinates\": [".$this->point["x"].",".$this->point["y"].",".$this->point["z"]."]}";
			}
			else {
				return "{\"type\": \"Point\", \"coordinates\": [".$this->point["x"].",".$this->point["y"]."]}";
			}
		}

		$e = new mb_exception("KMLPoint: toGeoJSON: this point is null.");
		return "";
	}

	/**
	 * @return array a point as associative array, coordinates as ["x"] and ["y"] and ["z"]
	 */
	public function getPoint () {
		return $this->point;
	}
		
	/**
	 * @var	float[] an associative array, with "x", and "y"  and "z" being float values.
	 */
	private $point;
}
?>
