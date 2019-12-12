<?php
# $Id: class_connector.php 8252 2012-02-15 09:38:37Z verenadiewald $
# http://www.mapbender.org/index.php/class_connector
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
/**
 * Establishes a connection to a given URL (and loads the content).
 * Supports HTTP (GET and POST), cURL and socket connections.
 * 
 * @class
 */
class connector {

	var $file;	
	private $connectionType;
	private $timeOut = 20;
	private $httpType = "get";
	private $httpVersion = "1.0";
	private $httpPostData;
	private $httpContentType;
	private $httpPostFieldsNumber;
	private $curlSendCustomHeaders = true; //decide to send own headers or not
	
		
	/**
	 * @constructor
	 * @param String url the URL that will be loaded (optional)
	 */
	public function __construct() {
		$this->set("connectionType", CONNECTION);
		if (func_num_args() == 1) {
			$url = func_get_arg(0);
			if ($url) {
				$this->load($url);
			}
		}
		else if (func_num_args() == 2) {
       		$auth = func_get_arg(1);
			$url = func_get_arg(0);
			if ($url) {
				$this->load($url,$auth);
			}
		}
	}
	
	/**
	 * Loads content from the given URL.
	 */
	public function load($url) {
		//some firewalls have problems to allow requests from a server behind it to the same server through 
		//an internet connection. It can be that some requests are done thru this class to the urls of 
		//HTTP_AUTH_PROXY or OWSPROXY. If some of those are part of the url they must be exchanged with 127.0.0.1 - 			//which hopefully should work. 
		//TODO: check if http is ok for all 
		$posPROXY = strpos($url,OWSPROXY);
		//$e = new mb_exception('class_connector: old url: '.$url);
 		if($posPROXY !== false && OWSPROXY_USE_LOCALHOST == true){
			$e = new mb_notice('class_connector: old url: '.$url);	
			$url = str_replace($_SERVER['HTTP_HOST'], "127.0.0.1", $url);
			$url = str_replace("https", "http", $url);//localhost no https should needed - it will be faster without
			$e = new mb_notice('class_connector: new url: '.$url);
		}

		$e = new mb_notice('class_connector: load url: '.$url);
		if (!$url) {
			$e = new mb_exception("connector: no URL given");
			return false;
		}
		switch ($this->connectionType) {
			case "curl":
			    if (func_num_args() == 2) {
            			$auth = func_get_arg(1);
						if (isset($auth)) {
							$e = new mb_notice("connector: curl auth");
							$this->file = $this->getCURL($url,$auth);
						}
				}
				else {
				$e = new mb_notice("connector: curl without auth");
					$this->file = $this->getCURL($url);
				}
				break;
			case "http":
			$e = new mb_notice("connector: http");
				$this->file = $this->getHTTP($url);
				break;
			case "socket":
			$e = new mb_notice("connector: socket");
				$this->file = $this->getSOCKET($url);
				break;
		}
		if(!$this->file){
			$e = new mb_exception("connector: unable to load: ".$url);
			return false;
		}
		return $this->file;		
	}
	
	/**
	 * Sets the environment variables. The following can be set:
	 * - connectionType ("http", "curl", "socket")
	 * - httpType ("get", "post")
	 * - etc.
	 */
	public function set ($key, $value) {
		switch ($key) {
			case "connectionType":
				if ($this->isValidConnectionType($value)) {
					$this->connectionType = $value;
				}
				break;
				
			case "httpVersion":
				if (in_array($value, array("1.0", "1.1"))) {
					$this->httpVersion = $value;
				}
				else {
					$e = new mb_exception("class_connector.php: invalid http type '" . $value . "'");
				}
				break;
							
			case "httpType":
				if (in_array(mb_strtoupper($value), array("POST", "GET"))) {
					$this->httpType = $value;
				}
				else {
					$e = new mb_exception("class_connector.php: invalid http type '" . $value . "'");
				}
				break;
			
			case "httpPostData":
				$this->httpPostData = $value;
				break;

			case "httpPostFieldsNumber":
				$this->httpPostFieldsNumber = $value;
				break;	
	
			case "curlSendCustomHeaders":
				$this->curlSendCustomHeaders = $value;
				break;
	
			case "timeOut":
				$this->timeOut = (integer)$value;
				break;	
	
			case "httpContentType":
				if ($this->isValidHttpContentType($value)) {
					$this->httpContentType = $value;
				}
				break;
		}
	}	
	
	private function isValidConnectionType ($value) {
		if (in_array(mb_strtoupper($value), array("HTTP", "CURL", "SOCKET"))) {
			return true;
		}
		else {
			$e = new mb_exception("class_connector.php: invalid connection type '" . $value . "'");
			return false;
		}
	}
	
	private function isValidHttpContentType ($value) {
		$validHttpContentTypeArray = array("XML","TEXT/XML","APPLICATION/XML","MULTIPART/FORM-DATA");
		if (in_array(mb_strtoupper($value), $validHttpContentTypeArray)) {
			switch (mb_strtoupper($value)) {
				case "XML":
					$this->httpContentType = "application/xml";
					break;
			}
			return true;
		}
		else {
			$e = new mb_exception("class_connector.php: invalid HTTP content type '" . $value . "'");
			return false;
		}
	}
	
	private function getCURL($url){
		$url=Str_replace(" ","+",$url); //to have no problems with image/png; mode=24bit!
		$url=str_replace(";","%3B",$url);
		if (func_num_args() == 2) {
			$auth = func_get_arg(1);
		} //auth should be an array of ['username', 'realm', 'password', 'auth_type'] - or false - problem would be, that these are stored without hashing them! 
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); //for images
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//dont store and load cookies from previous sessions
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		//allow https connections and handle certificates quite simply ;-)
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeOut);
		//$e = new mb_notice("connector: test1:");
		//get hostname/ip out of url
		//$host = parse_url($url,PHP_URL_HOST);
		$arURL = parse_url($url);
		$host = $arURL["host"];
		$port = $arURL["port"]; 
		if($port == ''){
			$port = 80;	
		}
		$path = $arURL["path"];
		
		// fill array (HOSTs not for Proxy)
		$e = new mb_notice("class_connector.php: NOT_PROXY_HOSTS:".NOT_PROXY_HOSTS);
 	    	$NOT_PROXY_HOSTS_array = explode(",", NOT_PROXY_HOSTS);
 	    
 	    	//check if http_proxy is set as env, if yes, unset it for the curl action here, it will be reset somewhere below - normally not needed, cause it will be only available when at execution time of the script http://php.net/manual/en/function.putenv.php
		if (getenv('http_proxy')) {
			$e = new mb_notice("class_connector.php: current http_proxy: ".getenv('http_proxy')." will be unset by putenv('http_proxy')");
			$tmpHttpProxy = getenv('http_proxy');
			putenv("http_proxy"); //this should unset the variable???
		} else {
			$e = new mb_notice("class_connector.php: http_proxy is not set as env variable!");
			$tmpHttpProxy = getenv('http_proxy');
		}
 	    	//$tmpHttpProxy = getenv('http_proxy')?getenv('http_proxy') : "";
 	    	//putenv("http_proxy");
		//check if proxy is set and server not in NOT_PROXY_HOSTS
 	    	if(CONNECTION_PROXY != "" AND (in_array($host, $NOT_PROXY_HOSTS_array)!= true)){
			curl_setopt($ch, CURLOPT_PROXY,CONNECTION_PROXY.":".CONNECTION_PORT);
			$e = new mb_notice("class_connector.php: Proxy will be used!");
			if(CONNECTION_PASSWORD != ""){
				curl_setopt ($ch, CURLOPT_PROXYUSERPWD, CONNECTION_USER.':'.CONNECTION_PASSWORD);	
			}
		} else {
			$e = new mb_notice("class_connector.php: Proxy will not be used!");
		}		
		//TODO maybe allow basic authentication for client, but this will store the passwords in plain text
		//TODO: store the  passwords as digest hash. Therefor we have to handle the realm which is defined in the 401 header and return it back to the scripts like mod_loadwms.php to store the digest into the database - problem: curl cannot handle digest connection without clear username and password - we have to send our own headers
		if(isset($auth) && $auth != false) {	
			curl_setopt($ch, CURLOPT_USERPWD, $auth['username'].':'.$auth['password']);
			if ($auth['auth_type'] == 'digest') {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);	
			}
			if ($auth['auth_type'] == 'basic') {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			}
		}
		
		
		
		//if httpType is POST, set CURLOPT_POST and CURLOPT_POSTFIELDS  
		//and set a usefull http header
		if(strtoupper($this->httpType) == 'POST'){
			$headers = array(
					"POST ".$path." HTTP/1.1",
            		"Content-type: ".$this->httpContentType."; charset=".CHARSET,
           			"Cache-Control: no-cache",
	           		"Pragma: no-cache",
	           		"Content-length: ".strlen($this->httpPostData)
			); 
			$e = new mb_notice("connector: CURL POST: ".$this->httpPostData); 
			$e = new mb_notice("connector: CURL POST length: ".strlen($this->httpPostData));
			
			if ($this->curlSendCustomHeaders) {			
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}
			if ($this->httpPostFieldsNumber != 1){
				curl_setopt($ch,CURLOPT_POST,$this->httpPostFieldsNumber);
			} else {
				curl_setopt($ch, CURLOPT_POST, 1);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->httpPostData);	
		}
		$useragent=CONNECTION_USERAGENT;
		//Build own headers for GET Requests - maybe needful?
		if(strtoupper($this->httpType) == 'GET'){
			$headers = array(
					"GET ".$path." HTTP/1.1",
					"User-Agent: ".$_SERVER['HTTP_USER_AGENT'],
       				"Host: ".$host.":".$port,
           		 	"Accept: */*",
					"Proxy-Connection: Keep-Alive"
			);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		#curl_setopt($ch, CURLOPT_HEADER, true);
		$e = new mb_notice("class_connector.php: CURL connect to: ".$url); 
		//curl_setopt ($ch,CURLOPT_USERAGENT,$useragent);
		curl_setopt($ch,CURLOPT_DNS_USE_GLOBAL_CACHE, false);
		curl_setopt($ch,CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, false); 
		//curl_setopt ($ch,HTTPPROXYTUNNEL, 1);
		//curl_setopt ($ch,CURLOPT_AUTOREFERER, 1);
		//curl_setopt ($ch,CURLOPT_VERBOSE, true);
		//$handle = fopen('/tmp/phpcurl_error.tmp', 'a'); //use this for debugging purposes
		//curl_setopt ($ch,CURLOPT_STDERR, $handle);
		$file = curl_exec ($ch);
		//handle http authentication
		$info = curl_getinfo($ch);
		/*$error_log = "";
		$error_log .= "http code: ".$info['http_code']."\n";
		$error_log .= "sent header: ".$info['request_header']."\n";
		$error_log .= "lookup time: ".$info['namelookup_time']."\n";
		$error_log .= "redirect_time: ".$info['redirect_time']."\n";
		$error_log .= "redirect_count: ".$info['redirect_count']."\n";*/
		if ($info['http_code'] == '401') {
			curl_close ($ch);
			return $info['http_code'];
		}
		if ($info['http_code'] == '502') {
			curl_close ($ch);
			$e = new mb_exception("class_connector.php: Problem with connecting Gateway - maybe problem with the configuration of the security proxy (mod_proxy?).");
			return $info['http_code'];
			/*fwrite($handle,"HEADER: \n");
			fwrite($handle,$error_log);
			fwrite($handle,"502: ".$file."\n");*/
		}
		curl_close ($ch);
		//fclose($handle);
		//reset the env variable http_proxy to the former value
		if ($tmpHttpProxy != '') {
			putenv("http_proxy=$tmpHttpProxy");
		}	
		$e = new mb_notice("class_connector.php: CURL give back: ".$file);
		return $file;	
	}

	private function getHTTP($url){
		if ($this->httpType == "get") {
			return @file_get_contents($url);
	 	}
		else {
			$errno = 0;
			$errstr = "";
			$urlComponentArray = parse_url($url);
			$scheme = $urlComponentArray["scheme"];
			$host = $urlComponentArray["host"];
			$port = $urlComponentArray["port"]; 
			if ($port == "") {
				if ($scheme == "https") {
					$port = 443;
				}
				else {
					$port = 80;		
				}
			}	
			$path = $urlComponentArray["path"];
			$query = $urlComponentArray["query"];
			$buf = '';
			if ($scheme == "https") {
				$fp = fsockopen("ssl://". $host, $port, $errno, $errstr);				
			}
			else {
			    $fp = fsockopen($host, $port);
			}
			$postStr = "";
			$postPath = "POST " . $path . "?" . $query . " HTTP/".$this->httpVersion . "\r\n";
			$postStr .= $postPath;
		    fputs($fp, $postPath);
			
			$postHost = "Host: " . $host . "\r\n";
			$postStr .= $postHost;
		    fputs($fp, $postHost);
			
		    if ($this->isValidHttpContentType($this->httpContentType)) {
				$postContentType = "Content-type: " . $this->httpContentType . "\r\n";
				$postStr .= $postContentType;
		    	fputs($fp, $postContentType);
		    }
			$postContentLength = "Content-length: " . strlen($this->httpPostData) . "\r\n";
			$postStr .= $postContentLength;
		    fputs($fp, $postContentLength);
			
			$postClose = "Connection: close\r\n\r\n";
			$postStr .= $postClose;
		    fputs($fp, $postClose);

		    $postStr .= $this->httpPostData;
			fputs($fp, $this->httpPostData);

			new mb_notice("connector.http.postData: ".$this->httpPostData);
			
		    $xmlstr = false;
		    //@TODO remove possibly infinite loop
			while (!feof($fp)) {
		    	$content = fgets($fp,4096);
//		    	if( strpos($content, '<?xml') === 0){
		    	if( strpos($content, '<') === 0){
		    		$xmlstr = true;
		    	}
		    	if($xmlstr == true){
		    		$buf .= $content;
		    	}
			}
		    fclose($fp);
//		    new mb_notice("connector.http.response: ".$buf);
		    return $buf;			
		}
	}

	private function getSOCKET($url){
		$r = "";
		$fp = fsockopen (CONNECTION_PROXY, CONNECTION_PORT, $errno, $errstr, 30);
		if (!$fp) {
			echo "$errstr ($errno)<br />\n";
		} 
		else {
			fputs ($fp, "GET ".$url." HTTP/1.0\r\n\r\n");
			while (!feof($fp)) {
				$r .= fgets($fp,4096);
			}
			fclose($fp);
			return $r;
		}
	}
}
?>
