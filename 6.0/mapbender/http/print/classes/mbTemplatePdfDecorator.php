<?php

abstract class mbTemplatePdfDecorator extends mbTemplatePdf {

	/* the template pdf object to decorate */
	public $pdf;
	/* the conf object for the desired decoration */
	public $conf;
	/* the controls object of the configuration */
	public $controls;
	/* possibly a zIndex will be needed */
	public $zIndex;	
	
	public function __construct($pdfObj, $mapConf, $controls) {
		$this->pdf = $pdfObj;
		$this->conf = $mapConf;
		$this->controls = $controls;
	}
	
	public function getPageElementLink($pageElementId) {
		$pageElementLinkArray = array();
		$e = new mb_notice("mbTemplatePdfDecorator: pageElementId: ".$pageElementId);
		foreach ($this->controls as $control) {
			foreach ($control->pageElementsLink as $pageElementLinkId => $pageElementLinkValue) {
				$e = new mb_notice("mbTemplatePdfDecorator: pageElementsLink: ".$control->id);
				if ($pageElementLinkId == $pageElementId) 
					#array_push($pageElementLinkArray, array($control->id => $pageElementLinkValue));
					$pageElementLinkArray[$control->id]=$pageElementLinkValue;
			}
		}
		return $pageElementLinkArray;
	} 
	
	abstract public function override();
	
	abstract public function decorate();

}

?>