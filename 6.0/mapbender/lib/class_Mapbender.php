<?php
/*
 * Created on 24.06.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once(dirname(__FILE__)."/class_Mapbender_session.php");
require_once(dirname(__FILE__)."/class_Singleton.php");
 
 class Mapbender extends Singleton{
 	
 	protected function __construct() {
 	}
 
 	public static function session() {	
 		return Mapbender_session::singleton();
 	}
 	
 	public static function singleton() {
        return parent::singleton(__CLASS__);
    }
	
	public static function postgisAvailable () {
		$sql = "Select postgis_full_version()";
		$res = db_query($sql);
		if ($row = db_fetch_array($res)) {
			return true;
		}
		return false;
	}
 }
?>
