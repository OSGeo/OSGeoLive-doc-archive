INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('admin_wmc_metadata','admin_wmc_metadata','GUI with tab, search modules',1);

-- give root access to admin_wms_metadata
INSERT INTO gui_mb_user (fkey_gui_id, fkey_mb_user_id, mb_user_type) VALUES ('admin_wmc_metadata', 1, 'owner');

INSERT into gui_gui_category VALUES ('admin_wmc_metadata', 1);

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_md_wmc_submit',1,1,'','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','<span style=''float:right''><input disabled="disabled" type=''button'' value=''Preview metadata''><input type=''submit'' value=''Save metadata''></span>','div','../plugins/mb_metadata_wmc_submit.js','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_submit','inputs','[
    {
        "method": "enable",
        "linkedTo": [
            {
                "id": "mb_md_wmc_select",
                "event": "selected"
            } 
        ] 
    }
]','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_position',1,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_datatables','defaultCss','../extensions/dataTables-1.5/media/css/demo_table_jui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_jstree',1,1,'jsTree','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jsTree.v.0.9.9a2/jquery.tree.js','','','http://www.jstree.com/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_jstree_checkbox',1,0,'jsTree checkbox plugin','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jsTree.v.0.9.9a2/plugins/jquery.tree.checkbox.js','','','http://www.jstree.com/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','body',1,1,'body (obligatory)','','body','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','body','css_class_bg','body{ background-color: #ffffff; margin: 5 5 5 5 }
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

.metadata_span {
        padding-right: 30px;
        vertical-align: top;
}

.metadata_img {
        width: 25px;
        height: 25px;
        vertical-align: top;
}

.metadata_selectbox {
        height: 130px;
        width: 250px;
        vertical-align: top;
}

div#choose {
        float: left;
        width: 20%;
        height:100%;
}

div#layer {
        margin-left: 20%;
        width: 80%;
}

div#preview {
        width: 30%;
        padding-top: 5px;
        float: left;
}

div#classification {
        margin-left: 50%;
        padding-top: 5px;
        width: 50%;
}

div#save {
        margin-left: 45em;
}

div#buttons {
        float: left;
}

div#selectbox {
        margin-left: 312px;
        padding-top: 20px;
}','to define the color of the body','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','body','favicon','../img/favicon.ico','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','body','includeWhileLoading','','show splash screen while the application is loading','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','body','jq_datatables_css','../extensions/dataTables-1.5/media/css/site_jui.ccss.css','css-file for jQuery datatables','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','body','jq_datatables_css_ui','../extensions/dataTables-1.5/media/css/demo_table_jui.min.css','css-file for jQuery datatables','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','body','jq_ui_theme','../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css','UI Theme from Themeroller','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','body','use_load_message','true','show splash screen while the application is loading','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_helpDialog',1,1,'','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','mb_helpDialog.js','','jq_ui_dialog, jq_metadata','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_pane',1,1,'','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'height:100%;width:100%','','div','../plugins/mb_pane.js','','','jq_layout','http://layout.jquery-dev.net');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_pane','center','mb_tabs_horizontal','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_pane','css','../extensions/jquery.layout.all-1.2.0/jquery.layout.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_pane','south','mb_md_wmc_submit','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_layout',1,1,'','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery.layout.all-1.2.0/jquery.layout.min.js','','','http://layout.jquery-dev.net');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_metadata',1,1,'Metadata plugin','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery.metadata.2.1/jquery.metadata.min.js','','','http://plugins.jquery.com/project/metadata');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_validate',1,1,'The jQuery validation plugin','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../javascripts/jq_validate.js','../extensions/jquery-validate/jquery.validate.min.js','','','http://docs.jquery.com/Plugins/Validation');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_validate','css','label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }','','text/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_effects',1,1,'Effects from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','jq_ui_effects.php','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.effects.core.min.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','blind','1','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','bounce','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','clip','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','drop','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','explode','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','fold','1','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','highlight','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','pulsate','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','scale','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','shake','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','slide','0','1 = effect active','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui_effects','transfer','0','1 = effect active','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mapframe1',1,1,'frame for a map','','div','','',230,55,200,200,3,'border:1px solid black;overflow:hidden;background-color:#ffffff;display:none;','','div','../plugins/mb_map.js','../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php','','','http://www.mapbender.org/index.php/Mapframe');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mapframe1','skipWmsIfSrsNotSupported','0','if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mapframe1','slippy','0','1 = Activates an animated, pseudo slippy map','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui',1,1,'The jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','jq_ui','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','zoomFull',2,1,'zoom to full extent button','gesamte Karte anzeigen','img','../img/button_gray/zoomFull_off.png','',320,10,24,24,2,'display: none;','','','mod_zoomFull.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomFull');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_md_wmc_preview_save',2,1,'','','img','../img/button_gray/wmc_save_off.png','',NULL ,NULL,NULL ,NULL,NULL ,'display: none;','','','../plugins/mb_metadata_saveWmcPreview.js','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_preview_save','inputs','[
    {   
        "method":"setWmc",
        "title":"set the current Wmc",
        "linkedTo": [
            {   
                "id":"mb_md_wmc_select",
                "event":"selected",
                "attr":"wmcId"
            }   
        ]   

    }   
     
]','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_md_wmc_showOriginal',2,1,'Show original metadata','Show original metadata','div','','',NULL ,NULL,NULL ,NULL,NULL ,'display:none;','','div','../plugins/mb_metadata_wmc_showOriginal.js','','','jq_ui_dialog','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_showOriginal','differentFromOriginalCss','.differentFromOriginal{
background-color:#FFFACD;
}','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_showOriginal','inputs','[
    {
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_wmc_edit",
                "event": "showOriginalMetadata",
                "attr": "data" 
            }
        ] 
    }
]','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_md_wmc_edit',2,1,'Edit WMC metadata','Edit WMC metadata','div','','',NULL ,NULL,NULL ,NULL,NULL ,'overflow:auto','','div','../plugins/mb_metadata_wmc_edit.js','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_edit','inputs','[
    {
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_wmc_select",
                "event": "selected",
                "attr": "wmcId" 
            } 
        ] 
    },
    {
        "method": "serialize",
        "linkedTo": [
            {
                "id": "mb_md_wmc_submit",
                "event": "submit",
                "attr": "callback" 
            } 
        ] 
    },
    {
        "method": "fill",
        "linkedTo": [
            {
                "id": "mb_md_wmc_showOriginal",
                "event": "replaceMetadata",
                "attr": "data" 
            } 
        ] 
    }
]','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','loadwmc',2,1,'load workspace from WMC','laden eines Web Map Context Dokumentes','img','../img/button_blink_red/wmc_load_off.png','onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");''',895,60,24,24,1,'display:none;','','','mod_loadwmc.php','popup.js','mapframe1','jq_ui_dialog,jq_ui_tabs,jq_upload,jq_datatables','http://www.mapbender.org/index.php/LoadWMC');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','loadwmc','deleteWmc','0','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','loadwmc','inputs','[
    {   
        "method": "load",
        "linkedTo": [
            {   
                "id": "mb_md_wmc_select",
                "event": "selected",
                "attr": "wmcId"
            }   
        ]   
    }
]','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','loadwmc','publishWmc','1','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','loadwmc','saveWmcTarget','savewmc','target for savewmc ','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','changeEPSG',2,1,'change EPSG, Postgres required, overview is targed for full extent','Projektion ändern','select','','',432,25,107,24,1,'','<option value="">undefined</option>
<option value="EPSG:4326">EPSG:4326</option>
<option value="EPSG:31466">EPSG:31466</option>
<option value="EPSG:31467">EPSG:31467</option>
<option value="EPSG:31468">EPSG:31468</option>
<option value="EPSG:31469">EPSG:31469</option>','select','mod_changeEPSG.js','','overview','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','pan1',2,1,'pan','Ausschnitt verschieben','img','../img/button_gray/pan_off.png','',270,10,24,24,1,'','','','mod_pan.js','','mapframe1','','http://www.mapbender.org/index.php/Pan');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','selArea1',2,1,'zoombox','Ausschnitt wählen','img','../img/button_gray/selArea_off.png','',295,10,24,24,3,'display: none;','','','mod_selArea.js','mod_box1.js','mapframe1','','http://www.mapbender.org/index.php/SelArea1');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_md_wmc_select',2,1,'Select a WMC','Select WMC','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','<table class=''display''></table>','div','../plugins/mb_metadata_wmc_select.js','','','jq_datatables','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','zoomIn1',2,1,'zoomIn button','In die Karte hineinzoomen','img','../img/button_gray/zoomIn2_off.png','',220,10,24,24,1,'','','','mod_zoomIn1.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomIn');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','zoomOut1',2,1,'zoomOut button','Aus der Karte herauszoomen','img','../img/button_gray/zoomOut2_off.png','',245,10,24,24,1,'','','','mod_zoomOut1.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomOut');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','scaleSelect',2,1,'Scale-Selectbox','Auswahl des Maßstabes','select','','',555,25,100,24,1,'','<option value = ''''>Scale</option> <option value=''100''>1 : 100</option> <option value=''250''>1 : 250</option> <option value=''500''>1 : 500</option> <option value=''1000''>1 : 1000</option> <option value=''2500''>1 : 2500</option> <option value=''5000''>1 : 5000</option> <option value=''10000''>1 : 10000</option> <option value=''25000''>1 : 25000</option> <option value=''30000''>1 : 30000</option> <option value=''50000''>1 : 50000</option> <option value=''75000''>1 : 75000</option> <option value=''100000''>1 : 100000</option> <option value=''200000''>1 : 200000</option> <option value=''300000''>1 : 300000</option> <option value=''400000''>1 : 400000</option> <option value=''500000''>1 : 500000</option> <option value=''600000''>1 : 600000</option> <option value=''700000''>1 : 700000</option> <option value=''800000''>1 : 800000</option> <option value=''900000''>1 : 900000</option> <option value=''1000000''>1 : 1000000</option>','select','../plugins/mb_selectScale.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_md_featuretype',3,0,'Edit featuretype metadata','Edit featuretype metadata','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_metadata_featuretype.js','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_featuretype','inputs','[
    {
        "method": "init",
        "title": "initialize wfs data",
        "linkedTo": [
            {
                "id": "mb_md_wmc_select",
                "event": "selected",
                "attr": "wmcId" 
            } 
        ] 
    },
    {
        "method": "serialize",
        "linkedTo": [
            {
                "id": "mb_md_wmc_submit",
                "event": "submit",
                "attr": "callback" 
            } 
        ] 
    },
    {
        "method": "fill",
        "linkedTo": [
            {
                "id": "mb_md_wmc_showOriginal",
                "event": "replaceMetadata",
                "attr": "data" 
            } 
        ] 
    }
] ','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_tabs_horizontal',3,1,'Puts existing elements into horizontal tabs, using jQuery UI tabs. List the elements comma-separated under target, and make sure they have a title.','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','<ul></ul><div class=''ui-layout-content''></div>','div','../plugins/mb_tabs_horizontal.js','','mb_md_wmc_select,mb_md_wmc_edit','jq_ui_tabs','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_tabs_horizontal','inputs','[
    {
        "type": "id",
        "method": "select",
        "title": "Select a tab",
        "linkedTo": [
            {
                "id": "mb_md_wmc_select",
                "event": "selected",
                "value": "mb_md_wmc_edit" 
            } 
        ] 
    }
] ','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_droppable',4,1,'jQuery UI droppable','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse,jq_ui_draggable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','mb_md_wmc_preview',4,1,'Allows selection of a preview image of a wmc','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_metadata_layerPreview.js','mod_addWMSgeneralFunctions.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_preview','inputs','[
    {   
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {   
                "id": "mb_md_wmc_select",
                "event": "selected"
            }   
        ]   
    }
]','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_preview','map','mapframe1','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_preview','toolbarLower','[''changeEPSG'',''scaleSelect'']','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','mb_md_wmc_preview','toolbarUpper','[''zoomFull'',''zoomOut1'',''zoomIn1'',''selArea1'',''pan1'',''mb_md_wmc_preview_save'']','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.button.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','jq_ui_dialog',5,1,'Module to manage jQuery UI dialog windows with multiple options for customization.','','div','','',-1,-1,NULL ,NULL,NULL ,'','','div','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wmc_metadata','switchLocale_noreload',8,1,'changes the locale without reloading the client','Sprache auswählen','div','','',900,20,50,30,5,'','<form id="form_switch_locale" action="window.location.href" name="form_switch_locale" target="parent"><select id="language" name="language" onchange="validate_locale()"></select></form>','div','mod_switchLocale_noreload.php','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wmc_metadata','switchLocale_noreload','languages','de,en,bg,gr,nl,hu,it,fr,es,pt','','var');

