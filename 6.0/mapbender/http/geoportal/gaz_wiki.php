<?php
# $Id: gaz_wiki.php 468 2006-11-15 15:54:05Z rothstein $
# http://www.mapbender.org/index.php/gaz_wiki.php
# Copyright (C) 2002 Melchior Moos 
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
//disable warnings
ini_set('display_errors', 0);
require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
require_once(dirname(__FILE__)."/../../conf/geoportal.conf");
require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");
//Vars
$url = WIKI_URL;
$term = "";
(isset($_SERVER["argv"][1]))? ($term = $_SERVER["argv"][1]) : ($e = new mb_exception("wiki_search: searchstring lacks!"));
if (($term === 'false') || ($term === '*')) {
	$term = 'e'; //use wiki search wildcard
}


$maxresults = WIKI_MAX_RESULTS;
#http://www.gdi-rp-dienste3.rlp.de/mediawiki/api.php?action=query&list=allpages&apfrom=inspire&aplimit=500
#http://www.gdi-rp-dienste3.rlp.de/mediawiki/api.php?action=query&list=search&srsearch=inspire&srwhat=text
//get url for links
$surl = "mediawiki/index.php/";
//search mediawiki from localhost -> this will be much faster than over www
$searchurl = $url;
//$searchurl.="Special:Search?search=".urlencode($term)."&fulltext=Search&limit=".$maxresults."&offset=0";//OLD Version - now new mediwiki API is used
$searchurl.="/api.php?action=query&list=search&srsearch=".urlencode($term)."&srwhat=text&format=xml";
$e = new mb_notice("gaz_wiki: url to load: ".$searchurl);
//load result by connector:
$wikiConnectorObject = new connector($searchurl);
$e = new mb_notice("gaz_wiki: read from connector".$wikiConnectorObject->file);
//get results
$wikiXmlString = $wikiConnectorObject->file;
//load as xml object
$wikiXmlObject = new SimpleXMLElement($wikiXmlString);
$e = new mb_notice("gaz_wiki: xml after parsing with php simplexml: ".$wikiXmlObject->asXML());
//get the list of results 
$lists = $wikiXmlObject->xpath('/api/query/search/p/@title');
$e = new mb_notice("gaz_wiki: number of results: ".count($lists));
//debug
//while(list( , $node) = each($lists)) {
//	$e = new mb_exception('/api/query/search/p/@title: '.$node);
//}
//create output xml
$xml = new DOMDocument('1.0');
$xml->encoding = CHARSET;
$resultnode = $xml->createElement("result");
$xml->appendChild($resultnode);

if(count($lists)==0) {//no results returned
	$ready = $xml->createElement('ready');
	$resultnode->appendChild($ready);
	$tready = $xml->createTextNode("true");
	$ready->appendChild($tready);
	echo $xml->saveXML();
} else {
	//parse title matches 
	while(list( , $node) = each($lists)) {
     		$e = new mb_notice('/api/query/search/p/@title: '.$node);
		$link = $surl.$node;
		$title = $node;
		$m = $xml->createElement('member');
		$resultnode->appendChild($m);	
		//create title
		$ntitle = $xml->createElement('title');
		$m->appendChild($ntitle);
		$ttitle = $xml->createTextNode($title);
		$ntitle->appendChild($ttitle);
		//abstract
		$abst = $xml->createElement('abstract');
		$m->appendChild($abst);  
		$tabst = $xml->createTextNode("");
		$abst->appendChild($tabst);
		//url
		$nurl = $xml->createElement('url');
		$m->appendChild($nurl);  
		$turl = $xml->createTextNode($link);
		$nurl->appendChild($turl);
	}
	$ready = $xml->createElement('ready');
	$resultnode->appendChild($ready);
	$tready = $xml->createTextNode("true");
	$ready->appendChild($tready);
	echo $xml->saveXML();	
}

?>
