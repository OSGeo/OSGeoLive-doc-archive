<?php
# $Id: mod_highlightPOI.php 7740 2011-04-04 13:36:23Z verenadiewald $ 
# http://www.mapbender.org/index.php/mod_highlightPOI.php
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

echo "var mod_highlightPOI_target = '".$e_target[0]."';";

include('../include/dyn_js.php');

?>
try{
	if (poi_image){}
}
catch(e){
	poi_image = '../img/redball.gif';
}

try{
	if (poi_width){}
}
catch(e){
	poi_width = 14;
}

try{
	if (poi_height){}
}
catch(e){
	poi_height = 14;
}

try{
	if (poi_style){}
}
catch(e){
	poi_style = 'background-color:white;font-weight: bold;color:black;font-family:Arial;';
}

eventAfterLoadWMS.register(function () {
	mod_highlightPOI_init();
});
eventAfterMapRequest.register(function () {
	mod_highlightPOI_draw();
});

var mod_highlightPOI_minx;
var mod_highlightPOI_miny;
var mod_highlightPOI_maxx;
var mod_highlightPOI_maxy;
var mod_highlightPOI_name = new Array();
var mod_highlightPOI_x = new Array();
var mod_highlightPOI_y = new Array();
var mod_highlightPOI_params = new Array();
var myPOI;

function mod_highlightPOI_init(){
		var myPOI = "<?php if (CHARSET == 'UTF-8'){
				echo addslashes(preg_replace("/\n/", "<br>", Mapbender::session()->get("mb_myPOI")));
			}else{
				echo addslashes(preg_replace("/\n/", "<br>", utf8_decode(Mapbender::session()->get("mb_myPOI"))));
			} 
			?>";

	if(myPOI != ""){
 		mod_highlightPOI_params = myPOI.split("___");

	  
  
	  for(var i=0; i<mod_highlightPOI_params.length; i=i+3){
	    if(i==0){
	      mod_highlightPOI_name[i] = mod_highlightPOI_params[i];
	      mod_highlightPOI_minx = parseInt(mod_highlightPOI_params[i+1]);
	      mod_highlightPOI_miny = parseInt(mod_highlightPOI_params[i+2]);
	      mod_highlightPOI_maxx = parseInt(mod_highlightPOI_params[i+1]);
	      mod_highlightPOI_maxy = parseInt(mod_highlightPOI_params[i+2]);
	    }  
	    else{
	      mod_highlightPOI_name[i] = mod_highlightPOI_params[i];
	      if(mod_highlightPOI_params[i+1] < mod_highlightPOI_minx){
	        mod_highlightPOI_minx = parseInt(mod_highlightPOI_params[i+1]);
	      }
	      if(mod_highlightPOI_params[i+2] < mod_highlightPOI_miny){
	        mod_highlightPOI_miny = parseInt(mod_highlightPOI_params[i+2]);
	      }
	      if(mod_highlightPOI_params[i+1] > mod_highlightPOI_maxx){
	        mod_highlightPOI_maxx = parseInt(mod_highlightPOI_params[i+1]);
	      }
	      if(mod_highlightPOI_params[i+2] > mod_highlightPOI_maxy){
	        mod_highlightPOI_maxy = parseInt(mod_highlightPOI_params[i+2]);
	      }  
	    }
	  }
	  if((mod_highlightPOI_maxx - mod_highlightPOI_minx) < 100){
	    mod_highlightPOI_minx -= 50;
	    mod_highlightPOI_maxx += 50;
	  }
	  if((mod_highlightPOI_maxy - mod_highlightPOI_miny) < 100){
	    mod_highlightPOI_miny -= 50;
	    mod_highlightPOI_maxy += 50;
	  }
	  mod_highlightPOI_minx -= 50;
	  mod_highlightPOI_maxx += 50;
	  mod_highlightPOI_miny -= 50;
	  mod_highlightPOI_maxy += 50;
	  //mb_calculateExtent( mod_highlightPOI_target,mod_highlightPOI_minx,mod_highlightPOI_miny,mod_highlightPOI_maxx,mod_highlightPOI_maxy);
  }
}


function mod_highlightPOI_draw(){
	var mapObject = getMapObjByName(mod_highlightPOI_target);
	var map_el = mapObject.getDomElement();
	if (!map_el.ownerDocument.getElementById(mapObject.elementName + "_permanent")) {
		//create Box Elements

		var $div = $("<div id='" + mapObject.elementName + "_permanent'><img src='../img/redball.gif'/></div>");
		$div.css({
			position: "absolute",
			top: "0px",
			left: "0px"
		});
		map_el.appendChild($div.get(0));
	}
	
	var tagSource = "";
	for (var i = 0; i < mod_highlightPOI_params.length; i = i + 3) {
		var pointFromUrl = new Point(parseFloat(mod_highlightPOI_params[i+1]), parseFloat(mod_highlightPOI_params[i+2]));
		var pos = mapObject.convertRealToPixel(pointFromUrl);
		tagSource += "<div style='z-index:105;position:absolute;left:"+(pos.x-Math.round(0.5*poi_width))+"px;top:"+(pos.y-Math.round(0.5*poi_height))+"px'>";
		tagSource += "<img src='"+poi_image+"'>";
		tagSource += "<div class='ui-widget-content ui-corner-all' style='padding:3px'><span style='white-space:nowrap;'>"+mod_highlightPOI_params[i].replace("\n", "<br>")+"</span></div>";
		tagSource += "</div>";
	}
	$("#" + mapObject.elementName + "_permanent").html(tagSource);
}
