<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../http/classes/class_rss_factory.php";

class RssTest extends PHPUnit_Framework_TestCase
{
	var $rss;

	protected $someRssFactory;
	protected $randomRss;

	public function setUp () {
    	$this->randomRss = dirname(__FILE__) . "/../../data/randomRss.xml";
	}

	public function testCreateRssAt()
    {
    	$this->someRssFactory = new RssFactory();
    	$anotherRss = $this->someRssFactory->createAt($this->randomRss);
    	$this->assertNotNull(
        	$anotherRss
        );
	}
    /*
     * @depends testCreateRssAt
    */
	public function testCreateRssFromUrl()
	{
    	$this->someRssFactory = new RssFactory();
		$yetAnotherRss = $this->someRssFactory->createFromUrl($this->randomRss);
		$this->assertEquals(
        	"Rss",
        	get_class($yetAnotherRss)
        );

        unlink($this->randomRss);
	}
}
?>
