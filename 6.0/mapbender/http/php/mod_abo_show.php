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

$e_id = "monitor_abo_show";
#require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_user.php");
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

$mail_admin_recipient = _mb("Mapbender Service Provider"); // "Diensteanbieter Geoportal.rlp"
$mail_admin_topic = _mb("Confirmation of subscribers notification"); // "Versandmitteilung Abo-Sammelmail"
$mail_admin_body = "You have sent the following mail to %d subscribers of your services"; // "Sie haben folgende Mail an %1\$s Abonnenten Ihrer Dienste versandt"
$mail_user_recipient = _mb("Mapbender monitoring subscriber"); // "Abo Nutzer Geoportal.rlp"
$mail_user_topic = _mb("Mapbender subscribers notification"); // "GeoPortal.rlp Mitteilung WMS Abonennten"
$email_body_info = _mb("The following services are affected"); // "Die versandten Informationen betreffen folgende im Geoportal.rlp registrierten Dienste"
$email_form_text = _mb("e-mail form for notifications to the subscribers of the following WMS"); // "EMail Formular f&uuml;r die Benachrichtung der Abonnenten folgender WMS Dienste"
$email_body_footer = _mb("Subscriber notification for Mapbender service providers"); // "Abo-Benachrichtigungsfunktion für Anbieter von Diensten im GeoPortal.rlp (http://www.geoportal.rlp.de)"
$email_sent_text = _mb("Notifications have been sent. You will receive a confirmation mail shortly."); // "Die Emails wurden versendet. Sie erhalten in Kürze eine Kontrollmail!"
$email_create_text = _mb("Create mail form"); // "EMail Formular generieren"
$wms_list_text = _mb("The subscribers to these WMS have been notified"); // "Liste der WMS-IDs deren Nutzer benachrichtigt wurden"
$further_inquiry_text = _mb("For further inquiries contact"); // "Rückfragen bitte an"
$mail_header = _mb("Subscriber notification"); // "EMail Benachrichtigung der Abonnenten"
$number_of_subscribers_text = _mb("# subscribers"); // "# Anzahl Abonnenten"
$notify_text = _mb("Notify"); // "Mailversand"
$mail_send_text = _mb("Send mail"); // "Mail versenden"

// WMS selected for notifications
$checkboxes = intval($_POST['cbs']);
if ($checkboxes > 0 || isset($_POST['wmslist'])){
	//initialize list
	$wms_id_list = "";	
	#get values for checked wms
	for ($i=0; $i < $checkboxes; $i++) {
		if (isset($_POST['cb'.$i])) {
			$mail_wmsid = intval($_POST['cb'.$i]); 
			if ($user->isWmsOwner($mail_wmsid)) {
				$wms_id_list .= "," . $mail_wmsid;
			}
		}
	}
	$wms_id_list=ltrim($wms_id_list, ",");

	if (!isset($_POST['wmslist'])){
		//do descriptive header
		echo "<h3>" . 
			htmlentities(
				$email_form_text,
				ENT_QUOTES,
				CHARSET
			) . ":</h3>";
		//Show wmslist to be noticed
		echo $wms_id_list;
		//generate form to send email
		echo "<form name = 'form2' method='post' action='".$_SERVER["SCRIPT_NAME"]."'>";
		//textfield for text to send
		echo "<br><textarea name='emailtext' cols='120' rows='20'></textarea>";
		//submit button for send email
		echo  "<br><input type=submit value='" . $mail_send_text . "'><br/>";
		#Liste der WMS_ID's in hidden-Feld übergeben
		echo "<input type=hidden name='wmslist' id='wmslist' value='".$wms_id_list."'>";
	}
	//if button send mail is pressed - send mails
	else {
	
		//create body for serviceurl list:
		$body_urllist = "\n\n" . 
			$email_body_info . 
			":\n";
		if (preg_match("/^[0-9]+(,[0-9]+)*$/", $_POST['wmslist'])) {
			$wms_array = explode(',',$_POST['wmslist']);

			$metadataUrl = preg_replace("/(.*)frames\/login.php/", "$1php/mod_showMetadata.php?resource=layer&id=", LOGIN);

			for ($i=0; $i<count($wms_array); $i++) {
				// get layer id
	       		$sql = "select layer_id from layer where fkey_wms_id= $1 and layer_pos = 0";
	       		$v = array($wms_array[$i]);
	        	$t = array('i');
	        	$res = db_prep_query($sql,$v,$t);
	
				$layerid_body=db_result($res,0,0);
	        	$body_urllist .= $metadataUrl . $layerid_body . "\n";
			}
			$body_urllist.="\n" . 
				$email_body_footer . 
				"\n";
		
			//get user email adresses of abo-user
			$sql = "select mb_user_email from mb_user, ";
			$sql .= "(select distinct fkey_mb_user_id from mb_user_abo_ows " . 
				"where fkey_wms_id in (".$_POST['wmslist'].")) as abo_user ";
			$sql .= "where abo_user.fkey_mb_user_id=mb_user.mb_user_id";
			$res=db_query($sql);
			$cnt = 0;
			//Initialisieren des Arrays
			$user_email=array();
			//Herauslesen der Ergebnisse
			//echo "wmslist ist gesetzt!\n<br>";
			echo "<h3>" . 
				htmlentities(
					$wms_list_text,
					ENT_QUOTES,
					CHARSET
				) . ":</h3> ".$_POST['wmslist']."\n<br><br>";
			//echo "wms_id_list: ".$wms_id_list."\n<br>";
			//get email of wms owner
			$mail_wms_owner=$admin->getEmailByUserId($user->id);
			while($row = db_fetch_array($res)){
				//echo $cnt;
				$user_email[$cnt] = $row["mb_user_email"];
		
				$admin->sendEmail(
					$mail_wms_owner, 
					$mail_wms_owner, 
					$user_email[$cnt], 
					$mail_user_recipient,
					$mail_user_topic . " " . date("F j, Y, G:i:s"), 
					utf8_decode(strip_tags($_POST['emailtext']) . "\n\n" . $further_inquiry_text . 
					": " . $mail_wms_owner . "\n" . $body_urllist));
				$cnt++;
			}
			//controll mail for wms_owner
			$admin->sendEmail(
				MAILADMIN,
				MAILADMINNAME, 
				$mail_wms_owner, 
				$mail_admin_recipient,
				$mail_admin_topic . " " . date("F j, Y, G:i:s"), 
				utf8_decode(_mb($mail_admin_body, $cnt).
				"\n\n" . 
				strip_tags($_POST['emailtext']) . $body_urllist));
		
		
			echo "<br>" . 
				htmlentities(
					$email_sent_text,
					ENT_QUOTES,
					CHARSET
				) . "<br>";
		}
	
	}
	echo "</form>\n";
}
else {

	//
	// monitoring results
	//
	$sql = "SELECT DISTINCT mb_monitor.fkey_wms_id FROM mb_monitor, wms " . 
		"WHERE mb_monitor.fkey_wms_id = wms.wms_id AND wms.wms_owner = $1"; 
	$res = db_prep_query($sql, array($user->id), array("i"));
	
	$wms = array();
	while($row = db_fetch_array($res)){
		$wms[] = $row["fkey_wms_id"];
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
	
		#Schleife zur Zaehlung der user die den jeweiligen Dienst abonniert haben
		$sql = "select count(*) from mb_user_abo_ows where fkey_wms_id=$1";
	        $v = array($wms[$i]);
	        $t = array('i');
	        $res = db_prep_query($sql,$v,$t);
	        $abo_count[$wms[$i]] = db_result($res,0,0);
		
		$sql = "SELECT fkey_upload_id,last_status, status_comment, " . 
			"upload_url, availability, average_resp_time " . 
			"FROM mb_wms_availability WHERE fkey_wms_id = $1";
		$v = array($wms_id[$wms[$i]]);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
	    $avg_response_time[$wms[$i]] = round(db_result($res,0,"average_resp_time"),1);
		$status[$wms[$i]] = intval(db_result($res,0,"last_status"));
		$comment[$wms[$i]] = db_result($res,0,"status_comment");
		$upload_url[$wms[$i]] = db_result($res,0,"upload_url");
		$percentage[$wms[$i]]=db_result($res,0,"availability");
		$upload_id[$wms[$i]] = db_result($res,0,"fkey_upload_id");
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
	}
	
	
	
	$str = "<span style='font-size:30'>" . 
		$mail_header . 
		"</span><hr><br>\n" . 
		"<form name = 'form1' method='post' action='" . $_SERVER["SCRIPT_NAME"] . "?sortby=" . 
		$_GET['sortby']."&elementID=monitor_abo_show'>\n\t" . 
		"\n\t<input type=submit value='" . $email_create_text . 
		"'>\n<br/><br/>\n" . 
		"<table cellpadding=10 cellspacing=0 border=0>" . 
		"<tr bgcolor='#dddddd'>" . 
		"<th align='left'>" . $notify_text . "</a></th>" . 
		"<th align='left'><a href='" . $_SERVER["SCRIPT_NAME"] . "?sortby=wms&elementID=monitor_abo_show'>wms</a></th>" . 
		"<th align='left' colspan = 2><a href='" . $_SERVER["SCRIPT_NAME"] . 
		"?sortby=status&elementID=monitor_abo_show'>current status</a></th>" . 
		"<th align='left'><a href='" . $_SERVER["SCRIPT_NAME"] . 
		"?sortby=avgresp&elementID=monitor_abo_show'>avg. response time</a></th>" . 
		"<th align='left'><a href='" . $_SERVER["SCRIPT_NAME"] . 
		"?sortby=avail&elementID=monitor_abo_show'>overall availability</a></th>" .
		"<th align='left'>" . $number_of_subscribers_text . "</th><th></th>" . 
		"</tr>";
	

	$cnt = 0;
	foreach ($newArray as $k => $value) {
		$img = "stop.bmp";
		if ($status[$k]==0) $img = "wait.bmp";
		elseif ($status[$k]==1) $img = "go.bmp";
	
		#if ($updated[$k] == "0" && $status[$k] == 0) $fill = "checked"; else $fill = "disabled";
	
		if (fmod($cnt, 2) == 1) {
			$str .= "\n\t\t<tr bgcolor='#e6e6e6'>";
		}
		else {
			$str .= "\n\t\t<tr bgcolor='#f0f0f0'>";
		}
	#generieren der checkboxen für jeden wms namen: cb123, wert 123, der andere input fuer upl_id123 mit wert des upload_ids aus der monitor tabelle - unnoetig
		$str .= "\n\t\t\t<td><input name='cb".$cnt."' value='" . $wms_id[$k] . "' type=checkbox  />";
	#<input type=hidden name='upl_id".$cnt."' value='".$upload_id[$k]."'>
		$str .= "</td>";
	$str .= "\n\t\t\t<td valign='top'><a href='../php/mod_showMetadata.php?resource=layer&id=" . $layer_id[$k] ."' onclick='window.open(this.href,'Metadaten','width=500,height=600,left=100,top=200,scrollbars=yes ,dependent=yes'); return false' target='_blank' >Dienst ID: ". $wms_id[$k] ."</a><br>" . $admin->getWmsTitleByWmsId($wms_id[$k]) . "</td>";
		#$str .= "\n\t\t\t<td valign='top'><b>" . $wms_id[$k] . "</b><br>" . $admin->getWmsTitleByWmsId($wms_id[$k]) . "</td>";
		$str .= "\n\t\t\t<td valign='top'><a href='".$upload_url[$k]."' target=_blank><img title='Connect to service' border=0 src = '../img/trafficlights/". $img. "'></a></td>";
		$str .= "\n\t\t\t<td valign='top'>" . $comment[$k] . "<br><div style='font-size:12'>".date("F j, Y, G:i:s", $upload_id[$k])."</div></td>";
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
		
		$str .= "\n\t\t\t<td>".$abo_count[$k]."</td>";
		$str .= "\n\t\t<td><input type=button value='details' onclick=\"var newWindow = window.open('../php/mod_monitorCapabilities_read_single.php?wmsid=".$wms_id[$k]."','wms','width=500,height=700,scrollbars');newWindow.href.location='test.php'\"></td></tr>";
		$cnt++;
	}


	$str .= "\n\t</table>\n\t<br/><input type=hidden name=cbs value='".$cnt."'>\n</form>";
	echo $str;
}
?>
</body></html>
