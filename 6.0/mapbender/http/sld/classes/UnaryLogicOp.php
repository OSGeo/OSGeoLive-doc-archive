<?php
# $Id: UnaryLogicOp.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the UnaryLogicOp-element
 *
 * @package filter_classes
 * @author Markus Krzyzanowski
 */
class UnaryLogicOp
{
	/**
	 * Defines the type of logical operation.
	 * Possible value: not
	 * @var string
	 */
	var $name = "";
	
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
	 * constructor tha directly fills the $name variable
	 * @param string $name the type of this logical operation
	 */
	function UnaryLogicOp($name)
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
		$temp = $offset."<".$this->name.">\n";
		foreach($this->operations as $operation)
		{
			$temp .= $operation->generateXml($offset." ");
		}
		$temp .= $offset."</".$this->name.">\n";
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
		$temp .= $offset."  <input type=\"hidden\" name=\"".$id."\" value=\"unaryLogicOp\">\n";
		$temp .= $offset."  <input type=\"hidden\" name=\"".$id."_name\" value=\"".$this->name."\">\n";
		$temp .= $offset."  ".$this->name."<br>\n";
		
		$number = split("_", $id);
		$number = $number[count($number)-1];
		$temp .= $offset."   <a class=\"edit\" href=\"?function=deleteoperation&id=".$this->parent."&number=".$number."\">";
		$temp .= "<img src='./img/minus.gif' border='0'>&nbsp;l&ouml;schen</a>\n";
				
		$temp .= $offset." </td>\n";
		$temp .= $offset." <td colspan=\"3\" valign=\"top\">\n";
		//Only 1 operation
		if (count($this->operations) < 1)
		{
			$addOperationModule = new AddOperationModule($this->id, $id);
			$temp .= $addOperationModule->generateHtmlForm($offset."   ");
		}
		else
		{
			$temp .= $offset."  <table>\n";
			$displayOperationModule = new DisplayOperationModule();
			$temp .= $displayOperationModule->generateHtmlForm($offset."   ", $this->operations, $id);
			$temp .= $offset."  </table>\n";
		}
		
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
		$countOperations = 0;
		while (isset($_REQUEST[$id."_operation_".$countOperations]))
		{
			if ($_REQUEST[$id."_operation_".$countOperations] == "binaryComparisonOp")
			{
				$operation = new BinaryComparisonOp();
			}
//			if ($_REQUEST[$id."_operation_".$countOperations] == "unaryComparisonOp")
//			{
//				$operation = new UnaryComparisonOp();
//			}
			if ($_REQUEST[$id."_operation_".$countOperations] == "binaryLogicOp")
			{
				$operation = new BinaryLogicOp();
			}
			if ($_REQUEST[$id."_operation_".$countOperations] == "unaryLogicOp")
			{
				$operation = new UnaryLogicOp();
			}
			if ($_REQUEST[$id."_operation_".$countOperations] == "propertyIsLike")
			{
				$operation = new PropertyIsLike();
			}
			if ($_REQUEST[$id."_operation_".$countOperations] == "propertyIsNull")
			{
				$operation = new PropertyIsNull();
			}
			if ($_REQUEST[$id."_operation_".$countOperations] == "propertyIsBetween")
			{
				$operation = new PropertyIsBetween();
			}
			$operation->generateObjectFromPost($id."_operation_".$countOperations);
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