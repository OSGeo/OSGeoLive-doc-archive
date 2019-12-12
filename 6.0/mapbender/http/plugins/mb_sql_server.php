<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../php/mb_validateSession.php";
require_once dirname(__FILE__) . "/../classes/class_gui.php";
require_once dirname(__FILE__) . "/../classes/class_user.php";

$ajaxResponse = new AjaxResponse($_POST);

switch ($ajaxResponse->getMethod()) {
	case "sql" :
		$application = new gui($ajaxResponse->getParameter("applicationId"));

		$user = new User();
		$apps = $user->getApplicationsByPermission();

		if (in_array($application->id, $apps)) {
			$sql = $application->toSql();
			$resultObj = array(
				"sql" => $sql
			);
			$ajaxResponse->setResult($resultObj);
			$ajaxResponse->setSuccess(true);
			break;
		}
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("You are not allowed to access this application."));
		break;

	default: 
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("An unknown error occured."));
		break;
}

$ajaxResponse->send();
?>