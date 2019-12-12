<?php

$name_month = explode("_",$_REQUEST["m"]);
foreach (array_keys($name_month) as $index) {
	$name_month[$index] = htmlentities($name_month[$index], ENT_QUOTES);
}

$name_day = explode("_",$_REQUEST["d"]);
foreach (array_keys($name_day) as $index) {
	$name_day[$index] = htmlentities($name_day[$index], ENT_QUOTES);
}

$today = htmlentities($_REQUEST["t"], ENT_QUOTES);

echo "<html>
<head>
<title>Datepicker</title>

<script src='datepicker.js' type='text/javascript'></script>

<style type=text/css>
	body { font-size: 8pt; font-family: Arial, helvetica, sans-serif; text-decoration:none; }
	input{width: 30px; background-color: #F5F5F5;	border: none;}
	input.std {	border: thin outset; background-color: Silver; width: 28px;	height: 24px}
	#cal { background-color: #006699; color: #cccccc; font-size: 10pt; font-weight: bold; text-align: center; }
</style>

</head>

<body topmargin='0' leftmargin='0' >
<center>         
<form name=frm>
<table cellSpacing=0 cellPadding=0 width=200 border=2>
<tr align='center' bgcolor='silver'>
<td>
	<input name='previous' class='std' onclick='prevMonth();' type='button' value='<'>
</td>
<td>
	<select name='lMonths' style='left: 2px; width: 80px; TOP: 2px; height: 22px' onchange='selMonth(this.selectedIndex);'>\n";
	for($i=0; $i<12; $i++) {
		echo "<option value=".$i.">".$name_month[$i]."</option>\n";
	}
echo "</select>
</td>
<td>
	<select name='lYears' style='width: 80px; height: 22px' onchange='selYear(this.selectedIndex);'>\n";
	for($i=2000; $i<2021; $i++){
		echo "<option value=".$i.">".$i."</option>\n";
	}
echo "</select>
</td> 
<td>
	<input name='next' class='std' onclick='nextMonth();' type='button' value='>'> 
</td>
</tr>
</table>

<table cellSpacing=0 cellPadding=0 width=200 border=2>
<tr id=cal>\n";
for ($i=0; $i<7; $i++){
	echo "<td>".$name_day[$i]."</td>\n";
}
echo "</tr><tr>\n";
for ($i=1; $i<43; $i++){
	echo "<td><input name='btn".$i."' onclick='go(this.value);' type='button'>
	      </td>\n";
	echo ($i % 7)?(""):("</tr><tr>\n");
} 
echo "<td colspan=7 align='center'>
	<input name='today' class='std' style='width: 100px' value='".$today."' onclick='go(\"x\");' type='button'>
</td>
</tr>
</table>
</form>
</center>
<script language='JavaScript'>
	picker();
</script>
</body>
</html>";
?>
