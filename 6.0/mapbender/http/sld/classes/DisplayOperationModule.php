<?php
# $Id: DisplayOperationModule.php 2412 2008-04-23 16:14:30Z christoph $
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
 * Template Class that is used to display the operations
 * creates the hmtl source code for the operations
 * similar to the AddOperationModule
 *
 * @package filter_classes
 * @author Markus Krzyzanowski
 */
class DisplayOperationModule
{
	/**
	 * function that creates the html-form-fragment sourcecode
	 * 
	 * @param $offset string whitespaces that should be added in every row of sourcecode for formatting
	 * @param $operations array containing the operations for which the source should be created
	 * @return string the created code
	 */
	function generateHtmlForm($offset = "", $operations, $id)
	{
		$temp = "";
		$operation_id = 0;
		$max = count($operations);
		foreach ($operations as $operation)
		{
			if ($id == "")
			{
				$temp .= $operation->generateHtmlForm($id."operation_".$operation_id, $offset);
			}
			else
			{
				$temp .= $operation->generateHtmlForm($id."_operation_".$operation_id, $offset);
			}
			//Display emtpy row or line to separate the operations
			if ($operation_id+1 < $max)
			{
				$temp .= $offset."<tr>\n";
				$temp .= $offset." <td colspan=\"4\">\n";
				$temp .= $offset."  <hr style=\"color: black\">\n";
				$temp .= $offset." </td>\n";
				$temp .= $offset."</tr>\n";	
			}
			$operation_id++;
		}
		return $temp;
	}
}
?>