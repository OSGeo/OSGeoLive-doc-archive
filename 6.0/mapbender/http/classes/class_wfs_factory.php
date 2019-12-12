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
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_ows_factory.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_featuretype.php");

/**
 * 
 * @return 
 * @param $xml String
 */
abstract class WfsFactory extends OwsFactory {
	
	/**
	 * Parses the capabilities document for the WFS 
	 * version number and returns it.
	 * 
	 * @return String
	 * @param $xml String
	 */
	private function getVersionFromXml ($xml) {

		$admin = new administration();
		$values = $admin->parseXml($xml);
		
		foreach ($values as $element) {
			if(strtoupper($element[tag]) == "WFS_CAPABILITIES" && $element[type] == "open"){
				return $element[attributes][version];
			}
		}
		throw new Exception("WFS version could not be determined from XML.");
	}

	protected function createFeatureTypeFromUrl () {
	}
		
	/**
	 * Retrieves the data of a WFS from the database and initiates the object.
	 * 
	 * @return 
	 * @param $id Integer
	 * @param $aWfs Wfs is being created by the subclass
	 */
	public function createFromDb ($id, $withProxyUrls = true) {
		if (func_num_args() == 2) {
			$aWfs = func_get_arg(1);
		}
		else {
			return null;
		}

		// WFS
		$sql = "SELECT * FROM wfs WHERE wfs_id = $1;";
		$v = array($id);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);
		$cnt = 0;
		while(db_fetch_row($res)){
			$hasOwsproxyUrl = false;
			$e = new mb_notice("class_wfs_factory: wfs_owsproxy: ".db_result($res, $cnt, "wfs_owsproxy"));
			if(db_result($res, $cnt, "wfs_owsproxy") != ''){
				$owsproxyUrl = OWSPROXY."/".session_id()."/".db_result($res, $cnt, "wfs_owsproxy")."?";
				$e = new mb_notice("class_wfs_factory: owsproxyURl: ".$owsproxyUrl);
				$hasOwsproxyUrl = true;
			}
			
			$aWfs->id = db_result($res, $cnt, "wfs_id");
			$aWfs->name = db_result($res, $cnt, "wfs_name");
			$aWfs->title = db_result($res, $cnt, "wfs_title");
			$aWfs->summary = db_result($res, $cnt, "wfs_abstract");
			$aWfs->getCapabilities = db_result($res, $cnt, "wfs_getcapabilities");
			$aWfs->getCapabilitiesDoc = db_result($res, $cnt, "wfs_getcapabilities_doc");
			$aWfs->uploadUrl = db_result($res, $cnt, "wfs_upload_url");
			$aWfs->describeFeatureType = db_result($res, $cnt, "wfs_describefeaturetype");			
			if(!$hasOwsproxyUrl || !$withProxyUrls){
				$aWfs->getFeature = db_result($res, $cnt, "wfs_getfeature");
			}
			else{
				$aWfs->getFeature = $owsproxyUrl;
			}
			new mb_notice("class_wfs_factory.getFeature.url: ".$aWfs->getFeature);
			if(!$hasOwsproxyUrl || !$withProxyUrls){
				$aWfs->transaction = db_result($res, $cnt, "wfs_transaction");
			}
			else{
				$aWfs->transaction = $owsproxyUrl;
			}
						
			$aWfs->fees = db_result($res, $cnt, "fees");
			$aWfs->accessconstraints = db_result($res, $cnt, "accessconstraints");
			$aWfs->owner = db_result($res, $cnt, "wfs_owner");
			$aWfs->timestamp = db_result($res, $cnt, "wfs_timestamp");
			$aWfs->timestamp_create = db_result($res, $cnt, "wfs_timestamp_create");
			$aWfs->network_access = db_result($res, $cnt, "wfs_network_access");
			$aWfs->fkey_mb_group_id = db_result($res, $cnt, "fkey_mb_group_id");
			$aWfs->uuid = db_result($res, $cnt, "uuid");
			// Featuretypes
			$sql_fe = "SELECT * FROM wfs_featuretype WHERE fkey_wfs_id = $1 ORDER BY featuretype_id";
			$v = array($aWfs->id);
			$t = array("i");
			$res_fe = db_prep_query($sql_fe, $v, $t);
			$cnt_fe = 0;
			
			while(db_fetch_row($res_fe)){

				$ft = new WfsFeatureType($aWfs);
				$ft->id = db_result($res_fe, $cnt_fe, "featuretype_id");
				$ft->name = db_result($res_fe, $cnt_fe, "featuretype_name");
				$ft->title = db_result($res_fe, $cnt_fe, "featuretype_title");
				$ft->summary = db_result($res_fe, $cnt_fe, "featuretype_abstract");
				$ft->srs = db_result($res_fe, $cnt_fe, "featuretype_srs");
				$ft->uuid = db_result($res_fe, $cnt_fe, "uuid");
				
				// Elements
				$sql_el = "SELECT * FROM wfs_element WHERE fkey_featuretype_id = $1 ORDER BY element_id";
				$v = array($ft->id);
				$t = array("i");
				$res_el = db_prep_query($sql_el, $v, $t);
				$cnt_el = 0;
				while(db_fetch_row($res_el)){

					$ft->addElement(
						db_result($res_el, $cnt_el, "element_name"), 
						db_result($res_el, $cnt_el, "element_type"),
						db_result($res_el, $cnt_el, "element_id")
					);
					$cnt_el++;
				}

				//Namespaces
				$sql_ns = "SELECT * FROM wfs_featuretype_namespace WHERE fkey_featuretype_id = $1 ORDER BY namespace";
				$v = array($ft->id);
				$t = array("i");
				$res_ns = db_prep_query($sql_ns, $v, $t);
				$cnt_ns = 0;
				while(db_fetch_row($res_ns)){

					$ft->addNamespace(
						db_result($res_ns, $cnt_ns, "namespace"),
						db_result($res_ns, $cnt_ns, "namespace_location")
					);
					$cnt_ns++;
				}
				
				$aWfs->addFeatureType($ft);
				
				$cnt_fe++;
			}
			$cnt++;
	    }
		return $aWfs;	
	}
}
?>
