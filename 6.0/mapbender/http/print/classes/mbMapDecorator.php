<?php

require_once(dirname(__FILE__)."/../../classes/class_stripRequest.php");
require_once(dirname(__FILE__)."/../../classes/class_weldMaps2PNG.php");
if (class_exists('Imagick')) {
	require_once(dirname(__FILE__)."/../../classes/class_weldMaps2PNG_rotate.php");
} else { 
	$e = new mb_warning("mbMapDecorator: php-imagick module has to be installed to be able to use rotated printing.");
} 
require_once(dirname(__FILE__)."/../../classes/class_map.php");

class mbMapDecorator extends mbTemplatePdfDecorator {

	protected $pageElementType = "map";
	protected $elementId;
	protected $filename;
	/* a decorator should declare which parameters could be overwritten through the request object */
	protected $overrideMembersFromRequest = array("res_dpi","angle");
	protected $res_dpi;
	protected $angle = 0;
	
	public function __construct($pdfObj, $elementId, $mapConf, $controls) {
		parent::__construct($pdfObj, $mapConf, $controls);
		$this->elementId = $elementId;
		$this->filename = TMPDIR."/".parent::generateOutputFileName("map","png");
		$this->override();		
		$this->decorate();	
	}
	
	public function override() {
		/* returns an array of (request key, member id) arrays */ 
		$idsFromRequest = $this->getPageElementLink($this->elementId);
		foreach ($idsFromRequest as $requestKey => $memberId) {
			$e = new mb_notice("mbMapDecorator: checking overrides: ".$requestKey.$memberId);
		}
		foreach ($_REQUEST as $k => $v) {
			$e = new mb_notice("mbMapDecorator: checking Request: ".$k."=".$v);
		}
		
		foreach ($this->overrideMembersFromRequest as $overrideMemberFromRequest) {
			/* take the value of the config in every case */
			$this->{$overrideMemberFromRequest} = $this->conf->{$overrideMemberFromRequest};
			foreach ($idsFromRequest as $requestKey => $memberId) {
				$e = new mb_notice("mbMapDecorator: before override: set ".$memberId." to ".$requestKey);
				if ($overrideMemberFromRequest==$memberId && isset($_REQUEST[$requestKey]) && $_REQUEST[$requestKey] != "") { 
					$this->{$overrideMemberFromRequest} = $_REQUEST[$requestKey];
					$e = new mb_notice("mbMapDecorator: override from Request: ".$overrideMemberFromRequest." to ".$this->{$overrideMemberFromRequest});
				}
				/* this else branch is not necessary anymore 
				else {
					$this->{$overrideMemberFromRequest} = $this->conf->{$memberId};
					$e = new mb_notice("mbMapDecorator: override from conf: ".$overrideMemberFromRequest." to ".$this->conf->{$memberId});
				}	
				*/
			}	
		}
	}
	
	public function decorate() {
		$urls = $_REQUEST["map_url"];
		$array_urls = explode("___", $urls);
		//problem with false in some map_urls see http/plugins/mb_metadata_wmcPreview.php
		//exchange array_urls with array_urls without false entries
		$newArray_urls = array();
		for ($i=0; $i<count($array_urls); $i++) {
			if ($array_urls[$i] != 'false') {
			    $newArray_urls[] = $array_urls[$i];
			}
		}
		$array_urls = $newArray_urls;
		$e = new mb_notice("print/classes/mbMapDecorator.php: array_urls[0]: ".$array_urls[0]);
		$width = $this->conf->width;
		$height = $this->conf->height;
		$res = $this->pdf->objPdf->k * ($this->res_dpi/72);
		$myURL = new stripRequest($array_urls[0]);
		$e = new mb_notice("mbMapDecorator: original bbox: ".$myURL->get('BBOX')); 
		if (isset($_REQUEST["coordinates"]) && $_REQUEST["coordinates"]!= "") {
			$mapPdfBbox = $_REQUEST["coordinates"];
		} else {			
			$mapPdfBbox = $myURL->get('BBOX');
		}
		$e = new mb_notice("mbMapDecorator: coordinates: ".$mapPdfBbox);
		$this->pdf->setMapInfo($this->conf->x_ul, $this->conf->y_ul, $width, $height, $this->pdf->adjustBbox($this->conf, explode(",",$mapPdfBbox), $myURL->get('srs')));
		$e = new mb_notice("mbMapDecorator: adjusted bbox: ".$this->pdf->getMapExtent());
		for($i=0; $i<count($array_urls); $i++){
			$m = new stripRequest($array_urls[$i]);
			$m->set('width',(intval($width*$res)));
			$m->set('height',(intval($height*$res)));
			$m->set('bbox', $this->pdf->getMapExtent());
			$array_urls[$i] = $m->url;

		}

                $this->pdf->logWmsRequests("maps", $array_urls);
		
		if ($this->angle != 0) {
			if (class_exists('weldMaps2PNG_rotate')) {
				$i = new weldMaps2PNG_rotate(implode("___",$array_urls), $this->filename, $this->angle, false);
			} else {
				$i = new weldMaps2PNG(implode("___",$array_urls), $this->filename, false);
				$e = new mb_warning("mbMapDecorator: no rotation possible.");
			}
		} else {
			$i = new weldMaps2PNG(implode("___",$array_urls), $this->filename, false);
		}
		$this->pdf->objPdf->Image($this->filename, $this->conf->x_ul, $this->conf->y_ul, $width, $height,'png');
		
		/* show coordinates ... */
		if ($this->conf->coords == 1) {
			$coord = mb_split(",",$this->pdf->getMapExtent());

			$myMinx = "R ".substr(round($coord[0]), 0, 4)."".substr(round($coord[0]), 4, 3)."";
			$myMiny = "H ".substr(round($coord[1]), 0, 4)."".substr(round($coord[1]), 4, 3)."";
			$myMaxx = "R ".substr(round($coord[2]), 0, 4)."".substr(round($coord[2]), 4, 3)."";
			$myMaxy = "H ".substr(round($coord[3]), 0, 4)."".substr(round($coord[3]), 4, 3)."";

			$this->pdf->objPdf->setTextColor(0, 0, 0);
			$this->pdf->objPdf->setFont($this->conf->coords_font_family, "", $this->conf->coords_font_size);
			#RotatedText($x, $y, $txt, $angle)
			$this->pdf->objPdf->RotatedText($this->conf->x_ul - 2, $this->conf->y_ul + $height, $myMinx, 90); 
			$this->pdf->objPdf->Text($this->conf->x_ul, $this->conf->y_ul + $height + 3.5, $myMiny);
			$this->pdf->objPdf->RotatedText($this->conf->x_ul + $width + 2, $this->conf->y_ul, $myMaxy, 270);
			$this->pdf->objPdf->Text($this->conf->x_ul + $width - ($this->pdf->objPdf->GetStringWidth($myMaxx)), $this->conf->y_ul - 2, $myMaxx);

		}

                $this->pdf->unlink($this->filename); 
	}
	
}

?>
