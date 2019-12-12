/**
 * Package: mb_extendedSearch.js
 *
 * Description:
 *
 * Module for the extended search form. The form itself a mapbender gui.
 * Corresponding server file plugins/mb_extendedSearch_server.php
 * 
 *  
 * Files:
 * - http/plugins/mb_extendedSearch.js
 *
 * SQL:
 * >see ../../db/extentedSearchGui.sql
 * >
 * >
 * >
 * >
 * >
 * >
 * >
 * >
 * 
 * Help:
 *
 * Maintainer:
 * http://www.mapbender.org/User:Armin_Retterath
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */




var empty = 'false';
var obj = {};
//define variables for the 2 different forms:
var form1 = document.getElementsByName('form1')[0];
var form2 = document.getElementsByName('form2')[0];

/**
 * get list of all information by ajax call when intializing the client
 */
function requestList(){
	obj['action'] = 'getList';
	var obj2json = $.toJSON(obj);
	getList(obj2json);
}

Mapbender.events.init.register(function () {
	$('.hasdatepicker').datepicker({dateFormat:'yy-mm-dd',showOn: 
'button', buttonImage: '../img/calendar.png', buttonImageOnly: true, 
constraintInput: true});
	$("button, input:submit, a", ".button").button();

    requestList();
});

/**Mapbender.events.init.register(
	function() {
		$("#tabs").tabs();
	}
);*/


/**
 * Ajax-JSON function to get list of all departments from server
 */
function getList(obj){
	mb_ajax_post("../plugins/mb_extendedSearch_server.php",{"obj":obj}, function (json,status){
		if(status == 'success'){
			appendList(json);
			$("form[name='form1']").find('.help-dialog').helpDialog();
		}		
		else{
			alert("An error occured!");
		}
	});
}

/**
 * Function to fill department list selectbox 
 */
function appendList(json){
	var djson = json;
	//new options for department list
	for(var i=0; i<djson.entries.group_name.length; i++){
		var newOption = new Option(djson.entries.group_name[i],djson.entries.user_department[i],false,false);
		var list = form1.registratingDepartments;
		newOption.newAttribute = "title";
		newOption.title = djson.entries.group_title[i];
		list.options[list.length] = newOption;
	}
	
	//new options for inspire list
	for(var i=0; i<djson.entries.inspire_cat_name.length; i++){
		var newOption = new Option(djson.entries.inspire_cat_name[i],djson.entries.inspire_cat_id[i],false,false);
		var list = form1.inspireThemes;
		newOption.newAttribute = "title";
		newOption.title = djson.entries.inspire_cat_title[i];
		list.options[list.length] = newOption;
	}

	//new options for iso list
	for(var i=0; i<djson.entries.iso_cat_name.length; i++){
		var newOption = new Option(djson.entries.iso_cat_name[i],djson.entries.iso_cat_id[i],false,false);
		var list = form1.isoCategories;
		newOption.newAttribute = "title";
		newOption.title = djson.entries.iso_cat_title[i];
		list.options[list.length] = newOption;
	}

	//new options for custom list
	for(var i=0; i<djson.entries.custom_cat_name.length; i++){
		var newOption = new Option(djson.entries.custom_cat_name[i],djson.entries.custom_cat_id[i],false,false);
		var list = form1.customCategories;
		newOption.newAttribute = "title";
		newOption.title = djson.entries.custom_cat_title[i];
		list.options[list.length] = newOption;
	}

	//push translations to form (text fields)
	//for (var key in djson.entries.translations) {
	for (var key in djson.entries.translations) {
		if (key != 'search') { //search is the text for the search button
			if (key.substring(0,4) != 'help') {
       				$("#" + key).text(djson.entries.translations[key]);
			}
			else {
				//alert("help found: "+djson.entries.translations[key]);
				$("#" + key).attr("help","{text:'"+djson.entries.translations[key]+"'}");
			}
		}
		else {
			$("#" + key).val(djson.entries.translations[key]);
			$("#" + key).text(djson.entries.translations[key]);
		}
	}
}

function removeListSelections(listname){
	eval("var list = document.getElementById('"+listname+"');");
	list.selectedIndex = -1;
} 

function getValueSearchTypeBbox()
{
for (var i=0; i < document.form2.searchTypeBbox.length; i++)
   {
   if (document.form2.searchTypeBbox[i].checked)
      {
      return document.form2.searchTypeBbox[i].value;
      }
   }
}

function getValueSortBy()
{
for (var i=0; i < document.form2.sortBy.length; i++)
   {
   if (document.form2.sortBy[i].checked)
      {
      return document.form2.sortBy[i].value;
      }
   }
}

/**
* Function to get map extent of current mapframe1 
*
*/
function getMapExtent(obj){
	if(obj.checked){
		var mapObj = getMapObjByName("mapframe1");
		document.form2.searchBbox.value = mapObj.extent;
	}
	else{
		document.form2.searchBbox.value = "";
	}
}

/**
* Function to get values of form1 and fill hidden fields of form2
*
*/
function validate(){
	var send = true;

	//departments
	var departments = [];
	var cnt=0;
	for(var i=0; i<form1.registratingDepartments.length; i++){
		if(form1.registratingDepartments.options[i].selected == true){
			departments[cnt] = form1.registratingDepartments.options[i].value;
			cnt++;
		}	
	}
	if(cnt>0){
		form2.registratingDepartments.value = departments.join(",");
	}
	else{
		form2.registratingDepartments.value = empty;
	}

	//isocategories
	var categories=[];
	var cnt=0;
	for(var i=0; i<form1.isoCategories.length; i++){
		if(form1.isoCategories.options[i].selected == true){
			categories[cnt] = form1.isoCategories.options[i].value;
			cnt++;
		}	
	}
	if(cnt>0){
		form2.isoCategories.value = categories.join(",");
	}
	else{
		form2.isoCategories.value = empty;
	}

	//customcategories
	var categories=[];
	var cnt=0;
	for(var i=0; i<form1.customCategories.length; i++){
		if(form1.customCategories.options[i].selected == true){
			categories[cnt] = form1.customCategories.options[i].value;
			cnt++;
		}	
	}
	if(cnt>0){
		form2.customCategories.value = categories.join(",");
	}
	else{
		form2.customCategories.value = empty;
	}

	//inspirethemes
	var categories=[];
	var cnt=0;
	for(var i=0; i<form1.inspireThemes.length; i++){
		if(form1.inspireThemes.options[i].selected == true){
			categories[cnt] = form1.inspireThemes.options[i].value;
			cnt++;
		}	
	}
	if(cnt>0){
		form2.inspireThemes.value = categories.join(",");
	}
	else{
		form2.inspireThemes.value = empty;
	}

	//searchText
	if(form1.searchText.value != ""){
		form2.searchText.value = form1.searchText.value;
	}
	else{
		form2.searchText.value = empty;
	}

	//validFrom
	var regTimeBegin = form1.regTimeBegin.value;
	if(regTimeBegin != ""){
		//var ds = regTimeBegin.split(".");
		// var d = new Date(parseInt(ds[2]),(parseInt(ds[1] - 1)),parseInt(ds[0]));
		 form2.regTimeBegin.value = regTimeBegin;
	}else{
		form2.regTimeBegin.value = empty;
	}

	//validTo
	var regTimeEnd = form1.regTimeEnd.value;
	if(regTimeEnd != ""){
		//var ds = regTimeEnd.split(".");
		//var d = new Date(parseInt(ds[2]),(parseInt(ds[1] - 1)),parseInt(ds[0]));
		form2.regTimeEnd.value = regTimeEnd;
	}
	else{
		form2.regTimeEnd.value = empty;
	}

	//dataFrom
	var timeBegin = form1.timeBegin.value;
	if(timeBegin != ""){
		//var ds = timeBegin.split(".");
		 //var d = new Date(parseInt(ds[2]),(parseInt(ds[1] - 1)),parseInt(ds[0]));
		 form2.timeBegin.value = timeBegin;
	}else{
		form2.timeBegin.value = empty;
	}
	//dataTo
	var timeEnd = form1.timeEnd.value;
	if(timeEnd != ""){
		//var ds = timeEnd.split(".");
		//var d = new Date(parseInt(ds[2]),(parseInt(ds[1] - 1)),parseInt(ds[0]));
		form2.timeEnd.value = timeEnd;
	}
	else{
		form2.timeEnd.value = empty;
	}

	//searchTypeBbox
	var searchTypeBbox = empty;
	
	for (var i=0; i < form1.searchTypeBbox.length; i++){
		
  		if (form1.searchTypeBbox[i].checked){
			
     			searchTypeBbox = form1.searchTypeBbox[i].value;
			
      		}
  	}
	form2.searchTypeBbox.value = searchTypeBbox;	
	
	//searchBbox
	getMapExtent(form1.searchBbox);
	
	//sortBy
	var orderBy = empty;
	
	for (var i=0; i < form1.orderBy.length; i++){
		
  		if (form1.orderBy[i].checked){
			
     			orderBy = form1.orderBy[i].value;
			
      		}
  	}
	form2.orderBy.value = orderBy;

	//searchResources
	var searchResources = [];
	var cnt=0;
	var inputElements = document.getElementsByTagName("input");
	for(var i=0; i<inputElements.length; i++){
    		if(inputElements[i].name.indexOf("checkResources") != -1){
    			if(inputElements[i].checked==true){
    				searchResources[cnt] = inputElements[i].value;
    				cnt++;	
    			}		
    		}
   	}
   	if(cnt>0){
		form2.searchResources.value = searchResources.join(",");
	}
	else{
		form2.searchResources.value = empty;
	}	

	if(send == true){
		form2.submit();
	}
	return false;
}

