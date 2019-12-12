<?php
# $Id: mod_setTimeout.php 2413 2008-04-23 16:21:04Z christoph $
# maintained by http://www.mapbender.org/index.php/User:Astrid Emde
# http://www.mapbender.org/index.php/mod_setTimeout.php
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
include '../include/dyn_js.php';

?>

try{
	if (mod_timeout_text){}
}
catch(e){
	mod_timeout_text = "Your session may not be valid anymore!";
	//mod_timeout_text = "Bitte beachten Sie, dass Ihre Session abgelaufen sein kann!";
}

try{
	if (mod_timeout_ttl){}
}
catch(e){
	mod_timeout_ttl = '15';
}

/*
* alert:    a confirm - window to reload the application when timeout is reached
* logout:  request mod_logout after timeout
*/
try{
	if (mod_timeout_action){}
}
catch(e){
	mod_timeout_action = 'alert';
}


/*version 2.0.0*/
mod_timeout_cnt = false;


mb_registerSubFunctions("mod_setTimeout_set()");            // maprequest
mb_registerInitFunctions("mod_setTimeout_interval()");

function mod_setTimeout_interval(){
   var aktiv = window.setInterval("mod_setTimeout_check()",60000);
   mod_setTimeout_set();
   mod_setTimeout_check();
}

function mod_setTimeout_set(){
   mod_timeout_cnt = parseInt(mod_timeout_ttl);
   mod_setTimeout_check();
}

function mod_setTimeout_check(){
   
   var str = "<span style='font-family: Arial, Helvetica, sans-serif;font-size:10px;'>";
   str += "Logout in ";
   
   if(mod_timeout_cnt >= 0){
      str += mod_timeout_cnt;
   }
   else { str += "0";  }
   
   str += " min";
   str += "</span>";
   writeTag("", "timeout",  str ); 
   
   if(mod_timeout_cnt == 0 && mod_timeout_action == 'alert'){       
      var go = confirm(mod_timeout_text);
      if(go == true){
        //  document.location.href = "./login.php"; 
      }
      
   }
   if(mod_timeout_cnt == 0 && mod_timeout_action == 'logout'){ document.location.href = "../php/mod_logout.php";}
   mod_timeout_cnt--;
}
