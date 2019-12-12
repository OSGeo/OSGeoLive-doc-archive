<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/class_administration.php";
require_once dirname(__FILE__) . "/class_ows_factory.php";

abstract class WmsFactory extends OwsFactory {
	/**
	 * Retrieves the data of a WMS from the database and initiates the object.
	 * 
	 * @return 
	 * @param $id Integer
	 * @param $aWms Wms is being created by the subclass
	 * @param $appId id of the application where this WMS is configured
	 */
	public function createFromDb ($id) {
		$myWms = func_num_args() >= 2 ? 
			func_get_arg(1) : null;

		$appId = func_num_args() >= 3 ? 
			func_get_arg(2) : null;

		if (!is_null($appId)) {
			$myWms->createObjFromDB($appId, $id);
		}
		else {
			$myWms->createObjFromDBNoGui($id);
		}
		return $myWms;
	}
}
?>
