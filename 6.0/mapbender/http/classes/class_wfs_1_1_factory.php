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
require_once(dirname(__FILE__)."/../classes/class_wfs_factory.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_1_1.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_featuretype.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

/**
 * Creates WFS 1.1 objects from a capabilities documents.
 *
 * @return Wfs_1_1
 */
class Wfs_1_1_Factory extends WfsFactory {

	protected function createFeatureTypeFromUrl ($aWfs, $featureTypeName, $featureTypeNsArray) {
		$postData = "<?xml version=\"1.0\"?>\n".
				"<DescribeFeatureType version=\"" . $aWfs->getVersion() . "\" " .
				"service=\"WFS\" xmlns=\"http://www.opengis.net/wfs\" ";

		$nsUrl = $featureTypeNsArray["xmlns:" . $key];
		if (!$nsUrl) {
			$nsUrl = $featureTypeNsArray[$key];
		}

		if ($featuretype_name != $this->sepNameSpace($featureTypeName)) {
			$key = "xmlns:" . $this->getNameSpace($featureTypeName);
			$postData .= $key . "=\"" . $nsUrl . "\" ";
		}
		$postData .= "><TypeName>" . $featureTypeName . "</TypeName>" .
				"</DescribeFeatureType>";

		$xml = $this->post($aWfs->describeFeatureType, $postData);
		return $this->createFeatureTypeFromXml ($xml, $aWfs, $featureTypeName);
	}

	protected function createFeatureTypeFromUrlGet ($aWfs, $featureTypeName, $featureTypeNsArray) {

		$key = $this->getNameSpace($featureTypeName);
		$nsUrl = $featureTypeNsArray["xmlns:" . $key];
		if (!$nsUrl) {
			$nsUrl = $featureTypeNsArray[$key];
		}
		$paramArray = array(
			"SERVICE=WFS",
			"VERSION=1.1.0",
			"REQUEST=DescribeFeatureType",
			"TYPENAME=" . urlencode($featureTypeName),
			"NAMESPACE=" . urlencode(
				"xmlns(" . $key . "=" . $nsUrl . ")"
		));

		$url = $aWfs->describeFeatureType .
			$aWfs->getConjunctionCharacter($aWfs->describeFeatureType) .
			implode("&", $paramArray);

		$xml = $this->get($url);
		return $this->createFeatureTypeFromXml ($xml, $aWfs, $featureTypeName);
	}

	/**
	 * Given an XSD document (usually from a DescribefeatureType Operation) 
	 * @returns a WfsFeatureType
	 * 

	*/
	protected function createFeatureTypeFromXml ($xml, $myWfs, $featureTypeName) {
		$newFeatureType = new WfsFeatureType($myWfs);

		$doc = new DOMDocument();
		$doc->loadXML($xml);
		$xpath =  new DOMXpath($doc);
		$xpath->registerNamespace("xs","http://www.w3.org/2001/XMLSchema");

		// populate a Namespaces Hastable where we can use thec namesopace as a lookup for the prefix
		// and also keep a 
		$namespaces = array();
		$namespaceList = $xpath->query("//namespace::*");
		$targetNamespace = $doc->documentElement->getAttribute("targetNamespace");
		$targetNamespaceNode = null;

		foreach($namespaceList as $namespaceNode){
			$namespaces[$namespaceNode->nodeValue] = $namespaceNode->localName;
			if($namespaceNode->nodeValue == $targetNamespace){
				$targetNamespaceNode = $namespaceNode;
			}
			$newFeatureType->addNamespace($namespaceNode->localName, $namespaceNode->nodeValue);
		}
	
		list($ftLocalname,$ftTypePrefix) = array_reverse(explode(":",$featureTypeName));
		// for the sake of simplicity we only care about top level elements. Seems to have worked so far
		$query = sprintf("/xs:schema/xs:element[@name='%s']",$ftLocalname);
		$elementList = $xpath->query($query);

		foreach ($elementList as $elementNode){
			$elementName = $elementNode->getAttribute("name");
			$elementType = $elementNode->getAttribute("type");

            // if Type is empty, we assume an anonymousType, else we go looking for the anmed Type
            if($elementType == ""){
                // Just querying for complexTypes containing a Sequence - good enough for Simple Features
                $query = "xs:complexType//xs:element";
                $subElementList = $xpath->query($query,$elementNode);

            }else{

                // The elementType is now bound to a prefix e.g. topp:housType
                // if the prefix is in the targetNamespace, changces are good it's defined in this very document
                // if the prefiox is not in the targetNamespace, it's likely not defined here, and we bail

                list($elementTypeLocalname,$elementTypePrefix) = array_reverse(explode(":",$elementType));
                $elementTypeNamespace = $doc->lookupNamespaceURI($elementTypePrefix);
                if($elementTypeNamespace !== $targetNamespaceNode->nodeValue){
                    $e = new mb_warning("Tried to parse FeatureTypeName $featureTypeName : $elementType is not in the targetNamespace");	
                    break;
                }

                // Just querying for complexTypes containing a Sequence - good enough for Simple Features
                $query = sprintf("//xs:complexType[@name='%s']//xs:element",$elementTypeLocalname);
                $subElementList = $xpath->query($query);

            }
			foreach($subElementList as $subElement){
                // Since this is a rewrite of the old way, it reproduces it quirks
                // in this case the namespace of the type was cut off for some reason
				$name = $subElement->getAttribute('name');
                $typeParts = explode(":",$subElement->getAttribute('type'));
                if(count($typeParts) == 1){
                    $type = $typeParts[0];
                }else{
                    $type = $typeParts[1];
                }
				$newFeatureType->addElement($name,$type);
			}

		}
		return $newFeatureType;
	}

	/**
	 * Creates WFS 1.0 objects from a capabilities documents.
	 *
	 * @return Wfs_1_1
	 * @param $xml String
	 */
	public function createFromXml ($xml) {
		try {

			$myWfs = new Wfs_1_1();

			$admin = new administration();
			$values = $admin->parseXml($xml);

			$myWfs->getCapabilitiesDoc = $admin->char_encode($xml);
			$myWfs->id = $this->createId();

			foreach ($values as $element) {
				$tag = $this->sepNameSpace(strtoupper($element[tag]));

				if($tag == "WFS_CAPABILITIES" && $element[type] == "open"){
					$myWfs->version = $element[attributes][version];
					if ($myWfs->version !== "1.1.0") {
						throw new Exception("Not a WFS 1.1.0 capabilities document.");
					}
				}
				if($tag == "NAME"  && $element[level] == '3'){
					$myWfs->name = $element[value];
				}
				if($tag == "TITLE"  && $element[level] == '3'){
					$myWfs->title = $this->stripEndlineAndCarriageReturn($element[value]);
				}
				if($tag == "ABSTRACT" && $element[level] == '3'){
					$myWfs->summary = $this->stripEndlineAndCarriageReturn($element[value]);
				}
				if($tag == "FEES"){
					$myWfs->fees = $element[value];
				}
				if($tag == "ACCESSCONSTRAINTS"){
					$myWfs->accessconstraints = $element[value];
				}

				if($tag == "OPERATION" && $element[type] == "open"){
					switch ($element[attributes][name]) {
						case "GetCapabilities" :
							$section = "getcapabilities";
							break;
						case "DescribeFeatureType" :
							$section = "describefeaturetype";
							break;
						case "GetFeature" :
							$section = "getfeature";
							break;
						case "Transaction" :
							$section = "transaction";
							break;
					}
				}
				# getCapabilities
				if($section == "getcapabilities" && $tag == "GET"){
					$myWfs->getCapabilities = html_entity_decode($element[attributes]["xlink:href"]);
				}

				# descriptFeatureType
#				if($section == "describefeaturetype" && $tag == "POST"){
#					$myWfs->describeFeatureType = html_entity_decode($element[attributes]["xlink:href"]);
#				}

				# descriptFeatureType
				if($section == "describefeaturetype" && $tag == "GET"){
					$myWfs->describeFeatureType = html_entity_decode($element[attributes]["xlink:href"]);
				}

				# getFeature
				if($section == "getfeature" && $tag == "POST"){
					$myWfs->getFeature = html_entity_decode($element[attributes]["xlink:href"]);
				}

				# transaction
				if($section == "transaction" && $tag == "POST"){
					$myWfs->transaction = html_entity_decode($element[attributes]["xlink:href"]);
				}

				if($tag == "OPERATION" && $element[type] == "close"){
					$section = "";
				}

				if($tag == "FEATURETYPE" && $element[type] == "open"){
					$section = "featuretype";
					$featureTypeNsArray = $element[attributes];
				}
				if($section == "featuretype" && $tag == "NAME"){
					$featuretype_name = $element[value];
				}
				if($section == "featuretype" && $tag == "TITLE"){
					$featuretype_title = $this->stripEndlineAndCarriageReturn($element[value]);
				}
				if($section == "featuretype" && $tag == "ABSTRACT"){
					$featuretype_abstract = $element[value];
				}
				if($section == "featuretype" && $tag == "DEFAULTSRS"){
					$featuretype_srs = $element[value];

					// Do not add defective featuretypes
					try {
						$currentFeatureType = $this->createFeatureTypeFromUrlGet($myWfs, $featuretype_name, $featureTypeNsArray);
						if ($currentFeatureType !== null) {
							$currentFeatureType->name = $featuretype_name;
							$currentFeatureType->title = $featuretype_title;
							$currentFeatureType->summary = $featuretype_abstract;
							$currentFeatureType->srs = $featuretype_srs;

							$myWfs->addFeatureType($currentFeatureType);
						}
					}
					catch (Exception $e) {
						new mb_exception("Failed to load featuretype " . $featuretype_name);
					}
				}
			}
			if (!$myWfs->title) {
				$myWfs->title = "Untitled";
			}
			return $myWfs;
		}
		catch (Exception $e) {
			$e = new mb_exception($e);
			return null;
		}
	}

	public function createFromDb ($id) {
		$myWfs = new Wfs_1_1();
		return parent::createFromDb($id, $myWfs);
	}
}
?>
