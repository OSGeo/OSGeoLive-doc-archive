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
require_once(dirname(__FILE__)."/class_connector.php");
require_once(dirname(__FILE__)."/class_administration.php");
require_once(dirname(__FILE__)."/class_wfs.php");

class Wfs_1_1 extends Wfs {
	const VERSION = "1.1.0";
	
	public function getVersion () {
		return "1.1.0";
	}

	public function transaction ($method, $wfsConf, $geoJson) {
		$gmlFactory = new Gml_3_Factory();
		$gmlObj = $gmlFactory->createFromGeoJson($geoJson);
	
		return parent::transaction ($method, $wfsConf, $gmlObj);
	}
	
	public function parseTransactionResponse ($xml) {
		$result = new stdClass();
		$result->success = false;
		$result->message = "";
		$result->xml = $xml;
		
		$simpleXml = simplexml_load_string($xml);
		$simpleXml->registerXPathNamespace('wfs', 'http://www.opengis.net/wfs');
		$simpleXml->registerXPathNamespace('ogc', 'http://www.opengis.net/ogc');
		
		//
		// get error messages
		//
		$nodeArray = $simpleXml->xpath("//wfs:TransactionResults/wfs:Action/wfs:Message");
		$messageArray = array();
		if ($nodeArray !== false) {
			foreach ($nodeArray as $node) {
				$domNode = dom_import_simplexml($node);
				
				$result->success = false;
				$messageArray[] = $domNode->nodeValue;
			}
		}
		if (count($messageArray) > 0) {
			$result->message = implode(". ", $messageArray);
			return $result;		
		}

		//
		// Get transaction results
		//
		$nodeArray = $simpleXml->xpath("//wfs:TransactionSummary/*");
		$messageArray = array();
		if ($nodeArray !== false) {
			foreach ($nodeArray as $node) {
				$domNode = dom_import_simplexml($node);
				$tagName = $this->sepNameSpace($domNode->nodeName);
				$result->success = true;
				$messageArray[] = $tagName . ": " . $domNode->nodeValue;
			}		
		}
		if (count($messageArray) > 0) {
			$result->message = implode(". ", $messageArray);

			// get fid
			$nodeArray = $simpleXml->xpath("//wfs:InsertResults/wfs:Feature/ogc:FeatureId");
			$e = new mb_exception(print_r($nodeArray, true));
			if ($nodeArray !== false) {
				foreach ($nodeArray as $node) {
					$domNode = dom_import_simplexml($node);
					if ($domNode->hasAttribute("fid")) {
						$result->fid = $domNode->getAttribute("fid");
					}
				}
			}
			return $result;		
		}
		
		//
		// Unknown error
		//
		$result->message = "An unknown error has occured.";
		return $result;
	}

	protected function getFeatureIdFilter ($fid) {
		return "<ogc:GmlObjectId gml:id=\"$fid\"/>";
	}
	
}
?>