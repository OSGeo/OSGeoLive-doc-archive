<html>
<head>
<?php

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

require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Test POST/SOAP Communication for CSW/WFS</title>
</head>
<?php
if(isset($_REQUEST["filter"]) && $_REQUEST["filter"] != "" && $_REQUEST["onlineresource"] != ''){
	$arURL = parse_url($_REQUEST["onlineresource"]);
	$host = $arURL["host"];
	$port = $arURL["port"]; 
	$doSOAP=false;
	if ($_REQUEST["soap"]=='true') { 
		$doSOAP=true;
	}
	if($port == ''){
		$port = 80;	
	}
	$path = $arURL["path"];
	$method = "POST";
	$data = stripslashes($_REQUEST["filter"]);
	$dataXMLObject = new SimpleXMLElement($data);
	$datanew = $dataXMLObject->asXML();
	$headers = array(
            "POST ".$path." HTTP/1.1",
            "Content-type: text/xml; charset=\"utf-8\"",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: ".strlen($datanew)
        ); 
	if ($doSOAP) {
		$soapHead = "<soapenv:Envelope ";
		$soapHead .= "xmlns:dc=\"http://purl.org/dc/elements/1.1/\" ";
               	$soapHead .= "xmlns:xi=\"http://www.w3.org/2001/XInclude\" ";
               	$soapHead .= "xmlns:dct=\"http://purl.org/dc/terms/\" ";
               	$soapHead .= "xmlns:ows=\"http://www.opengis.net/ows\" ";
               	$soapHead .= "xmlns:xlink=\"http://www.w3.org/1999/xlink\" ";
               	$soapHead .= "xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" ";
		$soapHead .= "xmlns:soapenv=\"http://www.w3.org/2003/05/soap-envelope\">\n";
		$soapHead .= "<soapenv:Header/>\n";
		$soapHead .= "<soapenv:Body>\n";
		$soapFoot = "</soapenv:Body>\n";
		$soapFoot .= "</soapenv:Envelope>\n";
		$data = $soapHead.$data.$soapFoot;
		$dataXMLObject = new SimpleXMLElement($data);
		$datanew = $dataXMLObject->asXML();
		$headers = array(
            		"POST ".$path." HTTP/1.1",
			"Content-type: application/soap+xml; charset=\"utf-8\"",
            		"Cache-Control: no-cache",
            		"Pragma: no-cache",
            		"SOAPAction: \"run\"",
            		"Content-length: ".strlen($datanew)
        	); 
	}
	//do curl connection and request 
	$out = getCURL($_REQUEST["onlineresource"],$datanew,$headers,$doSOAP);
}
//of class_connector
function getCURL($url,$data,$headers,$doSOAP){	
		$ch = curl_init ($url);
		$arURL = parse_url($url);
		$host = $arURL["host"];
		$port = $arURL["port"]; 
		if($port == ''){
			$port = 80;	
		}
		$path = $arURL["path"];
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
		//or with own headers
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		if(CONNECTION_PROXY != ""){
			curl_setopt($ch, CURLOPT_PROXY,CONNECTION_PROXY.":".CONNECTION_PORT);	
		}		
		if(CONNECTION_PASSWORD != ""){
			curl_setopt ($ch, CURLOPT_PROXYUSERPWD, CONNECTION_USER.':'.CONNECTION_PASSWORD);	
		}
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$useragent='GeoPortal Rheinland-Pfalz Testsystem'; 
		curl_setopt ($ch,CURLOPT_USERAGENT,$useragent);
		$file = curl_exec ($ch);
		curl_close ($ch);
		$e = new mb_exception("send_post_curl.php: url  " . $url);
		$e = new mb_exception("send_post_curl.php: send post  " . $data);
		$e = new mb_exception("send_post_curl.php: response  " . $file);
		return $file;			
	}
?>
<body>
<form action='send_post_curl.php' method='post'>
OnlineResource (Choose the right one out of the Capabilities - SOAP and POST may differ!):<br>
<input name='onlineresource' type='text' size='100' value='<?php echo $_REQUEST["onlineresource"]; ?>'>
<br>
Use SOAP <input type='checkbox' id='soap' name='soap' value='true'><br>
Filter:<br>
<textarea name='filter' cols='100' rows='15'><?php echo stripslashes($_REQUEST["filter"]); ?></textarea><br>
Filter which is posted (maybe SOAP):<br>
<textarea name='postfilter' cols='100' rows='15'><?php echo $datanew; ?></textarea><br>
<input type='submit' value='submit'><br>
HTTP Headers of sended Request (php array):<br>
<textarea name='headers' cols='100' rows='5'><?php print_r($headers); ?></textarea><br>
<br>
Response:<br>
<textarea name='response' cols='100' rows='30'><?php echo htmlentities($out); ?></textarea><br>
</form>
</body>
</html>
