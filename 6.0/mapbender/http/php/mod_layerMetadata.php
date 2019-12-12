<?php
# $Id: mod_layerMetadata.php 235 2006-05-11 08:34:48Z uli $
# http://www.mapbender.org/index.php/Administration
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

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";

$redirectToMetadataUrl = intval($_GET["redirectToMetadataUrl"]);
$subscribe = intval($_GET["subscribe"]);
$layer_id = htmlentities($_GET['id'], ENT_QUOTES);
$wms_getmap = urldecode($_GET['wms']); 
$layer_name = urldecode($_GET['name']); 

function display_text($string) {
    $string = mb_eregi_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/{}]", "<a href=\"\\0\" target=_blank>\\0</a>", $string);   
    $string = mb_eregi_replace("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([0-9a-z](-?[0-9a-z])*\.)+[a-z]{2}([zmuvtg]|fo|me)?$", "<a href=\"mailto:\\0\" target=_blank>\\0</a>", $string);   
    $string = mb_eregi_replace("\n", "<br>", $string);
    return $string;
}  

function getEpsgByLayerId ($layer_id) { // from merge_layer.php
	$epsg_list = "";
	$sql = "SELECT DISTINCT epsg FROM layer_epsg WHERE fkey_layer_id = $1";
	$v = array($layer_id);
	$t = array('i');
	$res = db_prep_query($sql, $v, $t);
	while($row = db_fetch_array($res)){
		$epsg_list .= $row['epsg'] . " ";
	}
	return trim($epsg_list);
}

//function to generate temporal kml-file
function generateKML($kml_id,$resdir,$getmapurl,$wmsversion,$layername,$layertitle,$north,$south,$east,$west){
$getmapurl = eregi_replace("&","&amp;", $getmapurl);
//$kml_id=md5(uniqid(rand(), true));
if($h = fopen($resdir."/".$kml_id.".kml","w+")){
//					$content = $text .chr(13).chr(10); //example for linefeeds
$kml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>".chr(13).chr(10);
$kml.="<kml xmlns=\"http://earth.google.com/kml/2.2\">".chr(13).chr(10);
$kml.="<GroundOverlay>".chr(13).chr(10);
$kml.="<name>".$layertitle." - www.geoportal.rlp.de</name>".chr(13).chr(10);
$kml.="<Icon>".chr(13).chr(10);
$kml.="<href>".$getmapurl."VERSION=".$wmsversion."&amp;REQUEST=GetMap&amp;SRS=EPSG:4326&amp;WIDTH=512&amp;HEIGHT=512&amp;LAYERS=".$layername."&amp;STYLES=&amp;TRANSPARENT=TRUE&amp;BGCOLOR=0xffffff&amp;FORMAT=image/png&amp;</href>".chr(13).chr(10);
//http://www.geoportal.rlp.de/owsproxy/3acc4cc90d02c754c531a9d5fa1b1545/5d38dd28a830f2c4ab97a506225d0a9b?VERSION=1.1.1&amp;REQUEST=GetMap&amp;SRS=EPSG:4326&amp;WIDTH=512&amp;HEIGHT=512&amp;LAYERS=boriweCD01&amp;TRANSPARENT=TRUE&amp;FORMAT=image/jpeg&amp;</href>
$kml.="<RefreshMode>onExpire</RefreshMode>".chr(13).chr(10);
$kml.="<viewRefreshMode>onStop</viewRefreshMode>".chr(13).chr(10);
$kml.="<viewRefreshTime>1</viewRefreshTime>".chr(13).chr(10);
$kml.="<viewBoundScale>0.87</viewBoundScale>".chr(13).chr(10);
$kml.="</Icon>".chr(13).chr(10);
$kml.="<LatLonBox>".chr(13).chr(10);
$kml.="<north>".$north."</north>".chr(13).chr(10);
$kml.="<south>".$south."</south>".chr(13).chr(10);
$kml.="<east>".$east."</east>".chr(13).chr(10);
$kml.="<west>".$west."</west>".chr(13).chr(10);
$kml.="</LatLonBox>".chr(13).chr(10);
$kml.="</GroundOverlay>".chr(13).chr(10);
$kml.="</kml>".chr(13).chr(10);
				if(!fwrite($h,$kml)){
						#exit;
					}
					fclose($h);
				}
}


$metadataStr = "";
$metadataStr .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">' . 
	'<head>' . 
		'<title>Metadaten</title>' . 
		'<meta name="description" content="Metadaten" xml:lang="de" />'.
		'<meta name="keywords" content="Metadaten" xml:lang="de" />'	.	
		'<meta http-equiv="cache-control" content="no-cache">'.
		'<meta http-equiv="pragma" content="no-cache">'.
		'<meta http-equiv="expires" content="0">'.
		'<meta http-equiv="content-language" content="de" />'.
		'<meta http-equiv="content-style-type" content="text/css" />'.
		'<link rel="stylesheet" type="text/css" href="../css/metadata.css" />' .
		'<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">' . 	
	'</head>'.
	'<body>';
	$wms_id = $_GET['wmsid'];
	
	if ($wms_id) {
		$sql = "SELECT layer_id FROM layer WHERE fkey_wms_id = $1 AND layer_pos = 0";
		$v = array($wms_id);
		$t = array("i");
		$res = db_prep_query($sql, $v, $t);
		$row = db_fetch_array($res);
		$layer_id = $row["layer_id"];
	}
	
	if ($layer_id) {
		$sql = "SELECT ";
		$sql .= "layer.layer_id, layer.layer_title, layer.layer_abstract, layer.layer_pos, layer.layer_parent, ";
		$sql .= "layer.layer_minscale, layer.layer_maxscale, layer_dataurl, layer_metadataurl, ";
		$sql .= "wms.wms_title, wms.wms_abstract, wms.wms_id, wms.fees, wms.accessconstraints, wms.contactperson, ";
		$sql .= "wms.contactposition, wms.contactorganization, wms.address, wms.city, wms_timestamp, wms_owner, ";
		$sql .= "wms.stateorprovince, wms.postcode, wms.contactvoicetelephone, wms.contactfacsimiletelephone, ";
		$sql .= "wms.contactelectronicmailaddress, wms.country ";
		$sql .= "FROM layer, wms WHERE layer.layer_id = $1 AND layer.fkey_wms_id = wms.wms_id LIMIT 1";
		$v = array($layer_id);
		$t = array('i');
	}
	else if ($wms_getmap && $layer_name) {
		$sql = "SELECT ";
		$sql .= "layer.layer_id, layer.layer_title, layer.layer_abstract, layer.layer_pos, layer.layer_parent, ";
		$sql .= "layer.layer_minscale, layer.layer_maxscale, layer_dataurl, layer_metadataurl, ";
		$sql .= "wms.wms_title, wms.wms_abstract, wms.wms_id, wms.fees, wms.accessconstraints, wms.contactperson, ";
		$sql .= "wms.contactposition, wms.contactorganization, wms.address, wms.city, wms_timestamp, wms_owner, ";
		$sql .= "wms.stateorprovince, wms.postcode, wms.contactvoicetelephone, wms.contactfacsimiletelephone, ";
		$sql .= "wms.contactelectronicmailaddress, wms.country ";
		$sql .= "FROM layer, wms WHERE layer.layer_pos <> 0 AND layer.layer_name = $1 AND layer.fkey_wms_id = wms.wms_id AND wms.wms_getmap LIKE $2 LIMIT 1";
		$v = array($layer_name, $wms_getmap."%");
		$t = array('s', 's');
	}
	else die("layer not specified!");
	$res = db_prep_query($sql,$v,$t);
	$metadataStr .=  db_error();
	$layer = array();
	$row = db_fetch_array($res);
	$layer_id = $row['layer_id'];
	$layer_name = $row['layer_name'];
if($row['wms_owsproxy']!='') {
	$secured=true;
}
else {
	$secured=false;
}


	$sql_dep = "SELECT mb_group_name FROM mb_group AS a, mb_user AS b, mb_user_mb_group AS c WHERE b.mb_user_id = $1  AND b.mb_user_id = c.fkey_mb_user_id AND c.fkey_mb_group_id = a.mb_group_id AND b.mb_user_department = a.mb_group_description LIMIT 1";
	$v_dep = array($row['wms_owner']);
	$t_dep = array('i');
	$res_dep = db_prep_query($sql_dep, $v_dep, $t_dep);
	$row_dep = db_fetch_array($res_dep);
	

	$layer['ID'] = $row['layer_id'];
	$layer['Titel'] = $row['layer_title'];
	$layer['Zusammenfassung'] = $row['layer_abstract'];
	if ($row['layer_pos'] || $row['layer_parent']) {
	  if ($row['layer_minscale'] > 0)
		{
		$layer['Minscale'] = "1 : ". $row['layer_minscale'];
		}
		else
		{$layer['Minscale'] = "-";}
		if ($row['layer_maxscale'] > 0)
		{
		$layer['Maxscale'] = "1 : ". $row['layer_maxscale'];
		}
		else
		{$layer['Maxscale'] = "-";}
	}
	$layer['Koordinatensysteme'] = preg_replace("/ /", ", ", getEpsgByLayerId($row['layer_id']));
	if ($row['wms_timestamp']) {
		$layer['Datum der Registrierung'] = date("d.m.Y",$row['wms_timestamp']); 
	}
	else {
		$layer['Datum der Registrierung'] = "Keine Angabe"; 
	}
	$layer['Registrierende Stelle'] = $row_dep['mb_group_name'];
	$layer['WMS ID'] = $row['wms_id'];
	$layer['Mapbender Capabilities Dokument'] = "<a href = '../php/wms.php?layer_id=".$layer_id."&PHPSESSID=".session_id()."&REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS' target=_blank>Capabilities-Dokument</a>";
	$layer['Service Metadaten'] = "<a href='../php/mod_layerISOMetadata.php?SERVICE=WMS&outputFormat=iso19139&Id=".	$layer_id."' target=_blank ><img src='../img/inspire_tr_36.png' title='INSPIRE Metadaten' style='width:34px;height:34px' alt='' /></a>"."<a href='../php/mod_layerISOMetadata.php?SERVICE=WMS&outputFormat=iso19139&Id=".$layer_id."&validate=true' target=_blank title='Validierung gegen INSPIRE Geoportal'>Validierung</a>";
	$layer['WMS Titel'] = $row['wms_title'];
	$layer['WMS Zusammenfassung'] = $row['wms_abstract'];
	$layer['Geb&uuml;hren'] = $row['fees'];
	$layer['Zugriffsbeschr&auml;nkung'] = $row['accessconstraints'];
	$layer['Ansprechpartner'] = $row['contactperson'];
	$layer['Organisation'] = $row['contactorganization'];
	$layer['Adresse'] = $row['address'];
	$layer['Stadt'] = $row['city'];
	$layer['Bundesland'] = $row['stateorprovince'];
	$layer['PLZ'] = $row['postcode'];
	$layer['Telefon'] = $row['contactvoicetelephone'];
	$layer['Fax'] = $row['contactfacsimiletelephone'];
	$layer['E-Mail'] = $row['contactelectronicmailaddress'];
	$layer['Land'] = $row['country'];
	$layer['Metadaten'] = $row['layer_metadataurl'];
	$metadataUrl = $row['layer_metadataurl'];

	if ($layer['Metadaten'] && $redirectToMetadataUrl) {
		header("Location: " . $layer['Metadaten']);		
	}	
	else {
		$metadataStr .=  "<table >\n";
		$t_a = "\t<tr>\n\t\t<th>\n\t\t\t";
		$t_b = "\n\t\t</th>\n\t\t<td>\n\t\t\t";
		$t_c = "\n\t\t</td>\n\t</tr>\n";
	
		$keys = array_keys($layer);
		for ($j=0; $j<count($layer); $j++) {
			$metadataStr .=  $t_a . $keys[$j] . $t_b . display_text($layer[$keys[$j]]) . $t_c;
		}
	
		if (!$row['layer_pos'] && !$row['layer_parent']) {
			$wms_id = $row['wms_id'];
			$sql = "SELECT layer.layer_title, layer.layer_id FROM layer WHERE fkey_wms_id = $1 AND layer_pos <> 0";
			$v = array($wms_id);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);
			$i=0;
			$layerArray = array();
			while ($row = db_fetch_array($res)) {
				$layerArray[$i] = array();
				$layerArray[$i]['Titel'] = $row['layer_title'];
				$layerArray[$i]['id'] = $row['layer_id'];
				$i++;
			}
			$metadataStr .=  "<tr><th>Ebenen</th><td>";
			for ($i=0; $i<count($layerArray); $i++) {
				if ($i >0) $metadataStr .=  ", ";
				$metadataStr .=  "<a href='mod_layerMetadata.php?id=".$layerArray[$i]['id']."'>" . $layerArray[$i]['Titel'] . "</a>";
			}
		}	
		$metadataStr .= "</td></tr>";
        
	
    $resdir = TMPDIR;
    $kml_id=md5(uniqid(rand(), true));
    //dbselect for generate KML	
    $sql_kml = "select wms.wms_getmap, wms.wms_version, layer.layer_name,layer.layer_title, layer_epsg.minx,layer_epsg.miny,layer_epsg.maxx,layer_epsg.maxy from wms, layer, layer_epsg, wms_format where layer.layer_id=$1 and layer.fkey_wms_id=wms.wms_id and layer.layer_id=layer_epsg.fkey_layer_id and layer_epsg.epsg='EPSG:4326' and wms.wms_id=wms_format.fkey_wms_id and wms_format.data_format like '%image/png%' LIMIT 1";

	$v_kml = array($layer_id);

	$t_kml = array('i');

	$res_kml = db_prep_query($sql_kml, $v_kml, $t_kml);

	$row_kml = db_fetch_array($res_kml);
	
     generateKML($kml_id,$resdir,$row_kml['wms_getmap'],$row_kml['wms_version'],$row_kml['layer_name'],$row_kml['layer_title'],$row_kml['maxy'],$row_kml['miny'],$row_kml['maxx'],$row_kml['minx']);
	//export KML 
	 $metadataStr .= "<tr><th>Weitere Schnittstellen</th><td>";
	 $metadataStr .= "<a href='kmldownload.php?download=".$kml_id.".kml'>KML (Keyhole Markup Language)</a>";

		$user = new User();
		
		//
		// Monitoring is only available if the user is allowed to access this service
		//
		if ($user->isLayerAccessible($layer['ID'])) {
			if ($subscribe === 1) {
				$user->addSubscription($layer['WMS ID']);
			}
			else if ($subscribe === 0) {
				$user = new User();
				$user->cancelSubscription($layer['WMS ID']);
			}

			$currentUser = new User();
			$is_subscribed = $currentUser->hasSubscription($wms_id);
			
			$is_public = $currentUser->isPublic();
			//show abo function to registred and authorized users
			if ($is_subscribed && !$is_public) {
				$metadataStr .= "<tr><th>Abo</th><td><img src = '../img/mail_delete.png'>" . 
					"<a href = '../php/mod_layerMetadata.php?id=" . 
					$layer_id . "&user_id=" . $currentUser->id . "&subscribe=0'>" . 
					_mb("Monitoring Abo l&ouml;schen") . "</a></td></tr>";
			}
			else if (!$is_subscribed && !$is_public) {
				$metadataStr .= "<tr><th>Abo</th><td><img src = '../img/mail_send.png'>" . 
					"<a href = '../php/mod_layerMetadata.php?id=" . $layer_id . 
					"&user_id=" . $currentUser->id . "&subscribe=1'>" . 
					_mb("Monitoring abonnieren") . "</a></td></tr>";
			}
		}
		//if service is secured
		if ($secured=true){
			$slink=HTTP_AUTH_PROXY."/".$layer_id."?REQUEST=GetCapabilities&VERSION=1.1.1&SERVICE=WMS";
			echo "<tr><th>Abgesicherte Verbindung</th><td><a href = '".$slink."' target=_blank>Secured Capabilities-Dokument</a></td></tr>";
		}	
		$metadataStr .= "</table>\n";
		$metadataStr .=  '</div></body></html>';
		echo $metadataStr;
	}
?>
