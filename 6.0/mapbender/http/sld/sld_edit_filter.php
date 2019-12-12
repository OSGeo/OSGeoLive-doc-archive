<?php
# $Id: sld_edit_filter.php 2412 2008-04-23 16:14:30Z christoph $
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
 * This file contains the source of the filter-expression-editor-module.
 * This was separated from the sld-editor's parsing and functionhandling
 * due to readability reasons.
 * Contains all the source for displaying the form and the functionhandling.
 *
 * @package filter_editor
 * @author Markus Krzyzanowski
 */




include_once(dirname(__FILE__)."/classes/StyledLayerDescriptor.php");
require_once(dirname(__FILE__)."/sld_config.php");
include_once(dirname(__FILE__)."/sld_filter_parse.php");

//get the neccessary variables from the request or from the session
//if from request set up the session variables

$sld_objects = $_SESSION["sld_objects"];

if ( isset($_REQUEST["sld_form_element_id"]) && isset($_REQUEST["sld_objects_rule_id"]) )
{
	$first_load = 1;
	$sld_form_element_id = $_REQUEST["sld_form_element_id"];
	$_SESSION["sld_form_element_id"] = $sld_form_element_id;
	
	$sld_objects_rule_id = $_REQUEST["sld_objects_rule_id"];
	$_SESSION["sld_objects_rule_id"] = $sld_objects_rule_id;
	
	$filter = $sld_objects[$sld_objects_rule_id]->filter;
	$_SESSION["sld_filter"] = $filter;
}
else
{
	$first_load = 0;
	$sld_form_element_id = $_SESSION["sld_form_element_id"];
	$sld_objects_rule_id = $_SESSION["sld_objects_rule_id"];
	$filter = $_SESSION["sld_filter"];
}

//Parse the Filter Expression
$filterObj = parseFilter($filter);
if ($filterObj == "")
{
	//$filterObj = new Filter();
}



//Function handling
if (isset($_REQUEST["function"]))
{
	$function = $_REQUEST["function"];
	//Handle the requested functions
	if ($function == "addoperation")
	{
		if ( isset($_REQUEST["id"]) && isset($_REQUEST["operation"]) )
		{
			$operation = $_REQUEST["operation"];
			
			switch(strtoupper($operation))
			{
				case "OR":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new BinaryLogicOp("Or");
					break;
				case "AND":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new BinaryLogicOp("And");
					break;
				case "NOT":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new UnaryLogicOp("Not");
					break;
				case "PROPERTYISEQUALTO":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new BinaryComparisonOp("PropertyIsEqualTo");
					break;
				case "PROPERTYISNOTEQUALTO":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new BinaryComparisonOp("PropertyIsNotEqualTo");
					break;
				case "PROPERTYISGREATERTHAN":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new BinaryComparisonOp("PropertyIsGreaterThan");
					break;
				case "PROPERTYISGREATERTHANOREQUALTO":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new BinaryComparisonOp("PropertyIsGreaterThanOrEqualTo");
					break;
				case "PROPERTYISLESSTHAN":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new BinaryComparisonOp("PropertyIsLessThan");
					break;
				case "PROPERTYISLESSTHANOREQUALTO":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new BinaryComparisonOp("PropertyIsLessThanOrEqualTo");
					break;
				case "PROPERTYISLIKE":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new PropertyIsLike();
					break;
				case "PROPERTYISNULL":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new PropertyIsNull();
					break;
				case "PROPERTYISBETWEEN":
					$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->operations[] = new PropertyIsBetween();
					break;
			}
			$_SESSION["sld_filter"] = $_SESSION["sld_filter_objects"][0]->generateXml();
		}
	}
	else if ($function == "deleteoperation")
	{
		if ( isset($_REQUEST["id"]) && isset($_REQUEST["number"]) )
		{
			$_SESSION["sld_filter_objects"][$_REQUEST["id"]]->deleteOperation($_REQUEST["number"]);
			$_SESSION["sld_filter"] = $_SESSION["sld_filter_objects"][0]->generateXml();
		}
	}
	else if ($function == "addfilter")
	{
		if ( isset($_REQUEST["type"]) )
		{
			if ($_REQUEST["type"] == "filter")
			{
				$_SESSION["sld_filter_objects"][0] = new Filter();
				$_SESSION["sld_filter"] = $_SESSION["sld_filter_objects"][0]->generateXml();
			}
			else if ($_REQUEST["type"] == "elsefilter")
			{
				$_SESSION["sld_filter_objects"][0] = new ElseFilter();
				$_SESSION["sld_filter"] = $_SESSION["sld_filter_objects"][0]->generateXml();
			}
		}
	}
	else if ($function == "deletefilter")
	{
		$_SESSION["sld_filter_objects"][0] = "";
		$_SESSION["sld_filter"] = "";
	}
	else if ($function == "save")
	{
		if( isset($_REQUEST["filter"]) )
		{
			$filter = new Filter();
			$filter->generateObjectFromPost();
			$_SESSION["sld_filter"] = $filter->generateXml();
		}
		else if( isset($_REQUEST["elsefilter"]) )
		{
			$filter = new ElseFilter();
			$_SESSION["sld_filter"] = $filter->generateXml();
		}
		else
		{
			$_SESSION["sld_filter"] = "";
		}
	}
	else
	{
		echo "Die Funktion: ".$function." wird nicht unterst�tzt.";
		exit();
	}
	
	
	header("Location: sld_edit_filter.php?".$urlParameters);
	exit();
}
else
{
	//Write the new filter expression to the sld objects
	if ($filterObj != "")
	{
		$sld_objects[$sld_objects_rule_id]->filter = $filterObj->generateXml();
	}
	else
	{
		$sld_objects[$sld_objects_rule_id]->filter = "";
	}
	//display the html form
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
        echo "<html>\n";
        echo "<head>\n";
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
        echo "<meta HTTP-EQUIV=\"CACHE-CONTROL\" CONTENT=\"NO-CACHE\">\n";
        echo "<META HTTP-EQUIV=\"PRAGMA\" CONTENT=\"NO-CACHE\">\n";
	echo "<title>Filter-Editor</title>\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../css/sldEditor.css\">\n";
	echo "<script Language=\"JavaScript\">\n";
	
	//write the filter expression into the hidden field in the sld editor on load automatically
	echo "function setFilter()\n";
	echo "{\n";
	echo "var filter = document.getElementById(\"filter_textarea\").value;\n";
	echo "window.opener.document.getElementById(\"sld_editor_form\").".$sld_form_element_id.".value = filter;\n";
	echo "//window.opener.document.getElementById(\"sld_editor_form\").submit();\n";
	echo "}\n";
	
	echo "function updateFilter()\n";
	echo "{\n";
	echo "//var filter = document.getElementById(\"filter_textarea\").value;\n";
	echo "//window.opener.document.getElementById(\"sld_editor_form\").".$sld_form_element_id.".value = filter;\n";
	echo "window.opener.document.getElementById(\"sld_editor_form\").submit();\n";
	echo "}\n";	
	
	echo "</script>\n";
	echo "</head>\n";
	echo "<body";
	if ($first_load != 1) echo " onLoad=\"setFilter();\"";
	echo ">\n";
	
	echo "<form name=\"editFilter\" action=\"sld_edit_filter.php?".$urlParameters."\" method=\"post\">\n";
	
	echo "<table cellspacing=\"0\" cellpadding=\"0\">\n";
	echo "<tr align=\"center\"><td class=\"bg2 text3\">Filter bearbeiten</td></tr>\n";
	echo "<tr><td class=\"line_left2 line_down2 line_right2 bg text1\">\n";
	
	if ($filterObj != "")
	{
		echo "<input type=\"hidden\" name=\"function\" value=\"save\">\n";
		echo $filterObj->generateHtmlForm();
		echo "<input class=\"edit hand\" type=\"submit\" value=\"Aktualisieren\">\n";
		echo "<input class=\"edit hand\" type=\"button\" value=\"&Auml;nderungen speichern\" onclick=\"updateFilter()\">\n";
		
	}
	else
	{
		echo "Filter oder ElseFilter?<br>";
		echo "<input type=\"radio\" name=\"type\" value=\"filter\" onClick=\"submit()\">Filter<br>\n";
		echo "<input type=\"radio\" name=\"type\" value=\"elsefilter\" onClick=\"submit()\">ElseFilter<br>\n";
		echo "<input type=\"hidden\" name=\"function\" value=\"addfilter\">\n";
	}
	echo "</td></tr>\n";
	
	echo "<tr><td>&nbsp;</td></tr>\n";
	
	echo "<tr align=\"center\"><td class=\"bg2 text3\">Vorschau</td></tr>\n";
	echo "<tr><td class=\"line_left2 line_down2 line_right2 bg text1\">\n";
	
	if ($filterObj != "")
	{
		echo "<pre>\n";
		echo htmlspecialchars($filterObj->generateXml());
		print_r ($_SESSION["sld_filter_objects"]);
		echo "</pre>\n";
		echo "<textarea  id=\"filter_textarea\" style=\"visibility:hidden;\">\n";
		echo htmlspecialchars($filterObj->generateXml());
		echo "</textarea>\n";
	}
	else
	{
		echo "&nbsp;\n";
	}
	
	echo "</td></tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	
	echo "</body>\n";
	echo "</html>";

}
?>