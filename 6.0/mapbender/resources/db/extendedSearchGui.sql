INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('extended_search','extended_search','Extented metadata search form',1);

-- give root access to extended_search
INSERT INTO gui_mb_user (fkey_gui_id, fkey_mb_user_id, mb_user_type) VALUES ('extended_search', 1, 'owner');

DELETE FROM gui_element WHERE fkey_gui_id = 'extended_search';


INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','mb_helpDialog',1,1,'Mapbender wrapper for helptexts in jq dialogs','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','','mb_helpDialog.js','','jq_ui, jq_metadata','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','jq_datatables','defaultCss','../extensions/dataTables-1.5/media/css/demo_table_jui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_upload',1,1,'','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../plugins/jq_upload.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','mapframe1',1,1,'Frame for a map','','div','','',10,218,280,154,20,'overflow:hidden;background-color:#ffffff','','div','../plugins/mb_map.js','../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWmcObj.php','','','http://www.mapbender.org/index.php/Mapframe');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','mapframe1','skipWmsIfSrsNotSupported','0','if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','mapframe1','slippy','1','1 = Activates an animated, pseudo slippy map','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_position',1,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','body',1,1,'body (obligatory)','','body','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','body','css_class_bg','body{ background-color: #f3f3f3; }','to define the color of the body','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','body','css_file_body','../css/mapbender.css','file/css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','body','favicon','../img/favicon.png','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','body','jq_ui_theme','../extensions/jquery-ui-1.8.1.custom/css/custom-theme/jquery-ui-1.8.4.custom.css','UI Theme from Themeroller','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','body','popupcss','../css/popup.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','body','tablesortercss','../css/tablesorter.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','body','use_load_message','false','show splash screen while the application is loading','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_metadata',1,1,'jQuery Metadata plugin - selects elements by tag names out of html?','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','','../extensions/jquery.metadata.2.1/jquery.metadata.min.js','','','http://plugins.jquery.com/project/metadata');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui',1,1,'jQuery UI core','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','jq_ui','css','../extensions/jquery-ui-1.8.1.custom/css/custom-theme/jquery-ui-1.8.4.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','imprint',2,0,'Information about the owner of the gui','','iframe','../html/tab_imprint.html','frameborder = "0" ',1,1,1,1,5,'visibility:hidden;','','iframe','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','dependentDiv',2,0,'displays infos in a sticky div-tag','','div','','',81,-19,1,1,NULL ,'visibility:visible;position:absolute;font-size: 11px;font-family: \"Arial\", sans-serif;','','div','mod_dependentDiv.php','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','mapbender',2,1,'Mapbender-Logo','','div','','onclick="javascript:window.open(''http://www.mapbender.org'','''','''');"',81,-19,1,1,30,'font-size : 10px;font-weight : bold;font-family: Arial, Helvetica, sans-serif;color:white;cursor:help;','<span>Ma</span><span style="color: blue;">P</span><span style="color: red;">b</span><span>ender</span><script type="text/javascript"> mb_registerSubFunctions("mod_mapbender()"); function mod_mapbender(){ document.getElementById("mapbender").style.left = parseInt(document.getElementById("mapframe1").style.left) + parseInt(document.getElementById("mapframe1").style.width) - 90; document.getElementById("mapbender").style.top = parseInt(document.getElementById("mapframe1").style.top) + parseInt(document.getElementById("mapframe1").style.height) -1; } </script>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','copyright',2,1,'a Copyright in the map','','div','','',0,0,0,0,0,'','','div','mod_copyright.php','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','copyright','mod_copyright_text','©IMAGI-rlp','Copyright text displayed in mapframe1','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','loadData',2,0,'IFRAME, um Daten zu laden','','iframe','../html/mod_blank.html','frameborder = "0" ',0,0,1,1,NULL ,'visibility:visible','','iframe','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','mapcheck',2,1,'alert if a map-image fails to load','','div','','',0,0,NULL ,NULL,NULL ,'','','div','','mod_mapcheck.php','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','zoomOut1',2,1,'zoomOut button','','img','../img/button_blink_red/zoomOut2_off.png','',305,233,24,24,20,'','','','mod_zoomOut1.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','sandclock',2,1,'displays a sand clock while waiting for requests','','div','','',80,0,0,0,0,'','','div','mod_sandclock.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','back',2,1,'History.back()','','img','../img/button_blink_red/back_off_disabled.png','',305,358,24,24,20,'','','','mod_back.php','','mapframe1,overview0','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','forward',2,1,'History.forward()','','img','../img/button_blink_red/forward_off_disabled.png','',305,333,24,24,20,'','','','mod_forward.php','','mapframe1,overview0','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','navFrame',2,1,'navigation mapborder','','div','','',0,0,NULL ,NULL,20,'font-size:1px;','','div','mod_navFrame.php','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','pan1',2,1,'pan','','img','../img/button_blink_red/pan_off.png','',305,308,24,24,20,'','','','mod_pan.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','selArea1',2,1,'zoombox','','img','../img/button_blink_red/selArea_off.png','',305,258,24,24,20,'','','','mod_selArea.js','mod_box1.js','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','zoomFull',2,1,'zoom to full extent button','','img','../img/button_blink_red/zoomFull_off.png','',305,283,24,24,20,'','','img','mod_zoomFull.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','zoomIn1',2,1,'zoomIn button','','img','../img/button_blink_red/zoomIn2_off.png','',305,208,24,24,20,'','','','mod_zoomIn1.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','renderGML',2,0,'renders a gml contained in $_SESSION[''GML'']','','div','','',NULL ,NULL,NULL ,NULL,1,'','','div','../javascripts/mod_renderGML.php','mod_highlight.php','overview,mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','classSearchResources',3,1,'Div which shows the different bsearchable resources for metadata search','Resources','div','','',NULL ,NULL,NULL ,NULL,20,'','<fieldset><legend id="legendSearchResources" name="legendSearchResources">Types of Resources</legend>
		<img class="help-dialog" id="helpSearchResources" title="Help" help="{text:''Help SearchResources''}"  src="../img/questionmark.png" align="right"></img>

		<div id=''searchResources'' name=''searchResources''>

			<input name=''checkResourcesWms'' id=''checkResourcesWms'' type=''checkbox'' value=''wms''>

			<label id="labelCheckResourcesWms" name="labelCheckResourcesWms">Viewing Services</label>

			<input name=''checkResourcesWfs'' id=''checkResourcesWfs'' type=''checkbox'' value=''wfs''>

			<label id="labelCheckResourcesWfs" name="labelCheckResourcesWfs">Search/Download/Digitize Modules</label>

			<input name=''checkResourcesWmc'' id=''checkResourcesWmc'' type=''checkbox'' value=''wmc''>

			<label id="labelCheckResourcesWmc" name="labelCheckResourcesWmc">Map Collections</label>

			<input disabled="disabled" name=''checkResourcesGeorss'' id=''checkResourcesGeorss'' type=''checkbox'' value=''georss''>

			<label disabled="disabled" id="labelCheckResourcesGeorss" name="labelCheckResourcesGeorss">GeoRSS Feeds</label>

		</div>
</fieldset>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','form2',3,1,'Form for the extended mapbender metadata search - this form submits the values as hidden to the search script','','form','','method=''get'' name=''form2'' action=''../php/mod_callMetadata.php'' target=''_top''',0,250,NULL ,NULL,NULL ,'','	<input type="hidden" value='''' name=''searchText''>

	<input type="hidden" value='''' name=''registratingDepartments''>

	<input type="hidden" value='''' name=''isoCategories''>

	<input type="hidden" value='''' name=''inspireThemes''>

	<input type="hidden" value='''' name=''customCategories''>

	<input type="hidden" value='''' name=''regTimeBegin''>

	<input type="hidden" value='''' name=''regTimeEnd''>

	<input type="hidden" value='''' name=''timeBegin''>

	<input type="hidden" value='''' name=''timeEnd''>

	<input type="hidden" value='''' name=''searchBbox''>

	<input type="hidden" value='''' name=''searchTypeBbox''>

	<input type="hidden" value='''' name=''searchResources''>

	<input type="hidden" value='''' name=''orderBy''>	','form','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','mb_tabs_horizontal',3,1,'Puts existing elements into horizontal tabs, using jQuery UI tabs. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,200,700,NULL ,NULL,'','<ul></ul><div class=''ui-layout-content''></div>','div','../plugins/mb_tabs_horizontal.js','','mb_div_collection2,actuality,classifications,provider,classSearchResources','jq_ui_tabs','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('extended_search','mb_tabs_horizontal','inputs','[
    {
        "type": "id",
        "method": "select",
        "title": "Select a tab",
        "linkedTo": [
            {
                "id": "mb_md_select",
                "event": "selected",
                "value": "mb_md_edit" 
            } 
        ] 
    }
] ','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','mb_div_collection1',3,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','mapframe1, zoomIn1,zoomFull,zoomOut1,selArea1,pan1,forward,back,navFrame','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','orderBy',3,1,'Div which shows orderby options for the metadata search','Order','div','','class=''orderBy''',0,80,480,NULL ,20,'','<fieldset>
<legend id="legendOrderBy" name="legendOrderBy">Sort by:</legend>

		<img class="help-dialog" id="helpOrderBy" title="Help" help="{text:''Help OrderBy''}"  src="../img/questionmark.png" align="right"></img>
		<p>
			<input type=''radio'' name=''orderBy'' value=''rank'' id=''orderBy'' checked >
			<label id="labelOrderByRank" name="labelOrderByRank">relevance</label>
			<input type=''radio'' name=''orderBy'' value=''title'' id=''orderBy''>
			<label id="labelOrderByTitle" name="labelOrderByTitle">title</label>
			<input type=''radio'' name=''orderBy'' value=''id'' id=''orderBy''>
			<label id="labelOrderById" name="labelOrderById">identification</label>
			<input type=''radio'' name=''orderBy'' value=''date'' id=''orderBy''>
			<label id="labelOrderByDate" name="labelOrderByDate">date</label>		
		</p>
	</fieldset>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','searchField',3,1,'Div which shows the search field for the metadata search','','div','','class=''searchField''',0,0,480,NULL ,20,'','		<fieldset>
		<legend id="legendSearchTextTitle" name="legendSearchTextTitle">Searchterm(s):</legend>
		<img class="help-dialog" id="helpSearchText" title="Help" help="{text:''Help SearchText''}"  src="../img/questionmark.png" align="right"></img>
			<p>
				<input type=''text'' size=30 name=''searchText'' id=''searchText''> 
			</p>
		</fieldset>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','button',3,1,'Div for the button to submit extended search module','','div','','class=''button''',500,25,60,40,20,'','<button id="search" name="search" type="button" value="Start Search" onclick="validate();">Start Search</button>
','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','actuality',3,1,'Div which shows the date field for temporal filters for metadata search','Wann?','div','','class=''actuality''',NULL ,NULL,NULL ,NULL,20,'','<fieldset>
<legend  id="legendActuality" name="legendActuality">Actuality</legend>
	<fieldset>
		<legend id="legendDateOfPublication" name="legendDateOfPublication">Date of Publication</legend>

		<img class="help-dialog" id="helpDateOfPublication" title="Help" help="{text:''Help DateOfPublication''}"  src="../img/questionmark.png" align="right"></img>

		<table>

			<p>

			<tr>

			<td><label id="labelDateOfPublicationStart" name="labelDateOfPublicationStart"  for="regTimeBegin">Date from</label></td>

			<td><input class=''hasdatepicker'' type=''text'' size=15 name=''regTimeBegin'' id=''regTimeBegin'' > </td>

			</tr>

			<tr>

			<td><label id="labelDateOfPublicationEnd" name="labelDateOfPublicationEnd"  for="regTimeEnd">Date to</label></td>

			<td><input class=''hasdatepicker'' type=''text'' size=15 name=''regTimeEnd'' id=''regTimeEnd'' > </td> 

			</tr>

			</p>

		</table>

	</fieldset>



	<fieldset>

		<legend id="legendDateOfLastRevision" name="legendDateOfLastRevision">Data actuality</legend>

		<img class="help-dialog" id="helpDateOfLastRevision" title="Help" help="{text:''Help DateOfLastRevision''}"  src="../img/questionmark.png" align="right"></img>

			<table>

			<p>

			<tr>

				<td><label id="labelDateOfLastRevisionStart" name="labelDateOfLastRevisionStart"  for="timeBegin" >Date from</label></td>

				<td><input disabled="disabled" type=''text'' size=15 name=''timeBegin'' id=''timeBegin'' > </td>



			</tr>

			<tr>

				<td><label id="labelDateOfLastRevisionEnd" name="labelDateOfLastRevisionEnd"   for="timeEnd">Date to</label></td>
<td><input disabled="disabled" type=''text'' size=15 name=''timeEnd'' id=''timeEnd'' > </td>

			</tr>

			</p>

			</table>

	</fieldset>

</fieldset>
','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','classifications',3,1,'Put category divs into one div collection','Themen','div','','',NULL ,NULL,NULL ,NULL,20,'','<fieldset>
	<legend  id="classificationsLegend" name="classificationsLegend">Classifications</legend>
		<div class="inspire">
			<fieldset>
			<legend id="legendInspireThemes" name="legendInspireThemes">Inspire Themes</legend>
			<img class="help-dialog" id="helpInspireThemes" title="Help" help="{text:''Help Inspire Categories''}"  src="../img/questionmark.png" align="right"></img>
<img id="imageInspireThemes" title="Inspire" src="../img/inspire_tr_36.png" align="right"></img>
				<p>
					<select class=''selectCat'' size=''5'' name=''inspireThemes'' id=''inspireThemes'' multiple ></select>
					<br><a  id="deleteSelection2" name="deleteSelection" href="#"  onclick=''removeListSelections("inspireThemes");''>Drop current selection</a>
				</p>
			</fieldset>
		</div>
		<div class="iso">
			<fieldset>
			<legend  id="legendIsoCategories" name="legendIsoCategories">ISO19115 Themes</legend>
			<img class="help-dialog" id="helpIsoCategories" title="Help" help="{text:''Help ISO Categories''}"  src="../img/questionmark.png" align="right"></img>
				<p> 
					<select class=''selectCat'' size=''5'' name=''isoCategories'' id=''isoCategories'' width="200px"  multiple></select>
					<br><a id="deleteSelection3" name="deleteSelection" href="#"  onclick=''removeListSelections("isoCategories");''>Drop current selection</a>
				</p>
			</fieldset>
		</div>
		<div class="custom">
			<fieldset>
				<legend  id="legendCustomCategories" name="legendCustomCategories">Other Themes</legend>
				<img class="help-dialog" id="helpCustomCategories" title="Help" help="{text:''Help Custom Categories''}"  src="../img/questionmark.png" align="right"></img>
				<p> 
					<select class=''selectCat'' size=''5'' name=''customCategories'' id=''customCategories'' multiple></select>
					<br><a id="deleteSelection4" name="deleteSelection" href="#" onclick=''removeListSelections("customCategories");''>Drop current selection</a>
				</p>
			</fieldset>
		</div>
	</fieldset>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','provider',3,1,'Div which shows a list of providers for the metadata search','Anbieter','div','','',NULL ,NULL,NULL ,NULL,20,'','<fieldset>
<legend  id="legendDepartment" name="legendDepartment">Provider</legend>
<img class="help-dialog" id="helpProvider" title="Help" help="{text:''Help Provider''}"  src="../img/questionmark.png" align="right"></img>
		<p>
			<select class=''selectCat'' size=''5'' name=''registratingDepartments'' id=''registratingDepartments'' multiple ></select>
			<br><a id="deleteSelection1" name="deleteSelection" href="#" c onclick=''removeListSelections("registratingDepartments");''>Drop current selection</a>
		</p>
	</fieldset>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','spatialFilter',3,1,'Div which shows the spatial filter options for the metadata search','','div','','class=''spatialFilter''',NULL ,NULL,NULL ,NULL,20,'','	<fieldset>
		<img class="help-dialog" id="helpSpatialFilter" title="Help" help="{text:''Help SpatialFilter''}"  src="../img/questionmark.png" align="right"></img>
		<p>
			<input type=''checkbox'' name=''searchBbox'' id=''searchBbox''>
			<label  id="labelSpatialFilter" name="labelSpatialFilter">Activate Spatial Filter</label>
			<legend id="labelSpatialFilterType" name="labelSpatialFilterType">how?</legend>
			<input type=''radio'' name=''searchTypeBbox'' value=''intersects'' id=''searchTypeBbox''>
			<label id="labelIntersects" name="labelIntersects" for="bbox">intersects</label>
			<input type=''radio'' name=''searchTypeBbox'' value=''outside'' id=''searchTypeBbox''>
			<label id="labelOutside" name="labelOutside">outside</label>
			<input type=''radio'' name=''searchTypeBbox'' value=''inside'' id=''searchTypeBbox''>
			<label id="labelInside" name="labelInside">fully inside</label>
		</p>
	</fieldset>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','mb_div_collection2',3,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','Wo?','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','mb_div_collection1,spatialFilter','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','form1',3,1,'Form for the extended mapbender metadata search','','form','','method=''post'' name=''form1''',0,0,400,NULL ,NULL,'','','form','../plugins/mb_div_collection.js','../plugins/mb_extendedSearch.js','searchField,button,orderBy,mb_tabs_horizontal','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.button.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_droppable',4,1,'jQuery UI droppable','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse,jq_ui_draggable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.resizable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/resizable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('extended_search','jq_ui_datepicker',5,1,'Datepicker from jQuery UI framework','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.datepicker.min.js','','jq_ui','');
insert into translations (locale,msgid,msgstr) values ('de','Where?','Wo?');
insert into translations (locale,msgid,msgstr) values ('de','When?','Wann?');
insert into translations (locale,msgid,msgstr) values ('de','Themes','Themen');
insert into translations (locale,msgid,msgstr) values ('de','Provider','Anbieter');
insert into translations (locale,msgid,msgstr) values ('de','What?','Was?');
insert into translations (locale,msgid,msgstr) values ('en','Where?','Where?');
insert into translations (locale,msgid,msgstr) values ('en','When?','When?');
insert into translations (locale,msgid,msgstr) values ('en','Themes','Themes');
insert into translations (locale,msgid,msgstr) values ('en','Provider','Provider');
insert into translations (locale,msgid,msgstr) values ('en','What?','What?');


