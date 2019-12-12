<?php
# $Id: sld_pick_color.php 4243 2009-06-26 11:10:34Z uli $
# http://www.mapbender.org/index.php/SLD/
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
 * Color-picker module.
 * This file realizes a color-picker module for the sld-editor so that
 * the users do not have to work with RGB values.
 * A number of colors from a wide spectrum is available with a preview
 * of each color.
 * Choosing a color and submitting the form will also submit the sld_edit_form
 * to directly save the changes to the sld.
 *
 * @package sld_pick_color
 * @author Markus Krzyzanowski, Design by Bao Ngan
 */



$id = $_REQUEST["id"];
$color= $_REQUEST["color"];
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/sldEditor.css">
<script Language="JavaScript">
function returnToMain()
{
	window.opener.document.getElementById("<?php echo $id; ?>").value = document.getElementById('color').value;
	var preview = window.opener.document.getElementById('<?php echo $id; ?>_preview');
	preview.style.background = document.getElementById('color').value;
	window.opener.document.getElementById("sld_editor_form").submit();
	window.close();
}
function showColor(color)
{
	document.getElementById('color').value = color;
	document.getElementById('preview').style.background = color;
}
function showColor_over(color)
{
	document.getElementById('preview_over').style.background = color;
}
</script>
</head>
<body leftmargin="2" topmargin="2">
<!------ Rahmentabelle ------>
<table width="294" height="188" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td height="14" colspan="4" align="center" class="text3 bg2">Farbpalette</td>
  </tr>
  <tr>
    <td width="33" height="135" class="line_left2 ">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid black; background-color: #FFFFFF">
    	<tr>
      	 <td height="132" id="preview_over" width="100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    	</tr>
      </table>
    </td>
    <td width="4">&nbsp;</td>
    <td colspan="2" class="line_right2 ">
	  <table style="border:1px solid black;" cellspacing="1" cellpadding="1" width="87%">
	   <tr>
  	  	<td width="10" style="width: 10px; background-color: #00FF00;" onClick="showColor('#00FF00');"   onMouseOver="showColor_over('#00FF00');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #00FF33;" onClick="showColor('#00FF33');"   onMouseOver="showColor_over('#00FF33');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #00FF66;" onClick="showColor('#00FF66');"   onMouseOver="showColor_over('#00FF66');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #00FF99;" onClick="showColor('#00FF99');"   onMouseOver="showColor_over('#00FF99');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #00FFCC;" onClick="showColor('#00FFCC');"   onMouseOver="showColor_over('#00FFCC');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #00FFFF;" onClick="showColor('#00FFFF');"    onMouseOver="showColor_over('#00FFFF');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #33FF00;" onClick="showColor('#33FF00');"   onMouseOver="showColor_over('#33FF00');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #33FF33;" onClick="showColor('#33FF33');"   onMouseOver="showColor_over('#33FF33');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #33FF66;" onClick="showColor('#33FF66');"   onMouseOver="showColor_over('#33FF66');">&nbsp;</td>
  	  	<td width="10" style="width: 10px; background-color: #33FF99;" onClick="showColor('#33FF99');"   onMouseOver="showColor_over('#33FF99');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #33FFCC;" onClick="showColor('#33FFCC');"   onMouseOver="showColor_over('#33FFCC');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #33FFFF;" onClick="showColor('#33FFFF');"    onMouseOver="showColor_over('#33FFFF');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #66FF00;" onClick="showColor('#66FF00');"   onMouseOver="showColor_over('#66FF00');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #66FF33;" onClick="showColor('#66FF33');"   onMouseOver="showColor_over('#66FF33');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #66FF66;" onClick="showColor('#66FF66');"   onMouseOver="showColor_over('#66FF66');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #66FF99;" onClick="showColor('#66FF99');"   onMouseOver="showColor_over('#66FF99');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #66FFCC;" onClick="showColor('#66FFCC');"   onMouseOver="showColor_over('#66FFCC');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #66FFFF;" onClick="showColor('#66FFFF');"    onMouseOver="showColor_over('#66FFFF');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #99FF00;" onClick="showColor('#99FF00');"   onMouseOver="showColor_over('#99FF00');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #99FF33;" onClick="showColor('#99FF33');"   onMouseOver="showColor_over('#99FF33');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #99FF66;" onClick="showColor('#99FF66');"   onMouseOver="showColor_over('#99FF66');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #99FF99;" onClick="showColor('#99FF99');"   onMouseOver="showColor_over('#99FF99');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #99FFCC;" onClick="showColor('#99FFCC');"   onMouseOver="showColor_over('#99FFCC');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #99FFFF;" onClick="showColor('#99FFFF');"    onMouseOver="showColor_over('#99FFFF');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #CCFF00;" onClick="showColor('#CCFF00');"   onMouseOver="showColor_over('#CCFF00');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #CCFF33;" onClick="showColor('#CCFF33');"   onMouseOver="showColor_over('#CCFF33');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #CCFF66;" onClick="showColor('#CCFF66');"   onMouseOver="showColor_over('#CCFF66');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #CCFF99;" onClick="showColor('#CCFF99');"   onMouseOver="showColor_over('#CCFF99');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #CCFFCC;" onClick="showColor('#CCFFCC');"   onMouseOver="showColor_over('#CCFFCC');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #CCFFFF;" onClick="showColor('#CCFFFF');"    onMouseOver="showColor_over('#CCFFFF');">&nbsp;</td>
    	<td width="10" style="width: 10px; background-color: #FFFF00;" onClick="showColor('#FFFF00');"    onMouseOver="showColor_over('#FFFF00');">&nbsp;</td>
		<td width="10" style="width: 10px; background-color: #FFFF33;" onClick="showColor('#FFFF33');"    onMouseOver="showColor_over('#FFFF33');">&nbsp;</td>
		<td width="10" style="width: 10px; background-color: #FFFF66;" onClick="showColor('#FFFF66');"    onMouseOver="showColor_over('#FFFF66');">&nbsp;</td>
		<td width="10" style="width: 10px; background-color: #FFFF99;" onClick="showColor('#FFFF99');"    onMouseOver="showColor_over('#FFFF99');">&nbsp;</td>
		<td width="10" style="width: 10px; background-color: #FFFFCC;" onClick="showColor('#FFFFCC');"    onMouseOver="showColor_over('#FFFFCC');">&nbsp;</td>
		<td width="10" style="width: 10px; background-color: #FFFFFF;" onClick="showColor('#FFFFFF');"    onMouseOver="showColor_over('#FFFFFF');">&nbsp;</td>
	   </tr>
	   <tr>                                                                                         
        <td style="width: 10px; background-color: #00CC00;" onClick="showColor('#00CC00');"		onMouseOver="showColor_over('#00CC00');">&nbsp;</td>
		<td style="width: 10px; background-color: #00CC33;" onClick="showColor('#00CC33');"		onMouseOver="showColor_over('#00CC33');">&nbsp;</td>
		<td style="width: 10px; background-color: #00CC66;" onClick="showColor('#00CC66');"		onMouseOver="showColor_over('#00CC66');">&nbsp;</td>
		<td style="width: 10px; background-color: #00CC99;" onClick="showColor('#00CC99');"		onMouseOver="showColor_over('#00CC99');">&nbsp;</td>
		<td style="width: 10px; background-color: #00CCCC;" onClick="showColor('#00CCCC');"		onMouseOver="showColor_over('#00CCCC');">&nbsp;</td>
		<td style="width: 10px; background-color: #00CCFF;" onClick="showColor('#00CCFF');"		onMouseOver="showColor_over('#00CCFF');">&nbsp;</td>
		<td style="width: 10px; background-color: #33CC00;" onClick="showColor('#33CC00');"		onMouseOver="showColor_over('#33CC00');">&nbsp;</td>
		<td style="width: 10px; background-color: #33CC33;" onClick="showColor('#33CC33');"		onMouseOver="showColor_over('#33CC33');">&nbsp;</td>
		<td style="width: 10px; background-color: #33CC66;" onClick="showColor('#33CC66');"		onMouseOver="showColor_over('#33CC66');">&nbsp;</td>
		<td style="width: 10px; background-color: #33CC99;" onClick="showColor('#33CC99');"		onMouseOver="showColor_over('#33CC99');">&nbsp;</td>
		<td style="width: 10px; background-color: #33CCCC;" onClick="showColor('#33CCCC');"		onMouseOver="showColor_over('#33CCCC');">&nbsp;</td>
		<td style="width: 10px; background-color: #33CCFF;" onClick="showColor('#33CCFF');"		onMouseOver="showColor_over('#33CCFF');">&nbsp;</td>
		<td style="width: 10px; background-color: #66CC00;" onClick="showColor('#66CC00');"		onMouseOver="showColor_over('#66CC00');">&nbsp;</td>
		<td style="width: 10px; background-color: #66CC33;" onClick="showColor('#66CC33');"		onMouseOver="showColor_over('#66CC33');">&nbsp;</td>
		<td style="width: 10px; background-color: #66CC66;" onClick="showColor('#66CC66');"		onMouseOver="showColor_over('#66CC66');">&nbsp;</td>
		<td style="width: 10px; background-color: #66CC99;" onClick="showColor('#66CC99');"		onMouseOver="showColor_over('#66CC99');">&nbsp;</td>
		<td style="width: 10px; background-color: #66CCCC;" onClick="showColor('#66CCCC');"		onMouseOver="showColor_over('#66CCCC');">&nbsp;</td>
		<td style="width: 10px; background-color: #66CCFF;" onClick="showColor('#66CCFF');"		onMouseOver="showColor_over('#66CCFF');">&nbsp;</td>
		<td style="width: 10px; background-color: #99CC00;" onClick="showColor('#99CC00');"		onMouseOver="showColor_over('#99CC00');">&nbsp;</td>
		<td style="width: 10px; background-color: #99CC33;" onClick="showColor('#99CC33');"		onMouseOver="showColor_over('#99CC33');">&nbsp;</td>
		<td style="width: 10px; background-color: #99CC66;" onClick="showColor('#99CC66');"		onMouseOver="showColor_over('#99CC66');">&nbsp;</td>
		<td style="width: 10px; background-color: #99CC99;" onClick="showColor('#99CC99');"		onMouseOver="showColor_over('#99CC99');">&nbsp;</td>
		<td style="width: 10px; background-color: #99CCCC;" onClick="showColor('#99CCCC');"		onMouseOver="showColor_over('#99CCCC');">&nbsp;</td>
		<td style="width: 10px; background-color: #99CCFF;" onClick="showColor('#99CCFF');"		onMouseOver="showColor_over('#99CCFF');">&nbsp;</td>
		<td style="width: 10px; background-color: #CCCC00;" onClick="showColor('#CCCC00');"		onMouseOver="showColor_over('#CCCC00');">&nbsp;</td>
		<td style="width: 10px; background-color: #CCCC33;" onClick="showColor('#CCCC33');"		onMouseOver="showColor_over('#CCCC33');">&nbsp;</td>
		<td style="width: 10px; background-color: #CCCC66;" onClick="showColor('#CCCC66');"		onMouseOver="showColor_over('#CCCC66');">&nbsp;</td>
		<td style="width: 10px; background-color: #CCCC99;" onClick="showColor('#CCCC99');"		onMouseOver="showColor_over('#CCCC99');">&nbsp;</td>
		<td style="width: 10px; background-color: #CCCCCC;" onClick="showColor('#CCCCCC');"		onMouseOver="showColor_over('#CCCCCC');">&nbsp;</td>
		<td style="width: 10px; background-color: #CCCCFF;" onClick="showColor('#CCCCFF');"		onMouseOver="showColor_over('#CCCCFF');">&nbsp;</td>
		<td style="width: 10px; background-color: #FFCC00;" onClick="showColor('#FFCC00');"		onMouseOver="showColor_over('#FFCC00');">&nbsp;</td>
		<td style="width: 10px; background-color: #FFCC33;" onClick="showColor('#FFCC33');"		onMouseOver="showColor_over('#FFCC33');">&nbsp;</td>
		<td style="width: 10px; background-color: #FFCC66;" onClick="showColor('#FFCC66');"		onMouseOver="showColor_over('#FFCC66');">&nbsp;</td>
		<td style="width: 10px; background-color: #FFCC99;" onClick="showColor('#FFCC99');"		onMouseOver="showColor_over('#FFCC99');">&nbsp;</td>
		<td style="width: 10px; background-color: #FFCCCC;" onClick="showColor('#FFCCCC');"		onMouseOver="showColor_over('#FFCCCC');">&nbsp;</td>
		<td style="width: 10px; background-color: #FFCCFF;" onClick="showColor('#FFCCFF');"		onMouseOver="showColor_over('#FFCCFF');">&nbsp;</td>
       </tr>
	   <tr>                                                                         	          
		<td style="width: 10px; background-color: #009900;" onClick="showColor('#009900');"		onMouseOver="showColor_over('#009900');">&nbsp;</td>
		<td style="width: 10px; background-color: #009933;" onClick="showColor('#009933');"		onMouseOver="showColor_over('#009933');">&nbsp;</td>
		<td style="width: 10px; background-color: #009966;" onClick="showColor('#009966');"		onMouseOver="showColor_over('#009966');">&nbsp;</td>
		<td style="width: 10px; background-color: #009999;" onClick="showColor('#009999');"		onMouseOver="showColor_over('#009999');">&nbsp;</td>
		<td style="width: 10px; background-color: #0099CC;" onClick="showColor('#0099CC');"		onMouseOver="showColor_over('#0099CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #0099FF;" onClick="showColor('#0099FF');"		onMouseOver="showColor_over('#0099FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #339900;" onClick="showColor('#339900');"		onMouseOver="showColor_over('#339900');">&nbsp;</td>
		<td style="width: 10px; background-color: #339933;" onClick="showColor('#339933');"		onMouseOver="showColor_over('#339933');">&nbsp;</td>
		<td style="width: 10px; background-color: #339966;" onClick="showColor('#339966');"		onMouseOver="showColor_over('#339966');">&nbsp;</td>
		<td style="width: 10px; background-color: #339999;" onClick="showColor('#339999');"		onMouseOver="showColor_over('#339999');">&nbsp;</td>
		<td style="width: 10px; background-color: #3399CC;" onClick="showColor('#3399CC');"		onMouseOver="showColor_over('#3399CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #3399FF;" onClick="showColor('#3399FF');"		onMouseOver="showColor_over('#3399FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #669900;" onClick="showColor('#669900');"		onMouseOver="showColor_over('#669900');">&nbsp;</td>
		<td style="width: 10px; background-color: #669933;" onClick="showColor('#669933');"		onMouseOver="showColor_over('#669933');">&nbsp;</td>
		<td style="width: 10px; background-color: #669966;" onClick="showColor('#669966');"		onMouseOver="showColor_over('#669966');">&nbsp;</td>
		<td style="width: 10px; background-color: #669999;" onClick="showColor('#669999');"		onMouseOver="showColor_over('#669999');">&nbsp;</td>
		<td style="width: 10px; background-color: #6699CC;" onClick="showColor('#6699CC');"		onMouseOver="showColor_over('#6699CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #6699FF;" onClick="showColor('#6699FF');"		onMouseOver="showColor_over('#6699FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #999900;" onClick="showColor('#999900');"		onMouseOver="showColor_over('#999900');">&nbsp;</td>
		<td style="width: 10px; background-color: #999933;" onClick="showColor('#999933');"		onMouseOver="showColor_over('#999933');">&nbsp;</td>
		<td style="width: 10px; background-color: #999966;" onClick="showColor('#999966');"		onMouseOver="showColor_over('#999966');">&nbsp;</td>
		<td style="width: 10px; background-color: #999999;" onClick="showColor('#999999');"		onMouseOver="showColor_over('#999999');">&nbsp;</td>
		<td style="width: 10px; background-color: #9999CC;" onClick="showColor('#9999CC');"		onMouseOver="showColor_over('#9999CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #9999FF;" onClick="showColor('#9999FF');"		onMouseOver="showColor_over('#9999FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC9900;" onClick="showColor('#CC9900');"		onMouseOver="showColor_over('#CC9900');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC9933;" onClick="showColor('#CC9933');"		onMouseOver="showColor_over('#CC9933');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC9966;" onClick="showColor('#CC9966');"		onMouseOver="showColor_over('#CC9966');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC9999;" onClick="showColor('#CC9999');"		onMouseOver="showColor_over('#CC9999');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC99CC;" onClick="showColor('#CC99CC');"		onMouseOver="showColor_over('#CC99CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC99FF;" onClick="showColor('#CC99FF');"		onMouseOver="showColor_over('#CC99FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF9900;" onClick="showColor('#FF9900');"		onMouseOver="showColor_over('#FF9900');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF9933;" onClick="showColor('#FF9933');"		onMouseOver="showColor_over('#FF9933');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF9966;" onClick="showColor('#FF9966');"		onMouseOver="showColor_over('#FF9966');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF9999;" onClick="showColor('#FF9999');"		onMouseOver="showColor_over('#FF9999');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF99CC;" onClick="showColor('#FF99CC');"		onMouseOver="showColor_over('#FF99CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF99FF;" onClick="showColor('#FF99FF');"		onMouseOver="showColor_over('#FF99FF');">&nbsp;</td>
       </tr>
	   <tr>                                                                          		           
		<td style="width: 10px; background-color: #006600;" onClick="showColor('#006600');"		onMouseOver="showColor_over('#006600');">&nbsp;</td>
		<td style="width: 10px; background-color: #006633;" onClick="showColor('#006633');"		onMouseOver="showColor_over('#006633');">&nbsp;</td>
		<td style="width: 10px; background-color: #006666;" onClick="showColor('#006666');"		onMouseOver="showColor_over('#006666');">&nbsp;</td>
		<td style="width: 10px; background-color: #006699;" onClick="showColor('#006699');"		onMouseOver="showColor_over('#006699');">&nbsp;</td>
		<td style="width: 10px; background-color: #0066CC;" onClick="showColor('#0066CC');"		onMouseOver="showColor_over('#0066CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #0066FF;" onClick="showColor('#0066FF');"		onMouseOver="showColor_over('#0066FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #336600;" onClick="showColor('#336600');"		onMouseOver="showColor_over('#336600');">&nbsp;</td>
		<td style="width: 10px; background-color: #336633;" onClick="showColor('#336633');"		onMouseOver="showColor_over('#336633');">&nbsp;</td>
		<td style="width: 10px; background-color: #336666;" onClick="showColor('#336666');"		onMouseOver="showColor_over('#336666');">&nbsp;</td>
		<td style="width: 10px; background-color: #336699;" onClick="showColor('#336699');"		onMouseOver="showColor_over('#336699');">&nbsp;</td>
		<td style="width: 10px; background-color: #3366CC;" onClick="showColor('#3366CC');"		onMouseOver="showColor_over('#3366CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #3366FF;" onClick="showColor('#3366FF');"		onMouseOver="showColor_over('#3366FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #666600;" onClick="showColor('#666600');"		onMouseOver="showColor_over('#666600');">&nbsp;</td>
		<td style="width: 10px; background-color: #666633;" onClick="showColor('#666633');"		onMouseOver="showColor_over('#666633');">&nbsp;</td>
		<td style="width: 10px; background-color: #666666;" onClick="showColor('#666666');"		onMouseOver="showColor_over('#666666');">&nbsp;</td>
		<td style="width: 10px; background-color: #666699;" onClick="showColor('#666699');"		onMouseOver="showColor_over('#666699');">&nbsp;</td>
		<td style="width: 10px; background-color: #6666CC;" onClick="showColor('#6666CC');"		onMouseOver="showColor_over('#6666CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #6666FF;" onClick="showColor('#6666FF');"		onMouseOver="showColor_over('#6666FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #996600;" onClick="showColor('#996600');"		onMouseOver="showColor_over('#996600');">&nbsp;</td>
		<td style="width: 10px; background-color: #996633;" onClick="showColor('#996633');"		onMouseOver="showColor_over('#996633');">&nbsp;</td>
		<td style="width: 10px; background-color: #996666;" onClick="showColor('#996666');"		onMouseOver="showColor_over('#996666');">&nbsp;</td>
		<td style="width: 10px; background-color: #996699;" onClick="showColor('#996699');"		onMouseOver="showColor_over('#996699');">&nbsp;</td>
		<td style="width: 10px; background-color: #9966CC;" onClick="showColor('#9966CC');"		onMouseOver="showColor_over('#9966CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #9966FF;" onClick="showColor('#9966FF');"		onMouseOver="showColor_over('#9966FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC6600;" onClick="showColor('#CC6600');"		onMouseOver="showColor_over('#CC6600');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC6633;" onClick="showColor('#CC6633');"		onMouseOver="showColor_over('#CC6633');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC6666;" onClick="showColor('#CC6666');"		onMouseOver="showColor_over('#CC6666');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC6699;" onClick="showColor('#CC6699');"		onMouseOver="showColor_over('#CC6699');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC66CC;" onClick="showColor('#CC66CC');"		onMouseOver="showColor_over('#CC66CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC66FF;" onClick="showColor('#CC66FF');"		onMouseOver="showColor_over('#CC66FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF6600;" onClick="showColor('#FF6600');"		onMouseOver="showColor_over('#FF6600');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF6633;" onClick="showColor('#FF6633');"		onMouseOver="showColor_over('#FF6633');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF6666;" onClick="showColor('#FF6666');"		onMouseOver="showColor_over('#FF6666');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF6699;" onClick="showColor('#FF6699');"		onMouseOver="showColor_over('#FF6699');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF66CC;" onClick="showColor('#FF66CC');"		onMouseOver="showColor_over('#FF66CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF66FF;" onClick="showColor('#FF66FF');"		onMouseOver="showColor_over('#FF66FF');">&nbsp;</td>
	   </tr>
	   <tr>                                                                          		            
		<td style="width: 10px; background-color: #003300;" onClick="showColor('#003300');"		onMouseOver="showColor_over('#003300');">&nbsp;</td>
		<td style="width: 10px; background-color: #003333;" onClick="showColor('#003333');"		onMouseOver="showColor_over('#003333');">&nbsp;</td>
		<td style="width: 10px; background-color: #003366;" onClick="showColor('#003366');"		onMouseOver="showColor_over('#003366');">&nbsp;</td>
		<td style="width: 10px; background-color: #003399;" onClick="showColor('#003399');"		onMouseOver="showColor_over('#003399');">&nbsp;</td>
		<td style="width: 10px; background-color: #0033CC;" onClick="showColor('#0033CC');"		onMouseOver="showColor_over('#0033CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #0033FF;" onClick="showColor('#0033FF');"		onMouseOver="showColor_over('#0033FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #333300;" onClick="showColor('#333300');"		onMouseOver="showColor_over('#333300');">&nbsp;</td>
		<td style="width: 10px; background-color: #333333;" onClick="showColor('#333333');"		onMouseOver="showColor_over('#333333');">&nbsp;</td>
		<td style="width: 10px; background-color: #333366;" onClick="showColor('#333366');"		onMouseOver="showColor_over('#333366');">&nbsp;</td>
		<td style="width: 10px; background-color: #333399;" onClick="showColor('#333399');"		onMouseOver="showColor_over('#333399');">&nbsp;</td>
		<td style="width: 10px; background-color: #3333CC;" onClick="showColor('#3333CC');"		onMouseOver="showColor_over('#3333CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #3333FF;" onClick="showColor('#3333FF');"		onMouseOver="showColor_over('#3333FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #663300;" onClick="showColor('#663300');"		onMouseOver="showColor_over('#663300');">&nbsp;</td>
		<td style="width: 10px; background-color: #663333;" onClick="showColor('#663333');"		onMouseOver="showColor_over('#663333');">&nbsp;</td>
		<td style="width: 10px; background-color: #663366;" onClick="showColor('#663366');"		onMouseOver="showColor_over('#663366');">&nbsp;</td>
		<td style="width: 10px; background-color: #663399;" onClick="showColor('#663399');"		onMouseOver="showColor_over('#663399');">&nbsp;</td>
		<td style="width: 10px; background-color: #6633CC;" onClick="showColor('#6633CC');"		onMouseOver="showColor_over('#6633CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #6633FF;" onClick="showColor('#6633FF');"		onMouseOver="showColor_over('#6633FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #993300;" onClick="showColor('#993300');"		onMouseOver="showColor_over('#993300');">&nbsp;</td>
		<td style="width: 10px; background-color: #993333;" onClick="showColor('#993333');"		onMouseOver="showColor_over('#993333');">&nbsp;</td>
		<td style="width: 10px; background-color: #993366;" onClick="showColor('#993366');"		onMouseOver="showColor_over('#993366');">&nbsp;</td>
		<td style="width: 10px; background-color: #993399;" onClick="showColor('#993399');"		onMouseOver="showColor_over('#993399');">&nbsp;</td>
		<td style="width: 10px; background-color: #9933CC;" onClick="showColor('#9933CC');"		onMouseOver="showColor_over('#9933CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #9933FF;" onClick="showColor('#9933FF');"		onMouseOver="showColor_over('#9933FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC3300;" onClick="showColor('#CC3300');"		onMouseOver="showColor_over('#CC3300');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC3333;" onClick="showColor('#CC3333');"		onMouseOver="showColor_over('#CC3333');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC3366;" onClick="showColor('#CC3366');"		onMouseOver="showColor_over('#CC3366');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC3399;" onClick="showColor('#CC3399');"		onMouseOver="showColor_over('#CC3399');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC33CC;" onClick="showColor('#CC33CC');"		onMouseOver="showColor_over('#CC33CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC33FF;" onClick="showColor('#CC33FF');"		onMouseOver="showColor_over('#CC33FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF3300;" onClick="showColor('#FF3300');"		onMouseOver="showColor_over('#FF3300');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF3333;" onClick="showColor('#FF3333');"		onMouseOver="showColor_over('#FF3333');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF3366;" onClick="showColor('#FF3366');"		onMouseOver="showColor_over('#FF3366');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF3399;" onClick="showColor('#FF3399');"		onMouseOver="showColor_over('#FF3399');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF33CC;" onClick="showColor('#FF33CC');"		onMouseOver="showColor_over('#FF33CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF33FF;" onClick="showColor('#FF33FF');"		onMouseOver="showColor_over('#FF33FF');">&nbsp;</td>
	   </tr>
	   <tr>                                                                          		                                                                                      		          
		<td height="21" style="width: 10px; background-color: #000000;" onClick="showColor('#000000');"		onMouseOver="showColor_over('#000000');">&nbsp;</td>
		<td style="width: 10px; background-color: #000033;" onClick="showColor('#000033');"		onMouseOver="showColor_over('#000033');">&nbsp;</td>
		<td style="width: 10px; background-color: #000066;" onClick="showColor('#000066');"		onMouseOver="showColor_over('#000066');">&nbsp;</td>
		<td style="width: 10px; background-color: #000099;" onClick="showColor('#000099');"		onMouseOver="showColor_over('#000099');">&nbsp;</td>
		<td style="width: 10px; background-color: #0000CC;" onClick="showColor('#0000CC');"		onMouseOver="showColor_over('#0000CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #0000FF;" onClick="showColor('#0000FF');"		onMouseOver="showColor_over('#0000FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #330000;" onClick="showColor('#330000');"		onMouseOver="showColor_over('#330000');">&nbsp;</td>
		<td style="width: 10px; background-color: #330033;" onClick="showColor('#330033');"		onMouseOver="showColor_over('#330033');">&nbsp;</td>
		<td style="width: 10px; background-color: #330066;" onClick="showColor('#330066');"		onMouseOver="showColor_over('#330066');">&nbsp;</td>
		<td style="width: 10px; background-color: #330099;" onClick="showColor('#330099');"		onMouseOver="showColor_over('#330099');">&nbsp;</td>
		<td style="width: 10px; background-color: #3300CC;" onClick="showColor('#3300CC');"		onMouseOver="showColor_over('#3300CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #3300FF;" onClick="showColor('#3300FF');"		onMouseOver="showColor_over('#3300FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #660000;" onClick="showColor('#660000');"		onMouseOver="showColor_over('#660000');">&nbsp;</td>
		<td style="width: 10px; background-color: #660033;" onClick="showColor('#660033');"		onMouseOver="showColor_over('#660033');">&nbsp;</td>
		<td style="width: 10px; background-color: #660066;" onClick="showColor('#660066');"		onMouseOver="showColor_over('#660066');">&nbsp;</td>
		<td style="width: 10px; background-color: #660099;" onClick="showColor('#660099');"		onMouseOver="showColor_over('#660099');">&nbsp;</td>
		<td style="width: 10px; background-color: #6600CC;" onClick="showColor('#6600CC');"		onMouseOver="showColor_over('#6600CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #6600FF;" onClick="showColor('#6600FF');"		onMouseOver="showColor_over('#6600FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #990000;" onClick="showColor('#990000');"		onMouseOver="showColor_over('#990000');">&nbsp;</td>
		<td style="width: 10px; background-color: #990033;" onClick="showColor('#990033');"		onMouseOver="showColor_over('#990033');">&nbsp;</td>
		<td style="width: 10px; background-color: #990066;" onClick="showColor('#990066');"		onMouseOver="showColor_over('#990066');">&nbsp;</td>
		<td style="width: 10px; background-color: #990099;" onClick="showColor('#990099');"		onMouseOver="showColor_over('#990099');">&nbsp;</td>
		<td style="width: 10px; background-color: #9900CC;" onClick="showColor('#9900CC');"		onMouseOver="showColor_over('#9900CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #9900FF;" onClick="showColor('#9900FF');"		onMouseOver="showColor_over('#9900FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC0000;" onClick="showColor('#CC0000');"		onMouseOver="showColor_over('#CC0000');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC0033;" onClick="showColor('#CC0033');"		onMouseOver="showColor_over('#CC0033');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC0066;" onClick="showColor('#CC0066');"		onMouseOver="showColor_over('#CC0066');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC0099;" onClick="showColor('#CC0099');"		onMouseOver="showColor_over('#CC0099');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC00CC;" onClick="showColor('#CC00CC');"		onMouseOver="showColor_over('#CC00CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #CC00FF;" onClick="showColor('#CC00FF');"		onMouseOver="showColor_over('#CC00FF');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF0000;" onClick="showColor('#FF0000');"		onMouseOver="showColor_over('#FF0000');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF0033;" onClick="showColor('#FF0033');"		onMouseOver="showColor_over('#FF0033');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF0066;" onClick="showColor('#FF0066');"		onMouseOver="showColor_over('#FF0066');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF0099;" onClick="showColor('#FF0099');"		onMouseOver="showColor_over('#FF0099');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF00CC;" onClick="showColor('#FF00CC');"		onMouseOver="showColor_over('#FF00CC');">&nbsp;</td>
		<td style="width: 10px; background-color: #FF00FF;" onClick="showColor('#FF00FF');"		onMouseOver="showColor_over('#FF00FF');">&nbsp;</td>
       </tr>
      </table>
	</td>
  </tr>
  <tr align="center">
    <td height="14" colspan="4" class="line_left2 line_right2 text3 bg2">&nbsp;Ausgew�hlte Farbe:</td>
  </tr>
  <tr>
    <td height="25" class="line_left2 line_down2 text1">
	   <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid black; background-color: #FFFFFF">
    	<tr>
      	  <td id="preview" width="100%">&nbsp;</td>
    	</tr>
      </table>
	</td>
    <td class="line_down2">&nbsp;</td>
    <td width="38" class="text1 line_down2"><input id="color" value="#000000" size="6" readonly class="inputfield"></td>
    <td width="364" class="line_down2 line_right2" align="right">
	<input type="button" value="Speichern" onClick="returnToMain();" class="button">
	<input type="button" value="Abbruch" onClick="window.close();" class="button">    
	</td>
  </tr>
</table>
</body>
</html>
