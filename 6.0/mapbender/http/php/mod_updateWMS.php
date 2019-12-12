<?php
# $Id: mod_updateWMS.php 7972 2011-07-19 13:04:37Z astrid_emde $
# http://www.mapbender.org/index.php/UpdateWMS
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

$e_id="updateWMSs";
require_once(dirname(__FILE__)."/mb_validatePermission.php");
require_once(dirname(__FILE__)."/../classes/class_wms.php"); 

$selWMS = $_POST["selWMS"];
$capURL = $_POST["capURL"];
$myWMS = $_POST["myWMS"];
$myURL = $_POST["myURL"];

$secParams = SID."&guiID=".$_REQUEST["guiID"]."&elementID=".$_REQUEST["elementID"];
$self = $_SERVER["SCRIPT_NAME"]."?".$secParams;



?>
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>updateWMS</title>
<?php
include '../include/dyn_css.php';
?>
<script type='text/javascript'>
function validate(){
	var ind = document.form1.selWMS.selectedIndex;
	if(ind < 0){
		alert("No WMS selected!");
		return;
	}
	else{
		document.form1.submit();
	}
}
function sel(){
	var ind = document.form1.selWMS.selectedIndex;
	var wmsData = document.form1.selWMS.options[ind].value.split("###");
	document.form1.capURL.value = wmsData[1];
	document.form1.myWMS.value = wmsData[0];
	//new for showing metadata - 30.05.2008 AR
	document.getElementById("metadatalink").href = "mod_layerMetadata.php?id="+wmsData[2];
	document.getElementById("metadatatext").firstChild.nodeValue = "WMS-ID: "+wmsData[0];
	//end ***
	
}
</script>
</head>
<body>
<form name='form1' action='<?php echo $self; ?>' method='POST'>
<?php



require_once(dirname(__FILE__)."/../classes/class_administration.php");
$admin = new administration();
$ownguis = $admin->getGuisByOwner(Mapbender::session()->get("mb_user_id"),true);
$permguis = $admin->getGuisByPermission(Mapbender::session()->get("mb_user_id"),true);
$wms_id_own = $admin->getWmsByOwnGuis($ownguis);

if (count($wms_id_own)>0 AND count($ownguis)>0 AND count($permguis)>0){
	$v = array();
	$t = array();
	$c = 1;
	//$sql = "SELECT wms_id, wms_title, wms_getcapabilities, wms_upload_url  FROM wms ";
	$sql = "SELECT wms.wms_id, wms.wms_title, wms.wms_getcapabilities, wms.wms_upload_url, layer.layer_id  FROM wms, layer ";
	$sql .= "WHERE wms_id IN(";
	for($i=0; $i<count($wms_id_own); $i++){
		if($wms_id_own[$i] != ''){
			if($i>0){ $sql .= ",";}
			$sql .= "$".$c;
			array_push($v,$wms_id_own[$i]);
			array_push($t,'i');
			$c++;
		}
	}
	//$sql .= ")";
	//select has been adopted for showing metadata
	$sql .= ") AND wms.wms_id=layer.fkey_wms_id and layer.layer_pos=0";
	$sql .= " ORDER BY wms_title";
	$res = db_prep_query($sql,$v,$t);
	$cnt = 0;
	echo "<select name='selWMS' size='15' onchange='sel()'>";
	while($row = db_fetch_array($res)){
		//echo "<option value='".$row['wms_id']."###".$row['wms_upload_url']."'>".$row['wms_title']."</option>";
		echo "<option value='".$row['wms_id']."###".$row['wms_upload_url']."###".$row['layer_id']."'>".$row['wms_title']."</option>";
		$cnt++;
	}
	echo "</select><br /><br />";
	?>
	<!--Line for showing wms metadata-->
	view wms metadata: <a id='metadatalink' href='' onclick="window.open(this.href,'Metadaten','width=500,height=600,left=100,top=200,scrollbars=yes ,dependent=yes'); return false" target="_blank"><span id="metadatatext">no WMS selected</span></a><br><br>
<?php
	
	echo "Link to the last uploaded Online Resource URL:<br><input type='text' size='120' name='capURL'><br />";
	echo "<input type='hidden' name='myWMS' value=''><br>";
	echo "Add the following REQUEST to the Online Resource URL to obtain the Capabilities document:<br>";
	echo "<i>(Triple click to select and copy)</i><br>"; 
	echo "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.1<br>";
	echo "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.0<br>";
	echo "REQUEST=capabilities&WMTVER=1.0.0<br><br>";
	echo "Link to new WMS Capabilities URL:<br><input size='120' type='text' name='myURL'><br>";
	echo "<input type='button' value='Preview Capabilities' onclick='window.open(this.form.myURL.value,\"\",\"\")'>&nbsp;";
	echo "<input type='button' value='Upload Capabilities' onclick='validate()'><br>";


if(isset($myURL) && $myURL != ''){

	$mywms = new wms();
	$mywms->createObjFromXML($myURL);    
	$mywms->optimizeWMS();
	echo "<br />";  
	if (!MD_OVERWRITE) {
		$mywms->overwrite=false;
	} 
	$mywms->updateObjInDB($myWMS);
	$mywms->displayWMS();

	// start (owners of the updated wms will be notified by email)
	if ($use_php_mailing) {
		$owner_ids = $admin->getOwnerByWms($myWMS);
		
		if ($owner_ids && count($owner_ids)>0) {
			$owner_mail_addresses = array();
			$j=0;
			for ($i=0; $i<count($owner_ids); $i++) {
				$adr_tmp = $admin->getEmailByUserId($owner_ids[$i]);
				if (!in_array($adr_tmp, $owner_mail_addresses) && $adr_tmp) {
					$owner_mail_addresses[$j] = $adr_tmp;
					$j++;
				} 
			}
			
			$replyto = $admin->getEmailByUserId(Mapbender::session()->get("mb_user_id"));
			$from = $replyto;
			$pathArray = explode("http/php/", $_SERVER["PATH_TRANSLATED"]);
			$path = $pathArray[0];		
			$body = "WMS '" . $admin->getWmsTitleByWmsId($myWMS) . "' has been updated. \n\nServer name:  " . $_SERVER["SERVER_NAME"] . "\nInstallation Path: " . $path . "\n\nYou may want to check the changes as you are an owner of this WMS.";
			$error_msg = "";
			for ($i=0; $i<count($owner_mail_addresses); $i++) {
				if (!$admin->sendEmail($replyto, $from, $owner_mail_addresses[$i], $owner_mail_addresses[$i], "[Mapbender] A user has updated one of your WMS", $body, $error)) {
					if ($error){
						$error_msg .= $error . " ";
					}
				}
			}
			if (!$error_msg) {
				echo "<script language='javascript'>";
				echo "alert('Other owners of this WMS have been informed about the changes!');";
				echo "</script>";
			}
			else {
				echo "<script language='javascript'>";
				echo "alert('When notifying the owners of this WMS about your changes, an error occured: ' + '" . $error_msg . "');";
				echo "</script>";
			}
		}
	}
	// end (owners of the updated wms will be notified by email)
}

	echo "</form>";
	echo "</body>";
}else{
	echo "There are no wms available for this user.<br>";
}
?>
</html>
