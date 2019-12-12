<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__) . "/../classes/class_wmc.php");
require_once(dirname(__FILE__) . "/../classes/class_kml.php");
require_once(dirname(__FILE__) . "/../classes/class_administration.php");

if (Mapbender::session()->get("mb_myWmc") && Mapbender::session()->get("mb_myWmc_action") && 
	in_array(Mapbender::session()->get("mb_myWmc_action"), 
	array("load", "merge", "append"))) {
	
	$action = Mapbender::session()->get("mb_myWmc_action");
	$wmc_id = Mapbender::session()->get("mb_myWmc");
}
$user = Mapbender::session()->get("mb_user_id");
$meetingPointId = Mapbender::session()->get("mb_myKml");

$x = false;
$y = false;
$icon = false;
$alt = false;
$url = false;

$adm = new administration();

//KML
if (isset($meetingPointId)) {
	$sql = "SELECT * FROM mb_meetingpoint WHERE mb_meetingpoint_id = $1";
	$v = array($meetingPointId);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
	if($row = db_fetch_array($res)){
		$user = $row['fkey_mb_user_id'];
		$password = $row['mb_user_password'];
		$gui = $row['fkey_gui_id'];
		$wmc_id = $row['fkey_wmc_serial_id'];
		$kml_doc = $row['kml'];
		
		$kml = new kml("", "", 0, 0, "");
		$value = $kml->createObjFromKML($kml_doc);
		
		$x = $kml->x;
		$y = $kml->y;
		$icon = $kml->icon;
		if ($kml->title) {
			if ($kml->description) $alt = $kml->title . ", " .$kml->description;
			else $alt = $kml->title;
		}
		else $alt = $kml->description;

		if (mb_ereg("^.*,[[:space:]]((([[:alpha:]]+://)|(www.))[^<>[:space:]]+[[:alnum:]/]).*$", $kml->title)) {
			$url = mb_ereg_replace("^.*,[[:space:]]((([[:alpha:]]+://)|(www.))[^<>[:space:]]+[[:alnum:]/]).*$", "\\1", $kml->title);
			if (mb_substr($url, 0, 4) == "www.") {
				$url = "http://" . $url;
			}
		}
	}
	else {
		$js_error .= "alert('Meetingpoint ID id ".$meetingPointId." is not valid. Default GUI will be loaded instead.');"; 
	}
}
	
//WMC
if (isset($wmc_id)) {
	$myInitWmc = new wmc();
	$success = $myInitWmc->createFromDb($wmc_id);
	if ($success) {

		$js_wmc = implode("", $myInitWmc->toJavaScript());
		new mb_exception("WMC JS: " . $js_wmc);
		
		if (!empty($x) && !empty($y) && !empty($icon)) {
			
			$js_kml .= "var myPoint = realToMap('".$e_target[0]."', new Point(".$x.",".$y."));\n";

			// 7 is half the width of pin.png
			$js_kml .= "myPoint.x -= 7;";
			// 20 is the height of pin.png
			$js_kml .= "myPoint.y -= 20;";

			$js_kml .= "var meetingPointLogoStyle = {'position':'absolute', 'top':0, 'left':0, 'z-index':100, 'font-size':'10px'};\n"; 
			$js_kml .= "meetingPointLogoTag = new DivTag('meeting_logo', '".$e_target[0]."', meetingPointLogoStyle);\n";
			$js_img .= "<img id='meeting_img' border='0' src='".$icon."' title='".$alt."'>";
			if ($url) {
				$js_img = "<a href='".$url."' target='_blank'>" . $js_img . "</a>";
			}
			$js_kml .= "var meetingPointLogoText = \"" . $js_img . "\";\n";
			$js_kml .= "meetingPointLogoTag.write(meetingPointLogoText);\n";
			$js_kml .= "var meeting_img = window.frames['".$e_target[0]."'].document.getElementById('meeting_img');";
			$js_kml .= "meeting_img.style.position = 'absolute';";
			$js_kml .= "meeting_img.style.top = myPoint.y + 'px';";
			$js_kml .= "meeting_img.style.left = myPoint.x + 'px';";
			$js_kml .= "mb_registerPanSubElement('meeting_logo');";
		}
	}
	else {
		$js_error .= "alert('WMC id ".$wmc_id." is not valid. Default GUI will be loaded instead.');"; 
	}
}
echo "function mod_initWMC_init() {";
echo $js_wmc;
echo $js_kml;
echo $js_error;
echo "}";

echo "function addFlag() {";
echo $js_kml;
echo "}";


?>

mb_registerInitFunctions("mod_initWMC()");
function mod_initWMC(){
	mod_initWMC_init();
	mb_registerSubFunctions('addFlag()');
} 
