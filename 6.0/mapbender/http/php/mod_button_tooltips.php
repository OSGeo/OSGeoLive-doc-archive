<?php
#$Id: mod_insertWmcIntoDb.php 507 2006-11-20 10:55:57Z christoph $
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

$buttonObj = array();

$sql = "SELECT e_id, gettext($1, e_title) AS e_title FROM gui_element WHERE fkey_gui_id = $2 AND e_element = 'img'";
$v = array(Mapbender::session()->get("mb_lang"), Mapbender::session()->get("mb_user_gui"));
 
$t = array("s", "s");
$res = db_prep_query($sql, $v, $t);
while ($row = db_fetch_array($res)) {
	array_push($buttonObj, array("id" => $row["e_id"], "title" => $row["e_title"]));
}

$ajaxResponse = new AjaxResponse($_REQUEST);
$ajaxResponse->setResult($buttonObj);
$ajaxResponse->send();
//$output = $json->encode($buttonObj);

//header("Content-type:text/plain; charset=utf-8");
//echo $output;
?>