<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_ows_factory.php";
require_once dirname(__FILE__) . "/../classes/class_wmc.php";
require_once dirname(__FILE__) . "/../classes/class_connector.php";
require_once dirname(__FILE__) . "/../classes/class_administration.php";

/**
 * Creates WMC 1.1.0 objects.
 * 
 * @return wmc
 */
class WmcFactory extends OwsFactory {

	/**
	 * Creates WMC 1.1.0 objects from an XML document.
	 * 
	 * @return wmc
	 * @param $xml String
	 */
	public function createFromXml ($xml) {
		try {
			$wmc = new wmc();
			$wmc->createFromXml($xml);
		}
		catch (Exception $e) {
			throw new Exception("Could not create WMC from XML.");
		}
		return $wmc;
	}
	
	public function createFromDb ($id) {
		try {
			$wmc = new wmc();
			$res = $wmc->createFromDb($id);
			if ($res === false) {
				throw new Exception("Could not create WMC from DB.");
			}
		}
		catch (Exception $e) {
			throw new Exception("Could not create WMC from DB.");
		}
		return $wmc;
	}
	
}
?>