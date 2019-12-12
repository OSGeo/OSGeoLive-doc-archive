<?php
# $Id: ext_featureInfoTunnel.php 5675 2010-02-25 14:28:23Z christoph $
# http://www.mapbender.org/index.php/ext_featureInfoTunnel.php
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
require_once(dirname(__FILE__) . "/../classes/class_stripRequest.php");
require_once(dirname(__FILE__) . "/../classes/class_connector.php");
if ($_GET["url"]) {
	$mr = new stripRequest(urldecode($_GET["url"]));
}
else {
	$mr = new stripRequest($_POST["url"]);
}

$nmr = $mr->encodeGET();
$isOwsproxyRequest = (mb_strpos($nmr,OWSPROXY) === 0);
if($isOwsproxyRequest){
	header("Location: ".$nmr);
}
else{
	$x = new connector($nmr);
	if (empty($x->file)) {
		//close window if featureInfo has no result
		//echo "<body onLoad=\"javascript:window.close()\">";
		echo "";
	} 
	else {
		echo $x->file;
	}	
}
?>
