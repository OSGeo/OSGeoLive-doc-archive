<?php
# $Id: class_keyword.php 2763 2008-08-11 08:19:21Z christoph $
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

/**
 * class to handle keywords for services
 */
 
class Mapbender_keyword{
	
	private $service;
	private $serviceId;
		
	/*
	 * Constructor of the keyword-class
	 * set the service to: wms, layer, wfs, featuretype
	 * 
	 * @param string service 
	 * @param integer id the id of the service
	 * 
	 */
	function __construct($service,$serviceId){
		$this->service = $service;
		$this->serviceId = $serviceId;
			
		if ($this->service == "layer"){
	
			$this->table = 'layer_keyword';
			$this->serviceIdColumn = 'fkey_layer_id';
								
		}
		//example for wms
		else if ($this->service == "wms"){
	
			$this->table = 'wms_keyword';
			$this->serviceIdColumn = 'fkey_wms_id';
						
		}
		//example for featuretype
		else if ($this->service == "featuretype"){
	
			$this->table = 'wfs_featuretype_keyword';
			$this->serviceIdColumn = 'fkey_featuretype_id';
						
		}
	}
	
	/*
	 * selects a keyword
	 *
	 * @param integer the keyword_id
	 * @return string the name of the selected keyword
	 */
	function get($keywordId){
		global $con;
		$sql = "SELECT keyword FROM keyword WHERE keyword_id = $1";
		$v = array($keywordId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		if($row = db_fetch_array($res)){
			return $row['keyword'];
			}
		else{
			return false;
		}
	}
	
	/* selects keywords of layer
	 *
	 * @param integer the layer_id
	 * @return array keywords
	 */
	function getLayerKeywords(){
		global $con;
		$sql = "SELECT keyword FROM keyword, layer_keyword, layer " .
               "WHERE keyword.keyword_id = layer_keyword.fkey_keyword_id " .
               "AND layer_keyword.fkey_layer_id = layer.layer_id " .
               "AND layer.layer_id = $1";
       	$v = array($this->serviceId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		$keywordList = "";
        $separator = "";
        while($row = db_fetch_array($res)){
	        if($keywordList != ""){    
	                $separator = ",";
	        }
            $keywordList .= $separator.$row["keyword"];
        }
        return $keywordList;
    }
    
    /* selects keywords of featuretype
	 *
	 * @param integer the featuretype_id
	 * @return array keywords
	 */
	function getFeaturetypeKeywords(){
		global $con;
		$sql = "SELECT keyword FROM keyword, wfs_featuretype_keyword, wfs_featuretype " .
               "WHERE keyword.keyword_id = wfs_featuretype_keyword.fkey_keyword_id " .
               "AND wfs_featuretype_keyword.fkey_featuretype_id = wfs_featuretype.featuretype_id " .
               "AND wfs_featuretype.featuretype_id = $1";
       	$v = array($this->serviceId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		$keywordList = "";
        $separator = "";
        while($row = db_fetch_array($res)){
	        if($keywordList != ""){    
	                $separator = ",";
	        }
            $keywordList .= $separator.$row["keyword"];
        }
        return $keywordList;
    }
	
	/*
	 * Checks whether the keyword exist in the table keywords
	 * 
	 * @param string the keyword
	 * @return integer the ID of the keyword
	 */
	function exists($keyword){
		global $con;
		$sql = "SELECT keyword_id FROM keyword WHERE keyword = $1";
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
	
	/*
	 * Inserts a new keyword in the parent table keyword
	 * Check first, if the keyword exists
	 *
	 * @param string the keyword
	 * @return integer the id of the new keyword
	 */
	function insert($keyword){
		global $con;
		if ($this->exists($keyword)){
			return false;
		}
		else{
			$sql = "INSERT INTO keyword (keyword) VALUES ($1)";
			$v = array($keyword);
			$t = array('s');
			$res = db_prep_query($sql,$v,$t);
			$id_sql = "SELECT keyword_id FROM keyword WHERE keyword = $1";
			$id_v = array($keyword);
			$id_t = array('s');
			$id_res = db_prep_query($id_sql,$id_v,$id_t);
				if($row = db_fetch_array($id_res)){
					return $row['keyword_id'];
				}
				else{
					return false;
				}
		}
	}
	
	/*
	 * Deletes the entry in the parent table keyword
	 *
	 * @param integer the keyword_id
	 * @return integer the ID of the keyword
	 */
	function delete($keywordId){
		global $con;
		$sql = "DELETE FROM keyword WHERE keyword_id = $1";
		$v = array($keywordId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		if($row = db_fetch_array($res)){
			return $row['keyword_id'];
		}
		else{
			return false;
		}
	}
	
	/*
	 * Inserts a new keyword-constraint in the crosstabulation
	 *
	 * @param integer the keyword_id
	 * @return integer the id of the new keyword-constraint
	 */
	function allocate($keywordId){
		global $con;
		$sql = "INSERT INTO ".$this->table." (".$this->serviceIdColumn.", fkey_keyword_id) VALUES ($1,$2)";
		$v = array($this->serviceId, $keywordId);
		$t = array('i','i');
		$res = db_prep_query($sql,$v,$t);
		if($row = db_fetch_array($res)){
			return $row['fkey_keyword_id'];
			}
		else{
			return false;
		}
	}
	
	/*
	 * Checks whether the keyword exist in the crosstabulation layer_keywords
	 * 
	 * @param integer the keyword_id
	 * @return true or false
	 */
	function isAllocated($keywordId){
		global $con;
		$sql = "SELECT ".$this->serviceIdColumn." FROM ".$this->table." WHERE fkey_keyword_id = $1";
		$v = array($keywordId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		if($row = db_fetch_array($res)){
			return true;
		}
		else{
			return false;
		}
	}

	/*
	 * Deletes the entry in the crosstabulation (relation)
	 *
	 * @param integer the keyword_id
	 * @return integer the id of the keyword
	 */
	function remove($keywordId){
		global $con;
		$sql = "DELETE FROM ".$this->table." WHERE ".$this->serviceIdColumn." = $1 AND fkey_keyword_id = $2";
		$v = array($this->serviceId, $keywordId);
		$t = array('i','i');
		$res = db_prep_query($sql,$v,$t);
		if($res!=FALSE){
			return true;
		}
		else{
			return false;
		}
	}
	
	/*
	 * Deletes all entries of service in the crosstabulation (relation)
	 *
	 * @param 
	 * @return 
	 */
	function removeAll(){
		global $con;
		$sql = "DELETE FROM ".$this->table." WHERE ".$this->serviceIdColumn." = $1";
		$v = array($this->serviceId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		if($res!=FALSE){
			return true;
		}
		else{
			return false;
		}
	}
	
	/*
	 * Deletes the keyword if it has no entry in one of the crosstabs
	 *
	 * @TODO check if the keyword exists in every crosstab (wms, layer, wfs, feturetype) !!!
	 * @return integer the ID of the keyword
	 */
	function orphaned(){
		global $con;
		$sql = "DELETE FROM keyword WHERE NOT EXISTS (SELECT fkey_keyword_id FROM layer_keyword WHERE keyword.keyword_id = layer_keyword.fkey_keyword_id);";
		//$sql .= " AND NOT EXISTS  (SELECT fkey_keyword_id FROM wms_keyword WHERE keyword.keyword_id = wms_keyword.fkey_keyword_id);";
		$res = db_query($sql);
		if($res!=FALSE){
			return true;
		}
		else{
			return false;
		}
	}
	
	/*
	 * add a new keyword. 
	 * 
	 * @param string the new keyword
	 */
	function add($keyword){
		// if the keyword exists add a new entry in the crosstab
		if ($keywordId = $this->exists($keyword)){
			$this->allocate($keywordId);
			}
		// if it doesn´t exist create a new entry in the keyword table and add a new entry in the crosstab
		else{
			$keywordId = $this->insert($keyword);
			$this->allocate($keywordId);
			}
	}
	
	/*
	 * add list of keywords to a service
	 * 
	 * @param string[] keywords an array of keywords 
	 */
	function addList($keywords){
		foreach ($keywords as $keyword){
			// if the keyword exists add a new entry in the crosstab
			if ($keywordId = $this->exists($keyword)){
				$this->allocate($keywordId);
			}
			// if it doesn´t exist create a new entry in the keyword table and add a new entry in the crosstab
			else{
				$keywordId = $this->insert($keyword);
				$this->allocate($keywordId);
			}
		}
	}
	
	/*
	 * replaces the keywords of a service
	 *
	 * @param string keywords an array of keywords
	 */
	function replaceList($keywords){
		// remove all entries in the crosstable for the current service
		$this->removeAll();
		// check for each keyword of the array if it exists 
		foreach ($keywords as $keyword){
			// if the keyword exists add a new entry in the crosstab
			if ($keywordId = $this->exists($keyword)){
				// check if the keyword is allocated
				$this->allocate($keywordId);
			}
			// if it doesn´t exist create a new entry in the keyword table and add a new entry in the crosstab
			else{
				$keywordIdNew = $this->insert($keyword);
				$this->allocate($keywordIdNew);
			}
		}
			// if a keyword exists, that has no constraint delete it
			$this->orphaned();
	}

}


//$x = new Mapbender_keyword("layer",20840);
//echo $x->replaceList(array(test1,test2));
?>
