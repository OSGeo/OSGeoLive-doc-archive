/**
 * Package: resultList_Highlight
 *
 * Description:
 * A highlighting functionality for a mapbender result list
 * 
 * Files:
 *  - http/plugins/mb_resultList_Highlight.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, 
 * > e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, 
 * > e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('<app_id>',
 * > 'resultList_Highlight',2,1,'highlighting functionality for resultList','','div','','',NULL,NULL,NULL,NULL,NULL,
 * > '','','','../plugins/mb_resultList_Highlight.js','','resultList,mapframe1,overview','',
 * > 'http://www.mapbender.org/ResultList'); 
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'maxHighlightedPoints', '500', '' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'resultHighlightColor', '#ff0000', '' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'resultHighlightLineWidth', '2', '' ,'var');
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
 * > VALUES('<app_id>', 'resultList_DetailPopup', 'resultHighlightZIndex', '100', '' ,'var');
 *
 * Help:
 * http://www.mapbender.org/resultList_Highlight
 *
 * Maintainer:
 * http://www.mapbender.org/User:Verena_Diewald
 * 
 * Parameters:
 * maxHighlightedPoints - *[optional]* maximum number of points of a geometry that can be highlighted in the client
 * resultHighlightColor - *[optional]* color of the resultHighlighting
 * resultHighlightLineWidth - *[optional]* line width of the resultHighlighting
 * resultHighlightZIndex - *[optional]* zindex of the resultHighlighting
 * 
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

//check element vars
options.maxHighlightedPoints	= options.maxHighlightedPoints || 5;
options.resultHighlightColor 	= options.resultHighlightColor || "#ff0000";
options.resultHighlightLineWidth  = options.resultHighlightLineWidth || 2;
options.resultHighlightZIndex  = options.resultHighlightZIndex || 100;

Mapbender.events.init.register(function(){
	var standingHighlight = null;
	Mapbender.modules[options.target[0]].rowclick.register(function(row){
		var me = Mapbender.modules[options.target[0]];
		var modelIndex = $(row).data("modelindex");
		var feature = me.model.getFeature(modelIndex);

		
		if(standingHighlight !== null){ standingHighlight.clean();}
		standingHighlight = new Highlight(
				[options.target[1],options.target[2]],
				"standingHighlight", 
				{"position":"absolute", "top":"0px", "left":"0px", "z-index":options.resultHighlightZIndex}, 
				options.resultHighlightLineWidth);
		standingHighlight.add(feature, options.resultHighlightColor);
		Mapbender.events.afterMapRequest.register( function(){
			standingHighlight.paint();
		});

	});

	Mapbender.modules[options.target[0]].rowmouseover.register(function(row){
		var me = Mapbender.modules[options.target[0]];
		var modelIndex = $(row).data("modelindex");
		var feature = me.model.getFeature(modelIndex);
	
		if (options.maxHighlightedPoints > 0 && feature.getTotalPointCount() > options.maxHighlightedPoints) {
			feature = feature.getBBox4();
		}
		
		me.resultHighlight = new Highlight(
				[options.target[1],options.target[2]],
				"resultListHighlight", 
				{"position":"absolute", "top":"0px", "left":"0px", "z-index":options.resultHighlightZIndex}, 
				options.resultHighlightLineWidth);
	
		me.resultHighlight.add(feature, options.resultHighlightColor);
		me.resultHighlight.paint();
	});
	
	Mapbender.modules[options.target[0]].rowmouseout.register(function(row){
		var me = Mapbender.modules[options.target[0]];
		var modelIndex = $(row).data("modelindex");
		var feature = me.model.getFeature(modelIndex);
		
		if (options.maxHighlightedPoints > 0 && feature.getTotalPointCount() > options.maxHighlightedPoints) {
			feature = feature.getBBox4();
		}
	
		me.resultHighlight = new Highlight(
				[options.target[1],options.target[2]], 
				"resultListHighlight", 
				{"position":"absolute", "top":"0px", "left":"0px", "z-index":options.resultHighlightZIndex}, 
				options.resultHighlightLineWidth);
		
		me.resultHighlight.del(feature, options.resultHighlightColor);
		me.resultHighlight.paint();
	});
});
