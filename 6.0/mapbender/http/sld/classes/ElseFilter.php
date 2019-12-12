<?php
# $Id: ElseFilter.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Implementation of the ElseFilter-element.
 * An ElseFilter can be used instead of a Filter.
 * An ElseFilter is always true an should be used to group those
 * entities that do not have a specific rule for themselves.
 *
 * @package filter_classes
 * @author Markus Krzyzanowski
 */
class ElseFilter extends Rule
{
	/**
	 * creates the xml for this object and its child objects
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the xml-fragment
	 */
	function generateXml($offset = "")
	{
		$temp = "";
		$temp .= $offset."<ElseFilter />\n";
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
		$temp .= $offset."<input type=\"hidden\" name=\"elsefilter\">\n";
		$temp .= $offset."<table>\n";
		$temp .= $offset." <tr>\n";
		$temp .= $offset."  <td>\n";
		$temp .= $offset."   ElseFilter:<br>\n";
		$temp .= $offset."   <a class=\"edit\" href=\"sld_edit_filter.php?function=deletefilter\">";
		$temp .= "<img src='./img/minus.gif' border='0'>&nbsp;l&ouml;schen</a>\n";
		$temp .= $offset."  </td>\n";
		$temp .= $offset." </tr>\n";
		$temp .= $offset."</table>\n";
		return $temp;
	}
	
	
}
?>