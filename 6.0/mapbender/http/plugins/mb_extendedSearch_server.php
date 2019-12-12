<?php 
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
include_once(dirname(__FILE__)."/../extensions/JSON.php");

//db connection
$con = db_connect($DBSERVER,$OWNER,$PW) or die ("Error while connecting database $dbname");
db_select_db(DB,$con);

//Define JSON object
$json = new Services_JSON();
$obj = $json->decode(stripslashes($_REQUEST['obj']));
//get language parameter out of mapbender session if it is set else set default language to de_DE
$sessionLang = Mapbender::session()->get("mb_lang");
if (($sessionLang != false) && ($sessionLang != '')) {
	$e = new mb_notice("mb_extentedSearch_server.php: language in session: ".$sessionLang);
	$language = $sessionLang;
} else {
	$language = "de_DE";//use locale for compatibility with later mapbender version
}

//extract language code out of locale

$langCode = explode("_", $language);

$langCode = $langCode[0];

#$langCode="de";

$e = new mb_notice("mb_extendendSearch_server.php: language: ".$langCode);

//workflow:
switch($obj->action){
	case 'getList':
		$obj->entries = getList($langCode);
		sendOutput($obj);
	break;
	default:
		sendOutput("no action specified...");
}

/**
 * Get all departments (user_ids and department names), categories
 * 
 * @return mixed[] the user_ids and names of the departments, category names and ids 
 */
  
function getList($langCode){
	global $con;
	$entries = array();
	$entries['translations'] = array();
	switch ($langCode) {
		case 'de':
			$entries['translations']['extendedSearchTitle'] = 'Erweiterte Suche';
			$entries['translations']['extendedSearchTitleBc'] = 'Erweiterte Suche';
			$entries['translations']['legendSearchTextTitle'] = 'Suchbegriff(e)';
			$entries['translations']['legendSpatialFilterTitle'] = 'Räumliche Eingrenzung';
			$entries['translations']['labelSpatialFilter'] = 'räumliche Eingrenzung aktivieren';
			$entries['translations']['labelSpatialFilterType'] = 'wie?';
			$entries['translations']['searchTypeBboxIntersects'] = 'angeschnitten';
			$entries['translations']['searchTypeBboxOutside'] = 'ganz außerhalb';
			$entries['translations']['searchTypeBboxInside'] = 'innerhalb';
			$entries['translations']['orderByRelevance'] = 'Nachfrage';
			$entries['translations']['orderByTitle'] = 'Alphabetisch';
			$entries['translations']['orderById'] = 'Identifizierungsnummer';
			$entries['translations']['orderByDate'] = 'Letzte Änderung';
			$entries['translations']['legendOrderBy'] = 'Sortieren nach:';
			$entries['translations']['legendDepartment'] = 'Anbieter:';
			$entries['translations']['legendInspireThemes'] = 'Inspire Themen';
			$entries['translations']['legendIsoCategories'] = 'ISO19115 Themen';
			$entries['translations']['legendCustomCategories'] = 'Andere Themen';
			$entries['translations']['legendDateOfPublication'] = 'Veröffentlichungsdatum';
			$entries['translations']['legendDateOfLastRevision'] = 'Datenaktualität';
			$entries['translations']['labelDateOfPublicationStart'] = 'Datum von:';
			$entries['translations']['labelDateOfPublicationEnd'] = 'Datum bis:';
			$entries['translations']['labelDateOfLastRevisionStart'] = 'Datum von:';
			$entries['translations']['labelDateOfLastRevisionEnd'] = 'Datum bis:';
			$entries['translations']['legendSearchResources'] = 'Ressourcentypen';
			$entries['translations']['labelIntersects'] = 'angeschnitten';
			$entries['translations']['labelOutside'] = 'außerhalb';
			$entries['translations']['labelInside'] = 'komplett innerhalb';
			$entries['translations']['labelOrderByRank'] = 'Nachfrage';
			$entries['translations']['labelOrderByTitle'] = 'Alphabetisch';
			$entries['translations']['labelOrderById'] = 'Ident. Nummer';
			$entries['translations']['labelOrderByDate'] = 'Letzte Änderung';
			$entries['translations']['labelCheckResourcesWms'] = 'Interaktive Karten';
			$entries['translations']['labelCheckResourcesWfs'] = 'Such/Download/Erfassungsmodule';
			$entries['translations']['labelCheckResourcesWmc'] = 'Kartensammlungen';
			$entries['translations']['labelCheckResourcesGeorss'] = 'GeoRSS Newsfeeds';
			$entries['translations']['checkResourcesWms'] = 'Kartendienste';
			$entries['translations']['checkResourcesWfs'] = 'Such- und Downloaddienste';
			$entries['translations']['checkResourcesWmc'] = 'Kartenzusammenstellungen';
			$entries['translations']['checkResourcesGeorss'] = 'GeoRSS Feeds';
			$entries['translations']['search'] = 'Suche starten';
			$entries['translations']['deleteSelection1'] = 'Auswahl löschen';
			$entries['translations']['deleteSelection2'] = 'Auswahl löschen';
			$entries['translations']['deleteSelection3'] = 'Auswahl löschen';
			$entries['translations']['deleteSelection4'] = 'Auswahl löschen';
			$entries['translations']['classificationsLegend'] = 'Klassifikationen';
			$entries['translations']['legendActuality'] = 'Zeitliche Einschränkung';
			$entries['translations']['helpInspireThemes'] = 'Entsprechend der 34 Annex-Themen der EU-INSPIRE-Richtlinie können hier Einzelthemen (auch Mehrfachauswahl) ausgesucht werden.';
			$entries['translations']['helpSearchText'] = 'Hilfe zur Textsuche. Bitte geben Sie hier ein oder kommasepariert mehrere Suchbegriffe ein. Die Begriffe werden für eine Volltextsuche über Titel, Beschreibung und Keywords verwendet. Die Verknüpfung der Suchbegriffe geschieht über ein UND. Je mehr Begriffe eingegeben werden, desto weniger Treffer werden gefunden.';
			# <a href="http://www.geoportal.rlp.de">testlink</a> - can be included in the content of a dialog
			$entries['translations']['helpIsoCategories'] = 'Die 20 Themen, die in der Norm ISO19115 definiert sind, sind weltweit abgestimmt und sollten bei der Beschreibung von Geodaten immer mit angegeben werden um eine eindeutige Identifizierbarkeit zu ermöglichen. Eine entsprechende Auswahl/Ergebniseinschränkung ist hier möglich.';
			$entries['translations']['helpCustomCategories'] = 'Falls individuelle Themenkategorien angelegt wurden, sind diese hierrüber anwählbar.';
			$entries['translations']['helpOrderBy'] = 'Hier können Sie angeben, nach welchen Kriterien die Ergebnisse sortiert werden sollen. „Nachfrage“ bedeutet, dass die am häufigsten aufgerufenen  Ergebnisse an oberste Stelle kommen. „Alphabetisch“ bedeutet eine alphabetische Sortierung. Die „Ident. Nummer“ ist eine automatisch generierte Zahl, über die eine Ressource eindeutig identifiziert werden kann. In der Trefferanzeige werden dann die Treffer nach Diensten gruppiert.  „Letzte Änderung“ bedeutet eine Sortierung nach der Aktualität der Metainformationen wodurch die neuesten Informationen an erster Stelle stehen.';
			$entries['translations']['helpSpatialFilter'] = 'Hier können Sie eine räumliche Einschränkung festsetzen. Die Einschränkung wird mit Ihrem Suchbegriff verknüpft. Sie bekommen nur Treffer, die sowohl den Suchbegriff beinhalten, als auch in dem von Ihnen definierten Gebiet liegen.';
			$entries['translations']['helpProvider'] = 'Hier finden Sie eine Auflistung aller Anbieter von GeoWebDiensten. Wenn Sie nur Daten eines Anbieters suchen, dann wählen Sie den oder die entsprechenden aus.';
			$entries['translations']['helpDateOfPublication'] = 'Geben Sie hier das Datum der Veröffentlichung der beschreibenden Informationen (Metadaten) ein bzw. einen Zeitraum, in dem diese Veröffentlichung stattgefunden haben kann. Das Datum bezeichnet dabei den Zeitpunkt der letztmaligen Aktualisierung der beschreibenden Informationen.';
			$entries['translations']['helpDateOfLastRevision'] = 'Tragen Sie hier den Zeitraum (bzw. nur das Start- oder das Enddatum) ein, auf denen die Daten stammen bzw. in dem sie aktualisiert wurden. <b>Hinweis: Diese Funktion steht erst ab Mitte 2011 zu Verfügung.</b>';
			$entries['translations']['helpSearchResources'] = 'Hierbei können Sie sich aussuchen, welche Art von Ressourcen Sie suchen wollen – je nach Auswahl wird dann nur dieser Ressourcentyp in der Trefferanzeige aufgeführt.';

			$exceptionGroupTitle = "Es wurde noch kein Titel für die Gruppe eingestellt!";
			break;
		case 'en':
			$entries['translations']['extendedSearchTitle'] = 'Extended Search';
			$entries['translations']['extendedSearchTitleBc'] = 'Extended Search';
			$entries['translations']['legendSearchTextTitle'] = 'Searchterm(s)';
			$entries['translations']['legendSpatialFilterTitle'] = 'Spatial Filter';
			$entries['translations']['labelSpatialFilter'] = 'activate spatial filter';
			$entries['translations']['labelSpatialFilterType'] = 'how?';
			$entries['translations']['searchTypeBboxIntersects'] = 'intersects';
			$entries['translations']['searchTypeBboxOutside'] = 'outside test';
			$entries['translations']['searchTypeBboxInside'] = 'fully inside';
			$entries['translations']['orderByRelevance'] = 'demand';
			$entries['translations']['orderByTitle'] = 'alphabetically';
			$entries['translations']['orderById'] = 'identification number';
			$entries['translations']['orderByDate'] = 'last change';
			$entries['translations']['legendOrderBy'] = 'Sort by:';
			$entries['translations']['legendDepartment'] = 'Provider:';
			$entries['translations']['legendInspireThemes'] = 'Inspire themes';
			$entries['translations']['legendIsoCategories'] = 'ISO19115 themes';
			$entries['translations']['legendCustomCategories'] = 'Other themes';
			$entries['translations']['legendDateOfPublication'] = 'Date of publication/last revision';
			$entries['translations']['legendDateOfLastRevision'] = 'Data Actuality';
			$entries['translations']['labelDateOfPublicationStart'] = 'Date from:';
			$entries['translations']['labelDateOfPublicationEnd'] = 'Date to:';
			$entries['translations']['labelDateOfLastRevisionStart'] = 'Date from:';
			$entries['translations']['labelDateOfLastRevisionEnd'] = 'Date to:';
			$entries['translations']['legendSearchResources'] = 'Types of resources';
			$entries['translations']['labelIntersects'] = 'intersects';
			$entries['translations']['labelOutside'] = 'outside';
			$entries['translations']['labelInside'] = 'completely inside';
			$entries['translations']['labelOrderByRank'] = 'demand';
			$entries['translations']['labelOrderByTitle'] = 'alphabetically';
			$entries['translations']['labelOrderById'] = 'identification number';
			$entries['translations']['labelOrderByDate'] = 'last change';
			$entries['translations']['labelCheckResourcesWms'] = 'Viewing Services';
			$entries['translations']['labelCheckResourcesWfs'] = 'Search/Download/Digitize modules';
			$entries['translations']['labelCheckResourcesWmc'] = 'Map Collections';
			$entries['translations']['labelCheckResourcesGeorss'] = 'GeoRSS Newsfeeds';
			$entries['translations']['search'] = 'Start Search';
			$entries['translations']['deleteSelection1'] = 'Delete current selection';
			$entries['translations']['deleteSelection2'] = 'Delete current selection';
			$entries['translations']['deleteSelection3'] = 'Delete current selection';
			$entries['translations']['deleteSelection4'] = 'Delete current selection';
			$entries['translations']['classificationsLegend'] = 'Classifications';
			$entries['translations']['legendActuality'] = 'Temporal Filter';
			$entries['translations']['helpInspireThemes'] = 'According to the EU-INSPIRE Directive one or a few of the 34 ANNEX-themes can be selected.';
			$entries['translations']['helpSearchText'] = 'Please use one or several search words (separated with a comma). These words will be used for a full text search of title, description and keyword. The more words you use, the less results will be found.';
			$entries['translations']['helpIsoCategories'] = 'These 20 themes which are defined in norm ISO19115 are adjusted worldwide and should be also given with the description of Geodata to allow for a unique identification. An adequate selection/limitation of outputs is possible.';
			$entries['translations']['helpCustomCategories'] = 'These categories are common to either the SDI of Rhineland-Palatinate or to the SDI of Germany (GDI-DE).';
			$entries['translations']['helpOrderBy'] = 'You can select the kind of sorting which will be used for the outputs. „Demand“ puts the most selected outputs first. The „identification number“ is an automaticly generated number which makes a specific identification possible. „Last change“ sort the outputs to actuality so the newest information will come first.';
			$entries['translations']['helpSpatialFilter'] = 'You can appoint regional constraints. The constraint will be attached with your search word. You will just get outputs, which include the search word and which find oneself in this defined area.';
			$entries['translations']['helpProvider'] = 'You can find a list of all providers of GeoWebServices. If you ar just looking for Services of one provider, please choose this one or these ones.';
			$entries['translations']['helpDateOfPublication'] = 'You can enter the date of publication of the describing informations (metadata) or a period in which this publication could have been proceeded. This date constitutes the point of the last update of the describing informations.';
			$entries['translations']['helpDateOfLastRevision'] = 'Please enter in here the period (or just the first or last date) from which the dates come from or in which they were updated. <b>Attention: This function just works from middle 2011.</b>';
			$entries['translations']['helpSearchResources'] = 'You can select in this category, which kind of ressource you are looking for. According to this assortment only this type of ressource will be enlisted in the output index.';

			$exceptionGroupTitle = "The title for this group have not been defined till now!";

			break;
		case 'fr':
			break;
		case 'es':

		
			break;
		default:
	}
	$entries['user_department'] = array();
	$entries['group_name'] = array();
	$entries['group_title'] = array();

	$sql= "SELECT mb_group_id, mb_group_name, mb_group_title from registrating_groups LEFT OUTER JOIN mb_group ON (mb_group_id=fkey_mb_group_id)  GROUP BY mb_group_id, mb_group_name, mb_group_title ORDER BY mb_group_name";
	$res = pg_query($sql);
	while($row = db_fetch_array($res)){
		array_push($entries['user_department'], $row['mb_group_id']);
		array_push($entries['group_name'], $row['mb_group_name']);
		if (isset($row['mb_group_title']) and $row['mb_group_title'] != ''){
			array_push($entries['group_title'], $row['mb_group_title']);
		} else {
			array_push($entries['group_title'], $exceptionGroupTitle);
		}
	}
	$maxStrLength = 200;
	//get list of iso categories
	$entries['iso_cat_id'] = array();
	$entries['iso_cat_name'] = array();
	$entries['iso_cat_title'] = array();
	/*
	 * @security_patch sqli done
	 */
	$sql_cat= "SELECT * FROM md_topic_category order by md_topic_category_code_".pg_escape_string($langCode);
	$res_cat = pg_query($sql_cat);
	while($row_cat = db_fetch_array($res_cat)){
		array_push($entries['iso_cat_id'], $row_cat['md_topic_category_id']);
		array_push($entries['iso_cat_name'], substr($row_cat["md_topic_category_code_".$langCode], 0,$maxStrLength));
		array_push($entries['iso_cat_title'], $row_cat["md_topic_category_code_".$langCode]);
	}
	//get list of inspire themes
	$entries['inspire_cat_id'] = array();
	$entries['inspire_cat_name'] = array();
	$entries['inspire_cat_title'] = array();
	$sql_cat= "SELECT * FROM inspire_category order by inspire_category_code_".$langCode;
	$res_cat = pg_query($sql_cat);
	while($row_cat = db_fetch_array($res_cat)){
		array_push($entries['inspire_cat_id'], $row_cat['inspire_category_id']);
		array_push($entries['inspire_cat_name'], substr($row_cat["inspire_category_code_".$langCode], 0,$maxStrLength));
		array_push($entries['inspire_cat_title'], $row_cat["inspire_category_key"]." ".$row_cat["inspire_category_code_".$langCode]);
	}
	//get list of custom categories
	$entries['custom_cat_id'] = array();
	$entries['custom_cat_name'] = array();
	$entries['custom_cat_title'] = array();
	$sql_cat= "SELECT * FROM custom_category WHERE custom_category_hidden != 1 order by custom_category_code_".$langCode;
	$res_cat = pg_query($sql_cat);
	while($row_cat = db_fetch_array($res_cat)){
		array_push($entries['custom_cat_id'], $row_cat['custom_category_id']);
		array_push($entries['custom_cat_name'], substr($row_cat["custom_category_code_".$langCode], 0,$maxStrLength));
		array_push($entries['custom_cat_title'], $row_cat["custom_category_code_".$langCode]);
	}
	return $entries;
}

/**
 * sends output of function back to client
 * 
 * @param mixed[] the output to send
 * @return 
 */
function sendOutput($out){
	global $json;
	$output = $json->encode($out);
	header("Content-Type: text/x-json");
	echo $output;
}
?>
