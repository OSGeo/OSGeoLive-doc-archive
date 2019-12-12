<?php
	require_once dirname(__FILE__) . "/../classes/class_universal_rss_factory.php";

	define("GEORSS", "test_rss_load.xml");
	define("RANDOM_GEORSS", "../tmp/rss.xml");
	define("TITLE", "ingrid OpenSearch: wms datatype:metadata ranking:score");	
	define("URL", "http://213.144.28.233:80/opensearch/query?q=wms+datatype:metadata+ranking:score&amp;h=10&amp;p=1&amp;xml=1&amp;georss=1&amp;ingrid=1");	
	define("DESCRIPTION", "Search results");	
	define("BBOX", "12.0016 54.0477 12.2862 54.2481");
	define("CLASSNAME", "GeoRss");

	$someRssFactory = new UniversalRssFactory();
	
	
	// create existing GeoRss from URL
	$success = true;
	$geoRss = $someRssFactory->createFromUrl(GEORSS);

	if (intval($_GET["test"]) == 1) {
		echo "<br><br>";
		echo "Read GeoRSS at: " . GEORSS . "<br>";
		
		if (is_null($geoRss)) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
		// correct RSS class
		$success = true;
		echo "<br><br>";
		echo "Class should be: " . CLASSNAME . "<br>";
		echo "Class is: " . get_class($geoRss) . "<br>";
		if (get_class($geoRss) !== CLASSNAME) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
		// correct title
		$success = true;
		echo "<br><br>";
		echo "Title should be: " . TITLE . "<br>";
		echo "Title is: " . $geoRss->channel_title . "<br>";
		if ($geoRss->channel_title !== TITLE) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
	
		// correct description
		$success = true;
		echo "<br><br>";
		echo "Description should be: " . DESCRIPTION . "<br>";
		echo "Description is: " . $geoRss->channel_description . "<br>";
		if ($geoRss->channel_description !== DESCRIPTION) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
		// correct URL
		$success = true;
		echo "<br><br>";
		echo "URL should be: " . URL . "<br>";
		echo "URL is: " . htmlentities($geoRss->channel_url, ENT_QUOTES, CHARSET) . "<br>";
		if (htmlentities($geoRss->channel_url, ENT_QUOTES, CHARSET) !== URL) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
		
		// correct bbox
		$success = true;
		echo "<br><br>";
		echo "Bounding box of item #3 should be: " . BBOX . "<br>";
		$box = $geoRss->getItem(2)->getBbox();
		$boxString = 
			$box->min->x . " " . $box->min->y . " " . 
			$box->max->x . " " . $box->max->y;
		echo "Bounding box of item #3  is: " . $boxString . "<br>";
		if ($boxString !== BBOX) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
		// create RSS at
		$success = true;
		echo "<br><br>";
		$aSecondRssFactory = new RssFactory();
		$randomRss = str_replace(".xml", "_" . rand() . ".xml", RANDOM_GEORSS);
		echo "Create random GeoRSS at: " . $randomRss . "<br>";
		$anotherRss = $aSecondRssFactory->createAt($randomRss);
		if (is_null($anotherRss)) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
	
		// create RSS from URL
		$success = true;
		echo "<br><br>";
		$yetAnotherRss = $someRssFactory->createFromUrl($randomRss);
		echo "Random RSS should be of type: Rss<br>";
		echo "Random RSS is of type: " . get_class($yetAnotherRss) . "<br>";
		if (get_class($yetAnotherRss) !== "Rss") {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
		// create GeoRSS at
		$success = true;
		echo "<br><br>";
		$anotherRssFactory = new GeoRssFactory();
		$randomGeoRss = str_replace(".xml", "_" . rand() . ".xml", RANDOM_GEORSS);
		echo "Create random GeoRSS at: " . $randomGeoRss . "<br>";
		$anotherGeoRss = $anotherRssFactory->createAt($randomGeoRss);
		if (is_null($anotherGeoRss)) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
	
		// create GeoRSS from URL
		$success = true;
		echo "<br><br>";
		$yetAnotherGeoRss = $someRssFactory->createFromUrl($randomGeoRss);
		echo "Random GeoRSS should be of type: GeoRss<br>";
		echo "Random GeoRSS is of type: " . get_class($yetAnotherGeoRss) . "<br>";
		if (get_class($yetAnotherGeoRss) !== "GeoRss") {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
		// append item to GeoRSS
		$success = true;
		echo "<br><br>";
		echo "Save random GeoRSS<br>";
		$yetAnotherGeoRss->setTitle("testTitel");
		$yetAnotherGeoRss->setDescription("testDescription");
		$yetAnotherGeoRss->setUrl("testUrl");
		$item = $yetAnotherGeoRss->append();
		$item->setBbox(new Mapbender_bbox(8,49,9,50,"EPSG:4326"));
		$success = $yetAnotherGeoRss->saveAsFile();
		if ($success) {
			echo "OK :-)"; 
		}
		else {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
	
	
		// reload random GeoRSS
		$success = true;
		echo "<br><br>";
		echo "Reload random GeoRSS<br>";
		$reloadedRss = $someRssFactory->createFromUrl($randomGeoRss);
		if (is_null($reloadedRss)) {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
		// correct title
		$success = true;
		echo "<br><br>";
		echo "Title should be: testTitel<br>";
		echo "Title is: " . $reloadedRss->channel_title . "<br>";
		if ($reloadedRss->channel_title !== "testTitel") {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
	
		// correct description
		$success = true;
		echo "<br><br>";
		echo "Description should be: testDescription<br>";
		echo "Description is: " . $reloadedRss->channel_description . "<br>";
		if ($reloadedRss->channel_description !== "testDescription") {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	
		// correct URL
		$success = true;
		echo "<br><br>";
		echo "URL should be: testUrl<br>";
		echo "URL is: " . htmlentities($reloadedRss->channel_url, ENT_QUOTES, CHARSET) . "<br>";
		if (htmlentities($reloadedRss->channel_url, ENT_QUOTES, CHARSET) !== "testUrl") {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
		
		// correct bbox
		$success = true;
		echo "<br><br>";
		echo "Bounding box of item #3 should be: 8 49 9 50<br>";
		$box = $reloadedRss->getItem(0)->getBbox();
		$boxString = 
			$box->min->x . " " . $box->min->y . " " . 
			$box->max->x . " " . $box->max->y;
		echo "Bounding box of item #3  is: " . $boxString . "<br>";
		if ($boxString !== "8 49 9 50") {
			$success = false;
			echo "FAIL :-(";
			die;
		}	
		echo "OK :-)"; 
	}
	else {
		header("Content-type:application/xml");
		echo $geoRss;
	}
?>