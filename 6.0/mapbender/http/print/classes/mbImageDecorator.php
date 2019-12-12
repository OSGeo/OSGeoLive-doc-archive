<?php

class mbImageDecorator extends mbTemplatePdfDecorator {

	protected $pageElementType = "image";
	protected $elementId;
	protected $angle = 0;
	protected $overrideMembersFromRequest = array("angle");
	
	public function __construct($pdfObj, $elementId, $mapConf, $controls) {
		parent::__construct($pdfObj, $mapConf, $controls);
		$this->elementId = $elementId;
		$this->override();		
		$this->decorate();	
	}
	
	public function override() {
		/* returns an array of (request key, member id) arrays */ 
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
		#Image($file,$x,$y,$w=0,$h=0,$type='',$link='', $isMask=false, $maskImg=0, $angle=0)
		$this->pdf->objPdf->Image($this->conf->filename, $this->conf->x_ul, $this->conf->y_ul, $this->conf->width, $this->conf->height,'','',false,0,5,-1*$this->angle);
	}
}


?>
