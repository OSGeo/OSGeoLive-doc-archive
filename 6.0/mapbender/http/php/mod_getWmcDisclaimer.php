<?php require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once dirname(__FILE__) . "/../classes/class_wmc_factory.php";
require_once(dirname(__FILE__) . "/../classes/class_user.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");

//following is needed cause sometimes the service is invoked as a localhost service and then no userId is known but the userId in the session is needed for class_wmc to read from database!!! TODO: check if needed in this class.
$userId = Mapbender::session()->get("mb_user_id");
if (!isset($userId) or $userId =='') {
	$userId = ANONYMOUS_USER; //or public
	Mapbender::session()->set("mb_user_id",$userId);
}
$languageCode = 'de';
//parameters: 
//id - wmc id
//languageCode - language parameter 'de', 'en', 'fr' 
//$wmsServiceDisclaimerUrl = "";

//initialize variables
$hostName = $_SERVER['HTTP_HOST'];
$userId = ANONYMOUS_USER;
$id = 4373; //dummy id
//TODO give requesting hostname to this script
if (isset($_REQUEST["id"]) & $_REQUEST["id"] != "") {
	//validate to integer 
	$testMatch = $_REQUEST["id"];
	$pattern = '/^[\d]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'id: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$id = (integer)$testMatch;
	$testMatch = NULL;	
}
//TODO give requesting hostname to this script
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
$e = new mb_notice("mod_getWmcDisclaimer.php: requested wmc id: ".$_REQUEST["id"]);
//
//
if (isset($_REQUEST["languageCode"]) & $_REQUEST["languageCode"] != "") {
	//validate to wms, wfs
	$testMatch = $_REQUEST["languageCode"];	
 	if (!($testMatch == 'de' or $testMatch == 'en' or  $testMatch == 'fr')){ 
		echo 'type: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$languageCode = $testMatch;
	$testMatch = NULL;
}

//Array with translations:
switch ($languageCode) {
	case "de":
		$translation['wmcDisclaimerHeader'] = 'Das vorliegende Dokument enthält Datenquellen von unterschiedlichen Stellen. Diese unterliegen den im folgenden aufgeführten Nutzungsbedingungen:';
		$translation['wms'] = "Kartendienst";
		$translation['wfs'] = "Datendienst";
		$translation['noTouInformation'] = 'Es sind keine Informationen über Nutzungsbedingungen verfügbar!';
		break;
	case "en":
		$translation['wmcDisclaimerHeader'] = 'The document includes data resources from different organizations. The following parapgraph shows the different terms of use for the includes resources:';
		$translation['wms'] = "Web Map Service";
		$translation['wfs'] = "Web Feature Service";
		$translation['noTouInformation'] = 'No informations about terms of use are available!';
		break;
	default: #to english
		$translation['wmcDisclaimerHeader'] = 'The document includes data resources from different organizations. The following parapgraph shows the different terms of use for the includes resources:';
		$translation['wms'] = "Web Map Service";
		$translation['wfs'] = "Web Feature Service";
		$translation['noTouInformation'] = 'No informations about terms of use are available!';
}









//javascript:openwindow("../php/mod_showMetadata.php?resource=layer&layout=tabs&redirectToMetadataUrl=1&id=20655");
//Generate wmc document by id
$wmcFactory = new WmcFactory;
$e = new mb_exception("mod_getWmcDisclaimer.php: wmcid: ".$id);
$wmcObj = $wmcFactory->createFromDb($id);
//generate header for disclaimer:
echo "<b>".$translation['wmcDisclaimerHeader']."</b><br><br>";#

//Part for wms
$resourceSymbol = "<img src='../img/osgeo_graphics/geosilk/server_map.png' alt='".$translation['wms']." - picture' title='".$translation['wms']."'>";
//read out all wms id's
$validWMS = $wmcObj->getValidWms();
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$mapbenderBaseUrl = "https://".$hostName;
			$mapbenderProtocol = "https://";
		}
		else {
			$mapbenderBaseUrl = "http://".$hostName;
			$mapbenderProtocol = "http://";
}

foreach($validWMS as $WMS) {
	
	echo $resourceSymbol." <a href='".$mapbenderBaseUrl.$_SERVER['SCRIPT_NAME']."/../mod_showMetadata.php?resource=wms&layout=tabs&id=".$WMS['id']."&languageCode=".$languageCode."'>".$WMS['title']."</a><br>";

	$touServiceConnector = new connector($mapbenderProtocol."localhost".$_SERVER['SCRIPT_NAME']."/../mod_getServiceDisclaimer.php?type=wms&id=".$WMS['id']."&languageCode=".$languageCode."&asTable=true");
	$touForWMS = $touServiceConnector->file;
	// 
	if ($touForWMS == ''){
		$wmstou = $translation['noTouInformation'];
	} else {
		$wmstou = $touForWMS;
	}
	//$wmstou = $touServiceConnector->file;
	#$wmstou = file_get_contents("http://localhost/mapbender/php/mod_getServiceDisclaimer.php?resource=wms&id=".$WMS['id']."&languageCode=".$languageCode."&asTable=true");
	echo $wmstou."<br>";
}
//var_dump($validWMS);
//module to read out all service ids which are stored in mapbender wmc documents and generate a Big Disclaimer for those Docs.
//It integrates all known disclaimers for the used webservices who are stored in the mapbender registry

//read out all wms id's
//read out all wfs id's
//generate the disclaimer part for wms 
//generate the disclaimer part for wfs
//push disclaimer to json or html


?>
