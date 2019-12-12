<?php
# $Id: mod_wfs_result.php 2144 2008-02-26 23:16:14Z christoph $
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__) . "/../classes/class_connector.php");
require_once(dirname(__FILE__) . "/../classes/class_georss_geometry.php");

header("Content-Type: text/x-json");
$geoRSS = new geoRSS();
$geoRSS->targetEPSG = '4326';
$geoRSS->setImportTags(array("title","description","link","ingrid:wms-url"));
try {
	#echo $geoRSS->parseFile("http://www.portalu.de/opensearch/query?q=wms+datatype:metadata+ranking:score+partner:sn+partner:bw+partner:bund+partner:he+partner:nw+partner:sl+partner:mv+partner:st+partner:bb+partner:hb+partner:be+partner:hh+partner:ni+partner:sh+partner:hb+partner:by&h=10&p=1&xml=1&georss=1&ingrid=1");
	#echo $geoRSS->parseFile("http://www.fao.org/geonetwork/srv/en/rss.latest?georss=simple");
	if (isset($_REQUEST["targetEPSG"])){
		$geoRSS->targetEPSG = $bodytag = str_replace("EPSG:", "",$_REQUEST["targetEPSG"]);
	}
	echo $geoRSS->parseFile($_REQUEST["url"]);
	
} catch (Exception $e) {
	die;
}

?>
