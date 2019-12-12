<?php
# $Id: mod_home.php 4210 2009-06-25 10:54:15Z vera $
# http://www.mapbender.org/index.php/mod_home
# Copyright (C) 2002 CCGIS
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
?>
function mod_home_init(){
<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
echo "var url = '".LOGIN."';";
echo "var name = '".urlencode(Mapbender::session()->get("mb_user_name"))."';";
echo "var pw = '".Mapbender::session()->get("mb_user_password")."';";

?>	
	var str = "<form name='myGuiList_form' method='POST' action='' target='_self'>";
	str += "<input type='hidden' name='name' value='"+name+"' />";
	str += "<input type='hidden' name='password' value='"+pw+"' />";
	str += "</form>";
	
	var mod_home_div = document.createElement('div');
	mod_home_div.setAttribute("id","mod_home_d");
	var tmp = document.body.appendChild(mod_home_div);
	document.getElementById("mod_home_d").innerHTML = str;
	document.forms.myGuiList_form.action = url;
	document.forms.myGuiList_form.submit();
	//document.location.href = url + "?name=" + name + "&password=" + pw;	
}