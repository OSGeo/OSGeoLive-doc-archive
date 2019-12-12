<?php
/**
 * $Id: print_functions.php 8089 2011-08-31 12:15:28Z verenadiewald $
 * 
 * converts the coordinates created via the JavaScript
 * functions in 'mod_measure.php' into an array which 
 * then holds both X- ans Y-values for each point in
 * the formats 'realworld coordinate' and 'pdf_coordinate' 
 * 
 * @param string commaseperated X-values of the points (realworld coordinate)
 * @param string commaseperated Y-values of the points (realworld coordinate)
 * 
 * @return array Array looking like 0 => array(
 *                                         'real_x' => 1234567,
 *                                         'real_y' => 7654321,
 *                                         'pdf_x'  => 451.12,
 *                                         'pdf_y'  => 254.7          
 *                                       )
 * 
 * @see transformForPDF
 *
 * @author M. Jansen <jansen@terrestris.de>, 2006-05-26
 */
function makeCoordPairs($x_values, $y_values) {
        $arr_x = explode(",", $x_values);
	$arr_y = explode(",", $y_values);
	$x_elems = count($arr_x);
	$y_elems = count($arr_y);
	
	$the_return_arr = array();
	
	if ($x_elems == $y_elems) {
	  for ($i = 0; $i < $x_elems; $i ++) {
            $the_return_arr[$i] = array(
                "real_x" => $arr_x[$i],
                "real_y" => $arr_y[$i],
                "pdf_x"  => transformForPDF($arr_x[$i], "x"),
                "pdf_y"  => transformForPDF($arr_y[$i], "y"));
            }
	}
	
	return $the_return_arr;
} // end of function makeCoordPairs

function makeCoordPairsForFpdi($x_values, $y_values) {
        $arr_x = explode(",", $x_values);
	$arr_y = explode(",", $y_values);
	$x_elems = count($arr_x);
	$y_elems = count($arr_y);

	$the_return_arr = array();

	if ($x_elems == $y_elems) {
	  for ($i = 0; $i < $x_elems; $i ++) {
            $the_return_arr[$i] = array(
                "real_x" => $arr_x[$i],
                "real_y" => $arr_y[$i],
                "pdf_x"  => transformForPDFFpdi($arr_x[$i], "x"),
                "pdf_y"  => transformForPDFFpdi($arr_y[$i], "y"));
            }
	}

	return $the_return_arr;
} // end of function makeCoordPairsForFpdi

/**
 * Transforms given realworld-coordinate according to its type (X or Y) 
 * into a pdf-coordinate. Needs the variables $mapOffset_left, $mapOffset_bottom,
 * $map_height, $map_width, $coord to be defined in a global scope.
 *
 * @param float the realworld coordinate
 * @param string type of coordinate either 'X' or 'Y'
 *   
 * @see makeCoordPairs [needs this function]
 *
 * @return float the pdf-coordinate
 *
 * @author M. Jansen <jansen@terrestris.de>, 2006-05-26
 */
function transformForPDF ($theRealCoord, $theType) {
  global $mapOffset_left, $mapOffset_bottom, $map_height, $map_width, $coord;
	
	$thePDFvalue = "";
	
	switch (mb_strtolower($theType)) {
	  case 'x':
		  // calculate pdf x-pos:
			$real_shown_width = $coord[2] - $coord[0];
			$ratio_to_display = $map_width / $real_shown_width;
			$target_width     = $theRealCoord - $coord[0];
			$thePDFvalue      = $mapOffset_left + ($target_width * $ratio_to_display);
			break;
		case 'y':
		  // calculate pdf y-pos:
			$real_shown_height = $coord[3] - $coord[1];
			$ratio_to_display  = ($map_height / $real_shown_height);
			$target_height     = $theRealCoord - $coord[1];
			$thePDFvalue       = $mapOffset_bottom + ($target_height * $ratio_to_display);
		  break;
		default:
		  // a non valid parameter was given
		  $thePDFvalue = $theRealCoord;
      break;
	}
	return $thePDFvalue;
} // end of function transformForPDF

/**
 * Use for new template print with lib fpdi:
 * Transforms given realworld-coordinate according to its type (X or Y)
 * into a pdf-coordinate. Needs the variables $mapOffset_left, $mapOffset_bottom,
 * $map_height, $map_width, $coord to be defined in a global scope.
 *
 * @param float the realworld coordinate
 * @param string type of coordinate either 'X' or 'Y'
 *
 * @see makeCoordPairsForFpdi [needs this function]
 *
 * @return float the pdf-coordinate
 *
 * @author V. Diewald
 */
function transformForPDFFpdi ($theRealCoord, $theType) {
  global $mapOffset_left, $mapOffset_bottom, $map_height, $map_width, $coord;

	$thePDFvalue = "";

	switch (mb_strtolower($theType)) {
	  case 'x':
		  // calculate pdf x-pos:
			$real_shown_width = $coord[2] - $coord[0];
			$ratio_to_display = $map_width / $real_shown_width;
			$target_width     = $theRealCoord - $coord[0];
			$thePDFvalue      = $mapOffset_left + ($target_width * $ratio_to_display);
			break;
		case 'y':
		  // calculate pdf y-pos:
			$real_shown_height = $coord[3] - $coord[1];
			$ratio_to_display  = ($map_height / $real_shown_height);
			$target_height     = $theRealCoord - $coord[1];
			$thePDFvalue       = $mapOffset_bottom +($map_height- ($target_height * $ratio_to_display));
		  break;
		default:
		  // a non valid parameter was given
		  $thePDFvalue = $theRealCoord;
      break;
	}
	return $thePDFvalue;
} // end of function transformForPDFFpdi


/**
 * extracts PDF-relevant information from a full coordinates array 
 * and returns a transformed array
 * 
 * @param array the Array containing all infos about single coordinates
 *              created via makeCoordPairs()
 * 
 * @return array the array containing PDF-Values for a polygon
 *
 * @see makeCoordPairs
 *
 * @author M. Jansen <jansen@terrestris.de>, 2006-05-26
 */
function makePolyFromCoord($theFullCoordinatesArray) {
  $theRetArr = array();
	foreach($theFullCoordinatesArray as $singlePoint) {
	  $theRetArr[] = $singlePoint['pdf_x'];
		$theRetArr[] = $singlePoint['pdf_y'];
	}
	return $theRetArr;
} // end of function makePolyFromCoord




/**
 * converts an array of coordinates (created e.g. by makeCoordPairs()) into
 * an array thats needed to draw lines via line(x0, y0, x1, y1)
 *
 * @param array the Array containing all infos about single coordinates
 *              created via makeCoordPairs()
 * 
 * @return array the array containing PDF-Values for single lines
 *
 * @see makeCoordPairs
 *
 * @author M. Jansen <jansen@terrestris.de>, 2006-05-26
 */
function makeStrokePointPairs($thePointArray) {
  $cnt = count($thePointArray);
	$theRetArray = array();
  for($i = 0; $i < $cnt; $i++) {
	  if($i != $cnt - 1) {
		  $theRetArray[] = array(
		    $thePointArray[$i]['pdf_x'],
				$thePointArray[$i]['pdf_y'],
				$thePointArray[$i+1]['pdf_x'],
				$thePointArray[$i+1]['pdf_y'],
				);
		} else {
		  $theRetArray[] = array(
		    $thePointArray[$i]['pdf_x'],
				$thePointArray[$i]['pdf_y'],
				$thePointArray[0]['pdf_x'],
				$thePointArray[0]['pdf_y'],
				);
		}
	}
	return $theRetArray;
} // end of function makeStrokePointPairs




/**
 * test whether the coordinates in an array form a closed polygon
 * meaning that first an last point of polygon are equal
 *
 * @param array the Array containing all infos about single coordinates
 *              created via makeCoordPairs()
 * 
 * @return bool is it closed (TRUE || FALSE)
 *
 * @see makeCoordPairs
 *
 * @author M. Jansen <jansen@terrestris.de>, 2006-05-26
 */
function isClosedPolygon($coord_arr) {
  $nr_of = count($coord_arr);
	if ($coord_arr[0]['pdf_x'] == $coord_arr[$nr_of-1]['pdf_x']
	    && $coord_arr[0]['pdf_y'] == $coord_arr[$nr_of-1]['pdf_y']
			&& $nr_of != 1) {
	  return TRUE; 
	} else {
	  return FALSE;
	}
}  // end of function isClosedPolygon




/**
 * Adds the measured item to the PDF output.
 * 
 * @param object reference (!) to the current ezPDF-Object
 * @param string commaseperated X-Values of polygon / line
 * @param string commaseperated Y-Values of polygon / line
 * @param array configuration settings.
 *
 * @return void nothing
 *
 * @see makeCoordPairs
 * @see isClosedPolygon
 * @see makeStrokePointPairs
 * @see makePolyFromCoord
 * @see transformForPDF
 * @see makeCoordPairs
 *
 * @author M. Jansen <jansen@terrestris.de>, 2006-05-26
 */
function addMeasuredItem($thePDF, $x_value_str, $y_value_str, $theConfArray = array()) {
  // get global variable:
  global $legendFilenameUserPolygon;
	// create legend image:
	$legend_width = 17;
	$leg_img = imagecreate($legend_width, $legend_width);
	// save previous state:
	$thePDF->saveState();
	
	// save colors for legend:
	if(!defined("MAP_HAS_USER_POLYGON")) {
	  define("MAP_HAS_USER_POLYGON", "test");
	}

	// get the arrays
	$theFullArr = makeCoordPairs($x_value_str, $y_value_str);
    $thePolyArr = makePolyFromCoord($theFullArr);
	
	if (isClosedPolygon($theFullArr)) {
		$isClosed = TRUE;
	} 
	else {
		$isClosed = FALSE;
	}
	$nr_of_points = count($theFullArr);
	
	// is fill option set?
	// wenn der erste und letzte punkt nicht �bereinstimmen,
	// so muss in jedem Falle dofill auf 0 gesetzt werden
	if($theConfArray['do_fill'] != '' && $isClosed) {
	  $doFill = 1;
	  // which color to use for filling?
	  if (is_array($theConfArray['fill_color']) 
	      && $theConfArray['fill_color']['r'] != ''
			  && $theConfArray['fill_color']['g'] != ''
			  && $theConfArray['fill_color']['b'] != '') {
	    $thePDF->setColor($theConfArray['fill_color']['r'], $theConfArray['fill_color']['g'], $theConfArray['fill_color']['b']);
			$legend_image_fill = $theConfArray['fill_color']['r'] . "," . $theConfArray['fill_color']['g'] . "," . $theConfArray['fill_color']['b'];
			// color to legend file
			$bg_color = imagecolorallocate($leg_img, round($theConfArray['fill_color']['r'] * 255), round($theConfArray['fill_color']['g'] * 255), round($theConfArray['fill_color']['b'] * 255));
	  } else {
	    $thePDF->setColor(0, 0, 0);
			// color to legend file
			$bg_color = imagecolorallocate($leg_img, 0, 0, 0);
	  }
	} else {
	  $doFill = 0;
		// color to legend file
	  $bg_color = imagecolorallocate($leg_img, -1, -1, -1);
	}
	
	// Do we need to stroke (outline)?
	if($theConfArray['do_stroke'] != '') {
	  // which color to use for filling?
	  if (is_array($theConfArray['stroke_color']) 
	      && $theConfArray['stroke_color']['r'] != ''
			  && $theConfArray['stroke_color']['g'] != ''
			  && $theConfArray['stroke_color']['b'] != '') {
	    $thePDF->setStrokeColor($theConfArray['stroke_color']['r'], $theConfArray['stroke_color']['g'], $theConfArray['stroke_color']['b']);
			$thePDF->setLineStyle($theConfArray['line_style']['width'], $theConfArray['line_style']['cap'], $theConfArray['line_style']['join'], $theConfArray['line_style']['dash']);
			$theStrokePointPairs = makeStrokePointPairs($theFullArr);
			for($i = 0; $i < count($theStrokePointPairs); $i++) {
			  $line = $theStrokePointPairs[$i];
				if ($i != count($theStrokePointPairs) - 1
				    || $isClosed) {
			    $thePDF->line($line[0], $line[1], $line[2], $line[3]);
					
					$stroke_color_legend_image = imagecolorallocate($leg_img, round($theConfArray['stroke_color']['r'] * 255), round($theConfArray['stroke_color']['g'] * 255), round($theConfArray['stroke_color']['b'] * 255));
					if (is_array($theConfArray['line_style']['dash'])
					    && $theConfArray['line_style']['dash'][1] != ""
							&& $theConfArray['line_style']['dash'][1] != 0) {
						imagedashedline($leg_img, 0, 0, $legend_width-1, 0, $stroke_color_legend_image);
						imagedashedline($leg_img, $legend_width-1, 1, $legend_width-1, $legend_width-1, $stroke_color_legend_image);
						imagedashedline($leg_img, 0, 0, 0, $legend_width-1, $stroke_color_legend_image);
						imagedashedline($leg_img, 0, $legend_width-1, $legend_width-1,$legend_width-1, $stroke_color_legend_image);
					} else {
					  imageline($leg_img, 0, 0, $legend_width-1, 0, $stroke_color_legend_image);
						imageline($leg_img, $legend_width-1, 0, $legend_width-1, $legend_width-1, $stroke_color_legend_image);
						imageline($leg_img, $legend_width-1, $legend_width-1, 0, $legend_width-1, $stroke_color_legend_image);
						imageline($leg_img, 0, $legend_width-1, 0, 0, $stroke_color_legend_image);
					}		
				} 
			}
	  } 
	}
	
  $thePDF->polygon($thePolyArr, $nr_of_points, $doFill);
  // eventually create the file:
	imagepng($leg_img, $legendFilenameUserPolygon);
	
	$thePDF->restoreState();
} // end of function addMeasuredItem

/**
 * Konvertiert einen Text in ein array aus einzelnen Zeilen. Parameter wie Zeichen pro
 * zeile etc. k�nnen in der Funktion ge�ndert werden.
 */
function convert2lines($the_text) {
    $words = explode(' ', $the_text);
	$maxlines                 = 6;
	$available_chars_per_line = 35;
	$total_number_of_lines    = 0;
	$chars_current_line       = 0;
	$the_return_array         = array();

    foreach($words as $word) {
    	// trimme wort auf maximal erlaubte zeichenzahl
        $word_fit = mb_substr($word, 0, $available_chars_per_line);
	    $chars = preg_split('//', $word_fit, -1, PREG_SPLIT_NO_EMPTY);
	    
	    if(count($chars) + $chars_current_line + 1 < $available_chars_per_line && $total_number_of_lines < $maxlines) {
		    $chars_current_line += count($chars) + 1;
			$the_return_array[$total_number_of_lines]  .= " " . $word_fit;
		} elseif($total_number_of_lines < $maxlines) {
	      $chars_current_line = count($chars) + 1;
		  $the_return_array[$total_number_of_lines + 1] = " " . $word_fit;
		  $total_number_of_lines++;
		}
	}
  return $the_return_array;
} // end of function convert2lines

/**
 * Adds two white polygons to hide any user elements outside the mapframe:
 * First all A-Areas are filled, then all B-Areas. This is a rather grumpy
 * way out of having user edited stuff flying outside the mapframe.
 * 
 * @example <pre>
 * BBBBBBBBBBBBBBBBBBBBBBBBBBBBAAA
 * BBBBBBBBBBBBBBBBBBBBBBBBBBBBAAA
 * BBB                                                  AAA
 * BBB                                                  AAA
 * BBB       Mapframe                              AAA
 * BBB                                                  AAA
 * BBB                                                  AAA
 * BBB                                                  AAA
 * AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
 * AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
 * </pre>
 * 
 * @param reference (!, pass over with '&') to the pdf-file
 * @author M. Jansen <jansen@terrestris.de>
 */
function hideElementsOutsideMapframe($pdf) {
  // get globals
  global $mapOffset_left, $map_width, $mapOffset_bottom, $map_height;
  $pdf->saveState();
  $pdf->setColor(1, 1, 1);
	// The AAA-Part
  $allOutside = array(
    0                              , 0,                       // 1st coord-pair
    $pdf->ez['pageWidth']          , 0,                       // 2nd coord-pair
    $pdf->ez['pageWidth']          , $pdf->ez['pageHeight'],  // ...
    $mapOffset_left + $map_width   , $pdf->ez['pageHeight'],
    $mapOffset_left + $map_width   , $mapOffset_bottom,
    0                              , $mapOffset_bottom,
    0                              , 0
  );
	// draw polygon
  $pdf->polygon($allOutside, 7, 1);    
  // The BBB-part
  $allOutside = array(
    0                              , $mapOffset_bottom,       // 1st coord-pair
    $mapOffset_left                , $mapOffset_bottom,       // 2nd coord-pair
    $mapOffset_left                , $mapOffset_bottom + $map_height, // ...
    $mapOffset_left + $map_width   , $mapOffset_bottom + $map_height,
    $mapOffset_left + $map_width   , $pdf->ez['pageHeight'],
    0                              , $pdf->ez['pageHeight'],
    0                              , $mapOffset_bottom,  
  );
	// draw polygon
  $pdf->polygon($allOutside, 7, 1);
  $pdf->restoreState();
}
?>