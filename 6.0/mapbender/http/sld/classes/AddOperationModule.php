<?php
# $Id: AddOperationModule.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Template class that only creates the source code for the function of adding an operation
 * is used whereever an operation can be added. Simpyl change this class and the change
 * will affect all the appearences of the add operation function.
 *
 * @package filter_classes
 * @author Markus Krzyzanowski
 */
class AddOperationModule
{
	/**
	 * stores the id of the referring object (the id member field of the object!)
	 */
	var $objId = "";
	/**
	 * stores the prefix of the referring object's html-form-fields
	 */
	var $htmlId = "";
	
	/**
	 * constructor that sets the necessary values ov the member fields
	 */
	function AddOperationModule($objId, $htmlId)
	{
		$this->objId = $objId;
		$this->htmlId = $htmlId;
	}
	
	/**
	 * creates the html-form-fragment for this object
	 *
	 * @param string $offset string used for formatting the output
	 * @return string containing the html-form-fragment
	 */
	function generateHtmlForm($offset = "")
	{
		$temp = "";
		$temp .= $offset."<select name=\"".$this->htmlId."_newoperation\">\n";
		$temp .= $offset." <option value=\"and\">AND - logisches UND</option>\n";
		$temp .= $offset." <option value=\"or\">OR - logisches ODER</option>\n";
		$temp .= $offset." <option value=\"not\">NOT - logisches NICHT</option>\n";
		$temp .= $offset." <option value=\"propertyisequalto\">Eigenschaft = x</option>\n";
		$temp .= $offset." <option value=\"propertyisnotequalto\">Eigenschaft != x</option>\n";
		$temp .= $offset." <option value=\"propertyisgreaterthan\">Eigenschaft &gt; x</option>\n";
		$temp .= $offset." <option value=\"propertyisgreaterthanorequalto\">Eigenschaft &gt;= x</option>\n";
		$temp .= $offset." <option value=\"propertyislessthan\">Eigenschaft &lt; x</option>\n";
		$temp .= $offset." <option value=\"propertyislessthanorequalto\">Eigenschaft &lt;= x</option>\n";
		$temp .= $offset." <option value=\"propertyislike\">Eigenschaft �hnlich x</option>\n";
		$temp .= $offset." <option value=\"propertyisnull\">Eigenschaft nicht definiert</option>\n";
		$temp .= $offset." <option value=\"propertyisbetween\">x &lt;= Eigenschaft &lt;= y</option>\n";
		$temp .= $offset."</select>\n";
		$temp .= $offset."<span class=\"edit hand\"";
		$temp .= " onClick=\"url='?function=addoperation&id=".$this->objId."&operation=';";
		$temp .= " url += document.editFilter.".$this->htmlId."_newoperation.value;";
		$temp .= " location.href = url;\"";
		$temp .= ">";
		$temp .= "<img src='./img/plus.gif' border='0'>&nbsp;hinzuf&uuml;gen\n</span>";
		return $temp;
	}
}
?>