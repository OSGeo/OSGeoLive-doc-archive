<?php
# $Id: sld_main.php 2412 2008-04-23 16:14:30Z christoph $
# http://www.mapbender.org/index.php/SLD
# Copyright (C) 2002 CCGIS 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

/**
 * This is the main page of the sld-editor.
 * It displays the preview images and creates the sld_edit_form
 * used for editing the sld.
 *
 * @package sld_main
 * @author Markus Krzyzanowski
 */

require_once(dirname(__FILE__)."/sld_config.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_conf.php");
$classWfsConf = new wfs_conf();

//read custom sld for this user&wms&layer&gui from the db instead using sld.xml

$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db($DB,$con);

//Read the layer_id from the DB
$dbutils = new DbUtils();
$layer_id = $dbutils->getLayerIdFromLayerName($_SESSION["sld_wms_id"], $_SESSION["sld_layer_name"]);
if ( $layer_id )
{
	$_SESSION["sld_layer_id"] = $layer_id;
}

//Read the mb_user_id from the Session
$mb_user_id = $_SESSION["mb_user_id"];


//if layer is not found in DB
if (!$layer_id)
{
	echo "layer existiert nicht in Datenbank";
	exit();
}
else
{
	//Try to read sld from the DB
	$sql = "SELECT * FROM sld_user_layer WHERE fkey_gui_id = $1 AND fkey_layer_id = $2 AND fkey_mb_user_id = $3";
	$v = array($_SESSION["sld_gui_id"], $layer_id, $mb_user_id);
	$t = array('s', 'i', 'i');
	$res = db_prep_query($sql,$v,$t);
	if (!$res || db_numrows($res)== 0)
	{
		//No user specific sld found in DB -> get it from the mapserver
		$file = $mapfileUrl."SERVICE=WMS&VERSION=1.1.1&REQUEST=GetStyles&LAYERS=".urlencode($layer_name);
		$data = readSld($file);
		$data = char_encode($data);
		//write the sld to the DB
		$sql = "INSERT INTO sld_user_layer(fkey_mb_user_id, fkey_layer_id, sld_xml, use_sld, fkey_gui_id) VALUES ($1, $2, $3, 0, $4);";
		$v = array($mb_user_id, $layer_id, $data, $_SESSION["sld_gui_id"]);
		$t = array('i', 'i', 's', 's');
		$res = @db_prep_query($sql,$v,$t);
		//Use the new sld
	}
	else
	{
		$data = db_result($res, 0, "sld_xml");
		$use_sld = db_result($res, 0, "use_sld");
	}
}

//$file = "sld.xml";
//$data = readSld($file);

$styledlayerdescriptor = parseSld($data);
$_SESSION["sld"] = $data;

/* Check for a related WFS featuretype */
$wfs_conf_id = $dbutils->getLayerWfsConfId($_SESSION["sld_gui_id"], $layer_id);
if ( $wfs_conf_id ) {
	$featuretype_id = $dbutils->getWfsConfFeatureTypeId($wfs_conf_id);
	$classWfsConf->getelements($featuretype_id);
	$wfs_element = $classWfsConf->elements;
	$fts = $styledlayerdescriptor->layers[0]->styles[0]->featuretypestyles[0];
	$fts->setElementArray("element_id",$wfs_element->element_id);
	$fts->setElementArray("element_name",$wfs_element->element_name);
} else {
	$wfs_element = false;
}

/* create getMap-Url for preview with/without sld */
$previewMapUrl = $dbutils->getPreviewMapUrl($_SESSION["sld_gui_id"], $layer_id, $_SESSION["sld_wms_id"]);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
echo "<meta HTTP-EQUIV=\"CACHE-CONTROL\" CONTENT=\"NO-CACHE\">\n";
echo "<META HTTP-EQUIV=\"PRAGMA\" CONTENT=\"NO-CACHE\">\n";
echo "<title>Mapbender - SLD Editor</title>\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../css/sldEditor.css\">\n";
echo "</head>\n";
echo "<body leftmargin=\"10\" topmargin=\"0\">\n";

## - Fensteranfang
echo "<table width=\"800px\"  border='0' cellspacing='0'>\n"; 	      
## - 1.Zeile
echo " <tr>\n";
echo "  <td class='text2 bg2' colspan='2'>\n";
echo "   &nbsp;&nbsp;Vorschau mit SLD:\n";
echo "  </td>\n";
echo "  <td>\n";
echo "   &nbsp;\n";
echo "  </td>\n";
echo "  <td class='text2 bg2' colspan='2'>\n";
echo "   &nbsp;&nbsp;Original ohne SLD:\n";
echo "  </td>\n";
echo " </tr>\n";

## - Build URL to SLD
$sld_url = "";
$mb_sld_url = "";
//$sld_url = "http://".$_SERVER["HTTP_HOST"]."/mapbender/sld/sld_function_handler.php?function=getusersld&layer_id=".$layer_id."&user_id=".$mb_user_id."&forcesld=1";
$sld_url = $MAPBENDER_URL."/sld_function_getusersld.php?function=getusersld&sld_gui_id=".$_SESSION["sld_gui_id"]."&sld_layer_id=".$layer_id."&user_id=".$mb_user_id."&forcesld=1";
$mb_sld_url = $MAPBENDER_URL."/sld_function_getusersld.php?function=getusersld&sld_gui_id=".$_SESSION["sld_gui_id"]."&sld_wms_id=".$_SESSION["sld_wms_id"]."&user_id=".$mb_user_id;
echo "<!-- $sld_url -->";
echo "<!-- $previewMapUrl -->";
## - 2.Zeile
/*
echo " <tr align='right'>\n";
echo "  <td class='line_left2 text4'>\n";
echo "   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Karte";
echo "  </td>\n";
echo "  <td class='line_right2'>\n";
echo "   <img src='./img/map_magnify.png' border='0' alt='Originalgr&ouml;&szlig;e im neuen Fenster anzeigen' onClick=\"window.open('map.php','','fullsreen=yes,resizable=no,scrollbars=yes');\">\n";
echo "  </td>\n";
echo "  <td>\n";
echo "   &nbsp;\n";
echo "  </td>\n";
echo "  <td class='line_left2 text4'>\n";
echo "   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Karte";
echo "  </td>\n";
echo "  <td class='line_right2'>\n";
echo "   <img src='./img/map_magnify.png' border='0' alt='Originalgr&ouml;&szlig;e im neuen Fenster anzeigen' onClick=\"window.open('map_original.php','','fullsreen=yes,resizable=no,scrollbars=yes');\">\n";
echo "  </td>\n";
echo " </tr>\n";
*/

## - 3.Zeile
echo " <tr align='center'>\n";
echo "  <td class='line_left2 line_right2' colspan='2'>\n";

## - Map with SLD
//Added rand(...) to force a reload of the image because the url should be different
echo "   <img src=\"".$previewMapUrl."&WIDTH=320&HEIGHT=240&sld=".urlencode($sld_url)."&".rand(0,10000)."\" border=\"0\" width=\"320\" height=\"240\">";
echo "  </td>\n";

echo "  <td>\n";
echo "   &nbsp;\n";
echo "  </td>\n";

echo "  <td class='line_left2 line_right2' colspan='2'>\n";

## - Map without SLD
echo "   <img src=\"".$previewMapUrl."&WIDTH=320&HEIGHT=240\" border=\"0\" width=\"320\" height=\"240\">";
echo "  </td>\n";

#4.Zeile
echo " <tr>\n";
echo "  <td class='line_left2 line_right2 text4' colspan='2'>\n";
echo "   &nbsp;&nbsp;&nbsp;&nbsp;Legende\n";
echo "  </td>\n";
echo "  <td>\n";
echo "   &nbsp;\n";
echo "  </td>\n";
echo "  <td class='line_left2 line_right2 text4' colspan='2'>\n";
echo "   &nbsp;&nbsp;&nbsp;&nbsp;Legende\n";
echo "  </td>\n";
echo " </tr>\n";

#5.Zeile
echo " <tr>\n";
echo "  <td class='line_left2 line_down2 line_right2' colspan='2'>\n";
echo "   &nbsp;\n";
## - Legend with SLD
echo "   <img src=\"".$mapfileUrl."VERSION=1.1.0&REQUEST=GetLegendGraphic&SERVICE=WMS&LAYER=".urlencode($layer_name)."&FORMAT=image/png&sld=".urlencode($sld_url)."&".rand(0,10000)."\">\n";
echo "  </td>\n";
echo "   &nbsp;\n";
echo "  <td>\n";
echo "   &nbsp;\n";
echo "  </td>\n";
echo "  <td class='line_left2 line_down2 line_right2' colspan='2'>\n";
echo "   &nbsp;\n";
## - Legend without SLD
echo "   <img src=\"".$mapfileUrl."VERSION=1.1.0&REQUEST=GetLegendGraphic&SERVICE=WMS&LAYER=".urlencode($layer_name)."&FORMAT=image/png\">";
echo "  </td>\n";
echo " </tr>\n";
echo "</table>\n";

echo "<br>\n";

echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo " <tr valign=\"top\">\n";
echo "  <td>\n";

echo "   <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "    <tr align=\"center\">\n";
echo "     <td class=\"bg2 text3\">MENU</td>\n";
echo "    </tr>\n";
echo "    <tr>\n";
echo "     <td class=\"line_left2 line_down2 line_right2 text4 bg_menu\">\n";

//echo "      <form name=\"use_sld\" action=\"http://".$_SERVER["HTTP_HOST"]."/mapbender/sld/".$SLD_FUNCTION_HANDLER."\" method=post >\n";
echo "      <form name=\"use_sld\" action=\"".$MAPBENDER_URL."/".$SLD_FUNCTION_HANDLER."\" method=post >\n";
echo "      <input type=\"hidden\" name=\"function\" value=\"usesld\">\n";
echo "      <input type=\"hidden\" name=\"sld_url\" value=\"".$sld_url."\">\n";
echo "      <input type=\"hidden\" name=\"mb_sld_url\" value=\"".$mb_sld_url."\">\n";
echo "      &nbsp;&nbsp;Ansicht in Mapbender&nbsp;&nbsp;<br>\n";
	
echo "      <input type=\"radio\" name=\"use_sld\" value=\"0\"";
if ($use_sld == 0) echo " checked";
echo " onClick=\"submit()\">\n";
echo "      &nbsp;Original ohne SLD&nbsp;&nbsp;<br>";

echo "      <input type=\"radio\" name=\"use_sld\" value=\"1\"";
if ($use_sld == 1) echo " checked";
echo " onClick=\"submit()\">\n";
echo "      &nbsp;mit SLD&nbsp;&nbsp;<br>";
echo "      </form>\n";
echo "     </td>\n";
echo "    </tr>\n";
echo "    <tr align=\"left\">\n";
echo "     <td class=\"line_left2 line_down2 line_right2 text1 bg_menu\" style=\"padding:3px;\">\n";

//echo "      <form id=\"sld_editor_form\" action=\"http://".$_SERVER["HTTP_HOST"]."/mapbender/sld/".$SLD_FUNCTION_HANDLER."\" method=post >\n";
echo "      <form id=\"sld_editor_form\" action=\"".$MAPBENDER_URL."/".$SLD_FUNCTION_HANDLER."\" method=post >\n";

echo "      <a href='".$SLD_FUNCTION_HANDLER."&function=getdefaultsld' onclick='if(!confirm(\"Aktuelle SLD-Definition ersetzen?\")) return false;'>\n";
echo "      <img src='./img/script.png' border='0' alt='Standard SLD aus WMS auslesen'>\n";
echo "      </a> Standard &ouml;ffnen<br /><br />\n";

echo "      <input type='image' src='./img/script_save.png' border='0' alt='Änderungen an die Map senden'>\n";
echo "      SLD speichern<br>\n";
echo "      <input type='hidden' name='function' value='save'>\n";
echo "      <br /><a href=\"".$sld_url."\" target=_new>";	
echo "      <img src='./img/script_code_red.png' border='0' alt='SLD anzeigen'>\n";
echo "      </a>";
echo "      SLD anzeigen";

echo "     </td>\n";
echo "    </tr>\n";
echo "   </table>\n";

echo "  </td>\n";
echo "  <td>\n";
echo "   &nbsp;&nbsp;&nbsp;";
echo "  </td>\n";
echo "  <td>\n";

##  -  Fenster Eigenschaft
echo "   <table border='0' cellspacing='0' cellpadding='0'>\n";
echo "    <tr align='center'>\n";
echo "     <td class='bg2 text3'>SLD Eigenschaften</td>\n";
echo "    </tr>\n";
echo "    <tr>\n";
echo "     <td class='line_left2 line_down2 line_right2'>\n";

echo $styledlayerdescriptor->generateHtmlForm("","      ");

echo "     </td>\n";
echo "    </tr>\n";
echo "   </table>\n";
echo "   </form>\n";

echo "  </td>\n";
echo " </tr>\n";
echo "</table>\n";
echo "<!-- ";
print_r ($wfs_element);
print_r ($_SESSION["sld_objects"][3]);
echo "-->";
echo "</body>\n";
echo "</html>\n";
##Debug
//echo "------------------------------------------------------------------\n";
//echo $styledlayerdescriptor->generateXml();
?>