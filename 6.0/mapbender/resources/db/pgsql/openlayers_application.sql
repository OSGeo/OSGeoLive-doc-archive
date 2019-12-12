INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('ol','ol','GUI with tab, search modules',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','body',1,1,'body (obligatory)','','body','','onload="init()"',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','help',2,1,'button help','Help','img','../img/button_gray/help_off.png','onmouseover = "mb_regButton(''init_help'')" ',764,22,24,24,1,'','','','mod_help.php','../extensions/wz_jsgraphics.js','','jsGraphics','http://www.mapbender.org');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','logo',2,1,'Logo','Logo','img','../img/mapbender_logo.png','',20,20,180,26,5,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','logout',2,1,'Logout','Logout','img','../img/button_gray/logout_off.png','onClick="window.location.href=''../php/mod_logout.php?sessionID''" border=''0'' onmouseover=''this.src="../img/button_gray/logout_over.png"'' onmouseout=''this.src="../img/button_gray/logout_off.png"'' ',792,22,24,24,1,'','','','','','','','http://www.mapbender.org/index.php/Logout');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','mapframe1',2,1,'frame for a map','Mapframe','iframe','../php/mod_map1.php?sessionID','scrolling="no" frameborder=''0'' ',422,67,391,349,2,'','','iframe','','map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php','','','http://www.mapbender.org/index.php/Mapframe');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','navFrame',2,1,'navigation mapborder','Navigation Frame','div','','',0,0,NULL ,NULL ,NULL ,'font-size:1px;','<div id=''mbN'' style=''position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;'' onclick=''mod_navFrame("N")''><img id=''arrow_n'' style=''position:relative;top:0;left:0'' src=''../img/arrows/arrow_n.gif'' width=''15'' height=''10''></div> <div id=''mbNE'' style=''position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;'' onclick=''mod_navFrame("NE")''><img id=''arrow_ne'' style=''position:relative;top:0;left:0'' src=''../img/arrows/arrow_ne.gif'' width=''10'' height=''10''></div> <div id=''mbE'' style=''position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;'' onclick=''mod_navFrame("E")''><img id=''arrow_e'' style=''position:relative;top:0;left:0'' src=''../img/arrows/arrow_e.gif'' width=''10'' height=''15''></div> <div id=''mbSE'' style=''position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;'' onclick=''mod_navFrame("SE")''><img id=''arrow_se'' style=''position:relative;top:0;left:0'' src=''../img/arrows/arrow_se.gif'' width=''10'' height=''10''></div> <div id=''mbS'' style=''position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;'' onclick=''mod_navFrame("S")''><img id=''arrow_s'' style=''position:relative;top:0;left:0'' src=''../img/arrows/arrow_s.gif'' width=''15'' height=''10''></div> <div id=''mbSW'' style=''position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;'' onclick=''mod_navFrame("SW")''><img id=''arrow_sw'' style=''position:relative;top:0;left:0'' src=''../img/arrows/arrow_sw.gif'' width=''10'' height=''10''></div> <div id=''mbW'' style=''position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;'' onclick=''mod_navFrame("W")''><img id=''arrow_w'' style=''position:relative;top:0;left:0'' src=''../img/arrows/arrow_w.gif'' width=''10'' height=''15''></div> <div id=''mbNW'' style=''position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;'' onclick=''mod_navFrame("NW")''><img id=''arrow_nw'' style=''position:relative;top:0;left:0'' src=''../img/arrows/arrow_nw.gif'' width=''10'' height=''10''></div> ','div','mod_navFrame.php','','mapframe1','','http://www.mapbender.org/index.php/NavFrame');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','openlayers',2,1,'div for an ol map','OpenLayers','div','','',14,55,391,369,2,'border-width:thin; border-color:#000000;  border-style:solid;','','div','mod_openlayers.php, initOpenLayersWms.php','../extensions/OpenLayers.js','','','http://www.openlayers.org');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','openlayers_layerSwitch',3,1,'layer switch for an ol map','OpenLayers layer switch','div','','',NULL ,NULL ,NULL ,NULL ,2,'','','div','mod_openlayers_layerSwitch.js','','','openlayers','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','pan1',2,1,'pan','Pan','img','../img/button_gray/pan_off.png','onmouseover = "mb_regButton(''init_mod_pan'')" ',513,22,24,24,1,'','','','mod_pan.php','','mapframe1','','http://www.mapbender.org/index.php/Pan');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','setBackground',2,1,'switch background-wms','Set Background','form','','',550,22,NULL ,NULL ,1,'','<select style=''font-family: Arial, sans-serif; font-size:12'' name=''mod_setBackground_list'' onchange=''mod_setBackground_change(this)'' ><option value=''0''></option></select>','form','mod_setBackground.php','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','switchLocale_noreload',8,0,'changes the locale without reloading the client','Set language','div','','frameborder=''0''',204,22,50,30,5,'','<form id=''form_switch_locale'' name=''form_switch_locale'' target=''parent''>
<select id=''language'' name=''language'' onchange=''validate_locale()''>
</select>
</form>
','div','mod_switchLocale_noreload.php','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','zoomFull',2,1,'zoom to full extent button','Display complete map','img','../img/button_gray/zoomFull_off.png','onclick="mod_zoomFull()" onmouseover="mod_zoomFull_init(this)" ',455,22,24,24,2,'','','img','mod_zoomFull.php','','mapframe1','','http://www.mapbender.org/index.php/ZoomFull');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','zoomIn1',2,1,'zoomIn button','Zoom in','img','../img/button_gray/zoomIn2_off.png','onclick=''mod_zoomIn1()'' onmouseover=''mod_zoomIn1_init(this)'' ',426,22,24,24,1,'','','','mod_zoomIn1.php','','mapframe1','','http://www.mapbender.org/index.php/ZoomIn');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('ol','zoomOut1',2,1,'zoomOut button','Zoom out','img','../img/button_gray/zoomOut2_off.png','onclick=''mod_zoomOut1()'' onmouseover=''mod_zoomOut1_init(this)''',484,22,24,24,1,'','','','mod_zoomOut1.php','','mapframe1','','http://www.mapbender.org/index.php/ZoomOut');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('ol', 'body', 'css_class_bg', 'body{ background-color: #ffffff; }', 'to define the color of the body', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('ol', 'body', 'css_file_body', '../css/mapbender.css', 'file/css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('ol', 'help', 'mod_help_color', '#cc33cc', 'color for highlighting', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('ol', 'help', 'mod_help_text', 'click highlighted elements for help', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('ol', 'help', 'mod_help_thickness', '3', 'thickness of highlighting', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('ol', 'logout', 'logout_location', '', 'webside to show after logout', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('ol', 'switchLocale_noreload', 'languages', 'de,en,bg,gr,nl,it,es', '', 'var');