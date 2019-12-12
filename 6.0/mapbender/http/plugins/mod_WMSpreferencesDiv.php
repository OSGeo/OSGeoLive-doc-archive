<?php
/**
 * Package: mod_WMSpreferencesDiv
 *
 * Description:
 * This module generates a table with wms of the mapObject. The user
 * can change the mapformat, featureinfoformat and the exceptionformat for each wms.
 * Also the wms priority and the map transparency can be changed interactivly. The module
 * should exchange the old mod_WMSpreferences module which was an iframe before.
 * The module is invoked thru a button click. The button must be a mb_button.js module
 * 
 * 
 * Files:
 *  - http/javascripts/mod_WMSpreferencesDiv.php
 *
 * SQL:
 * > 
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment,
 * >   e_title, e_element, e_src, e_attributes, e_left, e_top, e_width,
 * >  e_height, e_z_index, e_more_styles, e_content, e_closetag,
 * >  e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES(
 * > '<app_id>','WMS_preferencesDiv',12,1,'Configure WMS preferences - div tag','WMS preferences',
 * > 'div','','',870,60,400,500,NULL ,'z-index:9999;','','div','../plugins/mod_WMSpreferencesDiv.php',
 * > '','mapframe1','jq_ui_dialog','http://www.mapbender.org/index.php/mod_WMSpreferencesDiv');
 * >
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment,
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height,
 * > e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
 * > VALUES('<app_id>','WMS_preferencesButton',2,1,'button for configure the preferences of each loaded wms',
 * > 'Manage WMS preferences','img','../img/button_blink_red/preferences_off.png','',
 * > 670,60,24,24,1,'','','','../plugins/mb_button.js','','WMS_preferencesDiv',
 * > '','http://www.mapbender.org/index.php/mb_button');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value,
 * > context, var_type) VALUES('gui1', 'WMS_preferencesButton', 'dialogWidth', '400', '' ,'var');
 * >
 *
 * Help:
 * http://www.mapbender.org/mod_WMSpreferencesDiv
 *
 * Maintainer:
 * http://www.mapbender.org/User:Armin_Retterath
 * 
 * Parameters:
 * none
 * 
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
$e_id = 'WMS_preferencesDiv';
$gui_id = array(Mapbender::session()->get("mb_user_gui"));

//include all element vars from the given element
include '../include/dyn_js.php';
include '../include/dyn_php.php';
$vis = "";
$wmsid = "";

$sql_visible = "SELECT * FROM gui_wms WHERE fkey_gui_id = $1";
$v = array(Mapbender::session()->get("mb_user_gui"));
$t = array("s"); 
$res_visible = db_prep_query($sql_visible, $v, $t); 
$cnt_visible = 0; 

while($row = db_fetch_array($res_visible)){
	$gui_wms_visible[$cnt_visible] = $row["gui_wms_visible"];
	$fkey_wms_id_visible[$cnt_visible] = $row["fkey_wms_id"];
	if($cnt_visible>0){
		$vis .= ",";
		$wmsid .= ",";		
	}
	$vis .= $gui_wms_visible[$cnt_visible];
	$wmsid .= $fkey_wms_id_visible[$cnt_visible];
	$cnt_visible++;
}


echo "var mod_gui_wms_visible = '".$vis."';";
echo "var mod_fkey_wms_id_visible = '".$wmsid."';";

?>

//initialize module
var WMS_preferencesDiv = function() {
	var that = this;
	var targetName = options.target;
	var ind = getMapObjIndexByName(targetName);
	var my = mb_mapObj[ind];

	//functions
	this.mb_swapWmsByIndex = function(mapObj_ind, indexA, indexB) {
 		var myMapObj = mb_mapObj[mapObj_ind];
		if (indexA != indexB && indexA >= 0 && indexA < myMapObj.wms.length && indexB >= 0 && indexB < myMapObj.wms.length) {
			upper = myMapObj.wms[indexA];
			myMapObj.wms[indexA] = myMapObj.wms[indexB];
			myMapObj.wms[indexB] = upper;
			var upperLayers = myMapObj.layers[indexA];
			var upperStyles = myMapObj.styles[indexA];
			var upperQuerylayers = myMapObj.querylayers[indexA];
			myMapObj.layers[indexA] = myMapObj.layers[indexB];
			myMapObj.styles[indexA] = myMapObj.styles[indexB];
			myMapObj.querylayers[indexA] = myMapObj.querylayers[indexB];
			myMapObj.layers[indexB] = upperLayers;
			myMapObj.styles[indexB] = upperStyles;
			myMapObj.querylayers[indexB] = upperQuerylayers;
			return true;
		}
		else {
			return false;
		}
	}

	this.setMapformat = function(val) {
		var tmp = val.split(",");
		my.wms[tmp[0]].gui_wms_mapformat = tmp[1];
		Mapbender.modules[options.target].setMapRequest();
		this.formContainer.remove();
		this.initForm();
	}
	this.setFeatureformat = function(val) {
		var tmp = val.split(",");
		my.wms[tmp[0]].gui_wms_featureinfoformat = tmp[1];
		this.formContainer.remove();
		this.initForm();
	}
	this.setExceptionformat = function(val) {
		var tmp = val.split(",");
		my.wms[tmp[0]].gui_wms_exceptionformat = tmp[1];
		Mapbender.modules[options.target].setMapRequest();
		this.formContainer.remove();
		this.initForm();
	}

	this.swap = function(index1, index2) {
		this.formContainer.remove();
		if (this.mb_swapWmsByIndex(ind, index1, index2) == true) {
			this.initForm();
			zoom(options.target, true, 1.0);
			mb_execloadWmsSubFunctions();
		}
	}


	this.remove_wms = function(num) {
		var cnt_vis=0;
		var wms_visible_down = mod_gui_wms_visible.split(",");
		var wms_vis_down = wms_visible_down.length;
	
		//check if there are more than two visible wms's
		for(var i=0; i < wms_visible_down.length; i++){
			var my_wms_visible = wms_visible_down[i];
			if(my_wms_visible == 0){
  				var cnt_vis = cnt_vis+1;		
			}
		}	
	
		if(my.wms.length - cnt_vis>1){
	  		var ind = getMapObjIndexByName(options.target);  
  			mb_mapObjremoveWMS(ind,num) 
			mb_execloadWmsSubFunctions();
		}
		else{
			alert ("<?php echo _mb('Last WMS cannot be removed'); ?>");
		}
		this.formContainer.remove();
		this.initForm();
			
	}

	this.deleteForm = function() {
		this.formContainer.remove();
	}

	this.refreshTransparency = function(visibleWmsIndexArray) {
		for (var i = 0 ; i < visibleWmsIndexArray.length ; i++) {
			wmsId = my.wms[visibleWmsIndexArray[i]].wms_id;
			$( "#transparency_"+wmsId ).val( $( "#slider_" + wmsId ).slider( "value" ) );
			my.wms[visibleWmsIndexArray[i]].setOpacity(100-($( "#slider_" + wmsId ).slider( "value" )));
		}
		Mapbender.modules[options.target].setMapRequest();
	}

	this.initForm = function() {
		var str = "";
		var wms_visible = mod_gui_wms_visible.split(",");
		var wms_id_visible = mod_fkey_wms_id_visible.split(",");
		var visibleWmsIndexArray = new Array();
		this.formContainer = $(document.createElement('form')).attr({'id':'wms-preferences-form'}).appendTo('#' + options.id);
		for(var i=0; i < my.wms.length; i++){
			var found = false;
			for(var j=0; j < wms_id_visible.length; j++){
				if (wms_visible[j] == 1 && wms_id_visible[j] == my.wms[i].wms_id){
					visibleWmsIndexArray[visibleWmsIndexArray.length] = i;
					found = true;
				}
			}
			if (found == false && my.wms[i].gui_wms_visible == 1) {
				visibleWmsIndexArray[visibleWmsIndexArray.length] = i;
			}
		}
		//loop over all visible wms if reversed layer order is wished, reverse visibleWmsIndexArray 
		options.reversePreferences = true;
		if (options.reversePreferences) {
			visibleWmsIndexArray.reverse();
		}
		//
		for (var i = 0 ; i < visibleWmsIndexArray.length ; i++) {
			z = visibleWmsIndexArray[i];

			var mapString = "";
			var featureinfoString = "";
			var exceptionString = "";
				
			for(var j=0; j<my.wms[z].data_type.length; j++){
				if(my.wms[z].data_type[j] == 'map'){
					mapString += "<option value='"+z+","+my.wms[z].data_format[j]+"'";
					if(my.wms[z].data_format[j] == my.wms[z].gui_wms_mapformat){
						mapString += "selected";
					}
					mapString += ">"+my.wms[z].data_format[j]+"</option>";
				}
				else if(my.wms[z].data_type[j] == 'featureinfo'){
					featureinfoString += "<option value='"+z+","+my.wms[z].data_format[j]+"'";
					if(my.wms[z].data_format[j] == my.wms[z].gui_wms_featureinfoformat){
						featureinfoString += "selected";
					}
					featureinfoString += ">"+my.wms[z].data_format[j]+"</option>";
				}
				else if(my.wms[z].data_type[j] == 'exception'){
					exceptionString += "<option value='"+z+","+my.wms[z].data_format[j]+"'";
					if(my.wms[z].data_format[j] == my.wms[z].gui_wms_exceptionformat){
						exceptionString += "selected";
					}
					exceptionString += ">"+my.wms[z].data_format[j]+"</option>";
				}
			}
			//extract wms title, id and abstract 
			var wmsTitle = my.wms[z].wms_title;
			var wmsAbstract = my.wms[z].wms_abstract;
			var wmsId = my.wms[z].wms_id;
			
			//generate Form 
			
			this.tableContainer = $(document.createElement('table')).appendTo(this.formContainer);
			this.tableContainer.attr({'border':'1'});
			this.tableContainer.attr({'rules':'rows'});
			this.tableContainer.attr({'width':'300'});
			this.rowContainer = $(document.createElement('tr')).appendTo(this.tableContainer);

			this.columnContainer = $(document.createElement('th')).appendTo(this.rowContainer);
			
			if (visibleWmsIndexArray.length > 1) {
				if (i != 0) {
					//show up arrow only if it is not the first entry and if the count of entries is greater than 1
			
					this.wmsUp = $(document.createElement('img')).appendTo(this.columnContainer);
					this.wmsUp.attr({'src':'../img/button_gray/up.png'});
					this.wmsUp.attr({'id':'wmsUp_'+i});
					//this.wmsUp.attr({'style':'filter:Chroma(color=#C2CBCF);'});
					this.wmsUp.attr({'value':'up'});
					this.wmsUp.attr({'title':'move WMS up'});
					$("#" + "wmsUp" + "_" + i).click((function (i,z) {
						return function(){
							Mapbender.modules[options.id].swap(visibleWmsIndexArray[i-1],z);
						}
                        			//alert("move up"); 
 		        		})(i,z)); 
				}
				//show down arrow only if the entry is not the last one and if the count of entries is greater than 1
				if (i != visibleWmsIndexArray.length-1) {
					this.wmsDown = $(document.createElement('img')).appendTo(this.columnContainer);
					this.wmsDown.attr({'src':'../img/button_gray/down.png'});
					this.wmsDown.attr({'id':'wmsDown_'+i});
					//this.wmsDown.attr({'style':'filter:Chroma(color=#C2CBCF);'});
					//this.wmsDown.attr({'onclick':'alert("move up");'});
					this.wmsDown.attr({'value':'down'});
					this.wmsDown.attr({'title':'move WMS down'});
					$("#" + "wmsDown" + "_" + i).click((function (i,z) {
						return function(){
							Mapbender.modules[options.id].swap(z,visibleWmsIndexArray[i+1]);
						}
          
 		        		})(i,z)); 
				}
			
				this.wmsRemove = $(document.createElement('img')).appendTo(this.columnContainer);
				this.wmsRemove.attr({'src':'../img/button_gray/del.png'});
				this.wmsRemove.attr({'id':'wmsRemove_'+i});
				//this.wmsRemove.attr({'onclick':'alert("remove");'});
				this.wmsRemove.attr({'value':'remove'});
				this.wmsRemove.attr({'title':'remove WMS from GUI'});
				$("#" + "wmsRemove" + "_" + i).click((function (z) {
					return function(){
						Mapbender.modules[options.id].remove_wms(z);
					}
          
 		       		})(z)); 
			}
			
			this.wmsTitleDiv = $(document.createElement('div')).appendTo(this.columnContainer);
			this.wmsTitleDiv.attr({'id':'id_'+wmsId});
			this.wmsTitleDiv.attr({'style':'cursor:pointer'});
			this.wmsTitleDiv.attr({'onmouseover':'title=\"'+'id:'+wmsId+' '+my.wms[z].wms_abstract+'\"'});
			//this.wmsTitleDiv.attr({'width':'300'});

			this.columnContainer = $(document.createElement('th')).appendTo(this.rowContainer);
			this.wmsTitle = $(document.createElement('b')).appendTo(this.columnContainer);
			this.wmsTitle.attr({'title':wmsId+':'+wmsAbstract});
			this.wmsTitle.append(wmsTitle);

			//new row mapImageFormat
			this.rowContainer = $(document.createElement('tr')).appendTo(this.tableContainer);
			this.columnContainer = $(document.createElement('td')).appendTo(this.rowContainer);
			this.mapInfoTitle = this.columnContainer.append('<?php echo _mb('MapFormat'); ?>: ');
	
			this.columnContainer = $(document.createElement('td')).appendTo(this.rowContainer);
			this.mapInfoSelect = $(document.createElement('select')).appendTo(this.columnContainer);
			this.mapInfoSelect.change(function () {
					Mapbender.modules[options.id].setMapformat(this.value);
 		       	}); 
			this.mapInfoSelect.attr({'onchange':'Mapbender.modules[options.id].setMapformat(this.value);'});

			this.mapInfoSelect.append(mapString);

			//new row featureInfoFormat
			this.rowContainer = $(document.createElement('tr')).appendTo(this.tableContainer);
			this.columnContainer = $(document.createElement('td')).appendTo(this.rowContainer);
			this.featureInfoTitle = this.columnContainer.append('<?php echo _mb('FeatureInfoFormat'); ?>: ');
	
			this.columnContainer = $(document.createElement('td')).appendTo(this.rowContainer);
			this.featureInfoSelect = $(document.createElement('select')).appendTo(this.columnContainer);
			this.featureInfoSelect.attr({'onchange':'Mapbender.modules[options.id].setFeatureformat(this.value);'});
			this.featureInfoSelect.append(featureinfoString);

			//new row exceptionFormat
			this.rowContainer = $(document.createElement('tr')).appendTo(this.tableContainer);
			this.columnContainer = $(document.createElement('td')).appendTo(this.rowContainer);
			this.exceptionTitle = this.columnContainer.append('<?php echo _mb('ExceptionFormat'); ?>: ');
	
			this.columnContainer = $(document.createElement('td')).appendTo(this.rowContainer);
			this.exceptionSelect = $(document.createElement('select')).appendTo(this.columnContainer);
			this.exceptionSelect.attr({'onchange':'Mapbender.modules[options.id].setExceptionformat(this.value);'});
			this.exceptionSelect.append(exceptionString);

			this.rowContainer = $(document.createElement('tr')).appendTo(this.tableContainer);
			this.columnContainer = $(document.createElement('td')).appendTo(this.rowContainer);
			this.transparencyTitle = this.columnContainer.append('<?php echo _mb('Transparency'); ?>: ');
			this.transparencyValue = $(document.createElement('input')).appendTo(this.columnContainer);
			this.transparencyValue.attr({'type':'text'});
			this.transparencyValue.attr({'id':'transparency_'+wmsId});

			if (isNaN(my.wms[visibleWmsIndexArray[i]].gui_wms_mapopacity)) {
				thisWmsOpacity = 0;
			} else {
				thisWmsOpacity =  100-(my.wms[visibleWmsIndexArray[i]].gui_wms_mapopacity*100);
			}

			this.transparencyValue.attr({'value':thisWmsOpacity});
			this.transparencyValue.attr({'style':'border:0; color:#f6931f; font-weight:bold;'});

			this.columnContainer = $(document.createElement('td')).appendTo(this.rowContainer);

			this.wmsTransparencySliderDiv = $(document.createElement('div')).appendTo(this.columnContainer)
			this.wmsTransparencySliderDiv.attr({'id':'slider_'+wmsId});

			$("#slider_" + wmsId).slider({
				min: 0,
				max: 100,
				step: 10,
				value: thisWmsOpacity,
				change: function( event, ui) {
						that.refreshTransparency(visibleWmsIndexArray);
				}
			});
			
			/*$("#slider_" + wmsId).slider();
			$("#slider_" + wmsId).slider( "option", "min", 100 );
			$("#slider_" + wmsId).slider( "option", "max", 0 );
			$("#slider_" + wmsId).slider( "option", "step", -10 );*/

			this.formContainer.append('<br>');
		}
	}
this.initForm();
}


Mapbender.events.init.register(function() {
	Mapbender.modules[options.id] = $.extend(new WMS_preferencesDiv(),Mapbender.modules[options.id]);	
});




