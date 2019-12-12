INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('admin_wfs_metadata','admin_wfs_metadata','GUI with tab, search modules',1);

-- give root access to admin_wms_metadata
INSERT INTO gui_mb_user (fkey_gui_id, fkey_mb_user_id, mb_user_type) VALUES ('admin_wfs_metadata', 1, 'owner');

INSERT into gui_gui_category VALUES ('admin_wfs_metadata', 1);

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','body',1,1,'body (obligatory)','','body','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_jstree',1,1,'jsTree','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jsTree.v.0.9.9a2/jquery.tree.js','','','http://www.jstree.com/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_jstree_checkbox',1,0,'jsTree checkbox plugin','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jsTree.v.0.9.9a2/plugins/jquery.tree.checkbox.js','','','http://www.jstree.com/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_layout',1,1,'','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery.layout.all-1.2.0/jquery.layout.min.js','','','http://layout.jquery-dev.net');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_metadata',1,1,'Metadata plugin','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery.metadata.2.1/jquery.metadata.min.js','','','http://plugins.jquery.com/project/metadata');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui',1,1,'The jQuery UI core','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.button.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_dialog',5,1,'Module to manage jQuery UI dialog windows with multiple options for customization.','','div','','',-1,-1,NULL ,NULL ,NULL ,'','','div','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_droppable',4,1,'jQuery UI droppable','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse,jq_ui_draggable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_effects',1,1,'Effects from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','jq_ui_effects.php','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.effects.core.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_position',1,1,'jQuery UI position','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','jq_validate',1,1,'The jQuery validation plugin','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../javascripts/jq_validate.js','../extensions/jquery-validate/jquery.validate.min.js','','','http://docs.jquery.com/Plugins/Validation');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_helpDialog',1,1,'','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','mb_helpDialog.js','','jq_ui_dialog, jq_metadata','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_md_featuretype',3,1,'Edit featuretype metadata','Edit featuretype metadata','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_metadata_featuretype.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_md_featuretype_tree',2,1,'Select a featuretype from a featuretype tree','Select a featuretype from a featuretype tree','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_metadata_featuretypeTree.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_md_wfs_edit',2,1,'Edit WFS metadata','Edit WFS metadata','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'overflow:auto','','div','../plugins/mb_metadata_wfs_edit.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_md_wfs_select',2,1,'Select a WFS','Select WFS','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','<table class=''display''></table>','div','../plugins/mb_metadata_wfs_select.js','','','jq_datatables','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_md_wfs_showOriginal',2,1,'Show original metadata','Show original metadata','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'display:none;','','div','../plugins/mb_metadata_wfs_showOriginal.js','','','jq_ui_dialog','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_md_wfs_submit',1,1,'','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','<span style=''float:right''><input disabled="disabled" type=''button'' value=''Preview metadata''><input type=''submit'' value=''Save metadata''></span>','div','../plugins/mb_metadata_wfs_submit.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_pane',1,1,'','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'height:100%;width:100%','','div','../plugins/mb_pane.js','','','jq_layout','http://layout.jquery-dev.net');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','mb_tabs_horizontal',3,1,'Puts existing elements into horizontal tabs, using jQuery UI tabs. List the elements comma-separated under target, and make sure they have a title.','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','<ul></ul><div class=''ui-layout-content''></div>','div','../plugins/mb_tabs_horizontal.js','','mb_md_wfs_select,mb_md_wfs_edit,mb_md_featuretype','jq_ui_tabs','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('admin_wfs_metadata','switchLocale_noreload',8,1,'changes the locale without reloading the client','Set language','div','','',900,20,50,30,5,'','<form id="form_switch_locale" action="window.location.href" name="form_switch_locale" target="parent"><select id="language" name="language" onchange="validate_locale()"></select></form>','div','mod_switchLocale_noreload.php','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'body', 'css_class_bg', 'body{ background-color: #ffffff; margin: 5 5 5 5 }
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
}', 'to define the color of the body', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'body', 'favicon', '../img/favicon.png', 'favicon', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'body', 'includeWhileLoading', '', 'show splash screen while the application is loading', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'body', 'jq_datatables_css', '../extensions/dataTables-1.5/media/css/site_jui.ccss.css', 'css-file for jQuery datatables', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'body', 'jq_datatables_css_ui', '../extensions/dataTables-1.5/media/css/demo_table_jui.min.css', 'css-file for jQuery datatables', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'body', 'jq_ui_theme', '../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css', 'UI Theme from Themeroller', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'body', 'use_load_message', 'true', 'show splash screen while the application is loading', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_datatables', 'defaultCss', '../extensions/dataTables-1.5/media/css/demo_table_jui.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui', 'css', '../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'blind', '1', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'bounce', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'clip', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'drop', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'explode', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'fold', '1', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'highlight', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'pulsate', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'scale', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'shake', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'slide', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_ui_effects', 'transfer', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'jq_validate', 'css', 'label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }', '', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_md_featuretype', 'inputs', '[
    {
        "method": "init",
        "title": "initialize wfs data",
        "linkedTo": [
            {
                "id": "mb_md_wfs_select",
                "event": "selected",
                "attr": "wfsId" 
            } 
        ] 
    },
    {
        "method": "fillForm",
        "title": "initialize featuretype data",
        "linkedTo": [
            {
                "id": "mb_md_featuretype_tree",
                "event": "selected",
                "attr": "featuretype" 
            } 
        ] 
    },
    {
        "method": "serialize",
        "linkedTo": [
            {
                "id": "mb_md_wfs_submit",
                "event": "submit",
                "attr": "callback" 
            } 
        ] 
    },
    {
        "method": "fill",
        "linkedTo": [
            {
                "id": "mb_md_wfs_showOriginal",
                "event": "replaceMetadata",
                "attr": "data" 
            } 
        ] 
    }
] ', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_md_featuretype_tree', 'inputs', '[
    {
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_featuretype",
                "event": "initialized",
                "attr": "wfsId" 
            } 
        ] 
    }
] ', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_md_wfs_edit', 'inputs', '[
    {
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_wfs_select",
                "event": "selected",
                "attr": "wfsId" 
            } 
        ] 
    },
    {
        "method": "serialize",
        "linkedTo": [
            {
                "id": "mb_md_wfs_submit",
                "event": "submit",
                "attr": "callback" 
            } 
        ] 
    },
    {
        "method": "fill",
        "linkedTo": [
            {
                "id": "mb_md_wfs_showOriginal",
                "event": "replaceMetadata",
                "attr": "data" 
            } 
        ] 
    }
]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_md_wfs_showOriginal', 'differentFromOriginalCss', '.differentFromOriginal{
background-color:#FFFACD;
}', '', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_md_wfs_showOriginal', 'inputs', '[
    {
        "method": "init",
        "title": "initialize",
        "linkedTo": [
            {
                "id": "mb_md_wfs_edit",
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
                "id": "mb_md_featuretype",
                "event": "showOriginalFeaturetypeMetadata",
                "attr": "data" 
            } 
        ] 
    }
]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_md_wfs_submit', 'inputs', '[
    {
        "method": "enable",
        "linkedTo": [
            {
                "id": "mb_md_wfs_select",
                "event": "selected"
            } 
        ] 
    }
]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_pane', 'center', 'mb_tabs_horizontal', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_pane', 'south', 'mb_md_wfs_submit', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'mb_tabs_horizontal', 'inputs', '[
    {
        "type": "id",
        "method": "select",
        "title": "Select a tab",
        "linkedTo": [
            {
                "id": "mb_md_wfs_select",
                "event": "selected",
                "value": "mb_md_wfs_edit" 
            } 
        ] 
    }
] ', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('admin_wfs_metadata', 'switchLocale_noreload', 'languages', 'de,en,bg,gr,nl,hu,it,fr,es,pt', '', 'var');

