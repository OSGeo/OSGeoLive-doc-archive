<?php
# $Id: NamedLayer.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the NamedLayer-element
 *
 * @package sld_classes
 * @see StyledLayerDescriptor::$layers
 * @author Markus Krzyzanowski
 */
class NamedLayer extends StyledLayerDescriptor
{
	/**
	 * The name attribute from the xml-scheme.
	 * @var string
	 */
	var $name = "";
	//Probably not supported by Mapserver - TODO
	//var $layerfeatureconstraints = "";
	/**
	 * Array containing the style-objects of the layer.
	 *
	 * @see UserStyle
	 * @var array
	 */
	var $styles = array();
	// $id and $parent for the $objects session array
	
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
		$temp = $offset."<NamedLayer>\n";
		$temp .= $offset." <Name>".htmlspecialchars($this->name)."</Name>\n";
		foreach($this->styles as $style)
		{
			$temp .= $style->generateXml($offset." ");
		}
		$temp .= $offset."</NamedLayer>\n";
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
		$temp .= $offset."<table>\n";
		$temp .= $offset." <tr>\n";
		$temp .= $offset."  <td class='edit_label_bg edit_label_text_head'>\n";
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."\" value=\"namedlayer\">\n";
		$temp .= $offset."   <em>&nbsp;&nbsp;Layer: ".htmlspecialchars($this->name)."</em>\n";
		$temp .= $offset."   <input type=\"hidden\" name=\"".$id."_name\" value=\"".htmlspecialchars($this->name)."\">\n";			
		$style_id = 0;
		foreach ($this->styles as $style)
		{
			$temp .= $style->generateHtmlForm($id."_style_".$style_id, $offset."    ");
			$style_id++;
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
		$this->name = $_REQUEST[$id."_name"];
		$countStyles = 0;
		while (isset($_REQUEST[$id."_style_".$countStyles]))
		{
			$style = "";
			if ($_REQUEST[$id."_style_".$countStyles] == "userstyle")
			{
				$style = new UserStyle();
			}
			else
			{
				//Todo evtl: namedstyle erstellen
				//NamedStyle is currently not supported by UMN-MapServer!
				//$style = new NamedStyle();
			}
			$style->generateObjectFromPost($id."_style_".$countStyles);
			$this->styles[] = $style;
			$countStyles++;
		}
	}
}

?>