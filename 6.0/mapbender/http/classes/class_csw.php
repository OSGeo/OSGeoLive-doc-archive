<?php
# $Id$
# http://www.mapbender.org/index.php/class_csw
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
require_once(dirname(__FILE__)."/class_user.php");
require_once(dirname(__FILE__)."/class_administration.php");

/**
 * CSW main class to hold catalog object
 * @author nazgul
 *
 */
class csw{
	var $cat_id;
	var $cat_title;
	var $cat_abstract;
	var $cat_version;
	
	var $cat_op_getcapabilities;
	var $cat_op_getrecords;
	var $cat_op_getrecordbyid;
	var $cat_op_describerecord;
	var $cat_getcapabilities_doc;
	
	var $cat_get_capabilities_values = array();
	var $cat_get_records_values = array();
	
	var $cat_op_values = array();
	
	var $cat_upload_url;
	var $fees;
	var $accessconstraints;
	var $contactperson;
	var $contactposition;
	var $contactorganization;
	var $address;
	var $city;
	var $stateorprovince;
	var $postcode;
	var $country;
	var $contactvoicetelephone;
	var $contactfacsimiletelephone;
	var $contactelectronicmailaddress;
	
	var $keywords = array();
	
	var $catowner;
	var $cattimestamp;
	var $providername;
	var $providersite;
	var $delivery='';
	
	//store catalog retrieval status
	var $cat_status;
	
	function csw(){
		
	}
	
	//Getters of common items
	function getCatVersion(){
		return $this->cat_version;	
	}
	
	/**
	 * 
	 * @param $request_type getrecords,describerecords..
	 * @param $request_method get,post,soap
	 * @return unknown_type
	 * @todo error check to see whether value is available in method
	 */
	function getURLValue($request_type,$request_method){
		return $this->cat_op_values[$request_type][$request_method];
	}
	
	
	public function getCatURL($type){
		
	}
	
	//XML to Persistance
	/**
	 * Called by admin function when adding catalog
	 * Create Catalog object from Getcapabilities XML
	 * @return unknown_type
	 * @param $url URL of getcapabilities request
	 */
	public function createCatObjFromXML($url)
	{
		//import connector
		$x = new connector($url);
		$data = $x->file;
		
		//handle non-availability of Internet
		
		if(!$data){
			$this->cat_status = false;
			$e = new mb_exception("class_csw: createCatObjFromXML: CSW " . $url . " could not be retrieved.");
			echo "Error: Unable to retrieve catalog XML. Please check your Network connection ";
			return false;
		}
		else {
			$this->cat_status = true;
		}
		
		//arrays to hold xml struct values and index
		$value_array = null;
		$index_array = null;
		
		//operational vars
		$op_type=null; //get-capabilities, getrecords ...
		$op_sub_type=null; //get,post,....
		$op_constraint=false;
		
		$this->cat_getcapabilities_doc = $data;
		$this->cat_upload_url = $url;
		$this->cat_id="";//Auto-assing catalog id
		
		$parser = xml_parser_create("");
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
		xml_parser_set_option($parser,XML_OPTION_TARGET_ENCODING,CHARSET);
		xml_parse_into_struct($parser,$data,$value_array,$index_array);
		
		//echo "values:".print_r($value_array);
		//echo "index:".print_r($vindex_array);
		
		$code = xml_get_error_code($parser);
		if ($code) {
			$line = xml_get_current_line_number($parser); 
			$mb_exception = new mb_exception(xml_error_string($code) .  " in line " . $line);
		}
		
		xml_parser_free($parser);
		
		foreach($value_array as $element){
			//Version 2.0.2
			//@todo: handle other profiles
			
			if((mb_strtoupper($element[tag]) == "CSW:CAPABILITIES" OR mb_strtoupper($element[tag]) == "CAPABILITIES") && $element[type] == "open"){
				$this->cat_version = $element[attributes][version];
			}
			
			//Title
			if((mb_strtoupper($element[tag]) == "OWS:TITLE" OR mb_strtoupper($element[tag]) == "TITLE") && $element[level] == '3'){
				$this->cat_title = $this->stripEndlineAndCarriageReturn($element[value]);
			}
			
			//Abstract
			
			if((mb_strtoupper($element[tag]) == "OWS:ABSTRACT" OR mb_strtoupper($element[tag]) == "ABSTRACT") && $element[level] == '3'){
				$this->cat_abstract = $this->stripEndlineAndCarriageReturn($element[value]);
			}
			
			//fees
			if(mb_strtolower($element[tag]) == "ows:fees" OR mb_strtolower($element[tag]) == "fees"){
				$this->fees = $element[value];
			}
			//
			if(mb_strtolower($element[tag]) == "ows:accessconstraints" OR mb_strtolower($element[tag]) == "accessconstraints"){
				$this->accessconstraints = $element[value];
			}
			if(mb_strtolower($element[tag]) == "ows:individualname" OR mb_strtolower($element[tag]) == "individualname"){
				$this->contactperson = $element[value];
			}
			if(mb_strtolower($element[tag]) == "ows:positionname" OR mb_strtolower($element[tag]) == "positionname"){
				$this->contactposition = $element[value];
			}
			if(mb_strtolower($element[tag]) == "contactorganization" OR mb_strtolower($element[tag]) == "contactorganization"){
				$this->contactorganization = $element[value];
			}
			if(mb_strtolower($element[tag]) == "address"){
				$this->address = $element[value];
			}
			if(mb_strtolower($element[tag]) == "city"){
				$this->city = $element[value];
			}
			if(mb_strtolower($element[tag]) == "stateorprovince"){
				$this->stateorprovince = $element[value];
			}
			if(mb_strtolower($element[tag]) == "postcode"){
				$this->postcode = $element[value];
			}
			if(mb_strtolower($element[tag]) == "country"){
				$this->country = $element[value];
			}
			if(mb_strtolower($element[tag]) == "ows:Voice" OR mb_strtolower($element[tag]) == "Voice"){
				$this->contactvoicetelephone = $element[value];
			}
			if(mb_strtolower($element[tag]) == "contactfacsimiletelephone"){
				$this->contactfacsimiletelephone = $element[value];
			}
			if(mb_strtolower($element[tag]) == "ows:electronicmailaddress" OR mb_strtolower($element[tag]) == "electronicmailaddress"){
				$this->contactelectronicmailaddress = $element[value];
			}
			
			//Store array of keywords
	  		if(mb_strtolower($element[tag]) == "ows:keyword" OR mb_strtolower($element[tag]) == "keyword"){
				$this->keywords[count($this->keywords)] = $element[value];
			}
			

			
			//Handle operational elements
			//Open operational element
			if((mb_strtoupper($element[tag]) == "OWS:OPERATION" OR mb_strtoupper($element[tag]) == "OPERATION") && $element[type] == "open"){
				$op_type = $element[attributes][name];
			}
			
			//Handle GET
			if(($op_type!=null) && (mb_strtoupper($element[tag]) == "OWS:GET" OR mb_strtoupper($element[tag]) == "GET")){
				//$this->cat_op_getcapabilities = $element[attributes]["xlink:href"];
				$this->cat_op_values[mb_strtolower($op_type)]['get']['dflt']=$element[attributes]["xlink:href"];
			}
			
			//Handle POST
			if(($op_type!=null) && (mb_strtoupper($element[tag]) == "OWS:POST" OR mb_strtoupper($element[tag]) == "POST")){
				//$this->cat_op_getcapabilities = $element[attributes]["xlink:href"];
				$this->cat_op_values[mb_strtolower($op_type)]['post']['dflt']=$element[attributes]["xlink:href"];
				$op_sub_type='post';
			}
			
			//Handle Constraint
			if(($op_type!=null) && (mb_strtoupper($element[tag]) == "OWS:CONSTRAINT" OR mb_strtoupper($element[tag]) == "CONSTRAINT")){
				$op_constraint=$element[attributes]["name"];
			}
			
			// Handle POST Constraint
			if(($op_type!=null) && (mb_strtolower($op_constraint)=='postencoding') && (mb_strtoupper($element[tag]) == "OWS:VALUE" OR mb_strtoupper($element[tag]) == "VALUE")){
				//$this->cat_op_getcapabilities = $element[attributes]["xlink:href"];
				$this->cat_op_values[mb_strtolower($op_type)]['post'][mb_strtolower($element[value])]=$this->cat_op_values[mb_strtolower($op_type)]['post']['dflt'];
				//Assume one value per constraint
				$op_constraint=null;
			}
			
			/*
			//GETCAPABILITIES
			if((mb_strtoupper($op_type)=='GETCAPABILITIES') &&   (mb_strtoupper($element[tag]) == "OWS:GET" OR mb_strtoupper($element[tag]) == "GET")){
				$this->cat_op_getcapabilities = $element[attributes]["xlink:href"];
				$this->cat_op_values['getcapabilities_get']=$element[attributes]["xlink:href"];
			}
			
			//GETRECORDS
			if((mb_strtoupper($op_type)=='GETRECORDS') &&   (mb_strtoupper($element[tag]) == "OWS:GET" OR mb_strtoupper($element[tag]) == "GET")){
				$this->cat_op_getrecords = $element[attributes]["xlink:href"];
				$this->cat_op_values['getrecords_get']=$element[attributes]["xlink:href"];
			}
			
			//GETRECORDS
			if((mb_strtoupper($op_type)=='GETRECORDBYID') &&   (mb_strtoupper($element[tag]) == "OWS:GET" OR mb_strtoupper($element[tag]) == "GET")){
				$this->cat_op_getrecordbyid = $element[attributes]["xlink:href"];
				$this->cat_op_values['getrecordbyid_get']=$element[attributes]["xlink:href"];
			}
			
			//DESCRIBERECORD
			if((mb_strtoupper($op_type)=='DESCRIBERECORDS') &&   (mb_strtoupper($element[tag]) == "OWS:GET" OR mb_strtoupper($element[tag]) == "GET")){
				$this->cat_op_describerecord = $element[attributes]["xlink:href"];
				$this->cat_op_values['getcapabilities_get']=$element[attributes]["xlink:href"];
			}
			*/
			
			//Close operational element
			if((mb_strtoupper($element[tag]) == "OWS:OPERATION" OR mb_strtoupper($element[tag]) == "OPERATION") && $element[type] == "close"){
				$op_type = null;
			}
			
		}
		
		//Success/Failure
		if(!$this->cat_title || $this->cat_title == ""){
			$this->cat_status = false;
			$e = new mb_exception("class_csw: createCatObjFromXML: CSW " . $url . " could not be loaded.");
			return false;
		}
		else{
			$this->cat_status = true;
			$e = new mb_notice("class_csw: createCatObjFromXML: CSW " . $url . " has been loaded successfully.");
			return true;
		}
		
		
		
	}
	
	/**
	 * Get catalog object from DB
	 * @param $cat_id
	 * @return unknown_type
	 */
	public function createCatObjFromDB($cat_id)
	{
		$sql = "select * from cat where cat_id = $1";
		$v = array($cat_id);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		while($row = db_fetch_array($res)){
			$this->cat_id = $row['cat_id'];
			$this->cat_version = $row['cat_version'];
			$this->cat_abstract = administration::convertIncomingString($this->stripEndlineAndCarriageReturn($row['cat_abstract']));
			$this->cat_title = administration::convertIncomingString($this->stripEndlineAndCarriageReturn($row['cat_title']));
			$this->cat_upload_url = $row['cat_upload_url'];
			$this->cat_getcapabilities_doc = $row['cat_getcapabilities_doc'];
			$this->cat_id = $row['cat_id'];

			//Get op values
			$sql = "select * from cat_op_conf where fk_cat_id=$1";
			$v = array($cat_id);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);
			while($subrow = db_fetch_array($res)){
				$this->cat_op_values[$subrow['param_type']][$subrow['param_name']]=$subrow['param_value'];
			}
		}
	}
	
	/**
	 * Write catalog object to persistent storage
	 * @param $gui
	 * @return unknown_type
	 */
	public function setCatObjToDB($gui)
	{
		global $con;
		
		$admin = new administration();//to char_encode XML
		db_begin();
		
		# INSERT INTO TABLE cat - auto insert cat_id
		$sql = "INSERT INTO cat( ";
        $sql .= "cat_version, cat_title, cat_abstract, ";  
        $sql .= "cat_upload_url, fees, accessconstraints, providername, providersite, "; 
        $sql .= "individualname, positionname, voice, facsimile, deliverypoint, "; 
        $sql .= "city, administrativearea, postalcode, country, electronicmailaddress, "; 
        $sql .= "cat_getcapabilities_doc, cat_owner, cat_timestamp) ";
    	$sql .= "VALUES($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21)";
    	
		$v = array($this->cat_version,$this->cat_title,$this->cat_abstract,
			$this->cat_upload_url,$this->fees,$this->accessconstraints,$this->providername,$this->providersite,
			$this->contactperson, $this->contactposition, $this->contactvoicetelephone,$this->contactfacsimiletelephone,$this->delivery,
			$this->city,$this->address,$this->postcode,$this->country,$this->contactelectronicmailaddress,
			$admin->char_encode($this->cat_getcapabilities_doc),
			$_SESSION['mb_user_id'],strtotime("now"));
			
		$t = array('s','s','s','s','s','s','s','s','s','s','s','s','s','s','s','s','s','s','s','i','i');
		
		$res = db_prep_query($sql,$v,$t);
		if(!$res){
			db_rollback();
		}
		
		$cat_insert_id = db_insert_id($con,'cat', 'cat_id');
		
		//GUI_CAT 
		$sql ="INSERT INTO gui_cat (fkey_gui_id, fkey_cat_id) ";
		$sql .= "VALUES($1,$2)";
		$v = array($gui,$cat_insert_id);
		$t = array('s','i');
		$res = db_prep_query($sql,$v,$t);
		if(!$res){
			db_rollback();	
		}
		
		//Insert operational values into cat_op_conf
		//CAT_OP_CONF
		
		
		foreach ($this->cat_op_values as $op_category=>$op_name_array){
			foreach($op_name_array as $op_type=>$op_value_array){
				foreach($op_value_array as $op_sub_type=>$value){
					$op_type_value = $op_type;
					if($op_sub_type != 'dflt'){
						//If not dflt, then it is either soap or xml - store this info as post_xml etc
						$op_type_value .= '_'.$op_sub_type;
					}
					if(!isset($value)){
						$value='';
					}
					//Store values
					$sql = " INSERT INTO cat_op_conf(fk_cat_id, param_type, param_name, param_value) " ;
	    			$sql .= " VALUES ($1, $2, $3, $4)";
	    			$v = array($cat_insert_id,$op_category,$op_type_value,$value);
					$t = array('i','s','s','s');
					$res = db_prep_query($sql,$v,$t);
					if(!$res){
						db_rollback();	
					}
				}
			}
		}
		
		
		//Commit Changes
		db_commit();
		
		$this->cat_id = $cat_insert_id;
	}
	
	public function displayCatalog(){
		echo "Your Catalog Has Been Successfully Added <br />";
		echo "Catalog Details: <br/>";
		echo "Id: " . $this->cat_id . " <br />";
		echo "Version: " . $this->cat_version . " <br />";
		echo "Title: " . $this->cat_title . " <br />";
		echo "Abstract: " . $this->cat_abstract . " <br />";
		
	} 
	
	/**
	 * Function to handle whitespace and carriage returns
	 * Inspired by WMS code
	 * @param $string
	 * @return unknown_type
	 */
	function stripEndlineAndCarriageReturn($string) {
	  	return preg_replace("/\n/", "", preg_replace("/\r/", " ", $string));
	}
	

}
?>