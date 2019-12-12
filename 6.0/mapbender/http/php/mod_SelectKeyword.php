<?php
# $Id: mod_SelectKeyword.php 6832 2010-08-30 08:34:57Z verenadiewald $
# ttp://www.mapbender.org/index.php/mod_SelectKeyword
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
<html>
<head>
<title><?php echo _mb("Select a keyword") ?></title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>

<script language='JavaScript'>
<!--
function insertValue(val){
  parent.window.frames["gazetteerMetadata"].document.form1.search.value = val;
  window.close();
}
-->
</script>
</head>

<body>

<?php
$sql = "Select keyword_id, ltrim(keyword) as keyword from keyword order by upper(ltrim(keyword));";
$res = db_query($sql);

echo "<select size='20' name='keywordlist' ondblClick='insertValue(this.value)' title='"._mb("Please double click to select a keyword.")."'>\n";
 
$cnt = 0;
while($row = db_fetch_array($res)){    
	echo "<option value='". $row["keyword"]."' >";
	echo $row["keyword"];
	$cnt++;
	echo "</option>";
}
echo "</select>";

?>
</body>
</html>
