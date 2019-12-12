<?php
# $Id: class_wmc.php 2466 2008-05-20 08:55:03Z christoph $
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

/**
 * The XML representation of a WMC object.
 * 
 * Usage:
 * 
 * $wmcToXml = new WmcToXml($wmc);
 * $xml = $wmcToXml->getXml();
 * 
 */
class WmcToXml {

	private $wmc = null;
	private $doc;
	private $xml = "";
	
	/**
	 * Constructor. Computes the XML of the WMC object given as parameter.
	 * 
	 * @param $someWmc wmc
	 */
	public function __construct ($someWmc) {
		$this->wmc = $someWmc;
		$this->toXml();
	} 

	// ---------------------------------------------------------------------
	// public functions
	// ---------------------------------------------------------------------
	
	public function getXml () {
		if (is_null($this->wmc)) {
			return null;
		}
		if ($this->xml == "") {
			$this->toXml();
		}
		return $this->xml;
	}
	
	// ---------------------------------------------------------------------
	// private functions
	// ---------------------------------------------------------------------
	
	private function toXml () {

		// generate XML
		$this->doc = new DOMDocument("1.0", CHARSET);
		$this->doc->preserveWhiteSpace = false;
		
		// ViewContext
		$e_view_context = $this->doc->createElementNS("http://www.opengis.net/context", "ViewContext");
		$e_view_context->setAttribute("version", "1.1.0");
		$e_view_context->setAttribute("id", $this->wmc->wmc_id);
		$e_view_context->setAttribute("xsi:schemaLocation", "http://www.opengis.net/context http://schemas.opengis.net/context/1.1.0/context.xsd");
		$e_view_context->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
		$e_view_context->setAttribute("xmlns:" . $this->wmc->extensionNamespace, $this->wmc->extensionNamespaceUrl);
		
		// General
		$e_general = $this->createGeneralNode();	
		if ($e_general !== null) {
			$e_view_context->appendChild($e_general);
		}	
		
		// Layerlist
		$e_layer_list = $this->doc->createElement("LayerList");

		// store overview layers
		$overviewLayerArray = array();
		if ($this->wmc->overviewMap !== null) {
			$currentOverviewWmsArray = $this->wmc->overviewMap->getWmsArray();
			for ($k = 0; $k < count($currentOverviewWmsArray); $k++) {
				$currentOverviewWms = $currentOverviewWmsArray[$k];

				for ($l = 0; $l < count($currentOverviewWms->objLayer); $l++) {
					$currentOverviewLayer = $currentOverviewWms->objLayer[$l];
					array_push($overviewLayerArray, $currentOverviewLayer);
				}
			}
		}

		$currentWmsArray = $this->wmc->mainMap->getWmsArray();
		$currentMap = $this->wmc->mainMap;
		for ($i = 0; $i < count($currentWmsArray); $i++) {
			$currentWms = $currentWmsArray[$i];
			for ($j = 0; $j < count($currentWms->objLayer); $j++) {
				$currentLayer = $currentWms->objLayer[$j];
				
				$layerNode = null;
				$found = false;
				for ($k = 0; $k < count($overviewLayerArray); $k++) {
					$currentOverviewLayer = $overviewLayerArray[$k];
					if ($currentLayer->layer_uid == $currentOverviewLayer->layer_uid) {
						$layerNode = $this->createLayerNode($currentMap, $currentWms, $currentLayer, $currentOverviewLayer);
						$found = true;
						break;
					}					
				}
				if (!$found) {
					$layerNode = $this->createLayerNode($currentMap, $currentWms, $currentLayer);
				}
				if ($layerNode !== null) {
					$e_layer_list->appendChild($layerNode);
				}
			}
		}
		$e_view_context->appendChild($e_layer_list);

		$this->doc->appendChild($e_view_context);
//		$this->xml = $this->doc->saveXML($this->doc->documentElement);
		$this->xml = $this->doc->saveXML();
		
//		$e = new mb_notice($this->xml);
	}

	private function createGeneralNode () {
		$extensionData = array();
		if ($this->wmc->overviewMap !== null) {
			$ovExtent = $this->wmc->overviewMap->getExtent();
			$extensionData["ov_minx"] = $ovExtent->min->x;
			$extensionData["ov_miny"] = $ovExtent->min->y;
			$extensionData["ov_maxx"] = $ovExtent->max->x;
			$extensionData["ov_maxy"] = $ovExtent->max->y;
			$extensionData["ov_srs"] = $ovExtent->epsg;
			$extensionData["ov_framename"] = $this->wmc->overviewMap->getFrameName();
			$extensionData["ov_width"] = $this->wmc->overviewMap->getWidth();
			$extensionData["ov_height"] = $this->wmc->overviewMap->getHeight();
		}
		
		// store epsg and bbox of root layer of 0th WMS
		$firstWms = $this->wmc->mainMap->getWms(0);
		if ($firstWms !== null) {
			for ($i = 0; $i < count($firstWms->objLayer[0]->layer_epsg); $i++) {
				$currentLayerEpsg = $firstWms->objLayer[0]->layer_epsg[$i];
				$extensionData["mainMapBox" . $i] = $currentLayerEpsg;
			}
		}
		
		if ($this->wmc->mainMap !== null) {
			$extensionData["main_framename"] = $this->wmc->mainMap->getFrameName();
		}
				
		// General
		$e_general = $this->doc->createElement("General");
		
		// Window
		$e_window = $this->doc->createElement("Window");
		if ($this->wmc->mainMap->getWidth() && $this->wmc->mainMap->getHeight()) {
			$e_window->setAttribute("width", $this->wmc->mainMap->getWidth());
			$e_window->setAttribute("height", $this->wmc->mainMap->getHeight());
		}
		$e_general->appendChild($e_window);
		
		// BoundingBox
		$mainExtent = $this->wmc->mainMap->getExtent();
		$e_bbox = $this->doc->createElement("BoundingBox");
		$e_bbox->setAttribute("SRS", $mainExtent->epsg);
		$e_bbox->setAttribute("minx", $mainExtent->min->x);
		$e_bbox->setAttribute("miny", $mainExtent->min->y);
		$e_bbox->setAttribute("maxx", $mainExtent->max->x);
		$e_bbox->setAttribute("maxy", $mainExtent->max->y);
		$e_general->appendChild($e_bbox);
				
		// Title
		$e_title = $this->doc->createElement("Title", $this->wmc->wmc_title);
		$e_general->appendChild($e_title);
				
		// Keywords
		if (count($this->wmc->wmc_keyword) > 0) {
			$e_keyword_list = $this->doc->createElement("KeywordList");
			for ($i=0; $i < count($this->wmc->wmc_keyword); $i++) {
				$e_keyword = $this->doc->createElement("Keyword", $this->wmc->wmc_keyword[$i]);
				$e_keyword_list->appendChild($e_keyword);
			}
			$e_general->appendChild($e_keyword_list);
		}
				
		// Abstract
		if ($this->wmc->wmc_abstract) {
			$e_abstract = $this->doc->createElement("Abstract", $this->wmc->wmc_abstract);
			$e_general->appendChild($e_abstract);
		}
	
		// Logo URL			
		if ($this->wmc->wmc_logourl_width && $this->wmc->wmc_logourl_height && 
			$this->wmc->wmc_logourl_format && $this->wmc->wmc_logourl){
			
			$e_logo_url = $this->doc->createElement("LogoURL");
			$e_logo_url->setAttribute("width", $this->wmc->wmc_logourl_width);
			$e_logo_url->setAttribute("height", $this->wmc->wmc_logourl_height);
			$e_logo_url->setAttribute("format", $this->wmc->wmc_logourl_format);
		
			$e_logo_url_or = $this->doc->createElement("OnlineResource");
			$e_logo_url_or->setAttributeNS("http://www.opengis.net/context", "xmlns:xlink", "http://www.w3.org/1999/xlink");
			$e_logo_url_or->setAttribute("xlink:type", "simple");
			$e_logo_url_or->setAttribute("xlink:href", $this->wmc->wmc_logourl);
			$e_logo_url->appendChild($e_logo_url_or);

			$e_general->appendChild($e_logo_url);
		}
				
		// Description URL	
		if ($this->wmc->wmc_descriptionurl){
			$e_description_url = $this->doc->createElement("DescriptionURL");

			$e_description_url_or = $this->doc->createElement("OnlineResource");
			$e_description_url_or->setAttributeNS("http://www.opengis.net/context", "xmlns:xlink", "http://www.w3.org/1999/xlink");
			$e_description_url_or->setAttribute("xlink:type", "simple");
			$e_description_url_or->setAttribute("xlink:href", $this->wmc->wmc_descriptionurl);
			$e_description_url->appendChild($e_description_url_or);

			$e_general->appendChild($e_description_url);
		}
				
		// Contact information
		$e_contact = $this->createContactInformationNode();
		if ($e_contact !== null) {
			$e_general->appendChild($e_contact);
		}

		// Extension data				
		if (count($extensionData) > 0) {
			$e_extensionGeneral = $this->doc->createElement("Extension");
			
			foreach ($extensionData as $keyExtensionData => $valueExtensionData) {
				$e_currentExtensionTag = $this->addExtension($keyExtensionData, $valueExtensionData);
				$e_extensionGeneral->appendChild($e_currentExtensionTag);
			}
			foreach ($this->wmc->generalExtensionArray as $keyExtensionData => $valueExtensionData) {
				$e_currentExtensionTag = $this->addExtension($keyExtensionData, $valueExtensionData);
				$e_extensionGeneral->appendChild($e_currentExtensionTag);
			}
			
			$e_general->appendChild($e_extensionGeneral);
		}
		return $e_general;
	}
	
	private function createLayerNode () {
		if (func_num_args() == 3) {
			$currentMap = func_get_arg(0);
			$currentWms = func_get_arg(1);
			$currentLayer = func_get_arg(2);
			$currentOverviewLayer = null;
		}
		else if (func_num_args() == 4) {
			$currentMap = func_get_arg(0);
			$currentWms = func_get_arg(1);
			$currentLayer = func_get_arg(2);
			$currentOverviewLayer = func_get_arg(3);		
		}
		else {
			return null;
		}

		// Layer
		$e_layer = $this->doc->createElement("Layer");
		$e_layer->setAttribute("queryable", $currentLayer->layer_queryable);
		$e_layer->setAttribute("hidden", ($currentLayer->gui_layer_visible ? 0 : 1));

		// Server
		$e_service = $this->doc->createElement("Server");
		$e_service->setAttribute("service", "OGC:WMS");
		$e_service->setAttribute("version", $currentWms->wms_version);
		$e_service->setAttribute("title", $currentWms->wms_title);

		// Online resource
		$e_service_or = $this->doc->createElement("OnlineResource");
		$e_service_or->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
		$e_service_or->setAttribute("xlink:type", "simple");
		$e_service_or->setAttribute("xlink:href", $currentWms->wms_getmap);
		$e_service->appendChild($e_service_or);
		$e_layer->appendChild($e_service);

		// Name
		$currentLayerName = $currentLayer->layer_name;
		$e_layer_name = $this->doc->createElement("Name", $currentLayerName);
		$e_layer->appendChild($e_layer_name);

		// Title
		$currentLayerTitle = $currentLayer->gui_layer_title;
		$e_layer_title = $this->doc->createElement("Title", $currentLayerTitle);
		$e_layer->appendChild($e_layer_title);
		
		// Abstract
		if ($currentWms->wms_abstract){
			$e_layer_abstract = $this->doc->createElement("Abstract", $currentWms->wms_abstract);
			$e_layer->appendChild($e_layer_abstract);
		}

		// Data URL
		if ($currentLayer->layer_dataurl_href){
			$e_layer_data_url = $this->doc->createElement("DataURL");

			$e_layer_data_url_or = $this->doc->createElement("OnlineResource");
			$e_layer_data_url_or->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
			$e_layer_data_url_or->setAttribute("xlink:type", "simple");
			$e_layer_data_url_or->setAttribute("xlink:href", $currentLayer->layer_dataurl_href);
			
			$e_layer_data_url->appendChild($e_layer_data_url_or);
			$e_layer->appendChild($e_layer_data_url);
		}

		// Metadata URL
		if ($currentLayer->layer_metadataurl){
			$e_layer_metadata_url = $this->doc->createElement("MetadataURL");

			// Metadata URL online resource
			$e_layer_metadata_url_or = $this->doc->createElement("OnlineResource");
			$e_layer_metadata_url_or->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
			$e_layer_metadata_url_or->setAttribute("xlink:type", "simple");
			$e_layer_metadata_url_or->setAttribute("xlink:href", $currentLayer->layer_metadataurl);
			$e_layer_metadata_url->appendChild($e_layer_metadata_url_or);
			$e_layer->appendChild($e_layer_metadata_url);
		}

		// SRS		
		$srsNode = $this->createSrsNode($currentMap, $currentWms);
		if ($srsNode !== null) {
			$e_layer->appendChild($srsNode);
		}

		// Layer format
		$formatListNode = $this->createLayerFormatListNode($currentWms);
		if ($formatListNode !== null) {
			$e_layer->appendChild($formatListNode);
		}

		// Layer style
		$layerStyleListNode = $this->createLayerStyleNode($currentWms, $currentLayer);
		if ($layerStyleListNode !== null) {
			$e_layer->appendChild($layerStyleListNode);
		}

		// Extension
		$extensionNode = $this->createLayerExtensionNode($currentMap, $currentWms, $currentLayer, $currentOverviewLayer);
		if ($extensionNode !== null) {
			$e_layer->appendChild($extensionNode);
		}
		return $e_layer;
	}

	private function createSrsNode ($currentMap, $currentWms) {
		$wms_epsg = array();
		$wms_epsg[0] = $currentMap->getEpsg();
	
		if ($currentWms->gui_wms_epsg != $currentMap->getEpsg()) {
			$wms_epsg[1] = $currentWms->gui_wms_epsg;
		}

		for ($j = 0; $j < count($currentWms->gui_epsg); $j++) {
			if (!in_array($currentWms->gui_epsg[$j], $wms_epsg)){
				array_push($wms_epsg, $currentWms->gui_epsg[$j]);
			}
		}

		$e_layer_srs = $this->doc->createElement("SRS", implode(" ", $wms_epsg));
		return $e_layer_srs;
	}

	private function createLayerFormatListNode ($currentWms) {
		$e_layer_format = $this->doc->createElement("FormatList");

		$data_format_current = false;
		
		for ($k = 0; $k < count($currentWms->data_format); $k++){

			if ($currentWms->data_type[$k] == "map") {
				$layerFormat = $currentWms->data_format[$k];

				$e_format = $this->doc->createElement("Format", $layerFormat);

				if ($data_format_current === false && ( 
						$currentWms->data_format[$k] == $currentWms->gui_wms_mapformat ||
						$k == (count($currentWms->data_format)-1)
				)){

					$e_format->setAttribute("current", "1");
					$data_format_current = true;
				}
				$e_layer_format->appendChild($e_format);
			}
		}
		return $e_layer_format;
	}

	private function createLayerExtensionNode ($currentMap, $currentWms, $currentLayer, $currentOverviewLayer) {
		$layerExtensionData = array();
		$layerExtensionData["wms_name"] = $currentWms->objLayer[0]->layer_name;
		$layerExtensionData["minscale"] = $currentLayer->layer_minscale;
		$layerExtensionData["maxscale"] = $currentLayer->layer_maxscale;
		$layerExtensionData["gui_minscale"] = $currentLayer->gui_layer_minscale;
		$layerExtensionData["gui_maxscale"] = $currentLayer->gui_layer_maxscale;
		$layerExtensionData["layer_id"] = $currentLayer->layer_uid;
		$layerExtensionData["wms_layer_id"] = $currentWms->objLayer[0]->layer_uid;
		$layerExtensionData["wms_selectable"] = $currentWms->objLayer[0]->gui_layer_selectable;
		$layerExtensionData["wms_visible"] = $currentWms->gui_wms_visible;
		$layerExtensionData["layer_pos"] = $currentLayer->layer_pos;
		$layerExtensionData["layer_parent"] = $currentLayer->layer_parent;
		$layerExtensionData["wms_id"] = $currentLayer->gui_layer_wms_id;
		$layerExtensionData["querylayer"] = $currentLayer->gui_layer_querylayer;
		$layerExtensionData["gui_selectable"] = $currentLayer->gui_layer_selectable;
		$layerExtensionData["gui_queryable"] = $currentLayer->gui_layer_queryable;
		$layerExtensionData["gui_status"] = $currentLayer->gui_layer_status;
		$layerExtensionData["layer_epsg"] = $currentLayer->layer_epsg;
		$layerExtensionData["gui_wms_opacity"] = $currentWms->gui_wms_opacity;
		
		for ($i = 0; $i < count($currentWms->gui_epsg); $i++) {
			$found = false;
			for ($j = 0; $j < count($layerExtensionData["layer_epsg"]); $j++) {
				if ($layerExtensionData["layer_epsg"][$j]["epsg"] == $currentWms->gui_epsg[$i]) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$layerExtensionData["layer_epsg"][]= array(
					"epsg" => $currentWms->gui_epsg[$i],
					"minx" => $currentWms->gui_minx[$i],
					"miny" => $currentWms->gui_miny[$i],
					"maxx" => $currentWms->gui_maxx[$i],
					"maxy" => $currentWms->gui_maxy[$i]
				);
			}
		}

		if ($currentLayer->gui_layer_wfs_featuretype !== "") {
			$layerExtensionData["wfsFeatureType"] = $currentLayer->gui_layer_wfs_featuretype;
		}

		if ($currentOverviewLayer != null) {
			$layerExtensionData["overviewData"] = array("overviewHidden" => ($currentOverviewLayer->gui_layer_visible ? 0 : 1));
		}
		if ($currentLayer->gui_layer_wfs_featuretype) {
			$layerExtensionData["wfsFeatureType"] = $currentLayer->gui_layer_wfs_featuretype;
		}

		if (count($layerExtensionData) > 0) {
			$e_extension = $this->doc->createElement("Extension");
			
			foreach ($layerExtensionData as $keyExtensionData => $valueExtensionData) {
				$e_currentExtensionTag = $this->addExtension($keyExtensionData, $valueExtensionData);
				$e_extension->appendChild($e_currentExtensionTag);
			}
			return $e_extension;
		}
		return null;
	}
	
	private function addExtension ($key, $value) {
		if (is_array($value)) {
			if (is_numeric($key)) {
				$key = "data" . $key;
			}
//			$e_currentExtensionTag = $this->doc->createElementNS($this->wmc->extensionNamespaceUrl, $this->wmc->extensionNamespace.":".$key);
			$e_currentExtensionTag = $this->doc->createElement($this->wmc->extensionNamespace.":".$key);
			foreach ($value as $childKey => $childValue) {
				$newNode = $this->addExtension($childKey, $childValue);
				$e_currentExtensionTag->appendChild($newNode);
			}
		}
		else {
//			$e_currentExtensionTag = $this->doc->createElementNS($this->wmc->extensionNamespaceUrl, $this->wmc->extensionNamespace.":".$key, $value);
			$e_currentExtensionTag = $this->doc->createElement($this->wmc->extensionNamespace.":".$key, $value);
		}
		return $e_currentExtensionTag;
	}
	
	private function createLayerStyleNode ($currentWms, $currentLayer) {
		$e_layer_stylelist = $this->doc->createElement("StyleList");
		
		for ($k = 0; $k < count($currentLayer->layer_style); $k++) {

			$currentStyle = $currentLayer->layer_style[$k];
			
			$layerStyle_current = 0;
			if ($k === 0){
				$layerStyle_current = 1; // To do: insert proper data
			}

			$e_layer_style = $this->doc->createElement("Style");

			$layerStyleSLD = "";

			if ($layerStyleSLD) {
				$e_layer_style_or = $this->doc->createElement("OnlineResource");
				$e_layer_style_or->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
				$e_layer_style_or->setAttribute("xlink:type", "simple");
				$e_layer_style_or->setAttribute("xlink:href", $currentWms->gui_wms_sldurl);
				$e_layer_style->appendChild($e_layer_style_or);
			}
			else{
				
				if ($layerStyle_current == 1){
					$e_layer_style->setAttribute("current", "1");
				}

				$e_layer_style_name = $this->doc->createElement("Name", $currentStyle["name"]);
				$e_layer_style->appendChild($e_layer_style_name);

				$e_layer_style_title = $this->doc->createElement("Title", $currentStyle["title"]);
				$e_layer_style->appendChild($e_layer_style_title);
				
				
				$e_layer_style_legendurl = $this->doc->createElement("LegendURL");

				//TODO: determine correct layer style entries
				$layerStyle_legendUrl_width = ""; // To Do: add proper data
				$layerStyle_legendUrl_height = ""; // To Do: add proper data
				$layerStyle_legendUrl_format = ""; // To Do: add proper data
				$e_layer_style_legendurl->setAttribute("width", $layerStyle_legendUrl_width);
				$e_layer_style_legendurl->setAttribute("height", $layerStyle_legendUrl_height);
				$e_layer_style_legendurl->setAttribute("format", $layerStyle_legendUrl_format);

				$e_layer_style_legendurl_or = $this->doc->createElement("OnlineResource");
				$e_layer_style_legendurl_or->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
				$e_layer_style_legendurl_or->setAttribute("xlink:type", "simple");
				$e_layer_style_legendurl_or->setAttribute("xlink:href", $currentStyle["legendurl"]);
				$e_layer_style_legendurl->appendChild($e_layer_style_legendurl_or);
				$e_layer_style->appendChild($e_layer_style_legendurl);
			}
			$e_layer_stylelist->appendChild($e_layer_style);
		}	
		return $e_layer_stylelist;
	}
	
	/**
	 * 
	 * @return 
	 */
	private function createContactInformationNode () {
		
		if ($this->wmc->wmc_contactemail || $this->wmc->wmc_contactorganization ||
			$this->wmc->wmc_contactperson || $this->wmc->wmc_contactposition || 
			$this->wmc->wmc_contactaddresstype || $this->wmc->wmc_contactaddress || 
			$this->wmc->wmc_contactcity || $this->wmc->wmc_contactstateorprovince ||
			$this->wmc->wmc_contactpostcode || $this->wmc->wmc_contactcountry || 
			$this->wmc->wmc_contactvoicetelephone || $this->wmc->wmc_contactfacsimiletelephone) {
				
			$e_contact = $this->doc->createElement("ContactInformation");
			$e_contact_person_primary = $this->wmc->createContactPersonPrimaryNode();
			if ($e_contact_person_primary !== null) {
				$e_contact->appendChild($e_contact_person_primary);
			}
		
			if ($this->wmc->wmc_contactposition){
				$e_contact_position = $this->doc->createElement("ContactPosition", $this->wmc->wmc_contactposition);
				$e_contact->appendChild($e_contact_position);
			}
		
			if ($this->wmc->wmc_contactaddresstype || $this->wmc->wmc_contactaddress || 
				$this->wmc->wmc_contactcity || $this->wmc->wmc_contactstateorprovince ||
				$this->wmc->wmc_contactpostcode || $this->wmc->wmc_contactcountry) {
		
				$e_contact_address = $this->doc->createElement("ContactAddress");
		
				if ($this->wmc->wmc_contactaddresstype){
					$e_address_type = $this->doc->createElement("AddressType", $this->wmc->wmc_contactaddresstype);
					$e_contact_address->appendChild($e_address_type);
				}
				if ($this->wmc->wmc_contactaddress){
					$e_address = $this->doc->createElement("Address", $this->wmc->wmc_contactaddress);
					$e_contact_address->appendChild($e_address);
				}
				if ($this->wmc->wmc_contactcity){
					$e_city = $this->doc->createElement("City", $this->wmc->wmc_contactcity);
					$e_contact_address->appendChild($e_city);
				}
				if ($this->wmc->wmc_contactstateorprovince){
					$e_state = $this->doc->createElement("StateOrProvince", $this->wmc->wmc_contactstateorprovince);
					$e_contact_address->appendChild($e_state);
				}
				if ($this->wmc->wmc_contactpostcode){
					$e_postcode = $this->doc->createElement("PostCode", $this->wmc->wmc_contactpostcode);
					$e_contact_address->appendChild($e_postcode);
				}
				if ($this->wmc->wmc_contactcountry){
					$e_country = $this->doc->createElement("Country", $this->wmc->wmc_contactcountry);
					$e_contact_address->appendChild($e_country);
				}
				$e_contact->appendChild($e_contact_address);
			}
			
			if ($this->wmc->wmc_contactvoicetelephone){
				$e_voice_telephone = $this->doc->createElement("ContactVoiceTelephone", $this->wmc->wmc_contactvoicetelephone);
				$e_contact->appendChild($e_voice_telephone);
			}
			if ($this->wmc->wmc_contactfacsimiletelephone){
				$e_facsimile_telephone = $this->doc->createElement("ContactFacsimileTelephone", $this->wmc->wmc_contactfacsimiletelephone);
				$e_contact->appendChild($e_facsimile_telephone);
			}
			if ($this->wmc->wmc_contactemail){
				$e_email = $this->doc->createElement("ContactElectronicMailAddress", $this->wmc->wmc_contactemail);
				$e_contact->appendChild($e_email);
			}
			return $e_contact;
		}		
		return null;
	}
	
	private function createContactPersonPrimaryNode () {
		if ($this->wmc->wmc_contactperson || $this->wmc->wmc_contactorganization){
			$e_contact_person_primary = $this->doc->createElement("ContactPersonPrimary");
	
			if ($this->wmc->wmc_contactperson){
				$e_contact_person = $this->doc->createElement("ContactPerson", $this->wmc->wmc_contactperson);
				$e_contact_person_primary->appendChild($e_contact_person);
			}
			if ($this->wmc->wmc_contactorganization){
				$e_contact_organization = $this->doc->createElement("ContactOrganization", $this->wmc->wmc_contactorganization);
				$e_contact_person_primary->appendChild($e_contact_organization);
			}
			return $e_contact_person_primary;
		}
		return null;		
	}

}
?>
