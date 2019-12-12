<?php
# $Id: CssParameter.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the CssParameter-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class CssParameter
{
	/**
	 * The name attribute from the xml-scheme.
	 * @var string
	 */
	var $name = "";
	
	/**
	 * The value attribute from the xml-scheme.
	 * @var string
	 */
	var $value = "";
	
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
		$temp = $offset."<CssParameter ";
		$temp .= "name=\"".htmlspecialchars($this->name)."\">";
		$temp .= htmlspecialchars($this->value);
		$temp .= "</CssParameter>\n";
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
		$temp .= $offset."<table>\n";
		$temp .= $offset." <tr valign=\"top\">\n";
		$temp .= $offset."  <td style=\"width: 100px;\">\n";
		$temp .= $offset."   CssParameter<br>\n";
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."\" value=\"cssparameter\">\n";
		
		$number = split("_", $id);
		$number = $number[count($number)-1];
		
		$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=deletecssparameter&id=".$this->parent."&number=".$number."\">l&ouml;schen</a>\n";
		
		$temp .= $offset."  </td>\n";
		$temp .= $offset."  <td>\n";
		
		$temp .= $offset."   <table>\n";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td style=\"width: 100px;\">\n";
		$temp .= $offset."      ".htmlspecialchars($this->name)."\n";
		$temp .= $offset."      <input type=\"hidden\" name=\"".$id."_name\" value=\"".htmlspecialchars($this->name)."\">\n";
		$temp .= $offset."     </td>\n";
		
		//Farbauswahl
		if ($this->name == "fill" || $this->name == "stroke")
		{
			$temp .= $offset."     <td";
			$temp .= " id=\"".$id."_value_preview\" style=\"border: 1px solid black; width: 100px; cursor: hand; background-color:".htmlspecialchars($this->value).";\" ";
			$temp .= "onClick=\"window.open('sld_pick_color.php?id=".$id."_value','Farbauswahl','width=299, height=194, resizable=no');\">\n";
			$temp .= $offset."      <input type=\"hidden\" name=\"".$id."_value\" id=\"".$id."_value\" value=\"".htmlspecialchars($this->value)."\">\n";
			$temp .= $offset."     </td>\n";
		}
		else if ($this->name == "stroke-width" || $this->name == "font-size")
		{
			$temp .= $offset."     <td>\n";
			$temp .= $offset."      <select name=\"".$id."_value\" onchange=\"document.getElementById('sld_editor_form').submit();\">\n";
			for ($i=0; $i < 17; $i++)
			{
				$temp .= $offset."      <option value=\"".$i."\"";
				if ( $this->value == $i ) $temp .= " selected";
				$temp .= ">".$i." px</option>\n";
			}
			$temp .= $offset."      </select>\n";
			$temp .= $offset."     </td>\n";
		}
		else if ($this->name == "font-family")
		{
			$temp .= $offset."     <td>\n";
			$temp .= $offset."      <select name=\"".$id."_value\" onchange=\"document.getElementById('sld_editor_form').submit();\">\n";
			$temp .= $offset."       <option value=\"verdana\"";
			if ($this->value == "verdana") $temp .= " selected";
			$temp .= ">Verdana</option>\n";
			$temp .= $offset."       <option value=\"arial\"";
			if ($this->value == "arial") $temp .= " selected";
			$temp .= ">Arial</option>\n";
			$temp .= $offset."      </select>\n";
			$temp .= $offset."     </td>\n";
		}
		else
		{
			$temp .= $offset."     <td>\n";
			$temp .= $offset."      <input name=\"".$id."_value\" value=\"".htmlspecialchars($this->value)."\">\n";
			$temp .= $offset."     </td>\n";
		}
		$temp .= $offset."    </tr>\n";
		$temp .= $offset."   </table>\n";
		
		$temp .= $offset."  </td>\n";
		$temp .= $offset." </tr>\n";
		$temp .= $offset."</table>\n";
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
		if (isset($_REQUEST[$id."_name"]))
		{
			$this->name = $_REQUEST[$id."_name"];
		}
		if (isset($_REQUEST[$id."_value"]))
		{
			$this->value = $_REQUEST[$id."_value"];
		}
	}
}
?>