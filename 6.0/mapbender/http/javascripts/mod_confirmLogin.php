<?php
# $Id: mod_confirmLogin.php  
# http://www.mapbender.org/index.php/mod_confirmLogin.php
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
include_once dirname(__FILE__) . "/../../conf/mapbender.conf";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">	
<title>Confirm Login</title>

<?php 
$userId = $_GET["user_id"];
if (!is_numeric($userId)) {
	echo "User ID not valid!";
	die;
}

$userName = $_GET["user_name"];
$pattern = "/[a-z0-9_-]/i";
if (!preg_match($pattern, $userName)) {
	echo "User Name not valid!";
	die;
}

$userTicket = $_GET["user_ticket"];
$pattern = "/[a-z0-9]{30}/i";
if (!preg_match($pattern, $userTicket)) {
	echo "User Ticket not valid!";
	die;
}
?>
<style type="text/css">
<!--
body{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
}
-->
</style>
<script type='text/javascript' src="../extensions/jquery.js"></script>
<script type="text/javascript">
<?php 
echo "var userId = ".$_REQUEST['user_id'].";\n";
echo "var userName = '".htmlentities($userName, ENT_QUOTES, CHARSET)."';\n";
echo "var userTicket = '".htmlentities($userTicket, ENT_QUOTES, CHARSET)."';\n";
?>

/*
 * Check if ticket number for this user is valid
 *
 * @return boolean return true if ticket number is valid
 */
function checkTicketNumber () {
	var parameters = {
		"command" : "checkTicket",
		"userId" : userId,
		userTicket : userTicket
	};
	$.post("../php/mod_confirmLogin_server.php", parameters, function (json, status) {
		if(status == 'success') {
			if (json == 'true') {
				createInsertFields();
			}
			else {
				$("#contentDiv").text("You are not authorized. Please request a new ticket from your administrator to set your password.");
			}
		}
	});
}

/*
 * Creates table with insert fields
 *
 */
function createInsertFields() {
	//create table
	var $table = $("<table></table>");
	$table.appendTo("#contentDiv");
	//create lines and fields
	var $tr1 = $("<tr><td>User name:</td><td><input type='text' readonly id='userName'/></td><td></td></tr>");
	var $tr2 = $("<tr><td>Password:</td><td><input type='password' id='userPw'/></td><td id='spanTd'></td></tr>");
	var $tr3 = $("<tr><td>Confirm password:</td><td><input type='password' id='userPw2'/></td><td></td></tr>");
	$tr1.appendTo($table);
	$tr2.appendTo($table);
	$tr3.appendTo($table);
	
	//fill in field userName 
	$("#userName").val(userName);
	
	//set keyup event for password check
	$("#userPw").keyup(function () {
		checkSafety(this.value);
	});
	
	//create span for pwd safety message
	$("<span />").attr("id","pwdSafetyMsg").appendTo("#spanTd");
	
	//set div and button for saving pw
	var $buttonDiv = $("<div><input type='button' value='Save'></div");
	$buttonDiv.click(function () {
    	savePwd();	  
    });
	$buttonDiv.appendTo("#contentDiv");
}

/*
 * Save new password
 *
 */
function savePwd() {
	if(checkPassword()) {
		var parameters = {
			command : "savePwd",
			userId : userId,
			userTicket : userTicket,
			userPassword : document.getElementById("userPw").value
		};
		$.post("../php/mod_confirmLogin_server.php", parameters, function (json, status) {
			if(status == 'success') {
				if (json == 'true') {
					var $loginHref = $("<div style='margin-top:20px'><a href='../frames/login.php'>Login</a></div");
					$loginHref.appendTo("#contentDiv");
				}
				else {
					var $errorMsg = $("<div style='margin-top:20px'>Error saving password. Please contact your administrator.</div");
					$errorMsg.appendTo("#contentDiv");
				}	
			}
		});
	}
}

/*
 * Check if password and password confirmation are inserted correctly 
 *
 */
function checkPassword() {
	var newPw = document.getElementById("userPw");
	var newPwConfirm = document.getElementById("userPw2");
	if(newPw.value == '' || newPwConfirm.value == '' || newPw.value != newPwConfirm.value) {
		alert("Password verification failed. Please insert password twice!");
	    newPw.value = "";
	    newPwConfirm.value = "";
	    newPw.focus();
	    $("#pwdSafetyMsg").html("");
	    return false;
	}
	else {
		return true;	
	}
}

function checkSafety(pwdString){
	var pwdMsg = "";
	var pwdPoints = pwdString.length;
	
	var hasLetter = new RegExp("[a-z]");
	var hasCaps	= new RegExp("[A-Z]");
	var hasNumbers = new RegExp("[0-9]");
	var hasSymbols = new RegExp("\\W");
	
	if(hasLetter.test(pwdString)){ pwdPoints += 4; }
	if(hasCaps.test(pwdString)){ pwdPoints += 4; }
	if(hasNumbers.test(pwdString)){ pwdPoints += 4; }
	if(hasSymbols.test(pwdString)){ pwdPoints += 4; }
	
	if(pwdPoints >= 24) {
		$("#pwdSafetyMsg").css("color","#0f0");
		pwdMsg = "Your password is strong!";
	} 
	else if(pwdPoints >= 16) {
		$("#pwdSafetyMsg").css("color","#00f");
		pwdMsg = "Your password is medium!";
	} 
	else if(pwdPoints >= 12) {
		$("#pwdSafetyMsg").css("color","#fa0");
		pwdMsg = "Your password is weak!";
	} 
	else {
		$("#pwdSafetyMsg").css("color","#f00");
		pwdMsg = "Your password is very weak!";
	}
	
	$("#pwdSafetyMsg").html(pwdMsg);
}

</script>
</head>

<body onload='checkTicketNumber();'>
	<div id='contentDiv'>
	</div>
</body>

</html>

