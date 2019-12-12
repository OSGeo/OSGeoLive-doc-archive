<?php
# $Id: mod_usemap.php 7772 2011-04-14 09:42:09Z verenadiewald $
# http://www.mapbender.org/index.php/UseMap
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

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once(dirname(__FILE__)."/../classes/class_gml2.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");

$ajaxResponse = new AjaxResponse($_POST);

if($ajaxResponse->getMethod() != "createUsemap") {
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage("method invalid");
	$ajaxResponse->send();
	exit;
}

$url = $ajaxResponse->getParameter('url'); //urldecode($_REQUEST["url"]);

$g = new gml2();
$g->parsegml($url);

$um_title = array();
$um_x = array();
$um_y = array();
for($i=0; $i<$g->getMemberCount();$i++) {
    $um_title[] = utf8_decode($g->getValueBySeparatedKey($i,"name"));
    $um_x[] = $g->getXfromMemberAsString($i,0);
    $um_y[] = $g->getYfromMemberAsString($i,0);
}
 
$resultObj = array();
$resultObj['um_title'] = $um_title;
$resultObj['um_x'] = $um_x;
$resultObj['um_y'] = $um_y;
	    
$ajaxResponse->setSuccess(true);
$ajaxResponse->setResult($resultObj);
$ajaxResponse->send();
?>