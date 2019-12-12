<?php
# $Id: Graphic.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the Graphic-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class Graphic
{
	/**
	 * Array containing ExternalGraphic or Mark objects.
	 *
	 * @see ExternalGraphic
	 * @see Mark
	 * @var array
	 */
	var $externalgraphicsormarks = array();
	
	/**
	 * The opacity element from the xml-scheme.
	 *
	 * Not sure if this feature is supported by UMN-MapServer!
	 *
	 * @var string
	 */
	var $opacity = "";
	
	/**
	 * The size element from the xml-scheme.
	 * @var string
	 */
	var $size = "";
	
	/**
	 * The rotation element from the xml-scheme.
	 *
	 * Not sure if this feature is supported by UMN-MapServer!
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
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<Graphic>\n";
		foreach ($this->externalgraphicsormarks as $externalgraphicormark)
		{
			$temp .= $externalgraphicormark->generateXml($offset." ");
		}
		if ($this->opacity != "") $temp .= $offset." <Opacity>".htmlspecialchars($this->opacity)."</Opacity>\n";
		if ($this->size != "") $temp .= $offset." <Size>".htmlspecialchars($this->size)."</Size>\n";
		if ($this->rotation != "") $temp .= $offset." <Rotation>".htmlspecialchars($this->rotation)."</Rotation>\n";
		$temp .= $offset."</Graphic>\n";
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
		$temp .= $offset."<table border='0' cellpadding='2' cellspacing='0'>\n";
		$temp .= $offset."<tr valign='top'>\n";
		$temp .= $offset."<td>\n";
		$temp .= $offset."<span>\n";
		$temp .= $offset."Graphic:\n";
		$temp .= $offset."</span>\n";		
		$temp .= $offset."<br>\n";
		$temp .= $offset."<input type=\"hidden\" name=\"".$id."\" value=\"graphic\">\n";
		$temp .= $offset."<a class='edit' href=\"sld_function_handler.php?function=deletegraphic&id=".$this->parent."\">";
		$temp .= $offset."<img src='./img/minus.gif' border='0' alt='Rule l&ouml;schen'>&nbsp;l&ouml;schen";
		$temp .= $offset."</a>\n";
		$temp .= $offset."</td>\n";
		$temp .= $offset."<td valign='top'>\n";
		
			$temp .= $offset."   <table border='1' cellpadding='2' cellspacing='1'>\n";
			$temp .= $offset."    <tr>\n";
			$temp .= $offset."     <td class='edit_label_bg edit_label_text' style=\"width: 100px;\">\n";
			$temp .= $offset."      Opacity:\n";
			$temp .= $offset."     </td>\n";
			$temp .= $offset."     <td>\n";
			$temp .= $offset."      <input class='inputfield edit_label_text' name=\"".$id."_opacity\" value=\"".htmlspecialchars($this->opacity)."\">\n";
			$temp .= $offset."     </td>\n";
			$temp .= $offset."    </tr>\n";
			$temp .= $offset."    <tr>\n";
			$temp .= $offset."     <td class='edit_label_bg edit_label_text'>\n";
			$temp .= $offset."      Size:\n";
			$temp .= $offset."     </td>\n";
			$temp .= $offset."     <td>\n";
			$temp .= $offset."      <input class='inputfield edit_label_text' name=\"".$id."_size\" value=\"".htmlspecialchars($this->size)."\">\n";
			$temp .= $offset."     </td>\n";
			$temp .= $offset."    </tr>\n";
			$temp .= $offset."    <tr>\n";
			$temp .= $offset."     <td class='edit_label_bg edit_label_text'>\n";
			$temp .= $offset."      Rotation:\n";
			$temp .= $offset."     </td>\n";
			$temp .= $offset."     <td>\n";
			$temp .= $offset."      <input class='inputfield edit_label_text' name=\"".$id."_rotation\" value=\"".htmlspecialchars($this->rotation)."\">\n";
			$temp .= $offset."     </td>\n";
			$temp .= $offset."    </tr>\n";
			$temp .= $offset."    <tr>\n";
			$temp .= $offset."     <td valign='top'>\n";
			$temp .= $offset."      <select class=\"edit_label_text\" name=\"".$id."_newexternalgraphicormark\">\n";
			$temp .= $offset."       <option value=\"mark\">Mark</option>\n";
			$temp .= $offset."       <option value=\"externalgraphic\">ExternalGraphic</option>\n";
			$temp .= $offset."      </select>\n";
			$temp .= $offset."     </td>\n";			
			$temp .= $offset."     <td>\n";
			$temp .= $offset."    &nbsp;\n";											
			$temp .= $offset."     </td>\n";						
			$temp .= $offset."    </tr>\n";
			$temp .= $offset."    <tr>\n";
			$temp .= $offset."     <td colspan='2'>\n";
			$externalgraphicormark_id = 0;
			foreach ($this->externalgraphicsormarks as $externalgraphicormark)
			{
				$temp .= $externalgraphicormark->generateHtmlForm($id."_externalgraphicormark_".$externalgraphicormark_id, $offset."");
				$externalgraphicormark_id++;
			}									
			$temp .= $offset."     </td>\n";									
			$temp .= $offset."    </tr>\n";
			$temp .= $offset."    <tr>\n";
			$temp .= $offset."     <td>\n";
			$temp .= $offset."<input class='edit' type=\"button\" value=\"hinzuf&uuml;gen\"";
			$temp .= " onClick=\"url='sld_function_handler.php?function=addexternalgraphicormark&id=".$this->id."&externalgraphicormark=';";
			$temp .= " url += ".$id."_newexternalgraphicormark.value;";
			$temp .= " location.href = url;\"";
			$temp .= ">\n";			
			$temp .= $offset."     </td>\n";						
			$temp .= $offset."     <td>\n";
			$temp .= $offset."    &nbsp;\n";
			$temp .= $offset."     </td>\n";			
			$temp .= $offset."    </tr>\n";
			$temp .= $offset."   </table>\n";
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
		$countExternalGraphicsOrMarks = 0;
		while (isset($_REQUEST[$id."_externalgraphicormark_".$countExternalGraphicsOrMarks]))
		{
			$externalgraphicormark = "";
			if ($_REQUEST[$id."_externalgraphicormark_".$countExternalGraphicsOrMarks] == "mark")
			{
				$externalgraphicormark = new Mark();
			}
			else if ($_REQUEST[$id."_externalgraphicormark_".$countExternalGraphicsOrMarks] == "externalgraphic")
			{
				$externalgraphicormark = new ExternalGraphic();
			}
			$externalgraphicormark->generateObjectFromPost($id."_externalgraphicormark_".$countExternalGraphicsOrMarks);
			$this->externalgraphicsormarks[] = $externalgraphicormark;
			$countExternalGraphicsOrMarks++;
		}
		if (isset($_REQUEST[$id."_opacity"]))
		{
			$this->opacity = $_REQUEST[$id."_opacity"];
		}
		if (isset($_REQUEST[$id."_size"]))
		{
			$this->size = $_REQUEST[$id."_size"];
		}
		if (isset($_REQUEST[$id."_rotation"]))
		{
			$this->rotation = $_REQUEST[$id."_rotation"];
		}
	}
	function addExternalGraphicOrMark($externalgraphicormark)
	{
		if ($externalgraphicormark == "externalgraphic")
		{
			$this->externalgraphicsormarks[] = new ExternalGraphic();
		}
		else if ($externalgraphicormark == "mark")
		{
			$test = new Mark();
			$this->externalgraphicsormarks[] = $test;
		}
	}
	function deleteExternalGraphicOrMark($index)
	{
		array_splice($this->externalgraphicsormarks, $index, 1);
	}
}
?>