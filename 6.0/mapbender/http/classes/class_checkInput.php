<?php
# $Id: class_checkInput.php 7134 2010-11-16 10:06:01Z christoph $
# http://www.mapbender.org/index.php/class_checkInput
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

require_once(dirname(__FILE__)."/../../conf/mapbender.conf");

class checkInput{
	var $v;
	function checkInput($q,$v,$t){
		if(is_array($v) == false){
			$v = array($v);
		}
		if(is_array($t) == false){
			$t = array($t);
		}
		if(count($v) != count($t)){
			$e = new mb_exception("array params and array types have a different count  in ".$_SERVER['SCRIPT_FILENAME'].": Sql: ".$q);
		}
		if(PREPAREDSTATEMENTS == true && SYS_DBTYPE == "pgsql"){
			$this->v = $v;
		}
		else{
			for($i=0; $i<count($v); $i++){
				if($t[$i] == 's'){
					$v[$i] = db_escape_string($v[$i]);
				}
				else if($t[$i] == 'i'){
					if(preg_match("/w/",$v[$i])){
						$e = new mb_exception($_SERVER['SCRIPT_FILENAME'].": Unable to parse integer in: ".$q." with: param ".$i.",".$v[i]);
						die("wrong data type in sql:".$q);
					}					
				}
				else if($t[$i] == 'd'){
					
				}	
			}
			$this->v = $v;
		}		
	}	
}
?>
