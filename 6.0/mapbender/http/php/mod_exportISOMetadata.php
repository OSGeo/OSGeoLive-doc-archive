<?php
#http://www.geoportal.rlp.de/mapbender/php/mod_exportISOMetadata.php?
# $Id: mod_exportISOMetadata.php 235
# http://www.mapbender.org/index.php/Inspire_Metadata_Editor
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

require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_connector.php");

$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);

//define the view or table where to read out the layer ids for which metadatafiles should be generated
$wmsView = "search_wms_view";

//would have been called from web
//parse request parameter
//make all parameters available as upper case
foreach($_REQUEST as $key => $val) {
	$_REQUEST[strtoupper($key)] = $val;
}
//validate request params
if (!isset($_REQUEST['TYPE'])) {
	echo 'GET Parameter Type lacks'; 
	die();
}
if (isset($_REQUEST['TYPE']) and $_REQUEST['TYPE'] != "ALL") {
	echo 'validate: <b>'.$_REQUEST['TYPE'].'</b> is not valid.<br/>'; 
	die();
}

$sql = "SELECT layer_id ";
$sql .= "FROM ".$wmsView;
//$sql .= "FROM layer WHERE layer_id IN (20203,20202)";
$v = array();
$t = array();
$res = db_prep_query($sql,$v,$t);

$generatorScript = '/mapbender/php/mod_layerISOMetadata.php?';
$generatorScriptMetadata = '/mapbender/php/mod_dataISOMetadata.php?';

$generatorBaseUrl = 'http://'.$_SERVER['HTTP_HOST'].$generatorScript;
$generatorBaseUrlMetadata = 'http://'.$_SERVER['HTTP_HOST'].$generatorScriptMetadata;

$countLayer = 0;
$countMetadataURL = 0;
echo date('Y-m-d - H:i:s', time())."<br>";
//remove files from METADATA_DIR!
if ($handle = opendir(METADATA_DIR)) {
	echo "Delete files from temporary metadata folder:<br>";
    	/* This is the correct way to loop over the directory. */
    	while (false !== ($file = readdir($handle))) {
		//check if file name begin with "mapbender";
		$pos = strpos($file, "mapbender");
		if ($pos !== false) {
			//delete file with unlink
			unlink(METADATA_DIR."/".$file); 
			echo METADATA_DIR."/".$file." has been deleted!<br>";
		} else {
        		echo "$file will not be deleted!<br>";
		}
    	}
   	closedir($handle);
}
echo "Begin to create new metadata: ".date('Y-m-d - H:i:s', time())."<br>";
while($row = db_fetch_array($res)){
	$generatorUrl = $generatorBaseUrl."SERVICE=WMS&outputFormat=iso19139&id=".$row['layer_id'];
	echo "URL requested : ".$generatorUrl."<br>";
	$generatorInterfaceObject = new connector($generatorUrl);
	$ISOFile = $generatorInterfaceObject->file;
	$layerId = $row['layer_id'];
	echo "File for layer ".$layerId." will be generated<br>";
	//generate temporary files under tmp
	if($h = fopen(METADATA_DIR."/mapbenderServiceMetadata_".$layerId."_iso19139.xml","w")){
		if(!fwrite($h,$ISOFile)){
			$e = new mb_exception("mod_exportISOMetadata.php: cannot write to file: ".METADATA_DIR."/mapbenderLayerMetadata_".$row['layer_id']."_iso19139.xml");
		}
	echo "Service metadata file for layer ".$layerId." written to ".METADATA_DIR."<br>";
	fclose($h);
	}

	//get all connected metadata for this layer and save it too	
	$sql = <<<SQL

SELECT metadata_id, uuid, link, linktype, md_format, origin FROM mb_metadata 
INNER JOIN (SELECT * from ows_relation_metadata 
WHERE fkey_layer_id = $layerId ) as relation ON 
mb_metadata.metadata_id = relation.fkey_metadata_id WHERE mb_metadata.export2csw = TRUE

SQL;
	
	$res_metadata = db_query($sql);
	while ($row_metadata = db_fetch_array($res_metadata)) {
		$generatorUrlMetadata = $generatorBaseUrlMetadata."outputFormat=iso19139&id=".$row_metadata['uuid'];
		echo "<BLOCKQUOTE>URL requested : ".$generatorUrlMetadata."<br>";
		$generatorInterfaceObject = new connector($generatorUrlMetadata);
		$ISOFile = $generatorInterfaceObject->file;
		echo "Metadata uuid: ".$row_metadata['uuid']."<br>";
		//generate temporary files under tmp
		if($h = fopen(METADATA_DIR."/mapbenderDataMetadata_".$layerId."_".$row_metadata['uuid']."_iso19139.xml","w")){
			if(!fwrite($h,$ISOFile)){
				$e = new mb_exception("mod_exportISOMetadata.php: cannot write to file: ".METADATA_DIR."/metadata/mapbenderMetadata_".$layerId."_".$row_metadata['uuid']."_iso19139.xml");
			}
			echo "Data metadate file for layer ".$row['layer_id']." and metadata ".$row_metadata['uuid']." written to ".METADATA_DIR."</BLOCKQUOTE><br>";
			fclose($h);
			$countMetadataURL++;	
		}
		
	}
	$countLayer++;
}
echo "Number of generated Service Metadata Records (one for each layer): ".$countLayer."<br>";
echo "Number of generated Data Metadata Records (multiple for each layer): ".$countMetadataURL."<br>";
echo date('Y-m-d - H:i:s', time())."<br>";
