<?php
# $Id: class_weldMaps2Image.php 7085 2010-11-03 14:07:26Z apour $
# http://www.mapbender.org/
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

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/class_stripRequest.php");
require_once(dirname(__FILE__)."/class_connector.php");
 
 /*
  * Class generats Images (jpegs/pngs/geotiff) 
  * of Image-URL-Array
  * For geotiff export gdal is necessary
  * 
  */
 class weldMaps2Image{
 	
	var $urls = array();
	var $filename; 	
 	
 	function weldMaps2Image($urls, $array_file){
 		$this->urls = $urls;
  		$this->array_file = $array_file;
 	}
 	
 	
	function getImage($imageTyp_imp, $outputKind=''){
		
		$imageTyp="";
		$imageTyp=$imageTyp_imp;
		
		if($imageTyp=='jpg'){
			$imageTyp='jpeg';
		}
				
		if(!$this->urls || $this->urls == ""){
			$e = new mb_exception("weldMaps2Image: no maprequests delivered");
		}
		$obj1 = new stripRequest($this->urls[0]);
		$width = $obj1->get("width");
		$height = $obj1->get("height");
		$wms_srs = $obj1->get("srs");
		$wms_bbox = $obj1->get("bbox");
		$wms_format = $obj1->getFormat();
		
		$image = imagecreatetruecolor($width, $height	);
		$white = ImageColorAllocate($image,255,255,255); 
		ImageFilledRectangle($image,0,0,$width,$height,$white); 
	
		for($i=0; $i<count($this->urls); $i++){
			$obj = new stripRequest($this->urls[$i]);
			if($imageTyp=='geotiff'){
				$this->urls[$i] = $obj->setFormat($wms_format);
			} else {
				$this->urls[$i] = $obj->setFormat($imageTyp);	
			}	
			
			$this->urls[$i] = $obj->encodeGET();			
			$img = $this->loadImage($this->urls[$i]);				
			if($img != false){
				imagecopy($image, $img, 0, 0, 0, 0, $width, $height);
			}
			else{
				$e = new mb_exception("weldMaps2Image: unable to load image: " . $this->urls[$i]);
			}
		}		
		
		$filename = $this->array_file['dir']."/";
		$timestamp = time();			
		$filenameOnly =$this->array_file['filename'].md5($timestamp);
		
		if($imageTyp=='png'){
			
			$filenameOnly .= '.png';
			$filename .= $filenameOnly;

			imagepng($image, $filename);
			
			$this->downloadLink($filenameOnly);
			
		} else if($imageTyp=='jpeg'){
			
			$filenameOnly .= '.jpeg';
			$filename .= $filenameOnly;
			imagejpeg($image, $filename);
			
			$this->downloadLink($filenameOnly);
					
		} else if($imageTyp=='geotiff'){
			
			$filenameOnly .= '.'.$wms_format;
			$filename .= $filenameOnly;
			
			if ($wms_format=='png'){
				imagepng($image, $filename);	
			} else if ($wms_format=='jpeg'){
				imagejpeg($image, $filename);	
			} else {
				$e = new mb_exception("weldMaps2Image: unable to generate temp-Image for getiff: " . $filename);
			}
			
			// gdal_translate...
			$wms_bbox = str_replace(',', ' ', $wms_bbox);
			$filename_tif = str_replace($wms_format, 'tif', $filenameOnly);

			$tmp_dir = $this->array_file['dir']."/";
			
			$array_bbox = explode(" ", $wms_bbox);	
			$wms_bbox = $array_bbox[0]." ".$array_bbox[3]." ".$array_bbox[2]." ".$array_bbox[1];

			/*
			 * @security_patch exec done
			 * Added escapeshellcmd()
			 */

			$cmd = "gdal_translate -a_srs ".$wms_srs." -a_ullr ".$wms_bbox." ".$tmp_dir.$filenameOnly." ".$tmp_dir.$filename_tif;
			exec(escapeshellcmd($cmd));
			
			$this->downloadLink($filename_tif);
					
		}else {
			
		}
		
		
	}
	
	function loadImage ($imgurl) {

		$x = new connector($imgurl);		
		$im = @imagecreatefromstring($x->file);
		if(!$im){
			$im = false;
			$e = new mb_exception("weldMaps2Image: unable to load image: ".$imgurl);
		}  
		return $im;
		
	}
 	
 	function downloadLink($downloadFilename){
 		

		$dwDir  = $this->array_file['dir']."/";
		$dwFilename = $downloadFilename;
		
		
		if(!(bool)$dwFilename) {
			die("No filename given.");
		}
		/*
		 * @security_patch fdl done
		 * This allows filenames like ../../
		 */
		if(strpos($dwFilename,"..") !== false) {
			die("Illegal filename given.");
		}
		
		$img = $dwDir."/".$dwFilename; 
		
		if(!file_exists($img) || !is_readable($img)) {
			die("An error occured.");
			
		}
		
		$now_date = date("Ymd_His");
		
		switch(substr($dwFilename,-4)) {
			case ".png":
				$filename = "map_export__".$now_date.".png";
				header('Content-Type: image/png');
				break;
			case "jpeg":
				$filename = "map_export__".$now_date.".jpeg";
				header('Content-Type: image/jpeg');
				break;
			case ".tif";
				$filename = "map_export__".$now_date.".tif";
				header('Content-Type: image/tif');
				break;
			default:
				die("An error occured.");
		}

 		if(file_exists($img) && is_file($img)) {
				header("Content-Disposition: attachment; filename=\"".$filename."\"");

				readfile($img); 
		} else {
			    die('Error: The file '.$img.' does not exist!');
			}

 	}
 	
 }
 

?>
