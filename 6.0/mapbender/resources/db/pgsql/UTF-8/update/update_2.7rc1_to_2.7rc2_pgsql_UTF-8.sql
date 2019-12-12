--
--bugfix for template_openlayers #759
--
UPDATE gui_element set e_pos='2' WHERE e_id='jq_ui_position';

--
--new function to get the load_count of wmc which are stored in the mapbender database
--
-- Function: f_wmc_load_count(integer)

CREATE OR REPLACE FUNCTION f_wmc_load_count(integer)
  RETURNS integer AS
$BODY$
DECLARE
   wmc_rel int8;
BEGIN
wmc_rel := load_count from wmc_load_count where wmc_load_count.fkey_wmc_serial_id=$1; 
IF wmc_rel IS NULL THEN
	RETURN 0;
ELSE
	RETURN wmc_rel;
END IF;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;


--
-- change view for search wmc to include the load_count
--
-- View: search_wmc_view

DROP VIEW IF EXISTS search_wmc_view;

CREATE OR REPLACE VIEW search_wmc_view AS 
 SELECT wmc_dep.fkey_user_id AS user_id, wmc_dep.wmc_id, wmc_dep.srs AS wmc_srs, wmc_dep.wmc_title, wmc_dep.abstract AS wmc_abstract, f_collect_searchtext_wmc(wmc_dep.wmc_id) AS searchtext, wmc_dep.wmc_timestamp, wmc_dep.department, wmc_dep.mb_group_name, wmc_dep.mb_group_title, wmc_dep.mb_group_country, wmc_dep.wmc_serial_id, f_wmc_load_count(wmc_dep.wmc_serial_id) as load_count, wmc_dep.mb_group_stateorprovince, f_collect_inspire_cat_wmc(wmc_dep.wmc_serial_id) AS md_inspire_cats, f_collect_custom_cat_wmc(wmc_dep.wmc_serial_id) AS md_custom_cats, f_collect_topic_cat_wmc(wmc_dep.wmc_id) AS md_topic_cats, st_transform(st_geometryfromtext(((((((((((((((((((('POLYGON(('::text || wmc_dep.minx::text) || ' '::text) || wmc_dep.miny::text) || ','::text) || wmc_dep.minx::text) || ' '::text) || wmc_dep.maxy::text) || ','::text) || wmc_dep.maxx::text) || ' '::text) || wmc_dep.maxy::text) || ','::text) || wmc_dep.maxx::text) || ' '::text) || wmc_dep.miny::text) || ','::text) || wmc_dep.minx::text) || ' '::text) || wmc_dep.miny::text) || '))'::text, regexp_replace(upper(wmc_dep.srs::text), 'EPSG:'::text, ''::text)::integer), 4326) AS the_geom, (((((wmc_dep.minx::text || ','::text) || wmc_dep.miny::text) || ','::text) || wmc_dep.maxx::text) || ','::text) || wmc_dep.maxy::text AS bbox, wmc_dep.mb_group_logo_path
   FROM ( SELECT mb_user_wmc.wmc_public, mb_user_wmc.maxy, mb_user_wmc.maxx, mb_user_wmc.miny, mb_user_wmc.minx, mb_user_wmc.srs, mb_user_wmc.wmc_serial_id AS wmc_id, mb_user_wmc.wmc_serial_id, mb_user_wmc.wmc_title, mb_user_wmc.abstract, mb_user_wmc.wmc_timestamp, mb_user_wmc.fkey_user_id, user_dep.mb_group_id AS department, user_dep.mb_group_name, user_dep.mb_group_title, user_dep.mb_group_country, user_dep.mb_group_stateorprovince, user_dep.mb_group_logo_path
           FROM ( SELECT registrating_groups.fkey_mb_user_id AS mb_user_id, mb_group.mb_group_id, mb_group.mb_group_name, mb_group.mb_group_title, mb_group.mb_group_country, mb_group.mb_group_stateorprovince, mb_group.mb_group_logo_path
                   FROM registrating_groups, mb_group
                  WHERE registrating_groups.fkey_mb_group_id = mb_group.mb_group_id) user_dep, mb_user_wmc
          WHERE user_dep.mb_user_id = mb_user_wmc.fkey_user_id) wmc_dep
  WHERE wmc_dep.wmc_public = 1
  ORDER BY wmc_dep.wmc_id;

--
-- Bugfix for normalize searchtext of wmc docs
--
-- Function: f_collect_searchtext_wmc(integer)

-- DROP FUNCTION f_collect_searchtext_wmc(integer);

CREATE OR REPLACE FUNCTION f_collect_searchtext_wmc(integer)
  RETURNS text AS
$BODY$
DECLARE
    p_wmc_id ALIAS FOR $1;
    
    r_keywords RECORD;
    l_result TEXT;
BEGIN
    l_result := '';
    l_result := l_result || (SELECT COALESCE(wmc_title, '') || ' ' || COALESCE(abstract, '') FROM mb_user_wmc WHERE wmc_serial_id = p_wmc_id);
    FOR r_keywords IN SELECT DISTINCT keyword FROM
        (SELECT keyword FROM wmc_keyword L JOIN keyword K ON (K.keyword_id = L.fkey_keyword_id )
        ) AS __keywords__ LOOP
        l_result := l_result || ' ' || COALESCE(r_keywords.keyword, '');
    END LOOP;
   l_result := UPPER(l_result);
   l_result := replace(replace(replace(replace(replace(replace(replace(l_result,'Ä','AE'),'ß','SS'),'Ö','OE'),'Ü','UE'),'ä','AE'),'ü','UE'),'ö','OE');

    RETURN l_result;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

--Bugfix in integral monitoring table

-- Function: mb_monitor_after()

--DROP FUNCTION mb_monitor_after();

CREATE OR REPLACE FUNCTION mb_monitor_after()
  RETURNS "trigger" AS
$BODY$DECLARE
   availability_new REAL;
   average_res_cap REAL;
   count_monitors REAL;
    BEGIN
     IF TG_OP = 'UPDATE' THEN
     
     count_monitors := count(fkey_wms_id) from mb_monitor where fkey_wms_id=NEW.fkey_wms_id;
      --the following should be adopted if the duration of storing is changed!!!
      average_res_cap := ((select average_resp_time from mb_wms_availability where fkey_wms_id=NEW.fkey_wms_id)*count_monitors+(NEW.timestamp_end-NEW.timestamp_begin))/(count_monitors+1);

     IF NEW.status > -1 THEN --service gives caps
      availability_new := round(cast(((select availability from mb_wms_availability where fkey_wms_id=NEW.fkey_wms_id)*count_monitors + 100)/(count_monitors+1) as numeric),2);
     ELSE --service has problems with caps
      availability_new := round(cast(((select availability from mb_wms_availability where fkey_wms_id=NEW.fkey_wms_id)*count_monitors)/(count_monitors+1) as numeric),2);
     END IF;

      UPDATE mb_wms_availability SET average_resp_time=average_res_cap,last_status=NEW.status, availability=availability_new, image=NEW.image, status_comment=NEW.status_comment,upload_url=NEW.upload_url,map_url=NEW.map_url, cap_diff=NEW.cap_diff WHERE mb_wms_availability.fkey_wms_id=NEW.fkey_wms_id;
      RETURN NEW;
     END IF;
     IF TG_OP = 'INSERT' THEN

	IF (select count(fkey_wms_id) from mb_wms_availability where fkey_wms_id=NEW.fkey_wms_id) > 0  then -- service is not new
			UPDATE mb_wms_availability set fkey_upload_id=NEW.upload_id,last_status=NEW.status,status_comment=NEW.status_comment,upload_url=NEW.upload_url, cap_diff=NEW.cap_diff where fkey_wms_id=NEW.fkey_wms_id;
		else --service has not yet been monitored
			INSERT INTO mb_wms_availability (fkey_upload_id,fkey_wms_id,last_status,status_comment,upload_url,map_url,cap_diff,average_resp_time,availability) VALUES (NEW.upload_id,NEW.fkey_wms_id,NEW.status,NEW.status_comment,NEW.upload_url::text,NEW.map_url,NEW.cap_diff,0,100);
		end if;

      RETURN NEW;
     END IF;
    END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

--
-- new columns with uuids for resources wms, layer, wfs, featuretype - is needed to generate konsistent metadatasets
-- you need a postgres >= 8.3 cause the new datatype uuid is used!
-- 
ALTER TABLE wms ADD COLUMN uuid UUID;
ALTER TABLE layer ADD COLUMN uuid UUID;
ALTER TABLE wfs ADD COLUMN uuid UUID;
ALTER TABLE wfs_featuretype ADD COLUMN uuid UUID;

-- ALTER TABLE wms ADD COLUMN uuid character varying;
-- ALTER TABLE layer ADD COLUMN uuid character varying;
-- ALTER TABLE wfs ADD COLUMN uuid character varying;
-- ALTER TABLE wfs_featuretype ADD COLUMN uuid character varying;


-- enlarge the size of the featureinfo dialog window
UPDATE gui_element_vars 
SET var_value = 400 WHERE fkey_gui_id = 'gui1' 
AND fkey_e_id = 'featureInfo1' AND var_name IN ('featureInfoPopupHeight','featureInfoPopupWidth');

--
--deactivate OnEarth Web Map Server in gui_digitize 
--
UPDATE gui_layer SET gui_layer_visible='0' where fkey_gui_id='gui_digitize' AND gui_layer_wms_id='640';

--
-- feedTree to load GeoRSS or KML
-- create a new tab in application gui to load georss
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'tabs', 'tab_frameHeight[8]', '240', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'tabs', 'tab_ids[8]', 'feedTree', '' ,'php_var');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui','feedTree',2,1,'Displays a GeoRSS feed on the map','GeoRss','ul','','',1,1,1,1,NULL ,'visibility:hidden','','ul','../plugins/feedTree.js','../../lib/mb.ui.displayFeatures.js','mapframe1','jq_ui_widget','http://www.mapbender.org/Loadkmlgeorss');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'feedTree', 'styles', '../css/feedtree.css', '' ,'file/css');

--
--fix 4 coordsLookup in gui
--
UPDATE gui_element_vars set var_value='EPSG:4326;Geographic Coordinates,EPSG:31466;Gauss-Krueger 2,EPSG:31467;Gaus-Krueger 3' WHERE fkey_gui_id='gui' AND fkey_e_id='coordsLookup' AND var_name='projections';
UPDATE gui_element_vars set var_type='php_var' WHERE fkey_gui_id='gui' AND fkey_e_id='coordsLookup' AND var_name='projections';

-- update dragMapSize in all applications not only in template applications
UPDATE gui_element set e_attributes = 'class="ui-state-default"',e_left=-59,e_top=1,e_width=15,e_height=15, e_more_styles='font-size:1px; cursor:move;', e_content= '<span class="ui-icon ui-icon ui-icon-arrow-4"></span>' where e_id = 'dragMapSize';

--
--remove old GeoRss Module from gui1 
--

DELETE FROM gui_element WHERE fkey_gui_id='gui1' AND e_id='addGeoRSS';

--
-- ad splash to template applications gui, gui1, gui2
--
UPDATE gui_element_vars set var_value = '../include/mapbender_logo_splash.php', 
context = 'show splash screen while the application is loading' 
where fkey_gui_id IN ('gui','gui1','gui2') AND fkey_e_id = 'body' AND var_name = 'includeWhileLoading';

--
-- new template_print with print demo and navigation
--
INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('template_print','template_print','This application demonstrates the print with PDF template functionality',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','jq_datatables','defaultCss','../extensions/dataTables-1.5/media/css/demo_table_jui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','mapframe1',1,1,'Frame for a map','','div','','',210,50,500,500,2,'overflow:hidden;background-color:#ffffff','','div','../plugins/mb_map.js','../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWmcObj.php','','','http://www.mapbender.org/index.php/Mapframe');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','mapframe1','skipWmsIfSrsNotSupported','0','if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','mapframe1','slippy','0','1 = Activates an animated, pseudo slippy map','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui',1,1,'The jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.core.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','jq_ui','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.widget.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','printButton',1,1,'','Druck','img','../img/button_blue_red/print_off.png','',480,10,24,24,NULL ,'','','','../plugins/mb_button.js','','printPDF','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','printButton','position','[720,100]','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','body',1,1,'body (obligatory)','','body','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','css_class_bg','body{ background-color: #ffffff; }','to define the color of the body','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','css_file_body','../css/mapbender.css','file/css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','popupcss','../css/popup.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','use_load_message','true','','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','tablesortercss','../css/tablesorter.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','favicon','../img/favicon.png','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','jquery_datatables','../extensions/dataTables-1.5/media/css/demo_table_jui.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','jq_ui_theme','../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css','UI Theme from Themeroller','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','print_css','../css/print_div.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','body','includeWhileLoading','../include/mapbender_logo_splash.php','show splash screen while the application is loading','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_upload',1,1,'','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../plugins/jq_upload.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','zoomIn1',2,1,'zoomIn button','In die Karte hineinzoomen','img','../img/button_blue_red/zoomIn2_off.png','',210,10,28,28,1,'','','','mod_zoomIn1.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomIn');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','sandclock',2,1,'displays a sand clock while waiting for requests','','div','','',0,0,0,0,0,'','','div','mod_sandclock.js','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','sandclock','mod_sandclock_image','../img/sandclock.gif','define a sandclock image ','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_position',2,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.position.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','zoomFull',2,1,'zoom to full extent button','gesamte Karte anzeigen','img','../img/button_blue_red/zoomFull_off.png','',330,10,28,28,2,'','','','mod_zoomFull.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomFull');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','featureInfo1',2,1,'FeatureInfoRequest','Datenabfrage','img','../img/button_blue_red/query_off.png','',400,10,28,28,1,'','','','mod_featureInfo.php','','mapframe1','','http://www.mapbender.org/index.php/FeatureInfo');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','featureInfo1','featureInfoLayerPopup','true','display featureInfo in dialog popup','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','featureInfo1','featureInfoPopupHeight','400','height of the featureInfo dialog popup','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','featureInfo1','featureInfoPopupPosition','[600,50]','position of the featureInfoPopup','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','featureInfo1','featureInfoPopupWidth','400','width of the featureInfo dialog popup','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','treeGDE',2,1,'new treegde2 - directory tree, checkbox for visible, checkbox for querylayer
for more infos have a look at http://www.mapbender.org/index.php/TreeGDE2','Karten','div','','class="ui-widget"',10,220,200,300,NULL ,'','','div','../html/mod_treefolderPlain.php','jsTree.js','mapframe1','mapframe1','http://www.mapbender.org/index.php/TreeGde');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','alerterror','true','alertbox for wms loading error','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','ficheckbox','true','checkbox for featureInfo requests','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','imagedir','../img/tree_new','image directory','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','menu','opacity_up,opacity_down,zoom,metainfo,hide,wms_up,wms_down,layer_up,layer_down,remove','context menu elements','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','metadatalink','false','link for layer-metadata','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','openfolder','false','initial open folder','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','showstatus','true','show status in folderimages','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','wmsbuttons','false','wms management buttons','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','switchwms','true','enables/disables all layer of a wms','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','treeGDE','css','../css/treeGDE2.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','zoomOut1',2,1,'zoomOut button','Aus der Karte herauszoomen','img','../img/button_blue_red/zoomOut2_off.png','',240,10,28,28,1,'','','','mod_zoomOut1.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomOut');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','overview',2,1,'OverviewFrame','','div','','',10,10,180,180,2,'overflow:hidden;background-color:#ffffff','<div id="overview_maps" style="position:absolute;left:0px;right:0px;"></div>','div','../plugins/mb_overview.js','../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWmcObj.php','mapframe1','mapframe1','http://www.mapbender.org/index.php/Overview');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','overview','overview_wms','0','wms that shows up as overview','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','overview','skipWmsIfSrsNotSupported','0','if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','pan1',2,1,'pan','Ausschnitt verschieben','img','../img/button_blue_red/pan_off.png','',270,10,28,28,1,'','','','mod_pan.js','','mapframe1','','http://www.mapbender.org/index.php/Pan');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','selArea1',2,1,'zoombox','Ausschnitt wählen','img','../img/button_blue_red/selArea_off.png','',300,10,28,28,1,'','','','mod_selArea.js','mod_box1.js','mapframe1','','http://www.mapbender.org/index.php/SelArea1');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','doubleclickZoom',2,1,'adds doubleclick zoom to map module (target).
Deactivates the browser contextmenu!!!','Doubleclick zoom','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','mod_doubleclickZoom.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','copyright',2,1,'a Copyright in the map','Copyright','div','','',0,0,0,0,0,'','','div','mod_copyright.php','','mapframe1','','http://www.mapbender.org/index.php/Copyright');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','copyright','mod_copyright_text','mapbender.org','define a copyright text which should be displayed','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','printPDF',2,1,'pdf print','Druck','div','','',860,10,200,380,5,'','<div id="printPDF_working_bg"></div><div id="printPDF_working"><img src="../img/indicator_wheel.gif" style="padding:10px 0 0 10px">Generating PDF</div><div id="printPDF_input"><form id="printPDF_form" action="../print/printFactory.php"><div id="printPDF_selector"></div><div class="print_option"><input type="hidden" id="map_url" name="map_url" value=""/><input type="hidden" id="overview_url" name="overview_url" value=""/><input type="hidden" id="map_scale" name="map_scale" value=""/><input type="hidden" name="measured_x_values" /><input type="hidden" name="measured_y_values" /><br /></div><div class="print_option" id="printPDF_formsubmit"><input id="submit" type="submit" value="Print"><br /></div></form><div id="printPDF_result"></div></div>','div','../plugins/mb_print.php','../../lib/printbox.js,../extensions/jquery-ui-1.7.2.custom/development-bundle/external/bgiframe/jquery.bgiframe.min.js,../extensions/jquery.form.min.js,../extensions/wz_jsgraphics.js','mapframe1','','http://www.mapbender.org/index.php/Print');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','printPDF','highqualitymapfiles','[
    {
        "pattern": "/data/umn/germany/germany.map",
        "replacement" : {
            "288": "/data/umn/germany/germany_4.map" 
        }
    },
	{
        "pattern": "/data/umn/mapbender_user/mapbender_user.map",
        "replacement" : {
            "288": "/data/umn/mapbender_user/mapbender_user_4.map" 
        }
    }
]','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','printPDF','mbPrintConfig','{"A4 landscape": "A4_landscape_template.json","A4 portrait": "A4_portrait_template.json","A3 landscape": "A3_landscape_template.json","A3 portrait": "A3_portrait_template.json"}','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','dragMapSize',2,1,'drag & drop Mapsize','Karte vergrößern','div','','class="ui-state-default"',-59,1,15,15,2,'font-size:1px; cursor:move;','<span class="ui-icon ui-icon ui-icon-arrow-4"></span>','div','mod_dragMapSize.php','','mapframe1','','http://www.mapbender.org/index.php/DragMapSize');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.mouse.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','mapframe1_navigation',4,1,'Adds navigation arrows on top of the map','Navigation','div','','',20,30,NULL ,NULL,1001,'','','div','../plugins/mb_navigation.js','','mapframe1','mapframe1','http://www.mapbender.org/Navigation');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_droppable',4,1,'jQuery UI droppable','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.droppable.js','','jq_ui,jq_ui_widget,jq_ui_mouse,jq_ui_draggable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.button.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','mapframe1_mousewheelZoom',4,1,'adds mousewheel zoom to map module (target)','Mousewheel zoom','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','mod_mousewheelZoom.js','../extensions/jquery.mousewheel.min.js','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','mapframe1_mousewheelZoom','factor','2','The factor by which the map is zoomed on each mousewheel unit','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','mapframe1_zoomBar',4,1,'zoom bar - slider to handle navigation, define zoom level with an element var','Zoom to scale','div','','',30,100,NULL ,200,1001,'','','div','../plugins/mb_zoomBar.js','','mapframe1','mapframe1, jq_ui_slider','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','mapframe1_zoomBar','defaultLevel','3','define the default level for application start','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_print','mapframe1_zoomBar','level','[2500,5000,10000,50000,100000,500000,1000000,3000000,5000000,10000000]','define an array of levels for the slider (element_var has to be defined)','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_slider',5,1,'slider from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.slider.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_dialog',5,1,'Module to manage jQuery UI dialog windows with multiple options for customization.','','div','','',-1,-1,NULL ,NULL,NULL ,'','','div','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.dialog.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.draggable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.tabs.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_print','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.resizable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');


-------------
--WMS germany gui_wms
INSERT INTO gui_wms (fkey_gui_id,fkey_wms_id,gui_wms_position,gui_wms_mapformat,
gui_wms_featureinfoformat,gui_wms_exceptionformat,gui_wms_epsg,gui_wms_visible,gui_wms_opacity,gui_wms_sldurl) 
(SELECT 
'template_print', fkey_wms_id, 0, 
gui_wms_mapformat,gui_wms_featureinfoformat,
gui_wms_exceptionformat,gui_wms_epsg,1 as gui_wms_visible,gui_wms_opacity,gui_wms_sldurl
FROM gui_wms WHERE fkey_gui_id = 'gui1' AND fkey_wms_id = 893);
-- WMS germany gui_layer
INSERT INTO gui_layer (fkey_gui_id,fkey_layer_id,gui_layer_wms_id,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,gui_layer_maxscale,
gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title)
(SELECT 
'template_print', 
fkey_layer_id,gui_layer_wms_id ,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,
gui_layer_maxscale,gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title
 FROM gui_layer WHERE fkey_gui_id = 'gui1' AND gui_layer_wms_id = 893);


--WMS mapbender_user gui_wms
INSERT INTO gui_wms (fkey_gui_id,fkey_wms_id,gui_wms_position,gui_wms_mapformat,
gui_wms_featureinfoformat,gui_wms_exceptionformat,gui_wms_epsg,gui_wms_visible,gui_wms_opacity,gui_wms_sldurl) 
(SELECT 
'template_print', fkey_wms_id, 1, 
gui_wms_mapformat,gui_wms_featureinfoformat,
gui_wms_exceptionformat,gui_wms_epsg,1 as gui_wms_visible,gui_wms_opacity,gui_wms_sldurl
FROM gui_wms WHERE fkey_gui_id = 'gui1' AND fkey_wms_id = 407);
-- WMS mapbender_user gui_layer
INSERT INTO gui_layer (fkey_gui_id,fkey_layer_id,gui_layer_wms_id,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,gui_layer_maxscale,
gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title)
(SELECT 
'template_print', 
fkey_layer_id,gui_layer_wms_id ,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,
gui_layer_maxscale,gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title
 FROM gui_layer WHERE fkey_gui_id = 'gui1' AND gui_layer_wms_id = 407);
Update gui_layer set gui_layer_maxscale = 0 WHERE fkey_gui_id = 'template_print' AND gui_layer_wms_id = 407;

INSERT into gui_mb_user (fkey_gui_id , fkey_mb_user_id , mb_user_type) VALUES ('template_print',1,'owner');

INSERT into gui_gui_category VALUES ('template_print', 2);

------
-- new application Administration
--
INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('Administration','Administration','Administration',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','jq_ui',1,1,'jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.core.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','jq_ui','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.widget.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','owsproxy_icon',2,1,'icon','','img','../img/gnome/emblem-readonly.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wms_metadata_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','wms_metadata,wms_metadata_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','updateWMSs_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','updateWMSs,updateWMSs_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wmc_metadata_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','wmc_metadata,wmc_metadata_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','menu_wmc',2,1,'WMC admin menu','Admin WMC','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','wmc_metadata_collection','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','menu_wmc','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','monitor_results_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,200,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','monitor_results,monitor_results_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','monitor_results_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-remote-desktop.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wmc_metadata_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-personal.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadWFS_icon',2,1,'icon','','img','../img/gnome/document-save.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','updateWMSs_icon',2,1,'icon','','img','../img/gnome/view-refresh.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wfs_metadata_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-personal.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wfs_metadata_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','wfs_metadata,wfs_metadata_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','fullScreen',2,1,'Link to open Admin in new window','Full Screen View','span','','onclick="window.open(window.location.href,''FullAdminWindow'',''scrollbars=yes,resizeable=yes'')"
',10,320,100,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Too small? - open in new window','span','','','','','http://www.mapbender.org/index.php/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','fullScreenIcon',2,1,'show iframe in new window','Open in new Window','img','../img/button_blue_red/resizemapsize_off.png','',120,320,24,24,NULL ,'','','','','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','fullScreenIcon','adjust_height','-35','to adjust the height of the mapframe on the bottom of the window','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','fullScreenIcon','adjust_width','30','to adjust the width of the mapframe on the right side of the window','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','fullScreenIcon','resize_option','button','auto (autoresize on load), button (resize by button)','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wms_metadata_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-personal.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wms_mail_abo_icon',2,1,'icon','','img','../img/gnome/mail-message-new.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteGui',2,0,'delete gui','Delete GUI','a','','href = "../php/mod_deleteGUI.php?sessionID" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','DELETE GUI','a','','','','','http://www.mapbender.org/index.php/DeleteGUI');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWFS',2,1,'delete wfs','Delete WFS','a','','href = "../php/mod_deleteWFS.php?sessionID"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','DELETE WFS','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWFSConf',2,1,'delete wfs conf','Delete WFS-Conf','a','','href = "../javascripts/mod_deleteWfsConf_client.html" ',80,15,250,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','DELETE FEATURETYPE-CONF','a','','','','AdminFrame','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','updateWFS_icon',2,1,'icon','','img','../img/gnome/view-refresh.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadWFS',2,1,'load capabilities in a gui','Load WFS','a','','href = "../php/mod_loadWFSCapabilities.php?sessionID" target="AdminFrame"',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','LOAD WFS','a','','','','','http://www.mapbender.org/index.php/WFS_Konfiguration');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','monitor_results',2,1,'','Monitoring results','a','','href = "../php/mod_monitorCapabilities_read.php?sessionID" ',80,15,NULL ,NULL,2,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','MONITORING RESULTS','a','','','','','http://www.mapbender.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','updateWFS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','updateWFS,updateWFS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','owsproxy',2,1,'secure services','OWSProxy WMS','a','','href="../php/mod_owsproxy_conf.php?sessionID"'' ',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','OWSPROXY','a','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWFS_icon',2,1,'icon','','img','../img/gnome/edit-delete.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','owsproxy_wfs',2,1,'secure services','OWSProxy WFS','a','','href="../javascripts/mod_wfs_client.html" ',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','OWSPROXY WFS','a','','','','AdminFrame','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWFS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteWFS,deleteWFS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','updateWFS',2,1,'edit the elements of the gui','Update WFS','a','','href="../javascripts/mod_wfs_client.html"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','UPDATE WFS','a','','','','AdminFrame','http://www.mapbender.org/index.php/UpdateWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wfs_conf',2,1,'configure wfs','Configure Featuretype','a','','href = "../php/mod_wfs_conf_client.php"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','FEATURETYPE-CONF','a','','','','AdminFrame','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wfs_metadata',2,1,'wfs metadata editor','Metadataeditor WFS','a','','href = "../frames/index.php?guiID=admin_wfs_metadata"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','METADATA EDITOR','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editGUI_WFS_icon',2,1,'icon','','img','../img/gnome/preferences-other.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wmc_metadata',2,1,'wmc metadata editor','Metadataeditor WMC','a','','href = "../frames/index.php?guiID=admin_wmc_metadata"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','METADATA EDITOR','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wms_metadata',2,1,'wms metadata editor','Metadataeditor WMS','a','','href = "../frames/index.php?guiID=admin_wms_metadata"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','METADATA EDITOR','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editGUI_WFS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,120,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editGUI_WFS,editGUI_WFS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','menu_auth',2,1,'GUI admin menu','Authorization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGui_user_collection,filteredGui_group_collection,filteredGui_filteredGroup_collection,gui_owner_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','monitor_abo_show',2,1,'send mail to user which have abos','Mail Abo WMS','a','','href = "../php/mod_abo_show.php?sessionID"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','MAIL ABO','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wms_mail_abo_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','monitor_abo_show,wms_mail_abo_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','owsproxy_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,160,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','owsproxy,owsproxy_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','owsproxy_wfs_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,160,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','owsproxy_wfs,owsproxy_wfs_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','owsproxy_wfs_icon',2,1,'icon','','img','../img/gnome/emblem-readonly.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wfs_conf_icon',2,1,'icon','','img','../img/gnome/preferences-other.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','wfs_conf_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','wfs_conf,wfs_conf_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWFSConf_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,120,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteWFSConf,deleteWFSConf_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','menu_wfs',2,1,'WFS admin menu','Admin WFS','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWFS_collection,updateWFS_collection,editGUI_WFS_collection,deleteWFS_collection,wfs_metadata_collection,owsproxy_wfs_collection,wfs_conf_collection,deleteWFSConf_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editFilteredGroup_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editFilteredGroup,editFilteredGroup_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','menu_user',2,1,'User admin menu','Admin User','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','editFilteredUser_collection,editFilteredGroup_collection,filteredGroup_User_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGroup_User_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGroup_User,filteredGroup_User_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editFilteredGroup_icon',2,1,'icon','','img','../img/gnome/editMyGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','newGui_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','newGui,newGui_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadWMS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWMS,loadWMS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadWFS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWFS,loadWFS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','newGui_icon',2,1,'icon','','img','../img/gnome/newGui.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','rename_copy_gui_icon',2,1,'icon','','img','../img/gnome/edit-copy.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','rename_copy_gui_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','rename_copy_Gui,rename_copy_gui_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWFSConf_icon',2,1,'icon','','img','../img/gnome/edit-delete.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editElements_icon',2,1,'icon','','img','../img/gnome/editGuiElements.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editElements_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,120,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editElements,editElements_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','delete_filteredGui_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','delete_filteredGui,delete_filteredGui_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','delete_filteredGui_icon',2,1,'icon','','img','../img/gnome/deleteGui.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteGui_icon',2,0,'icon','','img','../img/gnome/deleteGui.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','category_filteredGui_icon',2,1,'icon','','img','../img/gnome/myGuiCategories.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','category_filteredGui_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,160,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','category_filteredGUI,category_filteredGui_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_user_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGui_user,filteredGui_user_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_user_icon',2,1,'icon','','img','../img/gnome/myGuiUser.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_group_icon',2,1,'icon','','img','../img/gnome/myGuiGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_group_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGui_Group,filteredGui_group_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGroup_User_icon',2,1,'icon','','img','../img/gnome/myGroupUser.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_filteredGroup_icon',2,1,'icon','','img','../img/gnome/myGuiMyGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_filteredGroup_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGui_filteredGroup,filteredGui_filteredGroup_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.mouse.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadWMS_icon',2,1,'icon','','img','../img/gnome/document-save.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editGUI_WMS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,120,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editGUI_WMS,editGUI_WMS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWMS_icon',2,1,'icon','','img','../img/gnome/edit-delete.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','jq_ui_position',2,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.position.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_user',2,1,'Allow several users access to an application','Allow several users access to an application','a','','href = "../php/mod_filteredGui_User.php?sessionID&e_id_css=filteredGui_user" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Allow several users access to an application','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','filteredGui_user','file css','../css/administration_alloc.css','a file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editGUI_WMS_icon',2,1,'icon','','img','../img/gnome/preferences-other.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWMS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteWMS,deleteWMS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','menu_wms',2,1,'WMS admin menu','Admin WMS','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWMS_collection,updateWMSs_collection,deleteWMS_collection,editGUI_WMS_collection,owsproxy_collection,monitor_results_collection,wms_metadata_collection,wms_mail_abo_collection,loadExtWMS_collection','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','menu_wms','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadWMSList',2,1,'Link WMS to application','Link WMS to application','a','','href = "../php/mod_loadCapabilitiesList.php?sessionID"',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Link WMS to application','a','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editGUI_WFS',2,1,'Assign WFS conf to application','Assign WFS conf to application','a','','href="../javascripts/mod_wfs_client.html"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Assign WFS conf to application','a','','','','AdminFrame','http://www.mapbender.org/index.php/Edit_GUI_WMS');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','editGUI_WFS','file_css','../css/edit_gui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','showLoggedUser',2,1,'Anzeige des eingeloggten Benutzers','','iframe','../php/mod_showLoggedUser.php?sessionID','frameborder="0" scrolling=''no''',1,1,200,30,1,'background-color:lightgrey;','','iframe','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','showLoggedUser','css_file_user_logged','../css/administration_alloc.css','file/css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGroup_User',2,1,'Add several users to one group','Add several users to one group','a','','href = "../php/mod_filteredGroup_User.php?sessionID&e_id_css=filteredGroup_User" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Add several users to one group','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','filteredGroup_User','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_Group',2,1,'Allocate applications to groups','Allocate applications to groups','a','','href = "../php/mod_filteredGui_group.php?sessionID&e_id_css=filteredGui_Group" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Allocate applications to groups','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','filteredGui_Group','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editElements',2,1,'Edit application elements','Edit application elements','a','','href = "../php/mod_editElements.php?sessionID" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none;color: #808080;','Edit application elements','a','','','','','http://www.mapbender.org/index.php/Edit_GUI_Elements');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editFilteredGroup',2,1,'Create and edit group','Create and edit group','a','','href = "../php/mod_editFilteredGroup.php?sessionID"',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Create and edit group','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadWMS',2,1,'Load Capabilities','Load WMS','a','','href = "../php/mod_loadCapabilities.php?sessionID"',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Load WMS','a','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','category_filteredGUI',2,1,'Add application to category','Add application to category','a','','href = "../php/mod_category_filteredGUI.php?sessionID&e_id_css=filteredUser_filteredGroup"',80,15,190,20,10,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Add application to category','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','category_filteredGUI','cssfile','../css/administration_alloc.css','css file for admin module','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','deleteWMS',2,1,'!Delete WMS completely!','Delete WMS completely','a','','href = "../php/mod_deleteWMS.php?sessionID"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','!Delete WMS completely!','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','delete_filteredGui',2,1,'Delete application','Delete application','a','','href = "../php/mod_deleteFilteredGUI.php?sessionID"',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Delete application','a','','','','','http://www.mapbender.org/index.php/DeleteGUI');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','filteredGui_filteredGroup',2,1,'Allow several groups access <br> to one application','Allow several groups access <br> to one application','a','','href = "../php/mod_filteredGui_filteredGroup.php?sessionID&e_id_css=filteredGui_filteredGroup" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Allow several groups access <br> to one application','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','filteredGui_filteredGroup','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editGUI_WMS',2,1,'WMS application settings','WMS application settings','a','','href="../php/mod_editGuiWms.php?sessionID" 
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','WMS application settings','a','','','','','http://www.mapbender.org/index.php/Edit_GUI_WMS');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','editGUI_WMS','file_css','../css/edit_gui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','newGui',2,1,'Create new application','Create new application','a','','href = "../php/mod_newGui.php?sessionID" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none;color: #808080;','Create new application','a','','','','','http://www.mapbender.org/index.php/newGUI');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','rename_copy_Gui',2,1,'Rename / copy application','Rename / copy application','a','','href = "../php/mod_renameGUI.php?sessionID" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Rename / copy application','a','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','updateWMSs',2,1,'Update with Capabilities','Update WMS','a','','href="../php/mod_updateWMS.php?sessionID"'' 
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Update WMS','a','','','','','http://www.mapbender.org/index.php/UpdateWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editFilteredUser',2,1,'Create and edit user','Create and edit user','a','','href = "../php/mod_editFilteredUser.php?sessionID" target="AdminFrame"',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Create and edit user','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','editFilteredUser','withPasswordInsertion','true','define if admin can set the new user','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editFilteredUser_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editFilteredUser,editFilteredUser_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','editFilteredUser_icon',2,1,'icon','','img','../img/gnome/editMyUser.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','menu_gui',2,1,'GUI admin menuA','Admin Application','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','newGui_collection,rename_copy_gui_collection,delete_filteredGui_collection,editElements_collection,category_filteredGui_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadExtWMS_icon',2,1,'icon','','img','../img/gnome/loadExternalWms.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','loadExtWMS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWMSList,loadExtWMS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.button.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.resizable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.dialog.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.draggable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','mb_iframepopup',7,1,'iframepopup','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_iframepopup.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration','mb_horizontal_accordion',10,1,'Put existing divs in new horizontal accordion div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,NULL ,NULL,NULL ,'','<dl></dl>','div','../plugins/mb_horizontal_accordion.js','../../extensions/jqueryEasyAccordion/jquery.easyAccordion.js','menu_wms,menu_wfs,menu_wmc,menu_user,menu_gui,menu_auth','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration','mb_horizontal_accordion','Accordion css file','../extensions/jqueryEasyAccordion/mb_jquery.easyAccordion.css','','file/css');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration','myGUIlist',2,1,'Move back to your application list','Move back to your application list','img','../img/button_blue_red/home_off.png','onClick="mod_home_init()" border=''0'' onmouseover=''this.src="../img/button_blue_red/home_over.png"'' onmouseout=''this.src="../img/button_blue_red/home_off.png"''',200,2,24,24,2,'','','','mod_home.php','','','','http://www.mapbender.org/index.php/MyGUIlist');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration','gui_owner',2,1,'Assign to edit an application to a user','Assign to edit an application to a user','a','','href = "../php/mod_gui_owner.php?sessionID" target = "AdminFrame" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Assign to edit an application to a user','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('Administration', 'gui_owner', 'file css', '../css/administration_alloc.css', 'file css' ,'file/css');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration','gui_owner_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,160,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','gui_owner,gui_owner_icon','','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration','gui_owner_icon',2,1,'icon','','img','../img/gnome/guiMyUser.png','',0,0,NULL ,NULL ,2,'','','','','','','','');
INSERT into gui_mb_user (fkey_gui_id , fkey_mb_user_id , mb_user_type) VALUES ('Administration',1,'owner');

INSERT into gui_gui_category VALUES ('Administration', 1);

------
-- new application Administration_DE
--
INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('Administration_DE','Administration_DE','Administrationsoberfläche',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','jq_ui',1,1,'jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.core.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','jq_ui','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','body',1,1,'Navigation','','body','','',0,0,200,40,NULL ,'','','','../geoportal/mod_revertGuiSessionSettings.php','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','body','favicon','../img/favicon.png','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','body','jq_ui_theme','../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','body','jq_ui_effects_transfer','.ui-effects-transfer { z-index:1003; border: 2px dotted gray; } ','','text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','body','includeWhileLoading','','show splash screen while the application is loading','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','body','use_load_message','false','show splash screen while the application is loading','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.widget.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','owsproxy_icon',2,1,'icon','','img','../img/gnome/emblem-readonly.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wms_metadata_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','wms_metadata,wms_metadata_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','updateWMSs_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','updateWMSs,updateWMSs_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wmc_metadata_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','wmc_metadata,wmc_metadata_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','menu_wmc',2,1,'WMC admin menu','Admin WMC','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','wmc_metadata_collection','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','menu_wmc','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','monitor_results_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,200,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','monitor_results,monitor_results_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','monitor_results_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-remote-desktop.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wmc_metadata_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-personal.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadWFS_icon',2,1,'icon','','img','../img/gnome/document-save.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','updateWMSs_icon',2,1,'icon','','img','../img/gnome/view-refresh.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wfs_metadata_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-personal.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wfs_metadata_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','wfs_metadata,wfs_metadata_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wms_metadata_icon',2,1,'icon','','img','../img/gnome/preferences-desktop-personal.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wms_mail_abo_icon',2,1,'icon','','img','../img/gnome/mail-message-new.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteGui',2,0,'delete gui','Delete GUI','a','','href = "../php/mod_deleteGUI.php?sessionID" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','DELETE GUI','a','','','','','http://www.mapbender.org/index.php/DeleteGUI');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','updateWFS_icon',2,1,'icon','','img','../img/gnome/view-refresh.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','updateWFS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','updateWFS,updateWFS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWFS_icon',2,1,'icon','','img','../img/gnome/edit-delete.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWFS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteWFS,deleteWFS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editGUI_WFS_icon',2,1,'icon','','img','../img/gnome/preferences-other.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editGUI_WFS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,120,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editGUI_WFS,editGUI_WFS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wms_mail_abo_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','monitor_abo_show,wms_mail_abo_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','owsproxy_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,160,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','owsproxy,owsproxy_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','owsproxy_wfs_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,160,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','owsproxy_wfs,owsproxy_wfs_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','owsproxy_wfs_icon',2,1,'icon','','img','../img/gnome/emblem-readonly.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wfs_conf_icon',2,1,'icon','','img','../img/gnome/preferences-other.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wfs_conf_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','wfs_conf,wfs_conf_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWFSConf_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,120,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteWFSConf,deleteWFSConf_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','menu_wfs',2,1,'WFS admin menu','Admin WFS','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWFS_collection,updateWFS_collection,editGUI_WFS_collection,deleteWFS_collection,wfs_metadata_collection,owsproxy_wfs_collection,wfs_conf_collection,deleteWFSConf_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editFilteredGroup_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editFilteredGroup,editFilteredGroup_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGroup_User_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGroup_User,filteredGroup_User_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editFilteredGroup_icon',2,1,'icon','','img','../img/gnome/editMyGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','newGui_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','newGui,newGui_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadWMS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWMS,loadWMS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadWFS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWFS,loadWFS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','newGui_icon',2,1,'icon','','img','../img/gnome/newGui.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','rename_copy_gui_icon',2,1,'icon','','img','../img/gnome/edit-copy.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','rename_copy_gui_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','rename_copy_Gui,rename_copy_gui_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWFSConf_icon',2,1,'icon','','img','../img/gnome/edit-delete.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editElements_icon',2,1,'icon','','img','../img/gnome/editGuiElements.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editElements_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,120,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editElements,editElements_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','delete_filteredGui_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','delete_filteredGui,delete_filteredGui_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','delete_filteredGui_icon',2,1,'icon','','img','../img/gnome/deleteGui.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteGui_icon',2,0,'icon','','img','../img/gnome/deleteGui.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','category_filteredGui_icon',2,1,'icon','','img','../img/gnome/myGuiCategories.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','category_filteredGui_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,160,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','category_filteredGUI,category_filteredGui_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_user_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGui_user,filteredGui_user_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_user_icon',2,1,'icon','','img','../img/gnome/myGuiUser.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_group_icon',2,1,'icon','','img','../img/gnome/myGuiGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_group_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,40,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGui_Group,filteredGui_group_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGroup_User_icon',2,1,'icon','','img','../img/gnome/myGroupUser.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_filteredGroup_icon',2,1,'icon','','img','../img/gnome/myGuiMyGroup.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_filteredGroup_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGui_filteredGroup,filteredGui_filteredGroup_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','fullScreen',2,1,'Link to open Admin in new window','Öffne in neuem Fenster','span','','onclick="window.open(window.location.href,''Administrations Fenster'',''scrollbars=yes,resizeable=yes'')"
',10,320,100,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Zu klein? - Öffne in neuem Fenster','span','','','','','http://www.mapbender.org/index.php/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadWMS_icon',2,1,'icon','','img','../img/gnome/document-save.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editGUI_WMS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,120,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editGUI_WMS,editGUI_WMS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','updateWFS',2,1,'edit the elements of the gui','Update WFS','a','','href="../javascripts/mod_wfs_client.html"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Aktualisieren','a','','','','AdminFrame','http://www.mapbender.org/index.php/UpdateWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wfs_conf',2,1,'configure wfs','Configure Featuretype','a','','href = "../php/mod_wfs_conf_client.php"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Featuretype Modul einrichten','a','','','','AdminFrame','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wfs_metadata',2,1,'wfs metadata editor','Metadataeditor WFS','a','','href = "../frames/index.php?guiID=admin_wfs_metadata"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Metadaten Editor','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wmc_metadata',2,1,'wmc metadata editor','Metadataeditor WMC','a','','href = "../frames/index.php?guiID=admin_wmc_metadata"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Metadaten Editor','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','menu_auth',2,1,'GUI admin menu','Autorisierung','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','filteredGui_user_collection,filteredGui_group_collection,filteredGui_filteredGroup_collection,gui_owner_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','fullScreenIcon',2,1,'show iframe in new window','Open in new Window','img','../img/button_blue_red/resizemapsize_off.png','',120,320,24,24,NULL ,'','','','','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','fullScreenIcon','adjust_height','-35','to adjust the height of the mapframe on the bottom of the window','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','fullScreenIcon','adjust_width','30','to adjust the width of the mapframe on the right side of the window','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','fullScreenIcon','resize_option','button','auto (autoresize on load), button (resize by button)','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','jq_ui_position',2,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.position.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.mouse.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','monitor_results',2,1,'','Monitoring results','a','','href = "../php/mod_monitorCapabilities_read.php?sessionID" ',80,15,NULL ,NULL,2,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Monitoring Ergebnisse','a','','','','','http://www.mapbender.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','owsproxy',2,1,'secure services','OWSProxy WMS','a','','href="../php/mod_owsproxy_conf.php?sessionID"'' ',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Sicherheits Proxy','a','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','wms_metadata',2,1,'wms metadata editor','Metadataeditor WMS','a','','href = "../frames/index.php?guiID=admin_wms_metadata"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Metadaten - Editor','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWMS',2,1,'!Vollständig löschen!','Delete WMS completely','a','','href = "../php/mod_deleteWMS.php?sessionID"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','!Vollständig löschen!','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','monitor_abo_show',2,1,'send mail to user which have abos','Mail Abo WMS','a','','href = "../php/mod_abo_show.php?sessionID"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Abonnenten benachrichtigen','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWFS',2,1,'delete wfs','Delete WFS','a','','href = "../php/mod_deleteWFS.php?sessionID"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Löschen','a','','','','','http://www.mapbender.org/index.php/DeleteWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWFSConf',2,1,'delete wfs conf','Delete WFS-Conf','a','','href = "../javascripts/mod_deleteWfsConf_client.html" ',80,15,250,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Löschen von WFS Modulen','a','','','','AdminFrame','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','delete_filteredGui',2,1,'Anwendung löschen','Anwendung löschen','a','','href = "../php/mod_deleteFilteredGUI.php?sessionID"',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Anwendung löschen','a','','','','','http://www.mapbender.org/index.php/DeleteGUI');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editElements',2,1,'Anwendungselemente bearbeiten','Anwendungselemente bearbeiten','a','','href = "../php/mod_editElements.php?sessionID" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none;color: #808080;','Anwendungselemente bearbeiten','a','','','','','http://www.mapbender.org/index.php/Edit_GUI_Elements');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editFilteredGroup',2,1,'Gruppe anlegen und editieren','Create and edit group','a','','href = "../php/mod_editFilteredGroup.php?sessionID"',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Gruppe anlegen und editieren','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editGUI_WFS',2,1,'edit the elements of the gui','WFS-Konfiguration Anwendung zuweisen','a','','href="../javascripts/mod_wfs_client.html"
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','WFS-Konfiguration Anwendung zuweisen','a','','','','AdminFrame','http://www.mapbender.org/index.php/Edit_GUI_WMS');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','editGUI_WFS','file_css','../css/edit_gui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGroup_User',2,1,'allocate groups of this admin to user','myGroup -> User','a','','href = "../php/mod_filteredGroup_User.php?sessionID&e_id_css=filteredGroup_User" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Nutzer in eigene Gruppe übernehmen','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','filteredGroup_User','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_Group',2,1,'allocate guis to groups','myGui -> Group','a','','href = "../php/mod_filteredGui_group.php?sessionID&e_id_css=filteredGui_Group" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Eigene Oberfläche externer Gruppe zuordnen','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','filteredGui_Group','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_filteredGroup',2,1,'mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben','mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben','a','','href = "../php/mod_filteredGui_filteredGroup.php?sessionID&e_id_css=filteredGui_filteredGroup" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','mehreren Gruppen Zugriff auf <br>einzelne Anwendung erlauben','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','filteredGui_filteredGroup','file css','../css/administration_alloc.css','file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','filteredGui_user',2,1,'allocate the guis of this admin to a user','myGui -> User','a','','href = "../php/mod_filteredGui_User.php?sessionID&e_id_css=filteredGui_user" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Eigene Oberfläche externem Nutzer zuordnen','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','filteredGui_user','file css','../css/administration_alloc.css','a file css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadWFS',2,1,'load capabilities in a gui','Load WFS','a','','href = "../php/mod_loadWFSCapabilities.php?sessionID" target="AdminFrame"',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Laden','a','','','','','http://www.mapbender.org/index.php/WFS_Konfiguration');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','newGui',2,1,'Anwendung erzeugen','Anwendung erzeugen','a','','href = "../php/mod_newGui.php?sessionID" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none;color: #808080;','Anwendung erzeugen','a','','','','','http://www.mapbender.org/index.php/newGUI');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','owsproxy_wfs',2,1,'secure services','OWSProxy WFS','a','','href="../javascripts/mod_wfs_client.html" ',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Sicherheits - Proxy','a','','','','AdminFrame','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editFilteredUser',2,1,'Benutzer anlegen und editieren','Benutzer anlegen und editieren','a','','href = "../php/mod_editFilteredUser.php?sessionID" target="AdminFrame"',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Benutzer anlegen und editieren','a','','','','','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','editFilteredUser','withPasswordInsertion','true','define if admin can set the new user','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWMS_icon',2,1,'icon','','img','../img/gnome/edit-delete.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editGUI_WMS_icon',2,1,'icon','','img','../img/gnome/preferences-other.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','deleteWMS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteWMS,deleteWMS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','menu_user',2,1,'User admin menu','Admin Nutzer','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','editFilteredUser_collection,editFilteredGroup_collection,filteredGroup_User_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','menu_gui',2,1,'GUI admin menu','Admin Anwendung','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','newGui_collection,rename_copy_gui_collection,delete_filteredGui_collection,editElements_collection,category_filteredGui_collection','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadExtWMS_icon',2,1,'icon','','img','../img/gnome/loadExternalWms.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadExtWMS_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',150,80,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWMSList,loadExtWMS_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','menu_wms',2,1,'WMS admin menu','Admin WMS','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_div_collection.js','','loadWMS_collection,updateWMSs_collection,deleteWMS_collection,editGUI_WMS_collection,owsproxy_collection,monitor_results_collection,wms_metadata_collection,wms_mail_abo_collection,loadExtWMS_collection','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','menu_wms','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadWMSList',2,1,'load capabilities in a gui','WMS in Anwendung einbinden','a','','href = "../php/mod_loadCapabilitiesList.php?sessionID"',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','WMS in Anwendung einbinden','a','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editGUI_WMS',2,1,'WMS Anwendungseinstellungen','WMS Anwendungseinstellungen','a','','href="../php/mod_editGuiWms.php?sessionID" 
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','WMS Anwendungseinstellungen','a','','','','','http://www.mapbender.org/index.php/Edit_GUI_WMS');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','editGUI_WMS','file_css','../css/edit_gui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','updateWMSs',2,1,'Hochgeladene aktualisieren','Update WMS','a','','href="../php/mod_updateWMS.php?sessionID"'' 
',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Hochgeladene aktualisieren','a','','','','','http://www.mapbender.org/index.php/UpdateWMS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','loadWMS',2,1,'Capabilities hochladen','Load WMS','a','','href = "../php/mod_loadCapabilities.php?sessionID"',80,15,NULL ,NULL,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Capabilities hochladen','a','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','category_filteredGUI',2,1,'Anwendung zu Kategorie zuordnen','Anwendung zu Kategorie zuordnen','a','','href = "../php/mod_category_filteredGUI.php?sessionID&e_id_css=filteredUser_filteredGroup"',80,15,190,20,10,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Anwendung zu Kategorie zuordnen','a','','','','','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','category_filteredGUI','cssfile','../css/administration_alloc.css','css file for admin module','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','rename_copy_Gui',2,1,'Anwendung kopieren/umbenennen','Anwendung kopieren/umbenennen','a','','href = "../php/mod_renameGUI.php?sessionID" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Anwendung kopieren/umbenennen','a','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','showLoggedUser',2,1,'Anzeige des eingeloggten Benutzers','','iframe','../php/mod_showLoggedUser.php?sessionID','frameborder="0" scrolling=''no''',1,1,200,30,1,'background-color:lightgrey;','','iframe','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','showLoggedUser','css_file_user_logged','../css/administration_alloc.css','file/css','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editFilteredUser_icon',2,1,'icon','','img','../img/gnome/editMyUser.png','',0,0,NULL ,NULL,2,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','editFilteredUser_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','editFilteredUser,editFilteredUser_icon','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.button.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.draggable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.resizable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.dialog.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','mb_iframepopup',7,1,'iframepopup','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_iframepopup.js','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('Administration_DE','mb_horizontal_accordion',10,1,'Put existing divs in new horizontal accordion div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,0,NULL ,NULL,NULL ,'','<dl></dl>','div','../plugins/mb_horizontal_accordion.js','../../extensions/jqueryEasyAccordion/jquery.easyAccordion.js','menu_wms,menu_wfs,menu_wmc,menu_user,menu_gui,menu_auth','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('Administration_DE','mb_horizontal_accordion','Accordion css file','../extensions/jqueryEasyAccordion/mb_jquery.easyAccordion.css','','file/css');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration_DE','myGUIlist',2,1,'Zurück zur Anwendungsübersicht','Zurück zur Anwendungsübersicht','img','../img/button_blue_red/home_off.png','onClick="mod_home_init()" border=''0'' onmouseover=''this.src="../img/button_blue_red/home_over.png"'' onmouseout=''this.src="../img/button_blue_red/home_off.png"''',200,2,24,24,2,'','','','mod_home.php','','','','http://www.mapbender.org/index.php/MyGUIlist');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration_DE','gui_owner',2,1,'Anwendung editieren Benutzer zuordnen','Anwendung editieren Benutzer zuordnen','a','','href = "../php/mod_gui_owner.php?sessionID" target = "AdminFrame" ',80,15,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Anwendung editieren Benutzer zuordnen','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('Administration_DE', 'gui_owner', 'file css', '../css/administration_alloc.css', 'file css' ,'file/css');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration_DE','gui_owner_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,160,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','gui_owner,gui_owner_icon','','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration_DE','gui_owner_icon',2,1,'icon','','img','../img/gnome/guiMyUser.png','',0,0,NULL ,NULL ,2,'','','','','','','','');

INSERT into gui_mb_user (fkey_gui_id , fkey_mb_user_id , mb_user_type) VALUES ('Administration_DE',1,'owner');

INSERT into gui_gui_category VALUES ('Administration_DE', 1);


------
-- new application admin_wms_metadata
--
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

------
-- new application admin_wfs_metadata
--
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


------
-- new application admin_wmc_metadata
--
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

--
--  update application description for new admin applications
--
UPDATE gui set gui_description = 'Administration for WMS metadata' where gui_id = 'admin_wms_metadata';
UPDATE gui set gui_description = 'Administration for WFS metadata' where gui_id = 'admin_wfs_metadata';
UPDATE gui set gui_description = 'Administration for WMC metadata' where gui_id = 'admin_wmc_metadata';

--
-- change position of some ui element 
--
UPDATE gui_element set e_pos='2' WHERE e_id='jq_ui_position' AND fkey_gui_id IN ('admin_wms_metadata','admin_wmc_metadata','admin_wfs_metadata');
UPDATE gui_element set e_pos='2' WHERE e_id='jq_ui_widget' AND fkey_gui_id IN ('admin_wms_metadata','admin_wmc_metadata','admin_wfs_metadata');
UPDATE gui_element set e_pos='3' WHERE e_id='jq_ui_mouse' AND fkey_gui_id IN ('admin_wms_metadata','admin_wmc_metadata','admin_wfs_metadata');

--
--Put a Map (germany to admin applications)
--WMS germany gui_wms 
--admin_wms_metadata
--
INSERT INTO gui_wms (fkey_gui_id,fkey_wms_id,gui_wms_position,gui_wms_mapformat,
gui_wms_featureinfoformat,gui_wms_exceptionformat,gui_wms_epsg,gui_wms_visible,gui_wms_opacity,gui_wms_sldurl) 
(SELECT 
'admin_wms_metadata', fkey_wms_id, 0, 
gui_wms_mapformat,gui_wms_featureinfoformat,
gui_wms_exceptionformat,gui_wms_epsg,1 as gui_wms_visible,gui_wms_opacity,gui_wms_sldurl
FROM gui_wms WHERE fkey_gui_id = 'gui1' AND fkey_wms_id = 893);
-- WMS germany gui_layer
INSERT INTO gui_layer (fkey_gui_id,fkey_layer_id,gui_layer_wms_id,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,gui_layer_maxscale,
gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title)
(SELECT 
'admin_wms_metadata', 
fkey_layer_id,gui_layer_wms_id ,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,
gui_layer_maxscale,gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title
 FROM gui_layer WHERE fkey_gui_id = 'gui1' AND gui_layer_wms_id = 893);

--
--WMS germany gui_wms 
--admin_wfs_metadata
--
INSERT INTO gui_wms (fkey_gui_id,fkey_wms_id,gui_wms_position,gui_wms_mapformat,
gui_wms_featureinfoformat,gui_wms_exceptionformat,gui_wms_epsg,gui_wms_visible,gui_wms_opacity,gui_wms_sldurl) 
(SELECT 
'admin_wfs_metadata', fkey_wms_id, 0, 
gui_wms_mapformat,gui_wms_featureinfoformat,
gui_wms_exceptionformat,gui_wms_epsg,1 as gui_wms_visible,gui_wms_opacity,gui_wms_sldurl
FROM gui_wms WHERE fkey_gui_id = 'gui1' AND fkey_wms_id = 893);
-- WMS germany gui_layer
INSERT INTO gui_layer (fkey_gui_id,fkey_layer_id,gui_layer_wms_id,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,gui_layer_maxscale,
gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title)
(SELECT 
'admin_wfs_metadata', 
fkey_layer_id,gui_layer_wms_id ,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,
gui_layer_maxscale,gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title
 FROM gui_layer WHERE fkey_gui_id = 'gui1' AND gui_layer_wms_id = 893);

--
--WMS germany gui_wms 
--admin_wmc_metadata
--
INSERT INTO gui_wms (fkey_gui_id,fkey_wms_id,gui_wms_position,gui_wms_mapformat,
gui_wms_featureinfoformat,gui_wms_exceptionformat,gui_wms_epsg,gui_wms_visible,gui_wms_opacity,gui_wms_sldurl) 
(SELECT 
'admin_wmc_metadata', fkey_wms_id, 0, 
gui_wms_mapformat,gui_wms_featureinfoformat,
gui_wms_exceptionformat,gui_wms_epsg,1 as gui_wms_visible,gui_wms_opacity,gui_wms_sldurl
FROM gui_wms WHERE fkey_gui_id = 'gui1' AND fkey_wms_id = 893);
-- WMS germany gui_layer
INSERT INTO gui_layer (fkey_gui_id,fkey_layer_id,gui_layer_wms_id,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,gui_layer_maxscale,
gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title)
(SELECT 
'admin_wmc_metadata', 
fkey_layer_id,gui_layer_wms_id ,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,
gui_layer_maxscale,gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title
 FROM gui_layer WHERE fkey_gui_id = 'gui1' AND gui_layer_wms_id = 893);

--
-- monitoring - wrong column name fixed, column added to mb_wms_availability 
--
ALTER TABLE mb_monitor ADD COLUMN cap_diff text DEFAULT '';
-- old column deleted 
ALTER TABLE mb_monitor DROP COLUMN caps_diff;
ALTER TABLE mb_wms_availability ADD COLUMN cap_diff text DEFAULT '';

--
-- new element delete category for admin2_de, admin2_en and admin1
--
UPDATE gui_element set e_width = 220 where fkey_gui_id IN('admin2_de','admin2_en') AND e_id = 'category_filteredGUI';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin2_de','deleteCategory',2,1,'Anwendungskategorie löschen','Anwendungskategorie löschen','a','','href = "../php/mod_deleteCategory.php?sessionID" target = "AdminFrame" ',8,742,190,20,5,'','Anwendungskategorie löschen','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin2_en','deleteCategory',2,1,'Delete application category','Delete application category','a','','href = "../php/mod_deleteCategory.php?sessionID" target = "AdminFrame" ',8,742,190,20,5,'','Delete application category','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin1','deleteCategory',2,1,'Delete application category','Delete application category','a','','href = "../php/mod_deleteCategory.php?sessionID" target = "AdminFrame" ',8,1065,190,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Delete application category','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');

------
-- Insert deleteCategory into new Administration
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration_DE','deleteCategory',2,1,'Anwendungskategorie löschen','Anwendungskategorie löschen','a','','href = "../php/mod_deleteCategory.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Anwendungskategorie löschen','a','','','','','http://www.mapbender.org/GUI_Category');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration_DE','deleteCategory_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,200,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteCategory,deleteCategory_icon','','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration_DE','deleteCategory_icon',2,1,'icon','','img','../img/gnome/deleteCategories.png','',0,0,NULL ,NULL ,2,'','','','','','','','');

UPDATE gui_element set e_target = e_target || ',deleteCategory_collection'
where fkey_gui_id IN ('Administration_DE', 'Administration') AND
e_id = 'menu_gui' and position('deleteCategory_collection' in e_target) = 0;
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration','deleteCategory',2,1,'delete application category','delete application category','a','','href = "../php/mod_deleteCategory.php?sessionID"',80,15,210,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','delete application category','a','','','','','http://www.mapbender.org/GUI_Category');


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration','deleteCategory_collection',2,1,'Put existing divs in new div object. List the elements comma-separated under target, and make sure they have a title.','','div','','',0,200,200,30,NULL ,'','','div','../plugins/mb_div_collection.js','','deleteCategory,deleteCategory_icon','','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('Administration','deleteCategory_icon',2,1,'icon','','img','../img/gnome/deleteCategories.png','',0,0,NULL ,NULL ,2,'','','','','','','','');

--***** new role system - should not influence the normal behaviour 
--UPDATE mb_user_mb_group set mb_user_mb_group_type = 2 where mb_user_mb_group_type = 1 ;
UPDATE mb_user_mb_group set mb_user_mb_group_type = 1 where mb_user_mb_group_type IS NULL;

-- Table: mb_role

-- DROP TABLE mb_role;

CREATE TABLE mb_role
(
  role_id serial NOT NULL,
  role_name character varying(50),
  role_description character varying(255),
  role_exclude_auth integer NOT NULL DEFAULT 0,
  CONSTRAINT role_id PRIMARY KEY (role_id)
);


--things to be done for mb_user_mb_group table:
--drop old constraint
--Allow to be member in a group with different roles
ALTER TABLE mb_user_mb_group DROP CONSTRAINT pk_fkey_mb_user_mb_group_id;

UPDATE mb_user_mb_group SET mb_user_mb_group_type = 1 WHERE mb_user_mb_group_type IS NULL OR mb_user_mb_group_type = 0;

--default to standard role
ALTER TABLE mb_user_mb_group ALTER COLUMN mb_user_mb_group_type SET DEFAULT 1;

-- Constraint: pk_fkey_mb_user_mb_group_id

-- ALTER TABLE mb_user_mb_group DROP CONSTRAINT pk_fkey_mb_user_mb_group_id;
--create new constraint
ALTER TABLE mb_user_mb_group
  ADD CONSTRAINT pk_fkey_mb_user_mb_group_id PRIMARY KEY(fkey_mb_user_id, fkey_mb_group_id, mb_user_mb_group_type);



--things for the role table
--standard roles:
INSERT INTO mb_role (role_id,role_name,role_description,role_exclude_auth) VALUES (1,'standard role','No special role - old behaviour.',0);

INSERT INTO mb_role (role_id,role_name,role_description,role_exclude_auth) VALUES (2,'primary','Primary group for a mapbender user.',0);

INSERT INTO mb_role (role_id,role_name,role_description,role_exclude_auth) VALUES (3,'metadata editor','Group for which the user can edit and publish metadata.',1);

--constraint for new role system
-- ALTER TABLE mb_user_mb_group DROP CONSTRAINT fkey_mb_user_mb_group_role_id;

ALTER TABLE mb_user_mb_group
  ADD CONSTRAINT fkey_mb_user_mb_group_role_id FOREIGN KEY (mb_user_mb_group_type)
      REFERENCES mb_role (role_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

--link for admin1
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin1','Group_User_Role',2,1,'allocate groups to user and roles','','a','','href = "../php/mod_group_user_role.php?sessionID&e_id_css=Group_User_Role" target = "AdminFrame" ',10,1234,200,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none;color: #808080;','GROUP -> USER -> ROLE','a','','','','AdminFrame','http://www.mapbender.org/index.php/user');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin1', 'Group_User_Role', 'file css', '../css/administration_alloc.css', 'file css' ,'file/css');

--update the group for the decentral registrating institutions
-- View: registrating_groups
CREATE OR REPLACE VIEW registrating_groups AS 
 SELECT f.fkey_mb_group_id, f.fkey_mb_user_id
   FROM mb_user_mb_group f, mb_user_mb_group s
  WHERE f.mb_user_mb_group_type = 2 AND s.fkey_mb_group_id = 36 AND f.fkey_mb_user_id = s.fkey_mb_user_id
  ORDER BY f.fkey_mb_group_id, f.fkey_mb_user_id;
--*****end of role concept****

--
-- Fix for ticket #795
--
UPDATE gui_element SET e_target = 'pan1' WHERE e_id = 'toggleModule';

-- Table: mb_metadata

--DROP TABLE mb_metadata CASCADE;
DROP TABLE content_metadata CASCADE; -- exchanged with md_metadata

CREATE TABLE mb_metadata
(
  metadata_id serial NOT NULL, --mapbender
  uuid character varying(100), --mapbender/orig
  origin character varying(100), --mapbender - capabilities, metador, external
  includeincaps BOOLEAN DEFAULT TRUE, --mapbender include this metadataUrl in new capabilities
  "schema" character varying(32), --mapbender/orig/ see geonetwork - maybe iso19139
  createdate timestamp, --metadata
  changedate timestamp, --metadata
  lastchanged timestamp NOT NULL DEFAULT now(), --mapbender
  data text, --metadata
  link character varying(250), --link from capabilities/registry/metador
  linktype character varying(100), -- from ows caps - metadataUrl type attribute
  md_format character varying(100), -- from ows caps - metadataUrl format tag
  title character varying(250), --metadata
  abstract text, --metadata
  searchtext text, --concatenate search strings
  status character varying(50), --metadator
  "type" character varying(50), --from iso19115 - service/dataset/application ...
  harvestresult integer, --when getting information from caps
  harvestexception text, --if mimetype from link result is not the expected format
  export2csw boolean, -- can be set to show if an added link should be harvested and this metadata should be published again
  tmp_reference_1 timestamp, --metadata
  tmp_reference_2 timestamp, --metadata
  spatial_res_type integer, --metadata
  spatial_res_value character varying(20), --metadata
  ref_system character varying(20), --metadata
  format character varying(100), --metadata
  inspire_charset character varying(10), --metadata
  inspire_top_consistence boolean, --metadata
  fkey_mb_user_id integer NOT NULL DEFAULT 1, -- from metador
  responsible_party integer, --what is this? --fkey_mb_group_id will be better
  individual_name integer, --what is this?
  visibility character varying(12), --from metador
  locked boolean DEFAULT false, --from metador
  copyof character varying(100), --from metador
 -- fkey_metadata_id integer,
 -- fkey_layer_id integer,
 -- fkey_featuretype_id integer,
 -- mb_user_mb_group integer,
 -- mb_user_mb_group_type integer,
  "constraints" text, --metadata/metador?
  fees character varying(2500), --metadata/metador?
  classification character varying(100), --metadata/metador?
  browse_graphic character varying(255), --metadata/metador?
  inspire_conformance boolean, --metadata/metador?
  preview_image text, --metadata/metador?
  the_geom geometry, --metador
  CONSTRAINT enforce_dims_the_geom CHECK (ndims(the_geom) = 2),
  CONSTRAINT enforce_geotype_the_geom CHECK (geometrytype(the_geom) = 'MULTIPOLYGON'::text OR the_geom IS NULL),
  CONSTRAINT enforce_srid_the_geom CHECK (srid(the_geom) = 4326),
  CONSTRAINT metadata_pkey PRIMARY KEY (metadata_id)
--  CONSTRAINT metadata_uuid_key UNIQUE (uuid) -- not used cause this could happen!
);


-- Table: ows_relation_metadata

DROP TABLE ows_relation_metadata CASCADE;

CREATE TABLE ows_relation_metadata
(
  fkey_metadata_id integer NOT NULL,
  fkey_layer_id integer,
  fkey_featuretype_id integer,
  CONSTRAINT ows_relation_metadata_fkey_featuretype_id_fkey FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ows_relation_metadata_fkey_layer_id_fkey FOREIGN KEY (fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ows_relation_metadata_fkey_metadata_id_fkey FOREIGN KEY (fkey_metadata_id)
      REFERENCES mb_metadata (metadata_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE OR REPLACE FUNCTION update_lastchanged_column()
RETURNS TRIGGER AS $$
BEGIN
   NEW.lastchanged = now(); 
   RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_mb_metadata_lastchanged BEFORE UPDATE
    ON mb_metadata FOR EACH ROW EXECUTE PROCEDURE 
    update_lastchanged_column();

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin_wms_metadata','mb_md_showMetadataAddon',2,1,'Show addon editor for metadata','Metadata Addon Editor','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'display:none;','','div','../plugins/mb_metadata_showMetadataAddon.js','','','jq_ui_dialog','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin_wms_metadata', 'mb_md_showMetadataAddon', 'differentFromOriginalCss', '.differentFromOriginal{
background-color:#FFFACD;
}', 'css for class differentFromOriginal' ,'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin_wms_metadata', 'mb_md_showMetadataAddon', 'inputs', '[
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
]', '' ,'var');

-- add column for mb_metadata
ALTER TABLE mb_metadata ADD COLUMN lineage text;
ALTER TABLE mb_metadata ALTER COLUMN spatial_res_type TYPE varchar(20);
ALTER TABLE mb_metadata ADD COLUMN datasetid text; --needed for service data coupling!
ALTER TABLE mb_metadata ADD COLUMN randomid character varying(100); --needed to identify inserted record!
ALTER TABLE mb_metadata ALTER COLUMN randomid SET STORAGE EXTENDED;

-- activate datepicker for forms
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin_wms_metadata','jq_ui_datepicker',5,1,'Datepicker from jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_ui_datepicker.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.datepicker.js','','jq_ui','');
-- activate upload functions
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin_wms_metadata','mb_metadata_xml_import',1,1,'','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_metadata_xml_import.js','','','','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin_wms_metadata','jq_upload',1,1,'Allows to upload files into Mapbender''s temporary files folder','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../plugins/jq_upload.js','','','');


