<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta name="author-mail" content="info@ccgis.de">
<meta name="author" content="U. Rothstein">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="DC.Rights" content="CCGIS GbR, Bonn">
<title>Suche</title>
<?
	include_once(dirname(__FILE__) . "/../include/dyn_css.php");
?>
<style type="text/css">
<!--
	body{
		font-family : Arial, Helvetica, sans-serif;
		font-size : 12px;
		font-weight : bold;
		color: #808080;
		background-color: 'ffffff';
	}
	.header{
		color: #cc3366;
	}
	a:link{
		font-family : Arial, Helvetica, sans-serif;
		text-decoration : none;
		color: #808080;
		font-size : 12px;
		font-weight : bold;
	}
	a:visited{
		font-family : Arial, Helvetica, sans-serif;
		text-decoration : none;
		color: #808080;
		font-size : 12px;
		font-weight : bold;
	}
	a:hover{
		font-family : Arial, Helvetica, sans-serif;
		color: white;
		text-decoration : none;
		font-weight : bold;
		background-color : #999999;
	}
	a:active{
		font-family : Arial, Helvetica, sans-serif;
		color: blue;
		text-decoration : none;
		font-weight : bold;
	}
	.textfield{
		border : 2 solid #D3D3D3;
		font-family : Arial, Helvetica, sans-serif;
		font-size : 12px;
		font-weight : bold;
		color: #808080;
		width: 100px;
		position: absolute;
		left: 50px
	}
	.sbutton{
	font-size : 10px;
		width: 28px;
		height: 22px;
		position: absolute;
		left: 152px;
	}
	.resultFrame{
		width: 180px;
		height: 140px;
		border: 1px;
		position: absolute;
		top: 25px;
		left: 5px;
		overflow-x : hidden;
	}
-->
</style>
<?php
#if(isset($lingo)){$lingo = $_REQUEST["lingo"];}
#else{$lingo = "deutsch";}
#$language = parse_ini_file("../language/".$lingo.".txt");

echo "<script type='text/javascript'>";  

$queryString = $_REQUEST["search"];
if (!preg_match("/^[a-zA-Z0-9_- \*]+$/", $search)) {

	$errorMessage = _mb("Invalid search term");
	echo htmlentities($errorMessage, ENT_QUOTES, CHARSET);
	$e = new mb_exception($errorMessage);
	$queryString = "";
}

$backlink = $_REQUEST["backlink"];

if ($backlink !== "parent") {
	$backlink = false;
}
echo "var backlink = '".$backlink."';";

$lingo = $_REQUEST["lingo"];
if (!preg_match("/^[a-zA-Z]+$/", $lingo)) {

	$errorMessage = _mb("Invalid language") . ": " . $lingo;
	echo htmlentities($errorMessage, ENT_QUOTES, CHARSET);
	$e = new mb_exception($errorMessage);
	die;
}
echo "var lingo = '".$lingo."';";


$title = "layername_".$lingo;

$confFile = basename($_REQUEST["conf_file"]);
if (!preg_match("/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9]+)$/", $confFile) || 
	!file_exists($confFile)) {

	$errorMessage = _mb("Invalid configuration file") . ": " . $confFile;
	echo htmlentities($errorMessage, ENT_QUOTES, CHARSET);
	$e = new mb_exception($errorMessage);
	die;
}
echo "var conffile = '".$confFile."';";

require_once(dirname(__FILE__) . "/../../conf/".$confFile);

echo "</script>"; 
?>
<script type="text/javascript">
<!--

function validate(){

   if(document.form1.search.value.length < 1){
      alert("Bitte vervollständigen Sie die Angaben!");
      document.form1.search.focus();
      return false;
   }
   else{   
      text = "mod_poi.php?&search=" + document.form1.search.value+"&conf_file="+conffile+"&lingo="+lingo+"&backlink="+backlink;
      if (backlink=='parent'){
      	window.frames["result"].location.href = text;
      }else{
      	parent.result.window.location.href = text;
      }
      return false;
   }
}
function statistic(value){
	if (backlink =='parent'){
   		parent.parent.StatisticFrame.location.href = "../statistic.php?request=" + escape(value);
   	}
   	else{
   		parent.StatisticFrame.location.href = "../statistic.php?request=" + escape(value);
    }
   return;
}


function showHighlight(x,y){

	if (backlink =='parent'){
		parent.parent.mb_showHighlight("mapframe1",x,y);
		parent.parent.mb_showHighlight("overview",x,y);
		//alert (backlink);
	}else{
		parent.mb_showHighlight("mapframe1",x,y);
		parent.mb_showHighlight("overview",x,y);
	}
}
function hideHighlight(){
	if (backlink =='parent'){
		parent.parent.mb_hideHighlight("mapframe1");
		parent.parent.mb_hideHighlight("overview");
	}else{
		parent.mb_hideHighlight("mapframe1");
		parent.mb_hideHighlight("overview");
	}
}

function handleLayer(sel_lay, wms_title){
    
	//var wms_title = document.forms[0].wmsTitle.value

	var x = new Array();

    x[0] = sel_lay;

    var y = new Array();
    
    if (backlink =='parent'){
		var wms_ID = parent.parent.getWMSIDByTitle('mapframe1',wms_title);
	}
	else{
		var wms_ID = parent.getWMSIDByTitle('mapframe1',wms_title);
	}

    y[0] = wms_ID;
    
	//alert(wms_title + " -- X "+ x + "wms_id" + wms_ID);
	
	if (backlink =='parent'){
		parent.parent.handleSelectedLayer_array('mapframe1',y,x,'querylayer',1);
		parent.parent.handleSelectedLayer_array('mapframe1',y,x,'visible',1);
		parent.parent.mb_execloadWmsSubFunctions();
	}
	else{
		parent.handleSelectedLayer_array('mapframe1',y,x,'querylayer',1);
		parent.handleSelectedLayer_array('mapframe1',y,x,'visible',1);		
		parent.mb_execloadWmsSubFunctions();
	}
}
// -->
</script>
</head>
<body leftmargin="2" topmargin="0" bgcolor="#ffffff">
<?php

if(!isset($queryString) || $queryString == ""){
	echo "<form name='form1' target='result' onsubmit='return validate();'>";
	echo "Suchen: &nbsp;&nbsp;<input class='textfield' name='search' type='text'> ";
	echo "<input class='sbutton' type='submit' name='send'  value='ok'>";
	echo "<iframe frameborder='1' name='result' src='../html/mod_blank.html' class='resultFrame' scrolling='auto'></iframe>";
	echo "</form>";
}
else{
	if(preg_match("/\*/",$queryString)){
		$search = trim(preg_replace("/\*/i","", $queryString));
	}

	$con = pg_connect ($con_string) or die ("Error while connecting database $dbname");
	/*
	 * @security_patch sqli done
	 */
	#$sql = "SELECT DISTINCT identificationinfo,minscale, md_fileidentifier ,search_columns, search_result  FROM tab_metadata WHERE public = '1' and not identificationinfo = 'Rasterebene' and not identificationinfo = 'rasterlayer'";
	$sql = "SELECT DISTINCT identificationinfo,minscale, md_fileidentifier ,".pg_escape_string($title).",search_columns, search_result,search_keywords, wms_title  FROM tab_metadata WHERE public = '1' and not identificationinfo = 'Rasterebene' and not identificationinfo = 'rasterlayer'";
	$res = pg_query($con,$sql);
	$cnt = 0;

	while(pg_fetch_row($res)){
		$table[$cnt] = pg_result($res,$cnt,"identificationinfo"); # Tabellen, Abfragenname
		$minscale[$cnt] = pg_result($res,$cnt,"minscale");	
		$md_fileidentifier[$cnt] = pg_result($res,$cnt,"md_fileidentifier"); # Layername
		$layername[$cnt] = pg_result($res,$cnt,"md_fileidentifier"); # Layername in der Mapdatei
		$result_title[$cnt] = pg_result($res,$cnt,"\"".$title."\""); # layer_deutsch Ergebnisname
		$search_columns[$cnt] = pg_result($res,$cnt,"search_columns"); # Suchspalten, Trennung über ,
		$search_result[$cnt] = pg_result($res,$cnt,"search_result"); # Ergebnisspalte
		$search_keywords[$cnt] = pg_result($res,$cnt,"search_keywords"); # Ergebnisspalte
		$wms_title[$cnt] = pg_result($res,$cnt,"wms_title"); # WMS tile
           
		# if one of the searchkeywords is found the data of the whole table is displayed as the result
		if($search_keywords[$cnt] != '') { 
			$array_search_keywords = explode(",", $search_keywords[$cnt]);
			$all[$cnt] = false;
			for ($p=0 ; $p<count($array_search_keywords);$p++){
				$hit = preg_match("/".$queryString."/i",$array_search_keywords[$p]);
				if ($hit >0){	
					$all[$cnt] = true;
				}
			}
		}  	
		//echo "hit:".$hit."all: ".$all[$cnt] ;
		$cnt++;
	}
	$field_has_parent = false; 
	$has_result = false; 

	for($i=0; $i<count($table); $i++){
		/*
		 * @security_patch sqli done
		 */
		$sql = "Select GeometryType(the_geom) as type FROM ".pg_escape_string($table[$i])." LIMIT 1";
		$res = pg_query($con,$sql);
		$type = pg_result($res,0,"type");

		$sql = "Select * FROM ".pg_escape_string($table[$i])." LIMIT 1";
		$res = pg_query($con,$sql);

		if(mb_strtoupper($type) =='MULTIPOLYGON'){
			$sql1 = "SELECT '". $layername[$i]."' as fkey_md_fileidentifier,".$search_result[$i].", '".$wms_title[$i]."' as wms_title, X(Centroid(the_geom)) as x,Y(Centroid(the_geom)) as y  FROM ".$table[$i];
		}
		if(mb_strtoupper($type) =='MULTILINESTRING'){
			$sql1 = "SELECT '". $layername[$i]."' as fkey_md_fileidentifier,".$search_result[$i].",'".$wms_title[$i]."' as wms_title, X(Centroid(the_geom)) as x,Y(Centroid(the_geom)) as y  FROM ".$table[$i];
		}
		if(mb_strtoupper($type)=='POINT'){
			$sql1 = "SELECT '". $layername[$i]."' as fkey_md_fileidentifier,".$search_result[$i].",'".$wms_title[$i]."' as wms_title, X(the_geom) as x,Y(the_geom) as y FROM ".$table[$i];
		}
      
		#---------------- search_columns search_result 
		if ($all[$i] == false){
			$array_search_columns = explode(",", $search_columns[$i]);

			if (count($array_search_columns)>0){ 
				$array_search_columns[count($array_search_columns)] =  $array_search_columns [0];
				$array_search_columns [0] = "platzhalterxy";

				for($j=0; $j<pg_num_fields($res); $j++){
					if(array_search(pg_field_name($res,$j),$array_search_columns) == true  ){
						if($field_has_parent == true){
							$sql1 .= " OR ";
						}
						else {
							$sql1 .= " WHERE ";
						};
						$field_has_parent = true;
						$sql1 .= pg_field_name($res,$j) ." ILIKE ";
						$sql1 .= "'%".$queryString."%'";
					}
				}
				$field_has_parent = false;
			};
		}
		else {
		}
		$sql1 .= " ORDER BY ".$search_result[$i];
		$res1 = pg_query($con,$sql1);
		$cnt = 0;
		if(pg_fetch_row($res1)>0){
			$sel_lay = pg_result($res1,$cnt,"fkey_md_fileidentifier"); 
      
			if($minscale[$i] > 0){$scale = $minscale[$i]+100; }

			for ($cnt=0; $cnt < pg_num_rows($res1); $cnt++){
				if($cnt == 0){
					$title = "layername_".$lingo;
					echo "<div class='header'>".$result_title[$i]. "</div>";
				}
				if($backlink=='parent'){
					echo "<nobr><a href='javascript:hideHighlight();parent.parent.mb_repaintScale(\"mapframe1\"," .pg_result($res1,$cnt,"x"). ",".pg_result($res1,$cnt,"y"). ",$scale);'";
				}
				else{
					echo "<nobr><a href='javascript:hideHighlight();parent.mb_repaintScale(\"mapframe1\"," .pg_result($res1,$cnt,"x"). ",".pg_result($res1,$cnt,"y"). ",$scale);'";
				}

				echo " onmouseover='showHighlight(" .pg_result($res1,$cnt,"x"). "," .pg_result($res1,$cnt,"y"). ")' ";
				echo "onmouseout='hideHighlight();' ";
				echo "onclick='handleLayer(\"" .pg_result($res1,$cnt,"fkey_md_fileidentifier"). "\",\"".pg_result($res1,$cnt,"wms_title")."\")'>";

				echo pg_result($res1,$cnt,$search_result[$i])."</a></nobr><br>";
				$has_result = true;
			}
		}
	}
	if($has_result == false){echo "Kein Ergebnis!";}
	echo "<form action='" . $_SERVER["SCRIPT_NAME"] . "?".SID."' method='post'>";
	echo "</form>";
}
?>
</body>
</html>