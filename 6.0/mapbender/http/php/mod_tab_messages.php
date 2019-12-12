<?php
#$Id: mod_tab_messages.php 4196 2009-06-25 10:16:23Z vera $
#$Header: /cvsroot/mapbender/mapbender/http/javascripts/mod_insertWmcIntoDb.php,v 1.19 2006/03/09 14:02:42 uli_rothstein Exp $
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");

$buttonObj = array();

$sql = "SELECT e_id, gettext($1, e_title) AS e_title FROM gui_element, " . 
		"(SELECT v.var_value AS current_e_id FROM gui_element AS e, " . 
		"gui_element_vars AS v WHERE e.e_id = v.fkey_e_id AND ".
		"e.fkey_gui_id = v.fkey_gui_id AND e.e_id = 'tabs' AND ".
		"v.var_name LIKE 'tab_ids%' AND e.fkey_gui_id = $2) ".
		"AS gui_element_temp WHERE gui_element_temp.current_e_id = e_id ".
		"AND fkey_gui_id = $3";
		
$v = array(Mapbender::session()->get("mb_lang"), Mapbender::session()->get("mb_user_gui"), Mapbender::session()->get("mb_user_gui")); 
$t = array("s", "s", "s");
$res = db_prep_query($sql, $v, $t);
while ($row = db_fetch_array($res)) {
	array_push($buttonObj, array("id" => $row["e_id"], "title" => $row["e_title"]));
}

$json = new Mapbender_JSON();
$output = $json->encode($buttonObj);

header("Content-type:text/plain; charset=utf-8");
echo $output;
?>