INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('layout_with_openlayers','layout_with_openlayers','An OpenLayers map with WMS configured by Mapbender',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','body',1,1,'body (obligatory)','','body','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','body_css','body{ background-color: #fff;margin: 0 } ','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','favicon','../img/favicon.png','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','includeWhileLoading','../include/preliminary_logo_splash.php','','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','jquery_UI','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','jq_ui_theme','../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css','UI Theme from Themeroller','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','ol_mousePosition','.olControlMousePosition
{
	background-color:white; 
	width:220px;
	height:15px;
	border:solid gray 1px;
	border-bottom:none;
	left:0px;
	bottom:2px;
	padding-left:8px;
}	','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','ol_olControlMeasureItemInactive','.olControlMeasureItemInactive, .olControlMeasureItemActive
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
}	','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','ol_panPanel','.olControlPanPanel {
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
          }','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','ol_zoomPanel','.olControlZoomPanel {
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
          }','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','openlayers_theme','../extensions/OpenLayers-2.9.1/theme/default/style.css','workaround for css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','popupcss','../css/popup.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','tablesortercss','../css/tablesorter.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','body','use_load_message','true','','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','description',1,1,'','Description','div','','',700,30,300,400,1,'background-color:#ffffff;border:1px solid black;padding:10px','This application consists of several elements wrapping OpenLayers components. Currently there are the OpenLayers map, the layer switch, mouse position, keyboard defaults and the WMS input from the Mapbender database.
<br><br>
An administrator can easily customize the OpenLayers application by (de)activating individual elements or configuring WMS. ','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','jq_layout',1,1,'','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery.layout.all-1.2.0/jquery.layout.min.js','','','http://layout.jquery-dev.net');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol',1,1,'','OpenLayers','div','','',80,90,600,400,2,'border:1px solid black; margin:10px;background-color:#FFFFFF','','div','../plugins/ol.js','OpenLayers_core.js','','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','jq_ui',1,1,'jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','jq_ui','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','jq_ui_position',1,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','pane',1,1,'','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'height:100%;width:100%','','div','../plugins/mb_pane.js','','','jq_layout','http://layout.jquery-dev.net');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','pane','center','ol','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','pane','css','../extensions/jquery.layout.all-1.2.0/jquery.layout.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','pane','east','description','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','pane','layoutOptions','{
applyDefaultStyles: true,
north__resizable: false,
north__closable: false,
north__size:90,
north__spacing_open: 0,
west__resizable: false,
west__size: 50,
east__resizable: true,
east__minSize: 200,
east__maxSize: 500,
east__size: 300
}','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','pane','north','header','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','header',1,1,'','Header','div','','',NULL ,NULL,NULL ,NULL,NULL ,'font-family:Trebuchet MS,Helvetica,Arial,sans-serif; font-size:30px; text-shadow:2px 2px 3px gray;','<span style=''float:left''><img style=''width:60px;height:60px'' src=''../img/openlayers/120px-OpenLayers_logo.svg.png'' alt=''OpenLayers Logo''></span><span style=''vertical-align:middle;padding:10px;float:left''>OpenLayers</span><span style=''vertical-align:middle;padding:10px;float:right''>
<img src=''../img/Mapbender_logo_and_text_200x50.png'' alt=''Mapbender Logo''></span>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_panel',2,1,'The Panel control is a container for other controls.  With it toolbars may be composed.','Panel','div','','',30,30,NULL ,NULL,NULL ,'padding:5px','','div','../plugins/ol_panel.js','Control.js,Control/Panel.js','ol','ol','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','ol_panel','controls','ol_navigationHistory,ol_wmsGetFeatureInfo','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_popup',2,1,'Various OpenLayers popups','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../javascripts/ol_popup.php','lib/OpenLayers/Popup.js','','','http://www.openlayers.org/');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','ol_popup','anchored','1','','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','ol_popup','anchoredBubble','1','','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','ol_popup','framed','1','','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_openlayers','ol_popup','framedCloud','1','','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_setExtent',2,1,'set extent for map','','div','','',0,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_setExtent.php','','ol','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_wms',2,1,'Load configured WMS from Mapbender application settings into OpenLayers','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_wms.php','Tile.js,Layer/Grid.js,Tile/Image.js,Layer/WMS.js','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_mousePosition',2,1,'An OpenLayers MousePosition','OpenLayers MousePosition','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_mousePosition.js','Control.js,Control/MousePosition.js','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_navigation',2,1,'An OpenLayers Navigation Control','OpenLayers Navigation','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_navigation.js','Handler.js,Control/ZoomBox.js,Control/DragPan.js,Handler/MouseWheel.js,Handler/Click.js,Control/Navigation.js','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_panZoomBar',2,1,'An OpenLayers PanZoomBar','OpenLayers Layer Switch','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_panZoomBar.js','Control.js,Handler.js,Handler/Drag.js,Handler/Box.js,Control/PanZoom.js,Control/PanZoomBar.js','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_keyboardDefaults',2,1,'An OpenLayers KeyboardDefaults.
Navigate with Keybords up, down, left and right key.','OpenLayers KeyboardDefaults','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_keyboardDefaults.js','Handler.js,Control.js,Handler/Keyboard.js,Control/KeyboardDefaults.js','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','ol_layerSwitch',2,1,'An OpenLayers LayerSwitcher','OpenLayers LayerSwitcher','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_layerSwitch.js','Control.js,Control/LayerSwitcher.js','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.resizable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/resizable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_openlayers','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');

INSERT into gui_mb_user (fkey_gui_id , fkey_mb_user_id , mb_user_type) VALUES ('layout_with_openlayers',1,'owner');


