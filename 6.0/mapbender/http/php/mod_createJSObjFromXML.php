<?php
# $Id: mod_createJSObjFromXML.php 7332 2010-12-17 14:48:05Z christoph $
# http://www.mapbender.org/index.php/mod_createJSObjFromXML.php
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
require_once(dirname(__FILE__)."/../classes/class_wms.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
#require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");

$capabilitiesURL = $_REQUEST['caps'];
$noHtml = intval($_GET["noHtml"]);

$output = "";
$charset = CHARSET;

$mywms = new wms();

#$e = new mb_exception("mod_createJSObjFromXML: CapUrl encoded to load: ".$capabilitiesURL);
$caps = $capabilitiesURL;
#$e = new mb_exception("mod_createJSObjFromXML: CapUrl decodes to load: ".$caps);
$caps = html_entity_decode($_REQUEST['caps']);
//$caps = html_entity_decode(base64_decode($_REQUEST['caps']));
$mywms->createObjFromXML($caps);

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
		$output .= $mywms->createJsObjFromWMS_(false);
	}
	else {
		$output .= $mywms->createJsObjFromWMS_(true);
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
