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
require_once(dirname(__FILE__)."/../classes/class_gml_point.php");
require_once(dirname(__FILE__)."/../classes/class_gml_multipoint.php");
require_once(dirname(__FILE__)."/../classes/class_gml_line.php");
require_once(dirname(__FILE__)."/../classes/class_gml_multiline.php");
require_once(dirname(__FILE__)."/../classes/class_gml_polygon.php");
require_once(dirname(__FILE__)."/../classes/class_gml_multipolygon.php");


class Feature {

	var $type = "Feature";
	var $fid;
	var $geometry = false;
	var $properties = array();
	
	public function __construct() {
	}
	

	public function toGeoJSON () {
		$str = "";
		$str .= "{\"type\":\"Feature\", \"id\":\"".$this->fid."\", \"crs\":";
		
		if (!$this->geometry || !$this->geometry->srs) {
			$str .= "null, ";
		}
		else {
			$str .= "{\"type\":\"name\", \"properties\":{\"name\":\"" . $this->geometry->srs . "\"}}, ";
		}
		
		$str .= "\"geometry\": ";
		if ($this->geometry) {
			$str .= $this->geometry->toGeoJSON();
		}
		else {
			$str .= "\"\"";
		}

		
		$prop = array();
		
		$str .= ", \"properties\": ";
		$cnt = 0;
		foreach ($this->properties as $key => $value) {
				$prop[$key] = preg_replace('/\r\n|\r|\n/', '\\n', $value);
				$cnt ++;
		}

		$json = new Mapbender_JSON();
		$str .= $json->encode($prop); 
		$str .= "}";
		
		return $str;
	}
	
	public function getBbox () {
		if (is_null($this->geometry) || $this->geometry === false) {
			return null;
		}
		return $this->geometry->getBbox();
	}
}

?>