<?php
# $Id: mod_wfs_conf.php 2327 2009-02-27 16:23:54Z baudson $
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

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_configuration.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_conf.php");

$ajaxResponse = new AjaxResponse($_REQUEST);
$command = $ajaxResponse->getMethod();

switch ($command) {


	case "getWfsConfsFromId":
		$wfsConfIdString = $ajaxResponse->getParameter("wfsConfIdString");

		if(!$wfsConfIdString){ 
			$ajaxResponse->setSuccess($false);
			$ajaxResponse->setMessage("missing wfsConfIdString");
			$ajaxResponse->send();
			break;
		}
		$wfsConfIdString = trim($wfsConfIdString,',');
		$wfsConfIds = explode(',',$wfsConfIdString);
		$result = array();
		foreach($wfsConfIds as $wfsId){
			$currentWfsConf = WfsConfiguration::createFromDb($wfsId);
			if ($currentWfsConf !== null) {
				$result[]= $currentWfsConf;
			}
		}
		$ajaxResponse->setResult($result);
		$ajaxResponse->send();
	break;
	case "getWfsConfs" : 
		$currentUser = new User(Mapbender::session()->get("mb_user_id"));
		$wfsConfIds = $currentUser->getWfsConfByWfsOwner();
		if ($wfsConfIds === null) {
			$wfsConfIds = array();
		}
		
		$result = array();
		foreach ($wfsConfIds as $id) {
			$currentWfsConf = WfsConfiguration::createFromDb($id);
			if ($currentWfsConf !== null) {
				$result[]= $currentWfsConf;
			}
		}
		$ajaxResponse->setResult($result);
		$ajaxResponse->send();
		break;

	case "getWfs" : 
		$aWFS = new wfs_conf();
		$aWFS->getallwfs(Mapbender::session()->get("mb_user_id"));
		
		$result = array();
		
		for ($i = 0; $i < count($aWFS->wfs_id); $i++) {
			// featuretypes
			
			$featuretypeArray = array();
			$aWFS->getfeatures($aWFS->wfs_id[$i]);
			for ($j = 0; $j < count($aWFS->features->featuretype_id); $j++) {

				// featuretype elements
				$ftElementArray = array();
       			$aWFS->getelements($aWFS->features->featuretype_id[$j]);
				for ($k = 0; $k < count($aWFS->elements->element_id); $k++) {
					$ftElementArray[]= array(
						"id" => $aWFS->elements->element_id[$k],
						"name" => $aWFS->elements->element_name[$k],
						"type" => $aWFS->elements->element_type[$k]
					);
				}

                $featuretypeArray[]= array(
					"id" => $aWFS->features->featuretype_id[$j],
                	"name" => $aWFS->features->featuretype_name[$j],
					"srs" => $aWFS->features->featuretype_srs[$j],
					"elementArray" => $ftElementArray
				);
			}

			$result[]= array(
				"id" => $aWFS->wfs_id[$i],
				"name" => $aWFS->wfs_name[$i],
				"title" => $aWFS->wfs_title[$i],
				"abstr" => $aWFS->wfs_abstract[$i],
				"getCapabilities" => $aWFS->wfs_getcapabilities[$i],
				"describeFeaturetype" => $aWFS->wfs_describefeaturetype[$i],
				"getFeature" => $aWFS->wfs_getfeature[$i],
				"featuretypeArray" => $featuretypeArray
			);

		}

		$ajaxResponse->setResult($result);
		$ajaxResponse->send();
		break;
	case "updateWfsConf":
		$wfsConfObj = $ajaxResponse->getParameter("wfsConf");
		$wfsConf = WfsConfiguration::createFromObject($wfsConfObj);
		$success = WfsConfiguration::updateInDb($wfsConf);
		$ajaxResponse->setSuccess($success);
		$message = "The WFS configuration has been updated in the database.";
		if (!$success) {
			$message = "An error occured when updating the WFS configuration in the database.";
		}
		$ajaxResponse->setMessage($message);
		$ajaxResponse->send();
		break;
	case "insertWfsConf":
		$wfsConfObj = $ajaxResponse->getParameter("wfsConf");
		$wfsConf = WfsConfiguration::createFromObject($wfsConfObj);
		$success = false;
		$id = WfsConfiguration::insertIntoDb($wfsConf);
		if ($id === null) {
			$success = false;
			$message = "An error occured when inserting the WFS configuration into the database.";
		}
		else {
			$success = true;
			$message = "The WFS configuration has been inserted into the database. Go to 'Assign WFS conf to application' and assign the new conf to an application.";
			$ajaxResponse->setResult("id", $id);
			
		}
		$ajaxResponse->setSuccess($success);
		$ajaxResponse->setMessage($message);
		$ajaxResponse->send();
		break;
}

// If no response is sent previously, send an error message
$ajaxResponse->setMessage("Invalid command.");
$ajaxResponse->setSuccess(false);
$ajaxResponse->send();
?>
