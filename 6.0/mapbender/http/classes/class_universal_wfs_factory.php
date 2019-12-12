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
require_once(dirname(__FILE__)."/../classes/class_wfs_factory.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_1_0_factory.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_1_1_factory.php");

/**
 * 
 * @return 
 * @param $xml String
 */
class UniversalWfsFactory extends WfsFactory {
	
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
			if($this->sepNameSpace(strtoupper($element[tag])) == "WFS_CAPABILITIES" && $element[type] == "open"){
				return $element[attributes][version];
			}
		}
		throw new Exception("WFS version could not be determined from XML.");
	}

	/**
	 * Creates a WFS object by parsing its capabilities document. 
	 * 
	 * The WFS version is determined by parsing 
	 * the capabilities document up-front.
	 * 
	 * @return Wfs
	 * @param $xml String
	 */
	public function createFromXml ($xml) {
		try {
			$version = $this->getVersionFromXml($xml);

			switch ($version) {
				case "1.0.0":
					$factory = new Wfs_1_0_Factory();
					break;
				case "1.1.0":
					$factory = new Wfs_1_1_Factory();
					break;
				default:
					throw new Exception("Unknown WFS version " . $version);
					break;
			}
			return $factory->createFromXml($xml);
		}
		catch (Exception $e) {
			new mb_exception($e);
			return null;
		}
	}
	
	public function createFromDb ($id) {
		try {
			$sql = "SELECT wfs_version FROM wfs WHERE wfs_id = $1";
			$v = array($id);
			$t = array("i");
			$res = db_prep_query($sql, $v, $t);
			$row = db_fetch_array($res);
			if ($row) {
				$version = $row["wfs_version"];
				
				switch ($version) {
					case "1.0.0":
						$factory = new Wfs_1_0_Factory();
						break;
					case "1.1.0":
						$factory = new Wfs_1_1_Factory();
						break;
					default:
						throw new Exception("Unknown WFS version " . $version);
						break;
				}
				return $factory->createFromDb($id);
			}
		}
		catch (Exception $e) {
			new mb_exception($e);
			return null;
		}
	}
}
?>