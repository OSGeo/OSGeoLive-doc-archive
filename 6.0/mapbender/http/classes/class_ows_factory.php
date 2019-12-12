<?php
# $Id: class_wfs.php 3094 2008-10-01 13:52:35Z christoph $
# http://www.mapbender.org/index.php/class_wfs.php
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
 * This abstract class is the superclass of all factories
 * creating OGC Web Services (OWS).
 */
abstract class OwsFactory {
	
	/**
	 * Creates an OWS from an XML, presumably a capabilities document.
	 * 
	 * @return Ows
	 * @param $xml String
	 */
	abstract public function createFromXml ($xml);

	/**
	 * Creates an OWS from the Mapbender database.
	 * 
	 * @return Ows
	 * @param $id Integer The index in the database
	 */
	abstract public function createFromDb ($id);
	
	/**
	 * Removes endlines and carriage returns from a string.
	 * 
	 * @return String
	 * @param $string String
	 */
	final protected function stripEndlineAndCarriageReturn ($string) {
		return preg_replace("/\n/", "", preg_replace("/\r/", " ", $string));
	}
	
	/**
	 * Creates a random id. The id contains non-numeric characters in 
	 * order to distinguish it from a database id.
	 * 
	 * @return String
	 */
	final protected function createId () {
		return "id_" . substr(md5(rand()),0,6);
	}
	
	/**
	 * Removes the namespace from a tag name.
	 * 
	 * Example: input is "topp:the_geom" will return "the_geom".
	 * 
	 * @return String
	 * @param $s String
	 */
	final protected function sepNameSpace($s) {
		$c = strpos($s, ":"); 
		if ($c > 0) {
			return substr($s, $c + 1);
		}
		return $s;
	}
	
	/**
	 * Get namespace from a tag name.
	 * 
	 * Example: input is "topp:the_geom" will return "topp".
	 * 
	 * @return String
	 * @param $s String
	 */
	final protected function getNameSpace($s) {
		$c = strpos($s, ":"); 
		if ($c > 0) {
			return substr($s, 0, $c);
		}
		return $s;
	}

	/**
	 * Retrieves a document from a URL, presumably a 
	 * capabilities document, via GET.
	 * 
	 * @return String
	 * @param $url String
	 */
	final protected function get ($url) {
		$x = new connector($url);
		$xml = $x->file;
		if(!$xml){
			throw new Exception("Unable to open document: " . $url);
			return null;
		}
		return $xml;		
	}

	/**
	 * Retrieves a document from a URL, presumably a 
	 * describe feature type document, via POST
	 * 
	 * @return String
	 * @param $url String
	 */
	final protected function post ($url, $postData) {
		$x = new connector();
		$x->set("httpType", "post");
		$x->set("httpPostData", $postData);
		$x->set("httpContentType", "XML");
		
		$xml = $x->load($url);
		if(!$xml){
			throw new Exception("Unable to open document: " . $url);
			return null;
		}
		return $xml;		
	}

	/**
	 * Creates an OWS from an XML, presumably a capabilities document, 
	 * which is retrieved from a URL.
	 * 
	 * @return Ows
	 * @param $url String
	 */
	final public function createFromUrl ($url) {
		try {
			$xml = $this->get($url);
	
			$myOws = $this->createFromXml($xml);
			if ($myOws != null) {
				$myOws->uploadUrl = $url;
				return $myOws;
			}		
			return null;
		}
		catch (Exception $e) {
			new mb_exception($e);
			return null;
		}
		return null;
	}
}
?>