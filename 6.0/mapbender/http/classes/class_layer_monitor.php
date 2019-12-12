<?php
# $Id: class_layer_monitor.php 7020 2010-10-04 14:23:22Z christoph $
# http://www.mapbender.org/index.php/
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

require_once(dirname(__FILE__)."/../../core/globalSettings.php");

class Layer_load_count {

	function __construct () {
	}
	
	/**
	 * increments the load count in table "layer_load_count" for
	 * each layer in the WMC document by one. 
	 */
	function increment($layer_id) {
		if (!is_numeric($layer_id)) {
			return false;
		}

		//check if an entry exists for the current layer id
		$sql = "SELECT COUNT(layer_id) AS i FROM layer WHERE layer_id = $1";
		$v = array($layer_id);
		$t = array('i');
		$res = db_prep_query($sql, $v, $t);
		$row = db_fetch_array($res);
		if (intval($row["i"]) === 0) {
			return false;
		}

		//check if an entry exists for the current layer id
		$sql = "SELECT load_count FROM layer_load_count WHERE fkey_layer_id = $1";
		$v = array($layer_id);
		$t = array('i');
		$res = db_prep_query($sql, $v, $t);
		$row = db_fetch_array($res);

		//if yes, increment the load counter
		if ($row) {
			$currentCount = $row["load_count"];
			$sql = "UPDATE layer_load_count SET load_count = $1 WHERE fkey_layer_id = $2";
			$v = array(intval($currentCount + 1), $layer_id);
			$t = array('i', 'i');
			$res = db_prep_query($sql, $v, $t);
		}
		//if no, insert a new row with current layer id and load_count = 1
		else {
			$sql = "INSERT INTO layer_load_count (fkey_layer_id, load_count) VALUES ($1, 1)";
			$v = array($layer_id);
			$t = array('i');
			$res = db_prep_query($sql, $v, $t);
		}
	}
}
?>
