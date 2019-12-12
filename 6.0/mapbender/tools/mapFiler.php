<html>
  <head>
    <title>MapFiler Version 0.2 for UMN MapServer 5.x</title>
    <style></style>
  <script type='text/javascript' >
  function validate(){
  	/*eventuell den Inhalt (Pfad) noch aufbereiten?
	* Slash und Backslash -> win, linux usw.
	*/
	document.form1.location.type = 'text';
	document.form1.myLocation.value = document.form1.location.value;
	document.form1.submit();
  }
  </script>
  </head>
  <body>
  <form name='form1' action='<?php $PHP_SELF ?>' method='POST'>
	<b>MapFiler Version 0.3 for UMN MapServer 5.4.x </b>
	<br><br>changes in Version 0.3
	<li>multiplicate SIZE MAXSIZE MINSIZE with factor</li>
	<li>division SYMBOLSCALEDENOM by factor</li>
	<li>multiplicate WIDTH MINWIDTH MAXWIDTH with factor</li>
	<li>division MINSCALEDENOM and MAXSCALEDENOM by factor  </li>
	<li>division LABELMINSCALEDENOM and LABELMAXSCALEDENOM by factor  </li>	
	<br>
  
  <table>
	<tr>
		<td>Location:</td>
		<td>
			<input type='file' name='location' >
			<input type='hidden' name='myLocation'>
		</td>
	</tr>
	<tr>
		<td>all files with the same extension:</td>
		<td>
		<input type='checkbox' name='dir'>
		</td>
	</tr>
	<tr>
		<td>Factor:</td>
		<td><input type='text' name='factor' value='4'></td>
	</tr>		
	<tr>
		<td><input type='button' value="let's go" onclick='validate()'></td>
		<td></td>
	</tr>
  </table>
  <?php
  
import_request_variables('PG');

/*********PARAMS***************/
$countSymbolsFiles = 0;
$arraySymbolsFiles;
/********************************/
function modify($myDir, $myFile){
	global $factor;
	$myContent = fopen($myDir."/".$myFile, "r") or die("Datei " . $myDir . " / ". $myFile ." konnte nicht zum Lesen ge�ffnet werden!");
	$myNewContent = fopen($myDir."/".str_replace(".","_".$factor.".",$myFile), "w") or die("Datei " . $myDir . " / ". str_replace(".","_".$factor.".",$myFile) ." konnte nicht zum Schreiben ge�ffnet werden!");
	fputs($myNewContent, "#Modified by MapbenderTools" . "Date: ". date("d.m.Y") . "Factor: ". $factor . "\n\n");
	//fputs($myNewContent, "#Date: ". date("d.m.Y") . "Factor: ". $factor . "\n\n");
	//fputs($myNewContent, "#Factor: ". $factor . "\n\n");
                
	while(!feof($myContent)){
		$myLine = fgets($myContent, 1024);
		if(preg_match("/\bSIZE\s*(\d+.?\d*)\s*/i",$myLine,$matches)){
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], multiplicate($matches[1]), $myLine));
			}
		}
		else if(preg_match("/(.*OFFSET)\s*(\d+.?\d*)(\s*)(\d+.?\d*)(\s*)/i",$myLine,$matches)){
			$myNewOFFSET=$matches[1] . " " .multiplicate($matches[2]) . " " . multiplicate($matches[4]).$matches[5];
			fputs($myNewContent,$myNewOFFSET);
		}
		else if(preg_match("/\bMAXSIZE\s*(\d+.?\d*)\s*/i",$myLine,$matches)){
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], multiplicate($matches[1]), $myLine));
			}
		} 
		else if(preg_match("/\bMINSIZE\s*(\d+.?\d*)\s*/i",$myLine,$matches)){
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], multiplicate($matches[1]), $myLine));
			}
		}
		else if(preg_match("/\bMAXWIDTH\s*(\d+.?\d*)\s*/i",$myLine,$matches)){
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], multiplicate($matches[1]), $myLine));
			}
		} 
		else if(preg_match("/\bMINWIDTH\s*(\d+.?\d*)\s*/i",$myLine,$matches)){
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], multiplicate($matches[1]), $myLine));
			}
		}		
		else if(preg_match("/\bWIDTH\s*(\d+.?\d*)\s*/i",$myLine,$matches)){ 
			//fputs($myNewContent, "# ". $myLine);
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], multiplicate($matches[1]), $myLine));
			}
		}
		else if(preg_match("/\bSYMBOLSCALEDENOM\s*(\d+.?\d*)\s*/i",$myLine,$matches)){ 
			//fputs($myNewContent, "# ". $myLine);
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], division($matches[1]), $myLine));
			}
		}
		else if(preg_match("/\bMINSCALEDENOM\s*(\d+.?\d*)\s*/i",$myLine,$matches)){ 
			//fputs($myNewContent, "# ". $myLine);
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], division($matches[1]), $myLine));
			}
		}
		else if(preg_match("/\bLABELMAXSCALEDENOM\s*(\d+.?\d*)\s*/i",$myLine,$matches)){ 
			//fputs($myNewContent, "# ". $myLine);
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], division($matches[1]), $myLine));
			}
		}				
		else if(preg_match("/\bLABELMINSCALEDENOM\s*(\d+.?\d*)\s*/i",$myLine,$matches)){ 
			//fputs($myNewContent, "# ". $myLine);
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], division($matches[1]), $myLine));
			}
		}
		else if(preg_match("/\bMAXSCALEDENOM\s*(\d+.?\d*)\s*/i",$myLine,$matches)){ 
			//fputs($myNewContent, "# ". $myLine);
			if(count($matches) <= 2){ 
				fputs($myNewContent, str_replace($matches[1], division($matches[1]), $myLine));
			}
		}				

		else if(preg_match("/\bSYMBOLSET\s*(.*)/i",$myLine,$matches)){ //noch nicht fertig
			$arraySymbolFiles[$countSymbolsFiles] = str_replace("\"","",$matches[1]);
			$countSymbolsFiles++;
			fputs($myNewContent, $myLine);
		}
		#catch all
		else{
			fputs($myNewContent, $myLine);
		}        
	}
	fclose($myContent);
	fclose($myNewContent);
}

function multiplicate($x){
	global $factor;
	$x = floatval($x) * $factor;
	return $x;
}

function division($x){
	global $factor;
	$x = floatval($x) / $factor;
	return $x;
}

 if(isset($myLocation) && $myLocation != ""){
	# what is to do...
	$myFile = basename($myLocation);
	$myDir = dirname($myLocation);
	$suffix = strstr($myFile, ".");

	
	if(!isset($dir)){
		modify($myDir, $myFile);
		echo "modified: " . $myDir . "/" . $myFile. "<br />";
	}
	else{
		if($verz = opendir($myDir)){
			while ($myFile = readdir ($verz)) {
				if($myFile != ".." && $myFile != "." && is_file($myDir."/".$myFile) && strstr($myFile, $suffix)){
					modify($myDir, $myFile);                         
					echo "modified: " . $myDir . "/" . $myFile. "<br />";
				}
			}
			closedir($verz);
		}
	}
} 
  ?>  
  </form>
  </body>
</html>