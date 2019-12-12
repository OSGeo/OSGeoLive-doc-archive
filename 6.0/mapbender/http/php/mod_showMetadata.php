<?php
# $Id: mod_showMetadata.php 235 2010-09-08 08:34:48Z armin11 $
# http://www.mapbender.org/index.php/Administration
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

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__)."/../classes/class_connector.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";
require_once dirname(__FILE__) . "/../classes/class_wms.php";
require_once dirname(__FILE__) . "/../classes/class_Uuid.php";
require_once dirname(__FILE__) . "/../../tools/wms_extent/extent_service.conf";
require_once dirname(__FILE__) . "/../extensions/phpqrcode/phpqrcode.php";

//GET:
//resource: wms, layer, wfs, featuretype, wfs-conf, wmc
//id: integer
//outputFormat: html, xml, georss, 
//languageCode: de, en, fr
//get language parameter out of mapbender session if it is set else set default language to de_DE
if (isset($_SESSION['mb_lang']) && ($_SESSION['mb_lang']!='')) {
	$e = new mb_notice("mod_showMetadata.php: language found in session: ".$_SESSION['mb_lang']);
	$language = $_SESSION["mb_lang"];
	$langCode = explode("_", $language);
	$langCode = $langCode[0]; # Hopefully de or s.th. else
	$languageCode = $langCode; #overwrite the GET Parameter with the SESSION information
}
$e = new mb_notice("mod_showMetadata.php: language in SESSION: ".$_SESSION['mb_lang']);
$e = new mb_notice("mod_showMetadata.php: new language: ".$languageCode);

$layout = 'tabs';
//Parse REQUEST Parameters
if (isset($_REQUEST["resource"]) & $_REQUEST["resource"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["resource"];
	if (!($testMatch == 'wms' or $testMatch == 'layer' or $testMatch == 'wfs' or $testMatch == 'featuretype' or $testMatch == 'wfs-conf'  or $testMatch == 'wmc')){ 
		echo 'resource: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$resource = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["id"]) & $_REQUEST["id"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["id"];
	$pattern = '/^[\d,]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'id: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$id = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["outputFormat"]) & $_REQUEST["outputFormat"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["outputFormat"];
	if (!($testMatch == 'iso19139' or $testMatch == 'html' or $testMatch == 'georss')){ 
		echo 'outputFormat: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$outputFormat = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["languageCode"]) & $_REQUEST["languageCode"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["languageCode"];
	if (!($testMatch == 'de' or $testMatch == 'fr' or $testMatch == 'en')){ 
		echo 'languageCode: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$languageCode = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["layout"]) & $_REQUEST["layout"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["layout"];
	if (!($testMatch == 'tabs' or $testMatch == 'accordion' or $testMatch == 'plain')){ 
		echo 'layout: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$layout = $testMatch;
	$testMatch = NULL;
}

if (isset($_REQUEST["subscribe"]) & $_REQUEST["subscribe"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["subscribe"];
	if (!($testMatch == '1' or $testMatch == '0')){ 
		echo 'layout: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$subscribe = $testMatch;
	$testMatch = NULL;
}


$subscribe = intval($subscribe);

$hostName = $_SERVER['HTTP_HOST'];

if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$mapbenderBaseUrl = "https://".$hostName;
			$mapbenderProtocol = "https://";
		}
		else {
			$mapbenderBaseUrl = "http://".$hostName;
			$mapbenderProtocol = "http://";
}


//Array with translations:
switch ($languageCode) {
	case "de":
		$translation['overview'] = 'Übersicht';
		$translation['properties'] = 'Eigenschaften';
		$translation['termsOfUse'] = 'Nutzungsbedingungen';
		$translation['contact'] = 'Kontakt';
		$translation['quality'] = 'Qualität';
		$translation['interfaces'] = 'Schnittstellen';
		$translation['metadata'] = 'Metadaten';
		$translation['kindOfResource'] = 'Art der Ressource';
		$translation['wms'] = 'Kartendienst';
		$translation['wfs'] = 'Datendienst';
		$translation['layer'] = 'Kartenebene';
		$translation['featuretype'] = 'Objektart';
		$translation['geomtype'] = 'Geometrietyp';
		$translation['contentId'] = 'Ressourcenidentifikator';
		$translation['contentName'] = 'Name der Ressource';
		$translation['serviceId'] = 'Informationen zum Dienst';
		$translation['preview'] = 'Voransicht';
		$translation['extent'] = 'Ausdehnung';
		$translation['resourceAbstract'] = 'Zusammenfassung';
		$translation['resourceTitle'] = 'Titel';
		$translation['metadataProvider'] = 'Inhaltlich verantwortliche Stelle';
		$translation['serviceProvider'] = 'Technisch verantwortliche Stelle';
		$translation['contactPerson'] = 'Ansprechpartner';
		$translation['contactOrganization'] = 'Organisation';
		$translation['contactAddress'] = 'Adresse';
		$translation['email'] = 'Email';
		$translation['city'] = 'Ort';
		$translation['logo'] = 'Logo';
		$translation['status'] = 'Status';
		$translation['availability'] = 'Verfügbarkeit';
		$translation['statusRed'] = 'Probleme beim letzten Monitoring!';
		$translation['statusGreen'] = 'Letztes Monitoring OK';
		$translation['statusYellow'] = 'Dienstebeschreibung hat sich geändert!';
		$translation['queryableFalse'] = 'Ebene nicht abfragbar';
		$translation['queryableTrue'] = 'Ebene abfragbar';
		$translation['queryable'] = 'Abfragbarkeit';
		$translation['restrictedScale'] = 'Sichtbarkeit';
		$translation['minscale'] = 'Minimaler Maßstab';
		$translation['maxscale'] = 'Maximaler Maßstab';
		$translation['crs'] = 'Koordinatenreferenzsysteme (mit BBOX)';
		$translation['wmccrs'] = 'Eingestelltes Koordinatenreferenzsystem';	
		$translation['wgs84Bbox'] = 'Eckpunkte in geogr. Koordinaten';
		$translation['wgs84BboxGraphic'] = 'Ausdehnung';
		$translation['mapbenderCapabilities'] = 'Geoportal Capabilities';
		$translation['originalCapabilities'] = 'Original Capabilities';
		$translation['describeFeaturetype'] = 'Link zum Datenschema';
		$translation['kml'] = 'KML';
		$translation['inspireMetadata'] = 'INSPIRE Service Metadaten';
		$translation['showInspireMetadata'] = 'Metadatendatei';
		$translation['securedCapabilities'] = 'Secured Capabilities URL';
		$translation['inspireCapabilities'] = 'INSPIRE Capabilities URL';
		$translation['capabilities'] = 'Link zum Capabilities Dokument';
		$translation['inspireMetadataValidation'] = 'Validierung gegen INSPIRE Geoportal';
		$translation['showInspireMetadataValidation'] = 'Validierung starten';
		$translation['statusOK'] = 'Stabil';
		$translation['statusChanged'] = 'Beschreibung hat sich geändert - Aktualisierung nötig';
		$translation['statusProblem'] = 'Problem bei letzter Kontrolle';
		$translation['contactTelephone'] = 'Telefon';
		$translation['wmc'] = 'Web Map Context Dokument';
		$translation['graphicUnavailable'] = 'Graphische Übersicht nicht aktiviert';
		$translation['notMonitored'] = 'Informationen über die Qualität sind nur verfügbar, wenn das Service Monitoring aktiv ist!';
		$translation['wmcQualityText'] = 'Für Web Map Context Dokumente entfällt die Angabe zur Qualität!';
		$translation['noTouInformation'] = 'Es sind keine Informationen über Nutzungsbedingungen verfügbar!';
		$translation['loadWmc'] = 'Link um Anwendung mit WMC zu starten';
		$translation['validate'] = "Validierung";
		$translation['uploaded metadata'] = "Hochgeladene Metadaten";
		$translation['linked metadata'] = "Verlinkte Metadaten";
		$translation['metadata from capabilities'] = "Metadaten von Service Capabilities";
		$translation['added from registry'] = "Metadaten über Registry angereichert";
		$translation['Coupled Metadata'] = "Verknüpfte Metadaten";
		$translation['addLayerToMap'] = "Karte im eigenen Viewer anzeigen";
		$translation['showMap'] = "Karte anzeigen";
		break;
	case "en":
		$translation['overview'] = 'Overview';
		$translation['properties'] = 'Properties';
		$translation['termsOfUse'] = 'Terms Of Use';
		$translation['contact'] = 'Contact';
		$translation['quality'] = 'Quality';
		$translation['interfaces'] = 'Interfaces';
		$translation['metadata'] = 'Metadata';
		$translation['kindOfResource'] = 'Kind of resource';
		$translation['wms'] = 'Map Service';
		$translation['wfs'] = 'Data Service';
		$translation['layer'] = 'Map Layer';
		$translation['featuretype'] = 'Featuretype';
		$translation['geomtype'] = 'Type of geometry';
		$translation['contentId'] = 'Resourceidentifier';
		$translation['contentName'] = 'Name of the resource';
		$translation['serviceId'] = 'Information about the service';
		$translation['preview'] = 'Preview';
		$translation['extent'] = 'Extent';
		$translation['resourceAbstract'] = 'Abstract';
		$translation['resourceTitle'] = 'Title';
		$translation['metadataProvider'] = 'Responsible party for content';
		$translation['serviceProvider'] = 'Responsible party for service';
		$translation['contactPerson'] = 'Contact person';
		$translation['contactOrganization'] = 'Organization';
		$translation['contactAddress'] = 'Address';
		$translation['email'] = 'Email';
		$translation['city'] = 'City';
		$translation['logo'] = 'Logo';
		$translation['status'] = 'Status';
		$translation['availability'] = 'Availability';
		$translation['statusRed'] = 'Problem with last monitoring!';
		$translation['statusGreen'] = 'Last Monitoring: OK';
		$translation['statusYellow'] = 'Servicedescription changed!';
		$translation['queryableFalse'] = 'Layer not queryable';
		$translation['queryableTrue'] = 'Layer queryable';
		$translation['queryable'] = 'Query';
		$translation['restrictedScale'] = 'Visibility';
		$translation['minscale'] = 'Minimum scale';
		$translation['maxscale'] = 'Maximum scale';
		$translation['crs'] = 'Coordinate Reference System';
		$translation['wmccrs'] = 'Used Coordinate Reference System';
		$translation['wgs84Bbox'] = 'Corner in geographic Coordinates';
		$translation['wgs84BboxGraphic'] = 'Spatial Extent';
		$translation['mapbenderCapabilities'] = 'Geoportal Capabilities';
		$translation['originalCapabilities'] = 'Original Capabilities';
		$translation['inspireCapabilities'] = 'INSPIRE Capabilities URL';
		$translation['describeFeaturetype'] = 'Dataschema';
		$translation['kml'] = 'KML';
		$translation['inspireMetadata'] = 'INSPIRE Service Metadata';
		$translation['showInspireMetadata'] = 'Metadatendatei';
		$translation['securedCapabilities'] = 'Secured Capabilities URL';
		$translation['capabilities'] = 'Link zum Capabilities Dokument';
		$translation['inspireMetadataValidation'] = 'Validation against INSPIRE Geoportal';
		$translation['showInspireMetadataValidation'] = 'Start Validation';
		$translation['statusOK'] = 'stable';
		$translation['statusChanged'] = 'Description changed - update necessary';
		$translation['statusProblem'] = 'Problem at last control';
		$translation['contactTelephone'] = 'Telephon';
		$translation['wmc'] = 'Web Map Context document';
		$translation['graphicUnavailable'] = 'Graphical Overview not active';
		$translation['notMonitored'] = 'Information about Quality is only available if the service monitoring is activated!';
		$translation['wmcQualityText'] = 'In case of Web Map Context Documents the Quality part is not applicable!';
		$translation['noTouInformation'] = 'No informations about terms of use are available!';
		$translation['loadWmc'] = 'Link to start application with WMC';
		$translation['validate'] = "validate";
		$translation['uploaded metadata'] = "uploaded metadata";
		$translation['linked metadata'] = "linked metadata";
		$translation['metadata from capabilities'] = "metadata from capabilities";
		$translation['added from registry'] = "added from registry";
		$translation['Coupled Metadata'] = "Coupled Metadata";
		$translation['addLayerToMap'] = "Show map in own viewer";
		$translation['showMap'] = "Show map";
		break;
	default: #to english
		$translation['overview'] = 'Overview';
		$translation['properties'] = 'Properties';
		$translation['termsOfUse'] = 'Terms Of Use';
		$translation['contact'] = 'Contact';
		$translation['quality'] = 'Quality';
		$translation['interfaces'] = 'Interfaces';
		$translation['metadata'] = 'Metadata';
		$translation['kindOfResource'] = 'Kind of resource';
		$translation['wms'] = 'Map Service';
		$translation['wfs'] = 'Data Service';
		$translation['layer'] = 'Map Layer';
		$translation['featuretype'] = 'Featuretype';
		$translation['geomtype'] = 'Type of geometry';
		$translation['contentId'] = 'Resourceidentifier';
		$translation['contentName'] = 'Name of the resource';
		$translation['serviceId'] = 'Information about the service';
		$translation['preview'] = 'Preview';
		$translation['extent'] = 'Extent';
		$translation['resourceAbstract'] = 'Abstract';
		$translation['resourceTitle'] = 'Title';
		$translation['metadataProvider'] = 'Responsible party for content';
		$translation['serviceProvider'] = 'Responsible party for service';
		$translation['contactPerson'] = 'Contact person';
		$translation['contactOrganization'] = 'Organization';
		$translation['contactAddress'] = 'Address';
		$translation['email'] = 'Email';
		$translation['city'] = 'City';
		$translation['logo'] = 'Logo';
		$translation['status'] = 'Status';
		$translation['availability'] = 'Availability';
		$translation['statusRed'] = 'Problem with last monitoring!';
		$translation['statusGreen'] = 'Last Monitoring: OK';
		$translation['statusYellow'] = 'Servicedescription changed!';
		$translation['queryableFalse'] = 'Layer not queryable';
		$translation['queryableTrue'] = 'Layer queryable';
		$translation['queryable'] = 'Query';
		$translation['restrictedScale'] = 'Visibility';
		$translation['minscale'] = 'Minimum scale';
		$translation['maxscale'] = 'Maximum scale';
		$translation['crs'] = 'Coordinate Reference System';
		$translation['wmccrs'] = 'Used Coordinate Reference System';
		$translation['wgs84Bbox'] = 'Corner in geographic Coordinates';
		$translation['wgs84BboxGraphic'] = 'Spatial Extent';
		$translation['mapbenderCapabilities'] = 'Geoportal Capabilities';
		$translation['originalCapabilities'] = 'Original Capabilities';
		$translation['describeFeaturetype'] = 'Dataschema';
		$translation['kml'] = 'KML';
		$translation['inspireMetadata'] = 'INSPIRE Service Metadata';
		$translation['showInspireMetadata'] = 'Metadatendatei';
		$translation['securedCapabilities'] = 'Secured Capabilities URL';
		$translation['inspireCapabilities'] = 'INSPIRE Capabilities URL';
		$translation['capabilities'] = 'Link zum Capabilities Dokument';
		$translation['inspireMetadataValidation'] = 'Validation against INSPIRE Geoportal';
		$translation['showInspireMetadataValidation'] = 'Start Validation';
		$translation['statusOK'] = 'stable';
		$translation['statusChanged'] = 'Description changed - update necessary';
		$translation['statusProblem'] = 'Problem at last control';
		$translation['contactTelephone'] = 'Telephon';
		$translation['wmc'] = 'Web Map Context document';
		$translation['graphicUnavailable'] = 'Graphical Overview not active';
		$translation['notMonitored'] = 'Information about Quality is only available if the service monitoring is activated!';
		$translation['wmcQualityText'] = 'In case of Web Map Context Documents the Quality part is not applicable!';
		$translation['noTouInformation'] = 'No informations about terms of use are available!';
		$translation['loadWmc'] = 'Link to start application with WMC';
		$translation['validate'] = "validate";
		$translation['uploaded metadata'] = "uploaded metadata";
		$translation['linked metadata'] = "linked metadata";
		$translation['metadata from capabilities'] = "metadata from capabilities";
		$translation['added from registry'] = "added from registry";
		$translation['Coupled Metadata'] = "Coupled Metadata";
		$translation['addLayerToMap'] = "Show map in own viewer";
		$translation['showMap'] = "Show map";
}

//Array with infos about the different elements which are shown in the tabs

//Check if an id and a resource was given
if (!isset($_REQUEST["id"]) or !isset($_REQUEST["resource"])) {
	echo 'Not enough input parameters. resource and id must be given!<br/>'; 
	die(); 	
}

//Read out information from mapbender database
switch ($resource) {
	case "wms":
		//get root layer information
		$sql = "SELECT layer_id FROM layer WHERE fkey_wms_id = $1 AND layer_pos = 0";
		$v = array($id);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);
		$row = db_fetch_array($res);
		$layerId = $row["layer_id"];
		$sql = "SELECT ";
		$sql .= "layer.layer_id as contentid, layer.layer_title as contenttitle, layer.layer_abstract as contentabstract, layer.layer_pos as contentpos, layer.layer_parent as contentparent, ";
		$sql .= "layer.layer_minscale as contentminscale, layer.layer_maxscale as contentmaxscale, layer.layer_queryable,";
		$sql .= "wms.wms_title as servicetitle, wms.wms_abstract as serviceabstract, wms.wms_id as serviceid, wms.fees, wms.accessconstraints, wms.contactperson, wms.wms_getcapabilities,";
		$sql .= "wms.contactposition, wms.contactorganization, wms.address, wms.city, wms_timestamp as timestamp, wms_owner as owner, wms.wms_owsproxy as owsproxy, wms.fkey_mb_group_id,";
		$sql .= "wms.stateorprovince, wms.postcode, wms.contactvoicetelephone, wms.contactfacsimiletelephone, ";
		$sql .= "wms.contactelectronicmailaddress, wms.country ";
		$sql .= "FROM layer, wms WHERE layer.layer_id = $1 AND layer.fkey_wms_id = wms.wms_id LIMIT 1";
		$v = array($layerId);
		$t = array('i');
		$serviceType = 'wms';
		$resourceSymbol = "<img src='../img/osgeo_graphics/geosilk/server_map.png' alt='".$translation['wms']." - Bild' title='".$translation['wms']."'> - ".$translation['wms'];
		break;
	case "layer":
		$layerId = $id;
		$sql = "SELECT ";
		$sql .= "layer.layer_id as contentid, layer.layer_title as contenttitle, layer.layer_abstract as contentabstract, layer.layer_pos as contentpos, layer.layer_parent as contentparent,layer.layer_name as contentname, ";
		$sql .= "layer.layer_minscale as contentminscale, layer.layer_maxscale as contentmaxscale, layer.layer_queryable,";
		$sql .= "wms.wms_title as servicetitle, wms.wms_abstract as serviceabstract, wms.wms_id as serviceid, wms.fees, wms.accessconstraints, wms.contactperson,  wms.wms_getcapabilities,";
		$sql .= "wms.contactposition, wms.contactorganization, wms.address, wms.city, wms_timestamp as timestamp, wms_owner as owner, wms.wms_owsproxy as owsproxy, wms.fkey_mb_group_id,";
		$sql .= "wms.stateorprovince, wms.postcode, wms.contactvoicetelephone, wms.contactfacsimiletelephone, ";
		$sql .= "wms.contactelectronicmailaddress, wms.country ";
		$sql .= "FROM layer, wms WHERE layer.layer_id = $1 AND layer.fkey_wms_id = wms.wms_id LIMIT 1";
		$v = array($layerId);
		$t = array('i');
		$serviceType = 'wms';
		$resourceSymbol = "<img src='../img/osgeo_graphics/Layer.png' alt='".$translation['layer']." - Bild' title='".$translation['layer']."'> - ".$translation['layer'];
		break;
	case "wfs":
		$wfsId = $id;
		$sql = "SELECT ";
		$sql .= "wfs.wfs_title as servicetitle, wfs.wfs_version as serviceversion, wfs.wfs_abstract as serviceabstract, wfs.wfs_id as serviceid,  wfs.wfs_id as contentid,wfs.fees, wfs.accessconstraints, wfs.individualname as contactperson, wfs.wfs_getcapabilities,";
		$sql .= "wfs.positionname as contactposition, wfs.providername as contactorganization, wfs.deliverypoint as address, wfs.city, wfs_timestamp as timestamp, wfs_owner as owner, wfs.wfs_owsproxy as owsproxy, wfs.fkey_mb_group_id,";
		$sql .= "wfs.administrativearea as stateorprovince, wfs.postalcode as postcode, wfs.voice as contactvoicetelephone, wfs.facsimile as contactfacsimiletelephone, ";
		$sql .= "wfs.electronicmailaddress as contactelectronicmailaddress, wfs.country ";
		$sql .= "FROM wfs WHERE wfs_id = $1";
		$v = array($wfsId);
		$t = array('i');
		$resourceSymbol = "<img src='../img/osgeo_graphics/geosilk/server_vector.png' alt='".$translation['wfs']." - Bild' title='".$translation['wfs']."'> - ".$translation['wfs'];
		$serviceType = 'wfs';
		break;
	case "featuretype":
		$featuretypeId = $id;
		$sql = "SELECT ";
		$sql .= "wfs_featuretype.featuretype_id as contentid, wfs_featuretype.featuretype_title as contenttitle, wfs_featuretype.featuretype_abstract as contentabstract, wfs_featuretype.featuretype_name as contentname,wfs_featuretype.featuretype_srs, ";
		$sql .= "wfs.wfs_title as servicetitle, wfs.wfs_version as serviceversion, wfs.wfs_abstract as serviceabstract, wfs.wfs_id as serviceid, wfs.fees, wfs.accessconstraints, wfs.individualname as contactperson, wfs.wfs_getcapabilities, wfs.wfs_describefeaturetype, ";
		$sql .= "wfs.positionname as contactposition, wfs.providername as contactorganization, wfs.deliverypoint as address, wfs.city, wfs_timestamp as timestamp, wfs_owner as owner, wfs.wfs_owsproxy as owsproxy, wfs.fkey_mb_group_id,";
		$sql .= "wfs.administrativearea as stateorprovince, wfs.postalcode as postcode, wfs.voice as contactvoicetelephone, wfs.facsimile as contactfacsimiletelephone, ";
		$sql .= "wfs.electronicmailaddress as contactelectronicmailaddress, wfs.country ";
		$sql .= "FROM wfs_featuretype, wfs WHERE wfs_featuretype.featuretype_id = $1 AND wfs_featuretype.fkey_wfs_id = wfs.wfs_id LIMIT 1";
		$v = array($featuretypeId);
		$t = array('i');
		$serviceType = 'wfs';
		$resourceSymbol = "<img src='../img/osgeo_graphics/geosilk/vector.png' alt='".$translation['featuretype']." - Bild' title='".$translation['featuretype']."'> - ".$translation['featuretype'];
		$serviceType = 'wfs';	
		break;
	case "wfs-conf":
		echo 'Not yet implemented!'; 
		$serviceType = 'wfs';
		die(); 	
		break;
	case "wmc":
		$wmcId = $id;
		$sql = "SELECT ";
		$sql .= "mb_user_wmc.wmc_serial_id as contentid, mb_user_wmc.wmc_title as contenttitle, mb_user_wmc.abstract as contentabstract, mb_user_wmc.minx as contentminx,mb_user_wmc.miny as contentminy,mb_user_wmc.maxx as contentmaxx,mb_user_wmc.maxy as contentmaxy,mb_user_wmc.srs as contentcrs, ";
		$sql .= "mb_user.mb_user_position_name as contactposition, mb_user.mb_user_organisation_name as contactorganization, (mb_user.mb_user_street || ' ' || mb_user.mb_user_housenumber)  as address, mb_user.mb_user_city as city, mb_user_wmc.wmc_timestamp as timestamp, mb_user_wmc.fkey_user_id as owner,";
		$sql .= "mb_user.mb_user_country as stateorprovince, mb_user.mb_user_postal_code as postcode, mb_user.mb_user_phone as contactvoicetelephone, mb_user.mb_user_phone1 as contactfacsimiletelephone, ";
		$sql .= "mb_user.mb_user_email as contactelectronicmailaddress ";
		$sql .= "FROM mb_user_wmc, mb_user WHERE mb_user_wmc.wmc_serial_id = $1 AND mb_user_wmc.fkey_user_id = mb_user.mb_user_id LIMIT 1";
		$v = array($wmcId);
		$t = array('i');
		$serviceType = 'wmc';
		$resourceSymbol = "<img src='../img/osgeo_graphics/Mapset.png' alt='".$translation['wmc']." - Bild' title='".$translation['wmc']."'> - ".$translation['wmc'];
		break;
}
//read resource information from database
//$e = new mb_exception("mod_showMetadata: sql: ".$sql);
$res = db_prep_query($sql,$v,$t);

$resourceMetadata = db_fetch_array($res);


if (!isset($resourceMetadata['contentid']) or ($resourceMetadata['contentid'] == '')) {
		echo 'No result for the requested id found in the registry!'; 
		die(); 	
}

if($resourceMetadata['owsproxy']!='') {
	$resourceSecured = true;
}
else {
	$resourceSecured = false;
}
$serviceId = $resourceMetadata['serviceid'];


/*switch ($serviceType) {
	case "wms":
		#$resourceSymbol = "<img src='../img/osgeo_graphics/geosilk/server_map.png' alt='".."' title='".."'>";
		die(); 	
		break;
	case "wfs":
		#$resourceSymbol = "<img src='../img/osgeo_graphics/geosilk/server_vector.png' alt='' title=''>";
		die(); 	
		break;		
	case "wmc":
		$resourceSymbol = "<img src='' alt='' title=''>";
		die(); 	
		break;
	case "kml":
		die(); 	
		break;
	case "georss":
		die(); 	
		break;
}

*/




//$e = new mb_exception("mod_showMetadata: fkey_mb_group_id from wms or wfs table: ".$resourceMetadata['fkey_mb_group_id']);
if (!isset($resourceMetadata['fkey_mb_group_id']) or is_null($resourceMetadata['fkey_mb_group_id']) or $resourceMetadata['fkey_mb_group_id'] == 0){
	$e = new mb_notice("mod_showMetadata: fkey_mb_group_id not found!");
	//Get information about owning user of the relation mb_user_mb_group - alternatively the defined fkey_mb_group_id from the service must be used!
	$sqlDep = "SELECT mb_group_name as metadatapointofcontactorgname, mb_group_title as metadatapointofcontactorgtitle, mb_group_id, mb_group_logo_path  as metadatapointofcontactorglogo, mb_group_address as metadatapointofcontactorgaddress, mb_group_email as metadatapointofcontactorgemail, mb_group_postcode as metadatapointofcontactorgpostcode, mb_group_city as metadatapointofcontactorgcity, mb_group_voicetelephone as metadatapointofcontactorgtelephone, mb_group_facsimiletelephone as metadatapointofcontactorgfax FROM mb_group AS a, mb_user AS b, mb_user_mb_group AS c WHERE b.mb_user_id = $1  AND b.mb_user_id = c.fkey_mb_user_id AND c.fkey_mb_group_id = a.mb_group_id AND c.mb_user_mb_group_type=2 LIMIT 1";
	$vDep = array($resourceMetadata['owner']);
	$tDep = array('i');
	$resDep = db_prep_query($sqlDep, $vDep, $tDep);
	$metadataContactGroup = db_fetch_array($resDep);
	$e = new mb_notice("mod_showMetadata: mb_group_id: ".$metadataContactGroup['mb_group_id']);
	$e = new mb_notice("mod_showMetadata: mb_group_logo_path: ".$metadataContactGroup['metadatapointofcontactorglogo']);
} else {
	$e = new mb_notice("mod_showMetadata: fkey_mb_group_id found!");
	$sqlDep = "SELECT mb_group_name as metadatapointofcontactorgname, mb_group_title as metadatapointofcontactorgtitle, mb_group_id, mb_group_logo_path  as metadatapointofcontactorglogo, mb_group_address as metadatapointofcontactorgaddress, mb_group_email as metadatapointofcontactorgemail, mb_group_postcode as metadatapointofcontactorgpostcode, mb_group_city as metadatapointofcontactorgcity, mb_group_voicetelephone as metadatapointofcontactorgtelephone, mb_group_facsimiletelephone as metadatapointofcontactorgfax FROM mb_group WHERE mb_group_id = $1 LIMIT 1";
	$vDep = array($resourceMetadata['fkey_mb_group_id']);
	$tDep = array('i');
	$resDep = db_prep_query($sqlDep, $vDep, $tDep);
	$metadataContactGroup = db_fetch_array($resDep);
}
//Get Geometry Type if featuretype info was requested
if ($resource == 'featuretype') {
	$getTypeSql = "SELECT element_id, element_type from wfs_element WHERE fkey_featuretype_id = $1 AND element_type LIKE '%PropertyType';";
	$vgetType = array($resourceMetadata['contentid']);
	$tgetType = array('i');
	$resGetType = db_prep_query($getTypeSql,$vgetType,$tgetType);
	$featuretypeElements = db_fetch_array($resGetType);
	$resourceMetadata['featuretype_geomType'] = $featuretypeElements['element_type'];
}



$e = new mb_notice("mod_showMetadata: mb_group_name: ".$metadataContactGroup['mb_group_name']);
//db select for layer previews
if ($resource == 'wms' or $resource == 'layer') {
	//$sqlP = "SELECT * FROM layer_preview WHERE fkey_layer_id = $1 LIMIT 1";
	//$vP = array($layerId);
	//$tP = array('i');
	//$resP = db_prep_query($sqlP, $vP, $tP);
	//$rowP = db_fetch_array($resP);
	//if ($rowP['layer_map_preview_filename'] != "") {
		$resourceMetadata['preview'] = "<img src = '../geoportal/mod_showPreview.php?resource=layer&id=".$layerId."'>";

	//}
	//if ($rowP['layer_legend_preview_filename'] != "") {
		$resourceMetadata['legend'] .= "<img src = '../geoportal/mod_showPreview.php?resource=layerlegend&id=".$layerId."'>";
	//}
/*	if ($rowP['layer_extent_preview_filename'] != "") {
		$resourceMetadata['extent'] .= "<img src = '../geoportal/layer_preview/".$rowP['layer_extent_preview_filename']."'>";
	}*/
	
}
if ($resource == 'wmc') {
	$resourceMetadata['preview'] = "<img src = '../geoportal/mod_showPreview.php?resource=wmc&id=".$resourceMetadata['contentid']."'>";
}
//db select for service quality
if ($resource == 'wms' or $resource == 'layer') {
	$sql = "SELECT availability, last_status FROM mb_wms_availability WHERE fkey_wms_id = $1";
	$v = array($serviceId);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	$serviceQuality = db_fetch_array($res);
}
//db select for content properties
if ($resource == 'wms' or $resource == 'layer') {
	//get bbox and crs codes for single layer - maybe some entries ;-)
	$sql = "SELECT * FROM layer_epsg WHERE fkey_layer_id = $1";
	$contentBboxes = array();
	$v = array($layerId);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	$j = 0;
	while ($row = db_fetch_array($res)){
		$contentBboxes[$j] = array();
		$contentBboxes[$j]['epsg'] = $row['epsg'];
		$contentBboxes[$j]['minx'] = $row['minx'];
		$contentBboxes[$j]['miny'] = $row['miny'];
		$contentBboxes[$j]['maxx'] = $row['maxx'];
		$contentBboxes[$j]['maxy'] = $row['maxy'];
		$j++;
	}
	$j = 0;
}
if ($resource == 'wmc') {
		$contentBboxes[$j]['epsg'] = $resourceMetadata['srs'];
		$contentBboxes[$j]['minx'] = $resourceMetadata['minx'];
		$contentBboxes[$j]['miny'] = $resourceMetadata['miny'];
		$contentBboxes[$j]['maxx'] = $resourceMetadata['maxx'];
		$contentBboxes[$j]['maxy'] = $resourceMetadata['maxy'];
}
//generate HTML frame

//Give out page

//Array with structure of metadata

//e.g. tabs and their content
$html = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$languageCode.'">';
$html .= '<body>';
$metadataStr .= '<head>' . 
		'<title>'.$translation['metadata'].'</title>' . 
		'<meta name="description" content="'.$translation['metadata'].'" xml:lang="'.$languageCode.'" />'.
		'<meta name="keywords" content="'.$translation['metadata'].'" xml:lang="'.$languageCode.'" />'	.	
		'<meta http-equiv="cache-control" content="no-cache">'.
		'<meta http-equiv="pragma" content="no-cache">'.
		'<meta http-equiv="expires" content="0">'.
		'<meta http-equiv="content-language" content="'.$languageCode.'" />'.
		'<meta http-equiv="content-style-type" content="text/css" />'.
		'<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">' . 	
		'</head>';
$html .= $metadataStr;
//define the javascripts to include
	$html .= '<link type="text/css" href="../css/metadata.css" rel="Stylesheet" />';

if ($layout == 'tabs') {
	$html .= '<link type="text/css" href="../extensions/jquery-ui-1.8.1.custom/css/custom-theme/jquery-ui-1.8.5.custom.css" rel="Stylesheet" />';	
	$html .= '<script type="text/javascript" src="../extensions/jquery-ui-1.8.1.custom/js/jquery-1.4.2.min.js"></script>';
	$html .= '<script type="text/javascript" src="../extensions/jquery-ui-1.8.1.custom/js/jquery-ui-1.8.1.custom.min.js"></script>';
//some js for dialog
	//following is added to give a window with an interated link which can be included in external applications
	$html .= '<script type="text/javascript">';
	
	$html .= 'showCapabilitiesUrl = function (url,title) {';
		$html .= 'hideCapabilitiesUrl();';
		$html .= 'var $capabilitiesUrlPopup = $(\'<div><input size="40" type="text" value="\' + url + \'"/></div>\');';
		$html .= '$capabilitiesUrlPopup.dialog({';
			$html .= 'title: title,';
			$html .= 'bgiframe: true,';
			$html .= 'autoOpen: true,';
			$html .= 'resizable: false,';
			$html .= 'modal: true,';
			$html .= 'width: 400,';
			$html .= 'height: 90,';
			$html .= 'pos: [600,40]';
		$html .= '});';
	$html .= '};';
	
	$html .= 'hideCapabilitiesUrl = function () {';
		$html .= 'if($(\'capabilitiesUrl.Popup\').size() > 0) {';
			$html .= '$(\'capabilitiesUrl.Popup\').dialog(\'destroy\');';
		$html .= '}';
	$html .= '};';
	$html .= '</script>';

/*if ($metadataContactGroup['metadatapointofcontactorglogo'] != '') {
	$html .= "<img src='".$metadataContactGroup['metadatapointofcontactorglogo']."'  height='30'>";
}
//TODO use right name
$html .= displayText($resourceMetadata['contactorganization']);
$html .= '<h3>'.displayText($resourceMetadata['contenttitle']).'</h3>';
*/



//initialize tabs
	$html .= '<script type="text/javascript">';
	$html .= '$(function() {';
	$html .= '	$("#tabs").tabs();';
	$html .= '});';
	$html .= '</script>';









//independently define the headers of the parts
	$html .= '<div class="demo">';
	$html .= '<div id="tabs">';
	$html .= '<ul>';
	$html .= 	'<li><a href="#tabs-1">'.$translation["overview"].'</a></li>';
	$html .= 	'<li><a href="#tabs-2">'.$translation["properties"].'</a></li>';
	$html .= 	'<li><a href="#tabs-3">'.$translation["contact"].'</a></li>';
	$html .= 	'<li><a href="#tabs-4">'.$translation["termsOfUse"].'</a></li>';
	$html .= 	'<li><a href="#tabs-5">'.$translation["quality"].'</a></li>';
	$html .= 	'<li><a href="#tabs-6">'.$translation["interfaces"].'</a></li>';
	$html .= '</ul>';

}

if ($layout == 'accordion') {
	$html .= '<link type="text/css" href="../extensions/jquery-ui-1.8.1.custom/css/custom-theme/jquery-ui-1.8.4.custom.css" rel="Stylesheet" />';	
	$html .= '<script type="text/javascript" src="../extensions/jquery-ui-1.8.1.custom/js/jquery-1.4.2.min.js"></script>';
	$html .= '<script type="text/javascript" src="../extensions/jquery-ui-1.8.1.custom/js/jquery-ui-1.8.1.custom.min.js"></script>';
	//define the javascript functions
	$html .= '<script type="text/javascript">';
	$html .= '	$(function() {';
	$html .= '		$("#accordion").accordion();';
	//$html .= '		$("#accordion").accordion({ autoHeight: false});';
	//$html .= '		$("#accordion").accordion({ autoHeight: false , clearStyle: true });';
	$html .= '	});';
	$html .= '	</script>';
	$html .= '<div class="demo">';
	$html .= '<div id="accordion">';
}
if ($layout == 'plain') {
	$html .= '<div class="demo">';
	$html .= '<div id="plain">';
}

//some placeholders
$tableBegin =  "<table>\n";
$t_a = "\t<tr>\n\t\t<th>\n\t\t\t";
$t_b = "\n\t\t</th>\n\t\t<td>\n\t\t\t";
$t_c = "\n\t\t</td>\n\t</tr>\n";
$tableEnd = "</table>\n";
//**************************overview part begin******************************
//generate div tags for the content - the divs are defined in the array
if ($layout == 'accordion') {
	$html .= '<h3><a href="#">'.$translation["overview"].'</a></h3>';
	$html .= '<div style="height:300px">';
}
if ($layout == 'tabs') {
	$html .= '<div id="tabs-1">';
}
if ($layout == 'plain') {
	$html .= '<h3>'.$translation["overview"].'</h3>';
	$html .= '<div>';
}
$html .= '<p>';
$html .= $tableBegin;
if ($resourceMetadata['contenttitle'] !='') {
	$html .= $t_a.$translation['resourceTitle'].$t_b."<em>".displayText($resourceMetadata['contenttitle'])."</em>".$t_c;
} else {
	$html .= $t_a.$translation['resourceTitle'].$t_b."<em>".displayText($resourceMetadata['servicetitle'])."</em>".$t_c;
}

//decide if a root layer have been found - then the type will be a server
#$html .= "<br>".$resourceMetadata['contentpos']."<br>";
#$html .= "<br>".$resource."<br>";
if ($resource == 'layer' & $resourceMetadata['contentpos'] == 0) {
	$resourceSymbol = "<img src='../img/osgeo_graphics/geosilk/server_map.png' alt='".$translation['wms']." - picture' title='".$translation['wms']."'> - ".$translation['wms'];
}

$html .= $t_a.$translation['kindOfResource'].$t_b.$resourceSymbol.$t_c;

//identification information:
$html .= $t_a.$translation['contentId'].$t_b.$resourceMetadata['contentid'].$t_c;

if (isset($resourceMetadata['contentname']) && ($resourceMetadata['contentname'] != '')) {
	$html .= $t_a.$translation['contentName'].$t_b.$resourceMetadata['contentname'].$t_c;
}



if ($resource != 'wmc') {
	$html .= $t_a.$translation['serviceId'].$t_b."<a href='".$self."?resource=".$serviceType."&id=".$serviceId."&layout=".$layout."&languageCode=".$languageCode."'>".$serviceId."</a>".$t_c;
}

if (($resource == 'wms' or $resource == 'layer' or $resource == 'wmc' ) and isset($resourceMetadata['preview'])) {
	$html .= $t_a.$translation['preview'].$t_b.$resourceMetadata['preview'];
	if (isset($resourceMetadata['legend'])) {
		$html .= $resourceMetadata['legend'];
	}
	$html .= $t_c;
}





if ($metadataContactGroup['metadatapointofcontactorglogo'] != '') {
	$html .= $t_a.$translation['contactOrganization'].$t_b."<img src='".$metadataContactGroup['metadatapointofcontactorglogo']."'  height='30'>";
}
$html .= displayText($metadataContactGroup['metadatacontactorganization']).$t_c;
if ($resourceMetadata['contentabstract'] != '') {
	$html .= $t_a.$translation['resourceAbstract'].$t_b.displayText($resourceMetadata['contentabstract']).$t_c;
} else {
	$html .= $t_a.$translation['resourceAbstract'].$t_b.displayText($resourceMetadata['serviceabstract']).$t_c;
}

$user = new User();
$layerAccessibility = $user->isLayerAccessible ($layerId);


//

//
// Monitoring is only available if the user is allowed to access this service
//

if ($resource == 'wms' or $resource == 'layer'){
	if ($layerAccessibility) {		
		$is_public = $user->isPublic();
		//show abo function to registred and authorized users
		if (!$is_public) {
			if ($subscribe == 1) {
				$user->addSubscription($resourceMetadata['serviceid']);
			}
			else if ($subscribe == 0) {
				$user->cancelSubscription($resourceMetadata['serviceid']);
			}
			$is_subscribed = $user->hasSubscription($resourceMetadata['serviceid']);
			if ($is_subscribed) {
				$aboStr = "<tr><th>Abo</th><td><a href = '../php/mod_showMetadata.php?id=" . 
					$layerId . "&resource=layer&user_id=" . $user->id . "&subscribe=0'><img  style='border: none;' src = '../img/mail_delete.png' title='"._mb("Monitoring Abo l&ouml;schen")."'></a></td></tr>"; //TODO check wherefor user_id should be given as parameter?
			}
			else if (!$is_subscribed) {
				$aboStr = "<tr><th>Abo</th><td><a href = '../php/mod_showMetadata.php?id=" . $layerId . 
					"&resource=layer&user_id=" . $user->id . "&subscribe=1'><img style='border: none;' src = '../img/mail_send.png' title='"._mb("Monitoring abonnieren")."'></a></td></tr>";
			}
		}	
	}
	$html .= $aboStr;
}


if ($layerAccessibility && WRAPPER_PATH != '' && ($resource == 'layer' or $resource == 'wms' )) {
	$showMapUrl = $mapbenderBaseUrl.WRAPPER_PATH."?LAYER[zoom]=1&LAYER[id]=".$resourceMetadata['contentid'];
	//$html .= $t_a.$translation['addLayerToMap'].$t_b."<a href='".$showMapUrl."' target='_blank'><img src='../img/osgeo_graphics/layer-wms-add.png'></a>".$t_c;
	$html .= $t_a."<button onclick='window.open(\"".$showMapUrl."\", 
  \"windowname1\", 
  \"width=1024, height=768\");'
   return false;><img src='../img/osgeo_graphics/layer-wms-add.png'>".$translation['showMap']."</button>".$t_b."".$t_c;
	
}

if (WRAPPER_PATH != '' && $resource == 'wmc') {
	$showMapUrl = $mapbenderBaseUrl.WRAPPER_PATH."?WMC=".$resourceMetadata['contentid'];
	//$html .= $t_a.$translation['addLayerToMap'].$t_b."<a href='".$showMapUrl."' target='_blank'><img src='../img/osgeo_graphics/layer-wms-add.png'></a>".$t_c;
	$html .= $t_a."<button onclick='window.open(\"".$showMapUrl."\", 
  \"windowname1\", 
  \"width=1024, height=768\");'
   return false;><img src='../img/osgeo_graphics/layer-wms-add.png'>".$translation['showMap']."</button>".$t_b."".$t_c;
	
}

$html .= $tableEnd;
$html .= '</p>';
$html .= '</div>';

//**************************overview part end******************************

//**************************properties part begin******************************
if ($layout == 'accordion') {
	$html .= '<h3><a href="#">'.$translation["properties"].'</a></h3>';
	$html .= '<div style="height:300px">';
}
if ($layout == 'tabs') {
	$html .= '<div id="tabs-2">';
}
if ($layout == 'plain') {
	$html .= '<h3>'.$translation["properties"].'</h3>';
	$html .= '<div>';
}
$html .= '<p>';
$html .= $tableBegin;

if ($resource == 'wms' or $resource == 'layer'){
	if ($resourceMetadata['layer_queryable'] == '1') {
			$html .= $t_a.$translation['queryable'].$t_b."<img src='../img/osgeo_graphics/select.png' title='".$translation['queryableTrue']."' alt='".$translation['queryableTrue']."'>".$t_c;	
	} else {
		$html .= $t_a.$translation['queryable'].$t_b."<img src='../img/osgeo_graphics/not_selectable.png' title='".$translation['queryableFalse']."' alt='".$translation['queryableFalse']."'>".$t_c;	
	}
}
$epsgString = '';
if (($resource == 'wms') || ($resource == 'layer')) {
	for ($j = 0; $j < count($contentBboxes); $j++) {
		$epsgString .= $contentBboxes[$j]['epsg']." ";
		if ($contentBboxes[$j]['epsg'] == 'EPSG:4326') {
			$wgs84Bbox = $contentBboxes[$j]['minx'].",".$contentBboxes[$j]['miny'].",".$contentBboxes[$j]['maxx'].",".$contentBboxes[$j]['maxy'];
			$getMapUrl = getExtentGraphic(explode(",", $wgs84Bbox));
		}
		
	}
$html .= $t_a.$translation['crs'].$t_b.$epsgString.$t_c;
}

if (($resource == 'featuretype') ) {
	$epsgString .= $resourceMetadata['featuretype_srs'];
	$html .= $t_a.$translation['crs'].$t_b.$epsgString.$t_c;
}






if ($resource == 'wmc') {
	$epsgString .= $resourceMetadata['contentcrs']." ";
	if ($resourceMetadata['contentcrs'] == 'EPSG:4326') {
		$wgs84Bbox = $resourceMetadata['contentminx'].",".$resourceMetadata['contentminy'].",".$resourceMetadata['contentmaxx'].",".$resourceMetadata['contentmaxy'];
		$getMapUrl = getExtentGraphic(explode(",", $wgs84Bbox));
	} elseif ($resourceMetadata['contentcrs'] != ''){
		//transform crs
		$oldEPSG = preg_replace("/EPSG:/","", $resourceMetadata['contentcrs']);
		$ll = transform(
					floatval($resourceMetadata['contentminx']), 
					floatval($resourceMetadata['contentminy']), 
					$oldEPSG, 
					"4326"
				);
		$ur = transform(
					floatval($resourceMetadata['contentmaxx']), 
					floatval($resourceMetadata['contentmaxy']), 
					$oldEPSG, 
					"4326"
				);
		$wgs84Bbox = round($ll["x"],4).",".round($ll["y"],4).",".round($ur["x"],4).",".round($ur["y"],4);
		$getMapUrl = getExtentGraphic(explode(",", $wgs84Bbox));
	}
$html .= $t_a.$translation['wmccrs'].$t_b.$epsgString.$t_c;
}



if (isset($wgs84Bbox)) {
	
	$html .= $t_a.$translation['wgs84Bbox'].$t_b.$wgs84Bbox.$t_c;
	if (defined('EXTENTSERVICEURL')) {
		$html .= $t_a.$translation['wgs84BboxGraphic'].$t_b."<img src='".$getMapUrl."'>".$t_c;
	} else {
		$html .= $t_a.$translation['wgs84BboxGraphic'].$t_b.$translation['graphicUnavailable'].$t_c;
	}
	//show preview map - dynamically
	
}
$html .= $tableEnd;
$html .= $tableBegin;

//Scales
if ((isset($resourceMetadata['contentminscale']) & $resourceMetadata['contentminscale'] != '0') or (isset($resourceMetadata['contentmaxscale']) & $resourceMetadata['contentmaxscale'] != '0')){
	$html .= $t_a.$translation['restrictedScale'].$t_b.$t_c;
	if (isset($resourceMetadata['contentminscale']) & $resourceMetadata['contentminscale'] != '0' & $resourceMetadata['contentminscale'] != "") {
		$html .= $t_a.$translation['maxscale'].$t_b. "1 : ".$resourceMetadata['contentminscale'].$t_c;	
	}
	if (isset($resourceMetadata['contentmaxscale']) & $resourceMetadata['contentmaxscale'] != '0' & $resourceMetadata['contentmaxscale'] != "") {
		$html .= $t_a.$translation['minscale'].$t_b. "1 : ".$resourceMetadata['contentmaxscale'].$t_c;	
	}
	//$html .= '</fieldset>';
}
$html .= $tableEnd;
if (isset($resourceMetadata['wfs_describefeaturetype']) && ($resourceMetadata['wfs_describefeaturetype'] != '')) {
	$html .= $t_a.$translation['describeFeaturetype'].$t_b."<a href='".$resourceMetadata['wfs_describefeaturetype']."SERVICE=WFS&VERSION=".$resourceMetadata['serviceversion']."&REQUEST=DescribeFeaturetype&typename=".$resourceMetadata['contentname']."' >Link</a>".$t_c;
	#$html .= $t_a.$translation['describeFeaturetype'].$t_b."<a href='".$resourceMetadata['wfs_describefeaturetype']."&REQUEST=DescribeFeaturetype&typename=".$resourceMetadata['contentname']."' >Link</a>".$t_c;
}


if (isset($resourceMetadata['featuretype_geomType']) && ($resourceMetadata['featuretype_geomType'] != '')) {
	$html .= $t_a.$translation['geomtype'].$t_b.$resourceMetadata['featuretype_geomType'].$t_c;
}

if ($resource == 'wms' or $resource == 'layer'){
	$html .= $tableBegin;
	//part for coupled resources - if they exists (first this is realized only for layers):
	//get metadata entries
	//get MetadataURLs from md_metadata table
	$sql = <<<SQL
	SELECT metadata_id, uuid, link, linktype, md_format, origin FROM mb_metadata 
	INNER JOIN (SELECT * from ows_relation_metadata 
	WHERE fkey_layer_id = $layerId ) as relation ON 
	mb_metadata.metadata_id = relation.fkey_metadata_id WHERE mb_metadata.origin
	IN('capabilities','external','metador','upload')
SQL;
	$res = db_query($sql);
	
	$i = 0;
	$metadataList = "";
	while ($row = db_fetch_assoc($res)) {
		//$html .= "<li>";
		switch ($row["origin"]) {
			case "capabilities" :
				$metadataList .= "<img src='../img/osgeo_graphics/geosilk/server_map.png' title='".$translation['metadata from capabilities']."'/>";
			break;
			case "external" :
				$metadataList .= "<img src='../img/osgeo_graphics/geosilk/link.png' title='".$translation['linked metadata']."'/>";
			break;
			case "upload" :
				$metadataList .= "<img src='../img/button_blue_red/up.png' title='".$translation['uploaded metadata']."'/>";
			break;	
			case "metador" :
				$metadataList .= "<img src='../img/gnome/edit-select-all.png' title='".$translation['added from registry']."'/>";
			break;
			default:
			break;
		}
		$metadataList .= "<a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id=".$row["uuid"]."'>".$row["uuid"]."</a> <a href='../php/mod_dataISOMetadata.php?outputFormat=iso19139&id=".$row["uuid"]."&validate=true'>".$translation['validate']."</a><br>";
		$i++;
	}
	if ($i != 0) {
		$html .= $t_a.$translation['Coupled Metadata'].$t_b;
		$html .= $metadataList;
	}
	
	$html .= $t_c;
	$html .= $tableEnd;
}

$html .= $tableEnd;
$html .= '</p>';
$html .= '</div>';
//**************************properties  part end******************************

//**************************contact part begin******************************
if ($layout == 'accordion') {
	$html .= '<h3><a href="#">'.$translation["contact"].'</a></h3>';
	$html .= '<div style="height:300px">';
}
if ($layout == 'tabs') {
	$html .= '<div id="tabs-3">';
}
if ($layout == 'plain') {
	$html .= '<h3>'.$translation["contact"].'</h3>';
	$html .= '<div>';
}
$html .= '<p>';

$html .= '<h4>'.$translation['metadataProvider'].'</h4>';

$html .= $tableBegin;
if ($metadataContactGroup['metadatapointofcontactorglogo'] != '') {
	$html .= $t_a.$translation['logo'].$t_b."<img src='".$metadataContactGroup['metadatapointofcontactorglogo']."'  height='30'>".$t_c;
}
$html .= $t_a.$translation['contactOrganization'].$t_b.displayText($metadataContactGroup['metadatapointofcontactorgtitle']).$t_c;
$html .= $t_a.$translation['contactAddress'].$t_b.displayText($metadataContactGroup['metadatapointofcontactorgaddress']).$t_c;
$html .= $t_a.$translation['city'].$t_b.displayText($metadataContactGroup['metadatapointofcontactorgpostcode'].' '.$metadataContactGroup['metadatapointofcontactorgcity']).$t_c;
$html .= $t_a.$translation['email'].$t_b.displayText($metadataContactGroup['metadatapointofcontactorgemail']).$t_c;
$html .= $tableEnd;

$html .= '<h4>'.$translation['serviceProvider'].'</h4>';
$html .= $tableBegin;
$html .= $t_a.$translation['contactOrganization'].$t_b.displayText($resourceMetadata['contactorganization']).$t_c;
$html .= $t_a.$translation['contactPerson'].$t_b.displayText($resourceMetadata['contactperson']).$t_c;
$html .= $t_a.$translation['contactAddress'].$t_b.displayText($resourceMetadata['address']).$t_c;
$html .= $t_a.$translation['contactCity'].$t_b.displayText($resourceMetadata['postcode'].' '.$resourceMetadata['city']).$t_c;
$html .= $t_a.$translation['contactTelephone'].$t_b.displayText($resourceMetadata['contactvoicetelephone']).$t_c;
$html .= $t_a.$translation['email'].$t_b.displayText($resourceMetadata['contactelectronicmailaddress']).$t_c;
$html .= $tableEnd;

$html .= '</p>';

$html .= '</div>';
//**************************contact part end******************************

//**************************termsOfUse part begin******************************
if ($layout == 'accordion') {
	$html .= '<h3><a href="#">'.$translation["termsOfUse"].'</a></h3>';
	$html .= '<div style="height:300px">';
}
if ($layout == 'tabs') {
	$html .= '<div id="tabs-4">';
}
if ($layout == 'plain') {
	$html .= '<h3>'.$translation["termsOfUse"].'</h3>';
	$html .= '<div>';
}
$html .= '<p>';
if ($resource == 'wms' or $resource == 'layer') {
	$touServiceConnector = new connector($mapbenderProtocol."localhost".$_SERVER['SCRIPT_NAME']."/../mod_getServiceDisclaimer.php?resource=wms&id=".$resourceMetadata['serviceid']."&languageCode=".$languageCode."&asTable=true");
	$tou = $touServiceConnector->file;
}
if ($resource == 'wmc' ) {
	$e = new mb_notice("mod_showMetadata: wmcid for disclaimer: ".$resourceMetadata['contentid']);
	$touWmcConnector = new connector($mapbenderProtocol."localhost".$_SERVER['SCRIPT_NAME']."/../mod_getWmcDisclaimer.php?&id=".$resourceMetadata['contentid']."&languageCode=".$languageCode."&hostName=".$hostName);
	$tou = $touWmcConnector->file;
}
if ($tou == ''){
	$html .= $translation['noTouInformation'];
} else {
	$html .= $tou;
}
$html .= '</p>';
$html .= '</div>';
//**************************termsOfUse part end******************************

//**************************quality part begin******************************
if ($layout == 'accordion') {
	$html .= '<h3><a href="#">'.$translation["quality"].'</a></h3>';
	$html .= '<div style="height:300px">';
}
if ($layout == 'tabs') {
	$html .= '<div id="tabs-5">';
}
if ($layout == 'plain') {
	$html .= '<h3>'.$translation["quality"].'</h3>';
	$html .= '<div>';
}
$html .= '<p>';
$html .= $tableBegin;
if ($resource != 'wmc') {
	switch ($serviceQuality['last_status']) {
		case '1':
			$html .= $t_a.$translation['status'].$t_b."<img src='../img/trafficlights/go.bmp' height='24px' width='24px' alt='".$translation['statusOK']."' title='".$translation['statusOK']."'>".$t_c;
			break;
		case '0':
			$html .= $t_a.$translation['status'].$t_b."<img src='../img/trafficlights/wait.bmp' height='24px' width='24px'  alt='".$translation['statusChanged']."' title='".$translation['statusChanged']."'>".$t_c;
			break;
		case '-1':
			$html .= $t_a.$translation['status'].$t_b."<img src='../img/trafficlights/stop.bmp' height='24px' width='24px'  alt='".$translation['statusProblem']."' title='".$translation['statusChanged']."'>".$t_c;
			break;
	}
	if (isset($serviceQuality['availability'])) {
		$html .= $t_a.$translation['availability'].$t_b.$serviceQuality['availability']." %".$t_c;
	} else {
		$html .= $t_a.$translation['availability'].$t_b.$translation['notMonitored'].$t_c;
	}
} else { //resource is wmc
	$html .= $translation['wmcQualityText'];
}

$html .= $tableEnd;
$html .= '</p>';
$html .= '</div>';
//**************************quality part end******************************

//**************************interfaces part begin******************************
if ($layout == 'accordion') {
	$html .= '<h3><a href="#">'.$translation["interfaces"].'</a></h3>';
	$html .= '<div style="height:300px">';
}
if ($layout == 'tabs') {
	$html .= '<div id="tabs-6">';
}
if ($layout == 'plain') {
	$html .= '<h3>'.$translation["interfaces"].'</h3>';
	$html .= '<div>';
}
$html .= '<p>';

/*$translation['mapbenderCapabilities'] = 'Geoportal Capabilities';
$translation['originalCapabilities'] = 'Original Capabilities';
$translation['kml'] = 'KML';
$translation['inspireMetadata'] = 'INSPIRE Service Metadaten';
$translation['securedCapabilities'] = 'Secured Capabilities URL';*/
$html .= $tableBegin;
if ($resource == 'wmc') {
	$html .= $t_a.$translation['wmc'].$t_b."XML".$t_c;
	//show qr for link 
	//create uuid for qr graphic
	$uuid = new Uuid;
	$filename = "qr_wmc_".$uuid.".png";
	//generate qr on the fly in tmp folder
	//link to invoke wmc per get api if wrapper path isset
	if (WRAPPER_PATH != "") {
		$invokeLink = $mapbenderBaseUrl."".WRAPPER_PATH."?WMC=".$resourceMetadata['contentid'];
		QRcode::png($invokeLink,TMPDIR."/".$filename);
		$html .= $t_a.$translation['loadWmc'].$t_b."<img src='".TMPDIR."/".$filename."'>".$t_c;
	}
	
}
if ($resource == 'wfs') {
}

if ($resource == 'wms' or $resource == 'layer'){

	$html .= $t_a.$translation['mapbenderCapabilities'].$t_b."<a href = '../php/wms.php?layer_id=".$layerId."&PHPSESSID=".session_id()."&REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS' target=_blank>".$translation['capabilities']."</a> <img src='../img/osgeo_graphics/geosilk/link.png' onclick='showCapabilitiesUrl(\"".$mapbenderBaseUrl.$_SERVER['PHP_SELF']."/../wms.php?layer_id=".$layerId."&PHPSESSID=".session_id()."&REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS"."\",\"".$translation['mapbenderCapabilities']."\");'>".$t_c;
$capUrl = $resourceMetadata['wms_getcapabilities'].getConjunctionCharacter($resourceMetadata['wms_getcapabilities']).'REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS';

	//show only original url if the resource is not secured!
	if (!$resourceSecured) {	
		$html .= $t_a.$translation['originalCapabilities'].$t_b."<a href = '".$capUrl."' target=_blank>".$translation['capabilities']."</a>".$t_c;
	}

	$html .= $t_a.$translation['inspireCapabilities'].$t_b."<a href = '../php/wms.php?layer_id=".$layerId."&PHPSESSID=".session_id()."&INSPIRE=1&REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS' target=_blank>".$translation['capabilities']."</a> <img src='../img/osgeo_graphics/geosilk/link.png' onclick='showCapabilitiesUrl(\"".$mapbenderBaseUrl.$_SERVER['PHP_SELF']."/../wms.php?layer_id=".$layerId."&PHPSESSID=".session_id()."&INSPIRE=1&REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS"."\",\"".$translation['inspireCapabilities']."\");'>".$t_c;

	$html .= $t_a.$translation['inspireMetadata'].$t_b."<a href='../php/mod_layerISOMetadata.php?SERVICE=WMS&outputFormat=iso19139&Id=".	$layerId."' target=_blank ><img style='border: none;' src='../img/inspire_tr_36.png' title='".$translation['inspireMetadata']."' style='width:34px;height:34px' alt='' /></a>".$t_c;
	$html .= $t_a.$translation['inspireMetadataValidation'].$t_b."<a href='../php/mod_layerISOMetadata.php?SERVICE=WMS&outputFormat=iso19139&Id=".$layerId."&validate=true' target=_blank title='".$translation['inspireMetadataValidation']."'>".$translation['showInspireMetadataValidation']."</a>".$t_c;

	//if service is secured and http_auth is adjusted show secured url
	if ($resourceSecured) {
		$securedLink = HTTP_AUTH_PROXY."/".$layerId."?REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS";
		$html .= $t_a.$translation['securedCapabilities'].$t_b."<a href = '".$securedLink."' target=_blank>".$translation['capabilities']."</a> <img src='../img/osgeo_graphics/geosilk/link.png' onclick='showCapabilitiesUrl(\"".$securedLink."\",\"".$translation['securedCapabilities']."\");'>".$t_c;
	}

	//kml
	$html .= $t_a.$translation['kml'].$t_b."<a href='../php/mod_interfaceWms4Kml.php?id=".$layerId."'><img style='border: none;' src='../img/misc/kml_icon.gif' title='".$translation['kml']."' style='width:34px;height:34px' alt='' /></a>".$t_c;
}





$html .= $tableEnd;
$html .= '</p>';
$html .= '</div>';
//**************************interfaces part end******************************

$html .= '</div>'; //accordion
$html .= '</div>'; //demo

$html .= '</body>';
$html .= '</html>';

echo $html;

//functions (from old metadata module):
function displayText($string) {
    $string = mb_eregi_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=_blank>\\0</a>", $string);   
    $string = mb_eregi_replace("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([0-9a-z](-?[0-9a-z])*\.)+[a-z]{2}([zmuvtg]|fo|me)?$", "<a href=\"mailto:\\0\" target=_blank>\\0</a>", $string);   
    $string = mb_eregi_replace("\n", "<br>", $string);
    return $string;
} 
function getExtentGraphic($layer_4326_box) {
		$rlp_4326_box = array(6.05,48.9,8.6,50.96);
		if ($layer_4326_box[0] <= $rlp_4326_box[0] || $layer_4326_box[2] >= $rlp_4326_box[2] || $layer_4326_box[1] <= $rlp_4326_box[1] || $layer_4326_box[3] >= $rlp_4326_box[3]) {
			if ($layer_4326_box[0] < $rlp_4326_box[0]) {
				$rlp_4326_box[0] = $layer_4326_box[0]; 
			}
			if ($layer_4326_box[2] > $rlp_4326_box[2]) {
				$rlp_4326_box[2] = $layer_4326_box[2]; 
			}
			if ($layer_4326_box[1] < $rlp_4326_box[1]) {
				$rlp_4326_box[1] = $layer_4326_box[1]; 
			}
			if ($layer_4326_box[3] > $rlp_4326_box[3]) {
				$rlp_4326_box[3] = $layer_4326_box[3]; 
			}

			$d_x = $rlp_4326_box[2] - $rlp_4326_box[0]; 
			$d_y = $rlp_4326_box[3] - $rlp_4326_box[1];
			
			$new_minx = $rlp_4326_box[0] - 0.05*($d_x);
			$new_maxx = $rlp_4326_box[2] + 0.05*($d_x);
			$new_miny = $rlp_4326_box[1] - 0.05*($d_y);
			$new_maxy = $rlp_4326_box[3] + 0.05*($d_y);

			if ($new_minx < -180) $rlp_4326_box[0] = -180; else $rlp_4326_box[0] = $new_minx;
			if ($new_maxx > 180) $rlp_4326_box[2] = 180; else $rlp_4326_box[2] = $new_maxx;
			if ($new_miny < -90) $rlp_4326_box[1] = -90; else $rlp_4326_box[1] = $new_miny;
			if ($new_maxy > 90) $rlp_4326_box[3] = 90; else $rlp_4326_box[3] = $new_maxy;
		}
		$getMapUrl = EXTENTSERVICEURL."VERSION=1.1.1&REQUEST=GetMap&SERVICE=WMS&LAYERS=".EXTENTSERVICELAYER."&STYLES=&SRS=EPSG:4326&BBOX=".$rlp_4326_box[0].",".$rlp_4326_box[1].",".$rlp_4326_box[2].",".$rlp_4326_box[3]."&WIDTH=120&HEIGHT=120&FORMAT=image/png&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=application/vnd.ogc.se_inimage&minx=".$layer_4326_box[0]."&miny=".$layer_4326_box[1]."&maxx=".$layer_4326_box[2]."&maxy=".$layer_4326_box[3];
		return $getMapUrl;
}
//from php/mod_coordsLookup_server.php
function transform ($x, $y, $oldEPSG, $newEPSG) {
	if (is_null($x) || !is_numeric($x) || 
		is_null($y) || !is_numeric($y) ||
		is_null($oldEPSG) || !is_numeric($oldEPSG) ||
		is_null($newEPSG) || !is_numeric($newEPSG)) {
		return null;
	}
		$sqlMinx = "SELECT X(transform(GeometryFromText('POINT(".$x." ".$y.")',".$oldEPSG."),".$newEPSG.")) as minx";
		$resMinx = db_query($sqlMinx);
		$minx = floatval(db_result($resMinx,0,"minx"));
		
		$sqlMiny = "SELECT Y(transform(GeometryFromText('POINT(".$x." ".$y.")',".$oldEPSG."),".$newEPSG.")) as miny";
		$resMiny = db_query($sqlMiny);
		$miny = floatval(db_result($resMiny,0,"miny"));

	return array("x" => $minx, "y" => $miny);
	
}
function getConjunctionCharacter ($url) {
	if (mb_strpos($url, "?") !== false) { 
		if (mb_substr($url, mb_strlen($url)-1, 1) == "?") { 
			return "";
		}
		else if (mb_substr($url, mb_strlen($url)-1, 1) == "&"){
			return "";
		}
		else {
			return "&";
		}
	}
	else {
		return "?";
	}
	return "";
}


?>
