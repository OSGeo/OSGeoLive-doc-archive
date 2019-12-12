<?php
# $Id: class_kml_geometry.php 2684 2008-07-22 07:26:19Z christoph $
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

require_once(dirname(__FILE__)."/../classes/class_kml_polygon.php");
require_once(dirname(__FILE__)."/../classes/class_kml_linearring.php");
require_once(dirname(__FILE__)."/../classes/class_kml_line.php");
require_once(dirname(__FILE__)."/../classes/class_kml_point.php");
require_once(dirname(__FILE__)."/../classes/class_kml_multigeometry.php");

/**
 * An abstract class representing a geometry, which is any of the following:
 * {@link KMLPoint}, {@link KMLPolygon}, {@link KMLLine} or 
 * {@link KMLMultiGeometry}
 *
 * @package KML 
 * @abstract
 */
abstract class KMLGeometry {

	/**
	 * @param  object $obj 	an object.
	 * @return bool			true, if the object is of type {@link KMLLine},
	 * 						{@link KMLPoint}, {@link KMLPolygon} or
	 * 						{@link KMLMultiGeometry}, or instance of 
	 * 						another class extending KMLGeometry.
	 */
	public static function isGeometry($obj) {
		if ($obj instanceof KMLGeometry) {
			return true;
		}
		
		//TODO: not sure if this type determination works
		$type = get_class($obj) || gettype($obj);
		$e = new mb_warning("class_kml_geometry.php: isGeometry: not a geometry, but " . $type);
		return false;
	}
	
	public function getGeometryType () {
		return get_class($this);
	} 
}
?>
