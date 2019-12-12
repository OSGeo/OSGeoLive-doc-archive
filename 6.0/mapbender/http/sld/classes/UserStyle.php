<?php
# $Id: UserStyle.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the UserStyle-element
 *
 * @package sld_classes
 * @author Markus Krzyzanowski
 */
class UserStyle extends NamedLayer
{
	/**
	 * Array containing the FeatureTypeStyle-objects.
	 *
	 * @see FeatureTypeStyle
	 * @var string
	 */
	var $featuretypestyles = array();
	
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
		$temp = $offset."<UserStyle>\n";
		foreach($this->featuretypestyles as $style)
		{
			$temp .= $style->generateXml($offset." ");
		}
		$temp .= $offset."</UserStyle>\n";
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
		$temp .= $offset."<input type=\"hidden\" name=\"".$id."\" value=\"userstyle\">\n";
		$featuretypestyle_id = 0;
		foreach ($this->featuretypestyles as $featuretypestyle)
		{
			$temp .= $featuretypestyle->generateHtmlForm($id."_featuretypestyle_".$featuretypestyle_id, $offset);
			$featuretypestyle_id++;
		}
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
		$countFeatureTypeStyles = 0;
		while (isset($_REQUEST[$id."_featuretypestyle_".$countFeatureTypeStyles]))
		{
			$featuretypestyle = new FeatureTypeStyle();
			$featuretypestyle->generateObjectFromPost($id."_featuretypestyle_".$countFeatureTypeStyles);
			$this->featuretypestyles[] = $featuretypestyle;
			$countFeatureTypeStyles++;
		}
	}
}

?>