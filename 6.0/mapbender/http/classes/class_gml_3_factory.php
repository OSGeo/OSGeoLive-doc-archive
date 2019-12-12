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
require_once(dirname(__FILE__)."/../classes/class_gml_3.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

/**
 * Creates GML 3 objects from a GML documents.
 * 
 * @return Gml_3
 */
class Gml_3_Factory extends GmlFactory {

	/**
	 * Creates a GML object from a GeoJSON (http://www.geojson.org) String
	 * 
	 * @return Gml_3
	 * @param $geoJson String
	 */
	public function createFromGeoJson ($geoJson) {
		$gml3 = new Gml_3();
		
		return parent::createFromGeoJson($geoJson, $gml3);
	}

	/**
	 * Creates GML 3 objects from GML documents.
	 * 
	 * @return Gml_3
	 * @param $xml String
	 */
	public function createFromXml ($xml, $wfsConf) {
		$gml3 = new Gml_3();
		return parent::createFromXml($xml, $wfsConf, $gml3);
	}	

	public static function getDimensionFromNode ($domNode) {
		if (!$domNode->hasAttribute("srsDimension")) {
			return 2;
		}
		$dim = intval($domNode->getAttribute("srsDimension"), 10);
		if (2 == $dim || 3 == $dim) {
			return $dim;
		}
		return 2;
	}
	
	function findNameSpace($s){
		list($ns,$FeaturePropertyName) = explode(":",$s);
		$nodeName = array('ns' => $ns, 'value' => $FeaturePropertyName);
		return $nodeName;
	}

	public static function parsePoint ($domNode) {
		$gmlPoint = new GmlPoint();
		$currentSibling = $domNode->firstChild;
		while ($currentSibling) {
			$coordArray = explode(" ", $currentSibling->nodeValue);
			$gmlPoint->setPoint($coordArray[0], $coordArray[1]);
			$currentSibling = $currentSibling->nextSibling;
		}
		return $gmlPoint;
	}

	public static function parseLine ($domNode) {
		$gmlLine = new GmlLine();
		
		$currentSibling = $domNode->firstChild;
		while ($currentSibling) {

			$dim = self::getDimensionFromNode($currentSibling);
			$coordArray = explode(' ', trim($currentSibling->nodeValue));
			for ($i = 0; $i < count($coordArray); $i += $dim) {
				$x = $coordArray[$i];
				$y = $coordArray[$i+1];
				$gmlLine->addPoint($x, $y);
			}
			$currentSibling = $currentSibling->nextSibling;
		}
		return $gmlLine;
	}

	public static function parsePolygon ($domNode) {
		$gmlPolygon = new GmlPolygon();
		
		$simpleXMLNode = simplexml_import_dom($domNode);

		$simpleXMLNode->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
		
		$allCoords = $simpleXMLNode->xpath("gml:exterior/gml:LinearRing/gml:posList");
			
		$cnt=0;
		foreach ($allCoords as $Coords) {
			$coordsDom = dom_import_simplexml($Coords);
			
			$dim = self::getDimensionFromNode($coordsDom);
			$coordArray = explode(' ', trim($coordsDom->nodeValue));
			for ($i = 0; $i < count($coordArray); $i += $dim) {
				$x = $coordArray[$i];
				$y = $coordArray[$i+1];
				$gmlPolygon->addPoint($x, $y);
			}
			$cnt++;
		}
		
		$innerRingNodeArray = $simpleXMLNode->xpath("gml:innerBoundaryIs/gml:LinearRing");
		if ($innerRingNodeArray) {
			$ringCount = 0;
			foreach ($innerRingNodeArray as $ringNode) {
				$coordinates = $ringNode->xpath("gml:coordinates");
				foreach ($coordinates as $coordinate) {
					$coordsDom = dom_import_simplexml($coordinate);
						
					$dim = self::getDimensionFromNode($coordsDom);
					$coordArray = explode(' ', trim($coordsDom->nodeValue));
					for ($i = 0; $i < count($coordArray); $i += $dim) {
						$x = $coordArray[$i];
						$y = $coordArray[$i+1];
						$gmlPolygon->addPointToRing($ringCount, $x, $y);
					}
				}
				$ringCount++;
			}
		}
		return $gmlPolygon;
	}

	public static function parseMultiLine ($domNode) {
		$gmlMultiLine = new GmlMultiLine();
		
		$simpleXMLNode = simplexml_import_dom($domNode);

		$simpleXMLNode->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
		
		$allCoords = $simpleXMLNode->xpath("gml:lineStringMember/gml:LineString/gml:posList");
		if (count($allCoords) === 0) {
			$allCoords = $simpleXMLNode->xpath("gml:lineStringMembers/gml:LineString/gml:posList");
		}
			
		$cnt=0;
		foreach ($allCoords as $Coords) {
			
			$gmlMultiLine->lineArray[$cnt] = array();
			
			$coordsDom = dom_import_simplexml($Coords);
				
			$dim = self::getDimensionFromNode($coordsDom);
			$coordArray = explode(' ', trim($coordsDom->nodeValue));
			for ($i = 0; $i < count($coordArray); $i += $dim) {
				$x = $coordArray[$i];
				$y = $coordArray[$i+1];
				$gmlMultiLine->addPoint($x, $y, $cnt);
			}			
			$cnt++;
		}
		return $gmlMultiLine;
	}	
	
	public static function parseMultiPoint ($domNode) {
		$gmlMultiPoint = new GmlMultiPoint();
		
		$simpleXMLNode = simplexml_import_dom($domNode);

		$simpleXMLNode->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
		
		$allCoords = $simpleXMLNode->xpath("gml:pointMember/gml:Point/gml:pos");
		if (count($allCoords) === 0) {
			$allCoords = $simpleXMLNode->xpath("gml:pointMembers/gml:Point/gml:pos");
		}
			
		foreach ($allCoords as $Coords) {
			
			$coordsDom = dom_import_simplexml($Coords);
				
			$dim = self::getDimensionFromNode($coordsDom);
			$coordArray = explode(' ', trim($coordsDom->nodeValue));
			for ($i = 0; $i < count($coordArray); $i += $dim) {
				$x = $coordArray[$i];
				$y = $coordArray[$i+1];
				$gmlMultiPoint->addPoint($x, $y);
				break;
			}			
		}
		return $gmlMultiPoint;
	}		

	public static function parseMultiCurve ($domNode) {
		$gmlMultiLine = new GmlMultiLine();
		
		$simpleXMLNode = simplexml_import_dom($domNode);

		$simpleXMLNode->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
		
		$allCoords = $simpleXMLNode->xpath("gml:curveMembers/gml:LineString/gml:posList");
		if (count($allCoords) === 0) {
			$allCoords = $simpleXMLNode->xpath("gml:curveMember/gml:LineString/gml:posList");
		}
			
		$cnt=0;
		foreach ($allCoords as $Coords) {
			
			$gmlMultiLine->lineArray[$cnt] = array();
			
			$coordsDom = dom_import_simplexml($Coords);

			$dim = self::getDimensionFromNode($coordsDom);
			$coordArray = explode(' ', trim($coordsDom->nodeValue));
			for ($i = 0; $i < count($coordArray); $i += $dim) {
				$x = $coordArray[$i];
				$y = $coordArray[$i+1];
				$gmlMultiLine->addPoint($x, $y, $cnt);
			}
			$cnt++;
		}
		return $gmlMultiLine;
	}		
	
	public static function parseMultiPolygon ($domNode) {
		$gmlMultiPolygon = new GmlMultiPolygon();
		
		$simpleXMLNode = simplexml_import_dom($domNode);

		$simpleXMLNode->registerXPathNamespace('gml', 'http://www.opengis.net/gml');

		$allPolygons = $simpleXMLNode->xpath("gml:surfaceMember/gml:Polygon");
		if (count($allPolygons) === 0) {
			$allPolygons = $simpleXMLNode->xpath("gml:surfaceMembers/gml:Polygon");
		}
		
		$cnt=0;
		foreach ($allPolygons as $polygon) {
			$allCoords = $polygon->xpath("gml:exterior/gml:LinearRing/gml:posList");
				
			$gmlMultiPolygon->polygonArray[$cnt] = array();
			foreach ($allCoords as $Coords) {
				
				$coordsDom = dom_import_simplexml($Coords);

				$dim = self::getDimensionFromNode($coordsDom);
				$coordArray = explode(' ', trim($coordsDom->nodeValue));
				for ($i = 0; $i < count($coordArray); $i += $dim) {
					$x = $coordArray[$i];
					$y = $coordArray[$i+1];
					$gmlMultiPolygon->addPoint($x, $y, $cnt);
				}
			}
			
			$gmlMultiPolygon->innerRingArray[$cnt] = array();
			$innerRingNodeArray = $polygon->xpath("gml:interior");
			if ($innerRingNodeArray) {
				$ringCount = 0;
				foreach ($innerRingNodeArray as $ringNode) {
					$currentRingNode = $ringNode->xpath("gml:LinearRing");
					foreach ($currentRingNode as $node) {
						$coordinates = $node->xpath("gml:posList");
						foreach ($coordinates as $coordinate) {
							$coordsDom = dom_import_simplexml($coordinate);
								
							$dim = self::getDimensionFromNode($coordsDom);
							$coordArray = explode(' ', trim($coordsDom->nodeValue));
							for ($i = 0; $i < count($coordArray); $i += $dim) {
								$x = $coordArray[$i];
								$y = $coordArray[$i+1];
								$gmlMultiPolygon->addPointToRing($cnt, $ringCount, $x, $y);
							}
						}
						$ringCount++;
						
					}
				}
			}
			$cnt++;
//			new mb_exception("create multipolygon " . serialize($gmlMultiPolygon->innerRingArray));
		}
		return $gmlMultiPolygon;		
	}
	
	/**
	 * Parses the feature segment of a GML and stores the geometry in the
	 * $geometry variable of the class.
	 * 	
	 * Example of a feature segment of a GML. 
	 * 
	 * <gml:featureMember>
	 * 	<ms:my_polygons gml:id="my_polygons.624">
	 * 		<gml:boundedBy>
	 * 			<gml:Envelope srsName="epsg:4326">
	 * 				<gml:lowerCorner>39.700000 29.400000</gml:lowerCorner>
	 * 				<gml:upperCorner>46.400000 35.400000</gml:upperCorner>
	 * 			</gml:Envelope>
	 * 		</gml:boundedBy>
	 * 		<ms:the_geom>
	 * 			<gml:MultiSurface srsName="epsg:4326">
	 * 				<gml:surfaceMembers>
	 * 					<gml:Polygon>
	 * 						<gml:exterior>
	 * 							<gml:LinearRing>
	 * 								<gml:posList srsDimension="2">
	 * 									43.200000 35.400000 
	 * 									46.400000 31.700000 
	 * 									44.100000 31.000000 
	 * 									41.700000 29.400000 
	 * 									39.700000 31.400000 
	 * 									43.300000 32.300000 
	 * 									43.200000 35.400000 
	 * 								</gml:posList>
	 * 							</gml:LinearRing>
	 * 						</gml:exterior>
	 * 					</gml:Polygon>
	 * 				</gml:surfaceMembers>
	 * 			</gml:MultiSurface>
	 * 		</ms:the_geom>
	 * 		<ms:oid>16752039</ms:oid>
	 * 		<ms:gid>624</ms:gid>
	 * 		<ms:name>inter_08</ms:name>
	 * 		<ms:angle/>
	 * 		<ms:annotation/>
	 * 		<ms:style>3</ms:style>
	 * 		</ms:my_polygons>
	 * 	</gml:featureMember>
	 * 
	 * @return void
	 * @param $domNode DOMNodeObject the feature tag of the GML 
	 * 								(<gml:featureMember> in the above example)
	 */
	protected function parseFeature($domNode, $feature, $wfsConf) {
		$geomFeaturetypeElement = $wfsConf->getGeometryColumnName();

		$feature->fid = $domNode->getAttribute("gml:id");
		
		$currentSibling = $domNode->firstChild;
		
		while ($currentSibling) {
		
			$name = $currentSibling->nodeName;
			$value = $currentSibling->nodeValue;
			
			$namespace = $this->findNameSpace($name);
			$ns = $namespace['ns'];
			$columnName = $namespace['value'];
			$isGeomColumn = ($geomFeaturetypeElement == null || $columnName == $geomFeaturetypeElement);
			
			// check if this node is a geometry node.
			// however, even if it is a property node, 
			// it has a child node, the text node!
			// So we might need to do something more 
			// sophisticated here...
			if ($currentSibling->hasChildNodes() && $isGeomColumn){
				$geomNode = $currentSibling->firstChild;
                                if($geomNode->nodeType != XML_ELEMENT_NODE){
                                    while($geomNode = $geomNode->nextSibling){
                                        if($geomNode->nodeType == XML_ELEMENT_NODE){
                                            break;
                                        }
                                    }
                                }
				$geomType = $geomNode->nodeName;
				if ($geomNode->hasAttribute("srsName")) {
					$srs = $geomNode->getAttribute("srsName");
				}
				switch ($geomType) {
					case "gml:Polygon" :// untested!
						$feature->geometry = self::parsePolygon($geomNode);
						if ($feature->geometry->isEmpty()) {
							$feature->geometry = Gml_2_Factory::parsePolygon($geomNode);
						}
						$feature->geometry->srs = $srs;
						break;
					case "gml:LineString" :// untested!
						$feature->geometry = self::parseLine($geomNode);
						if ($feature->geometry->isEmpty()) {
							$feature->geometry = Gml_2_Factory::parseLine($geomNode);
						}
						$feature->geometry->srs = $srs;
						break;
					case "gml:Point" :
						$feature->geometry = self::parsePoint($geomNode);
						if ($feature->geometry->isEmpty()) {
							$feature->geometry = Gml_2_Factory::parsePoint($geomNode);
						}
						$feature->geometry->srs = $srs;
						break;
					case "gml:MultiPoint" :
						$feature->geometry = self::parseMultiPoint($geomNode);
						if ($feature->geometry->isEmpty()) {
							$feature->geometry = Gml_2_Factory::parseMultiPoint($geomNode);
						}
						$feature->geometry->srs = $srs;
						break;
					case "gml:MultiLineString" :
						new mb_exception("found multilinestring");
						$feature->geometry = self::parseMultiLine($geomNode);
						if ($feature->geometry->isEmpty()) {
							$feature->geometry = Gml_2_Factory::parseMultiLine($geomNode);
						}
						$feature->geometry->srs = $srs;
						break;
					case "gml:MultiCurve" :
						$feature->geometry = self::parseMultiCurve($geomNode);
						if ($feature->geometry->isEmpty()) {
							$feature->geometry = Gml_2_Factory::parseMultiLine($geomNode);
						}
						$feature->geometry->srs = $srs;
						break;
					case "gml:MultiSurface" : 
						$feature->geometry = self::parseMultiPolygon($geomNode);
						if ($feature->geometry->isEmpty()) {
							$feature->geometry = Gml_2_Factory::parseMultiPolygon($geomNode);
						}
						$feature->geometry->srs = $srs;
						break;
					default:
						$feature->properties[$columnName] = $value;
						break;
				}
			} 
			else {
				if ($currentSibling->hasChildNodes() && $currentSibling->firstChild instanceof DOMText) {
					$feature->properties[$columnName] = $value;
				}
			}
			
			$currentSibling = $currentSibling->nextSibling;
		}
	}
	
	
}
?>
