<?php
# $Id: mb_validatePermission.php 6728 2010-08-10 08:31:29Z christoph $
# http://www.mapbender.org/index.php/mb_validatePermission.php
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

require(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

$admin = new administration();

//
// check if GUI id and element id are set
//
$msg = "missing in " .  $_SERVER["SCRIPT_NAME"];
if (!isset($gui_id)) {
	$e = new mb_exception("GUI id " . $msg);
	die();
}
if (!isset($e_id)) {
	$e = new mb_exception("Element id " . $msg);
	die();
}

//
// check if the user is allowed to access this module
//
$isAllowed = $admin->getModulPermission(Mapbender::session()->get("mb_user_id"), $gui_id, $e_id);

//
// if the module is an iframe, also check if the filename matches the
// filename of the GUI element with the given e_id
//
// (if SCRIPT_NAME is "map.php", we trust the script.)
//
if (!preg_match("/^.*\/javascripts\/map\.php$/", $_SERVER["SCRIPT_NAME"])) {

	$isCorrectScript = true;
	
	$sql = "SELECT e_element FROM gui_element WHERE e_id = $1 AND fkey_gui_id = $2";
	$v = array($e_id, $gui_id);
	$t = array("s", "s");
	$res = db_prep_query($sql, $v, $t);
	while ($row = db_fetch_array($res)) {
		if (!$admin->checkModulePermission_new(Mapbender::session()->get("mb_user_id"), $_SERVER["SCRIPT_NAME"], $row["e_element"])) {
			$isCorrectScript = false;
			break;
		}
	}
//	$e = new mb_notice($e_id . ": isAllowed: " . $isAllowed . ", isCorrectScript: " . $isCorrectScript);
	$isAllowed = $isAllowed && $isCorrectScript;
}

//
// If the user is not allowed to access the module, return to the login screen.
//
if (!$isAllowed) {
	$msg = "mb_validatePermission.php: User " . Mapbender::session()->get("mb_user_id") . " is not allowed to access ".
			"module " . $e_id;
	$e = new mb_exception($msg);
	header("Location: ".LOGIN);
	die();
}

$e = new mb_notice("mb_validatePermission.php: checking file " . $_SERVER["SCRIPT_NAME"] . "...permission valid.");

//
// delete global variables
//
unset($admin, $isAllowed, $e, $isCorrectScript, $msg, $myGuisArray);
?>