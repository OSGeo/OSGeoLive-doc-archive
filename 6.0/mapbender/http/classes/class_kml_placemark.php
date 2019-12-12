<?php
# $Id: class_kml_placemark.php 5985 2010-04-20 14:44:14Z kmq $
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

/**
 * A Placemark consists of a geometry of type {@link KMLPoint}, {@link KMLPolygon}, 
 * {@link KMLLineString} or {@link KMLMultiGeometry} and an array of properties.
 * 
 * @package KML
 */
class KMLPlacemark {
	/**
	 * @param	KMLGeometry	$aGeometry 
	 */
	public function __construct ($aGeometry) {
		$this->geometry = $aGeometry;
	}
	
	/**
	 * @return	string	a string representation of the object, currently geoJSON.
	 */
	public function __toString() {
		return $this->toGeoJSON();
	}

	/**
	 * @param 	mixed 	$key	The key of the property.
	 * @param	mixed	$value	The value of the property.
	 */
	public function setProperty ($key, $value) {
		// TODO: keys are unique, may be not intended in KML OWS5
		$this->properties[$key] = $value;
	}
	
	/**
	 * @param  string $key the property name
	 * @return mixed       the property value; if none exists, null.
	 */
	public function getProperty ($key) {
		if (array_key_exists($key, $this->properties)) {
			return $this->properties[$key];
		}
		$e = new mb_exception("class_kml_placemark.php: getProperty: no value for key '" . $key . "'");
		return null;
	}
	
	/**
	 * @return array the array of properties.
	 */
	public function getProperties () {
		return $this->properties;
	}	

	/**
	 * @return string a geoJSON string representation of the object.
	 */
	public function toGeoJSON () {
		$str = "";
		if ($this->geometry !== null) {
			$str .= "{\"type\":\"Feature\", ";
//			$str .= "\"sid\":\"id". time() ."\", ";
			$str .= "\"geometry\": ";
			$str .= $this->geometry->toGeoJSON();
			$str .= ", \"properties\": {";
			$cnt = 0;
			foreach ($this->properties as $key => $value) {
				if ($cnt > 0) {
					$str .= ",";
				}
				$str .= "\"" . $key . "\":" . json_encode($value) . "";
				$cnt ++;
			}
			$str .= "}}";
		}
		else {
			$e = new mb_exception("KMLPlacemark: toGeoJSON: this geometry is null!");
		}
		return $str;
	}
	
	/**
	 * @return KMLGeometry the geometry of this placemark
	 */
	public function getGeometry () {
		return $this->geometry;
	}
	
	/**
	 * @return string class name of geometry
	 */
	public function getGeometryType () {
		if (KMLGeometry::isGeometry($this->geometry)) {
			return $this->geometry->getGeometryType();
		}
		$e = new mb_exception("class_kml_placemark.php: getGeometryType: Geometry not set.");
		return "";
	}

	private $geometry;
	private $properties = array();
}
?>
