<?php
# $Id: StyledLayerDescriptor.php 2412 2008-04-23 16:14:30Z christoph $
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
 * This file contains the class definitions for the sld-editor.
 *
 * This class is the implementation of the StyledLayerDescriptor-element of the sld-xml-scheme.
 * This element builds up the root of every sld-document.
 * This class is directly referred to in the sld_main.php.
 *
 * @package sld_classes
 * @author Markus Krzyzanowski, Design for all HTML-output by Bao Ngan
 */
class StyledLayerDescriptor
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
	 * The abstract attribute from the xml-scheme.
	 * @var string
	 */
	var $abstract = "";
	/**
	 * Array containing the layers of the sld.
	 * @see NamedLayer
	 * @var array
	 */
	var $layers = array();
	/**
	 * The version attribute from the xml-scheme.
	 * @var string
	 */
	var $version = "";
	
	/**
	 * Index of this object in the $_SESSION("sld_objects") array.
	 * @var int
	 */
	var $id = "";
	/**
	 * Index of this object's parent object in the $_SESSION("sld_objects") array.
	 * @var int
	 */
	var $parent = "";
	
	/**
	 * Generates the sld-document as an xml-string and returns it.
	 *
	 * Calls the generateXml-function of its child objects.
	 * This object only has layers as childs, all other child-elements of
	 * StyledLayerDescriptor are modelled as primitive datatypes
	 * 
	 * @param string $offset string that should be added at the beginning
	 * of every line of xml that is being created. Should only contain
	 * a number of whitespaces to format the output and provide a
	 * good readability.
	 *
	 * @return string the created sld-document
	 */
	function generateXml($offset = "")
	{
		$temp = $offset."<StyledLayerDescriptor";
		if ($this->version != "") $temp .= " version=\"".htmlspecialchars($this->version)."\"";
		$temp .= " xsi:schemaLocation=\"http://www.opengis.net/sld StyledLayerDescriptor.xsd\"";
		$temp .= " xmlns=\"http://www.opengis.net/sld\"";
		$temp .= " xmlns:ogc=\"http://www.opengis.net/ogc\""; 
		$temp .= " xmlns:xlink=\"http://www.w3.org/1999/xlink\"";
		$temp .= " xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"";
		$temp .= ">\n";
		if ($this->name != "") $temp .= $offset." <Name>".htmlspecialchars($this->name)."</Name>\n";
		if ($this->title != "") $temp .= $offset." <Title>".htmlspecialchars($this->title)."</Title>\n";
		if ($this->abstract != "") $temp .= $offset." <Abstract>".htmlspecialchars($this->abstract)."</Abstract>\n";

		
		foreach($this->layers as $layer)
		{
			$temp .= $layer->generateXml($offset." ");
		}
		$temp .= $offset."</StyledLayerDescriptor>\n";
		return $temp;
	}
	
	
	/**
	 * Generates a html-form-fragment
	 * that contains form-elements (e.g. input) for every member of this class.
	 * Some of these are hidden fields and so they are not editable by the user.
	 * The reason for this is that some values should not be changed (such as the layer name)
	 * or they are to complicated for end-users that are not familiar to the sld-xml-scheme.
	 * 
	 * Calls the generateHtmlForm-function of its child objects.
	 *
	 * @param string $id string identifying the form-elements belonging to this object
	 * @param string $offset string for formatting the output
	 * @return string the created html-form-fragment
	 */
	function generateHtmlForm($id, $offset = "")
	{
		$temp = "";
		//Commented out, because these options should not be changed by the end user
		//$temp .= $offset."<table style=\"border: 1px solid black\">\n";
		//$temp .= $offset."<tr>\n";
		//$temp .= $offset."<td>Name:</td>\n";
		//$temp .= $offset."<td><input name=\"name\" value=\"".$this->name."\"></td>\n";
		//$temp .= $offset."</tr>\n";
		//$temp .= $offset."<tr>\n";
		//$temp .= $offset."<td>Title:</td>\n";
		//$temp .= $offset."<td><input name=\"title\" value=\"".$this->title."\"></td>\n";
		//$temp .= $offset."</tr>\n";
		//$temp .= $offset."<tr>\n";
		//$temp .= $offset."<td>Abstract:</td>\n";
		//$temp .= $offset."<td><input name=\"abstract\" value=\"".$this->abstract."\"></td>\n";
		//$temp .= $offset."</tr>\n";
		//$temp .= $offset."<tr>\n";
		//$temp .= $offset."<td>Version:</td>\n";
		//$temp .= $offset."<td><input name=\"version\" value=\"".$this->version."\"></td>\n";
		//$temp .= $offset."</tr>\n";
		//$temp .= $offset."</table>\n";
		//$temp .= $offset."<br>\n";
		
		//Hidden fields to hold the values
		$temp .= $offset."<input type=\"hidden\" name=\"name\" value=\"".htmlspecialchars($this->name)."\">\n";
		$temp .= $offset."<input type=\"hidden\" name=\"title\" value=\"".htmlspecialchars($this->title)."\">\n";
		$temp .= $offset."<input type=\"hidden\" name=\"abstract\" value=\"".htmlspecialchars($this->abstract)."\">\n";
		$temp .= $offset."<input type=\"hidden\" name=\"version\" value=\"".htmlspecialchars($this->version)."\">\n";
		
		$layer_id = 0;
		foreach ($this->layers as $layer)
		{
			$temp .= $layer->generateHtmlForm("layer_".$layer_id, $offset." ");
			$layer_id++;
		}
		return $temp;
	}
	
	/**
	 * Populates the member fields of a new object from the data in the http-post-request
	 * to rebuild the object after the submission of the html-form.
	 *
	 * creates its own child objects from the post parameters and calls their
	 * generateObjectFromPost(...) function
	 *
	 * @param string $id string that contains a prefix for the html-form-fields
	 * that is common to all of the fields belonging to this object
	 * this parameter has no use for this object, because it is only called without a value
	 * in other classes this parameter should have the same value, that was used in
	 * generateHtmlForm(...) for the $id to create the html-form
	 *
	 */
	function generateObjectFromPost($id = "")
	{
		$this->version = $_REQUEST[$id."version"];
		$this->name = $_REQUEST[$id."name"];
		$this->title = $_REQUEST[$id."title"];
		$this->abstract = $_REQUEST[$id."abstract"];
		$countLayers = 0;
		while (isset($_REQUEST[$id."layer_".$countLayers]))
		{
			if ($_REQUEST[$id."layer_".$countLayers] == "namedlayer")
			{
				$layer = new NamedLayer();
			}
			else
			{
				//Todo evtl: userlayer erstellen
				$layer = new NamedLayer();
			}
			$layer->generateObjectFromPost($id."layer_".$countLayers);
			$this->layers[] = $layer;
			$countLayers++;
		}
	}
}

/**
 * Loads classes by name
 * 
 * If the php interpreter finds a class that is needed in a module or class 
 * and it is not yet included via include, then it tries to load this class 
 * with the given name in the current context. No need for includes ... 
 * 
 * @param string $class_name
 */
function __autoload($class_name) {
	if (file_exists(dirname(__FILE__). "/{$class_name}.php"))
    	require_once $class_name . '.php';
}
?>