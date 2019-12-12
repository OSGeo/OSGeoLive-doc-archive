<?php
include_once(dirname(__FILE__)."/../../core/globalSettings.php");

$pw = $_REQUEST['password'];
$name = $_REQUEST['name'];

$e = new mb_exception('SESSION[mb_user_name]: '.Mapbender::session()->get("mb_user_name"));

# Q4U - Michael Spitz - 16.08.2006 - Falls Cookies deaktiviert sind, muss die Session-ID an die Folgeseiten weitergereicht werden
$URLAdd="";
if($_COOKIE[session_name()]=="") {
	$URLAdd="?".session_name()."=".$_REQUEST[session_name()];
}

$isAuthenticated = authenticate($name,$pw);

if($isAuthenticated != false) {
	setSession();
	Mapbender::session()->set("mb_user_password",$pw); 
  	Mapbender::session()->set("mb_user_id",$isAuthenticated["mb_user_id"]);
	Mapbender::session()->set("mb_user_name",$isAuthenticated["mb_user_name"]);
	Mapbender::session()->set("mb_user_ip",$_SERVER['REMOTE_ADDR']);
	Mapbender::session()->set("mb_user_email",$isAuthenticated["mb_user_email"]);
	Mapbender::session()->set("mb_user_department",$isAuthenticated["mb_user_department"]);
	Mapbender::session()->set("mb_user_organisation_name",$isAuthenticated["mb_user_organisation_name"]);
	Mapbender::session()->set("mb_user_position_name",$isAuthenticated["mb_user_position_name"]);
	Mapbender::session()->set("mb_user_phone",$isAuthenticated["mb_user_phone"]);
	Mapbender::session()->set("Textsize",$isAuthenticated["mb_user_textsize"]);
	Mapbender::session()->set("Glossar",$isAuthenticated["mb_user_glossar"]);
	Mapbender::session()->set("mb_user_description",$isAuthenticated["mb_user_description"]);
	Mapbender::session()->set("mb_user_city",$isAuthenticated["mb_user_city"]);
	Mapbender::session()->set("mb_user_postal_code",$isAuthenticated["mb_user_postal_code"]);
	Mapbender::session()->set("epsg","EPSG:31466");
	Mapbender::session()->set("HTTP_HOST",$_SERVER["HTTP_HOST"]);
//INSERT LAST LOGIN DATE AND TIME
//NEW Filed required "ALTER TABLE mapbender.mb_user ADD COLUMN mb_user_last_login_date date;"
	$sql = "UPDATE mb_user SET";
	$sql .= " mb_user_last_login_date = now()";
	$V[0] = Mapbender::session()->get('mb_user_id');
	$T[0] = 'i';
	$sql .= 'WHERE mb_user_id = $1';
	$res = db_prep_query($sql, $V, $T);
	//UPDATE USER LOGIN DATE and TIME	
	require_once(dirname(__FILE__)."/../php/mb_getGUIs.php");
	$arrayGUIs = mb_getGUIs($isAuthenticated["mb_user_id"]);
	Mapbender::session()->set("mb_user_guis",$arrayGUIs);
	header ("Location: http://".$_SERVER['HTTP_HOST']."/portal/success.html".$URLAdd);
	session_write_close();
} else {
	header ("Location: http://".$_SERVER['HTTP_HOST']."/portal/failed.html".$URLAdd);
}

function authenticate ($name,$pw){
 $con = db_connect(DBSERVER,OWNER,PW);
 db_select_db(DB,$con);

 $sql = "SELECT * FROM mb_user WHERE mb_user_name = $1 AND mb_user_password = $2";
 $v = array($name,md5($pw)); // is md5 used really?
 $t = array('s','s');
 $res = db_prep_query($sql,$v,$t);

 if($row = db_fetch_array($res)){
   $e = new mb_exception('row mb_user_name: '.$row['mb_user_name']);
   return $row;	
  }
  else 
  {
  return false;
  }
}
function setSession(){
	session_start(); //function is ok cause the session will be closed directly after starting it!
	session_write_close();
}
function killSession(){
	Mapbender::session()->kill();
}
?>
