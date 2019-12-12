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

/**
* class for wfs-objects
*/

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/class_connector.php");
require_once(dirname(__FILE__)."/class_administration.php");
require_once(dirname(__FILE__)."/class_ows.php");

abstract class Wfs extends Ows {
}

class wfs {

  var $wfs_id;
  var $wfs_version;
  var $wfs_name;
  var $wfs_title;
  var $wfs_abstract;
  var $wfs_getcapabilities;
  var $wfs_getcapabilities_doc; //new SB 2007-08-09
  var $wfs_describefeaturetype;
  var $wfs_describefeaturetype_namespace = array();
  var $wfs_getfeature;
  var $wfs_transaction;
  var $wfs_upload_url; //new SB 2007-08-09

  //new WFS 1.0.0 -- SB 2007-08-06

  var $fees;
  var $accessconstraints;
  
  var $wfs_featuretype = array();
 
 
function wfs() {

} 
/*
function createObjFromXML($url){
	
	$x = new connector($url);
	$data = $x->file;
	#$data = implode("",file($url));
	if(!$data){
		echo "Unable to open document: ".$url;
		die; 
	}
	
	$values = null;
	$tags = null;
	$admin = new administration();
	$this->wfs_getcapabilities_doc = $admin->char_encode($data);
	$this->wfs_upload_url = $url;
	
	# for temporary wfs a id has to be created...
	$this->wfs_id = "id_" . substr(md5(rand()),0,6);
	$parser = xml_parser_create(CHARSET);
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	xml_parser_set_option($parser,XML_OPTION_TARGET_ENCODING,CHARSET);
	xml_parse_into_struct($parser,$this->wfs_getcapabilities_doc,$values,$tags);
	//xml_parse_into_struct($parser,$data,$values,$tags);
	
	xml_parser_free($parser);
	
	$section = false;
	$request = false;
	$featuretype_name = false;
 	$featuretype_title = false;
 	$featuretype_abstract = false;
 	$featuretype_srs = false;
	
	foreach ($values as $element) {
	
		if(strtoupper($element[tag]) == "WFS_CAPABILITIES" && $element[type] == "open"){
			$this->wfs_version = $element[attributes][version];
		}
		if(strtoupper($element[tag]) == "NAME"  && $element[level] == '3'){
			$this->wfs_name = $element[value];
		}
		if(strtoupper($element[tag]) == "TITLE"  && $element[level] == '3'){
			$this->wfs_title = $this->stripEndlineAndCarriageReturn($element[value]);
		}
		if(strtoupper($element[tag]) == "ABSTRACT" && $element[level] == '3'){
			$this->wfs_abstract = $this->stripEndlineAndCarriageReturn($element[value]);
		}
		//new WFS 1.0.0 -- SB 2007-08-06
		if(strtolower($element[tag]) == "fees"){
			$this->fees = $element[value];
		}
		if(strtolower($element[tag]) == "accessconstraints"){
			$this->accessconstraints = $element[value];
		}
		
		
		if($this->wfs_version == "1.0.0"){
			
			# getCapabilities
			if(strtoupper($element[tag]) == "GETCAPABILITIES" && $element[type] == "open"){
				$section = "getcapabilities";
			}
			if($section == "getcapabilities" && strtoupper($element[tag]) == "POST"){
				$this->wfs_getcapabilities = $element[attributes][onlineResource];
			}
			
			# descriptFeatureType
			if(strtoupper($element[tag]) == "DESCRIBEFEATURETYPE" && $element[type] == "open"){
				$section = "describefeaturetype";
				$this->wfs_describefeaturetype = $element[attributes][onlineResource];
				
				
			}
			if($section == "describefeaturetype" && strtoupper($element[tag]) == "POST"){
				$this->wfs_describefeaturetype = $element[attributes][onlineResource];
			}
			
			# getFeature
			if(strtoupper($element[tag]) == "GETFEATURE" && $element[type] == "open"){
				$section = "getfeature";
			}
			if($section == "getfeature" && strtoupper($element[tag]) == "POST"){
				$this->wfs_getfeature = $element[attributes][onlineResource];
			}
			if(strtoupper($element[tag]) == "GETFEATURE" && $element[type] == "close"){
				$section = "";
			}			
			# transaction
			if(strtoupper($element[tag]) == "TRANSACTION" && $element[type] == "open"){
				$section = "transaction";
			}
			if($section == "transaction" && strtoupper($element[tag]) == "POST"){
				$this->wfs_transaction = $element[attributes][onlineResource];
			}
			if(strtoupper($element[tag]) == "TRANSACTION" && $element[type] == "close"){
				$section = "";
			}
		} 
		if(strtoupper($element[tag]) == "FEATURETYPE" && $element[type] == "open"){
			$section = "featuretype";
		}
		if($section == "featuretype" && strtoupper($element[tag]) == "NAME"){
			$featuretype_name = $element[value];
		}
		if($section == "featuretype" && strtoupper($element[tag]) == "TITLE"){
			$featuretype_title = $this->stripEndlineAndCarriageReturn($element[value]);
		}
		if($section == "featuretype" && strtoupper($element[tag]) == "ABSTRACT"){
			$featuretype_abstract = $this->$element[value];
		}
		if($section == "featuretype" && strtoupper($element[tag]) == "SRS"){
			$featuretype_srs = $element[value];
			$this->addFeaturetype($featuretype_name,$featuretype_title,$featuretype_abstract,$featuretype_srs,$this->wfs_describefeaturetype,$this->wfs_version);
		}
	}
}
*/
/*

function displayWFS(){
	echo "id: " . $this->wfs_id . " <br>";
	echo "version: " . $this->wfs_version . " <br>";
	echo "name: " . $this->wfs_name . " <br>";
	echo "title: " . $this->wfs_title . " <br>";
	echo "abstract: " . $this->wfs_abstract . " <br>";
	echo "capabilitiesrequest: " . $this->wfs_getcapabilities . " <br>";
	echo "describefeaturetype: " . $this->wfs_describefeaturetype . " <br>";
	echo "getfeature: " . $this->wfs_getfeature . " <br>";
	echo "transaction: " . $this->wfs_transaction . " <br>";
	for($i=0; $i<count($this->wfs_featuretype); $i++){
		echo "<hr>";
		echo "name: ". $this->wfs_featuretype[$i]->featuretype_name . "<br>";
		echo "title: ". $this->wfs_featuretype[$i]->featuretype_title . "<br>";
		echo "abstract: ". $this->wfs_featuretype[$i]->featuretype_abstract . "<br>";
		echo "srs: ". $this->wfs_featuretype[$i]->featuretype_srs . "<br>";
		for($j=0; $j<count($this->wfs_featuretype[$i]->featuretype_element);$j++){
			echo " element: " . $this->wfs_featuretype[$i]->featuretype_element[$j]["name"] ." - ".$this->wfs_featuretype[$i]->featuretype_element[$j]["type"]."<br>";
		}
		for($j=0; $j<count($this->wfs_featuretype[$i]->featuretype_namespace);$j++){
			echo " namespace: " . $this->wfs_featuretype[$i]->featuretype_namespace[$j]["name"] ." - ".$this->wfs_featuretype[$i]->featuretype_namespace[$j]["value"]."<br>";
		}
	}
} 
*/
/*
function addFeaturetype($name,$title,$abstract,$srs,$url,$version){
	$this->wfs_featuretype[count($this->wfs_featuretype)] = new featuretype($name,$title,$abstract,$srs,$url,$version);
}
*/
/*
 function stripEndlineAndCarriageReturn($string) {
	  	return preg_replace("/\n/", "", preg_replace("/\r/", " ", $string));
	  }
	 */
	/*
function createJsObjFromWFS($parent){
	if(!$this->wfs_title || $this->wfs_title == ""){
		echo "alert('Error: no valid capabilities-document !!');";
		die; exit;
	}
		if($parent){
			echo "parent.";
		}
		print("add_wfs('". 
		$this->wfs_id ."','".
		$this->wfs_version ."','".
		$this->wfs_title ."','".
		$this->wfs_abstract ."','". 
		$this->wfs_getcapabilities ."','" .
		$this->wfs_describefeaturetype ."');");
		

	for($i=0; $i<count($this->wfs_featuretype); $i++){
		if($parent){
			echo "parent.";
		}
		print ("wfs_add_featuretype('". 
			$this->wfs_featuretype[$i]->featuretype_name ."','". 
			$this->wfs_featuretype[$i]->featuretype_title . "','".
			$this->wfs_featuretype[$i]->featuretype_abstract . "','".  
			$this->wfs_featuretype[$i]->featuretype_srs ."','". 
			$this->wfs_featuretype[$i]->featuretype_geomtype ."');");
		for($j=0; $j<count($this->wfs_featuretype[$i]->featuretype_element);$j++){
			if($parent){
			echo "parent.";
			}
			print("wfs_add_featuretype_element('".$this->wfs_featuretype[$i]->featuretype_element[$j]["name"]."', '".$this->wfs_featuretype[$i]->featuretype_element[$j]["type"]."', ".$j.", ".$i.");");
		}
		for($j=0; $j<count($this->wfs_featuretype[$i]->featuretype_namespace);$j++){
			if($parent){
			echo "parent.";
			}
			print("wfs_add_featuretype_namespace('".$this->wfs_featuretype[$i]->featuretype_namespace[$j]["name"]."', '".$this->wfs_featuretype[$i]->featuretype_namespace[$j]["value"]."', ".$j.", ".$i.");");
		}
	}
}
*/
/**
 * Inserts this WFS in the database
 */
/*
function insertWfs() {
	global $DBSERVER,$DB,$OWNER,$PW;
	$con = db_connect($DBSERVER,$OWNER,$PW);
	db_select_db($DB,$con);

	$sql = "INSERT INTO wfs (wfs_version, wfs_name, wfs_title, wfs_abstract, ";
	$sql .= "wfs_getcapabilities, wfs_getcapabilities_doc, wfs_upload_url, ";
	$sql .= "wfs_describefeaturetype, wfs_getfeature, wfs_transaction, fees, ";
	$sql .= "accessconstraints, wfs_owner, wfs_timestamp) ";
	$sql .= "VALUES($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14)";

	$v = array($this->wfs_version, $this->wfs_name, $this->wfs_title, 
		$this->wfs_abstract, $this->wfs_getcapabilities, $this->wfs_getcapabilities_doc,
		$this->wfs_upload_url, $this->wfs_describefeaturetype, $this->wfs_getfeature,
		$this->wfs_transaction, $this->fees, $this->accessconstraints, 
		Mapbender::session()->get("mb_user_id"), strtotime("now"));
		
	$t = array('s', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 'i', 'i');

	$res = db_prep_query($sql,$v,$t);

	if(!$res){
		db_rollback();
		$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");
		return false;
	}
	$this->wfs_id = db_insert_id($con,'wfs','wfs_id');
	return true;
}
*/
/*
function updateWfs() {
	global $DBSERVER,$DB,$OWNER,$PW;
	$con = db_connect($DBSERVER,$OWNER,$PW);
	db_select_db($DB,$con);

	$sql = "UPDATE wfs SET wfs_version = $1, wfs_name = $2, wfs_title = $3, ";
	$sql .= "wfs_abstract = $4, wfs_getcapabilities = $5, wfs_getcapabilities_doc = $6, ";
	$sql .= "wfs_upload_url = $7, wfs_describefeaturetype = $8, wfs_getfeature = $9, ";
	$sql .= "wfs_transaction = $10, fees = $11, accessconstraints = $12, wfs_owner = $13, ";
	$sql .= "wfs_timestamp = $14 ";
	$sql .= "WHERE wfs_id = $15";
	//echo $sql."<br />";
	$v = array($this->wfs_version, $this->wfs_name, $this->wfs_title, $this->wfs_abstract,
		$this->wfs_getcapabilities, $this->wfs_getcapabilities_doc, $this->wfs_upload_url,
		$this->wfs_describefeaturetype, $this->wfs_getfeature, $this->wfs_transaction,
		$this->fees, $this->accessconstraints, Mapbender::session()->get("mb_user_id"),strtotime("now"), 
		$this->wfs_id);
		
	$t = array('s', 's', 's', 's', 's', 's', 's', 's' ,'s' ,'s' ,'s' ,'s' ,'i' ,'i' ,'i');
	$res = db_prep_query($sql,$v,$t);
	if(!$res){
		db_rollback();
		$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");
		return false;
	}
	return true;
}
*/
/**
* wfs2db
*
* this function saves the class information to the mapbender database 
* @return boolean true if sucessful false otherwise
*/


/*
function wfs2db($gui_id){
	global $DBSERVER,$DB,$OWNER,$PW;
	$con = db_connect($DBSERVER,$OWNER,$PW);
	db_select_db($DB,$con);
	
	db_begin();
	
	// check if WFS already might exists (it might exist when wfs_id is numeric)
	$wfs_exists = is_numeric($this->wfs_id);

	// if it might exist, update it
	if ($wfs_exists) {
		// but check first if it really exists in the database
		$sql = "SELECT * FROM wfs WHERE wfs_id = $1;";
		$v = array($this->wfs_id);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);	
		if($res){
			$this->updateWfs();
		}
		// if not, insert as new WFS
		else {
			$this->insertWfs();
		}
	}
	// if it does not exist, insert as new WFS
	else {
		$this->insertWfs();
	}

	# delete featuretypes from DB that does not exist and find the ids
	$sql = "SELECT * FROM wfs_featuretype WHERE fkey_wfs_id = $1;";
	$v = array($this->wfs_id);
	$t = array("i");
	$res_ft = db_prep_query($sql, $v, $t);
	$cnt = 0;
	while(db_fetch_row($res_ft)){
		$found = false;
		for($i=0; $i<count($this->wfs_featuretype); $i++){
			if($this->wfs_featuretype[$i]->featuretype_name == db_result($res_ft, $cnt, "featuretype_name")){
				$this->wfs_featuretype[$i]->featuretype_id = db_result($res_ft, $cnt, "featuretype_id");
				$found = true;
				break;
			}
		}
		
		if(!$found){
			$sql = "DELETE FROM wfs_featuretype WHERE featuretype_id = $1 AND fkey_wfs_id = $2";
			$v = array(db_result($res_ft, $cnt, "featuretype_id"), $this->wfs_id);
			$t = array('i','i');
			//echo $sql."<br />";
			$res = db_prep_query($sql,$v,$t);
			if(!$res){db_rollback();$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");return;}
		}
		$cnt++;
	}
	# TABLE wfs_featuretype
	
	for($i=0; $i<count($this->wfs_featuretype); $i++){
		if(!$this->wfs_featuretype[$i]->featuretype_id){
			$sql = "INSERT INTO wfs_featuretype(fkey_wfs_id, featuretype_name, featuretype_title, featuretype_abstract, featuretype_srs) ";
			$sql .= "VALUES($1,$2,$3,$4,$5)";
			$v = array($this->wfs_id,$this->wfs_featuretype[$i]->featuretype_name,$this->wfs_featuretype[$i]->featuretype_title,$this->wfs_featuretype[$i]->featuretype_abstract,$this->wfs_featuretype[$i]->featuretype_srs);
			$t = array('i','s','s','s','s');
			//echo $sql."<br />";
			$res = db_prep_query($sql,$v,$t);
			if(!$res){db_rollback();$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");return;}
			
			# save the id of each featuretype: 
			$this->wfs_featuretype[$i]->featuretype_id = db_insert_id($con,'wfs_featuretype','featuretype_id');
		}
		else{
			$sql = "UPDATE wfs_featuretype SET ";
			$sql .= "featuretype_title = $1,";
			$sql .= "featuretype_abstract = $2,";
			$sql .= "featuretype_srs = $3 ";
			$sql .= "WHERE featuretype_id = $4";
			$v = array($this->wfs_featuretype[$i]->featuretype_title,$this->wfs_featuretype[$i]->featuretype_abstract,$this->wfs_featuretype[$i]->featuretype_srs,$this->wfs_featuretype[$i]->featuretype_id);
			$t = array('s','s','s','i');
			//echo $sql."<br />";
			$res = db_prep_query($sql,$v,$t);
			if(!$res){db_rollback();$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");return;}
		}
		//delete featuretype elements from db and find the ids
		$sql = "SELECT * FROM wfs_element WHERE fkey_featuretype_id = $1;";
		$v = array($this->wfs_featuretype[$i]->featuretype_id);
		$t = array("i");
		$res_el = db_prep_query($sql, $v, $t);
		$cnt = 0;
		while(db_fetch_row($res_el)){
			$found = false;
			for($j=0; $j<count($this->wfs_featuretype[$i]->featuretype_element); $j++){
				if($this->wfs_featuretype[$i]->featuretype_element[$j]["name"] == db_result($res_el, $cnt, "element_name")){
					$this->wfs_featuretype[$i]->featuretype_element[$j]["id"] = db_result($res_el, $cnt, "element_id");
					$found = true;
					break;
				}
			}
			
			if(!$found){
				$sql = "DELETE FROM wfs_element WHERE element_id = $1 AND fkey_featuretype_id = $2";
				$v = array(db_result($res_el, $cnt, "element_id"), $this->wfs_featuretype[$i]->featuretype_id);
				$t = array('i','i');
				//echo $sql."<br />";
				$res = db_prep_query($sql,$v,$t);
				if(!$res){db_rollback();$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");return;}
			}
			$cnt++;
		}

		for($j=0; $j<count($this->wfs_featuretype[$i]->featuretype_element);$j++){
			if(!$this->wfs_featuretype[$i]->featuretype_element[$j]["id"]){
				$sql = "INSERT INTO wfs_element(fkey_featuretype_id, element_name,element_type) ";
				$sql .= "VALUES($1, $2, $3);";
				
				$v = array($this->wfs_featuretype[$i]->featuretype_id, $this->wfs_featuretype[$i]->featuretype_element[$j]["name"], $this->wfs_featuretype[$i]->featuretype_element[$j]["type"]);
				$t = array("s", "s", "s");
				$res = db_prep_query($sql, $v, $t);
				if(!$res){db_rollback();$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");return;}
			}
			else{
				$sql = "UPDATE wfs_element SET element_type = $1 ";
				$sql .= "WHERE element_id = $2 AND ";
				$sql .= "fkey_featuretype_id = $3;";
				$v = array($this->wfs_featuretype[$i]->featuretype_element[$j]["type"], $this->wfs_featuretype[$i]->featuretype_element[$j]["id"], $this->wfs_featuretype[$i]->featuretype_id);
				$t = array("s", "i", "i");
				$res = db_prep_query($sql, $v, $t);
				if (!$res) {
					db_rollback();
					$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");
					return;
				}
			}
		}

		$sql = "DELETE FROM wfs_featuretype_namespace WHERE ";
		$sql .= "fkey_wfs_id = $1 AND fkey_featuretype_id = $2";
		$v = array($this->wfs_id, $this->wfs_featuretype[$i]->featuretype_id);
		$t = array("i", "i");
		$res = db_prep_query($sql, $v, $t);
		if (!$res) {
			db_rollback();
			$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");
			return;
		}
				
		for($j=0; $j<count($this->wfs_featuretype[$i]->featuretype_namespace);$j++){
			$sql = "INSERT INTO wfs_featuretype_namespace ";
			$sql .= "(fkey_wfs_id, fkey_featuretype_id, namespace, namespace_location) ";
			$sql .= "VALUES ($1, $2, $3, $4);"; 

			$v = array($this->wfs_id, $this->wfs_featuretype[$i]->featuretype_id, $this->wfs_featuretype[$i]->featuretype_namespace[$j]["name"], $this->wfs_featuretype[$i]->featuretype_namespace[$j]["value"]);
			$t = array("s", "s", "s", "s");
			$res = db_prep_query($sql, $v, $t);

			if (!$res) {
				db_rollback();
				$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");
				return;
			}
		}
	}
	
	# TABLE gui_wfs
	/*
	if($gui_id){
		$sql ="INSERT INTO gui_wfs (fkey_gui_id, fkey_wfs_id)";
		$sql .= "VALUES($1, $2);";
		$v = array($gui_id, $this->wfs_id);
		$t = array("s", "i");
		$res = db_prep_query($sql, $v, $t);

		if (!$res) {
			db_rollback();
			$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");
			return;
		}
	}
	//echo "Successfully saved!<br />";
	db_commit();
	
	return true;
}
*/

/**
* updateObjFromXML
*
* this function regets the information from the xml (update)
* @return boolean true if successful, false otherwise
*/
function updateObjFromXML($url){
	$x = new connector($url);
	$data = $x->file;
	#$data = implode("",file($url));
	if(!$data){
		$e = new mb_exception("Unable to open document: ".$url);
		return false; 
	}
	
	$values = null;
	$tags = null;
	$admin = new administration();
	$this->wfs_getcapabilities_doc = $admin->char_encode($data);
	$this->wfs_featuretype = array();
	$this->wfs_upload_url = $url;
	
	# for temporary wfs a id has to be created...
	//$this->wfs_id = "id_" . substr(md5(rand()),0,6);
	$parser = xml_parser_create(CHARSET);
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	xml_parser_set_option($parser,XML_OPTION_TARGET_ENCODING,CHARSET);
	xml_parse_into_struct($parser,$this->wfs_getcapabilities_doc,$values,$tags);
	//xml_parse_into_struct($parser,$data,$values,$tags);
	
	xml_parser_free($parser);
	
	$section = false;
	$request = false;
	$featuretype_name = false;
 	$featuretype_title = false;
 	$featuretype_abstract = false;
 	$featuretype_srs = false;
	
	foreach ($values as $element) {
	
		if(strtoupper($element[tag]) == "WFS_CAPABILITIES" && $element[type] == "open"){
			$this->wfs_version = $element[attributes][version];
		}
		if(strtoupper($element[tag]) == "NAME"  && $element[level] == '3'){
			$this->wfs_name = $element[value];
		}
/*		if(strtoupper($element[tag]) == "TITLE"  && $element[level] == '3'){
			$this->wfs_title = $this->stripEndlineAndCarriageReturn($element[value]);
		}
		if(strtoupper($element[tag]) == "ABSTRACT" && $element[level] == '3'){
			$this->wfs_abstract = $this->stripEndlineAndCarriageReturn($element[value]);
		}
		//new WFS 1.0.0 -- SB 2007-08-06
		if(strtolower($element[tag]) == "fees"){
			$this->fees = $element[value];
		}
		if(strtolower($element[tag]) == "accessconstraints"){
			$this->accessconstraints = $element[value];
		}
*/		
		/*capability section*/
		
		if($this->wfs_version == "1.0.0"){
			
			# getCapabilities
			if(strtoupper($element[tag]) == "GETCAPABILITIES" && $element[type] == "open"){
				$section = "getcapabilities";
			}
			if($section == "getcapabilities" && strtoupper($element[tag]) == "POST"){
				$this->wfs_getcapabilities = $element[attributes][onlineResource];
			}
			
			# descriptFeatureType
			if(strtoupper($element[tag]) == "DESCRIBEFEATURETYPE" && $element[type] == "open"){
				$section = "describefeaturetype";
				$this->wfs_describefeaturetype = $element[attributes][onlineResource];
				
				
			}
			if($section == "describefeaturetype" && strtoupper($element[tag]) == "POST"){
				$this->wfs_describefeaturetype = $element[attributes][onlineResource];
			}
			
			# getFeature
			if(strtoupper($element[tag]) == "GETFEATURE" && $element[type] == "open"){
				$section = "getfeature";
			}
			if($section == "getfeature" && strtoupper($element[tag]) == "POST"){
				$this->wfs_getfeature = $element[attributes][onlineResource];
			}
			if(strtoupper($element[tag]) == "GETFEATURE" && $element[type] == "close"){
				$section = "";
			}			
			# transaction
			if(strtoupper($element[tag]) == "TRANSACTION" && $element[type] == "open"){
				$section = "transaction";
			}
			if($section == "transaction" && strtoupper($element[tag]) == "POST"){
				$this->wfs_transaction = $element[attributes][onlineResource];
			}
			if(strtoupper($element[tag]) == "TRANSACTION" && $element[type] == "close"){
				$section = "";
			}
		} 
		if(strtoupper($element[tag]) == "FEATURETYPE" && $element[type] == "open"){
			$section = "featuretype";
		}
		if($section == "featuretype" && strtoupper($element[tag]) == "NAME"){
			$featuretype_name = $element[value];
		}
		if($section == "featuretype" && strtoupper($element[tag]) == "TITLE"){
			$featuretype_title = $this->stripEndlineAndCarriageReturn($element[value]);
		}
		if($section == "featuretype" && strtoupper($element[tag]) == "ABSTRACT"){
			$featuretype_abstract = $this->$element[value];
		}
		if($section == "featuretype" && strtoupper($element[tag]) == "SRS"){
			$featuretype_srs = $element[value];
			$this->addFeaturetype($featuretype_name,$featuretype_title,$featuretype_abstract,$featuretype_srs,$this->wfs_describefeaturetype,$this->wfs_version);
		}
	}
	return true;
}

/**
* creatObjfromDB
*
* this function fills the object with wfs information from db
* 
* @param int the id of wfs to get the information from
*/ 
/*
function createObjFromDB($wfs_id){
	global $DBSERVER,$DB,$OWNER,$PW;
	$con = db_connect($DBSERVER,$OWNER,$PW);
	db_select_db($DB,$con);

	$sql = "SELECT * FROM wfs WHERE wfs_id = $1;";
	$v = array($wfs_id);
	$t = array("i");
	$res = db_prep_query($sql, $v, $t);
	$cnt = 0;
	while(db_fetch_row($res)){
		$this->wfs_id = db_result($res, $cnt, "wfs_id");
		$this->wfs_version = db_result($res, $cnt, "wfs_version");
		$this->wfs_name = db_result($res, $cnt, "wfs_name");
		$this->wfs_title = db_result($res, $cnt, "wfs_title");
		$this->wfs_abstract = db_result($res, $cnt, "wfs_abstract");
		$this->wfs_getcapabilities = db_result($res, $cnt, "wfs_getcapabilities");
		$this->wfs_getcapabilities_doc = db_result($res, $cnt, "wfs_getcapabilities_doc");
		$this->wfs_upload_url = db_result($res, $cnt, "wfs_upload_url");
		$this->wfs_describefeaturetype = db_result($res, $cnt, "wfs_describefeaturetype");
		$this->wfs_getfeature = db_result($res, $cnt, "wfs_getfeature");
		$this->wfs_transaction = db_result($res, $cnt, "wfs_transaction");
		$this->fees = db_result($res, $cnt, "fees");
		$this->accessconstraints = db_result($res, $cnt, "accessconstraints");
		$this->wfs_owner = db_result($res, $cnt, "wfs_owner");
		$this->wfs_timestamp = db_result($res, $cnt, "wfs_timestamp");
		
		$sql_fe = "SELECT * FROM wfs_featuretype WHERE fkey_wfs_id = $1 ORDER BY featuretype_id";
		$v = array($this->wfs_id);
		$t = array("i");
		$res_fe = db_prep_query($sql_fe, $v, $t);
		$cnt_fe = 0;
		while(db_fetch_row($res_fe)){
			$c = count($this->wfs_featuretype);
//			$this->wfs_featuretype[$c]->featuretype_id = db_result($res_fe, $cnt_fe, "featuretype_id");
			$this->wfs_featuretype[$c]->featuretype_name = db_result($res_fe, $cnt_fe, "featuretype_name");
			$this->wfs_featuretype[$c]->featuretype_title = db_result($res_fe, $cnt_fe, "featuretype_title");
			$this->wfs_featuretype[$c]->featuretype_abstract = db_result($res_fe, $cnt_fe, "featuretype_abstract");
			$this->wfs_featuretype[$c]->featuretype_srs = db_result($res_fe, $cnt_fe, "featuretype_srs");
			
			$sql_el = "SELECT * FROM wfs_element WHERE fkey_featuretype_id = $1 ORDER BY element_id";
			$v = array(db_result($res_fe, $cnt_fe, "featuretype_id"));
			$t = array("i");
			$res_el = db_prep_query($sql_el, $v, $t);
			$cnt_el = 0;
			while(db_fetch_row($res_el)){
				$z = count($this->wfs_featuretype[$c]->featuretype_element);
				$this->wfs_featuretype[$c]->featuretype_element[$z]["name"] = db_result($res_el, $cnt_el, "element_name");
				$this->wfs_featuretype[$c]->featuretype_element[$z]["type"] = db_result($res_el, $cnt_el, "element_type");
				$cnt_el++;
			}
			$sql_ns = "SELECT * FROM wfs_featuretype_namespace WHERE fkey_featuretype_id = $1 ORDER BY namespace";
			$v = array(db_result($res_fe, $cnt_fe, "featuretype_id"));
			$t = array("i");
			$res_ns = db_prep_query($sql_ns, $v, $t);
			$cnt_ns = 0;
			while(db_fetch_row($res_ns)){
				$z = count($this->wfs_featuretype[$c]->featuretype_namespace);
				$this->wfs_featuretype[$c]->featuretype_namespace[$z]["name"] = db_result($res_ns, $cnt_ns, "namespace");
				$this->wfs_featuretype[$c]->featuretype_namespace[$z]["value"] = db_result($res_ns, $cnt_ns, "namespace_location");
				$cnt_ns++;
			}
			$cnt_fe++;
		}
		$cnt++;
    }
}
*/
/** end createObjfromDB **/

	public function getallwfs($userid){
		$this->wfs_id = array();
		$this->wfs_name = array();
		$this->wfs_title = array();
		$this->wfs_abstract = array();
		
		global $DBSERVER,$DB,$OWNER,$PW;
		$con = db_connect($DBSERVER,$OWNER,$PW);
		db_select_db($DB,$con);
		if($userid){
		 	$sql = "SELECT * FROM wfs WHERE wfs_owner = $1";
			$v = array($userid);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);
		}
		else{
		 	$sql = "SELECT * FROM wfs";
			$res = db_query($sql);
		}
		
		$cnt = 0;
		while ($row = db_fetch_array($res)){
			$this->wfs_version[$cnt] = $row["wfs_version"];
			$this->wfs_id[$cnt] = $row["wfs_id"];
			$this->wfs_name[$cnt] = $row["wfs_name"];
			$this->wfs_title[$cnt] = $row["wfs_title"];
			$this->wfs_abstract[$cnt] = $row["wfs_abstract"];
			$this->wfs_getcapabilities[$cnt] = $row["wfs_getcapabilities"];
			$this->wfs_describefeaturetype[$cnt] = $row["wfs_describefeaturetype"];
			$this->wfs_getfeature[$cnt] = $row["wfs_getfeature"];
			$cnt++;
		}	
	}
}

/*
class featuretype extends wfs{
	var $featuretype_element = array();	
	var $featuretype_namespace = array();	
	
	function featuretype($name,$title,$abstract,$srs,$url,$version){

		$url .= "&SERVICE=WFS&VERSION=".$version."&REQUEST=DescribeFeatureType&TYPENAME=".$name;
		
		$this->featuretype_name = $name;
		$this->featuretype_title = $title;
		$this->featuretype_abstract = $abstract;
		$this->featuretype_srs = $srs;
		
		$y = new connector($url);
		$data = $y->file;
				
		#$data = implode("",file($url));
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
		xml_parse_into_struct($parser,$data,$values,$tags);
		xml_parser_free($parser);
		
		foreach ($values as $element) {	
			if($this->sepNameSpace($element[tag]) == "schema" && $element[type] == "open"){
				$section = "namespace";
//				echo "namespace<br>";
			}
			
			if($section == "namespace"){		
				while (list($k, $val) = each ($element[attributes])) {
   					if (substr($k, 0, 5) == "xmlns") {
						$cnt = count($this->featuretype_namespace);
						$match = false;
						for ($i = 0 ; $i < $cnt && $match == false ; $i++) {
							if ($this->sepNameSpace($k) == $this->featuretype_namespace[$i]["name"] && $val == $this->featuretype_namespace[$i]["value"]) {
								$match = true;
							}
						}
						if ($match == false) {
							$this->featuretype_namespace[$cnt]["name"] = $this->sepNameSpace($k);
							$this->featuretype_namespace[$cnt]["value"] = $val;
//							echo "namespace: " . $this->sepNameSpace($k) . " -> " . $val . "<br>";
						}
   					}
				}
			}
			if($this->sepNameSpace($element[tag]) == "complexType" && $element[type] == "open"){
				$section = "";
			}
			if($this->sepNameSpace($element[tag]) == "complexContent" && $element[type] == "open"){
				$section = "complexcontent";
//				echo "complexcontent<br>";
			}
			if($section == "complexcontent" && $this->sepNameSpace($element[tag]) == "element" && $element[attributes][name]){
				$cnt = count($this->featuretype_element);
				$this->featuretype_element[$cnt]["name"] = $element[attributes]["name"];
				$this->featuretype_element[$cnt]["type"] = $this->sepNameSpace($element[attributes]["type"]);
//				echo "element: ".$this->featuretype_element[$cnt]["name"]."<br>";
			}
			if($this->sepNameSpace($element[tag]) == "complexContent" && $element[type] == "close"){
				$section = "";
			}
		}
	}
	function sepNameSpace($s){
		$c = strpos($s,":"); 
		if($c>0){
			return substr($s,$c+1);
		}
		else{
			return $s;
		}		
	}
}
*/
?>