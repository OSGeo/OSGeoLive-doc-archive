<?php
# $Id: RasterSymbolizer.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the RasterSymbolizer-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class RasterSymbolizer extends Rule
{
	/**
	 * The Opacity element from the xml-scheme.
	 * This element is not implemented as an object.
	 *
	 * @var string
	 */
	var $opacity = "";
	
	/**
	 * The ColorMap object from the xml-scheme.
	 *
	 * @see ColorMap
	 * @var object
	 */
	var $colormap = "";
	
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
	
	
	function RasterSymbolizer()
	{
		//$this->colormap = new ColorMap();
	}
	
	/**
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<RasterSymbolizer>\n";
		if ($this->opacity != "") $temp .= $offset." <Opacity>".htmlspecialchars($this->opacity)."</Opacity>\n";
		if ($this->colormap != "") $temp .= $this->colormap->generateXml($offset." ");
		$temp .= $offset."</RasterSymbolizer>\n";
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
		$temp .= $offset."<table bgcolor=\"#FFFFFF\" style=\"border: 1px solid black;width:100%\">\n";
		$temp .= $offset." <tr valign=\"top\">\n";
		$temp .= $offset."  <td style=\"width: 130px;\">\n";
		
		$temp .= $offset."   RasterSymbolizer<br>\n";
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."\" value=\"rastersymbolizer\">\n";
		
		$number = split("_", $id);
		$number = $number[count($number)-1];
		
		$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=deletesymbolizer&id=".$this->parent."&number=".$number."\">l&ouml;schen</a>\n";
		
		$temp .= $offset."  </td>\n";
		
		$temp .= $offset."  <td>\n";
		
		$temp .= $offset."   <table>\n";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td style=\"width: 100px;\">\n";
		$temp .= $offset."      Opacity:\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      <input name=\"".$id."_opacity\" value=\"".htmlspecialchars($this->opacity)."\">\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."    </tr>\n";
		if ($this->colormap != "")
		{
			$temp .= $this->colormap->generateHtmlForm($id."_colormap", $offset."    ");
		}
		else
		{
			$temp .= $offset."    <tr>\n";
			$temp .= $offset."     <td colspan=\"2\">\n";
			$temp .= $offset."      <a class='edit' href=\"sld_function_handler.php?function=addcolormap&id=".$this->id."\">ColorMap hinzuf&uuml;gen</a>\n";
			$temp .= $offset."     </td>\n";
			$temp .= $offset."    </tr>\n";
		}
		$temp .= $offset."   </table>\n";
		
		
		
		$temp .= $offset."  </td>\n";
		$temp .= $offset." </tr>\n";
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
		if (isset($_REQUEST[$id."_opacity"]))
		{
			$this->opacity = $_REQUEST[$id."_opacity"];
		}
		if (isset($_REQUEST[$id."_colormap"]))
		{
			$this->colormap = new ColorMap();
			$this->colormap->generateObjectFromPost($id."_colormap");
		}
	}
}
?>