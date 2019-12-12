<?php
# $Id: class_point.php 7166 2010-11-26 10:17:26Z verenadiewald $
# http://www.mapbender.org/index.php/
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
 * A Mapbender_point is a 2- or 3-dimensional point with an EPSG. 
 */
class Mapbender_point {
	var $x;
	var $y;
	var $z;
	var $epsg;
	
	/**
	 * @constructor
	 */
	function __construct() {
		if (func_num_args() == 4) {
			$x = func_get_arg(0);
			$y = func_get_arg(1);
			$z = func_get_arg(2);
			$epsg = func_get_arg(3);
		}
		else if (func_num_args() == 3) {
			$x = func_get_arg(0);
			$y = func_get_arg(1);
			$z = false;
			$epsg = func_get_arg(2);
		}
		else {
			return;
		}

		if (!isset($x) || !isset($y) || !isset($epsg)) {
			$e = new mb_exception("Mapbender_point: constructor: some parameters are not set (set (x: ".$x.", y: ".$y.", z: ".$z.", epsg:".$epsg.")!");
		}
		$this->x = is_array($x)&&count($x)>0 ? $x[0] : $x;
		$this->y = is_array($y)&&count($y)>0 ? $y[0] : $y;
		$this->z = is_array($z)&&count($z)>0 ? $z[0] : $z;
		$this->epsg = $epsg;
	}
	
	/**
	 * computes a new point with the minimal coordinates of this point and $point
	 */
	static function min ($point1, $point2) {
		if ($point1->epsg == $point2->epsg) {
			if ($point1->isWestOf($point2)) {
				$minx = $point1->x;
			}
			else {
				$minx = $point2->x;
			}
			if ($point1->isSouthOf($point2)) {
				$miny = $point1->y;
			}
			else {
				$miny = $point2->y;
			}
			return new Mapbender_point($minx, $miny, $point1->epsg);
		}
		else {
			$e = new mb_exception("Mapbender_point: cannot process min with different EPSG codes");
		}
	}
	
	/**
	 * computes a new point with the maximal coordinates of this point and $point
	 */
	static function max ($point1, $point2) {
		if ($point1->epsg == $point2->epsg) {
			if ($point1->isWestOf($point2)) {
				$maxx = $point2->x;
			}
			else {
				$maxx = $point1->x;
			}
			if ($point1->isSouthOf($point2)) {
				$maxy = $point2->y;
			}
			else {
				$maxy = $point1->y;
			}
			return new Mapbender_point($maxx, $maxy, $point1->epsg);
		}
		else {
			$e = new mb_exception("Mapbender_point: cannot process min with different EPSG codes");
		}
	}
	
	function equals($point) {
		if ($this->x === $point->x &&
			$this->y === $point->y && 
			$this->z === $point->z && 
			$this->epsg === $point->epsg 
		) {
			return true;
		}
	}

	function isWestOf($point) {
		if ($this->x < $point->x) {
			return true;
		}
	}

	function isSouthOf($point) {
		if ($this->y < $point->y) {
			return true;
		}
	}
	
	/**
	 * Addition
	 * 
	 * @param anotherPoint another Mapbender_point
	 */
	function plus ($anotherPoint) {
		return new Mapbender_point($this->x + $anotherPoint->x, $this->y + $anotherPoint->y, $this->epsg);
	}

	/**
	 * Subtraction
	 * 
	 * @param anotherPoint another Mapbender_point
	 */
	function minus ($anotherPoint) {
		return $this->plus($anotherPoint->times(-1));
	}
	
	/**
	 * Scalar multiplication
	 * 
	 * @param aFloat a floating point number
	 */
	function times ($aFloat) {
		return new Mapbender_point($this->x * $aFloat, $this->y * $aFloat, $this->epsg);
	}

	/**
	 * transforms this point to another EPSG
	 * 
	 * @param {Integer} toEpsg the coordinates are transformed to this EPSG code.
	 */
	function transform($toEpsg) {
		if(SYS_DBTYPE=='pgsql'){
			$currentEpsg = preg_replace("/EPSG:/", "", $this->epsg);
			$targetEpsg = preg_replace("/EPSG:/", "", $toEpsg);
			db_begin();
			$sql = "SELECT X(transform(GeometryFromText('POINT(".$this->x." ".$this->y.")',".$currentEpsg."),".$targetEpsg.")) as x, ";
			$sql .= "Y(transform(GeometryFromText('POINT(".$this->x." ".$this->y.")',".$currentEpsg."),".$targetEpsg.")) as y, ";
			if (isset($this->z)) {
				$sql .= "Z(transform(GeometryFromText('POINT(".$this->x." ".$this->y." ".$this->z.")',".$currentEpsg."),".$targetEpsg.")) as z";
			}
			$res = db_query($sql);
			db_commit();
			if (isset($this->z)) {
				$point = new Mapbender_point(db_result($res,0,"x"), db_result($res,0,"y"), db_result($res,0,"z"), $toEpsg);
			}
			else {
				$point = new Mapbender_point(db_result($res,0,"x"), db_result($res,0,"y"), $toEpsg);
			}
			$this->x = $point->x;
			$this->y = $point->y;
			$this->z = $point->z;
			$this->epsg = $point->epsg;
		}
		else {
			$e = new mb_exception("transformCoordinates needs PostgreSQL");
		}
	}
	
	function toHtml () {
		$str = "";

		$xArray = explode(".", strval($this->x));
		$str .= $xArray[0] . "°";
		if ($xArray[1]) {
			$str .= $xArray[1] . "'";
		}
		$str .= " O / ";
		
		$yArray = explode(".", strval($this->y));
		$str .= $yArray[0] . "°";
		if ($yArray[1]) {
			$str .= $yArray[1] . "'";
		}
		$str .= " N";
		return $str;
		
	}
	
	function __toString() {
		return (string) "(" . $this->x . "," . $this->y . "," . $this->z . "," . $this->epsg . ")";
	}
}
?>
