<?php
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
require_once(dirname(__FILE__)."/../core/globalSettings.php");
require_once(dirname(__FILE__)."/../http/classes/class_administration.php");
require_once(dirname(__FILE__)."/../http/classes/class_user.php");
$admin = new administration();
function getRootLayerId ($wms_id) {
	$sql = "SELECT layer_id FROM layer, wms " . 
		"WHERE wms.wms_id = layer.fkey_wms_id AND layer_pos='0' " . 
		"AND wms.wms_id = $1";
	$v=array($wms_id);
	$t=array('i');
	$res=db_prep_query($sql,$v,$t);
	$row=db_fetch_array($res);
	return $row ? $row["layer_id"] : null;
}
//Read out user which have subscribed some services (abo)
$sql = "SELECT DISTINCT fkey_mb_user_id FROM mb_user_abo_ows";
$res = db_query($sql);
$cnt = 0;
$user_id_all=array();
while($row = db_fetch_array($res)){
	echo $cnt."\n";
	$user_id_all[] = $row["fkey_mb_user_id"];
	echo "User_id: ". $row["fkey_mb_user_id"] ."\n";
	$cnt++;
}
$cnt=0;
$mail_user_topic = _mb("Mapbender subscribers notification"); // "GeoPortal.rlp Mitteilung WMS Abonnenten"
$number_of_users_text = _mb("Number of users"); // "Zahl der eingetragenen User IDs"
$subscribed_wms_text = _mb("All WMS subscribed by this user"); // "Alle abonnierten WMS des Users"
$wms_with_problems_text = _mb("WMS with problems"); // "WMS mit Problemen"
$metadata_text = _mb("Metadata"); // Metadaten
$wms_unreachable_text = "WMS '%s' with ID %d unreachable!"; // "WMS %s mit ID %d nicht erreichbar!"
$body_text = _mb("Mapbender was unable to access the services listed above. " . 
	"These service may be unreachable on short notice. Please contact the " . 
	"service provider listed in the service metadata. You will find the " . 
	"metadata by following the link mentioned above. " . 
	"Note: This e-mail has been sent automatically because you subscribed " . 
	"to this service. You can unsubscribe by logging in and clicking the " . 
	"unsubscribe button in the Mapbender metadata dialogue."
); 
// Das Geoportal.rlp hat Probleme beim Zugriff auf die oben in dieser E-Mail 
// genannten Dienste. Es ist möglich, dass diese kurzfristig nicht verfügbar 
// sind. Weitere Informationen erhalten Sie auf Anfrage beim 
// Dienstebereitsteller, der in den Metadaten des Dienstes angegeben ist. 
// Folgen Sie dazu dem oben in dieser E-Mail aufgeführten Link.\n 
// Hinweis: Diese E-Mail wurde automatisiert erzeugt und der Versand von 
// Ihnen beantragt. Diese E-Mail-Benachrichtigung können Sie jederzeit 
// abbestellen, indem Sie das Abonnement über die Metadatenanzeige im 
// GeoPortal.rlp deaktivieren.\n http://www.geoportal.rlp.de				
echo "\n" . $number_of_users_text . ": " . count($user_id_all) . "\n";
for ($iz = 0; $iz < count($user_id_all); $iz++) {
	$userid = $user_id_all[$iz];
	echo "User: ".$userid."\n";
	//read out services from mb_user_abo_ows
	$sql="SELECT fkey_wms_id FROM mb_user_abo_ows WHERE fkey_mb_user_id = $1";
	$v=array($userid);
	$t=array('i');
	$res=db_prep_query($sql,$v,$t);
	$cnt = 0;
	//initialize array
	$wms_id_all=array();
	echo $subscribed_wms_text . ":\n";
	//read results
	while($row = db_fetch_array($res)){
		$wms_id_all[$cnt] = $row["fkey_wms_id"];
		$cnt++;
	}
	echo "WMS: " . implode(",", $wms_id_all) . "\n";
	$mailhead="";
	$body="";
	//read results from mb_monitor
	for ($iz2=0; $iz2<count($wms_id_all);$iz2++){
		$wmsid = $wms_id_all[$iz2];
		$sql = "SELECT status, status_comment, to_timestamp(timestamp_end) " . 
			"AS timestamp_end FROM mb_monitor WHERE fkey_wms_id = $1 " . 
			"ORDER BY timestamp_end DESC LIMIT 1";
		$v = array($wmsid);
		$t = array('i');
		$res=db_prep_query($sql,$v,$t);
		//read results
		$row = db_fetch_array($res);
		$wms_monitor_status = $row["status"];
		$wms_monitor_status_comment = $row["status_comment"];
		$wms_monitor_timestamp_end = $row["timestamp_end"];
		echo $wms_with_problems_text . ":\n";
		#read wms_title
		$sql="SELECT wms_title FROM wms WHERE wms_id = $1";
		$v_wms_t = array($wmsid);
		$t_wms_t = array('i');
		$res_wms_t = db_prep_query($sql, $v_wms_t, $t_wms_t);
		$row_wms_t = db_fetch_array($res_wms_t);
		if ($wms_monitor_status == '-1') {
			if (isset(MAPBENDER_PATH) && MAPBENDER_PATH != '') {
				$metadataUrl = MAPBENDER_PATH."/php/mod_showMetadata.php?resource=layer&id=";
			} else {
				$metadataUrl = preg_replace(
					"/(.*)frames\/login.php/", 
					"$1php/mod_showMetadata.php?resource=layer&id=", 
					LOGIN
				);
			}
			echo "WMS: ".$wmsid."\n";
			$body .= _mb($wms_unreachable_text, $row_wms_t["wms_title"], $wmsid) . 
				" (" . $wms_monitor_timestamp_end . ")\n" . 
				$metadata_text . ": " . $metadataUrl . getRootLayerId ($wmsid)."\n\n";
		}			
	}
	//send mail to subscribers (if a service (capabilities doc) is not available)
	if ($body) {
		$body .= "\n" . $body_text;
		$time = strval(time()-2);
		$error_msg = "";
		if ($admin->getEmailByUserId($userid)) {
			$admin->sendEmail(
				MAILADMIN, 
				MAILADMINNAME, 
				$admin->getEmailByUserId($userid), 
				$user, 
				$mail_user_topic . " " . date("F j, Y, G:i:s"), 
				utf8_decode($body), 
				&$error_msg
			);
		}
		else {
			$error_msg = _mb(
				"Email address of user '%d' unknown!", 
				$admin->getUserNameByUserId($userid)
				) . "\n";
		}
		if ($error_msg) {
			echo "\n ERROR: " . $error_msg;
		}
	}
}
		
?>
