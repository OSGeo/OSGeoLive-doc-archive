<?php
#$Id: mod_insertKmlIntoDb.php 4226 2009-06-25 11:50:57Z vera $
#$Header: /cvsroot/mapbender/mapbender/http/javascripts/mod_insertWmcIntoDb.php,v 1.19 2006/03/09 14:02:42 uli_rothstein Exp $
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
include(dirname(__FILE__) . "/../classes/class_kml.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<!-- 
Licensing: See the GNU General Public License for more details.
http://www.gnu.org/copyleft/gpl.html
or:
mapbender/licence/ 
-->

<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>MB2 - <?php  echo  $gui_id;?></title>
</head>
<body>
<?php
if ($_POST["data"]) {
	
	$d = explode("____", $_POST["data"]);	
	$wmc_id = $d[0];
	$x = $d[1];
	$y = $d[2];
	$icon = $d[3];
	$gui_id = $d[4];
	$title_array = array();
	if ($_POST['name']) $title_array[count($title_array)] = $_POST['name']; 
	if ($_POST['street']) $title_array[count($title_array)] = $_POST['street'];
	if ($_POST['postcode']) {
		if ($_POST['city']) {
			$title_array[count($title_array)] = $_POST['postcode'] . " " . $_POST['city'];
		}
		else {
			$title_array[count($title_array)] = $_POST['postcode'];
		}
	}
	elseif ($_POST['city']) {
		$title_array[count($title_array)] = $_POST['city'];
	}
	if ($_POST['website']) $title_array[count($title_array)] = $_POST['website'];

	$title = implode(", ", $title_array);	
	$description = $_POST['description']; 
	
	$kml = new kml($title, $description, $x, $y, $icon);
	$kml->createKMLFromObj();
	
	$sql = "INSERT INTO mb_meetingpoint VALUES ($1, $2, $3, $4, $5, $6)";
	$v = array($kml->kml_id, $wmc_id, preg_replace("/&/", "&#38;" , html_entity_decode($kml->kml)), Mapbender::session()->get("mb_user_id"), Mapbender::session()->get("mb_user_password"), $gui_id);
	$t = array('s', 's', 's', 'i', 's', 's');
	$res = db_prep_query($sql, $v, $t);
	
	if (db_error()) {
		echo "<script>var title = \"" . $title . "\";alert(\"Error while saving KML document \" + title + \"! ".db_error()."\");</script>";
	}
	else {
		if ($alert) {
			 echo "<script>var title = \"" . $title . "\";alert(\"KML document \" + title + \"has been saved!\")</script>";
		}
	}
	$filename = "../tmp/".$kml->kml_id.'.kml';
	$handle = fopen($filename, "w");
	fputs($handle, preg_replace("/&/", "&#38;" , html_entity_decode($kml->kml)));
	fclose($handle);
	
	echo "Please feel free to add the <a href = 'http://".$_SERVER['HTTP_HOST']."/mburl/".$kml->kml_id."'>link</a> to your meeting point to your website.<br><br>";
	echo "<a href='".$filename."'>kml</a>";
}
?>

</body>
</html>
