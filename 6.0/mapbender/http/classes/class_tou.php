<?php
require_once(dirname(__FILE__) . "/../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");

class tou{
 	private $id;
	private $json;
	private $acceptedTou;
	//obj structure for acceptedTou:
	//acceptedTou {
	//		wms [100,101,112],
	//		wfs [12,34]
	//		}

 	
 	/**
	 * @constructor
	 */
 	function __construct() {
 		$id = false;
		$this->json = new Mapbender_JSON();

		$resultObject = array();
 		new mb_notice("mapbender tou instantiated ... ");
 	}

	function set($serviceType, $serviceId) {
		if (!Mapbender::session()->exists("acceptedTou")) {
			//create one initially
			$acceptedTou = new stdClass;
			$acceptedTou = (object) array(
				'wms' => array(),
				'wfs' => array()
				);
			
			$acceptedTou->{$serviceType}[0] = $serviceId;
			$acceptedTouJson = $this->json->encode($acceptedTou);
			Mapbender::session()->set("acceptedTou",$acceptedTouJson);
			$resultObj = array(
				"setTou" => 1,
				"message" => "New session var acceptedTou generated"
			);
			return $resultObj;
		} else {
			//tou has been set before - add an element to the corresponding list
			$acceptedTou = Mapbender::session()->get("acceptedTou");
			$acceptedTou = json_decode($acceptedTou);
			#print_r($acceptedTou);
			$serviceIdArray = $acceptedTou->{$serviceType};
			//check if id is defined in array already if not append it
			if (!in_array($serviceId,$serviceIdArray)) {
				array_push($acceptedTou->{$serviceType},$serviceId);
				$acceptedTouJson = $this->json->encode($acceptedTou);
				Mapbender::session()->set("acceptedTou",$acceptedTouJson);
				$resultObj = array(
					"setTou" => 1,
					"message" => "Id appended to existing session var acceptedTou"
				);
			} else {
				$resultObj = array(
					"setTou" => 0,
					"message" => "Id was set before, don't append to session var acceptedTou"
				);
			}
			return $resultObj;
		}
	}

	function check($serviceType, $serviceId) {
		if (!Mapbender::session()->exists("acceptedTou")) {
			$resultObj = array(
				"accepted" => 0,
				"message" => "No session var acceptedTou exists til now"
			);	
			return $resultObj;
		} else {		
			$acceptedTou = Mapbender::session()->get("acceptedTou");
			$acceptedTou = json_decode($acceptedTou);
			//read out service part
			$serviceIdArray = $acceptedTou->{$serviceType};
			#print_r($serviceIdArray);
			if (in_array($serviceId,$serviceIdArray)) {
				$resultObj = array(
					"accepted" => 1,
					"message" => "Session var acceptedTou found - id was set before - don't show tou anymore"
				);
				return $resultObj;
			} else {
				$resultObj = array(
					"accepted" => 0,
					"message" => "Session var acceptedTou found - id was not set before - show tou before load resource"
				);	
				return $resultObj;
			}
		}
	}

}

?>
