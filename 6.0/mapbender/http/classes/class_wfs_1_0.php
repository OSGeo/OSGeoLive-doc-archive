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
require_once(dirname(__FILE__)."/class_connector.php");
require_once(dirname(__FILE__)."/class_administration.php");
require_once(dirname(__FILE__)."/class_wfs.php");

class Wfs_1_0 extends Wfs {
	const VERSION = "1.0.0";
	
	public function getVersion () {
		return "1.0.0";
	}
	
	public function transaction ($method, $wfsConf, $geoJson) {
		$gmlFactory = new Gml_2_Factory();
		$gmlObj = $gmlFactory->createFromGeoJson($geoJson);
	
		return parent::transaction ($method, $wfsConf, $gmlObj);
	}
	
	public function parseTransactionResponse ($xml) {
		$result = new stdClass();
		$result->success = false;
		$result->message = "";
		$result->xml = $xml;

		$data = mb_eregi_replace("^[^<]*", "", $xml);
		$data = mb_eregi_replace("[^>]*$", "", $data);
		$resObj = array();
		if (mb_strpos(mb_strtoupper($data), "SUCCESS") !== false) {
			$result->success = true;
			if (mb_ereg("^.*ogc:FeatureId fid=\"(.+)\"/>.*$", $data)) {
				$fid = mb_ereg_replace("^.*ogc:FeatureId fid=\"(.+)\"/>.*$", "\\1", $data);
				$result->fid = $fid;
			}
			$result->message = "Success.";
		}
		else {
			$result->message = "An unknown error occured.";
		}
		return $result;
	}

	protected function getFeatureIdFilter ($fid) {
		return "<ogc:FeatureId fid=\"$fid\"/>";
	}
	
	
}

?>