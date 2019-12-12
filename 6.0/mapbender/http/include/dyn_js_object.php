<?php
# $Id: dyn_js.php 3850 2009-04-03 09:02:12Z christoph $
# $Header: /cvsroot/mapbender/mapbender/http/classes/class_wfs.php,v 1.15 2006/03/09 13:55:46 uli_rothstein Exp $
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

require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");

if (isset($gui_id)) {
	$sql = "SELECT * FROM gui_element_vars WHERE fkey_e_id = $1 AND fkey_gui_id = $2 and var_type='var'";
	$v = array($e_id, $gui_id);
	$t = array('s', 's');
   	$res = db_prep_query($sql, $v, $t);
	$arrays = array();
	$varArray = array();
	while ($row = db_fetch_array($res)) {
		if (mb_strpos($row["var_name"], "[")) {

			//
			// backwards compatibility for var names like name[0], name[1] etc
			//
			$arrayname = mb_substr($row["var_name"], 0, mb_strpos($row["var_name"], "["));
			
			if (!in_array($arrayname, $arrays)) {
				$arrays[]= $arrayname;
				$varArray[]= $arrayname  . ": []";
			}
			else {
				for ($i = 0; $i < count($varArray); $i++) {
					if (mb_substr($varArray[$i], 0, mb_strlen($arrayname) + 1) === $arrayname . ":") {
						if (is_numeric(stripslashes($row["var_value"]) || 
							strpos(stripslashes($row["var_value"]), "[") === 0 || 
							strpos(stripslashes($row["var_value"]), "{") === 0)) {
							
							$varArray[$i] = substr_replace(
								$varArray[$i], 
								$row["var_name"].": ".stripslashes($row["var_value"]) . "]",
								-1);
						}
						else {
							$varArray[$i] = substr_replace(
								$varArray[$i], 
								$row["var_name"].": '".stripslashes($row["var_value"]) . "']",
								-1);
						}
					}
				}
			}
		}
		if (is_numeric(stripslashes($row["var_value"]))) {
			$varArray[]= $row["var_name"].": ".stripslashes($row["var_value"]);
		}
		elseif (strpos(stripslashes($row["var_value"]), "[") === 0 || 
				strpos(stripslashes($row["var_value"]), "{") === 0) {
			$varArray[]= $row["var_name"].": ".stripslashes($row["var_value"]);
		}
		else {
			$varArray[]= $row["var_name"].": '" . 
				str_replace(
					array('"',"'", "\r", "\n", "\0"), 
					array('\"','\\\'','\r', '\n', '\0'), 
					stripslashes($row["var_value"])
				) .	"'";
		}
	}
	echo "$.extend(options, {";
	echo implode(",\n", $varArray);
	echo "});";
}
else {
	$e = new mb_exception("Application ID not set while retrieving element vars of module " . $e_id);
}
?>