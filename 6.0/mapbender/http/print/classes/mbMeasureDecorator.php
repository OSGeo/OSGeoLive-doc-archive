<?php
class mbMeasureDecorator extends mbTemplatePdfDecorator {

	protected $pageElementType = "measure";
	protected $elementId;
	
	public function __construct($pdfObj, $elementId, $mapConf, $controls) {
		parent::__construct($pdfObj, $mapConf, $controls);
		$this->elementId = $elementId;
		$this->override();		
		$this->decorate();
	}
	
	public function override() {}
	
	public function decorate() {
		include (dirname(__FILE__)."/../print_functions.php");
		
		global $mapOffset_left, $mapOffset_bottom, $map_height, $map_width, $coord;
		global $yAxisOrientation;
		$yAxisOrientation = 1;
		
		if (isset($_REQUEST["measured_x_values"]) && $_REQUEST["measured_x_values"] != "") {
			$x_value_str = $_REQUEST["measured_x_values"];
			$y_value_str = $_REQUEST["measured_y_values"];
			$e = new mb_notice("mbMeasureDecorator: x values: ".$x_value_str);
			$e = new mb_notice("mbMeasureDecorator: y values: ".$y_value_str);
		}
		else {
			return "No measurements found.";
		}
		
		$coord = mb_split(",",$this->pdf->getMapExtent());
		$mapInfo = $this->pdf->getMapInfo();
		foreach ($mapInfo as $k => $v) {
			$e = new mb_notice("mbMeasureDecorator: mapInfo: ".$k."=".$v);
		}
		$mapOffset_left = $mapInfo["x_ul"];
		$mapOffset_bottom = $mapInfo["y_ul"];
		$map_height = $mapInfo["height"];
		$map_width = $mapInfo["width"];
		
		// get the arrays, be aware of the different y-axis values
		$theFullArr = makeCoordPairsForFpdi($x_value_str, $y_value_str);
		foreach ($theFullArr as $oneFullArr) {
			$e = new mb_notice("mbMeasureDecorator: coordinates: ".implode(" ",array_values($oneFullArr)));
		}
    	$thePolyArr = makePolyFromCoord($theFullArr);
    	$e = new mb_notice("mbMeasureDecorator: coordinates: ".implode(" ",$thePolyArr));
	
		if (isClosedPolygon($theFullArr)) {
			$isClosed = TRUE;
		} 
		else {
			$isClosed = FALSE;
		}
		$nr_of_points = count($theFullArr);
		$e = new mb_notice("mbMeasureDecorator: closed polygon: ".$isClosed);
		
		$style = "";
		if($this->conf->do_fill === 1) {
		    $style .= "F";
		}
		if ($this->conf->do_stroke === 1) {
		    $style .= "D";
		}
		if($style != "") {
			$this->pdf->objPdf->SetDrawColor($this->conf->stroke_color->r, $this->conf->stroke_color->g, $this->conf->stroke_color->b);
			$this->pdf->objPdf->ClippingRect($mapOffset_left, $mapOffset_bottom, $map_width, $map_height, true);
    		if ($isClosed) {
    		    $this->pdf->objPdf->Polygon($thePolyArr, $style, array("all" => get_object_vars($this->conf->line_style)), $this->conf->fill_color);
            }
    		else {
    		    if ($this->conf->do_stroke === 1) {
                	$theStrokePointPairs = makeStrokePointPairs($theFullArr);
        			for($i = 0; $i < count($theStrokePointPairs); $i++) {
        				$line = $theStrokePointPairs[$i];
        				$e = new mb_notice("mbMeasureDecorator: line coordinates: ".implode(" ",$line));
        				if ($i != count($theStrokePointPairs) - 1) {
        				    $this->pdf->objPdf->Line($line[0], $line[1], $line[2], $line[3], get_object_vars($this->conf->line_style));
        				}
        			}
    		    }
    		}
    		$this->pdf->objPdf->UnsetClipping();
		}
	}
	

}

?>