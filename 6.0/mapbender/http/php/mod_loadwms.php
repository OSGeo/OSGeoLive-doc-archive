<?php
# $Id: mod_loadwms.php 4691 2009-09-25 10:39:48Z christoph $
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

require_once(dirname(__FILE__) . "/mb_validatePermission.php");
require_once(dirname(__FILE__) . "/../classes/class_wms.php"); 

if(isset($_REQUEST["wms_id"]) == false) {
    echo "file: ".$_REQUEST["xml_file"];
    $gui_id = $_REQUEST["guiList"];
    $xml = $_REQUEST["xml_file"];
    
    if ($_REQUEST["auth_type"] == 'basic' 
		|| $_REQUEST["auth_type"] == 'digest') {
		
		$auth = array();
    	$auth['username'] = $_REQUEST["username"];
    	$auth['password'] = $_REQUEST["password"];
    	$auth['auth_type'] = $_REQUEST["auth_type"];
    }
    $mywms = new wms();
	if (isset($auth)) {
		$mywms->createObjFromXML($xml, $auth);
	    $mywms->writeObjInDB($gui_id, $auth);  
	}
	else {
		$mywms->createObjFromXML($xml);
		$mywms->writeObjInDB($gui_id);
	}
        
   	$mywms->displayWMS();
	$wms_id = $mywms->wms_id;
}
else {
	$wms_id = $_REQUEST["wms_id"];
}
require_once(dirname(__FILE__)."/../php/mod_editWMS_Metadata.php");
editWMSByWMSID ($wms_id);
?>
