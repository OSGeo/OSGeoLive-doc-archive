<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../http/classes/class_universal_rss_factory.php";

class GeoRssFactoryTest extends PHPUnit_Framework_TestCase
{
	var $someRssFactory;
	var $geoRss;
	var $filename;
	
	public function setUp () {
		$this->someRssFactory = new UniversalRssFactory();		
		$this->filename = dirname(__FILE__) . "/../../data/GeoRss_PortalU.xml";
    	$this->geoRss = $this->someRssFactory->createFromUrl($this->filename);
	}

	public function tearDown () {
		unset($this->someRssFactory);	
	}
	
	public function testCreateFromUrl()
    {
        $this->assertNotNull($this->geoRss);
    }
    public function testIsGeoRss()
    {
        $this->assertEquals("GeoRss", get_class($this->geoRss));
    }
}
?>