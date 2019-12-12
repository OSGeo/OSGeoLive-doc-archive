<?php
# $Id: class_kml_linearring.php 3092 2008-10-01 13:50:57Z christoph $
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

require_once(dirname(__FILE__)."/../classes/class_kml_line.php");

/**
 * Represents a linear ring, consisting of an array of points. 
 * The first and last point must be identical (no validation up to now)
 * 
 * @package KML 
 */
class KMLLinearRing extends KMLLine {

	/**
	 * The only difference from the method of the super class is the exception.
	 * 
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
			return "[" . $str . "]";
		}

		$e = new mb_exception("KMLLinearRing: toGeoJSON: no points in this linear ring.");
		return "";
	}
}
?>
