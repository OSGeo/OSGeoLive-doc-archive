<?php
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");


import_request_variables("PG");

function forgotten_password() {
	if(
		!isset($_REQUEST["Benutzername"]) || !isset($_REQUEST["EMail"]) || ($_REQUEST["Benutzername"] == 'guest') || 
		empty($_REQUEST["Benutzername"]) || empty($_REQUEST["EMail"]) || 
		!(bool)trim($_REQUEST["Benutzername"]) || !(bool)trim($_REQUEST["EMail"])
	) {
		return -1;
	}

	if(!USE_PHP_MAILING) {
		return -4;
	}

	$administration = new administration();
	define("USER_NAME", trim($_REQUEST["Benutzername"]));
	define("USER_EMAIL",trim($_REQUEST["EMail"]));

	if(
		!$administration->getUserIdByUserName(USER_NAME) || 
		USER_EMAIL != $administration->getEmailByUserId($administration->getUserIdByUserName(USER_NAME))
	) {
		return -2;
	}

	$new_password  = $administration->getRandomPassword();

	$sql_update = "UPDATE mb_user SET mb_user_password = $1, mb_user_digest = $3 WHERE mb_user_id = $2";
	$v          = array(md5($new_password),$administration->getUserIdByUserName(USER_NAME),md5(USER_NAME.";".USER_EMAIL.":".REALM.":".$new_password));
	$t          = array("s","i");		      

	if(!db_prep_query($sql_update,$v,$t)) {
		return -3;
	}

	$email_subject = "New GeoPortal.rlp Password";
	$email_body    = sprintf("Your new GeoPortal.rlp password is: %s",$new_password);

	if(!$administration->sendEmail(NULL,NULL,USER_EMAIL,USER_NAME,$email_subject,$email_body,$error_msg)) {
		return -4;
	}

	return 1;
}

$success = forgotten_password();
?>
