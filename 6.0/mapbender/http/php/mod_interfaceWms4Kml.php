<?php
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
if (isset($_REQUEST["id"]) & $_REQUEST["id"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["id"];
	$pattern = '/^[\d,]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'id: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$layerId = $testMatch;
	$testMatch = NULL;
}
//dbselect for generate KML
$sqlKML = "select wms.wms_getmap, wms.wms_version, layer.layer_name,layer.layer_title, layer_epsg.minx,layer_epsg.miny,layer_epsg.maxx,layer_epsg.maxy from wms, layer, layer_epsg, wms_format where layer.layer_id=$1 and layer.fkey_wms_id=wms.wms_id and layer.layer_id=layer_epsg.fkey_layer_id and layer_epsg.epsg='EPSG:4326' and wms.wms_id=wms_format.fkey_wms_id and wms_format.data_format like '%image/png%' LIMIT 1";
$vKML = array($layerId);
$tKML = array('i');
$resKML = db_prep_query($sqlKML, $vKML, $tKML);
$rowKML = db_fetch_array($resKML);

if (!isset($rowKML['layer_name'])) {
	echo "Layer with requested id doesn't exists in registry or layer has no name, so it can't be invoked by Google Earth!";
	die();
}
$getmapurl = eregi_replace("&","&amp;", $rowKML['wms_getmap']);
$kml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>".chr(13).chr(10);
$kml.="<kml xmlns=\"http://earth.google.com/kml/2.2\">".chr(13).chr(10);
$kml.="<GroundOverlay>".chr(13).chr(10);
$kml.="<name>".$rowKML['layer_title']."</name>".chr(13).chr(10);
$kml.="<Icon>".chr(13).chr(10);
$kml.="<href>".$getmapurl."VERSION=".$rowKML['wms_version']."&amp;REQUEST=GetMap&amp;SRS=EPSG:4326&amp;WIDTH=512&amp;HEIGHT=512&amp;LAYERS=".$rowKML['layer_name']."&amp;STYLES=&amp;TRANSPARENT=TRUE&amp;BGCOLOR=0xffffff&amp;FORMAT=image/png&amp;</href>".chr(13).chr(10);
$kml.="<RefreshMode>onExpire</RefreshMode>".chr(13).chr(10);
$kml.="<viewRefreshMode>onStop</viewRefreshMode>".chr(13).chr(10);
$kml.="<viewRefreshTime>1</viewRefreshTime>".chr(13).chr(10);
$kml.="<viewBoundScale>0.87</viewBoundScale>".chr(13).chr(10);
$kml.="</Icon>".chr(13).chr(10);
$kml.="<LatLonBox>".chr(13).chr(10);
$kml.="<north>".$rowKML['maxy']."</north>".chr(13).chr(10);
$kml.="<south>".$rowKML['miny']."</south>".chr(13).chr(10);
$kml.="<east>".$rowKML['maxx']."</east>".chr(13).chr(10);
$kml.="<west>".$rowKML['minx']."</west>".chr(13).chr(10);
$kml.="</LatLonBox>".chr(13).chr(10);
$kml.="</GroundOverlay>".chr(13).chr(10);
$kml.="</kml>".chr(13).chr(10);
header("Content-Type: application/vnd.google-earth.kml+xml");
header("Content-Disposition: attachment; filename=\"Mapbender_layer_".$layerId.".kml\"");
echo $kml;
?>
