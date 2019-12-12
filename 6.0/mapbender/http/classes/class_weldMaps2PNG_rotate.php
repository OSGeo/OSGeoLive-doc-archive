<?php
# $Id: class_weldMaps2PNG_rotate.php 2684 2008-07-22 07:26:19Z christoph $
# http://www.mapbender.org/index.php/class_weldMaps2PNG_rotate.php
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
* class_weldMaps2PNG_rotate
* @version 1.0.0
* get/post '___' separated maprequests
*
**/
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/class_stripRequest.php");
require_once(dirname(__FILE__)."/class_connector.php");
require_once(dirname(__FILE__)."/class_weldMaps2PNG.php");

class weldMaps2PNG_rotate extends weldMaps2PNG{

    function weldMaps2PNG_rotate($urls,$filename, $angle, $encode = true){
        if(!$urls || $urls == ""){
            $e = new mb_exception("weldMaps2PNG_rotate: no maprequests delivered");
        }
        $url = explode("___", $urls);
        $obj1 = new stripRequest($url[0]);
        $width = $obj1->get("width");
        $height = $obj1->get("height");
        	
        //calculate rotated dimensions
        $neededWidth = round(abs(sin(deg2rad($angle))*$height)+abs(cos(deg2rad($angle))*$width));
        $neededHeight = round(abs(sin(deg2rad($angle))*$width)+abs(cos(deg2rad($angle))*$height));
    
        //modify requests
        for($i=0; $i<count($url); $i++){
            $obj = new stripRequest($url[$i]);
            $obj->set("width", $neededWidth);
            $obj->set("height", $neededHeight);
                
            $map_extent = $obj->get("BBOX");
            $coord = explode(",",$map_extent);
            $coord = $this->enlargeExtent($coord, $width, $height, $neededWidth, $neededHeight);
            
            $obj->set("BBOX", implode(",", $coord));
            $url[$i] = $obj->url;
        }

        //get image
        $urls = implode("___", $url);
        $this->weldMaps2PNG($urls, $filename, $encode);
        

        //rotate image
        $imagick = new Imagick();
        $imagick->readImage($filename);
        $imagick->rotateImage(new ImagickPixel(), $angle);
        
        //get the new dimensions
        $imgWidth = $imagick->getImageWidth();
        $imgHeight = $imagick->getImageHeight();
            
        //crop empty areas
        $imagick->cropImage($width, $height, ($imgWidth-$width)/2, ($imgHeight-$height)/2);
        
        //write modified image
        $imagick->writeImage($filename);
        $image = imagecreatefrompng($filename);
        imagepng($image,$filename);
    }
 
    function enlargeExtent($coordArray, $oldWidth, $oldHeight, $newWidth, $newHeight){
         $extentx = ($coordArray[2] - $coordArray[0]);
         $extenty = ($coordArray[3] - $coordArray[1]);
         $coordArray[0]+=($extentx/$oldWidth)*($oldWidth-$newWidth)/2;
         $coordArray[2]+=($extentx/$oldWidth)*($newWidth-$oldWidth)/2;
         $coordArray[1]+=($extenty/$oldHeight)*($oldHeight-$newHeight)/2;
         $coordArray[3]+=($extenty/$oldHeight)*($newHeight-$oldHeight)/2;
        return $coordArray;
    }
    
}

?>
