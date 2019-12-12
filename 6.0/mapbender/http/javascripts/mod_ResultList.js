/**
 * Package: resultList
 *
 * Description:
 * A result list for featureCollections
 * 
 * Files:
 *  - http/javascripts/mod_ResultList.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) 
 * > VALUES('<gui_id>','resultList',2,1,'','Result List','div','','',NULL ,NULL 
 * > ,NULL ,NULL ,NULL 
 * > ,'','','div','mod_ResultList.js','../../lib/resultGeometryListController.js, 
 * > ../../lib/resultGeometryListModel.js','','',''); 
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList', 'resultListTitle', 'Search results', 'title of the result list dialog', 'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList', 'resultListHeight', '250', 'height of the result list dialog' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList', 'resultListWidth', '800', 'width of the result list dialog' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList', 'position', '[200,200]', 'position of the result list dialog' ,'var');
 *
 * Help:
 * http://www.mapbender.org/ResultList
 *
 * Maintainer:
 * http://www.mapbender.org/User:Karim Malhas
 * 
 * Parameters:
 * resultListTitle - *[optional]* title of the resultList dialog
 * resultListHeight - *[optional]* height of the resultList dialog
 * resultListWidth  - *[optional]* width of the resultList dialog
 * position - *[optional]* position of the result list detail popup
 * 
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

//check element vars
//var display_popup = display_popup || true;
options.display_popup = options.display_popup !== undefined ? options.display_popup :true;
options.resultListTitle  = options.resultListTitle || "ResultList";
options.resultListHeight	= options.resultListHeight || 250;
options.resultListWidth 	= options.resultListWidth || 400;
// see http://docs.jquery.com/UI/Dialog for possible values
options.position = options.position || 'center';

var ResultGeometryList = function(){
	//store a callback for the function to call when a row is clicke
	var row_callback = function(){};
	this.popupButtons = [];
	var me = this;

	me.options = options;
	me.rowclick = new Mapbender.Event();
	me.rowmouseover = new Mapbender.Event();
	me.rowmouseout = new Mapbender.Event();
	me.events = {
			wfsConfSet : new Mapbender.Event()
	};
	
	/*
	 * Method: addGlobalButton
	 * Descriptions: adds a Button to the bottom of the resultList
	 * Parameters:
	 * buttondef: {Object} an object with five properties, "title", "type" (either button or select)  "classes" (the classes the button should have), "selectoptions" (an array in case the type is select), "callback"
	 *
  	*/
	this.addGlobalButton =  function(buttondef){
			//buttondef format:
			//{"title":<if no image is given then this is displayed>, callback: <function( ))
			//type: <type> // either "select" or "button", defaults to "button"
			//  if type is "select", add options like this:
			// selectoptions : [{label :<label>,value : <value>},...   ]
			//
			// this is the domelement
				
			var bd = buttondef|| {};
			bd.title = bd.title || "no title";
			bd.type = bd.type || "button";
			bd.classes = bd.classes || "";
			bd.selectoptions = bd.selectoptions || [];
			bd.callback = bd.callback || function(){};

			var result = function(){
				var  args =  { 
					WFSConf: me.WFSConf,
					geoJSON: me.model.toString(),
					selectedRows: me.getSelected(),
					table: me.datatable
				};
				buttondef.callback.call(this,args);
			};
			
			if($('#'+options.id+"buttonrow").length < 1){
				$('#'+options.id).append('<div id="'+options.id +'buttonrow"></div>');
				$('#'+options.id+'buttonrow').css("clear","both");
			}
		
			if (bd.type == "button") {
				var button = $('<span><button type="button" class="ui-state-default ui-corner-all '+ bd.classes  +'">'+buttondef.title+'</button></span>').click(result);
				$('#'+options.id +'buttonrow').append(button);
				return button.find("button");
			}
			else if (bd.type == "select"){
				var select_options = "";
				for (c in bd.selectoptions){	
					select_options += '<option value="'+ bd.selectoptions[c].value +'">'+bd.selectoptions[c].label +'</options>';
				}	
				var selectbox = '<select class="'+ bd.classes  + '">'+ select_options  +'</select>';
				var rowSelect = $('#'+options.id +'buttonrow').append(selectbox);
				return rowSelect.find("select");
			}
			else {
				alert("invalid buttondefintion");
			}
		};

	/*
	 * Method: addPopupButton
	 * Description: adds a Button to the Popupmenu that is displayed when the user clicks an entry in the result list
	 * Parameters:
	 * buttondef: {Object} an object with two properties: "title" and  "callback", a function that gets the  feature that corresponds to the popup as it's argument
  */
	this.addPopupButton = function(buttondef){
		this.popupButtons.push(buttondef);
	};

	/*
	 * Method : setTitle
	 *
	 *  Description: sets the title of the resultList
	 *  Parameters:
	 *  title - {String} the new title of the resultList
	*/
	this.setTitle = function(title){
		if(options.resultListTitle == 'ResultList') {
			$('#'+options.id).data("title.dialog",title);
		}
		else {
			$('#'+options.id).data("title.dialog",options.resultListTitle);
		}
	};

	/*
	 * Method: getSelected
	 * Description: gets all rows from the datatable which are selected
	 * Returns: an an array of DOMTableRow
	*/
	this.getSelected = function() {
		var selected = [];//new resultGeometryListModel();
		var tr_rows = me.datatable.fnGetNodes();
		for(trindex in tr_rows)
		{
			if($(tr_rows[trindex]).hasClass("row_selected")){
				var modelindex = $(tr_rows[trindex]).data("modelindex");
				selected.push(me.model.getFeature(modelindex));
			}
		}
		return selected;
	};
	
	this.show = function(){
		$('#'+options.id).addClass("resultList");
			
		if(options.display_popup) {
			$('#'+options.id).dialog("open");
		} 
		else {
			$('#'+options.id).css("display","block");
		}
	
		var rowNodes = me.datatable.fnGetNodes();
		for (rowNodeIndex in rowNodes){
			(function() {
				var row = rowNodes[rowNodeIndex];
				$(".wfsFproperty", row).click(function(){
					me.rowclick.trigger((function(){return row;})());
				});		

				$(".wfsFproperty", row).mouseover(function(){
					me.rowmouseover.trigger((function(){return row;})());
				});
				
				$(".wfsFproperty", row).mouseout(function(){
					me.rowmouseout.trigger((function(){return row;})());
				});
				

				for(bId in me.rowbuttons){
					var callback  = me.rowbuttons[bId].callback;
					var buttonClass = "rowbutton_" + me.rowbuttons[bId].title.replace(' ','_');
					$("."+buttonClass, row).click(function(){
						callback((function(){ return row;})());
					});
				}
				
				// make rows selectable only when there are Global Buttons that could actually do something with that selection
				if($('#'+options.id+"buttonrow").length >  0){
					$(row).click((function(){
						if($(this).hasClass('row_selected')){
							$(this).removeClass('row_selected');
						}
						else{
							$(this).addClass('row_selected');
						}
						return true;
					}));
				}
				// set style of cursor to pointer for the result table
				if(me.rowclick.getProperties().count > 0) {$(".wfsFproperty", row).css("cursor","pointer");} 
			})();

		}
	};

	this.hide = function(){
		if(options.display_popup){
			$('#'+options.id).dialog("close");
		}
		else{
			$('#'+options.id).css("display","none");
		}
	};

	if(options.display_popup){
		$('#'+options.id).dialog({autoOpen: false , width:options.resultListWidth, draggable: true, title: options.resultListTitle, position: options.position});
	}
	else {
		$(".ui-searchContainer-result").replaceWith($('#'+options.id));
		$("#"+options.id).addClass("ui-searchContainer-result").hide();
	}
	
};

ResultGeometryList.prototype = new ResultGeometryListController(options);
ResultGeometryList.prototype.constructor = ResultGeometryList;

Mapbender.modules[options.id] = $.extend(
	new ResultGeometryList(), Mapbender.modules[options.id]
);
