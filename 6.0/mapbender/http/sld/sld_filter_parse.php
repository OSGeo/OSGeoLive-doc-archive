<?php
# $Id: sld_filter_parse.php 2412 2008-04-23 16:14:30Z christoph $
# http://www.mapbender.org/index.php/SLD/
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
 * This file parses the filter expressions and creates the corresponding objects.
 *
 * @package filter_editor
 * @author Markus Krzyzanowski
 */



include_once(dirname(__FILE__)."/classes/StyledLayerDescriptor.php");
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");

/**
 * Parses a given filter expression and creates the object structure.
 * @param string $data filter expression that has to be parsed
 * @return Filter root object of the filter expression
 */
function parseFilter($data)
{
	$xml_parser = xml_parser_create_ns();
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parse_into_struct($xml_parser, $data, $vals, $index);
	xml_parser_free($xml_parser);
	
	
	$filter;
	$parent = array();
	$objects = array();
	$parentactual = 0;
	
	for ($i=0; $i<count($vals); $i++)
	{
		$element = $vals[$i];
		$tag = $element["tag"];
		$tagname=strtoupper($tag);
		switch($tagname)
		{
			case "FILTER":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = false;
					$parentactual = 0;
					$filter = new Filter();
					$filter->id = 0;
					$filter->parent = false;
					$objects[] = &$filter;
					if ($element["type"] == "complete")
					{
						$parentactual = false;
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "ELSEFILTER":
				if ($element["type"] == "complete")
				{
					$parent[] = false;
					$parentactual = 0;
					$filter = new ElseFilter();
					$filter->id = 0;
					$filter->parent = false;
					$objects[] = &$filter;
				}
				break;
			case "OR":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new BinaryLogicOp("Or");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "AND":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new BinaryLogicOp("And");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "NOT":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new UnaryLogicOp("Not");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISEQUALTO":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new BinaryComparisonOp("PropertyIsEqualTo");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISNOTEQUALTO":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new BinaryComparisonOp("PropertyIsNotEqualTo");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISGREATERTHAN":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new BinaryComparisonOp("PropertyIsGreaterThan");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISGREATERTHANOREQUALTO":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new BinaryComparisonOp("PropertyIsGreaterThanOrEqualTo");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISLESSTHAN":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new BinaryComparisonOp("PropertyIsLessThan");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISLESSTHANOREQUALTO":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new BinaryComparisonOp("PropertyIsLessThanOrEqualTo");
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISLIKE":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new PropertyIsLike();
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[count($parent)-1]->wildCard = $element["attributes"]["wildCard"];
					$objects[count($parent)-1]->singleChar = $element["attributes"]["singleChar"];
					$objects[count($parent)-1]->escape = $element["attributes"]["escape"];
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISNULL":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new PropertyIsNull();
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "PROPERTYISBETWEEN":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new PropertyIsBetween();
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[$parentactual]->operations[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;

			case "LITERAL":
				if ($element["type"] == "complete")
				{
					if ($boundary == "upper")
					{
						$objects[$parentactual]->upperBoundary = $element["value"];
					}
					else if ($boundary == "lower")
					{
						$objects[$parentactual]->lowerBoundary = $element["value"];
					}
					else
					{
						$objects[$parentactual]->ogcLiteral = $element["value"];
					}
				}
				break;
			case "PROPERTYNAME":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->ogcPropertyName = $element["value"];
				}
				break;
			case "LOWERBOUNDARY":
				if ($element["type"] == "open")
				{
					$boundary = "lower";
				}
				else if ($element["type"] == "close")
				{
					$boundary = "";
				}
				break;
			case "UPPERBOUNDARY":
				if ($element["type"] == "open")
				{
					$boundary = "upper";
				}
				else if ($element["type"] == "close")
				{
					$boundary = "";
				}
				break;
		}
	}
	
	$_SESSION["sld_filter_objects"] = $objects;
	
	return $filter;
}
?>