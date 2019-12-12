<?php
//class_lzw_decompress.php
//
/*function uniord($ch) {

    $n = ord($ch{0});

    if ($n < 128) {
        return $n; // no conversion required
    }

    if ($n < 192 || $n > 253) {
        return false; // bad first byte || out of range
    }

    $arr = array(1 => 192, // byte position => range from
                 2 => 224,
                 3 => 240,
                 4 => 248,
                 5 => 252,
                 );

    foreach ($arr as $key => $val) {
        if ($n >= $val) { // add byte to the 'char' array
            $char[] = ord($ch{$key}) - 128;
            $range  = $val;
        } else {
            break; // save some e-trees
        }
    }

    $retval = ($n - $range) * pow(64, sizeof($char));

    foreach ($char as $key => $val) {
        $pow = sizeof($char) - ($key + 1); // invert key
        $retval += $val * pow(64, $pow);   // dark magic
    }

    return $retval;
}
*/
/*function unichr($dec) {
  if ($dec < 128) {
    $utf = chr($dec);
  } else if ($dec < 2048) {
    $utf = chr(192 + (($dec - ($dec % 64)) / 64));
    $utf .= chr(128 + ($dec % 64));
  } else {
    $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
    $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
    $utf .= chr(128 + ($dec % 64));
  }
  return $utf;
*/

/*function uniord($c) {
    $h = ord($c{0});
    if ($h <= 0x7F) {
        return $h;
    } else if ($h < 0xC2) {
        return false;
    } else if ($h <= 0xDF) {
        return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
    } else if ($h <= 0xEF) {
        return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
                                 | (ord($c{2}) & 0x3F);
    } else if ($h <= 0xF4) {
        return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
                                 | (ord($c{2}) & 0x3F) << 6
                                 | (ord($c{3}) & 0x3F);
    } else {
        return false;
    }
}
*/
/**
 * Return unicode char by its code
 *
 * @param int $u
 * @return char
 */
/*function unichr($u) {
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}*/

  function unichr($u) {
    return mb_convert_encoding(pack("N",$u), mb_internal_encoding(), 'UCS-4BE');
 }

function lzw_decompress($compressed) {
	// Build the dictionary.
	$dictSize = 256;
	$dictionary = array();
	for ($i=0; $i < $dictSize; $i++)
        {
            $dictionary[$i] = unichr($i);
	    //$e = new mb_exception('dictionary['.$i.']:'.$dictionary[$i]);
 	}
	$w = (string)unichr($compressed[0]);
        $result = $w;
	for ($i=1; $i < count($compressed); $i++) {
            $entry = "";
            $k = $compressed[$i];
            if (isset($dictionary[$k])) { //whats with null?
		 
        	$entry = $dictionary[$k];
		
	    }
            else if ($k == $dictSize) { 
                $entry = $w.$w[0];
	    }
            else {
                
            }
	    
            $result = $result.$entry;
            
            $dictionary[$dictSize++] = $w.$entry[0]; //for the first time 256 after that it will be increased
	    
            
            $w = $entry;
	    
        }
	
        return $result;
}
?>
