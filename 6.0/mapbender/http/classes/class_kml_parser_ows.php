<?php
# $Id: class_kml_parser_ows.php 5988 2010-04-20 14:51:43Z kmq $
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
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_point.php");
require_once(dirname(__FILE__)."/../classes/class_kml_polygon.php");
require_once(dirname(__FILE__)."/../classes/class_kml_linearring.php");
require_once(dirname(__FILE__)."/../classes/class_kml_line.php");
require_once(dirname(__FILE__)."/../classes/class_kml_point.php");
require_once(dirname(__FILE__)."/../classes/class_kml_multigeometry.php");
require_once(dirname(__FILE__)."/../classes/class_kml_placemark.php");

/**
 * @package KML
 */
 class KmlOwsParser {
	var $placemarkArray = array();
	
	public function __construct() {
	}
	
	public function parseGeoJSON ($geoJSON, $kmlId) {
		
//		$e = new mb_notice("GEOJSON: " . $geoJSON);
		$json = new Mapbender_JSON();
		$geometryFromGeoJSON = $json->decode($geoJSON);
		$id = 0;
		
		if (gettype($geometryFromGeoJSON) == "object" && $geometryFromGeoJSON->type == "FeatureCollection") {
			if ($geometryFromGeoJSON->crs->type == "EPSG" && $geometryFromGeoJSON->crs->properties->code) {
				$epsg = $geometryFromGeoJSON->crs->properties->code;	
			}
			// create Placemarks
			for ($i = 0; $i < count($geometryFromGeoJSON->features); $i++) {
				$e = new mb_notice("parsing plm #" . $i . "...length of placemarkArray: " . count($this->placemarkArray));
				$feature = $geometryFromGeoJSON->features[$i];
				if (gettype($feature) == "object" && $feature->type == "Feature") {
					if ($feature->geometry->crs->type == "EPSG") {
						$epsg = $feature->geometry->crs->properties->code;	
					}
					if (!$epsg) {
						$e = new mb_notice("EPSG is not set! Aborting...(" . $epsg . ")");
					}
					$geometry = $feature->geometry;
					
					$currentGeometry = false;
					//TODO: missing Polygon and MultiGeometry
					switch ($geometry->type) {
						case "LineString" :
							$coordinateList = "";
							for ($j = 0; $j < count($geometry->coordinates); $j++) {
								if ($j > 0) {
									$coordinateList .= " ";
								}
								$coordinateList .= implode(",", $geometry->coordinates[$j]);
							}
							$currentGeometry = new KMLLine($coordinateList, $epsg);
							break;
						case "Point" :
							$coordinateList = implode(",", $geometry->coordinates);
							$currentGeometry = new KMLPoint($coordinateList, $epsg);
							break;
					}
					
					if ($currentGeometry) {
						$currentPlacemark = new KMLPlacemark($currentGeometry);

						if (gettype($feature->properties) == "object") {
							
							foreach ($feature->properties as $key => $value) {
								$currentPlacemark->setProperty($key, $value);
							}
							$currentPlacemark->setProperty("Mapbender:kml", true);
							$currentPlacemark->setProperty("Mapbender:name", "unknown");
							$currentPlacemark->setProperty("Mapbender:id", $kmlId);
							$currentPlacemark->setProperty("Mapbender:placemarkId", $id);
							$e = new mb_notice("adding to placemarkArray (current length: " . count($this->placemarkArray) . ")");
							array_push($this->placemarkArray, $currentPlacemark);
							$e = new mb_notice("added...new length: " . count($this->placemarkArray));
							$id ++;
						}
					}
				}
			}
		}
		return true;
	}
		
	public function parseKML ($kml, $kmlId) {
		$doc = new DOMDocument("1.0");
		$doc->preserveWhiteSpace = false;
		$doc->loadXML($kml);

		/*
		 * Get geometry information only, store it in placemarkArray
		 */
		$placemarkTagArray = $doc->getElementsByTagName("Placemark");
		
		if (count($placemarkTagArray) > 0) {
			$id = 0;
			
			foreach ($placemarkTagArray as $node) {
	
				$geometryArray = $this->getGeometryArrayFromPlacemarkOrMultigeometryNode($node);
				$metadataArray = $this->getMetadataFromPlacemarkNode($node);
				
				/*
				 * For a placemark, the geometryArray should only contain 1 geometry!
				 */
				for ($i=0; $i < count($geometryArray); $i++) {
					$currentPlacemark = new KMLPlacemark($geometryArray[$i]);
					
					foreach ($metadataArray as $key => $value) {
						$currentPlacemark->setProperty($key, $value);
					}
					$currentPlacemark->setProperty("Mapbender:kml", true);
					$currentPlacemark->setProperty("Mapbender:name", "unknown");
					$currentPlacemark->setProperty("Mapbender:id", $kmlId);
					$currentPlacemark->setProperty("Mapbender:placemarkId", $id);

					// add description and name:
					$namesNode = $node->getElementsByTagName('name');
					if($namesNode->length > 0){
						$name = $namesNode->item(0)->nodeValue;
					}
					$descriptionsNode = $node->getElementsByTagName('description');
					if($descriptionsNode->length > 0){
						$description = $descriptionsNode->item(0)->nodeValue;
					}
					$currentPlacemark->setProperty("name", $name);
					$currentPlacemark->setProperty("description", $description);
					array_push($this->placemarkArray, $currentPlacemark);
				}
				$id ++;		    
			}
		}
		else {
			$e = new mb_exception("class_kml.php: KMLOWSParser: No placemarks found in KML.");
			return false;
		}
		return true;
	}

	/**
	 * Returns an associative array, containing metadata
	 */
	private function getMetadataFromPlacemarkNode ($node) {
	    $children = $node->childNodes;
	    
	    $metadataArray = array();
	    
		// search "ExtendedData" tag
		foreach ($children as $child) {
			if (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "EXTENDEDDATA") {  
				$extendedDataNode = $child;
				$extDataChildren = $extendedDataNode->childNodes; 
				
				// search "Data" or "SchemaData" tag
				foreach ($extDataChildren as $extDataChild) {
					if (mb_strtoupper($this->sepNameSpace($extDataChild->nodeName)) == "SCHEMADATA") {
						$simpleDataNode = $extDataChild->firstChild;
						while ($simpleDataNode !== NULL) {
							if (mb_strtoupper($this->sepNameSpace($simpleDataNode->nodeName)) == "SIMPLEDATA") {
								$name = $simpleDataNode->getAttribute("name");
								$value = $simpleDataNode->nodeValue;
								$metadataArray[$name] = $value;
							}
							$simpleDataNode = $simpleDataNode->nextSibling;
						}
					}
					if (mb_strtoupper($this->sepNameSpace($extDataChild->nodeName)) == "DATA") {
						$dataNode = $extDataChild;
						$name = $dataNode->getAttribute("name");
						$metadataArray[$name] = $dataNode->nodeValue;
					}
				}
			}
			if(mb_strtoupper($this->sepNameSpace($child->nodeName)) == "STYLE"){
			$hrefNodes = $child->getElementsByTagName("href");	
			if($hrefNodes->length > 0){
				$href = $hrefNodes->item(0)->nodeValue;
				$metadataArray["iconurl"] = $href;	
			}	
					
			}
		}
		return $metadataArray;		
	}
	
	/**
	 * Given a "Point" node, this function returns the geometry (KMLPoint)
	 * from within the node.
	 */
	private function getGeometryFromPointNode ($node) {
		$coordinatesNode = $this->getCoordinatesNode($node);
		$geomString = $coordinatesNode->nodeValue;
		return new KMLPoint($geomString, 4326);
	}
	
	/**
	 * Given a "LineString" node, this function returns the geometry (KMLLine)
	 * from within the node.
	 */
	private function getGeometryFromLinestringNode ($node) {
		$coordinatesNode = $this->getCoordinatesNode($node);
		$geomString = $coordinatesNode->nodeValue;
		return new KMLLine($geomString, 4326);
	}
	
	/**
	 * Given a "Polygon" node, this function returns the geometry (KMLPolygon)
	 * from within the node.
	 */
	private function getGeometryFromPolygonNode ($node) {
		$polygon = null;

	    $children = $node->childNodes;
	    
		// create new KMLPolygon
		foreach ($children as $child) {
			if (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "EXTERIOR" || 
				mb_strtoupper($this->sepNameSpace($child->nodeName)) == "OUTERBOUNDARYIS") {
				// create a new Linear Ring
				$outerBoundary = $this->getGeometryFromLinearRingNode($child);
				$polygon = new KMLPolygon($outerBoundary);
			}
		}
		
		if ($polygon !== null) {
			// append inner boundaries to KMLPolygon
			foreach ($children as $child) {
				if (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "INTERIOR" || 
					mb_strtoupper($this->sepNameSpace($child->nodeName)) == "INNERBOUNDARYIS") {
					// create a new Linear Ring
					$innerBoundary = $this->getGeometryFromLinearRingNode($child);
					$polygon->appendInnerBoundary($innerBoundary);
				}
			}
		}
		return $polygon;
	}
	
	/**
	 * Given a "OuterBoundaryIs" or "InnerBoundaryIs" node, this function 
	 * returns the geometry (KMLLinearRing) within the child node named "linearring"
	 */
	private function getGeometryFromLinearRingNode ($node) {
	    $children = $node->childNodes;
		foreach($children as $child) {
			if (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "LINEARRING") {
				$coordinatesNode = $this->getCoordinatesNode($child);
				$geomString = $coordinatesNode->nodeValue;
				return new KMLLinearRing($geomString);
			}
		}
		return null;
	}
	
	/**
	 * Checks if the child nodes of a given KML node contains any geometries and
	 * returns an array of geometries (KMLPoint, KMLPolygon, KMLLinestring and KMLMultigeometry)
	 */
	private function getGeometryArrayFromPlacemarkOrMultigeometryNode ($node) {
	    $geometryArray = array();
	    
	    $children = $node->childNodes;
		foreach($children as $child) {
			if (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "POINT") {
				array_push($geometryArray, $this->getGeometryFromPointNode($child));
			}
			elseif (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "POLYGON") {
				array_push($geometryArray, $this->getGeometryFromPolygonNode($child));
			}
			elseif (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "LINESTRING") {
				array_push($geometryArray, $this->getGeometryFromLinestringNode($child));
			}
			elseif (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "MULTIGEOMETRY") {
				$geometryArray = $this->getGeometryArrayFromPlacemarkOrMultigeometryNode($child);
				$multigeometry = new KMLMultiGeometry();
				
				for ($i=0; $i < count($geometryArray); $i++) {
					$multigeometry->append($geometryArray[$i]);	
				}
				array_push($geometryArray, $multigeometry);
			}
		}
		return $geometryArray;
	}

	/**
	 * Returns the child node with node name "coordinates" of a given KML node.
	 * If no node is found, null is returned.
	 */
	private function getCoordinatesNode ($node) {
	    $children = $node->childNodes;
		foreach($children as $child) {
			if (mb_strtoupper($this->sepNameSpace($child->nodeName)) == "POSLIST" || 
				mb_strtoupper($this->sepNameSpace($child->nodeName)) == "POS" || 
				mb_strtoupper($this->sepNameSpace($child->nodeName)) == "COORDINATES") {
				return $child;
			}
		}
		return null;
	}

	private function sepNameSpace($s){
		$c = mb_strpos($s,":"); 
		if($c>0){
			return mb_substr($s,$c+1);
		}
		else{
			return $s;
		}		
	}
}
?>
