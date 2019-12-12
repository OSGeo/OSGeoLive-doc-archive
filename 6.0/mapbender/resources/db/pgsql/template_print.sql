Delete from gui_element where fkey_gui_id = 'template_print' and e_id = 'printPDF';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes,
 e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES(
'template_print','printPDF',2,1,'pdf print','Print','div','','',1,1,2,2,5,'','<div id="printPDF_working_bg"></div><div id="printPDF_working"><img src="../img/indicator_wheel.gif" style="padding:10px 0 0 10px">Generating PDF</div><div id="printPDF_input"><form id="printPDF_form" action="../print/printFactory.php"><div id="printPDF_selector"></div><div class="print_option"><input type="hidden" id="map_url" name="map_url" value=""/><input type="hidden" id="overview_url" name="overview_url" value=""/><input type="hidden" id="map_scale" name="map_scale" value=""/><input type="hidden" name="measured_x_values" /><input type="hidden" name="measured_y_values" /><br /></div><div class="print_option" id="printPDF_formsubmit"><input id="submit" type="submit" value="Print"><br /></div></form><div id="printPDF_result"></div></div>','div','../plugins/mb_print.js','../../lib/printbox.js,../extensions/jquery-ui-1.7.2.custom/development-bundle/external/bgiframe/jquery.bgiframe.min.js,../extensions/jquery.form.min.js','mapframe1','','http://www.mapbender.org/index.php/Print');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES(
'template_print', 'printPDF', 'mbPrintConfig', '{"A4 landscape": "A4_landscape_template.json","A4 portrait": "A4_portrait_template.json","A3 landscape": "A3_landscape_template.json"}', '' ,'var');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES(
'template_print', 'body', 'print_css', '../css/print_div.css', '' ,'file/css');

