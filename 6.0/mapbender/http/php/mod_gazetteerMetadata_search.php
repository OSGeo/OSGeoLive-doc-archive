<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");

$json = new Mapbender_JSON();

$user_id = Mapbender::session()->get("mb_user_id");
$query = $_REQUEST["search"];
$srs = $_REQUEST["srs"];
$searchColumnsWms = $_REQUEST["searchColumnsWms"];
$searchColumnsLayer = $_REQUEST["searchColumnsLayer"];

if ($searchColumnsWms && !preg_match("/^[a-zA-Z_\-, ]+$/", $searchColumnsWms)) {
	echo "[]"; die;
}

if ($searchColumnsLayer && !preg_match("/^[a-zA-Z_\-, ]+$/", $searchColumnsLayer)) {
	echo "[]"; die;
}

if (!preg_match("/^[a-zA-Z_\- ]+$/", $query)) {
	echo "[]"; die;
}

if (!preg_match("/^[a-zA-Z_\-:0-9 ]+$/", $srs)) {
	echo "[]"; die;
}

$n = new administration();
$myguis = $n->getGuisByPermission($user_id, true);
$mywms = $n->getWmsByOwnGuis($myguis);

if($mywms == false){
	$mywms = array();	
}
$mylayer = array();

for($i = 0; $i < count($mywms); $i++){
	$mylayer = array_merge($mylayer,$n->getLayerByWms($mywms[$i]));
}

$res_container_wms = array();
$res_container_layer = array();
$obj = array();

if(preg_match("/\*/",$_REQUEST["search"])){
	$search = trim(preg_replace("/\*/i","", $_REQUEST["search"]));
}

if (count($mywms) > 0) {
	$v = array();   
	$t = array();   

	$sql_wms = "SELECT DISTINCT layer.layer_id, wms.wms_title, " . 
		"wms.wms_getcapabilities, wms.wms_version, " . 
		"e.minx, e.miny, e.maxx, e.maxy " . 
		"FROM wms LEFT JOIN layer ON wms.wms_id = layer.fkey_wms_id " . 
		"LEFT JOIN layer_epsg e ON layer.layer_id = e.fkey_layer_id " . 
		"AND e.epsg = '$srs' " . 
		"WHERE layer.layer_pos = 0 AND wms.wms_id IN ("; 
	for($i=0; $i<count($mywms); $i++){
		if ($i > 0) {$sql_wms .= ",";}
		$sql_wms .= "$".($i+1);
		array_push($v, $mywms[$i]);
		array_push($t, 'i');   
	}
	
	$sql_wms .= ") AND (";
	if($searchColumnsWms == "") {
		$sql_wms .= "wms_title ILIKE '%".$query."%' OR wms_abstract ILIKE '%".$query."%'";
	}
	else{
		$wmsColumnArray = split(",", $searchColumnsWms);
		for($j = 0; $j < count($wmsColumnArray); $j++) {
			if ($j > 0) {
				$sql_wms .= " OR ";
			}
			$sql_wms .= trim($wmsColumnArray[$j]) . " ILIKE '%".$query."%'";
		}
	}
	
	$sql_wms .= ") ORDER BY wms_title";
	$res_wms = db_prep_query($sql_wms,$v,$t);

	while ($row = db_fetch_array($res_wms)) {
		array_push($obj, array(
			'wms_getcapabilities' => $row['wms_getcapabilities'], 
			'wms_version' => $row['wms_version'], 
			'layer_id' => $row['layer_id'], 
			'title' => $row['wms_title'],
			'extent' => array(
				$row['minx'],
				$row['miny'],
				$row['maxx'],
				$row['maxy']
			)
		));
	}
}

if (count($mylayer) > 0) {
	$v = array();   
	$t = array();   
	$sql_layer = "SELECT DISTINCT l.layer_id, l.fkey_wms_id, l.layer_title, " . 
		"l.layer_name, w.wms_getcapabilities, w.wms_version, " . 
		"e.minx, e.miny, e.maxx, e.maxy " . 
		"FROM layer l LEFT JOIN layer_keyword lkw " . 
		"LEFT JOIN keyword kw ON kw.keyword_id = lkw.fkey_keyword_id " . 
		"ON l.layer_id = lkw.fkey_layer_id " . 
		"LEFT JOIN wms w ON l.fkey_wms_id = w.wms_id " . 
		"LEFT JOIN layer_epsg e ON l.layer_id = e.fkey_layer_id " . 
		"AND e.epsg = '$srs' " . 
		"WHERE l.layer_id IN (";

	for($i = 0; $i < count($mylayer); $i++){
		if ($i > 0) {$sql_layer .= ",";}
		$sql_layer .= "$".($i+1);
		array_push($v, $mylayer[$i]);
		array_push($t, 'i');   
	}

	$sql_layer .= ") AND (";
	if($searchColumnsLayer == "") {
		$sql_layer .= 	"layer_title ILIKE '%".$query."%' OR " . 
						"layer_name ILIKE '%".$query."%' OR " . 
						"layer_abstract ILIKE '%".$query."%' "; 
	}
	else{
		$layerColumnArray = split(",", $searchColumnsLayer);
		for($k = 0; $k < count($layerColumnArray); $k++) {
			if ($k > 0) {
				$sql_layer .= " OR ";
			}
			$sql_layer .= trim($layerColumnArray[$k]) . " ILIKE '%".$query."%'";
		}
	}
	
	$sql_layer .= " OR kw.keyword ILIKE '%".$query."%') ORDER BY l.layer_title;";
	
	$res_layer = db_prep_query($sql_layer,$v,$t);


	while ($row = db_fetch_array($res_layer)) {
		array_push($obj, array(
			'wms_getcapabilities' => $row['wms_getcapabilities'], 
			'wms_version' => $row['wms_version'], 
			'layer_name' => $row['layer_name'], 
			'layer_id' => $row['layer_id'], 
			'title' => $row['layer_title'],
			'extent' => array(
				$row['minx'],
				$row['miny'],
				$row['maxx'],
				$row['maxy']
			)
		));
	}
}
$output = $json->encode($obj);
echo $output;
?>
