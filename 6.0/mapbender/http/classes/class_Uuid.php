<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

class Uuid {
	private $uuid;
	
	public function __construct ($aUuid = null) {
		if (is_null($aUuid)) {
			$chars = md5(uniqid(mt_rand(), true));
			$uuid  = substr($chars,0,8) . '-';
			$uuid .= substr($chars,8,4) . '-';
			$uuid .= substr($chars,12,4) . '-';
			$uuid .= substr($chars,16,4) . '-';
			$uuid .= substr($chars,20,12);
			$this->uuid = $uuid;
		}
		else {
			$this->uuid = $aUuid;
		}
	}	

	public function isValid () {
		if(preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $this->uuid)){
			return true;
		}
		return false;
	}
	
	public static function isuuid ($uuid) {
		$obj = new Uuid($uuid);
		return $obj->isValid();
	}

	public function __toString () {
		return $this->uuid;
	}
}
?>