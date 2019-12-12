<?php
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

#TODO:Check if the following line is enough:
#require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_gml2.php");

class geoRSS {
	var $doc;
	var $importItems = array("title","link","description");
	var $targetEPSG;
	function parseFile($req){
		#$data = implode("",file($req));
		$x = new connector($req);
		$data = $x->file;
		$data = $this->removeWhiteSpace($data);
		if($data=="")
			return false;
		return $this->parseXML($data);		
		#$e = new mb_exception("data = ".$data); 		
	}
	
	/**
	 * Set the Rss Elements that should go to the property tags of geoJSON
	 * default is "title","link" and "description"
	 */
	public function setImportTags($tags){
		$this->importItems = $tags;
	}
	
	function parseXML($data) {
		$this->doc = $data;
		return $this->toGeoJSON();
	}

	function removeWhiteSpace ($string) {
		return preg_replace("/\>(\s)+\</", "><", trim($string));
	}
	
	function toGeoJSON () {
		$rssDoc = new SimpleXMLElement($this->doc);
		
		$rssDoc->registerXPathNamespace('xls', 'http://www.opengis.net/xls');
		$rssDoc->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
		$rssDoc->registerXPathNamespace('georss', 'http://www.georss.org/georss');
		#for ingrid - portalu georss
		$rssDoc->registerXPathNamespace('ingrid', 'http://www.portalu.de/opensearch/extension/1.0');

		// build feature collection
		$featureCollection = new FeatureCollection();
		
		// elements of rss document
		$rssItems = $rssDoc->xpath("//item");
		
		if(count($rssItems)>0){
			foreach($rssItems as $item){
				$rssItem_dom = dom_import_simplexml($item);
				
				$feature = new geoRSSItem();
				$feature->targetEPSG = $this->targetEPSG;
				$feature->parse($rssItem_dom, $this->importItems);
				if (isset($feature->geometry) && $feature->geometry!==false) {
					$featureCollection->addFeature($feature);
				}
			}

			return $featureCollection->toGeoJSON();
		}
		else{
			return "{'errorMessage':'Kein Ergebnis'}";
		}
	}
}

class geoRSSItem extends Feature{
	var $targetEPSG;
	#$this->targetEPSG='4326';
	public function parse($domNode, $importItems) {
		$currentSibling = $domNode->firstChild;
		
		while ($currentSibling) {
			$tag = $currentSibling->nodeName;
			if(in_array($tag, $importItems)){
				$this->properties[$tag] = $currentSibling->nodeValue;
			}
			else{
				switch ($tag) {
				case "georss:where":
					$this->parseGML($currentSibling);
					break;
				case "georss:point":
					$this->geometry = new geoRSSPoint();
					$this->geometry->targetEPSG = $this->targetEPSG;
					$this->geometry->parsePoint($currentSibling);
					break;
				case "georss:line":
					$this->geometry = new geoRSSLine();
					$this->geometry->targetEPSG = $this->targetEPSG;
					$this->geometry->parseLine($currentSibling);
					break;
				case "georss:box":
					$this->geometry = new geoRSSBox();
					$this->geometry->targetEPSG = $this->targetEPSG;
					$this->geometry->parseBox($currentSibling);
					break;
				case "georss:polygon":
					$this->geometry = new geoRSSPolygon();
					$this->geometry->targetEPSG = $this->targetEPSG;
					$this->geometry->parsePolygon($currentSibling);
					break;
				case "geo:point":
					$this->geometry = new geoPoint();
					$this->geometry->parsePoint($currentSibling);
					break;
				case "geo:lat":
					if(!$this->geometry)
						$this->geometry = new geoPoint();
						$this->geometry->targetEPSG = $this->targetEPSG;
					$this->geometry->parseLat($currentSibling);
					break;
				case "geo:long":
					if(!$this->geometry)
						$this->geometry = new geoPoint();
						$this->geometry->targetEPSG = $this->targetEPSG;
					$this->geometry->parseLong($currentSibling);
					break;
				default:
					break;
				}
			}			
			$currentSibling = $currentSibling->nextSibling;
		}		
	}
	
	function parseGML($domNode){
		$currentSibling = $domNode->firstChild;
	
		while ($currentSibling) {
			$geomType = $currentSibling->nodeName;
			switch ($geomType) {
				case "gml:Polygon" :
					$this->geometry = new GMLPolygon();
					$this->geometry->parsePolygon($currentSibling);
					break;
				case "gml:LineString" :
					$this->geometry = new GMLLine();
					$this->geometry->parseLine($currentSibling);
					break;
				case "gml:Point" :
					$this->geometry = new GMLPoint();
					$this->geometry->parsePoint($currentSibling);
					break;
				case "gml:MultiLineString" :
					$this->geometry = new GMLMultiLine();
					$this->geometry->parseMultiLine($currentSibling);
					break;
				case "gml:MultiPolygon" :
					$this->geometry = new GMLMultiPolygon();
					$this->geometry->parseMultiPolygon($currentSibling);
					break;
				case "gml:Envelope" :
					$this->geometry = new GMLEnvelope();
					$this->geometry->parseEnvelope($currentSibling);					
				default:
					break;
			}
			$currentSibling = $currentSibling->nextSibling;
		}
	}
}

class geoRSSPoint extends GMLPoint{
	var $targetEPSG;
	public function parsePoint($domNode){
		list($y, $x) = explode(" ", $domNode->nodeValue);
		if ($this->targetEPSG != '4326') {
			$tCoords = transform($x, $y,'4326', $this->targetEPSG);
			$x = $tCoords["x"];
			$y = $tCoords["y"];
			$this->setPoint($x, $y);
		}
		else {
			$this->setPoint($x, $y);
		}
	}
}

class geoPoint extends GMLPoint{
	var $targetEPSG;
	public function parsePoint($domNode){
		$currentSibling = $domNode->firstChild;
		while ($currentSibling) {			
			switch ($currentSibling->nodeName){
			case "geo:lat":
				$this->parseLat($currentSibling);
				break;
			case "geo:lon":
				$this->parseLon($currentSibling);
				break;
			}
			$currentSibling = $currentSibling->nextSibling;
		}
	}
	public function parseLat($node){
		if(!$this->point)
			$this->point=array();
		if ($this->targetEPSG != '4326') {
			$tCoords = transform(0, $node->nodeValue,'4326', $this->targetEPSG);
			
			$this->point["y"] = $tCoords["y"];
				
		}
		else {
			$this->point["y"] = $node->nodeValue;
		}
	}
	public function parseLong($node){
		if(!$this->point)
			$this->point=array();
		if ($this->targetEPSG != '4326') {
			$tCoords = transform($node->nodeValue, 0, '4326', $this->targetEPSG);
			
			$this->point["x"] = $tCoords["x"];
				
		}
		else {
			$this->point["x"] = $node->nodeValue;
		}
	}
}



class geoRSSLine extends GMLLine{
	var $targetEPSG;
	public function parseLine ($domNode) {
		$cnt = 0;
		$y = 0;
		foreach(explode(' ',$domNode->nodeValue) as $cord){
			if($cnt % 2)
				$this->addPoint($cord, $y);
			$y = $cord;
			$cnt++;
		}
	}
}

class geoRSSPolygon extends GMLPolygon{
	var $targetEPSG;
	public function parsePolygon ($domNode) {
		$cnt = 0;
		$y = 0;
		foreach(explode(' ',$domNode->nodeValue) as $cord){
			if($cnt % 2)
				$this->addPoint($cord, $y);
			$y = $cord;
			$cnt++;
		}
	}
}

class geoRSSBox extends GMLPolygon{
	var $targetEPSG;
	public function parseBox ($domNode) {
		list($x1,$y1,$x2,$y2) = explode(' ',$domNode->nodeValue);
		if ($this->targetEPSG != '4326') {
			$tCoords = transform($x1, $y1,'4326', $this->targetEPSG);
			$x1 = $tCoords["x"];
			$y1 = $tCoords["y"];	
			$tCoords = transform($x2, $y2,'4326', $this->targetEPSG);
			$x2 = $tCoords["x"];
			$y2 = $tCoords["y"];
		}
		$this->addPoint($x1, $y1);
		$this->addPoint($x1, $y2);
		$this->addPoint($x2, $y2);
		$this->addPoint($x2, $y1);
		$this->addPoint($x1, $y1);
	}
}
function transform ($x, $y, $oldEPSG, $newEPSG) {
    if (is_null($x) || !is_numeric($x) ||
        is_null($y) || !is_numeric($y) ||
        is_null($oldEPSG) || !is_numeric($oldEPSG) ||
        is_null($newEPSG) || !is_numeric($newEPSG)) {
        return null;
    }

    /*
     * @security_patch sqli done
     */

    if(SYS_DBTYPE=='pgsql'){
        $con = db_connect(DBSERVER, OWNER, PW);
        $sqlMinx = "SELECT X(transform(GeometryFromText('POINT(".pg_escape_string($x)." ".pg_escape_string($y).")',".pg_escape_string($oldEPSG)."),".pg_escape_string($newEPSG).")) as minx";
        $resMinx = db_query($sqlMinx);
        $minx = floatval(db_result($resMinx,0,"minx"));
       
        $sqlMiny = "SELECT Y(transform(GeometryFromText('POINT(".pg_escape_string($x)." ".pg_escape_string($y).")',".pg_escape_string($oldEPSG)."),".pg_escape_string($newEPSG).")) as miny";
        $resMiny = db_query($sqlMiny);
        $miny = floatval(db_result($resMiny,0,"miny"));
       
    }else{
        $con_string = "host=".GEOS_DBSERVER." port=".GEOS_PORT." dbname=".GEOS_DB."user=".GEOS_OWNER ."password=".GEOS_PW;
        $con = pg_connect($con_string) or die ("Error while connecting database");

	/*
	 * @security_patch sqli done
	 */

        $sqlMinx = "SELECT X(transform(GeometryFromText('POINT(".pg_escape_string($x)." ".pg_escape_string($y).")',".pg_escape_string($oldEPSG)."),".pg_escape_string($newEPSG).")) as minx";
        $resMinx = pg_query($con,$sqlMinx);
        $minx = floatval(pg_fetch_result($resMinx,0,"minx"));
       
        $sqlMiny = "SELECT Y(transform(GeometryFromText('POINT(".pg_escape_string($x)." ".pg_escape_string($y).")',".pg_escape_string($oldEPSG)."),".pg_escape_string($newEPSG).")) as miny";
        $resMiny = pg_query($con,$sqlMiny);
        $miny = floatval(pg_fetch_result($resMiny,0,"miny"));
    }
    return array("x" => $minx, "y" => $miny);
}
?>
