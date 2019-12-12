<?php
# $Id: mod_createJSLayerObjFromXML.php 868 2006-11-20 14:30:43Z verena $
# http://www.mapbender.org/index.php/mod_createJSLayerObjFromXML.php
# Copyright (C) 2006 WhereGroup 
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
require_once(dirname(__FILE__)."/../classes/class_wms.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

$capabilitiesURL = $_REQUEST['caps'];
$layerName = $_REQUEST['layerName'];
$noHtml = intval($_GET["noHtml"]);

$output = "";
$charset = CHARSET;

$mywms = new wms();
$mywms->createObjFromXML($capabilitiesURL);

$errorMessage = _mb("Error: The Capabilities Document could not be accessed. " . 
	"Please check whether the server is responding and accessible to " . 
	"Mapbender.");
if (!$mywms->wms_status) { 
	$output .= "try {" . 
		"Mapbender.Modules.dialogManager.openDialog({" . 
		"content: '" . $errorMessage . "<br><br><b>" . $capabilitiesURL . 
		"', modal: false, effectShow: 'puff'});" . 
		"} catch (e) {" . 
		"prompt('" . $errorMessage . "', '" . $capabilitiesURL . "');" . 
		"}";
}
else {
	if ($noHtml) {
		$output .= $mywms->createJsLayerObjFromWMS(false, $layerName);
	}
	else {
		$output .= $mywms->createJsLayerObjFromWMS(true, $layerName);
	}
}

$js = administration::convertOutgoingString($output);
unset($output);

if ($noHtml) {
	echo $js;
}
else {
/*
	$js .= "parent.mod_addWMS_refresh();";
	echo <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Load WMS</title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="Content-Type" content="text/html; charset='$charset'">	
<script type='text/javascript'>
$js
</script>
</head>
<body>
</body>
</html>
HTML;
*/
}
