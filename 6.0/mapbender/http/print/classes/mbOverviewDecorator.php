<?php

require_once(dirname(__FILE__)."/../../classes/class_stripRequest.php");
require_once(dirname(__FILE__)."/../../classes/class_weldOverview2PNG.php");
if (class_exists('Imagick')) {
	require_once(dirname(__FILE__)."/../../classes/class_weldOverview2PNG_rotate.php");
} else {
	$e = new mb_warning("mbOverviewDecorator: php-imagick module has to be installed to be able to use rotated printing.");
}

class mbOverviewDecorator extends mbTemplatePdfDecorator {

	protected $pageElementType = "overview";
	protected $elementId;
	protected $filename;
	protected $overrideMembersFromRequest = array("angle");
	protected $angle = 0;
		
	
	public function __construct($pdfObj, $elementId, $mapConf, $controls) {
		parent::__construct($pdfObj, $mapConf, $controls);
		$this->elementId = $elementId;
		$this->filename = TMPDIR."/".parent::generateOutputFileName("map","png");		
		$this->override();		
		$this->decorate();	
	}
	
	public function override() {
		$idsFromRequest = $this->getPageElementLink($this->elementId);
		foreach ($idsFromRequest as $requestKey => $memberId) {
			$e = new mb_notice("mbOverviewDecorator: checking overrides: ".$requestKey.$memberId);
		}
		foreach ($_REQUEST as $k => $v) {
			$e = new mb_notice("mbOverviewDecorator: checking Request: ".$k."=".$v);
		}
		
		foreach ($this->overrideMembersFromRequest as $overrideMemberFromRequest) {
			/* take the value of the config in every case */
			$this->{$overrideMemberFromRequest} = $this->conf->{$overrideMemberFromRequest};
			foreach ($idsFromRequest as $requestKey => $memberId) {
				$e = new mb_notice("mbOverviewDecorator: before override: set ".$memberId." to ".$requestKey);
				if ($overrideMemberFromRequest==$memberId && isset($_REQUEST[$requestKey]) && $_REQUEST[$requestKey] != "") { 
					$this->{$overrideMemberFromRequest} = $_REQUEST[$requestKey];
					$e = new mb_notice("mbOverviewDecorator: override from Request: ".$overrideMemberFromRequest." to ".$this->{$overrideMemberFromRequest});
				}
				/* this else branch is not necessary anymore 
				else {
					$this->{$overrideMemberFromRequest} = $this->conf->{$memberId};
					$e = new mb_notice("mbOverviewDecorator: override from conf: ".$overrideMemberFromRequest." to ".$this->conf->{$memberId});
				}
				*/	
			}	
		}
	}
	
	public function decorate() {
		$overview_url = $_REQUEST["overview_url"];
		$o_url = new stripRequest($overview_url);
		$width = $this->conf->width;
		$height = $this->conf->height;
		$res = $this->pdf->objPdf->k*($this->conf->res_dpi/72);		
		$o_url->set('width',intval($width*$res));
		$o_url->set('height',intval($height*$res));
		$o_url->set('bbox', $this->pdf->adjustBbox($this->conf, explode(",",$o_url->get('BBOX')), $o_url->get('srs')));
		$overview_url = $o_url->url;	

		$urls = $_REQUEST["map_url"];
		$array_urls = explode("___", $urls);
		//problem with false in some map_urls see http/plugins/mb_metadata_wmcPreview.php
		//exchange array_urls with array_urls without false entries - it depends on the scale hints - if not visible the map_url is false!
		$newArray_urls = array();
		for ($i=0; $i<count($array_urls); $i++) {
			if ($array_urls[$i] != 'false') {
			    $newArray_urls[] = $array_urls[$i];
			}
		}
		$array_urls = $newArray_urls;

                $this->pdf->logWmsRequests("overview", $array_urls);
                
		$myURL = new stripRequest($array_urls[0]);
		$myURL->set('bbox', $this->pdf->getMapExtent());
		if ($this->angle != 0) {
			if (class_exists('weldOverview2PNG_rotate')) {
				$rotatedExtent = $this->rotatePoints(explode(",",$this->pdf->getMapExtent()), intval($this->angle));
				for ($i==0;$i<count($rotatedExtent);$i++) 
					$e = new mb_notice("mbOverviewDecorator: rotated extent: " . implode("|",$rotatedExtent[$i]));
				$i = new weldOverview2PNG_rotate($overview_url, $myURL->url, $this->filename, $rotatedExtent);
			} else {
				$i = new weldOverview2PNG($overview_url, $myURL->url, $this->filename);
				$e = new mb_warning("mbOverviewDecorator: no rotation possible.");				
			}
		} else {
			$i = new weldOverview2PNG($overview_url, $myURL->url, $this->filename);
		}		
		
		$this->pdf->objPdf->Image($this->filename, $this->conf->x_ul, $this->conf->y_ul, $width, $height, 'png');

                $this->pdf->unlink($this->filename);
	}
	
	protected function rotate($point, $center, $angle) {
		if ($angle === 0) {
			return $point;
		}
		// first, calculate point around 0
		// then rotate
		// then add center vector again
		
		$pNew = array(
			$point[0] - $center[0],
			$point[1] - $center[1]
		);
		
		$angle = deg2rad(-$angle);
		return array(
			($pNew[0] * cos($angle) + $pNew[1] * sin($angle)) + $center[0],
			($pNew[0] * -sin($angle) + $pNew[1] * cos($angle)) + $center[1]
		);
	}
	
	protected function rotatePoints($coordArray, $angle) {
		$center = array(
			($coordArray[2] + $coordArray[0]) / 2,
			($coordArray[3] + $coordArray[1]) / 2,
		);
		
		$p1 = array(
			$coordArray[0],
			$coordArray[1]
		);
		$p2 = array(
			$coordArray[2],
			$coordArray[1]
		);
		$p3 = array(
			$coordArray[2],
			$coordArray[3]
		);
		$p4 = array(
			$coordArray[0],
			$coordArray[3]
		);
		
		$newCoordArray = array(
			$this->rotate($p1, $center, $angle),
			$this->rotate($p2, $center, $angle),
			$this->rotate($p3, $center, $angle),
			$this->rotate($p4, $center, $angle)
		);
		return $newCoordArray;
	}	
}


?>
