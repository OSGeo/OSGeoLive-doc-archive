-- new file for db changes to 2.7.3

-- replace gettext to handle ' in french translation
DROP FUNCTION IF EXISTS gettext(text, text);
CREATE OR REPLACE FUNCTION gettext(locale_arg text, string text)
  RETURNS character varying AS
$BODY$
 DECLARE
    msgstr varchar(512);
    trl RECORD;
 BEGIN
    -- RAISE NOTICE '>%<', locale_arg;

    SELECT INTO trl * FROM translations
    WHERE trim(from locale) = trim(from locale_arg) AND msgid = string;
    -- we return the original string, if no translation is found.
    -- this is consistent with gettext's behaviour
    IF NOT FOUND THEN
        RETURN string;
    ELSE
	--RAISE NOTICE '>%<', trl.msgstr;
	msgstr := replace(trl.msgstr,'''','`');
        RETURN msgstr;
    END IF;
 END;
 $BODY$
  LANGUAGE 'plpgsql' VOLATILE;

--
-- update for franch translation to handle '
--
-- 360
UPDATE translations set msgstr = 'Sauvegarder la vue/l''espace de travail en tant que Web Map Context'
where locale = 'fr' and msgid = 'Save workspace as web map context document';

--379
UPDATE translations set msgstr = 'Carte de''aperçu'
where locale = 'fr' and msgid = 'Overview';

-- 383
UPDATE translations set msgstr = 'Sélection de l''échelle'
where locale = 'fr' and msgid = 'Scale Select';


-- 384
UPDATE translations set msgstr = 'Texte de l''échelle'
where locale = 'fr' and msgid = 'Scale Text';


--386
UPDATE translations set msgstr = 'Sélectionner la carte d''arrière-plan'
where locale = 'fr' and msgid = 'Set Background';

-- update all body elements for guis containing wz_jsgraphics.js
---> otherwise new wz_jsgraphics version leads to an error
UPDATE gui_element set e_mb_mod = 
CASE 
WHEN e_mb_mod IS NULL 
THEN '../extensions/RaphaelJS/raphael-1.4.7.min.js' 
ELSE
	CASE 
	WHEN e_mb_mod LIKE '%../extensions/RaphaelJS/raphael-1.4.7.min.js%'
	THEN e_mb_mod
	ELSE  e_mb_mod::text ||',../extensions/RaphaelJS/raphael-1.4.7.min.js' 
	END
END 
where e_id = 'body' and fkey_gui_id IN (SELECT DISTINCT fkey_gui_id FROM gui_element where e_js_file LIKE '%wz_jsgraphics%' OR e_mb_mod LIKE '%wz_jsgraphics%');

--update all gui elements using jquery-ui version 1.8.1 to 1.8.16
update gui_element set e_mb_mod = replace(e_mb_mod, 'jquery-ui-1.8.1.custom', 'jquery-ui-1.8.16.custom') where e_mb_mod LIKE '%jquery-ui-1.8.1.custom%';
--update all gui elements using jquery-ui version 1.8.14 to 1.8.16
update gui_element set e_mb_mod = replace(e_mb_mod, 'jquery-ui-1.8.14.custom', 'jquery-ui-1.8.16.custom') where e_mb_mod LIKE '%jquery-ui-1.8.14.custom%';
--update all gui elements using jquery-ui version 1.7.2 to 1.8.16
update gui_element set e_mb_mod = replace(e_mb_mod, 'jquery-ui-1.7.2.custom', 'jquery-ui-1.8.16.custom') where e_mb_mod LIKE '%jquery-ui-1.7.2.custom%';
--update gui element printPDF -> new file for jquery bgiframe js
update gui_element set e_mb_mod = replace(e_mb_mod, '/external/bgiframe/jquery.bgiframe.min.js', '/external/jquery.bgiframe-2.1.2.js') where e_mb_mod LIKE '%jquery.bgiframe.min.js%';
update gui_element set e_mb_mod = replace(e_mb_mod, '/external/bgiframe/jquery.bgiframe.js', '/external/jquery.bgiframe-2.1.2.js') where e_mb_mod LIKE '%jquery.bgiframe.js%';

-- new element_var pointStrokeDefault for element measure_widget
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'measure_widget', 'pointStrokeDefault', '#FF0000', '' ,'var'
from gui_element
WHERE
gui_element.e_id = 'measure_widget' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'measure_widget' AND var_name = 'pointStrokeDefault');

-- new element_var pointStrokeSnapped for element measure_widget
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'measure_widget', 'pointStrokeSnapped', '#FF0000', '' ,'var'
from gui_element
WHERE
gui_element.e_id = 'measure_widget' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'measure_widget' AND var_name = 'pointStrokeSnapped');

-- new element_var pointStrokeWidthDefault for element measure_widget
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'measure_widget', 'pointStrokeWidthDefault', '2', '' ,'var'
from gui_element
WHERE
gui_element.e_id = 'measure_widget' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'measure_widget' AND var_name = 'pointStrokeWidthDefault');

--get featureInfoTunnel ready for working with toggleModule
UPDATE gui_element SET e_attributes = NULL WHERE e_id = 'featureInfoTunnel';

-- Function: f_count_layer_couplings(integer)

DROP FUNCTION IF EXISTS f_count_layer_couplings(integer);
CREATE OR REPLACE FUNCTION f_count_layer_couplings(integer)
  RETURNS integer AS
$BODY$
DECLARE
   layer_rel int4;
BEGIN
layer_rel := count(*) from ows_relation_metadata WHERE fkey_layer_id=$1;
RETURN layer_rel;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE;

-- Function: f_collect_searchtext(integer, integer)

-- DROP FUNCTION f_collect_searchtext(integer, integer);

CREATE OR REPLACE FUNCTION f_collect_searchtext(integer, integer)
  RETURNS text AS
$BODY$DECLARE
    p_wms_id ALIAS FOR $1;
    p_layer_id ALIAS FOR $2;
    
    r_keywords RECORD;
    l_result TEXT;
BEGIN
    l_result := '';
    l_result := l_result || (SELECT COALESCE(wms_title, '') || ' ' || COALESCE(wms_abstract, '')  || ' ' || wms_id::text FROM wms WHERE wms_id = p_wms_id);
    l_result := l_result || (SELECT COALESCE(layer_name, '')|| ' ' || COALESCE(layer_title, '')  || ' ' || COALESCE(layer_abstract, '') || ' ' || layer_id::text FROM layer WHERE layer_id = p_layer_id);
    FOR r_keywords IN SELECT DISTINCT keyword FROM
        (SELECT keyword FROM layer_keyword L JOIN keyword K ON (K.keyword_id = L.fkey_keyword_id AND L.fkey_layer_id = p_layer_id)
        ) AS __keywords__ LOOP
        l_result := l_result || ' ' || COALESCE(r_keywords.keyword, '');
    END LOOP;
    FOR r_keywords IN SELECT DISTINCT md_topic_category_code_de FROM
        (SELECT md_topic_category_code_de FROM md_topic_category T JOIN layer_md_topic_category C ON (C.fkey_md_topic_category_id = T.md_topic_category_id AND C.fkey_layer_id = p_layer_id)
        ) AS __keywords__ LOOP
        l_result := l_result || ' ' || COALESCE(r_keywords.md_topic_category_code_de, '');
    END LOOP;
   l_result := UPPER(l_result);
   l_result := replace(replace(replace(replace(replace(replace(replace(l_result,'Ä','AE'),'ß','SS'),'Ö','OE'),'Ü','UE'),'ä','AE'),'ü','UE'),'ö','OE');

    RETURN l_result;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE STRICT
  COST 100;

-- Fix error of missing wmc_serial_id

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
        (SELECT keyword FROM wmc_keyword L JOIN keyword K ON (K.keyword_id = L.fkey_keyword_id AND L.fkey_wmc_serial_id = p_wmc_id)
        ) AS __keywords__ LOOP
        l_result := l_result || ' ' || COALESCE(r_keywords.keyword, '');
    END LOOP;
   l_result := UPPER(l_result);
   l_result := replace(replace(replace(replace(replace(replace(replace(l_result,'Ä','AE'),'ß','SS'),'Ö','OE'),'Ü','UE'),'ä','AE'),'ü','UE'),'ö','OE');

    RETURN l_result;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

-- Fix: wrong tooltip for Legend-button
UPDATE gui_element SET e_title = 'Legend' WHERE e_id = 'legendButton' AND fkey_gui_id = 'template_print';

-- new element_var reverseLegend for element legend
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'legend', 'reverseLegend', 'false', 'parameter to decide wether the legend should be in the reverse direction' ,'var'
from gui_element
WHERE
gui_element.e_id = 'legend' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'legend' AND var_name = 'reverseLegend');

--
-- add WMS_preferences as dialog to gui1
--
DELETE FROM gui_element WHERE e_id = 'WMS_preferences' AND fkey_gui_id = 'gui1';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','WMS_preferencesButton',2,1,'button for configure the preferences of each loaded wms','Manage WMS preferences','img','../img/button_blink_red/preferences_off.png','',670,60,24,24,1,'','','','../plugins/mb_button.js','','WMS_preferencesDiv','','http://www.mapbender.org/index.php/WMS_preferences');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'WMS_preferencesButton', 'dialogWidth', '400', '' ,'var');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','WMS_preferencesDiv',12,1,'Configure WMS preferences - div tag','WMS preferences','div','','',870,60,400,500,NULL ,'z-index:9999;','','div','../plugins/mod_WMSpreferencesDiv.php','','mapframe1','jq_ui_dialog','http://www.mapbender.org/index.php/mod_WMSpreferencesDiv');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'WMS_preferencesDiv', 'overwrite', '0', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'WMS_preferencesDiv', 'saveInSession', '0', '' ,'var');


--
-- new gazetteer jsonAutocompleteGazetteer
-- 
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','jq_ui_autocomplete',5,1,'Module to manage jQuery UI autocomplete module','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.autocomplete.js','','jq_ui,jq_ui_widget,jq_ui_position','http://jqueryui.com/demos/autocomplete/');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','jsonAutocompleteGazetteer',12,1,'Client for json webservices like geonames.org','Gazetteer','div','','',250,110,NULL ,NULL ,999,'','','div','../plugins/mod_jsonAutocompleteGazetteer.php','','mapframe1','','http://www.mapbender.org/index.php/mod_jsonAutocompleteGazetteer');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'jsonAutocompleteGazetteer', 'gazetteerUrl', 'http://ws.geonames.org/searchJSON?lang=de&',
  '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'jsonAutocompleteGazetteer', 'isGeonames', 'true', '' ,'var');


--
-- change datatype from 50 to 256 to handle longer style names
--
ALTER TABLE layer_style ALTER COLUMN name TYPE character varying(256);
ALTER TABLE gui_layer ALTER COLUMN gui_layer_style TYPE character varying(256);
ALTER TABLE keyword ALTER COLUMN keyword TYPE text;

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','jq_ui_slider',5,1,'slider from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.16.custom/development-bundle/ui/minified/jquery.ui.slider.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','resultList_Highlight',2,1,'highlighting functionality for resultList','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/mb_resultList_Highlight.js','','resultList,mapframe1,overview','','http://www.mapbender.org/resultList_Highlight');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'resultList_Highlight', 'maxHighlightedPoints', '500', 'max number of points to highlight' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'resultList_Highlight', 'resultHighlightColor', '#ff0000', 'color of the highlighting' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'resultList_Highlight', 'resultHighlightLineWidth', '2', 'width of the highlighting line' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'resultList_Highlight', 'resultHighlightZIndex', '100', 'zindex of the highlighting' ,'var');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','resultList_Zoom',2,1,'zoom functionality for resultList','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/mb_resultList_Zoom.js','','resultList,mapframe1','','http://www.mapbender.org/resultList_Zoom');


INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','resultList',2,1,'','Result List','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','mod_ResultList.js','../../lib/resultGeometryListController.js, ../../lib/resultGeometryListModel.js','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'resultList', 'position', '[600,50]', 'position of the result list dialog' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'resultList', 'resultListHeight', '400', 'height of the result list dialog' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'resultList', 'resultListTitle', 'Search results', 'title of the result list dialog' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'resultList', 'resultListWidth', '600', 'width of the result list dialog' ,'var');

UPDATE gui_element SET e_js_file = '../extensions/wz_jsgraphics.js,geometry.js,../extensions/RaphaelJS/raphael-1.4.7.min.js,../extensions/RaphaelJS/raphael-1.4.7.min.js,../extensions/RaphaelJS/raphael-1.4.7.min.js,../extensions/RaphaelJS/raphael-1.4.7.min.js' where fkey_gui_id = 'gui1' AND e_id = 'body';

--
-- replace server name wms1.ccgis.de to wms.wheregroup.com 
--
Update wms set wms_getcapabilities = replace(wms_getcapabilities,'wms1.ccgis.de','wms.wheregroup.com');
Update wms set wms_getmap = replace(wms_getmap,'wms1.ccgis.de','wms.wheregroup.com');
Update wms set wms_getfeatureinfo = replace(wms_getfeatureinfo,'wms1.ccgis.de','wms.wheregroup.com');
Update wms set wms_getlegendurl = replace(wms_getlegendurl,'wms1.ccgis.de','wms.wheregroup.com');

Update layer_style set legendurl = replace(legendurl,'wms1.ccgis.de','wms.wheregroup.com');

Update wfs set wfs_getcapabilities = replace(wfs_getcapabilities,'wms1.ccgis.de','wms.wheregroup.com');
Update wfs set wfs_describefeaturetype = replace(wfs_describefeaturetype,'wms1.ccgis.de','wms.wheregroup.com');
Update wfs set wfs_getfeature = replace(wfs_getfeature,'wms1.ccgis.de','wms.wheregroup.com');
Update wfs set wfs_transaction = replace(wfs_transaction,'wms1.ccgis.de','wms.wheregroup.com');


-- Fix: Ticket #870, http://trac.osgeo.org/mapbender/ticket/870
UPDATE gui_element set e_attributes='frameborder="1" onmouseover="this.style.zIndex=300;this.style.width=''350px'';" onmouseout="this.style.zIndex=3;this.style.width=''200px'';"' where fkey_gui_id='gui' and e_id='legend';

--
-- legend: add new element vars enlargetreewidth and enlargetreewidthopacity to gui
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'treeGDE', 'enlargetreewidth', '300', 'false (default): no enlargement of the div, integer value to enlarge on mouseover' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'treeGDE', 'enlargetreewidthopacity', 'true', '' ,'var');

UPDATE gui_element set e_width = 220 where fkey_gui_id = 'gui_digitize' and e_id = 'treeGDE';
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'treeGDE', 'enlargetreewidth', '300', 'false (default): no enlargement of the div, integer value to enlarge on mouseover' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'treeGDE', 'enlargetreewidthopacity', 'true', '' ,'var');

INSERT INTO wfs_featuretype_namespace VALUES (1,4,'topp','http://www.openplans.org/topp');

--
-- merged SQl from trunk
--
-- Function: f_normalize_load_count(text,integer)

--DROP FUNCTION f_normalize_load_count(text,integer);

CREATE OR REPLACE FUNCTION f_normalize_load_count(tablename text, newmaxvalue integer)
  RETURNS void AS
$BODY$
DECLARE
   tablename ALIAS FOR $1;
   newmaxvalue ALIAS FOR $2;
   oldmaxvalue integer;
   querytext text;
   result record;
BEGIN
   querytext:= 'SELECT max(load_count) FROM ' || lower(tablename);
   EXECUTE querytext INTO oldmaxvalue;
   querytext:= 'UPDATE ' || lower(tablename) || ' SET load_count = ' || 'floor((load_count::real/' || oldmaxvalue ||  ')*' || newmaxvalue || ')';
   EXECUTE querytext;
RETURN;--querytext;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

UPDATE translations SET msgstr = 'Kartenprojektion ändern' WHERE msgstr = 'Karenprojektion ändern';

ALTER TABLE gui_wms ADD COLUMN gui_wms_parent_gui character varying(50);

ALTER TABLE gui_wms ADD CONSTRAINT gui_wms_ibfk_5 FOREIGN KEY (gui_wms_parent_gui) REFERENCES gui (gui_id) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE; -- delete wms from gui, if original gui will be deleted!


ALTER TABLE gui_wms ADD COLUMN gui_wms_timestamp TIMESTAMP WITHOUT TIME ZONE;
UPDATE gui_wms SET gui_wms_timestamp = to_timestamp('1970-01-01','YYYY-MM-DD') WHERE gui_wms_timestamp IS NULL;
ALTER TABLE gui_wms ALTER COLUMN gui_wms_timestamp SET NOT NULL;
ALTER TABLE gui_wms ALTER COLUMN gui_wms_timestamp SET DEFAULT now();

-- Table: scheduler - is used to schedule ows updates

-- DROP TABLE scheduler;

CREATE TABLE scheduler
(
  scheduler_id serial NOT NULL,
  scheduler_type character varying(50) NOT NULL DEFAULT ''::character varying,
  fkey_wms_id integer,
  fkey_wfs_id integer,
  fkey_dataurl_id integer,
  scheduler_interval interval,
  scheduler_mail integer,
  scheduler_publish integer,
  scheduler_searchable integer,
  scheduler_create timestamp without time zone,
  scheduler_change timestamp without time zone,
  scheduler_status integer,
  scheduler_status_error_message text,
  scheduler_overwrite integer,
  CONSTRAINT pk_scheduler_id PRIMARY KEY (scheduler_id),
  CONSTRAINT scheduler_fkey_wfs_id_fkey FOREIGN KEY (fkey_wfs_id)
      REFERENCES wfs (wfs_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT scheduler_fkey_wms_id_fkey FOREIGN KEY (fkey_wms_id)
      REFERENCES wms (wms_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


-- Table: ows_relation_data

-- DROP TABLE ows_relation_data;

CREATE TABLE ows_relation_data
(
  fkey_datalink_id integer NOT NULL,
  fkey_layer_id integer,
  CONSTRAINT ows_relation_data_fkey_layer_id_fkey FOREIGN KEY (fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ows_relation_data_fkey_data_id_fkey FOREIGN KEY (fkey_datalink_id)
      REFERENCES datalink (datalink_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Column: datalink_format

-- ALTER TABLE datalink DROP COLUMN datalink_format;

ALTER TABLE datalink ADD COLUMN datalink_format character varying(50);
ALTER TABLE datalink ALTER COLUMN datalink_format SET NOT NULL;
ALTER TABLE datalink ALTER COLUMN datalink_format SET DEFAULT ''::character varying;

-- Column: datalink_origin

-- ALTER TABLE datalink DROP COLUMN datalink_origin;

ALTER TABLE datalink ADD COLUMN datalink_origin character varying(50);
ALTER TABLE datalink ALTER COLUMN datalink_origin SET NOT NULL;
ALTER TABLE datalink ALTER COLUMN datalink_origin SET DEFAULT ''::character varying;

-- Column: randomid

-- ALTER TABLE datalink DROP COLUMN randomid;

ALTER TABLE datalink ADD COLUMN randomid character varying(100);

ALTER TABLE datalink DROP COLUMN datalink_timestamp;

ALTER TABLE datalink DROP COLUMN datalink_timestamp_create;

ALTER TABLE datalink DROP COLUMN datalink_timestamp_last_usage;

ALTER TABLE datalink ADD COLUMN datalink_timestamp TIMESTAMP WITHOUT TIME ZONE;

ALTER TABLE datalink ADD COLUMN datalink_timestamp_create TIMESTAMP WITHOUT TIME ZONE;

ALTER TABLE datalink ADD COLUMN datalink_timestamp_last_usage TIMESTAMP WITHOUT TIME ZONE;
 
-- Function: f_count_featuretype_couplings(integer)

-- DROP FUNCTION f_count_featuretype_couplings(integer);

CREATE OR REPLACE FUNCTION f_count_featuretype_couplings(integer) RETURNS integer AS 
$BODY$
DECLARE
   featuretype_rel int4;
BEGIN
featuretype_rel := count(*) from ows_relation_metadata WHERE fkey_featuretype_id=$1;
RETURN featuretype_rel;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;


-- Function: f_collect_inspire_cat_wfs_featuretype(integer)
-- DROP FUNCTION f_collect_inspire_cat_wfs_featuretype(integer);

CREATE OR REPLACE FUNCTION f_collect_inspire_cat_wfs_featuretype(integer)
  RETURNS text AS
$BODY$DECLARE
  i_wfs_featuretype_id ALIAS FOR $1;
  inspire_cat_string  TEXT;
  inspire_cat_record  RECORD;

BEGIN
inspire_cat_string := '';

FOR inspire_cat_record IN SELECT wfs_featuretype_inspire_category.fkey_inspire_category_id from wfs_featuretype_inspire_category WHERE wfs_featuretype_inspire_category.fkey_featuretype_id=$1  LOOP
inspire_cat_string := inspire_cat_string || '{' ||inspire_cat_record.fkey_inspire_category_id || '}';
END LOOP ;
  
RETURN inspire_cat_string;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE STRICT
  COST 100;

ALTER TABLE ows_relation_metadata ADD COLUMN internal INTEGER;

--new table for wfs featuretype supported crs - new up from wfs 1.1.0 - the bbox are normally not filled!

CREATE TABLE wfs_featuretype_epsg
(
  fkey_featuretype_id integer NOT NULL DEFAULT 0,
  epsg character varying(50) NOT NULL DEFAULT ''::character varying,
  minx double precision DEFAULT 0,
  miny double precision DEFAULT 0,
  maxx double precision DEFAULT 0,
  maxy double precision DEFAULT 0,
  CONSTRAINT wfs_featuretype_epsg_ibfk_1 FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Column: featuretype_latlon_bbox
-- ALTER TABLE wfs_featuretype DROP COLUMN featuretype_latlon_bbox;

ALTER TABLE wfs_featuretype ADD COLUMN featuretype_latlon_bbox character varying;

ALTER TABLE datalink RENAME column randomid TO datalink_randomid;

-- Fix: Ticket #870, http://trac.osgeo.org/mapbender/ticket/870
UPDATE gui_element set e_attributes='frameborder="1" onmouseover="this.style.zIndex=300;this.style.width=''350px'';" onmouseout="this.style.zIndex=3;this.style.width=''200px'';"' where fkey_gui_id='gui' and e_id='legend';

-- add possibility to define if inspire download should be possible for wms layers and wfs featuretypes 
ALTER TABLE wfs_featuretype ADD COLUMN inspire_download integer;
UPDATE wfs_featuretype SET inspire_download = 0 WHERE inspire_download IS NULL;

ALTER TABLE layer ADD COLUMN inspire_download integer;
UPDATE layer SET inspire_download = 0 WHERE inspire_download IS NULL;

ALTER TABLE mb_monitor ALTER COLUMN map_url TYPE varchar;
ALTER TABLE mb_monitor ALTER COLUMN cap_diff TYPE text; 
--Add handling of codespaces as demanded from the INSPIRE regulation
ALTER TABLE mb_metadata ADD COLUMN datasetid_codespace TEXT;

ALTER TABLE ows_relation_metadata ADD COLUMN relation_type TEXT;
--update table ows_relation_metadata set type from table mb_metadata -- it is better to have it for relation not for instance - this is only a initial filling, afterwards the code handles the updates and inserts automatical
UPDATE ows_relation_metadata SET relation_type = origin FROM  mb_metadata WHERE ows_relation_metadata.fkey_metadata_id = mb_metadata.metadata_id AND ows_relation_metadata.relation_type IS NULL;
--allow to decide if update of wms metadata will be published via twitter/rss
UPDATE gui_element set e_content = '<span style=''float:right''><input type=''checkbox'' id=''twitter_news''>Publish via Twitter<input type=''checkbox'' id=''rss_news''>Publish via RSS</input><input disabled="disabled" type=''button'' value=''Preview metadata''><input disabled="disabled" type=''submit'' value=''Save metadata''></span>' WHERE fkey_gui_id = 'admin_wms_metadata' and e_id = 'mb_md_submit';


