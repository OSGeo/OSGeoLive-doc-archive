<?php
# $Id: globalSettings.php 7138 2010-11-16 14:37:08Z christoph $
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

//
// constants
//
require_once(dirname(__FILE__)."/../core/system.php");

//
// initiates the session-handling
//
session_start();
if (defined("SESSION_NAME") && is_string(SESSION_NAME)) {
	session_name(SESSION_NAME);
}

//
// Basic Mapbender classes, for session handling etc.
//
require_once dirname(__FILE__)."/../lib/class_Mapbender.php";

//
// define LC_MESSAGES if unknown (for Windows platforms)
// 
if (!defined("LC_MESSAGES")) define("LC_MESSAGES", LC_CTYPE);

//
// I18n wrapper function, gettext
//
require_once dirname(__FILE__) . "/../core/i18n.php";
require_once dirname(__FILE__) . "/../http/classes/class_locale.php";
$localeObj = new Mb_locale(Mapbender::session()->get("mb_lang"));


?>
