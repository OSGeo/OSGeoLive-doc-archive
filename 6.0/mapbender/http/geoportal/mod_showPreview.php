<?php
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
//get type of resource and id of resource - normally layer and wmc
$resource = 'layer';
$id = 0;
if (isset($_REQUEST["resource"]) & $_REQUEST["resource"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["resource"];
	if (!($testMatch == 'layer' or $testMatch == 'wmc' or $testMatch == 'layerlegend')){ 
		echo 'resource: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$resource = $testMatch;
	$testMatch = NULL;
}
if (isset($_REQUEST["id"]) & $_REQUEST["id"] != "") {
	//validate to integer
	$testMatch = $_REQUEST["id"];
	$pattern = '/^[0-9]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'id: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$id = $testMatch;
	$testMatch = NULL;
}

if ($resource == 'layer'){
	if (file_exists(PREVIEW_DIR."/".$id."_layer_map_preview.jpg")) {
		header("Expires: -1");
		header("Cache-Control: no-cache; must-revalidate");
		header("Pragma: no-cache");
		header('Content-Type: image/jpeg');
		readfile(PREVIEW_DIR."/".$id."_layer_map_preview.jpg");
	} else {
		if (file_exists(PREVIEW_DIR."/"."keinevorschau.jpg")) {
			header("Expires: -1");
			header("Cache-Control: no-cache; must-revalidate");
			header("Pragma: no-cache");
			header('Content-Type: image/jpeg');
			readfile(PREVIEW_DIR."/"."keinevorschau.jpg");
		} else {
			echo "No preview dummy found!";
		}
	}
}
elseif ($resource == 'wmc') {
	if (file_exists(PREVIEW_DIR."/".$id."_wmc_preview.jpg")) {
		header("Expires: -1");
		header("Cache-Control: no-cache; must-revalidate");
		header("Pragma: no-cache");
		header('Content-Type: image/jpeg');
		readfile(PREVIEW_DIR."/".$id."_wmc_preview.jpg");
	} else {
		if (file_exists(PREVIEW_DIR."/"."keinevorschau.jpg")) {
			header("Expires: -1");
			header("Cache-Control: no-cache; must-revalidate");
			header("Pragma: no-cache");
			header('Content-Type: image/jpeg');
			readfile(PREVIEW_DIR."/"."keinevorschau.jpg");
		} else {
			echo "No preview dummy found!";
		}
	}

}
elseif ($resource == 'layerlegend') {
	if (file_exists(PREVIEW_DIR."/".$id."_layer_legend_preview.jpg")) {
		header("Expires: -1");
		header("Cache-Control: no-cache; must-revalidate");
		header("Pragma: no-cache");
		header('Content-Type: image/jpeg');
		readfile(PREVIEW_DIR."/".$id."_layer_legend_preview.jpg");
	} else {
		if (file_exists(PREVIEW_DIR."/"."keinevorschau.jpg")) {
			header("Expires: -1");
			header("Cache-Control: no-cache; must-revalidate");
			header("Pragma: no-cache");
			header('Content-Type: image/jpeg');
			readfile(PREVIEW_DIR."/"."keinevorschau.jpg");
		} else {
			echo "No preview dummy found!";
		}
	}

} else die();

?>
