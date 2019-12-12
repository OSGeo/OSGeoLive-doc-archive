<?php
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchText=sozial&outputFormat=json&resultTarget=web&searchResources=wfs
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchText=sozial+schwanger&outputFormat=json&resultTarget=web&searchResources=wfs&registratingDepartments=72
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchText=sozial&outputFormat=json&resultTarget=web&searchResources=wms&
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchText=niedersachsen&outputFormat=json&resultTarget=web&searchResources=wms&maxResults=50
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=wal&searchText=wald&outputFormat=json&resultTarget=web&searchResources=wms&maxResults=10
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=test&searchText=e,w&outputFormat=json&registratingDepartments=44,33,29,30,31,35,40,61,101,87&languageCode=en&resultTarget=web&searchResources=wms&maxResults=10
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=45&searchText=e&outputFormat=json&registratingDepartments=44,33,29,30,31,35,40,61,101,87&languageCode=en&resultTarget=debug&searchResources=wms&maxResults=10&searchPages=1&isoCategories=10
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=45&searchText=e&outputFormat=json&registratingDepartments=44,33,29,30,31,35,40,61,101,87&languageCode=en&resultTarget=debug&searchResources=wms&maxResults=20&searchPages=1&isoCategories=10,5
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=wa&searchText=e&outputFormat=json&languageCode=de&resultTarget=debug&searchResources=wms&maxResults=10&isoCategories=10,5&registratingDepartments=44,31
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=wa&searchText=e&outputFormat=json&languageCode=de&resultTarget=debug&searchResources=wms&maxResults=5&registratingDepartments=44,31&inspireThemes=11&isoCategories=5,10
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=wa&searchText=e&outputFormat=json&languageCode=de&resultTarget=debug&searchResources=wms&maxResults=5&registratingDepartments=44,31&inspireThemes=11&isoCategories=5,10
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=wa&searchText=e&outputFormat=json&languageCode=de&resultTarget=debug&searchResources=wms&maxResults=5&registratingDepartments=44,31&inspireThemes=11&isoCategories=5,10&searchBbox=7,48,9,51
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=wa&searchText=e&outputFormat=json&languageCode=de&resultTarget=debug&searchResources=wms&maxResults=99&registratingDepartments=44,31,52&inspireThemes=11&isoCategories=5,10&searchBbox=7,48,9,51&regTimeBegin=2001-12-24&regTimeEnd=2020-10-10
#http://www.geoportal.rlp.de/mapbender/php/mod_callMetadata.php?searchId=wa&outputFormat=json&languageCode=de&resultTarget=debug&searchResources=wms&maxResults=99&registratingDepartments=44,31,52&inspireThemes=11&isoCategories=5,10&searchBbox=7,48,9,51&regTimeBegin=2001-12-24&regTimeEnd=2020-10-10
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../../conf/geoportal.conf");#???
require_once(dirname(__FILE__)."/../classes/class_metadata_new.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");

//initialize request parameters:
$searchId = "dummysearch";
$searchText = "*";
#$registratingDepartments = "33,29,30,31,35,40,61,101,87,44";
$registratingDepartments = NULL;
#$isoCategories = "1,2,3";
$isoCategories = NULL;
$inspireThemes = NULL;
$customCategories = NULL;
$timeBegin = NULL;
$timeEnd = NULL;
$regTimeBegin = NULL;
$regTimeEnd = NULL;
$maxResults = 5;
#$searchBbox = "-180.0,-90.0,180.0,90.0";
$searchBbox = NULL;
$searchTypeBbox = "intersects"; //outside / inside
$accessRestrictions = "false"; 
$languageCode = "de";
$outputFormat = 'json';
#$searchResources = "wms,wfs,wmc,georss";
#$searchResources = "wms";
$searchPages = "1";
$resultTarget = "debug";
$preDefinedMaxResults = array(5,10,15,20,25,30);
$searchEPSG = "EPSG:31466";
$classJSON = new Mapbender_JSON;
#$tempFolder = "/tmp";
$tempFolder = TMPDIR;
$orderBy = "rank"; //rank or title or id or date
$hostName = $_SERVER['HTTP_HOST'];

//read the whole query string:
$searchURL = $_SERVER['QUERY_STRING'];
//decode it !
$searchURL = urldecode($searchURL);
//control if some request variables are not set and set them explicit to NULL

$checkForNullRequests = array("registratingDepartments","isoCategories","inspireThemes","customCategories","regTimeBegin","regTimeEnd","timeBegin","timeEnd","searchBbox","searchTypeBbox","searchResources","orderBy","hostName");

for($i=0; $i < count($checkForNullRequests); $i++){
	if (!$_REQUEST[$checkForNullRequests[$i]] or $_REQUEST[$checkForNullRequests[$i]] == 'false' or $_REQUEST[$checkForNullRequests[$i]] == 'undefined') {
		$_REQUEST[$checkForNullRequests[$i]] = "";
		$searchURL = delTotalFromQuery($checkForNullRequests[$i],$searchURL);
	}
}

//Read out request Parameter:
if (isset($_REQUEST["searchId"]) & $_REQUEST["searchId"] != "") {
	//gernerate md5 representation, cause the id is used as a filename later on! - no validation needed
	$searchId = md5($_REQUEST["searchId"]);
}
if (isset($_REQUEST["searchText"]) & $_REQUEST["searchText"] != "") {
	$test="(SELECT\s[\w\*\)\(\,\s]+\sFROM\s[\w]+)| (UPDATE\s[\w]+\sSET\s[\w\,\'\=]+)| (INSERT\sINTO\s[\d\w]+[\s\w\d\)\(\,]*\sVALUES\s\([\d\w\'\,\)]+)| (DELETE\sFROM\s[\d\w\'\=]+)";
	//validate to csv integer list
	$testMatch = $_REQUEST["searchText"];
	$pattern = '/(\%27)|(\')|(\-\-)|(\")|(\%22)/';		
 	if (preg_match($pattern,$testMatch)){ 
		echo 'searchText: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$searchText = $testMatch;
	$testMatch = NULL;
	if ($searchText ==='false') {
		$searchText ='*';
	}
}
if (isset($_REQUEST["registratingDepartments"]) & $_REQUEST["registratingDepartments"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["registratingDepartments"];
	$pattern = '/^[\d,]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'registratingDepartments: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$registratingDepartments = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["isoCategories"]) & $_REQUEST["isoCategories"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["isoCategories"];
	$pattern = '/^[\d,]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'isoCategories: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$isoCategories = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["inspireThemes"]) & $_REQUEST["inspireThemes"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["inspireThemes"];
	$pattern = '/^[\d,]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'inspireThemes: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$inspireThemes = $testMatch;
	$testMatch = NULL;
}

if (isset($_REQUEST["customCategories"]) & $_REQUEST["customCategories"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["customCategories"];
	$pattern = '/^[\d,]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'customCategories: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$customCategories = $testMatch;
	$testMatch = NULL;
}

if (isset($_REQUEST["timeBegin"]) & $_REQUEST["timeBegin"] != "") {
	//validate to iso date format YYYY-MM-DD
	$testMatch = $_REQUEST["timeBegin"];
	$pattern = '/^(19|20)[0-9]{2}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'timeBegin: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$timeBegin = $testMatch;
	$testMatch = NULL;	
}
if (isset($_REQUEST["timeEnd"]) & $_REQUEST["timeEnd"] != "") {
	$testMatch = $_REQUEST["timeEnd"];
	$pattern = '/^(19|20)[0-9]{2}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'timeEnd: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$timeEnd = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["regTimeBegin"]) & $_REQUEST["regTimeBegin"] != "") {
	//validate to iso date format YYYY-MM-DD
	$testMatch = $_REQUEST["regTimeBegin"];
	$pattern = '/^(19|20)[0-9]{2}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'regTimeBegin: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$regTimeBegin = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["regTimeEnd"]) & $_REQUEST["regTimeEnd"] != "") {
	//validate to iso date format YYYY-MM-DD
	$testMatch = $_REQUEST["regTimeEnd"];
	$pattern = '/^(19|20)[0-9]{2}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'regTimeEnd: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$regTimeEnd = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["maxResults"]) & $_REQUEST["maxResults"] != "") {
	//validate integer to 100 - not more
	$testMatch = $_REQUEST["maxResults"];
	//give max 99 entries - more will be to slow
	$pattern = '/^([0-9]{0,1})([0-9]{1})$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'maxResults: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$maxResults = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["searchBbox"]) & $_REQUEST["searchBbox"] != "") {
	//validate to float/integer
	$testMatch = $_REQUEST["searchBbox"];
	//$pattern = '/^[-\d,]*$/';	
	$pattern = '/^[-+]?([0-9]*\.[0-9]+|[0-9]+)*$/';
	$testMatchArray = explode(',',$testMatch);
 	if (count($testMatchArray) != 4) {
		echo 'searchBbox: <b>'.$testMatch.'</b> has a wrong amount of entries.<br/>'; 
		die(); 
	}
	for($i=0; $i<count($testMatchArray);$i++){
		if (!preg_match($pattern,$testMatchArray[$i])){ 
			echo 'searchBbox: <b>'.$testMatchArray[$i].'</b> is not a valid coordinate value.<br/>'; 
			die(); 		
 		}
	}
	$searchBbox = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["searchTypeBbox"]) & $_REQUEST["searchTypeBbox"] != "") {
	//validate to inside / outside - TODO implement other ones than intersects which is default
	$testMatch = $_REQUEST["searchTypeBbox"];	
 	if (!($testMatch == 'inside' or $testMatch == 'outside' or $testMatch == 'intersects')){ 
		echo 'searchTypeBbox: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$searchTypeBbox = $testMatch; //TODO activate this
	$testMatch = NULL;
}
if (isset($_REQUEST["accessRestrictions"]) & $_REQUEST["accessRestrictions"] != "") {
	//validate to ?
	#TODO implement me //$accessRestrictions = $_REQUEST["accessRestrictions"];
}
if (isset($_REQUEST["languageCode"]) & $_REQUEST["languageCode"] != "") {
	//validate to de, en, fr
	$testMatch = $_REQUEST["languageCode"];	
 	if (!($testMatch == 'de' or $testMatch == 'en' or $testMatch == 'fr')){ 
		echo 'languageCode: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$languageCode = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["outputFormat"]) & $_REQUEST["outputFormat"] != "") {
	$testMatch = $_REQUEST["outputFormat"];	
 	if (!($testMatch == 'json' or $testMatch == 'georss')){ 
		echo 'outputFormat: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$outputFormat = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["hostName"]) & $_REQUEST["hostName"] != "") {
	//validate to some hosts
	$testMatch = $_REQUEST["hostName"];	
	//look for whitelist in mapbender.conf
	$HOSTNAME_WHITELIST_array = explode(",",HOSTNAME_WHITELIST);
	if (!in_array($testMatch,$HOSTNAME_WHITELIST_array)) {
		echo "Requested hostname <b>".$testMatch."</b> not whitelist! Please control your mapbender.conf.";
		$e = new mb_notice("Whitelist: ".HOSTNAME_WHITELIST);
		$e = new mb_notice($testMatch." not found in whitelist!");
		die(); 	
	}
	$hostName = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["orderBy"]) & $_REQUEST["orderBy"] != "") {
	$testMatch = $_REQUEST["orderBy"];	
 	if (!($testMatch == 'rank' or $testMatch == 'title' or $testMatch == 'id' or $testMatch == 'date')){ 
		echo 'orderBy: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$orderBy = $testMatch;
	$testMatch = NULL;
}// else {
//$orderBy= 'rank';
//}
if (isset($_REQUEST["searchResources"]) & $_REQUEST["searchResources"] != "") {
	//validate to wms,wfs,wmc,georss
	$testMatch = $_REQUEST["searchResources"];
	#$pattern = '/^(19|20)[0-9]{2}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/';
	$countSR = count(explode(',',$testMatch));
 	if (!($countSR >= 1 && $countSR <= 4)){ 
		echo 'searchResources: <b>'.$testMatch.'</b> count of requested resources out of sync.<br/>'; 
		die(); 		
 	} else {
		$testArray = explode(',',$testMatch);
		for($i=0; $i<count($testArray);$i++){
			if (!($testArray[$i] == 'wms' or $testArray[$i] == 'wfs' or $testArray[$i] == 'wmc' or $testArray[$i] == 'daData')) {
			echo 'searchResources: <b>'.$testMatch.'</b>at least one of them does not exists!<br/>'; 
			die();
			}
		}
		unset($i);
	}
	$searchResources = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["searchPages"]) & $_REQUEST["searchPages"] != "") {
	//validate to csv integer list with dimension of searchResources list
	$testMatch = $_REQUEST["searchPages"];
	$pattern = '/^[-\d,]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'searchPages: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	if (count(explode(',',$testMatch)) != count(explode(',',$searchResources))) {
		echo 'searchPages: <b>'.$testMatch.'</b> has a wrong amount of entries.<br/>'; 
		die(); 
	}
	$searchPages = $testMatch;
	$testMatch = NULL;
#$searchPages = $_REQUEST["searchPages"];
	#$searchPages = split(',',$searchPages);
	
}
if (isset($_REQUEST["resultTarget"]) & $_REQUEST["resultTarget"] != "") {
	//validate to web,debug,file
	$testMatch = $_REQUEST["resultTarget"];	
 	if (!($testMatch == 'web' or $testMatch == 'debug' or $testMatch == 'file')){ 
		echo 'resultTarget: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$resultTarget = $testMatch;
	$testMatch = NULL;
}
$e = new mb_exception("UserID GET: ".$_REQUEST['userId']);
$e = new mb_exception("UserID from session (new): ".Mapbender::session()->get("mb_user_id"));
$e = new mb_exception("UserID from session (old): ".$_SESSION['mb_user_id']);

if (isset($_REQUEST["userId"]) & $_REQUEST["userId"] != "") {
        //validate integer to 100 - not more
        $testMatch = $_REQUEST["userId"];
        //give max 99 entries - more will be to slow
        $pattern = '/^[0-9]*$/';  
        if (!preg_match($pattern,$testMatch)){
                echo 'userId: <b>'.$testMatch.'</b> is not valid.<br/>';
                die();
        }
        $userId = $testMatch;
        $testMatch = NULL;
#
} else { //look for id in session
  $userId = Mapbender::session()->get("mb_user_id");
  if ($userId == false) {
	  $userId = PUBLIC_USER;
    }
}



#$searchResources = array('wms','wfs','wmc','georss');
#$searchPages = array(1,1,1,1);

//TODO: if class is called directly












if ($resultTarget == 'debug') {
	echo "<br>DEBUG: searchURL: ".$searchURL."<br>";
	#echo "<br>DEBUG: languageCode: ".$languageCode."<br>";
}


if ($resultTarget == 'file') {
	if (!isset($searchResources) OR ($searchResources == "")) {
		$searchResources = "wms,wfs,wmc";
		$searchPages = "1,1,1";
	}

}
if ($resultTarget == 'web' or $resultTarget == 'debug') {
	if (!isset($searchResources) OR ($searchResources == "")) {
		$searchResources = "wms";
		$searchPages = "1";
	}

}

//convert the respources and the pagenumbers into arrays
$searchResources = explode(",",$searchResources);
$searchPages = explode(",",$searchPages);

//Generate search filter file. This file holds the defined search filter to allow the user to see how he searched 
//The user should become the possibility to drop the search filters by clicking in some buttons
//list of options to display:
//searchText (textfields) - dropping only if more than one text is given
//registratingDepartments (list) - dropping allowed - maybe give a webservice for mb_group data
//isoCategories - dropping allowed
//inspireThemes - dropping allowed
//customCategories - dropping allowed
//bbox (show) - dropping allowed
//regTimeBegin - dropping allowed
//regTimeEnd -dropping allowed
//the idea is, to rewrite the searchURL directly and then have another url for the special case!
//we have to get the searchURL as a parameter for this wrapper cause the class_metadata should give filters for the found categories
//use regular expressions to do this!

//define internationalization
//searchText
//registratingDepartments
//bbox

//generate query json:
//some objects like names of categories and other objects have to be pulled of the database. Maybe a webservice is the better way? But now there are no webservices - therefor: pull the names out of the database into arrays - only those who are requested:

//function to get the information about the registrating departments (mb_groups) out of the mapbender database
function get_registratingDepartmentsArray($departmentIds,$languageCode) {
			$sql = "SELECT mb_group_id, mb_group_name FROM mb_group WHERE mb_group_id IN (";
			$v = array();
			$t = array();
			$departmentsArray = array();
			for($i=0; $i<count($departmentIds);$i++){
				if($i > 0){$sql .= ",";}
				$sql .= "$".strval($i+1);
				array_push($v,$departmentIds[$i]);
				array_push($t,"i");
			}
			$sql .= ")";
			$res = db_prep_query($sql,$v,$t);
			$countDepArray = 0;
			while($row = db_fetch_array($res)){
				$departmentsArray[$countDepArray]["id"] = $row["mb_group_id"];
				$departmentsArray[$countDepArray]["name"] = $row["mb_group_name"];
				$departmentsArray[$countDepArray]["showScript"] = "../php/mod_showRegistratingGroup.php?";
				$countDepArray = $countDepArray + 1; 
			}
			return $departmentsArray;
		}

//get the information about the requested isoCategories
function get_isoCategoriesArray($isoCategoryIds,$languageCode) {
			
			$sql = "SELECT md_topic_category_id, md_topic_category_code_".$languageCode;
			#$e = new mb_exception("php/mod_callMetadata.php: language code: ".$languageCode);
			$sql .= " FROM md_topic_category WHERE md_topic_category_id IN (";
			$v = array();
			$t = array();
			$isoCategoryArray = array();
			for($i=0; $i<count($isoCategoryIds);$i++){
				if($i > 0){$sql .= ",";}
				$sql .= "$".strval($i+1);
				array_push($v,$isoCategoryIds[$i]);
				array_push($t,"i");
			}
			$sql .= ")";
			#$e = new mb_exception("php/mod_callMetadata.php: sql for getting topic cats: ".$sql);
			$res = db_prep_query($sql,$v,$t);
			$countIsoArray = 0;
			while($row = db_fetch_array($res)){
				$isoCategoryArray[$countIsoArray]["id"] = $row["md_topic_category_id"];
				$isoCategoryArray[$countIsoArray]["name"] = $row["md_topic_category_code_".$languageCode];
				$countIsoArray = $countIsoArray + 1; 
			}
			return $isoCategoryArray;
		}

//get the information about the inspireThemes
function get_inspireThemesArray($inspireThemesIds,$languageCode) {
			$sql = "SELECT inspire_category_id, inspire_category_code_".$languageCode." FROM inspire_category WHERE inspire_category_id IN (";
			$v = array();
			$t = array();
			$inspireCategoryArray = array();
			for($i=0; $i<count($inspireThemesIds);$i++){
				if($i > 0){$sql .= ",";}
				$sql .= "$".strval($i+1);
				array_push($v,$inspireThemesIds[$i]);
				array_push($t,"i");
			}
			$sql .= ")";
			$e = new mb_exception("php/mod_callMetadata.php: sql for getting inspire cats: ".$sql);
			$res = db_prep_query($sql,$v,$t);
			$countInspireArray = 0;
			while($row = db_fetch_array($res)){
				$inspireCategoryArray[$countInspireArray]["id"] = $row["inspire_category_id"];
				$inspireCategoryArray[$countInspireArray]["name"] = $row["inspire_category_code_".$languageCode];
				$countInspireArray = $countInspireArray + 1; 
			}
			return $inspireCategoryArray;
		}

function get_customCategoriesArray($customCategoriesIds,$languageCode) {
			$sql = "SELECT custom_category_id, custom_category_code_".$languageCode." FROM custom_category WHERE custom_category_id IN (";
			$v = array();
			$t = array();
			$customCategoryArray = array();
			for($i=0; $i<count($customCategoriesIds);$i++){
				if($i > 0){$sql .= ",";}
				$sql .= "$".strval($i+1);
				array_push($v,$customCategoriesIds[$i]);
				array_push($t,"i");
			}
			$sql .= ")";
			$e = new mb_exception("php/mod_callMetadata.php: sql for getting custom cats: ".$sql);
			$res = db_prep_query($sql,$v,$t);
			$countCustomArray = 0;
			while($row = db_fetch_array($res)){
				$customCategoryArray[$countCustomArray]["id"] = $row["custom_category_id"];
				$customCategoryArray[$countCustomArray]["name"] = $row["custom_category_code_".$languageCode];
				$countCustomArray = $countCustomArray + 1; 
			}
			return $customCategoryArray;
		}







//define where to become the information from - this is relavant for the information which must be pulled out of the database
$classificationElements = array();





$classificationElements[0]['name'] = 'searchText';
$classificationElements[1]['name'] = 'registratingDepartments';
$classificationElements[2]['name'] = 'isoCategories';
$classificationElements[3]['name'] = 'inspireThemes';
$classificationElements[4]['name'] = 'customCategories';
$classificationElements[5]['name'] = 'searchBbox';
$classificationElements[6]['name'] = 'regTimeBegin';
$classificationElements[7]['name'] = 'regTimeEnd';

$classificationElements[0]['source'] = '';
$classificationElements[1]['source'] = 'database';
$classificationElements[2]['source'] = 'database';
$classificationElements[3]['source'] = 'database';
$classificationElements[4]['source'] = 'database';
$classificationElements[5]['source'] = '';
$classificationElements[6]['source'] = '';
$classificationElements[7]['source'] = '';

$classificationElements[0]['list'] = true;
$classificationElements[1]['list'] = true;
$classificationElements[2]['list'] = true;
$classificationElements[3]['list'] = true;
$classificationElements[4]['list'] = true;
$classificationElements[5]['list'] = false;
$classificationElements[6]['list'] = false;
$classificationElements[7]['list'] = false;

//Defining of the different result categories		
		$resourceCategories = array();
		#$resourceCategories[0]['wms'] = 'WMS';
		#$resourceCategories[1]['wfs'] = 'WFS';
		#$resourceCategories[2]['wmc'] = 'WMC';
		#$resourceCategories[3]['georss'] = 'directAccessData';
		

switch($languageCode){
        case 'de':
        	$classificationElements[0]['name2show'] = 'Suchbegriff(e):';
		$classificationElements[1]['name2show'] = 'Anbietende Stelle(n):';
		$classificationElements[3]['name2show'] = 'INSPIRE Themen:';
		$classificationElements[2]['name2show'] = 'ISO Kategorien:';
		$classificationElements[4]['name2show'] = 'RP Kategorien:';
		$classificationElements[5]['name2show'] = 'Räumliche Einschränkung:';
		$classificationElements[6]['name2show'] = 'Registrierung/Aktualisierung von:';
		$classificationElements[7]['name2show'] = 'Registrierung/Aktualisierung bis:';

		$resourceCategories['wms'] = 'Darstellungsdienste';
		$resourceCategories['wfs'] = 'Such- und Download- und Erfassungsmodule';
		$resourceCategories['wmc'] = 'Kartenzusammenstellungen';
		$resourceCategories['georss'] = 'KML/Newsfeeds';

		$orderByTitle['header'] = 'Sortierung nach:';
		$orderByTitle['id'] = 'Identifizierungsnummer';
		$orderByTitle['title'] = 'Alphabetisch';
		$orderByTitle['rank'] = 'Nachfrage';
		$orderByTitle['date'] = 'Letzte Änderung';

		$maxResultsTitle['header'] = 'Treffer pro Seite:'; 

	
       	break;
        case 'en':
        	$classificationElements[0]['name2show'] = 'Search Term(s):';
		$classificationElements[1]['name2show'] = 'Department(s):';
		$classificationElements[3]['name2show'] = 'INSPIRE Themes:';
		$classificationElements[2]['name2show'] = 'ISO Topic Categories:';
		$classificationElements[4]['name2show'] = 'RP Categories:';
		$classificationElements[5]['name2show'] = 'Spatial Filter:';
		$classificationElements[6]['name2show'] = 'Registration/Update from:';
		$classificationElements[7]['name2show'] = 'Registration/Update till:';

		$resourceCategories['wms'] = 'Viewingservices';
		$resourceCategories['wfs'] = 'Search- and Downloadservices';
		$resourceCategories['wmc'] = 'Combined Maps';
		$resourceCategories['georss'] = 'KML/Newsfeeds';

		$orderByTitle['header'] = 'Sort by:';
		$orderByTitle['id'] = 'identification number';
		$orderByTitle['title'] = 'alphabetically';
		$orderByTitle['rank'] = 'demand';
		$orderByTitle['date'] = 'last change';

		$maxResultsTitle['header'] = 'Results per page:';

        break;
        case 'fr':
        	$classificationElements[0]['name2show'] = 'Suchbegriff(e):';
		$classificationElements[1]['name2show'] = 'Anbietende Stelle(n):';
		$classificationElements[3]['name2show'] = 'INSPIRE Themen:';
		$classificationElements[2]['name2show'] = 'ISO Kategorien:';
		$classificationElements[4]['name2show'] = 'RP Kategorien:';
		$classificationElements[5]['name2show'] = 'Räumliche Einschränkung:';
		$classificationElements[6]['name2show'] = 'Registrierung/Aktualisierung von:';
		$classificationElements[7]['name2show'] = 'Registrierung/Aktualisierung bis:';

		$resourceCategories['wms'] = 'Viewingservices';
		$resourceCategories['wfs'] = 'Search- and Downloadservices';
		$resourceCategories['wmc'] = 'Combined Maps';
		$resourceCategories['georss'] = 'KML/Newsfeeds';

		$orderByTitle['header'] = 'Sort by:';
		$orderByTitle['id'] = 'identification number';
		$orderByTitle['title'] = 'alphabetically';
		$orderByTitle['rank'] = 'demand';
		$orderByTitle['date'] = 'last change';

		$maxResultsTitle['header'] = 'Results per page:';

       	break;
     	default:
        	$classificationElements[0]['name2show'] = 'Suchbegriff(e):';
		$classificationElements[1]['name2show'] = 'Anbietende Stelle(n):';
		$classificationElements[3]['name2show'] = 'INSPIRE Themen:';
		$classificationElements[2]['name2show'] = 'ISO Kategorien:';
		$classificationElements[4]['name2show'] = 'RP Kategorien:';
		$classificationElements[5]['name2show'] = 'Räumliche Einschränkung:';
		$classificationElements[6]['name2show'] = 'Registrierung/Aktualisierung von:';
		$classificationElements[7]['name2show'] = 'Registrierung/Aktualisierung bis:';

		$resourceCategories['wms'] = 'Darstellungsdienste';
		$resourceCategories['wfs'] = 'Such- und Downloaddienste';
		$resourceCategories['wmc'] = 'Kartenzusammenstellungen';
		$resourceCategories['georss'] = 'KML/Newsfeeds';

		$orderByTitle['header'] = 'Sortierung nach:';
		$orderByTitle['id'] = 'ID';
		$orderByTitle['title'] = 'Titel';
		$orderByTitle['rank'] = 'Relevanz';
		$orderByTitle['date'] = 'Letzte Änderung';

		$maxResultsTitle['header'] = 'Results per page:';

}	

$queryJSON = new stdClass;
$queryJSON->searchFilter = (object) array();
$queryJSON->searchFilter->origURL = $searchURL;
#$queryJSON->searchFilter->classes = (object) array();
for($i=0; $i<count($searchResources);$i++){
//fill in the different search classes into the filter - the client can generate the headers out of this information
	$queryJSON->searchFilter->classes[$i]->title = $resourceCategories[$searchResources[$i]];
	$queryJSON->searchFilter->classes[$i]->name = $searchResources[$i];
}
//generate search filter file - if more categories are defined give 
#echo "<br> number of filter elements: ".count($classificationElements)."<br>";
for($i=0; $i < count($classificationElements); $i++){
	if (isset(${$classificationElements[$i]['name']}) & ${$classificationElements[$i]['name']} !='' & ${$classificationElements[$i]['name']} != NULL) {
		//pull register information out of database in arrays
		if ($classificationElements[$i]['source'] == 'database') {
			$funcName = "get_".$classificationElements[$i]['name']."Array";
			${$classificationElements[$i]['name']."Array"} = $funcName(explode(',',${$classificationElements[$i]['name']}),$languageCode);
		}
		$queryJSON->searchFilter->{$classificationElements[$i]['name']}->title = $classificationElements[$i]['name2show'];
		//check if the filter has subfilters - if not delete the whole filter from query
		if ($classificationElements[$i]['list'] == false) { //the object has no subsets - like bbox or time filters
			$queryJSON->searchFilter->{$classificationElements[$i]['name']}->delLink = delTotalFromQuery($classificationElements[$i]['name'],$searchURL);
			$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item = array();
			if ($classificationElements[$i]['name'] == 'searchBbox') {
				$sBboxTitle = $searchTypeBbox." ".${$classificationElements[$i]['name']};
				$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item[0]->title = $sBboxTitle;
			}
			else {
			$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item[0]->title = ${$classificationElements[$i]['name']};
			}
			$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item[0]->delLink = delTotalFromQuery($classificationElements[$i]['name'],$searchURL);
		} else {



			//TODO delete all entries of this main category (not for searchText)
			if ($classificationElements[$i]['name'] != 'searchText') { 
				$queryJSON->searchFilter->{$classificationElements[$i]['name']}->delLink = delTotalFromQuery($classificationElements[$i]['name'],$searchURL);
			} else {
				$queryJSON->searchFilter->{$classificationElements[$i]['name']}->delLink = NULL;
			}
			$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item = array();
			$queryArray = explode(',',${$classificationElements[$i]['name']});
			//loop for the subcategories
			for($j=0; $j < count($queryArray); $j++){
				if ($classificationElements[$i]['source'] == 'database') {
					$identArray = ${$classificationElements[$i]['name']."Array"};
					$identArray = flipDiagonally($identArray);
					//find searched id in information from database
					$key = array_search($queryArray[$j], $identArray['id']);
					if ($key === false) {
						$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item[$j]->title = "no information found in database";
					} else {
						$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item[$j]->title = ${$classificationElements[$i]['name']."Array"}[$key]['name'];
					}
				} else {
					$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item[$j]->title = $queryArray[$j];
				}
				//generate links to disable filters on a simple way
				if ($classificationElements[$i]['name'] === 'searchText' & count(explode(',',${$classificationElements[$i]['name']})) === 1) { 
					$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item[$j]->delLink = NULL;
				} else {
					$newSearchLink = delFromQuery($classificationElements[$i]['name'], $searchURL,$queryArray[$j],$queryArray,${$classificationElements[$i]['name']});
					$newSearchLink = delTotalFromQuery('searchId',$newSearchLink);
					$queryJSON->searchFilter->{$classificationElements[$i]['name']}->item[$j]->delLink = $newSearchLink;
				}
			}
		}
	}	
}

//generate filter for different maxResults entries
//$preDefinedMaxResults
if ($_REQUEST["maxResults"] == '') {
	$queryJSON->searchFilter->maxResults->header = $maxResultsTitle['header'];
	$queryJSON->searchFilter->maxResults->title = $preDefinedMaxResults[0];
	for ($i=0; $i<(count($preDefinedMaxResults)-1); $i++) {
		$queryJSON->searchFilter->maxResults->item[$i]->title = $preDefinedMaxResults[$i+1];
		$queryJSON->searchFilter->maxResults->item[$i]->url = $searchURL."&maxResults=".$preDefinedMaxResults[$i+1];
	}
} else {
	if (in_array($maxResults, $preDefinedMaxResults)) { //is part of preDefined array
		$queryJSON->searchFilter->maxResults->header = $maxResultsTitle['header'];
		$queryJSON->searchFilter->maxResults->title = $maxResults;
		//delete entry from array
		//$preDefinedMaxResultsRed = deleteEntry($preDefinedMaxResults, $maxResults);
		for ($i=0; $i<(count($preDefinedMaxResults)); $i++) {
			$queryJSON->searchFilter->maxResults->item[$i]->title = $preDefinedMaxResults[$i];
			$queryJSON->searchFilter->maxResults->item[$i]->url = $searchURL."&maxResults=".$preDefinedMaxResults[$i];
		} 
	} else { // is some other value 
		$queryJSON->searchFilter->maxResults->header = $maxResultsTitle['header'];
		$queryJSON->searchFilter->maxResults->title = $maxResults;
		for ($i=0; $i<(count($preDefinedMaxResults)); $i++) {
			$queryJSON->searchFilter->maxResults->item[$i]->title = $preDefinedMaxResults[$i];
			$queryJSON->searchFilter->maxResults->item[$i]->url = $searchURL."&maxResults=".$preDefinedMaxResults[$i];
		} 
	}
}






//generate filter for different order possibilities

//$queryJSON->searchFilter = (object) array();
if ($_REQUEST["orderBy"] == '') {
//echo "<br>orderBy:>".$_REQUEST["orderBy"]."<<br>";
	$queryJSON->searchFilter->orderFilter->header = $orderByTitle['header'];
	$queryJSON->searchFilter->orderFilter->title = $orderByTitle['rank'];
	$queryJSON->searchFilter->orderFilter->item[0]->title = $orderByTitle['title'];
	$queryJSON->searchFilter->orderFilter->item[0]->url = $searchURL."&orderBy=title";
	$queryJSON->searchFilter->orderFilter->item[1]->title = $orderByTitle['id'];
	$queryJSON->searchFilter->orderFilter->item[1]->url = $searchURL."&orderBy=id";
	$queryJSON->searchFilter->orderFilter->item[2]->title = $orderByTitle['date'];
	$queryJSON->searchFilter->orderFilter->item[2]->url = $searchURL."&orderBy=date";


} else {
//read out actual order filter
	$queryJSON->searchFilter->orderFilter->header = $orderByTitle['header'];
switch ($orderBy) {
					case "rank":
						$queryJSON->searchFilter->orderFilter->title = $orderByTitle['rank'];
						$queryJSON->searchFilter->orderFilter->item[0]->title = $orderByTitle['id'];
						
						$queryJSON->searchFilter->orderFilter->item[0]->url = str_replace("orderBy=rank", "orderBy=id", $searchURL);
						$queryJSON->searchFilter->orderFilter->item[1]->title = $orderByTitle['title'];

						$queryJSON->searchFilter->orderFilter->item[1]->url = str_replace("orderBy=rank", "orderBy=title", $searchURL);
$queryJSON->searchFilter->orderFilter->item[2]->title = $orderByTitle['date'];

						$queryJSON->searchFilter->orderFilter->item[2]->url = str_replace("orderBy=rank", "orderBy=date", $searchURL);


						break;
					case "id":
						$queryJSON->searchFilter->orderFilter->title = $orderByTitle['id'];
						$queryJSON->searchFilter->orderFilter->item[0]->title = $orderByTitle['rank'];

						$queryJSON->searchFilter->orderFilter->item[0]->url = str_replace("orderBy=id", "orderBy=rank", $searchURL);
						$queryJSON->searchFilter->orderFilter->item[1]->title = $orderByTitle['title'];

						$queryJSON->searchFilter->orderFilter->item[1]->url = str_replace("orderBy=id", "orderBy=title", $searchURL);
						$queryJSON->searchFilter->orderFilter->item[2]->title = $orderByTitle['date'];

						$queryJSON->searchFilter->orderFilter->item[2]->url = str_replace("orderBy=id", "orderBy=date", $searchURL);


						break;
					case "title":
						$queryJSON->searchFilter->orderFilter->title = $orderByTitle['title'];
						$queryJSON->searchFilter->orderFilter->item[0]->title = $orderByTitle['rank'];

						$queryJSON->searchFilter->orderFilter->item[0]->url = str_replace("orderBy=title", "orderBy=rank", $searchURL);
						$queryJSON->searchFilter->orderFilter->item[1]->title = $orderByTitle['id'];

						$queryJSON->searchFilter->orderFilter->item[1]->url = str_replace("orderBy=title", "orderBy=id", $searchURL);
						$queryJSON->searchFilter->orderFilter->item[2]->title = $orderByTitle['date'];

						$queryJSON->searchFilter->orderFilter->item[2]->url = str_replace("orderBy=title", "orderBy=date", $searchURL);
						break;
					case "date":
						$queryJSON->searchFilter->orderFilter->title = $orderByTitle['date'];
						$queryJSON->searchFilter->orderFilter->item[0]->title = $orderByTitle['rank'];

						$queryJSON->searchFilter->orderFilter->item[0]->url = str_replace("orderBy=date", "orderBy=rank", $searchURL);
						$queryJSON->searchFilter->orderFilter->item[1]->title = $orderByTitle['id'];

						$queryJSON->searchFilter->orderFilter->item[1]->url = str_replace("orderBy=date", "orderBy=id", $searchURL);
						$queryJSON->searchFilter->orderFilter->item[2]->title = $orderByTitle['title'];

						$queryJSON->searchFilter->orderFilter->item[2]->url = str_replace("orderBy=date", "orderBy=title", $searchURL);
						break;
					
}
}

//write out json to file or web
	
$queryFilter = $classJSON->encode($queryJSON);

if ($resultTarget == 'debug') {
	echo "<br>DEBUG: filter: ".$queryFilter."<br>";
	#echo "<br>DEBUG: searchTypeBbox: ".$searchTypeBbox."<br>";
}
if ($resultTarget == 'file') {
	$filename = $tempFolder."/".$searchId."_filter.json";
	if (file_exists($filename)) {
    		$e = new mb_notice("php/callMetdata.php: The file $filename exists - it will not be overwritten!");

	} else {
   		if($catFileHandle = fopen($filename, "w")){
			fwrite($catFileHandle,$queryFilter);
			fclose($catFileHandle);
			$e = new mb_notice("php/callMetdata.php: new filter_file created!");
		} else {
		$e = new mb_notice("php/callMetdata.php: cannot create filter_file!");
		}
	}
	
}
//function to transpose a matrix - sometimes needed to do an array search
function flipDiagonally($arr) {
    	$out = array();
    	foreach ($arr as $key => $subarr) {
      		foreach ($subarr as $subkey => $subvalue) {
        		$out[$subkey][$key] = $subvalue;
       		}
    	}
   	return $out;
}

//function to delete one of the comma separated values from one get request
function delFromQuery($paramName,$queryString,$string,$queryArray,$queryList) {
	//check if if count searchArray = 1
	if (count($queryArray) == 1){
		//remove request parameter from url by regexpr or replace
		$str2search = $paramName."=".$queryList;
		$str2exchange = "";
		$queryStringNew = str_replace($str2search, $str2exchange, $queryString);
		$queryStringNew = str_replace("&&", "&", $queryStringNew);
	} else {
	//there are more than one filter - reduce the filter  
		$objectList = "";
		for($i=0; $i < count($queryArray); $i++){
			if ($queryArray[$i] != $string){
				$objectList .= $queryArray[$i].",";		
			} 
		}
		//remove last comma
		$objectList = rtrim($objectList, ",");
		$str2search = $paramName."=".$queryList;
		echo "string to search: ".$str2search."<br>";
		$str2exchange = $paramName."=".$objectList;
		echo "string to exchange: ".$str2exchange."<br>";
		$queryStringNew = str_replace($str2search, $str2exchange, urldecode($queryString));
	}
	return $queryStringNew;
}

//function to remove one complete get param out of the query
function delTotalFromQuery($paramName,$queryString) {
	//echo $paramName ."<br>";
	$queryString = "&".$queryString;
	$queryStringNew = preg_replace('/\b'.$paramName.'\=[^&]*&?/',"",$queryString); //TODO find empty get params
	$queryStringNew = ltrim($queryStringNew,'&');
	$queryStringNew = rtrim($queryStringNew,'&');
	return $queryStringNew;
}
//delete all string entries from array
function deleteEntry($arrayname, $entry) {
	$n = $arrayname.length;
	for($i=0; $i<($n+1); $i++){
		if ($arrayname[$i] == $entry) {
			$arrayname.splice($i, 1);
		}
	}
	return $arrayname;
} 

//call class_metadata - in case of file for all requested resources, in case of web only for one resource - cause there are different result files


if ($resultTarget == 'file') {
	for($i=0; $i<count($searchResources);$i++){
		$str = "nohup php5 /data/mapbender/http/php/mod_metadataWrite.php ";
		$str .= "'".$userId."' ";		
		$str .= "'".$searchId."' ";		
		$str .= "'".$searchText."' "; 		
		$str .= "'".$registratingDepartments."' ";						
		$str .= "'".$isoCategories."' ";							
		$str .= "'".$inspireThemes."' ";
		$str .= "'".$timeBegin."' ";
		$str .= "'".$timeEnd."' ";
		$str .= "'".$regTimeBegin."' ";
		$str .= "'".$regTimeEnd."' ";
		$str .= "'".$maxResults."' ";
		$str .= "'".$searchBbox."' ";
		$str .= "'".$searchTypeBbox."' ";
		$str .= "'".$accessRestrictions."' ";
		$str .= "'".$languageCode."' ";
		$str .= "'".$searchEPSG."' ";
		$str .= "'".$searchResources[$i]."' ";
		$str .= "'".$searchPages[$i]."' ";
		$str .= "'".$outputFormat."' ";
		$str .= "'".$resultTarget."' ";
		$str .= "'".$searchURL."' ";
		$str .= "'".$customCategories."' ";
		$str .= "'".$hostName."' ";
		$str .= "'".$orderBy."' ";
		$str .= " & ";
		$e = new mb_notice($str);
		exec($str);
	}
}
if ($resultTarget == 'web' or $resultTarget == 'debug') {
	if (count($searchResources) == 1) {
		$metadata = new searchMetadata($userId, $searchId, $searchText, $registratingDepartments, $isoCategories, $inspireThemes, $timeBegin, $timeEnd, $regTimeBegin, $regTimeEnd, $maxResults, $searchBbox, $searchTypeBbox, $accessRestrictions, $languageCode, $searchEPSG, $searchResources[0], $searchPages[0], $outputFormat, $resultTarget, $searchURL, $customCategories, $hostName, $orderBy);
		#if ($outputFormat == 'xml') {
		#	header("Content-type: application/xhtml+xml; charset=UTF-8");		
		#}
		#echo "class initiated<br>";
	}
	else {
		echo "Result for web can only requested for one type of resource (wms, wfs, wmc, georss)!";
	}
}
/*
How does the webservice look like?
First request: Do search for all classes. 
Next request: Search only in the requested class and the numbered page.
search.php?q=test&classes=wms,wfs,wmc,georss&pages=1,1,1,1&iso=1,2,3&inspire=1,2,3,4&department=1,2,3,4&bbox=123,123,123,123
simple other request:
search.php?q=test&classes=wms&pages=2&iso=1,2,3&inspire=1,2,3,4&department=1,2,3,4&beginDate=2009-10-10&endDate=2010-11-12&searchId=12hjxa31231
There is a possibility to exchange some classes by other information - the id will be used to update the search result files - but this can only update the class infos. The pagenumber should be updated in the metadata file
*/
//
//Name of searchMetadata file
//searchid_classes.json
//Name of searchCategories file
//Name of searchResult files
//searchid_wms_1.json
//searchid_wfs_1.json
//searchid_wmc_1.json - doesn't exists till now
//searchid_georss_1.json - doesn't exists till now
//categories files - will only be generated when the search is started and resultType = 'file'. if the categories files already exists it will not be updated! - Here we can spare a reasonable amount of calculating power. Another approach is to generate a md5 hash of an ordered searchURL. With this we can cache the requests!

//searchid_wms_cat.json
//searchid_wfs_cat.json
//searchid_wmc_cat.json - doesn't exists till now
//searchid_georss_cat.json - doesn't exists till now
//searchid_filter.json

?>
