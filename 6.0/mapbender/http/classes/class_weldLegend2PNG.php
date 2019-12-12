<?php
# $Id: class_weldLegend2PNG.php 1583 2007-08-06 07:55:22Z christoph $
# http://www.mapbender.org/index.php/class_weldLegend2PNG.php
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


require_once(dirname(__FILE__)."/class_stripRequest.php");
require_once(dirname(__FILE__)."/class_mb_exception.php");

/**
 * Class used to weld different images (coming from various maprequests) together.
 * These maprequests are supposed to be passed to the constructor as '___'-seperated urls.
 *
 * @version 1.0.0
 * @uses stripRequest
 * @uses mb_exception
 * @todo merge all weldXXX2PNg? different might extend a base class?
 */
class weldMaps2PNG{
    /**
     * Constructor, assumes you pass over two parameters: the first is a string with
     * different maprequests, each seperated by three underscores (___).
     *
     * Iteratively calls a method to generate temporary images which are welded together at once.
     * The generated image is saved to the filesystem under the name
     * passed over as second parameter to the constructor.
     *
     * @param <string> the maprequests seperated by three underscores
     */
	function weldMaps2PNG($urls, $filename){
		if(!$urls || $urls == ""){
			$e = new mb_exception("weldMaps2PNG: no maprequests delivered");
		}
		// getting the array of urls
		$url = explode("___", $urls);
		// make url parameters accessible
		$obj1 = new stripRequest($url[0]);
		$width = $obj1->get("width");
		$height = $obj1->get("height");
		// create output image
		$image = imagecreatetruecolor($width, $height);
		// assign a white background color
		$white = ImageColorAllocate($image,255,255,255);
		ImageFilledRectangle($image,0,0,$width,$height,$white);

		// iterate over each request
		for($i=0; $i<count($url); $i++){
			$obj = new stripRequest($url[$i]);
//            $url[$i] = $obj->setPNG();
			$url[$i] = $obj->encodeGET();
			// get temporary image for this URL
			$img = $this->loadpng($url[$i]);
			if($img != false){
				// copy temporary image over already assigned images
				imagecopy($image, $img, 0, 0, 0, 0, $width, $height);
				imagedestroy($img);
			}
			else{
				$e = new mb_exception("weldMaps2PNG: unable to copy image: " . $url[$i]);
			}
		}
		// output the image to the filesystem
		/**
		 * @todo different outputs?
		 */
		imagepng($image,$filename);
		imagedestroy($image);
	}

	/**
	 * Helper function to generate temporary images for each URL
	 *
	 * @param <string> a single URL which represents a maprequest
	 */
	function loadpng ($imgurl) {
		$obj = new stripRequest($imgurl);
		$f = $obj->get("format");
		/**
		 * react on format
		 * @todo create switch?
		 * @todo handle as reg-exp?
		 * @todo instanciate $im as false or null?
		 */
		if(mb_strtolower($f) == 'image/png' || mb_strtolower($f) == 'png'){
			$im = @ImageCreateFromPNG($imgurl);
		}
		if(mb_strtolower($f) == 'image/jpeg' || mb_strtolower($f) == 'jpeg'){
			$im = @ImageCreateFromJPEG($imgurl);
		}
		if(mb_strtolower($f) == 'image/gif' || mb_strtolower($f) == 'gif'){
			$im = @ImageCreateFromGIF($imgurl);
		}
		if(!$im){
			$im = false;
			$e = new mb_exception("weldMaps2PNG: unable to load image: ".$imgurl);
		}
		// return the temporary image
		return $im;
	}
} // eof class weldMaps2PNG

?>