<?php
# 
#
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
$e = new mb_notice("revert GUI session settings:");
$e = new mb_notice("curr: " . Mapbender::session()->get("mb_user_gui"));
$e = new mb_notice("prev: " . Mapbender::session()->get("previous_gui"));
Mapbender::session()->set("mb_user_gui", Mapbender::session()->get("previous_gui"));
?>
