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
require_once(dirname(__FILE__)."/../classes/class_json.php");

$e = new mb_notice("locale: " . Mapbender::session()->get("mb_locale") . "; lang: " . Mapbender::session()->get("mb_lang"));
$e = new mb_notice(setlocale(LC_ALL, Mapbender::session()->get("mb_locale")));

//
// Messages
//
$msg_obj = array();
$msg_obj["srsNotSupported"] = _mb("The following WMS do not support the current SRS");

$json = new Mapbender_JSON();
$output = $json->encode($msg_obj);

header("Content-type:application/x-json; charset=utf-8");
echo $output;
?>