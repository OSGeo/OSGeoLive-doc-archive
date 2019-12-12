
<?php
# $Id: mod_monitorCapabilities_read.php 517 2006-11-21 12:37:01Z christoph $
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
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_user.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<style type="text/css">
*{margin:0;padding:0;font-size:1em;font-family:Arial,Helvetica,"Sans Serif" !important;}
a:link {
COLOR: #000000;
text-decoration:none;
font-weight:bold;
}
a:visited {
COLOR: #000000;
text-decoration:none;
font-weight:bold;
}
a:hover {
COLOR: #871d33;
text-decoration:underline;
font-weight:bold;
}
a:active {
COLOR: #000000;
text-decoration:none;
font-weight:bold;
}
</style> 
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
</head>
<body>
<?php
if (isset($_REQUEST["languageCode"]) & $_REQUEST["languageCode"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["languageCode"];
	if (!($testMatch == 'de' or $testMatch == 'fr' or $testMatch == 'en')){ 
		echo 'languageCode: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$languageCode = $testMatch;
	$testMatch = NULL;
	Mapbender::session()->set("mb_lang",$languageCode);
	//set session var to languageCode
	$localeObj = new Mb_locale(Mapbender::session()->get("mb_lang"));
}

$admin = new administration();
$user = new User();
//echo $user->id."<br>";
//only logged in user can see their subscribed services
$sql = "SELECT DISTINCT mb_wms_availability.fkey_wms_id FROM mb_wms_availability,mb_user_abo_ows WHERE mb_wms_availability.fkey_wms_id=mb_user_abo_ows.fkey_wms_id AND mb_user_abo_ows.fkey_mb_user_id=$1";
$res = db_prep_query($sql, array($user->id), array("i"));
$cnt = 0;
$wms = array();

while(db_fetch_row($res)){
	$wms[$cnt] = db_result($res,$cnt,"fkey_wms_id");
	
	$cnt++;
}

$status = array();
$upload_id = array();
for ($i=0; $i<count($wms); $i++) {
	$wms_id[$wms[$i]] = $wms[$i];
        // get layer id
        $sql = "select layer_id from layer where fkey_wms_id= $1 and layer_pos=0";
        $v = array($wms[$i]);
        $t = array('i');
        $res = db_prep_query($sql,$v,$t);
        $layer_id[$wms[$i]] = db_result($res,0,0);
	$sql = "SELECT fkey_upload_id,last_status, status_comment, upload_url,availability ,average_resp_time, map_url, image FROM mb_wms_availability ";
	$sql .= "WHERE fkey_wms_id = $1";
	$v = array($wms_id[$wms[$i]]);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
        $avg_response_time[$wms[$i]] = round(db_result($res,0,"average_resp_time"),1);
	$status[$wms[$i]] = intval(db_result($res,0,"last_status"));
	$comment[$wms[$i]] = db_result($res,0,"status_comment");
	$upload_url[$wms[$i]] = db_result($res,0,"upload_url");
	$percentage[$wms[$i]]=db_result($res,0,"availability");
	$upload_id[$wms[$i]] = db_result($res,0,"fkey_upload_id");
	$image[$wms[$i]] =db_result($res,0,"image");
	$map_url[$wms[$i]] =db_result($res,0,"map_url");
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
	elseif ($_GET['sortby'] == "image") {
		$newArray = $image;
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
}

$str .= "<form name = 'form1' method='post' action='".$_SERVER["SCRIPT_NAME"]."?sortby=".$_GET['sortby']."'>\n\t";
$str .= "<table cellpadding=10 cellspacing=0 border=0 style='font-family:verdana;font-size:0.8em;'>";
$str .= "<tr bgcolor='#f0f0f0'><th align='left'><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=wms'>"._mb("WebMapService")."</a></th>";
$str .= "<th align='left' colspan = 2><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=status'>"._mb("Availability of Capabilities")."</a></th>";//Verfügbarkeit Dienstebeschreibung
$str .= "<th align='left'><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=avgresp'>"._mb("Average Response Time for GetCapabilities")."</a></th>";//Durchschnittliche Antwortzeit Beschreibung
$str .= "<th align='left'><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=image'>"._mb("Availability of GetMap")."</a></th>";//Verfügbarkeit Kartenbild
$str .= "<th align='left'><a href='".$_SERVER["SCRIPT_NAME"]."?sortby=avail'>"._mb("Availability (2 month)")."</a></th><th></th></tr>";//Verfügbarkeit (2 Monate)
$cnt = 0;

foreach ($newArray as $k => $value) {
	$img = "stop.png";
	$img_map = "nopicture.png";
	if ($status[$k]==0) $img = "wait.png";
	elseif ($status[$k]==1) $img = "go.png";
	if ($image[$k]==1) $img_map = "picture.png";

	if ($updated[$k] == "0" && $status[$k] == 0) $fill = "checked"; else $fill = "disabled";

	if (fmod($cnt, 2) == 1) {
		$str .= "\n\t\t<tr bgcolor='#e6e6e6'>";
	}
	else {
		$str .= "\n\t\t<tr bgcolor='#f0f0f0'>";
	}

$str .= "\n\t\t\t<td valign='top'><a href='../php/mod_showMetadata.php?resource=layer&id=".$layer_id[$k]."&subscribe=1' onclick='var metadataWindow=window.open(this.href,'"._mb("Metadata")."','width=500,height=600,left=100,top=200,scrollbars=yes ,dependent=yes'); metadataWindow.focus();newWindow.href.location='test.php';  >"._mb("Service ID").": ".$wms_id[$k]."</a><br>".$admin->getWmsTitleByWmsId($wms_id[$k])."</td>";
	$str .= "\n\t\t\t<td valign='top'><a href='".$upload_url[$k]."' target=_blank><img title='"._mb("Request GetCapabilities")."' border=0 src = '../img/trafficlights/". $img. "'></a></td>";//Aufruf des Capabilities Dokument
	$str .= "\n\t\t\t<td valign='top'>" . $comment[$k] . "<br><div style='font-size:12'>".date("F j, Y, G:i:s", $upload_id[$k])."</div></td>";
	$str .= "\n\t\t\t<td valign='top' align = 'left'>";
	if ($avg_response_time[$k] == NULL) {
		$str .= "n/a";
	}
	else {  	
		if($avg_response_time[$k] == 0){
		$str .= "< 1 s";
		}
		else
		{
			$str .= $avg_response_time[$k] . " s";
		}
	}
	$str .= "</td>";
	$str .= "\n\t\t\t<td valign='top'>";

	$str .= "<a href='".$map_url[$k]."' target=_blank><img title='"._mb("Tested GetMap Request")."' border=0 src = '../img/trafficlights/". $img_map. "'></a>";//Getesteter GetMap Aufruf

	$str .= "</td>";
	//$str .= "\n\t\t\t<td valign='top'><b>" . $percentage[$k] . " %</b>&nbsp;&nbsp;<span style='font-size:12'>(" . $total[$k] . " "._mb('cycles').")</span><br>";
	$str .= "\n\t\t\t<td valign='top'><b>" . $percentage[$k] . " %</b>&nbsp;&nbsp;<br>";
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
	
	$str .= "\n\t\t<td><input type=button value='details' onclick=\"var newWindow = window.open('../php/mod_monitorCapabilities_read_single.php?wmsid=".$wms_id[$k]."','wms','width=500,height=700,scrollbars');newWindow.href.location='test.php'\"></td></tr>";
	$cnt++;
}
$str .= "\n\t</table>\n\t<br/>\n</form>";
echo $str;

?>
</body></html>
