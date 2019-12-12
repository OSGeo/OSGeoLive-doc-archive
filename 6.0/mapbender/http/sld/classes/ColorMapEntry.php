<?php
# $Id: ColorMapEntry.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the ColorMapEntry-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class ColorMapEntry
{
	/**
	 * The color attribute from the xml-scheme.
	 * @var string
	 */
	var $color = "";
	
	/**
	 * The opacity attribute from the xml-scheme.
	 * @var string
	 */
	var $opacity = "";
	
	/**
	 * The quantity attribute from the xml-scheme.
	 * @var int
	 */
	var $quantity = "";
	
	/**
	 * The label attribute from the xml-scheme.
	 * @var string
	 */
	var $label = "";
	
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
		$temp = $offset."<ColorMapEntry";
		if ($this->color != "") $temp.= " color=\"".htmlspecialchars($this->color)."\"";
		if ($this->opacity != "") $temp.= " opacity=\"".htmlspecialchars($this->opacity)."\"";
		if ($this->quantity != "") $temp.= " quantity=\"".htmlspecialchars($this->quantity)."\"";
		if ($this->label != "") $temp.= " label=\"".htmlspecialchars($this->label)."\"";
		$temp .= " />\n";
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
		$temp .= $offset."   ColormapEntry<br>\n";
		
		$number = split("_", $id);
		$number = $number[count($number)-1];
		
		$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=deletecolormapentry&id=".$this->parent."&number=".$number."\">l&ouml;schen</a>\n";
		
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."\" value=\"colormapentry\">\n";
		$temp .= $offset."  </td>\n";
		$temp .= $offset." <td>\n";
		
		
		$temp .= $offset."   <table>\n";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td style=\"width: 100px\">\n";
		$temp .= $offset."      Color:\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."     <td";
		$temp .= " id=\"".$id."_color_preview\" style=\"border: 1px solid black; width: 100px; cursor: hand; background-color:".htmlspecialchars($this->color).";\" ";
		$temp .= "onClick=\"window.open('sld_pick_color.php?id=".$id."_color','Farbauswahl','width=299, height=194, resizable=no');\">\n";
		$temp .= $offset."      <input type=\"hidden\" name=\"".$id."_color\" value=\"".htmlspecialchars($this->color)."\">\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."    </tr>\n";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      Opacity:\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      <input name=\"".$id."_opacity\" value=\"".htmlspecialchars($this->opacity)."\">\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."    </tr>\n";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      Quantity:\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      <input name=\"".$id."_quantity\" value=\"".htmlspecialchars($this->quantity)."\">\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."    </tr>\n";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      Label:\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      <input name=\"".$id."_label\" value=\"".htmlspecialchars($this->label)."\">\n";
		$temp .= $offset."     </td>\n";
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
		if (isset($_REQUEST[$id."_color"]))
		{
			$this->color = $_REQUEST[$id."_color"];
		}
		if (isset($_REQUEST[$id."_opacity"]))
		{
			$this->opacity = $_REQUEST[$id."_opacity"];
		}
		if (isset($_REQUEST[$id."_quantity"]))
		{
			$this->quantity = $_REQUEST[$id."_quantity"];
		}
		if (isset($_REQUEST[$id."_label"]))
		{
			$this->label = $_REQUEST[$id."_label"];
		}
	}
}
?>