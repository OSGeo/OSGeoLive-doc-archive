<?php
# $Id: PointPlacement.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the PointPlacement-element
 *
 * Creating an instance of this class will also create the anchorpoint and displacement objects for this instance.
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class PointPlacement
{
	/**
	 * The anchorpoint object from the xml-scheme.
	 *
	 * @see AnchorPoint
	 * @var object
	 */
	var $anchorpoint = "";
	
	/**
	 * The displacement object from the xml-scheme.
	 *
	 * @see Displacement
	 * @var object
	 */
	var $displacement = "";
	
	/**
	 * The rotation attribute from the xml-scheme.
	 *
	 * Not sure if this feature is supported in UMN-MapServer.
	 *
	 * @var int
	 */
	var $rotation = "";
	
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
	 * Default constructor that automatically creates an AnchorPoint and a Displacement object.
	 */
	function PointPlacement()
	{
		$this->anchorpoint = new AnchorPoint();
		$this->displacement = new Displacement();
	}
	
	/**
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<PointPlacement>\n";
		$temp .= $this->anchorpoint->generateXml($offset." ");
		$temp .= $this->displacement->generateXml($offset." ");
		if ($this->rotation != "") $temp .= $offset." <Rotation>".htmlspecialchars($this->rotation)."</Rotation>\n";
		$temp .= $offset."</PointPlacement>\n";
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
		$temp .= $offset."<input type=\"hidden\" name=\"".$id."\" value=\"pointplacement\">\n";
		$temp .= $offset."<table>\n";

		$temp .= $this->anchorpoint->generateHtmlForm($id."_anchorpoint", $offset." ");
		$temp .= $this->displacement->generateHtmlForm($id."_displacement", $offset." ");
		
		$temp .= $offset." <tr>\n";
		$temp .= $offset."  <td style=\"width: 100px;\">\n";
		$temp .= $offset."   Rotation:\n";
		$temp .= $offset."  </td>\n";
		$temp .= $offset."  <td>\n";
		$temp .= $offset."   <input name=\"".$id."_rotation\" value=\"".htmlspecialchars($this->rotation)."\"><br>\n";
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
		if (isset($_REQUEST[$id."_anchorpoint"]))
		{
			$this->anchorpoint = new AnchorPoint();
			$this->anchorpoint->generateObjectFromPost($id."_anchorpoint");
		}
		if (isset($_REQUEST[$id."_displacement"]))
		{
			$this->displacement = new Displacement();
			$this->displacement->generateObjectFromPost($id."_displacement");
		}
		if (isset($_REQUEST[$id."_rotation"]))
		{
			$this->rotation = $_REQUEST[$id."_rotation"];
		}
	}
}
?>