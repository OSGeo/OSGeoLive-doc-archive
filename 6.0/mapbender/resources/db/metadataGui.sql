
INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('admin_wms_metadata','admin_wms_metadata','GUI with tab, search modules',1);

-- give root access to admin_wms_metadata
INSERT INTO gui_mb_user (fkey_gui_id, fkey_mb_user_id, mb_user_type) VALUES ('admin_wms_metadata', 1, 'owner');

INSERT into gui_gui_category VALUES ('admin_wms_metadata', 1);

DELETE FROM gui_element WHERE fkey_gui_id = 'admin_wms_metadata';

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','body',1,1,'body (obligatory)','','body','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','changeEPSG',2,1,'change EPSG, Postgres required, overview is targed for full extent','Change Projection','select','','',432,25,107,24,1,'','<option value="">undefined</option>
<option value="EPSG:4326">EPSG:4326</option>
<option value="EPSG:31466">EPSG:31466</option>
<option value="EPSG:31467">EPSG:31467</option>
<option value="EPSG:31468">EPSG:31468</option>
<option value="EPSG:31469">EPSG:31469</option>','select','mod_changeEPSG.js','','overview','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_jstree',1,0,'jsTree','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jsTree.v.0.9.9a2/jquery.tree.js','','','http://www.jstree.com/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_jstree_1',1,1,'jsTree','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jsTree.v.1.0rc/jquery.jstree.min.js','','','http://www.jstree.com/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_jstree_checkbox',1,0,'jsTree checkbox plugin','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jsTree.v.0.9.9a2/plugins/jquery.tree.checkbox.testbaudson.js','','','http://www.jstree.com/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_layout',1,1,'','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery.layout.all-1.2.0/jquery.layout.min.js','','','http://layout.jquery-dev.net');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_metadata',1,1,'Metadata plugin','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery.metadata.2.1/jquery.metadata.min.js','','','http://plugins.jquery.com/project/metadata');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui',1,1,'The jQuery UI core','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.button.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_dialog',5,1,'Module to manage jQuery UI dialog windows with multiple options for customization.','','div','','',-1,-1,NULL ,NULL ,NULL ,'','','div','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_droppable',4,1,'jQuery UI droppable','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse,jq_ui_draggable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_effects',1,1,'Effects from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','jq_ui_effects.php','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.effects.core.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_position',1,1,'jQuery UI position','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','jq_validate',1,1,'The jQuery validation plugin','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../javascripts/jq_validate.js','../extensions/jquery-validate/jquery.validate.min.js','','','http://docs.jquery.com/Plugins/Validation');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mapframe1',1,1,'frame for a map','','div','','',230,55,200,200,3,'border:1px solid black;overflow:hidden;background-color:#ffffff;display:none;','','div','../plugins/mb_map.js','../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php','','','http://www.mapbender.org/index.php/Mapframe');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_helpDialog',1,1,'','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','mb_helpDialog.js','','jq_ui_dialog, jq_metadata','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_md_edit',2,1,'Edit WMS metadata','Edit WMS metadata','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'overflow:auto','','div','../plugins/mb_metadata_edit.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_md_layer',3,1,'Edit layer metadata','Edit layer metadata','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_metadata_layer.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_md_layer_preview',4,1,'Allows selection of a preview image of a Map','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_metadata_layerPreview.js','mod_addWMSgeneralFunctions.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_md_layer_preview_save',2,1,'','','img','../img/button_gray/wmc_save_off.png','',NULL ,NULL ,NULL ,NULL ,NULL ,'display: none;','','','../plugins/mb_metadata_saveLayerPreview.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_md_layer_tree',2,1,'Select a layer from a layer tree','Select a layer from a layer tree','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_metadata_layerTree.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_md_select',2,1,'Select a WMS','Select WMS','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','<table class=''display''></table>','div','../plugins/mb_metadata_select.js','','','jq_datatables','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_md_showOriginal',2,1,'Show original metadata','Show original metadata','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'display:none;','','div','../plugins/mb_metadata_showOriginal.js','','','jq_ui_dialog','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_md_submit',1,1,'','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','<span style=''float:right''><input disabled="disabled" type=''button'' value=''Preview metadata''><input disabled="disabled" type=''submit'' value=''Save metadata''></span>','div','../plugins/mb_metadata_submit.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_pane',1,1,'','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'height:100%;width:100%','','div','../plugins/mb_pane.js','','','jq_layout','http://layout.jquery-dev.net');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','mb_tabs_horizontal',3,1,'Puts existing elements into horizontal tabs, using jQuery UI tabs. List the elements comma-separated under target, and make sure they have a title.','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','<ul></ul><div class=''ui-layout-content''></div>','div','../plugins/mb_tabs_horizontal.js','','mb_md_select,mb_md_edit,mb_md_layer','jq_ui_tabs','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','pan1',2,1,'pan','Pan','img','../img/button_gray/pan_off.png','',270,10,24,24,1,'','','','mod_pan.js','','mapframe1','','http://www.mapbender.org/index.php/Pan');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','sandclock',2,1,'displays a sand clock while waiting for requests','','div','','',80,0,NULL ,NULL ,NULL ,'','','div','mod_sandclock.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','scaleSelect',2,1,'Scale-Selectbox','Scale Select','select','','',555,25,100,20,1,'','<option value = ''''>Scale</option> <option value=''100''>1 : 100</option> <option value=''250''>1 : 250</option> <option value=''500''>1 : 500</option> <option value=''1000''>1 : 1000</option> <option value=''2500''>1 : 2500</option> <option value=''5000''>1 : 5000</option> <option value=''10000''>1 : 10000</option> <option value=''25000''>1 : 25000</option> <option value=''30000''>1 : 30000</option> <option value=''50000''>1 : 50000</option> <option value=''75000''>1 : 75000</option> <option value=''100000''>1 : 100000</option> <option value=''200000''>1 : 200000</option> <option value=''300000''>1 : 300000</option> <option value=''400000''>1 : 400000</option> <option value=''500000''>1 : 500000</option> <option value=''600000''>1 : 600000</option> <option value=''700000''>1 : 700000</option> <option value=''800000''>1 : 800000</option> <option value=''900000''>1 : 900000</option> <option value=''1000000''>1 : 1000000</option>','select','../plugins/mb_selectScale.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','selArea1',2,1,'zoombox','Zoom by rectangle','img','../img/button_gray/selArea_off.png','',295,10,24,24,3,'display: none;','','','mod_selArea.js','mod_box1.js','mapframe1','','http://www.mapbender.org/index.php/SelArea1');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','switchLocale_noreload',8,1,'changes the locale without reloading the client','Set language','div','','',900,20,50,30,5,'','<form id="form_switch_locale" action="window.location.href" name="form_switch_locale" target="parent"><select id="language" name="language" onchange="validate_locale()"></select></form>','div','mod_switchLocale_noreload.php','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','zoomFull',2,1,'zoom to full extent button','Display complete map','img','../img/button_gray/zoomFull_off.png','',320,10,24,24,2,'display: none;','','','mod_zoomFull.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomFull');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','zoomIn1',2,1,'zoomIn button','Zoom in','img','../img/button_gray/zoomIn2_off.png','',220,10,24,24,1,'','','','mod_zoomIn1.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomIn');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wms_metadata','zoomOut1',2,1,'zoomOut button','Zoom out','img','../img/button_gray/zoomOut2_off.png','',245,10,24,24,1,'','','','mod_zoomOut1.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomOut');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'body', 'css_class_bg', 'body{ background-color: #ffffff; margin: 5 5 5 5 }
* {
        font-family: Verdana;
        font-size: 96%;
}

label {
        width: 250px;
        height: 40px;
        float: left;
}

p {
        clear: both;
}

input {
        font-weight: bold;
        vertical-align: top;
        width: 250px;
        height: 25px;
}

fieldset {
        margin-top: 10px;
}

.metadata_span {
        padding-right: 30px;
        vertical-align: top;
}

.help-dialog {
        cursor: pointer;
}

.metadata_img, .help-dialog, .original-metadata-wms {
        width: 25px;
        height: 25px;
        vertical-align: middle;
}

.metadata_selectbox {
        height: 66px;
        width: 250px;
        vertical-align: top;
}

.label_classification {
        width: 150px;
        height: 40px;
        float: left;
}

div#choose {
        float: left;
        width: 35%;
        height:100%;
position:relative;
}

div#layer {
        margin-left: 35%;
        width: 65%;
}

div#preview {
        padding: 5px 0px 0px 35%;
        width: 25%;
        float: left;
}

div#classification {
        margin-left: 60%;
        padding-top: 5px;
        width: 40%;
}

div#buttons {
        float: left;
}

div#selectbox {
        margin-left: 280px;
        padding-top: 60px;
}', 'to define the color of the body', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'body', 'favicon', '../img/favicon.png', 'favicon', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'body', 'includeWhileLoading', '', 'show splash screen while the application is loading', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'body', 'jq_datatables_css', '../extensions/dataTables-1.5/media/css/site_jui.css', 'css-file for jQuery datatables', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'body', 'jq_datatables_css_ui', '../extensions/dataTables-1.5/media/css/demo_table_jui.min.css', 'css-file for jQuery datatables', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'body', 'jq_ui_theme', '../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css', 'UI Theme from Themeroller', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'body', 'use_load_message', 'true', 'show splash screen while the application is loading', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_datatables', 'defaultCss', '../extensions/dataTables-1.5/media/css/demo_table_jui.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui', 'css', '../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'blind', '1', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'bounce', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'clip', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'drop', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'explode', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'fold', '1', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'highlight', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'pulsate', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'scale', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'shake', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'slide', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_ui_effects', 'transfer', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'jq_validate', 'css', 'label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }', '', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mapframe1', 'skipWmsIfSrsNotSupported', '0', 'if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mapframe1', 'slippy', '0', '1 = Activates an animated, pseudo slippy map', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_edit', 'inputs', '[
    {
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_select",
                "event": "selected",
                "attr": "wmsId" 
            } 
        ] 
    },
    {
        "method": "serialize",
        "linkedTo": [
            {
                "id": "mb_md_submit",
                "event": "submit",
                "attr": "callback" 
            } 
        ] 
    },
    {
        "method": "fill",
        "linkedTo": [
            {
                "id": "mb_md_showOriginal",
                "event": "replaceMetadata",
                "attr": "data" 
            } 
        ] 
    }
]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_layer', 'inputs', '[
    {
        "method": "init",
        "title": "initialize wms data",
        "linkedTo": [
            {
                "id": "mb_md_select",
                "event": "selected",
                "attr": "wmsId" 
            } 
        ] 
    },
    {
        "method": "fillForm",
        "title": "initialize layer data",
        "linkedTo": [
            {
                "id": "mb_md_layer_tree",
                "event": "selected",
                "attr": "layer" 
            } 
        ] 
    },
    {
        "method": "serialize",
        "linkedTo": [
            {
                "id": "mb_md_submit",
                "event": "submit",
                "attr": "callback" 
            } 
        ] 
    },
    {
        "method": "fill",
        "linkedTo": [
            {
                "id": "mb_md_showOriginal",
                "event": "replaceMetadata",
                "attr": "data" 
            } 
        ] 
    }
] ', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_layer_preview', 'inputs', '[
    {   
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {   
                "id": "mb_md_layer",
                "event": "initialized",
                "attr": "wmsId"
            }   
        ]   
    },  
    {   
        "method":"layer",
        "title":"set the current Layer",
        "linkedTo": [
            {   
                "id":"mb_md_layer_tree",
                "event":"selected",
                "attr":"layer"
            }   
        ]   

    }   
     
]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_layer_preview', 'map', 'mapframe1', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_layer_preview', 'toolbarLower', '[''changeEPSG'',''scaleSelect'']', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_layer_preview', 'toolbarUpper', '[''zoomFull'',''zoomOut1'',''zoomIn1'',''selArea1'',''pan1'',''mb_md_layer_preview_save'']', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_layer_preview_save', 'inputs', '[
    {   
        "method":"setLayer",
        "title":"set the current Layer",
        "linkedTo": [
            {   
                "id":"mb_md_layer_tree",
                "event":"selected",
                "attr":"layer"
            }   
        ]   

    }   
     
]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_layer_tree', 'inputs', '[
    {
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_layer",
                "event": "initialized",
                "attr": "wmsId" 
            } 
        ] 
    },
    {
        "method": "serialize",
        "linkedTo": [
            {
                "id": "mb_md_submit",
                "event": "submit",
                "attr": "callback" 
            } 
        ] 
    }
] ', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_showOriginal', 'differentFromOriginalCss', '.differentFromOriginal{
background-color:#FFFACD;
}', 'css for class differentFromOriginal', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_showOriginal', 'inputs', '[
    {
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_edit",
                "event": "showOriginalMetadata",
                "attr": "data" 
            } 
        ] 
    },
    {
        "method": "initLayer",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_layer",
                "event": "showOriginalLayerMetadata",
                "attr": "data" 
            } 
        ] 
    }
]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_md_submit', 'inputs', '[
    {
        "method": "enable",
        "linkedTo": [
            {
                "id": "mb_md_select",
                "event": "selected"
            } 
        ] 
    }
]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_pane', 'center', 'mb_tabs_horizontal', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_pane', 'south', 'mb_md_submit', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'mb_tabs_horizontal', 'inputs', '[
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
] ', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'sandclock', 'mod_sandclock_image', '../img/sandclock.gif', 'define a sandclock image ', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wms_metadata', 'switchLocale_noreload', 'languages', 'de,en,bg,gr,nl,hu,it,fr,es,pt', '', 'var');

