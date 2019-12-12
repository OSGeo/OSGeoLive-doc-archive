<?php
# $Id: mod_treefolder2.php 2975 2008-09-18 12:58:42Z nimix $
# http://www.mapbender.org/index.php/Mod_treefolder2.php
# Copyright (C) 2007 Melchior Moos
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
?>
function openwindow (Adresse) {
	Fenster1 = window.open(Adresse, '<?php echo _mb("Information");?>', "width=500,height=500,left=100,top=100,scrollbars=yes,resizable=yes");
	Fenster1.focus();
}
<?php
	echo "mod_treeGDE_map = '".$e_target[0]."';";
?>
var msgObj = {};
msgObj.tooltipHandleWms = '<?php echo _mb("(De)activate this service");?>';
msgObj.tooltipLayerVisible = '<?php echo _mb("Toggles the visibility of this service");?>';
msgObj.tooltipLayerQuerylayer = '<?php echo _mb("Toggles the queryability of this service");?>';
msgObj.tooltipLayerContextMenu = '<?php echo _mb("Opens the layer context menu");?>';
msgObj.tooltipWmsContextMenu = '<?php echo _mb("Opens the WMS context menu");?>';
msgObj.tooltipRemoveWms = '<?php echo _mb("Removes the selected WMS");?>';
msgObj.tooltipMoveSelectionUp = '<?php echo _mb("Moves the selection up");?>';
msgObj.tooltipMoveSelectionDown = '<?php echo _mb("Moves the selection down");?>';
msgObj.tooltipMetadata = '<?php echo _mb("Show metadata");?>';

if (typeof(localizetree) === 'undefined')localizetree = 'false';

function localizeTree () {
	var treefolderTitleArray = [];
	var map = Mapbender.modules[mod_treeGDE_map];

	if (map === null) {
		return;
	}
	for(var i = 0; i < map.wms.length; i++) {
		var currentWms = map.wms[i];

		treefolderTitleArray.push({
			title : currentWms.objLayer.length > 0 ?
				currentWms.objLayer[0].gui_layer_title :
				currentWms.wms_title,
			layer : []
		});

		for (var j = 0; j < currentWms.objLayer.length; j++) {
			var currentLayer = currentWms.objLayer[j];
			treefolderTitleArray[treefolderTitleArray.length-1].layer.push({
				title : currentLayer.gui_layer_title
			});
		}
	}

	var req = new Mapbender.Ajax.Request({
		url: "../php/mod_treefolder2_l10n.php",
		method: "translateServiceData",
		parameters: {
			data: treefolderTitleArray
		},
		callback: function (obj, success, message) {
			var translatedTitleArray = obj;
			for (var i = 0; i < translatedTitleArray.length; i++) {
				var currentWms = map.wms[i];
				currentWms.wms_currentTitle = translatedTitleArray[i].title;

				for(var j = 0; j < currentWms.objLayer.length; j++) {
					var currentLayer = currentWms.objLayer[j];
					if (translatedTitleArray[i].layer.length > j) {
						currentLayer.layer_currentTitle = translatedTitleArray[i].layer[j].title;
					}
				}
			}
			reloadTree();
		}
	});
	req.send();
}
/*
eventInit.register(function () {
	localizeTree();
});
*/

if (localizetree == 'true') {
	eventLocalize.register(function () {
		localizeTree();
	});
}

var jst_container = "document.getElementById('treeContainer')";
var jst_image_folder = imagedir;
var jst_display_root = false;
var defaultTarget = 'examplemain';
var lock=false;
var lock_update=false;
var lock_check=false;
var lock_maprequest = false; //global var to prohibit multiple saving states as wmc to session when loading wmc 
var selectedMap=-1;
var selectedWMS=-1;
var selectedLayer=-1;
var initialized=false;
var errors = 0;
var state=Array();
var treeState = "";
<?php
//load structure
$sql = "SELECT * FROM gui_treegde WHERE fkey_gui_id = $1 AND NOT lft = 1 ORDER BY lft;";
$v = array(Mapbender::session()->get("mb_user_gui"));
$t = array("s");
$res = db_prep_query($sql, $v, $t);

//init tree converting arrays
$nr = array(); 			//array for nested sets numbers
$str = array();			//array for js array elements
$categories = array();	//array for wms folders
$path = array();		//stack for actual path elements
$rights = array();		//stack for rights of open elements

//build javascript data array for jsTree
while($row = db_fetch_array($res)){
	//push javascript array elements to a single array with lefts and rights
	$left = "['folder_".$row['id']."', ['".$row['my_layer_title']."', 'javascript:_foo()'],[";
	$right = "]],";
	array_push($nr, $row['lft']);
	array_push($str, $left);
	array_push($nr, $row['rgt']);
	array_push($str, $right);

	//finish all nodes that have no further childs
	while(count($rights) > 0 && $rights[count($rights)-1]<$row['lft']){
		array_pop($rights);
		array_pop($path);
	}

	//set path for each wms that is referenced in this folder
	array_push($rights, $row['rgt']);
	array_push($path, "folder_".$row['id']);
	if($row['wms_id']!=""){
		foreach(explode(",",$row['wms_id']) as $wms){
			array_push($categories, "'wms_".$wms."':\"root_id|".implode("|", $path)."\"");
		}
	}
}
//if we have a configured structure output it
if(count($str)>0){
	//order js array elements
	array_multisort($nr, $str);

	//output javascript vars
	$arrNodesStr = "[['root_id', ['Layer','javascript:_foo()'],[".implode("",$str)."]]];";
	$arrNodesStr = str_replace(array("[]", ",]"),array("","]"),$arrNodesStr);
	echo "var arrNodesStr = \"".$arrNodesStr."\";\n";
	echo "var categories = {".implode(",", $categories)."};\n";
}
else{
//if there is no structure take default
?>
var arrNodesStr = "[['root_id', ['Layer','javascript:_foo()']]];";
var categories = {};
<?php
}
?>
var arrNodes = eval(arrNodesStr);
function _foo(){selectedMap=-1;selectedWMS=-1;selectedLayer=-1}

// some defaults
if (typeof(reverse) === 'undefined')reverse = 'false';
if (typeof(switchwms) === 'undefined')switchwms = 'true';
if (typeof(ficheckbox) === 'undefined')ficheckbox = 'false';
if (typeof(metadatalink) === 'undefined')metadatalink = 'false';
if (typeof(wmsbuttons) === 'undefined')wmsbuttons = 'false';
if (typeof(showstatus) === 'undefined')showstatus = 'false';
if (typeof(alerterror) === 'undefined')alerterror = 'false';
if (typeof(openfolder) === 'undefined')openfolder = 'false';
if (typeof(handlesublayer) === 'undefined')handlesublayer = 'false';
if (typeof(enlargetreewidth) === 'undefined') enlargetreewidth = 'false';
if (typeof(enlargetreewidthopacity) === 'undefined') enlargetreewidthopacity = 'false';
if (typeof(menu) === 'undefined')menu = '';
if (typeof(redirectToMetadataUrl) !== 'undefined' && redirectToMetadataUrl == "false") {
	redirectToMetadataUrl = false;
}
else {
	redirectToMetadataUrl = true;
}

var defaultMetadataUrl = '../php/mod_showMetadata.php?resource=layer&layout=tabs&';
if (redirectToMetadataUrl) {
	defaultMetadataUrl += 'redirectToMetadataUrl=1';
}
else {
	defaultMetadataUrl += 'redirectToMetadataUrl=0';
}

//menu elements
var menu_move_up = ['menu_move_up', ['<?php echo _mb("Move up");?>&nbsp;','javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];move_up(ids[0],ids[1],ids[2]);',,'move_up.png']];
var menu_move_down = ['menu_move_down', ['<?php echo _mb("Move down");?>&nbsp;', 'javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];move_down(ids[0],ids[1],ids[2]);',,'move_down.png']];
var menu_delete = ['menu_delete', ['<?php echo _mb("Remove");?>&nbsp;', 'javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];remove_wms(ids[0],ids[1],ids[2]);',,'delete_wms.png']];
var menu_opacity_up = ['menu_opacity_up', ['<?php echo _mb("Opacity up");?>&nbsp;','javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];opacity_up(ids[0],ids[1],ids[2]);',,'move_up.png']];
var menu_opacity_down = ['menu_opacity_down', ['<?php echo _mb("Opacity down");?>&nbsp;','javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];opacity_down(ids[0],ids[1],ids[2]);',,'move_down.png']];
var menu_metalink = ['menu_metalink', ['<?php echo _mb("Information");?>&nbsp;', 'javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];openwindow(defaultMetadataUrl + "&id="+parent.mb_mapObj[ids[0]].wms[ids[1]].objLayer[ids[2]].layer_uid);',,'info.png']];
var menu_zoom = ['menu_zoom', ['<?php echo _mb("Zoom");?>&nbsp;', 'javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];zoomToLayer(ids[0],ids[1],ids[2]);',,'zoom.png']];
var menu_hide = ['menu_hide', ['<?php echo _mb("Hide menu");?>&nbsp;', 'javascript:hideMenu()',,'hide.png']];
var menu_style = ['menu_style', ['<?php echo _mb("Change style");?>&nbsp;', 'javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];openStyleDialog(ids[0],ids[1],ids[2])',,'palette.png']];
var menu_legend = ['menu_legend', ['<?php echo _mb("Legende öffnen");?>&nbsp;', 'javascript:var sd = "{@strData}";var ids=eval(sd.substr(0, sd.length-6))[1][7];openLegendHtml(ids[0],ids[1],ids[2])',,'legend_tree.png']];
//var menu_wms_switch = ['menu_zoom', ['<?php echo _mb("Zoom");?>&nbsp;', 'javascript:var sd = "{@strData}";eval(eval(sd.substr(0, sd.length-6))[1][1]);openwindow(defaultMetadataUrl + "&id="+parent.mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].layer_uid);',,'info.png']];
//var menu_layer_switch = ['menu_zoom', ['Zjjj&nbsp;', 'javascript:var sd = "{@strData}";eval(eval(sd.substr(0, sd.length-6))[1][1]);openwindow(defaultMetadataUrl + "&id="+parent.mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].layer_uid);',,'info.png']];
//var menu_info_switch = ['menu_zoom', ['Zmn&nbsp;', 'javascript:var sd = "{@strData}";eval(eval(sd.substr(0, sd.length-6))[1][1]);openwindow(defaultMetadataUrl + "&id="+parent.mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].layer_uid);',,'info.png']];

//parent.eventMapRequestFailed.register(function(t){imgerror(t)});

eventAfterLoadWMS.register(reloadTree);
eventAfterLoadWMS.register(registerInitMapCheck);
Mapbender.events.init.register(function () {
	$("#" + mod_treeGDE_map).mapbender().events.afterMoveWms.register(reloadTree);
});
eventInit.register(loadTree);
if(showstatus=='true'||alerterror=='true'){
	//eventAfterMapRequest.register(init_mapcheck);
	//init_mapcheck();
}
eventAfterMapRequest.register(updateScale);
eventAfterMapRequest.register(updateCheckState);

function registerInitMapCheck() {
	if(showstatus=='true'||alerterror=='true'){
		//console.log("Registrierung init_mapcheck an eventAfterMapRequest");
		eventAfterMapRequest.register(init_mapcheck);
	}
}

if (enlargetreewidth) {
    eventAfterInit.register(function(){

        var initialWidth = parseInt($('#treeGDE').css("width"));
        $('#treeGDE').bind("mouseenter", function(){
            $(this).css({
                'width': initialWidth + enlargetreewidth,
                'zIndex': '300'
            });
		if (enlargetreewidthopacity) {
			$(this).css({
				'-moz-opacity': '0.8',
				'opacity': '0.8',
				'filter': 'alpha(opacity=80)'
            		});
		}
            $(this).mousewheel();
        });
        $('#treeGDE').bind("mouseleave", function(){
            $(this).css({
                'width': initialWidth,
                'zIndex': '3'
            });
		if (enlargetreewidthopacity) {
			$(this).css({
				'-moz-opacity': '1',
				'opacity': '1',
				'filter': 'alpha(opacity=100)'
            		});
		}
        });
    });
}

if(wmsbuttons != "true")
	jst_highlight = false;

function select(i,ii,iii){
	//ignore if selected
	if(selectedMap==i && selectedWMS==ii && selectedLayer==iii)return;
	if(selectedMap==-1 && selectedWMS==-1 && selectedLayer==-1){
		selectedMap=i;
		selectedWMS=ii;
		selectedLayer=iii;
		return;
	}
	//scalehints
	var scale = parseInt(mb_getScale(mod_treeGDE_map));
	if(scale < parseInt(mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].gui_layer_minscale) && parseInt(mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].gui_layer_minscale) != 0){
		if(selectedLayer==0)
		   	setNodeColor(arrNodes[0][0]+"|wms_"+ mb_mapObj[selectedMap].wms[selectedWMS].wms_id, '#999999');
		else
		   	setNodeColor(arrNodes[0][0]+"|wms_"+ mb_mapObj[selectedMap].wms[selectedWMS].wms_id+"|"+ mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].layer_id, '#999999');
	}
	else if(scale > parseInt( mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].gui_layer_maxscale) && parseInt( mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].gui_layer_maxscale) != 0){
		if(selectedLayer==0)
		   	setNodeColor(arrNodes[0][0]+"|wms_"+ mb_mapObj[selectedMap].wms[selectedWMS].wms_id, '#999999');
		else
		   	setNodeColor(arrNodes[0][0]+"|wms_"+ mb_mapObj[selectedMap].wms[selectedWMS].wms_id+"|"+ mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].layer_id, '#999999');
	}
	else{
		if(selectedLayer==0)
		   	setNodeColor(arrNodes[0][0]+"|wms_"+ mb_mapObj[selectedMap].wms[selectedWMS].wms_id, '');
		else
		   	setNodeColor(arrNodes[0][0]+"|wms_"+ mb_mapObj[selectedMap].wms[selectedWMS].wms_id+"|"+ mb_mapObj[selectedMap].wms[selectedWMS].objLayer[selectedLayer].layer_id, '');
	}

	selectedMap=i;
	selectedWMS=ii;
	selectedLayer=iii;
}

function updateScale(){
	if(!initialized)return;
	myMapObj = getMapObjByName(mod_treeGDE_map);
	if(myMapObj){
		var scale = parseInt( myMapObj.getScale());
		for(var ii=0; ii< myMapObj.wms.length; ii++){
			for(var iii=1; iii< myMapObj.wms[ii].objLayer.length; iii++){
				if(scale < parseInt( myMapObj.wms[ii].objLayer[iii].gui_layer_minscale) && parseInt( myMapObj.wms[ii].objLayer[iii].gui_layer_minscale) != 0){
						if(iii==0)
					   	setNodeColor(arrNodes[0][0]+"|wms_"+ myMapObj.wms[ii].wms_id, '#999999');
						else
					   	setNodeColor(arrNodes[0][0]+"|wms_"+ myMapObj.wms[ii].wms_id+"|"+ myMapObj.wms[ii].objLayer[iii].layer_id, '#999999');
					}
				else if(scale > parseInt( myMapObj.wms[ii].objLayer[iii].gui_layer_maxscale) && parseInt( myMapObj.wms[ii].objLayer[iii].gui_layer_maxscale) != 0){
						if(iii==0)
					   	setNodeColor(arrNodes[0][0]+"|wms_"+ myMapObj.wms[ii].wms_id, '#999999');
						else
					   	setNodeColor(arrNodes[0][0]+"|wms_"+ myMapObj.wms[ii].wms_id+"|"+ myMapObj.wms[ii].objLayer[iii].layer_id, '#999999');
					}
					else{
						if(iii==0)
					   	setNodeColor(arrNodes[0][0]+"|wms_"+ myMapObj.wms[ii].wms_id, '');
						else
					   	setNodeColor(arrNodes[0][0]+"|wms_"+ myMapObj.wms[ii].wms_id+"|"+ myMapObj.wms[ii].objLayer[iii].layer_id, '');
					}
				}
			}
		}
	}

function updateCheckState(){
	if(!initialized||lock_check)return;
	lock_check=true;
	var map = getMapObjByName(mod_treeGDE_map);
	for(var i=0; i< mb_mapObj.length; i++){
		var scale = parseInt( map.getScale());
		if( mb_mapObj[i].elementName == mod_treeGDE_map){
			for(var ii=0; ii< mb_mapObj[i].wms.length; ii++){
				for(var iii=1; iii< mb_mapObj[i].wms[ii].objLayer.length; iii++){
					if(! mb_mapObj[i].wms[ii].objLayer[iii].has_childs){
						path = arrNodes[0][0]+"|wms_"+ mb_mapObj[i].wms[ii].wms_id+"|"+ mb_mapObj[i].wms[ii].objLayer[iii].layer_id;
						checkNode(path, 0,  mb_mapObj[i].wms[ii].objLayer[iii].gui_layer_visible==='1'||mb_mapObj[i].wms[ii].objLayer[iii].gui_layer_visible===1, false);
						if(ficheckbox == 'true')
							checkNode(path, 1,  mb_mapObj[i].wms[ii].objLayer[iii].gui_layer_querylayer=='1', false);
					}
				}
			}
		}
	}
	lock_check=false;
}

function operaLoad(){
	initArray();
	renderTree();
	setTimeout('initWmsCheckboxen();updateScale();',100);
}

function loadTree(){
	if(wmsbuttons=='true'){
		var div = document.createElement("div");
		div.innerHTML = '<a href="javascript:move_up()"><img title="'+msgObj.tooltipMoveSelectionUp+'" src="'+imagedir+'/move_up.png" alt="move up" style="position:relative;top:0px;left:0px;"/></a><a href="javascript:move_down()"><img title="'+msgObj.tooltipMoveSelectionDown+'" src="'+imagedir+'/move_down.png" alt="move down" style="position:relative;top:0px;left:-3px"/></a><a href="javascript:remove_wms()"><img title="'+msgObj.tooltipRemoveWms+'" src="'+imagedir+'/delete_wms.png" alt="remove wms" style="position:relative;top:0px;left:-6px"/></a>';
		document.getElementById("treeGDE").appendChild(div);
	}
	var div = document.createElement("div");
	div.id = "treeContainer"
	document.getElementById("treeGDE").appendChild(div);

	if(window.opera){
		setTimeout('operaLoad()',200);
		return;
	}
	initArray();
	renderTree();
	initWmsCheckboxen();
	updateScale();
}

function reloadTree(){
	if(!initialized) return;
	selectedMap=-1;
	selectedWMS=-1;
	selectedLayer=-1;
	initialized=false;
	arrNodes = eval(arrNodesStr)
	initArray();
	if(showstatus=='true'||alerterror=='true')
		//init_mapcheck();
	renderTree();
	if(window.opera)
		setTimeout('initWmsCheckboxen();updateScale();',100);
	else{
		initWmsCheckboxen();
		updateScale();
	}
}

function imgerror(t){
	var map= getMapObjIndexByName(mod_treeGDE_map);
	var wms=Number(t.id.substr(4));
	t.onerror=null;
	t.onabort=null;
	if(state[wms]!=-1 && alerterror=='true'){
		state[wms]=-1;
		if(confirm('<?php echo _mb("Failed to load WMS");?> ' +
			mb_mapObj[map].wms[wms].objLayer[0].layer_currentTitle +
			'\n<?php echo _mb("Do you want to try to load it in a new window?");?>')) {
			window.open(t.src,"");
		}
	}
	state[wms]=-1;
	errors++;
	if(showstatus=='true')
		setNodeImage(arrNodes[0][0]+"|wms_"+ mb_mapObj[map].wms[wms].wms_id, "error_folder.png");
}

function checkComplete(wms, map, img, first){
	var ind= getMapObjIndexByName(mod_treeGDE_map);
	if (mb_mapObj[ind].wms[wms].mapURL == false ||
		!mb_mapObj[ind].getDomElement().ownerDocument.getElementById(map) ||
		mb_mapObj[ind].getDomElement().ownerDocument.getElementById(map).complete) {

		if(state[wms]!=-1){
			for(var i=1;i< mb_mapObj[ind].wms[wms].objLayer.length;i++){
				if(mb_mapObj[ind].wms[wms].objLayer[i].gui_layer_visible===1||mb_mapObj[ind].wms[wms].objLayer[i].gui_layer_visible==="1"){
					state[wms]=1;
					if(showstatus=='true')
						setNodeImage(img);
						//Removes the previous Maprequest image from dom
						var prevlastRequestDiv = $("#"+mod_treeGDE_map+"_maps > div:last").prev().attr("id");
						var prevWmsgid=prevlastRequestDiv+"_map_"+wms;
						var $prevWmsgid=$("#"+prevWmsgid);
						$prevWmsgid.remove();	
					break;
				}
			}
		}
	}
	else{
		if(first){
			state[wms]=0;
//			 frames[mod_treeGDE_map].document.getElementById(map).onerror=imgerror;
//			 frames[mod_treeGDE_map].document.getElementById(map).onabort=imgerror;

			if(showstatus=='true')
				setNodeImage(img, "loading_folder.gif");
		}

		if(state[wms]!=-1)
			setTimeout('checkComplete('+wms+', "'+map+'", "'+img+'");',100);
	}
}

// mb_registerWmsLoadErrorFunctions("window.frames['treeGDE'].imgerror();");

function init_mapcheck(){
	if(!initialized)return;
	errors = 0;
	var ind =  getMapObjIndexByName(mod_treeGDE_map);
	if(! mb_mapObj[ind]||!initialized){
		setTimeout("init_mapcheck();",100);
		return;
	}
	for(var wms=0;wms< mb_mapObj[ind].wms.length;wms++){		
		var lastRequestDiv = $("#"+mod_treeGDE_map+"_maps > div:last" ).attr("id");
		var wmsimgid=lastRequestDiv+"_map_"+wms;	
		if( mb_mapObj[ind].getDomElement().ownerDocument.getElementById(wmsimgid)){
			checkComplete(wms, wmsimgid, arrNodes[0][0]+'|wms_'+ mb_mapObj[ind].wms[wms].wms_id, true);
		}
	}
}

function local_handleSelectedLayer(mapObj,wms_id,layername,type,status){
	if(lock_update||lock_check)return;
	var ind =  getMapObjIndexByName(mapObj);
	for(var i=0; i< mb_mapObj[ind].wms.length; i++){
		if( mb_mapObj[ind].wms[i].wms_id == wms_id){
			mb_mapObj[ind].wms[i].handleLayer(layername, type, status);
			break;
		}
	}
}

function zoomToLayer(j,k,l){
	if(!j&&!k&&!l){
		j=selectedMap;
		k=selectedWMS;
		l=selectedLayer;
	}
	var my= mb_mapObj[j].wms[k].objLayer[l].layer_epsg;
	for (var i=0; i < my.length;i++) {
		if(my[i]["epsg"]== mb_mapObj[j].epsg){
			mb_calculateExtent(mod_treeGDE_map,my[i]["minx"],my[i]["miny"],my[i]["maxx"],my[i]["maxy"]);
			var arrayExt =  mb_mapObj[j].extent.toString().split(",");
			mb_repaint(mod_treeGDE_map,arrayExt[0],arrayExt[1],arrayExt[2],arrayExt[3]);
			//mb_repaint(mod_treeGDE_map,my[i]["minx"],my[i]["miny"],my[i]["maxx"],my[i]["maxy"]);
			break;
		}
	}
}

function openLegendHtml(j,k,l){
	if(!j && !k&& !l){
		j=selectedMap;
		k=selectedWMS;
		l=selectedLayer;
	}
	var my= mb_mapObj[j].wms[k].objLayer[l];
	var legendWindow = window.open("../metadata/"+my.layer_name+".html", '<?php echo _mb("Legend");?>', "width=800,height=800,left=100,top=100,scrollbars=yes,resizable=no");
	legendWindow.focus();
}

function openStyleDialog(j,k,l){
	if(!j && !k&& !l){
		j=selectedMap;
		k=selectedWMS;
		l=selectedLayer;
	}
	var my= mb_mapObj[j].wms[k].objLayer[l];
	var dialogHtml = "<select id='styleSelect'>";
	for (var i=0;i < my.layer_style.length;i++) {
		dialogHtml += "<option value='" + my.layer_style[i].name + "'";
		if(my.layer_style[i].name == my.gui_layer_style) {
			dialogHtml += " selected";
		}
		dialogHtml += ">" + my.layer_style[i].title + "</option>";
	}
	dialogHtml += "</select>";

	if(my.layer_style.length > 1) {
		$("<div id='changeStyleDialog' title='<?php echo _mb('Change layer style');?>'><?php echo _mb('Please select a style');?>: </div>").dialog(
			{
				bgiframe: true,
				autoOpen: true,
				modal: false,
				buttons: {
					"<?php echo _mb('Close');?>": function(){
						$(this).dialog('close').remove();
					}
				}
			}
		);
		$(dialogHtml).appendTo("#changeStyleDialog");
		$("#styleSelect").change(function() {
			my.gui_layer_style = this.options[this.selectedIndex].value;
			Mapbender.modules[mod_treeGDE_map].setMapRequest();
		});
	}
	else {
		alert("<?php echo _mb('There is no different style available for this layer.');?>");
	}

}

//---begin------------- opacity --------------------

var opacityIncrement = 20;

function opacity_up(j, k, l) {
	handleOpacity(j, k, opacityIncrement);
}

function opacity_down(j, k, l) {
	handleOpacity(j, k, -opacityIncrement);
}

function handleOpacity(mapObj_id, wms_id, increment) {
	var opacity =  mb_mapObj[mapObj_id].wms[wms_id].gui_wms_mapopacity*100 + increment;
	mb_mapObj[mapObj_id].wms[wms_id].setOpacity(opacity);
	reloadTree();
	Mapbender.modules[mod_treeGDE_map].setMapRequest();
}

//---end------------- opacity --------------------

function move_up(j,k,l){
	if(isNaN(j)&&isNaN(k)&&isNaN(l)){
		j=selectedMap;
		k=selectedWMS;
		l=selectedLayer;
	}
	if(j==-1||k==-1||l==-1){
		alert("<?php echo _mb('You have to select the WMS you want to move up!');?> ")
		return;
	}
	var lid= mb_mapObj[j].wms[k].objLayer[l].layer_id;
	if(! mb_mapObj[j].move( mb_mapObj[j].wms[k].wms_id,lid,(reverse=="true")?false:true)){
		alert("<?php echo _mb('Illegal move operation');?>");
		return;
	}
	treeState = getState();
	 mb_mapObj[j].zoom(true, 1.0);
	 mb_execloadWmsSubFunctions();
	//find layer and select
	for(k=0;k< mb_mapObj[j].wms.length;k++){
		for(l=0;l< mb_mapObj[j].wms[k].objLayer.length;l++){
			if( mb_mapObj[j].wms[k].objLayer[l].layer_id==lid){
				select(j,k,l);
				if(l!=0)
					selectNode(String(lid));
				else
					selectNode("wms_"+String( mb_mapObj[j].wms[k].wms_id));
			}
		}
	}
}

function move_down(j,k,l){
	if(isNaN(j)&&isNaN(k)&&isNaN(l)){
		j=selectedMap;
		k=selectedWMS;
		l=selectedLayer;
	}
	if(j==-1||k==-1||l==-1){
		alert("<?php echo _mb('You have to select the WMS you want to move down!');?>")
		return;
	}
	var lid= mb_mapObj[j].wms[k].objLayer[l].layer_id;
	if(! mb_mapObj[j].move( mb_mapObj[j].wms[k].wms_id,lid,(reverse=="true")?true:false)){
		alert("<?php echo _mb('Illegal move operation');?>");
		return;
	}
	treeState = getState();
	 mb_mapObj[j].zoom(true, 1.0);
	 mb_execloadWmsSubFunctions();
	//find layer and select
	for(k=0;k< mb_mapObj[j].wms.length;k++){
		for(l=0;l< mb_mapObj[j].wms[k].objLayer.length;l++){
			if( mb_mapObj[j].wms[k].objLayer[l].layer_id==lid){
				select(j,k,l);
				if(l!=0)
					selectNode(String(lid));
				else
					selectNode("wms_"+String( mb_mapObj[j].wms[k].wms_id));
			}
		}
	}
}

function remove_wms(j,k,l){
	if(isNaN(j)&&isNaN(k)&&isNaN(l)){
		j=selectedMap;
		k=selectedWMS;
		l=selectedLayer;
	}
	if(j==-1||k==-1||l==-1){
		alert("<?php echo _mb('You have to select the WMS you want to delete!');?>")
		return;
	}
	if(l!=0){
		alert("<?php echo _mb('It is not possible to delete a single layer, please select a WMS!');?>")
		return;
	}
	var visibleWMS=0;
	for(var i=0;i< mb_mapObj[j].wms.length;i++)
		if( mb_mapObj[j].wms[i].gui_wms_visible==='1'|| mb_mapObj[j].wms[i].gui_wms_visible===1)
			visibleWMS++;
	if(visibleWMS<=1){
		alert ("<?php echo _mb('Last WMS can not be removed.');?>");
		return;
	}
	if(confirm('<?php echo _mb("Are you sure you want to remove");?>' + ' "'+ mb_mapObj[j].wms[k].objLayer[l].layer_currentTitle+'"?')){
  		 mb_mapObjremoveWMS(j,k);
		 mb_mapObj[j].zoom(true, 1.0);
		 lock_maprequest = true; //done to prohibit save wmc for each wms
		 mb_execloadWmsSubFunctions();
		 lock_maprequest = false;
	}
}

function updateParent(path){
	if(lock_check)return;
	var reset_lock=!lock_update;
	lock_update=true;
	var state=getChildrenCheckState(path, 0);
	//enableCheckbox(path, (state!=-1)); //3rd state
	checkNode(path, 0, (state==1));
	if(state==0 && showstatus=='true' && path.split(jst_delimiter[0]).length == 2){
		setTimeout('setNodeImage("'+path+'", "error_folder.png");', 100);
	}
	else{
		setTimeout('setNodeImage("'+path+'", "closed_folder.png");', 100);
	}

	if(reset_lock){
		lock_update=false;
	}
	handleSelectedWMS(path, true);

}

function handleSelectedWMS(path){
	if(lock_update)return;
	var t = path.split("|");
	var wms_id = t[t.length-1].substr(4);
	var reset_lock=!lock_check;
	var ind =  getMapObjIndexByName(mod_treeGDE_map);
	var wms =  getWMSIndexById(mod_treeGDE_map,wms_id);
	var layername =  mb_mapObj[ind].wms[wms].objLayer[0].layer_name;
	var bChk = IsChecked(path, 0);

	// in this case, only the root layer visibility/querylayer
	// needs to be adjusted, without cascading the changes to
	// its children
	if (arguments.length === 2 && arguments[1]) {
		var l = mb_mapObj[ind].wms[wms].getLayerByLayerName(layername);
		l.gui_layer_visible = bChk ? 1 : 0;
		l.gui_layer_querylayer = bChk ? 1 : 0;

		mb_restateLayers(mod_treeGDE_map,wms_id);
		if (!lock_maprequest) {
			setSingleMapRequest(mod_treeGDE_map,wms_id);
		}		
		return;
	}
	mb_mapObj[ind].wms[wms].handleLayer(layername,"visible",bChk?"1":"0");
	mb_mapObj[ind].wms[wms].handleLayer(layername,"querylayer",bChk?"1":"0");
	lock_check=true;
	checkChildren(path, 0, bChk);
	if(ficheckbox)checkChildren(path, 1, bChk);
	if(bChk==false && showstatus=='true'){
		setTimeout('setNodeImage("'+path+'", "error_folder.png");', 100);
	}
	else{
		setTimeout('setNodeImage("'+path+'", "closed_folder.png");', 100);
	}
	if(reset_lock)
	{
		mb_restateLayers(mod_treeGDE_map,wms_id);
		if (!lock_maprequest) {
			setSingleMapRequest(mod_treeGDE_map,wms_id);
		}
		lock_check=false;
	}
}

function handleSelection(path, box){
	if(lock_update)return;
	var reset_lock=!lock_check;
	lock_check=true;
	var bChk = IsChecked(path, box);
//	enableCheckbox(path, 0, true);
	checkChildren(path, box, bChk);
	if(reset_lock){
		//find wms id from path
		var t = path.split("|");
		for(var i=1;t[i].indexOf("wms_")!=0;i++){}
		var wms_id = t[i].substr(4);
		//set maprequest
		 mb_restateLayers(mod_treeGDE_map,wms_id);
		if(box==0)
			 setSingleMapRequest(mod_treeGDE_map,wms_id);
		lock_check=false;
	}
}

function initArray(){
	var parentObj = "";
	var controls="";
	if( mb_mapObj.length > 0){
		for(var i=0; i< mb_mapObj.length; i++){
			if( mb_mapObj[i].elementName == mod_treeGDE_map){
				for(var ii=0; ii< mb_mapObj[i].wms.length; ii++){
					if( mb_mapObj[i].wms[ii].gui_wms_visible === '1' ||  mb_mapObj[i].wms[ii].gui_wms_visible === 1){
						for(var iii=0; iii< mb_mapObj[i].wms[ii].objLayer.length; iii++){
							var temp =  mb_mapObj[i].wms[ii].objLayer[iii];
							if( mb_mapObj[i].wms[ii].objLayer[iii].layer_parent == ""){
								if(!temp.gui_layer_selectable == '1' && !temp.gui_layer_queryable == '1')
									continue;

								parentNode = arrNodes[0][0];
								if(eval("categories.wms_"+ mb_mapObj[i].wms[ii].wms_id) !== undefined)
									parentNode = eval("categories.wms_"+ mb_mapObj[i].wms[ii].wms_id);
								else
									eval("categories['wms_"+ mb_mapObj[i].wms[ii].wms_id+"'] = parentNode");

								var c_menu="[";
								if(reverse=="true"){
									if(menu.indexOf("wms_down")!=-1 && ii!= mb_mapObj[i].wms.length-1)c_menu+="menu_move_up,";
									if(menu.indexOf("wms_up")!=-1 && parentObj!="")c_menu+="menu_move_down,";
								}
								else{
									if(menu.indexOf("wms_up")!=-1 && parentObj!="")c_menu+="menu_move_up,";
									if(menu.indexOf("wms_down")!=-1 && ii!= mb_mapObj[i].wms.length-1)c_menu+="menu_move_down,";
								}
								if(menu.indexOf("remove")!=-1)c_menu+="menu_delete,";
//								if(menu.indexOf("wms_switch")!=-1)c_menu+="menu_wms_switch,";
								if(menu.indexOf("opacity_up")!=-1 && parseFloat( mb_mapObj[i].wms[ii].gui_wms_mapopacity) < 1)c_menu+="menu_opacity_up,";
								if(menu.indexOf("opacity_down")!=-1 && parseFloat( mb_mapObj[i].wms[ii].gui_wms_mapopacity) > 0)c_menu+="menu_opacity_down,";
								if(menu.indexOf("hide")!=-1)c_menu+="menu_hide";
								c_menu+="]";
								controls='';
								if(switchwms=='true')controls='<INPUT type="checkbox" title="' + msgObj.tooltipHandleWms + '"  onclick="handleSelectedWMS(\''+parentNode+'|wms_'+ mb_mapObj[i].wms[ii].wms_id+'\');" />';
								if(wmsbuttons == 'true'&&metadatalink == 'true')controls+='<a href="'+'javascript:openwindow(\''+ defaultMetadataUrl + '&id='+temp.layer_uid+'\');'+'"><img alt="'+msgObj.tooltipMetadata+'" title="'+msgObj.tooltipMetadata+'" src="'+imagedir+'/info.png" /></a>';
								addNode(parentNode,["wms_"+ mb_mapObj[i].wms[ii].wms_id,[temp.layer_currentTitle,((metadatalink=='true'&&wmsbuttons != 'true')?('javascript:openwindow(\"'+ defaultMetadataUrl + '&id='+temp.layer_uid+'\");'):"javascript:select("+i+","+ii+","+iii+");"),,,temp.layer_currentTitle,eval(c_menu),controls,[i,ii,iii]]],false,false,reverse=="true");
								parentObj = parentNode+"|wms_"+ mb_mapObj[i].wms[ii].wms_id;
							}
							if( mb_mapObj[i].wms[ii].objLayer[iii].layer_parent && (handlesublayer=="true"|| mb_mapObj[i].wms[ii].objLayer[iii].layer_parent=="0")){
								var parentLayer = "";
								var j = iii;
								while( mb_mapObj[i].wms[ii].objLayer[j].layer_parent!="0"){
									//find parent
									for(var jj=0; jj <  mb_mapObj[i].wms[ii].objLayer.length; jj++){
										if( mb_mapObj[i].wms[ii].objLayer[jj].layer_pos==parseInt( mb_mapObj[i].wms[ii].objLayer[j].layer_parent)){
											j=jj;
											break;
										}
									}
									parentLayer = "|" +  mb_mapObj[i].wms[ii].objLayer[j].layer_id + parentLayer;
								}
								if(temp.gui_layer_selectable == '1' || temp.gui_layer_queryable == '1'){
									var c_menu="[";
									if(reverse=="true"){
										if(menu.indexOf("layer_down")!=-1 && iii!= mb_mapObj[i].wms[ii].objLayer.length-1)c_menu+="menu_move_up,";
										if(menu.indexOf("layer_up")!=-1 && iii!=1)c_menu+="menu_move_down,";
									}
									else{
										if(menu.indexOf("layer_up")!=-1 && iii!=1)c_menu+="menu_move_up,";
										if(menu.indexOf("layer_down")!=-1 && iii!= mb_mapObj[i].wms[ii].objLayer.length-1)c_menu+="menu_move_down,";
									}
									if(menu.indexOf("metainfo")!=-1)c_menu+="menu_metalink,";
									if(menu.indexOf("zoom")!=-1 && temp.layer_epsg.length>0)c_menu+="menu_zoom,";
//									if(menu.indexOf("layer_switch")!=-1)c_menu+="menu_layer_switch,";
//									if(menu.indexOf("info_switch")!=-1)c_menu+="menu_info_switch,";
									if(menu.indexOf("hide")!=-1)c_menu+="menu_hide,";
									if(menu.indexOf("change_style")!=-1)c_menu+="menu_style,";
									if(menu.indexOf("legend")!=-1)c_menu+="menu_legend";
									c_menu+="]";

									controls = [];
									controls.push('<input type="checkbox"  title="' + msgObj.tooltipLayerVisible + '" ');
									if(temp.layer_name=="")
										controls.push('style="display:none;" ');
									if(temp.gui_layer_visible==='1' ||temp.gui_layer_visible===1){
										controls.push('checked ');
									}
									if(temp.gui_layer_selectable!='1')
										controls.push('disabled ');
									controls.push("onclick=\"local_handleSelectedLayer('"+mod_treeGDE_map+"','"+ mb_mapObj[i].wms[ii].wms_id+"','"+temp.layer_name+"','visible',this.checked?1:0);");
									if(ficheckbox == 'false')
										controls.push("local_handleSelectedLayer('"+mod_treeGDE_map+"','"+ mb_mapObj[i].wms[ii].wms_id+"','"+temp.layer_name+"','querylayer',this.checked?1:0);");
									controls.push("handleSelection('"+parentObj+parentLayer+"|"+temp.layer_id+"', 0);");
									controls.push("updateParent('"+parentObj+parentLayer+"');\" />");
									if(ficheckbox == 'true'){
										controls.push('<input type="checkbox" title="' + msgObj.tooltipLayerQuerylayer + '" ');
										if(temp.gui_layer_querylayer=='1')
											controls.push('checked ');
										if(temp.gui_layer_queryable!='1')
											controls.push('disabled ');
										controls.push("onclick=\"local_handleSelectedLayer('"+mod_treeGDE_map+"','"+ mb_mapObj[i].wms[ii].wms_id+"','"+temp.layer_name+"','querylayer',this.checked?1:0);");
										controls.push("handleSelection('"+parentObj+parentLayer+"|"+temp.layer_id+"', 1);\" />");
									}
									if(wmsbuttons == 'true'&&metadatalink == 'true'){
										controls.push('<a href="javascript:openwindow(\''+ defaultMetadataUrl + '&id='+temp.layer_uid+'\');"><img alt="'+msgObj.tooltipMetadata+'" title="'+msgObj.tooltipMetadata+'" src="'+imagedir+'/info.png" /></a>');
									}
									var groupedImageStyle ='';
									if (temp.has_childs == true){
										groupedImageStyle ='closed_folder.png';
									}
									else{
										groupedImageStyle ='menu.png';
									}
									addNode(parentObj + parentLayer, [temp.layer_id,[temp.layer_currentTitle,((metadatalink=='true'&&wmsbuttons != 'true')?('javascript:openwindow(\"'+ defaultMetadataUrl + '&id='+temp.layer_uid+'\");'):"javascript:select("+i+","+ii+","+iii+");"),,((c_menu!='[]'&&temp.layer_name!="")?groupedImageStyle:null),temp.layer_currentTitle,eval(c_menu),controls.join(""),[i,ii,iii]]],false,false,reverse=="true");
								}
							}
						}
					}
				}
			}
		}
	}
	initialized=true;
}

function initWmsCheckboxen(){
	var hidden=0;
	if( mb_mapObj.length > 0){
		for(var i=0; i< mb_mapObj.length; i++){
			if( mb_mapObj[i].elementName == mod_treeGDE_map){
				for(var ii=0; ii< mb_mapObj[i].wms.length; ii++){
					if( mb_mapObj[i].wms[ii].gui_wms_visible === '1' ||  mb_mapObj[i].wms[ii].gui_wms_visible === 1){
						for(var iii=0; iii< mb_mapObj[i].wms[ii].objLayer.length; iii++){
							var temp =  mb_mapObj[i].wms[ii].objLayer[iii];
							if( mb_mapObj[i].wms[ii].objLayer[iii].layer_parent == ""){
								updateParent(arrNodes[0][0]+"|wms_"+ mb_mapObj[i].wms[ii].wms_id);
							}
						}
					}
					else if(ii<= parseInt(openfolder, 10)+hidden)
						hidden++;
				}
				closeAll();
				var openFolderIndex = parseInt(openfolder, 10) + hidden;
				if(treeState!='') {
					setState(treeState);
				}
				else if(openfolder!=='false' && openFolderIndex < mb_mapObj[i].wms.length && openFolderIndex >= 0) {

					setState(arrNodes[0][0]+"|wms_"+ mb_mapObj[i].wms[ openFolderIndex].wms_id);
				}
			}
		}
	}
}
