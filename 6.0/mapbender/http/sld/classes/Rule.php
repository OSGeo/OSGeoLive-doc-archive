<?php
# $Id: Rule.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the Rule-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class Rule extends FeatureTypeStyle
{
	/**
	 * The name attribute from the xml-scheme.
	 * @var string
	 */
	var $name = "";
	
	/**
	 * The title attribute from the xml-scheme.
	 * @var string
	 */
	var $title = "";
	
	/**
	 * The LegendGraphic element from the xml-scheme.
	 * @see LegendGraphic
	 * @var object
	 */
	var $legendgraphic = "";
	
	/**
	 * The complete filter expression.
	 *
	 * The parsing an editing of the filter expressions is not done in the main sld editor.
	 * This field contains the complete filter expression without any parsing.
	 * The parsing is done in sld_filter_parse.php using the classes in sld_filter_classes.php.
	 * Editing is possible in sld_edit_filter.php. This page is opened in a popup-window.
	 *
	 * @see sld_filter_classes.php
	 * @var string
	 */
	var $filter = "";
	
	/**
	 * The miscaledenominator attribute from the xml-scheme.
	 * @var int
	 */
	var $minscaledenominator = "";
	
	/**
	 * The maxscaledenominator attribute from the xml-scheme.
	 * @var int
	 */
	var $maxscaledenominator = "";
	
	/**
	 * Array containing the different symbolizers for this rule.
	 * @var array
	 */
	var $symbolizers = array();
	
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
		$temp = $offset."<Rule>\n";
		if ($this->name != "") $temp .= $offset." <Name>".htmlspecialchars($this->name)."</Name>\n";
		if ($this->title != "") $temp .= $offset." <Title>".htmlspecialchars($this->title)."</Title>\n";
		if ($this->legendgraphic != "") $temp .= $this->legendgraphic->generateXml($offset." ");
		
		if ($this->filter != "") $temp .= $offset." ".$this->filter."\n";
		if ($this->minscaledenominator != "") $temp .= $offset." <MinScaleDenominator>".htmlspecialchars($this->minscaledenominator)."</MinScaleDenominator>\n";
		if ($this->maxscaledenominator != "") $temp .= $offset." <MaxScaleDenominator>".htmlspecialchars($this->maxscaledenominator)."</MaxScaleDenominator>\n";
		foreach($this->symbolizers as $symbolizer)
		{
			$temp .= $symbolizer->generateXml($offset." ");
		}
		$temp .= $offset."</Rule>\n";
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
		$temp .= $offset."<td rowspan=\"2\" valign='top'>\n";
		
			//Table in the first cell for the attributes of this rule
			###### -----------> Fenster Rule Eigenschaften
			$temp .= $offset." <table class='edit_label_bg2' border=\"0\" cellspacing=\"2\" cellpadding=\"1\">\n";
			$temp .= $offset."  <tr>\n";
			$temp .= $offset."   <td class='edit_label_bg edit_label_text'>\n";
			$temp .= $offset."    Rule:\n";
			$temp .= $offset."    <input type=\"hidden\" name=\"".$id."\" value=\"rule\">\n";
			$temp .= $offset."   </td>\n";
			
			$number = split("_", $id);
			$number = $number[count($number)-1];
			
			$temp .= $offset."   <td>\n";
			$temp .= $offset."    &nbsp;\n";
			$temp .= $offset."   </td>\n";
			$temp .= $offset."  </tr>\n";
			$temp .= $offset."  <tr>\n";
			$temp .= $offset."   <td class='edit_label_bg edit_label_text'>Name:</td>\n";
			$temp .= $offset."   <td><input class='inputfield edit_label_text' name=\"".$id."_name\" value=\"".htmlspecialchars($this->name)."\"></td>\n";
			$temp .= $offset."  </tr>\n";
			$temp .= $offset."  <tr>\n";
			$temp .= $offset."   <td class='edit_label_bg edit_label_text'>Title:</td>\n";
			$temp .= $offset."   <td><input class='inputfield edit_label_text' name=\"".$id."_title\" value=\"".htmlspecialchars($this->title)."\"></td>\n";
			$temp .= $offset."  </tr>\n";
			
			$temp .= $offset."  <tr>\n";
			$temp .= $offset."   <td class='edit_label_bg edit_label_text'>";
			$temp .= $offset."	 Filter:";
			$temp .= $offset."	</td>\n";
			$temp .= $offset."   <td>\n";
			$temp .= $offset."    <input type=\"hidden\" name=\"".$id."_filter\" value=\"".htmlspecialchars($this->filter)."\">\n";
			$temp .= $offset."    	<img src='./img/lightning.png' class='hand' border='0' alt='Filter bearbeiten'";
			$temp .= "onClick=\"window.open('sld_edit_filter.php?".$urlParameters."&sld_form_element_id=".$id."_filter&sld_objects_rule_id=".$this->id."','editFilter',";
			$temp .= " 'width=1000, height=800, resizable=yes');\">\n";
			$temp .= $offset."   </td>\n";
			$temp .= $offset."  </tr>\n";
			
			$temp .= $offset."  <tr>\n";
			$temp .= $offset."   <td class='edit_label_bg edit_label_text'>Minscale:</td>\n";
			$temp .= $offset."   <td><input class='inputfield edit_label_text' name=\"".$id."_minscale\" value=\"".$this->minscaledenominator."\"></td>\n";
			$temp .= $offset."  </tr>\n";
			$temp .= $offset."  <tr>\n";
			$temp .= $offset."   <td class='edit_label_bg edit_label_text'>Maxscale:</td>\n";
			$temp .= $offset."   <td><input class='inputfield edit_label_text' name=\"".$id."_maxscale\" value=\"".$this->maxscaledenominator."\"></td>\n";
			$temp .= $offset."  </tr>\n";
			$temp .= $offset."  <tr>\n";				
			$temp .= $offset."   <td>\n";
			$temp .= $offset."	  <a class='edit' href=\"sld_function_handler.php?function=deleterule&id=".$this->parent."&number=".$number."\">\n";
			$temp .= $offset."    <img src='./img/minus.gif' border='0' alt='Rule l&ouml;schen'>&nbsp;l&ouml;schen\n";
			$temp .= $offset."    </a>\n";
			$temp .= $offset."   </td>\n";
			$temp .= $offset."   <td>\n";
			$temp .= $offset."   	&nbsp;";
			$temp .= $offset."   </td>\n";
			$temp .= $offset."  </tr>\n";
			$temp .= $offset." </table>\n";
			//End Table in first cell
		
		$temp .= $offset."</td>\n";
		$temp .= $offset."<td>\n";
		$temp .= $offset."-\n";
		$temp .= $offset."</td>\n";

		
		$temp .= $offset."<td>\n";
			//Second cell for the symbolizers and the legendgraphic			
			// Table1 in second cell
			$temp .= $offset."<table class='edit_label_bg2' border=\"0\" cellspacing=\"2\" cellpadding=\"1\">\n";
			$temp .= $offset."<tr>\n";
			########### -----------> LEGENDGRAPHIC		
			if ($this->legendgraphic != "")
			{
				$temp .= $offset."<td>\n";
				$temp .= $this->legendgraphic->generateHtmlForm($id."_legendgraphic", $offset." ");
				$temp .= $offset."</td>\n";
				$temp .= $offset."</tr>\n";							
			}
			else
			{	
			$temp .= $offset."<td class='edit_label_bg edit_label_text' width='136'>\n";			
			$temp .= $offset."LegendGraphic:\n";
			$temp .= $offset."</td>\n";			
			$temp .= $offset."<td>\n";						
			$temp .= $offset."&nbsp;\n";			
			$temp .= $offset."</td>\n";						
			$temp .= $offset."</tr>\n";
			$temp .= $offset."<tr>\n";
			$temp .= $offset."<td>\n";
			$temp .= $offset."<a class='edit' href=\"sld_function_handler.php?function=addlegendgraphic&id=".$this->id."\">\n";
			$temp .= $offset."<img src='./img/plus.gif' border='0' alt='LegendGraphic hinzuf&uuml;gen'>&nbsp;hinzuf&uuml;gen</a>\n";
			$temp .= $offset."</td>\n";	
			$temp .= $offset."<td>\n";
			$temp .= $offset."&nbsp;\n";									
			$temp .= $offset."</td>\n";														
			$temp .= $offset."</tr>\n";
			}
			$temp .= $offset."</table>\n";
			//End Table1 in second cell
			
		$temp .= $offset."</td>\n";
		$temp .= $offset."</tr>\n";

		$temp .= $offset."<tr>\n";
		$temp .= $offset."<td>\n";
		$temp .= $offset."-\n";
		$temp .= $offset."</td>\n";

		$temp .= $offset."<td>\n";

			// Table in second cell
			$temp .= $offset."<table class='edit_label_bg2' border=\"0\" cellspacing=\"2\" cellpadding=\"1\">\n";
			########### -----------> SYMBOLIZER
			$temp .= $offset."<tr>\n";
			$temp .= $offset."<td class='edit_label_bg edit_label_text' valign='top'>\n";
			$temp .= $offset." <select class=\"edit_label_text\" name=\"".$id."_newsymbolizer\">\n";
			$temp .= $offset."  <option value=\"textsymbolizer\">Textsymbolizer</option>\n";
			$temp .= $offset."  <option value=\"polygonsymbolizer\">Polygonsymbolizer</option>\n";
			$temp .= $offset."  <option value=\"pointsymbolizer\">Pointsymbolizer</option>\n";
			$temp .= $offset."  <option value=\"rastersymbolizer\">Rastersymbolizer</option>\n";
			$temp .= $offset."  <option value=\"linesymbolizer\">Linesymbolizer</option>\n";
			$temp .= $offset." </select>\n";					
			$temp .= $offset."</td>\n";
			$temp .= $offset."</tr>\n";
			$temp .= $offset."<tr>\n";			
			$temp .= $offset."<td>\n";	
			$symbolizer_id = 0;
			foreach ($this->symbolizers as $symbolizer)
			{
				$temp .= $symbolizer->generateHtmlForm($id."_symbolizer_".$symbolizer_id, $offset."  ");
				$symbolizer_id++;
			}
			$temp .= $offset."</td>\n";							
			$temp .= $offset."</tr>\n";
			$temp .= $offset."<tr>\n";
			$temp .= $offset."<td valign='bottom'>\n";		
			$temp .= $offset."<input class=\"edit\" type=\"button\" value=\"hinzuf&uuml;gen\"";
			//Javascript to make a http request
			$temp .= " onClick=\"url='sld_function_handler.php?function=addsymbolizer&id=".$this->id."&symbolizer=';";
			$temp .= " url += ".$id."_newsymbolizer.value;";
			$temp .= " location.href = url;\"";
			$temp .= ">\n";
			$temp .= $offset."</td>\n";
			$temp .= $offset."</tr>\n";
			$temp .= $offset."</table>\n";
			//End Table in second cell
			
		$temp .= $offset."</td>\n";													
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
		$this->title = $_REQUEST[$id."_title"];
		
		$this->filter = $_REQUEST[$id."_filter"];
		
		$this->minscaledenominator = $_REQUEST[$id."_minscale"];
		$this->maxscaledenominator = $_REQUEST[$id."_maxscale"];
		if (isset($_REQUEST[$id."_legendgraphic"]))
		{
			$this->legendgraphic = new LegendGraphic();
			$this->legendgraphic->generateObjectFromPost($id."_legendgraphic");
		}
		$countSymbolizers = 0;
		while (isset($_REQUEST[$id."_symbolizer_".$countSymbolizers]))
		{
			$symbolizer = "";
			if ($_REQUEST[$id."_symbolizer_".$countSymbolizers] == "linesymbolizer")
			{
				$symbolizer = new LineSymbolizer();
			}
			else if ($_REQUEST[$id."_symbolizer_".$countSymbolizers] == "polygonsymbolizer")
			{
				$symbolizer = new PolygonSymbolizer();
			}
			else if ($_REQUEST[$id."_symbolizer_".$countSymbolizers] == "pointsymbolizer")
			{
				$symbolizer = new PointSymbolizer();
			}
			else if ($_REQUEST[$id."_symbolizer_".$countSymbolizers] == "textsymbolizer")
			{
				$symbolizer = new TextSymbolizer();
			}
			else if ($_REQUEST[$id."_symbolizer_".$countSymbolizers] == "rastersymbolizer")
			{
				$symbolizer = new RasterSymbolizer();
			}
			
			$symbolizer->generateObjectFromPost($id."_symbolizer_".$countSymbolizers);
			$this->symbolizers[] = $symbolizer;
			$countSymbolizers++;
		}
	}
	
	/**
	 * Function that adds a symbolizer to the symbolizers array.
	 *
	 * This function is called from sld_function_handler.php
	 *
	 * @param string $symbolizer string containing the type of the symbolizer that has to be added
	 */
	function addSymbolizer($symbolizer)
	{
		if ($symbolizer == "linesymbolizer")
		{
			$this->symbolizers[] = new LineSymbolizer();
		}
		else if ($symbolizer == "textsymbolizer")
		{
			$this->symbolizers[] = new TextSymbolizer();
		}
		else if ($symbolizer == "polygonsymbolizer")
		{
			$this->symbolizers[] = new PolygonSymbolizer();
		}
		else if ($symbolizer == "rastersymbolizer")
		{
			$this->symbolizers[] = new RasterSymbolizer();
		}
		else if ($symbolizer == "pointsymbolizer")
		{
			$this->symbolizers[] = new PointSymbolizer();
		}
	}
	
	/**
	 * Deletes the symbolizer at the given index from the symbolizers array.
	 *
	 * This function is called from sld_function_handler.php
	 *
	 * @param int $index index of the symbolizer that should be deleted.
	 */
	function deleteSymbolizer($index)
	{
		array_splice($this->symbolizers, $index, 1);
	}
}
?>