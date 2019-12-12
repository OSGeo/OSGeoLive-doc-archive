<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../http/classes/class_gml_2_factory.php";

class Gml2FactoryTest extends PHPUnit_Framework_TestCase {

	protected $gml2Factory;
	protected $geoJsonPolygonDonut;

	public function setUp () {
		$this->geoJsonPolygonDonut = dirname(__FILE__) . "/../../data/polygonDonut.json";
		$this->gml2PolygonDonut = dirname(__FILE__) . "/../../data/polygonDonut.gml2.xml";
                $this->geoJsonLine = dirname(__FILE__) . "/../../data/line.json";
		$this->gml2Line = dirname(__FILE__) . "/../../data/line.gml2.xml";
                $this->geoJsonPoint = dirname(__FILE__) . "/../../data/point.json";
		$this->gml2Point = dirname(__FILE__) . "/../../data/point.gml2.xml";
                $this->geoJsonPolygon = dirname(__FILE__) . "/../../data/polygon.json";
		$this->gml2Polygon = dirname(__FILE__) . "/../../data/polygon.gml2.xml";
                $this->geoJsonMultiPolygon = dirname(__FILE__) . "/../../data/multipolygon.json";
		$this->gml2MultiPolygon = dirname(__FILE__) . "/../../data/multipolygon.gml2.xml";

                $this->geoJsonMultiLine = dirname(__FILE__) . "/../../data/multiline.json";
		$this->gml2MultiLine = dirname(__FILE__) . "/../../data/multiline.gml2.xml";

                $this->geoJsonMultiPoint = dirname(__FILE__) . "/../../data/multipoint.json";
		$this->gml2MultiPoint = dirname(__FILE__) . "/../../data/multipoint.gml2.xml";

                $this->gml2PolygonDonutNoWhiteSpace = dirname(__FILE__) . "/../../data/polygonDonutNoWhiteSpace.gml2.xml";
		$this->gml2Factory = new Gml_2_Factory();
	}

	public function testRemoveWhiteSpace () {
		$gml2 = file_get_contents($this->gml2PolygonDonut);
		$expectedGml2 = file_get_contents($this->gml2PolygonDonutNoWhiteSpace);
		$this->assertEquals(
			$expectedGml2,
			$this->gml2Factory->removeWhiteSpace($gml2)
		);

	}

	public function testGeoJsonToGml2PolygonDonut() {
		$geoJson = file_get_contents($this->geoJsonPolygonDonut);
		$expectedGml2 = file_get_contents($this->gml2PolygonDonut);

		$gmlObj = $this->gml2Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml2Factory->removeWhiteSpace($expectedGml2),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml2Line() {
		$geoJson = file_get_contents($this->geoJsonLine);
		$expectedGml2 = file_get_contents($this->gml2Line);

		$gmlObj = $this->gml2Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml2Factory->removeWhiteSpace($expectedGml2),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml2Point() {
		$geoJson = file_get_contents($this->geoJsonPoint);
		$expectedGml2 = file_get_contents($this->gml2Point);

		$gmlObj = $this->gml2Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml2Factory->removeWhiteSpace($expectedGml2),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml2Polygon() {
		$geoJson = file_get_contents($this->geoJsonPolygon);
		$expectedGml2 = file_get_contents($this->gml2Polygon);

		$gmlObj = $this->gml2Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml2Factory->removeWhiteSpace($expectedGml2),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml2MultiPolygon() {
		$geoJson = file_get_contents($this->geoJsonMultiPolygon);
		$expectedGml2 = file_get_contents($this->gml2MultiPolygon);

		$gmlObj = $this->gml2Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml2Factory->removeWhiteSpace($expectedGml2),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml2MultiPoint() {
		$geoJson = file_get_contents($this->geoJsonMultiPoint);
		$expectedGml2 = file_get_contents($this->gml2MultiPoint);

		$gmlObj = $this->gml2Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml2Factory->removeWhiteSpace($expectedGml2),
			$gmlObj->toGml()
		);
	}

        public function testGeoJsonToGml2MultiLine() {
		$geoJson = file_get_contents($this->geoJsonMultiLine);
		$expectedGml2 = file_get_contents($this->gml2MultiLine);

		$gmlObj = $this->gml2Factory->createFromGeoJson($geoJson);

		$this->assertEquals(
			$this->gml2Factory->removeWhiteSpace($expectedGml2),
			$gmlObj->toGml()
		);
	}

 
}
?>