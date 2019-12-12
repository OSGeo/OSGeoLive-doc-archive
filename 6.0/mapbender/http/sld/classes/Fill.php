<?php
# $Id: Fill.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the Fill-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class Fill
{
	/**
	 * The GraphicFill object from the xml-scheme.
	 *
	 * @see GraphicFill
	 * @var string
	 */
	var $graphicfill = "";
	
	/**
	 * Array containing the CssParameter objects from the xml-scheme.
	 *
	 * @see CssParameter
	 * @var array
	 */
	var $cssparameters = array();
	
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
		$temp = $offset."<Fill>\n";
		if ($this->graphicfill != "") $temp .= $this->graphicfill->generateXml($offset." ");
		foreach ($this->cssparameters as $cssparameter)
		{
			$temp .= $cssparameter->generateXml($offset." ");
		}
		$temp .= $offset."</Fill>\n";
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
		$temp .= $offset."   Fill<br>\n";
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."\" value=\"fill\">\n";
		$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=deletefill&id=".$this->parent."\">l&ouml;schen</a>\n";
		$temp .= $offset."  </td>\n";
		$temp .= $offset."  <td valign=\"top\">\n";
		if ($this->graphicfill != "")
		{
			$temp .= $this->graphicfill->generateHtmlForm($id."_graphicfill", $offset."   ");
		}
		else
		{
			$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=addgraphicfill&id=".$this->id."\">GraphicFill hinzuf&uuml;gen</a><br>\n";
		}
		$cssparameter_id = 0;
		foreach ($this->cssparameters as $cssparameter)
		{
			$temp .= $cssparameter->generateHtmlForm($id."_cssparameter_".$cssparameter_id, $offset."   ");
			$cssparameter_id++;
		}
		$temp .= $offset."   <select name=\"".$id."_newcssparameter\">\n";
		$temp .= $offset."    <option value=\"fill\">fill</option>\n";
		$temp .= $offset."    <option value=\"fill-opacity\">fill-opacity</option>\n";
		$temp .= $offset."   </select>\n";
		$temp .= $offset."   <input type=\"button\" value=\"hinzuf&uuml;gen\"";
		//Javascript to make a http request
		$temp .= " onClick=\"url='sld_function_handler.php?function=addcssparameter&id=".$this->id."&cssparameter=';";
		$temp .= " url += ".$id."_newcssparameter.value;";
		$temp .= " location.href = url;\"";
		$temp .= ">\n";
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
		if (isset($_REQUEST[$id."_graphicfill"]))
		{
			$this->graphicfill = new GraphicFill();
			$this->graphicfill->generateObjectFromPost($id."_graphicfill");
		}
		$countCssParameters = 0;
		while (isset($_REQUEST[$id."_cssparameter_".$countCssParameters]))
		{
			$cssParameter = new CssParameter();
			$cssParameter->generateObjectFromPost($id."_cssparameter_".$countCssParameters);
			$this->cssparameters[] = $cssParameter;
			$countCssParameters++;
		}
	}
	
	/**
	 * Function that adds a new CssParameter to the array.
	 * @param string $cssparameter the name of the new CssParameter object
	 */
	function addCssParameter($cssParameter)
	{
		$newCssParameter = new CssParameter();
		$newCssParameter->name = $cssParameter;
		$this->cssparameters[] = $newCssParameter;
	}
	
	/**
	 * Deletes the CssParameter at the given index.
	 * @param int $index index of the CssParamater in the array that has to be deleted
	 */
	function deleteCssParameter($index)
	{
		array_splice($this->cssparameters, $index, 1);
	}
}
?>