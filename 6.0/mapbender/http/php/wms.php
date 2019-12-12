<?php
# $Id: wms.php 
# http://www.mapbender.org/index.php/wms.php
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
require_once(dirname(__FILE__)."/../classes/class_layer_monitor.php");

//
// make all parameters available as upper case
//
foreach($_GET as $key => $val) {
	$_GET[strtoupper($key)] = $val;
}

$requestType = $_GET["REQUEST"];
$version = $_GET["VERSION"];
$service = strtoupper($_GET["SERVICE"]);
$layerId = $_GET["LAYER_ID"];
$updateSequence = intval($_GET["UPDATESEQUENCE"]);
$inspire = $_GET["INSPIRE"];

if (isset($inspire) && $inspire === 1 ) {
	$inspire = true;
}

$mapbenderMetadaUrl = $_SERVER['HTTP_HOST']."/mapbender/php/mod_showMetadata.php?resource=layer&id=";
$inspireServiceMetadataUrl =  $_SERVER['HTTP_HOST']."/mapbender/php/mod_layerISOMetadata.php?SERVICE=WMS&outputFormat=iso19139&Id=";
$mapbenderMetadataUrlUrl = $_SERVER['HTTP_HOST']."/mapbender/php/mod_dataISOMetadata.php?outputFormat=iso19139&id=";

//http://www.geoportal.rlp.de/mapbender/php/mod_layerISOMetadata.php?SERVICE=WMS&outputFormat=iso19139&Id=24615
if (isset($_SERVER["HTTPS"])){
	$urlPrefix = "https://";
} else {
	$urlPrefix = "http://";
}
$mapbenderMetadataUrl = $urlPrefix.$mapbenderMetadataUrl;
$inspireServiceMetadataUrl = $urlPrefix.$inspireServiceMetadataUrl;
$mapbenderMetadataUrlUrl = $urlPrefix.$mapbenderMetadataUrlUrl;

$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);

/**
 * Creates an XML Exception according to WMS 1.1.1
 * 
 * @return an XML String
 * @param $errorCode String
 * @param $errorMessage String
 */
function createExceptionXml ($errorCode, $errorMessage) {
	// see http://de2.php.net/manual/de/domimplementation.createdocumenttype.php
	$imp = new DOMImplementation;
	$dtd = $imp->createDocumentType("ServiceExceptionReport", "", "http://schemas.opengis.net/wms/1.1.1/exception_1_1_1.dtd");
	
	$doc = $imp->createDocument("", "", $dtd);
	$doc->encoding = 'UTF-8';
	$doc->standalone = false;
	
	$el = $doc->createElement("ServiceExceptionReport");
	$exc = $doc->createElement("ServiceException", $errorMessage);
	if ($errorCode) {
		$exc->setAttribute("code", $errorCode);
	}
	$el->appendChild($exc);
	$doc->appendChild($el);
	
	return $doc->saveXML();
}

//
// check if service param is set
//
if (!isset($service) || $service === "" || $service != "WMS") {
	header("Content-type: application/xhtml+xml; charset=UTF-8");
	echo createExceptionXml("", "Parameter SERVICE invalid");
	die;
}

//
// check if request param is set
//
if (!isset($requestType) || $requestType === "" || ($service == "WMS" && $requestType != "GetCapabilities")) {
	header("Content-type: application/xhtml+xml; charset=UTF-8");
	echo createExceptionXml("", "Parameter REQUEST invalid");
	die;
}

//
// check if version param is set
//
if (!isset($version) || $version === "" || ($service == "WMS" && $version != "1.1.1")) {
	// optional parameter, set to 1.1.1 if not set
	$version = "1.1.1";
}

//
// check if layer id is set
//
if (!isset($layerId) || !is_numeric($layerId)) {
	// TO DO: create exception XML
	header("Content-type: application/xhtml+xml; charset=UTF-8");
	echo createExceptionXml("Layer not defined", "Unknown layer id " . $layerId);
	die;
}

//
// check if layer is stored in database
//
$wms_sql = "SELECT * FROM wms AS w, layer AS l " . 
	"where l.layer_id = $1 AND l.fkey_wms_id = w.wms_id LIMIT 1";
$v = array($layerId);
$t = array("i");
$res_wms_sql = db_prep_query($wms_sql, $v, $t);
$wms_row = db_fetch_array($res_wms_sql);

if (!$wms_row["wms_id"]) {
	// TO DO: create exception XML
	header("Content-type: application/xhtml+xml; charset=UTF-8");
	echo createExceptionXml("Layer not defined", "Unknown layer id " . $layerId);
	die;
}

//
// check if update sequence is valid
//
$updateSequenceDb = intval($wms_row["wms_timestamp"]);

if ($updateSequence) {
	if ($updateSequence > $updateSequenceDb) {
		// Exception: code=InvalidUpdateSequence
		header("Content-type: application/xhtml+xml; charset=UTF-8");
		echo createExceptionXml("InvalidUpdateSequence", "Invalid update sequence");
		die;
	}
	else if ($updateSequence == $updateSequenceDb) {
		// Exception: code=CurrentUpdateSequence
		header("Content-type: application/xhtml+xml; charset=UTF-8");
		echo createExceptionXml("CurrentUpdateSequence", "Current update sequence");
		die;
	}
}

//
// increment layer count
//
$monitor = new Layer_load_count();
$monitor->increment($layerId);



// ---------------------------------------------------------------------------
//
// START TO CREATE CAPABILITIES DOC
// (return most recent Capabilities XML)
//
// ---------------------------------------------------------------------------

$doc = new DOMDocument('1.0');
$doc->encoding = 'UTF-8';
$doc->standalone = false;

#Check for existing content in database
#to be adopted TODO armin 
function validate ($contactInformation_column) {
    if ($contactInformation_column <> "" AND $contactInformation_column <> NULL) {
             $contactinformationcheck = true;
    }
    else {
		$contactinformationcheck = false;
	}
	return $contactinformationcheck;
}
	
#Creating the "WMT_MS_Capabilities" node
$wmt_ms_capabilities = $doc->createElement("WMT_MS_Capabilities");
$wmt_ms_capabilities->setAttribute("updateSequence", $wms_row["wms_timestamp"]);
if ($inspire){
	$wmt_ms_capabilities->setAttribute("xmlns:inspire_common", "http://inspire.ec.europa.eu/schemas/inspire/1.0");
	$wmt_ms_capabilities->setAttribute("xmlns:inspire_vs", "http://inspire.ec.europa.eu/schemas/inspire_vs/1.0");
}
$wmt_ms_capabilities = $doc->appendChild($wmt_ms_capabilities);
$wmt_ms_capabilities->setAttribute('version', '1.1.1');

#Creatig the "Service" node 
$service = $doc->createElement("Service");
$service = $wmt_ms_capabilities->appendChild($service);

#Creating the "Name" Node
$name = $doc->createElement("Name");
$name = $service->appendChild($name);
$nameText = $doc->createTextNode("OGC:WMS");
$nameText = $name->appendChild($nameText);

#Creating the "Title" node
if($wms_row['wms_title'] <> "" AND $wms_row['wms_title'] <> NULL) {
    $title = $doc->createElement("Title");
	$title = $service->appendChild($title);
	$titleText = $doc->createTextNode($wms_row['wms_title']);
	$titleText = $title->appendChild($titleText);
}

#Creating the "Abstract" node
if($wms_row['wms_abstract'] <> "" AND $wms_row['wms_abstract'] <> NULL) {
	$abstract = $doc->createElement("Abstract");
	$abstract = $service->appendChild($abstract);
	$abstractText = $doc->createTextNode($wms_row['wms_abstract']);
	$abstractText = $abstract->appendChild($abstractText);
}
	
# switch URLs for OWSPROXY
if($wms_row['wms_owsproxy'] <> "" AND $wms_row['wms_owsproxy'] <> NULL) {
	$tmpOR = $urlPrefix.$_SERVER["HTTP_HOST"]."/owsproxy/".session_id()."/".$wms_row["wms_owsproxy"]."?";
	$tmpOR = str_replace(SERVERIP, SERVERNAME, $tmpOR);
	$wms_row['wms_getcapabilities'] = $tmpOR;
	$wms_row['wms_getmap'] = $tmpOR;
	$wms_row['wms_getfeatureinfo'] = $tmpOR;

}
#Creating the "OnlineResource" node
//if($wms_row['wms_getcapabilities'] <> "" AND $wms_row['wms_getcapabilities'] <> NULL) {
    $onlineResource = $doc->createElement("OnlineResource");
	$onlineResource = $service->appendChild($onlineResource);
	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
//	$onlineResource->setAttribute("xlink:href", $wms_row['wms_getcapabilities']);
	$onlRes = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] . "?layer_id=" . $layerId."&".session_name()."=".session_id();
	if (isset($_SERVER["HTTPS"])) {
		$onlRes = "https://" . $onlRes;
	}
	else {
		$onlRes = "http://" . $onlRes;
	}
	$onlineResource->setAttribute("xlink:href", $onlRes);
	$onlineResource->setAttribute("xlink:type", "simple");
//}

#Insert contact information

#Creating "Contact Information" node
if (validate($wms_row['contactperson']) &&
	validate($wms_row['contactorganization']) &&
	validate($wms_row['contactposition']) && 
	validate($wms_row['address']) && 
	validate($wms_row['city']) && 
	validate($wms_row['stateorprovince']) && 
	validate($wms_row['postcode']) && //AND validate($wms_row['country']) &&
	validate($wms_row['contactvoicetelephone']) && 
	validate($wms_row['contactfacsimiletelephone']) &&
	validate($wms_row['contactelectronicmailaddress']))
{
$contactInformation = $doc->createElement("ContactInformation");
$contactInformation = $service->appendChild($contactInformation);

#Creating "Contact Person Primary" node
if(validate($wms_row['contactperson']) AND validate($wms_row['contactorganization']))
{
$contactPersonPrimary = $doc->createElement("ContactPersonPrimary");
$contactPersonPrimary = $contactInformation->appendChild($contactPersonPrimary); 
}

#Creating the "ContactPerson" node
if(validate($wms_row['contactperson']))
{
    $contactPerson = $doc->createElement("ContactPerson");
    $contactPerson = $contactPersonPrimary->appendChild($contactPerson);
    $contactPersonText = $doc->createTextNode($wms_row['contactperson']);
    $contactPersonText = $contactPerson->appendChild($contactPersonText);
}

#Creating the "ContactOrganization" node
if(validate($wms_row['contactorganization']))
{
    $contactOrganization = $doc->createElement("ContactOrganization");
    $contactOrganization = $contactPersonPrimary->appendChild($contactOrganization);
    $contactOrganizationText = $doc->createTextNode($wms_row['contactorganization']);
    $contactOrganizationText = $contactOrganization->appendChild($contactOrganizationText);
}


#Creating the "ContactPosition" node
if(validate($wms_row['contactposition']))
{
    $contactPosition = $doc->createElement("ContactPosition");
    $contactPosition = $contactInformation->appendChild($contactPosition);
    $contactPositionText = $doc->createTextNode($wms_row['contactposition']);
    $contactPositionText = $contactPosition->appendChild($contactPositionText);    
}

#Creating "ContactAddress" node
if(validate($wms_row['address']) AND validate($wms_row['city']) AND validate($wms_row['stateorprovince']) AND               validate($wms_row['postcode']) /*AND validate($wms_row['country'])*/)
{
$contactAddress = $doc->createElement("ContactAddress");
$contactAddress = $contactInformation->appendChild($contactAddress); 
}

#Creating the "AddressType" and "Address" textnode
if(validate($wms_row['address']))
{
	
    $addressType = $doc->createElement("AddressType");
    $addressType = $contactAddress->appendChild($addressType);
    $addresstypeText = $doc->createTextNode("postal");
    $addresstypeText = $addressType->appendChild($addresstypeText);
    
    $address = $doc->createElement("Address");
    $address = $contactAddress->appendChild($address);
    $addressText = $doc->createTextNode($wms_row['address']);
    $addressText = $address->appendChild($addressText);
}

#Creatig the "City" node  
if(validate($wms_row['city']))
{
    $city = $doc->createElement("City");
    $city = $contactAddress->appendChild($city);
    $cityText = $doc->createTextNode($wms_row['city']);
    $cityText = $city->appendChild($cityText);
}

#Creatig the "StateOrProvince" node    
if(validate($wms_row['stateorprovince']))
{
    $stateOrProvince = $doc->createElement("StateOrProvince");
    $stateOrProvince = $contactAddress->appendChild($stateOrProvince);
    $stateOrProvinceText = $doc->createTextNode($wms_row['stateorprovince']);
    $stateOrProvinceText = $stateOrProvince->appendChild($stateOrProvinceText);
}

#Creatig the "PostCode" node    
if(validate($wms_row['postcode']))
{
    $postCode = $doc->createElement("PostCode");
    $postCode = $contactAddress->appendChild($postCode);
    $postCodeText = $doc->createTextNode($wms_row['postcode']);
    $postCodeText = $postCode->appendChild($postCodeText);
}

 
#Creatig the "Country" node   
if(isset($wms_row['country']) AND validate($wms_row['country']))
{
    $country = $doc->createElement("Country");
    $country = $contactAddress->appendChild($country);
    $countryText = $doc->createTextNode($wms_row['country']);
    $countryText = $country->appendChild($countryText);
}

#Creatig the "ContactVoiceTelephone" node
if(validate($wms_row['contactvoicetelephone']))
{
    $contactVoiceTelephone = $doc->createElement("ContactVoiceTelephone");
    $contactVoiceTelephone = $contactInformation->appendChild($contactVoiceTelephone);
    $contactVoiceTelephoneText = $doc->createTextNode($wms_row['contactvoicetelephone']);
    $contactVoiceTelephoneText = $contactVoiceTelephone->appendChild($contactVoiceTelephoneText);
}

#Creatig the "ContactFacsimileTelephone" node
if(validate($wms_row['contactfacsimiletelephone']))
{
    $contactFacsimileTelephone = $doc->createElement("ContactFacsimileTelephone");
    $contactFacsimileTelephone = $contactInformation->appendChild($contactFacsimileTelephone);
    $contactFacsimileTelephoneText = $doc->createTextNode($wms_row['contactfacsimiletelephone']);
    $contactFacsimileTelephoneText = $contactFacsimileTelephone->appendChild($contactFacsimileTelephoneText);
}

#Creatig the "ContactElectronicMailAddress" node
if(validate($wms_row['contactelectronicmailaddress']))
{
    $contactElectronicMailAddress = $doc->createElement("ContactElectronicMailAddress");
    $contactElectronicMailAddress = $contactInformation->appendChild($contactElectronicMailAddress);
    $contactElectronicMailAddressText = $doc->createTextNode($wms_row['contactelectronicmailaddress']);
    $contactElectronicMailAddressText = $contactElectronicMailAddress->appendChild($contactElectronicMailAddressText);
}
}

#Creatig the "Fees" node
if(validate($wms_row['fees']))
{
    $fees = $doc->createElement("Fees");
    $fees = $service->appendChild($fees);
    $feesText = $doc->createTextNode($wms_row['fees']);
    $feesText = $fees->appendChild($feesText);
}
   
#Creating the "AccessConstraints" node
if(validate($wms_row['accessconstraints']))
{
	$accessConstraints = $doc->createElement("AccessConstraints");
    $accessConstraints = $service->appendChild($accessConstraints);
    $accessConstraintsText = $doc->createTextNode($wms_row['accessconstraints']);
    $accessConstraintsText = $accessConstraints->appendChild($accessConstraintsText);
}

 
#Creating the "Capability" node 
$capability = $doc->createElement("Capability");
$capability = $wmt_ms_capabilities->appendChild($capability);

#Creatig the "Request" node 
$request = $doc->createElement("Request");
$request = $capability->appendChild($request);

############################################################
#GetCapabilities
#Creatig the "GetCapabilities" node 
$getCapabilities = $doc->createElement("GetCapabilities");
$getCapabilities = $request->appendChild($getCapabilities);

#Creatig the "Format" node 
$wms_format_sql ="SELECT data_format FROM wms_format WHERE fkey_wms_id = $1 AND data_type = 'capability'";
$v = array($wms_row['wms_id']);
$t = array("i");
$res_wms_format_sql = db_prep_query($wms_format_sql, $v, $t);
while ($wms_format_row = db_fetch_array($res_wms_format_sql)) {
    $format = $doc->createElement("Format");
    $format = $getCapabilities->appendChild($format);
    $formatText = $doc->createTextNode($wms_format_row['data_format']);
    $formatText = $format->appendChild($formatText);    
}
#cause the format for capabilities is not read :
    $format = $doc->createElement("Format");
    $format = $getCapabilities->appendChild($format);
    $formatText = $doc->createTextNode('application/vnd.ogc.wms_xml');
    $formatText = $format->appendChild($formatText); 



#Creating the "DCPType" node
$DCPType = $doc->createElement("DCPType");
$DCPType = $getCapabilities->appendChild($DCPType);

#Creating the "HTTP" node
$HTTP = $doc->createElement("HTTP");
$HTTP = $DCPType->appendChild($HTTP);

#Creating the "Get" node
$get = $doc->createElement("Get");
$get = $HTTP->appendChild($get);

#Creating the "OnlineResource" node

//if ($wms_row['wms_getcapabilities'] <> "" AND $wms_row['wms_getcapabilities'] <> NULL) {
	$onlineResource = $doc->createElement("OnlineResource");
	$onlineResource = $get->appendChild($onlineResource);
	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
//	$onlineResource->setAttribute("xlink:href", $wms_row['wms_getcapabilities']);
	$onlRes = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] . "?layer_id=" . $layerId;
	if (isset($_SERVER["HTTPS"])) {
		$onlRes = "https://" . $onlRes;
	}
	else {
		$onlRes = "http://" . $onlRes;
	}
	$onlineResource->setAttribute("xlink:href", $onlRes);
	$onlineResource->setAttribute("xlink:type", "simple");		
//}

#Creating the "Post" node
$post = $doc->createElement("Post");
$post = $HTTP->appendChild($post);

#Creating the "OnlineResource" node
//if ($wms_row['wms_getcapabilities'] <> "" AND $wms_row['wms_getcapabilities'] <> NULL) {
	$onlineResource = $doc->createElement("OnlineResource");
	$onlineResource = $post->appendChild($onlineResource);
	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
//	$onlineResource->setAttribute("xlink:href", $wms_row['wms_getcapabilities']);
	$onlRes = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] . "?layer_id=" . $layerId;
	if (isset($_SERVER["HTTPS"])) {
		$onlRes = "https://" . $onlRes;
	}
	else {
		$onlRes = "http://" . $onlRes;
	}
	$onlineResource->setAttribute("xlink:href", $onlRes);
	$onlineResource->setAttribute("xlink:type", "simple");
//}

##########################################################
#GetMap	
#Creatig the "GetMap" node 
$getMap = $doc->createElement("GetMap");
$getMap = $request->appendChild($getMap);

#Creatig the "Format" node 
$wms_format_sql ="SELECT data_format FROM wms_format WHERE fkey_wms_id = $1 AND data_type = 'map'";
$v = array($wms_row['wms_id']);
$t = array("i");
$res_wms_format_sql = db_prep_query($wms_format_sql, $v, $t);

while ($wms_format_row = db_fetch_array($res_wms_format_sql)) {
    $format = $doc->createElement("Format");
    $format = $getMap->appendChild($format);
    $formatText = $doc->createTextNode($wms_format_row['data_format']);
    $formatText = $format->appendChild($formatText);	
}

#Creating the "DCPType" node
$DCPType = $doc->createElement("DCPType");
$DCPType = $getMap->appendChild($DCPType);

#Creating the "HTTP" node
$HTTP = $doc->createElement("HTTP");
$HTTP = $DCPType->appendChild($HTTP);

#Creating the "Get" node
$get = $doc->createElement("Get");
$get = $HTTP->appendChild($get);

#Creating the "OnlineResource" node
if ($wms_row['wms_getmap'] <> "" AND $wms_row['wms_getmap'] <> NULL) {
	$onlineResource = $doc->createElement("OnlineResource");
	$onlineResource = $get->appendChild($onlineResource);
	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
	$onlineResource->setAttribute("xlink:href", $wms_row['wms_getmap']);
	$onlineResource->setAttribute("xlink:type", "simple");
}

#Creating the "Post" node
$post = $doc->createElement("Post");
$post = $HTTP->appendChild($post);

#Creating the "OnlineResource" node
if($wms_row['wms_getmap'] <> "" AND $wms_row['wms_getmap'] <> NULL) {
	$onlineResource = $doc->createElement("OnlineResource");
	$onlineResource = $post->appendChild($onlineResource);
	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
	$onlineResource->setAttribute("xlink:href", $wms_row['wms_getmap']);
	$onlineResource->setAttribute("xlink:type", "simple");
}

##########################################################
#GetFeatureInfo	
#Creatig the "GetFeatureInfo" node 
$getFeatureInfo = $doc->createElement("GetFeatureInfo");
$getFeatureInfo = $request->appendChild($getFeatureInfo);

#Creatig the "Format" node 
$wms_format_sql ="SELECT data_format FROM wms_format WHERE fkey_wms_id = $1 AND data_type = 'featureinfo'";
$v = array($wms_row['wms_id']);
$t = array("i");
$res_wms_format_sql = db_prep_query($wms_format_sql, $v, $t);
while ($wms_format_row = db_fetch_array($res_wms_format_sql))
{
    $format = $doc->createElement("Format");
    $format = $getFeatureInfo->appendChild($format);
    $formatText = $doc->createTextNode($wms_format_row['data_format']);
    $formatText = $format->appendChild($formatText);    
}
	
#Creating the "DCPType" node
$DCPType = $doc->createElement("DCPType");
$DCPType = $getFeatureInfo->appendChild($DCPType);

#Creating the "HTTP" node
$HTTP = $doc->createElement("HTTP");
$HTTP = $DCPType->appendChild($HTTP);

#Creating the "Get" node
$get = $doc->createElement("Get");
$get = $HTTP->appendChild($get);

#Creating the "OnlineResource" node
if($wms_row['wms_getfeatureinfo'] <> "" AND $wms_row['wms_getfeatureinfo'] <> NULL)
{
	$onlineResource = $doc->createElement("OnlineResource");
	$onlineResource = $get->appendChild($onlineResource);
	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
	$onlineResource->setAttribute("xlink:href", $wms_row['wms_getfeatureinfo']);
	$onlineResource->setAttribute("xlink:type", "simple");
}
#Creating the "Post" node
$post = $doc->createElement("Post");
$post = $HTTP->appendChild($post);

#Creating the "OnlineResource" node

if($wms_row['wms_getfeatureinfo'] <> "" AND $wms_row['wms_getfeatureinfo'] <> NULL) {
	$onlineResource = $doc->createElement("OnlineResource");
	$onlineResource = $post->appendChild($onlineResource);
	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
	$onlineResource->setAttribute("xlink:href", $wms_row['wms_getfeatureinfo']);
	$onlineResource->setAttribute("xlink:type", "simple");
}
	
#Creating the "Exeption" node
$exception = $doc->createElement("Exception");
$exception = $capability->appendChild($exception);	

#Creatig the "Format" node 
$wms_format_sql ="SELECT data_format FROM wms_format WHERE fkey_wms_id = $1 AND data_type = 'exception'";
$v = array($wms_row['wms_id']);
$t = array("i");
$res_wms_format_sql = db_prep_query($wms_format_sql, $v, $t);
while ($wms_format_row = db_fetch_array($res_wms_format_sql)) {
    $format = $doc->createElement("Format");
    $format = $exception->appendChild($format);
    $formatText = $doc->createTextNode($wms_format_row['data_format']);
    $formatText = $format->appendChild($formatText); 
} 
################################################################
#INSPIRE
if ($inspire) {
	#generating the vendor specific node
	$vendorSpecificCapabilities = $doc->createElement("VendorSpecificCapabilities");
	$vendorSpecificCapabilities = $capability->appendChild($vendorSpecificCapabilities);
	#generate inspire_vs:ExtendedCapabilities node
	$inspire_vs_ExtendedCapabilities = $doc->createElement("inspire_vs:ExtendedCapabilities");
	$inspire_vs_ExtendedCapabilities->setAttribute("xmlns:inspire_common", "http://inspire.ec.europa.eu/schemas/common/1.0");
	$inspire_vs_ExtendedCapabilities = $vendorSpecificCapabilities->appendChild($inspire_vs_ExtendedCapabilities);
	#generate inspire_vs: node
	#$inspire_vs_ExtendedCapabilities = $doc->createElement("inspire_vs:ExtendedCapabilities");
	#$inspire_vs_ExtendedCapabilities = $vendorSpecificCapabilities->appendChild($inspire_vs_ExtendedCapabilities);
	#MetadataUrl to inspire service metadata
	$inspire_common_MetadataUrl = $doc->createElement("inspire_common:MetadataUrl");
	$inspire_common_MetadataUrl = $inspire_vs_ExtendedCapabilities->appendChild($inspire_common_MetadataUrl);
	#URL
	$inspire_common_URL = $doc->createElement("inspire_common:URL");
	$inspire_common_URLText = $doc->createTextNode($inspireServiceMetadataUrl.$layerId);
	$inspire_common_URL->appendChild($inspire_common_URLText);
	$inspire_common_URL = $inspire_common_MetadataUrl->appendChild($inspire_common_URL);
	#MediaType
	$inspire_common_MediaType = $doc->createElement("inspire_common:MediaType");
	$inspire_common_MediaTypeText = $doc->createTextNode('application/vnd.iso.19139+xml');#from http://inspire.ec.europa.eu/schemas/inspire_vs/1.0/examples/WMS_Image2000GetCapabilities_InspireSchema.xml
	$inspire_common_MediaType->appendChild($inspire_common_MediaTypeText);
	$inspire_common_MediaType = $inspire_common_MetadataUrl->appendChild($inspire_common_MediaType);
	#Language Part
	#SupportedLanguages
	$inspire_common_SupportedLanguages = $doc->createElement("inspire_common:SupportedLanguages");
	$inspire_common_SupportedLanguages = $inspire_vs_ExtendedCapabilities->appendChild($inspire_common_SupportedLanguages);
	#DefaultLanguage
	$inspire_common_DefaultLanguage = $doc->createElement("inspire_common:DefaultLanguage");
	$inspire_common_DefaultLanguage = $inspire_common_SupportedLanguages->appendChild($inspire_common_DefaultLanguage);
	#Language
	$inspire_common_Language = $doc->createElement("inspire_common:Language");
	$inspire_common_LanguageText = $doc->createTextNode('ger');
	$inspire_common_Language->appendChild($inspire_common_LanguageText);
	$inspire_common_Language = $inspire_common_DefaultLanguage->appendChild($inspire_common_Language);
	#SupportedLanguage
	$inspire_common_SupportedLanguage = $doc->createElement("inspire_common:SupportedLanguage");
	$inspire_common_SupportedLanguage = $inspire_common_SupportedLanguages->appendChild($inspire_common_SupportedLanguage);
	#Language
	$inspire_common_Language = $doc->createElement("inspire_common:Language");
	$inspire_common_LanguageText = $doc->createTextNode('ger');
	$inspire_common_Language->appendChild($inspire_common_LanguageText);
	$inspire_common_Language = $inspire_common_SupportedLanguage->appendChild($inspire_common_Language);
	#ResponseLanguage
	$inspire_common_ResponseLanguage = $doc->createElement("inspire_common:ResponseLanguage");
	$inspire_common_ResponseLanguage = $inspire_vs_ExtendedCapabilities->appendChild($inspire_common_ResponseLanguage);
	#Language
	$inspire_common_Language = $doc->createElement("inspire_common:Language");
	$inspire_common_LanguageText = $doc->createTextNode('ger');
	$inspire_common_Language->appendChild($inspire_common_LanguageText);
	$inspire_common_Language = $inspire_common_ResponseLanguage->appendChild($inspire_common_Language);
}
################################################################
#Querying layer table
$layer_sql = "SELECT * FROM layer WHERE layer.fkey_wms_id = $1 AND layer.layer_parent = ''";
$v = array($wms_row['wms_id']);
$t = array("i");

$res_layer_sql = db_prep_query($layer_sql, $v, $t);
$layer_row = db_fetch_array($res_layer_sql);
			
#Creating layer node
$layer = $doc->createElement("Layer");
$layer = $capability->appendChild($layer);

#Write layer to parent layer array
$parentLayerArray[$layer_row['layer_pos']] = $layer;
		
#Creating Name node
if ($layer_row['layer_name'] <> "" AND $layer_row['layer_name'] <> NULL) {
	$name = $doc->createElement("Name");
	$name = $layer->appendChild($name);
	$nameText = $doc->createTextNode($layer_row['layer_name']);
	$nameText = $name->appendChild($nameText);
}

#Creating Title node
if ($layer_row['layer_title'] <> "" AND $layer_row['layer_title'] <> NULL) {
	$title = $doc->createElement("Title");
	$title = $layer->appendChild($title);
	$titleText = $doc->createTextNode($layer_row['layer_title']);
	$titleText = $title->appendChild($titleText);
}

#Creating the "Abstract" node
if($layer_row['layer_abstract'] <> "" AND $layer_row['layer_abstract'] <> NULL) {
    $abstract = $doc->createElement("Abstract");
    $abstract = $layer->appendChild($abstract);
    $abstractText = $doc->createTextNode($layer_row['layer_abstract']);
    $abstractText = $abstract->appendChild($abstractText);	
}

#Request the specific wms- and layerkeywords

$keyword_sql = "SELECT DISTINCT keyword FROM keyword, layer_keyword, layer " . 
	"WHERE keyword.keyword_id = layer_keyword.fkey_keyword_id " . 
	"AND layer_keyword.fkey_layer_id = layer.layer_id " . 
	"AND layer.fkey_wms_id = $1";
$v = array($wms_row['wms_id']);
$t = array("i");
$res_keyword_sql = db_prep_query($keyword_sql, $v, $t);

#Creating list of keyword nodes
#Iterating over a List of Keywords
$keywordlistExist = 0;

while ($keyword_sql = db_fetch_array($res_keyword_sql))
{
    #Creating the "KeywordList" node
    if ($keywordlistExist == 0) {
        $keywordList = $doc->createElement("KeywordList");
        $keywordList = $layer->appendChild($keywordList);
		$keywordlistExist = 1;	
    }
    
    #Creating the "Keyword" node
    $keyword_dom = $doc->createElement("Keyword");
    $keyword_dom = $keywordList->appendChild($keyword_dom); 
    $keyword_domText = $doc->createTextNode($keyword_sql['keyword']);
    $keyword_domText = $keyword_dom->appendChild($keyword_domText);
}



#SQL statement to get additional layer information from layer epsg	
$epsg_sql = "SELECT layer_epsg.epsg, layer_epsg.minx, layer_epsg.miny, " . 
	"layer_epsg.maxy, layer_epsg.maxx " . 
	"FROM layer_epsg WHERE layer_epsg.fkey_layer_id = $1";
	
$v = array($layer_row['layer_id']);
$t = array("i");
$res_espg_sql = db_prep_query($epsg_sql, $v, $t);

$latLonBoundingBoxCreated = false;
$BoundingBoxCreated = false;

while ($epsg_row = db_fetch_array($res_espg_sql)) {

	#Creating SRS node
	$srs = $doc->createElement("SRS");
	$srs = $layer->appendChild($srs);
	$srsText = $doc->createTextNode($epsg_row['epsg']);
	$srsText = $srs->appendChild($srsText);
	

}

#SQL statement to get additional layer information from layer epsg	
$epsg_sql = "SELECT layer_epsg.epsg, layer_epsg.minx, layer_epsg.miny, " . 
	"layer_epsg.maxy, layer_epsg.maxx " . 
	"FROM layer_epsg WHERE layer_epsg.fkey_layer_id = $1";
	
$v = array($layer_row['layer_id']);
$t = array("i");
$res_espg_sql = db_prep_query($epsg_sql, $v, $t);

while ($epsg_row = db_fetch_array($res_espg_sql)) {
	#set only epsg 4326 for latlonbbox
	if ($epsg_row['epsg'] == "EPSG:4326") {
		
		$latlon['minx'] = $epsg_row['minx'];
		$latlon['miny'] = $epsg_row['miny'];
		$latlon['maxx'] = $epsg_row['maxx'];
		$latlon['maxy'] = $epsg_row['maxy'];

		#Creating LatLongBoundingBox node
		$latLonBoundingBox = $doc->createElement("LatLonBoundingBox");
		$latLonBoundingBox = $layer->appendChild($latLonBoundingBox);
		$latLonBoundingBox->setAttribute('minx', $latlon['minx']);
		$latLonBoundingBox->setAttribute('miny', $latlon['miny']);
		$latLonBoundingBox->setAttribute('maxx', $latlon['maxx']);
		$latLonBoundingBox->setAttribute('maxy', $latlon['maxy']);
	    break;
    }	
}

#SQL statement to get additional layer information from layer epsg	
$epsg_sql = "SELECT layer_epsg.epsg, layer_epsg.minx, layer_epsg.miny, " . 
	"layer_epsg.maxy, layer_epsg.maxx " . 
	"FROM layer_epsg WHERE layer_epsg.fkey_layer_id = $1";
	
$v = array($layer_row['layer_id']);
$t = array("i");
$res_espg_sql = db_prep_query($epsg_sql, $v, $t);

while ($epsg_row = db_fetch_array($res_espg_sql)) {
	#set only first epsg for bbox
	$bbox['epsg'] = $epsg_row['epsg'];
	$bbox['minx'] = $epsg_row['minx'];
	$bbox['miny'] = $epsg_row['miny'];
	$bbox['maxx'] = $epsg_row['maxx'];
	$bbox['maxy'] = $epsg_row['maxy'];

	#Creating BoundingBox node
	$boundingBox = $doc->createElement("BoundingBox");
	$boundingBox = $layer->appendChild($boundingBox);
	$boundingBox->setAttribute('SRS', $bbox['epsg']);
	$boundingBox->setAttribute('minx', $bbox['minx']);
	$boundingBox->setAttribute('miny', $bbox['miny']);
	$boundingBox->setAttribute('maxx', $bbox['maxx']);
	$boundingBox->setAttribute('maxy', $bbox['maxy']);
}


#Append epsg string to srs node
$srsText = $doc->createTextNode($epsgText);
$srsText = $srs->appendChild($srsText);


####### duplicate root layer 
#if layer is root layer itself!
#<armin>
##if ($layer_row['layer_pos']=='0'){
#</armin>
##$clonedLayer = $layer->cloneNode(true);
##$clonedLayer->setAttribute("queryable", "0");
##$clonedLayer->setAttribute("cascaded", "0");
##$layer->appendChild($clonedLayer);
#<armin>
##}
#</armin>



############## sublayer 
	
#SQL statement to get all layers
$sub_layer_sql = "SELECT * FROM layer WHERE fkey_wms_id = $1 AND layer_parent <> ''";
$v = array($wms_row['wms_id']);
$t = array("i");

if (isset($layerId) && $layerId > 0) {	
	$sub_layer_sql .= " AND layer_id = $2";
	array_push($v, $layerId);
	array_push($t, "i");
}
$sub_layer_sql .= " ORDER BY layer_pos";
$res_sub_layer_sql = db_prep_query($sub_layer_sql, $v, $t);


#<armin>
$res_sub_layer_sql_2 = db_prep_query($sub_layer_sql, $v, $t);   
$sub_layer_row_2 = db_fetch_array($res_sub_layer_sql_2);


if (!isset($sub_layer_row_2['layer_pos'])) {
	$clonedLayer = $layer->cloneNode(true);
	$clonedLayer->setAttribute("queryable", "0");
	$clonedLayer->setAttribute("cascaded", "0");
	$layer->appendChild($clonedLayer);
}
#</armin>




while ($sub_layer_row = db_fetch_array($res_sub_layer_sql)) {
	
	#Creating layer node

	$sub_layer = $doc->createElement("Layer");
	$e = new mb_exception("wms.php: layer_parent:".$sub_layer_row['layer_parent']);
	$parent = $parentLayerArray[$sub_layer_row['layer_parent']];
	
	$sub_layer = $parent->appendChild($sub_layer);
    
    if($sub_layer_row['layer_queryable'] <> "" AND $sub_layer_row['layer_queryable'] <> NULL) {
		$sub_layer->setAttribute('queryable', $sub_layer_row['layer_queryable']);
    }
	#Getting information about the hierarchie of layers
	$cascadeSQL = "SELECT COUNT(*) FROM layer WHERE fkey_wms_id = $1 AND layer_parent = $2";
	
	$v = array($wms_row[0], $sub_layer_row['layer_pos']);
	$t = array("i", "i");
	$res_cascadeSQL = db_prep_query($cascadeSQL, $v, $t);
	$cascade = 0;
	$cascade_row = db_fetch_row($res_cascadeSQL);
	if($cascade_row[0] > 0)
	{
		$cascade = 1;
	}
	$sub_layer->setAttribute('cascaded', $cascade);
	
	#Write layer to parent layer array
	$parentLayerArray[$sub_layer_row['layer_pos']] = $sub_layer;
	
	#Creating name node
    if($sub_layer_row['layer_name'] <> "" AND $sub_layer_row['layer_name'] <> NULL)
    {
		$name = $doc->createElement("Name");
		$name = $sub_layer->appendChild($name);
		$nameText = $doc->createTextNode($sub_layer_row['layer_name']);
		$nameText = $name->appendChild($nameText);
    }
	
	#Creating Title node
    if($sub_layer_row['layer_title'] <> "" AND $sub_layer_row['layer_title'] <> NULL)
    {
		$title = $doc->createElement("Title");
		$title = $sub_layer->appendChild($title);
		$titleText = $doc->createTextNode($sub_layer_row['layer_title']);
		$titleText = $title->appendChild($titleText);
    }
	
		#Creating the "Abstract" node
    if($sub_layer_row['layer_abstract'] <> "" AND $sub_layer_row['layer_abstract'] <> NULL)
    {
    	$abstract = $doc->createElement("Abstract");
    	$abstract = $sub_layer->appendChild($abstract);
    	$abstractText = $doc->createTextNode($sub_layer_row['layer_abstract']);
    	$abstractText = $abstract->appendChild($abstractText);
    }
	
    #Request the specific wms- and layerkeywords
    $keyword_sql = "SELECT DISTINCT keyword FROM layer LEFT JOIN layer_keyword ON layer_keyword.fkey_layer_id = layer.layer_id LEFT JOIN keyword ON  keyword.keyword_id = layer_keyword.fkey_keyword_id WHERE layer.fkey_wms_id = ".$wms_row['wms_id']." AND layer.layer_id = ".$sub_layer_row['layer_id']."";
    $res_keyword_sql = db_query($keyword_sql);
    
    #Creating list of keyword nodes
    #Iterating over a List of Keywords
    $keywordlistExist = 0;
    while ($keyword_sql = db_fetch_array($res_keyword_sql))
    {
        #Creating the "KeywordList" node
        if ($keywordlistExist == 0)
        {
            $keywordList = $doc->createElement("KeywordList");
            $keywordList = $sub_layer->appendChild($keywordList);
		    $keywordlistExist = 1;			
        }
        
        #Creating the "Keyword" node
        $keyword_dom = $doc->createElement("Keyword");
        $keyword_dom = $keywordList->appendChild($keyword_dom); 
        $keyword_domText = $doc->createTextNode($keyword_sql['keyword']);
        $keyword_domText = $keyword_dom->appendChild($keyword_domText);
    }

	// inherit srs from parent layer
	$layer_srs_sql = "SELECT DISTINCT epsg FROM layer_epsg " . 
			"WHERE fkey_layer_id = ".$sub_layer_row['layer_id'] . 
			" OR fkey_layer_id = " . $layer_row['layer_id'];
	$res_layer_srs_sql = db_query($layer_srs_sql);

	while ($layer_srs_row = db_fetch_array($res_layer_srs_sql)) {
		#Creating SRS node
		$srs = $doc->createElement("SRS");
		$srs = $sub_layer->appendChild($srs);
		$srsText = $doc->createTextNode($layer_srs_row['epsg']);
		$srsText = $srs->appendChild($srsText);

	}
	#SQL statement to get additional layer information from layer epsg	
	$epsg_sql = "SELECT layer_epsg.epsg, layer_epsg.minx, layer_epsg.miny, " . 
			"layer_epsg.maxy, layer_epsg.maxx FROM layer_epsg " . 
			"WHERE layer_epsg.fkey_layer_id = ".$sub_layer_row['layer_id'];
	$res_espg_sql = db_query($epsg_sql);
	
	while ($epsg_row = db_fetch_array($res_espg_sql)) {
		#set epsg 4326 for latlonbbox
		if ($epsg_row['epsg'] == "EPSG:4326" AND $latLonBoundingBoxCreated == false) {
			
			$latlon['minx'] = $epsg_row['minx'];
			$latlon['miny'] = $epsg_row['miny'];
			$latlon['maxx'] = $epsg_row['maxx'];
			$latlon['maxy'] = $epsg_row['maxy'];

			#Creating LatLongBoundingBox node
		    $latLonBoundingBox = $doc->createElement("LatLonBoundingBox");
		    $latLonBoundingBox = $sub_layer->appendChild($latLonBoundingBox);
		    $latLonBoundingBox->setAttribute('minx', $latlon['minx']);
		    $latLonBoundingBox->setAttribute('miny', $latlon['miny']);
		    $latLonBoundingBox->setAttribute('maxx', $latlon['maxx']);
		    $latLonBoundingBox->setAttribute('maxy', $latlon['maxy']);
	    }	
	}
	
	#SQL statement to get additional layer information from layer epsg	
	$epsg_sql = "SELECT layer_epsg.epsg, layer_epsg.minx, layer_epsg.miny, " . 
			"layer_epsg.maxy, layer_epsg.maxx FROM layer_epsg " . 
			"WHERE layer_epsg.fkey_layer_id = ".$sub_layer_row['layer_id'];
	$res_espg_sql = db_query($epsg_sql);
	
	while ($epsg_row = db_fetch_array($res_espg_sql)) {
	
		
		#set only first epsg for bbox
		$bbox['epsg'] = $epsg_row['epsg'];
		$bbox['minx'] = $epsg_row['minx'];
		$bbox['miny'] = $epsg_row['miny'];
		$bbox['maxx'] = $epsg_row['maxx'];
		$bbox['maxy'] = $epsg_row['maxy'];

		#Creating BoundingBox node
		$boundingBox = $doc->createElement("BoundingBox");
		$boundingBox = $sub_layer->appendChild($boundingBox);
		$boundingBox->setAttribute('SRS', $bbox['epsg']);
		$boundingBox->setAttribute('minx', $bbox['minx']);
		$boundingBox->setAttribute('miny', $bbox['miny']);
		$boundingBox->setAttribute('maxx', $bbox['maxx']);
		$boundingBox->setAttribute('maxy', $bbox['maxy']);
	}
	
	# Creating Metadata Node
	//read out all metadata entries for specific layer


	$subLayerId = $sub_layer_row['layer_id'];
	$sql = <<<SQL

SELECT metadata_id, uuid, link, linktype, md_format, origin FROM mb_metadata 
INNER JOIN (SELECT * from ows_relation_metadata 
WHERE fkey_layer_id = $subLayerId ) as relation ON 
mb_metadata.metadata_id = relation.fkey_metadata_id WHERE mb_metadata.origin IN ('capabilities','external','metador','upload')

SQL;
	$e = new mb_notice("layerid: ".$sub_layer_row['layer_id']);
	$i = 0;
	$res_metadata = db_query($sql);

	$e = new mb_notice("row size: ".count($row_metadata));
	while ($row_metadata = db_fetch_array($res_metadata)) {
		$e = new mb_exception("i: ".$i);
		//push entries into xml structure	
		//check for kind of link - push the right one into the link field	
		switch ($row_metadata['origin']) {
			case 'capabilities':
				$metadataUrl = $doc->createElement("MetadataURL");
				$metadataUrl = $sub_layer->appendChild($metadataUrl);
				$metadataUrl->setAttribute('type', $row_metadata['linktype']);
				$format = $doc->createElement("Format");
    				$format = $metadataUrl->appendChild($format);
    				$formatText = $doc->createTextNode($row_metadata['md_format']);
    				$formatText = $format->appendChild($formatText);
				$onlineResource = $doc->createElement("OnlineResource");
	    			$onlineResource = $metadataUrl->appendChild($onlineResource);
				$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
				$onlineResource->setAttribute("xlink:href", $row_metadata['link']);
			break;
			case 'external':
				$metadataUrl = $doc->createElement("MetadataURL");
				$metadataUrl = $sub_layer->appendChild($metadataUrl);
				$metadataUrl->setAttribute('type', 'ISO19115:2003');
				$format = $doc->createElement("Format");
    				$format = $metadataUrl->appendChild($format);
    				$formatText = $doc->createTextNode("text/xml");
    				$formatText = $format->appendChild($formatText);
				$onlineResource = $doc->createElement("OnlineResource");
	    			$onlineResource = $metadataUrl->appendChild($onlineResource);
				$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
				$onlineResource->setAttribute("xlink:href", $row_metadata['link']);
				$onlineResource->setAttribute("xlink:href", $mapbenderMetadataUrlUrl.$row_metadata['uuid']);
			break;
			case 'upload':
				$metadataUrl = $doc->createElement("MetadataURL");
				$metadataUrl = $sub_layer->appendChild($metadataUrl);
				$metadataUrl->setAttribute('type', 'ISO19115:2003');
				$format = $doc->createElement("Format");
    				$format = $metadataUrl->appendChild($format);
    				$formatText = $doc->createTextNode("text/xml");
    				$formatText = $format->appendChild($formatText);
				$onlineResource = $doc->createElement("OnlineResource");
	    			$onlineResource = $metadataUrl->appendChild($onlineResource);
				$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
				$onlineResource->setAttribute("xlink:href", $row_metadata['link']);
				$onlineResource->setAttribute("xlink:href", $mapbenderMetadataUrlUrl.$row_metadata['uuid']);
			break;
			case 'metador':
				$metadataUrl = $doc->createElement("MetadataURL");
				$metadataUrl = $sub_layer->appendChild($metadataUrl);
				$metadataUrl->setAttribute('type', 'ISO19115:2003');
				$format = $doc->createElement("Format");
    				$format = $metadataUrl->appendChild($format);
    				$formatText = $doc->createTextNode("text/xml");
    				$formatText = $format->appendChild($formatText);
				$onlineResource = $doc->createElement("OnlineResource");
	    			$onlineResource = $metadataUrl->appendChild($onlineResource);
				$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
				$onlineResource->setAttribute("xlink:href", $row_metadata['link']);
				$onlineResource->setAttribute("xlink:href", $mapbenderMetadataUrlUrl.$row_metadata['uuid']);
			break;
			default:
				$metadataUrl = $doc->createElement("MetadataURL");
				$metadataUrl = $sub_layer->appendChild($metadataUrl);
				$metadataUrl->setAttribute('type', 'ISO19115:2003');
				$format = $doc->createElement("Format");
    				$format = $metadataUrl->appendChild($format);
    				$formatText = $doc->createTextNode("text/xml");
    				$formatText = $format->appendChild($formatText);
				$onlineResource = $doc->createElement("OnlineResource");
	    			$onlineResource = $metadataUrl->appendChild($onlineResource);
				$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
				$onlineResource->setAttribute("xlink:href", $row['link']);
				$onlineResource->setAttribute("xlink:href", "Url not given - please check your registry!");
			break;
		}
		$onlineResource->setAttribute("xlink:type", "simple");
		//Add linkage to Capabilities
		$i++;
		$e = new mb_exception("i: ".$i);
	}
	

	# Creating DataURL Node
	$dataUrl = $doc->createElement("DataURL");
	$dataUrl = $sub_layer->appendChild($dataUrl);
	$format = $doc->createElement("Format");
    	$format = $dataUrl->appendChild($format);
    	$formatText = $doc->createTextNode('text/html');
   	$formatText = $format->appendChild($formatText); 

	if($wms_row['wms_owsproxy'] <> "" AND $wms_row['wms_owsproxy'] <> NULL)
	{
		$onlineResource = $doc->createElement("OnlineResource");
	   	$onlineResource = $dataUrl->appendChild($onlineResource);
	   	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
		$onlineResource->setAttribute("xlink:href", OWSPROXY."/".session_id()."/".$wms_row["wms_owsproxy"]."?");
		$onlineResource->setAttribute("xlink:type", "simple");
	}
	else
	{
		if($sub_layer_row['layer_dataurl'] <> "" AND $sub_layer_row['layer_dataurl'] <> NULL)
	    {
	    	$onlineResource = $doc->createElement("OnlineResource");
	    	$onlineResource = $dataUrl->appendChild($onlineResource);
			$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
			$onlineResource->setAttribute("xlink:href", $sub_layer_row['layer_dataurl']);
			$onlineResource->setAttribute("xlink:type", "simple");
	    }
else
{
 $onlineResource = $doc->createElement("OnlineResource");
                $onlineResource = $dataUrl->appendChild($onlineResource);
                        $onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
                        $onlineResource->setAttribute("xlink:href", $mapbenderMetadataUrl.$sub_layer_row['layer_id']);
                        $onlineResource->setAttribute("xlink:type", "simple");

}







	}
	
	
	#Creating Style Node
	$style = $doc->createElement("Style");
	$style = $sub_layer->appendChild($style);
	
	$name = $doc->createElement("Name");
    $name = $style->appendChild($name);
    $nameText = $doc->createTextNode('default');
    $nameText = $name->appendChild($nameText);

	$title = $doc->createElement("Title");
    $title = $style->appendChild($title);
    $titleText = $doc->createTextNode('default');
    $titleText = $title->appendChild($titleText);
	


    if($wms_row['wms_getlegendurl'] <> "" AND $wms_row['wms_getlegendurl'] <> NULL){	
	$legendUrl = $doc->createElement("LegendURL");
	$legendUrl = $style->appendChild($legendUrl);
	$legendUrl->setAttribute("width", "10" );
	$legendUrl->setAttribute("height", "8" );

	$format = $doc->createElement("Format");
    $format = $legendUrl->appendChild($format);
    $formatText = $doc->createTextNode('image/png');
    $formatText = $format->appendChild($formatText); 

	if($wms_row['wms_owsproxy'] <> "" AND $wms_row['wms_owsproxy'] <> NULL)
	{
		$onlineResource = $doc->createElement("OnlineResource");
	   	$onlineResource = $legendUrl->appendChild($onlineResource);
	   	$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
		$onlineResource->setAttribute("xlink:href", OWSPROXY."/".session_id()."/".$wms_row["wms_owsproxy"]."?version=1.1.1&service=WMS&request=GetLegendGraphic&layer=".$sub_layer_row['layer_name']."&format=image/png");
		$onlineResource->setAttribute("xlink:type", "simple");
	}
	else
	{
		if($wms_row['wms_getlegendurl'] <> "" AND $wms_row['wms_getlegendurl'] <> NULL)
	    {
	    	$onlineResource = $doc->createElement("OnlineResource");
	    	$onlineResource = $legendUrl->appendChild($onlineResource);
			$onlineResource->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
			$onlineResource->setAttribute("xlink:href", $wms_row['wms_getlegendurl']."version=1.1.1&service=WMS&request=GetLegendGraphic&layer=".$sub_layer_row['layer_name']."&format=image/png");
			$onlineResource->setAttribute("xlink:type", "simple");
	    }
	}


}
	#Creating "ScaleHint" node
    if ($sub_layer_row['layer_minscale'] <> "" AND $sub_layer_row['layer_minscale'] <> NULL) {
		$scaleHint = $doc->createElement("ScaleHint");
		$scaleHint = $sub_layer->appendChild($scaleHint);
		$scaleHint->setAttribute('min', (floatval($sub_layer_row['layer_minscale'])/2004.3976484406788493955738891127));
		$scaleHint->setAttribute('max', (floatval($sub_layer_row['layer_maxscale'])/2004.3976484406788493955738891127));
    }
}	

header("Content-type: application/xhtml+xml; charset=UTF-8");
echo $doc->saveXml();
?>
