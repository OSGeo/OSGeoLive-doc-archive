<?php
require dirname(__FILE__) . "/../../core/globalSettings.php" ;
$request = new AjaxResponse($_REQUEST);
$method = $request->getMethod();

try{
switch ($method) {

	case "intersect":
		$geometries = $request->getParameter("geometries");
		$clickPoint = $request->getparameter("clickPoint");
		$resultGeometries = array();

		$i = 0;
		foreach($geometries as $geometry){
			$sql = "SELECT ST_Intersects ('$clickPoint'::geometry, '$geometry'::geometry);";
			$dbresult = db_query($sql);
			$row =  db_fetch_array($dbresult);
			$result = $row["st_intersects"] == "f" ? false : true;
			if($result){
				$resultGeometries[$i] = $geometry;
			}
			$i++;
		}

		$request->setSuccess(true);
		$request->setResult(array("geometries" => $resultGeometries));
	break;

	default:
		$e = new mb_exception(__FILE__ . ": RPC called with invalid Method '$method'");
		$request->setSuccess(false);
		$request->setMessage(__FILE__ . ": RPC called with invalid Method '$method'");
}
}catch(Exception $E){
	$e = new mb_exception(__FILE__ . ": RPC failed. Exception: '$E'");
	$request->setSuccess(false);
	$request->setMessage(__FILE__ . ": RPC failed. Exception: '$E'");
}
$request->send();
?>
