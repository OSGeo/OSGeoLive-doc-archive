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
require_once(dirname(__FILE__)."/../classes/class_gml_factory.php");
require_once(dirname(__FILE__)."/../classes/class_gml_2_factory.php");
require_once(dirname(__FILE__)."/../classes/class_gml_3_factory.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");


class UniversalGmlFactory extends GmlFactory {

	/**
	 * This function checks if the XML contains a tag "gml:posList"
	 * If yes, the xml is considered to be a GML3 document,
	 * otherwise GML2.
	 * 
	 * This has to be done another way; it is just a quick hack.
	 * 
	 * @return Boolean
	 * @param $xml String
	 */
	private function getVersionFromXml ($xml) {
		$simpleXml = simplexml_load_string($xml);
		if ($simpleXml === false) {
			return null;
		}
		$simpleXml->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
		
		$nodeArray = $simpleXml->xpath("//gml:posList");
		if (count($nodeArray) > 0 ) {
			return "3";
		}

		$nodeArray = $simpleXml->xpath("//gml:pos");
		if (count($nodeArray) > 0 ) {
			return "3";
		}

		return "2";
		throw new Exception("GML version could not be determined from XML.");
	}

	/**
	 * Creates a GML object from a GeoJSON (http://www.geojson.org) String
	 * 
	 * @return Gml
	 * @param $geoJson String
	 */
	public function createFromGeoJson ($geoJson, $wfsConf = null) {
		if (is_a($wfsConf, "WfsConfiguration")) {
			$wfsFactory = new UniversalWfsFactory();
			$myWfsFactory = $wfsFactory->createFromDb($wfsConf->wfsId);
			if (is_a($myWfsFactory, "Wfs_1_1")) {
				$gml3Factory = new Gml_3_Factory();
				return $gml3Factory->createFromGeoJson($geoJson);
			}
			if (is_a($myWfsFactory, "Wfs_1_0")) {
				$gml2Factory = new Gml_2_Factory();
				return $gml2Factory->createFromGeoJson($geoJson);
			}
			throw new Exception ("UniversalGmlFactory: Unknown WFS version");
		}
		else {
			$gml2Factory = new Gml_2_Factory();
			return $gml2Factory->createFromGeoJson($geoJson);
		}
	}
	
	/**
	 * Creates a GML object by parsing its XML representation. 
	 * 
	 * The GML version is determined by parsing 
	 * the XML document up-front.
	 * 
	 * @return Wfs
	 * @param $xml String
	 */
	public function createFromXml ($xml, $wfsConf) {
		try {
			$version = $this->getVersionFromXml($xml);

			switch ($version) {
				case "2":
					$e = new mb_warning("Using GML2 Factory...");
					$factory = new Gml_2_Factory();
					break;
				case "3":
					$e = new mb_warning("Using GML3 Factory...");
					$factory = new Gml_3_Factory();
					break;
				default:
					throw new Exception("Unknown GML version " . $version . " in XML: \n\n" . $xml);
					break;
			}
			return $factory->createFromXml($xml, $wfsConf);
		}
		catch (Exception $e) {
			new mb_exception($e);
			return null;
		}
	}
}
?>