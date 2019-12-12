<?php
# $Id:$
# http://www.mapbender.org/index.php/mod_georss.php
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
#require_once(dirname(__FILE__)."/../include/dyn_php.php");
?>
var georssTargetArray = [];
<?php

//for old versions:
//$e_target = explode(",",$e_target);

for ($i = 0; $i < count($e_target); $i++) {
	echo "georssTargetArray.push('".$e_target[$i]."');";
	#echo "georssTargetArray.push('mapframe1');";
	#echo "georssTargetArray.push('mapframe1');";
}

//function _mb($a) {
//return $a;
//}


?>
var georssWin=null;
var georssHighlighter;
var georssUsemap;
var geoms=null;

<?php
if ($loadGeorssFromSession == "1" && isset($_SESSION["georssURL"]) && $_SESSION["georssURL"] != "") {
	//if (isset($_SESSION["georssURL"]) && $_SESSION["georssURL"] != "") {
	echo "var url=\"".$_SESSION["georssURL"]."\";";
	//echo "alert(\"Found following url in Session - will try to load it from there: \"+url);";
	echo "mb_registerInitFunctions(\"loadGeoRSS('".$_SESSION["georssURL"]."');\");";
	$_SESSION["georssURL"]=null;
	#echo "return false;"
}
?>
//Show prompt to load URL by hand dynamically - like addWMS
function loadGeoRSSByForm(){
   var loadGeoRSSUrl = prompt("GeoRSS-URL:","");
   if(loadGeoRSSUrl){
      loadGeoRSS(loadGeoRSSUrl);    
   }
}


function loadGeoRSS(url){
	//	
	//alert("<?php echo _mb("mapframe: ");?>get");
	//return;

	var targetEPSG = getMapObjByName('mapframe1').epsg;
	//alert(targetEPSG);
	//
	$.post("../php/geoRSSToGeoJSON.php",{url:url,targetEPSG:targetEPSG}, function(jsCode, status){
			if(status=='success'){
				if(jsCode==""){
					alert("<?php echo _mb("No GeoRSS feed information found. Please check your URL.");?>");
					return;
				}
				var geoObj = eval('(' + jsCode + ')');	
	        	if (typeof(geoObj) == 'object'&&typeof(geoObj.errorMessage)=='undefined') {
	           		//create georssHighlighter and usemap
	           		if(typeof(georssHighlighter)==='undefined'){
						georssHighlighter = new Highlight(georssTargetArray, "geoRssHL", {"position":"absolute", "top":"0px", "left":"0px", "z-index":30}, 2);
						georssUsemap = new Usemap(georssTargetArray, "geoRssUM", 120, 5, 2);
	           		}
	           		else{
						georssHighlighter.clean();
						georssUsemap.clean();
	           		}
	           		
	           		//Import Geometries
					geoms = new GeometryArray();
	           		geoms.importGeoJSON(geoObj);
	           		
	           		//Zoom to Extent of Geometries
	           		extent = geoms.getBBox();
	           		mb_calculateExtent(georssTargetArray[0], extent[0].x, extent[0].y, extent[1].x, extent[1].y);
	           		extent = enlargeExtent(extent, 10, georssTargetArray[0]);
	           		mb_calculateExtent(georssTargetArray[0], extent[0].x, extent[0].y, extent[1].x, extent[1].y);
	           		setMapRequest(georssTargetArray[0]);
					
					//Add geometries to usemap and georssHighlighter
					for( var i=0;i<geoms.count();i++){
						georssHighlighter.add(geoms.get(i),"red");
						georssUsemap.add(geoms.get(i), geoms.get(i).e.getElementValueByName("title"),null,null,showGeorssTooltip);
					}
					georssHighlighter.paint();
					georssUsemap.setUsemap();
						

					eventAfterMapRequest.register(function () {
						georssHighlighter.paint();
					});
					eventAfterMapRequest.register("georssUsemap.setUsemap();");
					//mb_registerSubFunctions("georssHighlighter.paint();");
					//mb_registerSubFunctions("georssUsemap.setUsemap();");
					//Delete geometries as they are now in georssHighlighter and Usemap
					delete geoms;
				}
				else {
					alert("<?php echo _mb("No GeoRSS feed information found. Please check your URL.");?>");
					return;
				}
	       	}
		});
}

function enlargeExtent(extent, pixel, frame){
	var min = realToMap(frame, extent[0]);
	var max = realToMap(frame, extent[1]);
	min.x-=pixel;
	min.y-=pixel;
	max.x+=pixel;
	max.y+=pixel;
	extent[0] = mapToReal(frame, min)
	extent[1] = mapToReal(frame, max)
	return extent;
}

function showGeorssTooltip(e){
	actGeom = this.geom;
	if(!actGeom)
		return;
	
	actFrame = georssTargetArray[0];
	
	//Get Mapframe Position
	x=parseInt(document.getElementById(actFrame).style.left, 10);
	y=parseInt(document.getElementById(actFrame).style.top, 10);
	
	x=0;
	y=0;
	
	//Hide old Window
	if(georssWin && georssWin.isVisible()){
		georssWin.destroy();
	}
	//create html code for the information to show
	var html="<html><br><br><br>";
	html = html + "<table border = \"1\" width=\"80%\">";
	//html = html + "<colgroup width=\"40\" span=\"1\"></colgroup>";
	html = html + "<tr>";
	//html = html + "<td>"+"Beschreibung"+"</td>";
	html = html + "<td>"+actGeom.e.getElementValueByName("description")+"</td>";
	html = html + "</tr><tr>";
	//html = html + "<td>"+"Link"+"</td>";
	html = html + "<td><a href=\""+actGeom.e.getElementValueByName("link")+"\" target=\"_blank\">Details</a></td>";
	//show button if some element is found which include an url to a wms capabilities document	
	if ( actGeom.e.getElementValueByName("ingrid:wms-url") != '') {
		html = html + "<tr>";
		html = html + "<td><a href=\""+actGeom.e.getElementValueByName("ingrid:wms-url")+"\" target=\"_blank\">Capabilities</a></td>";
		html = html + "</tr>";
		html = html + "<tr>";
		html = html + "<td><input type=button value='Laden' onclick=\"mod_addWMS_load(actGeom.e.getElementValueByName('ingrid:wms-url'));\"></td>";
		html = html + "</tr>";
	}
	html = html + "</tr></table></html>";

	
	//alert(html); 	
	//Show Modal Popup
	mb_getMousePos(e,actFrame);

	georssWin = new mb_popup({title:actGeom.e.getElementValueByName("title"),
		html:html,balloon:true,left:clickX+x,top:clickY+y,modal:true});
	georssWin.show();
}
