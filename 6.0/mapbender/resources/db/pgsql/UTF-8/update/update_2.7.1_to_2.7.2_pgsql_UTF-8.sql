UPDATE gui_element_vars set 
var_value =
'{"A4 landscape": "A4_landscape_template.json","A4 portrait": "A4_portrait_template.json","A4 landscape": "A4_landscape_template.json",
"A4 landscape official": "A4_landscape_official_template.json",
"A4 portrait official": "A4_portrait_official_template.json",
"A4 portrait (map only)": "A4_portrait_template_pure_map.json",
"A3 landscape": "A3_landscape_template.json","A3 portrait": "A3_portrait_template.json",
"A3 landscape official": "A3_landscape_official_template.json",
"A3 portrait official": "A3_portrait_official_template.json",
"A3 portrait (map only)": "A3_portrait_template_pure_map.json",
"A2 landscape": "A2_landscape_template.json",
"A1 landscape": "A1_landscape_template.json",
"A0 landscape": "A0_landscape_template.json"
}'
where fkey_gui_id = 'template_print' and fkey_e_id = 'printPDF' and var_name = 'mbPrintConfig';

UPDATE gui_element set e_width = 28, e_height = 28
where fkey_gui_id = 'template_print' and e_id ='printButton' ;

UPDATE gui_element  SET e_more_styles = '' WHERE e_id = 'showCoords_div'  AND e_more_styles LIKE 'background-color:white%';

-- replace too many ' in definition
UPDATE gui_element set e_attributes = replace(e_attributes,'''"','"') where e_id IN ('updateWMSs','owsproxy','editGUI_WMS');

--add possibility to edit cyclic update in inspire metadata addon editor (admin_wms_metadata):
-- Column: update_frequency

-- ALTER TABLE mb_metadata DROP COLUMN update_frequency;

ALTER TABLE mb_metadata ADD COLUMN update_frequency character varying(100);

--
-- new option for metadata publishing in Administration_DE GUI
--
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','allowPublishMetadata_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,115,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','allowPublishMetadata,allowPublishMetadata_icon','','');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','allowPublishMetadata',2,1,'Einrichtungen erlauben im Auftrag <br>Metadaten zu veröffentlichen','Einrichtungen erlauben im Auftrag <br>Metadaten zu veröffentlichen','a','','href = "../geoportal/mod_allow_publishing_metadata.php?sessionID&e_id_css=allowPublishMetadata" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Einrichtungen erlauben im Auftrag <br>Metadaten zu veröffentlichen','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','allowPublishMetadata','adminGroupId','25','','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','allowPublishMetadata','authorizeRoleId','3','','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','allowPublishMetadata','file css','../css/allow_publishing_metadata.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','allowPublishMetadata_icon',2,1,'icon','','img','../img/gnome/emblem-shared.png','',0,0,NULL ,NULL,2,'','','','','','','','');

UPDATE gui_element SET e_target = 'filteredGui_user_collection,filteredGui_group_collection,filteredGui_filteredGroup_collection,gui_owner_collection,allowPublishMetadata_collection' WHERE fkey_gui_id = 'Administration_DE' AND e_id = 'menu_auth'; 

-- set width and height of body element to NULL for Gui admin2_de, otherwise this gui is not displayed correctly in IE8
UPDATE gui_element set e_width = NULL, e_height = NULL
where fkey_gui_id = 'admin2_de' and e_id ='body' ;

-- delete old entries from old treeConfiguration
DELETE FROM gui_treegde WHERE my_layer_title ILIKE 'new' AND wms_id IS NULL;

UPDATE gui_element set e_attributes = 'href = "../php/mod_category_filteredGUI.php?sessionID&e_id_css=category_filteredGUI"' where fkey_gui_id = 'Administration_DE' and e_id ='category_filteredGUI' ;

UPDATE gui_element set e_attributes = 'href = "../php/mod_category_filteredGUI.php?sessionID&e_id_css=category_filteredGUI"' where fkey_gui_id = 'Administration' and e_id ='category_filteredGUI' ;

--
-- refers to #786: after update the applications with savewmc and load wmc do not work as jq_ui_dialog and button are missing
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, 
e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, 
e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
Select fkey_gui_id, foo.* from (
SELECT 'jq_ui_button',e_pos,e_public,e_comment,e_title,e_element, e_src, e_attributes, 
e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, 
e_js_file, e_mb_mod, e_target, e_requires, e_url FROM gui_element 
WHERE e_id = 'jq_ui_button' AND fkey_gui_id = 'gui1' 
) as foo , gui_element where e_id = 'loadwmc' AND fkey_gui_id IN 
 (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'loadwmc' AND fkey_gui_id NOT IN (
	SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui_button'
 ));


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, 
e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, 
e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
Select fkey_gui_id, foo.* from (
SELECT 'jq_ui_dialog',e_pos,e_public,e_comment,e_title,e_element, e_src, e_attributes, 
e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, 
e_js_file, e_mb_mod, e_target, e_requires, e_url FROM gui_element 
WHERE e_id = 'jq_ui_dialog' AND fkey_gui_id = 'gui1' 
) as foo , gui_element where e_id = 'loadwmc' AND fkey_gui_id IN 
 (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'loadwmc' AND fkey_gui_id NOT IN (
	SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui_dialog'
 ));

-- Bugfix for ie9 startup problem. problem with dialogs remain - they cannot be dragged
UPDATE gui_element SET e_mb_mod = '../../lib/printbox.js,../extensions/jquery-ui-1.7.2.custom/development-bundle/external/bgiframe/jquery.bgiframe.js,../extensions/jquery.form.min.js,../extensions/wz_jsgraphics.js' 
WHERE e_id = 'printPDF' AND e_js_file = '../plugins/mb_print.php';

-- new element var unlink for gui element template printPDF
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'printPDF', 'unlink', 'true', 'delete print pngs after pdf creation' ,'php_var'
FROM gui_element WHERE gui_element.e_id = 'printPDF' AND gui_element.e_js_file = '../plugins/mb_print.php' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'unlink' AND fkey_e_id = 'printPDF');

-- new element var logRequests for gui element template printPDF
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'printPDF', 'logRequests', 'false', 'log wms requests for debugging' ,'php_var'
FROM gui_element WHERE gui_element.e_id = 'printPDF' AND gui_element.e_js_file = '../plugins/mb_print.php' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'logRequests' AND fkey_e_id = 'printPDF');

-- new element var logType for gui element template printPDF
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'printPDF', 'logType', 'file', 'log mode can be set to file or db' ,'php_var'
FROM gui_element WHERE gui_element.e_id = 'printPDF' AND gui_element.e_js_file = '../plugins/mb_print.php' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'logType' AND fkey_e_id = 'printPDF');

-- new element var timeout for gui element template printPDF
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'printPDF', 'timeout', '90000', 'define maximum milliseconds to wait for print request finished' ,'var'
FROM gui_element WHERE gui_element.e_id = 'printPDF' AND gui_element.e_js_file = '../plugins/mb_print.php' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'timeout' AND fkey_e_id = 'printPDF');

--
-- mb_metadata
-- new column responsible_party_name
--
ALTER TABLE mb_metadata ADD COLUMN responsible_party_name character varying(100);

UPDATE gui_element SET 
e_attributes = '' ,
e_url = 'http://www.mapbender.org/ScaleSelect',
e_js_file = '../plugins/mb_selectScale.js' where e_id = 'scaleSelect';

-- 
-- add legend div to template_print
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('template_print','legend',2,1,'legend','Legend','div','','',-59,100,180,600,3,'','','div','mod_legendDiv.php','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('template_print', 'legend', 'checkbox_on_off', 'false', 'display or hide the checkbox to set the legend on/off' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('template_print', 'legend', 'css_file_legend', '../css/legend.css', '' ,'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('template_print', 'legend', 'legendlink', 'false', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('template_print', 'legend', 'showgroupedlayertitle', 'true', 'show the title of the grouped layers in the legend' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('template_print', 'legend', 'showlayertitle', 'true', 'show the layer title in the legend' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('template_print', 'legend', 'showwmstitle', 'true', 'show the wms title in the legend' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('template_print', 'legend', 'stickylegend', 'false', 'parameter to decide wether the legend should stick on the mapframe1' ,'var');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('template_print','legendButton',1,1,'','Druck','img','../img/button_blue_red/select_choose_off.png','',440,10,28,28,NULL ,'','','','../plugins/mb_button.js','','legend','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('template_print', 'legendButton', 'position', '[720,100]', '' ,'var');



-- add CustomizeTree to Administration and Administration_de
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','CustomizeTree_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-personal.png','',0,0,NULL ,NULL,2,'','','','','','','','');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','CustomizeTree_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',250,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','CustomizeTree,CustomizeTree_icon','','');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','CustomizeTree',2,1,'Customize Tree','Baumstruktur konfigurieren','a','','href = "../php/mod_customTree.php?elementID=Customize Tree&sessionID"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Customize Tree','a','','','','','http://www.mapbender.org/CustomTree');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','CustomizeTree_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-personal.png','',0,0,NULL ,NULL,2,'','','','','','','','');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','CustomizeTree_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',250,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','CustomizeTree,CustomizeTree_icon','','');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','CustomizeTree',2,1,'Customize Tree','Baumstruktur konfigurieren','a','','href = "../php/mod_customTree.php?elementID=Customize Tree&sessionID"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Customize Tree','a','','','','','http://www.mapbender.org/CustomTree');

-- update
UPDATE gui_element set 
e_target= 'newGui_collection,rename_copy_gui_collection,delete_filteredGui_collection,editElements_collection,category_filteredGui_collection,deleteCategory_collection,CustomizeTree_collection'
where fkey_gui_id IN ('Administration','Administration_DE') AND e_id = 'menu_gui';

-- provide more space for the collections
Update gui_element set e_left = 250 where e_left = 150 and fkey_gui_id = 'Administration_DE' and e_element = 'div';
Update gui_element set e_left = 250 where e_left = 150 and fkey_gui_id = 'Administration' and e_element = 'div';

--
-- rename Customize Tree to CustomizeTree
--
UPDATE gui_element set 
e_id = 'CustomizeTree' where e_id = 'Customize Tree';


--
-- Add new application PortalAdmin_DE
--
INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('PortalAdmin_DE','PortalAdmin_DE','Administrationsoberfläche',1);

INSERT INTO gui_mb_user (fkey_gui_id,fkey_mb_user_id,mb_user_type) values ('PortalAdmin_DE',1,'owner');
INSERT INTO gui_gui_category (fkey_gui_id,fkey_gui_category_id) values ('PortalAdmin_DE',1);

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','body',1,1,'Navigation','','body','','',0,0,200,40,NULL ,'','','','../geoportal/mod_revertGuiSessionSettings.php','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','body','favicon','../img/favicon.png','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','body','includeWhileLoading','','show splash screen while the application is loading','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','body','jq_ui_effects_transfer','.ui-effects-transfer { z-index:1003; border: 2px dotted gray; } ','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','body','jq_ui_theme','../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','body','use_load_message','false','show splash screen while the application is loading','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.widget.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','jq_ui',1,1,'jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.core.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','jq_ui','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','groupUser_icon',2,1,'icon','','img','../img/gnome/groupUser.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','menu_category',2,1,'GUI admin menu','Kategorien','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','createGuiCategory_collection,deleteCategory_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','groupUser_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','Group_User,groupUser_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','showLoggedUser',2,1,'Anzeige des eingeloggten Benutzers','','iframe','../php/mod_showLoggedUser.php?sessionID','frameborder="0" scrolling=''no''',1,1,200,30,1,'background-color:lightgrey;','','iframe','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','showLoggedUser','css_file_user_logged','../css/administration_alloc.css','file/css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','menu_group',2,1,'GUI admin menu','Gruppen','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','editGroup_collection,groupUser_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','editGroup_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editGroup,editGroup_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','editGroup_icon',2,1,'icon','','img','../img/gnome/editGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','menu_role',2,1,'GUI admin menu','Rollen','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','groupUserRole_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','editGroup',2,1,'Gruppe anlegen und editieren','Create and edit group','a','','href = "../php/mod_editGroup.php?sessionID"',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Gruppe anlegen und editieren','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','Group_User_Role',2,1,'Rollenzuweisung','Rollenzuweisung','a','','href = "../php/mod_group_user_role.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Rollenzuweisung','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','Group_User_Role','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','groupUserRole_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','Group_User_Role,groupUserRole_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','createCategory',2,1,'Anwendungskategorie erstellen','Anwendungskategorie erstellen','a','','href = "../php/mod_createCategory.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Anwendungskategorie erstellen','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','createGuiCategory_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','createCategory,createGuiCategory_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','fullScreenIcon',2,1,'show iframe in new window','Open in new Window','img','../img/button_blue_red/resizemapsize_off.png','',120,320,24,24,NULL ,'','','','','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','fullScreenIcon','adjust_height','-35','to adjust the height of the mapframe on the bottom of the window','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','fullScreenIcon','adjust_width','30','to adjust the width of the mapframe on the right side of the window','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','fullScreenIcon','resize_option','button','auto (autoresize on load), button (resize by button)','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','fullScreen',2,1,'Link to open Admin in new window','Öffne in neuem Fenster','span','','onclick="window.open(window.location.href,''Administrations Fenster'',''scrollbars=yes,resizeable=yes'')"
',10,320,100,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Zu klein? - Öffne in neuem Fenster','span','','','','','http://www.mapbender.org/index.php/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','deleteCategory_icon',2,1,'icon','','img','../img/gnome/deleteCategories.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','groupUserRole_icon',2,1,'icon','','img','../img/gnome/myUserMyGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','myGUIlist',2,1,'Zurück zur Anwendungsübersicht','Zurück zur Anwendungsübersicht','img','../img/button_blue_red/home_off.png','onClick="mod_home_init()" border=''0'' onmouseover=''this.src="../img/button_blue_red/home_over.png"'' onmouseout=''this.src="../img/button_blue_red/home_off.png"''',200,2,24,24,2,'','','','mod_home.php','','','','http://www.mapbender.org/index.php/MyGUIlist');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','deleteCategory_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteCategory,deleteCategory_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.mouse.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','jq_ui_position',2,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.position.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','createGuiCategory_icon',2,1,'icon','','img','../img/gnome/createCategories.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','deleteCategory',2,1,'Anwendungskategorie löschen','Anwendungskategorie löschen','a','','href = "../php/mod_deleteCategory.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Anwendungskategorie löschen','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','Group_User',2,1,'Gruppen Nutzer Zuordnung','Gruppen <-> Nutzer','a','','href = "../php/mod_group_user.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Gruppen Nutzer Zuordnung','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','Group_User','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.button.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.dialog.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.draggable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.resizable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','mb_iframepopup',7,1,'iframepopup','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_iframepopup.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin_DE','mb_horizontal_accordion',10,1,'Put existing divs in new horizontal accordion div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,NULL ,NULL,NULL ,'','<dl></dl>','div','../plugins/mb_horizontal_accordion.js','../../extensions/jqueryEasyAccordion/jquery.easyAccordion.js','menu_group,menu_role,menu_category','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin_DE','mb_horizontal_accordion','Accordion css file','../extensions/jqueryEasyAccordion/mb_jquery.easyAccordion.css','','file/css');


--
-- Add new application PortalAdmin_DE
--
INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('PortalAdmin','PortalAdmin','Administration',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.widget.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','jq_ui',1,1,'jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.core.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','jq_ui','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','body',1,1,'Navigation','','body','','',0,0,200,40,NULL ,'','','','../geoportal/mod_revertGuiSessionSettings.php','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','body','favicon','../img/favicon.png','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','body','includeWhileLoading','','show splash screen while the application is loading','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','body','jq_ui_effects_transfer','.ui-effects-transfer { z-index:1003; border: 2px dotted gray; } ','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','body','jq_ui_theme','../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','body','use_load_message','false','show splash screen while the application is loading','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','editGroup_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editGroup,editGroup_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','editGroup_icon',2,1,'icon','','img','../img/gnome/editGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','groupUserRole_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','Group_User_Role,groupUserRole_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','createGuiCategory_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','createCategory,createGuiCategory_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','fullScreenIcon',2,1,'show iframe in new window','Open in new Window','img','../img/button_blue_red/resizemapsize_off.png','',120,320,24,24,NULL ,'','','','','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','fullScreenIcon','adjust_height','-35','to adjust the height of the mapframe on the bottom of the window','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','fullScreenIcon','adjust_width','30','to adjust the width of the mapframe on the right side of the window','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','fullScreenIcon','resize_option','button','auto (autoresize on load), button (resize by button)','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','deleteCategory_icon',2,1,'icon','','img','../img/gnome/deleteCategories.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','myGUIlist',2,1,'Zurück zur Anwendungsübersicht','Zurück zur Anwendungsübersicht','img','../img/button_blue_red/home_off.png','onClick="mod_home_init()" border=''0'' onmouseover=''this.src="../img/button_blue_red/home_over.png"'' onmouseout=''this.src="../img/button_blue_red/home_off.png"''',200,2,24,24,2,'','','','mod_home.php','','','','http://www.mapbender.org/index.php/MyGUIlist');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','deleteCategory_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteCategory,deleteCategory_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.mouse.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','jq_ui_position',2,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.position.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','createGuiCategory_icon',2,1,'icon','','img','../img/gnome/createCategories.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','fullScreen',2,1,'Link to open Admin in new window','Full Screen View','span','','onclick="window.open(window.location.href,''FullAdminWindow'',''scrollbars=yes,resizeable=yes'')"
',10,320,100,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Too small? - open in new window','span','','','','','http://www.mapbender.org/index.php/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','groupUser_icon',2,1,'icon','','img','../img/gnome/groupUser.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','groupUser_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','Group_User,groupUser_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','showLoggedUser',2,1,'Anzeige des eingeloggten Benutzers','','iframe','../php/mod_showLoggedUser.php?sessionID','frameborder="0" scrolling=''no''',1,1,200,30,1,'background-color:lightgrey;','','iframe','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','showLoggedUser','css_file_user_logged','../css/administration_alloc.css','file/css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','menu_category',2,1,'GUI admin menu','CATEGORY','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','createGuiCategory_collection,deleteCategory_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','editGroup',2,1,'Gruppe anlegen und editieren','Create and edit group','a','','href = "../php/mod_editGroup.php?sessionID"',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Create and edit group','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','createCategory',2,1,'Anwendungskategorie erstellen','Create application category','a','','href = "../php/mod_createCategory.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Create application category','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','deleteCategory',2,1,'Delete application category','Delete application category','a','','href = "../php/mod_deleteCategory.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Delete application category','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','Group_User_Role',2,1,'Assign role','Assign role','a','','href = "../php/mod_group_user_role.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Assign role','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','Group_User_Role','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','Group_User',2,1,'Gruppen Nutzer Zuordnung','Add user to group','a','','href = "../php/mod_group_user.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Add user to group','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','Group_User','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','groupUserRole_icon',2,1,'icon','','img','../img/gnome/myUserMyGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','menu_group',2,1,'GUI admin menu','GROUP','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','editGroup_collection,groupUser_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','menu_role',2,1,'GUI admin menu','ROLE','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','groupUserRole_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.button.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.dialog.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.draggable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.resizable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','mb_iframepopup',7,1,'iframepopup','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_iframepopup.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('PortalAdmin','mb_horizontal_accordion',10,1,'Put existing divs in new horizontal accordion div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,NULL ,NULL,NULL ,'','<dl></dl>','div','../plugins/mb_horizontal_accordion.js','../../extensions/jqueryEasyAccordion/jquery.easyAccordion.js','menu_group,menu_role,menu_category','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('PortalAdmin','mb_horizontal_accordion','Accordion css file','../extensions/jqueryEasyAccordion/mb_jquery.easyAccordion.css','','file/css');


--
-- update for franch translation to handle '
--
-- 360
UPDATE translations set msgstr = 'Sauvegarder la vue/l`espace de travail
en tant que Web Map Context'
where locale = 'fr' and msgid = 'Save workspace as web map context document';

--379
UPDATE translations set msgstr = 'Carte d`aperçu'
where locale = 'fr' and msgid = 'Overview';

-- 383
UPDATE translations set msgstr = 'Sélection de l`échelle'
where locale = 'fr' and msgid = 'Scale Select';


-- 384
UPDATE translations set msgstr = 'Texte de l`échelle'
where locale = 'fr' and msgid = 'Scale Text';


--386
UPDATE translations set msgstr = 'Sélectionner la carte d`arrière-plan'
where locale = 'fr' and msgid = 'Set Background';

-- add exportGUI to Administration and Administration_de
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, 
e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, 
e_requires, e_url) VALUES('Administration_DE','exportGUI_collection',2,1,'Put 
existing divs in new div object. List the elements comma-separated under 
target, and make sure they have a title.','','div','','',250,40,200,30,NULL 
,'','','div','../plugins/mb_div_collection.js','','exportGUI,exportGUI_icon','','');


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, 
e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, 
e_requires, e_url) 
VALUES('Administration_DE','exportGUI_icon',2,1,'icon','','img','../img/gnome/exportGui2Sql.png','',0,0,NULL 
,NULL ,2,'','','','','','','','');


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, 
e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, 
e_requires, e_url) VALUES('Administration_DE','exportGUI',2,1,'Anwendung exportieren (SQL)','Anwendung exportieren (SQL)','a','','href = 
"../php/mod_exportGUI.php?sessionID"',80,15,190,20,10,'font-family: Arial, 
Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: 
#808080;','Anwendung exportieren (SQL)','a','','','','','http://www.mapbender.org/exportGUI');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
context, var_type) VALUES('Administration_DE', 'exportGUI', 'cssfile', 
'../css/administration_alloc.css', 'css file for admin module' ,'file/css');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, 
e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, 
e_requires, e_url) VALUES('Administration','exportGUI_collection',2,1,'Put 
existing divs in new div object. List the elements comma-separated under 
target, and make sure they have a title.','','div','','',250,40,200,30,NULL 
,'','','div','../plugins/mb_div_collection.js','','exportGUI,exportGUI_icon','','');


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, 
e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, 
e_requires, e_url) 
VALUES('Administration','exportGUI_icon',2,1,'icon','','img','../img/gnome/exportGui2Sql.png','',0,0,NULL 
,NULL ,2,'','','','','','','','');


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, 
e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, 
e_requires, e_url) VALUES('Administration','exportGUI',2,1,'Export application (SQL)','Export application (SQL)','a','','href = 
"../php/mod_exportGUI.php?sessionID"',80,15,190,20,10,'font-family: Arial, 
Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: 
#808080;','Export application (SQL)','a','','','','','http://www.mapbender.org/exportGUI');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
context, var_type) VALUES('Administration', 'exportGUI', 'cssfile', 
'../css/administration_alloc.css', 'css file for admin module' ,'file/css');

-- update
UPDATE gui_element set 
e_target= 'newGui_collection,rename_copy_gui_collection,delete_filteredGui_collection,editElements_collection,category_filteredGui_collection,deleteCategory_collection,CustomizeTree_collection,exportGUI_collection'
where fkey_gui_id IN ('Administration','Administration_DE') AND e_id = 'menu_gui';

