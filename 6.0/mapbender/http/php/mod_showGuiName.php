<?php
# $Id: mod_showGuiName.php 7266 2010-12-12 10:06:50Z christoph $
#http://www.mapbender.org/ShowGuiName
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Show User</title>
<?php
include '../include/dyn_css.php';
?>
</head>
<body leftmargin="5" topmargin="0">
<?php
echo "<div class='text4'>".htmlentities($gui_id,ENT_QUOTES)."</div>";
?>
</body>
</html>
