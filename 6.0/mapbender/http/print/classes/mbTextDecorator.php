<?php

class mbTextDecorator extends mbTemplatePdfDecorator {

	protected $pageElementType = "text";
	protected $elementId;
	/* a decorator should declare which parameters could be overwritten through the request object */
	protected $overrideMembersFromRequest = array("value");
	/* the actual text that should be printed */
	protected $value = "Lorem ipsum";
	
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
			$e = new mb_notice("mbTextDecorator: checking overrides: ".$requestKey.$memberId);
		}
		foreach ($_REQUEST as $k => $v) {
			$e = new mb_notice("mbTextDecorator: checking Request: ".$k."=".$v);
		}
		
		foreach ($this->overrideMembersFromRequest as $overrideMemberFromRequest) {
			switch ($this->conf->{$overrideMemberFromRequest})  {
				case "date": 
					$this->{$overrideMemberFromRequest} = date("j.n.Y");
					break;
				case "time": 
					$this->{$overrideMemberFromRequest} = date("G:i");
					break;
				case "scale": 
					$mapInfoScale = $this->pdf->getMapInfo();
					$this->{$overrideMemberFromRequest} = "1 : ".$mapInfoScale["scale"];
					break;
				default:
					$this->{$overrideMemberFromRequest} = $this->conf->{$overrideMemberFromRequest};
					foreach ($idsFromRequest as $requestKey => $memberId) {
						$e = new mb_notice("mbTextDecorator: before override: set ".$memberId." to ".$requestKey);
						if ($overrideMemberFromRequest==$memberId && isset($_REQUEST[$requestKey]) && $_REQUEST[$requestKey] != "") { 
							$this->{$overrideMemberFromRequest} = $_REQUEST[$requestKey];
							$e = new mb_notice("mbTextDecorator: override from Request: ".$overrideMemberFromRequest." to ".$this->{$overrideMemberFromRequest});
						}
					}	
					break;
			}
		}
	}
	
	public function decorate() {
		$this->pdf->objPdf->setTextColor(0, 0, 0);
		$this->pdf->objPdf->setFont($this->conf->font_family, "", $this->conf->font_size);
		$this->pdf->objPdf->Text($this->conf->x_ul, $this->conf->y_ul, utf8_decode($this->value));
	}
	

}

?>
