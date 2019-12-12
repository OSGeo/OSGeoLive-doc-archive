<?php
# $Id: Halo.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the Halo-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class Halo
{
	/**
	 * The radius attribute from the xml-scheme.
	 * @var int
	 */
	var $radius = "";
	
	/**
	 * The Fill object from the xml-scheme.
	 *
	 * @see Fill
	 * @var string
	 */
	var $fill = "";
	
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
		$temp = $offset."<Halo>\n";
		if ($this->radius != "") $temp .= $offset." <Radius>".htmlspecialchars($this->radius)."</Radius>\n";
		if ($this->fill != "") $temp .= $this->fill->generateXml($offset." ");
		$temp .= $offset."</Halo>\n";
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
		$temp = "<hr class=\"sep\">\n";
		$temp .= $offset."<table>\n";
		$temp .= $offset." <tr valign=\"top\">\n";
		$temp .= $offset."  <td style=\"width: 100px;\">\n";
		$temp .= $offset."   Halo\n";
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."\" value=\"halo\">\n<br />";
		$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=deletehalo&id=".$this->parent."\">l&ouml;schen</a>\n";		
		$temp .= $offset."  </td>\n";
		$temp .= $offset."  <td>\n";
		$temp .= $offset."   Radius: <input name=\"".$id."_radius\" value=\"".htmlspecialchars($this->radius)."\"><br>\n";
		if ($this->fill != "") { 
			$temp .= $this->fill->generateHtmlForm($id."_fill", $offset."   "); }
		else {
			$temp .= $offset."<a class='edit' href=\"sld_function_handler.php?function=addfill&id=".$this->id."\">Fill hinzuf&uuml;gen</a><br>\n";
		}
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
		if (isset($_REQUEST[$id."_radius"]))
		{
			$this->radius = $_REQUEST[$id."_radius"];
		}
		if (isset($_REQUEST[$id."_fill"]))
		{
			$this->fill = new Fill();
			$this->fill->generateObjectFromPost($id."_fill");
		}
	}
}
?>