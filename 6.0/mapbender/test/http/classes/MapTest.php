<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../http/classes/class_map.php";

class MapTest extends PHPUnit_Framework_TestCase
{

	var $map;
	
	public function setUp () {
		$this->map = new Map();
		echo "setup";
	}

	public function tearDown () {
		unset($this->map);
	}
	
	public function testMapCreate()
    {
        $this->assertNotNull(
        	$this->map
        );
    }

	public function testMapSetWidthHeight()
    {
    	$this->map->setWidth(100);
        $this->assertEquals(100,$this->map->getWidth(100));
        $this->map->setHeight(100);
        $this->assertEquals(100,$this->map->getHeight(100));
    }
    
	public function testMapSetExtent()
    {
    	$mapExtent = new Mapbender_bbox(0,0,20,20,"EPSG:4326");
    	$this->map->setExtent($mapExtent);
		$this->assertEquals($mapExtent,$this->map->getExtent());
		echo "-- testMapSetExtent";
    } 
       
	public function testMapGetEpsg()
    {
    	$mapExtent = new Mapbender_bbox(0,0,20,20,"EPSG:4326");
    	$this->map->setExtent($mapExtent);    
		$this->assertEquals('EPSG:4326',$this->map->getEpsg());
		echo "-- testMapGetEpsg";
    } 

}
?>