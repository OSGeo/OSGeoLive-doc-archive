<?php
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);
$languageCode = 'de';
$maxObjects = 10;
$outputFormat = 'json';
$hostName = $_SERVER['HTTP_HOST'];
$pathToLoadScript = '/portal/karten.html?WMC=';
$pathToMetadata = '/mapbender/php/mod_showMetadata.php?';
$pathToPreview = '/mapbender/geoportal/mod_showPreview.php?';
if (isset($_REQUEST["outputFormat"]) & $_REQUEST["outputFormat"] != "") {
	$testMatch = $_REQUEST["outputFormat"];	
 	if (!($testMatch == 'html' or $testMatch == 'json')){ 
		echo 'outputFormat: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$outputFormat = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["maxObjects"]) & $_REQUEST["maxObjects"] != "") {
	$testMatch = $_REQUEST["maxObjects"];	
 	$pattern = '/^[0-9]*$/';  
        if (!preg_match($pattern,$testMatch)){
                echo 'maxObjects: <b>'.$testMatch.'</b> is not valid.<br/>';
                die();
        }	
	$maxObjects = (integer)$testMatch;
	if ($maxObjects > 15){
                echo '<b>'.$maxObjects.'</b> objects are too much, at maximum 15 ojects are allowed.<br/>';
                die();
        }	
	$testMatch = NULL;
}
if (isset($_REQUEST["languageCode"]) & $_REQUEST["languageCode"] != "") {
	//validate to wms, wfs
	$testMatch = $_REQUEST["languageCode"];	
 	if (!($testMatch == 'de' or $testMatch == 'en')){ 
		echo 'languageCode: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$languageCode = $testMatch;
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
if ($outputFormat == 'json'){
	$classJSON = new Mapbender_JSON;
}
if ($languageCode == 'en'){
	$pathToLoadScript = '/portal/en/maps.html?WMC=';
}
/*
//define sql for selecting informations from database:
$sql = "";
$sql .= "SELECT search_wmc_view.wmc_serial_id,search_wmc_view.wmc_title,search_wmc_view.wmc_abstract, custom_category.custom_category_code_".$languageCode. ", search_wmc_view.load_count ";
$sql .= "FROM search_wmc_view INNER JOIN wmc_custom_category ON "; 
$sql .= "(wmc_custom_category.fkey_wmc_serial_id=search_wmc_view.wmc_serial_id) INNER JOIN custom_category ON ";
$sql .= "(custom_category.custom_category_id=wmc_custom_category.fkey_custom_category_id) WHERE ";
$sql .= "custom_category.custom_category_key = 'mbc1' ORDER BY search_wmc_view.load_count DESC LIMIT $1 ";
*/
//define sql for selecting informations from database:
$sql = "";
$sql .= "SELECT search_wmc_view.wmc_serial_id,search_wmc_view.wmc_title,search_wmc_view.wmc_abstract, search_wmc_view.load_count ";
$sql .= "FROM search_wmc_view ORDER BY search_wmc_view.load_count DESC LIMIT $1 ";
$v = array($maxObjects);
$t = array('i');
$res = db_prep_query($sql,$v,$t);
$initialWmc = array();
$i = 0;
while($row = db_fetch_array($res)){
	$initialWmc[$i] = array('id'  =>$row['wmc_serial_id'], 'title' =>$row['wmc_title'], 'abstract' =>$row['wmc_abstract'],'loadUrl'=>'http://'.$hostName.$pathToLoadScript.$row['wmc_serial_id'],'metadataUrl'=>'http://'.$hostName.$pathToMetadata."languageCode=".$languageCode."&resource=wmc&id=".$row['wmc_serial_id'], 'previewUrl'=>'http://'.$hostName.$pathToPreview."resource=wmc&id=".$row['wmc_serial_id']);
	$i++;
}
if ($outputFormat == 'html'){
	echo "<html>";
	echo "<title>Mapbender Initial WMC</title>";
	echo "<body>";
		for($i=0; $i<count($initialWmc);$i++){
			echo "<b>ID: </b>".$initialWmc[$i]['id']."<br>";
				echo "<b>Titel: </b>".$initialWmc[$i]['title']."<br>";
				echo "<b>Zusammenfassung: </b>".$initialWmc[$i]['abstract']."<br>";
				echo "<b>Metadaten: </b><a href='".$initialWmc[$i]['metadataUrl']."'>".$initialWmc[$i]['metadataUrl']."</a>"."<br>";
				echo "<b>Öffnen: </b><a href='".$initialWmc[$i]['loadUrl']."'>".$initialWmc[$i]['loadUrl']."</a>"."<br>";
				echo "<b>Preview Link: </b><a href='".$initialWmc[$i]['previewUrl']."'>".$initialWmc[$i]['previewUrl']."</a>"."<br>";
				echo "<b>Preview: </b><img src='".$initialWmc[$i]['previewUrl']."'/>"."<br>";
				echo "<hr>";
			}
	echo "</body>";
	echo "</html>";
}
if ($outputFormat == 'json'){
	$wmcJSON = new stdClass;
	$wmcJSON->initialWmcDocs = array();
	for($i=0; $i<count($initialWmc);$i++){
    		$wmcJSON->initialWmcDocs[$i]->id = $initialWmc[$i]['id'];
		$wmcJSON->initialWmcDocs[$i]->title = $initialWmc[$i]['title'];
		$wmcJSON->initialWmcDocs[$i]->abstract = $initialWmc[$i]['abstract'];
		$wmcJSON->initialWmcDocs[$i]->metadataUrl = $initialWmc[$i]['metadataUrl'];
		$wmcJSON->initialWmcDocs[$i]->loadUrl = $initialWmc[$i]['loadUrl'];
		$wmcJSON->initialWmcDocs[$i]->previewUrl = $initialWmc[$i]['previewUrl'];
		$wmcJSON->initialWmcDocs[$i]->loadCount = 10;
   	 }
	$wmcJSON = $classJSON->encode($wmcJSON);
	echo $wmcJSON;
}
?>
