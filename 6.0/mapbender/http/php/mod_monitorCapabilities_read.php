<?php
# $Id: mod_monitorCapabilities_read.php 1283 2007-10-25 15:20:25Z baudson $
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

$e_id = "monitor_results";
#require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_user.php");
require_once(dirname(__FILE__)."/../classes/class_wms.php");
require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
</head>
<body>
<?php
$admin = new administration();
$user = new User();

// update selected wms
$checkboxes = intval($_POST['cbs']);

for ($i=0; $i < $checkboxes; $i++) {
	echo $i;
	if (!isset($_POST['cb'.$i]) || 
		!isset($_POST['upl_id'.$i])
	) {
		continue;
	}	
	$upd_wmsid = intval($_POST['cb'.$i]);
	$upload_id = intval($_POST['upl_id'.$i]);
	
	if ($upd_wmsid) {
		
		// get upload URL
		$sql = "SELECT wms_upload_url, wms_owner FROM wms WHERE wms_id = $1";
		$v = array($upd_wmsid);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);
		$row = db_fetch_array($res);
		$uploadUrl = $row["wms_upload_url"];
		$wmsOwner = $row["wms_owner"];
		
		if ($wmsOwner !== $user->id) {
			echo "<br>Skipped: " . $upd_wmsid . "<br>";
			continue;
		}
		
		// update WMS from upload URL
		$mywms = new wms();
		$mywms->createObjFromXML($uploadUrl);    
		$mywms->optimizeWMS();
		
		echo "<br />";  
		$mywms->updateObjInDB($upd_wmsid);
		echo "<br>Updated: " . $upd_wmsid . "<br>";

/*
		// start new monitoring for this WMS
		$now = time();
		$sql = "UPDATE mb_monitor SET status = '-2', status_comment = 'Monitoring is still in progress...', " . 
			"timestamp_begin = $1, timestamp_end = $2 WHERE upload_id = $3 AND fkey_wms_id = $4";
		$v = array($now, $now, $upload_id, $upd_wmsid);
		$t = array('s', 's', 's', 'i');
		$res = db_prep_query($sql,$v,$t);

		$currentFilename = "wms_monitor_report_" . $upload_id . "_" . 
			$upd_wmsid . "_" . $wmsOwner . ".xml";		
		$exec = PHP_PATH . "php5 ../../tools/mod_monitorCapabilities_write.php " . 
			$currentFilename. " 0";
		echo exec(escapeshellcmd($exec));
*/
	}
	echo "<br>Please note: The updated services need to be monitored again in order to update the database.<br><br>";
}
$e = new mb_exception("mod_monitorCapabilities_read.php: userId: ".$_SESSION["mb_user_id"]);
$sql = "SELECT DISTINCT mb_monitor.fkey_wms_id FROM mb_monitor, wms " . 
	"WHERE mb_monitor.fkey_wms_id = wms.wms_id AND wms.wms_owner = $1";

$res = db_prep_query($sql, array($_SESSION["mb_user_id"]), array("i"));

$wms = array();

while($row = db_fetch_array($res)){
	$wms[] = $row["fkey_wms_id"];
	$e = new mb_exception("mod_monitorCapabilities_read.php: wmsId: ".$row["fkey_wms_id"]);
}
$wms_id = array();
$status = array();
$upload_id = array();
$avg_response_time = array();
$comment = array();
$timestamp_begin = array();
$timestamp_end = array();
$upload_url = array();
$updated = array();
$mapurl = array();
$image = array();

for ($i = 0; $i < count($wms); $i++) {
	$wms_id[$wms[$i]] = $wms[$i];
	// get upload id
	$sql = "SELECT MAX(upload_id) FROM mb_monitor WHERE fkey_wms_id = $1";
	$v = array($wms[$i]);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	$upload_id[$wms[$i]] = db_result($res,0,0);

	$sql = "SELECT AVG(timestamp_begin), AVG(timestamp_end) FROM mb_monitor " . 
		"WHERE fkey_wms_id = $1 AND NOT status = '-1' AND NOT status = '-2'";
	$v = array($wms[$i]);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	if (db_result($res,0,1) == 0 && db_result($res,0,0) == 0) {
		$avg_response_time[$wms[$i]] = NULL;	
	}
	else {
		$avg_response_time[$wms[$i]] = round(db_result($res,0,1)-db_result($res,0,0), 1);
	}
	
	$sql = "SELECT status, status_comment, timestamp_begin, timestamp_end, " . 
		"upload_url, updated, image, map_url, cap_diff FROM mb_monitor, wms " . 
		"WHERE upload_id = $1 AND fkey_wms_id = $2 AND wms_owner = $3 ORDER BY status, " . 
		"status_comment, timestamp_end, fkey_wms_id";
	$v = array($upload_id[$wms[$i]], $wms_id[$wms[$i]], $user->id);
	$t = array('s', 'i', 'i');
	$res = db_prep_query($sql,$v,$t);

	$status[$wms[$i]] = intval(db_result($res,0,"status"));
	$comment[$wms[$i]] = db_result($res,0,"status_comment");
	$timestamp_begin[$wms[$i]] = db_result($res,0,"timestamp_begin");
	$timestamp_end[$wms[$i]] = db_result($res,0,"timestamp_end");
	$upload_url[$wms[$i]] = db_result($res,0,"upload_url");
	$updated[$wms[$i]] = db_result($res,0,"updated");
	$mapurl[$wms[$i]] = db_result($res,0,"map_url");
	$image[$wms[$i]] = db_result($res,0,"image");
	$cap_diff[$wms[$i]] = db_result($res,0,"cap_diff");
	
 	if ($status[$wms[$i]] == -2 && 
		intval(time())-intval($timestamp_begin[$wms[$i]]) > intval(TIME_LIMIT)) 
	{
		$comment[$wms[$i]] = "Monitoring process timed out.";	
		
		$new_sql = "UPDATE mb_monitor SET status = '-1', status_comment = 'Monitoring process timed out.', timestamp_end = $1 WHERE fkey_wms_id = $2 AND upload_id = $3";
		$new_v = array((intval($upload_id[$wms[$i]])+intval(TIME_LIMIT)), $wms_id[$wms[$i]], $upload_id[$wms[$i]]);
		$new_t = array('s', 'i', 's');
		$new_res = db_prep_query($new_sql,$new_v,$new_t);
	}

	$sql = "SELECT COUNT(upload_id) FROM mb_monitor WHERE fkey_wms_id = $1 AND NOT status = '-2'";
	$v = array($wms[$i]);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	$total[$wms[$i]] = db_result($res, 0, 0);

	$sql = "SELECT COUNT(upload_id) FROM mb_monitor WHERE fkey_wms_id = $1 AND status = '-1'";
	$v = array($wms[$i]);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	$fail = db_result($res, 0, 0);
	
	$percentage[$wms[$i]] = 100 - round(100*floatval($fail)/floatval($total[$wms[$i]]), 1);
}

$newArray = $status;
if ($_GET['sortby']) {
	if ($_GET['sortby'] == "wms") {
		$newArray = $wms_id;
		asort($newArray);
	}
	elseif ($_GET['sortby'] == "status") {
		$newArray = $status;
		asort($newArray);
	}
	elseif ($_GET['sortby'] == "avgresp") {
		$newArray = $avg_response_time;
		asort($newArray);
	}
	elseif ($_GET['sortby'] == "avail") {
		$newArray = $percentage;
		arsort($newArray);
	}
	elseif ($_GET['sortby'] == "last") {
		$newArray = $upload_id;
		arsort($newArray);
	}
	elseif ($_GET['image'] == "last") {
		$newArray = $image;
		arsort($newArray);
	}
}



$str = "<span style='font-size:30'>monitoring results</span><hr><br>\n";
$str .= "<form name = 'form1' method='post' action='".$_SERVER["SCRIPT_NAME"]."?sortby=".$_GET['sortby']."'>\n\t";
$str .= "\n\t<input type=submit value='update selected WMS'>\n";
$str .= "\n\t<input type=button onclick=\"self.location.href='".$_SERVER["SCRIPT_NAME"]."?sortby=".$_GET['sortby']."'\" value='refresh'>\n<br/><br/>\n	";
$str .= "<table cellpadding=10 cellspacing=0 border=0>";
$str .= "<tr bgcolor='#dddddd'><th></th><th align='left'><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=wms'>wms</a></th>";
$str .= "<th align='left' colspan = 2><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=status'>current status</a></th>";
$str .= "<th align='left'><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=image'>image</a></th>";
$str .= "<th align='left'><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=avgresp'>avg. response time</a></th>";
$str .= "<th align='left'><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=avail'>overall availability</a></th><th></th><th>Diff</th></tr>";

$cnt = 0;
foreach ($newArray as $k => $value) {
	$img = "stop.bmp";
	if ($status[$k]==0) $img = "wait.bmp";
	elseif ($status[$k]==1) $img = "go.bmp";

	if ($updated[$k] == "0" && $status[$k] == 0) $fill = "checked"; else $fill = "disabled";

	if (fmod($cnt, 2) == 1) {
		$str .= "\n\t\t<tr bgcolor='#e6e6e6'>";
	}
	else {
		$str .= "\n\t\t<tr bgcolor='#f0f0f0'>";
	}
	$str .= "\n\t\t\t<td><input name='cb".$cnt."' value='" . $wms_id[$k] . "' type=checkbox ".$fill." /><input type=hidden name='upl_id".$cnt."' value='".$upload_id[$k]."'></td>";
	$str .= "\n\t\t\t<td valign='top'><b>" . $wms_id[$k] . "</b><br>" . $admin->getWmsTitleByWmsId($wms_id[$k]) . "</td>";
	$str .= "\n\t\t\t<td valign='top'><a href='".$upload_url[$k]."' target=_blank><img title='Connect to service' border=0 src = '../img/trafficlights/". $img. "'></a></td>";
	$str .= "\n\t\t\t<td valign='top'>" . $comment[$k] . "<br><div style='font-size:12'>".date("F j, Y, G:i:s", $upload_id[$k])."</div></td>";
	$str .= "\n\t\t\t<td valign='top'>";

	$str .= "<table bgcolor='black' border=1 cellspacing=1 cellpadding=0><tr><td height=20 width=20 align=center valign=middle bgcolor='";

	if ($image[$k] == -1) {
		$str .= "red";
	}
	elseif ($image[$k] == 0) {
		$str .= "yellow";
	}
	elseif ($image[$k] == 1) {
		$str .= "green";
	}

	if ($image[$k] != -1) {
		$str .= "'><a href='".$mapurl[$k]."'>o</a></td></tr></table></td>";
	}
	else {
		$str .= "'><a href='".$mapurl[$k]."'>x</a></td></tr></table></td>";
	}

	$str .= "\n\t\t\t<td valign='top' align = 'left'>";
	if ($avg_response_time[$k] == NULL) {
		$str .= "n/a";
	}
	else {
		$str .= $avg_response_time[$k] . " s";
	}
	$str .= "</td>";
	$str .= "\n\t\t\t<td valign='top'><b>" . $percentage[$k] . " %</b>&nbsp;&nbsp;<span style='font-size:12'>(" . $total[$k] . " cycles)</span><br>";
	$str .= "<table bgcolor='black' border=1 cellspacing=1 cellpadding=0><tr>";
	$val = $percentage[$k];
	for ($i=0; $i<10; $i++) {
		if ($val>=10) {
			$str .= "<td height=10 width='10' bgcolor='red'></td>";
			$val-=10;
		}
		elseif($val>0){
			$str .= "<td height=10 width='" . round($val) . "' bgcolor='red'></td>";
			if (round($val) < 10) {
				$str .= "<td height=10 width='" . (9-round($val)) . "' bgcolor='white'></td>";
			}
			$val=-1;
		}
		else {
			$str .= "<td height=10 width='10' bgcolor='white'></td>";
		}
	}
	$str .= "</tr></table></td>";
	
#	$str .= "\n\t\t\t<td><a href='output_".$wms_id[$k]."_".$max.".txt' target=_blank>log</a></td>";

	$str .= "\n\t\t<td><input type=button value='details' onclick=\"var newWindow = window.open('../php/mod_monitorCapabilities_read_single.php?wmsid=".$wms_id[$k]."','wms','width=500,height=700,scrollbars');newWindow.focus();\"></td>";
	$str .= "\n\t\t\t<td>";	
	if ($cap_diff[$k] != "")
		#$str .= "<a href='mod_monitorCapabilities_read_single_diff.php?wmsid=".$wms_id[$k]."&upload_id=".$upload_id[$k]."' target=_blank>view</a>";
		$str .= "<input type=button value='show' onclick=\"var newWindow = window.open('../php/mod_showCapDiff.php?wmsid=".$wms_id[$k]."','Caps Diff','width=700,height=300,scrollbars');newWindow.focus();\">";
	$str .= "</td></tr>";


	$cnt++;
}


$str .= "\n\t</table>\n\t<br/><input type=hidden name=cbs value='".$cnt."'>\n</form>";
echo $str;

?>
</body></html>
