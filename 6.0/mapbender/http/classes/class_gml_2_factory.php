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
require_once(dirname(__FILE__)."/../classes/class_gml_2.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

/**
 * Creates GML 2 objects from a GML documents.
 * 
 * @return Gml_2
 */
class Gml_2_Factory extends GmlFactory {

	/**
	 * Creates a GML object from a GeoJSON (http://www.geojson.org) String
	 * 
	 * @return Gml_3
	 * @param $geoJson String
	 */
	public function createFromGeoJson ($geoJson) {
		$gml2 = new Gml_2();
		
		return parent::createFromGeoJson($geoJson, $gml2);
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
			list($x, $y, $z) = explode(",", $currentSibling->nodeValue);
			$gmlPoint->setPoint($x, $y);
			$currentSibling = $currentSibling->nextSibling;
		}
		return $gmlPoint;
	}

	public static function parseLine ($domNode) {
		$gmlLine = new GmlLine();
		
		$currentSibling = $domNode->firstChild;
		while ($currentSibling) {
			
			foreach(explode(' ',trim($currentSibling->nodeValue)) as $cords){
				list($x,$y,$z) = explode(',',$cords);
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
		
		$allCoords = $simpleXMLNode->xpath("gml:outerBoundaryIs/gml:LinearRing/gml:coordinates");
			
		$cnt=0;
		foreach ($allCoords as $Coords) {
			$coordsDom = dom_import_simplexml($Coords);
				
			foreach(explode(' ',trim($coordsDom->nodeValue)) as $pointCoords){

				list($x,$y,$z) = explode(',',$pointCoords);
				$gmlPolygon->addPoint($x, $y);
			}
			
			$cnt++;
		}
		
		$innerRingNodeArray = $simpleXMLNode->xpath("gml:innerBoundaryIs/gml:LinearRing");
		if ($innerRingNodeArray) {
			$ringCount = 1;
			foreach ($innerRingNodeArray as $ringNode) {
				$coordinates = $ringNode->xpath("gml:coordinates");
				foreach ($coordinates as $coordinate) {
					$coordsDom = dom_import_simplexml($coordinate);
						
					foreach(explode(' ',trim($coordsDom->nodeValue)) as $pointCoords){
		
						list($x,$y,$z) = explode(',',$pointCoords);
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
		
		$allCoords = $simpleXMLNode->xpath("gml:lineStringMember/gml:LineString/gml:coordinates");
			
		$cnt=0;
		foreach ($allCoords as $Coords) {
			
			$gmlMultiLine->lineArray[$cnt] = array();
			
			$coordsDom = dom_import_simplexml($Coords);
				
//			$name = $coordsDom->nodeName;
//			$value = $coordsDom->nodeValue;				
//			echo "===> name: ".$name. ", Value: ".$value."<br>";
			
			foreach(explode(' ',trim($coordsDom->nodeValue)) as $pointCoords){
				list($x,$y,$z) = explode(',',$pointCoords);
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

		$allPolygons = $simpleXMLNode->xpath("gml:polygonMember/gml:Polygon");
		
		$cnt=0;
		foreach ($allPolygons as $polygon) {
			$allCoords = $polygon->xpath("gml:outerBoundaryIs/gml:LinearRing/gml:coordinates");
				
			$gmlMultiPolygon->polygonArray[$cnt] = array();
			foreach ($allCoords as $Coords) {
				
				$coordsDom = dom_import_simplexml($Coords);
					
				foreach (explode(' ',trim($coordsDom->nodeValue)) as $pointCoords) {
					list($x,$y,$z) = explode(',',$pointCoords);
					$gmlMultiPolygon->addPoint($x, $y, $cnt);
				}
			}
			
			$gmlMultiPolygon->innerRingArray[$cnt] = array();
			$innerRingNodeArray = $polygon->xpath("gml:innerBoundaryIs");
			if ($innerRingNodeArray) {
				$ringCount = 0;
				foreach ($innerRingNodeArray as $ringNode) {
					$currentRingNode = $ringNode->xpath("gml:LinearRing");
					foreach ($currentRingNode as $node) {
						$coordinates = $node->xpath("gml:coordinates");
						foreach ($coordinates as $coordinate) {
							$coordsDom = dom_import_simplexml($coordinate);
								
							foreach(explode(' ',trim($coordsDom->nodeValue)) as $pointCoords){
				
								list($x,$y,$z) = explode(',',$pointCoords);
								$gmlMultiPolygon->addPointToRing($cnt, $ringCount, $x, $y);
							}
						}
						$ringCount++;
						
					}
				}
			}
			$cnt++;
		}		
		return $gmlMultiPolygon;
	}
	
	/**
	 * Parses the feature segment of a GML and stores the geometry in the
	 * $geometry variable of the class.
	 * 	
	 * Example of a feature segment of a GML. 
	 * 	<gml:featureMember>
	 * 		<ms:ROUTE fid="ROUTE.228168">
	 * 			<gml:boundedBy>
	 * 				<gml:Box srsName="EPSG:31466">
	 * 					<gml:coordinates>2557381.0,5562371.1 2557653.7,5562526.0</gml:coordinates>
	 * 				</gml:Box>
	 * 			</gml:boundedBy>
	 * 			<ms:geometry>
	 * 				<gml:LineString>
	 * 					<gml:coordinates>
	 * 						2557380.97,5562526 2557390.96,
	 * 						5562523.22 2557404.03,5562518.2 2557422.31,
	 * 						5562512 2557437.16,5562508.37 2557441.79,
	 * 						5562507.49 2557454.31,5562505.1 2557464.27,
	 * 						5562503.97 2557473.24,5562502.97 2557491.67,
	 * 						5562502.12 2557505.65,5562502.43 2557513.78,
	 * 						5562501.12 2557520.89,5562498.79 2557528.5,
	 * 						5562495.07 2557538.9,5562488.91 2557549.5,
	 * 						5562483.83 2557558.55,5562476.61 2557569.07,
	 * 						5562469.82 2557576.61,5562462.72 2557582.75,
	 * 						5562457.92 2557588.57,5562452.56 2557590.38,
	 * 						5562449.69 2557593.57,5562445.07 2557596.17,
	 * 						5562441.31 2557601.71,5562433.93 2557612.97,
	 * 						5562421.03 2557626,5562405.33 2557639.66,
	 * 						5562389.75 2557653.69,5562371.12 
	 * 					</gml:coordinates>
	 * 				</gml:LineString>
	 * 			</ms:geometry>
	 * 			<code>354</code>
	 * 			<Verkehr>0</Verkehr>
	 * 			<rlp>t</rlp>
	 * 		</ms:ROUTE>
	 * 	</gml:featureMember>
	 * 
	 * @return void
	 * @param $domNode DOMNodeObject the feature tag of the GML 
	 * 								(<gml:featureMember> in the above example)
	 */
	protected function parseFeature($domNode, $feature, $wfsConf) {
		$geomFeaturetypeElement = $wfsConf->getGeometryColumnName();

		$currentSibling = $domNode;
		
		$feature->fid = $currentSibling->getAttribute("fid");
		
		$currentSibling = $currentSibling->firstChild;
		
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
				if ($geomNode->hasAttribute("srsName")) {
					$srs = $geomNode->getAttribute("srsName");
				}
				$geomType = $geomNode->nodeName;
				switch ($geomType) {
					case "gml:Polygon" :
						$feature->geometry = self::parsePolygon($geomNode);
						$feature->geometry->srs = $srs;
						break;
					case "gml:LineString" :
						$feature->geometry = self::parseLine($geomNode);
						$feature->geometry->srs = $srs;
						break;
					case "gml:Point" :
						$feature->geometry = self::parsePoint($geomNode);
						$feature->geometry->srs = $srs;
						break;
					case "gml:MultiLineString" :
						$feature->geometry = self::parseMultiLine($geomNode);
						$feature->geometry->srs = $srs;
						break;
					case "gml:MultiPolygon" :
						$feature->geometry = self::parseMultiPolygon($geomNode);
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
	
	
	/**
	 * Creates GML 2 objects from GML documents.
	 * 
	 * @return Gml_2
	 * @param $xml String
	 */
	public function createFromXml ($xml, $wfsConf) {
		$gml2 = new Gml_2();
		return parent::createFromXml($xml, $wfsConf, $gml2);
	}	
}
?>
