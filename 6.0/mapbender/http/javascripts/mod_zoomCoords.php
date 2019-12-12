<?php
#$Id: mod_zoomCoords.php 6798 2010-08-24 09:44:38Z christoph $
# http://www.mapbender.org/Mapbender_without_iframes
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
?>
var mod_zoomCoords_target = options.target;
options.zoomCoords_permanentHighlight = 
	typeof options.zoomCoords_permanentHighlight === "undefined" ?
	"false" : options.zoomCoords_permanentHighlight;


function zoomCoordinate (x,y) {
   x=x.replace(",",".");
   y=y.replace(",",".");

   $("#fieldX").val(x);
   $("#fieldY").val(y);

   if (isNaN(x)==true || isNaN(y)==true) {
       alert ("<?php echo _mb("Please type a number.");?>");
   }
   else {
	   if(options.zoomCoords_permanentHighlight =='true'){
		   setPermanentMarker(x,y);
	   }
  		hideHighlight();
      	Mapbender.modules[mod_zoomCoords_target[0]].zoom(true, 1.0, x, y);
   }
}

function highlight(x, y){
	if (x!='' && y!='') {
	   x=x.replace(",",".");
	   y=y.replace(",",".");

  	 	$("#fieldX").val(x);
   		$("#fieldY").val(y);

	   if (isNaN(x)==true || isNaN(y)==true) {

	   }
	   else {
			for(var i=0;i<mod_zoomCoords_target.length;i++){
				mb_showHighlight(mod_zoomCoords_target[i],x,y);
			}
	   }
	}
}


function hideHighlight(){
	for(var i=0;i<mod_zoomCoords_target.length;i++){
		mb_hideHighlight(mod_zoomCoords_target[i]);
	}
}

function setPermanentMarker(x,y){
 	mod_permanentHighlight_x = parseFloat(x);
   	mod_permanentHighlight_y = parseFloat(y);
   	mod_permanentHighlight_text = x + ' / '+ y;

   	mod_permanentHighlight_init();
}

Mapbender.events.initMaps.register(function zoomCoordsInit(){
	if($(this)){
		$("<form />").attr({"id":"zoomCoordsForm","name":"zoomCoordsForm","method":"post"}).appendTo($("#zoomCoords"));
		$("<span />").attr({"id":"spanLon","name":"spanLon"}).appendTo($("#zoomCoordsForm"));
		$("<span />").attr({"id":"spanLat","name":"spanLat"}).appendTo($("#zoomCoordsForm"));
		$("<input type='text'/>").attr({"id":"fieldX","name":"X"}).appendTo($("#zoomCoordsForm"));
		$("<input type='text'/>").attr({"id":"fieldY","name":"Y"}).appendTo($("#zoomCoordsForm"));
		$("<input type='button'/>").attr({"id":"buttonZoomCoord","name":"buttonZoomCoord","value":"<?php echo _mb("ok"); ?>"}).appendTo($("#zoomCoordsForm"));

		$("#zoomCoordsForm").css({"font-family":"Arial, Helvetica, sans-serif","font-size":"11px"});
		$("#spanLon").css({"position":"absolute","left":"5px","top":"5px","color":"Gray"}).text("<?php echo _mb("Longitude");?>");
		$("#spanLat").css({"position":"absolute","left":"80px","top":"5px","color":"Gray"}).text("<?php echo _mb("Latitude");?>");
		$("#fieldX").css({"position":"absolute","left":"5px","top":"20px","color":"Gray","width":"65px","border":"solid thin","height":"20px"});
		$("#fieldY").css({"position":"absolute","left":"80px","top":"20px","color":"Gray","width":"65px","border":"solid thin","height":"20px"});
		$("#buttonZoomCoord").css({"position":"absolute","left":"150px","top":"20px","color":"Gray","border":"solid thin","height":"20px"});

		$("#buttonZoomCoord").click(function () {
			zoomCoordinate($("#fieldX").val(), $("#fieldY").val());
			highlight($("#fieldX").val(), $("#fieldY").val());
		}).mouseover(function () {
			highlight($("#fieldX").val(), $("#fieldY").val());
		}).mouseout(function () {
			hideHighlight($("#fieldX").val(), $("#fieldY").val());
		});

	}
});