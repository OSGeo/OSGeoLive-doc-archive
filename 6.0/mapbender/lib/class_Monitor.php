<?php
# $Id: class_layer_monitor.php 791 2007-08-10 10:36:04Z baudson $
# http://www.mapbender.org/index.php/
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

//require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
require_once(dirname(__FILE__)."/../http/classes/class_connector.php");
require_once(dirname(__FILE__)."/../http/classes/class_mb_exception.php");
require_once(dirname(__FILE__)."/../http/extensions/DifferenceEngine.php");
require_once(dirname(__FILE__) . "/../http/classes/class_administration.php");

class Monitor {
	/**
	 *  1 = reachable and in sync with db
	 *  0 = reachable and out of sync with db
	 * -1 = unreachable
	 * -2 = monitoring in progress
	 * 
	 */
	var $result = -1;

	/**
	 * 1  = the get map request DEFINITELY returns a valid map image
	 * 0  = the WMS doesn't support XML error format. Who knows if the image is really a map?
	 * -1 = the get map request doesn't return an image
	 */
	var $returnsImage;

	var $comment = "";
	var $updated = "0";
	var $supportsXMLException = false;
	
	var $timestamp;
	var $timestamp_cap_begin;
	var $timestamp_cap_end;
	var $capabilitiesURL;
	var $mapURL;
	
	var $remoteXML;
	var $localXML;
	var $capabilitiesDiff;
	
	var $tmpDir = null;
	
	function __construct($reportFile, $autoUpdate, $tmpDir) {
		$this->tmpDir = $tmpDir;
		
		$this->reportFile = $tmpDir.$reportFile;
		//$this->reportFile = $reportFile;
		$this->wmsId = $this->getTagOutOfXML($this->reportFile,'wms_id');
		$this->uploadId = $this->getTagOutOfXML($this->reportFile,'upload_id');
		$this->autoUpdate = $autoUpdate;
		$e=new mb_notice("Monitor Report File: ".$this->reportFile);
		$e=new mb_notice("WMS ID: ".$this->wmsId);
		$this->capabilitiesURL = urldecode($this->getTagOutOfXML($this->reportFile,'getcapurl'));//read out from xml
		$e=new mb_notice("GetCapURL: ".$this->capabilitiesURL);

		set_time_limit(TIME_LIMIT);
		
		$this->timestamp = microtime(TRUE);
		//get authentication info for service
		$admin = new administration();
		$auth = $admin->getAuthInfoOfWMS($this->wmsId);
		if ($auth['auth_type']==''){
			unset($auth);
		}
		if ($this->capabilitiesURL) {
		
			//$remoteWms = new wms(); #exchange by other handling
			

			$this->timestamp_cap_begin=microtime(TRUE);//ok
			

			//$remoteWms->createObjFromXML($this->capabilitiesURL);#exchange by other handling
			

			//$this->remoteXML = $remoteWms->wms_getcapabilities_doc;
			if (isset($auth)) {
				$capObject = new connector($this->capabilitiesURL,$auth);
			} else {
				$capObject = new connector($this->capabilitiesURL);
			}
			//decode and encode to have the same behavior as loading caps to database
			$this->remoteXML = $capObject->file;

			$this->timestamp_cap_end=microtime(TRUE);
			//read local copy out of xml
			$this->localXML = urldecode($this->getTagOutOfXML($this->reportFile,'getcapdoclocal'));
//			$e=new mb_notice("Remote Caps: ".$this->remoteXML);
			// service unreachable
			if (!$this->remoteXML) {
				$this->result = -1;
				$this->comment = "Connection failed.";
				//$e=new mb_exception("Connection failed");
			}
			/*
			 * service available;
			 * no local copy of capabilities file,
			 * so it has to be updated anyway
			 */
			elseif (!$this->localXML) {
				$this->result = 0;
				//$e=new mb_exception("No local Copy of Caps available");
			}
			/*
			 * service available;
			 * check if local copy is different
			 * to remote capabilties document
			 */
			else {
				/*
				 * compare to local capabilities document
				 */
				// capabilities files match
				if ($this->localXML == $this->remoteXML) {
					$this->result = 1;
					$this->comment = "WMS is stable.";
					//$e=new mb_exception("Compare ok - Docs ident");
				
				}
				// capabilities files don't match
				else {
					$this->result = 0;
					$this->comment = "WMS is not up to date.";
					$localXMLArray = explode("\n", htmlentities($this->localXML));
					$remoteXMLArray = explode("\n", htmlentities($this->remoteXML));
					$this->capabilitiesDiff = $this->outputDiffHtml($localXMLArray,$remoteXMLArray);
					//$e=new mb_exception("Problem Docs are out of sync");
				}
			}
			/*
			 * if the WMS is available,
			 * 1) get a map image
			 * 2) update the local backup of the capabilities doc if necessary
			 */
			if ($this->result != -1) {
				
				$this->mapURL = urldecode($this->getTagOutOfXML($this->reportFile,'getmapurl'));
				//$e=new mb_exception("mapurl:".$this->mapURL);
			if (isset($auth)) {
				if ($this->isImage($this->mapURL,$auth)) {
					$this->returnsImage = 1;
				}
				else {
					$this->returnsImage = -1;
				}
				
			} else {
				if ($this->isImage($this->mapURL)) {
					$this->returnsImage = 1;
					//$e=new mb_exception("Returns image");
				}
				else {
					$this->returnsImage = -1;
					//$e=new mb_exception("Returns no image!");
				}
			}



				//Check for valid XML - validate it again wms 1.1.1 -some problems occur?
				#$dtd = "../schemas/capabilities_1_1_1.dtd";
				#$dom = new domDocument;
				#$dom->loadXML($this->remoteXML);
				#if (!$dom->validate($dtd)) {
					#$this->result = -1;
					#$this->comment = "Invalid getCapabilities request/document or service exception.";
				#}
					#else {
					#$this->comment = "WMS is not up to date but valid!";
				#}

				//Do a simple check if <WMT_MS_Capabilities version="1.1 is part of the remote Cap Dokument
				
				$searchString  = 'WMT_MS_Capabilities';
				$pos = strpos($this->remoteXML, $searchString);
				if ($pos === false) {
    					$this->result = -1;
					$this->comment = "Invalid getCapabilities request/document or service exception.";
				}


				/*
				 * if the local backup of the capabilities document
				 * is deprecated, update the local backup
				 */
				#if ($this->result == 0) {
					//$mywms = new wms();
		
					/* 
					 * if the capabilities document is valid,
					 * update it OR mark it as "not up to date"
					 */ 
					#if ($this->localXML==) {//check validation of capabilities document
						#if ($this->autoUpdate) {
							#$mywms->updateObjInDB($this->wmsId);
							#$this->updated = "1";
							#$this->comment = "WMS has been updated.";
							
						#}
						#else {
						#	$this->comment = "WMS is not up to date.";
						#}
					#}
					// capabilities document is invalid
					#else {
					#	$this->result = -1;
					#	$this->comment = "Invalid getCapabilities request/document or service exception.";
					#}    
				#}
			}
		}
		else {
			$this->result = -1;
			$this->comment = "Invalid upload URL.";
		}
		#$e = new mb_notice("class_monitor: constructor: result = " . $this->result);
		#$e = new mb_notice("class_monitor: constructor: comment = " . $this->comment);
		#$e = new mb_notice("class_monitor: constructor: returnsImage = " . $this->returnsImage);
	}
	
	/**
	 * 
	 */
	#function toString() {
		#$str = "";
		#$str .= "wmsid: " . $this->wmsId . "\nupload_id: " . $this->uploadId . "\n";
		#$str .= "autoupdate: " . $this->autoUpdate . "\n";
		#$str .= "result: " . $this->result . "\ncomment: " . $this->comment . "\n";
		#$str .= "timestamp: " . $this->timestamp . " (".date("F j, Y, G:i:s", $this->timestamp).")\n";
		#$str .= "getCapabilities URL: " . $this->capabilitiesURL . "\nupdated: " . $this->updated . "\n\n";
		#$str .= "getMap URL: " . $this->mapURL . "\nis image: " . $this->returnsImage . "\n\n";
		#$str .= "-------------------------------------------------------------------\n";
		#$str .= "remote XML:\n\n" . $this->remoteXML . "\n\n";
		#$str .= "-------------------------------------------------------------------\n";
		#$str .= "local XML:\n\n" . $this->localXML . "\n\n";
		#$str .= "-------------------------------------------------------------------\n";
		#return (string) $str;
	#}

	/**
	 * Update database
	 */
	function updateInDB() {
		$sql = "UPDATE mb_monitor SET updated = $1, status = $2, image = $3, status_comment = $4, upload_url = $5, timestamp_end = $6, map_url = $7 , timestamp_begin = $10 WHERE upload_id = $8 AND fkey_wms_id=$9";
		$v = array($this->updated, $this->result, $this->returnsImage, $this->comment, $this->capabilitiesURL, $this->timestamp_cap_end, $this->mapURL, $this->uploadId, $this->wmsId, $this->timestamp_cap_begin);
		$t = array('s', 'i', 'i', 's', 's', 's', 's', 's', 'i','s');
		$res = db_prep_query($sql,$v,$t);		
	}
	/**
	 * Update xml
	 */
	function updateInXMLReport() {
		//create text for diff
		$difftext = "<html>\n";
		$difftext .= "<head>\n";
		$difftext .= "<title>Mapbender - monitor diff results</title>\n";
		$difftext .= "<meta http-equiv=\"cache-control\" content=\"no-cache\">\n";
		$difftext .= "<meta http-equiv=\"pragma\" content=\"no-cache\">\n";
		$difftext .= "<meta http-equiv=\"expires\" content=\"0\">\n";
		$difftext .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset='.CHARSET.'\">";	
		$difftext .= "<style type=\"text/css\">\n";
		$difftext .= "* {font-family: Arial, \"Courier New\", monospace; font-size: small;}\n";
		$difftext .= ".diff-context {background: #eee;}\n";
		$difftext .= ".diff-deletedline {background: #eaa;}\n";
		$difftext .= ".diff-addedline {background: #aea;}\n";
		$difftext .= ".diff-blockheader {background: #ccc;}\n";
		$difftext .= "</style>\n";
		$difftext .= "</head>\n";
		$difftext .= "<body>\n";
		$difftext .= "<table cellpadding=3 cellspacing=0 border=0>\n";
		$difftext .= "<tr><td align='center' colspan='2'>Local</td><td align='center' colspan='2'>Remote</td></tr>\n";		
		$difftext .= $this->capabilitiesDiff;
		$difftext .= "\n\t</table>\n\t";
		$difftext .= "</body></html>";
		//write to report
		$xml=simplexml_load_file($this->reportFile);	
		$xml->wms->image=$this->returnsImage;
		$xml->wms->status=$this->result;
		$xml->wms->getcapduration = intval(($this->timestamp_cap_end-$this->timestamp_cap_begin)*1000);
		$xml->wms->getcapdocremote = rawurlencode($this->remoteXML);
		$xml->wms->getcapdiff = rawurlencode($difftext);
		$xml->wms->comment=$this->comment;
		$xml->wms->getcapbegin=$this->timestamp_cap_begin;
		$xml->wms->getcapend=$this->timestamp_cap_end;
		$xml->asXML($this->reportFile);
	}
	/*
	 * Checks if the mapUrl returns an image or an exception
	 */
	function isImage($url) {
		#$headers = get_headers($url, 1);#controll this function TODO
		//$e = new mb_notice("class_monitor: isImage: map URL is " . $url);
		#$e = new mb_notice("class_monitor: isImage: Content-Type is " . $headers["Content-Type"]);
		#if (preg_match("/xml/", $headers["Content-Type"])) {
		#	return false;
		#}


		if (func_num_args() == 2) { //new for HTTP Authentication
        	    	$auth = func_get_arg(1);
			$imgObject = new connector($url, $auth);
		}
		else {
			$imgObject = new connector($url);
		}
		$image = $imgObject->file;
		//write images to tmp folder
		$imageName=$this->tmpDir."/"."monitor_getmap_image_".md5(uniqid()).".png";
		$fileMapImg = fopen($imageName, 'w+');
		$bytesWritten = fwrite($fileMapImg, $image);
		fclose($fileMapImg);

		//$e = new mb_notice("class_monitor: isImage: path: ".$imageName);
		//$e = new mb_notice("class_monitor: isImage: Content-Type is " . mime_content_type($image));
		//$e = new mb_notice("class_monitor: isImage: Content-Type (file) is " . mime_content_type($imageName));
		if (mime_content_type($imageName)=="image/png") {
			return true;
		}
		return false;
	}

	/**
	 * Returns the objects out of the xml file
 	 */
       private function getTagOutOfXML($reportFile,$tagName) {
		#$e = new mb_notice("class_monitor: getUploadURL: wms = " . $wmsId);
		#$e = new mb_notice("class_monitor: getUploadURL: upload_id = " . $upload_id);
		#$sql = "SELECT upload_url FROM mb_monitor WHERE fkey_wms_id = $1 AND upload_id = $2";
		#$v = array($wmsId, $upload_id);
		#$t = array('i', 'i');
		#$res = db_prep_query($sql,$v,$t);
		#$someArray = db_fetch_array($res);
		#$e = new mb_notice("class_monitor: getUploadURL: url = " . $someArray["upload_url"]);
		#$xmlobj = new DOMDocument('1.0');
  		#$xmlobj->load($reportFile);
		$xml=simplexml_load_file($reportFile);
		$result=(string)$xml->wms->$tagName;
		return $result;
	}
	/*
	* creates a html diff of the xml documents
	*/
	private function outputDiffHtml($localXMLArray,$remoteXMLArray) {
		$diffObj = new Diff($localXMLArray,$remoteXMLArray);
		$dft = new TableDiffFormatter();
		return $dft->format($diffObj);
	}		
	//TODO get this things out of administration - but without database connection	
	function is_utf8_string($string) {
		return preg_match('%(?:
		[\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
		|\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
		|\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
		|\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
		|[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
		|\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
		)+%xs', $string);
	}
//function to get documents with authentication
	function getDocumentContent($url){
		if (func_num_args() == 2) { //new for HTTP Authentication
        	    	$auth = func_get_arg(1);
			$d = new connector($url, $auth);
		}
		else {
			$d = new connector($url);
		}
		return $d->file;
	}

	function is_utf8_xml($xml) {
		return preg_match('/<\?xml[^>]+encoding="utf-8"[^>]*\?>/is', $xml);
	}
	
	function is_utf8 ($data) {
		return ($this->is_utf8_xml($data) || $this->is_utf8_string($data));
	}
	
	function char_encode($data) {
		if (CHARSET == "UTF-8") {
			if (!$this->is_utf8($data)) {
				$e = new mb_notice("Conversion: ISO-8859-1 to UTF-8");
				return utf8_encode($data);
			}
		}
		else {
			if ($this->is_utf8($data)) {
				$e = new mb_notice("Conversion: UTF-8 to ISO-8859-1");
				return utf8_decode($data);
			}
		}
		$e = new mb_notice("No conversion: is " . CHARSET);
		return $data;
	}

	function char_decode($data) {
		if (CHARSET == "UTF-8") {
			if ($this->is_utf8($data)) {
				$e = new mb_notice("Conversion: UTF-8 to ISO-8859-1");
				return utf8_decode($data);
			}
		}
		$e = new mb_notice("no conversion: is " . CHARSET);
		return $data;
	}
}
