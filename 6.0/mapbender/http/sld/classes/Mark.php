<?php
# $Id: Mark.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the Mark-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class Mark
{
	/**
	 * The wellknownname element from the xml-scheme.
	 * @var string
	 */
	var $wellknownname = "";
	
	/**
	 * The Fill object from the xml-scheme.
	 *
	 * @see Fill
	 * @var object
	 */
	var $fill = "";
	
	/**
	 * The Stroke object from the xml-scheme.
	 *
	 * @see Stroke
	 * @var object
	 */
	var $stroke = "";
	
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
		$temp = $offset."<Mark>\n";
		if ($this->wellknownname != "") $temp .= $offset." <WellKnownName>".htmlspecialchars($this->wellknownname)."</WellKnownName>\n";
		if ($this->fill != "") $temp .= $this->fill->generateXml($offset." ");
		if ($this->stroke != "") $temp .= $this->stroke->generateXml($offset." ");
		$temp .= $offset."</Mark>\n";
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
		$temp .= $offset."<table style=\"border: 1px solid black;\">\n";
		$temp .= $offset." <tr valign=\"top\">\n";
		$temp .= $offset."  <td style=\"width: 100px\">\n";
		$temp .= $offset."   Mark<br>\n";
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."\" value=\"mark\">\n";
		
		$number = split("_", $id);
		$number = $number[count($number)-1];
		$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=deleteexternalgraphicormark&id=".$this->parent."&number=".$number."\">l&ouml;schen</a>\n";
		
		
		$temp .= $offset."  </td>\n";
		$temp .= $offset."  <td>\n";
		$temp .= $offset."   WellKnownName:\n";
		$temp .= $offset."   <select name=\"".$id."_wellknownname\" onchange=\"document.getElementById('sld_editor_form').submit();\">\n";
		$temp .= $offset."    <option value=\"square\"";
		if ($this->wellknownname == "square") $temp .= " selected";
		$temp .= ">square</option>\n";
		$temp .= $offset."    <option value=\"circle\"";
		if ($this->wellknownname == "circle") $temp .= " selected";
		$temp .= ">circle</option>\n";
		$temp .= $offset."    <option value=\"triangle\"";
		if ($this->wellknownname == "triangle") $temp .= " selected";
		$temp .= ">triangle</option>\n";
		$temp .= $offset."    <option value=\"star\"";
		if ($this->wellknownname == "star") $temp .= " selected";
		$temp .= ">star</option>\n";
		$temp .= $offset."    <option value=\"cross\"";
		if ($this->wellknownname == "cross") $temp .= " selected";
		$temp .= ">cross</option>\n";
		$temp .= $offset."<option value=\"x\"";
		if ($this->wellknownname == "x") $temp .= " selected";
		$temp .= ">x</option>\n";
		$temp .= $offset."   </select>\n";
		$temp .= $offset."   <br>\n";
		//TODO - Zeilenumbruch
		if ($this->fill != "")
		{
			$temp .= $this->fill->generateHtmlForm($id."_fill", $offset."   ");
		}
		else
		{
			$temp .= $offset."<a class='edit' href=\"sld_function_handler.php?function=addfill&id=".$this->id."\">Fill hinzuf&uuml;gen</a><br>\n";
		}
		if ($this->stroke != "")
		{
			$temp .= $this->stroke->generateHtmlForm($id."_stroke", $offset."   ");
		}
		else
		{
			$temp .= $offset."<a class='edit' href=\"sld_function_handler.php?function=addstroke&id=".$this->id."\">Stroke hinzuf&uuml;gen</a><br>\n";
		}

		$temp .= $offset."</td>\n";
		$temp .= $offset."</tr>\n";
		$temp .= $offset."</table>\n";
		$temp .= $offset."<br>\n";		
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
		if (isset($_REQUEST[$id."_wellknownname"]))
		{
			$this->wellknownname = $_REQUEST[$id."_wellknownname"];
		}
		if (isset($_REQUEST[$id."_fill"]))
		{
			$this->fill = new Fill();
			$this->fill->generateObjectFromPost($id."_fill");
		}
		if (isset($_REQUEST[$id."_stroke"]))
		{
			$this->stroke = new Stroke();
			$this->stroke->generateObjectFromPost($id."_stroke");
		}
	}
}
?>