<?php
# $Id: mod_evalArea.php 8085 2011-08-31 08:45:24Z verenadiewald $
# http://www.mapbender.org/index.php/mod_evalArea.php
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

import_request_variables("PG");
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

$x = $_REQUEST["x"];
$y = $_REQUEST["y"];
$epsg = $_REQUEST["srs"];
$length = $_REQUEST["length"];
$posX = explode (",", $x);
$posY = explode (",", $y);

echo "{";
if(SYS_DBTYPE=='pgsql' && count($posX) > 3){
  $sql = "SELECT area2d(GeometryFromText('MULTIPOLYGON(((";
  for($i=0; $i<count($posX); $i++){
	if($i>0){$sql .= ",";}
	$sql .= $posX[$i] . " " . $posY[$i];
  }
  $sql .= ")))',".rawurldecode($epsg).")) as myArea";
  $res = db_query($sql);
  if($row = db_fetch_array($res)){
	 echo "'area': ".round($row[0]*100)/100 ;
  }
	else{
		 echo "'area': 0";
	}
}
else{
	 echo "'area': 0";
}
if($length == "undefined") {
    $length = "0";
}
echo ",'perimeter': ". $length . "}";
?>