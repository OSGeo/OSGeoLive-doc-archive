<?php
# $Id: mod_state.php 2413 2008-04-23 16:21:04Z christoph $
# http://www.mapbender.org/index.php/mod_state.php
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
?>
<HTML>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>image map generator</title>
<STYLE TYPE="text/css">
<!--
	body{	
		background-color:#ffffff;
		text-decoration: none;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-weight: bold;
		font-size:9pt;	
		color: #003366;
		margin-left: 0px;
		margin-top: 0px;                                                                
	}
   
	table{
		font-family:Verdana, Geneva, Arial, Helvetica, sans-serif;
		color: #0066cc;
		font-size:10pt
	}   
   
	-->
</STYLE>
<BODY bgcolor='#ffffff'>
<?php
$sql = "SELECT stand FROM layer_metadata;";
$res = db_prep_query($sql);

echo "Daten der Vertriebsplattform sind vom Stand: ".db_result($res,0,"stand");
?>
</BODY>
</HTML>