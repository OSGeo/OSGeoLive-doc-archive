<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../lib/class_OgcFilter.php";

class OgcFilterTest extends PHPUnit_Framework_TestCase {

	public function testConstructorEmpty () {
		try {
			$filter = new OgcFilter();
		}
		catch (Exception $e) {
			return;
		}
		$this->fail('An expected Exception has not been raised.');
	}

	private function removeWhiteSpace ($string) {
		$str = preg_replace("/\>(\s)+\</", "><", trim($string));
//		$str = preg_replace("/\\n/", "\\n", $str);
		return $str;

	}

	public function testInvalidOperator () {
		try {
			$filter = new OgcFilter("gsdfhj", "a", "b", new WfsConfiguration());
		}
		catch (Exception $e) {
			return;
		}
		$this->fail('An expected Exception has not been raised.');
	}

	public function testInvalidWfsConf () {
		try {
			$filter = new OgcFilter("Intersects", "a", 3, null);
		}
		catch (Exception $e) {
			return;
		}
		$this->fail('An expected Exception has not been raised.');
	}

	public function testPropertyIsLike () {
		$filter = new OgcFilter("LIKE", "a", "3*");
		$expected = <<<FILTER
<ogc:Filter>
	<ogc:PropertyIsLike wildCard="*" singleChar="#" escapeChar="!">
		<ogc:PropertyName>a</ogc:PropertyName>
		<ogc:Literal>3*</ogc:Literal>
	</ogc:PropertyIsLike>
</ogc:Filter>
FILTER;

		$this->assertEquals($this->removeWhiteSpace($expected), $this->removeWhiteSpace($filter->toXml()));
	}

	public function testPropertyIsLikeAnd () {
		$filter1 = new OgcFilter("LIKE", "a", "3*");
		$filter2 = new OgcFilter("LIKE", "b", "*asd");
		$filter = new OgcFilter("AND", array($filter1, $filter2));
		$expected = <<<FILTER
<ogc:Filter>
	<ogc:And>
		<ogc:PropertyIsLike wildCard="*" singleChar="#" escapeChar="!">
			<ogc:PropertyName>a</ogc:PropertyName>
			<ogc:Literal>3*</ogc:Literal>
		</ogc:PropertyIsLike>
		<ogc:PropertyIsLike wildCard="*" singleChar="#" escapeChar="!">
			<ogc:PropertyName>b</ogc:PropertyName>
			<ogc:Literal>*asd</ogc:Literal>
		</ogc:PropertyIsLike>
	</ogc:And>
</ogc:Filter>
FILTER;

		$this->assertEquals($this->removeWhiteSpace($expected), $this->removeWhiteSpace($filter->toXml()));
	}


	// this test is not operational
/*
	public function testOperatorIntersects () {
		$geoJson = <<<GEOJSON
{
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "crs": {
                "type": "name",
                "properties": {
                    "name": "EPSG:4326"
                }
            },
            "geometry": {
                "type": "Polygon",
                "coordinates": [
                    [
                        28,
                        -38
                    ],
                    [
                        31,
                        -38
                    ],
                    [
                        31,
                        -41
                    ],
                    [
                        28,
                        -41
                    ],
                    [
                        28,
                        -38
                    ]
                ]
            }
        }
    ]
}
GEOJSON;
		$wfsConf = new WfsConfiguration();
		
		$filter = new OgcFilter("Intersects", "topp:the_geom", $geoJson, $wfsConf);

		$filterXml = <<<FILTER
<ogc:Filter>
	<Intersects>
		<ogc:PropertyName>topp:the_geom</ogc:PropertyName>
		<gml:Polygon srsName="EPSG:4326">
			<gml:outerBoundaryIs>
				<gml:LinearRing>
					<gml:coordinates>28,-38 31,-38 31,-41 28,-41 28,-38</gml:coordinates>
				</gml:LinearRing>
			</gml:outerBoundaryIs>
		</gml:Polygon>
	</Intersects>
</ogc:Filter>
FILTER;
		
//		$this->assertEquals($filterXml, $filter->toXml());
	}

*/
}
?>