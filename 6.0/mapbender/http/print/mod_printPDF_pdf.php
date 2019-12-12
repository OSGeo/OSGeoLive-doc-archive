<?php
# $Id: mod_printPDF_pdf.php 8381 2012-06-18 08:40:29Z verenadiewald $
# http://www.mapbender.org/index.php/mod_printPDF_pdf.php
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

include (dirname(__FILE__)."/../classes/class.ezpdf.php");
include (dirname(__FILE__)."/../classes/class_stripRequest.php");
include (dirname(__FILE__)."/../classes/class_weldMaps2PNG.php");
include (dirname(__FILE__)."/../classes/class_weldOverview2PNG.php");

$confFile = basename($_REQUEST["conf"]);
if (!preg_match("/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9]+)$/", $confFile) || 
	!file_exists($confFile)) {

	$errorMessage = _mb("Invalid configuration file");
	echo htmlentities($errorMessage, ENT_QUOTES, CHARSET);
	$e = new mb_exception($errorMessage);
	die;
}

include (dirname(__FILE__)."/../print/".$confFile);
include (dirname(__FILE__)."/../classes/class_SaveLegend.php");
include (dirname(__FILE__)."/../print/print_functions.php");

if($log == true){
	include (dirname(__FILE__)."/../classes/class_log.php");
}

#Globals

$factor = intval($_REQUEST["quality"]);

#$date = date("d.m.Y",strtotime("now"));
$linewidth_dashed = 0.5;
$linewidth = 0.5;

// DURATION TIME:
function microtime_float(){
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();
// END DURATION TIME

$time_end = microtime_float();
$time = $time_end - $time_start;

/* -------------------------------------- */

$size = $_REQUEST["size"];
$format = $_REQUEST["format"];
$map_scale = $_REQUEST["map_scale"];
$overview_url = urldecode($_REQUEST["overview_url"]);
$epsg = $_REQUEST["epsg"];
if($overview_url=='false'){
	$overview = false;	
}

function setscalebar($scale){
    	$mb_resolution = 28.35;
        
		if($scale < 16){
	      $value = "10";
	      $unit = "cm";
	      $scalefactor = 10/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }
	   if($scale >= 16 && $scale < 151){
	      $value = "1";	      
	      $unit = "Meter";
	      $scalefactor = 100/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }
	   if($scale >= 151 && $scale < 1550 ){
	      $value = "10";	      
	      $unit = "Meter";
	      $scalefactor = 1000/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }
	   if($scale >= 1550 && $scale < 15050){
	      $value = "100";	      
	      $unit = "Meter";
	      $scalefactor = 10000/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }
	   if($scale < 150050 && $scale >= 15050){
	      $value = "1";	      
	      $unit = "Kilometer";
	      $scalefactor = 100000/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }
	   if($scale < 1500050 && $scale >= 150050){
	      $value = "10";	      
	      $unit = "Kilometer";
	      $scalefactor = 1000000/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }
	   if($scale < 15000050 && $scale >= 1500050){
	      $value = "100";	      
	      $unit = "Kilometer";
	      $scalefactor = 10000000/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }
	   if($scale < 150000001 && $scale >= 15000001){
	      $value = "1000";	      
	      $unit = "Kilometer";
	      $scalefactor = 100000000/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }
	   if($scale >= 150000001){
	      $value = "1000";	      
	      $unit = "Kilometer";
	      $scalefactor = 100000000/$scale;
	      $img_width = round($scalefactor * $mb_resolution);
	   }   
	   $array_scale[0] = $unit;
	   $array_scale[1] = $img_width;
		$array_scale[2] = $value;	   
	   
	   return  $array_scale; 
}


$border = 0.8 * $DPC;

if($matching == true){
   $urls = preg_replace($pattern,$replacement,urldecode($_REQUEST["map_url"]));  
}
else{
   $urls = urldecode($_REQUEST["map_url"]);
}


$array_urls = explode("___", $urls);
//problem with false in some map_urls see http/plugins/mb_metadata_wmcPreview.php
//exchange array_urls with array_urls without false entries - it depends on the scale hints - if not visible the map_url is false!
$newArray_urls = array();
for ($i=0; $i<count($array_urls); $i++) {
	if ($array_urls[$i] != 'false') {
		$newArray_urls[] = $array_urls[$i];
	}
}
$array_urls = $newArray_urls;
$myURL = new stripRequest($array_urls[0]);
$map_width = round($myURL->get("width"));
$map_height = round($myURL->get("height"));
$map_extent = $myURL->get("BBOX");
if($factor>1){
	for($i=0; $i<count($array_urls); $i++){
		$m = new stripRequest($array_urls[$i]);
		$m->set('width',(intval($m->get('width'))*4));
		$m->set('height',(intval($m->get('height'))*4));
		if(in_array($m->get('map'),$highqualitymapfiles)){	
			$m->set('map',preg_replace("/\.map/","_4.map",$m->get('map')));			
		}
		$array_urls[$i] = $m->url;
	}
}
$coord = mb_split(",",$map_extent);

if($overview === true){
	// analyse overview url and draw rectangle with position
	$o_url = new stripRequest($overview_url);
	$overview_width = round($o_url->get("width"));
	$overview_height = round($o_url->get("height"));
	
	if($factor>1){
		$o_url->set('width',(intval($o_url->get('width'))*4));
		$o_url->set('height',(intval($o_url->get('height'))*4));
		if(in_array($o_url->get('map'),$highqualitymapfiles)){	
				$o_url->set('map',preg_replace("/\.map/","_4.map",$o_url->get('map')));		
				$overview_url = $o_url->url;	
		}
	}
	
	if($matching == true){
		$overview_url = preg_replace($pattern,$replacement,$overview_url);  
	}
	
	
	if ($size == "A4" && $format == "portrait"){
		$overview_left = $a4p_overviewOffset_left;
		$overview_bottom =$a4p_overviewOffset_bottom;
	}elseif ($size == "A4" && $format == "landscape"){
		$overview_left = $a4l_overviewOffset_left;
		$overview_bottom =$a4l_overviewOffset_bottom;
	}elseif ($size == "A3" && $format == "portrait"){
		$overview_left = $a3p_overviewOffset_left;
		$overview_bottom =$a3p_overviewOffset_bottom;
	}elseif ($size == "A3" && $format == "landscape"){
		$overview_left = $a3l_overviewOffset_left;
		$overview_bottom = $a3l_overviewOffset_bottom;
	}elseif ($size == "A2" && $format == "portrait"){
		$overview_left = $a2p_overviewOffset_left;
		$overview_bottom =$a2p_overviewOffset_bottom;
	}elseif ($size == "A2" && $format == "landscape"){
		$overview_left = $a2l_overviewOffset_left;
		$overview_bottom = $a2l_overviewOffset_bottom;
	}elseif ($size == "A1" && $format == "portrait"){
		$overview_left = $a1p_overviewOffset_left;
		$overview_bottom =$a1p_overviewOffset_bottom;
	}elseif ($size == "A1" && $format == "landscape"){
		$overview_left = $a1l_overviewOffset_left;
		$overview_bottom = $a1l_overviewOffset_bottom;
	}elseif ($size == "A0" && $format == "portrait"){
		$overview_left = $a0p_overviewOffset_left;
		$overview_bottom =$a0p_overviewOffset_bottom;
	}elseif ($size == "A0" && $format == "landscape"){
		$overview_left = $a0l_overviewOffset_left;
		$overview_bottom = $a0l_overviewOffset_bottom;
	}
	
	$o_extent = $o_url->get("BBOX");
	
	$array_overview_url[0] = $overview_url;
	if($log == true){
		$l = new log("printPDF_overview",$array_overview_url);
	}
	
	/*
	$o_new = new stripRequest($overview_url);
	$o_new->set('width',50);
	$o_new->set('height',50);
	//$o->set('BBOX',$overview_extent);
	$o_url_new =$o_new->url;
	$array_overview[0] = $overview_url;
	$array_overview[1] = $o_url;
	*/
}	

/*
 * north arrow
 */
if($size == "A4" && $format == "portrait"){
	$northarrow_left = $a4p_northarrow_left;
	$northarrow_bottom = $a4p_northarrow_bottom;
}elseif ($size == "A4" && $format == "landscape"){
	$northarrow_left = $a4l_northarrow_left;
	$northarrow_bottom = $a4l_northarrow_bottom;
}elseif ($size == "A3" && $format == "portrait"){
	$northarrow_left = $a3p_northarrow_left;
	$northarrow_bottom = $a3p_northarrow_bottom;
}elseif ($size == "A3" && $format == "landscape"){
	$northarrow_left = $a3l_northarrow_left;
	$northarrow_bottom = $a3l_northarrow_bottom;
}elseif ($size == "A2" && $format == "portrait"){
	$northarrow_left = $a2p_northarrow_left;
	$northarrow_bottom = $a2p_northarrow_bottom;
}elseif ($size == "A2" && $format == "landscape"){
	$northarrow_left = $a2l_northarrow_left;
	$northarrow_bottom = $a2l_northarrow_bottom;
}elseif ($size == "A1" && $format == "portrait"){
	$northarrow_left = $a1p_northarrow_left;
	$northarrow_bottom = $a1p_northarrow_bottom;
}elseif ($size == "A1" && $format == "landscape"){
	$northarrow_left = $a1l_northarrow_left;
	$northarrow_bottom = $a1l_northarrow_bottom;
}elseif ($size == "A0" && $format == "portrait"){
	$northarrow_left = $a0p_northarrow_left;
	$northarrow_bottom = $a0p_northarrow_bottom;
}elseif ($size == "A0" && $format == "landscape"){
	$northarrow_left = $a0l_northarrow_left;
	$northarrow_bottom = $a0l_northarrow_bottom;
}

/*
 * special image
 */
if ($size == "A4" && $format == "portrait"){
	$specialImage_left = $a4p_special_left;
	$specialImage_bottom = $a4p_special_bottom;
}elseif ($size == "A4" && $format == "landscape"){
	$specialImage_left = $a4l_special_left;
	$specialImage_bottom = $a4l_special_bottom;
}elseif ($size == "A3" && $format == "portrait"){
	$specialImage_left = $a3p_special_left;
	$specialImage_bottom = $a3p_special_bottom;
}elseif ($size == "A3" && $format == "landscape"){
	$specialImage_left = $a3l_special_left;
	$specialImage_bottom = $a3l_special_bottom;
}elseif ($size == "A2" && $format == "portrait"){
	$specialImage_left = $a2p_special_left;
	$specialImage_bottom = $a2p_special_bottom;
}elseif ($size == "A2" && $format == "landscape"){
	$specialImage_left = $a2l_special_left;
	$specialImage_bottom = $a2l_special_bottom;
}elseif ($size == "A1" && $format == "portrait"){
	$specialImage_left = $a1p_special_left;
	$specialImage_bottom = $a1p_special_bottom;
}elseif ($size == "A1" && $format == "landscape"){
	$specialImage_left = $a1l_special_left;
	$specialImage_bottom = $a1l_special_bottom;
}elseif ($size == "A0" && $format == "portrait"){
	$specialImage_left = $a0p_special_left;
	$specialImage_bottom = $a0p_special_bottom;
}elseif ($size == "A0" && $format == "landscape"){
	$specialImage_left = $a0l_special_left;
	$specialImage_bottom = $a0l_special_bottom;
}

if($log == true){
	$l = new log("printPDF",$array_urls);
}
$pdf = new Cezpdf();

$pdf->Cezpdf(mb_strtolower($size),$format);
$diff=array(196=>'Adieresis',228=>'adieresis',
   214=>'Odieresis',246=>'odieresis',
   220=>'Udieresis',252=>'udieresis',
   223=>'germandbls');
$pdf->selectFont('../classes/fonts/LiberationSans-Regular.afm', array('encoding'=>'WinAnsiEncoding','differences'=>$diff));
if($size == "A4" && $format == "portrait"){
	$mapOffset_left = $a4p_mapOffset_left;
	$mapOffset_bottom = $a4p_mapOffset_bottom;
	$header_height = $a4p_header_height;
	$footer_height = $a4p_footer_height;
}
else{
	$mapOffset_left = $a4l_mapOffset_left;
	$mapOffset_bottom = $a4l_mapOffset_bottom;
	$header_height = $a4l_header_height;
	$header_width = $a4l_header_width;
}
session_write_close();
$i = new weldMaps2PNG(implode("___",$array_urls),$filename);

$pdf->addPngFromFile($filename, $mapOffset_left, $mapOffset_bottom, $map_width, $map_height);
if($unlink == true){
	unlink($filename);
}

/** ******************************************************************
* user drawn elements
*/

# mypermanentImage (highlight symbol from geometry.js)
if ($_REQUEST["mypermanentImage"]){
	$array_permanentImage = explode("___", $_REQUEST["mypermanentImage"]);
	
	if(count($array_permanentImage)>0){
		$permanentImage = $array_permanentImage[0] ;
		if($permanentImage=='false'){
			$permanentImage=''; 
		}
		$permanentImage_x = $array_permanentImage[1] ;
		$permanentImage_y = $array_permanentImage[2] ;
		$permanentImage_width = $array_permanentImage[3] ;
		$permanentImage_height = $array_permanentImage[4] ;
		
		$pdf->addPngFromFile($permanentImage, $permanentImage_x + $mapOffset_left , $mapOffset_bottom + $map_height -  $permanentImage_y - $permanentImage_height, $permanentImage_width , $permanentImage_height);
	   
	}
}

$theMeasureConfigArray = array(
   "do_fill" => FALSE,
     "fill_color" => array(
     "r" => 128 / 255,
     "g" => 128 / 255,
     "b" => 128 / 255
   ),
   "do_stroke" => FALSE,
     "stroke_color" => array(
     "r" => 254 / 255,
     "g" => 1 / 255,
     "b" => 1 / 255
   ),
   "line_style" => array(
       "width" => 2,
       "cap" => 'butt',
       "join" => 'miter',
       "dash" => array(10, 6)
       )
   );

if ($_REQUEST["measured_x_values"]!=''){
	addMeasuredItem($pdf, $_REQUEST["measured_x_values"], $_REQUEST["measured_y_values"], $theMeasureConfigArray);
	hideElementsOutsideMapframe($pdf);
}

# dashed line
$pdf->setLineStyle($linewidth_dashed, '','', array(2,2));
$pdf->line($mapOffset_left - $linewidth_dashed, $mapOffset_bottom - $linewidth_dashed, $mapOffset_left - $linewidth_dashed, $mapOffset_bottom  + $map_height + $linewidth_dashed);
$pdf->line($mapOffset_left - $linewidth_dashed, $mapOffset_bottom  + $map_height + $linewidth_dashed, $mapOffset_left + $map_width + $linewidth_dashed, $mapOffset_bottom + $map_height + $linewidth_dashed);
$pdf->line($mapOffset_left + $map_width + $linewidth_dashed, $mapOffset_bottom + $map_height + $linewidth_dashed, $mapOffset_left + $map_width + $linewidth_dashed, $mapOffset_bottom -$linewidth_dashed);
$pdf->line($mapOffset_left + $map_width + $linewidth_dashed, $mapOffset_bottom -$linewidth_dashed, $mapOffset_left - $linewidth_dashed, $mapOffset_bottom - $linewidth_dashed);


#line
$pdf->setLineStyle($linewidth, '', '', array());
$pdf->line($mapOffset_left - $border, $mapOffset_bottom - $border, $mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border);
$pdf->line($mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border, $mapOffset_left + $map_width + $border, $mapOffset_bottom + $map_height + $border);
$pdf->line($mapOffset_left + $map_width + $border, $mapOffset_bottom + $map_height + $border, $mapOffset_left + $map_width + $border, $mapOffset_bottom -$border);
$pdf->line($mapOffset_left + $map_width + $border, $mapOffset_bottom -$border, $mapOffset_left - $border, $mapOffset_bottom - $border);

if($epsg == "EPSG:4326"){

	$text4 = "";
}

if($size == "A4" && $format == "portrait"){
   #header:
   $pdf->line($mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border, $mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border + $header_height);
   $pdf->line($mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border + $header_height, $mapOffset_left + $map_width + $border, $mapOffset_bottom  + $map_height + $border + $header_height);
   $pdf->line($mapOffset_left + $map_width + $border, $mapOffset_bottom  + $map_height + $border + $header_height, $mapOffset_left + $map_width + $border, $mapOffset_bottom  + $map_height + $border);
   $pdf->line( $mapOffset_left + $map_width/2, $mapOffset_bottom  + $map_height + $border, $mapOffset_left + $map_width/2, $mapOffset_bottom  + $map_height + $border + $header_height);
   #header-text
   $mySize = 9;
   $length = $pdf->getTextWidth($mySize, $text1);
   $pdf->addText($mapOffset_left - ($border/2) + ($map_width/4) - ($length/2),$mapOffset_bottom + $map_height + $border + 60,$mySize,$text1);

   $mySize = 8;
   $length = $pdf->getTextWidth($mySize, $text2);   
   $pdf->addText($mapOffset_left - ($border/2) + ($map_width/4) - ($length/2),$mapOffset_bottom + $map_height + $border + 45,$mySize,$text2);
   
   $length = $pdf->getTextWidth($mySize, $text3);
   $pdf->addText($mapOffset_left - ($border/2) + ($map_width/4) - ($length/2),$mapOffset_bottom + $map_height + $border + 35,$mySize,$text3);

   $mySize = 9;
   $length = $pdf->getTextWidth($mySize, $text4);   
   $pdf->addText($mapOffset_left - ($border/2) + ($map_width/4) - ($length/2),$mapOffset_bottom + $map_height + $border + 20,$mySize,$text4);

   $mySize = 8;
   $length = $pdf->getTextWidth($mySize, $text5);   
   $pdf->addText($mapOffset_left - ($border/2) + ($map_width/4) - ($length/2),$mapOffset_bottom + $map_height + $border + 10,$mySize,$text5);
   
   $mySize = 9;
   $length = $pdf->getTextWidth($mySize, $text6);   
   $pdf->addText($mapOffset_left + ($border/2) + ($map_width/4*3) - ($length/2),$mapOffset_bottom + $map_height + $border + 60,$mySize,$text6);
   $length = $pdf->getTextWidth($mySize, $text7);   
   $pdf->addText($mapOffset_left + ($border/2) + ($map_width/4*3) - ($length/2),$mapOffset_bottom + $map_height + $border + 50,$mySize,$text7);
   $length = $pdf->getTextWidth($mySize, $text8);   
   $pdf->addText($mapOffset_left + ($border/2) + ($map_width/4*3) - ($length/2),$mapOffset_bottom + $map_height + $border + 30,$mySize,$text8);
   $length = $pdf->getTextWidth($mySize, $text9);   
   $pdf->addText($mapOffset_left + ($border/2) + ($map_width/4*3) - ($length/2),$mapOffset_bottom + $map_height + $border + 20,$mySize,$text9);
   $length = $pdf->getTextWidth($mySize, $text10);   
   $pdf->addText($mapOffset_left + ($border/2) + ($map_width/4*3) - ($length/2),$mapOffset_bottom + $map_height + $border + 10,$mySize,$text10);
   
   //special image on the map-page
   if ($special == true){
   	$pdf->addPngFromFile($specialImage, $specialImage_left, $specialImage_bottom , $specialImage_width, $specialImage_height);
   }
   
   #footer
   $pdf->line($mapOffset_left - $border, $mapOffset_bottom - $border, $mapOffset_left - $border, $mapOffset_bottom - $border - $footer_height);
   $pdf->line($mapOffset_left - $border, $mapOffset_bottom - $border - $footer_height, $mapOffset_left + $map_width + $border, $mapOffset_bottom - $border - $footer_height);
   $pdf->line($mapOffset_left + $map_width + $border, $mapOffset_bottom - $border - $footer_height, $mapOffset_left + $map_width + $border, $mapOffset_bottom - $border);
   
   $mySize = 8;
   $length = $pdf->getTextWidth($mySize, $text11);   
   $pdf->addText( $mapOffset_left + $map_width/2 - ($length/2),$mapOffset_bottom - $border - 10,$mySize,$text11);
   $mySize = 6;
   $length = $pdf->getTextWidth($mySize, $text12);   
   $pdf->addText( $mapOffset_left + $map_width/2 - ($length/2),$mapOffset_bottom - $border - 18,$mySize,$text12);
   $length = $pdf->getTextWidth($mySize, $text13);
   $pdf->addText( $mapOffset_left + $map_width/2 - ($length/2),$mapOffset_bottom - $border - 25,$mySize,$text13);
}
else{
   $pdf->setColor(1,1,1);
   $ll = array($mapOffset_left + $map_width - $header_width + $border - $linewidth, $mapOffset_bottom - $border + 1);
   $pdf->filledRectangle($ll[0], $ll[1], $header_width,$header_height);
   $pdf->line($ll[0], $ll[1], $ll[0], $ll[1] + $header_height);
   $pdf->line($ll[0], $ll[1] + $header_height, $ll[0] + $header_width, $ll[1] + $header_height);

   $pdf->line($ll[0] + 2, $ll[1] + 2, $ll[0] + 2,  $ll[1] + $header_height - 2);
   $pdf->line($ll[0] + 2,  $ll[1] + $header_height - 2, $ll[0] - 2 + $header_width, $ll[1] + $header_height - 2);
   $pdf->line($ll[0] - 2 + $header_width, $ll[1] + $header_height - 2, $ll[0] - 2 + $header_width, $ll[1] + 2);
   $pdf->line($ll[0] - 2 + $header_width, $ll[1] + 2, $ll[0] + 2, $ll[1] + 2);
   
   $pdf->line($ll[0] + 2, $ll[1] + 110 , $ll[0] - 2 + $header_width, $ll[1] + 110);
   $pdf->line($ll[0] + 2, $ll[1] + 40 , $ll[0] - 2 + $header_width, $ll[1] + 40);
   
   
   //special image on the map-page
   if ($special == true){
   	$pdf->addPngFromFile($specialImage, $specialImage_left, $specialImage_bottom , $specialImage_width, $specialImage_height);
   }
   
    if($epsg == "EPSG:4326"){

	$text4 = "";
	}
	
   $pdf->setColor(0,0,0);
   $mySize = 9;
   $length = $pdf->getTextWidth($mySize, $text1);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,185,$mySize,$text1);
   
   $mySize = 8;
   $length = $pdf->getTextWidth($mySize, $text2);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,175,$mySize,$text2);
   $length = $pdf->getTextWidth($mySize, $text3);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,165,$mySize,$text3);
   $mySize = 9;
   $length = $pdf->getTextWidth($mySize, $text4);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,155,$mySize,$text4);
   $mySize = 8;
   $length = $pdf->getTextWidth($mySize, $text5);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,145,$mySize,$text5);   
   
   $mySize = 9;
   $length = $pdf->getTextWidth($mySize, $text6);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,125,$mySize,$text6);
   $length = $pdf->getTextWidth($mySize, $text7);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,115,$mySize,$text7);
   $length = $pdf->getTextWidth($mySize, $text8);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,95,$mySize,$text8);
   $length = $pdf->getTextWidth($mySize, $text9);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,85,$mySize,$text9);
   $length = $pdf->getTextWidth($mySize, $text10);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,75,$mySize,$text10);
   
   $mySize = 8;
   $length = $pdf->getTextWidth($mySize, $text11);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,60,$mySize,$text11); 
   $mySize = 6;
   $length = $pdf->getTextWidth($mySize, $text14);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,53,$mySize,$text14);
   $length = $pdf->getTextWidth($mySize, $text15);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,47,$mySize,$text15);
   $length = $pdf->getTextWidth($mySize, $text16);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,41,$mySize,$text16);
   $length = $pdf->getTextWidth($mySize, $text17);   
   $pdf->addText($ll[0] + $header_width/2 - $length/2,35,$mySize,$text17);                             
}
#Coordinates
$myMinx = "R ".substr(round($coord[0]), 0, 4)." ".substr(round($coord[0]), 4, 3)."";
$myMiny = "H ".substr(round($coord[1]), 0, 4)." ".substr(round($coord[1]), 4, 3)."";
$myMaxx = "R ".substr(round($coord[2]), 0, 4)." ".substr(round($coord[2]), 4, 3)."";
$myMaxy = "H ".substr(round($coord[3]), 0, 4)." ".substr(round($coord[3]), 4, 3)."";

$mySize = 9;
$pdf->addText($mapOffset_left - 3, $mapOffset_bottom, $mySize, $myMiny, -90);
$pdf->addText($mapOffset_left, $mapOffset_bottom  - ($pdf->getFontHeight($mySize)), $mySize, $myMinx);
$pdf->addText($mapOffset_left  + $map_width - ($pdf->getTextWidth($mySize, $myMaxx)), $mapOffset_bottom + $map_height  + 3, $mySize, $myMaxx);
$pdf->addText($mapOffset_left + $map_width + 3, $mapOffset_bottom + $map_height, $mySize, $myMaxy, 90);






if ($overview==true){
	// analyse request, draw rectancle
	$filename = preg_replace("/map_/","overview_",$filename);
	if($size == "A4" && $format == "portrait"){
		$i = new weldOverview2PNG($overview_url,$array_urls[0] ,$filename);

		$pdf->addPngFromFile($filename, $overview_left,$overview_bottom, $overview_width, $overview_height);
		if($unlink == true){
			unlink($filename);
		}
	}
	else{
		$i = new weldOverview2PNG($overview_url,$array_urls[0],$filename);

		//$pdf->addPngFromFile($filename, $mapOffset_left,$mapOffset_bottom, $overview_width, $overview_height);
		$pdf->addPngFromFile($filename, $overview_left,$overview_bottom, $overview_width, $overview_height);
		if($unlink == true){
			unlink($filename);
		}
	}
}

if ($northarrow==true){
	$pdf->addPngFromFile($northarrowImage, $northarrow_left, $northarrow_bottom , $northarrowImage_width, $northarrowImage_height);
}

if($epsg == "EPSG:4326"){

	$scalebar = false;
}

if($scalebar == true){
	if ($size == "A4" && $format == "portrait"){
		$scalebar_left = $a4p_scalebar_left;
		$scalebar_bottom = $a4p_scalebar_bottom;
	}elseif ($size == "A4" && $format == "landscape"){
		$scalebar_left = $a4l_scalebar_left;
		$scalebar_bottom = $a4l_scalebar_bottom;
	}elseif ($size == "A3" && $format == "portrait"){
		$scalebar_left = $a3p_scalebar_left;
		$scalebar_bottom = $a3p_scalebar_bottom;
	}elseif ($size == "A3" && $format == "landscape"){
		$scalebar_left = $a3l_scalebar_left;
		$scalebar_bottom = $a3l_scalebar_bottom;
	}elseif ($size == "A2" && $format == "portrait"){
		$scalebar_left = $a2p_scalebar_left;
		$scalebar_bottom = $a2p_scalebar_bottom;
	}elseif ($size == "A2" && $format == "landscape"){
		$scalebar_left = $a2l_scalebar_left;
		$scalebar_bottom = $a2l_scalebar_bottom;
	}elseif ($size == "A1" && $format == "portrait"){
		$scalebar_left = $a1p_scalebar_left;
		$scalebar_bottom = $a1p_scalebar_bottom;
	}elseif ($size == "A1" && $format == "landscape"){
		$scalebar_left = $a1l_scalebar_left;
		$scalebar_bottom = $a1l_scalebar_bottom;
	}elseif ($size == "A0" && $format == "portrait"){
		$scalebar_left = $a0p_scalebar_left;
		$scalebar_bottom = $a0p_scalebar_bottom;
	}elseif ($size == "A0" && $format == "landscape"){
		$scalebar_left = $a0l_scalebar_left;
		$scalebar_bottom = $a0l_scalebar_bottom;
	}
	
	$array_scalebar = setscalebar($map_scale);
	
	$scalebar_left = $scalebar_left + $header_width/2 - $array_scalebar[1]/2;
	$pdf->setLineStyle($scalebar_height, '','', array());
   	$pdf->setColor(0,0,0);
	#$pdf->line($scalebar_left, $scalebar_bottom , $scalebar_left - 200 + $array_scalebar[1], $scalebar_bottom);
	$pdf->filledRectangle($scalebar_left, $scalebar_bottom, $array_scalebar[1],$scalebar_height);
	
	$pdf->setColor(1,1,1);
	$pdf->filledRectangle($scalebar_left + $array_scalebar[1]/4 + 1 , $scalebar_bottom + 1, $array_scalebar[1]/4 - 1 ,$scalebar_height-2);	
	$pdf->setColor(1,1,1);
	$pdf->filledRectangle($scalebar_left + 3*($array_scalebar[1]/4) + 1 , $scalebar_bottom + 1, $array_scalebar[1]/4 - 2 ,$scalebar_height-2);	
		
		
	#$pdf->setColor(1,0,1);
	#$pdf->filledRectangle($scalebar_left  , $scalebar_bottom - 20, 1 * $DPC ,$scalebar_height-2);	
	
	
	# value - Einheiten 
	$pdf->setColor(0,0,0);	
   $mySize = 8;
   $scalebar_height_half = 0.5 * $scalebar_height;  
    
	$myText = 0;
   $length = $pdf->getTextWidth($mySize, $myText);  
   $pdf->addText($scalebar_left - $length/2 ,$scalebar_bottom + 9 ,$mySize,$myText);
   
	$myText = $array_scalebar[2]/2;
   $length = $pdf->getTextWidth($mySize, $myText);     
   $pdf->addText($scalebar_left + $array_scalebar[1]/2 - $length/2 ,$scalebar_bottom + 9 ,$mySize,$myText);
	
	$myText = $array_scalebar[2];
   $length = $pdf->getTextWidth($mySize, $myText);  
   $pdf->addText($scalebar_left + $array_scalebar[1] - $length/2 - $length/4 ,$scalebar_bottom + 9 ,$mySize,$myText);
 
	
	$pdf->setColor(0,0,0);	
   $mySize = 8;
   $scalebar_height_half = 0.5 * $scalebar_height;   
   $myText = $array_scalebar[0];

   #$pdf->addText($scalebar_left + $scalebar_width + 5,$scalebar_bottom - $scalebar_height_half ,$mySize,$myText);
  

	#units  
   $length = $pdf->getTextWidth($mySize, $myText);
   $pdf->addText($scalebar_left + $array_scalebar[1]/2 - $length/2 ,$scalebar_bottom - 12 ,$mySize,$myText);
 
}


/* ------------------------ 
    new page for legend
   ------------------------ */
if ($legend == true && $_REQUEST["mylegend"]=='true'){

	$pdf->ezNewPage();

	//Pageborder (top, bottom, left, right)
	
	if($size == "A4" && $format == "portrait"){
	  $pdf->ezSetMargins(50,50,80,30);
	} else {
	  $pdf->ezSetMargins(60,35,60,60);
	}
	
	//Requests
	if(CHARSET=="UTF-8"){
		$new_wms_title=utf8_decode($_REQUEST["wms_title"]);
	}else{
		$new_wms_title=$_REQUEST["wms_title"];
	}
	
	if(CHARSET=="UTF-8"){
		$new_layers=utf8_decode($_REQUEST["layers"]);
	}else{
		$new_layers=$_REQUEST["layers"];
	}
	
	$my_wms_id = explode("___",$_REQUEST["wms_id"]);
	$my_wms_title = explode("___",$new_wms_title);
	$my_layers = explode("___",$new_layers);
	if($matching == true){
        $my_legend = preg_replace($pattern,$replacement,$_REQUEST["legendurl"]);  
    }
    else{
        $my_legend = $_REQUEST["legendurl"];
    }
    $my_legend = explode("___",$my_legend);

	//columns
	if($size == "A4" && $format == "portrait"){
	  $pdf->ezColumnsStart(array ('num'=>2, 'gap'=>10));
	} else {
	  $pdf->ezColumnsStart(array ('num'=>3, 'gap'=>10));
	}

	//header from printPDF.conf
	//$pdf->ezText("<b>".$titel."</b>", 13);
	$pdf->ezText("<b><u>".$legendText."</u></b>", 13);

	
	//Seitenraender (top, bottom, left, right)
	if($size == "A4" && $format == "portrait"){
	  $pdf->ezSetMargins(70,35,80,30);
	} else {
	  $pdf->ezSetMargins(100,35,60,60);
	}
	
	//generate the legend---------------------------------------------
	
	// Gesamthoehe Legende / height of the legend
	$sum_legend_height = 0;


	for($i=0; $i<count($my_wms_id); $i++){
		if ($my_wms_id[$i] != '0'){  //wms_id not 0
			$layer = explode(",",$my_layers[$i]);
			$my_legendurls = explode(",",$my_legend[$i]);
			
			$wms_y_position = $pdf->ezText("<b>".$my_wms_title[$i]."</b>", 12, array('spacing'=>1.2));
			$wms_zeilenhoehe   =  $pdf->getFontHeight(12);
				 
			// add this to the height of the legend /addiere dies zur Gesamthoehe Legende
			$sum_legend_height += $wms_zeilenhoehe;
			
			//Layer
			$l = 0;		#l temporary parameter to count the layer /Hilfvariable zum durchz�hlen der angezeigten Layer
			for($j=0; $j<count($my_legendurls); $j++){
				// url with grouped layers------------------
				$temp_url = explode('*',$my_legendurls[$j]);
				$temp_layers = explode('*',$layer[$j]);
	
				for ($q=0; $q <count($temp_url);$q++){  
					if($temp_url[$q] == '1' ){			// Layertitle for the parent of grouped layers	 
						// add this to the height of the legend /addiere dies zur Gesamth�he Legende		
						$layer_y_position = $pdf->ezText($temp_layers[$q], 11, array('spacing'=>1.2));
						$layer_zeilenhoehe   =  $pdf->getFontHeight(12);
						$sum_legend_height += $layer_zeilenhoehe;
						
					}elseif($temp_url[$q] != '0' ){
						$funktionsaufruf = new SaveLegend($temp_url[$q],$legendFilename);
						$imgsize = getimagesize($legendFilename);
						// add this to the height of the legend /addiere dies zur Gesamthoehe der Legende
						$sum_legend_height += $imgsize[1];
	
						//calculate text + picture / Berechnung Groesse Schrift + Bild
						if($l == 0){
						       $y_position = $wms_y_position;
						       $wms_y_position = '';
						}else{
							 $y_position = $pdf->ezText("", 1, array('spacing'=>1.2));
						}
						$layer_zeilenhoehe = $pdf->getFontHeight(11);
						$next_position = $y_position - $layer_zeilenhoehe - $imgsize[1];
						
						// add this to the height of the legend / addiere dies zur Gesamth�he Legende
						$sum_legend_height += $layer_zeilenhoehe;
							
						$l = $l+1;
		
		     			// if text + picture are smaler then the lower margin + textsize, then set a space
						//wenn Schrift + Bild kleiner der unteren Margin + Zeilenhoehe, dann Abstand setzen
						 if($size == "A4" && $format == "portrait" && $next_position <= 35 +$layer_zeilenhoehe){ //90 $layer_zeilenhoehe
							$space = $layer_zeilenhoehe + $imgsize[1];
						  $pdf->ezSetDy(-$space);
						} 
						if($size == "A4" && $format == "landscape" && $next_position <= 35+$layer_zeilenhoehe){//50
							$space = $layer_zeilenhoehe + $imgsize[1];
						  $pdf->ezSetDy(-$space);
						}
				
						//write the header layername / Ueberschrift schreiben
						$legend = $temp_layers[$q]."\n";  //$layer[$j]."\n"; 
						$pdf->ezText($legend, 11, array('spacing'=>1.2));
						
						//$pdf->ezText($url, 9, array('spacing'=>1.2));
						//print the picture / Bild schreiben
						$pdf->ezImage($legendFilename, 0, 'width', 'none', 'left');
						if($unlink == true){
							unlink($legendFilename);
						}
						
					} //if legendurl
				}// for legendurl
	
			  	//frames (x1, y1, x2, y2)
				if($size == "A4" && $format == "portrait"){
					#line  
					$pdf->setLineStyle($linewidth, '', '', array());
					//left
					$pdf->line($mapOffset_left - $border, $mapOffset_bottom - $border - $footer_height, $mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border + $header_height);
					//right
					$pdf->line($mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border + $header_height, $mapOffset_left + $map_width + $border, $mapOffset_bottom + $map_height + $border + $header_height);
					//top
					$pdf->line($mapOffset_left + $map_width + $border, $mapOffset_bottom + $map_height + $border + $header_height, $mapOffset_left + $map_width + $border, $mapOffset_bottom - $border - $footer_height);
					//bottom
					$pdf->line($mapOffset_left + $map_width + $border, $mapOffset_bottom -$border - $footer_height, $mapOffset_left - $border, $mapOffset_bottom - $border - $footer_height);
					
					if ($legendImage!=''){
						//image on top of page
						$pdf->addPngFromFile($legendImage, $mapOffset_left + $map_width + $border - $legendImage_width -6, $mapOffset_bottom + $map_height + $border + $header_height - $legendImage_height - 4 , $legendImage_width, $legendImage_height);
					}    
				} else {
				  $pdf->setLineStyle($linewidth, '', '', array());
				  //left
				  $pdf->line($mapOffset_left - $border, $mapOffset_bottom - $border, $mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border);
				  //right
				  $pdf->line($mapOffset_left - $border, $mapOffset_bottom  + $map_height + $border , $mapOffset_left + $map_width + $border, $mapOffset_bottom + $map_height + $border);
				  //top
				  $pdf->line($mapOffset_left + $map_width + $border, $mapOffset_bottom + $map_height + $border , $mapOffset_left + $map_width + $border, $mapOffset_bottom - $border);
				   //bottom
				   $pdf->line($mapOffset_left + $map_width + $border, $mapOffset_bottom -$border , $mapOffset_left - $border, $mapOffset_bottom - $border);
				   
					if ($legendImage!=''){
						//image on top of page
						$pdf->addPngFromFile($legendImage, $mapOffset_left + $map_width + $border - $legendImage_width -6, $mapOffset_bottom + $map_height + $border - $legendImage_height -4 , $legendImage_width, $legendImage_height);
						//line under legend (only landscape)
						//$pdf->line($mapOffset_left - $border, $mapOffset_bottom + $map_height + $border  - $legendImage_height - 4 , $mapOffset_left + $map_width + $border, $mapOffset_bottom + $map_height + $border - $legendImage_height - 6);
					}
				}
			}// for layers
		} //if wms_id not 0
	}// for wms
	
	/****
    * USER POLYGON:
    */
   if($_REQUEST["measured_x_values"] != ""
      && $_REQUEST["measured_y_values"] != ""
        && is_file($legendFilenameUserPolygon)) {
     // load image
       $myY = $pdf->ezText("<b>temporary Object</b>", 11);
       $pdf->ezSetDy(-15);
       $pdf->ezImage($legendFilenameUserPolygon, 5, 17, 'none', 'left');
       if($unlink == true){
       		unlink($legendFilenameUserPolygon);
       }
       $pdf->ezSetY($myY - 7);
       
        $pdf->ezText("Element", 11, array("left" => 25));
             // deletes image
	    
	    $pdf->ezSetDy(-15);             
                    
   } 
	
$pdf->ezText($legendFooter, 11);
}//legend true
/* ------------------------ 
    end of legend  
   ------------------------ */

ob_clean();
if($download == false){
	$pdf->ezStream();
}
else{
	$content = $pdf->ezOutput();

	$fp = fopen($downloadFile,'w');
	fwrite($fp,$content);
	fclose($fp);
	echo "<html><head></head><body><a href='".$downloadFile."'>".$downloadText."</a></body></html>";
}
?>
