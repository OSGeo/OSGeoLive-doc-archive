<?php
# $Id: $
# http://www.mapbender.org/index.php/mod_horizTab.php
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
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");

$tab_ids = array();
include(dirname(__FILE__)."/../include/dyn_php.php");
include(dirname(__FILE__)."/../include/dyn_js.php");

//default styles
echo 'try{if(horiztab_style){}}catch(e){horiztab_style="-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px;-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;font-size:7pt;border:solid #222222 1px;padding:1px 8px 1px 8px;line-height:22px;background:#aaaaaa;cursor:pointer;white-space:nowrap;";}';
echo 'try{if(horiztab_style_active){}}catch(e){horiztab_style_active="-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px;-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;font-size:7pt;border:solid #222222 1px;border-bottom:solid #f8f8f8 1px;padding:2px 10px 1px 10px;line-height:22px;background:#eeeeee;font-weight:bold;cursor:pointer;white-space:nowrap;";}';

//load styles
echo "var styleObj = new StyleTag();";
echo 'styleObj.addClass("tabButton", horiztab_style);';
echo 'styleObj.addClass("tabButtonActive", horiztab_style_active);';

//write tab creation javascript function
echo "open_tab_".$e_id."=".($open_tab?$open_tab:0).";\n";
echo "function init_".$e_id."(){";
echo "$(\"#".$e_id."\").tabControl()";
for ($i=0; $i < count($tab_ids); $i++) {
	$sql = "SELECT gettext($1, e_title) AS e_title FROM gui_element WHERE fkey_gui_id = $2 AND e_id = $3";
	$v = array(Mapbender::session()->get("mb_lang"), $gui_id, $tab_ids[$i]);
	$t = array("s", "s", "s");
	$res = db_prep_query($sql, $v, $t);
	$row = db_fetch_array($res);
	echo ".addTab({title:\"".$row["e_title"]."\",id:\"".$tab_ids[$i]."\"})";	
}
echo ".activateTab(open_tab_".$e_id.");}\n";

//register init event for function
echo "eventInit.register(init_".$e_id.");\n";
?>