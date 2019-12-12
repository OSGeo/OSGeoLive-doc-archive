<?php
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
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db(DB,$con);
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
//Test if ID was valid
if ($_REQUEST['wmsid']) {
	$wms_id = intval($_GET['wmsid']); 
}
else {
	echo "Invalid WMS ID.";
	die;
}
//Test if userid was valid
if ($_REQUEST['userid']) {
	$userid = intval($_REQUEST['userid']); 
}
//else {
//	echo "Invalid User ID.";
//	die;
//}
//Test if User Id exists in db







//Test if current User is Owner of the service
//get owners of the wms:
$wmsOwners = $admin->getOwnerByWms($wms_id);
echo $wmsOwners;
if (!(in_array(Mapbender::session()->get("mb_user_id"), $wmsOwners))) {
	echo "User with ID: ".Mapbender::session()->get("mb_user_id")." - You have no rights to see the requested pages!";
	//echo "<br> You are: ".$admin->getOwnerByWms($wms_id);
	die;
}
else {
	echo "OwnerID: ".Mapbender::session()->get("mb_user_id")."<br>";
}


if (!isset($_REQUEST['userid'])){

//List Users wich have used the service - read the log table
$sql = "SELECT distinct fkey_mb_user_id, mb_user_name, mb_user_email, mb_user_phone from mb_proxy_log, mb_user ";
$sql .= " WHERE fkey_wms_id = $1 AND fkey_mb_user_id = mb_user_id";

#$sql = "SELECT upload_id, status, status_comment, timestamp_begin, timestamp_end, upload_url, updated FROM mb_monitor ";
#$sql .= "WHERE fkey_wms_id = $1 AND NOT status = '-2' ORDER BY upload_id DESC";
$v = array($wms_id);
$t = array('i');
$res = db_prep_query($sql,$v,$t);
$cnt = 0;


$str = "<span style='font-size:30'>Usage for WMS: </span><hr><br>\n";
$str .= "<b>" . $wms_id . "</b><br>" . $admin->getWmsTitleByWmsId($wms_id) . "<br><br><br>\n";
$str .= "<table cellpadding=10 cellspacing=0 border=0>";
$str .= "<tr bgcolor='#dddddd'>";
$str .= "<th align='left'>Index</th><th align='left'>ID</th><th align='left'>User Name</th><th align='left'>User Email</th>";
$str .= "<th align='left'>User Phone</th>";
$str .= "<th align='left'>Single requests</th></tr>";

while ($row = db_fetch_array($res)) {
	$str .= "\n\t\t<tr bgcolor='#e6e6e6'>";
	$str .= "\n\t\t\t<td>".$cnt."</td>";
	$UserId[$cnt] = db_result($res,$cnt,"fkey_mb_user_id");
	$str .= "\n\t\t\t<td>".$UserId[$cnt]."</td>";
	$UserName[$cnt] = db_result($res,$cnt,"mb_user_name");
	$str .= "\n\t\t\t<td>".$UserName[$cnt]."</td>";
	$UserEmail[$cnt] = db_result($res,$cnt,"mb_user_email");
	$str .= "\n\t\t\t<td>".$UserEmail[$cnt]."</td>";	
	$UserPhone[$cnt] = db_result($res,$cnt,"mb_user_phone");
	$str .= "\n\t\t\t<td>".$UserPhone[$cnt]."</td>";	
	#$timestamp_end = db_result($res,$cnt,"timestamp_end");
	#$upload_url[$cnt] = db_result($res,$cnt,"upload_url");
	#if ($status[$cnt] == '0' || $status[$cnt] == '1') {
	#	$response_time[$cnt] = strval($timestamp_end-$timestamp_begin) . " s"; 
	#}
	#else {
	#	$response_time[$cnt] = "n/a"; 
	#}
	$str .= "<td><input type=button value='details' onclick=\"var newWindow = window.open('../php/mod_UsageShow.php?wmsid=".$_REQUEST['wmsid']."&userid=".$UserId[$cnt]."','details','width=500,height=700,scrollbars');newWindow.href.location='Usage of Service: ".$wms_id."'\"></td>";
	$str .= "</tr>";
	$cnt++;
}
$str .= "\n\t</table>\n\t";

}
else {
//UserID was set
//Get infos about specific user

$sql = "SELECT mb_user_id, mb_user_name, mb_user_email, mb_user_phone from mb_user ";
$sql .= " WHERE mb_user_id = $1";
$v = array($_REQUEST['userid']);
$t = array('i','i');
$res = db_prep_query($sql,$v,$t);
$row = db_fetch_array($res);


//show usage of this user
$sql = "SELECT count(*) as sumrequests, sum(pixel) as sumpixel , max(proxy_log_timestamp) as lastaccess FROM mb_proxy_log where fkey_mb_user_id=$2 AND fkey_wms_id=$1";
//$sql .= " WHERE fkey_wms_id = $1 AND fkey_mb_user_id = mb_user_id";
$v = array($wms_id,$_REQUEST['userid']);
$t = array('i','i');
$res = db_prep_query($sql,$v,$t);
$row = db_fetch_array($res);
$sumrequests = $row["sumrequests"];
$sumpixel = $row["sumpixel"];
$lastAccess = $row["lastaccess"];

//$sumpixel=0;
$str = "<span style='font-size:30'>Usage of Secured WMS: </span><hr><br>\n";
$str .= "<b>" . $wms_id . "</b><br>" . $admin->getWmsTitleByWmsId($wms_id) . "<br><br><br>\n";
$str .= "<hr><b>for User: ".$row['mb_user_name']."</b><br><br>";
$str .= "UserID: ".$_REQUEST['userid']."<br>UserEmail: ".$row['mb_user_email']."<br>UserPhone: ".$row['mb_user_phone']."<br>Last Access: <b>".$lastAccess."</b><br>";

//Maximum requests
$maxRequests = 50;

//case showSingleRequests
if (($_REQUEST['requests']) & ($_REQUEST['requests']) == 1 ) {
	//show usage of this user
	$sql = "SELECT proxy_log_timestamp, pixel, request FROM mb_proxy_log where fkey_mb_user_id=$2 AND fkey_wms_id=$1 ORDER BY proxy_log_timestamp DESC LIMIT ".$maxRequests;
	$v = array($wms_id,$_REQUEST['userid']);
	$t = array('i','i');
	$res = db_prep_query($sql,$v,$t);
	$cnt=0;
	$str .= "<hr><br><b>Last ".$maxRequests." Requests:</b><br>";
	$str .= "<table cellpadding=10 cellspacing=0 border=0>";
	$str .= "<tr bgcolor='#dddddd'>";
	$str .= "<th align='left'>timestamp</th><th align='left'>requested pixels</th><th align='left'>GetMap Request</th></tr>";
	while ($row = db_fetch_array($res)) {
		$str .= "\n\t\t<tr bgcolor='#e6e6e6'>";
		$timestamp[$cnt] = db_result($res,$cnt,"proxy_log_timestamp");
		$str .= "\n\t\t\t<td>".$timestamp[$cnt]."</td>";
		$pixels[$cnt] = db_result($res,$cnt,"pixel");
		//$sumpixel=$sumpixel+$pixels[$cnt];
		$str .= "\n\t\t\t<td>".$pixels[$cnt]."</td>";
		$request[$cnt] = db_result($res,$cnt,"request");
		$str .= "\n\t\t\t<td><input type=button value='Show Map' onclick=\"var newWindow = window.open('".$request[$cnt]."','GetMap','width=500,height=700,scrollbars');newWindow.href.location='Get Map from: ".$timestamp[$cnt]."'\"></td>";
		$str .= "</tr>";
		$cnt++;
	}
}

$str .= "<hr><b>Total Requests: ".$sumrequests."</b><br><br>";
$sumpixel=$sumpixel/1000000;
$str .= "<hr><b>Total MegaPixel: ".$sumpixel."</b><br><br>";
if (!$_REQUEST['requests']) {
		$str .= "<input type=button value='Show last ".$maxRequests." Requests' onclick=\"var newWindow = window.open('../php/mod_UsageShow.php?wmsid=".$_REQUEST['wmsid']."&userid=".$_REQUEST["userid"]."&requests=1','details','width=500,height=700,scrollbars');newWindow.href.location='Show Requests: ".$timestamp[$cnt]."'\">";
	}

}

echo $str;

?>
</body></html>
