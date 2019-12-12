<?php
# $Id: mod_owsproxy.php 4229 2009-06-25 12:02:50Z vera $
# http://www.mapbender.org/index.php/mod_owsproxy.php
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

include(dirname(__FILE__)."/../php/mb_validateSession.php");
include(dirname(__FILE__)."/../classes/class_administration.php");

?>
<html>
<head><title></title></head>
<body>
<table>
<?php
$n = new administration();
$myguis = $n->getGuisByPermission(Mapbender::session()->get("mb_user_id"),true);
$mywms = $n->getWmsByOwnGuis($myguis);
$v = array();
$t = array();
$sql = "SELECT * FROM wms WHERE wms_id IN(";
for($i=0; $i<count($mywms); $i++){
	if($i>0){$sql .= ",";}
	$sql .= "$".strval($i+1);
	array_push($v, $mywms[$i]);
	array_push($t, "i");
}
$sql .= ")";
$res = db_prep_query($sql,$v,$t);
while($row = db_fetch_array($res)){
	if($row["wms_owsproxy"] != ""){
		echo "<tr>";
			echo "<td>";
				echo "<input type='button' value='getURL' onclick='prompt(\"Online-resource: \",\"";
				echo OWSPROXY."/".session_id()."/".$row["wms_owsproxy"]."?";
				echo "\")'>";
			echo "</td>";
			echo "<td>";
				echo $row["wms_title"];
			echo "</td>";
			echo "<td>";
				echo $row["wms_abstract"];
			echo "</td>";
		echo "</tr>";			
	}	
}
?>
</table>
</body>
</html>