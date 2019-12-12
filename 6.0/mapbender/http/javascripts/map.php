<?php
# $Id: map.php 7318 2010-12-16 11:40:51Z armin11 $
# http://www.mapbender.org/index.php/Map.php
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
require_once(dirname(__FILE__)."/../classes/class_json.php");

$json = new Mapbender_JSON();

// see http://trac.osgeo.org/mapbender/ticket/79
ini_set('session.bug_compat_42',0); 
ini_set('session.bug_compat_warn',0);

Mapbender::session()->set("mb_user_gui", $gui_id);

ob_start();
header('Content-type: application/x-javascript');
//
// Define global variables (TODO: move to mapbender object later on)
//
echo "Mapbender.sessionId = '".session_id()."';\n";
echo "var mb_nr = Mapbender.sessionId;\n";
echo "Mapbender.sessionName = '".session_name()."';\n";
echo "var mb_session_name = Mapbender.sessionName;\n";
echo "Mapbender.loginUrl = '".Mapbender::session()->get("mb_login")."';\n";
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
		echo "Mapbender.baseUrl = 'https://".$_SERVER['HTTP_HOST']."';\n";
	}
	else {
		echo "Mapbender.baseUrl = 'http://".$_SERVER['HTTP_HOST']."';\n";
}
echo "Mapbender.userId = '".Mapbender::session()->get("mb_user_id")."';\n";
echo "var mb_myLogin = Mapbender.loginUrl;\n";
echo "var mb_styleID = '".md5(Mapbender::session()->get("mb_user_name"))."';\n";
echo "var mb_myBBOX = '".Mapbender::session()->get("mb_myBBOX")."';\n";
echo "Mapbender.locale = '" . Mapbender::session()->get("mb_locale") . "';\n";
echo "Mapbender.languageId = '" . Mapbender::session()->get("mb_lang") . "';\n";
echo "Mapbender.application = '" . Mapbender::session()->get("mb_user_gui") . "';\n";
echo "Mapbender.versionNumber = '" . MB_VERSION_NUMBER . "';\n";
echo "Mapbender.versionAppendix = '" . MB_VERSION_APPENDIX . "';\n";
echo "Mapbender.releaseDate = new Date(".date("Y",MB_RELEASE_DATE).",".date("n",MB_RELEASE_DATE).",".date("j",MB_RELEASE_DATE).");\n";
echo "var owsproxy = '".OWSPROXY."';\n";
echo "var global_mb_log_js = '".LOG_JS."';\n";
echo "var global_mb_log_level = '".LOG_LEVEL."';\n";
echo "var global_log_levels = '".LOG_LEVEL_LIST."';\n";
echo "var mb_feature_count = ".MB_FEATURE_COUNT.";\n";
echo "var mb_resolution = ".MB_RESOLUTION.";\n";
echo "var mb_security_proxy = '" . MB_SECURITY_PROXY . "';\n";

//
// Load external JavaScript libraries
//
$extPath = dirname(__FILE__) . "/../extensions/";
$extFileArray = array();
if (!LOAD_JQUERY_FROM_GOOGLE) {
	$extFileArray[]= "jquery-ui-1.8.1.custom/js/jquery-1.4.2.min.js";
}
$extFileArray[]= "jqjson.js";

for ($i = 0; $i < count($extFileArray); $i++) {
	$currentFile = $extPath . $extFileArray[$i];
	if (file_exists($currentFile)) {
	    	/*
		 * @security_patch finc done
		 */
		require_once(secure($currentFile,"/http/extensions/"));
		echo "\n\n\n\n";
	}
	else {
		$e = new mb_exception("Extension not found: " . $currentFile);
		echo "var e = new Mb_exception('Library not found: " . $currentFile . "');";
		die;
	}
}

echo "var getParams = ".json_encode($_GET).";";

//
// Load internal JavaScript libraries
//
$libPath = dirname(__FILE__) . "/../../lib/";
$libFileArray = array(
	"exception.js", 
	"ajax.js", 
	"basic.js",
	"div.js",
	"list.js",
	"point.js",
	"button.js",
	"extent.js",
	"marker.js",
	"backwards_compatibility_to_2.6.js"
);

for ($i = 0; $i < count($libFileArray); $i++) {
	$currentFile = $libPath . $libFileArray[$i];
	if (file_exists($currentFile)) {
		/*
		 * @security_patch finc done
		 *
		 */
		require_once(secure($currentFile,"lib/"));
		echo "\n\n\n\n";
	}
	else {
		$e = new mb_exception("Library not found: " . $currentFile);
		echo "var e = new Mb_exception('Library not found: " . $currentFile . "');";
		die;
	}
}

//
// Load JavaScript modules of GUI elements
//
$sql = "SELECT DISTINCT e_mb_mod, e_id, e_pos FROM gui_element WHERE e_public = 1 AND fkey_gui_id = $1 ORDER BY e_pos ";
$v = array($gui_id);
$t = array('s');
$res = db_prep_query($sql, $v, $t);
$moduleArray = array();
while($row = db_fetch_array($res)){
	if($row["e_mb_mod"] != ""){
		$moduleArray = array_merge($moduleArray, explode(",", $row["e_mb_mod"]));
	}
}

for ($moduleIndex = 0; $moduleIndex < count($moduleArray); $moduleIndex++) {
	if (trim($moduleArray[$moduleIndex]) !== "") {
		$fileFound = false;
		$pathArray = explode(",", MODULE_SEARCH_PATHS);
		foreach ($pathArray as $path) {
			$currentFile = dirname(__FILE__) . "/" . $path . trim($moduleArray[$moduleIndex]);
			if (file_exists($currentFile)) {
				$e = new mb_notice("LOADING module : " . $currentFile);
				/*
				 * @security_patch finc done
				 * we dont check the file extension
				 */
				require_once(secure($currentFile));
				echo "\n";
				$fileFound = true;
				break;
			}
		}

		if ($fileFound) {
			continue;
		}
	}

	$e = new mb_exception("Javascript module not found: " . trim($moduleArray[$moduleIndex]));
	echo "var e = new Mb_exception('Javascript module not found: " . trim($moduleArray[$moduleIndex]) . "');";
}

//
// Load JavaScript files of GUI elements
//

$modulesNotRelyingOnGlobalsArray = explode(",", MODULES_NOT_RELYING_ON_GLOBALS);
?>
Mapbender.modules = {};
<?php
$executeJsPluginsArray = array();
$linkJsPluginsArray = array();
//get language code
$langCode = Mapbender::session()->get("mb_lang");
$mb_sql = "SELECT DISTINCT e_js_file, e_id, e_src, e_target, e_pos, e_url, " . 
	"e_left, e_top, e_title, gettext($2, e_title) AS e_current_title, " . 
	"e_width, e_height, e_requires FROM gui_element WHERE e_public = 1 AND " .
	"fkey_gui_id = $1 ORDER BY e_pos";
$mb_v = array($gui_id, $langCode);
$mb_t = array("s","s");
$mb_res = db_prep_query($mb_sql, $mb_v, $mb_t);
while ($row_js = db_fetch_array($mb_res)) {

	//
	// Create element properties
	//
	$e_id = isset($row_js["e_id"]) ? 
	str_replace(" ", "", $row_js["e_id"]) : "";
	$e_src = $row_js["e_src"];
	$e_require = $row_js["e_requires"];
	$e_title = $row_js["e_title"];
	$e_currentTitle = $row_js["e_current_title"];
	$e_target = explode(",",$row_js["e_target"]);
	$e_width = intval($row_js["e_width"]);
	$e_height = intval($row_js["e_height"]);
	$e_top = intval($row_js["e_top"]);
	$e_left = intval($row_js["e_left"]);
	$e_url = $row_js["e_url"];

	$elementAttributes = "{" . 
		"id:'$e_id'," . 
		"target:" . ($e_target[0] != "" ? $json->encode($e_target) : "[]") . "," .
		"url:'$e_url'," .
		"top:$e_top," . 
		"left:$e_left," . 
		"width:$e_width," . 
		"height:$e_height," . 
		"src:'$e_src'," . 
		"title:'$e_title'," . 
		"currentTitle:'$e_currentTitle'" . 
		"}";

	echo "Mapbender.modules." . $e_id . " = " . $elementAttributes . ";\n";
	echo "var t = Mapbender.modules." . $e_id . ".target;";
	echo "var sel = [];for (var k in t) {t[k] = $.trim(t[k]);if($('#' + t[k]).size() > 0) {sel.push($('#' + t[k]).get(0))}}\n";
	echo "Mapbender.modules." . $e_id . ".\$target = sel.length > 0 ? \$(sel) : $([]);\n";
	echo "$('#" . $e_id . "').data('api', Mapbender.modules." . $e_id . ");";
	
	//
	// Include JavaScript files
	//
	$jsFileString = $row_js["e_js_file"];
	if ($jsFileString){
		if (in_array($e_id, $modulesNotRelyingOnGlobalsArray) || preg_match("/\/plugins\//", $jsFileString)) {
			//
			// Create the jQuery plugin in output buffer
			//
			ob_start();
			include "../include/dyn_js_object.php";
			$elementVars = ob_get_contents();
			ob_end_clean();

			ob_start();
			echo "var options = Mapbender.modules." . $e_id . ";\n";
			
			// extend the options variable by JS element vars 
			echo $elementVars;
	
			echo "\n$.fn.$e_id = function (options) {\n" . 
				"\treturn this.each(function () {\n\n";
	
			$jsArray = explode(",", $jsFileString);
			for ($i = 0; $i < count($jsArray); $i++) {
				$currentFile = trim($jsArray[$i]);
		
				if (!file_exists($currentFile)) {
					$e = new mb_exception("Javascript not found: " . $currentFile);
					echo "var e = new Mb_exception('Javascript not found: " . $currentFile . "');";
					continue;
				}
				$e = new mb_notice("LOADING JS : " . $currentFile);
				/*
				 * @security_patch finc done
				 * folder?
				 */
				require(secure($currentFile));
			}	

			echo "\n\t});\n};\n\n";
	
			echo "$('#$e_id').$e_id(options);\n";
			$executeJsPluginsArray[] = ob_get_contents();
			ob_end_clean();
			$linkJsPluginsArray[] = "var options = {'id':'" . $e_id . 
				"'};" . $elementVars . ";linkPlugins(options);";
		}
		else {
			$jsArray = explode(",", $jsFileString);
			for ($i = 0; $i < count($jsArray); $i++) {
				$currentFile = trim($jsArray[$i]);
		
				if (!file_exists($currentFile)) {
					$e = new mb_exception("Javascript not found: " . $currentFile);
					echo "var e = new Mb_exception('Javascript not found: " . $currentFile . "');";
					continue;
				}
				$e = new mb_notice("LOADING JS : " . $currentFile);
				/*
				 * @security_patch finc done
				 * folder?
				 */
				require(secure($currentFile,""));
			}
		}
	}
}

$jsText = implode("\n", $executeJsPluginsArray);
$linkJsPluginsText = implode("\n", $linkJsPluginsArray);

echo <<<JS

$(function () {
	var linkPlugins = function (settings) {
		if (!settings.inputs || !settings.inputs.length) {
			return;
		}
		for (var p = 0; p < settings.inputs.length; p++) {
			(function () {
				var i = settings.inputs[p];
				for (q = 0; q < i.linkedTo.length; q++) {
					(function () {
						var link = i.linkedTo[q];
						var method = i.method;
						var element = $("#" + link.id).mapbender();
						if (!element || typeof element.events === "undefined") {
							new Mapbender.Exception("Unknown element " + link.id);
						}
						else {
							element.events[link.event].register(function (obj) {
								var target = $("#" + settings.id).mapbender();
								if (target && typeof target[i.method] === "function") {
									if (typeof link.value !== "undefined") {
										new Mapbender.Warning("Method '" + i.method + "' in element '" + settings.id + "' called with HARDWIRED value '" + $.toJSON(link.value) + "' by event '" + link.event + "' in element '" + link.id + "'.");
										target[i.method](link.value);
									}
									else {
										var arg = !obj ? undefined : ((!obj[link.attr]) ? obj : obj[link.attr]);
										new Mapbender.Warning("Method '" + i.method + "' in element '" + settings.id + "' called with value '" + $.toJSON(arg) + "' by event '" + link.event + "' in element '" + link.id + "'.");
										target[i.method](arg);
									}
								}
								else {
									new Mapbender.Warning("Method '" + i.method + "' does not exist or is not a function");
								}
							});
						}
					})();
				}
			})();
		}		
	};

	Mapbender.events.hideSplashScreen.trigger();

	$jsText
	
	$linkJsPluginsText

	$("img").bind("mousedown", function (e) {
		e.preventDefault();
	}).bind("mouseover", function (e) {
		e.preventDefault();
	}).bind("mousemove", function (e) {
		e.preventDefault();
	});
	
	// creates the map objects (mapframe1, overview...)
	Mapbender.events.initMaps.trigger();

	Mapbender.events.beforeInit.trigger();

	// initialisation
	Mapbender.events.init.done = true;
	Mapbender.events.init.trigger();

	Mapbender.events.afterInit.trigger();
	Mapbender.events.localize.trigger();
});

JS;
?>
