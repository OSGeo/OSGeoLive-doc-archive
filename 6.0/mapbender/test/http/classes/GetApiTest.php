<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../lib/class_GetApi.php";

class GetApiTest extends PHPUnit_Framework_TestCase {

	public function testSingleLayer () {
		parse_str("LAYER=12", $getArray);

		$apiObject = new GetApi($getArray);
		
		$expected = array(
			array(
				"id" => 12
			)
		);
		$this->assertEquals($expected, $apiObject->getLayers());
	}

	public function testSingleLayerFromApplication () {
		parse_str("LAYER[application]=gui1&LAYER[id]=12", $getArray);
		$apiObject = new GetApi($getArray);
		
		$expected = array(
			array(
				"id" => 12,
				"application" => "gui1"
			)
		);
		$this->assertEquals($expected, $apiObject->getLayers());
	}

	public function testMultipleLayer () {
		parse_str("LAYER[0][application]=gui1&LAYER[0][id]=12&LAYER[1]=13", $getArray);
		$apiObject = new GetApi($getArray);
		
		$expected = array(
			array(
				"id" => 12,
				"application" => "gui1"
			),
			array(
				"id" => 13
			)
		);
		$this->assertEquals($expected, $apiObject->getLayers());
	}

	public function testMultipleLayerComplex () {
		parse_str("LAYER[visible]=0&LAYER[zoom]=1&LAYER[application]=gui1&LAYER[id]=12", $getArray);
		$apiObject = new GetApi($getArray);
		
		$expected = array(
			array(
				"id" => 12,
				"application" => "gui1",
				"visible" => 0,
				"zoom" => 1
			)
		);
		$this->assertEquals($expected, $apiObject->getLayers());
	}
	
	public function testSingleFeaturetype () {
		parse_str("FEATURETYPE=12", $getArray);
		$apiObject = new GetApi($getArray);
		
		$expected = array(
			array(
				"id" => 12
			)
		);
		$this->assertEquals($expected, $apiObject->getFeaturetypes());
	}
	
	public function testMultipleFeaturetypes () {
		parse_str("FEATURETYPE=12,13", $getArray);
		$apiObject = new GetApi($getArray);
		
		$expected = array(
			array(
				"id" => 12
			),
			array(
				"id" => 13
			)
		);
		$this->assertEquals($expected, $apiObject->getFeaturetypes());
	}

	public function testMultipleFeaturetypesArray () {
		parse_str("FEATURETYPE[]=12&FEATURETYPE[]=13", $getArray);
		$apiObject = new GetApi($getArray);
		
		$expected = array(
			array(
				"id" => 12
			),
			array(
				"id" => 13
			)
		);
		$this->assertEquals($expected, $apiObject->getFeaturetypes());
	}

	public function testMultipleFeaturetypesArrayComplex () {
		parse_str("FEATURETYPE[active]=0&FEATURETYPE[search][firstname]=a&FEATURETYPE[search][lastname]=b&FEATURETYPE[id]=12", $getArray);
		$apiObject = new GetApi($getArray);

		$expected = array(
			array(
				"id" => 12,
				"active" => 0,
				"search" => array(
					"firstname" => "a",
					"lastname" => "b"
				)
			)
		);
		$this->assertEquals($expected, $apiObject->getFeaturetypes());
	}

	public function testWmcSimple () {
		parse_str("WMC=12", $getArray);
		$apiObject = new GetApi($getArray);

		$expected = array(
			array(
				"id" => 12
			)
		);
		$this->assertEquals($expected, $apiObject->getWmc());
	}

	public function testWmcSimpleMultiple () {
		parse_str("WMC=12,13,14", $getArray);
		$apiObject = new GetApi($getArray);

		$expected = array(
			array(
				"id" => 12
			),
			array(
				"id" => 13
			),
			array(
				"id" => 14
			)
		);
		$this->assertEquals($expected, $apiObject->getWmc());
	}

}
?>