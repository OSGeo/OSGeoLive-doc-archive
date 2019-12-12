<?php
//
// Load WMS via WMC
// 
require_once dirname(__FILE__)."/../php/mb_validateSession.php";
require_once dirname(__FILE__)."/../classes/class_wmc.php";
require_once dirname(__FILE__)."/../classes/class_wmc_factory.php";
require_once dirname(__FILE__)."/../classes/class_administration.php";
require_once dirname(__FILE__)."/../../lib/class_GetApi.php";
require_once(dirname(__FILE__) . "/../classes/class_bbox.php");
require_once(dirname(__FILE__) . "/../classes/class_gml2.php");
require_once dirname(__FILE__)."/../classes/class_elementVar.php";
require_once(dirname(__FILE__) . "/../classes/class_tou.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");

function getConfiguration ($key) {
//check if key param can be found in SESSION,
// otherwise take it from GET
	if (Mapbender::session()->exists($key)) {
		return Mapbender::session()->get($key);
	}
	return $_GET[$key];
}

$admin = new administration();

$resultObj = array(
	"noPermission" => array(
		"message" => _mb("You as User")." '" .
			Mapbender::session()->get("mb_user_name") . "' " .
			_mb("have no authorization to access following layers."),
		"wms" => array()
	),
	"withoutId" => array(
		"message" => _mb("Following layers come from an unkown origin. There is no information about the links. They may be broken and the underlaying services may not exist anymore!"),
		"wms" => array(),
	),
	"unavailable" => array(
		"message" => _mb("The last monitoring had problems with the following layers. Maybe the underlying services will not be able to answer the requests for sometime."),
		"wms" => array()
	),
	"invalidId" => array(
		"message" => _mb("Following layers have been removed from the registry. They may be broken and the underlaying services may not exist anymore!"),
		"wms" => array()
	),
	"wmcTou" => array(
		"message" => ""
	)
);


//
// Load WMC from session or application
//

$wmc = new wmc();

$app = Mapbender::session()->get("mb_user_gui");
//$wmcDocSession = Mapbender::session()->get("mb_wmc");

$wmcDocSession = false;
if(Mapbender::session()->get("mb_wmc")) {
    $wmc_filename = Mapbender::session()->get("mb_wmc");
    $wmcDocSession = file_get_contents($wmc_filename);
}


try {
	$loadFromSession = new ElementVar($app, "loadwmc", "loadFromSession");
	if ($wmcDocSession && $loadFromSession->value === "1") {
	//check if session contains a wmc,
	//otherwise create a new wmc from application
		$e = new mb_notice("trying to load session WMC...");
		if (!$wmc->createFromXml($wmcDocSession)) {
			$e = new mb_notice("loading session WMC failed.");
			$e = new mb_notice("creating wmc from app: " . $app);
			$wmc->createFromApplication($app);
		}
	}
	else {
		$e = new mb_notice("loading from session WMC disabled in loadwmc or no session WMC set.");
		$e = new mb_notice("creating wmc from app: " . $app);
		$wmc->createFromApplication($app);
	}
}
catch (Exception $e) {
	$e = new mb_notice("creating wmc from app: " . $app);
	$wmc->createFromApplication($app);
}

//
// create new WMC with services from GET API
//
$wmcGetApi = WmcFactory::createFromXml($wmc->toXml());

$options = array();
if (Mapbender::session()->exists("addwms_showWMS")) {
	$options["show"] = intval(Mapbender::session()->get("addwms_showWMS"));
}
if (Mapbender::session()->exists("addwms_zoomToExtent")) {
	$options["zoom"] = !!Mapbender::session()->get("addwms_zoomToExtent");
}

$getParams = array(
	"WMC" => getConfiguration("WMC"),
	"WMS" => getConfiguration("WMS"),
	"LAYER" => getConfiguration("LAYER"),
	"FEATURETYPE" => getConfiguration("FEATURETYPE"),
	"GEORSS"=>getConfiguration("GEORSS")
);
$getApi = new GetApi($getParams);

//
// WMC
//
$inputWmcArray = $getApi->getWmc();

if ($inputWmcArray) {
	foreach ($inputWmcArray as $input) {
	// just make it work for a single Wmc
		try {
			$wmcGetApi = WmcFactory::createFromDb($input["id"]);
			//update urls from wmc with urls from database if id is given 
			$updatedWMC = $wmcGetApi->updateUrlsFromDb();
	        	$wmcGetApi->createFromXml($updatedWMC);
			//increment load count
			$wmcGetApi->incrementWmcLoadCount();
		}
		catch (Exception $e) {
			new mb_exception("Failed to load WMC from DB. Keeping original WMC.");
		}
	}
}

//
// WMS
//
if ($getParams['WMS']) {
// WMS param given as array
	if (is_array($getParams['WMS'])) {
		$inputWmsArray = $getParams['WMS'];
	}
	// WMS param given as comma separated list
	else {
		$inputWmsArray = split(",",$getParams['WMS']);
	}

	$wmsArray = array();
	$singleAssocArray = array();
	$multipleAssocArray = array();

	foreach ($inputWmsArray as $key=>$val) {
		if (is_array($val)) {
			foreach ($val as $attr=>$value) {
				$multipleAssocArray[$attr] = $value;
			}
			//get WMS by ID with settings of given application
			if (array_key_exists('application', $multipleAssocArray) &&
				array_key_exists('id', $multipleAssocArray)) {
				$currentWms = new wms();
				$currentWms->createObjFromDB(
					$multipleAssocArray['application'],
					$multipleAssocArray['id']
				);
			}
			//get WMS by URL
			elseif (array_key_exists('url', $multipleAssocArray)) {
				$currentWms = new wms();
				$currentWms->createObjFromXML($multipleAssocArray['url']);
			}
			else {
				continue;
			}
			array_push($wmsArray, $currentWms);

			$options['visible'] = $multipleAssocArray['visible'] === "1" ?
				true : false;

			$options['zoom'] = $multipleAssocArray['zoom'] === "1" ?
				true : false;

			$wmcGetApi->mergeWmsArray($wmsArray, $options);
			$wmsArray = array();
			$multipleAssocArray = array();
		}
		else {
			$currentWms = new wms();
			if(is_numeric($key)) {
				//get WMS by ID
				if (is_numeric($val)) {
					$currentWms->createObjFromDBNoGui($val);
				}
				//get WMS by URL
				else if (is_string($val)) {
					$currentWms->createObjFromXML($val);
				}
				array_push($wmsArray, $currentWms);
				$wmcGetApi->mergeWmsArray($wmsArray);
				$wmsArray = array();
			}
			else {
				$singleAssocArray[$key] = $val;
			}
		}
	}

	//get WMS by ID with settings of given application
	if (array_key_exists('application', $singleAssocArray) &&
		array_key_exists('id', $singleAssocArray)) {
		$currentWms = new wms();
		$currentWms->createObjFromDB(
			$singleAssocArray['application'],
			$singleAssocArray['id']
		);
		array_push($wmsArray, $currentWms);
		$options['visible'] = $singleAssocArray['visible'] === "1" ?
			true : false;

		$options['zoom'] = $singleAssocArray['zoom'] === "1" ? true : false;

		$wmcGetApi->mergeWmsArray($wmsArray, $options);
		$wmsArray = array();
		$singleAssocArray = array();
	}
	//get WMS by URL
	elseif (array_key_exists('url', $singleAssocArray)) {
		$currentWms = new wms();
		$currentWms->createObjFromXML($singleAssocArray['url']);
		array_push($wmsArray, $currentWms);
		if($singleAssocArray['visible']) {
			$options['visible'] = $singleAssocArray['visible'] === "1" ?
				true : false;
		}
		if($singleAssocArray['zoom']) {
			$options['zoom'] = $singleAssocArray['zoom'] === "1" ?
				true : false;
		}
		$wmcGetApi->mergeWmsArray($wmsArray, $options);
		$wmsArray = array();
		$singleAssocArray = array();
	}
}

//
// LAYER
//

$inputLayerArray = $getApi->getLayers();
if ($inputLayerArray) {
	foreach ($inputLayerArray as $input) {
	// just make it work for a single layer id
		$wmsFactory = new UniversalWmsFactory();
		try {
			if (isset($input["application"])) {
				$wms = $wmsFactory->createLayerFromDb(
					$input["id"], $input["application"]
				);
			}
			else {
				$wms = $wmsFactory->createLayerFromDb($input["id"]);
			}
		}
		catch (AccessDeniedException $e) {
			$resultObj["noPermission"]["wms"][] = array(
				"title" => $admin->getLayerTitleByLayerId($input["id"]),
				"id" => $input["id"]
			);
		}

		if (is_a($wms, "wms")) {
			$options = array();
			if ($input["visible"]) {
			// this is a hack for the time being:
			// make WMS visible if it has less than 100000 layers
				$options["show"] = 100000;
			}
			if (isset($input["querylayer"])) {
				$options["querylayer"] = $input["querylayer"];
			}
			$wmcGetApi->mergeWmsArray(array($wms), $options);

			// do not use "zoom" attribute of mergeWmsArray,
			// as this would zoom to the entre WMS.
			// Here we set extent to the layer extent only.
			if ($input["zoom"]) {
				$bboxArray = array();
				try {
					$layer = $wms->getLayerById(intval($input["id"]));
					for ($i = 0; $i < count($layer->layer_epsg); $i++) {
						$bboxArray[]= Mapbender_bbox::createFromLayerEpsg(
							$layer->layer_epsg[$i]
						);
					}
					$wmcGetApi->mainMap->mergeExtent($bboxArray);
				}
				catch (Exception $e) {

				}
			}

		}
	}
}

//
// FEATURETYPE
//
$inputFeaturetypeArray = $getApi->getFeaturetypes();
if ($inputFeaturetypeArray) {
	$wfsConfIds = array();
	foreach ($inputFeaturetypeArray as $input) {
		array_push($wfsConfIds, $input["id"]);
	}

	$wmcGetApi->generalExtensionArray['WFSCONFIDSTRING'] = implode(",", array_unique(array_merge(
		$wmcGetApi->generalExtensionArray['WFSCONFIDSTRING'] ?
		explode(",", $wmcGetApi->generalExtensionArray['WFSCONFIDSTRING']) :
		array(),
		$wfsConfIds
	)));
}

$inputGeoRSSArray = $getApi->getGeoRSSFeeds();
if($inputGeoRSSArray){
	$wmc->generalExtensionArray['GEORSS'] = $inputGeoRSSArray; 
}

//workaround to have a fully merged WMC for loading

$xml = $wmcGetApi->toXml();

$wmcGetApi = new wmc();
//new Object with merged layers and other features
$wmcGetApi->createFromXml($xml);



//
// CONSTRAINTS
//
$currentUser = new User();

// remove all WMS with no permission
$deniedIdsArray = $wmcGetApi->getWmsWithoutPermission($currentUser);
$deniedIdsTitles = array();
$deniedIdsIndices = array();
foreach ($deniedIdsArray as $i) {
	$deniedIdsTitles[]= array(
		"id" => $i["id"],
		"index" => $i["index"],
		"title" => $i["title"]
	);
	$deniedIdsIndices[]= $i["index"];
}
$resultObj["noPermission"]["wms"] = array_merge(
	$resultObj["noPermission"]["wms"],
	$deniedIdsTitles
);
$wmcGetApi->removeWms($deniedIdsIndices);

// find WMS without ID
$withoutIdsArray = $wmcGetApi->getWmsWithoutId();
$withoutIdsTitles = array();
foreach ($withoutIdsArray as $i) {
	$withoutIdsTitles[]= array(
		"id" => $i["id"],
		"index" => $i["index"],
		"title" => $i["title"]
	);
}
$resultObj["withoutId"]["wms"] = array_merge(
	$resultObj["withoutId"]["wms"],
	$withoutIdsTitles
);

// find orphaned WMS
$invalidIdsArray = $wmcGetApi->getInvalidWms();
$invalidIdsTitles = array();
foreach ($invalidIdsArray as $i) {
	$invalidIdsTitles[]= array(
		"id" => $i["id"],
		"index" => $i["index"],
		"title" => $i["title"]
	);
}
$resultObj["invalidId"]["wms"] = array_merge(
	$resultObj["invalidId"]["wms"],
	$invalidIdsTitles
);

// find potentially unavailable WMS
$unavailableIdsArray = $wmcGetApi->getUnavailableWms($currentUser);
$unavailableIdsTitles = array();
foreach ($unavailableIdsArray as $i) {
	$unavailableIdsTitles[]= array(
		"id" => $i["id"],
		"index" => $i["index"],
		"title" => $i["title"]
	);
}
$resultObj["unavailable"]["wms"] = array_merge(
	$resultObj["unavailable"]["wms"],
	$unavailableIdsTitles
);

//get terms of use from wms objects which are in the remaining wmc and are not already accepted for this session

$validWMS = $wmcGetApi->getValidWms();
$translation['wms'] = _mb("MapService");
$resourceSymbol = "<img src='../img/osgeo_graphics/geosilk/server_map.png' alt='".$translation['wms']." - picture' title='".$translation['wms']."'>";
$languageCode = 'de';
$hostName = $_SERVER['HTTP_HOST'];
$tou = "";
$classTou = new tou();
foreach($validWMS as $WMS) {
	//check if tou has already been read - if not show them in the message
	$resultOfCheck = $classTou->check('wms',$WMS['id']);
	if ($resultOfCheck['accepted'] == 0) {
		$touHeader = $resourceSymbol." <a href='../php/mod_showMetadata.php?resource=wms&layout=tabs&id=".$WMS['id']."&languageCode=".$languageCode."' target='_blank'>".$WMS['title']."</a><br>";

		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$mapbenderProtocol = "https://";
			$mapbenderBaseUrl = "https://".$hostName;
		}
		else {
			$mapbenderProtocol = "http://";
			$mapbenderBaseUrl = "http://".$hostName;
		}
		$touServiceConnector = new connector($mapbenderProtocol."localhost".$_SERVER['SCRIPT_NAME']."/../../php/mod_getServiceDisclaimer.php?resource=wms&id=".$WMS['id']."&languageCode=".$languageCode."&asTable=true");
		$touForWMS = $touServiceConnector->file;
		//add only those who have no special tou defined - 
		if ($touForWMS != ''){
			$tou .= $touHeader.$touForWMS;
		}
		//set the tou to be accepted - TODO maybe do this after the button which deletes the message window - from a ajax request.

		$classTou->set('wms',$WMS['id']);
	} 
}
	
if ($tou != "") {
	$tou = _mb("The configuration, which should be loaded, consists of different services which have the following terms of use:")."<br>".$tou;
}
$resultObj["wmcTou"]["message"] = $tou;


#$resultObj["wmcTou"]["message"] = "Terms of Use";

//
// Output
//
// Check if session WMC module is loaded
$sql = "SELECT COUNT(e_id) AS i FROM gui_element WHERE fkey_gui_id = $1 AND e_id = $2";
$v = array(Mapbender::session()->get("mb_user_gui"), "sessionWmc");
$t = array("s", "s");
$res = db_prep_query($sql, $v, $t);
$row = db_fetch_assoc($res);
$isSessionWmcModuleLoaded = intval($row["i"]);

// check if Session contains a GML, and then zoom to it
$gml_string = Mapbender::session()->get("GML");
if($gml_string){
	$gml = new gml2();
	$gml->parse_xml($gml_string);
	$bbox = new Mapbender_bbox(
	$gml->bbox[0],
	$gml->bbox[1],
	$gml->bbox[2],
	$gml->bbox[3],
	$epsg = "EPSG:".$gml->epsg);
	$wmcGetApi->mainMap->setExtent($bbox);
}
if (
	count($resultObj["withoutId"]["wms"]) === 0 &&
	count($resultObj["invalidId"]["wms"]) === 0 &&
	count($resultObj["unavailable"]["wms"]) === 0 ||
	!$isSessionWmcModuleLoaded
) {
	
	Mapbender::session()->set("wmcConstraints", $resultObj);
	$output = $wmcGetApi->wmsToJavaScript();
	$wmcJs = $wmcGetApi->toJavaScript(array());
	$wmcJs = implode(";\n",$wmcJs);
	$extentJs = $wmcGetApi->extentToJavaScript();
	$output[] = <<<JS
		Mapbender.events.afterInit.register(function () {
			$wmcJs;
		});
		Mapbender.events.beforeInit.register(function () {
			$extentJs
		});
JS;
	Mapbender::session()->delete("wmcGetApi", $wmcGetApi);
}
else {
	Mapbender::session()->set("wmcConstraints", $resultObj);
	$output = $wmc->wmsToJavaScript();
	$wmcJs = $wmc->toJavaScript(array());
	$wmcJs = implode(";\n",$wmcJs);
	$extentJs = $wmc->extentToJavaScript();
	$output[] = <<<JS
		Mapbender.events.afterInit.register(function () {
			$wmcJs;
		});
		Mapbender.events.beforeInit.register(function () {
			$extentJs
		});
JS;
	Mapbender::session()->set("wmcGetApi", $wmcGetApi);
}


$outputString = "";
for ($i = 0; $i < count($output); $i++) {
	$outputString .= administration::convertOutgoingString($output[$i]);
}

$wmcFeaturetypeJson = $wmc->featuretypeConfToJavaScript();
$wfsConfIdString = $wmcGetApi->generalExtensionArray['WFSCONFIDSTRING'];
if($wfsConfIdString != ""){
	$wmcFeaturetypeStr = <<<JS
		Mapbender.events.afterInit.register(function () {
			$('#body').trigger('addFeaturetypeConfs', [
				{ featuretypeConfObj : $wmcFeaturetypeJson,
					wfsConfIdString: "$wfsConfIdString"}
			]);
		});
JS;
}
$outputString .= $wmcFeaturetypeStr;

$GeoRSSStr = " Mapbender.events.afterInit.register(function () {";
foreach($inputGeoRSSArray as $inputGeoRSSUrl){
	$GeoRSSStr .= 'try {$("#mapframe1").georss({url: "'.$inputGeoRSSUrl .'"})} catch(e) {new Mb_warning("GeoRSS module not loaded")}';
}
$GeoRSSStr .="}); ";

$outputString .= $GeoRSSStr;


echo $outputString;

Mapbender::session()->delete("addwms_showWMS");
Mapbender::session()->delete("addwms_zoomToExtent");
unset($output);
unset($wmc);
?>
