<?php
# $Id: mod_navFrame.php 6673 2010-08-02 13:52:19Z christoph $
# http://www.mapbender.org/index.php/mod_navFrame.php
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
include '../include/dyn_js.php';
include '../include/dyn_php.php';
?>
(function () {

<?php
echo "var mod_navFrame_target = '".$e_target[0]."';";
echo "var mod_navFrame_id = '".$e_id."';";
echo "var mod_navFrame_src = '".$e_src."';";

?>

mod_navFrame_ext = typeof mod_navFrame_ext === "undefined" ? 10 : mod_navFrame_ext;

<?php
$html = <<<HTML
<div id="mbN_$e_id" style="position:absolute;border:0px;width:0;height:0;top:0;left:0;" class="ui-state-default">
<img id="arrow_n_$e_id" title="move map to north" style="position:relative;top:0;left:0;" src="../img/arrows/arrow_n.gif" width="15" height="10"  />
</div> 
<div id="mbNE_$e_id" style="position:absolute;border:0px;width:0;height:0;top:0;left:0;" class="ui-state-default">
<img id="arrow_ne_$e_id" title="move map to north-east" style="position:relative;top:0;left:0;" src="../img/arrows/arrow_ne.gif" width="10" height="10" />
</div> 
<div id="mbE_$e_id" style="position:absolute;border:0px;width:0;height:0;top:0;left:0;" class="ui-state-default">
<img id="arrow_e_$e_id" title="move map to east"style="position:relative;top:0;left:0;" src="../img/arrows/arrow_e.gif" width="10" height="15" />
</div> 
<div id="mbSE_$e_id" style="position:absolute;border:0px;width:0;height:0;top:0;left:0;" class="ui-state-default">
<img id="arrow_se_$e_id" title="move map to south-east" style="position:relative;top:0;left:0;" src="../img/arrows/arrow_se.gif" width="10" height="10"  />
</div> 
<div id="mbS_$e_id" style="position:absolute;border:0px;width:0;height:0;top:0;left:0;" class="ui-state-default">
<img id="arrow_s_$e_id" title="move map to south" style="position:relative;top:0;left:0;" src="../img/arrows/arrow_s.gif" width="15" height="10"  />
</div> 
<div id="mbSW_$e_id" style="position:absolute;border:0px;width:0;height:0;top:0;left:0;" class="ui-state-default">
<img id="arrow_sw_$e_id" title="move map to south-west" style="position:relative;top:0;left:0;" src="../img/arrows/arrow_sw.gif" width="10" height="10"  />
</div>
<div id="mbW_$e_id" style="position:absolute;border:0px;width:0;height:0;top:0;left:0;" class="ui-state-default">
<img id="arrow_w_$e_id" title="move map to west" style="position:relative;top:0;left:0;" src="../img/arrows/arrow_w.gif" width="10" height="15" />
</div> 
<div id="mbNW_$e_id" style="position:absolute;border:0px;width:0;height:0;top:0;left:0;" class="ui-state-default">
<img id="arrow_nw_$e_id" title="move map to north-west" style="position:relative;top:0;left:0;" src="../img/arrows/arrow_nw.gif" width="10" height="10" />
</div>
HTML;
echo "var html = '" . str_replace("\n", "\\n", $html) . "';";
?>
	$("#" + mod_navFrame_id).html(html).children("div").css("cursor", "pointer").hover(
		function () {
			$(this).addClass("ui-state-hover");
		},
		function () {
			$(this).removeClass("ui-state-hover");
		}
	);
	
eventInit.register(function () {
	var directionArray = ["N", "NE", "E", "SE", "S", "SW", "W", "NW"];
	for (var i in directionArray) {
		(function () {
			var currentDirection = directionArray[i];
			$("#mb"+currentDirection+"_"+mod_navFrame_id).click(function () {
//			$("#arrow_"+currentDirection.toLowerCase()+"_"+mod_navFrame_id).click(function () {
				mod_navFrame(currentDirection);
			});
		}());
	}
});

eventAfterMapRequest.register(function () {
	mod_navFrame_arrange();
});


function  mod_navFrame_arrange(){
	var el = document.getElementById(mod_navFrame_target).style;
	var ext = mod_navFrame_ext;
	var myLeft = parseInt(el.left, 10);
	var myTop = parseInt(el.top, 10);
	var myWidth = parseInt(el.width, 10);
	var myHeight = parseInt(el.height, 10);
	
	//left,top,width,height
	mod_navFrame_pos("mbN_"+mod_navFrame_id,(myLeft),(myTop - ext),(myWidth),(ext));
	document.getElementById("arrow_n_"+mod_navFrame_id).style.left = (myWidth/2 - parseInt(document.getElementById("arrow_n_"+mod_navFrame_id).width, 10)/2) + "px";   
	mod_navFrame_pos("mbNE_"+mod_navFrame_id,(myLeft + myWidth),(myTop - ext),(ext),(ext));
	mod_navFrame_pos("mbE_"+mod_navFrame_id,(myLeft + myWidth),(myTop),(ext),(myHeight));
	document.getElementById("arrow_e_"+mod_navFrame_id).style.top = (myHeight/2 - parseInt(document.getElementById("arrow_n_"+mod_navFrame_id).height, 10)/2) + "px";
	mod_navFrame_pos("mbSE_"+mod_navFrame_id,(myLeft + myWidth),(myTop + myHeight),(ext),(ext));
	mod_navFrame_pos("mbS_"+mod_navFrame_id,(myLeft),(myTop + myHeight),(myWidth),(ext));
	document.getElementById("arrow_s_"+mod_navFrame_id).style.left = (myWidth/2 - parseInt(document.getElementById("arrow_s_"+mod_navFrame_id).width, 10)/2) + "px";
	mod_navFrame_pos("mbSW_"+mod_navFrame_id,(myLeft - ext),(myTop + myHeight),(ext),(ext));
	mod_navFrame_pos("mbW_"+mod_navFrame_id,(myLeft - ext),(myTop),(ext),(myHeight));
	document.getElementById("arrow_w_"+mod_navFrame_id).style.top = (myHeight/2 - parseInt(document.getElementById("arrow_w_"+mod_navFrame_id).height, 10)/2) + "px";
	mod_navFrame_pos("mbNW_"+mod_navFrame_id,(myLeft - ext),(myTop -ext),(ext),(ext));   
}
function mod_navFrame(val){
	mb_panMap(mod_navFrame_target,val);  
}
function mod_navFrame_pos(el,left,top,width,height){
	document.getElementById(el).style.left = left + "px";
	document.getElementById(el).style.top = top + "px";
	document.getElementById(el).style.width = width + "px";
	document.getElementById(el).style.height = height + "px";
}
}());
