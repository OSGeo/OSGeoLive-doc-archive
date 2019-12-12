<?php
# $Id: mod_usemap.php 7772 2011-04-14 09:42:09Z verenadiewald $
# http://www.mapbender.org/index.php/UseMap
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
?>
var mod_usemap_target = 'mapframe1';
var mod_usemap_wfs = "<url>";
var mod_usemap_px = 10;

eventAfterMapRequest.register(function () {
	mod_usemap_init();
});

Mapbender.events.init.register(function () {
	um_init();
});

function mod_usemap_init(){
	var ind = getMapObjIndexByName(mod_usemap_target);
	var extent = mb_mapObj[ind].extent.toString();
	//var url = "../php/mod_usemap.php?url="
	var url = mod_usemap_wfs + "&BBOX=" + extent;
	url += "&extent=" + extent;
	url += "&width=" + mb_mapObj[ind].width;
	url += "&height=" + mb_mapObj[ind].height;
	url += "&gui_id=<?php echo $gui_id;?>";
	url += "&e_id=<?php echo $e_id;?>";
	

	$("#um_img").css({
  		position:'absolute', 
	  	width:mb_mapObj[ind].width, 
	  	height:mb_mapObj[ind].height, 
	  	zIndex:100
	});
	
	var req = new Mapbender.Ajax.Request({
		url: "../php/mod_usemap.php",
		method: "createUsemap",
		parameters: {
			url: url
		},
		callback: function (obj, result, message) {
			if (!result) {
				return;
			}
			mod_usemap_set(obj.um_title,obj.um_x,obj.um_y);
		}
	});
	req.send();
}

function mod_usemap_set(title,x,y){
	var str = "";
	for(var i=0; i<title.length; i++){
		var pos = makeRealWorld2mapPos(mod_usemap_target,x[i],y[i]);
		str += "<AREA onmouseover='over(event, \"" + title[i] + "\", this)' ";
		str += "onmouseout=out(this) shape='circle'  coords='";
		str += Math.round(pos[0]) + "," + Math.round(pos[1]) + "," + mod_usemap_px + "'";
		str += " href='#'>";
	}
	writeTag('', 'um', str);
}

/* Opacity for highlighting */
    cw_opacity = 0.5;
/* Color for polygon boundary */
    cw_bndcolor = "#ff0000";
/* Color for polygon fill */
    cw_fillcolor = "#ffff00";

function polyXcoords(coords){
	var Xcoords = '';
	for (var z = 0; z<coords.length; z=z+2){
		if (z > 0){
			Xcoords += ', ';
		}
		Xcoords += parseInt(coords[z]);
	}
	return Xcoords;
}
function polyYcoords(coords){
	var Ycoords = '';
	for (var z = 1; z<coords.length; z=z+2){
		if (z > 1){
			Ycoords += ', ';
		}
		Ycoords += parseInt(coords[z]);
	}
	return Ycoords;
}
function setFocus(objid){
	if (canvasHasDrawing == true) return true;
		var coords = objid.coords.split(',');
		if ((objid.shape.toUpperCase() == 'POLY') || (objid.shape.toUpperCase() == 'POLYGON')){
			var Xcoords = polyXcoords(coords);
			var Ycoords = polyYcoords(coords);

			var pgx = Xcoords.split(',');
			var pgy = Ycoords.split(',');
			for (var i=0 ; i<pgx.length ; i++ ){
				pgx[i] = parseInt(pgx[i]);
				pgy[i] = parseInt(pgy[i]);
			}
			canvas.setColor(cw_fillcolor);
			canvas.fillPolygon(pgx,pgy);
			canvas.paint();
			canvas.setColor(cw_bndcolor);
			canvas.drawPolygon(pgx,pgy);
			canvas.paint();
		}
		if ((objid.shape.toUpperCase() == 'CIRCLE')){
			var c = coords;

			c[0] = parseInt(c[0]);
			c[1] = parseInt(c[1]);

			canvas.setColor(cw_fillcolor);
			canvas.fillEllipse(c[0]-mod_usemap_px/2,c[1]-mod_usemap_px/2,parseInt(c[2]),parseInt(c[2]));
			canvas.paint();
			canvas.setColor(cw_bndcolor);
			canvas.drawEllipse(c[0]-mod_usemap_px/2,c[1]-mod_usemap_px/2,parseInt(c[2]),parseInt(c[2]));
			canvas.paint();
		}
		canvasHasDrawing = true;
		return true;
}

function clearFocus(objid){
	if (canvasHasDrawing) canvas.clear();
		canvasHasDrawing = false;
		return true;
}

function over(e, id, area){
	var coords = area.coords.split(',');
	area.setAttribute('title', "");
	if (!isOver){
		$("#um_title").html(id).css("display", "block");
		isOver = area;
		setFocus(area);
	}
	$("#um_title").css({
	    position: "absolute",
	    top: parseInt(coords[1])+mod_usemap_px + "px",
	    left: parseInt(coords[0])+mod_usemap_px + "px"
	});
	canvasHasDrawing = true;
}

function out(area){
	$("#um_title").html("").css("display", "none");
	clearFocus(area);
	isOver = false;
	canvasHasDrawing = false;
}

function um_init() {
	$("#mapframe1").append("<img src='../img/transparent.gif' usemap='#um' name='um_img' id='um_img' />");
	$("#mapframe1").append("<div id='um_draw'></div>");
	$("#um_draw").css({
	  	left:0,
	  	overflow:'visible',
	  	position:'absolute',
	  	top:0,
	  	zIndex:99
	});
	$("#mapframe1").append("<div name='um_title' id='um_title'></div>");
	$("#um_title").css({
	  	'font-family': 'Arial, Helvetica, sans-serif',
	  	display:'none', 
	  	overflow:'visible',
	  	position:'absolute', 
	  	background:'#BEC1C4',
	  	border:'1px solid black', 
	  	zIndex:98
	});
	$("#mapframe1").append("<map id='um' name='um'></map>");

	var mapObjInd = getMapObjIndexByName(mod_usemap_target);
	canvas = new jsGraphics('um_draw', mb_mapObj[mapObjInd].getDomElement().frameName ? window.frames[mapframe] : window);
	canvas.setStroke(2);
	canvasHasDrawing = false;
	isOver = false;
}
