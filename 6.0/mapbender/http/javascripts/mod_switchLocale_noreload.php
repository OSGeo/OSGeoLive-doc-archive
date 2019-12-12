<?php
# $Id$
# http://www.mapbender.org/index.php/localeSwitch
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

require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

include(dirname(__FILE__) . "/../include/dyn_js.php");

if (!USE_I18N) {
	echo "var languages = '" . Mapbender::session()->get("mb_lang") . "';";
}
?>

function validate_locale(){
	var index = document.getElementById("language").selectedIndex;
	var lang = document.getElementById("language").options[index].value;
	
	try {
		Mapbender.modules.i18n.localize(lang);
	}
	catch (e) {
		new Mb_exception(e.message);
	}
}

Mapbender.events.init.register(function () {
	var localeSelectNode = document.getElementById('language');
	var languageArray = languages.split(",");
	var selected = false;
	for (var i = 0; i < languageArray.length; i++) {
		if (languageArray[i] == '<?php echo Mapbender::session()->get("mb_lang");?>') {
			selected = true;
		}		
		else {
			selected = false;
		}
		
		var currentOption = new Option(languageArray[i], languageArray[i], selected, selected);
		localeSelectNode.options[i] = currentOption;
	}
});