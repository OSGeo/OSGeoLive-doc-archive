<?php
# $Id: mod_legend.php 8411 2012-07-04 14:58:57Z astrid_emde $
# http://www.mapbender.org/index.php/Legend
# Copyright (C) 2005 CCGIS / terrestris 
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

$e_id = "legend";
$e_id_css = "legend";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Legend</title>
<?php
include '../include/dyn_css.php';
$sql = "SELECT DISTINCT e_target, e_width, e_height FROM gui_element WHERE e_id = $1 AND fkey_gui_id = $2";
$v = array($e_id, $gui_id);
$t = array('s', 's');
$res = db_prep_query($sql, $v, $t);
$cnt = 0;
while($row = db_fetch_array($res)){    
	$e_target = $row["e_target"];
	$cnt++;
}
if($cnt > 1){
	echo "alert('legend: ID not unique!');";
}

$sql2 = "SELECT e_left,e_top,e_width,e_height FROM gui_element WHERE e_id = $1 AND fkey_gui_id = $2";
$v = array($e_target, $gui_id);
$t = array('s','s');
$res2 = db_prep_query($sql2, $v, $t);
echo "<script type='text/javascript'>\n";
echo "var mod_legend_target = '".$e_target."';\n";
echo "var mod_legend_target_left = ".intval(db_result($res2,0,"e_left")).";\n";
echo "var mod_legend_target_top = ".intval(db_result($res2,0,"e_top")).";\n";
//echo "var mod_legend_target_width = ".db_result($res2,0,"e_width").";\n";
//echo "var mod_legend_target_height = ".db_result($res2,0,"e_height").";\n";
echo "</script>\n";
?>

<script type="text/javascript">
<!--

mod_legend_offsetLeft = 25;
mod_legend_offsetTop = -10;

var checkbox_on_off = typeof checkbox_on_off === "undefined" ? 'false' : checkbox_on_off;
var stickylegend = typeof stickylegend === "undefined" ? "false" : stickylegend;
var reverseLegend = typeof reverseLegend === "undefined" ? "false" : reverseLegend;
var exclude = exclude || [];

function array_contains(hay,needle){
    for(var i = 0; i < hay.length; i++ ){
        if (hay[i] == needle){
            return true
        }
    } 
    return false;
}

function mod_legend_pos(frameName){
	if(frameName == mod_legend_target){
		var ind = parent.getMapObjIndexByName(mod_legend_target);
		var obj = parent.document.getElementById("legend");
	
		if(stickylegend == 'true'){
			obj.style.left = (parseInt(parent.mb_mapObj[ind].width) + mod_legend_target_left + mod_legend_offsetLeft) + "px";
		}
	
		if(document.forms[0].sw.checked ==  false){
			parent.writeTag("legend", "leg", "");
			return true;
		}
		
		var str = "";
		var str_tmp = "";

		if(reverseLegend == 'true') {
			for(var i=parent.mb_mapObj[ind].wms.length-1; i>=0; i--){
                if (array_contains(exclude,parent.mb_mapObj[ind].wms[i].wms_id)){
                    continue;
                }
			 	var layerNames = parent.mb_mapObj[ind].wms[i].getLayers(parent.mb_mapObj[ind]);
				for(var j=0; j<layerNames.length; j++){
					var layerParent = parent.mb_mapObj[ind].wms[i].checkLayerParentByLayerName(layerNames[j]); 
					var layerTitle = parent.mb_mapObj[ind].wms[i].getTitleByLayerName(layerNames[j]);
					var layerStyle = parent.mb_mapObj[ind].wms[i].getCurrentStyleByLayerName(layerNames[j]);
					var legendUrl = false;
					if(layerStyle == false){
						legendUrl = parent.mb_mapObj[ind].wms[i].getLegendUrlByGuiLayerStyle(layerNames[j],"");
						
						//alert("mapObj ind: "+ind+" wms index: "+i+"layer name: "+layerNames[j]+" : LegendUrl (style false): "+legendUrl);	
					}
					else{
						legendUrl = parent.mb_mapObj[ind].wms[i].getLegendUrlByGuiLayerStyle(layerNames[j],layerStyle);
					}
			         
					if (legendUrl !== false){
						
//	 					if(layerParent == 0){
		 					if(showlayertitle == 'true'){
								str_tmp += "<div><span class='titles'>" + layerTitle+ "</span></div>";
							}
							str_tmp += "<div>";
		                    if(legendlink == 'true'){
		                    	str_tmp += "<a href='../php/result.php?lingo=deutsch&layer="+layerNames[j]+"' class='link_metadata' title='Zeigt Liste zum Thema: "+layerTitle+"' target='result'>";
		                    }
				
		                    str_tmp += "<img border=0 src = '";			
							str_tmp += legendUrl;
							str_tmp += "'></img>";
							if (legendlink == 'true'){
		                    	str_tmp += "</a>";
		                    }
		                    str_tmp += "</div>";
//	                	}
					} 
				}
				if(str_tmp !=''){
					if (showwmstitle == 'true'){
						str += "<div><span class='header'>" +parent.mb_mapObj[ind].wms[i].wms_title+ "</span></div>";
					}
					str += str_tmp;
					str_tmp ='';
				}
			}	
		}
		else {
			for(var i=0; i<parent.mb_mapObj[ind].wms.length; i++){
                    if (array_contains(exclude,parent.mb_mapObj[ind].wms[i].wms_id)){
                        continue;
                    }
				 	var layerNames = parent.mb_mapObj[ind].wms[i].getLayers(parent.mb_mapObj[ind]);
					for(var j=0; j<layerNames.length; j++){
						var layerParent = parent.mb_mapObj[ind].wms[i].checkLayerParentByLayerName(layerNames[j]); 
						var layerTitle = parent.mb_mapObj[ind].wms[i].getTitleByLayerName(layerNames[j]);
						var layerStyle = parent.mb_mapObj[ind].wms[i].getCurrentStyleByLayerName(layerNames[j]);
						var legendUrl = false;
						if(layerStyle == false){
							legendUrl = parent.mb_mapObj[ind].wms[i].getLegendUrlByGuiLayerStyle(layerNames[j],"");
							
							//alert("mapObj ind: "+ind+" wms index: "+i+"layer name: "+layerNames[j]+" : LegendUrl (style false): "+legendUrl);	
						}
						else{
							legendUrl = parent.mb_mapObj[ind].wms[i].getLegendUrlByGuiLayerStyle(layerNames[j],layerStyle);
						}
				         
						if (legendUrl !== false){
							
	//	 					if(layerParent == 0){
			 					if(showlayertitle == 'true'){
									str_tmp += "<div><span class='titles'>" + layerTitle+ "</span></div>";
								}
								str_tmp += "<div>";
			                    if(legendlink == 'true'){
			                    	str_tmp += "<a href='../php/result.php?lingo=deutsch&layer="+layerNames[j]+"' class='link_metadata' title='Zeigt Liste zum Thema: "+layerTitle+"' target='result'>";
			                    }
					
			                    str_tmp += "<img border=0 src = '";			
								str_tmp += legendUrl;
								str_tmp += "'></img>";
								if (legendlink == 'true'){
			                    	str_tmp += "</a>";
			                    }
			                    str_tmp += "</div>";
	//	                	}
						} 
					}
				if(str_tmp !=''){
					if (showwmstitle == 'true'){
						str += "<div><span class='header'>" +parent.mb_mapObj[ind].wms[i].wms_title+ "</span></div>";
					}
					str += str_tmp;
					str_tmp ='';
				}		
			}
		}

	    if(str != ""){
			parent.writeTag("legend", "leg", str);
		}
		else{
			parent.writeTag("legend", "leg", "");
		}
		return true;
	}
} 

// Todo: return-value may change in the next version....
parent.eventAfterMapRequest.register(function (obj) {
	mod_legend_pos(obj.map.elementName)
});


function mod_legend_init(){
	var obj = parent.document.getElementById("legend");
//	obj.style.top = mod_legend_target_top  + mod_legend_offsetTop;
//	obj.style.left = mod_legend_target_left + mod_legend_target_width + mod_legend_offsetLeft; 
	var checkobj = document.getElementById("checkboxstyle");
	if (checkbox_on_off == 'false'){
		checkobj.style.display = "none";
		checkobj.style.width = 0;
		checkobj.style.height = 0;
	}
	if (parent.Mapbender.events.init.done) {
		mod_legend_pos(mod_legend_target);
	}
	else {
		parent.Mapbender.events.init.register(function () {
			mod_legend_pos(mod_legend_target);
		});
	}
}

function mod_legend_repaint(obj){
	var checkobj = document.getElementById("checkboxstyle");
	if(checkobj.style.display == "block"){
		mod_legend_pos(mod_legend_target);     
	}
}

// -->
</script>

</head>
<body onload='mod_legend_init()'>
<form>

<span class='switch'>
<div id="checkboxstyle" style="display:block;">on/off<input type='checkbox' name='sw' checked='true' onclick='mod_legend_repaint(this)'></div>
</span>
<div name='leg' id='leg' ></div>

</form>
</body>
</html>
