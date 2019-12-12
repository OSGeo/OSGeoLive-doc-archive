<?php
require_once dirname(__FILE__)."/../../classes/class_SaveLegend.php";

class mbLegendDecorator extends mbTemplatePdfDecorator {

	protected $pageElementType = "legend";
	protected $elementId;
	
	public function __construct($pdfObj, $elementId, $mapConf, $controls) {
		parent::__construct($pdfObj, $mapConf, $controls);
		$this->elementId = $elementId;
		$this->decorate();	
	}
	
	public function override () {
		return null;
	}
	
	public function decorate() {
		$mmPerPt = 0.3527;

		$this->pdf->objPdf->setTextColor(0, 0, 0);
		$this->pdf->objPdf->setFont($this->conf->font_family, "", $this->conf->font_size);

		$currentX = $this->conf->x_ul;
		$currentY = $this->conf->y_ul;

		$json = new Mapbender_JSON();
		$wmsLegendArray = $json->decode($_POST["legend_url"]);
		if (!is_array($wmsLegendArray)) {
			//TODO: error message
			return;
		}
		for ($i = 0; $i < count($wmsLegendArray); $i ++) {
			$layerLegendObj = $wmsLegendArray[$i];
			if (!is_object($layerLegendObj)) {
				continue;
			}
			foreach ($layerLegendObj as $title => $layerLegendArray) {
				if (!is_array($layerLegendArray)) {
					continue;
				}
				// Title
				$titleFontSize = $this->conf->font_size + 1;
				$this->pdf->objPdf->setFont($this->conf->font_family, "", $titleFontSize);
				$this->pdf->objPdf->Text($currentX, $currentY, html_entity_decode(utf8_decode($title)));
//				$currentY += $mmPerPt * $this->conf->font_size;
				$currentY += $titleFontSize;
				
				$this->pdf->objPdf->setFont($this->conf->font_family, "", $this->conf->font_size);
				for ($j = 0; $j < count($layerLegendArray); $j++) {
					// Legend
					$currentLegendObj = $layerLegendArray[$j];

					$this->pdf->objPdf->Text($currentX, $currentY, html_entity_decode(utf8_decode($currentLegendObj->title)));
					//$currentY += $mmPerPt * $this->conf->font_size;
					$currentY += $this->conf->font_size;
					// store current legend image temporarily
					$legendFilename = TMPDIR . "/legend_" . 
					substr(md5(uniqid(rand())),0,7).".png";

					$saveLegend = new SaveLegend(
						$currentLegendObj->legendUrl,
						$legendFilename
					);
					list($width, $height) = getimagesize($legendFilename);
					$width = $width/$this->pdf->objPdf->k;
					$height = $height/$this->pdf->objPdf->k;
					
					try {
					$this->pdf->objPdf->Image(
						$legendFilename, 
						$currentX, 
						$currentY-3.5,
						($width * $this->conf->scale)
					);
					}catch(Exception $E){
						$e = new mb_exception("Can't write Legend Image to pdf: ".$E->getmessage);
					}


					$currentY += ($height * $this->conf->scale) ;
					
				}
				$currentY += 5;

			}
		}
                $this->pdf->unlink($legendFilename); 
	}
}


?>
