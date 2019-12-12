<?php

require_once(dirname(__FILE__)."/../../extensions/fpdf/mb_fpdi.php");

class mbTemplatePdf extends mbPdf {

	/* it seems several decorators are going to need this information */
	public $mapInfo = array();
        public $unlinkFiles = false;
        public $logRequests = false;
        public $logType = "file";

	public function __construct($jsonConf) {
		$this->confPdf = $jsonConf;
		if (!$this->confPdf->orientation ||	!$this->confPdf->units || !$this->confPdf->format) {
			die("no valid config");
		}	
		$this->objPdf = new mb_fpdi($this->confPdf->orientation,
									$this->confPdf->units,
									$this->confPdf->format);
		$this->outputFileName = $this->generateOutputFileName("map","pdf");
	}

	public function setMapInfo($x_ul, $y_ul, $width, $height, $aBboxString) {
		$this->mapinfo["x_ul"] = $x_ul;
		$this->mapinfo["y_ul"] = $y_ul;
		$this->mapinfo["width"] = $width;
		$this->mapinfo["height"] = $height;
		$this->mapinfo["extent"] = $aBboxString;
		$e = new mb_notice("mbTemplatePdf: setting mapInfo ...");
	}

	public function getMapInfo() {
		$e = new mb_notice("mbTemplatePdf: getting mapInfo ..");
		return $this->mapinfo;
	}
	
	public function setMapExtent($aBboxString) {
		$this->mapinfo["extent"] = $aBboxString;
		$e = new mb_notice("mbTemplatePdf: setting mapExtent to ".$this->mapinfo["extent"]);
	}
		
	public function getMapExtent() {
		$e = new mb_notice("mbTemplatePdf: getting mapExtent as ".$this->mapinfo["extent"]);
		return $this->mapinfo["extent"];
	}	

	public function adjustBbox($elementConf, $aBboxArray, $aSrsString) {
		$aMbBbox = new Mapbender_bbox($aBboxArray[0],$aBboxArray[1],$aBboxArray[2],$aBboxArray[3],$aSrsString);
		$aMap = new Map();
		$aMap->setWidth($elementConf->width);
		$aMap->setHeight($elementConf->height);
		$aMap->calculateExtent($aMbBbox);
		$this->mapinfo["scale"] = isset($_REQUEST["scale"]) ? $_REQUEST["scale"] : $aMap->getScale($elementConf->res_dpi);
		$adjustedMapExt = $aMap->getExtentInfo();
		return implode(",",$adjustedMapExt);
	}	
	
	public function render() {
		foreach ($this->confPdf->pages as $pageConf) {
			/* apply the template to the pdf page */
			$this->objPdf->addPage();
            $pagecount = $this->objPdf->setSourceFile(dirname(__FILE__)."/../".$pageConf->tpl);
            $tplidx = $this->objPdf->importPage($pageConf->useTplPage);
            $controls = $this->confPdf->controls;
            $this->objPdf->useTemplate($tplidx);
            
            foreach ($pageConf->elements as $pageElementId => $pageElementConf) {
				
                                switch ($pageElementConf->type) {
                                    	case "map":
						$err = new mbMapDecorator($this, $pageElementId, $pageElementConf, $controls);
						break;
					case "overview":
						$err = new mbOverviewDecorator($this, $pageElementId, $pageElementConf, $controls);
						break;
					case "text":
						$err = new mbTextDecorator($this, $pageElementId, $pageElementConf, $controls);
						break;		
					case "para":
						$err = new mbParagraphDecorator($this, $pageElementId, $pageElementConf, $controls);
						break;	
					case "measure":
						$err = new mbMeasureDecorator($this, $pageElementId, $pageElementConf, $controls);
						break;		
					case "image":
						$err = new mbImageDecorator($this, $pageElementId, $pageElementConf, $controls);
						break;																									
					case "legend":
						$err = new mbLegendDecorator($this, $pageElementId, $pageElementConf, $controls);
						break;
                                        case "permanentImage":
						$err = new mbPermanentImgDecorator($this, $pageElementId, $pageElementConf, $controls);
						break;
				}
            }
            
            $this->isRendered = true;
		}
	}
	
	public function save() {
		if ($this->isRendered) {
			$this->objPdf->Output(TMPDIR."/".$this->outputFileName, "F");
			$this->isSaved = true;
		}
	}

        public function unlink($filename) {
            if($this->unlinkFiles && $this->unlinkFiles == 'true') {
               unlink($filename);
            }
        }

        public function logWmsRequests($requestType, $wmsRequest) {
            if($this->logRequests && $this->logRequests == 'true') {
                include_once (dirname(__FILE__)."/../../classes/class_log.php");
        	$logMessage = new log("printPDF_".$requestType,$wmsRequest,"",$this->logType);
	    }
        }
}


?>
