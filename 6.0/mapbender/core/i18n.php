<?php
# $Id: i18n.php 5322 2010-01-13 15:31:38Z christoph $
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

	function _mb ($someString) {
	    if ($someString === "") {
	    	return "";
	    }
		$arg = array();
	    for($i = 1 ; $i < func_num_args(); $i++) {
	        $arg[] = func_get_arg($i);
	    }
	   
		if (USE_I18N) {
//			$e = new mb_notice("Translating '" . $someString . "' to language " . Mapbender::session()->get("mb_locale") . ": '" . _($someString) . "'");
		    return vsprintf(_($someString), $arg);			
		}
	    return vsprintf($someString, $arg);			
	}
?>
