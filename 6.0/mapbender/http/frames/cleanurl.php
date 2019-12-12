<?php
# $Id: cleanurl.php 6733 2010-08-10 09:38:39Z christoph $
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

ob_start(); 
include_once(dirname(__FILE__) . "/../../conf/mapbender.conf");
include(dirname(__FILE__) . "/../classes/class_administration.php");


$adm = new administration();

$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);

$id = $_GET['id'];

$sql = "SELECT * FROM mb_meetingpoint WHERE mb_meetingpoint_id = $1";
$v = array($id);
$t = array('s');
$res = db_prep_query($sql,$v,$t);
if($row = db_fetch_array($res)){
	$user = $adm->getUserNameByUserId($row['fkey_mb_user_id']);
	$password = $row['mb_user_password'];
	$gui_id = $row['fkey_gui_id'];
}
else {
	exit();
}
$url = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/login.php?";
$url .= "name=".$user."&password=".urlencode($password)."&mb_user_myGui=".$gui_id."&kml_id=".$id;
		
header ("Location: ".$url);
ob_end_flush();
?>
</body>
</html>
