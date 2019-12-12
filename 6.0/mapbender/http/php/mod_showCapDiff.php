<?php
# $Id: mod_showCapDiff.php 3342 2008-12-16 12:31:26Z mschulz $
# http://www.mapbender.org/index.php/Monitor_Capabilities
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
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");
$wms_id = intval($_REQUEST["wmsid"]);
if (isset($wms_id)){
	if (!is_int($wms_id)) {
		echo 'Error: wms_id is no integer<br>';
		die();
	}
	} else {
		echo 'Error: wms_id not requested<br>';
	die();
}
$sql = "SELECT cap_diff FROM mb_wms_availability WHERE fkey_wms_id = $1";
$v = array($wms_id);
$t = array('i');
$res = db_prep_query($sql,$v,$t);

$cap_diff_row = db_fetch_row($res);
$html = urldecode($cap_diff_row[0]);

echo $html;


?>
