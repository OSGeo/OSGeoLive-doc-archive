<?php
# $Id: class_wfs.php 3094 2008-10-01 13:52:35Z christoph $
# http://www.mapbender.org/index.php/class_wfs.php
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
 * An abstract class modelling an OGC web service (OWS), for example
 * Web Map Service (WMS) or Web Feature Service (WFS).
 */
abstract class Ows {
	var $id;
	var $name;
	var $title;
	var $summary;
	var $uploadUrl;
	var $getCapabilities;
	var $getCapabilitiesDoc;
	var $fees;
	var $accessconstraints;
	var $individualName;
	var $positionName;
	var $providerName;
	var $city;
	var $deliveryPoint;
	var $administrativeArea;
	var $postalCode;
	var $voice;
	var $facsimile;
	var $electronicMailAddress;
	var $country;
	var $termsofuse;
	
	
	/**
	 * Removes the namespace from a tag name.
	 * 
	 * Example: input is "topp:the_geom" will return "the_geom".
	 * 
	 * @return String
	 * @param $s String
	 */
	final protected function sepNameSpace($s) {
		$c = strpos($s, ":"); 
		if ($c > 0) {
			return substr($s, $c + 1);
		}
		return $s;
	}
	
	/**
	 * Get namespace from a tag name.
	 * 
	 * Example: input is "topp:the_geom" will return "topp".
	 * 
	 * @return String
	 * @param $s String
	 */
	final public function getNameSpace($s) {
		$c = strpos($s, ":"); 
		if ($c !== false) {
			return substr($s, 0, $c);
		}
		return $s;
	}
	
	/**
	 * Returns the conjunction character of an URL
	 * 
	 * @param String $url
	 * @return String the character "&", "?", or ""
	 */
	final public function getConjunctionCharacter ($url) {
		// does the URL contain "?"
		$pos = strpos($url, "?");

		// if yes, ...
		if ($pos > -1) { 
			// if the last character is "?", return ""
			if (substr($url, -1) == "?") { 
				return "";
			}
			// if the last character is "&", return ""
			else if (substr($url, -1) == "&") {
				return "";
			}
			// "?" exists, so the conunction character must be "&"
			return "&";
		}
		// "?" doesn't exist, so the conunction character must be "?"
		return "?";
	}
	
	final protected function get ($url) {
		$connection = new connector($url);
		$e = new mb_notice("OWS REQUEST: " . $url);
		$data = $connection->file;
		if (!$data) {
			$e = new mb_exception("OWS request returned no result: " . $url);
			return null;
		}
		return $data;
	}
	
	final protected function post ($url, $postData) {
		$connection = new connector();
		$connection->set("httpType", "post");
		$connection->set("httpContentType", "xml");
		$connection->set("httpPostData", $postData);
		
		$e = new mb_notice("OWS REQUEST: " . $url . "\n\n" . $postData);
		$data = $connection->load($url);
		if (!$data) {
			$e = new mb_exception("OWS request returned no result: " . $url . "\n" . $postData);
			return null;
		}
		return $data;
	}
	
	
}
?>