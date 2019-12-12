<?php
# $Id: class_wfs.php 8431 2012-07-06 10:06:48Z astrid_emde $
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
require_once(dirname(__FILE__)."/class_connector.php");
require_once(dirname(__FILE__)."/class_administration.php");
require_once(dirname(__FILE__)."/class_gml.php");
require_once(dirname(__FILE__)."/class_ows.php");
require_once(dirname(__FILE__)."/class_wfsToDb.php");
require_once(dirname(__FILE__)."/class_wfs_configuration.php");

/**
 * An abstract Web Feature Service (WFS) class, modelling for example
 * WFS 1.0.0 or WFS 1.1.0
 */
abstract class Wfs extends Ows {
	var $describeFeatureType;
	var $describeFeatureTypeNamespace;
	var $getFeature;
	var $transaction;
	var $featureTypeArray = array();
	
	/**
	 * Returns the version of this WFS. Has to be implemented by the subclass.
	 * @return String the version, for example "1.0.0"
	 */
	public function getVersion () {
		return "";
	}
	
	public function addFeatureType ($aFeatureType) {
		array_push($this->featureTypeArray, $aFeatureType);
	}
	
	protected function findFeatureTypeByName ($name) {
		foreach ($this->featureTypeArray as $ft) {
			if ($ft->name == $name) {
				return $ft;
			}
		}
		new mb_exception("This WFS doesn't have a featuretype with name " . $name);
		return null;
	}
	
	public function findFeatureTypeById ($id) {
		foreach ($this->featureTypeArray as $ft) {
			if ($ft->id == $id) {
				return $ft;
			}
		}
		new mb_exception("This WFS doesn't have a featuretype with ID " . $id);
		return null;
	}
	
	public static function getWfsIdByFeaturetypeId ($id) {
		$sql = "SELECT DISTINCT fkey_wfs_id FROM wfs_featuretype WHERE featuretype_id = $1";
		$res = db_prep_query($sql, array($id), array("i"));
		$row = db_fetch_assoc($res);
		if ($row) {
			return $row["fkey_wfs_id"];
		}
		return null;
	}
	
	protected function getFeatureGet ($featureTypeName, $filter) {
		$url = $this->getFeature .
				$this->getConjunctionCharacter($this->getFeature) . 
				"service=WFS&request=getFeature&version=" . 
				$this->getVersion() . "&typename=" . $featureTypeName . 
				"&filter=" . urlencode($filter);

		return $this->get($url);
	}
	
	protected function getFeaturePost ($featureTypeName, $filter, $destSrs) {
		$postData = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . 
			"<wfs:GetFeature service=\"WFS\" version=\"" . $this->getVersion() . "\" " . 
			"xmlns:wfs=\"http://www.opengis.net/wfs\" " . 
			"xmlns:gml=\"http://www.opengis.net/gml\" " . 
			"xmlns:ogc=\"http://www.opengis.net/ogc\">" . 
			"<wfs:Query ";
		
		if($destSrs) {
			$postData .= "srsName=\"" . $destSrs . "\" ";
		}
		
		// add namespace
		if (strpos($featureTypeName, ":") !== false) {
			$ft = $this->findFeatureTypeByName($featureTypeName);
			$ns = $this->getNamespace($featureTypeName);
			$url = $ft->getNamespace($ns);
			$postData .= "xmlns:" . $ns . "=\"" . $url . "\" ";
			
		}
		$postData .= "typeName=\"" . $featureTypeName . "\">" . 
			$filter . 
			"</wfs:Query></wfs:GetFeature>";		

		return $this->post($this->getFeature, $postData);
	}
	
	public function getFeature ($featureTypeName, $filter, $destSrs=null) {

// FOR NOW, WE ONLY DO POST REQUESTS!
// THE FILTERS ARE CONSTRUCTED ON THE CLIENT SIDE,
// SO CHANGING THEM HERE ACCORDING TO GET/POST
// WOULD BE TOO MUCH FOR NOW
//
//		if (strpos($this->getFeature, "?") === false) {
			return $this->getFeaturePost($featureTypeName, $filter, $destSrs);
//		}
//		return $this->getFeatureGet($featureTypeName, $filter);
	}
	
	/**
	 * Performs a WFS transaction (delete, update or insert).
	 * 
	 * @return String the WFS reply
	 * @param $method String "delete", "update" or "insert"
	 * @param $wfsConf WfsConfiguration
	 * @param $gmlObj Gml
	 */
	public function transaction ($method, $wfsConf, $gmlObj) {
		
		//
		// get feature type and geometry column from WFS configuration
		//
		if (!($wfsConf instanceof WfsConfiguration)) {
			$e = new mb_exception("Invalid WFS configuration.");
			return null;
		}
		$featureType = $this->findFeatureTypeById($wfsConf->featureTypeId);
		$featureTypeName = $featureType->name;
		$geomColumnName = $wfsConf->getGeometryColumnName();
		$authWfsConfElement = $wfsConf->getAuthElement();

		//
		// GML string
		//
		if (!($gmlObj instanceof Gml)) {
			$e = new mb_exception("Not a GML object.");
			return null;
		}
		$gml = $gmlObj->toGml();
		if (is_null($gml)) {
			$e = new mb_exception("GML is not set.");
			return null;
		}
		
		// I assume that only one feature is contained in the GeoJSON,
		// so I just take the first from the collection.
		$feature = $gmlObj->featureCollection->featureArray[0];

		switch (strtolower($method)) {
			case "delete":
				$requestData = $this->transactionDelete($feature, $featureType, $authWfsConfElement);
				break;
			case "update":
				$requestData = $this->transactionUpdate($feature, $featureType, $authWfsConfElement, $gml, $geomColumnName);
				break;
			case "insert":
				$requestData = $this->transactionInsert($feature, $featureType, $authWfsConfElement, $gml, $geomColumnName);
				break;
			default:
				$e = new mb_exception("Invalid transaction method: " . $method);
				return null;
		}		

		$postData = $this->wrapTransaction($featureType, $requestData);
		return $this->post($this->transaction, $postData);
	}
	
	protected function transactionInsert ($feature, $featureType, $authWfsConfElement, $gml, $geomColumnName) {
		$featureTypeName = $featureType->name;
		$ns = $this->getNamespace($featureTypeName);
		
		// authentication
		$authSegment = "";
		if (!is_null($authWfsConfElement)) {
			$user = eval("return " . $authWfsConfElement->authVarname . ";");
			$authSegment = "<$ns:{$authWfsConfElement->name}>$user</$ns:{$authWfsConfElement->name}>";
		}

		// add properties
		$propertiesSegment = "";
		foreach ($feature->properties as $key => $value) {
			if (isset($value)) {
				if (is_numeric($value) || $value == "" || $value == "NULL") {
					$value = $value;
				} else {
					$value = "<![CDATA[$value]]>";
				}
				if ($value != "NULL") {
					$propertiesSegment .= "<$ns:$key>$value</$ns:$key>";
				}
			}
		}

		// add spatial data
		$geomSegment = "<$ns:$geomColumnName>" . $gml . "</$ns:$geomColumnName>";

		return "<wfs:Insert><$featureTypeName>" . $authSegment . 
				$propertiesSegment . $geomSegment . 
				"</$featureTypeName></wfs:Insert>";
	}
	
	protected function transactionUpdate ($feature, $featureType, $authWfsConfElement, $gml, $geomColumnName) {
		$featureTypeName = $featureType->name;
		$featureNS = $this->getNameSpace($featureType->name);

		// authentication
		$authSegment = "";
		if (!is_null($authWfsConfElement)) {
			$user = eval("return " . $authWfsConfElement->authVarname . ";");
			$authSegment = "<ogc:PropertyIsEqualTo><ogc:PropertyName>" . 
				$featureNS . ":" . $authWfsConfElement->name . 
				"</ogc:PropertyName><ogc:Literal>" . 
				$user . "</ogc:Literal></ogc:PropertyIsEqualTo>";

		}

		// add properties
		$propertiesSegment = "";
		foreach ($feature->properties as $key => $value) {
			if (isset($value)) {
				if (is_numeric($value) || $value == "" || $value == "NULL") {
					$value = $value;
				}
				else {
					$value = "<![CDATA[$value]]>";
				}
				if ($value != "NULL") {
					$propertiesSegment .= "<wfs:Property><wfs:Name>$featureNS:$key</wfs:Name>" . 
						"<wfs:Value>$value</wfs:Value></wfs:Property>";
				}
			}
		}

		// filter
		if (!isset($feature->fid)) {
			$e = new mb_exception("Feature ID not set.");
			return null;
		}
		$condition = $this->getFeatureIdFilter($feature->fid);
		if ($authSegment !== "") {
			$condition = "<And>" . $condition . $authSegment . "</And>";
		}
		$filterSegment = "<ogc:Filter>" . $condition . "</ogc:Filter>";

		// add geometry
		$geomSegment = "<wfs:Property><wfs:Name>$featureNS:$geomColumnName</wfs:Name>" . 
			"<wfs:Value>" . $gml . "</wfs:Value></wfs:Property>";
					

		return "<wfs:Update typeName=\"$featureTypeName\">" . 
				$propertiesSegment . 
				$geomSegment . 
				$filterSegment . 
				"</wfs:Update>";
	}
	
	protected function getFeatureIdFilter ($fid) {
	}
	
	protected function transactionDelete ($feature, $featureType, $authWfsConfElement) {
		$featureTypeName = $featureType->name;
		$featureNS = $this->getNameSpace($featureType->name);
		
		// authentication
		$authSegment = "";
		if (!is_null($authWfsConfElement)) {
			$user = eval("return " . $authWfsConfElement->authVarname . ";");
			$authSegment = "<ogc:PropertyIsEqualTo><ogc:PropertyName>" . 
				$featureNS . ":" . $authWfsConfElement->name . 
				"</ogc:PropertyName><ogc:Literal>" . 
				$user . "</ogc:Literal></ogc:PropertyIsEqualTo>";

		}

		// filter
		if (!isset($feature->fid)) {
			$e = new mb_exception("Feature ID not set.");
			return null;
		}
		$condition = $this->getFeatureIdFilter($feature->fid);
		if ($authSegment !== "") {
			$condition = "<And>" . $condition . $authSegment . "</And>";
		}

		return "<wfs:Delete typeName=\"$featureTypeName\">" . 
			"<ogc:Filter>" . $condition . "</ogc:Filter>" . 
			"</wfs:Delete>";
	}
	
	protected function wrapTransaction ($featureType, $wfsRequest) {
		$featureNS = $this->getNameSpace($featureType->name);
		
		$ns_gml = 'xmlns:gml="http://www.opengis.net/gml" ';	
		$ns_ogc = 'xmlns:ogc="http://www.opengis.net/ogc" ';	
		$ns_xsi = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
		$ns_featureNS = "xmlns:$featureNS=\"http://www.someserver.com/$featureNS\" ";	
		$ns_wfs = "xmlns:wfs=\"http://www.opengis.net/wfs\" ";	
		$strForSchemaLocation = "";
		
		foreach ($featureType->namespaceArray as $namespace) {
			$n = $namespace->name;
			$v = $namespace->value;

			if ($n === "gml") {
				 $ns_gml = "xmlns:$n=\"$v\" ";
			} 
			else if ($n === "ogc") {
				$ns_ogc = "xmlns:$n=\"$v\" ";
			} 
			else if ($n === "xsi") {
				$ns_xsi = "xmlns:$n=\"$v\" ";
			} 
			else if ($n === "wfs") {
				$ns_wfs = "xmlns:$n=\"$v\" ";
			} 
			else if ($n === $featureNS) {
				$ns_featureNS = "xmlns:$n=\"$v\" ";
				$strForSchemaLocation = $v;
			}
		}

		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . 
			"<wfs:Transaction version=\"" . $this->getVersion() . 
			"\" service=\"WFS\" " . $ns_gml . $ns_ogc . $ns_xsi . 
			$ns_featureNS . $ns_wfs . 
			# xsi:schemaLocation does not work with GS >2.1.
			#"xsi:schemaLocation=\"http://www.opengis.net/wfs" . 
			#" http://schemas.opengis.net/wfs/1.0.0/WFS-transaction.xsd " . 
			#$strForSchemaLocation . " " . $this->describeFeatureType . 
			#$this->getConjunctionCharacter($this->describeFeatureType) . 
			#"typename=" . $featureType->name . "\"".
			">" .	$wfsRequest . "</wfs:Transaction>";
	}
	
	
	// -----------------------------------------------------------------------
	//
	// Output formats
	//
	// -----------------------------------------------------------------------
	
	/**
	 * Compiles a string containing HTML formatted information about the WFS.
	 * 
	 * @return String 
	 */
	public function toHtml () {
		$wfsString = "";
		$wfsString .= "id: " . $this->id . " <br>";
		$wfsString .= "version: " . $this->getVersion() . " <br>";
		$wfsString .= "name: " . $this->name . " <br>";
		$wfsString .= "title: " . $this->title . " <br>";
		$wfsString .= "abstract: " . $this->summary . " <br>";
		$wfsString .= "capabilitiesrequest: " . $this->getCapabilities . " <br>";
		$wfsString .= "describefeaturetype: " . $this->describeFeatureType . " <br>";
		$wfsString .= "getfeature: " . $this->getFeature . " <br>";
		$wfsString .= "transaction: " . $this->transaction . " <br>";

		for ($i = 0; $i < count($this->featureTypeArray); $i++) {
			$currentFeatureType = $this->featureTypeArray[$i];
			$wfsString .= $currentFeatureType->toHtml();
		}
		return $wfsString;
	}

	/**
	 * Can be switched to other output format if desired.
	 * 
	 * @return String
	 */
	public function __toString () {
		return $this->toHtml();
	}

	/**
	 * Creates a string of JavaScript code. This code will then 
	 * create a WFS object on the client side.
	 * 
	 * @return String
	 */
	public function toJavaScript () {
		$jsString = "";

		$parent = "";
		if (func_num_args() == 1 && func_get_arg(0) == true) {
			$parent = "parent.";
		}
		
		if(!$this->title || $this->title == ""){
			$e = new mb_exception("Error: no valid capabilities-document !!");
			return null;
		}

		$jsString .= $parent . "add_wfs('". 
			$this->id ."','".
			$this->getVersion() ."','".
			$this->title ."','".
			$this->summary ."','". 
			$this->getCapabilities ."','" .
			$this->describeFeatureType .
			"');";
			
	
		for ($i = 0; $i < count($this->featureTypeArray); $i++) {
			$currentFeatureType = $this->featureTypeArray[$i];
			
			$jsString .= $parent . "wfs_add_featuretype('". 
				$currentFeatureType->name ."','". 
				$currentFeatureType->title . "','".
				$currentFeatureType->summary . "','".  
				$currentFeatureType->srs ."','". 
				$currentFeatureType->geomtype .
				"');";
				
			for ($j = 0; $j < count($currentFeatureType->elementArray); $j++) {
				$currentElement = $currentFeatureType->elementArray[$j];
				
				$jsString .= $parent . "wfs_add_featuretype_element('" . 
					$currentElement->name . "', '" . 
					$currentElement->type . "', " .
					$j . ", " . 
					$i . 
					");";
			}
			
			for ($j = 0; $j < count($currentFeatureType->namespaceArray); $j++) {
				$currentNamespace = $currentFeatureType->namespaceArray[$j];
				
				$jsString .= $parent . "wfs_add_featuretype_namespace('" . 
					$currentNamespace->name . "', '" . 
					$currentNamespace->value . "', " . 
					$j . ", " . 
					$i . 
					");";
			}
			
		}
		return $jsString;
	}

	/**
	 * For backwards compatibility only. Echoes a string directly.
	 * 
	 * @deprecated
	 * @return 
	 * @param $parent Boolean
	 */
	public function createJsObjFromWFS($parent){
		echo $this->toJavaScript($parent);
	}

	// -----------------------------------------------------------------------
	//
	// Database interface
	//
	// -----------------------------------------------------------------------
	
	/**
	 * Database wrapper function
	 * 
	 * @return Boolean
	 */
	public function insertOrUpdate () {
		return WfsToDb::insertOrUpdate($this);
	}

	/**
	 * Database wrapper function
	 * 
	 * @return Boolean
	 */
	public function insert () {
		return WfsToDb::insert($this);
	}

	/**
	 * Database wrapper function
	 * 
	 * @return Boolean
	 */
	public function update () {
		return WfsToDb::update($this);
	}
	
	/**
	 * Database wrapper function
	 * 
	 * @return Boolean
	 */
	public function delete () {
		return WfsToDb::delete($this);
	}

	/**
	 * Database wrapper function
	 * 
	 * @return Boolean
	 */
	public function exists () {
		return WfsToDb::exists($this);
	}
}
?>
