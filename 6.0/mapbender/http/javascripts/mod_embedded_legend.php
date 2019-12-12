<?php
# $Id: mod_embedded_legend.php 2413 2008-04-23 16:21:04Z christoph $
# http://www.mapbender.org/index.php/mod_embedded_legend.php
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Legend</title>
<?php

include_once '../include/dyn_css.php';

$sql = "SELECT DISTINCT e_target, e_width, e_height FROM gui_element WHERE e_id = 'legend_embedded' AND fkey_gui_id = $1";
$v = array($gui_id);
$t = array('s');
$res = db_prep_query($sql, $v, $t);
$cnt = 0;
while($row = db_fetch_array($res)){
   $e_target = $row["e_target"];
   $cnt++;
}
if($cnt > 1){
	echo "alert('legend: ID not unique!');";
}
#target position, with and height...
$sql = "SELECT e_left,e_top,e_width,e_height FROM gui_element WHERE e_id = '".$e_target."' AND fkey_gui_id = $1";
$v = array($gui_id);
$t = array('s');
$res = db_prep_query($sql, $v, $t);
echo "<script type='text/javascript'>";
echo "var mod_legend_target = '".$e_target."';";
echo "var mod_legend_target_left = ".db_result($res,0,"e_left").";";
echo "var mod_legend_target_top = ".db_result($res,0,"e_top").";";
echo "var mod_legend_target_width = ".db_result($res,0,"e_width").";";
echo "var mod_legend_target_height = ".db_result($res,0,"e_height").";";

echo "</script>";
?>
</head>
<body  onload='mod_legend_init()'>
<form><span class='switch'>Legende ON/OFF<input type='checkbox' name='sw' onclick='mod_legend_repaint(this)'></span></form>
<div name='leg' id='leg'></div>
</body>
</html>
