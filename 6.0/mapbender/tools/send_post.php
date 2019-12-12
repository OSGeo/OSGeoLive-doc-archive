<html>
<head>
<?php
# $Id: send_post.php 1601 2007-08-06 08:30:31Z christoph $
# http://www.mapbender.org/index.php/send_post.php
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

require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Test WFS-T operated by CCGIS</title>
</head>
<?php
if(isset($_REQUEST["filter"]) && $_REQUEST["filter"] != "" && $_REQUEST["onlineresource"] != ''){
	$arURL = parse_url($_REQUEST["onlineresource"]);
	$host = $arURL["host"];
	$port = $arURL["port"]; 
	if($port == ''){
		$port = 80;	
	}
	$path = $arURL["path"];
	$method = "POST";

	$data = stripslashes($_REQUEST["filter"]);

	$out = sendToHost($host,$port,$method,html_entity_decode($path),$data);
	echo "-------------------get-------------<br>";
	echo htmlentities($out);
	echo "-------------------end of get-------------<br>";
}
function sendToHost($host,$port,$method,$path,$data)
{
	echo "-------------------send-------------<br>";
	echo $host."<br>".$method."<br>".$path."<br>".htmlspecialchars($data)."<br>";
	echo "-------------------end of send-------------<br>";
	$buf = '';
    if (empty($method)) {
        $method = 'POST';
    }
    $method = mb_strtoupper($method);
    $fp = fsockopen($host, $port);
    fputs($fp, "$method $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
    fputs($fp,"Content-type: application/x-www-form-urlencoded\r\n");
    fputs($fp, "Content-length: " . strlen($data) . "\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
	fputs($fp, $data);
    while (!feof($fp)) {
        $buf .= fgets($fp,4096);
    }
    fclose($fp);
    return $buf;
}
?>
<body>
<form action='wfs_post.php' method='post'>
OnlineResource:
<input name='onlineresource' type='text' size='100' value='<?php echo $_REQUEST["onlineresource"]; ?>'>
<br>
Filter:
<textarea name='filter' cols='100' rows='10'><?php echo stripslashes($_REQUEST["filter"]); ?></textarea>
<input type='submit' value='ok'>
</form>
</body>
</html>