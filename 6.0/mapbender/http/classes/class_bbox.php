<?php
# $Id: class_bbox.php 7019 2010-10-04 14:23:01Z christoph $
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
require_once(dirname(__FILE__)."/class_point.php");

/**
 * A bounding box consisting of an lower left and an upper right point, and an EPSG.
 */
class Mapbender_bbox {
	var $min;
	var $max;
	var $epsg;
	
	/**
	 * @constructor
	 */
	function __construct() {
		if (func_num_args() == 5) {
			$param0 = func_get_arg(0);
			$param1 = func_get_arg(1);
			$param2 = func_get_arg(2);
			$param3 = func_get_arg(3);
			$param4 = func_get_arg(4);
		}
		else if (func_num_args() == 3) {
			$param0 = func_get_arg(0);
			$param1 = func_get_arg(1);
			$param2 = func_get_arg(2);
		}
		else {
			throw new Exception("Invalid argument count.");
			return;
		}
		
		// params are point, point, epsg
		if (is_a($param0, "Mapbender_point") && is_a($param1, "Mapbender_point") && is_string($param2) && !$param3 && !$param4) {
			$e = new mb_notice("Mapbender_bbox: constructor: point1, point2, epsg");
			$min = $param0; // is a Mapbender_point
			$max = $param1; // is a Mapbender_point
			$epsg = $param2; // is an EPSG code like "EPSG:4326"
			
			if (($min->isWestOf($max) && $min->isSouthOf($max)) || $min->equals($max)) {
				if ($min->epsg == $max->epsg && $min->epsg == $epsg) {
					$this->min = $min;
					$this->max = $max;
					$this->epsg = $epsg;
				}
				else {
					$e = new mb_exception("Mapbender_bbox: constructor: EPSG mismatch!");
				}
			}
			else {
				$e = new mb_exception("Mapbender_bbox: constructor: min (".$this->min.") is not southwest of max (".$this->max.")!");
			}
		}
		// params are x1, y1, x2, xy, epsg
		else if (is_numeric($param0) && is_numeric($param1) && is_numeric($param2) && is_numeric($param3) && is_string($param4)) {
			$e = new mb_notice("Mapbender_bbox: constructor: x1, y1, x2, y2, epsg");
			$min = new Mapbender_point(floatval($param0), floatval($param1), $param4);
			$max = new Mapbender_point(floatval($param2), floatval($param3), $param4);
			$epsg = $param4; // is an EPSG code like "EPSG:4326"
			
			if ($min->isWestOf($max) && $min->isSouthOf($max)) {
				if ($min->epsg == $max->epsg && $min->epsg == $epsg) {
					$this->min = $min;
					$this->max = $max;
					$this->epsg = $epsg;
				}
				else {
					$e = new mb_exception("Mapbender_bbox: constructor: EPSG mismatch!");
				}
			}
			else {
				$e = new mb_exception("Mapbender_bbox: constructor: min (".$this->min.") is not southwest of max (".$this->max.")!");
			}
		}
		else {
			throw new Exception("invalid parameters to Mapbender_bbox");
		}
	}

	public static function createFromLayerEpsg ($c) {
		return new Mapbender_bbox(
			$c["minx"], $c["miny"], $c["maxx"], $c["maxy"], $c["epsg"]
		);
	}

	/**
	 * Computes a new bounding box, bbox1 UNION bbox2
	 */
	static function union ($bboxArray) {
		if (count($bboxArray) == 1) {
			return array_pop($bboxArray);
		}
		elseif (count($bboxArray) >= 2) {
			
			$bbox1 = array_pop($bboxArray);
			$bbox2 = Mapbender_bbox::union($bboxArray);

			if (!($bbox1 != null && $bbox1->isValid()) && !($bbox2 != null && $bbox2->isValid())) {
				$e = new mb_exception("Mapbender_bbox: union: both parameters invalid!");
				return null;
			}
			elseif (!($bbox1 != null && $bbox1->isValid()) && ($bbox2 != null && $bbox2->isValid())) {
				$e = new mb_exception("Mapbender_bbox: union: first parameter invalid!");
				return $bbox2;
			}
			elseif (($bbox1 != null && $bbox1->isValid()) && !($bbox2 != null && $bbox2->isValid())) {
				$e = new mb_exception("Mapbender_bbox: union: second parameter invalid!");
				return $bbox1;
			}
			else {
				if ($bbox1->epsg == $bbox2->epsg) {
					$e = new mb_notice("Mapbender_bbox: union: bbox1 is: " . $bbox1);
					$e = new mb_notice("Mapbender_bbox: union: bbox2 is: " . $bbox2);
					$e = new mb_notice("Mapbender_bbox: union: merging bbox1 and bbox2...");
					return new Mapbender_bbox(Mapbender_point::min($bbox1->min, $bbox2->min), Mapbender_point::max($bbox1->max, $bbox2->max), $bbox1->epsg);
				}
				else {
					$e = new mb_exception("Mapbender_bbox: cannot process union with different EPSG codes");
				}
			}
		}
		else {
			$e = new mb_exception("Mapbender_bbox: Invalid parameter (Not an array)!");
		}
		return null;
	}
	
	/**
	 * transforms this bbox in another EPSG
	 * @param toEpsg transform the bbox to this EPSG code, example: "EPSG:4326" 
	 */
	function transform($toEpsg) {
		if ($this->isValid()) {
			$this->epsg = $toEpsg;
			$this->min->transform($toEpsg);
			$this->max->transform($toEpsg);
			return true;
		}
		return false;
	}

	/**
	 * checks if lower left and upper right coordinate are set, as well as EPSG
	 */
	function isValid() {
		if ($this->min != null && $this->max != null && $this->epsg != null) {
			return true;
		}
		$e = new mb_exception("Mapbender_bbox: this is not a valid bbox!");
		return false;
	}
	
	function toHtml () {
		return (string) $this->min->toHtml() . " | " . $this->max->toHtml(); 
	}
	
	function __toString() {
		return (string) "[" . $this->min . $this->max . " " . $this->epsg . "]"; 
	}
	
	function toJson() {
		return (string) "[" . $this->min->x . ",". $this->min->y . "," . $this->max->x . "," . $this->max->y . "]";
	}

	function toJavaScript () {
		return (string) "{" .
			"srs: '" . $this->epsg . "'," .
			"extent: new Mapbender.Extent(" .
			$this->min->x . ", " .
			$this->min->y . ", " .
			$this->max->x . ", " .
			$this->max->y . ")}";
	}
}
?>
