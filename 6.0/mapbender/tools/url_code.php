<html>
<head>
<?php
# $Id: url_code.php 916 2006-11-21 10:54:10Z christoph $
# http://www.mapbender.org/index.php/url_code.php
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

require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Url-Encode and -Decode</title>
</head>
<body>
<form method='POST'>
  <textarea name="c" rows="10" cols="100"><?php  if($_REQUEST["c"]){echo $_REQUEST["c"];}?></textarea>
  <br>
  <input type='submit' name='encode' value='encode'>
  <br>
  <input type='submit' name='decode' value='decode'>  
</form>
<hr>
<textarea rows="10" cols="100">
<?php
if($_REQUEST["encode"]){
echo urlencode($_REQUEST["c"]);
}
if($_REQUEST["decode"]){
echo urldecode($_REQUEST["c"]);	
}
?>
</textarea>
</body>
</html>