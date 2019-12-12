<?php
# $Id: ext_weldMaps.php 8077 2011-08-28 14:35:04Z astrid_emde $
# http://www.mapbender.org/index.php/ext_weldMaps.php
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

/*
* extension weldMaps
* expects an array Mapbender::session()->get("mb_print_urls") containing the mapRequests  
* expects Mapbender::session()->get("mb_print_resolution")
* **/

require(dirname(__FILE__)."/../php/mb_validateSession.php");

class weldMaps{
	var $map_width;
	var $map_height;
   var $integrateURL;
   var $matching;
   var $pattern;
   var $replacement;

	function weldMaps($map_urls, $factor){
      #{ file | false }

      #$debug_file = LOG_DIR."/print.log";
      $debug_file = false;
      
      settype($factor, "integer");     
      
      if($debug_file){
         $debug = fopen($debug_file,"a") ;
         fputs($debug, str_repeat("-",50) . chr(13). chr(10). date("d.m.y, H:i:s") . " Open debug-file: " . chr(13). chr(10));
         for($i=0; $i<count($map_urls); $i++){
            fputs($debug, $i . " " . $map_urls[$i] . chr(13). chr(10));
            //print_r($map_urls);
         }
         fclose($debug);
      }

      if(!isset($factor)){
         $factor = 1;
      }
      include(dirname(__FILE__)."/../../conf/print.conf");
      $this->integrateURL = $integrateURL;
      $this->matching = $matching;
      $this->pattern = $pattern;
      $this->replacement = $replacement;
		if(preg_match("/width=(\d*)/i", $map_urls[0], $matches)){
			$this->map_width = round(intval($matches[1]) * intval($factor) * floatval($deformation));
		}
		if(preg_match("/height=(\d*)/i", $map_urls[0], $matches)){
			$this->map_height = round(intval($matches[1]) * intval($factor) * floatval($deformation));
		}
		for($i=0; $i<count($map_urls); $i++){         
      	$my_urls[$i] = $this->changeResolution($map_urls[$i], $factor);
         if($debug_file){
            $debug = fopen($debug_file,"a") ;
            fputs($debug, "transformated: " . chr(13). chr(10));         
            fputs($debug, $i . " ". $my_urls[$i] . chr(13). chr(10));
            fclose($debug);
         }
		}  
		$image = imagecreate($this->map_width, $this->map_height);
		$white = ImageColorAllocate($image,255,255,255); 
		ImageFilledRectangle($image,0,0,$this->map_width,$this->map_height,$white); 

		for($i = 0; $i<count($my_urls); $i++){
    		$im = $this->loadpng($my_urls[$i]);
	      	imagecopy($image, $im, 0, 0, 0, 0, $this->map_width, $this->map_height); 
		}
		ImagePNG($image);
		ImageDestroy($image);
	}
	function loadpng ($imgname) {
   $im = @ImageCreateFromPNG ($imgname);
   if (!$im) {                           
      $im = ImageCreate ($this->map_width, $this->map_height); 
      $bgc = ImageColorAllocate ($im, 255, 255, 255);
      $tc  = ImageColorAllocate ($im, 0, 0, 0);
      ImageFilledRectangle ($im, 0, 0, $map_width, $map_height, $bgc); 
      ImageString($im, 1, 5, 5, "Fehler beim �ffnen von: ", $tc);
      $chunk = chunk_split(urldecode($imgname), 60, "###");
      $array_chunk = explode("###", $chunk);
      for($i=0; $i<count($array_chunk); $i++){
         ImageString($im, 1, 5, 20+($i*10), $array_chunk[$i] , $tc);
      } 
   }
   if($this->integrateURL == true){
      $tc  = ImageColorAllocate ($im, 0, 0, 0);
      ImageString($im, 1, 5, 5, $imgname, $tc);
   }
  	return $im;
	} 
	function changeResolution($map_url,$factor){
		$newResolution = preg_replace("/width=\d*/i", "WIDTH=" . $this->map_width, $map_url);
		$newResolution = preg_replace("/height=\d*/i", "HEIGHT=" . $this->map_height, $newResolution);
      if($this->matching == true && $factor > 1){
         $newResolution = preg_replace($this->pattern, $this->replacement, $newResolution);
      }
		return $newResolution;
	}	
}

$map_urls = explode("###",Mapbender::session()->get("mb_print_url"));
$output = new weldMaps($map_urls, Mapbender::session()->get("mb_print_resolution"));
?>
