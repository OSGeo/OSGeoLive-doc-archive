<?php
#http://www.geoportal.rlp.de/mapbender/php/mod_dataISOMetadata.php?outputFormat=iso19139&Id=uuid
#http://localhost/mapbender/php/mod_dataISOMetadata.php?outputFormat=iso19139&ID=cb567df4-57da-449a-be74-821903a59d45
# $Id: mod_dataISOMetadata.php 235
# http://www.mapbender.org/index.php/Inspire_Metadata_Editor
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
//function to pull out information from mapbenders md_metadata table
//the information is pulled out by uuid cause the uuid is not changed
//there are 3 ways to access the information:
//1. read a link out from database and give the link content back to the requesting client
//2. read the metadata addon information and fill the rest from the wms/mb_user/mb_group/layer table
//3. give back the harvested content of the column data - if conform?

require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_connector.php");
require_once(dirname(__FILE__) . "/../classes/class_administration.php");
require_once(dirname(__FILE__) . "/../classes/class_Uuid.php");

$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);

$admin = new administration();
$wmsView = '';
//parse request parameter
//make all parameters available as upper case
foreach($_REQUEST as $key => $val) {
	$_REQUEST[strtoupper($key)] = $val;
}
//example mapbender uuid: 2494d033-ccdd-cdd7-71c6-3e3c195e1d85
//cb567df4-57da-449a-be74-821903a59d45

//validate request params
if (isset($_REQUEST['ID']) & $_REQUEST['ID'] != "") {
	//validate uuid
	$testMatch = $_REQUEST["ID"];
	$uuid = new Uuid($testMatch);
	$isUuid = $uuid->isValid();
	if (!$isUuid) {
		echo 'Id: <b>'.$testMatch.'</b> is not a valid mapbender uuid.<br/>'; 
		die(); 		
 	}
	$recordId = $testMatch;
	$testMatch = NULL;
}
//
if ($_REQUEST['OUTPUTFORMAT'] == "iso19139") {
	//Initialize XML document
	$iso19139Doc = new DOMDocument('1.0');
	$iso19139Doc->encoding = 'UTF-8';
} else {
	echo 'outputFormat: <b>'.$_REQUEST['OUTPUTFORMAT'].'</b> is not set or valid.<br/>'; 
	die();
}
//if validation is requested
//
if (isset($_REQUEST['VALIDATE']) and $_REQUEST['VALIDATE'] != "true") {
	//
	echo 'validate: <b>'.$_REQUEST['VALIDATE'].'</b> is not valid.<br/>'; 
	die();
}
	//get record from mb_metadata and prohibit duplicates:
	$sql = <<<SQL

SELECT * FROM mb_metadata WHERE uuid = $1 ORDER BY lastchanged DESC LIMIT 1

SQL;
	$v = array($uuid);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
	if (!$res) {
		echo "No record with uuid ".$recordId." found in mapbender database!";
		die();
	}
	$row = db_fetch_assoc($res);
	$mb_metadata = $row;
	//convert dates to timestamps
	$mb_metadata['createdate'] = strtotime($mb_metadata['createdate']);
	$mb_metadata['changedate'] = strtotime($mb_metadata['changedate']);
	$mb_metadata['lastchanged'] = strtotime($mb_metadata['lastchanged']);
	//check which kind of metadata was found:
	switch ($mb_metadata['origin']) {
		case 'metador':
		//generate the xml on the fly - there is no need to store it as xml in the database
		//do the things which had to be done ;-)
		if ($_REQUEST['VALIDATE'] == "true"){
			validateInspireMetadata($iso19139Doc, $recordId); //calls fillISO19139 to!
		} else {
			pushISO19139($iso19139Doc, $recordId); //throw it out!
		}

		break;
		case 'external':
			if ($mb_metadata['export2csw']) { //the metadata must have been harvested before!
				if ($mb_metadata['harvestresult'] == 1) {
					if ($_REQUEST['VALIDATE'] != "true") {
						header("Content-type: text/xml");
						echo $mb_metadata['data'];
						die();
					} else {
						validateInspireMetadataFromData($row['data']);
						die();
					}
				} else {
					//send error report - metadata has not been harvested - maybe
					$errMsg = "Metadata should have been harvested, but some unkown error occured";
					$errMsg .= "<br>Please use following URL directly: <a href='".$mb_metadata['link']."'>".$row['link']."</a><br>";
					echo $errMsg;
					die();
				}
			} else {
				//load metadata, from url and send it to requesting client
				$metadataUrlObject = new connector($mb_metadata['link']);
				$metadataXml = $metadataUrlObject->file;

				if ($_REQUEST['VALIDATE'] != "true") {
					header("Content-type: text/xml");
					echo $metadataXml;
					die();
				} else {
					validateInspireMetadataFromData($metadataXml);
					die();
				}
				
			}	
		//if xml has been harvested - push this xml from database, if not just harvest it and push the result
		break;
		case 'upload':
			if ($mb_metadata['harvestresult'] == 1) {
				if ($_REQUEST['VALIDATE'] != "true") {
					header("Content-type: text/xml");
					echo $mb_metadata['data'];
					die();
				} else {
					validateInspireMetadataFromData($row['data']);
					die();
				}
			} else {
				//send error report - metadata has not been harvested - maybe
				$errMsg = "Metadata should have been harvested, but some unkown error occured";
				echo $errMsg;
				die();
			}
		break;
		case 'capabilities':
			//do the same as for the external case but all from caps should be harvested
			if ($mb_metadata['harvestresult'] == 1) {
				if ($_REQUEST['VALIDATE'] != "true") {
					header("Content-type: text/xml");
					echo $mb_metadata['data'];
					die();
				} else {
					validateInspireMetadataFromData($mb_metadata['data']);
					die();
				}
			} else {
				//send error report - metadata has not been harvested - maybe
				$errMsg = "Metadata should have been harvested, but some unkown error occured";
				$errMsg .= "<br>Please use following URL directly: <a href='".$mb_metadata['link']."'>".$mb_metadata['link']."</a><br>";
				echo $errMsg;
				die();
			}
		default:
		break;	
	}
//function to give away the xml data
function pushISO19139($iso19139Doc, $recordId) {
	//echo $recordId;
	header("Content-type: application/xhtml+xml; charset=UTF-8");
	$xml = fillISO19139($iso19139Doc, $recordId);
	echo $xml;
	die();
}
//some needfull functions to pull metadata out of the database!
function fillISO19139($iso19139, $recordId) {
	global $admin;
	global $mb_metadata;
	//read out relevant information from mapbender database:
	//layer and service information:
	$sql = <<<SQL
SELECT layer_id, fkey_wms_id FROM layer INNER JOIN ows_relation_metadata ON 
(ows_relation_metadata.fkey_layer_id=layer.layer_id)  WHERE ows_relation_metadata.fkey_metadata_id = $1
SQL;
	//TODO: Problem - the metadata may be used for more than one service - not often but sometimes - does one get the right contact data?
	$e = new mb_exception("used metadata id: ".$mb_metadata['metadata_id']);
	$v = array($mb_metadata['metadata_id']);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	if (!$res) {
		$exception = "<exception>No coupled resource found in database!</exception>";
		return $exception;
	} else {
		$mb_metadata_coupling = db_fetch_array($res);
	}
	$layerId = $mb_metadata_coupling['layer_id'];
	$wmsId = $mb_metadata_coupling['wms_id'];
	$e = new mb_notice("mod_dataISOMetadata.php: Found coupled layer with id: ".$layerId);
	if ($wmsView != '') {
		$sql = "SELECT * ";
		$sql .= "FROM ".$wmsView." WHERE layer_id = $1";
	}
	else {
	//next function is for normal mapbender installations and read the info directly from the wms and layer tables
		$sql = "SELECT ";
		$sql .= "layer.layer_id,layer.layer_name, layer.layer_title, layer.layer_abstract, layer.layer_pos, layer.layer_parent, layer.layer_minscale, layer.layer_maxscale, layer.uuid,";
		$sql .= "wms.wms_title, wms.wms_abstract, wms.wms_id, wms.fees, wms.accessconstraints, wms.contactperson, ";
		$sql .= "wms.contactposition, wms.contactorganization, wms.address, wms.city, wms_timestamp, wms_owner, ";
		$sql .= "wms.stateorprovince, wms.postcode, wms.contactvoicetelephone, wms.contactfacsimiletelephone, wms.wms_owsproxy,";
		$sql .= "wms.contactelectronicmailaddress, wms.country, wms.fkey_mb_group_id, ";
		$sql .= "layer_epsg.minx || ',' || layer_epsg.miny || ',' || layer_epsg.maxx || ',' || layer_epsg.maxy  as bbox ";
		$sql .= "FROM wms, layer, layer_epsg WHERE layer_id = $1 and layer.fkey_wms_id = wms.wms_id";
		$sql .= " and layer_epsg.fkey_layer_id=layer.layer_id and layer_epsg.epsg='EPSG:4326'";
	}
	$v = array((integer)$layerId);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	$mapbenderMetadata = db_fetch_array($res);
	//infos about the registrating department, check first if a special metadata point of contact is defined in the service table - function from mod_showMetadata - TODO: should be defined in admin class
	if (!isset($mapbenderMetadata['fkey_mb_group_id']) or is_null($mapbenderMetadata['fkey_mb_group_id']) or $mapbenderMetadata['fkey_mb_group_id'] == 0){
		$e = new mb_exception("mod_dataISOMetadata.php: fkey_mb_group_id not found!");
		//Get information about owning user of the relation mb_user_mb_group - alternatively the defined fkey_mb_group_id from the service must be used!
		$sqlDep = "SELECT mb_group_name, mb_group_title, mb_group_id, mb_group_logo_path, mb_group_address, mb_group_email, mb_group_postcode, mb_group_city, mb_group_voicetelephone, mb_group_facsimiletelephone FROM mb_group AS a, mb_user AS b, mb_user_mb_group AS c WHERE b.mb_user_id = $1  AND b.mb_user_id = c.fkey_mb_user_id AND c.fkey_mb_group_id = a.mb_group_id AND c.mb_user_mb_group_type=2 LIMIT 1";
		$vDep = array($mapbenderMetadata['wms_owner']);
		$tDep = array('i');
		$resDep = db_prep_query($sqlDep, $vDep, $tDep);
		$departmentMetadata = db_fetch_array($resDep);
	} else {
		$e = new mb_exception("mod_dataISOMetadata.php: fkey_mb_group_id found!");
		$sqlDep = "SELECT mb_group_name , mb_group_title, mb_group_id, mb_group_logo_path , mb_group_address, mb_group_email, mb_group_postcode, mb_group_city, mb_group_voicetelephone, mb_group_facsimiletelephone FROM mb_group WHERE mb_group_id = $1 LIMIT 1";
		$vDep = array($mapbenderMetadata['fkey_mb_group_id']);
		$tDep = array('i');
		$resDep = db_prep_query($sqlDep, $vDep, $tDep);
		$departmentMetadata = db_fetch_array($resDep);
	}
	//infos about the owner of the service - he is the man who administrate the metadata - register the service
	$sql = "SELECT mb_user_email ";
	$sql .= "FROM mb_user WHERE mb_user_id = $1";
	$v = array((integer)$mapbenderMetadata['wms_owner']);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	$userMetadata = db_fetch_array($res);
	//check if resource is freely available to anonymous user - which are all users who search thru metadata catalogues:
	$hasPermission=$admin->getLayerPermission($mapbenderMetadata['wms_id'],$mapbenderMetadata['layer_name'],PUBLIC_USER);
	//Creating the "MD_Metadata" node
	$MD_Metadata = $iso19139->createElementNS('http://www.isotc211.org/2005/gmd', 'gmd:MD_Metadata');
	$MD_Metadata = $iso19139->appendChild($MD_Metadata);
	$MD_Metadata->setAttribute("xmlns:srv", "http://www.isotc211.org/2005/srv");
	$MD_Metadata->setAttribute("xmlns:gml", "http://www.opengis.net/gml");
	$MD_Metadata->setAttribute("xmlns:gco", "http://www.isotc211.org/2005/gco");
	$MD_Metadata->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
	$MD_Metadata->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
	$MD_Metadata->setAttribute("xsi:schemaLocation", "http://www.isotc211.org/2005/gmd ./xsd/gmd/gmd.xsd://www.isotc211.org/2005/srv ./xsd/srv/srv.xsd");

	//generate fileidentifier part (metadata record identification) 
	$identifier = $iso19139->createElement("gmd:fileIdentifier");
	$identifierString = $iso19139->createElement("gco:CharacterString");
	if (isset($mb_metadata['uuid'])) {
		$identifierText = $iso19139->createTextNode($mb_metadata['uuid']);
	}
	else {
		$identifierText = $iso19139->createTextNode("no id found");
	}
	$identifierString->appendChild($identifierText);
	$identifier->appendChild($identifierString);
	$MD_Metadata->appendChild($identifier);
	//generate language part B 10.3 (if available) of the inspire metadata regulation
	$language = $iso19139->createElement("gmd:language");
	$languagecode = $iso19139->createElement("gmd:LanguageCode");
	$languagecode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#LanguageCode");
	if (isset($mapbenderMetadata['metadata_language'])) {
		$languageText = $iso19139->createTextNode($mapbenderMetadata['metadata_language']);
		$languagecode->setAttribute("codeListValue", $mapbenderMetadata['metadata_language']);
	}
	else {
		$languageText = $iso19139->createTextNode("ger");
		$languagecode->setAttribute("codeListValue", "ger");
	}
	$languagecode->appendChild($languageText);
	$language ->appendChild($languagecode);
	$language = $MD_Metadata->appendChild($language);

	//generate Characterset 
	$characterSet = $iso19139->createElement("gmd:characterSet");
	$characterSetCode = $iso19139->createElement("gmd:MD_CharacterSetCode");
	$characterSetCode->setAttribute("codeList", "./resources/codeList.xml#MD_CharacterSetCode");
	$characterSetCode->setAttribute("codeListValue", $mb_metadata['inspire_charset']);
	$characterSet->appendChild($characterSetCode);
	$characterSet = $MD_Metadata->appendChild($characterSet);

	#generate MD_Scope part B 1.3 (if available)
	$hierarchyLevel = $iso19139->createElement("gmd:hierarchyLevel");
	$scopecode = $iso19139->createElement("gmd:MD_ScopeCode");
	$scopecode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_ScopeCode");
	if (isset($mb_metadata['type'])) {
		$scopecode->setAttribute("codeListValue", $mb_metadata['type']);
		$scopeText = $iso19139->createTextNode($mb_metadata['type']);
	}
	else {
		$scopecode->setAttribute("codeListValue", "dataset");
		$scopeText = $iso19139->createTextNode("dataset");
	}
	$scopecode->appendChild($scopeText);
	$hierarchyLevel->appendChild($scopecode);
	$hierarchyLevel=$MD_Metadata->appendChild($hierarchyLevel);

	#Part B 10.1 responsible party for the resource
	$contact=$iso19139->createElement("gmd:contact");
	$CI_ResponsibleParty=$iso19139->createElement("gmd:CI_ResponsibleParty");
	$organisationName=$iso19139->createElement("gmd:organisationName");
	$organisationName_cs=$iso19139->createElement("gco:CharacterString");
	if (isset($departmentMetadata['mb_group_name'])) {
		$organisationNameText = $iso19139->createTextNode($departmentMetadata['mb_group_name']); 
	}
	else
	{
		$organisationNameText=$iso19139->createTextNode('department not known');
	}
	$contactInfo=$iso19139->createElement("gmd:contactInfo");
	$CI_Contact=$iso19139->createElement("gmd:CI_Contact");
	$address=$iso19139->createElement("gmd:address");
	$CI_Address=$iso19139->createElement("gmd:CI_Address");
	$electronicMailAddress=$iso19139->createElement("gmd:electronicMailAddress");
	$electronicMailAddress_cs=$iso19139->createElement("gco:CharacterString");
	if (isset($userMetadata['mb_user_email']) && $userMetadata['mb_user_email'] != '') {
		//get email address from ows service metadata out of mapbender database	
		$electronicMailAddressText=$iso19139->createTextNode($userMetadata['mb_user_email']); 
	}
	else
	{
		$electronicMailAddressText=$iso19139->createTextNode('kontakt@geoportal.rlp.de');
	}
	$role=$iso19139->createElement("gmd:role");
	$CI_RoleCode=$iso19139->createElement("gmd:CI_RoleCode");
	$CI_RoleCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_RoleCode");
	$CI_RoleCode->setAttribute("codeListValue", "pointOfContact");
	$CI_RoleCodeText=$iso19139->createTextNode("pointOfContact");
	$organisationName_cs->appendChild($organisationNameText);
	$organisationName->appendChild($organisationName_cs);
	$CI_ResponsibleParty->appendChild($organisationName);
	$electronicMailAddress_cs->appendChild($electronicMailAddressText);
	$electronicMailAddress->appendChild($electronicMailAddress_cs);
	$CI_Address->appendChild($electronicMailAddress);
	$address->appendChild($CI_Address);
	$CI_Contact->appendChild($address);
	$contactInfo->appendChild($CI_Contact);
	$CI_RoleCode->appendChild($CI_RoleCodeText);
	$role->appendChild($CI_RoleCode);
	$CI_ResponsibleParty->appendChild($contactInfo);
	$CI_ResponsibleParty->appendChild($role);
	$contact->appendChild($CI_ResponsibleParty);
	$MD_Metadata->appendChild($contact);

	#generate dateStamp part B 10.2 (if available)
	$dateStamp = $iso19139->createElement("gmd:dateStamp");
	$mddate = $iso19139->createElement("gco:Date");
	if (isset($mb_metadata['lastchanged'])) {
		$mddateText = $iso19139->createTextNode(date("Y-m-d h:i:s",$mb_metadata['lastchanged']));
	}
	else {
		$mddateText = $iso19139->createTextNode("2000-01-01");
	}
	$mddate->appendChild($mddateText);
	$dateStamp->appendChild($mddate);
	$dateStamp=$MD_Metadata->appendChild($dateStamp);

	//standard definition - everytime the same
	$metadataStandardName = $iso19139->createElement("gmd:metadataStandardName");
	$metadataStandardVersion = $iso19139->createElement("gmd:metadataStandardVersion");
	$metadataStandardNameText = $iso19139->createElement("gco:CharacterString");
	$metadataStandardVersionText = $iso19139->createElement("gco:CharacterString");
	$metadataStandardNameTextString = $iso19139->createTextNode("ISO19115");
	$metadataStandardVersionTextString = $iso19139->createTextNode("2003/Cor.1:2006");
	$metadataStandardNameText->appendChild($metadataStandardNameTextString);
	$metadataStandardVersionText->appendChild($metadataStandardVersionTextString);
	$metadataStandardName->appendChild($metadataStandardNameText);
	$metadataStandardVersion->appendChild($metadataStandardVersionText);
	$MD_Metadata->appendChild($metadataStandardName);
	$MD_Metadata->appendChild($metadataStandardVersion);

	#do the things for identification
	$identificationInfo=$iso19139->createElement("gmd:identificationInfo");
	$MD_DataIdentification=$iso19139->createElement("gmd:MD_DataIdentification");
	$citation=$iso19139->createElement("gmd:citation");
	$CI_Citation=$iso19139->createElement("gmd:CI_Citation");

	#create nodes for things which are defined
	#Create Resource title element B 1.1
	$title=$iso19139->createElement("gmd:title");
	$title_cs=$iso19139->createElement("gco:CharacterString");
	if (isset($mb_metadata['title'])) {
		$titleText = $iso19139->createTextNode($mb_metadata['title']);
	}
	else {
		$titleText = $iso19139->createTextNode("title not given");
	}
	$title_cs->appendChild($titleText);
	$title->appendChild($title_cs);
	$CI_Citation->appendChild($title);

	#Create date elements B5.2-5.4
	#Do things for B 5.2 date of publication - maybe the date when the first metadata was generated or it should not be available at all
	if (isset($mb_metadata['createdate'])) {
		$date1=$iso19139->createElement("gmd:date");
		$CI_Date=$iso19139->createElement("gmd:CI_Date");
		$date2=$iso19139->createElement("gmd:date");
		$gcoDate=$iso19139->createElement("gco:Date");
		$dateType=$iso19139->createElement("gmd:dateType");
		$dateTypeCode=$iso19139->createElement("gmd:CI_DateTypeCode");
		$dateTypeCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_DateTypeCode");
		$dateTypeCode->setAttribute("codeListValue", "publication");
		$dateTypeCodeText=$iso19139->createTextNode('publication');
		$dateText= $iso19139->createTextNode(date("Y-m-d h:i:s",$mb_metadata['createdate']));
		$dateTypeCode->appendChild($dateTypeCodeText);
		$dateType->appendChild($dateTypeCode);
		$gcoDate->appendChild($dateText);
		$date2->appendChild($gcoDate);
		$CI_Date->appendChild($date2);
		$CI_Date->appendChild($dateType);
		$date1->appendChild($CI_Date);
		$CI_Citation->appendChild($date1);
	}
	#Do things for B 5.3 date of revision
	//this should be created from the information of maintenance if available
	//some initialization for the temporal extent:
	$beginPositionValue = $mb_metadata['tmp_reference_1'];
	$endPositionValue = $mb_metadata['tmp_reference_2'];
	$dateOfLastRevision = date('Y-m-d h:i:s');

	if (isset($mb_metadata['update_frequency']) && $mb_metadata['update_frequency'] != "") {
		switch ($mb_metadata['update_frequency']) {
			case ('continual'):
				//set value to now
				$endPositionValue = date('Y-m-d h:i:s');
				$dateOfLastRevision = $endPositionValue;
			break;
			case ('daily'):
				//set value to now - one day
				$endPositionValue = date('Y-m-d h:i:s', mktime(0, 0, 0, date("m"), date("d")-1,   date("Y")));
				$dateOfLastRevision = $endPositionValue;
			break;
			case ('weekly'):
				//set value to now - one week
				$endPositionValue = date('Y-m-d h:i:s', mktime(0, 0, 0, date("m"), date("d")-7,   date("Y")));
				$dateOfLastRevision = $endPositionValue;
			break;
			case ('fortnightly'):
				//set value to now - two weeks
				$endPositionValue = date('Y-m-d h:i:s', mktime(0, 0, 0, date("m"), date("d")-14,   date("Y")));
				$dateOfLastRevision = $endPositionValue;
			break;
			case ('monthly'):
				//set value to now - one month
				$endPositionValue =  date('Y-m-d h:i:s', mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
				$dateOfLastRevision = $endPositionValue;
			break;
			case ('quarterly'):
				//set value to now - 3 months
				$endPositionValue = date('Y-m-d h:i:s', mktime(0, 0, 0, date("m")-3, date("d"),   date("Y")));
				$dateOfLastRevision = $endPositionValue;
			break;
			case ('biannually'):
				//set value to now - half a year
				$endPositionValue = date('Y-m-d h:i:s', mktime(0, 0, 0, date("m")-6, date("d"),   date("Y")));
				$dateOfLastRevision = $endPositionValue;
			break;
			case ('annually'):
				//set value to now - one year
				$endPositionValue = date('Y-m-d h:i:s', mktime(0, 0, 0, date("m"), date("d"),   date("Y")-1));
				$dateOfLastRevision = $endPositionValue;
			break;
			default:
			break;
		}
	

		$date1=$iso19139->createElement("gmd:date");
		$CI_Date=$iso19139->createElement("gmd:CI_Date");
		$date2=$iso19139->createElement("gmd:date");
		$gcoDate=$iso19139->createElement("gco:Date");
		$dateType=$iso19139->createElement("gmd:dateType");
		$dateTypeCode=$iso19139->createElement("gmd:CI_DateTypeCode");
		$dateTypeCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_DateTypeCode");
		$dateTypeCode->setAttribute("codeListValue", "revision");
		$dateTypeCodeText=$iso19139->createTextNode('revision');
		$dateText= $iso19139->createTextNode($dateOfLastRevision);
		$dateTypeCode->appendChild($dateTypeCodeText);
		$dateType->appendChild($dateTypeCode);
		$gcoDate->appendChild($dateText);
		$date2->appendChild($gcoDate);
		$CI_Date->appendChild($date2);
		$CI_Date->appendChild($dateType);
		$date1->appendChild($CI_Date);
		$CI_Citation->appendChild($date1);
	}

/*	#Do things for B 5.4 date of creation not applicable cause it cannot be edited
	if (isset($mb_metadata['changedate']) || isset($mb_metadata['lastchanged'])) {
		$date1=$iso19139->createElement("gmd:date");
		$CI_Date=$iso19139->createElement("gmd:CI_Date");
		$date2=$iso19139->createElement("gmd:date");
		$gcoDate=$iso19139->createElement("gco:Date");
		$dateType=$iso19139->createElement("gmd:dateType");
		$dateTypeCode=$iso19139->createElement("gmd:CI_DateTypeCode");
		$dateTypeCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_DateTypeCode");
		$dateTypeCode->setAttribute("codeListValue", "creation");
		$dateTypeCodeText=$iso19139->createTextNode('creation');
		if (!isset($mb_metadata['changedate'])) {
			$dateText= $iso19139->createTextNode($mb_metadata['lastchanged']);
		} else {
			$dateText= $iso19139->createTextNode($mb_metadata['changedate']);
		}
		$dateTypeCode->appendChild($dateTypeCodeText);
		$dateType->appendChild($dateTypeCode);
		$gcoDate->appendChild($dateText);
		$date2->appendChild($gcoDate);
		$CI_Date->appendChild($date2);
		$CI_Date->appendChild($dateType);
		$date1->appendChild($CI_Date);
		$CI_Citation->appendChild($date1);
	}
*/
	#Do things for B 1.5 Resource unique identifier NOTE: not applicable for services
//example from INSPIRE Guidance Metadata 
//The main problem will be, that this entry is the identifier for the service data coupling 
//to make it easy, we will use the same identifier as for fileidentifier! In case of external datasets, the right data identifier must be harvested! 
//
/*<gmd:identifier><gmd:RS_Identifier><gmd:code><gco:CharacterString>gdi-rp</gco:CharacterString></gmd:code><gmd:codeSpace><gco:CharacterString>gdi-rp</gco:CharacterString></gmd:codeSpace></gmd:RS_Identifier></gmd:identifier>*/
	$identifier=$iso19139->createElement("gmd:identifier");
	$rs_identifier=$iso19139->createElement("gmd:RS_Identifier");

	$code=$iso19139->createElement("gmd:code");
	$code_cs=$iso19139->createElement("gco:CharacterString");
	$codeText=$iso19139->createTextNode($mb_metadata['uuid']); //in case of the mapbender addon!

	$codeSpace=$iso19139->createElement("gmd:codeSpace");
	$codeSpace_cs=$iso19139->createElement("gco:CharacterString");
	$codeSpaceText=$iso19139->createTextNode("http://www.geoportal.rlp.de"); //in case of the mapbender addon!

	$code_cs->appendChild($codeText);
	$code->appendChild($code_cs);
	$rs_identifier->appendChild($code);

	$codeSpace_cs->appendChild($codeSpaceText);
	$codeSpace->appendChild($codeSpace_cs);
	$rs_identifier->appendChild($codeSpace);



	$identifier->appendChild($rs_identifier);
	$CI_Citation->appendChild($identifier);

	$citation->appendChild($CI_Citation);
	$MD_DataIdentification->appendChild($citation);

	#Create part for abstract B 1.2
	$abstract=$iso19139->createElement("gmd:abstract");
	$abstract_cs=$iso19139->createElement("gco:CharacterString");
	if (isset($mb_metadata['abstract'])) {
		$abstractText = $iso19139->createTextNode($mb_metadata['abstract']);
	}
	else {
		$abstractText = $iso19139->createTextNode("not yet defined");
	}
	$abstract_cs->appendChild($abstractText);
	$abstract->appendChild($abstract_cs);
	$MD_DataIdentification->appendChild($abstract);

	#Create part for point of contact for data identification - use contact from service provider! 
	#Define relevant objects
	$pointOfContact=$iso19139->createElement("gmd:pointOfContact");
	$CI_ResponsibleParty=$iso19139->createElement("gmd:CI_ResponsibleParty");
	$organisationName=$iso19139->createElement("gmd:organisationName");
	$orgaName_cs=$iso19139->createElement("gco:CharacterString");
	$contactInfo=$iso19139->createElement("gmd:contactInfo");
	$CI_Contact=$iso19139->createElement("gmd:CI_Contact");
	$address_1=$iso19139->createElement("gmd:address");
	$CI_Address=$iso19139->createElement("gmd:CI_Address");
	$electronicMailAddress=$iso19139->createElement("gmd:electronicMailAddress");
	$email_cs=$iso19139->createElement("gco:CharacterString");
	$role=$iso19139->createElement("gmd:role");
	$CI_RoleCode=$iso19139->createElement("gmd:CI_RoleCode");
	$CI_RoleCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_RoleCode");
	$CI_RoleCode->setAttribute("codeListValue", "publisher");
	if (isset($mapbenderMetadata['contactorganization'])) {
		$resOrgaText = $iso19139->createTextNode($mapbenderMetadata['contactorganization']);
	}
	else {
		$resOrgaText= $iso19139->createTextNode("not yet defined");
	}
	if (isset($mapbenderMetadata['contactelectronicmailaddress']) && $mapbenderMetadata['contactelectronicmailaddress'] != '') {
		$resMailText = $iso19139->createTextNode($mapbenderMetadata['contactelectronicmailaddress']);
	}
	else {
		$resMailText = $iso19139->createTextNode("kontakt@geoportal.rlp.de");
	}
	$resRoleText = $iso19139->createTextNode("publisher");
	$orgaName_cs->appendChild($resOrgaText);
	$organisationName->appendChild($orgaName_cs);
	$CI_ResponsibleParty->appendChild($organisationName);
	$email_cs->appendChild($resMailText);
	$electronicMailAddress->appendChild($email_cs);
	$CI_Address->appendChild($electronicMailAddress);
	$address_1->appendChild($CI_Address);
	$CI_Contact->appendChild($address_1);
	$contactInfo->appendChild($CI_Contact);
	$CI_ResponsibleParty->appendChild($contactInfo);
	$CI_RoleCode->appendChild($resRoleText);
	$role->appendChild($CI_RoleCode);
	$CI_ResponsibleParty->appendChild($role);
	$pointOfContact->appendChild($CI_ResponsibleParty);
	$MD_DataIdentification->appendChild($pointOfContact);
	//Part for maintenance if given
	/* example
<gmd:resourceMaintenance>
	<gmd:MD_MaintenanceInformation>
		<gmd:maintenanceAndUpdateFrequency>
			<gmd:MD_MaintenanceFrequencyCode codeListValue="asNeeded" codeList="http://www.isotc211.org/2005/resources/codeList.xml#MD_MaintenanceFrequencyCode"/>
		</gmd:maintenanceAndUpdateFrequency>
	</gmd:MD_MaintenanceInformation>
</gmd:resourceMaintenance>
*/
	if (isset($mb_metadata['update_frequency']) && $mb_metadata['update_frequency'] != "") {
		$resourceMaintenance=$iso19139->createElement("gmd:resourceMaintenance");
		$MD_MaintenanceInformation=$iso19139->createElement("gmd:MD_MaintenanceInformation");
		$maintenanceAndUpdateFrequency=$iso19139->createElement("gmd:maintenanceAndUpdateFrequency");
		$MD_MaintenanceFrequencyCode=$iso19139->createElement("gmd:MD_MaintenanceFrequencyCode");
		$MD_MaintenanceFrequencyCode->setAttribute("codeListValue", $mb_metadata['update_frequency']);
		$MD_MaintenanceFrequencyCode->setAttribute("codeList", "http://www.isotc211.org/2005/resources/codeList.xml#MD_MaintenanceFrequencyCode");
		$maintenanceAndUpdateFrequency->appendChild($MD_MaintenanceFrequencyCode);
		$MD_MaintenanceInformation->appendChild($maintenanceAndUpdateFrequency);
		$resourceMaintenance->appendChild($MD_MaintenanceInformation);
		$MD_DataIdentification->appendChild($resourceMaintenance);
		
	}
	//generate keyword part - for services the inspire themes are not applicable!!!**********
	//read keywords for resource out of the database:
	$sql = "SELECT keyword.keyword FROM keyword, layer_keyword WHERE layer_keyword.fkey_layer_id=$1 AND layer_keyword.fkey_keyword_id=keyword.keyword_id";
	$v = array((integer)$layerId);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	$descriptiveKeywords=$iso19139->createElement("gmd:descriptiveKeywords");
	$MD_Keywords=$iso19139->createElement("gmd:MD_Keywords");
	//push in keywords without thesaurus
	while ($row = db_fetch_array($res)) {
		$keyword=$iso19139->createElement("gmd:keyword");
		$keyword_cs=$iso19139->createElement("gco:CharacterString");
		$keywordText = $iso19139->createTextNode($row['keyword']);
		$keyword_cs->appendChild($keywordText);
		$keyword->appendChild($keyword_cs);
		$MD_Keywords->appendChild($keyword);
	}
	//pull special keywords from custom categories:
	$e = new mb_notice("layer: ".$layerId);	

	$sql = "SELECT custom_category.custom_category_key FROM custom_category, layer_custom_category WHERE layer_custom_category.fkey_layer_id = $1 AND layer_custom_category.fkey_custom_category_id =  custom_category.custom_category_id AND custom_category_hidden = 0";
	$v = array((integer)$layerId);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	$e = new mb_notice("look for custom categories: ");
	$countCustom = 0;
	while ($row = db_fetch_array($res)) {
		$keyword=$iso19139->createElement("gmd:keyword");
		$keyword_cs=$iso19139->createElement("gco:CharacterString");
		$keywordText = $iso19139->createTextNode($row['custom_category_key']);
		$keyword_cs->appendChild($keywordText);
		$keyword->appendChild($keyword_cs);
		$MD_Keywords->appendChild($keyword);
		$countCustom++;
	}
	$e = new mb_notice("count custom categories: ".$countCustom);
	//close decriptive keywords and generate a new entry for inspire themes:
	$descriptiveKeywords->appendChild($MD_Keywords);
	$MD_DataIdentification->appendChild($descriptiveKeywords);
	//new entry - with gemet thesaurus referenced
	$descriptiveKeywords=$iso19139->createElement("gmd:descriptiveKeywords");
	$MD_Keywords=$iso19139->createElement("gmd:MD_Keywords");
	//read out the inspire categories and push them in as controlled keywords
/* example
<gmd:keyword><gco:CharacterString>Geographical names</gco:CharacterString></gmd:keyword><gmd:thesaurusName><gmd:CI_Citation><gmd:title><gco:CharacterString>GEMET - INSPIRE themes, version 1.0</gco:CharacterString></gmd:title><gmd:date><gmd:CI_Date><gmd:date><gco:Date>2008-06-01</gco:Date></gmd:date><gmd:dateType><gmd:CI_DateTypeCode codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_DateTypeCode" codeListValue="publication">publication</gmd:CI_DateTypeCode></gmd:dateType></gmd:CI_Date></gmd:date></gmd:CI_Citation></gmd:thesaurusName></gmd:MD_Keywords></gmd:descriptiveKeywords><gmd:descriptiveKeywords><gmd:MD_Keywords><gmd:keyword><gco:CharacterString>BasisDLM</gco:CharacterString></gmd:keyword>	
*/
	$sql = "SELECT inspire_category.inspire_category_code_en FROM inspire_category, layer_inspire_category WHERE layer_inspire_category.fkey_layer_id=$1 AND layer_inspire_category.fkey_inspire_category_id=inspire_category.inspire_category_id";
	$v = array((integer)$layerId);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	while ($row = db_fetch_array($res)) {

		//part for the name of the inspire category
		$keyword=$iso19139->createElement("gmd:keyword");
		$keyword_cs=$iso19139->createElement("gco:CharacterString");
		$keywordText = $iso19139->createTextNode($row['inspire_category_code_en']);
		$keyword_cs->appendChild($keywordText);
		$keyword->appendChild($keyword_cs);

		//part for the vocabulary
		$thesaurusName = $iso19139->createElement("gmd:thesaurusName");
		$CI_Citation = $iso19139->createElement("gmd:CI_Citation");
		$title = $iso19139->createElement("gmd:title");
		$title_cs = $iso19139->createElement("gco:CharacterString");
		$titleText = $iso19139->createTextNode("GEMET - INSPIRE themes, version 1.0");

		$title_cs->appendChild($titleText);
		$title->appendChild($title_cs);
		$CI_Citation->appendChild($title);

		$date1=$iso19139->createElement("gmd:date");
		$CI_Date=$iso19139->createElement("gmd:CI_Date");
		$date2=$iso19139->createElement("gmd:date");
		$gcoDate=$iso19139->createElement("gco:Date");
		$dateType=$iso19139->createElement("gmd:dateType");
		$dateTypeCode=$iso19139->createElement("gmd:CI_DateTypeCode");
		$dateTypeCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_DateTypeCode");
		$dateTypeCode->setAttribute("codeListValue", "publication");
		$dateTypeCodeText=$iso19139->createTextNode('publication');
		$dateText= $iso19139->createTextNode('2008-06-01');
		$dateTypeCode->appendChild($dateTypeCodeText);
		$dateType->appendChild($dateTypeCode);
		$gcoDate->appendChild($dateText);
		$date2->appendChild($gcoDate);
		$CI_Date->appendChild($date2);
		$CI_Date->appendChild($dateType);
		$date1->appendChild($CI_Date);

		$CI_Citation->appendChild($date1);
		$thesaurusName->appendChild($CI_Citation);

		$MD_Keywords->appendChild($keyword);
		$MD_Keywords->appendChild($thesaurusName);
	}

	//add the keyword part to the right leaf
	$descriptiveKeywords->appendChild($MD_Keywords);
	$MD_DataIdentification->appendChild($descriptiveKeywords);
	//End of keyword part **********

	//generate constraints part
	//generate service type part
	//generate extent part
	//generate coupling part
	//end of service identification
	//generate distribution part
	//generate data quality part
	#Part B 3 INSPIRE Category
	#do this only if an INSPIRE keyword (Annex I-III) is set
	#Resource Constraints B 8
	$resourceConstraints=$iso19139->createElement("gmd:resourceConstraints");
	$MD_LegalConstraints=$iso19139->createElement("gmd:MD_Constraints");
	$useLimitation=$iso19139->createElement("gmd:useLimitation");
	$useLimitation_cs=$iso19139->createElement("gco:CharacterString");
	if(isset($mapbenderMetadata['accessconstraints'])){
		$useLimitationText=$iso19139->createTextNode($mapbenderMetadata['accessconstraints']);
	}
	else
	{
		$useLimitationText=$iso19139->createTextNode("no conditions apply");
	}
 	//TODO: Mapping of constraints between OWS/registry and INSPIRE 
	$useLimitation_cs->appendChild($useLimitationText);
	$useLimitation->appendChild($useLimitation_cs);
	$MD_LegalConstraints->appendChild($useLimitation);
	$resourceConstraints->appendChild($MD_LegalConstraints);
	$MD_DataIdentification->appendChild($resourceConstraints);

	$resourceConstraints=$iso19139->createElement("gmd:resourceConstraints");
	$MD_LegalConstraints=$iso19139->createElement("gmd:MD_LegalConstraints");
	$accessConstraints=$iso19139->createElement("gmd:accessConstraints");
	$MD_RestrictionCode=$iso19139->createElement("gmd:MD_RestrictionCode");
	$MD_RestrictionCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_RetrictionCode");
	$MD_RestrictionCode->setAttribute("codeListValue", "otherRestrictions");
	$MD_RestrictionCodeText=$iso19139->createTextNode("otherRestrictions");
	$otherConstraints=$iso19139->createElement("gmd:otherConstraints");
	$otherConstraints_cs=$iso19139->createElement("gco:CharacterString");
	if (isset($mapbenderMetadata['accessconstraints']) & strtoupper($mapbenderMetadata['accessconstraints']) != 'NONE'){
			$otherConstraintsText=$iso19139->createTextNode($mapbenderMetadata['accessconstraints']);
	}
	else {
			$otherConstraintsText=$iso19139->createTextNode("no constraints");
	}
	$otherConstraints_cs->appendChild($otherConstraintsText);
	$otherConstraints->appendChild($otherConstraints_cs);

	$MD_RestrictionCode->appendChild($MD_RestrictionCodeText);
	$accessConstraints->appendChild($MD_RestrictionCode);
		
	$MD_LegalConstraints->appendChild($accessConstraints);
	$MD_LegalConstraints->appendChild($otherConstraints);
	$resourceConstraints->appendChild($MD_LegalConstraints);
		
	$MD_DataIdentification->appendChild($resourceConstraints);
	//Spatial Resolution
/* Example
<gmd:spatialResolution><gmd:MD_Resolution><gmd:distance><gco:Distance uom="m">3.0</gco:Distance></gmd:distance></gmd:MD_Resolution></gmd:spatialResolution>
*/
	$spatialResolution=$iso19139->createElement("spatialResolution");
	$MD_Resolution=$iso19139->createElement("gmd:MD_Resolution");
	
	if ($mb_metadata['spatial_res_type'] == 'groundDistance') {
		$distance = $iso19139->createElement("gmd:distance");
		$Distance = $iso19139->createElement("gmd:Distance");
		$Distance->setAttribute("uom", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/uom/ML_gmxUom.xml#m");
		$DistanceText=$iso19139->createTextNode($mb_metadata['spatial_res_value']);
		$Distance->appendChild($DistanceText);
		$distance->appendChild($Distance);
		$MD_Resolution->appendChild($distance);
	} else { //will be scaleDenominator
		/*<gmd:equivalentScale>
	<gmd:MD_RepresentativeFraction>
	<gmd:denominator>
	<gco:Integer>50000</gco:Integer>
	</gmd:denominator>
	</gmd:MD_RepresentativeFraction>
	</gmd:equivalentScale>*/
		$equivalentScale = $iso19139->createElement("gmd:equivalentScale");
		$MD_RepresentativeFraction = $iso19139->createElement("gmd:MD_RepresentativeFraction");
		$denominator = $iso19139->createElement("gmd:denominator");
		$Integer = $iso19139->createElement("gco:Integer");
		$IntegerText=$iso19139->createTextNode($mb_metadata['spatial_res_value']);
		$Integer->appendChild($IntegerText);
		$denominator->appendChild($Integer);
		$MD_RepresentativeFraction->appendChild($denominator);
		$equivalentScale->appendChild($MD_RepresentativeFraction);
		$MD_Resolution->appendChild($equivalentScale);
	}
	$spatialResolution->appendChild($MD_Resolution);
	$MD_DataIdentification->appendChild($spatialResolution);

	#Part B 1.7 Dataset Language
	$language=$iso19139->createElement("gmd:language");
	$LanguageCode=$iso19139->createElement("gmd:LanguageCode");
	$LanguageCodeText=$iso19139->createTextNode('ger');
	$LanguageCode->setAttribute("codeListValue", "ger");
	$LanguageCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#LanguageCode");
	$LanguageCode->appendChild($LanguageCodeText);

	$language->appendChild($LanguageCode);
	$MD_DataIdentification->appendChild($language);

/*
<gmd:topicCategory>
<gmd:MD_TopicCategoryCode>imageryBaseMapsEarthCover</gmd:MD_TopicCategoryCode>
</gmd:topicCategory>
*/
	#Topic Category B 2.1 - not needed for services

	//read keywords for resource out of the database:
	$sql = "SELECT md_topic_category.md_topic_category_code_en FROM md_topic_category, layer_md_topic_category WHERE layer_md_topic_category.fkey_layer_id = $1 AND layer_md_topic_category.fkey_md_topic_category_id=md_topic_category.md_topic_category_id";
	$v = array((integer)$layerId);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	$e = new mb_notice("look for topic: ");
	$countTopic = 0;
	while ($row = db_fetch_array($res)) {
		$e = new mb_exception("topic cat found!");
		$topicCategory=$iso19139->createElement("gmd:topicCategory");
		$MD_TopicCategoryCode=$iso19139->createElement("gmd:MD_TopicCategoryCode");
		$MD_TopicCategoryText=$iso19139->createTextNode($row['md_topic_category_code_en']);
		$MD_TopicCategoryCode->appendChild($MD_TopicCategoryText);
		$topicCategory->appendChild($MD_TopicCategoryCode);
		$MD_DataIdentification->appendChild($topicCategory);
		$countTopic++;
	}
	$e = new mb_exception("count topic: ".$countTopic);
	if ($countTopic == 0) {
		$e = new mb_exception("no topic cat found!");
		$topicCategory=$iso19139->createElement("gmd:topicCategory");
		$MD_TopicCategoryCode=$iso19139->createElement("gmd:MD_TopicCategoryCode");
		$MD_TopicCategoryText=$iso19139->createTextNode("no category defined till now!");
		$MD_TopicCategoryCode->appendChild($MD_TopicCategoryText);
		$topicCategory->appendChild($MD_TopicCategoryCode);
		$MD_DataIdentification->appendChild($topicCategory);
	}

	#Geographical Extent
	$bbox = array();
	//initialize if no extent is defined in the database
	$bbox[0] = -180;
	$bbox[1] = -90;
	$bbox[2] = 180;
	$bbox[3] = 90;
	if (isset($mapbenderMetadata['bbox']) & ($mapbenderMetadata['bbox'] != '')) {
		$bbox = explode(',',$mapbenderMetadata['bbox']);
	}
	$extent=$iso19139->createElement("gmd:extent");
	$EX_Extent=$iso19139->createElement("gmd:EX_Extent");
	$geographicElement=$iso19139->createElement("gmd:geographicElement");
	$EX_GeographicBoundingBox=$iso19139->createElement("gmd:EX_GeographicBoundingBox");

	$westBoundLongitude=$iso19139->createElement("gmd:westBoundLongitude");
	$wb_dec=$iso19139->createElement("gco:Decimal");
	$wb_text=$iso19139->createTextNode($bbox[0]);

	$eastBoundLongitude=$iso19139->createElement("gmd:eastBoundLongitude");
	$eb_dec=$iso19139->createElement("gco:Decimal");
	$eb_text=$iso19139->createTextNode($bbox[2]);

	$southBoundLatitude=$iso19139->createElement("gmd:southBoundLatitude");
	$sb_dec=$iso19139->createElement("gco:Decimal");
	$sb_text=$iso19139->createTextNode($bbox[1]);

	$northBoundLatitude=$iso19139->createElement("gmd:northBoundLatitude");
	$nb_dec=$iso19139->createElement("gco:Decimal");
	$nb_text=$iso19139->createTextNode($bbox[3]);

	$wb_dec->appendChild($wb_text);
	$westBoundLongitude->appendChild($wb_dec);
	$EX_GeographicBoundingBox->appendChild($westBoundLongitude);

	$eb_dec->appendChild($eb_text);
	$eastBoundLongitude->appendChild($eb_dec);
	$EX_GeographicBoundingBox->appendChild($eastBoundLongitude);

	$sb_dec->appendChild($sb_text);
	$southBoundLatitude->appendChild($sb_dec);
	$EX_GeographicBoundingBox->appendChild($southBoundLatitude);

	$nb_dec->appendChild($nb_text);
	$northBoundLatitude->appendChild($nb_dec);
	$EX_GeographicBoundingBox->appendChild($northBoundLatitude);

	$geographicElement->appendChild($EX_GeographicBoundingBox);
	$EX_Extent->appendChild($geographicElement);
	$extent->appendChild($EX_Extent);

	$MD_DataIdentification->appendChild($extent);
	//generate temporal extent from begin and end positions
	/*
<gmd:extent>
<gmd:EX_Extent>
<gmd:temporalElement>
<gmd:EX_TemporalExtent>
<gmd:extent>
<gml:TimePeriod gml:id="extent">
<gml:beginPosition>1977-03-
10T11:45:30</gml:beginPosition>
<gml:endPosition>2005-01-
15T09:10:00</gml:endPosition>
</gml:TimePeriod>
</gmd:extent>
</gmd:EX_TemporalExtent>
</gmd:temporalElement>
</gmd:EX_Extent>
</gmd:extent>
*/
	//check if maintenance is set and adopt the last time - both times are always set, cause the editor demands this!!!!

	$extent=$iso19139->createElement("gmd:extent");
	$EX_Extent=$iso19139->createElement("gmd:EX_Extent");
	$temporalElement=$iso19139->createElement("gmd:temporalElement");
	$EX_TemporalExtent=$iso19139->createElement("gmd:EX_TemporalExtent");
	$extent2=$iso19139->createElement("gmd:extent");
	$TimePeriod=$iso19139->createElement("gml:TimePeriod");
	$TimePeriod->setAttribute("gml:id", "temporalextent"); //maybe exchange thru uuid?
	$beginPosition=$iso19139->createElement("gml:beginPosition");
	$beginPositionText=$iso19139->createTextNode($beginPositionValue);
	$endPosition=$iso19139->createElement("gml:endPosition");
	$endPositionText=$iso19139->createTextNode($endPositionValue);
	//generate xml

	$endPosition->appendChild($endPositionText);
	$beginPosition->appendChild($beginPositionText);
	$TimePeriod->appendChild($beginPosition);
	$TimePeriod->appendChild($endPosition);
	$extent2->appendChild($TimePeriod);
	$EX_TemporalExtent->appendChild($extent2);
	$temporalElement->appendChild($EX_TemporalExtent);
	$EX_Extent->appendChild($temporalElement);
	$extent->appendChild($EX_Extent);
	$MD_DataIdentification->appendChild($extent);


	

//reference system
/*
https://geo-ide.noaa.gov/wiki/index.php?title=ISO_Boilerplate
<gmd:referenceSystemInfo>
    <gmd:MD_ReferenceSystem>
        <gmd:referenceSystemIdentifier>
            <gmd:RS_Identifier>
                <gmd:authority>
                    <gmd:CI_Citation>
                        <gmd:title>
                            <gco:CharacterString>European Petroleum Survey Group (EPSG) Geodetic Parameter Registry</gco:CharacterString>
                        </gmd:title>
                        <gmd:date>
                            <gmd:CI_Date>
                                <gmd:date>
                                    <gco:Date>2008-11-12</gco:Date>
                                </gmd:date>
                                <gmd:dateType>
                                    <gmd:CI_DateTypeCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#CI_DateTypeCode" codeListValue="publication">publication</gmd:CI_DateTypeCode>
                                </gmd:dateType>
                            </gmd:CI_Date>
                        </gmd:date>
                        <gmd:citedResponsibleParty>
                            <gmd:CI_ResponsibleParty>
                                <gmd:organisationName>
                                    <gco:CharacterString>European Petroleum Survey Group</gco:CharacterString>
                                </gmd:organisationName>
                                <gmd:contactInfo>
                                    <gmd:CI_Contact>
                                        <gmd:onlineResource>
                                            <gmd:CI_OnlineResource>
                                                <gmd:linkage>
                                                    <gmd:URL>http://www.epsg-registry.org/</gmd:URL>
                                                </gmd:linkage>
                                            </gmd:CI_OnlineResource>
                                        </gmd:onlineResource>
                                    </gmd:CI_Contact>
                                </gmd:contactInfo>
                                <gmd:role gco:nilReason="missing"/>
                            </gmd:CI_ResponsibleParty>
                        </gmd:citedResponsibleParty>                            
                    </gmd:CI_Citation>
                </gmd:authority>
                <gmd:code>
                    <gco:CharacterString>urn:ogc:def:crs:EPSG:4326</gco:CharacterString>
                </gmd:code>
                <gmd:version>
                    <gco:CharacterString>6.18.3</gco:CharacterString>
                </gmd:version>
            </gmd:RS_Identifier>
        </gmd:referenceSystemIdentifier>
    </gmd:MD_ReferenceSystem>
</gmd:referenceSystemInfo>
*/

	$gmd_referenceSystemInfo=$iso19139->createElement("gmd:referenceSystemInfo");
	$gmd_MD_ReferenceSystem=$iso19139->createElement("gmd:MD_ReferenceSystem");
	$gmd_referenceSystemIdentifier=$iso19139->createElement("gmd:referenceSystemIdentifier");
	$gmd_RS_Identifier=$iso19139->createElement("gmd:RS_Identifier");
	$gmd_authority=$iso19139->createElement("gmd:authority");
	$gmd_CI_Citation=$iso19139->createElement("gmd:CI_Citation");
	$gmd_title=$iso19139->createElement("gmd:title");
	$gmd_title_cs=$iso19139->createElement("gco:CharacterString");
	$gmd_title_Text=$iso19139->createTextNode("European Petroleum Survey Group (EPSG) Geodetic Parameter Registry");

	$gmd_title_cs->appendChild($gmd_title_Text);
	$gmd_title->appendChild($gmd_title_cs);
	$gmd_CI_Citation->appendChild($gmd_title);

	$gmd_date=$iso19139->createElement("gmd:date");
	$gmd_CI_Date=$iso19139->createElement("gmd:CI_Date");
	$gmd_date2=$iso19139->createElement("gmd:date");
	$gco_Date=$iso19139->createElement("gco:Date");
	$gmd_dateType=$iso19139->createElement("gmd:dateType");
	$gmd_CI_DateTypeCode=$iso19139->createElement("gmd:CI_DateTypeCode");
	$gmd_CI_DateTypeCode_Text=$iso19139->createTextNode("publication");
	$gmd_CI_DateTypeCode->setAttribute("codeList", "http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#CI_DateTypeCode");
	$gmd_CI_DateTypeCode->setAttribute("codeListValue", "publication");

	$gmd_CI_DateTypeCode->appendChild($gmd_CI_DateTypeCode_Text);
	$gmd_dateType->appendChild($gmd_CI_DateTypeCode);
	$gco_Date->appendChild($gmd_dateType);
	$gmd_date2->appendChild($gco_Date);
	$gmd_CI_Date->appendChild($gmd_date2);
	$gmd_date->appendChild($gmd_CI_Date);
	$gmd_CI_Citation->appendChild($gmd_date);

	$gmd_citedResponsibleParty=$iso19139->createElement("gmd:citedResponsibleParty");
	$gmd_CI_ResponsibleParty=$iso19139->createElement("gmd:CI_ResponsibleParty");
	$gmd_organisationName=$iso19139->createElement("gmd:organisationName");
	$gmd_organisationName_cs=$iso19139->createElement("gco:CharacterString");
	$gmd_organisationName_Text=$iso19139->createTextNode("European Petroleum Survey Group");

	$gmd_organisationName_cs->appendChild($gmd_organisationName_Text);
	$gmd_organisationName->appendChild($gmd_organisationName_cs);
	$gmd_CI_ResponsibleParty->appendChild($gmd_organisationName);
	



	$gmd_contactInfo=$iso19139->createElement("gmd:contactInfo");
	$gmd_CI_Contact=$iso19139->createElement("gmd:CI_Contact");
	$gmd_onlineResource=$iso19139->createElement("gmd:onlineResource");
	$gmd_CI_OnlineResource=$iso19139->createElement("gmd:CI_OnlineResource");
	$gmd_linkage=$iso19139->createElement("gmd:linkage");
	$gmd_URL=$iso19139->createElement("gmd:URL");
	$gmd_URL_Text=$iso19139->createTextNode("http://www.epsg-registry.org/");
	
	$gmd_URL->appendChild($gmd_URL_Text);
	$gmd_linkage->appendChild($gmd_URL);
	$gmd_CI_OnlineResource->appendChild($gmd_linkage);
	$gmd_onlineResource->appendChild($gmd_CI_OnlineResource);
	$gmd_CI_Contact->appendChild($gmd_onlineResource);
	$gmd_contactInfo->appendChild($gmd_CI_Contact);

	$gmd_CI_ResponsibleParty->appendChild($gmd_contactInfo);

	$gmd_role=$iso19139->createElement("gmd:role");
	$gmd_role->setAttribute("gco:nilReason", "missing");
	
	$gmd_CI_ResponsibleParty->appendChild($gmd_role);

	$gmd_citedResponsibleParty->appendChild($gmd_CI_ResponsibleParty);
	$gmd_CI_Citation->appendChild($gmd_citedResponsibleParty);
	$gmd_authority->appendChild($gmd_CI_Citation);

	$gmd_RS_Identifier->appendChild($gmd_authority);

	$gmd_code=$iso19139->createElement("gmd:code");
	$gmd_code_cs=$iso19139->createElement("gco:CharacterString");
	$gmd_code_text=$iso19139->createTextNode("urn:ogc:def:crs:".$mb_metadata['ref_system']);
	
	$gmd_code_cs->appendChild($gmd_code_text);
	$gmd_code->appendChild($gmd_code_cs);
	$gmd_RS_Identifier->appendChild($gmd_code);

	$gmd_version=$iso19139->createElement("gmd:version");
	$gmd_version_cs=$iso19139->createElement("gco:CharacterString");
	$gmd_version_text=$iso19139->createTextNode("6.18.3");
	
	$gmd_version_cs->appendChild($gmd_version_text);
	$gmd_version->appendChild($gmd_version_cs);
	$gmd_RS_Identifier->appendChild($gmd_version);
	
	$gmd_referenceSystemIdentifier->appendChild($gmd_RS_Identifier);
	$gmd_MD_ReferenceSystem->appendChild($gmd_referenceSystemIdentifier);
	$gmd_referenceSystemInfo->appendChild($gmd_MD_ReferenceSystem);

	$MD_DataIdentification->appendChild($gmd_referenceSystemInfo);

	$identificationInfo->appendChild($MD_DataIdentification);


//distributionInfo
	$gmd_distributionInfo=$iso19139->createElement("gmd:distributionInfo");
	$MD_Distribution=$iso19139->createElement("gmd:MD_Distribution");
	$gmd_distributionFormat=$iso19139->createElement("gmd:distributionFormat");
	$MD_Format=$iso19139->createElement("gmd:MD_Format");
	$gmd_name=$iso19139->createElement("gmd:name");
	$MD_FormatName_cs=$iso19139->createElement("gco:CharacterString");	
	$MD_FormatNameText=$iso19139->createTextNode($mb_metadata['format']);

	$gmd_version=$iso19139->createElement("gmd:version");
	$MD_FormatVersion_cs=$iso19139->createElement("gco:CharacterString");
	$MD_FormatVersionText=$iso19139->createTextNode("unkown");

	$MD_FormatName_cs->appendChild($MD_FormatNameText);
	$MD_FormatVersion_cs->appendChild($MD_FormatVersionText);

	$gmd_name->appendChild($MD_FormatName_cs);
	$gmd_version->appendChild($MD_FormatVersion_cs);
	//$gmd_name->setAttribute("gco:nilReason", "inapplicable");
	//$gmd_version->setAttribute("gco:nilReason", "inapplicable");

	$gmd_transferOptions=$iso19139->createElement("gmd:transferOptions");
	$MD_DigitalTransferOptions=$iso19139->createElement("gmd:MD_DigitalTransferOptions");
	$gmd_onLine=$iso19139->createElement("gmd:onLine");

	$CI_OnlineResource=$iso19139->createElement("gmd:CI_OnlineResource");

	$gmd_linkage=$iso19139->createElement("gmd:linkage");
	$gmd_URL=$iso19139->createElement("gmd:URL");
	//Check if anonymous user has rights to access this layer - if not ? which resource should be advertised? TODO
	if ($hasPermission) {
		$gmd_URLText=$iso19139->createTextNode("http://".$_SERVER['HTTP_HOST']."/mapbender/php/wms.php?inspire=1&layer_id=".$mapbenderMetadata['layer_id']."");
	}
	else {
		$gmd_URLText=$iso19139->createTextNode("https://".$_SERVER['HTTP_HOST']."/http_auth/".$mapbenderMetadata['layer_id']."?");
	}
	$gmd_URL->appendChild($gmd_URLText);
	$gmd_linkage->appendChild($gmd_URL);
	$CI_OnlineResource->appendChild($gmd_linkage);	
//***********************************************************************************
	$gmd_onLine->appendChild($CI_OnlineResource);
	$MD_DigitalTransferOptions->appendChild($gmd_onLine);
	$gmd_transferOptions->appendChild($MD_DigitalTransferOptions);

	$MD_Format->appendChild($gmd_name);
	$MD_Format->appendChild($gmd_version);

	$gmd_distributionFormat->appendChild($MD_Format);

	$MD_Distribution->appendChild($gmd_distributionFormat);

	$MD_Distribution->appendChild($gmd_transferOptions);
	$gmd_distributionInfo->appendChild($MD_Distribution);
	//dataQualityInfo
	$gmd_dataQualityInfo=$iso19139->createElement("gmd:dataQualityInfo");
	$DQ_DataQuality=$iso19139->createElement("gmd:DQ_DataQuality");

	$gmd_scope=$iso19139->createElement("gmd:scope");
	$DQ_Scope=$iso19139->createElement("gmd:DQ_Scope");
	$gmd_level=$iso19139->createElement("gmd:level");
	$MD_ScopeCode=$iso19139->createElement("gmd:MD_ScopeCode");
	$MD_ScopeCodeText=$iso19139->createTextNode("dataset");
	$MD_ScopeCode->setAttribute("codeList", "http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_RestrictionCode");
	$MD_ScopeCode->setAttribute("codeListValue", "dataset");
	$MD_ScopeCode->appendChild($MD_ScopeCodeText);
	$gmd_level->appendChild($MD_ScopeCode);
	$DQ_Scope->appendChild($gmd_level);
	$gmd_scope->appendChild($DQ_Scope);
	$DQ_DataQuality->appendChild($gmd_scope);
/*
<gmd:lineage>
<gmd:LI_Lineage>
<gmd:statement>
<gco:CharacterString>test</gco:CharacterString>
</gmd:statement>
</gmd:LI_Lineage>
</gmd:lineage>
*/
	$lineage=$iso19139->createElement("gmd:lineage");
	$LI_Lineage=$iso19139->createElement("gmd:LI_Lineage");
	$statement=$iso19139->createElement("gmd:statement");
	$statement_cs=$iso19139->createElement("gco:CharacterString");
	$statementText=$iso19139->createTextNode($mb_metadata['lineage']);
	$lineage->appendChild($LI_Lineage);
	$LI_Lineage->appendChild($statement);
	$statement->appendChild($statement_cs);
	$statement_cs->appendChild($statementText);
	$DQ_DataQuality->appendChild($lineage);

	//gmd:report in dataQualityInfo
	$gmd_report=$iso19139->createElement("gmd:report");
	$DQ_DomainConsistency=$iso19139->createElement("gmd:DQ_DomainConsistency");
	$gmd_result=$iso19139->createElement("gmd:result");
	$DQ_ConformanceResult=$iso19139->createElement("gmd:DQ_ConformanceResult");
	$gmd_specification=$iso19139->createElement("gmd:specification");
	$CI_Citation=$iso19139->createElement("gmd:CI_Citation");
	$gmd_title=$iso19139->createElement("gmd:title");
	$gmd_title_cs=$iso19139->createElement("gco:CharacterString");
	$gmd_titleText=$iso19139->createTextNode("INSPIRE Metadata Regulation"); //TODO put in the inspire test suites from mb database!
	$gmd_date=$iso19139->createElement("gmd:date");
	$CI_Date=$iso19139->createElement("gmd:CI_Date");
	$gmd_date_2=$iso19139->createElement("gmd:date");
	$gco_Date=$iso19139->createElement("gco:Date");
	$gco_DateText=$iso19139->createTextNode("2008-12-03"); //TODO put in the info from database
	
	$gmd_dateType=$iso19139->createElement("gmd:dateType");
	$CI_DateTypeCode=$iso19139->createElement("gmd:CI_DateTypeCode");
	$CI_DateTypeCode->setAttribute("codeList","http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_DateTypeCode");
	$CI_DateTypeCode->setAttribute("codeListValue","publication");
	$CI_DateTypeCodeText=$iso19139->createTextNode("publication");
	
	$gmd_explanation=$iso19139->createElement("gmd:explanation");
	$gmd_explanation_cs=$iso19139->createElement("gco:CharacterString");
	$gmd_explanationText=$iso19139->createTextNode("No explanation available");
		
	$gmd_pass=$iso19139->createElement("gmd:pass");
	$gco_Boolean=$iso19139->createElement("gco:Boolean");
	$gco_BooleanText=$iso19139->createTextNode("true"); //TODO maybe set here a string cause it can be unevaluated!! See Implementing Rules
	//generate XML objects
	$gco_Date->appendChild($gco_DateText);
	$gmd_date->appendChild($gco_Date);
	
	$CI_DateTypeCode->appendChild($CI_DateTypeCodeText);
	$gmd_dateType->appendChild($CI_DateTypeCode);
	$CI_Date->appendChild($gmd_date);
	$CI_Date->appendChild($gmd_dateType);
	$gmd_date_2->appendChild($CI_Date);
	$gmd_title_cs->appendChild($gmd_titleText);
	$gmd_title->appendChild($gmd_title_cs);
	$CI_Citation->appendChild($gmd_title);
	$CI_Citation->appendChild($gmd_date_2);
	$gmd_specification->appendChild($CI_Citation);
	$gmd_explanation_cs->appendChild($gmd_explanationText);
	$gmd_explanation->appendChild($gmd_explanation_cs);
	$gco_Boolean->appendChild($gco_BooleanText);
	$gmd_pass->appendChild($gco_Boolean);
	$DQ_ConformanceResult->appendChild($gmd_specification);
	$DQ_ConformanceResult->appendChild($gmd_explanation);
	$DQ_ConformanceResult->appendChild($gmd_pass);
	$gmd_result->appendChild($DQ_ConformanceResult);
	$DQ_DomainConsistency->appendChild($gmd_result);
	$gmd_report->appendChild($DQ_DomainConsistency);
	$DQ_DataQuality->appendChild($gmd_report);

	$gmd_dataQualityInfo->appendChild($DQ_DataQuality);
	//$MD_ScopeCode->setAttribute("codeListValue", "service");
	$MD_Metadata->appendChild($identificationInfo);
	$MD_Metadata->appendChild($gmd_distributionInfo);
	$MD_Metadata->appendChild($gmd_dataQualityInfo);
	return $iso19139->saveXML();
}

	//function to validate against the inspire validation service
	function validateInspireMetadata($iso19139Doc, $recordId){
		$validatorUrl = 'http://www.inspire-geoportal.eu/INSPIREValidatorService/resources/validation/inspire';
		#$validatorUrl2 = 'http://localhost/mapbender/x_geoportal/log_requests.php';
		//send inspire xml to validator and push the result to requesting user
		$validatorInterfaceObject = new connector();
		$validatorInterfaceObject->set('httpType','POST');
		#$validatorInterfaceObject->set('httpContentType','application/xml');
		$validatorInterfaceObject->set('httpContentType','multipart/form-data'); # maybe given automatically
		$xml = fillISO19139($iso19139Doc, $recordId);
		//first test with data from ram - doesn't function 
		$fields = array(
			'dataFile'=>urlencode($xml)
			);
		//generate file identifier:
		$fileId = guid();
		//generate temporary file under tmp
		 if($h = fopen(TMPDIR."/".$fileId."iso19139_validate_tmp.xml","w")){
			if(!fwrite($h,$xml)){
				$e = new mb_exception("mod_layerISOMetadata: cannot write to file: ".TMPDIR."iso19139_validate_tmp.xml");
			}
		fclose($h);
		}
		//send file as post like described under http://www.tecbrat.com/?itemid=13&catid=1
		$fields['dataFile']='@'.TMPDIR.'/'.$fileId.'iso19139_validate_tmp.xml';
		#if we give a string with parameters
		#foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; } 
		#rtrim($fields_string,'&');
		#$postData = $fields_string;
		$postData = $fields;
		#$e = new mb_exception("mod_layerISOMetadata: postData: ".$postData['dataFile']);
		//number of post fields:
		//curl_setopt($ch,CURLOPT_POST,count($fields));
		$validatorInterfaceObject->set('httpPostFieldsNumber',count($postData));
		$validatorInterfaceObject->set('curlSendCustomHeaders',false);
		//$validatorInterfaceObject->set('httpPostData', $postData);
		$validatorInterfaceObject->set('httpPostData', $postData); #give an array
		$validatorInterfaceObject->load($validatorUrl);
		header("Content-type: text/html; charset=UTF-8");
		echo $validatorInterfaceObject->file;
		//delete file in tmp 
		//TODO - this normally done by a cronjob
		die();
	}

	//function to validate against the inspire validation service
	function validateInspireMetadataFromData($iso19139Xml){
		$validatorUrl = 'http://www.inspire-geoportal.eu/INSPIREValidatorService/resources/validation/inspire';
		#$validatorUrl2 = 'http://localhost/mapbender/x_geoportal/log_requests.php';
		//send inspire xml to validator and push the result to requesting user
		$validatorInterfaceObject = new connector();
		$validatorInterfaceObject->set('httpType','POST');
		#$validatorInterfaceObject->set('httpContentType','application/xml');
		$validatorInterfaceObject->set('httpContentType','multipart/form-data'); # maybe given automatically
		//first test with data from ram - doesn't function 
		$fields = array(
			'dataFile'=>urlencode($iso19139Xml)
			);
		//generate file identifier:
		$fileId = guid();
		//generate temporary file under tmp
		 if($h = fopen(TMPDIR."/".$fileId."iso19139_validate_tmp.xml","w")){
			if(!fwrite($h,$iso19139Xml)){
				$e = new mb_exception("mod_layerISOMetadata: cannot write to file: ".TMPDIR."iso19139_validate_tmp.xml");
			}
		fclose($h);
		}
		//send file as post like described under http://www.tecbrat.com/?itemid=13&catid=1
		$fields['dataFile']='@'.TMPDIR.'/'.$fileId.'iso19139_validate_tmp.xml';
		#if we give a string with parameters
		#foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; } 
		#rtrim($fields_string,'&');
		#$postData = $fields_string;
		$postData = $fields;
		#$e = new mb_exception("mod_layerISOMetadata: postData: ".$postData['dataFile']);
		//number of post fields:
		//curl_setopt($ch,CURLOPT_POST,count($fields));
		$validatorInterfaceObject->set('httpPostFieldsNumber',count($postData));
		$validatorInterfaceObject->set('curlSendCustomHeaders',false);
		//$validatorInterfaceObject->set('httpPostData', $postData);
		$validatorInterfaceObject->set('httpPostData', $postData); #give an array
		$validatorInterfaceObject->load($validatorUrl);
		header("Content-type: text/html; charset=UTF-8");
		echo $validatorInterfaceObject->file;
		//delete file in tmp 
		//TODO - this normally done by a cronjob
		die();
	}

function getEpsgByLayerId ($layer_id) { // from merge_layer.php
	$epsg_list = "";
	$sql = "SELECT DISTINCT epsg FROM layer_epsg WHERE fkey_layer_id = $1";
	$v = array($layer_id);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	while($row = db_fetch_array($res)){
		$epsg_list .= $row['epsg'] . " ";
	}
	return trim($epsg_list);
}
function getEpsgArrayByLayerId ($layer_id) { // from merge_layer.php
	//$epsg_list = "";
	$epsg_array=array();
	$sql = "SELECT DISTINCT epsg FROM layer_epsg WHERE fkey_layer_id = $1";
	$v = array($layer_id);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	$cnt=0;
	while($row = db_fetch_array($res)){
		$epsg_array[$cnt] = $row['epsg'];
		$cnt++;
	}
	return $epsg_array;
}

function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
        return $uuid;
    }
}


?>

