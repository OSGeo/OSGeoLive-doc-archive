<?php
# $Id: class_weldMaps2PNG.php 5529 2010-02-19 15:54:30Z christoph $
# http://www.mapbender.org/index.php/class_weldMaps2PNG.php
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
* class_weldMaps2PNG
* @version 1.0.0
* get/post '___' separated maprequests
*
**/
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/class_stripRequest.php");
require_once(dirname(__FILE__)."/class_connector.php");

class weldMaps2PNG{

	function weldMaps2PNG($urls,$filename, $encode = true){
		if(!$urls || $urls == ""){
			$e = new mb_exception("weldMaps2PNG: no maprequests delivered");
		}
		$url = explode("___", $urls);
		$obj1 = new stripRequest($url[0]);
		$width = $obj1->get("width");
		$height = $obj1->get("height");
		
		$image = imagecreatetruecolor($width, $height	);
		$white = ImageColorAllocate($image,255,255,255); 
		ImageFilledRectangle($image,0,0,$width,$height,$white); 

		for($i=0; $i<count($url); $i++){
			$obj = new stripRequest($url[$i]);

			$url[$i] = $obj->setPNG();
			$url[$i] = $obj->encodeGET($encode);
			$img = $this->loadpng($url[$i]);
			if($img != false){
				imagecopy($image, $img, 0, 0, 0, 0, $width, $height);
				@imagedestroy($img); 
			}
			else{
				$e = new mb_exception("weldMaps2PNG: unable to load image: " . $url[$i]);
			}
		}
		imagepng($image,$filename);
		imagedestroy($image); 

	}
	
	function loadpng ($imgurl) {
		$obj = new stripRequest($imgurl);
		$x = new connector($imgurl);
		
		//
		$f = $obj->get("format");
		
		$im = @imagecreatefromstring($x->file);
		if(!$im){
			$im = false;
			$e = new mb_exception("weldMaps2PNG: unable to load image: ".$imgurl);
		}  
		return $im;
		
	}
	
}

?>
