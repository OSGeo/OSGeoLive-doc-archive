<?php
/*
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");

//get language parameter out of mapbender session if it is set else set default language to de_DE
if (isset($_SESSION['mb_lang']) && ($_SESSION['mb_lang']!='')) {
	$e = new mb_notice("javascripts/mod_savewmc.php: language found in session: ".$_SESSION['mb_lang']);
	$language = $_SESSION["mb_lang"];
	$langCode = explode("_", $language);
	$langCode = $langCode[0]; # Hopefully de or s.th. else
	$languageCode = $langCode; #overwrite the GET Parameter with the SESSION information
	$languageCode = str_replace(" ", "", $languageCode);
} else {
	$languageCode = "en";//default to en for iso categories
}
//echo $languageCode;
if ($languageCode != "de" && $languageCode != "en") { // only those are defined in the database
	$languageCode = "en";
}

//get INSPIRE categories

// On server:
// get inspirecat, gety $wmc->MainMap->getExtendInfo (minx etx /o-o/ mail von Testbausdon)
// get keywords

/*
* @return String containing checkboxes and labels as HTML
*/
function createIsoTopicCategoryString($prefix, $languageCode) {
	$str = "";
	$htmlrows = "";
	$sql = "SELECT md_topic_category_id, md_topic_category_code_".$languageCode." FROM md_topic_category";
	$v = array();
	$t = array();
	$res = db_prep_query($sql, $v, $t);
	if(db_error()){ return "Could not get Categories from db";}
	$i = 0;
	while($row = db_fetch_array($res)) {
		$i++;
 		$str .= "<label for=\"{$prefix}_wmcIsoTopicCategory_{$row[0]}\">" .
			"<input class=\"wmcIsoTopicCategory\" id=\"{$prefix}_wmcIsoTopicCategory_{$row[0]}\" " . 
			"type=\"checkbox\" />{$row[1]}</label>";
	}
	return $str;
}

$originalI18nObj = array(
	"labelNewOrOverwrite" => "New / overwrite",
	"labelNewWmc" => "(new WMC)",
	"labelName" => "Name",
	"labelAbstract" => "Abstract",
	"labelKeywords" => "Keywords",
	"labelCategories" => "Categories",
	"labelCancel" => "Abort",
	"labelSave" => "Save",
	"title" => $e_title,
	"labelSaveInSession" => "Save configuration"
);

$translatedI18nObj = array();
foreach ($originalI18nObj as $key => $value) {
	$translatedI18nObj[$key] = _mb($value);
}

$json = new Mapbender_JSON();

$saveWmcCategoryString = createIsoTopicCategoryString($e_id, $languageCode);
$originalI18nObjJson = $json->encode($originalI18nObj);
$translatedI18nObjJson = $json->encode($translatedI18nObj);

$labelNewOrOverwrite = $translatedI18nObj["labelNewOrOverwrite"];
$labelNewWmc = $translatedI18nObj["labelNewWmc"];
$labelName = $translatedI18nObj["labelName"];
$labelAbstract = $translatedI18nObj["labelAbstract"];
$labelKeywords = $translatedI18nObj["labelKeywords"];
$labelCategories = $translatedI18nObj["labelCategories"];

echo <<<HTML

var wmcSaveFormHtml = '<form><fieldset>' + 
	'<label for="{$e_id}_wmctype">{$labelNewOrOverwrite}</label>' + 
	'<select class="ui-corner-all" id="{$e_id}_wmctype">' + 
	'<option value="">{$labelNewWmc}</option></select>' + 
	'<label for="{$e_id}_wmcname">{$labelName}</label>' + 
	'<input id="{$e_id}_wmcname" type="text" class="text ui-widget-content ui-corner-all" />' + 
	'<label for="{$e_id}_wmcabstract">{$labelAbstract}</label>' + 
	'<textarea id="{$e_id}_wmcabstract" class="text ui-widget-content ui-corner-all"></textarea>' + 
	'<label for="{$e_id}_wmckeywords">{$labelKeywords}</label>' + 
	'<input id="{$e_id}_wmckeywords" type="text" class="text ui-widget-content ui-corner-all" />' + 
	'<input id="{$e_id}_wmc_id" type="hidden" />' + 
	'</fieldset><fieldset id="{$e_id}_isoTopic_cat"><legend>{$labelCategories}' + 
	'</legend>' + 
	'{$saveWmcCategoryString}' + 
	'</fieldset></form>';
	
var originalI18nObj = $originalI18nObjJson;
var translatedI18nObj = $translatedI18nObjJson;

HTML;

include(dirname(__FILE__) . "/../javascripts/mod_savewmc.js");
?>
