<?php
# $Id: BinaryComparisonOp.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the BinaryComparisonOp-element
 *
 * @package filter_classes
 * @author Markus Krzyzanowski
 */
class BinaryComparisonOp
{
	/**
	 * Defines the type of comparison operation.
	 * @var string
	 */
	var $name = "";
	
	/**
	 * Name of the property that is addressed in this comparison.
	 * @var string
	 */
	var $ogcPropertyName = "";
	
	/**
	 * String that is compared to the property-value.
	 * Regular Expression?
	 * @var string
	 */
	var $ogcLiteral = "";
	
	/**
	 * Index of this object in the $_SESSION("sld_filter_objects") array.
	 * @var int
	 */
	var $id = "";
	
	/**
	 * Index of this object's parent object in the $_SESSION("sld_filter_objects") array.
	 * @var int
	 */
	var $parent = "";
	
	/**
	 * constructor that directly fills the $name variable.
	 * @param string $name the type of the comparison operation
	 */
	function BinaryComparisonOp($name)
	{
		$this->name = $name;
	}
	
	/**
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<ogc:".$this->name.">\n";
		$temp .= $offset. " <ogc:PropertyName>".$this->ogcPropertyName."</ogc:PropertyName>\n";
		$temp .= $offset. " <ogc:Literal>".$this->ogcLiteral."</ogc:Literal>\n";
		$temp .= $offset."</ogc:".$this->name.">\n";
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
	function generateHtmlForm($id = "", $offset = "")
	{
		$temp = "";
		$temp .= $offset."<tr>\n";
		$temp .= $offset." <td>\n";
		
		$temp .= $offset."  <input type=\"hidden\" name=\"".$id."\" value=\"binaryComparisonOp\">\n";
		$temp .= $offset."  <input type=\"hidden\" name=\"".$id."_name\" value=\"".$this->name."\">\n";
		if (count($_SESSION["sld_objects"][3]->attrs)>0) {
			$temp .= $offset."  <input type=\"hidden\" id=\"".$id."_ogcpropertyname\" name=\"".$id."_ogcpropertyname\" value=\"".$this->ogcPropertyName."\">\n";
			$temp_elements = $_SESSION["sld_objects"][3]->generateElementsHtml($id."_ogcpropertyname",$this->ogcPropertyName);
			$temp .= $offset.$temp_elements;
		} else {
			$temp .= $offset."  <input name=\"".$id."_ogcpropertyname\" value=\"".$this->ogcPropertyName."\">\n";
		}
		$temp .= $offset." </td>\n";
		$temp .= $offset." <td style=\"width:50px; text-align: center; font-size: large; \">";
				
		switch($this->name)
		{
			case "PropertyIsEqualTo": $temp .= "="; break;
			case "PropertyIsNotEqualTo": $temp .= "!="; break;
			case "PropertyIsGreaterThan": $temp .= "&gt;"; break;
			case "PropertyIsGreaterThanOrEqualTo": $temp .= "&gt;="; break;
			case "PropertyIsLessThan": $temp .= "&lt;"; break;
			case "PropertyIsLessThanOrEqualTo": $temp .= "&lt;="; break;
			default: $temp .= $this->name;
		}
		$temp .= "\n";
		$temp .= $offset." </td>\n";
		$temp .= $offset." <td>\n";
		
		$temp .= $offset."  <input name=\"".$id."_ogcliteral\" value=\"".$this->ogcLiteral."\">\n";
		
		$temp .= $offset." </td>\n";
		$temp .= $offset." <td>\n";
		
		$number = split("_", $id);
		$number = $number[count($number)-1];
		$temp .= $offset."  <a class=\"edit\" href=\"?function=deleteoperation&id=".$this->parent."&number=".$number."\">";
		$temp .= "<img src='./img/minus.gif' border='0'>&nbsp;l&ouml;schen</a>\n";
		
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
		$this->name = $_REQUEST[$id."_name"];
		$this->ogcPropertyName = $_REQUEST[$id."_ogcpropertyname"];
		$this->ogcLiteral = $_REQUEST[$id."_ogcliteral"];
	}
}
?>