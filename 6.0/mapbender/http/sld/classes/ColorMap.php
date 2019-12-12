<?php
# $Id: ColorMap.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the ColorMap-element
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class ColorMap
{
	/**
	 * Array containing the ColorMapEntry objects from the sld.
	 *
	 * @see ColorMapEntry
	 * @var object
	 */
	var $colormapentries = "";
	
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
		$temp = $offset."<ColorMap>\n";
		foreach ($this->colormapentries as $colormapentry)
		{
			$temp .= $colormapentry->generateXml($offset." ");
		}
		$temp .= $offset."</ColorMap>\n";
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
		$temp .= $offset."<tr valign=\"top\">\n";
		$temp .= $offset." <td style=\"width: 100px;\">\n";
		$temp .= $offset."  ColorMap<br>\n";
		$temp .= $offset."  <input type=\"hidden\" name=\"".$id."\" value=\"colormap\">\n";
		$temp .= $offset."  <a class='edit' href=\"sld_function_handler.php?function=deletecolormap&id=".$this->parent."\">l&ouml;schen</a>\n";
		$temp .= $offset." </td>\n";
		$temp .= $offset." <td>\n";
		$colormapentry_id = 0;
		foreach ($this->colormapentries as $colormapentry)
		{
			$temp .= $colormapentry->generateHtmlForm($id."_colormapentry_".$colormapentry_id, $offset."  ");
			$colormapentry_id++;
		}
		$temp .= $offset."  <a class='edit' href=\"sld_function_handler.php?function=addcolormapentry&id=".$this->id."\">ColorMapEntry hinzuf&uuml;gen</a>\n";
		$temp .= $offset." </td>\n";
		$temp .= $offset."</tr>\n";
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
		$countColorMapEntries = 0;
		while (isset($_REQUEST[$id."_colormapentry_".$countColorMapEntries]))
		{
			$colormapentry = new ColorMapEntry();
			$colormapentry->generateObjectFromPost($id."_colormapentry_".$countColorMapEntries);
			$this->colormapentries[] = $colormapentry;
			$countColorMapEntries++;
		}
	}
	
	/**
	 * Adds a ColorMapEntry object to the array.
	 */
	function addColorMapEntry()
	{
		$this->colormapentries[] = new ColorMapEntry();
	}
	
	/**
	 * Deletes the ColorMapEntry object at the given index.
	 * @param int $index index of the ColorMapEntry that has to be deleted
	 */
	function deleteColorMapEntry($index)
	{
		array_splice($this->colormapentries, $index, 1);
	}
}
?>