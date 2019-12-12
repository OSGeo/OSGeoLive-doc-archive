<?php
/**
 * @version 	$Id: class_kml_polygon.php 2684 2008-07-22 07:26:19Z christoph $
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
require_once(dirname(__FILE__)."/../classes/class_kml_linearring.php");

/**
 * Represents a polygon, consisting of 1 outer boundary and 0..n inner boundaries 
 * (these boundaries are of type {@link KMLLinearRing})
 * 
 * @package KML
 */
class KMLPolygon extends KMLGeometry {

	/**
	 * @param 	KMLLinearRing	$aLinearRing	the outer ring of the polygon
	 */
	public function __construct ($aLinearRing) {
		if ($aLinearRing instanceof KMLLinearRing) {
			$this->outerBoundary = $aLinearRing;
		}
		else {
			$e = new mb_exception("class_kml_polygon.php: __construct: parameter not a linear ring, but a " . get_class($aLinearRing));
		}
	}

	/**
	 * @return	string	a string representation of the object, currently 
	 * 					{@link http://www.geojson.org GeoJSON}
	 */
	public function __toString() {
		return $this->toGeoJSON();
	}
	
	/**
	 * @return	string	the geoJSON representation of the object
	 */
	public function toGeoJSON () {
		if ($this->outerBoundary !== null) {
			$str = $this->outerBoundary->toGeoJSON();
			
			$numberOfInnerBoundaries = count($this->innerBoundaryArray);
			if ($numberOfInnerBoundaries > 0) {
				$str .= ", ";
				for ($i=0; $i < $numberOfInnerBoundaries; $i++) {
					if ($i > 0) {
						$str .= ",";
					}
					$str .= $this->innerBoundaryArray[$i]->toGeoJSON();
				}
			}
			return "{\"type\": \"Polygon\", \"coordinates\": [" . $str . "]}";
		}
		
		$e = new mb_exception("KMLPolygon: toGeoJSON: this point is null.");
		return "";
	}

	/**
	 * Cuts a hole in the polygon.
	 * 
	 * @param	KMLLinearRing	$aLinearRing	the linear ring describing the hole that is being cut.
	 * @return	bool							true, if the parameter is a linear ring; else false
	 */
	public function appendInnerBoundary ($aLinearRing) {
		if ($aLinearRing instanceof KMLLinearRing) {
			array_push($this->innerBoundaryArray, $aLinearRing);
			return true;
		}
		$e = new mb_exception("class_kml_polygon.php: appendInnerBoundary: parameter not a linear ring.");
		return false;
	}

	/**
	 * @var	KMLLinearRing	The outer boundary of the polygon
	 */
	private $outerBoundary;

	/**
	 * @var	KMLLinearRing[]	The inner boundaries (holes) of the polygon
	 */
	private $innerBoundaryArray = array();
}
?>
