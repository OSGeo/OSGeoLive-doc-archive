<?php
# $Id: mb_getGUIs.php 485 2006-06-19 14:35:01Z vera_schulze $
# http://www.mapbender.org/index.php/mb_getGUIs.php
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

#returns an array of all guis of the user
function mb_getGUIs($mb_user_id){
	$arrayGuis = array();
	if(isset($mb_user_id)){
		$sql_groups = "SELECT fkey_mb_group_id FROM mb_user_mb_group WHERE fkey_mb_user_id = $1 ";
		$v = array($mb_user_id);
		$t = array('i');
		$res_groups = db_prep_query($sql_groups,$v,$t);
		$cnt_groups = 0;
		while(db_fetch_row($res_groups)){
			$mb_user_groups[$cnt_groups] = db_result($res_groups,$cnt_groups,"fkey_mb_group_id");
			$cnt_groups++;
		}
		$count_g = 0;
		if($cnt_groups > 0){
			$v = array();
			$t = array();
			$sql_g = "SELECT DISTINCT gui.gui_id FROM gui JOIN gui_mb_group ";     
			$sql_g .= " ON gui.gui_id = gui_mb_group.fkey_gui_id WHERE( gui_mb_group.fkey_mb_group_id IN (";  
			for($i=0; $i<count($mb_user_groups);$i++){
				if($i > 0){$sql_g .= ",";}
				$sql_g .= "$".($i + 1);
				array_push($v,$mb_user_groups[$i]);
				array_push($t,'i');
			}
			$sql_g .= "))";
			$res_g = db_prep_query($sql_g,$v,$t);
			while(db_fetch_row($res_g)){
				$arrayGuis[$count_g] = db_result($res_g, $count_g, "gui_id");
				$count_g++;
			}
		}
		$sql_guis = "SELECT DISTINCT gui.gui_id FROM gui JOIN gui_mb_user ";  
		$sql_guis .= "ON gui.gui_id = gui_mb_user.fkey_gui_id WHERE (gui_mb_user.fkey_mb_user_id = $1) ";
		$sql_guis .= " AND gui.gui_public = 1";
		$v = array($mb_user_id);
		$t = array('i');
		$res_guis = db_prep_query($sql_guis,$v,$t);
		$count_guis = 0;
		while(db_fetch_row($res_guis)){
			if( !in_array(db_result($res_guis,$count_guis,"gui_id"),$arrayGuis)){
				$arrayGuis[$count_g] = db_result($res_guis,$count_guis,"gui_id");
				$count_g++;
			}
			$count_guis++;
		}
	}
	return $arrayGuis;
}
?>
