<?php
# http://www.mapbender.org/index.php/class_SaveLegend.php
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
* class_SaveLegend
* @version 1.0.0
* getlegendurl
*
**/
require_once(dirname(__FILE__)."/../../core/globalSettings.php");

require_once(dirname(__FILE__)."/class_stripRequest.php");
require_once(dirname(__FILE__)."/class_connector.php");

class SaveLegend{

	function SaveLegend($url,$legend_filename){
		if(!$url || $url == ""){
			$e = new mb_exception("SaveLegend: no legendurl delivered");
		}
		$x = new connector($url);
		//save file in tmp folder to extract right size - sometimes the size could not be detected by url!
		if($legendFileHandle = fopen($legend_filename, "w")){
			fwrite($legendFileHandle,$x->file);
			fclose($legendFileHandle);
			$e = new mb_notice("SaveLegend: new legend file created: ".$legend_filename);
		} else {
			$e = new mb_exception("SaveLegend: legend file ".$legend_filename." could not be created!");
		}
		//get size of image
		$size = getimagesize($legend_filename);
		$width = $size[0];
		$height = $size[1];
		$image = imagecreatetruecolor($width, $height);
		$white = ImageColorAllocate($image,255,255,255); 
		ImageFilledRectangle($image,0,0,$width,$height,$white);
		//load image from url to create new image
		$img = $this->loadpng($url);
		if($img != false){
			imagecopy($image, $img, 0, 0, 0, 0, $width, $height);
		}else{
			$e = new mb_exception("SaveLegend: unable to load image: " . $url);
		}
		//save image to same filename as before
		imagepng($image,$legend_filename);
		imagedestroy($img);
	}
	function loadpng ($imgurl) {
		$x = new connector($imgurl);
		try {
			$im = imagecreatefromstring($x->file);
		}catch(Exception $E){
			$e = new mb_exception("Can't read image from string: ".$E->getmessage);
		}
		if(!$im){
			$im = false;
			$e = new mb_exception("SaveLegend: unable to load image: ".$imgurl);
		}  
		imageinterlace($im, 0); 
		return $im;
		imagedestroy($im);
	}
}

?>
