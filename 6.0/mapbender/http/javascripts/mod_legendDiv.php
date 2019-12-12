<?php
# $Id: mod_legend.php 6887 2010-09-03 14:51:46Z christoph $
# http://www.mapbender.org/index.php/Legend
# Copyright (C) 2005 CCGIS / terrestris 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
/*
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('<app_id>','legend',2,1,'legend','Legend','div','','',0,0,NULL ,NULL ,600,'','','div','../javascripts/mod_legendDiv.php','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<app_id>', 'legend', 'legendlink', 'false', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<app_id>', 'legend', 'showgroupedlayertitle', 'true', 'show the title of the grouped layers in the legend' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<app_id>', 'legend', 'showlayertitle', 'true', 'show the layer title in the legend' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('<app_id>', 'legend', 'showwmstitle', 'true', 'show the wms title in the legend' ,'var');

*/
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

$e_target = "mapframe1";
$e_top = '10';
$e_left = '10';

echo "var mod_legend_target = '".$e_target."';\n";
echo "var mod_legend_target_left = ".intval($e_left).";\n";
echo "var mod_legend_target_top = ".intval($e_top).";\n";

include '../include/dyn_js.php';
//defaults for element vars
?>
mod_legend_offsetLeft = 25;
mod_legend_offsetTop = -10;

var legendlink = typeof legendlink === "undefined" ? 'false' : legendlink;
var showwmstitle = typeof showwmstitle === "undefined" ? "false" : showwmstitle;
var showlayertitle = typeof showlayertitle === "undefined" ? "false" : showlayertitle;
var showgroupedlayertitle = typeof showgroupedlayertitle === "undefined" ? "false" : showgroupedlayertitle;
var reverseLegend = typeof reverseLegend === "undefined" ? "false" : reverseLegend;

function mod_legend_pos(frameName){
	if(frameName == mod_legend_target){
		var ind = getMapObjIndexByName(mod_legend_target);
		var obj = document.getElementById("legend");
	
		var str = "";
		var str_tmp = "";
		
		if(reverseLegend == 'true') {
			for(var i=mb_mapObj[ind].wms.length-1; i>=0; i--){
				 	var layerNames = mb_mapObj[ind].wms[i].getLayers(mb_mapObj[ind]);
					for(var j=0; j<layerNames.length; j++){
						var layerParent = mb_mapObj[ind].wms[i].checkLayerParentByLayerName(layerNames[j]); 
						var layerTitle = mb_mapObj[ind].wms[i].getTitleByLayerName(layerNames[j]);
						var layerStyle = mb_mapObj[ind].wms[i].getCurrentStyleByLayerName(layerNames[j]);
						var legendUrl = false;
						if(layerStyle == false){
							legendUrl = mb_mapObj[ind].wms[i].getLegendUrlByGuiLayerStyle(layerNames[j],"");	
						}
						else{
							legendUrl = mb_mapObj[ind].wms[i].getLegendUrlByGuiLayerStyle(layerNames[j],layerStyle);
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
						str += "<div><span class='header'>" +mb_mapObj[ind].wms[i].wms_title+ "</span></div>";
					}
					str += str_tmp;
					str_tmp ='';
				}		
			}
		}
		else {
			for(var i=0; i<mb_mapObj[ind].wms.length; i++){
				 	var layerNames = mb_mapObj[ind].wms[i].getLayers(mb_mapObj[ind]);
					for(var j=0; j<layerNames.length; j++){
						var layerParent = mb_mapObj[ind].wms[i].checkLayerParentByLayerName(layerNames[j]); 
						var layerTitle = mb_mapObj[ind].wms[i].getTitleByLayerName(layerNames[j]);
						var layerStyle = mb_mapObj[ind].wms[i].getCurrentStyleByLayerName(layerNames[j]);
						var legendUrl = false;
						if(layerStyle == false){
							legendUrl = mb_mapObj[ind].wms[i].getLegendUrlByGuiLayerStyle(layerNames[j],"");	
						}
						else{
							legendUrl = mb_mapObj[ind].wms[i].getLegendUrlByGuiLayerStyle(layerNames[j],layerStyle);
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
						str += "<div><span class='header'>" +mb_mapObj[ind].wms[i].wms_title+ "</span></div>";
					}
					str += str_tmp;
					str_tmp ='';
				}		
			}
		}				

	    if(str != ""){
			writeTag("", "legend", str);
		}
		else{
			writeTag("", "legend", "");
		}
		return true;
	}
} 

// Todo: return-value may change in the next version....
eventAfterMapRequest.register(function (obj) {
	mod_legend_pos(obj.map.elementName)
});


function mod_legend_init(){
	var obj = document.getElementById("legend");
//	obj.style.top = mod_legend_target_top  + mod_legend_offsetTop;
//	obj.style.left = mod_legend_target_left + mod_legend_target_width + mod_legend_offsetLeft; 
//	var checkobj = document.getElementById("checkboxstyle");
//	if (checkbox_on_off == 'false'){
//		checkobj.style.display = "none";
//		checkobj.style.width = 0;
//		checkobj.style.height = 0;
//	}
	if (Mapbender.events.init.done) {
		mod_legend_pos(mod_legend_target);
	}
	else {
		Mapbender.events.init.register(function () {
			mod_legend_pos(mod_legend_target);
		});
	}
}

