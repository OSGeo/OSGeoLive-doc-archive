<?php
# $Id: mod_editGroup.php
# http://www.mapbender.org/index.php/mod_editGroup.php
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

$e_id="editGroup";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

/*  
 * @security_patch irv done
 */ 
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");

$postvars = explode(",", "selected_group,name,title,owner_name,owner_id,description,address,postcode,city,stateorprovince,country,voicetelephone,facsimiletelephone,email,logo_path,action");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}


require_once(dirname(__FILE__)."/../classes/class_user.php");
require_once(dirname(__FILE__)."/../classes/class_group.php");
$myGroup = false;

include "../../lib/editGroup.php";
?>