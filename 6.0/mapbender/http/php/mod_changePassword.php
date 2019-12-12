<?php
# $Id: mod_changePassword.php 7287 2010-12-12 11:03:14Z apour $
# http://www.mapbender.org/index.php/ChangePassword
#
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
/*  
 * @security_patch irv done
 */ 
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");

$postvars = explode(",", "oldpassword,newpassword,confirmpassword,profile_id,upd");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Change Password</title>
<style type="text/css">
<!--
body{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
}
.desc{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 9px;
}
.myButton{
	font-family: Arial, Helvetica, sans-serif;
	width : 150px;
}
-->
</style>
<script language="JavaScript">
function validate(wert){
	if(wert == 'newpassword'){
		if(document.form1.newpassword.value == ""){
			//alert("Bitte geben Sie ein neues Passwort an.");
			alert("Please enter a new password.");
			document.form1.newpassword.focus();
			document.form1.upd.value='false';
			return false;
		}
		if(document.form1.newpassword.value.length < 6 || document.form1.newpassword.value.search(/\d/) == -1 || document.form1.newpassword.value.search(/\D/) == -1 ){
			//alert("Bitte beachten Sie die unten\naufgef�hrten Passwortregeln!");
			alert("Please note the rules for choosing a password below!");
			document.form1.newpassword.focus();
			document.form1.upd.value='false';
			return false;
		}
		var letter =  document.form1.newpassword.value.match(/\D/);
		if(eval("document.form1.newpassword.value.match(/" + letter + "/gi).length") > 4){
			//alert("Bitte beachten Sie die unten\naufgef�hrten Passwortregeln!");
			alert("Please note the rules for choosing a password below!");
			document.form1.newpassword.focus();
			document.form1.upd.value='false';
			return false;
		}
		var integer =  document.form1.newpassword.value.match(/\d/);
		if(eval("document.form1.newpassword.value.match(/" + integer + "/gi).length") > 4){
			//alert("Bitte beachten Sie die unten\naufgef�hrten Passwortregeln!");
			alert("Please note the rules for choosing a password below!");
			document.form1.newpassword.focus();
			document.form1.upd.value='false';
			return false;
		}
		if(document.form1.confirmpassword.value == ""){
			//alert("Bitte best�tigen Sie das Passwort.");
			alert("Please confirm the password.");
			document.form1.confirmpassword.focus();
			document.form1.upd.value='false';
			return false;
		}
		if(document.form1.newpassword.value!=document.form1.confirmpassword.value) {
			//alert("Die Passw�rter stimmen nicht �berein.");
			alert("The password entries do not match.");
			document.form1.confirmpassword.value='';
			document.form1.confirmpassword.focus();
			document.form1.upd.value='false';
			return false;
		}
		else{
			document.form1.upd.value='true';
			document.form1.submit();
		}
	}
}
</script>
</head>
<body>
<?php
//the database-params
$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db(DB,$con);

$logged_user_name=Mapbender::session()->get("mb_user_name");
$logged_user_id=Mapbender::session()->get("mb_user_id");

/* handle INSERT and DELETE */
if($upd){
	$sql_user_id = "SELECT mb_user_id FROM mb_user WHERE mb_user_id = $1 ";
	$v = array($logged_user_id);
	$t = array('i');
	$res_user_id = db_prep_query($sql_user_id,$v,$t);
	$real_user_id = db_result($res_user_id,0,"mb_user_id");

	$sql_password = "SELECT mb_user_password, mb_user_password = $1 as new FROM mb_user where mb_user_id = $2";
	$v = array($newpassword,$real_user_id);
	$t = array('s','i');
	$res_password = db_prep_query($sql_password,$v,$t);

	if( db_result($res_password,0,"mb_user_password") == md5($oldpassword) &&
            db_result($res_password,0,"mb_user_password") != db_result($res_password,0,"new")){
		$sql_update = "UPDATE mb_user SET mb_user_password = $1";
		$v = array(md5($newpassword));
		$t = array('s');
		$sql_update .= " WHERE mb_user_id = $2 ";
		array_push($v,$real_user_id);
		array_push($t,'i');
		db_prep_query($sql_update,$v,$t);
		echo "<script language='javascript'>";
		//echo "alert('Passwort wurde ge�ndert!');";
		echo "alert('Password has been updated!');";
		echo "</script>";
	}
	elseif (db_result($res_password,0,"mb_user_password") != md5($oldpassword)){
		echo "<script language='javascript'>";
		echo "alert('Please enter the correct current password!');";
		echo "</script>";
	} else {
		echo "<script language='javascript'>";
		//echo "alert('Bitte beachten Sie die unten aufgef�hrten Passwortregeln!');";
		echo "alert('Please note the rules for choosing a password below!');";
		echo "</script>";
	}
}

/* HTML */
echo "<fieldset><legend>Change password:</legend>";
echo "<form name='form1' action='" . $self ."' method='post'>";
echo "<table cellpadding='5' cellspacing='0' border='0'>";
echo "<tr>";
echo "<td>";
echo "old password:";
echo "</td>";
echo "<td>";
echo "<input type='password' name='oldpassword' value=''>";
echo "</td>";
echo"</tr>";
echo "<tr>";
echo "<td>";
echo "new password:";
echo "</td>";
echo "<td>";
echo "<input type='password' name='newpassword' value=''>";
echo "</td>";
echo"</tr>";
echo"<tr>";
echo "<td>";
echo "confirm:";
echo "</td>";
echo "<td>";
echo "<input type='password' name='confirmpassword' value=''>";
echo "</td>";
echo "</tr>";
echo"<tr>";
echo"<td>";
echo "<input type='hidden' name='profile_id' value='";
echo "$profile_id";
echo "'>";
echo "</td>";
echo"<td></tr></table>";
echo "<input type='hidden' name='upd' value=''>";
echo "<center><input class='myButton' type='button' name='update' value='update' onClick='validate(\"newpassword\")'></center>";
echo "</form>";

?>
</fieldset><br />

<div class='desc'>
Please note: <br />
<ul>
<li>the password has to be different from the old one</li>
<li>the minimum length is six characters</li>
<li>it must contain a number</li>
<li>it must not be composed entirely of numbers</li>
<li>no character may be used more than four times</li>
</ul>
</div>
</body>
</html>
