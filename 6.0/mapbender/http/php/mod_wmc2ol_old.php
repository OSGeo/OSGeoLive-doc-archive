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

require_once(dirname(__FILE__)."/../../core/globalSettings.php");

$userId = Mapbender::session()->get("mb_user_id");

//check for parameter wmc_id
if (!isset($_GET["wmc_id"])) {
	echo 'Error: wmc_id not requested<br>';
	die;
	//must leave script
}

function _e ($str) {
	return htmlentities($str, ENT_QUOTES, CHARSET);
}

if (!$userId) {
	$userId = PUBLIC_USER;
}

//the next functions should come from class administration, but they are not included as they are needed
//TODO
function getLayerPermission($wms_id, $layer_id, $user_id){
	
	//$layer_id = $this->getLayerIdByLayerName($wms_id,$layer_name);
	$array_guis = getGuisByPermission($user_id,true);
	$v = array();
	$t = array();
	$sql = "SELECT * FROM gui_layer WHERE fkey_gui_id IN (";
	$c = 1;
	//generate guilist assigned to specific user
	for($i=0; $i<count($array_guis); $i++){
		if($i>0){ $sql .= ",";}
		$sql .= "$".$c;
		$c++;
		array_push($v, $array_guis[$i]);
		array_push($t, 's');
	}
	$sql .= ") AND fkey_layer_id = $".$c." AND gui_layer_status = 1"; //status 1 must be
	array_push($v,$layer_id);
	array_push($t,'i');
	
	$res = db_prep_query($sql,$v,$t);
	if($row = db_fetch_array($res)){
		return true;
	}
	else{
		return false;
	}
}

function getGuisByPermission($mb_user_id,$ignoreublic){
	$arrayGuis = array();
	$mb_user_groups = array();
	$sql_groups = "SELECT fkey_mb_group_id FROM mb_user_mb_group WHERE fkey_mb_user_id = $1 ";
	$v = array($mb_user_id);
	$t = array("i");
	$res_groups = db_prep_query($sql_groups,$v,$t);
	$cnt_groups = 0;
	while($row = db_fetch_array($res_groups)){
		$mb_user_groups[$cnt_groups] = $row["fkey_mb_group_id"];
		$cnt_groups++;
	}
	if($cnt_groups > 0){
		$v = array();
		$t = array();
		$sql_g = "SELECT gui.gui_id FROM gui JOIN gui_mb_group ";
		$sql_g .= " ON gui.gui_id = gui_mb_group.fkey_gui_id WHERE gui_mb_group.fkey_mb_group_id IN (";
		for($i=0; $i<count($mb_user_groups);$i++){
			if($i > 0){$sql_g .= ",";}
			$sql_g .= "$".strval($i+1);
			array_push($v,$mb_user_groups[$i]);
			array_push($t,"i");
		}
		$sql_g .= ") GROUP BY gui.gui_id";
		$res_g = db_prep_query($sql_g,$v,$t);
		while($row = db_fetch_array($res_g)){
			array_push($arrayGuis,$row["gui_id"]);
		}
	}
	$sql_guis = "SELECT gui.gui_id FROM gui JOIN gui_mb_user ON gui.gui_id = gui_mb_user.fkey_gui_id";
	$sql_guis .= " WHERE (gui_mb_user.fkey_mb_user_id = $1) ";
	if (!isset($ignore_public) OR $ignore_public== false){
		$sql_guis .= " AND gui.gui_public = 1 ";
	}
	$sql_guis .= " GROUP BY gui.gui_id";
	$v = array($mb_user_id);
	$t = array("i");
	$res_guis = db_prep_query($sql_guis,$v,$t);
	$guis = array();
	while($row = db_fetch_array($res_guis)){
		if(!in_array($row['gui_id'],$arrayGuis)){
			array_push($arrayGuis,$row["gui_id"]);
		}
	}
	return $arrayGuis;
}
//end of functions which m,ay be included from class_administration in next versions
#**************************************************************************

//Function to create an OpenLayers Javascript from a mapbender wmc document
function createOlFromWMC_id($wmc_id){
	global $userId;
	//Get WMC out of mb Database
	$sql = "SELECT wmc FROM mb_user_wmc WHERE wmc_serial_id = $1";
	$res = db_prep_query($sql, array($wmc_id), array("s"));
	$wmc = db_fetch_row($res);
	//Read out WMC into XML object
	$xml=simplexml_load_string($wmc[0], "SimpleXMLElement", LIBXML_NOBLANKS);
	//generate general html data
	$html='';
	$html.="<html xmlns='http://www.w3.org/1999/xhtml'>\n";
	$html.="<head>\n";
	//define global variables for extent out of WMC File
	$windowWidth=$xml->General->Window->attributes()->width;
	$windowHeight=$xml->General->Window->attributes()->height;
	$htmlWidth=$windowWidth+40;
	$htmlHeight=$windowHeight+70;
	//define CSS 
   	$html.="<style type='text/css'>\n";
        $html.=" #map {\n";
        	$html.="width: ".$windowWidth."px;\n";
        	$html.="height: ".$windowHeight."px;\n";
        	$html.="border: 1px solid black;\n";
		$html.="overflow:visible;\n";
        $html.="}\n";
	$html.=" #srs {\n";
	        $html.="font-size: 80%;\n";
	        $html.="color: #444;\n";
	        $html.="}\n";
	$html.=" #showpos {\n";
	        $html.="font-size: 80%;\n";
	        $html.="color: #444;\n";
	        $html.="}\n";
	$html.="</style>\n";
	//Generate Title
	$html.="<title>".$xml->General->Title."</title>\n";
	//include OL libs from local source - must be minimized
	$html.="<script src='../extensions/OpenLayers-2.8/OpenLayers.js'></script>\n";
	$html.="<script type='text/javascript'>\n";
	//check for queryable layers
	$layer_array_queryable=array();
	$layer_array=$xml->LayerList->Layer;
	$html.="var map;\n";
	$someLayerQueryable=false;
	for ($i=0; $i<count($layer_array); $i++) {
		$html.="var layer".$i.";\n";
		$mb_extensions=$xml->LayerList->Layer[$i]->Extension->children('http://www.mapbender.org/context');
		$layer_array_queryable[$i]=$mb_extensions->querylayer;
		if (($layer_array_queryable[$i]=='1') and ($xml->LayerList->Layer[$i]->attributes()->hidden=='0')){
			$someLayerQueryable=true;
		}	
	}
	//define special BBOX
	$out_box=0.3;
	//get min/max extents for olbox
	$minx = $xml->General->BoundingBox->attributes()->minx;
	$miny = $xml->General->BoundingBox['miny'];
	$maxx = $xml->General->BoundingBox['maxx'];
	$maxy = $xml->General->BoundingBox['maxy'];
	$centralx=floor(($maxx+$minx)/2);
	$centraly=floor(($maxy+$miny)/2);
	$dx=$maxx-$minx;//in meters
	$dy=$maxy-$miny;//in meters
	//define zoom levels
	$numberZoomLevels=20;
	//define central position in projected system
	$html.="var lat = $centralx;\n"; 
       	$html.="var lon = $centraly;\n";
	$centralPointx=($maxx+$minx)/2;
	$centralPointy=($maxy+$miny)/2;
	//startzoom faktor - check if usefull
     	$html.="var zoom = 10;\n";
	//start function for initialize client
	$html.="function init(){\n";
	//define ol map object	
	$html.="map = new OpenLayers.Map( 'map' );\n";
	$html.=" var markers;\n";
	//define options for ol map object	
	$html.="var options = {\n";
        	$html.=" projection: \"".$xml->General->BoundingBox['SRS']."\",\n";
		if ($xml->General->BoundingBox['SRS']=='EPSG:4326'){
			echo 'Please choose an other coordinatereferencesystem. Converting Scales to Geographic Coordinates is not yet implemented!';
			return; 
		}
		$html.=" units: \"m\",\n";
		$html.="numZoomLevels: ".$numberZoomLevels.",\n";
		$html.="minResolution: 0.01\n";
	$html.="};\n";
	//New for given GET Params mb_myBBOX and mb_myBBOXEpsg******************************************
	//Before defining the bounds check if mb_myBBOX and mb_myBBOXEpsg are defined.
	//Check for given mb_myBBOX
	if(isset($_REQUEST["mb_myBBOX"])){
		//Check for numerical values for BBOX
		$array_bbox=explode(',',$_REQUEST["mb_myBBOX"]);
		if ((is_numeric($array_bbox[0])) and (is_numeric($array_bbox[1])) and (is_numeric($array_bbox[2])) and (is_numeric($array_bbox[3])) ) {
			if(isset($_REQUEST["mb_myBBOXEpsg"])){
				//Check epsg
				$targetEpsg=intval($_REQUEST["mb_myBBOXEpsg"]);
				if (($targetEpsg >= 1) and ($targetEpsg <= 50001)) {
					#echo "is in the codespace of the epsg registry\n";
					} else {
					#echo "is outside\n";
 					echo "alert('The REQUEST parameter mb_myBBOXEpsg is not in the epsg realm - please define another EPSG Code.');";
 					return;
				}
				//Check if epsg is equal to BBOXEpsg
				//Get epsg code out of WMC
				$xml_epsg=str_replace('EPSG:','',$xml->General->BoundingBox['SRS']);
				if ($_REQUEST["mb_myBBOXEpsg"]!=$xml_epsg){
					//Transform the given BBOX to epsg of WMC
					$sql= "select asewkt(transform(GeometryFromText ( 'LINESTRING ( ".$array_bbox[0]." ".$array_bbox[1].",".$array_bbox[2]." ".$array_bbox[3]." )', $targetEpsg ),".intval($xml_epsg)."))";
					$e = new mb_notice("mod_wms2ol.php: sql (transform)=".$sql);
					$res = db_query($sql);
					//read out result
					$text_bbox = db_fetch_row($res);
					$e = new mb_notice("mod_wms2ol.php: text_bbox=".$text_bbox[0]);
					$pattern = '~LINESTRING\((.*)\)~i';
					preg_match($pattern, $text_bbox[0], $subpattern);
					$e = new mb_notice("mod_wms2ol.php: subpattern=".$subpattern[1]);
					//exchange blancspaces
					$new_bbox = str_replace(" ", ",", $subpattern[1]);
					//set new BBOX
					$array_bbox_new=explode(',',$new_bbox);
					$minx_new=$array_bbox_new[0];
					$miny_new=$array_bbox_new[1];
					$maxx_new=$array_bbox_new[2];
					$maxy_new=$array_bbox_new[3];
					$centralx=($maxx_new+$minx_new)/2;
					$centraly=($maxy_new+$miny_new)/2;
				}
				else
				{
				//Set the new BBOX unaltered
				$minx=$array_bbox[0];
				$miny=$array_bbox[1];
				$maxx=$array_bbox[2];
				$maxy=$array_bbox[3];
				}
			}
		}
		else
		{
			echo "alert('The REQUEST parameters for mb_myBBOX are not numeric - please give numeric values!');";
			return;
		}
	} 
	//**********************************************************************************************
	//define variable bounds	
	$html.="var bounds = new OpenLayers.Bounds(".$minx.",".$miny.",".$maxx.",".$maxy.");\n";
	//if some layer defined, create base layer -> first layer in wmc	
	if (count($layer_array) != 0){
		$i=0;
		$html.="layer0 = new OpenLayers.Layer.WMS( \"".$xml->LayerList->Layer[$i]->Title."\",\n";
		$extensions=$xml->LayerList->Layer[$i]->Extension->children('http://www.mapbender.org/context');
		$layer_id=dom_import_simplexml($extensions->layer_id)->nodeValue;
		$wms_id=$extensions->wms_id;

//	?!	$has_permission=getLayerPermission($wms_id,$layer_id,2);//problem: guest user must have fix id
		$has_permission=getLayerPermission($wms_id,$layer_id,$userId);//problem: guest user must have fix id
		//echo $layer_id."<br>";
		if ($has_permission || $layer_id==''){
			$html.="\"".$xml->LayerList->Layer[$i]->Server->OnlineResource->attributes('http://www.w3.org/1999/xlink')->href."\",\n";
			$html.="{\n";
			$html.="layers: \"".$xml->LayerList->Layer[$i]->Name."\",\n";
			//get FormatList and the current active format -> TODO: make a function for getting actual format for request
			$format='png';
			foreach ($xml->LayerList->Layer[$i]->FormatList->Format as $current_format) {
				if ($current_format->attributes()->current=='1'){    
					$format=$current_format;
				}
			}
			#$format=str_replace('image/','',$format);
			$html.="format: \"".$format."\",\n";
			$html.="transparent: \"On\"\n";
			$html.="},\n";
			$html.="{\n";
	             	$html.="maxExtent: new OpenLayers.Bounds(".$minx.",".$miny.",".$maxx.",".$maxy."),\n";                    
                        // then check map.baseLayer.resolutions[0] for
                        // a reasonable value.
			$html.="projection: \"".$xml->General->BoundingBox['SRS']."\",\n";  
              		$html.="units: \"m\",\n"; 
			$html.="numZoomLevels: ".$numberZoomLevels.",\n";
			$minScale=dom_import_simplexml($extensions->gui_minscale)->nodeValue;
			$maxScale=dom_import_simplexml($extensions->gui_maxscale)->nodeValue;
			if (!$maxScale){
				$maxScale='10000000';
			}
			if (!$minScale){
				$minScale='0.1';
			}
			$html.="minScale: ".$minScale.",\n"; 
			$html.="maxScale: ".$maxScale.",\n"; 
			$html.="singleTile: true\n";
                 	//Only neccesary for working with scales.
                	$html.="  } );\n";
          		$html.=" map.addLayer(layer0);\n";
		} else {
			echo "Guest don't have permission on Base-Layer or ".$layer_id." therefor OpenLayers client will not be generated!<br>";
		}
	}
	//create the overlay layers for which the user guest has permissions
	for ($i=1; $i<count($layer_array); $i++) {
		$extensions=$xml->LayerList->Layer[$i]->Extension->children('http://www.mapbender.org/context');
		$wms_id=$extensions->wms_id;
		$layer_id=dom_import_simplexml($extensions->layer_id)->nodeValue;

		$has_permission=getLayerPermission($wms_id,$layer_id,$userId);//problem: guest user must have fix id TODO
		if (($xml->LayerList->Layer[$i]->attributes()->hidden=='0' && $has_permission) ||
			($layer_id=='' && $xml->LayerList->Layer[$i]->attributes()->hidden=='0')){
				
			$html.="layer".$i." = new OpenLayers.Layer.WMS( \"".$xml->LayerList->Layer[$i]->Title."\",\n";
			$html.="\"".$xml->LayerList->Layer[$i]->Server->OnlineResource->attributes('http://www.w3.org/1999/xlink')->href."\",\n";
			$html.="{\n";
			$html.="layers: \"".$xml->LayerList->Layer[$i]->Name."\",\n";
			//Get FormatList and the current active format
			$format='png';
			foreach ($xml->LayerList->Layer[$i]->FormatList->Format as $current_format) {
				if ($current_format->attributes()->current=='1'){    
					$format=$current_format;
				}
			}
			#$format=str_replace('image/','',$format);
			$html.="format: \"".$format."\",\n";
			$html.="transparent: \"TRUE\"\n";
			$html.="},\n";
			$html.="{\n";
             		$html.="maxExtent: new OpenLayers.Bounds(".$minx.",".$miny.",".$maxx.",".$maxy."),\n";       
			$html.="projection: \"".$xml->General->BoundingBox['SRS']."\",\n";  
              		$html.="units: \"m\",\n"; 
			$html.="singleTile: true,\n";
			$html.="numZoomLevels: ".$numberZoomLevels.",\n";
			//$extensions=$xml->LayerList->Layer[$i]->Extension->children('http://www.mapbender.org/context');
			$minScale=dom_import_simplexml($extensions->gui_minscale)->nodeValue;
			$maxScale=dom_import_simplexml($extensions->gui_maxscale)->nodeValue;
			if (!$maxScale){
				$maxScale='10000000';
			}
			if (!$minScale){
				$minScale='0.1';
			}
			$html.="minScale: ".$minScale.",\n"; 
			$html.="maxScale: ".$maxScale.",\n"; 
			$html.="'isBaseLayer': false\n";
                	$html.="  } );\n";
          		$html.=" map.addLayer(layer".$i.");\n";
		}
	}
	//do some global things
	//vector layer for logo or link
	//$html.="var vector = new OpenLayers.Layer.Vector('Simple Geometry',\n";
	//$html.="{attribution:'test'});\n";
  	//$html.="map.addLayer(vector);\n";
   	//$html.="map.addControl(new OpenLayers.Control.Attribution({'div':OpenLayers.Util.getElement('attribution')}));\n";
	//Check if central marker should be set and draw one
	if(isset($_REQUEST["mb_drawCentre"])&isset($centralx)&isset($centraly)){
		if ($_REQUEST["mb_drawCentre"]='1'){
			$html.="var markers = new OpenLayers.Layer.Markers(\"Markers\", {'calculateInRange': function() { return true; }});\n";
			$html.="var size = new OpenLayers.Size(15,20);\n";
			$html.="calculateOffset = function(size) {return new OpenLayers.Pixel(-(size.w/2), -size.h); };\n";
			$html.="var icon = new OpenLayers.Icon('../extensions/OpenLayers-2.8/img/marker.png',size, null, calculateOffset);\n";
			$html.="markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(".$centralx.",".$centraly."),icon));\n";
			$html.="map.addLayer(markers);\n";
		}
		else {
			echo "alert('The REQUEST parameter mb_drawCentre is outside of his realm!');";
 			return;
		}
	}
	//Zoom to extent of given mb_myBBOX 
	if(isset($_REQUEST["mb_myBBOX"])){
		$html.="var newBounds = new OpenLayers.Bounds(".$minx_new.",".$miny_new.",".$maxx_new.",".$maxy_new.");\n";
		$html.="map.setCenter(new OpenLayers.LonLat(".$centralx.",".$centraly."),zoom);\n";
		$html.="map.zoomToExtent(newBounds);\n";

	} else {
		$html.="map.zoomToExtent(bounds);\n";
	}
	if(isset($_REQUEST["showCoords"])){
		if($_REQUEST["showCoords"]=='1'){
			$html.="var mp = new OpenLayers.Control.MousePosition({'div':OpenLayers.Util.getElement('showpos'),'numDigits':2});\n";
			$html.="mp.numDigits = 2;\n";
			$html.="map.addControl(mp);";
		}	
	}
	//Generate the possibility to do GetFeatureInfo if this was activated in wmc
	if ($someLayerQueryable){
		$html.="map.events.register('click', map, function (e) {\n";
		//loop for all layers
		for ($i=0; $i<count($layer_array); $i++){
			if ($layer_array_queryable[$i]=='1'){
				$html.="var url".$i." =  layer".$i.".getFullRequestString({\n";
                $html.=" REQUEST: \"GetFeatureInfo\",\n";
                $html.=" FEATURE_COUNT: \"100\",\n";
				$html.="EXCEPTIONS: \"application/vnd.ogc.se_xml\",\n";
				$html.="BBOX: layer".$i.".map.getExtent().toBBOX(),\n";
				$html.="X: e.xy.x,\n";
				$html.="Y: e.xy.y,\n";
				$html.="INFO_FORMAT: 'text/html',\n";
				$html.="QUERY_LAYERS: layer".$i.".params.LAYERS,\n";
				$html.=" WIDTH: layer".$i.".map.size.w,\n";
				$html.="HEIGHT: layer".$i.".map.size.h});\n";
				$html.="window.open(url".$i.",target=\"_blank\",\"width=300,height=400,left=100,top=200\");\n";	
			}	
		}
		$html.="OpenLayers.Event.stop(e);\n";
		$html.=" });\n";
	}
	//end GetfeatureInfo
	$html.="}\n";//End of function
 	$html.="</script>\n";
 	$html.=" </head>\n";
  	$html.="<body onload='init()'>\n";
   	$html.="<div id='tags'></div>\n";
   	$html.="<div id='map' class='smallmap'></div>\n";//class dont exists
   	$html.="<div id='docs'>\n";
  	$html.="\n";
   	$html.="</div>\n";
	//Show coords if wished
	if($_REQUEST["mb_showCoords"]=='1'){
//		$html.="<div id='srs' class='csrs'>Koordinaten in <a href = '../../../mediawiki/index.php/".$xml->General->BoundingBox['SRS']."' target='_blank'>".$xml->General->BoundingBox['SRS']."</a>:</div>\n";
		$html.="<div id='srs' class='csrs'>Koordinaten in ".$xml->General->BoundingBox['SRS'].":</div>\n";
	}
	$html.="<div id='showpos'></div>\n";
	$html.="<div id='attribution'></div>\n";
  	$html.="</body>\n";
	$html.="</html>\n";
	//Print out HTML code
	echo $html;
}

//end of function createOlfromWMC_id()
createOlfromWMC_id($_GET["wmc_id"]);
?>