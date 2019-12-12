<?php
define("MONITOR_DEFAULT_SCALE", 500000);
define("MONITOR_IMG_WIDTH", 20);
define("MONITOR_IMG_HEIGHT", 20);
define("MB_RESOLUTION", 28.35);
/**
	 * retrieves all information necessary to build a map request, 
	 * concatenates them and returns a valid get map request
	 */
	 function getMapRequest($wmsId,$version,$getmap) {

		//map format
		$sql = "SELECT * FROM wms_format WHERE fkey_wms_id = $1 AND data_type = 'map'";
		$res = db_prep_query($sql, array($wmsId), array('i'));
		$row = db_fetch_array($res);
		$mapFormat = "";
		while ($row = db_fetch_array($res)) {
			$mapFormat = urlencode($row["data_format"]);
			if (preg_match("/png/", $mapFormat) || preg_match("/gif/", $mapFormat) || preg_match("/jp.{1}g/", $mapFormat)) {
				break;
			}
		}
		
		//do the request as simple png for all layers, cause this is the minimum that all should support. The function above get only the last format out of the database!
		$mapFormat = "image/png";
	
		// layers (all layers)
		$sql = "SELECT layer_name FROM layer WHERE fkey_wms_id = $1 AND layer_parent <> '' AND layer_pos > 0";
		$res = db_prep_query($sql, array($wmsId), array('i'));
		$layerArray = array();
		while ($row = db_fetch_array($res)) {
			array_push($layerArray, urlencode($row["layer_name"]));
		}
		$layerList = implode(",", $layerArray);
	        //Styles
		$styleList='';
		for($i=0; $i<count($layerArray);$i++){
			$styleList .= ',';								}
		$styleList=substr($styleList,1);
		// srs (layer_epsg: epsg)
		// bbox (layer_epsg: minx, miny, maxx, maxy)
		//first read out root layer_id - cause this request is needed more than once!
		$sql = "SELECT layer_id from layer WHERE fkey_wms_id = $1 AND layer_pos = 0 AND layer_parent = ''";
		$res = db_prep_query($sql, array($wmsId), array('i'));
		$row = db_fetch_array($res);
		$rootLayerId = $row["layer_id"];
		//get bbox of the root layer
		$sql = "SELECT epsg, minx, miny, maxx, maxy ";
		$sql .= "FROM layer_epsg WHERE fkey_layer_id = $1";
		//this is done only for root layers!
		$res = db_prep_query($sql, array($rootLayerId), array('i'));
		//push all bboxes from mb_db into one array as mb_bbox object
		$bboxArray = array();
		while ($row = db_fetch_array($res)) {
			array_push($bboxArray, new Mapbender_bbox($row["minx"], $row["miny"], $row["maxx"], $row["maxy"], $row["epsg"]));
		}
	
		// get a bbox in a preferably non WGS84 epsg to use the scalehints
		for ($i=0; $i < count($bboxArray); $i++) {
			$bbox = $bboxArray[$i];		//read out the object	
			if ($bboxArray[$i]->epsg != "EPSG:4326") {
				break; //it ends if some none epsg:4326 bbox was found -maybe this behavior can be exchanged TODO
			}
		}
		
		/*
		 * transform to 31466 if is 4326 - this is done if the loop before dont give other bbox than epsg:4326  
		 * TODO: extend to other EPSG apart from 31466
		 */
		if ($bbox->epsg == "EPSG:4326") {
			$bbox->transform("EPSG:31466");
		}
		
		/*
		 * get map and check if result is image 
		 */
		// check if this WMS supports exception type XML
		$sql = "SELECT data_format FROM wms_format WHERE fkey_wms_id = $1 AND data_type = 'exception'";
		$v = array($wmsId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		while ($row = db_fetch_array($res)) {
			$exceptionFormat = $row["data_format"];
			// set the exception type to xml (if possible)
			if (preg_match('/xml/', $exceptionFormat)) {
				$supportsXMLException = true;
				break; 
			}
		}

		// correct bbox according to scale
		$scale = getScaleForWMS($wmsId);
		//echo "layer_id: ".$rootLayerId."\n";
		//echo "Scale: ".$scale."\n";
		//var_dump($bbox);
		//echo "\n";
		$e = new mb_exception("monitorDefineGetMapBBOX: BBOX berechnen fuer wms " . $wmsId);
		if (($bbox!=NULL) and ($bbox->min!=NULL) and ($bbox->max!=NULL)) {
			$bbox = getBBoxInScale($bbox, $scale);
			return concatenateMapRequest($getmap, $version, $mapFormat, $layerList, $styleList,$bbox, MONITOR_IMG_WIDTH, MONITOR_IMG_HEIGHT, $exceptionFormat);
		}
		else {
			return "Monitor Error: The root-layer of the service dont include a BBOX";			
		}
		 
	}
	
	/**
	 * updates a given BBox according to a given scale
	 * 
	 * @param bbox 
	 * @param scale 
	 */
	 function getBBoxInScale($bbox, $scale) {
		#$e = new mb_notice("class_monitor: getMapRequest: old bbox = " . $bbox);
		#$e = new mb_notice("class_monitor: getMapRequest: old bbox = " . $bbox->min->x . "," . $bbox->min->y . "," . $bbox->max->x . "," . $bbox->max->y);
		#$e = new mb_notice("class_monitor: getMapRequest: scale = " . $scale);
		if ($scale) {
			$e = new mb_exception("monitorDefineGetMapBBOX: minmaxwerte? " . $bbox->max." ".$bbox->min);
			$center = $bbox->min->plus($bbox->max)->times(0.5);
		#	$e = new mb_notice("class_monitor: getMapRequest: center = " . $center);
			
			/*
			 * TODO: this formula should have documentation
			 */
			$offset =  MONITOR_IMG_WIDTH / (MB_RESOLUTION * 100 * 2) * $scale;
			$offsetPoint = new Mapbender_point($offset, $offset, $bbox->epsg);
			$min = $center->minus($offsetPoint);
			$max = $center->plus($offsetPoint);
			$bbox->min = $min;
			$bbox->max = $max;
			#$e = new mb_notice("class_monitor: getMapRequest: new bbox = " . $bbox);
			#$e = new mb_notice("class_monitor: getMapRequest: new bbox = " . $bbox->min->x . "," . $bbox->min->y . "," . $bbox->max->x . "," . $bbox->max->y);
		}
		return $bbox;
	}
	
	
	/**
	 * Returns an online resource representing a get map request
	 */
	 function concatenateMapRequest(	$getmap, $wmsVersion, $mapFormat, $layerList, $sytleList,
							$bbox, $width, $height, $exceptionFormat) {
		/*
		 * getMap URL
		 */
		$mapRequest = $getmap;
		$mapRequest .= mb_getConjunctionCharacter($getmap);
		
		/*
		 * WMS version
		 */
		if ($wmsVersion == "1.0.0") {
			$mapRequest .= "WMTVER=" . $wmsVersion . "&REQUEST=map&";
		}
		else {
			$mapRequest .= "VERSION=" . $wmsVersion . "&REQUEST=GetMap&SERVICE=WMS&";
		}
		
		/*
		 * Layer list
		 */
		$mapRequest .= "LAYERS=" . $layerList . "&";
		/*
		 * Sytle List
		 */
		$mapRequest .= "STYLES=" . $styleList ."&"; 
		
		/*
		 * Format
		 */
		 $mapRequest .= "FORMAT=" . $mapFormat . "&";
		 
		/*
		 * SRS and BBox
		 */
		$mapRequest .= "SRS=" . $bbox->epsg . "&";
		$mapRequest .= "BBOX=" . $bbox->min->x . "," . $bbox->min->y . "," . $bbox->max->x . "," . $bbox->max->y . "&";
		 
		/*
		 * Width and height
		 */
		$mapRequest .= "WIDTH=" . $width . "&";
		$mapRequest .= "HEIGHT=" . $height . "&";
		 
		/*
		 * BGColor
		 */
		$mapRequest .= "BGCOLOR=0xffffff&";
	
		/*
		 * Transparency
		 */
		if (preg_match("/png/", $mapFormat) || preg_match("/gif/", $mapFormat)) {
			$mapRequest .= "TRANSPARENT=TRUE&";
		}
		 
		/*
		 * Exception format
		 */
		$mapRequest .= "EXCEPTIONS=" . $exceptionFormat . "&";
		
//		return urlencode($mapRequest);	
		return $mapRequest;	
	}
	
	/**
	 * Checks if the given WMS has ScaleHints. If yes, a scale is selected and returned.
	 */
	 function getScaleForWMS($rootLayerId) {
		// get the scalehints
		$sql = "SELECT layer_minscale, layer_maxscale FROM layer WHERE layer_id = $1 AND layer_minscale <> layer_maxscale LIMIT 1";
		$v = array($rootLayerId);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		$scaleHintArray = db_fetch_array($res);
		
		/*
		 *  determine the scalehint
		 */
		// if a scalehint exists 
		if ($scaleHintArray) {
			// if upper boundary
			if ($scaleHintArray["layer_minscale"] < $scaleHintArray["layer_maxscale"]) {
				// TODO: find a better algorithm with a less obscure scale
				$scaleHint = round($scaleHintArray["layer_maxscale"] - $scaleHintArray["layer_minscale"]) / 2;				
			}
			// if lower boundary
			else {
				if ($scaleHintArray["layer_minscale"] < MONITOR_DEFAULT_SCALE) {
					$scaleHint = MONITOR_DEFAULT_SCALE;
				}
				else {
					// TODO: find a better algorithm with a less obscure scale
					$scaleHint = $scaleHintArray["layer_minscale"] + 1000;
				}
			}
		}
		// otherwise, use a default value
		else {
			$scaleHint = MONITOR_DEFAULT_SCALE;
		}
		return $scaleHint;
	}
/**
	 * Returns the character that needs to be appended to 
	 * a given online resource, in order to append other GET 
	 * parameters.
	 * 
	 * Possible characters: "?", "&", ""
	 */
	function mb_getConjunctionCharacter($onlineresource){
		// index of character ? in online resource
		$indexOfChar = mb_strpos($onlineresource,"?");
	
		if($indexOfChar) {
			// no conjunction character needed
			if($indexOfChar == mb_strlen($onlineresource)-1){ 
				return "";
			}
			// no conjunction character needed
			else if (mb_substr($onlineresource, mb_strlen($onlineresource)-1) == "&") {
				return "";
			}
			else{
				return "&";
			}
		}
		return "?";
	}

?>
