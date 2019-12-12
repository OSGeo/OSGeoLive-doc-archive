<?php
# $Id: class_weldOverview2PNG.php 1584 2007-08-06 07:56:11Z christoph $
# $Header: /cvsroot/mapbender/mapbender/http/classes/class_weldOverview2PNG.php,v 1.3 2006/02/22 11:56:22 astrid_emde Exp $
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
* class_weldOverview2PNG
* @version 1.0.0
* get/post '___' separated maprequests
*
**/
require_once(dirname(__FILE__)."/class_stripRequest.php");
require_once(dirname(__FILE__)."/class_mb_exception.php");
require_once(dirname(__FILE__)."/class_connector.php");

class weldOverview2PNG_rotate{

	function weldOverview2PNG_rotate($url_overview,$url_extent,$filename, $rotatedExtent){

		if(!$url_overview || $url_overview == ""){
			$e = new mb_exception("weldOverview2PNG: no maprequests delivered");
		}
		$url = $url_overview;
		$obj1 = new stripRequest($url);
		$width = $obj1->get("width");
		$height = $obj1->get("height");
		
		/*
		$e = new mb_exception("--------overview-----------------");
		$e = new mb_exception("-----width ".$width." / height: ".$height."--------------------");
		$e = new mb_exception("url_overview: ".$url_overview);
		$e = new mb_exception("url_extent: ".$url_extent);
		*/	
		$image = imagecreatetruecolor($width, $height);
		$white = ImageColorAllocate($image,255,255, 255); 
		ImageFilledRectangle($image,0,0,$width,$height,$white); 
		
		//overview
		$obj = new stripRequest($url_overview);
		$xurl_overview = $obj->setPNG();
		$xurl_overview = $obj->encodeGET();
		$img = $this->loadpng($xurl_overview);		
		if($img != false){
			imagecopy($image, $img, 0, 0, 0, 0, $width, $height);
		}
		else{
			$e = new mb_exception("weldMaps2PNG: unable to load image: " . $url_overview);
		}
		
		
		// rectangle - position of the map in the overview
		$objx = new stripRequest($url_extent);
		$ex_width = $objx->get("width");
		$ex_height = $objx->get("height");
		$extent = explode(",",$objx->get("BBOX"));

		$p1 = $this->makeRealWorld2mapPos($url_overview, round($rotatedExtent[0][0]), round($rotatedExtent[0][1]));
		$p2 = $this->makeRealWorld2mapPos($url_overview, round($rotatedExtent[1][0]), round($rotatedExtent[1][1]));
		$p3 = $this->makeRealWorld2mapPos($url_overview, round($rotatedExtent[2][0]), round($rotatedExtent[2][1]));
		$p4 = $this->makeRealWorld2mapPos($url_overview, round($rotatedExtent[3][0]), round($rotatedExtent[3][1]));
		
		/*
		$e = new mb_exception("ex_width: " .$ex_width);
		$e = new mb_exception("ex_height: " . $ex_height);
		$e = new mb_exception("bbox:".$extent[0]."--".$extent[1]."--".$extent[2]."--------".$extent[3]);
		$e = new mb_exception("ll: " . $lowerleft[0]." / ".$lowerleft[1]);
		$e = new mb_exception("ur: " . $upperright[0]." / ".$upperright[1]);
		*/
		
		$red = ImageColorAllocate($image,255,0,0); 
		imageline ( $image, $p1[0], $p1[1], $p2[0], $p2[1], $red);
		imageline ( $image, $p2[0], $p2[1], $p3[0], $p3[1], $red);
		imageline ( $image, $p3[0], $p3[1], $p4[0], $p4[1], $red);
		imageline ( $image, $p4[0], $p4[1], $p1[0], $p1[1], $red);
		
		
		// black frame - size of the overview
		$black = ImageColorAllocate($image,0,0,0); 
		imageline ( $image, 0, 0, $width-1, 0, $black);
		imageline ( $image, $width-1, 0, $width-1, $height-1, $black);
		imageline ( $image, $width-1, $height-1, 0, $height-1, $black);
		imageline ( $image, 0, $height-1, 0, 0, $black);
		
		imagepng($image,$filename);
		imagedestroy($image); 
		
	}
	
	
	function loadpng ($imgurl) {
		$obj = new stripRequest($imgurl);
		$x = new connector($imgurl);
			
		$f = $obj->get("format");
		$im = @imagecreatefromstring($x->file);

		if(!$im){
			$im = false;
			$e = new mb_exception("weldOverview2PNG: unable to load image: ".$imgurl);
		}  
		return $im;
	}
	
	function makeRealWorld2mapPos($url, $rw_posx, $rw_posy){
	   	$obj = new stripRequest($url);
		$width = $obj->get("width");
		$height = $obj->get("height");
		
		
		#$e = new mb_exception("weld_url: ".$url);
		#$e = new mb_exception("w: ".$width."height".$height);

	   $arrayBBox = explode(",",$obj->get("BBOX"));
	   $minX = $arrayBBox[0];
	   $minY = $arrayBBox[1];
	   $maxX = $arrayBBox[2];
	   $maxY = $arrayBBox[3];

	#$e = new mb_exception("------- minx: ".$minX." miny:".$minY." maxx:".$maxX." maxy:".$maxY."----------");

	   $xtentx = $maxX - $minX ; 
	   $xtenty = $maxY - $minY ;  

	   $pixPos_x = round((($rw_posx - $minX)/$xtentx)*$width);
	   $pixPos_y = round((($maxY - $rw_posy)/$xtenty)*$height);

	   $pixPos = array($pixPos_x, $pixPos_y);
	   
	   return $pixPos;
	}
	
}

?>