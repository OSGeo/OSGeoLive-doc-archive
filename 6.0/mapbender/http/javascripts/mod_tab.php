<?php
# $Id: mod_tab.php 6022 2010-04-23 11:04:11Z astrid_emde $
# http://www.mapbender.org/index.php/mod_tab.php
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

/********** Configuration*************************************************/

require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
include(dirname(__FILE__)."/../include/dyn_js.php");

$tab_ids = array();
include(dirname(__FILE__)."/../include/dyn_php.php");


echo "var tab_titles = [];\n";
for ($i=0; $i < count($tab_ids); $i++) {
	$sql = "SELECT gettext($1, e_title) AS e_title FROM gui_element WHERE fkey_gui_id = $2 AND e_id = $3";
	$v = array(Mapbender::session()->get("mb_lang"), $gui_id, $tab_ids[$i]);
	$t = array("s", "s", "s");
	$res = db_prep_query($sql, $v, $t);
	$row = db_fetch_array($res);
	echo "tab_titles[" . $i . "] = '" . $row["e_title"] . "';\n";	
}

$json = new Mapbender_JSON();
$output = $json->encode($tab_ids);

echo "var tab_ids = " . $output . ";";	
if (!isset($expandable) || !$expandable) {
	include(dirname(__FILE__) . "/mod_tab.js");
}
else {
	include(dirname(__FILE__) . "/mod_tab_expandable.js");
}
?>