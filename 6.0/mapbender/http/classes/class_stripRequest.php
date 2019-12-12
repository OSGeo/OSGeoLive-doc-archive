<?php
# $Id: class_stripRequest.php 5810 2010-03-19 13:36:31Z christoph $
# http://www.mapbender.org/index.php/class_stripRequest.php
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

require_once(dirname(__FILE__)."/class_mb_exception.php");


class stripRequest{
	var $url;
	var $encodeParams = array("LAYERS", "QUERY_LAYERS");
	function stripRequest($mr){
		if(!$mr || $mr == ""){
			$t = new mb_exception("stripRequest: maprequest lacks ");
			die;
		}
		$this->url = $mr;
	}
	
	function set($key,$value){
		$exists = false;
		$a = explode("?",$this->url);
		$patterns = explode("&", $a[1]);
		for($i=0; $i<count($patterns); $i++){
			$tmp = explode("=", $patterns[$i]);
			if(mb_strtoupper($tmp[0]) == mb_strtoupper($key)){
				$replacement = mb_strtoupper($key) . "=" . $value;
				$currentPattern = $patterns[$i];
				$this->url = str_replace($currentPattern, $replacement, $this->url);
				$exists = true;
			}
		}
		if($exists == false){
			$e = new mb_exception("stripRequest: key '".$key."' lacks");
			return false;
		}
		else{
			return $this->url;
		}
	}
	
	function get($key){
		$exists = false;
		$a = explode("?",$this->url);
		$patterns = explode("&", $a[1]);
		for($i=0; $i<count($patterns); $i++){
			$tmp = explode("=", $patterns[$i]);
			if(mb_strtoupper($tmp[0]) == mb_strtoupper($key)){
				$exists = true;
				return $tmp[1];
			}
		}
		if($exists == false){
			$e = new mb_exception("stripRequest: key '".$key."' lacks");
			return false;
		}
	}
	
	function setPNG(){
		$version = $this->get("version");
		if($version == "1.0.0"){
			$output = $this->set("format","PNG");
			return $output;
		}
		else{
			$output = $this->set("format","image/png");
			return $output;
		}
	}
	
	function getFormat(){
		$format = $this->get("format");
			
		if ($format=="PNG" || $format=="image/png"){
			return "png";
		} else if ($format=="JPEG" || $format=="image/jpeg"){
			return "jpeg";
		} else {
			return null;
		}

	}
	
	function setFormat($formatType){
		$version = $this->get("version");
		if($version == "1.0.0"){
			if ($formatType=='png'){
				$output = $this->set("format","PNG");
				return $output;	
			} else if ($formatType=='jpeg'){
				$output = $this->set("format","JPEG");
				return $output;	
			} else {
				return;
			}
			
		}
		else{						
			if ($formatType=='png'){
				$output = $this->set("format","image/png");
				return $output;	
			} else if ($formatType=='jpeg'){
				$output = $this->set("format","image/jpeg");
				return $output;	
			} else {
				return;
			}
			
		}
	}
	
	function append($param){
		$this->url .= "&".$param;
		$this->encodeGET();
		return $this->url;
	}
	function remove($key){
		$a = explode("?",$this->url);
		$patterns = explode("&", $a[1]);
		for($i=0; $i<count($patterns); $i++){
			$tmp = explode("=", $patterns[$i]);
			if(mb_strtoupper($tmp[0]) == mb_strtoupper($key)){
				$replacement = "";
				$currentPattern = "/" . $patterns[$i] . "/";
				$this->url = preg_replace($currentPattern, $replacement, $this->url);
			}
		}		
		$this->encodeGET();
		return $this->url;
	}
	function encodeGET($encode = true){
		$a = explode("?",$this->url);
		$patterns = explode("&", $a[1]);
		$a[0].= "?";
		for($i=0; $i<count($patterns); $i++){
			$tmp = explode("=", $patterns[$i]);
			if(in_array(mb_strtoupper($tmp[0]),$this->encodeParams)){
				$val = explode(",", $tmp[1]);
				$a[0] .= $tmp[0]."=";
				for($ii=0; $ii<count($val); $ii++){
					if($ii>0){$a[0].=",";}
					if($encode){
						$a[0].= urlencode($val[$ii]);
					}else{
						$a[0].= $val[$ii];
					}
				}
				if ($i < (count($patterns)-1)) {
					$a[0] .= "&";
				}
			}
			else{
				$a[0] .= $tmp[0] . "=" .$tmp[1];
				if ($i < (count($patterns)-1)) {
					$a[0] .= "&";
				}
			}
		} 
		$this->url = $a[0];
		return $this->url;
	}
	
	function encodeLegGET(){
		$this->url = preg_replace("/&amp;/", "\&", $this->url); 
		return $this->url;
	}
}
?>