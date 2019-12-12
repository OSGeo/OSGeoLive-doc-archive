<?php
# $Id: TextSymbolizer.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the TextSymbolizer-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class TextSymbolizer extends Rule
{
	/**
	 * The Label element from the xml-scheme.
	 * This element is not implemented as an object.
	 * Although it is an object, we'll use a workaround ...
	 * <Label>Townname: <ogc:PropertyValue>NAME</ogc:PropertyValue></Label> should be possible
	 *
	 * @var object
	 */
	var $label = "";
	
	/**
	 * The Font object from the xml-scheme.
	 *
	 * @see Font
	 * @var object
	 */
	var $font = "";
	
	/**
	 * The Labelplacement object from the xml-scheme.
	 *
	 * @see LabelPlacement
	 * @var object
	 */
	var $labelplacement = "";
	
	//Halo is not supported by Mapserver
	var $halo = "";
	
	//Fill is not supported by Mapserver - only solid color
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
	
	
	function TextSymbolizer()
	{
		//$this->label = new ParameterValue();
		//$this->labelplacement = new LabelPlacement();
		//$this->fill = new Fill();
	}
	
	/**
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<TextSymbolizer>\n";
		#if ($this->label != "") $temp .= $offset." <Label>".htmlspecialchars($this->label)."</Label>\n";
		if ($this->label != "") $temp .= $offset." <Label>".$this->label->generateXml()."</Label>\n";
		if ($this->font != "") $temp .= $this->font->generateXml($offset." ");
		if ($this->labelplacement != "") $temp .= $this->labelplacement->generateXml($offset." ");
		if ($this->halo != "") $temp .= $this->halo->generateXml($offset." ");
		if ($this->fill != "") $temp .= $this->fill->generateXml($offset." ");
		$temp .= $offset."</TextSymbolizer>\n";
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
	function generateHtmlForm($id, $offset= "")
	{
		$temp = "";
		$label_value = "";
		$temp .= $offset."<table bgcolor=\"#FFFFFF\" cellspacing='2' cellpadding='0' style=\"border: 1px solid black; width:100%;\">\n";
		$temp .= $offset." <tr valign=\"top\">\n";
		$temp .= $offset."   <td class='text' style=\"width: 130px;\">\n";
		$temp .= $offset."    TextSymbolizer<br>\n";
		$temp .= $offset."    <input type=\"hidden\" name=\"".$id."\" value=\"textsymbolizer\">\n";
		
		$number = split("_", $id);
		$number = $number[count($number)-1];
		
		$temp .= $offset."<a class='edit' href=\"sld_function_handler.php?function=deletesymbolizer&id=".$this->parent."&number=".$number."\">l&ouml;schen</a>\n";
		
		$temp .= $offset."  </td>\n";
		$temp .= $offset."  <td>\n";
		
		$temp .= $offset."   <table>";
		$temp .= $offset."    <tr>\n";
		$temp .= $offset."     <td style=\"width: 100px\">\n";
		$temp .= $offset."      Label:\n";
		$temp .= $offset."     </td>\n";
		$temp .= $offset."     <td>\n";
		$temp .= $offset."      <input name=\"".$id."_label\" id=\"".$id."_label\" value=\"";
		if ($this->label != "")
			$label_value = $this->label->generateHtmlForm($id."_label","");
			$temp .= $label_value;
		$temp .= "\">\n";
		//experimental
		$temp_elements = $_SESSION["sld_objects"][3]->generateElementsHtml($id."_label",$label_value);
		$temp .= $offset.$temp_elements;
		$temp .= $offset."     </td>\n";
		$temp .= $offset."    </tr>\n";
		$temp .= $offset."   </table>\n";

		if ($this->font != "")
		{
			$temp .= $this->font->generateHtmlForm($id."_font", $offset."   ");
		}
		else
		{
			$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=addfont&id=".$this->id."\">Font hinzuf&uuml;gen</a><br>\n";
		}
		if ($this->labelplacement != "")
		{
			$temp .= $this->labelplacement->generateHtmlForm($id."_labelplacement", $offset."   ");
		}
		else
		{
			$temp .= $offset."<a class='edit' href=\"sld_function_handler.php?function=addlabelplacement&id=".$this->id."\">Labelplacement hinzuf&uuml;gen</a><br>\n";
		}
		//Halo is not supported - removed from source
		if ($this->halo != "")
		{
			$temp .= $this->halo->generateHtmlForm($id."_halo", $offset."   ");
		}
		else
		{
			$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=addhalo&id=".$this->id."\">Halo hinzuf&uuml;gen</a><br>\n";
		}
		
		if ($this->fill != "")
		{
			$temp .= $this->fill->generateHtmlForm($id."_fill", $offset."   ");
		}
		else
		{
			$temp .= $offset."   <a class='edit' href=\"sld_function_handler.php?function=addfill&id=".$this->id."\">Fill hinzuf&uuml;gen</a><br>\n";
		}
		$temp .= $offset."  </td>\n";
		$temp .= $offset." </tr>\n";
		$temp .= $offset."</table>\n";
		$temp .= $offset."<br>\n";		
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
		if (isset($_REQUEST[$id."_label"]))
		{
			$this->label = new ParameterValue();
			$this->label->value = $_REQUEST[$id."_label"];
		}
		if (isset($_REQUEST[$id."_font"]))
		{
			$this->font = new Font();
			$this->font->generateObjectFromPost($id."_font");
		}
		if (isset($_REQUEST[$id."_labelplacement"]))
		{
			$this->labelplacement = new LabelPlacement();
			$this->labelplacement->generateObjectFromPost($id."_labelplacement");
		}
		if (isset($_REQUEST[$id."_halo"]))
		{
			$this->halo = new Halo();
			$this->halo->generateObjectFromPost($id."_halo");
		}
		if (isset($_REQUEST[$id."_fill"]))
		{
			$this->fill = new Fill();
			$this->fill->generateObjectFromPost($id."_fill");
		}
	}
}
?>
