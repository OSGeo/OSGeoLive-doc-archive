<?php
/*
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
 
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_locale.php");

// translates all string values in a tree It can find
function translateTree($tree) {

	if (is_object($tree)) {
    	foreach ($tree as $key => $value) {
			$tree->$key = translateTree($value);
		}
	}
	else if (is_array($tree)) {
		// strings with arguments, like 'Found %d results'
		$tree = call_user_func_array("_mb",	$tree);
	}
	else if (is_string($tree)) {
		$tree = _mb($tree);
	}
 	return $tree;
}

$ajaxResponse = new AjaxResponse($_POST);

switch ($ajaxResponse->getMethod()) {
	case "translate" :

		$localeObj = new Mb_locale($ajaxResponse->getParameter("locale"));
		$msg_obj = $ajaxResponse->getParameter("data");
        $translated_obj = translateTree($msg_obj);
		
        $ajaxResponse->setSuccess(true);
		$ajaxResponse->setResult(array(
			"data" => $translated_obj,
			"locale" => $localeObj->name
		));
		break;
	default :
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("An unknown error occured."));
		break;
}

$ajaxResponse->send();
?>