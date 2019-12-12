<?php
# $Id: mod_monitorCapabilities_read_single_diff.php 3342 2008-12-16 12:31:26Z mschulz $
# http://www.mapbender.org/index.php/Monitor_Capabilities
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
#require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

/*  
 * @security_patch irv done
 */ 
//security_patch_log(__FILE__,__LINE__);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Mapbender - monitor diff results</title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<style type="text/css">
* {font-family: Arial, "Courier New", monospace; font-size: small;}
.diff-context {background: #eee;}
.diff-deletedline {background: #eaa;}
.diff-addedline {background: #aea;}
.diff-blockheader {background: #ccc;}
</style>
</head>
<body>
<?php
$admin = new administration();

if ($_GET['wmsid']) {
	$wms_id = intval($_GET['wmsid']); 
}
else {
	echo "Invalid WMS ID.";
	die;
}

if ($_GET['upload_id']) {
        $upload_id = intval($_GET['upload_id']);
}
else {
        echo "Invalid upload ID.";
        die;
}

$sql = "SELECT cap_diff FROM mb_monitor ";
$sql .= "WHERE fkey_wms_id = $1 AND upload_id = $2";
$v = array($wms_id,$upload_id);
$t = array('i','i');
$res = db_prep_query($sql,$v,$t);

while ($row = db_fetch_array($res)) {
	$cap_diff = db_result($res,0,"cap_diff");
}
	

$str = "<span style='font-size:30'>monitoring results</span><hr><br>\n";
$str .= "<b>" . $wms_id . "</b><br>" . $admin->getWmsTitleByWmsId($wms_id) . "<br><br><br>\n";
$str .= "<table cellpadding=3 cellspacing=0 border=0>";
$str .= "<tr><td align='center' colspan='2'>Local</td><td align='center' colspan='2'>Remote</td></tr>";

$str .= $cap_diff;

$str .= "\n\t</table>\n\t";
echo $str;

?>
</body></html>
