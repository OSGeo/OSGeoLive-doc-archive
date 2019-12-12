<?php
# $Id: ExternalGraphic.php 2412 2008-04-23 16:14:30Z christoph $
# http://www.mapbender.org/index.php/SLD
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

/**
 * Implementation of the ExternalGraphic-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class ExternalGraphic
{
	/**
	 * The onlineresource element from the xml-scheme.
	 * @var string
	 */
	var $onlineresource = "";
	
	/**
	 * The format element from the xml-scheme.
	 * @var string
	 */
	var $format = "";
	
	/**
	 * Index identifying the object in the $_SESSION("sld_objects") array.
	 * @var int
	 */
	var $id = "";
	
	/**
	 * Index identifying the object's parent object in the $_SESSION("sld_objects") array.
	 * @var int
	 */
	var $parent = "";
	
	/**
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<ExternalGraphic>\n";
		$temp .= $offset." <OnlineResource xlink:href=\"".htmlspecialchars($this->onlineresource)."\" />\n";
		$temp .= $offset." <Format>".htmlspecialchars($this->format)."</Format>\n";
		$temp .= $offset."</ExternalGraphic>\n";
		return $temp;
	}
	
	/**
	 * creates the html-form-fragment for this object
	 *
	 * @param $id string containing a prefix that should be used to identify this
	 * object's html fields. This must be done, so that the generateObjectFromPost(...)
	 * function can address the fields belonging to this object in the http-post.
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the html-form-fragment
	 */
	function generateHtmlForm($id, $offset = "")
	{
		$temp = "";
		$temp .= $offset."<table style=\"border: 1px solid black;\" >\n";
		$temp .= $offset." <tr valign='top'>\n";
		$temp .= $offset."  <td>\n";
		$temp .= $offset."   ExternalGraphic<br>\n";
		
		$number = split("_", $id);
		$number = $number[count($number)-1];
		$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=deleteexternalgraphicormark&id=".$this->parent."&number=".$number."\">l&ouml;schen</a>\n";
		
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."\" value=\"externalgraphic\">\n";
		$temp .= $offset."  </td>\n";
		$temp .= $offset."  <td>\n";
		
		$temp .= $offset."   <table border=\"0\">\n";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      OnlineResource:\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      <input name=\"".$id."_onlineresource\" value=\"".htmlspecialchars($this->onlineresource)."\">\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."    </tr>\n";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      Format:\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      <input name=\"".$id."_format\" value=\"".htmlspecialchars($this->format)."\">\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."    </tr>\n";
		$temp .= $offset."   </table>\n";
		
		//TODO: Selectbox f�r Format

		$temp .= $offset."  </td>\n";
		$temp .= $offset." </tr>\n";
		$temp .= $offset."</table>\n";
		$temp .= $offset."  <br>\n";		
		return $temp;
	}
	
	/**
	 * populates the member fields of a new object from the data in the http-post-request
	 * to rebuild the object after the submission of the html-form.
	 *
	 * creates its own child objects from the post parameters and calls their
	 * generateObjectFromPost(...) function
	 *
	 * @param string $id string that contains a prefix for the html-form-fields
	 * that is common to all of the fields belonging to this object
	 */
	function generateObjectFromPost($id = "")
	{
		if (isset($_REQUEST[$id."_onlineresource"]))
		{
			$this->onlineresource = $_REQUEST[$id."_onlineresource"];
		}
		if (isset($_REQUEST[$id."_format"]))
		{
			$this->format = $_REQUEST[$id."_format"];
		}
	}
}
?>