<?php
# $Id: Filter.php 2412 2008-04-23 16:14:30Z christoph $
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
 * This file contains the classes used for editing filter expressions.
 * Filter expressions are currently limited to logical and comparison operations.
 *
 * @package filter_classes
 * @author Markus Krzyzanowski
 */



/**
 * This class is the implementation of the filter-element from ogc:filter.
 *
 * This element builds up the root of filter-expressions.
 * This class is directly referred to in the sld_edit_filter.php.
 * 
 * @package filter_classes
 */
class Filter extends Rule
{
	/**
	 * Array containing the operations of this filter
	 * @see UnaryLogicOp
	 * @see BinaryLogicOp
	 * @see BinaryComparisonOp
	 * @see PropertyIsLike
	 * @see PropertyIsNull
	 * @see PropertyIsBetween
	 * @var array
	 */
	var $operations = array();
	
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
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<ogc:Filter>\n";
		foreach($this->operations as $operation)
		{
			$temp .= $operation->generateXml($offset." ");
		}
		$temp .= $offset."</ogc:Filter>\n";
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
		$temp .= $offset."<input type=\"hidden\" name=\"filter\">\n";
		$temp .= $offset."<table>\n";
		$temp .= $offset." <tr>\n";
		$temp .= $offset."  <td>\n";
		$temp .= $offset."   Filter:<br>\n";
		$temp .= $offset."   <a class=\"edit\" href=\"sld_edit_filter.php?function=deletefilter\">";
		$temp .= "<img src='./img/minus.gif' border='0'>&nbsp;l&ouml;schen</a>\n";
		$temp .= $offset."  </td>\n";
		$temp .= $offset."  <td colspan=\"3\">\n";
		$temp .= $offset."  &nbsp;\n";
		$temp .= $offset."  </td>\n";
		$temp .= $offset." </tr>\n";
		
		$displayOperationModule = new DisplayOperationModule();
		$temp .= $displayOperationModule->generateHtmlForm($offset." ", $this->operations, $id);
		
		if (count($this->operations) == 0)
		{
			$temp .= $offset." <tr>\n";
			$temp .= $offset."  <td colspan=\"4\">\n";
			
			$addOperationModule = new AddOperationModule($this->id, $id);
			$temp .= $addOperationModule->generateHtmlForm($offset."    ");
			
			$temp .= $offset."  </td>\n";
			$temp .= $offset." </tr>\n";
		}
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
		$countOperations = 0;
		while (isset($_REQUEST[$id."operation_".$countOperations]))
		{
			if ($_REQUEST[$id."operation_".$countOperations] == "binaryComparisonOp")
			{
				$operation = new BinaryComparisonOp();
			}
			if ($_REQUEST[$id."operation_".$countOperations] == "binaryLogicOp")
			{
				$operation = new BinaryLogicOp();
			}
			if ($_REQUEST[$id."operation_".$countOperations] == "unaryLogicOp")
			{
				$operation = new UnaryLogicOp();
			}
			if ($_REQUEST[$id."operation_".$countOperations] == "propertyIsLike")
			{
				$operation = new PropertyIsLike();
			}
			if ($_REQUEST[$id."operation_".$countOperations] == "propertyIsNull")
			{
				$operation = new PropertyIsNull();
			}
			if ($_REQUEST[$id."operation_".$countOperations] == "propertyIsBetween")
			{
				$operation = new PropertyIsBetween();
			}
			$operation->generateObjectFromPost($id."operation_".$countOperations);
			$this->operations[] = $operation;
			$countOperations++;
		}
	}
	
	/**
	 * deletes the rule with the given index from the $operations array
	 * @param int $index index of the operation that has to be deleted
	 */
	function deleteOperation($index)
	{
		array_splice($this->operations, $index, 1);
	}
}
?>