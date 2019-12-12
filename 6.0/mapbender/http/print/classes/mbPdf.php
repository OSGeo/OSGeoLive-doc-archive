<?php

require_once(dirname(__FILE__)."/../../../core/globalSettings.php");
require_once(dirname(__FILE__) . "/../../classes/class_json.php");

abstract class mbPdf {

	/* the actual pdf */
	public $objPdf; 
	public $confPdf;
	public $outputFileName;

	public $isRendered = false;
	public $isSaved = false;

	abstract public function render();
	abstract public function save();
	
	public function generateOutputFileName($prefix, $suffix) {
		return $prefix."_".substr(md5(uniqid(rand())),0,7).".".$suffix;
	}

	public function returnPDF() {
		if ($this->isSaved) {
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename="'.$this->outputFileName.'"');
		    header('Pragma: public');
		    ob_end_flush();
			$this->objPdf->Output($this->outputFileName,'S');
		}
		else
			die("PDF output not rendered yet.");
	}	

	public function returnAbsoluteUrl() {
		$mbjson = new Mapbender_JSON();
		if ($this->isSaved) {
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
				$prot = "https://";
			else
				$prot = "http://";
			$absoluteUrlToPdf = $prot.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/printPDF_download.php?f=".$this->outputFileName."&".SID;
			return $mbjson->encode(array("outputFileName"=>$absoluteUrlToPdf));
		}
		else
		    return $mbjson->encode(array("error"=>"Possibly no map urls delivered."));
	}	
	
	public function returnUrl() {
		$mbjson = new Mapbender_JSON();
		if ($this->isSaved) {
			return $mbjson->encode(array("outputFileName"=>TMPDIR."/".$this->outputFileName));
		}
		else
		    return $mbjson->encode(array("error"=>"Possibly no map urls delivered."));
	}	

}


?>
