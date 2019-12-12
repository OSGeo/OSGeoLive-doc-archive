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

require_once dirname(__FILE__) ."/../core/globalSettings.php";
require_once dirname(__FILE__) ."/../http/classes/class_administration.php";
require_once dirname(__FILE__) ."/../tools/mod_monitorCapabilities_defineGetMapBbox.php";
require_once dirname(__FILE__) ."/../http/classes/class_bbox.php";
require_once(dirname(__FILE__)."/../http/classes/class_mb_exception.php");

#do db close at the most reasonable point 
$admin = new administration();

$user = null;
$group = null;
$application = null;

$cl = false;

// can be replaced by existing functionality
function getConjunctionCharacter($onlineresource) {
	if(strstr($onlineresource, "?")) {
		$lastChar = substr($onlineresource,strlen($onlineresource)-1, 1);  
		if ($lastChar == "?" || $lastChar == "&") {return "";}
		else{return "&";}
	}
	else {return "?";} 
}

function getTagOutOfXML($reportFile,$tagName) {
	$xml=simplexml_load_file($reportFile);
	$result=(string)$xml->wms->$tagName;
	return $result;
}

//command line
if ($_SERVER["argc"] > 0) {
	$cl = true;

	if ($_SERVER["argc"] > 1 && $_SERVER["argv"][1] !== "") {
		$param = $_SERVER["argv"][1];
		if (substr($param, 0,5) == "user:") {
			$user = substr($param, 5);
		}
		if (substr($param, 0,6) == "group:") {
			$group = substr($param, 6);
		}
	}
	else {
		echo _mb("Specify a user ID or a group ID to monitor.") . "\n\n";
		echo "php5 <script name> user:<user_id> \n\n";
		echo "php5 <script name> group:<group_id> \n\n";
		die;
	}
}
//browser
else if ($_GET['user'] || $_GET['group'] || $_GET['app']) {
	$user = $_GET['user'] ? intval($_GET['user']) : null;
	$group = $_GET['group'] ? intval($_GET['group']) : null;
}
else {
	echo _mb("Please specify a user ID or a group ID!") . " ";
	echo _mb("You can pass the GET arguments 'user' or 'group'!");
	die;
}

$br = "<br><br>";
if ($cl) {
	$br = "\n\n";
}

$e = new mb_notice("mod_monitorCapabilities_main.php: group: ".$group);

$userIdArray = array();

//loop for doing the monitor for all registrating institutions ****************
if (!is_null($group)) {
	echo "monitoring " . $group;
	if (!is_numeric($group)) {
		echo _mb("Parameter 'group' must be numeric.");
		die;
	}

	//read out user who are subadmins - only their services are controlled - til now
	$sql = "SELECT fkey_mb_user_id FROM mb_user_mb_group WHERE " . 
		"fkey_mb_group_id = (SELECT mb_group_id FROM mb_group WHERE " . 
		"mb_group_id = $1)";

	$v = array($group);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);

	$userIdArray = array();
	while ($row = db_fetch_array($res)) {
		$userIdArray[] = $row["fkey_mb_user_id"];
	}
}
else if (!is_null($user)) {
	if (!is_numeric($user)) {
		echo _mb("Parameter 'user' must be numeric.");
		die;
	}
	$userIdArray = array($user);
}
else {
	if ($_SERVER["argc"] > 0) {
		echo _mb("Specify a user ID or a group ID to monitor.") . "\n\n";
		echo "php5 <script name> user:<user_id> \n\n";
		echo "php5 <script name> group:<group_id> \n\n";
	}
	else {
		echo _mb("Please specify a user ID or a group ID!") . " ";
		echo _mb("You can pass the GET arguments 'user' or 'group'!");
	}
	die;
}

if (count($userIdArray) === 0) {
	echo _mb("No user found for the given parameters.");

	die;
}

$user_id_all = $userIdArray;
echo $br ."Count of registrating users: " . count($user_id_all) . $br;
$e = new mb_notice("mod_monitorCapabilities_main.php: count of group members: ".count($user_id_all));
$time_array = array();

for ($iz = 0; $iz < count($user_id_all); $iz++) {
	$userid = $user_id_all[$iz];

	//get all owned wms
	$wms_id_own = $admin->getWmsByWmsOwner($userid);

	// initialize monitoring processes
	echo "Starting monitoring cycle...$br";
	echo "WMS services are requested for availability.$br"; 
	echo "Capabilities documents are requested and compared to the infos in the service db.$br";
	$e = new mb_notice("mod_monitorCapabilities_main.php: monitoring for user: ".$userid);
	//new: time user-monitoring cycle must stored in array
	$time_array[$userid] = strval(time());
	//wait 2 seconds to give enough time between to different users the time can differ also for one user!
	sleep(1);

	$time = $time_array[$userid];

	for ($k=0; $k<count($wms_id_own); $k++) {
		
		//get relevant data out of registry
		$sql = "SELECT wms_upload_url, wms_getcapabilities_doc, " . 
			"wms_version, wms_getcapabilities, wms_getmap FROM wms " . 
			"WHERE wms_id = $1";
		$v = array($wms_id_own[$k]);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		$someArray = db_fetch_array($res);
		$url = $someArray['wms_upload_url'];
		
		$capDoc = $someArray['wms_getcapabilities_doc'];
		$version = $someArray['wms_version'];
		$capabilities = $someArray['wms_getcapabilities'];
		$getmap = $someArray['wms_getmap'];
		$getMapUrl = getMapRequest($wms_id_own[$k], $version, $getmap);

		// for the case when there is no upload url - however - we need the 
		// url to the capabilities file
   		if (!$url || $url == "") {
			$capabilities=$admin->checkURL($capabilities);
			if ($version == "1.0.0" ) {
				$url = $capabilities . "REQUEST=capabilities&WMTVER=1.0.0";
			}
			else {
				$url = $capabilities . "REQUEST=GetCapabilities&" . 
					"SERVICE=WMS&VERSION=" . $version;
			}
   		}
		//$url is the url to the service which should be monitored in this cycle
		//initialize monitoriung in db (set status=-2)
		echo "initialize monitoring for user: " . $userid . 
			" WMS: " . $wms_id_own[$k] . $br;
		$e = new mb_notice("mod_monitorCapabilities_main.php: wms: ".$wms_id_own[$k]);
		$sql = "INSERT INTO mb_monitor (upload_id, fkey_wms_id, " . 
				"status, status_comment, timestamp_begin, timestamp_end, " . 
				"upload_url, updated)";
		$sql .= "VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";

		$v = array(
			$time,
			$wms_id_own[$k],
			"-2",
			"Monitoring is still in progress...", 
			time(),
			"0",
			$url,
			"0"
		);
		$t = array('s', 'i', 's', 's', 's', 's', 's', 's');
		$res = db_prep_query($sql,$v,$t);

		// Decode orig capabilities out of db cause they are converted before 
		// saving them while upload
		$capDoc=$admin->char_decode($capDoc);

		// do the next to exchange the update before by another behavior! - 
		// look in class_monitor.php !
		$currentFilename = "wms_monitor_report_" . $time . "_" . 
			$wms_id_own[$k] . "_" . $userid . ".xml";
 		$report = fopen(dirname(__FILE__)."/tmp/".$currentFilename,"a");
		//$e = new mb_notice("mod_monitorCapabilities_main.php: currentFilename: ".dirname(__FILE__)."/tmp/".$currentFilename);
		$lb = chr(13).chr(10);

		fwrite($report,"<monitorreport>".$lb);
		fwrite($report,"<wms>".$lb);
		fwrite($report,"<wms_id>".$wms_id_own[$k]."</wms_id>".$lb);
		fwrite($report,"<upload_id>".$time."</upload_id>".$lb);
		fwrite($report,"<getcapbegin></getcapbegin>".$lb);
		fwrite($report,"<getcapurl>".urlencode($url)."</getcapurl>".$lb);
		fwrite($report,"<getcapdoclocal>".urlencode($capDoc)."</getcapdoclocal>".$lb);
		fwrite($report,"<getcapdocremote></getcapdocremote>".$lb);
		fwrite($report,"<getcapdiff></getcapdiff>".$lb);
		fwrite($report,"<getcapend></getcapend>".$lb);
		fwrite($report,"<getcapduration></getcapduration>".$lb);
		fwrite($report,"<getmapurl>".urlencode($getMapUrl)."</getmapurl>".$lb);
		fwrite($report,"<status>-2</status>".$lb);
		fwrite($report,"<image></image>".$lb);
		fwrite($report,"<comment>Monitoring in progress...</comment>".$lb);
		fwrite($report,"<timeend></timeend>".$lb);
		fwrite($report,"</wms>".$lb);
		fwrite($report,"</monitorreport>".$lb);
		fclose($report);

		// start of the monitoring processes on shell 
		// (maybe problematic for windows os)
		//$e = new mb_notice("mod_monitorCapabilities_main.php: php call: ".$exec);
   		$exec = PHP_PATH . "php5 " . dirname(__FILE__) . "/mod_monitorCapabilities_write.php " . 
			$currentFilename . " 0 > /dev/null &";
		/*
		 * @security_patch exec done
		 * Added escapeshellcmd()
		 */
   		#exec(escapeshellcmd($exec));TODO what goes wrong here?
		exec($exec);
	}
	echo "Monitoring start cycle for user: ".$userid." has ended. " . 
		"(Altogether: " . count($wms_id_own) . " WMS monitorings started).$br";
}
//set time limit (mapbender.conf)
set_time_limit(TIME_LIMIT);
// wait until all monitoring processes are finished
echo "please wait " . TIME_LIMIT . " seconds for the monitoring to finish...$br";

sleep(TIME_LIMIT);
//when time limit has ended: begin to collect results for every registrating user
for ($iz = 0; $iz < count($user_id_all); $iz++) {
	// when time limit has ended: begin to collect results for every 
	// registrating user
	$problemOWS = array();//define array with id's of problematic wms
	$commentProblemOWS = array();
	$userid = $user_id_all[$iz];
	//get the old upload_id from the monitoring to identify it in the database
	$time = $time_array[$userid];	
	//read sequencialy all user owned xml files from tmp and update the 
	// records in the database 
	$wms_id_own = $admin->getWmsByWmsOwner($userid);
	for ($k = 0; $k < count($wms_id_own); $k++) {
		
		$monitorFile = "./tmp/wms_monitor_report_" . $time . "_" . 
			$wms_id_own[$k] . "_".$userid.".xml";
		$e = new mb_notice("mod_monitorCapabilities_main.php: look for following file: ".$monitorFile);
		$status = getTagOutOfXML($monitorFile,"status");
		$status_comment = getTagOutOfXML($monitorFile,"comment");
		$cap_diff = getTagOutOfXML($monitorFile,"getcapdiff");
		$image = getTagOutOfXML($monitorFile,"image");
		$map_url = urldecode(getTagOutOfXML($monitorFile,"getmapurl"));
		$timestamp_begin = getTagOutOfXML($monitorFile,"getcapbegin");
		$timestamp_end = getTagOutOfXML($monitorFile,"getcapend");

		$sql = "UPDATE mb_monitor SET updated = $1, status = $2, " . 
			"image = $3, status_comment = $4, timestamp_end = $5, " . 
			"map_url = $6 , timestamp_begin = $7, cap_diff = $8 " . 
			"WHERE upload_id = $9 AND fkey_wms_id=$10 ";

		// check if status = -2 return new comment and status -1, 
		// push into problematic array
		if ($status == '-1' or $status == '-2') {
			$status_comment = "Monitoring process timed out.";
			$status = '-1';
			array_push($problemOWS,$wms_id_own[$k]);
			array_push($commentProblemOWS,$status_comment);
		} 

		$v = array(
			'0', 
			intval($status), 
			intval($image), 
			$status_comment, 
			(string)intval($timestamp_end), 
			$map_url, 
			(string)intval($timestamp_begin), 
			$cap_diff,
			(string)$time, 
			$wms_id_own[$k]
		);
		$t = array('s', 'i', 'i', 's', 's', 's', 's', 's','s','s');
		$res = db_prep_query($sql,$v,$t);
	}
	$body = "";
	echo "\nmonitoring info in db for user: ".$userid."\n";
	//loop for single monitor requests that has problems
	for ($i=0; $i < count($problemOWS); $i++) {
		$body .= $br . $admin->getWmsTitleByWmsId($problemOWS[$i]) . 
			" (" . $problemOWS[$i] . "): " . $commentProblemOWS[$i] . $br;
	}
	unset($problemOWS);
	unset($commentProblemOWS);
	//end of loop for single monitor requests
	// Send an email to the user if body string exists
	if ($body) {
		$error_msg = "";
		if ($admin->getEmailByUserId($userid)) {
			$admin->sendEmail(
				MAILADMIN, 
				MAILADMINNAME, 
				$admin->getEmailByUserId($userid), 
				$user, 
				"Mapbender monitoring report " . date("F j, Y, G:i:s", $time), 
				utf8_decode($body), 
				&$error_msg
			);
		}
		else {
			$error_msg = "Email address of user '" . 
				$admin->getUserNameByUserId($userid) . 
				"' unknown!\n";
		}
		if ($error_msg) {
			echo "\n ERROR: " . $error_msg;
		}
	}
}
?>
