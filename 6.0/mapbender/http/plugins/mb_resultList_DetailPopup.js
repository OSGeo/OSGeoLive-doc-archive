/**
 * Package: resultList_DetailPopup
 *
 * Description:
 * A result list detail popup for showing single feature attributes
 * 
 * Files:
 *  - http/plugins/mb_resultList_DetailPopup.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, 
 * > e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, 
 * > e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('<app_id>',
 * > 'resultList_DetailPopup',2,1,'Detail Popup For resultList','','div','','',NULL,NULL,NULL,NULL,NULL,
 * > '','','','../plugins/mb_resultList_DetailPopup.js','','resultList','',
 * > 'http://www.mapbender.org/ResultList'); 
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'detailPopupTitle', 'Details', 'title of the result list detail popup', 'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'detailPopupHeight', '250', 'height of the result list detail popup' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'detailPopupWidth', '400', 'width of the result list detail popup' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'position', '[200,200]', 'position of the result list detail popup' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'openLinkFromSearch', '0', 'open link directly if feature attr is defined as link' ,'var');
 *
 * Help:
 * http://www.mapbender.org/ResultList_DetailPopup
 *
 * Maintainer:
 * http://www.mapbender.org/User:Karim Malhas
 * 
 * Parameters:
 * detailPopupTitle - *[optional]* title of the result list detail popup
 * detailPopupHeight - *[optional]* height of the result list detail popup
 * detailPopupWidth  - *[optional]* width of the result list detail popup
 * position - *[optional]* position of the result list detail popup
 * openLinkFromSearch - *[optional]* open link directly if feature attr is defined as link
 * 
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

//check element vars
options.detailPopupTitle  = options.detailPopupTitle || "Details";
options.detailPopupHeight	= options.detailPopupHeight || 250;
options.detailPopupWidth 	= options.detailPopupWidth || 400;
// see http://docs.jquery.com/UI/Dialog for possible values
options.position = options.position || 'center';
options.openLinkFromSearch = options.openLinkFromSearch || '0';

Mapbender.events.init.register(function(){
	Mapbender.modules[options.target[0]].rowclick.register(function(row){
		var me = Mapbender.modules[options.target[0]];
		var modelIndex = $(row).data("modelindex");
		var feature = me.model.getFeature(modelIndex);
		
		//close old dialog before opening new one
		$('.infoPopup').dialog('close');
		
		if(me.detailColumns.length > 0) {
			var infoPopupHtml = "<table>";
			for (var columnIndex in me.detailColumns) {
				infoPopupHtml += "<tr>";
				infoPopupHtml += "<td>" + me.detailColumns[columnIndex].label + "</td>";
				infoPopupHtml += "<td>";
				if(me.detailColumns[columnIndex].html.indexOf("href") != -1) {
					var featureHref = me.model.getFeatureProperty(modelIndex, me.detailColumns[columnIndex].name);
					var setUrl = me.detailColumns[columnIndex].html.replace(/href\s*=\s*['|"]\s*['|"]/, "href='" + featureHref + "' target='_blank'");
					if(setUrl.match(/><\/a>/)){
						var newLink	= setUrl.replace(/><\/a>/, ">" + featureHref + "</a>");
					}
					else{
						var newLink = setUrl;
					}
					if(options.openLinkFromSearch == '1'){
						window.open(featureHref, featureHref,"width=500, height=400,left=100,top=100,scrollbars=yes");
					}
					infoPopupHtml += newLink;
				}
				else {
					infoPopupHtml += me.model.getFeatureProperty(modelIndex, me.detailColumns[columnIndex].name);
				}
				infoPopupHtml += "</td>";
				
				infoPopupHtml += "</tr>";
			}
			infoPopupHtml += "</table>";
			
			var infoPopup = $('<div class="infoPopup"></div>');
			infoPopup.append(infoPopupHtml);
			
			buttonList = $("<ul></ul>").css("list-style-type","none");
			
			for (var c in me.popupButtons){
				var callback = function() {
					var  args =  { 
						WFSConf: me.WFSConf,
						geoJSON: feature,
						selectedRows: me.getSelected()
					};
					me.popupButtons[c].callback.call(this,args);
				};
				var button = $("<li><button type='button' class='ui-state-default ui-corner-all'>"+me.popupButtons[c].title+"</button></li>").click(callback);
				button.css("display","inline");
				buttonList.append(button);
			}
			infoPopup.append(buttonList);
			infoPopup.dialog({
				title : options.detailPopupTitle, 
				autoOpen : false, 
				draggable : true,
				width : options.detailPopupWidth,
//				height : options.detailPopupHeight,
				position : options.position
			});
			infoPopup.dialog("open");
			$("#"+Mapbender.modules[options.target[0]].id).bind("dialogbeforeclose",function(){
				infoPopup.dialog('close');
			});
		}
		return false;
	});
});
