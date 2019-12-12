<?php
# $Id: class_metadata.php 8404 2012-07-03 07:22:14Z tbaschetti $
# http://www.mapbender.org/index.php/class_administration
# Copyright (C) 2002 CCGIS
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
require_once(dirname(__FILE__)."/class_administration.php");

/**
 * class to handle keywords for services
 */
 
class class_metadata{
	
	private $user_id;
	
	private $departments = array();	
	private $categories = array();	
	private $searchtext;
	private $timestamp_beg;
	private $timestamp_end;
	private $limit;
	private $search_bbox = array();
	private $search_epsg;
	
	private $obj_members = array();	
	
	private $availabilityTime;
	
	private $doc;
	
	/*
	 * Constructor of the class_metadata-class
	 * 	
	 * @$user_id 		integer 	users ID 
	 * @$departments 	array 		departments
	 * @$categories 	array 		categories
	 * @$searchtext 	string 		searchtext (with blanks => more searchwords)
	 * @$timestamp_beg 	integer		Timebegin of the Search in TIMESTAMP-Format
	 * @$timestamp_end 	integer		Timeend of the Search in TIMESTAMP-Format    
	 * @$limit			integer		Limit of the whole Result of the Metadata Search
	 * @$search_bbox	array		BoundingBox
	 * @$search_epsg	string		EPSG like: "EPSG:4326"
	 */
	 function logit($text){
	 	if($h = fopen("/data/mapbender/http/tmp/gregor.txt","a")){
					$content = $text .chr(13).chr(10);
					if(!fwrite($h,$content)){
						#exit;
					}
					fclose($h);
				}
	 	
	 }
	 
	function __construct($user_id, $departments, $categories, $searchtext, $timestamp_beg, $timestamp_end, $limit, $search_bbox, $search_epsg){
		$this->user_id = $user_id;

		$this->departments = $departments;
		$this->categories = $categories;
		$this->searchtext = $searchtext;
		$this->timestamp_beg = $timestamp_beg;
		$this->timestamp_end = $timestamp_end;
		$this->limit = $limit;
		$this->search_bbox = $search_bbox;
		$this->search_epsg = $search_epsg;
		
		$this->availabilityTime = 60*60*24*90;	// sec*min*hour*days
		
//		$e = new mb_notice("Search extended => user_id: ".$this->user_id."");
//		$e = new mb_notice("Search extended => searchtext: ".$this->searchtext."");
//		$e = new mb_notice("Search extended => timestamp_beg: ".$this->timestamp_beg."");
//		$e = new mb_notice("Search extended => timestamp_end: ".$this->timestamp_end."");
//		$e = new mb_notice("Search extended => limit: ".$this->limit."");
//		$e = new mb_notice("Search extended => search_epsg: ".$this->search_epsg."");
//		
//		$e = new mb_notice("Search extended => count departments: ".count($this->departments)."");
//		$e = new mb_notice("Search extended => count categories: ".count($this->categories)."");
//		$e = new mb_notice("Search extended => count search_bbox: ".count($this->search_bbox)."");

//		echo "Search extended => user_id: ".$this->user_id."<br>";
//		echo "Search extended => searchtext: ".$this->searchtext."<br>";
//		echo "Search extended => timestamp_beg: ".$this->timestamp_beg."<br>";
//		echo "Search extended => timestamp_end: ".$this->timestamp_end."<br>";
//		echo "Search extended => limit: ".$this->limit."<br>";
//		echo "Search extended => search_epsg: ".$this->search_epsg."<br>";
//		echo "Search extended => count search_bbox: ".count($this->search_bbox)."<br>";
//
//		echo "Search extended => count departments: ".count($this->departments)."<br>";
//		echo "Search extended => count categories: ".count($this->categories)."<br>";
//		echo "Search extended => count search_bbox: ".count($this->search_bbox)."<br>";
//		
//		echo "Search extended => count departments: ".$this->departments[0]."<br>";
//		echo "Search extended => count categories: ".$this->categories[0]."<br>";
//		echo "Search extended => count search_bbox: ".$this->search_bbox[0]."<br>";


		$this->logit("searchtext: ".$this->searchtext);
		$this->logit("count categories: ".count($this->categories));
		$this->logit("count departments: ".count($this->departments));
		$this->logit("count search_bbox: ".count($this->search_bbox));
		
		$cat_id="";
		for ($index = 0; $index < sizeof($this->categories); $index++) {
			$array_element = $this->categories[$index];
			
			$cat_id .= $array_element.", ";
		}
		
		$this->isOverLimit;
		
		$this->anz_wms=0;
		$this->anz_wfs=0;
		
		$cat = array();	
		$this->cat = $cat;

		$this->user_authorization();

		$this->set_categories();
		
		$this->doc = new DOMDocument('1.0');
		
		$this->generate_xml_output();
				
	}

	/*
	 * user authorization
	 */
	function user_authorization(){

		$n = new administration();
		$this->n = $n; 
		$myguis = $this->n->getGuisByPermission($this->user_id,true);
		$mywms = $this->n->getWmsByOwnGuis($myguis);
		
		$myWFSconfs = $this->n->getWfsConfByPermission($this->user_id);
		

		
		$this->myWFSConfs = $myWFSconfs;
		
		
		for ($index = 0; $index < sizeof($this->myWFSConfs); $index++) {
			$array_element = $this->myWFSConfs[$index];
//			 echo $index."wfs_conf_id: ".$array_element."<br>";
		}
		
		
		$mylayer = array();
		
		$this->mylayer = $mylayer;
		if($mywms == false){
			$mywms = array();	
		}

	}
	
	/* set categories as array */
		function set_categories(){
			$sql = "SELECT * FROM md_topic_category ";
			$sql .= "ORDER BY md_topic_category_id";
			
			$res = db_query($sql);
			$this->cat[0] = array();
			$this->cat[0]['md_topic_category_code_de'] = '';
			$this->cat[0]['member'] = array();
			$cnt = 1;
			while($row = db_fetch_array($res)){
				$this->cat[$cnt] = array();
				$this->cat[$cnt]['md_topic_category_code_de'] = $row['md_topic_category_code_de'];
				$this->cat[$cnt]['member'] = array();
				$cnt++;
//				echo " => ".$row['md_topic_category_code_de'];
//				echo " => ".$row['md_topic_category_id']."<br>";
				
			}
			
			
			$cat_de="";
			
			for ($index = 0; $index < sizeof($this->cat); $index++) {
				$array_element = $this->cat[$index];

				$cat_de .= "'".$this->cat[$index]['md_topic_category_code_de']."', ";
				
			}
			
//			$this->logit("set cat => count categories in [][]: ".count($this->cat));
//			$this->logit("set cat => categories: ".$cat_de);

		}

	/*
	 * replace german letters (for example � => ae)
	 */
	function replaceChars($text){
	$search = array( "�",  "�",  "�",  "�",  "�",  "�",  "�");
	$repwith = array("ae", "oe", "ue", "AE", "OE", "UE", "ss");
	
	if(CHARSET=="UTF-8")
		$text = utf8_decode($text);

	$ret = str_replace($search, $repwith, $text);

	if(CHARSET=="UTF-8")
		$ret = utf8_encode($ret);

	return $ret;
	}
	
	
	function replaceString($string , $replaceString, $newString){
		
		$newstr = str_replace($string, $replaceString, $newString);
		
		return $newstr;
		
	}
	
	
	function generate_xml_output(){
		$this->writeXmlhead();
		$this->writeXmlWMSPart();
		$this->writeXmlWFSPart();
		$this->writeXmlEnd();
	}
	
	function writeXmlhead(){
	
		$this->doc->encoding = CHARSET;
		$result = $this->doc->createElement("result");
		$this->doc->appendChild($result);
		
		//Trefferanzahl
		$overLimit = $this->doc->createElement('overLimit');
		$result->appendChild($overLimit);
		$tr_text = $this->doc->createTextNode($this->isOverLimit);
		$overLimit->appendChild($tr_text);
		
		$rd = $this->doc->createElement("redirect");
		$result->appendChild($rd);
		$trd = $this->doc->createTextNode("muss noch...");
		$rd->appendChild($trd);
	
	}	
	
	function writeXmlEnd(){
		
		$overLimitText = $this->doc->createTextNode($this->isOverLimit);		
		$overLimits = $this->doc->getElementsByTagName("overLimit");			
		foreach ($overLimits as $overLimit) {
		
			$overLimit->appendChild($overLimitText);
		}
		
	
		$ready = $this->doc->createElement('ready');
		$tready = $this->doc->createTextNode("true");
		$ready->appendChild($tready);	
		
		$results = $this->doc->getElementsByTagName("result");			
				foreach ($results as $result) {
				
				$result->appendChild($ready);
				
				}
		
		echo $this->doc->saveXML();	// Ausgabe

		$e = new mb_notice("Wrote the XML-File");
	}
	
	
	/*
	 * generats categories with members in the xml-File
	 */

	function writeXmlWMSPart(){
		
		$asstr = array();
		
		if ($this->searchtext!="false"){
			$asstr = explode(" ",$this->searchtext);	
		}
		
		
		$isTextSearch = "false";
	
	
		for ($index = 0; $index < count($asstr); $index++) {
			$array_element = $asstr[$index];
			$e = new mb_notice("Search-Text: ".$index.".  ".$array_element."");
		}
	
		$v = array();
		$t = array();

		$sql = "SELECT DISTINCT * from service_metadata where ";
	
		
		

		
		for($i=0; $i<count($asstr); $i++){
			$isTextSearch = "true";
			if($i>0){$sql .= " AND ";}
			$sql .= "searchtext LIKE $".($i+1);			
			$va = "%".trim(strtoupper($this->replaceChars($asstr[$i])))."%";
			array_push($v,$va);
			array_push($t,"s");
		}	

		
		$whereBedArray = array();
		//layer_searchable condition
		array_push($whereBedArray, "(layer_searchable = 1)");
		
		
		//date condition
		if ($this->timestamp_beg!="false" && $this->timestamp_end!="false"){
			
			$time ="(wms_timestamp BETWEEN ".$this->timestamp_beg." AND ".$this->timestamp_end.")";
			array_push($whereBedArray, $time);
				
		} else if ($this->timestamp_beg!="false" && $this->timestamp_end=="false"){

			$time ="(wms_timestamp > ".$this->timestamp_beg.")";
			array_push($whereBedArray, $time);
				
		} else if ($this->timestamp_beg=="false" && $this->timestamp_end!="false"){

			$time ="(wms_timestamp < ".$this->timestamp_end.")";
			array_push($whereBedArray, $time);	
		}

		
		//department condition
		if(count($this->departments)>0){

			$txt = $this->getDepartmentBed("wms_owner", "mb_user_department", $this->departments, "OR");			
			$dep = " (". $txt . ") ";
			array_push($whereBedArray, $dep);	

		}
		


//		//Testausgabe
//		for ($index = 0; $index < sizeof($this->whereBedArray); $index++) {
//			$array_element = $this->whereBedArray[$index];
//			echo $index." el: ".$array_element."<br>";
//			
//		}

		//Creating the Where Clausel, based on a Array
		if(count($whereBedArray)>0){
			$txt_whereBed="";
			
			for ($index = 0; $index < sizeof($whereBedArray); $index++) {
			$array_element = $whereBedArray[$index];


				if($isTextSearch=="true") {
					$txt_whereBed .=" AND ".$array_element;
				} else {
					if($index>0){
						$txt_whereBed .=" AND ".$array_element;		
					} else {
						$txt_whereBed .=" ".$array_element;	
					}	
				}

			}
			
			$sql .= $txt_whereBed;
		}
		
		//order by
		$sql .= " ORDER BY load_count DESC";
		
		
		//Beschr�nkung
		$lim = $this->limit;
		$overLimit = $lim+1;
		$sql .= " LIMIT ".$overLimit;

		
		for ($index = 0; $index < sizeof($asstr); $index++) {
			$array_element = $asstr[$index];
			
			$this->logit($index.".Suchwort : ".$array_element);	
		}

		$this->logit("SQL_WMS: ".$sql);
		
		$e = new mb_notice("Search extended => SQL-Request of view service_metadata: ".$sql."");
		$res = db_prep_query($sql, $v, $t);
		
		//	spatial search
		
		
		
//		for ($index = 0; $index < sizeof($this->cat); $index++) {
//		$array_element = $this->cat[$index];
//		
//		$this->logit("0. vor Kategorieeinordnung => Kategorie: ".$this->cat[$index]['md_topic_category_code_de'].", Anzahl Member: ".count($this->cat[$index]['member']));
//		}
		
	
		if(count($this->search_bbox)>0){
		$anz = 0;
			$resultColumns = array();
			$resultColumns = $this->generateNewReaArray($res);
			
			$e = new mb_notice("count service_metadata: ".count($resultColumns)."");

			for ($index = 0; $index < count($resultColumns); $index++) {
				$row = $resultColumns[$index];
		
				if ($this->n->getLayerPermission($row['wms_id'], $row['layer_name'], $this->user_id)) { 
					array_push($this->mylayer,$row['layer_id']);
				}
				//accessconstraints -> email subadmin
//				$sql_ac = "SELECT mb_user_email FROM mb_user WHERE mb_user_id = $1";
//				$v = array($row["wms_owner"]);
//				$t = array('i');
//				$res_ac = db_prep_query($sql_ac, $v, $t);
//				if($row_ac = db_fetch_array($res_ac)){
//					$row["accessconstraints"] = $row_ac["mb_user_email"];
//				}
				$ns = $this->make_members($row);
				$anz++;
			}
			$e = new mb_notice("count service_metadata(spatial-search): ".$anz."");
			$this->logit("ANZ_WMS mit bbox: ".$anz);
		} else {
		$anz = 0;
			
			while($row = db_fetch_array($res)){

				if ($this->n->getLayerPermission($row['wms_id'], $row['layer_name'], $this->user_id)) { 
					array_push($this->mylayer,$row['layer_id']);
				}
				//accessconstraints -> email subadmin
//				$sql_ac = "SELECT mb_user_email FROM mb_user WHERE mb_user_id = $1";
//				$v = array($row["wms_owner"]);
//				$t = array('i');
//				$res_ac = db_prep_query($sql_ac, $v, $t);
//				
//				if ($row_ac = db_fetch_array($res_ac)){
//					$row["accessconstraints"] = $row_ac["mb_user_email"];
//				}
				
				$ns = $this->make_members($row);
				$anz++;
			} 
		
		$e = new mb_notice("count service_metadata(no spatial-search): ".$anz."");	
		$this->logit("ANZ_WMS ohne bbox: ".$anz);		
		}
		
		
		
//		for ($index = 0; $index < sizeof($this->cat); $index++) {
//		$array_element = $this->cat[$index];
//		
//		$this->logit("1.nach Kategorieeinordnung => Kategorie: ".$this->cat[$index]['md_topic_category_code_de'].", Anzahl Member: ".count($this->cat[$index]['member']));
//		}
		
		if ($this->limit < $anz ){
			$this->isOverLimit="true";
		} else {
			$this->isOverLimit="false";
		}

		$ns = $this->make_xml();
	}	
	
	/*
	 *  fills an array[][] with members based on categories
	 */
	function make_members($r){
		
//		$sql = "SELECT fkey_md_topic_category_id FROM layer_md_topic_category WHERE fkey_layer_id = $1";
		
		$sql = "SELECT fkey_md_topic_category_id, md_topic_category_code_de, fkey_layer_id " .
		"FROM layer_md_topic_category, md_topic_category " .
		"WHERE layer_md_topic_category.fkey_md_topic_category_id = md_topic_category.md_topic_category_id " .
		"AND fkey_layer_id = $1";

		$sql .= " ORDER BY fkey_md_topic_category_id";

		$v = array($r['layer_id']);
		$t = array('i');
		$res = db_prep_query($sql, $v, $t);
		$c = 0;
		
		//layer with category
		while($row = db_fetch_array($res)){
			
			if (count($this->categories)>0){
				if (in_array($row['fkey_md_topic_category_id'], $this->categories) ){		
					$this->cat[$row['fkey_md_topic_category_id']]['member'][count($this->cat[$row['fkey_md_topic_category_id']]['member'])] = $r;
				}	
			} else {
					$this->cat[$row['fkey_md_topic_category_id']]['member'][count($this->cat[$row['fkey_md_topic_category_id']]['member'])] = $r;	
			}
			
//			$this->cat[$row['fkey_md_topic_category_id']]['member'][count($this->cat[$row['fkey_md_topic_category_id']]['member'])] = $r;			
//			$catID = $row['fkey_md_topic_category_id'];
//			array_push($this->cat[$catID]['member'],$r);
			$c++;
		}

		// no category for this layer
		if($c == 0){
			array_push($this->cat[0]['member'],$r);
		}
		return true;
	}
	
	

	/*
	 * generats the xml-document
	 */
	function make_xml(){

//	for ($index = 0; $index < sizeof($this->cat); $index++) {
//		$array_element = $this->cat[$index];
//		
//		echo "Kategorie: ".$this->cat[$index]['md_topic_category_code_de'].", Anzahl Member: ".count($this->cat[$index]['member'])."<br>";
//	}
	

	$i=0;
	if (count($this->categories) == 0){		// Wenn eine Kategorieauswahl werde WMSe/Layer die keiner Kategorie zugeordnet sind nicht angezeigt
		$i=0;
	} else {
		$i=1;
	}

	for($i; $i<count($this->cat); $i++){
		if(count($this->cat[$i]['member'])>0){
			
			
			
			$c = $this->doc->createElement("category");
			$results = $this->doc->getElementsByTagName("result");
			
			foreach ($results as $result) {
			
			$result->appendChild($c);
			
			$c->setAttribute('name',$this->cat[$i]['md_topic_category_code_de']);
			$c->setAttribute('count',count($this->cat[$i]['member']));
			}
			
			$this->logit("IM XML-Aufbau: category: ".$this->cat[$i]['md_topic_category_code_de'].", Anzahl: ".count($this->cat[$i]['member']));
			
			for($ii=0; $ii<count($this->cat[$i]['member']); $ii++){

//					###################
// 					Schachtelung Start
					$m = $this->doc->createElement('member');
					$m->setAttribute('wms_id',$this->cat[$i]['member'][$ii]['wms_id']);			// Attribut muss gesetzt werden um es wieder zu finden
					$m->setAttribute('layer_pos',$this->cat[$i]['member'][$ii]['layer_pos']);	// Attribut muss gesetzt werden um es wieder zu finden
//					$m->setAttribute('layer_parent',$this->cat[$i]['member'][$ii]['layer_parent']);
					
					if ($ii != 0){					
						
						$members = $c->getElementsByTagName('member');
						$sub = "false";
						$mem = null;
						
						foreach ($members as $member) {
							$tmp_wms_id = $member -> getAttribute('wms_id');
							$tmp_layer_pos = $member -> getAttribute('layer_pos');
						    	    
						    /*
						     * Wiederfinden des Members �ber die Attribute
						     */   
							if ($this->cat[$i]['member'][$ii]['wms_id'] == $tmp_wms_id){
								
								if ($this->cat[$i]['member'][$ii]['layer_parent'] == $tmp_layer_pos){
									$sub = "true";
									$member->appendChild($m);	// dem member $member wird ein weiteres member $m hinzugef�gt
									break;
									
								} else {
									$sub = "false";
								}
							} 						       
						}

						if ($sub == "true"){
										     // member konnte schon in der foreach-schleife eingefuegt werden
						} else {
						$c->appendChild($m); // member kategorie 1	(anderer WMS)
						}
					} else {
						
						$c->appendChild($m);	 // member kategorie 1 (erstes member)
						
					}

// 				Schachtelung Ende			
//              ###################
				
				// LayerName
				$state = $this->doc->createElement('layername');
				$m->appendChild($state);
				
				$name = "";
				$layname = $this->cat[$i]['member'][$ii]['layer_name']; 
				if ($layname == "NULL" || $layname == "") {
					$name = "";
				} else {
					$name = $layname;
				}
				$lay_name_text = $this->doc->createTextNode($name);
				$state->appendChild($lay_name_text);
				
				 
				//Abfragbarkeit
				$queryable = $this->doc->createElement('queryable');
				$m->appendChild($queryable);
				
				if ($this->cat[$i]['member'][$ii]['layer_queryable']==1){
					$lay_queryable = "true";	
				} else {
					$lay_queryable = "false";
				}
				
				$abf_text = $this->doc->createTextNode($lay_queryable);
				$queryable->appendChild($abf_text);
				
				// Epsg
				$epsg = $this->doc->createElement('epsg');
				$m->appendChild($epsg);
				
				$equalEPSG = $this->checkEPSG($this->cat[$i]['member'][$ii]['wms_id']);
				
				$epsg_text = $this->doc->createTextNode($equalEPSG);
				$epsg->appendChild($epsg_text);
				
				// L�nderkennung
				$state = $this->doc->createElement('federalstate');
				$m->appendChild($state);
				
				$spatialSource = "";
				$stateorprovince = $this->cat[$i]['member'][$ii]['stateorprovince']; 
				if ($stateorprovince == "NULL" || $stateorprovince == "") {
					
					$spatialSource = $this->cat[$i]['member'][$ii]['country'];
				} else {
					$spatialSource = $this->cat[$i]['member'][$ii]['stateorprovince'];
				}
				
				$countr_code_text = $this->doc->createTextNode($spatialSource);
				$state->appendChild($countr_code_text);
				
				// Satus des letzten Monitorings
				$last_monitoring = $this->doc->createElement('last_monitoring');
				$m->appendChild($last_monitoring);
				
				$lastMonitoring = $this->getLastMonitoringDate($this->cat[$i]['member'][$ii]['wms_id']);

				$last_monitoring_text = $this->doc->createTextNode($lastMonitoring);
				$last_monitoring->appendChild($last_monitoring_text);
				
				// Verf�gbarkeit - Prozentzahl
				$availability = $this->doc->createElement('availability');
				$m->appendChild($availability);
				
				$availabilityPercent = $this->getWMSavailability($this->cat[$i]['member'][$ii]['wms_id'], $this->availabilityTime); 
				
				$availability_text = $this->doc->createTextNode($availabilityPercent);
				
				
				
				$availability->appendChild($availability_text); 
				
				// Relevanz / Count
				$relevance = $this->doc->createElement('relevance');
				$m->appendChild($relevance);
				
				$lay_count=0;
				if($this->cat[$i]['member'][$ii]['load_count'] != null){
				$lay_count=$this->cat[$i]['member'][$ii]['load_count'];
				}
				$relevance_text = $this->doc->createTextNode($lay_count);
				$relevance->appendChild($relevance_text);
				
				
				//type
				$type = $this->doc->createElement('type');
				$m->appendChild($type);
				if($this->cat[$i]['member'][$ii]['layer_pos'] > 0){
					$ttype = $this->doc->createTextNode("layer");
				}
				else{
					$ttype = $this->doc->createTextNode("wms");
				}
				$type->appendChild($ttype);
				
				//wms_id				
				$wms__id = $this->doc->createElement('wms_id');
				$m->appendChild($wms__id);
				$id_text = $this->doc->createTextNode($this->cat[$i]['member'][$ii]['wms_id']);
				$wms__id->appendChild($id_text);
				//layer_id
				$id = $this->doc->createElement('id');
				$m->appendChild($id);
				$tid = $this->doc->createTextNode($this->cat[$i]['member'][$ii]['layer_id']);
				$id->appendChild($tid);
				//title
				$title = $this->doc->createElement('title');
				$m->appendChild($title);
				$ttitle = $this->doc->createTextNode($this->cat[$i]['member'][$ii]['layer_title']);
				$title->appendChild($ttitle);
				//abstract
				$abst = $this->doc->createElement('abstract');
				$m->appendChild($abst);  
				$tabst = $this->doc->createTextNode($this->cat[$i]['member'][$ii]['layer_abstract']);
				$abst->appendChild($tabst);
				
				// accesscontraints				
				$ac = $this->doc->createElement('accessconstraints');
				$m->appendChild($ac);					
				$myac = $this->doc->createTextNode($this->cat[$i]['member'][$ii]['accessconstraints']);							
				$ac->appendChild($myac);
				
				//data
				$date = $this->doc->createElement('date');
				$m->appendChild($date);
				$tdate = $this->doc->createTextNode(date("d.m.Y",$this->cat[$i]['member'][$ii]['wms_timestamp']));
				$date->appendChild($tdate);
				//department
				$dm = $this->doc->createElement('department');
				$m->appendChild($dm);
				$tdm = $this->doc->createTextNode($this->cat[$i]['member'][$ii]['mb_group_name']);
				$dm->appendChild($tdm);
				
				//permission // Leseberechtigung des Benutzers(true), sonst Email zur Beantragung(email)
				$per = $this->doc->createElement('permission');
				$m->appendChild($per);
				$per_text = $this->getPermissionValue( $this->cat[$i]['member'][$ii]['wms_id'], $this->cat[$i]['member'][$ii]['layer_id']);
				$pe =  $this->doc->createTextNode($per_text);
				$per->appendChild($pe);
				

				// termsofuse				
				$ter = $this->doc->createElement('termsofuse');
				$m->appendChild($ter);
				$myter_text = $this->getTermOfUse($this->cat[$i]['member'][$ii]['wms_id']);
				$myter =  $this->doc->createTextNode($myter_text);				
				$ter->appendChild($myter);
		
		
		
//				$this->logit("Ende member: ".$ii);
//				echo "ende member: ".$ii."<br>";
			}
		}
	}

	}
		
		
	function writeXmlWFSPart(){
		
//		$asstr = split(" ",$this->searchtext);
		
		$asstr = array();
		
		if ($this->searchtext!="false"){
			$asstr = explode(" ",$this->searchtext);	
		}
		
		for ($index = 0; $index < count($asstr); $index++) {
			$array_element = $asstr[$index];
			$e = new mb_notice("Search-Text: ".$index.".  ".$array_element."");
		}
		
		$v = array();
		$t = array();
	
		$sql = "SELECT DISTINCT * from wfs_service_metadata where ";	
		$whereBedArray = array();
		
		
		$isTextSearch="false";
		//textsearch
		for($i=0; $i<count($asstr); $i++){
			$isTextSearch="true";
			if($i>0){$sql .= " AND ";}
			$sql .= "searchtext LIKE $".($i+1);			
			$va = "%".trim(strtoupper($this->replaceChars($asstr[$i])))."%";
			array_push($v,$va);
			array_push($t,"s");
					
		}	
			
		//date condition
		if ($this->timestamp_beg!="false" && $this->timestamp_end!="false"){
			$time ="(wfs_timestamp BETWEEN ".$this->timestamp_beg." AND ".$this->timestamp_end.")";
			array_push($whereBedArray, $time);
				
		} else if ($this->timestamp_beg!="false" && $this->timestamp_end=="false"){

			$time ="(wfs_timestamp > ".$this->timestamp_beg.")";
			array_push($whereBedArray, $time);
				
		} else if ($this->timestamp_beg=="false" && $this->timestamp_end!="false"){

			$time ="(wfs_timestamp < ".$this->timestamp_end.")";
			array_push($whereBedArray, $time);	
		}
			
			
		//department condition
			if(count($this->departments)>0){
	
				$txt = $this->getDepartmentBed("wfs_owner", "mb_user_department", $this->departments, "OR");			
				$dep = " (". $txt . ") ";
				array_push($whereBedArray, $dep);	
	
			}
			
		//featuretype_searchable condition
			array_push($whereBedArray, "(featuretype_searchable = 1)");
	

	
		// Creating the Where Clausel, based on a Array
			if(count($whereBedArray)>0){
				$txt_whereBed="";
				for ($index = 0; $index < sizeof($whereBedArray); $index++) {
				$array_element = $whereBedArray[$index];
				
					if($isTextSearch=="true") {
						$txt_whereBed .=" AND ".$array_element;
					} else {
						if($index>0){
							$txt_whereBed .=" AND ".$array_element;		
						} else {
							$txt_whereBed .=" ".$array_element;	
						}	
					}

//				$txt_whereBed .=" AND ".$array_element;
				}
				$sql .= $txt_whereBed;
			}
			
			
			//order by
			$sql .= " ORDER BY featuretype_id ";
			
//			echo "WFS-SQL:<br>".$sql."<br>";

			$this->logit("WFS_SQL: ".$sql);
			
			$e = new mb_notice("Search extended => SQL-Request of view service_metadata: ".$sql."");
			
			$res = db_prep_query($sql, $v, $t);
			$c = $this->doc->createElement("category");
			$results = $this->doc->getElementsByTagName("result");
			
			foreach ($results as $result) {
			
				$result->appendChild($c);
				
				$c->setAttribute('name', "Geometriedienste");
				$c->setAttribute('count', "0");
			}
		
			$i = 0;
	
			while($row = db_fetch_array($res)){
			
			$m = $this->doc->createElement('member');
			$m->setAttribute('wfs_id', $row['wfs_id']);
			$m->setAttribute('layer_pos', "");
			
			$c->appendChild($m);	 // member kategorie 1 (erstes member)
							 
			//Abfragbarkeit
			$queryable = $this->doc->createElement('queryable');
			$m->appendChild($queryable);

			
			if ($row['featuretype_searchable'] == "1"){
				$lay_queryable = "true";	
			} else {
				$lay_queryable = "false";
			}
			
			$search_text = $this->doc->createTextNode($lay_queryable);
			$queryable->appendChild($search_text);
			
			// Epsg
			$epsg = $this->doc->createElement('epsg');
			$m->appendChild($epsg);
			
			$equalEPSG = $row['featuretype_srs'];
			
			$epsg_text = $this->doc->createTextNode($equalEPSG);
			$epsg->appendChild($epsg_text);
			
			// L�nderkennung
			$state = $this->doc->createElement('federalstate');
			$m->appendChild($state);
			
			$spatialSource = "";
			$stateorprovince = $row['administrativearea']; 
			if ($stateorprovince == "NULL" || $stateorprovince == "") {
				
				$spatialSource = $row['country'];
			} else {
				$spatialSource = $row['administrativearea'];
			}
			
			$countr_code_text = $this->doc->createTextNode($spatialSource);
			$state->appendChild($countr_code_text);
			
			
			//type
			$type = $this->doc->createElement('type');
			$m->appendChild($type);
			$ttype = $this->doc->createTextNode("wfs");
			$type->appendChild($ttype);
			
//			// FeaturetypeName
//			$state = $this->doc->createElement('name');
//			$m->appendChild($state);
//			
//			$name = "";
//			$stateorprovince = $row['featuretype_name']; 
//			if ($stateorprovince == "NULL" || $stateorprovince == "") {
//				
////				$name = $row[''];
//			} else {
//				$name = $row['featuretype_name'];
//			}
//			
//			$countr_code_text = $this->doc->createTextNode($name);
//			$state->appendChild($countr_code_text);
			
			//id
			$id = $this->doc->createElement('featuretype_id');
			$m->appendChild($id);
			$tid = $this->doc->createTextNode($row['featuretype_id']);
			$id->appendChild($tid);
			
			//wfs_conf_id
			$conf_id = $this->doc->createElement('wfs_conf_id');
			$m->appendChild($conf_id);
			$c_id = $this->doc->createTextNode($row['wfs_conf_id']);
			$conf_id->appendChild($c_id);
			
			//title
			$title = $this->doc->createElement('title');
			$m->appendChild($title);
			$ttitle = $this->doc->createTextNode($row['wfs_conf_abstract']);
			
			//title
			$title = $this->doc->createElement('title');
			$m->appendChild($title);
			$ttitle = $this->doc->createTextNode($row['wfs_conf_abstract']);
			
			$title->appendChild($ttitle);
			//abstract
			$abst = $this->doc->createElement('abstract');
			$m->appendChild($abst);  
			$tabst = $this->doc->createTextNode($row['wfs_conf_description']);
			$abst->appendChild($tabst);
			
			
			// Geomtype
			$geo = $this->doc->createElement('geomtype');
			$m->appendChild($geo);
			$geo_text = $this->getGeoType($row['wfs_id'], $row['featuretype_id']);
			$ge =  $this->doc->createTextNode($geo_text);
			$geo->appendChild($ge);
			
			
			// accesscontraints				
			$ac = $this->doc->createElement('accessconstraints');
			$m->appendChild($ac);	
//				$myac = $this->getAccessConstraints($this->cat[$i]['member'][$ii]['wms_id'], $this->cat[$i]['member'][$ii]['accessconstraints'], $this->cat[$i]['member'][$ii]['layer_id']);				
			$myac = $this->doc->createTextNode($row['accessconstraints']);							
			$ac->appendChild($myac);
			
			//data
			$date = $this->doc->createElement('date');
			$m->appendChild($date);
			$tdate = $this->doc->createTextNode(date("d.m.Y",$row['wfs_timestamp']));
			$date->appendChild($tdate);
			//department
			$dm = $this->doc->createElement('department');
			$m->appendChild($dm);
			$tdm = $this->doc->createTextNode($row['mb_group_name']);
			$dm->appendChild($tdm);
			
			//permission  		 Leseberechtigung des Benutzers(true), sonst Email zur Beantragung(email)
			$per = $this->doc->createElement('permission');
			$m->appendChild($per);
			$per_text = $this->getPermissionValueForWFS($row['wfs_id'], $row['wfs_conf_id']);
			$pe =  $this->doc->createTextNode($per_text);
			$per->appendChild($pe);
			
//			// termsofuse				
//			$ter = $this->doc->createElement('termsofuse');
//			$m->appendChild($ter);
////			$myter = $this->getAccessConstraints($this->cat[$i]['member'][$ii]['wms_id'], $this->cat[$i]['member'][$ii]['accessconstraints'], $this->cat[$i]['member'][$ii]['layer_id']);
//			$myter_text = "";
//			$myter =  $this->doc->createTextNode($myter_text);				
//			$ter->appendChild($myter);
			
			$i++;
			}
		
		$c->setAttribute('count', $i);
		
		$this->anz_wfs = $i;
	}		
		
		
		
	function getLastMonitoringDate($wms_id){

	$sql = "SELECT max(upload_id) as last_time, status FROM mb_monitor WHERE fkey_wms_id=$1 GROUP BY (status)";
	$v = array($wms_id);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	$c = 0;
	
	$status = "";	
		while($row = db_fetch_array($res)){

		$status = $row['status']; 
		$c++;

		}	
		
		return $status;
	}		
	
	/*
	 * Returns the availability of a WMS/Layer during a period of time
	 * in percent 
	 */
	function getWMSavailability ($wms_id, $time_period){

		$timestamp = time();
		$datum = date("d.m.Y - H:i", $timestamp);
	
		$last_timestamp = $timestamp - $time_period;
	
		$last_date = date("d.m.Y - H:i", $last_timestamp);
	
//		Vorr�bergehend auskommentiert da keine aktuellen (auf Timestamp basierten) Daten vorliegen
//		$sql = "SELECT upload_id as time, status FROM mb_monitor WHERE fkey_wms_id=$1 AND upload_id > ".$last_timestamp;

		$sql = "SELECT upload_id as time, status FROM mb_monitor WHERE fkey_wms_id=$1";
	
		$v = array($wms_id);
		$t = array('i');
		$res = db_prep_query($sql, $v, $t);
		$c = 0;
		$positivStatus=0;

		while($row = db_fetch_array($res)){

			if ($row['status'] == "1" || $row['status'] == "0"){
				$positivStatus++;
			}
			$c++;

		}	
	
		$percent=0;
	
		if ($c!=0){
			$percent = ($positivStatus*100) / $c;	
		} else {
			$percent = 0;
		}
		
		return number_format($percent,0, ",", ".") ;
	
	}
	
	function checkEPSG ($wms_id){

	$sql = "SELECT wms.wms_id, wms_srs.wms_srs FROM wms, wms_srs where wms.wms_id=fkey_wms_id and wms.wms_id=$1";
	$v = array($wms_id);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	$c = 0;

	$isequal = "false";	
		while($row = db_fetch_array($res)){

		$wms_epsg = $row['wms_srs.wms_srs']; 
		$c++;

		}	
		
		if ($wms_epsg==$this->epsg){
			$isequal="true";		
		}	
		return $isequal;	
	}


	function isPermissioned() {
		
		
		
	}

	function getPermissionValue($wms_id, $lay_id){
		$return_permission="";
		// get permission
		if (in_array($lay_id, $this->mylayer)){
			$return_permission = "true";
		} else {
			
			$sql = "SELECT wms.wms_id, mb_user.mb_user_email as email FROM wms, mb_user where wms.wms_owner=mb_user.mb_user_id " .
						"and wms.wms_id=$1";
					
			$v = array($wms_id);
			$t = array('i');
			$res = db_prep_query($sql, $v, $t);
			
			// get email
			$mail="";
			while($row = db_fetch_array($res)){
			$mail = $row['email'];
			$return_permission = $mail; 
			}
		}
		return $return_permission;
	}
	
	function getPermissionValueForWFS($wfs_id, $wfs_conf_id){
	$return_permission="";
	// get permission
	if (in_array($wfs_conf_id, $this->myWFSConfs)){
		$return_permission = "true";
	} else {
		$sql = "SELECT wfs.wfs_id, mb_user.mb_user_email as email FROM wfs, mb_user where wfs.wfs_owner=mb_user.mb_user_id " .
					"and wfs.wfs_id=$1";

		$v = array($wfs_id);
		$t = array('i');
		$res = db_prep_query($sql, $v, $t);
		
		// get email
		$mail="";
		while($row = db_fetch_array($res)){
		$mail = $row['email'];
		$return_permission = $mail; 
		}
	}
	return $return_permission;
	}
	

	function getTermOfUse($wms_id){

		$return_tou_id="";
	
		$sql = "SELECT wms.wms_id, wms_termsofuse.fkey_wms_id, wms_termsofuse.fkey_termsofuse_id, termsofuse.termsofuse_id as tid " .
				"FROM wms, wms_termsofuse, termsofuse " .
				"where wms.wms_id=wms_termsofuse.fkey_wms_id " .
				"and wms_termsofuse.fkey_termsofuse_id=termsofuse.termsofuse_id " .
				"and wms.wms_id=$1";
				
		$v = array($wms_id);
		$t = array('i');
		$res = db_prep_query($sql, $v, $t);

		// get termsofuse_id
		$termsofuse_id="";
		while($row = db_fetch_array($res)){
		$termsofuse_id = $row['tid'];
//		$return_tou_id = "mapbender/php/mod_termsofuse_service.php?id=".$termsofuse_id;
		$return_tou_id = $termsofuse_id;

		}	
		
		return $return_tou_id;
		
	}
	
	function generateNewReaArray($result){
		global $con;
//		echo "func generateNewReaArray<br>";
		
//		$this->search_bbox;
//		$this->search_epsg;
		
		$delColumns = array();
		
		$columnsResult = array(); 
		
		$db_epsg="";
		$db_minx="";
		$db_miny="";
		$db_maxx="";
		$db_maxy="";
		
		$isIntersecting = "";
		
		$anz = 0;

		while($row = db_fetch_array($result)){


			$status="";
//			echo $anz.". layer_id: ".$row['layer_id']; 
			
			//EPSG Vergleich
			$sql_ac = "SELECT * FROM layer_epsg WHERE fkey_layer_id = $1";
			$v = array($row["layer_id"]);
			$t = array('i');
			$res_ac = db_prep_query($sql_ac, $v, $t);
			
			$tra_epsg="";
			$tra_minx="";
			$tra_miny="";
			$tra_maxx="";
			$tra_maxy="";
			
			
			
			$i=0;
			while($row_ac = db_fetch_array($res_ac)){
				
				if ($row_ac["epsg"]=="EPSG:4326"){ // maybe for transformation needed
					$db_epsg=$row_ac["epsg"];
					$db_minx=$row_ac["minx"];
					$db_miny=$row_ac["miny"];
					$db_maxx=$row_ac["maxx"];
					$db_maxy=$row_ac["maxy"];
				}
				
				if ($row_ac["epsg"]==$this->search_epsg){
//				if ($row_ac["epsg"]=="EPSG:31467"){
					
					$db_epsg=$row_ac["epsg"];
					$db_minx=$row_ac["minx"];
					$db_miny=$row_ac["miny"];
					$db_maxx=$row_ac["maxx"];
					$db_maxy=$row_ac["maxy"];

					
					$status="match";
					break;
				
				} else {
					
					
				}
				$i++;
			}
			
			if ($status=="match"){
					
//					echo "match<br>";
					
					$temp = $this->search_bbox;
					
					$isIntersecting = $this->intersect($temp[0], $temp[1], $temp[2], $temp[3], $db_minx, $db_miny, $db_maxx, $db_maxy, $this->search_epsg);
					
				
			} else if ($status != "match" && $i>0){
				
//					echo "Transformation<br>";
//					Transformation
				
					$temp = $this->search_bbox;
		
					$sqlMinx = "SELECT X(transform(GeometryFromText('POINT(".$temp[0]." ".$temp[1].")',".str_replace("EPSG:","",$this->search_epsg)."), 4326)) as minx";					
					
					$resMinx = @pg_query($con,$sqlMinx);
					$s_minx = pg_result($resMinx,0,"minx");

					$sqlMiny = "SELECT Y(transform(GeometryFromText('POINT(".$temp[0]." ".$temp[1].")',".str_replace("EPSG:","",$this->search_epsg)."), 4326)) as miny";
					$resMiny = @pg_query($con,$sqlMiny);
					$s_miny = pg_result($resMiny,0,"miny");
					
					$sqlMaxx = "SELECT X(transform(GeometryFromText('POINT(".$temp[2]." ".$temp[3].")',".str_replace("EPSG:","",$this->search_epsg)."), 4326)) as maxx";
					$resMaxx = @pg_query($con,$sqlMaxx);
					$s_maxx = pg_result($resMaxx,0,"maxx");
					
					$sqlMaxy = "SELECT Y(transform(GeometryFromText('POINT(".$temp[2]." ".$temp[3].")',".str_replace("EPSG:","",$this->search_epsg)."), 4326)) as maxy";
					$resMaxy = @pg_query($con,$sqlMaxy);		 
					$s_maxy = pg_result($resMaxy,0,"maxy");	
					
					$isIntersecting = $this->intersect($s_minx, $s_miny, $s_maxx, $s_maxy, $db_minx, $db_miny, $db_maxx, $db_maxy, "EPSG:4326");
				
			} else {
//					"no intersection possible()<br>";	
					$isIntersecting="f";
			}		
			
			if ($isIntersecting=="t"){
				
//				Datensatz loeschen				
				array_push($columnsResult, $row);
				
			} 
			
			$anz++;	
			
		}
		return $columnsResult;
		
	}

	function intersect($s_minx, $s_miny, $s_maxx, $s_maxy, $db_minx, $db_miny, $db_maxx, $db_maxy, $epsg){
		global $con;
		/*
		 * @security_patch sqli done
		 */
                $s_minx = floatval($s_minx);
                $s_miny = floatval($s_miny);
                $s_maxx = floatval($s_maxx);
                $s_maxy = floatval($s_maxy);
                $db_minx = floatval($db_minx);
                $db_miny = floatval($db_miny);
                $db_maxx = floatval($db_maxx);
                $db_maxy = floatval($db_maxy);
                $epsg = pg_escape_string($epsg);

                $result="";
		
		$sqlint = "SELECT intersects(envelope(geometryFROMtext('LINESTRING(".$s_minx." ".$s_miny.", ".$s_maxx." ".$s_maxy.")',".str_replace("EPSG:","",$epsg).")) " .
								   ",envelope(geometryFROMtext('LINESTRING(".$db_minx." ".$db_miny.", ".$db_maxx." ".$db_maxy.")',".str_replace("EPSG:","",$epsg).")))";
													
		$resInt = @pg_query($con,$sqlint);
		$result = pg_result($resInt,0,"intersects");
	
		return $result;
		
	}

	

	/*
	 * Checks whether the keyword exist in the table keywords
	 * 
	 * @todo is UPPER() working with mysql
	 * @param string the keyword
	 * @return integer the ID of the keyword
	 */
	function exists($keyword){
		global $con;
		$sql = "SELECT keyword_id FROM keyword WHERE UPPER(keyword) = UPPER($1)";
		$v = array($keyword);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		if($row = db_fetch_array($res)){
			return $row['keyword_id'];
		}
		else{
			return false;
		}
	}


	function getSQLBedfromArray($culumnName, $columnValues, $additionalChar){
		
		
		$a = array();
		$a = $columnValues;
		
		$a_char = $additionalChar;
		$colName=$culumnName;
		
		
		
		$resultSql="";
		
		for ($index = 0; $index < sizeof($a); $index++) {
			$array_element = $a[$index];
			
			if ($index<1){
				$resultSql .= $colName."='".$a[$index]."'";
			}else{

				$resultSql .= " ".$a_char." " .$colName."='".$a[$index]."'";
				
			}
		}

		return $resultSql;
	}
	
	function getDepartmentBed ($columnFkey, $culumnName, $columnValues, $additionalChar){

		$a = array();
		$a = $columnValues;

		$colName=$culumnName;		
		$a_char = $additionalChar;

		$resultSql = $columnFkey." in (SELECT mb_user_id FROM mb_user WHERE ";
		
		for ($index = 0; $index < count($a); $index++) {
			$array_element = $a[$index];
			
			if ($index<1){
				$resultSql .= $colName."='".$a[$index]."'";
			}else{

				$resultSql .= " ".$a_char." " .$colName."='".$a[$index]."'";
				
			}
		}			
		$resultSql .=")"; 	
		
	return $resultSql;
	}
	
	function getGeoType($wfs_id, $featureType_id){
		
		$return_geoType="";
	
		$sql = "SELECT element_type FROM wfs_element, wfs_featuretype " .
				"where wfs_featuretype.featuretype_id=wfs_element.fkey_featuretype_id " .
				"and element_name='the_geom'" .
				"and wfs_featuretype.fkey_wfs_id = $1" .
				"and wfs_element.fkey_featuretype_id=$2";
				
		$v = array($wfs_id, $featureType_id);
		$t = array('i', 'i');
		$res = db_prep_query($sql, $v, $t);

		// get element_type
		while($row = db_fetch_array($res)){
		$return_geoType = $row['element_type'];;
		}	
		
		return $return_geoType;
		
	}
	
}

?>
