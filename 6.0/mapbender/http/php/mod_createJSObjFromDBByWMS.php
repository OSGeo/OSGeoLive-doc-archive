<?php
# $Id: mod_createJSObjFromDBByWMS.php 1199 2007-03-07 10:06:22Z christoph $
# http://www.mapbender.org/index.php/mod_createJSObjectFromDBByWMS.php
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

$wms_id = $_GET["wms_id"];
$gui_id = $_GET["gui_id"];
$user_id = $_SESSION["mb_user_id"];
$noHtml = intval($_GET["noHtml"]);

$js = "";
// check if user is allowed to access this wms
$admin = new administration();
if ($admin->getWmsPermission($wms_id, $user_id)) {
	$mywms = new wms();
	if ($gui_id !== '') {
		$mywms->createObjFromDB($gui_id, $wms_id);
	}
	else{
		$mywms->createObjFromDBNoGui($wms_id);	
	}
	if ($noHtml) {
		$output .= $mywms->createJsObjFromWMS_(false);
	}
	else {
		$output .= $mywms->createJsObjFromWMS_(true);
	}
	$js .= administration::convertOutgoingString($output);
	unset($output);
}
else {
	$e = new mb_exception("You are not allowed to access this WMS (WMS ID " . $wms_id . ").");
}

if ($noHtml) {
	echo $js;
}
else {
	$js .= "parent.mod_addWMS_refresh();";
	
	$charset = CHARSET;
	echo <<<HTML

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="Content-Type" content="text/html; charset='$charset'">	
<title>Load WMS with one layer</title>
<script language="JavaScript" type="text/javascript">
$js
</script>
</head>
<body bgcolor='#ffffff'>
</body>
</html>
HTML;
}


