<?php
# $Id: mod_exportMapImage_server.php 2413 2008-04-23 16:21:04Z christoph $
# http://www.mapbender.org/ExportMapimage
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

require_once(dirname(__FILE__) . "/../php/mb_validateSession.php");
include_once(dirname(__FILE__)."/../classes/class_weldMaps2Image.php");

$imageType = "";
if(isset($_REQUEST["imagetype"])){
	
	$imageType = $_REQUEST["imagetype"];
	
}

$urls = "";
if(isset($_REQUEST["wms_urls"])){
	
	$wms_urls = $_REQUEST["wms_urls"];
	
}

$array_file = array();
$array_file["dir"]  = TMPDIR; 
$array_file["filename"] = "image"; 

$array_urls = explode("___", $wms_urls);
foreach ($array_urls as $key => $value) {
      if (is_null($value) || $value=="") {
        unset($array_urls[$key]);
      }
}

$new_array = array_values($array_urls); 
		
$image = new weldMaps2Image($new_array, $array_file);
$image->getImage($imageType, 'file');

 
?>