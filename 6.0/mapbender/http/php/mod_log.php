<?php
# $Id: mod_log.php 6678 2010-08-03 08:35:41Z christoph $
# http://www.mapbender.org/index.php/Administration
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

require_once dirname(__FILE__) . "/../../core/globalSettings.php" ;

$ajaxRequest = new AjaxRequest($_POST);

$e_id = "log";
$gui_id = Mapbender::session()->get("mb_user_gui");

require dirname(__FILE__) . "/../include/dyn_php.php" ;

switch ($ajaxRequest->getMethod()) {
	case "logRequest":

		$request = $ajaxRequest->getParameter("req");

		if (!is_null($request)) {
			ignore_user_abort();
			$time_client = $ajaxRequest->getParameter("time_client");
			
			if (empty($request)) {
				$request = "init";
			}
			require dirname(__FILE__) . "/../classes/class_log.php";
			$log = new log("default", $request, $time_client, $logtype);
		}
		break;
	default:
		$e = new mb_exception("Invalid method in " . __FILE__);
}
?>