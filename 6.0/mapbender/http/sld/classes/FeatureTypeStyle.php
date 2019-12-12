<?php
# $Id: FeatureTypeStyle.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the FeatureTypeStyle-element
 * 
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class FeatureTypeStyle extends UserStyle
{
	/**
	 * Array containing the Rule-objects
	 *
	 * @see Rule
	 * @var array
	 */
	var $rules = array();
	
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
	 * Array containing the FeatureTypes attribute names (aka elements), acquired either by a 
	 * - Mapbender WFS-configuration
	 * - or via a DescribeFeatureType request (niy)
	 * @see PropertyIsEqualTo, TextSymbolizer 
	 * @var array
	 */
	var $attrs = array();
	
	final function setElementArray($name,$array) {
		$this->attrs[$name] = $array;
	}
	
	/**
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<FeatureTypeStyle>\n";
		foreach($this->rules as $rule)
		{
			$temp .= $rule->generateXml($offset." ");
		}
		$temp .= $offset."</FeatureTypeStyle>\n";
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
		###### -----------> Fensterrahmen Rule
		$temp = "";
		$temp .= $offset."<input type=\"hidden\" name=\"".$id."\" value=\"featuretypestyle\">\n";
		$temp .= $offset."<table border=\"0\" cellspacing=\"2\" cellspacing=\"1\">\n";
		
		$rule_id = 0;
		foreach ($this->rules as $rule)
		{
			$temp .= $offset." <tr>\n";
			$temp .= $rule->generateHtmlForm($id."_rule_".$rule_id, $offset."  ");
			$rule_id++;
			$temp .= $offset." </tr>\n";
			
			$temp .= $offset."<tr>\n";
			$temp .= $offset."<td colspan=\"3\"' height='5'>\n";
			$temp .= $offset."<hr size='1' noshade color='#86A6C5'>\n";
			$temp .= $offset."</td>\n";				
			$temp .= $offset."</tr>\n";
		}
		$temp .= $offset."<tr>\n";
		$temp .= $offset."<td colspan=\"3\">\n";
			$temp .= $offset."<table width='100%' border='0' cellspacing=\"2\" cellspacing=\"1\">\n";
			$temp .= $offset."<tr>\n";
			$temp .= $offset."<td>\n";		
			$temp .= $offset."<a class='edit' href=\"sld_function_handler.php?function=addrule&id=".$this->id."\">\n";
			$temp .= $offset."<img src='./img/plus.gif' border='0' alt='Rule hinzuf�gen'>&nbsp;hinzuf&uuml;gen\n";		
			$temp .= $offset."</a>\n";
			$temp .= $offset."</td>\n";		
			$temp .= $offset."</tr>\n";
			$temp .= $offset."</table>\n";				
			$temp .= $offset."</td>\n";
			$temp .= $offset."</tr>\n";
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
		$countRules = 0;
		while (isset($_REQUEST[$id."_rule_".$countRules]))
		{
			$rule = new Rule();
			$rule->generateObjectFromPost($id."_rule_".$countRules);
			$this->rules[] = $rule;
			$countRules++;
		}
	}
	/**
	 * adds a new Rule object to the $rules array
	 *
	 * this function is called from sld_function_handler.php
	 */
	function addRule()
	{
		$this->rules[] = new Rule();
	}
	/**
	 * deletes the rule with the given index from the $rules array
	 *
	 * this function is called from sld_function_handler.php
	 *
	 * @param int $index index of the rule that has to be deleted
	 */
	function deleteRule($index)
	{
		array_splice($this->rules, $index, 1);
	}
	/**
	 * generates html with the fts elements to choose from, useful for Label, Filter, etc.
	 * 
	 * @param string $field string that holds the id of the form field where to return the selected value
	 *
	 * @param string $value string that holds the name of an already used element name
	 * 
	 * @return string html-fragment that lists the elements, each can be clicked and the name is 
	 * returned to the form field 
	 */
	final function generateElementsHtml($field,$value)
	{
		$html = "";
		if ($this->attrs) {
			$html .= "<select name='elementname' onchange=\"if (this.selectedIndex!=0) document.getElementById('".$field."').value=this.options[this.selectedIndex].value;\">\n";
			$html .= "<option>Choose ...</option>";
			foreach ($this->attrs["element_name"] as $ename) {
				$html .= "<option value='".$ename."' ";
				if ($value == $ename)
					$html .= "selected";
				$html .= ">".$ename."<otion>\n";
			}
			$html .= "</select>\n";
		}
		return $html;
	}
	
}

?>