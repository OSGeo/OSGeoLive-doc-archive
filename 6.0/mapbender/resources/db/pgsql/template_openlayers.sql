INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('template_openlayers','template_openlayers','An OpenLayers map with WMS configured by Mapbender',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','body',1,1,'body (obligatory)','','body','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','description',1,1,'','Description','div','','class=''ui-corner-all'' ',700,30,300,400,1,'background-color:#ffffff;border:1px solid black;padding:10px','This application consists of several elements wrapping OpenLayers components. Currently there are the OpenLayers map, the layer switch, mouse position, keyboard defaults and the WMS input from the Mapbender database.
<br><br>
An administrator can easily customize the OpenLayers application by (de)activating individual elements or configuring WMS. ','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','logo',2,1,'Logo','Logo','img','../img/mapbender_logo_transparent.png','',455,26,NULL ,NULL ,NULL ,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol',1,1,'An OpenLayers Map, configured with WMS from Mapbender application settings','OpenLayers','div','','class=''ui-corner-all''',10,90,600,400,2,'border:1px solid black; margin:10px;background-color:#FFFFFF','','div','../plugins/ol.js','../extensions/OpenLayers-2.8/OpenLayers.js','','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_keyboardDefaults',1,1,'An OpenLayers KeyboardDefaults.
Navigate with Keybords up, down, left and right key.','OpenLayers KeyboardDefaults','div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div','../plugins/ol_keyboardDefaults.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_label',1,1,'Just the label saying OpenLayers','OpenLayers Label','span','','',85,15,NULL ,NULL ,NULL ,'font-family:Trebuchet MS,Helvetica,Arial,sans-serif; font-size:30px; text-shadow:2px 2px 3px gray;','OpenLayers','span','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_layerSwitch',1,1,'An OpenLayers LayerSwitcher','OpenLayers LayerSwitcher','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/ol_layerSwitch.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_logo',1,1,'OpenLayers logo','OpenLayers Logo','img','../img/openlayers/120px-OpenLayers_logo.svg.png','',15,10,60,60,NULL ,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_measureLine',1,0,'An OpenLayers measureLine Control
(currently not working!)','OpenLayers measureLine','div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div','../plugins/ol_measureLine.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_mousePosition',1,1,'An OpenLayers MousePosition','OpenLayers MousePosition','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/ol_mousePosition.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_navigation',1,1,'An OpenLayers Navigation Control','OpenLayers Navigation','div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div','../plugins/ol_navigation.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_navigationHistory',1,1,'An OpenLayers NavigationHistory Control','OpenLayers NavigationHistory','div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div','../plugins/ol_navigationHistory.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_overviewMap',1,1,'An OpenLayers OverviewMap','OpenLayers OverviewMap','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/ol_overview.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_panPanel',1,0,'An OpenLayers PanPanel','OpenLayers PanPanel','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/ol_panPanel.js','','ol','','http://www.mapbender.org/ol_panPanel');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_panZoomBar',1,1,'An OpenLayers PanZoomBar','OpenLayers Layer Switch','div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div','../plugins/ol_panZoomBar.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_permalink',1,1,'An OpenLayers Permalink Control','OpenLayers Permalink','div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div','../plugins/ol_permalink.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_scale',1,1,'An OpenLayers Scale Control','OpenLayers Scale','div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div','../plugins/ol_scale.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_scaleLine',1,1,'An OpenLayers ScaleLine Control','OpenLayers ScaleLine','div','','',NULL ,0,NULL ,NULL ,NULL ,'','','div','../plugins/ol_scaleLine.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_wms',2,1,'Load configured WMS from Mapbender application settings into OpenLayers','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_wms.php','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'favicon', '../img/favicon.png', 'favicon', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'includeWhileLoading', '../include/preliminary_logo_splash.php', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'jquery_UI', '../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'jq_ui_theme', '../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css', 'UI Theme from Themeroller', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'ol_mousePosition', '.olControlMousePosition
{
	background-color:white; 
	width:220px;
	height:15px;
	border:solid gray 1px;
	border-bottom:none;
	left:0px;
	bottom:2px;
	padding-left:8px;
}	', '', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'ol_olControlMeasureItemInactive', '.olControlMeasureItemInactive, .olControlMeasureItemActive
{
  height:24px;
  width:24px;
  border: 1px solid red;
}
.olControlMeasureItemActive
{
  height:24px;
  width:24px;
  border: 1px solid blue;
}	', '', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'ol_panPanel', '.olControlPanPanel {
               width: 100%;
               height: 100%;
               left: 0;
               top: 0;
          }
          .olControlPanPanel .olControlPanNorthItemInactive {
               left: 50%;
               margin-left: -9px;
               top: 0;
          }
          .olControlPanPanel .olControlPanSouthItemInactive {
               left: 50%;
               margin-left: -9px;
               top: auto;
               bottom: 0;
          }
          .olControlPanPanel .olControlPanWestItemInactive {
               top: 50%;
               margin-top: -9px;
               left: 0;
          }
          .olControlPanPanel .olControlPanEastItemInactive {
               top: 50%;
               margin-top: -9px;
               left: auto;
               right: 0;
          }', '', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'ol_zoomPanel', '.olControlZoomPanel {
             left: auto;
             right: 23px;
             top: 80px;
       } 

          .olControlPanPanel .olControlPanNorthItemInactive {
               left: 50%;
               margin-left: -9px;
               top: 0;
          }
          .olControlPanPanel .olControlPanSouthItemInactive {
               left: 50%;
               margin-left: -9px;
               top: auto;
               bottom: 0;
          }
          .olControlPanPanel .olControlPanWestItemInactive {
               top: 50%;
               margin-top: -9px;
               left: 0;
          }
          .olControlPanPanel .olControlPanEastItemInactive {
               top: 50%;
               margin-top: -9px;
               left: auto;
               right: 0;
          }', '', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'openlayers_theme', '../extensions/OpenLayers-2.8/theme/default/style.css', 'workaround for css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'popupcss', '../css/popup.css', 'file css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'tablesortercss', '../css/tablesorter.css', 'file css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers', 'body', 'use_load_message', 'true', '', 'php_var');

INSERT into gui_mb_user (fkey_gui_id , fkey_mb_user_id , mb_user_type) VALUES ('template_openlayers',1,'owner');
