<?php
# $Id: mod_loadwmc.php 6697 2010-08-05 12:18:37Z christoph $
# http://www.mapbender.org/index.php/mod_loadwmc.php
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_wmc.php");

/*
$loadFromSession = false;
include(dirname(__FILE__) . "/../include/dyn_php.php");
include(dirname(__FILE__) . "/../include/dyn_js.php");

function createJs ($mergeWms) {
	$jsString = "";
	$wmc = new wmc();
	if (!Mapbender::session()->get("mb_wmc")) {
		$e = new mb_notice("wmc not set, generating from app: " . Mapbender::session()->get("mb_user_gui"));
		$wmc->createFromApplication(Mapbender::session()->get("mb_user_gui"));		
		Mapbender::session()->set("mb_wmc",$wmc->toXml());
		$e = new mb_notice("creating initial WMC.");
	}
	else {
		if (!$wmc->createFromXml(Mapbender::session()->get("mb_wmc"))) {
			$jsString .= "var e = new Mb_notice('mod_loadwmc: load_wmc_session: error parsing wmc');";
		}
	}


	if ($mergeWms) {
		$e = new mb_notice("merging with WMS.");
		$wmsArray = array();
		$inputWmsArray = Mapbender::session()->get("wms");
		if ($inputWmsArray && is_array($inputWmsArray)) {
			for ($i = 0; $i < count($inputWmsArray); $i++) {
				$currentWms = new wms();
				$currentWms->createObjFromXML($inputWmsArray[$i]);
				array_push($wmsArray, $currentWms);
			}
		}
		$options = array();
		if (Mapbender::session()->exists("addwms_showWMS")) {
			$options["show"] = Mapbender::session()->get("addwms_showWMS");
		}
		if (Mapbender::session()->exists("addwms_zoomToExtent")) {
			$options["zoom"] = Mapbender::session()->get("addwms_zoomToExtent");
		}
		if (count($options) > 0) {
			$wmc->mergeWmsArray($wmsArray, $options);
		}
		else {
			$wmc->mergeWmsArray($wmsArray);
		}
		Mapbender::session()->set("command","");
		Mapbender::session()->set("wms",array());

		Mapbender::session()->delete("addwms_showWMS");
		Mapbender::session()->delete("addwms_zoomToExtent");
	}

	$javaScriptArray = array();
	$javaScriptArray = $wmc->toJavaScript();

	$jsString .= implode("", $javaScriptArray);
	return $jsString;
}

//
// Creates the function load_wmc_session.
// This function loads a WMC from the session, if the element var
// "loadFromSession" is set to true.
//
$output = "";
if ($loadFromSession) {
	if (Mapbender::session()->get("command") && Mapbender::session()->get("command") == "ADDWMS") {
		$e = new mb_notice("merging with WMS in Session...");
		$output = createJs(true);
	}
	else {
		$e = new mb_notice("NOT merging with WMS in Session...");
		$output = createJs(false);
	}
}

echo "\nvar loadFromSession = " . intval($loadFromSession) . ";";

$output = administration::convertOutgoingString($output);
echo "function load_wmc_session() {";
echo $output;
echo "}\n";

*/
if ($e_src) {
	sprintf("var mod_loadwmc_img = new Image(); 
			mod_loadwmc_img.src = '%s'", $e_src);
	
}

//
// Creates a pop up with a dialogue to load, view or delete WMC documents
//
include("mod_loadwmc.js");
?>