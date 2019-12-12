<?php
# $Id: mod_changeEPSG.php 2413 2008-04-23 16:21:04Z christoph $
# http://www.mapbender.org/index.php/mod_changeEPSG.php
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

require(dirname(__FILE__)."/mb_validateSession.php");

$epsgObj = array();

$ajaxResponse = new AjaxResponse($_POST);

function transform ($x, $y, $oldEPSG, $newEPSG) {
	if (is_null($x) || !is_numeric($x) || 
		is_null($y) || !is_numeric($y) ||
		is_null($oldEPSG) || !is_numeric($oldEPSG) ||
		is_null($newEPSG) || !is_numeric($newEPSG)) {
		return null;
	}
	if(SYS_DBTYPE=='pgsql'){
		$con = db_connect(DBSERVER, OWNER, PW);
		$sqlMinx = "SELECT X(transform(GeometryFromText('POINT(".pg_escape_string($x)." ".pg_escape_string($y).")',".pg_escape_string($oldEPSG)."),".pg_escape_string($newEPSG).")) as minx";
		$resMinx = db_query($sqlMinx);
		$minx = floatval(db_result($resMinx,0,"minx"));
		
		$sqlMiny = "SELECT Y(transform(GeometryFromText('POINT(".pg_escape_string($x)." ".pg_escape_string($y).")',".pg_escape_string($oldEPSG)."),".pg_escape_string($newEPSG).")) as miny";
		$resMiny = db_query($sqlMiny);
		$miny = floatval(db_result($resMiny,0,"miny"));
		
	}else{
		$con_string = "host=" . GEOS_DBSERVER . " port=" . GEOS_PORT . 
			" dbname=" . GEOS_DB . "user=" . GEOS_OWNER . 
			"password=" . GEOS_PW;
		$con = pg_connect($con_string) or die ("Error while connecting database");
		/*
		 * @security_patch sqli done
		 */
		$sqlMinx = "SELECT X(transform(GeometryFromText('POINT(".pg_escape_string($x)." ".pg_escape_string($y).")',".pg_escape_string($oldEPSG)."),".pg_escape_string($newEPSG).")) as minx";
		$resMinx = pg_query($con,$sqlMinx);
		$minx = floatval(pg_fetch_result($resMinx,0,"minx"));
		
		$sqlMiny = "SELECT Y(transform(GeometryFromText('POINT(".pg_escape_string($x)." ".pg_escape_string($y).")',".pg_escape_string($oldEPSG)."),".pg_escape_string($newEPSG).")) as miny";
		$resMiny = pg_query($con,$sqlMiny);
		$miny = floatval(pg_fetch_result($resMiny,0,"miny"));
	}
	return array("x" => $minx, "y" => $miny);
	
}

switch ($ajaxResponse->getMethod()) {
	case "transform" :
		if (!Mapbender::postgisAvailable()) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("PostGIS is not available. Please contact the administrator."));
			$ajaxResponse->send();
		}

		$fromSrs = $ajaxResponse->getParameter("fromSrs");
		$toSrs = $ajaxResponse->getParameter("toSrs");
		$x = $ajaxResponse->getParameter("x");
		$y = $ajaxResponse->getParameter("y");
		$bboxStr = $ajaxResponse->getParameter("bbox");
		$bbox = explode(",", $bboxStr);
		$response = null;

		$oldEPSG = preg_replace("/EPSG:/","", $fromSrs);
		$newEPSG = preg_replace("/EPSG:/","", $toSrs);
		
		if (!is_null($bbox) && is_array($bbox) && count($bbox) === 4) {

			$response = array(
				"newSrs" => $toSrs,
				"points" => array()
			);
			for ($i = 0; $i < count($bbox); $i+=2) {
				$pt = transform(
					floatval($bbox[$i]), 
					floatval($bbox[$i+1]), 
					$oldEPSG, 
					$newEPSG
				);
		
				if (!is_null($pt)) {
					$response["points"][]= array(
						"x" => $pt["x"],
						"y" => $pt["y"]
					);
				}
				else {
					$response = null;
					break;
				}
			}
			
		}	 
		else {
			
			$pt = transform($x, $y, $oldEPSG, $newEPSG);
	
			if (!is_null($pt)) {
				$response = array(
					"newSrs" => $toSrs,
					"points" => array(array(
						"x" => $pt["x"],
						"y" => $pt["y"]
					))
				);
			}
		}
		
		if (is_null($response)) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage(_mb("An unknown error occured."));
			$ajaxResponse->send();
		}
		else {
			$ajaxResponse->setSuccess(true);
			$ajaxResponse->setResult($response);
		}
		break;
	default :
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("An unknown error occured."));
		break;
}

$ajaxResponse->send();
?>