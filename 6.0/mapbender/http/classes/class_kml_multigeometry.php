<?php
# $Id: class_kml_multigeometry.php 2684 2008-07-22 07:26:19Z christoph $
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

require_once(dirname(__FILE__)."/../classes/class_kml_geometry.php");

/**
 * Represents a multi geometry, consisting of an array of geometries 
 * ({@link KMLPoint}, {@link KMLPolygon}, {@link KMLLine} and 
 * {@link KMLMultiGeometry} allowed)
 * 
 * @package KML
 */
class KMLMultiGeometry extends KMLGeometry {
	
	/**
	 * Creates an empty multi geometry. Geometries may be added via {@link KMLMultiGeometry::append()}.
	 */
	public function __construct () {
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
		$numberOfGeometries = count($this->geometryArray);
		if ($numberOfGeometries > 0) {
			$str = "";
			for ($i=0; $i < $numberOfGeometries; $i++) {
				if ($i > 0) {
					$str .= ",";
				}
				$str .= $this->geometryArray[$i]->toGeoJSON();
			}
			return "{\"type\": \"GeometryCollection\", \"geometries\": [" . $str . "]}";
		}

		$e = new mb_exception("KMLMultiGeometry: toGeoJSON: this geometryArray is empty.");
		return "";
	}

	/**
	 * Appends a new geometry to this multi geometry.
	 * 
	 * @param  object	$aGeometry 	should be of type {@link KMLLine}, {@link KMLPoint}, 
	 * 								{@link KMLPolygon} or {@link KMLMultiGeometry}. 
	 * @return bool		true, if appending the geometry succeeded; else false.
	 */
	public function append ($aGeometry) {
		if (KMLGeometry::isGeometry($aGeometry)) {
			array_push($this->geometryArray, $aGeometry);
			return true;
		}
		return false;
	}

	private $geometryArray = array();
}
?>
