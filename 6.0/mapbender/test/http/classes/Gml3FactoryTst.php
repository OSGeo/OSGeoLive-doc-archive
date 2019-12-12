<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../http/classes/class_gml_3_factory.php";

class Gml3FactoryTest extends PHPUnit_Framework_TestCase {

	protected $gml3Factory;
	protected $geoJsonPolygonDonut;

	public function setUp () {
		$this->geoJsonPolygonDonut = dirname(__FILE__) . "/../../data/polygonDonut.json";
		$this->gml3PolygonDonut = dirname(__FILE__) . "/../../data/polygonDonut.gml3.xml";
                $this->geoJsonLine = dirname(__FILE__) . "/../../data/line.json";
		$this->gml3Line = dirname(__FILE__) . "/../../data/line.gml3.xml";
                $this->geoJsonPoint = dirname(__FILE__) . "/../../data/point.json";
		$this->gml3Point = dirname(__FILE__) . "/../../data/point.gml3.xml";
                $this->geoJsonPolygon = dirname(__FILE__) . "/../../data/polygon.json";
		$this->gml3Polygon = dirname(__FILE__) . "/../../data/polygon.gml3.xml";
                $this->geoJsonMultiPolygon = dirname(__FILE__) . "/../../data/multipolygon.json";
		$this->gml3MultiPolygon = dirname(__FILE__) . "/../../data/multipolygon.gml3.xml";

                $this->geoJsonMultiLine = dirname(__FILE__) . "/../../data/multiline.json";
		$this->gml3MultiLine = dirname(__FILE__) . "/../../data/multiline.gml3.xml";

                $this->geoJsonMultiPoint = dirname(__FILE__) . "/../../data/multipoint.json";
		$this->gml3MultiPoint = dirname(__FILE__) . "/../../data/multipoint.gml3.xml";

                $this->gml3PolygonDonutNoWhiteSpace = dirname(__FILE__) . "/../../data/polygonDonutNoWhiteSpace.gml3.xml";
		$this->gml3Factory = new Gml_3_Factory();
	}

	public function testRemoveWhiteSpace () {
		$gml3 = file_get_contents($this->gml3PolygonDonut);
		$expectedGml3 = file_get_contents($this->gml3PolygonDonutNoWhiteSpace);
		$this->assertEquals(
			$expectedGml3,
			$this->gml3Factory->removeWhiteSpace($gml3)
		);

	}

	public function testGeoJsonToGml3PolygonDonut() {
		$geoJson = file_get_contents($this->geoJsonPolygonDonut);
		$expectedGml3 = file_get_contents($this->gml3PolygonDonut);

		$gmlObj = $this->gml3Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml3Factory->removeWhiteSpace($expectedGml3),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml3Line() {
		$geoJson = file_get_contents($this->geoJsonLine);
		$expectedGml3 = file_get_contents($this->gml3Line);

		$gmlObj = $this->gml3Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml3Factory->removeWhiteSpace($expectedGml3),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml3Point() {
		$geoJson = file_get_contents($this->geoJsonPoint);
		$expectedGml3 = file_get_contents($this->gml3Point);

		$gmlObj = $this->gml3Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml3Factory->removeWhiteSpace($expectedGml3),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml3Polygon() {
		$geoJson = file_get_contents($this->geoJsonPolygon);
		$expectedGml3 = file_get_contents($this->gml3Polygon);

		$gmlObj = $this->gml3Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml3Factory->removeWhiteSpace($expectedGml3),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml3MultiPolygon() {
		$geoJson = file_get_contents($this->geoJsonMultiPolygon);
		$expectedGml3 = file_get_contents($this->gml3MultiPolygon);

		$gmlObj = $this->gml3Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml3Factory->removeWhiteSpace($expectedGml3),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml3MultiPoint() {
		$geoJson = file_get_contents($this->geoJsonMultiPoint);
		$expectedGml3 = file_get_contents($this->gml3MultiPoint);

		$gmlObj = $this->gml3Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml3Factory->removeWhiteSpace($expectedGml3),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml3MultiLine() {
		$geoJson = file_get_contents($this->geoJsonMultiLine);
		$expectedGml3 = file_get_contents($this->gml3MultiLine);

		$gmlObj = $this->gml3Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml3Factory->removeWhiteSpace($expectedGml3),
			$gmlObj->toGml()
		);
	}

 
}
?>