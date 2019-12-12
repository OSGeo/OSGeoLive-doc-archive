<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../../conf/gazetteerSQL.conf");

$con = 	pg_connect($connstring);		

$command = $_GET["command"];
$communeId = $_GET["communeId"];
$streetName = $_GET["streetName"];
$districtId = $_GET["districtId"];
$parcelNumber1 = $_GET["parcelNumber1"];
$parcelNumber2 = $_GET["parcelNumber2"];
$ownerQueryString = $_GET["ownerQueryString"];
$numberOfResults = $_GET["numberOfResults"];

if (isLimited($numberOfResults)) {
	$limit = $numberOfResults + 1; 
}
else {
	$limit = 0;
}

$obj = array();

function isLimited($numberOfResults) {
	if (isset($numberOfResults) && $numberOfResults > 0) {
		return true;
	}
	return false;
}

function isUnderLimit($counter, $numberOfResults, $max) {
	return (!isLimited($numberOfResults) || $counter <= $max);
}

function isOverLimit($counter, $numberOfResults, $max) {
	return (isLimited($numberOfResults) && $counter > $max);
}

if ($command == "getCommunes") {
	$obj["communes"] = array();

	$sql = "SELECT DISTINCT gkz, name FROM public.gemeinden ORDER BY name";
	$v = array();
	$t = array();
	$res = db_prep_query($sql, $v, $t);
	while($row = db_fetch_array($res)){
		$communeId = trim($row["gkz"]);
		$communeName = trim($row["name"]);
		$obj["communes"][$communeId] = $communeName;
	}
	$obj["limited"] = false;
}
else if ($command == "getStreets") {
	$obj["streets"] = array();

	if (!empty($communeId)) {
		$sql = "SELECT DISTINCT strk_schl, str_name FROM alb.navigation WHERE gkz = $1 ORDER BY str_name";
		$v = array($communeId);
		$t = array("i");
	}
	else {
		$sql = "SELECT DISTINCT strk_schl, str_name FROM alb.navigation ORDER BY str_name";
		$v = array();
		$t = array();
	}
	$res = db_prep_query($sql, $v, $t);
	while($row = db_fetch_array($res)){
		$streetId = trim($row["strk_schl"]);
		$streetName = trim($row["str_name"]);
		$obj["streets"][$streetId] = $streetName;
	}
	$obj["limited"] = false;
}
else if ($command == "getNumbers") {
	$obj["houseNumbers"] = array();
	$paramCounter = 0;
	
	if (!empty($communeId)) {
		$sql = "SELECT DISTINCT hnr, hnrzu, rw, hw FROM alb.navigation WHERE gkz = $". ++$paramCount ." AND str_name ILIKE $". ++$paramCount ." ORDER BY hnr, hnrzu";
		$v = array($communeId, $streetName."%");
		$t = array("i", "s");
	}
	else {
		$sql = "SELECT DISTINCT hnr, hnrzu, rw, hw FROM alb.navigation WHERE str_name ILIKE $". ++$paramCount ." ORDER BY hnr, hnrzu";
		$v = array($streetName."%");
		$t = array("s");
	}

	if (isLimited($numberOfResults)) {
		$sql .= " LIMIT $". ++$paramCount;
		array_push($v, $limit);
		array_push($t, "i");
	}

	$res = db_prep_query($sql, $v, $t);

	$counter = 0;
	while($row = db_fetch_array($res)){
		$counter++;
		if (isUnderLimit($counter, $numberOfResults, $numberOfResults)) {
			$houseNumber = trim($row["hnr"] . $row["hnrzu"]);
			$x = trim(floatval($row["rw"]));
			$y = trim(floatval($row["hw"]));
			$obj["houseNumbers"][$houseNumber] = array("x" => $x, "y" => $y);
		}
	}
	$obj["limited"] = isOverLimit($counter, $numberOfResults, $numberOfResults);
}
else if ($command == "getLandparcelsByOwner") {
	$obj["landparcels"] = array();

	$sql = "SELECT DISTINCT eig.e_name, flst.flst_kennz, flst.rechtsw, flst.hochw FROM alb.albflst AS flst JOIN alb.albeig AS eig ON (flst.gemschl = eig.gemschl AND flst.flur = eig.flur AND flst.flstz = eig.flstz AND flst.flstn = eig.flstn) JOIN public.gemarkungen AS gem ON (flst.gemschl = gem.gemschl) WHERE gem.gkz = $1 AND eig.e_name ILIKE $2 ORDER BY flst.flst_kennz";
	$v = array($communeId, "%".$ownerQueryString."%");
	$t = array("i", "s");

	if (isLimited($numberOfResults)) {
		$sql .= " LIMIT $3";
		array_push($v, $limit);
		array_push($t, "i");
	}
	$res = db_prep_query($sql, $v, $t);

	$counter = 0;
	while($row = db_fetch_array($res)){
		$counter++;
		if (isUnderLimit($counter, $numberOfResults, $numberOfResults)) {
			$landparcelId = $row["flst_kennz"];
			$x = trim(floatval($row["rechtsw"]));
			$y = trim(floatval($row["hochw"]));
			$owner = trim($row["e_name"]);
			array_push($obj["landparcels"], array("landparcelId" => $landparcelId, "owner" => $owner, "x" => $x, "y" => $y));
		}
	}
	$obj["limited"] = isOverLimit($counter, $numberOfResults, $numberOfResults);
}
else if ($command == "getLandparcelsByDistrict") {
	$obj["landparcels"] = array();
	$paramCounter = 0;

	$sql = "SELECT DISTINCT flst_kennz, rechtsw, hochw FROM alb.albflst WHERE gemschl = $" . ++$paramCounter;
	$v = array($districtId);
	$t = array("i");
	if (!empty($parcelNumber1)) {
		$sql .= " AND flur = $" . ++$paramCounter;
		array_push($v, $parcelNumber1);
		array_push($t, "i");
	}
	if (!empty($parcelNumber2)) {
		$sql .= " AND flstz = $" . ++$paramCounter;
		array_push($v, $parcelNumber2);
		array_push($t, "i");
	}
	$sql .= " ORDER BY flst_kennz";
	if (isLimited($numberOfResults)) {
		$sql .= " LIMIT $" . ++$paramCounter;
		array_push($v, $limit);
		array_push($t, "i");
	}
	$res = db_prep_query($sql, $v, $t);

	$counter = 0;
	while($row = db_fetch_array($res)){
		$counter++;
		if (isUnderLimit($counter, $numberOfResults, $numberOfResults)) {
			$landparcelId = $row["flst_kennz"];
			$x = trim(floatval($row["rechtsw"]));
			$y = trim(floatval($row["hochw"]));

			$obj["landparcels"][$landparcelId] = array("x" => $x, "y" => $y);
		}
	}
	$obj["limited"] = isOverLimit($counter, $numberOfResults, $numberOfResults);
}
else if ($command == "getDistricts") {
	$obj["districts"] = array();

	$sql = "SELECT DISTINCT gemschl, name FROM public.gemarkungen WHERE gkz = $1 ORDER BY name";
	$v = array($communeId);
	$t = array("i");
	$res = db_prep_query($sql, $v, $t);

	while($row = db_fetch_array($res)){
		$districtID = trim($row["gemschl"]);
		$districtName = trim($row["name"]);
		$obj["districts"][$districtID] = $districtName;
	}
	$obj["limited"] = false;
}
else {
	// unknown command
	$e = new mb_exception("unknown command: " . $command);
}

$json = new Mapbender_JSON();
$output = $json->encode($obj);
echo $output;
?>