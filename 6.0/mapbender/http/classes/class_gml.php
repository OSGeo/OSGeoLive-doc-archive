<?php
# $Id: class_gml2.php 3099 2008-10-02 15:29:23Z nimix $
# http://www.mapbender.org/index.php/class_gml2.php
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
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_gml_feature_collection.php");

abstract class Gml {
	var $featureCollection = null;
	var $doc;
	
	abstract public function toGml ();
	
	public function toGeoJSON () {
		if ($this->featureCollection === null) {
			return null;
		}
		return $this->featureCollection->toGeoJSON();
	}	
	
	/**
	 * Shortcut for GeoJSON conversion
	 * 
	 * @return String
	 * @param $geoJson String
	 */
	public static function geoJsonToGml ($geoJson) {
		$gmlFactory = new UniversalGmlFactory();
		$myGmlObj = $gmlFactory->createFromGeoJson($geoJson);
		return $myGmlObj->toGml();
	}
	
	public function getBbox () {
		if (is_null($this->featureCollection)) {
			return null;
		}
		return $this->featureCollection->getBbox();
	}
	
	/**
	 * Exports the file to CSV.
	 * 
	 * @param string $filenamePrefix the filename without an ending like .csv
	 */
	public function toCsv ($xmlData, $filenamePrefix, $currentFeaturetype) {
		/*
		 * @security_patch exec done
		 * Added escapeshellcmd()
		 */

		$unique = TMPDIR . $filenamePrefix;
		$fCsv = $unique.".csv";
		$fGml = $unique.".gml";
		$pathOgr = '/usr/bin/ogr2ogr';
		
		$w = $this->toFile($fGml,$xmlData);
		
 		$exec = $pathOgr.' -f "CSV" "'.$unique.'" '.$fGml;
 		#$e = new mb_exception($exec);
 		exec(escapeshellcmd($exec));
 		
 		$exec = 'recode UTF-8..latin1 '.$unique.'/'.$currentFeaturetype.'.csv';
 		$e = new mb_exception($exec);
 		exec(escapeshellcmd($exec));
 		
		$exec = 'chmod 777 '.$unique.'.*';
		exec(escapeshellcmd($exec));
	}
	
	/**
	 * Writes a file containing the GML.
	 * 
	 * @param string $path the path to the file.
	 * @param string $path the path to the file.
	 * @return bool true if the file could be saved; else false.
	 */
	public function toFile ($file,$xml) {
		$handle = fopen($file, "w");
		if (!$handle) {
			$e = new mb_exception("class_gml.php: Filehandler error (" . $file . ").");
			return false;
		}
		if (!fwrite($handle,$xml)) {
			$e = new mb_exception("class_gml.php: Could not write file (" . $file . ").");
			fclose($handle);
			return false;
		}
		fclose($handle);
		return true;
	}
	
	public function getFeatureMemberCount () {
		if ($this->featureCollection === null) {
			return null;
		}
		return $this->featureCollection->getFeatureMemberCount();
	}
}

?>