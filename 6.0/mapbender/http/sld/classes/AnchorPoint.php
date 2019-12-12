<?php
# $Id: AnchorPoint.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the AnchorPoint-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class AnchorPoint
{
	/**
	 * The anchorpointx attribute from the xml-scheme.
	 * Allowed values are between 0 and 1.
	 * @var float
	 */
	var $anchorpointx = "";
	
	/**
	 * The anchorpointy attribute from the xml-scheme.
	 * Allowed values are between 0 and 1.
	 * @var float
	 */
	var $anchorpointy = "";
	
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
		$temp = $offset."<AnchorPoint>\n";
		$temp .= $offset." <AnchorPointX>".htmlspecialchars($this->anchorpointx)."</AnchorPointX>\n";
		$temp .= $offset." <AnchorPointY>".htmlspecialchars($this->anchorpointy)."</AnchorPointY>\n";
		$temp .= $offset."</AnchorPoint>\n";
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
		$temp .= $offset."<input type=\"hidden\" name=\"".$id."\" value=\"anchorpoint\">\n";
		$temp .= $offset."<tr>\n";
		$temp .= $offset." <td>\n";
		$temp .= $offset."  AnchorPointX:\n";
		$temp .= $offset." </td>\n";
		$temp .= $offset." <td>\n";
		
		//TODO - check possible values and their meaning
		$temp .= $offset."  <select name=\"".$id."_anchorpointx\">\n";
		$temp .= $offset."   <option value=\"0.0\"";
		if ( $this->anchorpointx == "0.0") $temp .= " selected";
		$temp .= ">0.0 - Ankerpunkt rechts</option>\n";
		$temp .= $offset."   <option value=\"0.5\"";
		if ( $this->anchorpointx == "0.5") $temp .= " selected";
		$temp .= ">0.5 - Ankerpunkt mitte</option>\n";
		$temp .= $offset."   <option value=\"1.0\"";
		if ( $this->anchorpointx == "1.0") $temp .= " selected";
		$temp .= ">1.0 - Ankerpunkt links</option>\n";
		$temp .= $offset."  </select>\n";
		
		$temp .= $offset." </td>\n";
		$temp .= $offset."</tr>\n";
		$temp .= $offset."<tr>\n";
		$temp .= $offset." <td>\n";
		$temp .= $offset."  AnchorPointY:\n";
		$temp .= $offset." </td>\n";
		$temp .= $offset." <td>\n";
		
		//TODO - check possible values and their meaning
		$temp .= $offset."  <select name=\"".$id."_anchorpointy\">\n";
		$temp .= $offset."   <option value=\"0.0\"";
		if ( $this->anchorpointy == "0.0") $temp .= " selected";
		$temp .= ">0.0 - Ankerpunkt oben</option>\n";
		$temp .= $offset."   <option value=\"0.5\"";
		if ( $this->anchorpointy == "0.5") $temp .= " selected";
		$temp .= ">0.5 - Ankerpunkt mitte</option>\n";
		$temp .= $offset."   <option value=\"1.0\"";
		if ( $this->anchorpointy == "1.0") $temp .= " selected";
		$temp .= ">1.0 - Ankerpunkt unten</option>\n";
		$temp .= $offset."  </select>\n";
		
		
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
		if (isset($_REQUEST[$id."_anchorpointx"]))
		{
			$this->anchorpointx = $_REQUEST[$id."_anchorpointx"];
		}
		if (isset($_REQUEST[$id."_anchorpointy"]))
		{
			$this->anchorpointy = $_REQUEST[$id."_anchorpointy"];
		}
	}
}
?>