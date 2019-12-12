<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../http/classes/class_georss_factory.php";

class GeoRssTest extends PHPUnit_Framework_TestCase
{

	var $geoRss;
	var $filename;
	protected $randomRss;

	public function setUp () {
    	$this->randomRss = dirname(__FILE__) . "/../../data/randomRss.xml";
		$this->geoRssFactory = new GeoRssFactory();
		$this->filename = dirname(__FILE__) . "/../../data/GeoRss_PortalU.xml";
    	$this->geoRss = $this->geoRssFactory->createFromUrl($this->filename);

    }

	public function tearDown () {
		unset($this->geoRssFactory);
	}

	public function testTitleCorrect()
    {
        $this->assertEquals(
        	"ingrid OpenSearch: wms datatype:metadata ranking:score",
        	$this->geoRss->channel_title
        );
    }
    public function testDescriptionCorrect()
    {
        $this->assertEquals(
        	"Search results",
        	$this->geoRss->channel_description
        );
    }
    public function testUrlCorrect()
    {
        $this->assertEquals(
        	"http://213.144.28.233:80/opensearch/query?q=wms+datatype:metadata+ranking:score&amp;h=10&amp;p=1&amp;xml=1&amp;georss=1&amp;ingrid=1",
        	htmlentities($this->geoRss->channel_url, ENT_QUOTES, "UTF-8")
        );
    }
    public function testBboxCorrect()
    {
    	$geoRssFactory = new GeoRssFactory();
		$geoRss = $geoRssFactory->createFromUrl($this->filename);
	   	$item = $this->geoRss->getItem(2);
		$this->assertType('GeoRssItem',$item);
		$bbox = $item->getBbox();
		$this->assertType('Mapbender_bbox',$bbox);
		$this->assertEquals(12.0016, $bbox->min->x);
		$this->assertEquals(54.0477, $bbox->min->y);
		$this->assertEquals(12.2862, $bbox->max->x);
		$this->assertEquals(54.2481, $bbox->max->y);

    }
	public function testCreateGeoRssAt()
	{
		$anotherRssFactory = new GeoRssFactory();
		$anotherGeoRss = $anotherRssFactory->createAt($this->randomRss);
		$this->assertNotNull(
        	$anotherGeoRss
        );
	}
	public function testCreateGeoRssFromUrl()
	{
		$someGeoRssFactory = new GeoRssFactory();
		$yetAnotherGeoRss = $someGeoRssFactory->createFromUrl($this->filename);
		$this->assertEquals(
        	"GeoRss",
        	get_class($yetAnotherGeoRss)
        );
	}
	public function testAppendItemToGeoRss()
	{
		$anotherRssFactory = new GeoRssFactory();
		$anotherGeoRss = $anotherRssFactory->createAt($this->randomRss);
		$someGeoRssFactory = new GeoRssFactory();
		$yetAnotherGeoRss = $someGeoRssFactory->createFromUrl($this->randomRss);
		$yetAnotherGeoRss->setTitle("testTitel");
		$yetAnotherGeoRss->setDescription("testDescription");
		$yetAnotherGeoRss->setUrl("testUrl");
		$item = $yetAnotherGeoRss->append();
		$item->setBbox(new Mapbender_bbox(8,49,9,50,"EPSG:4326"));
		$this->assertTrue($yetAnotherGeoRss->saveAsFile());
	}
}
?>