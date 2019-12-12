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
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");

/**
 * Creates GML objects from GML documents.
 * 
 * @return Gml
 */
abstract class GmlFactory {

	public function removeWhiteSpace ($string) {
		$str = preg_replace("/\>(\s)+\</", "><", trim($string));
//		$str = preg_replace("/\\n/", "\\n", $str);
		return $str;
		
	}
	
	/**
	 * Creates a GML object from a GeoJSON (http://www.geojson.org) String
	 * 
	 * @return Gml
	 * @param $geoJson String
	 */
	public function createFromGeoJson ($geoJson, $gml) {
		$json = new Mapbender_JSON ();
		$jsonObj = $json->decode($geoJson);

		// check if valid feature collection
		if (strtoupper($jsonObj->type) != "FEATURECOLLECTION" || !is_array($jsonObj->features)) {
			$e = new mb_exception("Not a valid GeoJSON Feature Collection: " . $geoJson);
			return null;
		}

		$gml->featureCollection = new FeatureCollection();
		foreach ($jsonObj->features as $currentFeature) {
			$feature = new Feature();
			if (!is_object($currentFeature->crs) || $currentFeature->crs->type !== "name") {
				$e = new mb_exception("Feature doesn't have a SRS.");
				return null;
			}
			$srs = $currentFeature->crs->properties->name;

			// set geometry
			if (is_object($currentFeature->geometry)) {
				$currentGeometry = $currentFeature->geometry;
				switch (strtoupper($currentGeometry->type)) {
					case "POLYGON":
						$geometry = new GMLPolygon();
						for ($i = 0; $i < count($currentGeometry->coordinates); $i++) {
							$currentRing = $currentGeometry->coordinates[$i];
							
							foreach ($currentRing as $coords) {
								if (in_array($srs, $geometry->latLonSrs)) {
									list($y, $x) = $coords;
								}
								else {
									list($x, $y) = $coords;
								}
	
								// exterior ring							
								if (0 == $i) {
									$geometry->addPoint($x, $y);
								}
								// interior ring
								else {
									$geometry->addPointToRing($i, $x, $y);
								}
							}
						}
						break;
					case "POINT":
						$geometry = new GMLPoint();
						if (in_array($srs, $geometry->latLonSrs)) {
							list($y, $x) = $currentGeometry->coordinates;
						}
						else {
							list($x, $y) = $currentGeometry->coordinates;
						}

						$geometry->setPoint($x, $y);
						break;
					case "MULTIPOINT":
						$geometry = new GMLMultiPoint();
						for ($i = 0; $i < count($currentGeometry->coordinates); $i++) {
							$currentPoint = $currentGeometry->coordinates[$i];
							if (in_array($srs, $geometry->latLonSrs)) {
								list($y, $x) = $currentPoint;
							}
							else {
								list($x, $y) = $currentPoint;
							}
							$geometry->addPoint($x, $y);
						}
						break;
					case "LINESTRING":
						$geometry = new GMLLine();
						for ($i = 0; $i < count($currentGeometry->coordinates); $i++) {
							$currentLinePoint = $currentGeometry->coordinates[$i];
							if (in_array($srs, $geometry->latLonSrs)) {
								list($y, $x) = $currentLinePoint;
							}
							else {
								list($x, $y) = $currentLinePoint;
							}
							$geometry->addPoint($x, $y);
						}
						break;
					case "MULTIPOLYGON":
						$geometry = new GMLMultiPolygon();
						for ($i = 0; $i < count($currentGeometry->coordinates); $i++) {
							$currentPolygon = $currentGeometry->coordinates[$i];
								
							for ($j = 0; $j < count($currentPolygon); $j++) {
								$currentRing = $currentPolygon[$j];
								
								foreach ($currentRing as $coords) {
									if (in_array($srs, $geometry->latLonSrs)) {
										list($y, $x) = $coords;
									}
									else {
										list($x, $y) = $coords;
									}
		
									// exterior ring							
									if (0 == $j) {
										$geometry->addPoint($x, $y, $i);
									}
									// interior ring
									else {
										$geometry->addPointToRing($i, $j-1, $x, $y);
									}
								}
							}
						}
						break;
					case "MULTILINESTRING": // not tested!
						$geometry = new GMLMultiLine();
						for ($i = 0; $i < count($currentGeometry->coordinates); $i++) {
							$currentLine = $currentGeometry->coordinates[$i];
							foreach ($currentLine as $currentLinePoint) {
								if (in_array($srs, $geometry->latLonSrs)) {
									list($y, $x) = $currentLinePoint;
								}
								else {
									list($x, $y) = $currentLinePoint;
								}
								$geometry->addPoint($x, $y, $i);
							}
						}
						break;
					case "GEOMETRYCOLLECTION":
						$e = new mb_exception($currentGeometry->type . " are not supported!");
						return null;
						break;
					default:
						$e = new mb_exception($currentGeometry->type . " is not a valid geometry type");
						return null;
						break;
				}
				// add the geometry to the feature
				$geometry->srs = $srs;
				$feature->geometry = $geometry;
			}
			else {
				$e = new mb_exception("This feature does not have a geometry.");
				return null;
			}
			

			// set fid and properties
			if (is_object($currentFeature->properties)) {
				foreach ($currentFeature->properties as $pName => $pValue) {
					if ("fid" == $pName) {
						$feature->fid = $pValue;
					}
					else {
						$feature->properties[$pName] = $pValue;
					}
				}
			}

			$gml->featureCollection->addFeature($feature);
		}
		return $gml;
	}

	/**
	 * Creates GML objects from GML documents.
	 * 
	 * @return Gml
	 * @param $xml String
	 */
	public function createFromXml ($xml, $wfsConf, $gml) {
		try {
			$xml = $this->removeWhiteSpace($xml);
			$gmlDoc = new SimpleXMLElement($xml);
			
			// we need to find the name and namespaces of the featuretype 
			// used in this WFS configuration
			$wfsFactory = new UniversalWfsFactory();
			$myWfs = $wfsFactory->createFromDb($wfsConf->wfsId);
			if (is_null($myWfs)) {
				new mb_exception("class_gml_factory.php: createFromXml: WFS not found! ID: " . $wfsConf->wfsId);
				return null;
			}
			$featureType = $myWfs->findFeatureTypeById($wfsConf->featureTypeId);
			
			// register namespace of feature type
			$pos = strpos($featureType->name, ":");
			if ($pos !== false) {
				$ns = substr($featureType->name, 0, $pos);
				$url = $featureType->getNamespace($ns);
				$gmlDoc->registerXPathNamespace($ns, $url);
			}
			
			$gmlDoc->registerXPathNamespace('xls', 'http://www.opengis.net/xls');
			$gmlDoc->registerXPathNamespace('wfs', 'http://www.opengis.net/wfs');
			$gmlDoc->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
			
			// build feature collection
			$gml->featureCollection = new FeatureCollection();
			
			// segments of the featureCollection
			//
			// for *non* Mapserver WFS
			$gmlFeatureMembers = $gmlDoc->xpath("//gml:featureMember");
			if (count($gmlFeatureMembers) > 0) {
				foreach ($gmlFeatureMembers as $gmlFeatureMember) {
					$gmlfeatureMember_dom = dom_import_simplexml($gmlFeatureMember);
					$feature = new Feature();
					$this->parseFeature($gmlfeatureMember_dom->firstChild, $feature, $wfsConf);
					if (isset($feature->geometry)) {
						$gml->featureCollection->addFeature($feature);
					}
				}
			}
			// for Mapserver WFS
			else {
				// segments of the featureCollection
				$gmlFeatureMembers = $gmlDoc->xpath("//" . $featureType->name);
	
				if (count($gmlFeatureMembers) > 0) {
					foreach ($gmlFeatureMembers as $gmlFeatureMember) {
						$gmlfeatureMember_dom = dom_import_simplexml($gmlFeatureMember);
						$feature = new Feature();
						$this->parseFeature($gmlfeatureMember_dom, $feature, $wfsConf);
						if (isset($feature->geometry)) {
							$gml->featureCollection->addFeature($feature);
						}
					}
				}			
			}
			return $gml;
		}
		catch (Exception $e) {
			$e = new mb_exception($e);
			return null;
		}
	}
}
?>
