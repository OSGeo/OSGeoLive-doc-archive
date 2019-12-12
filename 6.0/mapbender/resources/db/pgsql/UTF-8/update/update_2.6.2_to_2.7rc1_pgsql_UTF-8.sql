-- remove event handlers, are now in the script
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'selArea1';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'pan1';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'featureInfo1';
UPDATE gui_element SET e_content = '' WHERE e_id = 'navFrame';


--
-- new definition of addWMSfromfilteredList_ajax due to i18n
UPDATE gui_element set e_attributes = '', e_js_file = 'mod_addWmsFromFilteredList_button.php' where e_id = 'addWMSfromfilteredList_ajax';


--
-- polish entries for translations table, delete old ones first to avoid multi entries for one locale

DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Pan';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Pan', 'Przesuń');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Display complete map';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Display complete map', 'Pokaż calą mapę');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Zoom in';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Zoom in', 'Powiększ');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Zoom out';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Zoom out', 'Pomniejsz');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Back';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Back', 'Wróć');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Forward';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Forward', 'Do przodu');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Coordinates';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Coordinates', 'Współrzędne');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Zoom by rectangle';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Zoom by rectangle', 'Wybierz fragment mapy');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Redraw';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Redraw', 'Załaduj ponownie');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Query';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Query', 'Szukaj danych');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Logout';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Logout', 'Wymelduj');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'WMS preferences';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'WMS preferences', 'Ustawienia WMS');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Adding WMS from filtered list';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Adding WMS from filtered list', 'Dodaj WMS z listy');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Set map center';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Set map center', 'Zaznacz środek mapy');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Help';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Help', 'Pomoc');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Show WMS infos';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Show WMS infos', 'Informacje WMS');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Save workspace as web map context document';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Save workspace as web map context document', 'Zapisz widok jako web map context dokument');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Resize Mapsize';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Resize Mapsize', 'Zmień rozmiar mapy');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Rubber';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Rubber', 'Usuń szkic');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Get Area';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Get Area', 'Oblicz powierzchnię');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Close Polygon';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Close Polygon', 'Zamknij poligon');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Move back to your GUI list';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Move back to your GUI list', 'Z powrotem do listy GUI');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Legend';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Legend', 'Legenda');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Print';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Print', 'Drukuj');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Imprint';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Imprint', 'Imprint');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Maps';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Maps', 'Mapy');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Search';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Search', 'Szukaj');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Meetingpoint';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Meetingpoint', 'Miejsce spotkań');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Metadatasearch';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Metadatasearch', 'Wyszukiwanie metadanych');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Adding WMS';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Adding WMS', 'Dodaj WMS');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Adding WMS from List';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Adding WMS from List', 'Dodaj WMS z listy');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Info';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Info', 'Informacja');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Change Projection';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Change Projection', 'Zmień układ współrzędnych');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Copyright';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Copyright', 'Copyright');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Digitize';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Digitize', 'Dygitalizacja');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Overview';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Overview', 'Mapa przeglądowa');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Drag Mapsize';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Drag Mapsize', 'Powiększ');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Mapframe';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Mapframe', 'Okno mapy');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Navigation Frame';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Navigation Frame', 'Pasek narzędzi');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Scale Select';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Scale Select', 'Wybierz skalę');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Scale Text';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Scale Text', 'Wpisz skalę');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Scalebar';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Scalebar', 'Podziałka');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Set Background';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Set Background', 'Wybierz mapę tematyczną jako tło');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Zoom to Coordinates';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Zoom to Coordinates', 'Powiększ według współrzędnych');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Change Password';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Change Password', 'Zmień hasło');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Load a web map context document';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Load a web map context document', 'Załaduj web map context dokument');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Logo';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Logo', 'Logo');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Measure distance';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Measure distance', 'Zmierz odległość');
DELETE FROM translations WHERE locale = 'pl' AND msgid = 'Set language';
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Set language', 'Wybierz język');


--
-- update wfs conf module
UPDATE gui_element SET
e_attributes = 'href = "../php/mod_wfs_conf_client.php" target="AdminFrame"'
WHERE e_id = 'wfs_conf' AND fkey_gui_id IN ('admin1', 'admin_de_services', 'admin_en_services');

ALTER TABLE wfs_conf ADD COLUMN
wfs_conf_type int4 NOT NULL DEFAULT 0;
--
-- new wfs conf columns
ALTER TABLE wfs_conf_element ADD COLUMN
f_helptext text;

ALTER TABLE wfs_conf_element ADD COLUMN
f_category_name varchar(255) NOT NULL DEFAULT '';

-- reload uses Mapbender API
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'reload';

-- init event is now triggered by jQuery
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'body';

-- mapframe layers are now added dynamically by the modules
UPDATE gui_element SET e_content = '' WHERE e_id = 'mapframe1';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_log.js' WHERE e_id = 'log';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_resize_mapsize.js' WHERE e_id = 'resizeMapsize';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_sandclock.js' WHERE e_id = 'sandclock';

-- init event is now triggered by jQuery
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'zoomFull';

-- file extension is now .js
UPDATE gui_element SET e_attributes = '', e_js_file = 'mod_zoomFull.js' WHERE e_id = 'zoomFull';

-- file extension is now .js
UPDATE gui_element SET e_attributes = '', e_js_file = 'mod_repaint.js' WHERE e_id = 'repaint';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_changeEPSG.js' WHERE e_id = 'changeEPSG';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_scalebar.js' WHERE e_id = 'scalebar';

-- parameter: skip WMS request if current SRS is not supported
-- I'm not sure how to insert this dynamically into all applications that have either mapframe1 or overview
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'mapframe1', 'skipWmsIfSrsNotSupported', '0', 'if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility' ,'var'
FROM gui_element WHERE gui_element.e_id = 'mapframe1' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'skipWmsIfSrsNotSupported' AND fkey_e_id = 'mapframe1');

-- element var skipWmsIfSrsNotSupported for gui element overview
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'overview', 'skipWmsIfSrsNotSupported', '0', 'if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility' ,'var'
FROM gui_element WHERE gui_element.e_id = 'overview' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'skipWmsIfSrsNotSupported'
AND fkey_e_id = 'overview');

---------------------------------------------MONITORING BEGIN
-- Index: idx_mb_monitor_status

-- DROP INDEX idx_mb_monitor_status;

CREATE INDEX idx_mb_monitor_status
  ON mb_monitor
  USING btree
  (status);

-- Index: idx_mb_monitor_upload_id

-- DROP INDEX idx_mb_monitor_upload_id;

CREATE INDEX idx_mb_monitor_upload_id
  ON mb_monitor
  USING btree
  (upload_id);


-- Table: mb_wms_availability

-- DROP TABLE mb_wms_availability;

CREATE TABLE mb_wms_availability
(
  fkey_wms_id integer,
  fkey_upload_id character varying,
  last_status integer,
  availability real,
  image integer,
  status_comment character varying,
  average_resp_time real,
  upload_url character varying,
  map_url character varying,
  CONSTRAINT mb_wms_availability_fkey_wms_id_wms_id FOREIGN KEY (fkey_wms_id)
      REFERENCES wms (wms_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Function: mb_monitor_after()

-- DROP FUNCTION mb_monitor_after();

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

      UPDATE mb_wms_availability SET average_resp_time=average_res_cap,last_status=NEW.status, availability=availability_new, image=NEW.image, status_comment=NEW.status_comment,upload_url=NEW.upload_url,map_url=NEW.map_url WHERE mb_wms_availability.fkey_wms_id=NEW.fkey_wms_id;
      RETURN NEW;
     END IF;
     IF TG_OP = 'INSERT' THEN

	IF (select count(fkey_wms_id) from mb_wms_availability where fkey_wms_id=NEW.fkey_wms_id) > 0  then -- service is not new
			UPDATE mb_wms_availability set fkey_upload_id=NEW.upload_id,last_status=NEW.status,status_comment=NEW.status_comment,upload_url=NEW.upload_url where fkey_wms_id=NEW.fkey_wms_id;
		else --service has not yet been monitored
			INSERT INTO mb_wms_availability (fkey_upload_id,fkey_wms_id,last_status,status_comment,upload_url,map_url,average_resp_time,availability) VALUES (NEW.upload_id,NEW.fkey_wms_id,NEW.status,NEW.status_comment,NEW.upload_url::text,NEW.map_url,0,100);
		end if;

      RETURN NEW;
     END IF;
    END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;


-- Trigger: mb_monitor_after on mb_monitor

-- DROP TRIGGER mb_monitor_after ON mb_monitor;

CREATE TRIGGER mb_monitor_after
  AFTER INSERT OR UPDATE
  ON mb_monitor
  FOR EACH ROW
  EXECUTE PROCEDURE mb_monitor_after();


-- Table: mb_user_abo_ows

-- DROP TABLE mb_user_abo_ows;

CREATE TABLE mb_user_abo_ows
(
  fkey_mb_user_id integer,
  fkey_wms_id integer,
  fkey_wfs_id integer,
  CONSTRAINT mb_user_abo_ows_user_id_fkey FOREIGN KEY (fkey_mb_user_id)
      REFERENCES mb_user (mb_user_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT mb_user_abo_ows_wfs_fkey FOREIGN KEY (fkey_wfs_id)
      REFERENCES wfs (wfs_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT mb_user_abo_ows_wms_fkey FOREIGN KEY (fkey_wms_id)
      REFERENCES wms (wms_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


-- Index: idx_wms_id

-- DROP INDEX idx_wms_id;

CREATE INDEX idx_wms_id
  ON wms
  USING btree
  (wms_id);



-- add monitor subscriber notification to admin1
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'monitor_abo_show',2,1,'monitoring','Show subscriptions','a','','href = "../php/mod_abo_show.php?sessionID" target = "AdminFrame" ',8,1080,190,20,10,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Manage monitoring subscriptions','a','','','','AdminFrame','http://www.mapbender.org/'
FROM gui WHERE gui.gui_id = 'admin1' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'monitor_abo_show' AND fkey_gui_id = 'admin1');

-- add monitor results to admin1
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'monitor_results',2,1,'monitoring results','Monitoring results','a','','href = "../php/mod_monitorCapabilities_read.php?sessionID" target = "AdminFrame" ',8,1110,190,20,10,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','View monitoring results','a','','','','AdminFrame','http://www.mapbender.org/'
FROM gui WHERE gui.gui_id = 'admin1' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'monitor_results');

---------------------------------------------MONITORING END

---------------------------
-- OWS Proxy log
CREATE TABLE mb_proxy_log (

    proxy_log_timestamp timestamp default now(),
    fkey_wms_id integer NOT NULL,
    fkey_mb_user_id integer NOT NULL,
    request varchar(4096),
    pixel bigint,
    price real

);
ALTER TABLE wms ADD COLUMN wms_proxylog integer;
ALTER TABLE wms ALTER COLUMN wms_proxylog SET STORAGE PLAIN;
ALTER TABLE wms ADD COLUMN wms_pricevolume integer;
ALTER TABLE wms ALTER COLUMN wms_pricevolume SET STORAGE PLAIN;
---------------------------

---------------------------
-- http auth
ALTER TABLE wms ADD COLUMN wms_username VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE wms ADD COLUMN wms_password VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE wms ADD COLUMN wms_auth_type VARCHAR(255) NOT NULL DEFAULT '';

-- Column: mb_user_digest
-- ALTER TABLE mb_user DROP COLUMN mb_user_digest;
ALTER TABLE mb_user ADD COLUMN mb_user_digest text;
ALTER TABLE mb_user ALTER COLUMN mb_user_digest SET STORAGE EXTENDED;

--Initial filling with empty strings
UPDATE mb_user set mb_user_digest='';

--howto set up the http_auth digest hash
--update mb_user set mb_user_digest=md5(mb_user_name || ';' || mb_user_email || ':' || '<realm_name>' || ':' || 'password') where mb_user_id = <ID>;

---------------------------
-- http://www.mapbender.org/FeatureInfo#considerScalehints --
-- INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'featureInfo1', 'considerScalehints', '0', '' ,'var');
-- INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'featureInfo1', 'considerScalehints', '0', '' ,'var');
-- INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'featureInfo1', 'considerScalehints', '0', '' ,'var');
-- INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'featureInfo1', 'considerScalehints', '0', '' ,'var');


------------------------------
-- greek updates for translations table
update translations set msgstr = 'Περίγραμμα/Κορνίζα πλοήγησης' where msgid = 'Navigation Frame' and locale = 'gr';
update translations set msgstr = 'Φόρτωση κειμένου διαδυκτιακού χάρτη' where msgid = 'Load a web map context document' and locale = 'gr';
update translations set msgstr = 'Αποθήκευση χώρου εργασίας με μορφή κείμενου διαδυκτιακού χάρτη' where msgid = 'Save workspace as web map context document' and locale = 'gr';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_selArea.js' WHERE e_id = 'selArea1';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_zoomIn1.js' WHERE e_id = 'zoomIn1';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_overview.js' WHERE e_id = 'overview';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_zoomOut1.js' WHERE e_id = 'zoomOut1';

-- set white background to Mapframe1
UPDATE gui_element set e_more_styles='overflow:hidden;background-color:#ffffff' where e_id='mapframe1';

-- background to overview
UPDATE gui_element set e_more_styles='overflow:hidden;background-color:#ffffff' where e_id='overview';

-- file extension is now .js
UPDATE gui_element SET e_js_file = 'mod_pan.js' WHERE e_id = 'pan1';

-- remove title tag of Mapframe1 because it's displayed  im Map
UPDATE gui_element SET e_title='' WHERE e_id='mapframe1';

-- remove title tag of Mapframe1 because it's displayed  im Map
UPDATE gui_element SET e_title='' WHERE e_id='overview';

-- resize admin frame in admin guis
UPDATE gui_element SET e_width = 1000 WHERE e_id = 'AdminFrame';


-- CSW handling add CSW administration to admin2_de , admin2_en, admin1
-- admin2_en
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'headline_Configure_CSW', 3, 1, 'Catalog Management', 'Catalog Management', 'div', '', '', 5, 760, 193, 66, 2, '', ' Catalog Management', 'div', '', '', '', '', ''
FROM gui WHERE gui.gui_id = 'admin2_en' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'headline_Configure_CSW');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'loadCSW', 3, 1, 'Add Catalog', 'Add Catalog', 'a', '', 'href = "../php/mod_loadCatalogCapabilities.php?sessionID" target = "AdminFrame" ', 8, 780, 190, 20, 5, '', 'Add Catalog', 'a', '', '', '', 'AdminFrame', 'http://www.mapbender.org/index.php/newGUI'
FROM gui WHERE gui.gui_id = 'admin2_en' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'loadCSW');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'loadCSWGUI', 3, 1, 'Link Catalog to GUI', 'Link Catalog to GUI', 'a', '', 'href = "../php/mod_loadCatalogToGUI.php?sessionID" target = "AdminFrame" ', 8, 800, 190, 20, 5, '', 'Link Catalog to GUI', 'a', '', '', '', 'AdminFrame', 'http://www.mapbender.org/index.php/newGUI'
FROM gui WHERE gui.gui_id = 'admin2_en' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'loadCSWGUI');

-- admin2_de
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'headline_Configure_CSW', 3, 1, 'Catalog Management', 'Catalog Management', 'div', '', '', 5, 760, 193, 66, 2, '', ' CSW Verwaltung', 'div', '', '', '', '', ''
FROM gui WHERE gui.gui_id = 'admin2_de' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'headline_Configure_CSW');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'loadCSW', 3, 1, 'CSW laden', 'CSW laden', 'a', '', 'href = "../php/mod_loadCatalogCapabilities.php?sessionID" target = "AdminFrame" ', 8, 780, 190, 20, 5, '', 'Add Catalog', 'a', '', '', '', 'AdminFrame', 'http://www.mapbender.org/index.php/newGUI'
FROM gui WHERE gui.gui_id = 'admin2_de' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'loadCSW');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'loadCSWGUI', 3, 1, 'CSW einer Applikation zuordnen', 'Link Catalog to GUI', 'a', '', 'href = "../php/mod_loadCatalogToGUI.php?sessionID" target = "AdminFrame" ', 8, 800, 190, 20, 5, '', 'Link Catalog to GUI', 'a', '', '', '', 'AdminFrame', 'http://www.mapbender.org/index.php/newGUI'
FROM gui WHERE gui.gui_id = 'admin2_de' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'loadCSWGUI');

-- admin1
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'loadCSW',3,1,'Add Catalog','Add Catalog','a','','href = "../php/mod_loadCatalogCapabilities.php?sessionID" target = "AdminFrame" ',8,1140,190,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Add Catalog','a','','','','AdminFrame','http://www.mapbender.org/index.php/newGUI'
FROM gui WHERE gui.gui_id = 'admin1' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'loadCSW');

INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'loadCSWGUI',3,1,'Link Catalog to GUI','Link Catalog to GUI','a','','href = "../php/mod_loadCatalogToGUI.php?sessionID" target = "AdminFrame" ',8,1160,190,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Link Catalog to GUI','a','','','','AdminFrame','http://www.mapbender.org/index.php/newGUI'
FROM gui WHERE gui.gui_id = 'admin1' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'loadCSWGUI');

-- add CSW search to gui1
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'addCSW',2,1,'search via a CSW Client','Search CSW','img','../img/button_gray/csw_off.png','onclick=''var searchCSWPopup = new mb_popup({title:"Search Catalog",url:"../javascripts/mod_searchCSW_ajax.php?sessionID",width:720, height:600,left:20, top:20});searchCSWPopup.show()''  onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");'' title="Search Catalog"',925,60,24,24,1,'','','','mod_addCSW.php','mod_addWMSgeneralFunctions.js','treeGDE,mapframe1','loadData','http://www.mapbender.org/index.php/AddCSW'
FROM gui WHERE gui.gui_id = 'gui1' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'addCSW');




-- remove event handlers, are now in the script
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'resizeMapsize';


--
-- CSW Support - new Tables to build up CSW support
--
CREATE TABLE cat
(
  cat_id serial NOT NULL, -- auto generated
  cat_version character varying(50) NOT NULL DEFAULT ''::character varying, -- get from cat version from xml - service_type_version
  --cat_name character varying(255), -- ows-service-id:title
  cat_title character varying(255) NOT NULL DEFAULT ''::character varying, --ows-service-id:title
  cat_abstract text, --ows-service-id:abstract
  --cat_keywords do we need them? There maybe a special table? - cs:keywords - another table to hold kw
  --UploadUrl
  cat_upload_url character varying(255),
  fees character varying(50), --servicetype-fees
  accessconstraints text, -- servicetype-accessconstraints
  --ServiceProvider
  providername character varying(255),
  providersite character varying(255),
  --ServiceContact
  individualname character varying(255),
  positionname character varying(255),
  --ContactInfo
  voice character varying(255), -- Phone
  facsimile character varying(255),
  --Address
  deliverypoint character varying(255),
  city character varying(255),
  administrativearea character varying(255),
  postalcode character varying(255),
  country character varying(255),
  electronicmailaddress character varying(255),
  --Whole Cap-doc
  cat_getcapabilities_doc text,
  --Information about Owner
  cat_owner integer,
  --Actuality
  cat_timestamp integer
);


ALTER TABLE ONLY cat
    ADD CONSTRAINT cat_pkey PRIMARY KEY (cat_id);

CREATE TABLE cat_keyword (
    fkey_cat_id integer NOT NULL,
    fkey_keyword_id integer NOT NULL
);

ALTER TABLE ONLY cat_keyword
    ADD CONSTRAINT pk_cat_keyword PRIMARY KEY (fkey_cat_id, fkey_keyword_id);

ALTER TABLE ONLY cat_keyword
    ADD CONSTRAINT fkey_keyword_id_fkey_cat_id FOREIGN KEY (fkey_keyword_id) REFERENCES keyword(keyword_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cat_keyword
    ADD CONSTRAINT fkey_cat_id_fkey_keyword_id FOREIGN KEY (fkey_cat_id) REFERENCES cat(cat_id) ON UPDATE CASCADE ON DELETE CASCADE;


CREATE TABLE gui_cat
(
  fkey_gui_id character varying(50) NOT NULL DEFAULT ''::character varying,
  fkey_cat_id integer NOT NULL DEFAULT 0,
  CONSTRAINT fkey_cat_gui_id FOREIGN KEY (fkey_gui_id)
      REFERENCES gui (gui_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fkey_cat_cat_id FOREIGN KEY (fkey_cat_id)
      REFERENCES cat (cat_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE cat_op_conf
(
  fk_cat_id integer NOT NULL,
  param_name character varying(255) NOT NULL,
  param_value text NOT NULL,
  param_type character varying(255) NOT NULL,
  CONSTRAINT pk_con_cat_op PRIMARY KEY (fk_cat_id, param_type, param_name, param_value),
  CONSTRAINT fk_cat_conf_to_cat FOREIGN KEY (fk_cat_id)
      REFERENCES cat (cat_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- remove obsolete src from treeGDE (no longer a frame)
UPDATE gui_element SET e_src = '' WHERE e_id = 'treeGDE';

-- remove frameborder = 0 from treeGDE (no longer a frame)
UPDATE gui_element SET e_attributes = 'onmouseover="this.style.zIndex=300;this.style.width=350;" onmouseout="this.style.zIndex=2;this.style.width=200"' WHERE e_id = 'treeGDE';

-- remove frameborder = 0 from switchLocale_noreload
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'switchLocale_noreload';

-- add title
UPDATE gui_element SET e_content = '<select style="font-family: Arial, sans-serif; font-size:12" title="Set background" name="mod_setBackground_list" onchange="mod_setBackground_change(this)"><option value="0"></option></select>' WHERE e_id = 'setBackground';

-- remove nobr tag from mapbender
UPDATE gui_element SET e_content = '<span>Ma</span><span style="color: blue;">P</span><span style="color: red;">b</span><span>ender</span><script type="text/javascript"> mb_registerSubFunctions("mod_mapbender()"); function mod_mapbender(){ document.getElementById("mapbender").style.left = parseInt(document.getElementById("mapframe1").style.left) + parseInt(document.getElementById("mapframe1").style.width) - 90; document.getElementById("mapbender").style.top = parseInt(document.getElementById("mapframe1").style.top) + parseInt(document.getElementById("mapframe1").style.height) -1; } </script>' WHERE e_id = 'mapbender';

-- correct URL encoding
UPDATE gui_element SET e_attributes = E'onclick="printWindow = window.open(\'../print/mod_printPDF.php?target=mapframe1&amp;sessionID&amp;conf=printPDF_b.conf\',\'printWin\',\'width=260, height=380, resizable=yes\');printWindow.focus();"  onmouseover="this.src = this.src.replace(/_off/,\'_over\');" onmouseout="this.src = this.src.replace(/_over/, \'_off\');"' WHERE e_id = 'printPDF' AND e_element = 'img';

-- added mandatory action attribute
UPDATE gui_element SET e_attributes = 'action="window.location.href"' WHERE e_id = 'setBackground';
UPDATE gui_element SET e_attributes = 'action="window.location.href" onsubmit="return mod_scaleText()"' WHERE e_id = 'scaleText';
UPDATE gui_element SET e_content = '<form id="form_switch_locale" action="window.location.href" name="form_switch_locale" target="parent"><select id="language" name="language" onchange="validate_locale()"></select></form>' WHERE e_id = 'switchLocale_noreload';

-- attributes only valid for printPDF button (img)
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'printPDF' AND e_element IN ('iframe','div');





-- New NavFrame element-vars (color and hoverColor) see http://trac.osgeo.org/mapbender/ticket/540
-- element var backGroundColor for gui element navFrame
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'navFrame','backGroundColor','#c6ced6','set the background-color of the NavFrame' ,'php_var'
FROM gui_element WHERE gui_element.e_id = 'navFrame' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'backGroundColor'
AND fkey_e_id = 'navFrame');

-- element var backGroundHoverColor for gui element navFrame
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'navFrame','backGroundHoverColor','#9cacbc','set the background-hover-color of the NavFrame' ,'php_var'
FROM gui_element WHERE gui_element.e_id = 'navFrame' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'backGroundHoverColor' AND fkey_e_id = 'navFrame');


--*****wmc adoptions****

DROP TABLE IF EXISTS wmc_keyword CASCADE;
DROP TABLE IF EXISTS wmc_md_topic_category CASCADE;


--adopt mb_user_wmc to store a serial column too!
ALTER TABLE mb_user_wmc DROP CONSTRAINT pk_user_wmc CASCADE;
ALTER TABLE mb_user_wmc DROP CONSTRAINT mb_user_wmc_pkey; --for older implementations
--DROP SEQUENCE mb_user_wmc_wmc_serial_id_seq cascade;
CREATE SEQUENCE mb_user_wmc_wmc_serial_id_seq;
ALTER  TABLE mb_user_wmc ADD COLUMN wmc_serial_id INTEGER;
ALTER TABLE mb_user_wmc ADD COLUMN wmc_timestamp_create INTEGER;
ALTER  TABLE mb_user_wmc ALTER COLUMN wmc_serial_id SET DEFAULT nextval('mb_user_wmc_wmc_serial_id_seq');
UPDATE mb_user_wmc SET wmc_serial_id = NEXTVAL('mb_user_wmc_wmc_serial_id_seq') WHERE wmc_serial_id is null;



-- Constraint: pk_user_wmc

ALTER TABLE mb_user_wmc
  ADD CONSTRAINT pk_user_wmc PRIMARY KEY(wmc_serial_id);

CREATE TABLE wmc_keyword (
	fkey_keyword_id	INTEGER REFERENCES keyword(keyword_id) ON DELETE CASCADE ON UPDATE CASCADE,
	fkey_wmc_serial_id INTEGER REFERENCES mb_user_wmc(wmc_serial_id) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER TABLE ONLY wmc_keyword
     ADD CONSTRAINT pk_wmc_keyword PRIMARY KEY (fkey_wmc_serial_id, fkey_keyword_id);

--****wmc adoptions end *****

-- jQuery UI
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'jq_ui',1,1,'The jQuery UI core','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.core.js','','',''
FROM gui WHERE gui.gui_id NOT LIKE 'admin%' AND gui.gui_id NOT LIKE 'wms%' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'jq_ui');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'jq_ui', 'css', '../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css', '' ,'file/css'
FROM gui_element WHERE gui_element.e_id = 'jq_ui' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'css'
AND fkey_e_id = 'jq_ui');

-- jQuery datatables
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/'
FROM gui WHERE gui.gui_id NOT LIKE 'admin%' AND gui.gui_id NOT LIKE 'wms%' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'jq_datatables');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'jq_datatables', 'defaultCss', '../extensions/dataTables-1.5/media/css/demo_table_jui.css', '' ,'file/css'
FROM gui_element WHERE gui_element.e_id = 'jq_datatables' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'defaultCss'
AND fkey_e_id = 'jq_datatables');

-- jQuery UI tabs
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.tabs.js','','jq_ui',''
FROM gui WHERE gui.gui_id NOT LIKE 'admin%' AND gui.gui_id NOT LIKE 'wms%' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'jq_ui_tabs');

-- jQuery upload
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'jq_upload',1,1,'','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../plugins/jq_upload.js','','',''
FROM gui WHERE gui.gui_id NOT LIKE 'admin%' AND gui.gui_id NOT LIKE 'wms%' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'jq_upload');

-- jQuery datatables CSS in Body
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'body', 'jquery_datatables', '../extensions/dataTables-1.5/media/css/demo_table_jui.css', '' ,'file/css'
FROM gui_element WHERE gui_element.e_id = 'body' AND gui_element.fkey_gui_id = 'gui1' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'jquery_datatables'
AND fkey_e_id = 'body');

-- add jquery ui dialog to gui1
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'jq_ui_dialog',5,1,'Module to manage jQuery UI dialog windows with multiple options for customization.','','div','','',-1,-1,NULL ,NULL ,NULL ,'','','div','../plugins/jq_ui_dialog.js','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.dialog.js','','jq_ui',''
FROM gui WHERE gui.gui_id = 'gui1' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'jq_ui_dialog');

ALTER TABLE mb_user_wmc ADD COLUMN
wmc_public INTEGER NOT NULL DEFAULT 0;

-- loadwmc from session is now a php var. The initial WMC is only created when this is set to 1
UPDATE gui_element_vars SET var_type = 'php_var' WHERE fkey_e_id = 'loadwmc' AND var_name = 'loadFromSession';

-- overwrite WMC documents if name and user are identical (0 == old behaviour, 1 == overwrite)
-- element var overwrite for gui element savewmc
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'savewmc', 'overwrite', '0', '' ,'var'
FROM gui_element WHERE gui_element.e_id = 'savewmc' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'overwrite' AND fkey_e_id = 'savewmc');

-- add history to map object
UPDATE gui_element SET e_mb_mod = '../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php' WHERE e_id = 'mapframe1' OR e_id = 'overview';

ALTER TABLE mb_user_wmc
		ADD COLUMN abstract TEXT,
		ADD COLUMN srs CHARACTER VARYING,
		ADD COLUMN minx DOUBLE PRECISION DEFAULT 0,
		ADD COLUMN miny DOUBLE PRECISION DEFAULT 0,
		ADD COLUMN maxx DOUBLE PRECISION DEFAULT 0,
		ADD COLUMN maxy DOUBLE PRECISION DEFAULT 0;


-- disable publish WMC and delete WMC for gui1
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'loadwmc', 'deleteWmc', '0', '' ,'var'
FROM gui_element WHERE gui_element.e_id = 'loadwmc'
AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'deleteWmc' AND fkey_e_id = 'loadwmc');

-- element var publishWmc for gui element loadwmc
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'loadwmc', 'publishWmc', '0', '' ,'var' FROM gui_element
WHERE gui_element.e_id = 'loadwmc' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'publishWmc' AND fkey_e_id = 'loadwmc');

-- add publish WMC to admin1
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id, 'wmcPublic',3,1,'Publish WMC','Publish WMC','a','','href = "../php/mod_wmc_publish.php?sessionID" target = "AdminFrame" ',8,1180,190,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','Publish WMC','a','','','','AdminFrame',''
FROM gui WHERE gui.gui_id = 'admin1' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'wmcPublic' AND fkey_gui_id = 'admin1');

-- add required modules for loadwmc and savewmc
UPDATE gui_element SET e_attributes = 'onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");''', e_requires = 'jq_ui_dialog,jq_ui_tabs,jq_upload,jq_datatables' WHERE e_id = 'loadwmc';
UPDATE gui_element SET e_requires = 'jq_ui_dialog' WHERE e_id = 'savewmc';


-- set new element vars for existing gui elements of non-standard guis

-- element var switchwms for gui element treeGDE
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'treeGDE', 'switchwms', 'true', 'enables/disables all layer of a wms' ,'var'
FROM gui_element WHERE gui_element.e_id = 'treeGDE' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'switchwms' AND fkey_e_id = 'treeGDE');

-- element i18n for all guis
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT DISTINCT fkey_gui_id,'i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','','','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext'
FROM gui_element WHERE gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'i18n');

-- increase fields for wfs featuretype attributes name
ALTER TABLE wfs_element ALTER COLUMN element_name TYPE VARCHAR(255);

-- add georss in gui1
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'addGeoRSS',2,1,'add a GeoRSS Feed to a running application','Add GeoRSS','img','../img/georss_logo_off.png','onclick=''loadGeoRSSByForm()''  onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");''',950,60,24,24,1,'','','','mod_georss.php','popupballon.js,usemap.js,geometry.js,../extensions/wz_jsgraphics.js','mapframe1','',''
FROM gui WHERE gui.gui_id = 'gui1' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'addGeoRSS');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'addGeoRSS', 'loadGeorssFromSession', '1', '' ,'php_var'
FROM gui_element WHERE gui_element.e_id = 'addGeoRSS' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'loadGeorssFromSession'
AND fkey_e_id = 'addGeoRSS');

-- element var initializeOnLoad for gazetteerWFS
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) SELECT gui_element.fkey_gui_id, 'gazetteerWFS', 'initializeOnLoad', '0', 'start gazetteer onload' ,'var' FROM gui_element WHERE gui_element.e_id = 'gazetteerWFS' AND gui_element.fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'initializeOnLoad' AND fkey_e_id = 'gazetteerWFS');

-- element var enableSearchWithoutParams for gazetteerWFS
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) SELECT gui_element.fkey_gui_id, 'gazetteerWFS', 'enableSearchWithoutParams', '0', 'define that search can be started without any search params' ,'var' FROM gui_element WHERE gui_element.e_id = 'gazetteerWFS' AND gui_element.fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'enableSearchWithoutParams' AND fkey_e_id = 'gazetteerWFS');

UPDATE gui_element SET e_js_file = '../plugins/mb_selectMapsize.js', e_attributes = '' WHERE e_id = 'selectMapsize';
INSERT INTO cat VALUES (2, '2.0.2', 'NLR CSW', '			NLR CSW: XQuery based catalog service conform to the HTTP protocol binding  of the OpenGIS Catalogue Service specification version 2.0.2/2.0.1		', 'http://geomatics.nlr.nl/excat/csw?request=GetCapabilities&service=CSW&version=2.0.2', 'NONE', 'NONE', NULL, NULL, 'Rob van Swol', 'Senior Scientist NLR-ASSP', NULL, NULL, '', NULL, NULL, NULL, NULL, 'vanswol@nlr.nl', '<?xml version="1.0" encoding="UTF-8"?><csw:Capabilities xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:ows="http://www.opengis.net/ows" xmlns:xlink="http://www.w3.org/1999/xlink" version="2.0.2">

	<!-- ========================================================= -->
	
	<ows:ServiceIdentification>
		<ows:Title>NLR CSW</ows:Title>
		
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ows:Abstract>
			NLR CSW: XQuery based catalog service conform to the HTTP protocol binding 
 of the OpenGIS Catalogue Service specification version 2.0.2/2.0.1
		</ows:Abstract>
	
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ows:Keywords>
			<ows:Keyword>CSW</ows:Keyword>
			<ows:Keyword>geospatial</ows:Keyword>
			<ows:Keyword>catalogue</ows:Keyword>
		</ows:Keywords>
		
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ows:ServiceType>CSW</ows:ServiceType>
		<ows:ServiceTypeVersion>2.0.2</ows:ServiceTypeVersion>
		<ows:Fees>NONE</ows:Fees>
		<ows:AccessConstraints>NONE</ows:AccessConstraints>
	</ows:ServiceIdentification>
	
	<!-- ========================================================= -->
	
	<ows:ServiceProvider>
		<ows:ProviderName>National Aerospace Laboratory NLR</ows:ProviderName>
		<ows:ProviderSite xlink:href="http://geomatics.nlr.nl/excat"/>
		
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ows:ServiceContact>
			<ows:IndividualName>Rob van Swol</ows:IndividualName>
			<ows:PositionName>Senior Scientist NLR-ASSP</ows:PositionName>
			
			<ows:ContactInfo>
				<ows:Phone>
					<ows:Voice>+31 527 248252</ows:Voice>
				</ows:Phone>
			
				<ows:Address>
					<ows:ElectronicMailAddress>vanswol@nlr.nl</ows:ElectronicMailAddress>
				</ows:Address>			
			</ows:ContactInfo>			
		</ows:ServiceContact>
	</ows:ServiceProvider>
	
	<!-- ========================================================= -->
	
	<ows:OperationsMetadata>
		<ows:Operation name="GetCapabilities">
			<ows:DCP>
				<ows:HTTP>
					<ows:Get xlink:href="http://geomatics.nlr.nl/excat/csw"/>
					<ows:Post xlink:href="http://geomatics.nlr.nl/excat/csw"/>
				</ows:HTTP>
			</ows:DCP>
			
			<ows:Parameter name="sections">
				<ows:Value>ServiceIdentification</ows:Value>
				<ows:Value>ServiceProvider</ows:Value>
				<ows:Value>OperationsMetadata</ows:Value>
				<ows:Value>Filter_Capabilities</ows:Value>
			</ows:Parameter>
			
		</ows:Operation>
		
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
	
		<ows:Operation name="DescribeRecord">
			<ows:DCP>
				<ows:HTTP>
					<ows:Get xlink:href="http://geomatics.nlr.nl/excat/csw"/>
					<ows:Post xlink:href="http://geomatics.nlr.nl/excat/csw"/>
				</ows:HTTP>
			</ows:DCP>
						
			<ows:Parameter name="TypeName">
				<ows:Value>csw:Record</ows:Value>
				<ows:Value>gmd:MD_Metadata</ows:Value>
			</ows:Parameter>
			
			<ows:Parameter name="OutputFormat">
				<ows:Value>application/xml</ows:Value>
			</ows:Parameter>
			
			<ows:Parameter name="SchemaLanguage">
				<ows:Value>XMLSCHEMA</ows:Value>
				<ows:Value>http://www.w3.org/XML/Schema</ows:Value>
			</ows:Parameter>
		</ows:Operation>

		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
<!--	
		<ows:Operation name="GetDomain">
			<ows:DCP>
				<ows:HTTP>
					<ows:Get  xlink:href="http://geomatics.nlr.nl/excat/csw" />
					<ows:Post xlink:href="http://geomatics.nlr.nl/excat/csw"  />
				</ows:HTTP>
			</ows:DCP>
		</ows:Operation>
-->		
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ows:Operation name="GetRecords">
			<ows:DCP>
				<ows:HTTP>
					<ows:Get xlink:href="http://geomatics.nlr.nl/excat/csw"/>
					<ows:Post xlink:href="http://geomatics.nlr.nl/excat/csw"/>
				</ows:HTTP>
			</ows:DCP>
			<ows:Parameter name="TypeName">
				<ows:Value>csw:Record</ows:Value>
			</ows:Parameter>
			<ows:Parameter name="OutputFormat">
				<ows:Value>application/xml</ows:Value>
			</ows:Parameter>
			<ows:Parameter name="OutputSchema">
				<ows:Value>csw:Record</ows:Value>
				<ows:Value>OGCCORE</ows:Value>
				<ows:Value>http://www.opengis.net/cat/csw/2.0.2</ows:Value>
				<!--ows:Value>http://www.isotc211.org/2005/gmd</ows:Value-->
				<!--ows:Value>ISO19139</ows:Value-->
				<!--ows:Value>CEN</ows:Value-->
				<!--ows:Value>ISO19115</ows:Value-->
			</ows:Parameter>
			<ows:Parameter name="ResultType">
				<ows:Value>results</ows:Value>
				<ows:Value>hits</ows:Value>
			</ows:Parameter>
			<ows:Parameter name="ElementSetName">
				<ows:Value>summary</ows:Value>
				<ows:Value>brief</ows:Value>
				<ows:Value>full</ows:Value>
			</ows:Parameter>
			<ows:Parameter name="ConstraintLanguage">
				<ows:Value>Filter</ows:Value>
				<ows:Value>CQL_Text</ows:Value>
			</ows:Parameter>
		</ows:Operation>
	
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ows:Operation name="GetRecordById">
			<ows:DCP>
				<ows:HTTP>
					<ows:Get xlink:href="http://geomatics.nlr.nl/excat/csw"/>
					<ows:Post xlink:href="http://geomatics.nlr.nl/excat/csw"/>
				</ows:HTTP>
			</ows:DCP>
			<ows:Parameter name="OutputFormat">
				<ows:Value>application/xml</ows:Value>
			</ows:Parameter>
			<ows:Parameter name="OutputSchema">
				<ows:Value>csw:Record</ows:Value>
				<ows:Value>OGCCORE</ows:Value>
				<ows:Value>http://www.opengis.net/cat/csw/2.0.2</ows:Value>
				<ows:Value>http://www.isotc211.org/2005/gmd</ows:Value>
				<ows:Value>ISO19139</ows:Value>
				<ows:Value>ISO19139NL</ows:Value>
				<!--ows:Value>CEN</ows:Value-->
				<!--ows:Value>ISO19115</ows:Value-->
			</ows:Parameter>
			<ows:Parameter name="ElementSetName">
				<ows:Value>summary</ows:Value>
				<ows:Value>brief</ows:Value>
				<ows:Value>full</ows:Value>
			</ows:Parameter>
		</ows:Operation>
	
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
<!--		
		<ows:Operation name="Transaction">
			<ows:DCP>
				<ows:HTTP>
					<ows:Get  xlink:href="http://geomatics.nlr.nl/excat/csw" />
					<ows:Post xlink:href="http://geomatics.nlr.nl/excat/csw"  />
				</ows:HTTP>
			</ows:DCP>
		</ows:Operation>
-->	
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
<!--		
		<ows:Operation name="Harvest">
			<ows:DCP>
				<ows:HTTP>
					<ows:Get  xlink:href="http://geomatics.nlr.nl/excat/csw" />
					<ows:Post xlink:href="http://geomatics.nlr.nl/excat/csw"  />
				</ows:HTTP>
			</ows:DCP>
		</ows:Operation>
-->	
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		<!-- Parameters -->
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ows:Parameter name="service">
			<ows:Value>http://www.opengis.net/cat/csw/2.0.2</ows:Value>
		</ows:Parameter>
		
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ows:Parameter name="version">
			<ows:Value>2.0.2</ows:Value>
			<ows:Value>2.0.1</ows:Value>
			<ows:Value>2.0.0</ows:Value>
		</ows:Parameter>		
	</ows:OperationsMetadata>

	<!-- ========================================================= -->
	
	<ogc:Filter_Capabilities>
		<ogc:Spatial_Capabilities>
			<ogc:GeometryOperands>
				<ogc:GeometryOperand>gml:Envelope</ogc:GeometryOperand>
				<!--
				<ogc:GeometryOperand>gml:Point</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:LineString</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Polygon</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:ArcByCenterPoint</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:CircleByCenterPoint</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Arc</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Circle</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:ArcByBulge</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Bezier</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Clothoid</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:CubicSpline</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Geodesic</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:OffsetCurve</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Triangle</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:PolyhedralSurface</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:TriangulatedSurface</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Tin</ogc:GeometryOperand>
				<ogc:GeometryOperand>gml:Solid</ogc:GeometryOperand>
				-->
			</ogc:GeometryOperands>
			
			<ogc:SpatialOperators>
				<ogc:SpatialOperator name="BBOX"/>
				<ogc:SpatialOperator name="Within"/>
				<!--
				<ogc:SpatialOperator name="Equals"/>
				<ogc:SpatialOperator name="Overlaps"/>
				<ogc:SpatialOperator name="Disjoint"/>
				<ogc:SpatialOperator name="Intersects"/>
				<ogc:SpatialOperator name="Touches"/>
				<ogc:SpatialOperator name="Crosses"/>
				<ogc:SpatialOperator name="Contains"/>
				<ogc:SpatialOperator name="Beyond"/>
				<ogc:SpatialOperator name="DWithin"/>
				-->
				<!-- The ''SpatialOperator'' element can have a GeometryOperands child -->
			</ogc:SpatialOperators>
		</ogc:Spatial_Capabilities>
		
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ogc:Scalar_Capabilities>
			<ogc:LogicalOperators/>
			
			<ogc:ComparisonOperators>
				<ogc:ComparisonOperator>EqualTo</ogc:ComparisonOperator>
				<ogc:ComparisonOperator>Like</ogc:ComparisonOperator>
				<ogc:ComparisonOperator>LessThan</ogc:ComparisonOperator>
				<ogc:ComparisonOperator>GreaterThan</ogc:ComparisonOperator>
				<ogc:ComparisonOperator>LessThanEqualTo</ogc:ComparisonOperator>
				<ogc:ComparisonOperator>GreaterThanEqualTo</ogc:ComparisonOperator>
				<ogc:ComparisonOperator>NotEqualTo</ogc:ComparisonOperator>
				<ogc:ComparisonOperator>Between</ogc:ComparisonOperator>
				<!--
				<ogc:ComparisonOperator>NullCheck</ogc:ComparisonOperator>
				-->
			</ogc:ComparisonOperators>
			
			<!--
			<ogc:ArithmeticOperators>
				<ogc:SimpleArithmetic>
				<ogc:Functions>
					<ogc:FunctionNames>
						<ogc:FunctionName nArgs="1">MIN</ogc:FunctionName>
					</ogc:FunctionNames>
				</ogc:Functions>
			</ogc:ArithmeticOperators>
			-->
		</ogc:Scalar_Capabilities>
		
		<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->	
		
		<ogc:Id_Capabilities>
			<ogc:EID/>
			<ogc:FID/>
		</ogc:Id_Capabilities>
	</ogc:Filter_Capabilities>
	
	<!-- ========================================================= -->

</csw:Capabilities>
', 1, 1260622475);


--
-- Data for Name: cat_keyword; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: cat_op_conf; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO cat_op_conf VALUES (2, 'get', 'http://geomatics.nlr.nl/excat/csw', 'getcapabilities');
INSERT INTO cat_op_conf VALUES (2, 'post', 'http://geomatics.nlr.nl/excat/csw', 'getcapabilities');
INSERT INTO cat_op_conf VALUES (2, 'get', 'http://geomatics.nlr.nl/excat/csw', 'describerecord');
INSERT INTO cat_op_conf VALUES (2, 'post', 'http://geomatics.nlr.nl/excat/csw', 'describerecord');
INSERT INTO cat_op_conf VALUES (2, 'get', 'http://geomatics.nlr.nl/excat/csw', 'getrecords');
INSERT INTO cat_op_conf VALUES (2, 'post', 'http://geomatics.nlr.nl/excat/csw', 'getrecords');
INSERT INTO cat_op_conf VALUES (2, 'get', 'http://geomatics.nlr.nl/excat/csw', 'getrecordbyid');
INSERT INTO cat_op_conf VALUES (2, 'post', 'http://geomatics.nlr.nl/excat/csw', 'getrecordbyid');
INSERT INTO gui_cat VALUES ('gui1', 2);

-- mapframe1 is now a jQuery plugin
UPDATE gui_element SET e_js_file = '../plugins/mb_map.js' WHERE e_id = 'mapframe1';
UPDATE gui_element SET e_js_file = '../plugins/mb_overview.js' WHERE e_id = 'overview';

--dragmapsize is themeroller ready
UPDATE gui_element SET e_attributes = 'class="ui-state-default"', e_more_styles = 'font-size:1px; cursor:move; width:10; height:10;' WHERE e_id = 'dragMapSize';

--tabs are themeroller ready
UPDATE gui_element_vars SET var_value = 'position:absolute;visibility:visible;cursor:pointer;' WHERE var_name = 'tab_style' AND fkey_e_id = 'tabs';

--nav frame is themeroller ready
DELETE FROM gui_element_vars WHERE var_name = 'backGroundColor' AND fkey_e_id = 'navFrame';
DELETE FROM gui_element_vars WHERE var_name = 'backGroundHoverColor' AND fkey_e_id = 'navFrame';

-- set UI classes for treeGDE
UPDATE gui_element SET e_more_styles = 'visibility:hidden;overflow:auto', e_attributes = 'class="ui-widget ui-widget-content"' 
WHERE e_id = 'treeGDE' AND fkey_gui_id IN 
(SELECT a.fkey_gui_id FROM gui_element AS a, gui_element AS b WHERE a.e_id = 'tabs' AND b.e_id = 'treeGDE' AND a.fkey_gui_id = b.fkey_gui_id);

-- remove expanding treeGDE in gui1
UPDATE gui_element SET e_more_styles = 'overflow:auto', e_attributes = 'class="ui-widget ui-widget-content"' 
WHERE e_id = 'treeGDE' AND fkey_gui_id IN ('gui1');

-- add new container toolbox_jquery
INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('toolbox_jquery','toolbox_jquery','All jQuery related elements in Mapbender',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','body',1,1,'body (obligatory)','','body','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_datatables',1,1,'jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_jgrowl',1,1,'A growl like widget to display messages','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','jq_jgrowl.js','../extensions/jGrowl-1.2.4/jquery.jgrowl_minimized.js','','','http://stanlemon.net/projects/jgrowl.html');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui',1,1,'jQuery UI core','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.core.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui_accordion',2,1,'accordion from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/ui.accordion.js','','jq_ui','http://docs.jquery.com/UI/Accordion');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_ui_dialog.js','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.dialog.js','','jq_ui','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui_effects',1,1,'Effects from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','jq_ui_effects.php','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.effects.core.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui_logo',2,1,'Logo','jQuery UI Logo','img','../img/jquery_ui_logo.png','',35,15,NULL ,NULL ,5,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.resizable.js','','jq_ui','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui_slider',5,1,'slider from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.slider.js','','jq_ui','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.tabs.js','','jq_ui','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_ui_themeroller',1,1,'A button that opens the ThemeRoller from a remote site','Themeroller','a','','class=''ui-state-default ui-corner-all'' 
href="javascript:(function(){if%20(!/Firefox/.test(navigator.userAgent)){alert(navigator.userAgent);%20return%20false;%20};%20if(window.jquitr){%20jquitr.addThemeRoller();%20}%20else{%20jquitr%20=%20{};%20jquitr.s%20=%20document.createElement(''script'');%20jquitr.s.src%20=%20''http://jqueryui.com/themeroller/developertool/developertool.js.php'';%20document.getElementsByTagName(''head'')[0].appendChild(jquitr.s);}%20})();"',320,15,NULL ,NULL ,NULL ,'text-decoration:none;padding:0.4em 1em 0.4em 20px','Custom theme','a','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('toolbox_jquery','jq_upload',1,1,'Allows to upload files into Mapbender''s temporary files folder','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../plugins/jq_upload.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'body', 'jq_ui_effects_transfer', '.ui-effects-transfer { z-index:1003; border: 2px dotted gray; } ', '', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'body', 'jq_ui_theme', '../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css', 'UI Theme from Themeroller', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_datatables', 'defaultCss', '../extensions/dataTables-1.5/media/css/demo_table_jui.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui', 'css', '../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'blind', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'bounce', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'clip', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'drop', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'explode', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'fold', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'highlight', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'pulsate', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'scale', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'shake', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'slide', '0', '1 = effect active', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('toolbox_jquery', 'jq_ui_effects', 'transfer', '1', '1 = effect active', 'php_var');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('toolbox_jquery','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.draggable.js','','jq_ui','http://jqueryui.com/demos/draggable/');

INSERT INTO gui_mb_user (fkey_gui_id, fkey_mb_user_id, mb_user_type) VALUES ('toolbox_jquery', 1, 'owner');


--
-- add jq_ui_theme
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'body', 'jq_ui_theme', 
'../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css', 'UI Theme from Themeroller' ,'file/css'
FROM gui_element WHERE gui_element.e_id = 'body' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'jq_ui_theme');

--
-- add resultList to applications with element gazetteerWFS
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, 
e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui_element.fkey_gui_id, 'resultList',2,1,'','Result List','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,
'','','div','mod_ResultList.js','../../lib/resultGeometryListController.js, ../../lib/resultGeometryListModel.js','mapframe1','','' 
from gui_element WHERE 
gui_element.e_id = 'gazetteerWFS' AND 
gui_element.fkey_gui_id 
NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'resultList');

--
-- add element var position to element resultList
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList', 'position', '[100,100]', 'position of the result list dialog' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList' AND var_name = 'position');

--
-- add element var resultListHeight to element resultList
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList', 'resultListHeight', '400', 'height of the result list dialog' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList' AND var_name = 'resultListHeight');

--
-- add element var resultListTitle to element resultList
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList', 'resultListTitle', 'Search results', 'title of the result list dialog' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList' AND var_name = 'resultListTitle');

--
-- add element var resultListWidth to element resultList
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList', 'resultListWidth', '800', 'width of the result list dialog' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList' AND var_name = 'resultListWidth');


--
-- add jq_ui_dialog to applications with element gazetteerWFS
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, 
e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)  
Select gui_element.fkey_gui_id ,'jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_ui_dialog.js','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.dialog.js,../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.draggable.js,../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.resizable.js','','jq_ui','' 
from gui_element WHERE 
gui_element.e_id = 'gazetteerWFS' AND 
gui_element.fkey_gui_id 
NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui_dialog');

--
-- add jq_ui to applications with element gazetteerWFS
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, 
e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
Select gui_element.fkey_gui_id ,'jq_ui',1,1,'The jQuery UI core','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','',
'','','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.core.js','','',''
from gui_element WHERE 
gui_element.e_id = 'gazetteerWFS' AND 
gui_element.fkey_gui_id 
NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui');

--
-- add jq_datatables to applications with element gazetteerWFS
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, 
e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
Select gui_element.fkey_gui_id ,
'jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/' 
from gui_element WHERE 
gui_element.e_id = 'gazetteerWFS' AND 
gui_element.fkey_gui_id 
NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_datatables');

-- add CSS for jQuery datatables
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'body', 'jq_datatables_css', '../extensions/dataTables-1.5/media/css/demo_table_jui.min.css', 'css-file for jQuery datatables' ,'file/css'
from gui_element
WHERE
gui_element.e_id = 'resultList' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'body' AND var_name = 'jq_datatables_css');

-- add resultList_DetailPopup to all resultLists to keep old functionality
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes,
 e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
	SELECT fkey_gui_id,'resultList_DetailPopup',2,1,'Detail Popup For resultList','','div','','',
	NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/mb_resultList_DetailPopup.js', '', 'resultList','', 'http://www.mapbender.org/resultList_DetailPopup' 

	FROM gui_element WHERE 
		(	gui_element.e_id = 'resultList' AND
  			gui_element.fkey_gui_id NOT IN ( SELECT fkey_gui_id FROM gui_element WHERE e_id = 'resultList_DetailPopup')
		)	;

--
-- add element var detailPopupHeight to element resultList_DetailPopup
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_DetailPopup', 'detailPopupHeight', '250', 'height of the result list detail popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_DetailPopup' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_DetailPopup' AND var_name = 'detailPopupHeight');

--
-- add element var detailPopupTitle to element resultList_DetailPopup
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_DetailPopup', 'detailPopupTitle', 'Details', 'title of the result list detail popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_DetailPopup' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_DetailPopup' AND var_name = 'detailPopupTitle');

--
-- add element var detailPopupWidth to element resultList_DetailPopup
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_DetailPopup', 'detailPopupWidth', '350', 'width of the result list detail popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_DetailPopup' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_DetailPopup' AND var_name = 'detailPopupWidth');

--
-- add element var position to element resultList_DetailPopup
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_DetailPopup', 'position', '[200,200]', 'position of the result list detail popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_DetailPopup' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_DetailPopup' AND var_name = 'position');

--
-- add element var openLinkFromSearch to element resultList_DetailPopup
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_DetailPopup', 'openLinkFromSearch', '0', 'open link directly if feature attr is defined as link' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_DetailPopup' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_DetailPopup' AND var_name = 'openLinkFromSearch');

--
-- add resultList_Zoom to applications with element gazetteerWFS
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, 
e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
Select gui_element.fkey_gui_id ,
'resultList_Zoom',2,1,'zoom functionality for resultList','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/mb_resultList_Zoom.js','','resultList,mapframe1','','http://www.mapbender.org/resultList_Zoom'
from gui_element WHERE 
gui_element.e_id = 'gazetteerWFS' AND 
gui_element.fkey_gui_id 
NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'resultList_Zoom');

--
-- add resultList_Highlight to applications with element gazetteerWFS
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, 
e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 
Select gui_element.fkey_gui_id ,
'resultList_Highlight',2,1,'highlighting functionality for resultList','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/mb_resultList_Highlight.js','','resultList,mapframe1,overview','','http://www.mapbender.org/resultList_Highlight'
from gui_element WHERE 
gui_element.e_id = 'gazetteerWFS' AND 
gui_element.fkey_gui_id 
NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'resultList_Highlight');

--
-- add element var maxHighlightedPoints to element resultList_Highlight
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_Highlight', 'maxHighlightedPoints', '500', 'max number of points to highlight' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_Highlight' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_Highlight' AND var_name = 'maxHighlightedPoints');

--
-- add element var resultHighlightColor to element resultList_Highlight
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_Highlight', 'resultHighlightColor', '#ff0000', 'color of the highlighting' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_Highlight' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_Highlight' AND var_name = 'resultHighlightColor');

--
-- add element var resultHighlightLineWidth to element resultList_Highlight
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_Highlight', 'resultHighlightLineWidth', '2', 'width of the highlighting line' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_Highlight' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_Highlight' AND var_name = 'resultHighlightLineWidth');

--
-- add element var resultHighlightZIndex to element resultList_Highlight
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'resultList_Highlight', 'resultHighlightZIndex', '100', 'zindex of the highlighting' ,'var'
from gui_element
WHERE
gui_element.e_id = 'resultList_Highlight' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'resultList_Highlight' AND var_name = 'resultHighlightZIndex');

--
-- delete deprecated element var maxHighlightedPoints of element gazetteerWFS
--
DELETE FROM gui_element_vars WHERE fkey_e_id = 'gazetteerWFS' AND var_name = 'maxHighlightedPoints';

--
-- added PRIMARYKEy for Table gui_gui_category
--
Alter Table gui_gui_category ADD
  CONSTRAINT pk_gui_gui_category PRIMARY KEY (fkey_gui_id, fkey_gui_category_id);

--
-- add Application toolbox_jquery to category 
--
INSERT INTO gui_gui_category (fkey_gui_id, fkey_gui_category_id)
VALUES('toolbox_jquery',2);


-- missing wz_jsgraphics in gazetteer
-- replacing obsolete tablesorter
UPDATE gui_element SET e_mb_mod = 'geometry.js,requestGeometryConstructor.js,popup.js,../extensions/wz_jsgraphics.js' WHERE e_id = 'gazetteerWFS';

-- translation entry
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('de', 'Add GeoRSS', 'GeoRSS hinzufügen');

-- remove deprecated attributes of gui element addWMS
UPDATE gui_element set e_attributes = '' WHERE e_id = 'addWMS';

-- remove onmouseover and onmouseout attributes for imprint 
UPDATE gui_element set e_attributes = 'frameborder = "0"' WHERE e_id = 'imprint' AND fkey_gui_id = 'gui';

INSERT INTO translations (locale, msgid, msgstr ) VALUES ('de', 'Search CSW', 'Katalogsuche');

-- deactivate slippy map by default
INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT DISTINCT fkey_gui_id, 'mapframe1' AS fkey_e_id, 'slippy' AS var_name, '0' AS var_value, 
'1 = Activates an animated, pseudo slippy map' AS context, 'var' AS var_type FROM gui_element 
WHERE e_id = 'mapframe1' AND fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element_vars 
WHERE var_name = 'slippy');

--
-- gui_digitze: added new printPDF (with rotation and pdf Template)
--
-- does not work with 4326 :(
--Delete from gui_element where fkey_gui_id = 'gui_digitize' and e_id = 'printPDF';
--INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes,
-- e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES(
--'gui_digitize','printPDF',2,1,'pdf print','Print','div','','',1,1,2,2,5,'','<div id="printPDF_working_bg"></div><div id="printPDF_working"><img src="../img/indicator_wheel.gif" style="padding:10px 0 0 10px">Generating PDF</div><div id="printPDF_input"><form id="printPDF_form" action="../print/printFactory.php"><div id="printPDF_selector"></div><div class="print_option"><input type="hidden" id="map_url" name="map_url" value=""/><input type="hidden" id="overview_url" name="overview_url" value=""/><input type="hidden" id="map_scale" name="map_scale" value=""/><input type="hidden" name="measured_x_values" /><input type="hidden" name="measured_y_values" /><br /></div><div class="print_option" id="printPDF_formsubmit"><input id="submit" type="submit" value="Print"><br /></div></form><div id="printPDF_result"></div></div>','div','../plugins/mb_print.js','../../lib/printbox.js,../extensions/jquery-ui-1.7.2.custom/development-bundle/external/bgiframe/jquery.bgiframe.min.js,../extensions/jquery.form.min.js','mapframe1','','http://www.mapbender.org/index.php/Print');

--INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES(
--'gui_digitize', 'printPDF', 'mbPrintConfig', '{"A4 landscape": "A4_lanscape_template.json","A4 portrait": "A4_portrait_template.json","A4 landscape": "A4_landscape_template.json","A3 landscape": "A3_landscape_template.json"}', '' ,'var');

--INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES(
--'gui_digitize', 'body', 'print_css', '../css/print_div.css', '' ,'file/css');

-- standard gui gui_wfs_conf entries for search
INSERT INTO  gui_wfs_conf(fkey_gui_id, fkey_wfs_conf_id) 
SELECT 'gui',wfs_conf_id from wfs_conf WHERE wfs_conf_id = 1 
AND wfs_conf_id
NOT IN (SELECT fkey_wfs_conf_id FROM gui_wfs_conf WHERE fkey_gui_id = 'gui');

INSERT INTO  gui_wfs_conf(fkey_gui_id, fkey_wfs_conf_id) 
SELECT 'gui2',wfs_conf_id from wfs_conf WHERE wfs_conf_id = 1 
AND wfs_conf_id
NOT IN (SELECT fkey_wfs_conf_id FROM gui_wfs_conf WHERE fkey_gui_id = 'gui2');

-- remove obsolete attributes from savewmc
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'savewmc';

-- insert datepicker in toolbox_jquery
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES ('toolbox_jquery','jq_ui_datepicker', 5,1,'Datepicker from jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_ui_datepicker.js','../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.datepicker.js','','jq_ui','');

-- remove obsolete js from dialog
UPDATE gui_element SET e_js_file = '' WHERE e_id = 'jq_ui_dialog';

--
-- add element var featureInfoLayerPopup to element featureInfoTunnel
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'featureInfoTunnel', 'featureInfoLayerPopup', 'true', 'display featureInfo in dialog popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'featureInfoTunnel' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'featureInfoTunnel' AND var_name = 'featureInfoLayerPopup');

--
-- add element var featureInfoPopupHeight to element featureInfoTunnel
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'featureInfoTunnel', 'featureInfoPopupHeight', '200', 'height of the featureInfo dialog popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'featureInfoTunnel' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'featureInfoTunnel' AND var_name = 'featureInfoPopupHeight');

--
-- add element var featureInfoPopupWidth to element featureInfoTunnel
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'featureInfoTunnel', 'featureInfoPopupWidth', '270', 'width of the featureInfo dialog popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'featureInfoTunnel' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'featureInfoTunnel' AND var_name = 'featureInfoPopupWidth');

--
-- add element var featureInfoPopupWidth to element featureInfoTunnel
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'featureInfoTunnel', 'featureInfoPopupPosition', '[100,100]', 'position of the featureInfoPopup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'featureInfoTunnel' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'featureInfoTunnel' AND var_name = 'featureInfoPopupPosition');

--
-- add element var featureInfoLayerPopup to element featureInfo1
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'featureInfo1', 'featureInfoLayerPopup', 'true', 'display featureInfo in dialog popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'featureInfo1' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'featureInfo1' AND var_name = 'featureInfoLayerPopup');

--
-- add element var featureInfoPopupHeight to element featureInfo1
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'featureInfo1', 'featureInfoPopupHeight', '200', 'height of the featureInfo dialog popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'featureInfo1' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'featureInfo1' AND var_name = 'featureInfoPopupHeight');

--
-- add element var featureInfoPopupWidth to element featureInfo1
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'featureInfo1', 'featureInfoPopupWidth', '270', 'width of the featureInfo dialog popup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'featureInfo1' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'featureInfo1' AND var_name = 'featureInfoPopupWidth');

--
-- add element var featureInfoPopupWidth to element featureInfo1
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'featureInfo1', 'featureInfoPopupPosition', '[100,100]', 'position of the featureInfoPopup' ,'var'
from gui_element
WHERE
gui_element.e_id = 'featureInfo1' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'featureInfo1' AND var_name = 'featureInfoPopupPosition');

--
-- add element var meetingPoint_export_url to element meetingPoint
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'meetingPoint', 'meetingPoint_export_url', '', 'set the export url for your meeting point (has to be a URL pointing to ..mapbender/frames/login.php)' ,'var'
from gui_element
WHERE
gui_element.e_id = 'meetingPoint' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'meetingPoint' AND var_name = 'meetingPoint_export_url');

-- move resultList_Popup back (after resultList)
 UPDATE gui_element SET e_pos = 3 WHERE e_id = 'resultList_Popup';
 
 -------------
 -------------
 --Things to be done for merging with geoportal.rlp***
--Adoption of the group table to allow contact information for groups
alter table mb_group add column mb_group_title character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_ext_id bigint;
alter table mb_group add column mb_group_address character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_postcode character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_city character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_stateorprovince character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_country character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_voicetelephone character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_facsimiletelephone character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_email character varying(255) NOT NULL DEFAULT ''::character varying;
alter table mb_group add column mb_group_logo_path text NOT NULL DEFAULT ''::character varying;
alter table mb_user_mb_group add column mb_user_mb_group_type integer;

--Adoption for adminsitration of conformities in the mapbender database***
--searchable
ALTER TABLE layer ADD COLUMN layer_searchable integer;
ALTER TABLE layer ALTER COLUMN layer_searchable SET STORAGE PLAIN;
ALTER TABLE layer ALTER COLUMN layer_searchable SET DEFAULT 1;

-- Column: featuretype_searchable

-- ALTER TABLE wfs_featuretype DROP COLUMN featuretype_searchable;

--ALTER TABLE wfs_featuretype ADD COLUMN featuretype_searchable integer;
--ALTER TABLE wfs_featuretype ALTER COLUMN featuretype_searchable SET STORAGE PLAIN;
--ALTER TABLE wfs_featuretype ALTER COLUMN featuretype_searchable SET DEFAULT 1;


ALTER TABLE wfs_featuretype ADD COLUMN featuretype_searchable integer;


-- Column: wms_timestamp_create
ALTER TABLE wms ADD COLUMN wms_timestamp_create integer;
ALTER TABLE wms ALTER COLUMN wms_timestamp_create SET STORAGE PLAIN;

--network accessibility
ALTER TABLE wms ADD COLUMN wms_network_access integer;
ALTER TABLE wms ALTER COLUMN wms_network_access SET STORAGE PLAIN;


-- Column: wfs_timestamp_create

-- ALTER TABLE wfs DROP COLUMN wfs_timestamp_create;
ALTER TABLE wfs ADD COLUMN wfs_timestamp_create integer;
ALTER TABLE wfs ALTER COLUMN wfs_timestamp_create SET STORAGE PLAIN;

--network accessibility
ALTER TABLE wfs ADD COLUMN wfs_network_access integer;
ALTER TABLE wfs ALTER COLUMN wfs_network_access SET STORAGE PLAIN;



--table for inspire metadata add on fields - for layer and featuretypes
-- Table: inspire_md_data
CREATE TABLE inspire_md_data
(
  data_id serial NOT NULL,
  data_time_begin integer, --timestamp
  data_time_end integer, --timestamp
  data_lineage text,
  data_spatial_res_value varchar(255), --
  data_spatial_res_type integer, --look up types like 1:equivalentScale, 2:Distance - see guidance paper for metadata
  CONSTRAINT data_id_pkey PRIMARY KEY (data_id)
);
--ALTER TABLE inspire_md_data OWNER TO "postgres";


--ok
--Conformity is a metadata information which is defined for spatial data services and spatial data sets - the information about the conformity should be generated automatically by the service registry
--mainly the available metadata information should be tested. This can be done when resitrating the service and when edit the service metadata.
-- There should be a list of available specifications (ir, or regulations) wherefor the services can be tested. This list have to be stored in the database.  


-- Table: conformity
CREATE TABLE conformity
(
  conformity_id serial NOT NULL,
  conformity_key varchar(255),
  fkey_spec_class_key character varying(255), 
  conformity_code_en character varying(255),
  conformity_code_fr character varying(255),
  conformity_code_de character varying(255),
  conformity_symbol character varying(255),
  conformity_description_de text,
  CONSTRAINT conformity_pkey PRIMARY KEY (conformity_id)
);

--ALTER TABLE conformity OWNER TO "postgres";
INSERT INTO conformity (fkey_spec_class_key, conformity_key, conformity_code_en, conformity_code_de, conformity_code_fr, conformity_symbol, conformity_description_de) VALUES ('inspire','1','conformant','Konform','','','Die Ressource stimmt mit der angegebenen Spezifikation in vollem Umfang überein.');
INSERT INTO conformity (fkey_spec_class_key, conformity_key, conformity_code_en, conformity_code_de, conformity_code_fr, conformity_symbol, conformity_description_de) VALUES ('inspire','2','notConformant','Nicht konform','','','Die Ressource stimmt mit der angegebenen Spezifikation nicht überein.');
INSERT INTO conformity (fkey_spec_class_key, conformity_key, conformity_code_en, conformity_code_de, conformity_code_fr, conformity_symbol, conformity_description_de) VALUES ('inspire','3','notEvaluated','Nicht überprüft','','','Die Übereinstimmung ist nicht überprüft worden.');


-- Table spec_classification: 
CREATE TABLE spec_classification
(
  spec_class_id serial NOT NULL,
 -- spec_key varchar(5) NOT NULL,
  spec_class_key character varying(255) UNIQUE,
  spec_class_code_de character varying(255),
  spec_class_code_en character varying(255),
  spec_class_code_fr character varying(255),
  spec_class_description_en text,
  spec_class_description_de text,
  spec_class_description_fr text,
  spec_class_timestamp integer,
  CONSTRAINT spec_class_id_pkey PRIMARY KEY (spec_class_id)

);


INSERT INTO spec_classification (spec_class_key, spec_class_code_de, spec_class_description_de) VALUES ('inspire','INSPIRE','Klasse der Inspire Spezifikationen/Regulations');

-- specification table:
CREATE TABLE spec
(
  spec_id serial NOT NULL,
  spec_key varchar(50) NOT NULL,
  spec_code_en character varying,
  spec_code_de character varying,
  spec_code_fr character varying,
  spec_link_en character varying,
  spec_link_de character varying,
  spec_link_fr character varying,
  spec_description_en text,
  spec_description_de text,
  spec_description_fr text,
  fkey_spec_class_key character varying(255), 
  spec_timestamp integer,
  CONSTRAINT spec_id_pkey PRIMARY KEY (spec_id),
  CONSTRAINT spec_spec_class_fkey FOREIGN KEY (fkey_spec_class_key)
      REFERENCES spec_classification (spec_class_key) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE

);


INSERT INTO spec (spec_key, spec_code_en, spec_link_en, spec_description_en, fkey_spec_class_key, spec_timestamp) VALUES ('ir_interop','INSPIRE Implementing rules laying down technical arrangements','http://www.geoportal.rlp.de/','INSPIRE Implementing rules laying down technical arrangements for the interoperability and harmonisation of orthoimagery','inspire',extract(epoch FROM (to_timestamp('2011-05-15','YYYY-MM-DD'))));

--conformity relation table:
-- Table: conformity_relation
CREATE TABLE conformity_relation
(
  relation_id serial NOT NULL,
  fkey_wms_id integer,
  fkey_wfs_id integer,
  fkey_inspire_md_id integer,
  fkey_conformity_id integer,
  fkey_spec_id integer,
  CONSTRAINT relation_id_pkey PRIMARY KEY (relation_id),
  CONSTRAINT conformity_relation_wms_id_fkey FOREIGN KEY (fkey_wms_id)
      REFERENCES wms (wms_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT conformity_relation_wfs_id_fkey FOREIGN KEY (fkey_wfs_id)
      REFERENCES wfs (wfs_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT conformity_relation_inspire_md_fkey FOREIGN KEY (fkey_inspire_md_id)
      REFERENCES inspire_md_data (data_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT conformity_relation_conformity_fkey FOREIGN KEY (fkey_conformity_id)
      REFERENCES conformity (conformity_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT conformity_relation_spec_fkey FOREIGN KEY (fkey_spec_id)
      REFERENCES spec (spec_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: custom_category
-- Table: custom_category
CREATE TABLE custom_category
(
  custom_category_id serial NOT NULL,
  custom_category_key varchar(5) NOT NULL,
  custom_category_code_en character varying(255),
  custom_category_code_de character varying(255),
  custom_category_code_fr character varying(255),
  custom_category_symbol character varying(255),
  custom_category_description_de text,
  CONSTRAINT custom_category_pkey PRIMARY KEY (custom_category_id)
);


INSERT INTO custom_category (custom_category_key, custom_category_code_en, custom_category_code_de, custom_category_code_fr, custom_category_symbol, custom_category_description_de) VALUES ('dc1','dummy category','Dummy Kategorie','','','Demo Kategorie zur Klassifizierung von Mapbender Registry Inhalten');

--delete double entries - cause they have serial ids:
DELETE FROM custom_category WHERE custom_category_id IN (SELECT max(custom_category_id) FROM custom_category GROUP BY custom_category_key HAVING count(*) > 1);

-- Table: layer_custom_category
CREATE TABLE layer_custom_category
(
  fkey_layer_id integer NOT NULL,
  fkey_custom_category_id integer NOT NULL,
  CONSTRAINT layer_custom_category_fkey_layer_id_fkey FOREIGN KEY (fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT layer_custom_category_fkey_custom_category_id_fkey FOREIGN KEY (fkey_custom_category_id)
      REFERENCES custom_category (custom_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: wfs_featuretype_custom_category
CREATE TABLE wfs_featuretype_custom_category
(
  fkey_featuretype_id integer NOT NULL,
  fkey_custom_category_id integer NOT NULL,
  CONSTRAINT wfs_featuretype_custom_category_fkey_featuretype_id_fkey FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wfs_featuretype_custom_category_fkey_custom_category_id_fkey FOREIGN KEY (fkey_custom_category_id)
      REFERENCES custom_category (custom_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

--functions to collect the categories into strings
CREATE OR REPLACE FUNCTION f_collect_custom_cat_layer(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_layer_id ALIAS FOR $1;
  custom_cat_string  TEXT;
  custom_cat_record  RECORD;

BEGIN
custom_cat_string := '';

FOR custom_cat_record IN SELECT layer_custom_category.fkey_custom_category_id from layer_custom_category WHERE layer_custom_category.fkey_layer_id=$1  LOOP
custom_cat_string := custom_cat_string || '{' ||custom_cat_record.fkey_custom_category_id || '}';
END LOOP ;
  
RETURN custom_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;


CREATE OR REPLACE FUNCTION f_collect_custom_cat_wfs_featuretype(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_featuretype_id ALIAS FOR $1;
  custom_cat_string  TEXT;
  custom_cat_record  RECORD;

BEGIN
custom_cat_string := '';

FOR custom_cat_record IN SELECT wfs_featuretype_custom_category.fkey_custom_category_id from wfs_featuretype_custom_category WHERE wfs_featuretype_custom_category.fkey_featuretype_id=$1  LOOP
custom_cat_string := custom_cat_string || '{' ||custom_cat_record.fkey_custom_category_id || '}';
END LOOP ;
  
RETURN custom_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;



-- Table: inspire_category
-- Table: inspire_category
CREATE TABLE inspire_category
(
  inspire_category_id serial NOT NULL,
  inspire_category_key varchar(5) NOT NULL,
  inspire_category_code_en character varying(255),
  inspire_category_code_de character varying(255),
  inspire_category_code_fr character varying(255),
  inspire_category_symbol character varying(255),
  inspire_category_description_de text,
  CONSTRAINT inspire_category_pkey PRIMARY KEY (inspire_category_id)
);



INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.1','Coordinate reference systems','Koordinatenreferenzsysteme','','','Systeme zur eindeutigen räumlichen Referenzierung von Geodaten anhand eines Koordinatensatzes (x, y, z) und/oder Angaben zu Breite, Länge und Höhe auf der Grundlage eines geodätischen horizontalen und vertikalen Datums.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.2','Geographical grid systems','Geografische Gittersysteme','','','Harmonisiertes Gittersystem mit Mehrfachauflösung, gemeinsamem Ursprungspunkt und standardisierter Lokalisierung und Größe der Gitterzellen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.3','Geographical names','Geografische Bezeichnungen','','','Namen von Gebieten, Regionen, Orten, Großstädten, Vororten, Städten oder Siedlungen sowie jedes geografische oder topografische Merkmal von öffentlichem oder historischem Interesse.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.4','Administrative units','Verwaltungseinheiten','','','Lokale, regionale und nationale Verwaltungseinheiten, die die Gebiete abgrenzen, in denen die Mitgliedstaaten Hoheitsbefugnisse haben und/oder ausüben und die durch Verwaltungsgrenzen voneinander getrennt sind.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.5','Addresses','Adressen','','','Lokalisierung von Grundstücken anhand von Adressdaten, in der Regel Straßenname, Hausnummer und Postleitzahl.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.6','Cadastral parcels','Flurstücke/Grundstücke (Katasterparzellen)','','','Gebiete, die anhand des Grundbuchs oder gleichwertiger Verzeichnisse bestimmt werden. ');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.7','Transport networks','Verkehrsnetze','','','Verkehrsnetze und zugehörige Infrastruktureinrichtungen für Straßen-, Schienen- und Luftverkehr sowie Schifffahrt. Umfasst auch die Verbindungen zwischen den verschiedenen Netzen. Umfasst auch das transeuropäische Verkehrsnetz im Sinne der Entscheidung Nr. 1692/96/EG des Europäischen Parlaments und des Rates vom 23. Juli 1996 über gemeinschaftliche Leitlinien für den Aufbau eines transeuropäischen Verkehrsnetzes und künftiger Überarbeitungen dieser Entscheidung. ');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.8','Hydrography','Gewässernetz','','','Elemente des Gewässernetzes, einschließlich Meeresgebieten und allen sonstigen Wasserkörpern und hiermit verbundenen Teilsystemen, darunter Einzugsgebiete und Teileinzugsgebiete. Gegebenenfalls gemäß den Definitionen der Richtlinie 2000/60/EG des Europäischen Parlaments und des Rates vom 23. Oktober 2000 zur Schaffung eines Ordnungsrahmens für Maßnahmen der Gemeinschaft im Bereich der Wasserpolitik und in Form von Netzen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('1.9','Protected sites','Schutzgebiete','','','Gebiete, die im Rahmen des internationalen und des gemeinschaftlichen Rechts sowie des Rechts der Mitgliedstaaten ausgewiesen sind oder verwaltet werden, um spezifische Erhaltungsziele zu erreichen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('2.1','Elevation','Höhe','','','Digitale Höhenmodelle für Land-, Eis- und Meeresflächen. Dazu gehören Geländemodell, Tiefenmessung und Küstenlinie.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('2.2','Land cover','Bodenbedeckung','','','Physische und biologische Bedeckung der Erdoberfläche, einschließlich künstlicher Flächen, landwirtschaftlicher Flächen, Wäldern, natürlicher (naturnaher) Gebiete, Feuchtgebieten und Wasserkörpern.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('2.3','Orthoimagery','Orthofotografie','','','Georeferenzierte Bilddaten der Erdoberfläche von satelliten- oder luftfahrzeuggestützten Sensoren.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('2.4','Geology','Geologie','','','Geologische Beschreibung anhand von Zusammensetzung und Struktur. Dies umfasst auch Grundgestein, Grundwasserleiter und Geomorphologie.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.1','Statistical units','Statistische Einheiten','','','Einheiten für die Verbreitung oder Verwendung statistischer Daten.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.2','Buildings','Gebäude','','','Geografischer Standort von Gebäuden.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.3','Soil','Boden','','','Beschreibung von Boden und Unterboden anhand von Tiefe, Textur, Struktur und Gehalt an Teilchen sowie organischem Material, Steinigkeit, Erosion, gegebenenfalls durchschnittliches Gefälle und erwartete Wasserspeicherkapazität.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.4','Land use','Bodennutzung','','','Beschreibung von Gebieten anhand ihrer derzeitigen und geplanten künftigen Funktion oder ihres sozioökonomischen Zwecks (z. B. Wohn-, Industrie- oder Gewerbegebiete, land- oder forstwirtschaftliche Flächen, Freizeitgebiete).');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.5','Human health and safety','Gesundheit und Sicherheit','','','Geografische Verteilung verstärkt auftretender pathologischer Befunde (Allergien, Krebserkrankungen, Erkrankungen der Atemwege usw.), Informationen über Auswirkungen auf die Gesundheit (Biomarker, Rückgang der Fruchtbarkeit, Epidemien) oder auf das Wohlbefinden (Ermüdung, Stress usw.) der Menschen in unmittelbarem Zusammenhang mit der Umweltqualität (Luftverschmutzung, Chemikalien, Abbau der Ozonschicht, Lärm usw.) oder in mittelbarem Zusammenhang mit der Umweltqualität (Nahrung, genetisch veränderte Organismen usw.).');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.6','Utility and governmental services','Versorgungswirtschaft und staatliche Dienste','','','Versorgungseinrichtungen wie Abwasser- und Abfallentsorgung, Energieversorgung und Wasserversorgung; staatliche Verwaltungs- und Sozialdienste wie öffentliche Verwaltung, Katastrophenschutz, Schulen und Krankenhäuser.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.7','Environmental monitoring facilities','Umweltüberwachung','','','Standort und Betrieb von Umweltüberwachungseinrichtungen einschließlich Beobachtung und Messung von Schadstoffen, des Zustands von Umweltmedien und anderen Parametern des Ökosystems (Artenvielfalt, ökologischer Zustand der Vegetation usw.) durch oder im Auftrag von öffentlichen Behörden.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.8','Production and industrial facilities','Produktions- und Industrieanlagen','','','Standorte für industrielle Produktion, einschließlich durch die Richtlinie 96/61/EG des Rates vom 24. September 1996 über die integrierte Vermeidung und Verminderung der Umweltverschmutzung erfasste Anlagen und Einrichtungen zur Wasserentnahme sowie Bergbau- und Lagerstandorte.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.9','Agricultural and aquaculture facilities','Landwirtschaftliche Anlagen und Aquakulturanlagen','','','Landwirtschaftliche Anlagen und Produktionsstätten (einschließlich Bewässerungssystemen, Gewächshäusern und Ställen).');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.10','Population distribution — demography','Verteilung der Bevölkerung — Demografie','','','Geografische Verteilung der Bevölkerung, einschließlich Bevölkerungsmerkmalen und Tätigkeitsebenen, zusammengefasst nach Gitter, Region, Verwaltungseinheit oder sonstigen analytischen Einheiten.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.11','Area management/restriction/regulation zones and reporting units','Bewirtschaftungsgebiete/Schutzgebiete/geregelte Gebiete und Berichterstattungseinheiten','','','Auf internationaler, europäischer, nationaler, regionaler und lokaler Ebene bewirtschaftete, geregelte oder zu Zwecken der Berichterstattung herangezogene Gebiete. Dazu zählen Deponien, Trinkwasserschutzgebiete, nitratempfindliche Gebiete, geregelte Fahrwasser auf See oder auf großen Binnengewässern, Gebiete für die Abfallverklappung, Lärmschutzgebiete, für Exploration und Bergbau ausgewiesene Gebiete, Flussgebietseinheiten, entsprechende Berichterstattungseinheiten und Gebiete des Küstenzonenmanagements.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.12','Natural risk zones','Gebiete mit naturbedingten Risiken','','','Gefährdete Gebiete, eingestuft nach naturbedingten Risiken (sämtliche atmosphärischen, hydrologischen, seismischen, vulkanischen Phänomene sowie Naturfeuer, die aufgrund ihres örtlichen Auftretens sowie ihrer Schwere und Häufigkeit signifikante Auswirkungen auf die Gesellschaft haben können), z. B. Überschwemmungen, Erdrutsche und Bodensenkungen, Lawinen, Waldbrände, Erdbeben oder Vulkanausbrüche.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.13','Atmospheric conditions','Atmosphärische Bedingungen','','','Physikalische Bedingungen in der Atmosphäre. Dazu zählen Geodaten auf der Grundlage von Messungen, Modellen oder einer Kombination aus beiden sowie Angabe der Messstandorte.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.14','Meteorological geographical features','Meteorologisch-geografische Kennwerte','','','Witterungsbedingungen und deren Messung; Niederschlag, Temperatur, Gesamtverdunstung (Evapotranspiration), Windgeschwindigkeit und Windrichtung.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.15','Oceanographic geographical features','Ozeanografisch-geografische Kennwerte','','','Physikalische Bedingungen der Ozeane (Strömungsverhältnisse, Salinität, Wellenhöhe usw.).');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.16','Sea regions','Meeresregionen','','','Physikalische Bedingungen von Meeren und salzhaltigen Gewässern, aufgeteilt nach Regionen und Teilregionen mit gemeinsamen Merkmalen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.17','Bio-geographical regions','Biogeografische Regionen','','','Gebiete mit relativ homogenen ökologischen Bedingungen und gemeinsamen Merkmalen.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.18','Habitats and biotopes','Lebensräume und Biotope','','','Geografische Gebiete mit spezifischen ökologischen Bedingungen, Prozessen, Strukturen und (lebensunterstützenden) Funktionen als physische Grundlage für dort lebende Organismen. Dies umfasst auch durch geografische, abiotische und biotische Merkmale gekennzeichnete natürliche oder naturnahe terrestrische und aquatische Gebiete.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.19','Species distribution','Verteilung der Arten','','','Geografische Verteilung des Auftretens von Tier- und Pflanzenarten, zusammengefasst in Gittern, Region, Verwaltungseinheit oder sonstigen analytischen Einheiten.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.20','Energy resources','Energiequellen','','','Energiequellen wie Kohlenwasserstoffe, Wasserkraft, Bioenergie, Sonnen- und Windenergie usw., gegebenenfalls mit Tiefen- bzw. Höhenangaben zur Ausdehnung der Energiequelle.');
INSERT INTO inspire_category (inspire_category_key, inspire_category_code_en, inspire_category_code_de, inspire_category_code_fr, inspire_category_symbol, inspire_category_description_de) VALUES ('3.21','Mineral resources','Mineralische Bodenschätze','','','Mineralische Bodenschätze wie Metallerze, Industrieminerale usw., gegebenenfalls mit Tiefen- bzw. Höhenangaben zur Ausdehnung der Bodenschätze.');

--delete double entries - cause they have serial ids:
DELETE FROM inspire_category WHERE inspire_category_id IN (SELECT max(inspire_category_id) FROM inspire_category GROUP BY inspire_category_key HAVING count(*) > 1);

-- Table: layer_inspire_category
CREATE TABLE layer_inspire_category
(
  fkey_layer_id integer NOT NULL,
  fkey_inspire_category_id integer NOT NULL,
  CONSTRAINT layer_inspire_category_fkey_layer_id_fkey FOREIGN KEY (fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT layer_inspire_category_fkey_inspire_category_id_fkey FOREIGN KEY (fkey_inspire_category_id)
      REFERENCES inspire_category (inspire_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: wfs_featuretype_inspire_category
CREATE TABLE wfs_featuretype_inspire_category
(
  fkey_featuretype_id integer NOT NULL,
  fkey_inspire_category_id integer NOT NULL,
  CONSTRAINT wfs_featuretype_inspire_category_fkey_featuretype_id_fkey FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wfs_featuretype_inspire_category_fkey_inspire_category_id_fkey FOREIGN KEY (fkey_inspire_category_id)
      REFERENCES inspire_category (inspire_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

--functions to collect the categories into strings
CREATE OR REPLACE FUNCTION f_collect_inspire_cat_layer(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_layer_id ALIAS FOR $1;
  inspire_cat_string  TEXT;
  inspire_cat_record  RECORD;

BEGIN
inspire_cat_string := '';

FOR inspire_cat_record IN SELECT layer_inspire_category.fkey_inspire_category_id from layer_inspire_category WHERE layer_inspire_category.fkey_layer_id=$1  LOOP
inspire_cat_string := inspire_cat_string || '{' ||inspire_cat_record.fkey_inspire_category_id || '}';
END LOOP ;
  
RETURN inspire_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;


CREATE OR REPLACE FUNCTION f_collect_inspire_cat_wfs_featuretype(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_featuretype_id ALIAS FOR $1;
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
  LANGUAGE 'plpgsql' VOLATILE STRICT;


-- Table: wfs_featuretype_md_topic_category
CREATE TABLE wfs_featuretype_md_topic_category
(
  fkey_featuretype_id integer NOT NULL,
  fkey_md_topic_category_id integer NOT NULL,
  CONSTRAINT wfs_featuretype_md_topic_category_fkey_featuretype_id_fkey FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wfs_featuretype_md_topic_category_fkey_md_topic_cat_id_fkey FOREIGN KEY (fkey_md_topic_category_id)
      REFERENCES md_topic_category (md_topic_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE OR REPLACE FUNCTION f_collect_topic_cat_layer(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_layer_id ALIAS FOR $1;
  topic_cat_string  TEXT;
  topic_cat_record  RECORD;

BEGIN
topic_cat_string := '';

FOR topic_cat_record IN SELECT layer_md_topic_category.fkey_md_topic_category_id from layer_md_topic_category WHERE layer_md_topic_category.fkey_layer_id=$1  LOOP
topic_cat_string := topic_cat_string || '{' ||topic_cat_record.fkey_md_topic_category_id || '}';
END LOOP ;
  
RETURN topic_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;


CREATE OR REPLACE FUNCTION f_collect_topic_cat_wfs_featuretype(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_featuretype_id ALIAS FOR $1;
  topic_cat_string  TEXT;
  topic_cat_record  RECORD;

BEGIN
topic_cat_string := '';

FOR topic_cat_record IN SELECT wfs_featuretype_md_topic_category.fkey_md_topic_category_id from wfs_featuretype_md_topic_category WHERE wfs_featuretype_md_topic_category.fkey_featuretype_id=$1  LOOP
topic_cat_string := topic_cat_string || '{' ||topic_cat_record.fkey_md_topic_category_id || '}';
END LOOP ;
  
RETURN topic_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;

--generate the group for the decentral registrating institutions
-- View: registrating_groups
CREATE OR REPLACE VIEW registrating_groups AS 
 SELECT f.fkey_mb_group_id, f.fkey_mb_user_id
   FROM mb_user_mb_group f, mb_user_mb_group s
  WHERE f.mb_user_mb_group_type = 1 AND s.fkey_mb_group_id = 36 AND f.fkey_mb_user_id = s.fkey_mb_user_id
  ORDER BY f.fkey_mb_group_id, f.fkey_mb_user_id;

-- Function: f_getwfs_tou(integer)
CREATE OR REPLACE FUNCTION f_getwfs_tou(integer)
  RETURNS integer AS
$BODY$
DECLARE
   wfs_tou int4;
BEGIN
wfs_tou := fkey_termsofuse_id from wfs_termsofuse where wfs_termsofuse.fkey_wfs_id=$1; 
RETURN wfs_tou;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: f_getwms_tou(integer)
CREATE OR REPLACE FUNCTION f_getwms_tou(integer)
  RETURNS integer AS
$BODY$
DECLARE
   wms_tou int4;
BEGIN
wms_tou := fkey_termsofuse_id from wms_termsofuse where wms_termsofuse.fkey_wms_id=$1; 
RETURN wms_tou;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: f_collect_epsg(integer)
CREATE OR REPLACE FUNCTION f_collect_epsg(integer)
  RETURNS text AS
$BODY$DECLARE
  i_layer_id ALIAS FOR $1;
  epsg_string  TEXT;
  epsg_record  RECORD;

BEGIN
epsg_string := '';

FOR epsg_record IN SELECT layer_epsg.epsg from layer_epsg WHERE layer_epsg.fkey_layer_id=$1  LOOP
epsg_string := epsg_string ||  epsg_record.epsg || ';';
END LOOP ;
  
RETURN epsg_string;

    --CASE
      --WHEN LEN(epsg) > 0 THEN LEFT(epsg, LEN(epsg) - 1)
     -- ELSE epsg
    

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;

-- Function: f_layer_load_count(integer)
CREATE OR REPLACE FUNCTION f_layer_load_count(integer)
  RETURNS integer AS
$BODY$
DECLARE
   layer_rel int4;
BEGIN
layer_rel := load_count from layer_load_count where layer_load_count.fkey_layer_id=$1; 
RETURN layer_rel;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: f_collect_searchtext(integer, integer)
CREATE OR REPLACE FUNCTION f_collect_searchtext(integer, integer)
  RETURNS text AS
$BODY$DECLARE
    p_wms_id ALIAS FOR $1;
    p_layer_id ALIAS FOR $2;
    
    r_keywords RECORD;
    l_result TEXT;
BEGIN
    l_result := '';
    l_result := l_result || (SELECT COALESCE(wms_title, '') || ' ' || COALESCE(wms_abstract, '') FROM wms WHERE wms_id = p_wms_id);
    l_result := l_result || (SELECT COALESCE(layer_name, '')|| ' ' || COALESCE(layer_title, '')  || ' ' || COALESCE(layer_abstract, '') FROM layer WHERE layer_id = p_layer_id);
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
  LANGUAGE 'plpgsql' VOLATILE STRICT;

-- Function: f_collect_searchtext_wfs(integer, integer)
-- DROP FUNCTION f_collect_searchtext_wfs(integer, integer)
CREATE OR REPLACE FUNCTION f_collect_searchtext_wfs(integer, integer)
  RETURNS text AS
$BODY$
DECLARE
   p_wfs_id ALIAS FOR $1;
   p_featuretype_id ALIAS FOR $2;
     r_keywords RECORD;
   l_result TEXT;
BEGIN
   l_result := '';
   l_result := l_result || (SELECT COALESCE(wfs_title, '') || ' ' || COALESCE(wfs_abstract, '') FROM wfs WHERE wfs_id = p_wfs_id);
   l_result := l_result || (SELECT COALESCE(featuretype_name, '')|| ' ' || COALESCE(featuretype_title, '')  || ' ' || COALESCE(featuretype_abstract, '') FROM wfs_featuretype WHERE featuretype_id = p_featuretype_id);
   FOR r_keywords IN SELECT DISTINCT keyword FROM
       (SELECT keyword FROM wfs_featuretype_keyword L JOIN keyword K ON (K.keyword_id = L.fkey_keyword_id AND L.fkey_featuretype_id = p_featuretype_id)
       ) AS __keywords__ LOOP
       l_result := l_result || ' ' || COALESCE(r_keywords.keyword, '');
   END LOOP;
  l_result := UPPER(l_result);
  l_result := replace(replace(replace(replace(replace(replace(replace(l_result,'Ä','AE'),'ß','SS'),'Ö','OE'),'Ü','UE'),'ü','UE'),'ö','OE'),'ä','AE');

   RETURN l_result;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: f_getwfsmodultype(integer)
CREATE OR REPLACE FUNCTION f_getwfsmodultype(integer)
  RETURNS integer AS
$BODY$
DECLARE
    i_search INT4;
BEGIN
i_search := count(*) from wfs_conf, wfs_conf_element where wfs_conf.wfs_conf_id=$1 and wfs_conf.wfs_conf_id=wfs_conf_element.fkey_wfs_conf_id and f_search=1;
IF i_search > 0 THEN 
RETURN 1;
else
RETURN 0;
END IF;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
--MetadataViews

DROP VIEW IF EXISTS search_wms_view;

CREATE OR REPLACE VIEW search_wms_view AS 
 SELECT DISTINCT ON (wms_unref.layer_id) wms_unref.wms_id, wms_unref.availability, wms_unref.status, wms_unref.wms_title, wms_unref.wms_abstract, wms_unref.stateorprovince, wms_unref.country, wms_unref.accessconstraints, wms_unref.termsofuse, wms_unref.wms_owner, wms_unref.layer_id, wms_unref.epsg, wms_unref.layer_title, wms_unref.layer_abstract, wms_unref.layer_name, wms_unref.layer_parent, wms_unref.layer_pos, wms_unref.layer_queryable, wms_unref.load_count, wms_unref.searchtext, wms_unref.wms_timestamp, wms_unref.department, wms_unref.mb_group_name, f_collect_custom_cat_layer(wms_unref.layer_id) AS md_custom_cats, f_collect_inspire_cat_layer(wms_unref.layer_id) AS md_inspire_cats, f_collect_topic_cat_layer(wms_unref.layer_id) AS md_topic_cats, st_geometryfromtext(((((((((((((((((((('POLYGON(('::text || layer_epsg.minx::text) || ' '::text) || layer_epsg.miny::text) || ','::text) || layer_epsg.minx::text) || ' '::text) || layer_epsg.maxy::text) || ','::text) || layer_epsg.maxx::text) || ' '::text) || layer_epsg.maxy::text) || ','::text) || layer_epsg.maxx::text) || ' '::text) || layer_epsg.miny::text) || ','::text) || layer_epsg.minx::text) || ' '::text) || layer_epsg.miny::text) || '))'::text, 4326) AS the_geom, (((((layer_epsg.minx::text || ','::text) || layer_epsg.miny::text) || ','::text) || layer_epsg.maxx::text) || ','::text) || layer_epsg.maxy::text AS bbox, wms_unref.wms_proxylog, wms_unref.wms_network_access, wms_unref.wms_pricevolume, wms_unref.mb_group_logo_path
   FROM ( SELECT wms_uncat.wms_id, wms_uncat.availability, wms_uncat.status, wms_uncat.wms_title, wms_uncat.wms_abstract, wms_uncat.stateorprovince, wms_uncat.country, wms_uncat.accessconstraints, wms_uncat.termsofuse, wms_uncat.wms_owner, wms_uncat.layer_id, wms_uncat.epsg, wms_uncat.layer_title, wms_uncat.layer_abstract, wms_uncat.layer_name, wms_uncat.layer_parent, wms_uncat.layer_pos, wms_uncat.layer_queryable, wms_uncat.load_count, wms_uncat.searchtext, wms_uncat.wms_timestamp, wms_uncat.department, wms_uncat.mb_group_name, wms_uncat.wms_proxylog, wms_uncat.wms_network_access, wms_uncat.wms_pricevolume, wms_uncat.mb_group_logo_path
           FROM ( SELECT wms_dep.wms_id, wms_dep.availability, wms_dep.status, wms_dep.wms_title, wms_dep.wms_abstract, wms_dep.stateorprovince, wms_dep.country, wms_dep.accessconstraints, wms_dep.termsofuse, wms_dep.wms_owner, layer.layer_id, f_collect_epsg(layer.layer_id) AS epsg, layer.layer_title, layer.layer_abstract, layer.layer_name, layer.layer_parent, layer.layer_pos, layer.layer_queryable, f_layer_load_count(layer.layer_id) AS load_count, f_collect_searchtext(wms_dep.wms_id, layer.layer_id) AS searchtext, wms_dep.wms_timestamp, wms_dep.department, wms_dep.mb_group_name, wms_dep.wms_proxylog, wms_dep.wms_network_access, wms_dep.wms_pricevolume, wms_dep.mb_group_logo_path
                   FROM ( SELECT wms.wms_id, wms.wms_title, wms.wms_abstract, wms.stateorprovince, wms.country, mb_wms_availability.availability, mb_wms_availability.last_status AS status, wms.accessconstraints, f_getwms_tou(wms.wms_id) AS termsofuse, wms.wms_timestamp, wms.wms_owner, wms.wms_proxylog, wms.wms_network_access, wms.wms_pricevolume, user_dep.fkey_mb_group_id AS department, user_dep.fkey_mb_group_id, user_dep.fkey_mb_group_id AS wms_department, user_dep.mb_group_name,  user_dep.mb_group_logo_path
                           FROM (SELECT registrating_groups.fkey_mb_user_id AS fkey_mb_user_id, mb_group.mb_group_id as fkey_mb_group_id , mb_group.mb_group_name, mb_group.mb_group_title, mb_group.mb_group_country, mb_group.mb_group_stateorprovince, mb_group.mb_group_logo_path
                   FROM registrating_groups, mb_group
                  WHERE registrating_groups.fkey_mb_group_id = mb_group.mb_group_id) user_dep, wms, mb_wms_availability
                          WHERE wms.wms_owner = user_dep.fkey_mb_user_id AND wms.wms_id = mb_wms_availability.fkey_wms_id) wms_dep, layer
                  WHERE layer.fkey_wms_id = wms_dep.wms_id AND layer.layer_searchable = 1) wms_uncat) wms_unref, layer_epsg
  WHERE layer_epsg.epsg::text = 'EPSG:4326'::text AND wms_unref.layer_id = layer_epsg.fkey_layer_id
  ORDER BY wms_unref.layer_id;

DROP VIEW IF EXISTS search_wfs_view;
-- View: search_wfs_view
CREATE OR REPLACE VIEW search_wfs_view AS 
 SELECT wfs_dep.wfs_id, wfs_dep.wfs_title, wfs_dep.wfs_abstract, wfs_dep.administrativearea, wfs_dep.country, wfs_dep.accessconstraints, wfs_dep.termsofuse, wfs_dep.wfs_owner, wfs_featuretype.featuretype_id, wfs_featuretype.featuretype_srs, wfs_featuretype.featuretype_title, wfs_featuretype.featuretype_abstract, f_collect_searchtext_wfs(wfs_dep.wfs_id, wfs_featuretype.featuretype_id) AS searchtext, wfs_element.element_type, wfs_conf.wfs_conf_id, wfs_conf.wfs_conf_abstract, wfs_conf.wfs_conf_description, f_getwfsmodultype(wfs_conf.wfs_conf_id) AS modultype, wfs_dep.wfs_timestamp, wfs_dep.department, wfs_dep.mb_group_name, wfs_dep.mb_group_logo_path
   FROM ( SELECT wfs.wfs_id, wfs.wfs_title, wfs.wfs_abstract, wfs.administrativearea, wfs.country, wfs.accessconstraints, f_getwfs_tou(wfs.wfs_id) AS termsofuse, wfs.wfs_timestamp, wfs.wfs_owner, user_dep.mb_group_id AS department, user_dep.mb_group_name, user_dep.mb_group_logo_path
           FROM ( SELECT registrating_groups.fkey_mb_user_id AS mb_user_id, mb_group.mb_group_id, mb_group.mb_group_name, mb_group.mb_group_logo_path
                   FROM registrating_groups, mb_group
                  WHERE registrating_groups.fkey_mb_group_id = mb_group.mb_group_id) user_dep, wfs
          WHERE user_dep.mb_user_id = wfs.wfs_owner) wfs_dep, wfs_featuretype, wfs_element, wfs_conf
  WHERE wfs_featuretype.fkey_wfs_id = wfs_dep.wfs_id AND wfs_featuretype.featuretype_searchable = 1 AND wfs_element.element_type::text ~~ '%Type'::text AND wfs_featuretype.featuretype_id = wfs_element.fkey_featuretype_id AND wfs_featuretype.featuretype_id = wfs_conf.fkey_featuretype_id
  ORDER BY wfs_featuretype.featuretype_id;

  
-- Table: content_metadata

-- DROP TABLE content_metadata;

CREATE TABLE content_metadata
(
  metadata_id serial NOT NULL,
  link_type character varying(255),
  schemaid character varying(32) NOT NULL,
  origin integer NOT NULL DEFAULT 1,
  createdate integer,
  changedate integer,
  data text NOT NULL,
  link character varying(255),
  harvestresult integer,
  harvestexception text,
  uuid character varying(250),
  title character varying(255),
  abstract text,
  temp_reference_1 integer,
  temp_reference_2 integer,
  spatial_res_type integer,
  spatial_res_value character varying(20),
  ref_system character varying(20),
  format text,
  inspire_charset character varying(10),
  inspire_top_consistance boolean,
  CONSTRAINT metadata_pkey PRIMARY KEY (metadata_id),
  CONSTRAINT metadata_uuid_key UNIQUE (uuid)
);





-- Table: ows_relation_metadata

-- DROP TABLE ows_relation_metadata;

CREATE TABLE ows_relation_metadata
(
  fkey_metadata_id integer NOT NULL,
  fkey_layer_id integer NOT NULL,
  fkey_featuretype_id integer NOT NULL,
  CONSTRAINT ows_relation_metadata_fkey_layer_id_fkey FOREIGN KEY (fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ows_relation_metadata_fkey_featuretype_id_fkey FOREIGN KEY (fkey_featuretype_id)
      REFERENCES wfs_featuretype (featuretype_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT ows_relation_metadata_fkey_metadata_id_fkey FOREIGN KEY (fkey_metadata_id)
      REFERENCES content_metadata (metadata_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

----  
-------

-- add CSS for resultList
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'body', 'resultListCss', '../css/resultList.css', 'css for resultList elements' ,'file/css'
from gui_element
WHERE
gui_element.e_id = 'resultList' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'body' AND var_name = 'resultListCss');

--
-- add element var withPasswordInsertion to element editFilteredUser
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id,
'editFilteredUser', 'withPasswordInsertion', 'true', 'define if admin can set the new user' ,'php_var'
from gui_element
WHERE
gui_element.e_id = 'editFilteredUser' AND
gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE fkey_e_id = 'editFilteredUser' AND var_name = 'withPasswordInsertion');


-- increase size of target field
ALTER TABLE gui_element ALTER COLUMN e_target TYPE VARCHAR(255);
--function to collect the md categories for a single wmc into a column 









--****wmc-begin****
DROP FUNCTION IF EXISTS f_collect_searchtext_wmc(character varying) CASCADE;
DROP FUNCTION IF EXISTS f_collect_topic_cat_wmc(character varying) CASCADE;

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
   l_result := replace(replace(replace(replace(l_result,'Ä','AE'),'ß','SS'),'Ö','OE'),'Ü','UE');

    RETURN l_result;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
--ALTER FUNCTION f_collect_searchtext_wmc(integer) OWNER TO postgres;








--adoptions for wmc categories
CREATE TABLE wmc_custom_category
(
  fkey_wmc_serial_id integer NOT NULL,
  fkey_custom_category_id integer NOT NULL,
  CONSTRAINT wmc_custom_category_fkey_wmc_serial_id_fkey FOREIGN KEY (fkey_wmc_serial_id)
      REFERENCES mb_user_wmc (wmc_serial_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wmc_custom_category_fkey_custom_category_id_fkey FOREIGN KEY (fkey_custom_category_id)
      REFERENCES custom_category (custom_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


--adopt relation for old implementations:
--create a new one and drop the old!

--new categories for publishing -> custom , inspire ?
-- Table: wmc_custom_category
CREATE TABLE wmc_md_topic_category
(
  fkey_wmc_serial_id integer NOT NULL,
  fkey_md_topic_category_id integer NOT NULL,
  CONSTRAINT wmc_topic_category_fkey_wmc_serial_id_fkey FOREIGN KEY (fkey_wmc_serial_id)
      REFERENCES mb_user_wmc (wmc_serial_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wmc_topic_category_fkey_md_topic_category_id_fkey FOREIGN KEY (fkey_md_topic_category_id)
      REFERENCES md_topic_category (md_topic_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


--new categories for publishing -> custom , inspire ?
-- Table: wmc_custom_category
-- allready done above
-- CREATE TABLE wmc_custom_category
-- (
  -- fkey_wmc_serial_id integer NOT NULL,
  -- fkey_custom_category_id integer NOT NULL,
  -- CONSTRAINT wmc_custom_category_fkey_wmc_serial_id_fkey FOREIGN KEY (fkey_wmc_serial_id)
      -- REFERENCES mb_user_wmc (wmc_serial_id) MATCH SIMPLE
      -- ON UPDATE CASCADE ON DELETE CASCADE,
  -- CONSTRAINT wmc_custom_category_fkey_custom_category_id_fkey FOREIGN KEY (fkey_custom_category_id)
      -- REFERENCES custom_category (custom_category_id) MATCH SIMPLE
      -- ON UPDATE CASCADE ON DELETE CASCADE
-- );

-- Table: wmc_inspire_category
CREATE TABLE wmc_inspire_category
(
  fkey_wmc_serial_id integer NOT NULL,
  fkey_inspire_category_id integer NOT NULL,
  CONSTRAINT wmc_inspire_category_fkey_wmc_serial_id_fkey FOREIGN KEY (fkey_wmc_serial_id)
      REFERENCES mb_user_wmc (wmc_serial_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wmc_inspire_category_fkey_inspire_category_id_fkey FOREIGN KEY (fkey_inspire_category_id)
      REFERENCES inspire_category (inspire_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

--functions to collect the categories into strings
CREATE OR REPLACE FUNCTION f_collect_custom_cat_wmc(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_wmc_serial_id ALIAS FOR $1;
  custom_cat_string  TEXT;
  custom_cat_record  RECORD;
BEGIN
custom_cat_string := '';
FOR custom_cat_record IN SELECT wmc_custom_category.fkey_custom_category_id from wmc_custom_category WHERE wmc_custom_category.fkey_wmc_serial_id=$1  LOOP
custom_cat_string := custom_cat_string || '{' ||custom_cat_record.fkey_custom_category_id || '}';
END LOOP ;
RETURN custom_cat_string;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;
--ALTER FUNCTION f_collect_custom_cat_wmc(integer) OWNER TO postgres;
--functions to collect the categories into strings
CREATE OR REPLACE FUNCTION f_collect_inspire_cat_wmc(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_wmc_serial_id ALIAS FOR $1;
  inspire_cat_string  TEXT;
  inspire_cat_record  RECORD;
BEGIN
inspire_cat_string := '';
FOR inspire_cat_record IN SELECT wmc_inspire_category.fkey_inspire_category_id from wmc_inspire_category WHERE wmc_inspire_category.fkey_wmc_serial_id=$1  LOOP
inspire_cat_string := inspire_cat_string || '{' ||inspire_cat_record.fkey_inspire_category_id || '}';
END LOOP ;
RETURN inspire_cat_string;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;
--ALTER FUNCTION f_collect_inspire_cat_wmc(integer) OWNER TO postgres;

CREATE OR REPLACE FUNCTION f_collect_topic_cat_wmc(integer)
  RETURNS text AS
  $BODY$DECLARE
  i_wmc_serial_id ALIAS FOR $1;
  topic_cat_string  TEXT;
  topic_cat_record  RECORD;

BEGIN
topic_cat_string := '';

FOR topic_cat_record IN SELECT wmc_md_topic_category.fkey_md_topic_category_id from wmc_md_topic_category WHERE wmc_md_topic_category.fkey_wmc_serial_id=$1  LOOP
topic_cat_string := topic_cat_string || '{' ||topic_cat_record.fkey_md_topic_category_id || '}';
END LOOP ;
  
RETURN topic_cat_string;

END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE STRICT;
--ALTER FUNCTION f_collect_topic_cat_wmc(integer) OWNER TO postgres;

--*******wmc end*****



--set mb_user_mb_group_type
--of mb_user_mb_group to 1 for root!

UPDATE mb_user_mb_group SET mb_user_mb_group_type = 1 WHERE fkey_mb_user_id = 1 AND fkey_mb_group_id = 20;
--generate demo group for decentral registrating and publishing services

-- View: search_wmc_view

DROP VIEW IF EXISTS search_wmc_view;
--TODO: get group information out of mb_group table instead of ows information, cause in this case there can be more than one different service in a resource 
--TODO: set public flag to some wmc docs to generate usefull results
--TODO: get infos from old wmc docs into table structure (abstract, coords, ...)
-- View: search_wmc_view

-- DROP VIEW search_wmc_view;

CREATE OR REPLACE VIEW search_wmc_view AS 
 SELECT wmc_dep.fkey_user_id AS user_id, wmc_dep.wmc_id, wmc_dep.srs AS wmc_srs, wmc_dep.wmc_title, wmc_dep.abstract AS wmc_abstract, f_collect_searchtext_wmc(wmc_dep.wmc_id) AS searchtext, wmc_dep.wmc_timestamp, wmc_dep.department, wmc_dep.mb_group_name, wmc_dep.mb_group_title, wmc_dep.mb_group_country, wmc_dep.wmc_serial_id, wmc_dep.mb_group_stateorprovince, f_collect_inspire_cat_wmc(wmc_dep.wmc_serial_id) AS md_inspire_cats, f_collect_custom_cat_wmc(wmc_dep.wmc_serial_id) AS md_custom_cats, f_collect_topic_cat_wmc(wmc_dep.wmc_id) AS md_topic_cats, st_transform(st_geometryfromtext(((((((((((((((((((('POLYGON(('::text || wmc_dep.minx::text) || ' '::text) || wmc_dep.miny::text) || ','::text) || wmc_dep.minx::text) || ' '::text) || wmc_dep.maxy::text) || ','::text) || wmc_dep.maxx::text) || ' '::text) || wmc_dep.maxy::text) || ','::text) || wmc_dep.maxx::text) || ' '::text) || wmc_dep.miny::text) || ','::text) || wmc_dep.minx::text) || ' '::text) || wmc_dep.miny::text) || '))'::text, regexp_replace(upper(wmc_dep.srs::text), 'EPSG:'::text, ''::text)::integer), 4326) AS the_geom, (((((wmc_dep.minx::text || ','::text) || wmc_dep.miny::text) || ','::text) || wmc_dep.maxx::text) || ','::text) || wmc_dep.maxy::text AS bbox, wmc_dep.mb_group_logo_path
   FROM ( SELECT mb_user_wmc.wmc_public, mb_user_wmc.maxy, mb_user_wmc.maxx, mb_user_wmc.miny, mb_user_wmc.minx, mb_user_wmc.srs, mb_user_wmc.wmc_serial_id AS wmc_id, mb_user_wmc.wmc_serial_id, mb_user_wmc.wmc_title, mb_user_wmc.abstract, mb_user_wmc.wmc_timestamp, mb_user_wmc.fkey_user_id, user_dep.mb_group_id AS department, user_dep.mb_group_name, user_dep.mb_group_title, user_dep.mb_group_country, user_dep.mb_group_stateorprovince, user_dep.mb_group_logo_path
           FROM ( SELECT registrating_groups.fkey_mb_user_id AS mb_user_id, mb_group.mb_group_id, mb_group.mb_group_name, mb_group.mb_group_title, mb_group.mb_group_country, mb_group.mb_group_stateorprovince, mb_group.mb_group_logo_path
                   FROM registrating_groups, mb_group
                  WHERE registrating_groups.fkey_mb_group_id = mb_group.mb_group_id) user_dep, mb_user_wmc
          WHERE user_dep.mb_user_id = mb_user_wmc.fkey_user_id) wmc_dep
  WHERE wmc_dep.wmc_public = 1
  ORDER BY wmc_dep.wmc_id;

--ALTER TABLE search_wmc_view OWNER TO postgres;


-- insert deleteWFSConf modul in admin1
--
-- add delete wfs conf module from geoportal.rlp
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin1','deleteWFSConf',2,1,'delete wfs conf','','a','','href = "../javascripts/mod_deleteWfsConf_client.html" target="AdminFrame"',10,1204,250,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','DELETE WFS CONF','a','','','','AdminFrame','');
--

-- element var removeSpatialRequestHighlight for gazetteerWFS
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) SELECT gui_element.fkey_gui_id, 'gazetteerWFS', 'removeSpatialRequestHighlight', '0', 'remove spatialrequest highlighting when firing search' ,'var' FROM gui_element WHERE gui_element.e_id = 'gazetteerWFS' AND gui_element.fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'removeSpatialRequestHighlight' AND fkey_e_id = 'gazetteerWFS');


-- compatibility with UI Layout
UPDATE gui_element SET e_content = '<ul></ul><div class=''ui-layout-content''></div>' WHERE e_id = 'mb_tabs_horizontal';


-- Table: layer_preview

CREATE TABLE layer_preview
(
  fkey_layer_id integer NOT NULL,
  layer_map_preview_filename character varying(100),
  layer_extent_preview_filename character varying(100),
  layer_legend_preview_filename character varying(100),
  CONSTRAINT fkey_layer_id FOREIGN KEY (fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT layer_preview_fkey_layer_id_key UNIQUE (fkey_layer_id)
);

-- Table: termsofuse

CREATE TABLE termsofuse
(
  termsofuse_id integer NOT NULL,
  "name" character varying(255),
  symbollink character varying(255),
  description character varying(255),
  descriptionlink character varying(255),
  CONSTRAINT termsofuse_pkey PRIMARY KEY (termsofuse_id)
);


-- Table: wfs_termsofuse

CREATE TABLE wfs_termsofuse
(
  fkey_wfs_id integer,
  fkey_termsofuse_id integer,
  CONSTRAINT wfs_termsofuse_fkey_wfs_id_fkey FOREIGN KEY (fkey_wfs_id)
      REFERENCES wfs (wfs_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wfs_termsofuse_termsofuse_fkey FOREIGN KEY (fkey_termsofuse_id)
      REFERENCES termsofuse (termsofuse_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: wms_termsofuse

CREATE TABLE wms_termsofuse
(
  fkey_wms_id integer,
  fkey_termsofuse_id integer,
  CONSTRAINT wms_termsofuse_termsofuse_fkey FOREIGN KEY (fkey_termsofuse_id)
      REFERENCES termsofuse (termsofuse_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wms_termsofuse_wms_fkey FOREIGN KEY (fkey_wms_id)
      REFERENCES wms (wms_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: layer_md_topic_category

CREATE TABLE layer_md_topic_category
(
  fkey_layer_id integer NOT NULL,
  fkey_md_topic_category_id integer NOT NULL,
  CONSTRAINT layer_md_topic_category_fkey_layer_id_fkey FOREIGN KEY 
(fkey_layer_id)
      REFERENCES layer (layer_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT layer_md_topic_category_fkey_md_topic_category_id_fkey FOREIGN 
KEY (fkey_md_topic_category_id)
      REFERENCES md_topic_category (md_topic_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- terms of use content

INSERT INTO termsofuse (termsofuse_id, name, symbollink, description, descriptionlink) VALUES (1, 'CC by-nc-nd','http://i.creativecommons.org/l/by-nc-nd/3.0/de/88x31.png','Creative Commons: Namensnennung - Keine kommerzielle Nutzung - Keine Bearbeitungen 3.0 Deutschland','http://creativecommons.org/licenses/by-nc-nd/3.0/de/');

--update to jQuery 1.4.2 and jQuery UI 1.8.1
DELETE FROM gui_element WHERE e_id IN ('jq_ui_widget','jq_ui_mouse','jq_ui_position');

UPDATE gui_element SET e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js' WHERE e_id = 'jq_ui';

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) SELECT fkey_gui_id,'jq_ui_widget',2,1,'jQuery UI widget','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js','','','' FROM gui_element WHERE e_id = 'jq_ui' AND fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui_widget');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) SELECT fkey_gui_id,'jq_ui_mouse',3,1,'jQuery UI mouse','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js','','jq_ui_widget','' FROM gui_element WHERE e_id = 'jq_ui_widget' AND fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui_mouse');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) SELECT fkey_gui_id,'jq_ui_position',2,1,'jQuery UI position','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js','','','' FROM gui_element WHERE e_id = 'jq_ui_widget' AND fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui_position');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES ('toolbox_jquery','jq_ui_autocomplete',4,1,'jQuery UI autocomplete','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.autocomplete.min.js','','jq_ui,jq_ui_widget,jq_ui_position','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES ('toolbox_jquery','jq_ui_progressbar',4,1,'jQuery UI progressbar','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.progressbar.min.js','','jq_ui,jq_ui_widget','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES ('toolbox_jquery','jq_ui_selectable',4,1,'jQuery UI selectable','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.selectable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES ('toolbox_jquery','jq_ui_sortable',4,1,'jQuery UI sortable','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.sortable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse','');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) SELECT fkey_gui_id,'jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.button.min.js','','jq_ui,jq_ui_widget','' FROM gui_element WHERE e_id = 'jq_ui_dialog' AND fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui_button');

INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) SELECT fkey_gui_id,'jq_ui_droppable',4,1,'jQuery UI droppable','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse,jq_ui_draggable','' FROM gui_element WHERE e_id = 'jq_ui_draggable' AND fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'jq_ui_droppable');


UPDATE gui_element SET e_requires = 'jq_ui,jq_ui_widget', e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.accordion.min.js' WHERE e_id = 'jq_ui_accordion';
UPDATE gui_element SET e_requires = 'jq_ui', e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.datepicker.min.js' WHERE e_id = 'jq_ui_datepicker';
UPDATE gui_element SET e_requires = 'jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable', e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js' WHERE e_id = 'jq_ui_dialog';
UPDATE gui_element SET e_requires = 'jq_ui,jq_ui_mouse,jq_ui_widget', e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js' WHERE e_id = 'jq_ui_draggable';
UPDATE gui_element SET e_requires = 'jq_ui,jq_ui_mouse,jq_ui_widget', e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.resizable.min.js' WHERE e_id = 'jq_ui_resizable';
UPDATE gui_element SET e_requires = 'jq_ui,jq_ui_mouse,jq_ui_widget', e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.slider.min.js' WHERE e_id = 'jq_ui_slider';
UPDATE gui_element SET e_requires = 'jq_ui,jq_ui_widget', e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js' WHERE e_id = 'jq_ui_tabs';
UPDATE gui_element SET e_pos = 1 WHERE e_id = 'jq_ui_widget';
UPDATE gui_element SET e_pos = 1 WHERE e_id = 'jq_ui_position';
UPDATE gui_element SET e_pos = 2 WHERE e_id = 'jq_ui_mouse';

UPDATE gui_element SET e_mb_mod = '../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.effects.core.min.js' WHERE e_id = 'jq_ui_effects';

-- saveWmcTarget for element loadwmc
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'loadwmc', 'saveWmcTarget', 'savewmc', '' ,'var'
FROM gui_element WHERE gui_element.e_id = 'loadwmc' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'saveWmcTarget'
AND fkey_e_id = 'loadwmc');


-- color pink in body had effect on other elements
UPDATE gui_element_vars 
set var_value = 'digitizeGeometryList {position:absolute; top:50px; left:0px;}
.digitizeGeometryListItem {color:#000000; font-size:10px;}
body {font-family: Arial, Helvetica, sans-serif; font-size:12px; background-color:#ffffff; margin-top: 0px; margin-left:0px;}
.button {height:18px; width:32px;}'
where fkey_gui_id = 'gui_digitize' and fkey_e_id = 'digitize'
and var_name = 'text css';


-- more fields in mb_user
alter table mb_user add column mb_user_firstname character varying(255) DEFAULT ''::character varying;
alter table mb_user add column mb_user_lastname character varying(255) DEFAULT ''::character varying;
alter table mb_user add column mb_user_academictitle character varying(255) DEFAULT ''::character varying;

-- mapframe1: initWms.php is replaced by initWmcObj.php
UPDATE gui_element SET e_mb_mod = '../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWmcObj.php' WHERE e_id = 'mapframe1';
-- overview: initWms.php is replaced by initWmcObj.php
UPDATE gui_element SET e_mb_mod = '../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWmcObj.php' WHERE e_id = 'overview';

-- Delete layer_id in layer_load_count that do not exist in layer before constraint is created
DELETE FROM layer_load_count WHERE not exists ( SELECT layer.layer_id 
FROM layer WHERE layer_load_count.fkey_layer_id = layer.layer_id );

-- missing foreign key in layer_load_count
ALTER TABLE ONLY layer_load_count
    ADD CONSTRAINT layer_load_count_fkey_layer_id_fkey FOREIGN KEY (fkey_layer_id) REFERENCES layer (layer_id) ON UPDATE CASCADE ON DELETE CASCADE;

-- adds Mapbender favicon to all applications whch do not have favicon yet
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) SELECT gui_element.fkey_gui_id, 'body', 'favicon', '../img/favicon.ico', 'refers to image to use as favicon' ,'php_var' 
FROM gui_element WHERE gui_element.e_id = 'body' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'favicon'
AND fkey_e_id = 'body');

-- use favicon.ico instead of favicon.png
UPDATE gui_element_vars set var_value = '../img/favicon.ico'
WHERE var_name = 'favicon'
AND fkey_e_id = 'body'
AND var_value = '../img/favicon.png' and (fkey_gui_id IN ('gui', 'gui1', 'gui_digitize', 'gui2') OR fkey_gui_id LIKE 'admin%' OR fkey_gui_id LIKE 'wms%');

-- tabs - expandable
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) SELECT gui_element.fkey_gui_id, 
'tabs', 'expandable', '0', '1 = expand the tabs to fit the document vertically, default is 0' ,'php_var'
FROM gui_element WHERE gui_element.e_id = 'tabs' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'expandable'
AND fkey_e_id = 'tabs');

-- draggable in map applications 
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/'
FROM gui WHERE gui.gui_id NOT LIKE 'admin%' AND gui.gui_id NOT LIKE 'wms%' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'jq_ui_draggable');


-- resize map applications 
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url)
SELECT gui.gui_id,'jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.resizable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/resizable/'
FROM gui WHERE gui.gui_id NOT LIKE 'admin%' AND gui.gui_id NOT LIKE 'wms%' AND gui.gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element
WHERE e_id = 'jq_ui_resizable');

-- add new column gui_layer_title
alter table gui_layer add column gui_layer_title character varying(255) NOT NULL DEFAULT ''::character varying;
-- fill new column gui_layer_title with layer_titles from layer, where gui_layer_title is ''
Update gui_layer set 
gui_layer_title = l.layer_title from layer l where
gui_layer_title = '' AND fkey_layer_id = l.layer_id;

--adoptions for managing georss/kml urls and data in the mapbender database
-- Table: datalink

-- DROP TABLE datalink;

CREATE TABLE datalink
(
  datalink_id serial NOT NULL,
  datalink_type character varying(50) NOT NULL DEFAULT ''::character varying, --georss,kml,gml,geojson
  datalink_type_version character varying(50) NOT NULL DEFAULT ''::character varying, --1.0,3.x?
  datalink_url text,
  datalink_owner integer,
  datalink_timestamp integer,
  datalink_timestamp_create integer,
  datalink_timestamp_last_usage integer,
  datalink_abstract text,
  datalink_title character varying(255) NOT NULL DEFAULT ''::character varying,
  datalink_data text, --maybe we want a data access for inspire and the data should stand on the geoportal cause the institutions have no webserver	
  --the following things have s.th. todo with metadata and maybe edited or filled or may not be present
  datalink_network_access integer,
  datalink_owsproxy character varying(50),
  fees character varying(255),
  accessconstraints text,
  crs character varying(50) NOT NULL DEFAULT ''::character varying,
  minx double precision DEFAULT 0,
  miny double precision DEFAULT 0,
  maxx double precision DEFAULT 0,
  maxy double precision DEFAULT 0,
  CONSTRAINT pk_datalink_id PRIMARY KEY (datalink_id),
--delete orphaned datalinks automatically
  CONSTRAINT datalink_owner_fkey FOREIGN KEY (datalink_owner)
    REFERENCES mb_user (mb_user_id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE CASCADE
);


-- Table: datalink_keyword

-- DROP TABLE datalink_keyword;

CREATE TABLE datalink_keyword
(
  fkey_datalink_id integer NOT NULL,
  fkey_keyword_id integer NOT NULL,
  CONSTRAINT pk_datalink_keyword PRIMARY KEY (fkey_datalink_id, fkey_keyword_id),
  CONSTRAINT fkey_keyword_id_fkey_datalink_id FOREIGN KEY (fkey_keyword_id)
      REFERENCES keyword (keyword_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fkey_datalink_id_fkey_keyword_id FOREIGN KEY (fkey_datalink_id)
      REFERENCES datalink (datalink_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table: datalink_md_topic_category

-- DROP TABLE datalink_md_topic_category;

CREATE TABLE datalink_md_topic_category
(
  fkey_datalink_id integer NOT NULL,
  fkey_md_topic_category_id integer NOT NULL,
  CONSTRAINT datalink_md_topic_category_fkey_datalink_id_fkey FOREIGN KEY (fkey_datalink_id)
      REFERENCES datalink (datalink_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT datalink_md_topic_category_fkey_md_topic_category_id_fkey FOREIGN KEY (fkey_md_topic_category_id)
      REFERENCES md_topic_category (md_topic_category_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);


--
-- add new zoomto coords to gui as a new tab
--


INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment,
e_title, e_element, e_src, e_attributes, e_left, e_top, e_width,
e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file,
e_mb_mod, e_target, e_requires, e_url) VALUES ('gui','coordsLookup',
10,1,'','Coordinate lookup','div','','',1,1,NULL ,NULL ,NULL ,
'z-index:9999;visibility:hidden','','div','mod_coordsLookup.php','',
'mapframe1','','http://www.mapbender.org/coordsLookup');
INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name,
var_value, context, var_type) VALUES ('gui', 'coordsLookup',
'perimeters', '[50,200,1000,10000]', '' ,'var');
INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name,
var_value, context, var_type) VALUES('gui', 'coordsLookup',
'projections', '[''EPSG:4326'',''EPSG:31467'',''EPSG:31468'',''EPSG:31469'',''EPSG:25832'',''EPSG:25833'']', 
'' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'tabs', 'tab_frameHeight[7]', '240', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'tabs', 'tab_ids[7]', 'coordsLookup', '' ,'php_var');
Update gui_element set e_js_file = 'mod_coordsLookup.php' where  e_id = 'coordsLookup';





-- element var saveInSession for gui element savewmc
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type)
SELECT gui_element.fkey_gui_id, 'savewmc', 'saveInSession', '0', '' ,'var'
FROM gui_element WHERE gui_element.e_id = 'savewmc' AND gui_element.fkey_gui_id
NOT IN (SELECT fkey_gui_id FROM gui_element_vars
WHERE var_name = 'saveInSession' AND fkey_e_id = 'savewmc');
-- previously, this had been a php var
UPDATE gui_element_vars SET var_type = 'var' WHERE var_name = 'saveInSession' AND fkey_e_id = 'savewmc';

--
-- remove filter:Chroma(color=#C2CBCF); from styles 
-- see Ticket http://trac.osgeo.org/mapbender/ticket/697
--
UPDATE gui_element set e_more_styles=replace(e_more_styles, 'filter:Chroma(color=#C2CBCF);', '');

--
--Add new Mapbender Logo to gui_2
--
DELETE FROM gui_element WHERE fkey_gui_id='gui2' AND e_id='logo';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui2','logo',2,1,'Logo','Logo','img','../img/Mapbender_logo_and_text.png','',40,38,120,30,5,'','','','','','','','');

--
--Add new Mapbender Logo to gui
--
DELETE FROM gui_element WHERE fkey_gui_id='gui' AND e_id='logo';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui','logo',2,1,'Logo','Logo','img','../img/Mapbender_logo_and_text_200x50.png','',10,10,200,50,5,'','','','','','','','');

--
--Styling fix of legend in gui_1
--
DELETE FROM gui_element WHERE fkey_gui_id='gui1' AND e_id='legend'; 
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','legend',2,1,'legend','Legend','iframe','../javascripts/mod_legend.php?sessionID&wms_legend = yes','frameborder=''0''',-59,100,180,600,3,'','','iframe','','','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'legend', 'checkbox_on_off', 'false', 'display or hide the checkbox to set the legend on/off' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'legend', 'css_file_legend', '../css/legend.css', '' ,'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'legend', 'legendlink', 'false', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'legend', 'showgroupedlayertitle', 'true', 'show the title of the grouped layers in the legend' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'legend', 'showlayertitle', 'true', 'show the layer title in the legend' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'legend', 'showwmstitle', 'true', 'show the wms title in the legend' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'legend', 'stickylegend', 'true', 'parameter to decide wether the legend should stick on the mapframe1' ,'var');

--
--Add drag Mapsize Button (JqueryUI Style) to gui, gui1,gui_2 and gui_digitize
--
DELETE FROM gui_element WHERE fkey_gui_id='gui' AND e_id='dragMapSize'; 
DELETE FROM gui_element WHERE fkey_gui_id='gui1' AND e_id='dragMapSize'; 
DELETE FROM gui_element WHERE fkey_gui_id='gui2' AND e_id='dragMapSize';
DELETE FROM gui_element WHERE fkey_gui_id='gui_digitize' AND e_id='dragMapSize';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui','dragMapSize',2,1,'drag & drop Mapsize','Drag Mapsize','div','','class="ui-state-default"',-59,1,15,15,2,'font-size:1px; cursor:move;','<span class="ui-icon ui-icon ui-icon-arrow-4"></span>','div','mod_dragMapSize.php','','mapframe1','','http://www.mapbender.org/index.php/DragMapSize');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','dragMapSize',2,1,'drag & drop Mapsize','Drag Mapsize','div','','class="ui-state-default"',-59,1,15,15,2,'font-size:1px; cursor:move;','<span class="ui-icon ui-icon ui-icon-arrow-4"></span>','div','mod_dragMapSize.php','','mapframe1','','http://www.mapbender.org/index.php/DragMapSize');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui2','dragMapSize',2,1,'drag & drop Mapsize','Drag Mapsize','div','','class="ui-state-default"',-59,1,15,15,2,'font-size:1px; cursor:move;','<span class="ui-icon ui-icon ui-icon-arrow-4"></span>','div','mod_dragMapSize.php','','mapframe1','','http://www.mapbender.org/index.php/DragMapSize');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui_digitize','dragMapSize',2,1,'drag & drop Mapsize','Drag Mapsize','div','','class="ui-state-default"',-59,1,15,15,2,'font-size:1px; cursor:move;','<span class="ui-icon ui-icon ui-icon-arrow-4"></span>','div','mod_dragMapSize.php','','mapframe1','','http://www.mapbender.org/index.php/DragMapSize');


--
-- Add layout to toolbox jquery
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('toolbox_jquery','jq_layout',1,1,'This jQuery plugin can create a sophisticated pane layout ','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery.layout.all-1.2.0/jquery.layout.min.js','','','http://layout.jquery-dev.net');

--
-- Now index.php loads all CSS not in iframes, they do no longer have to be bound to body
--
DELETE FROM gui_element_vars WHERE var_name = 'cssfileAddWMS' AND fkey_e_id = 'addWMSfromfilteredList_ajax';

--
-- Remove class ui-widget-content from treeGDE
--
UPDATE gui_element SET e_attributes = 'class="ui-widget"' WHERE e_id = 'treeGDE';

--
-- Move treeGDE CSS to treeGDE
--
INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) SELECT gui_element.fkey_gui_id, 'treeGDE', 'css', '../css/treeGDE2.css', '' ,'file/css' FROM gui_element WHERE gui_element.e_id = 'treeGDE';
DELETE FROM gui_element_vars WHERE fkey_e_id = 'body' AND var_name = 'treeGDE_css';

--
-- Add pane CSS
--
INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) SELECT DISTINCT gui_element.fkey_gui_id, gui_element.e_id, 'css' AS var_name, '../extensions/jquery.layout.all-1.2.0/jquery.layout.css' AS var_value, '' AS context, 'file/css' AS var_type FROM gui_element, gui_element_vars WHERE fkey_e_id = e_id AND gui_element.e_js_file = '../plugins/mb_pane.js';

--
-- Move CSS from configuration to file (frame)
--
DELETE FROM gui_element_vars WHERE fkey_e_id = 'editGUI_WMS' AND var_name = 'css_file';
DELETE FROM gui_element_vars WHERE fkey_e_id = 'EditWMSMetadata' AND var_name = 'file_css';


-- add a column group to the service tables to allow special group relation for metadataPointOfContact - which can be another group than the one from the owner of the service. The fkey has explicitly no special constraint! 
-- Column: fkey_mb_group_id

-- ALTER TABLE wms DROP COLUMN fkey_mb_group_id;

ALTER TABLE wms ADD COLUMN fkey_mb_group_id integer;
ALTER TABLE wms ALTER COLUMN fkey_mb_group_id SET STORAGE PLAIN;

-- Column: fkey_mb_group_id

-- ALTER TABLE wms DROP COLUMN fkey_mb_group_id;

ALTER TABLE wfs ADD COLUMN fkey_mb_group_id integer;
ALTER TABLE wfs ALTER COLUMN fkey_mb_group_id SET STORAGE PLAIN;



--
-- Add homepage to mb_group
--
ALTER TABLE mb_group ADD COLUMN mb_group_homepage VARCHAR(255);

--
--UPDATE addCSW (Ticket #682)
--
UPDATE gui_element SET e_attributes='', e_js_file='mod_searchCSW_ajax_button.php' WHERE e_id='addCSW';

-- add possibility to store previews for wmc docs 
-- Table: wmc_preview

-- DROP TABLE wmc_preview;

CREATE TABLE wmc_preview
(
  fkey_wmc_serial_id integer NOT NULL,
  wmc_preview_filename character varying(100),
  CONSTRAINT c_fkey_wmc_serial_id FOREIGN KEY (fkey_wmc_serial_id)
      REFERENCES mb_user_wmc (wmc_serial_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT wmc_fkey_layer_id_key UNIQUE (fkey_wmc_serial_id)
);
--ALTER TABLE wmc_preview OWNER TO postgres;
-- Table wmc_load_count

-- DROP TABLE wmc_load_count;

CREATE TABLE wmc_load_count
(
  fkey_wmc_serial_id integer,
  load_count bigint,
  CONSTRAINT wmc_load_count_fkey_wmc_serial_id_fkey FOREIGN KEY (fkey_wmc_serial_id)
      REFERENCES mb_user_wmc (wmc_serial_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
--ALTER TABLE wmc_load_count OWNER TO postgres;

-- Index: idx_fkey_layer_id

-- DROP INDEX idx_fkey_layer_id;

CREATE INDEX idx_fkey_wmc_serial_id
  ON wmc_load_count
  USING btree
  (fkey_wmc_serial_id);
-- Column: custom_category_hidden

-- ALTER TABLE custom_category DROP COLUMN custom_category_hidden;

ALTER TABLE custom_category ADD COLUMN custom_category_hidden integer;
ALTER TABLE custom_category ALTER COLUMN custom_category_hidden SET STORAGE plain;
-- one special category as options to start mapbender geoportal 
insert into custom_category (custom_category_key, custom_category_code_en,custom_category_code_de,custom_category_hidden) values ('mbc1','special standard wmc documents','Spezielle Standard WMC Dokumente',1);

--
--replace jquery ui minified folder
--resizeable
--
UPDATE gui_element SET e_mb_mod=replace(e_mb_mod,'/minified/','/') where e_id ilike 'jq_ui%' ;
UPDATE gui_element SET e_mb_mod=replace(e_mb_mod,'.min.js','.js') where e_id ilike 'jq_ui%' ;

--
--add RaphaelJS to gui_digitize
--
UPDATE gui_element set e_mb_mod='../extensions/RaphaelJS/raphael-1.4.7.min.js' WHERE e_id ilike 'body' AND fkey_gui_id= 'gui_digitize';

--
--bugfix in loading order of ui modules
--
UPDATE gui_element set e_pos='2' WHERE e_id='jq_ui_position';

-- ########################
--Update Gui Digitize BEGIN
--
--
--remove the old element from gui_digitize
Delete from gui_element_vars where fkey_gui_id='gui_digitize';
Delete from gui_element where fkey_gui_id='gui_digitize';

--Set all WMS to visible because background module is missing in new style
UPDATE gui_wms SET gui_wms_visible='1' where fkey_gui_id='gui_digitize';

--INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('gui_digitize','gui_digitize','application with WFS search and digitizing using WFS-T',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','back',2,1,'History.back()','Back','img','../img/button_blue_red/back_off_disabled.png','',385,8,28,28,1,'','','','mod_back.php','','mapframe1,overview','','http://www.mapbender.org/index.php/Back');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','body',1,1,'body (obligatory)','','body','','',NULL ,NULL ,NULL ,NULL ,NULL ,'background-color:#ffffe0; ','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','copyright',2,1,'a Copyright in the map','Copyright','div','','',0,0,NULL ,NULL ,NULL ,'','','div','mod_copyright.php','','mapframe1','','http://www.mapbender.org/index.php/Copyright');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','dependentDiv',2,1,'displays infos in a sticky div-tag','','div','','',81,-19,1,1,0,'visibility:visible;position:absolute;font-size: 11px;font-family: "Arial", sans-serif;','','div','mod_dependentDiv.php','','mapframe1','','http://www.mapbender.org/index.php/DependentDiv');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','digitize',2,1,'Digitize tool.','Digitize','iframe','../javascripts/mod_digitize_tab.php?sessionID','frameborder = "0" class="ui-widget-content"',1,1,1,1,5,'visibility:hidden;','','iframe','','geometry.js','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','dragMapSize',2,1,'drag & drop Mapsize','Drag Mapsize','div','','class="ui-state-default"',-59,1,15,15,2,'font-size:1px; cursor:move;','<span class="ui-icon ui-icon ui-icon-arrow-4"></span>','div','mod_dragMapSize.php','','mapframe1','','http://www.mapbender.org/index.php/DragMapSize');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','featureInfoTunnel',2,1,'FeatureInfoRequest with local path','Query','img','../img/button_blue_red/query_off.png','onmouseover = "mb_regButton(''init_featureInfoTunnel'')"',488,8,28,28,1,'','','','mod_featureInfoTunnel.php','popup.js','mapframe1','','http://www.mapbender.org/index.php/FeatureInfoTunnel');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','forward',2,1,'History.forward()','Forward','img','../img/button_blue_red/forward_off_disabled.png','',410,8,28,28,1,'','','','mod_forward.php','','mapframe1,overview','','http://www.mapbender.org/index.php/Forward');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','gazetteerWFS',2,1,'a gazetteer for user in the mapbender user map','Search','iframe','../javascripts/mod_wfs_gazetteer_client.php?sessionID&target=mapframe1,overview','frameborder = "0" class="ui-widget-content"',10,600,300,150,4,'visibility:hidden;','','iframe','','geometry.js,requestGeometryConstructor.js,popup.js,../extensions/wz_jsgraphics.js','mapframe1,overview','wz-graphics','http://www.mapbender.org/GazetteerWFS');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','imprint',2,1,'Information about the owner of the gui','Imprint','iframe','../html/tab_imprint.html','frameborder = "0" class="ui-widget-content"',1,1,1,1,5,'visibility:hidden;','','iframe','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.js','','','http://www.datatables.net/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui',1,1,'The jQuery UI core','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.core.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.button.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.dialog.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.draggable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.mouse.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui_position',2,1,'jQuery UI position','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.position.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.resizable.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.tabs.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery.ui.widget.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','jq_upload',1,1,'','','','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','','../plugins/jq_upload.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','layout_InfoButtons',3,0,'layout, background for buttons','','div','','class="ui-widget-header ui-corner-top"',483,5,65,32,NULL ,'','','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','layout_frontBackButtons',3,0,'layout, background for buttons','','div','','class="ui-widget-header ui-corner-top"',385,5,85,32,NULL ,'','','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','layout_logoutButtons',3,0,'layout, background for buttons','','div','','class="ui-widget-header ui-corner-top "',731,5,36,32,NULL ,'','','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','layout_selectButtons',3,0,'layout, background for buttons','','div','','class="ui-widget-header ui-corner-top "',557,5,155,32,NULL ,'','','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','layout_zoomButtons',3,0,'layout, background for buttons','','div','','class="ui-widget-header ui-corner-top "',225,5,150,32,NULL ,'','','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','legend',2,1,'legend','Legend','iframe','../javascripts/mod_legend.php?sessionID&e_id_css=legend','frameborder=''0'' class="ui-widget-content"',1,1,1,1,3,'visibility:hidden; border:1','','iframe','','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','loadData',2,1,'IFRAME, um Daten zu laden','','iframe','../html/mod_blank.html','frameborder = "0" ',0,0,1,1,0,'visibility:visible','','iframe','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','logo',2,1,'Logo','','img','../img/Mapbender_logo_and_text_200x50.png','',10,10,200,50,5,'','','','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','logout',2,1,'Logout','Logout','img','../img/button_blue_red/logout_off.png','onClick="window.location.href=''../php/mod_logout.php?sessionID''" border=''0''
onmouseover=''this.src="../img/button_blue_red/logout_over.png"''
onmouseout=''this.src="../img/button_blue_red/logout_off.png"''',735,8,28,28,1,'','','','','','','','http://www.mapbender.org/index.php/Logout');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','mapbender',2,1,'Mapbender-Logo','','div','','onclick="javascript:window.open(''http://www.mapbender.org'','''','''');"',81,-19,1,1,30,'font-size : 10px;font-weight : bold;font-family: Arial, Helvetica, sans-serif;color:white;cursor:help;','<span>Ma</span><span style="color: blue;">P</span><span style="color: red;">b</span><span>ender</span><script type="text/javascript"> mb_registerSubFunctions("mod_mapbender()"); function mod_mapbender(){ document.getElementById("mapbender").style.left = parseInt(document.getElementById("mapframe1").style.left) + parseInt(document.getElementById("mapframe1").style.width) - 90; document.getElementById("mapbender").style.top = parseInt(document.getElementById("mapframe1").style.top) + parseInt(document.getElementById("mapframe1").style.height) -1; } </script>','div','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','mapframe1',1,1,'frame for a map','','div','','',235,50,525,450,2,'overflow:hidden;background-color:#ffffff','','div','../plugins/mb_map.js','../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php','','','http://www.mapbender.org/index.php/Mapframe');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','navFrame',2,1,'navigation mapborder','Navigation Frame','div','','class="ui-widget-header"',0,0,NULL ,NULL ,NULL ,'font-size:1px;','','div','mod_navFrame.php','','mapframe1','','http://www.mapbender.org/index.php/NavFrame');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','overview',2,1,'OverviewFrame','','div','','class="ui-widget-content hide-during-splash"',10,60,200,160,100,'overflow:hidden;background-color:#ffffff','<div id="overview_maps" style="position:absolute;left:0px;right:0px;"></div>','div','../plugins/mb_overview.js','../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php','mapframe1','mapframe1','http://www.mapbender.org/index.php/Overview');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','pan1',2,1,'pan','Pan','img','../img/button_blue_red/pan_off.png','',287,8,28,28,1,'','','','mod_pan.js','','mapframe1','','http://www.mapbender.org/index.php/Pan');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','resizeMapsize',2,1,'resize mapsize to browser window size','Resize Mapsize','img','../img/button_blue_red/resizemapsize_off.png','',439,8,28,28,3,'','','','mod_resize_mapsize.js','','mapframe1','','http://www.mapbender.org/index.php/ResizeMapsize');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','resultList',2,1,'','Result List','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','mod_ResultList.js','../../lib/resultGeometryListController.js, ../../lib/resultGeometryListModel.js','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','resultList_DetailPopup',2,1,'Detail Popup For resultList','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/mb_resultList_DetailPopup.js','','resultList','','http://www.mapbender.org/resultList_DetailPopup');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','resultList_Highlight',2,1,'highlighting functionality for resultList','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/mb_resultList_Highlight.js','','resultList,mapframe1,overview','','http://www.mapbender.org/resultList_Highlight');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','resultList_Zoom',2,1,'zoom functionality for resultList','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/mb_resultList_Zoom.js','','resultList,mapframe1','','http://www.mapbender.org/resultList_Zoom');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','sandclock',2,1,'displays a sand clock while waiting for requests','','div','','',80,0,0,0,0,'','','div','mod_sandclock.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','scaleSelect',2,0,'Scale-Selectbox','Scale Select','select','','onchange=''mod_scaleSelect(this)''',555,25,100,20,1,'','<option value = ''''>Scale</option>
<option value=''100''>1 : 100</option>
<option value=''250''>1 : 250</option>
<option value=''500''>1 : 500</option>
<option value=''1000''>1 : 1000</option>
<option value=''2500''>1 : 2500</option>
<option value=''5000''>1 : 5000</option>
<option value=''10000''>1 : 10000</option>
<option value=''25000''>1 : 25000</option>
<option value=''30000''>1 : 30000</option>
<option value=''50000''>1 : 50000</option>
<option value=''75000''>1 : 75000</option>
<option value=''100000''>1 : 100000</option>
<option value=''200000''>1 : 200000</option>
<option value=''300000''>1 : 300000</option>
<option value=''400000''>1 : 400000</option>
<option value=''500000''>1 : 500000</option>
<option value=''600000''>1 : 600000</option>
<option value=''700000''>1 : 700000</option>
<option value=''800000''>1 : 800000</option>
<option value=''900000''>1 : 900000</option>
<option value=''1000000''>1 : 1000000</option>','select','mod_scaleSel.php','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','selArea1',2,1,'zoombox','Zoom by rectangle','img','../img/button_blue_red/selArea_off.png','',317,8,28,28,1,'','','','mod_selArea.js','mod_box1.js','mapframe1','','http://www.mapbender.org/index.php/SelArea1');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','setBBOX',2,1,'set extent for mapframe and overviewframe','','div','','',0,0,0,0,0,'','','div','mod_setBBOX1.php','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','setSpatialRequest',2,1,'Spatial Request','','div','','',1,1,1,1,1,'visibility:hidden;','','div','../javascripts/mod_wfs_SpatialRequest.php','geometry.js,requestGeometryConstructor.js,popup.js','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','showCoords_div',2,1,'displays coodinates by onmouseover','Coordinates','img','../img/button_blue_red/coords_off.png','onmouseover = "mb_regButton(''init_mod_showCoords_div'')" ',518,8,28,28,1,'','','','mod_coords_div.php','','mapframe1','dependendDiv','http://www.mapbender.org/index.php/ShowCoords_div');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','tabs',1,1,'vertical tabs to handle iframes','','div','','',2,240,220,20,2,'font-family: Arial,Helvetica;font-weight:bold;','','div','mod_tab.php','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','treeGDE',2,1,'new treegde2 - directory tree, checkbox for visible, checkbox for querylayer
for more infos have a look at http://www.mapbender.org/index.php/TreeGDE2','Maps','div','','class="ui-widget ui-widget-content"',10,220,200,300,NULL ,'visibility:hidden;overflow:auto','','div','../html/mod_treefolderPlain.php','jsTree.js','mapframe1','mapframe1','http://www.mapbender.org/index.php/TreeGde');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','wfs',2,1,'wfs request and result handling','','div','','',1,1,1,1,NULL ,'visibility:hidden','','div','wfs.php','../extensions/wz_jsgraphics.js,geometry.js','mapframe1,overview','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','wfs_conf',2,1,'get all wfs_conf-params','','iframe','../php/mod_wfs.php','frameborder = "0"',1,1,1,1,0,'visibility:hidden','','iframe','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','zoomFull',2,1,'zoom to full extent button','Display complete map','img','../img/button_blue_red/zoomFull_off.png','onmousedown=''this.src = this.src.replace(/_over/, "_on");''
onmouseup=''this.src = this.src.replace(/_on/, "_off");''',347,8,28,28,2,'','','','mod_zoomFull.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomFull');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','zoomIn1',2,1,'zoomIn button','Zoom in','img','../img/button_blue_red/zoomIn2_off.png','onmousedown=''this.src = this.src.replace(/_over/, "_on");''
onmouseup=''this.src = this.src.replace(/_on/, "_off");''',227,8,28,28,1,'','','','mod_zoomIn1.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomIn');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui_digitize','zoomOut1',2,1,'zoomOut button','Zoom out','img','../img/button_blue_red/zoomOut2_off.png','onmousedown=''this.src = this.src.replace(/_over/, "_on");''
onmouseup=''this.src = this.src.replace(/_on/, "_off");''',257,8,28,28,1,'','','','mod_zoomOut1.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomOut');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'css_class_bg', 'body{ background-color: #FFFFCC; }', 'to define the color of the body', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'css_file_body', '../css/mapbender.css', 'file/css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'favicon', '../img/favicon.ico', 'favicon', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'includeWhileLoading', '../include/mapbender_logo_digitize.php', 'show splash screen while the application is loading', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'jq_datatables_css', '../extensions/dataTables-1.5/media/css/demo_table_jui.min.css', 'css-file for jQuery datatables', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'jq_ui_theme', '../extensions/jquery-ui-1.7.2.custom/css/ui-customized_4_digitize/jquery-ui-1.7.3.custom.css', 'UI Theme from Themeroller', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'popupcss', '../css/popup.css', 'file css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'resultListCss', '../css/resultList.css', 'css for resultList elements', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'tablesortercss', '../css/tablesorter.css', 'file css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'treeGDE_css', '../css/treeGDE2.css', 'cssfile for TreeGDE', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'use_load_message', 'true', 'show splash screen while the application is loading', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'copyright', 'mod_copyright_text', 'mapbender.org', 'define a copyright text which should be displayed', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'digitize', 'cssUrl', '../css/digitize.css', 'url to the style sheet of the mapframe', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'digitize', 'digitize_conf_filename', 'digitize_default.conf', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'digitize', 'text css', 'digitizeGeometryList {position:absolute; top:50px; left:0px;}
.digitizeGeometryListItem {color:#000000; font-size:10px;}
body {font-family: Arial, Helvetica, sans-serif; font-size:12px; background-color:#ffffff; margin-top: 0px; margin-left:0px;}
.button {height:18px; width:32px;}', 'text css', 'text/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'digitize', 'wfsCssUrl', '../css/mapbender.css', 'var', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'featureInfoTunnel', 'featureInfoLayerPopup', 'true', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'featureInfoTunnel', 'featureInfoPopupHeight', '320', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'featureInfoTunnel', 'featureInfoPopupPosition', '[100,100]', 'position of the featureInfoPopup', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'featureInfoTunnel', 'featureInfoPopupWidth', '320', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'gazetteerWFS', 'enableSearchWithoutParams', '0', 'define that search can be started without any search params', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'gazetteerWFS', 'initializeOnLoad', '0', 'start gazetteer onload', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'gazetteerWFS', 'removeSpatialRequestHighlight', '0', 'remove spatialrequest highlighting when firing search', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'gazetteerWFS', 'showResultInPopup', '1', 'if value is 1 search results will be displayed in popup, otherwise in gazetteer div', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'gazetteerWFS', 'wfsConfIdString', '1,2,3,4', 'comma seperated list of WFS conf ids', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'gazetteerWFS', 'wfs_spatial_request_conf_filename', 'wfs_additional_spatial_search.conf', 'location and name of the WFS configuration file for spatialRequest', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'jq_datatables', 'defaultCss', '../extensions/dataTables-1.5/media/css/demo_table_jui.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'jq_ui', 'css', '../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css', '', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'legend', 'checkbox_on_off', 'false', 'display or hide the checkbox to set the legend on/off', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'legend', 'css_file_legend', '../css/legend.css', 'file/css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'legend', 'legendlink', 'false', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'legend', 'showgroupedlayertitle', 'true', 'show the title of the grouped layers in the legend', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'legend', 'showlayertitle', 'true', 'show the layer title in the legend', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'legend', 'showwmstitle', 'true', 'show the wms title in the legend', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'legend', 'stickylegend', 'false', 'decide wether your legend should stick on the mapframe1', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'logout', 'logout_location', '', 'webside to show after logout', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'mapframe1', 'skipWmsIfSrsNotSupported', '0', 'if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'mapframe1', 'slippy', '0', '1 = Activates an animated, pseudo slippy map', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'overview', 'overview_wms', '0', 'wms that shows up as overview', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'overview', 'skipWmsIfSrsNotSupported', '0', 'if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resizeMapsize', 'adjust_height', '-35', 'to adjust the height of the mapframe on the bottom of the window', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resizeMapsize', 'adjust_width', '-45', 'to adjust the width of the mapframe on the right side of the window', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resizeMapsize', 'resize_option', 'button', 'auto (autoresize on load), button (resize by button)', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList', 'position', '[600,50]', 'position of the result list dialog', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList', 'resultListHeight', '400', 'height of the result list dialog', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList', 'resultListTitle', 'Search results', 'title of the result list dialog', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList', 'resultListWidth', '600', 'width of the result list dialog', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_DetailPopup', 'detailPopupHeight', '250', 'height of the result list detail popup', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_DetailPopup', 'detailPopupTitle', 'Details', 'title of the result list detail popup', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_DetailPopup', 'detailPopupWidth', '350', 'width of the result list detail popup', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_DetailPopup', 'openLinkFromSearch', '0', 'open link directly if feature attr is defined as link', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_DetailPopup', 'position', '[700,300]', 'position of the result list detail popup', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_Highlight', 'maxHighlightedPoints', '500', 'max number of points to highlight', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_Highlight', 'resultHighlightColor', '#ff0000', 'color of the highlighting', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_Highlight', 'resultHighlightLineWidth', '2', 'width of the highlighting line', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'resultList_Highlight', 'resultHighlightZIndex', '100', 'zindex of the highlighting', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'sandclock', 'mod_sandclock_image', '../img/sandclock.gif', 'define a sandclock image ', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'setSpatialRequest', 'useUsemap', '0', '"1" adds a usemap to each geometry; the geometry will be highlighted onMouseOver. Every other value will skip the usemap.', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'setSpatialRequest', 'wfs_conf_filename', 'wfs_default.conf', 'location and name of the WFS configuration file', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'expandable', '0', '1 = expand the tabs to fit the document vertically, default is 0', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'open_tab', '4', 'define which tab should be opened when a gui is opened', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_frameHeight[0]', '200', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_frameHeight[1]', '260', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_frameHeight[2]', '380', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_frameHeight[3]', '340', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_frameHeight[4]', '180', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_ids[0]', 'treeGDE', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_ids[1]', 'legend', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_ids[2]', 'gazetteerWFS', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_ids[3]', 'imprint', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_ids[4]', 'digitize', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_prefix', '  ', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'tabs', 'tab_style', 'position:absolute;visibility:visible;cursor:pointer;', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'alerterror', 'true', 'alertbox for wms loading error', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'ficheckbox', 'true', 'checkbox for featureInfo requests', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'imagedir', '../img/tree_new', 'image directory', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'menu', 'opacity_up,opacity_down,zoom,metainfo,hide,wms_up,wms_down,layer_up,layer_down,remove', 'context menu elements', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'metadatalink', 'true', 'link for layer-metadata', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'openfolder', 'false', 'initial open folder', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'showstatus', 'true', 'show status in folderimages', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'switchwms', 'true', 'enables/disables all layer of a wms', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'treeGDE', 'wmsbuttons', 'true', 'wms management buttons', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'wfs', 'displayWfsResultList', '1', '', 'var');
--set Raphael JS 
UPDATE gui_element set e_mb_mod='../extensions/RaphaelJS/raphael-1.4.7.min.js' WHERE e_id ilike 'body' AND fkey_gui_id= 'gui_digitize';  
--
--
--Update Gui Digitize END


--
--
-- Insert Template OpenLayers BEGIN

INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('template_openlayers','template_openlayers','An OpenLayers map with WMS configured by Mapbender',1);
INSERT INTO gui_mb_user VALUES ('template_openlayers', 1, 'owner');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','body',1,1,'body (obligatory)','','body','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','favicon','../img/favicon.png','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','jquery_UI','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','jq_ui_theme','../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css','UI Theme from Themeroller','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','ol_mousePosition','.olControlMousePosition
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
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','ol_olControlMeasureItemInactive','.olControlMeasureItemInactive, .olControlMeasureItemActive
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
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','ol_panPanel','.olControlPanPanel {
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
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','ol_zoomPanel','.olControlZoomPanel {
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
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','openlayers_theme','../extensions/OpenLayers-2.8/theme/default/style.css','workaround for css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','popupcss','../css/popup.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','tablesortercss','../css/tablesorter.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','use_load_message','true','','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','body','includeWhileLoading','../include/mapbender_logo_digitize.php','','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','jq_datatables','defaultCss','../extensions/dataTables-1.5/media/css/demo_table_jui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_ui',1,1,'The jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('template_openlayers','jq_ui','css','../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_overviewMap',1,1,'An OpenLayers OverviewMap','OpenLayers OverviewMap','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_overview.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_panPanel',1,0,'An OpenLayers PanPanel','OpenLayers PanPanel','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_panPanel.js','','ol','','http://www.mapbender.org/ol_panPanel');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_panZoomBar',1,1,'An OpenLayers PanZoomBar','OpenLayers Layer Switch','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_panZoomBar.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_permalink',1,0,'An OpenLayers Permalink Control','OpenLayers Permalink','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_permalink.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_scale',1,1,'An OpenLayers Scale Control','OpenLayers Scale','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_scale.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_scaleLine',1,1,'An OpenLayers ScaleLine Control','OpenLayers ScaleLine','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_scaleLine.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_ui_position',1,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_upload',1,1,'','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../plugins/jq_upload.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol',1,1,'An OpenLayers Map, configured with WMS from Mapbender application settings','OpenLayers','div','','class=''ui-corner-all hide-during-splash''',10,13,600,400,2,'border:1px solid black; margin:10px;background-color:#FFFFFF','','div','../plugins/ol.js','../extensions/OpenLayers-2.8/OpenLayers.js','','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_keyboardDefaults',1,1,'An OpenLayers KeyboardDefaults.
Navigate with Keybords up, down, left and right key.','OpenLayers KeyboardDefaults','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_keyboardDefaults.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_label',1,0,'Just the label saying OpenLayers','OpenLayers Label','span','','',85,15,NULL ,NULL,NULL ,'font-family:Trebuchet MS,Helvetica,Arial,sans-serif; font-size:30px; text-shadow:2px 2px 3px gray;','OpenLayers','span','','','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_layerSwitch',1,1,'An OpenLayers LayerSwitcher','OpenLayers LayerSwitcher','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_layerSwitch.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_measureLine',1,0,'An OpenLayers measureLine Control
(currently not working!)','OpenLayers measureLine','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_measureLine.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_mousePosition',1,1,'An OpenLayers MousePosition','OpenLayers MousePosition','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_mousePosition.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_navigation',1,1,'An OpenLayers Navigation Control','OpenLayers Navigation','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_navigation.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_navigationHistory',1,1,'An OpenLayers NavigationHistory Control','OpenLayers NavigationHistory','div','','',NULL ,0,NULL ,NULL,NULL ,'','','div','../plugins/ol_navigationHistory.js','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','ol_wms',2,1,'Load configured WMS from Mapbender application settings into OpenLayers','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/ol_wms.php','','ol','','http://www.openlayers.org/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_ui_droppable',4,1,'jQuery UI droppable','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse,jq_ui_draggable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.resizable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/resizable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('template_openlayers','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js','','jq_ui,jq_ui_widget','');

--demis 
INSERT INTO gui_wms (fkey_gui_id,fkey_wms_id,gui_wms_position,gui_wms_mapformat,
gui_wms_featureinfoformat,gui_wms_exceptionformat,gui_wms_epsg,gui_wms_visible,gui_wms_opacity,gui_wms_sldurl) 
(SELECT 
'template_openlayers', fkey_wms_id, 0, 
gui_wms_mapformat,gui_wms_featureinfoformat,
gui_wms_exceptionformat,gui_wms_epsg,1 as gui_wms_visible,gui_wms_opacity,gui_wms_sldurl
FROM gui_wms WHERE fkey_gui_id = 'gui' AND fkey_wms_id = 250);


INSERT INTO gui_layer (fkey_gui_id,fkey_layer_id,gui_layer_wms_id,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,gui_layer_maxscale,
gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title)
(SELECT 
'template_openlayers', 
fkey_layer_id,gui_layer_wms_id ,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,
gui_layer_maxscale,gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title
 FROM gui_layer WHERE fkey_gui_id = 'gui' AND gui_layer_wms_id = 250);

-------------
--germany
INSERT INTO gui_wms (fkey_gui_id,fkey_wms_id,gui_wms_position,gui_wms_mapformat,
gui_wms_featureinfoformat,gui_wms_exceptionformat,gui_wms_epsg,gui_wms_visible,gui_wms_opacity,gui_wms_sldurl) 
(SELECT 
'template_openlayers', fkey_wms_id, 1, 
gui_wms_mapformat,gui_wms_featureinfoformat,
gui_wms_exceptionformat,gui_wms_epsg,1 as gui_wms_visible,gui_wms_opacity,gui_wms_sldurl
FROM gui_wms WHERE fkey_gui_id = 'gui' AND fkey_wms_id = 893);

INSERT INTO gui_layer (fkey_gui_id,fkey_layer_id,gui_layer_wms_id,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,gui_layer_maxscale,
gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title)
(SELECT 
'template_openlayers', 
fkey_layer_id,gui_layer_wms_id ,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,
gui_layer_maxscale,gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title
 FROM gui_layer WHERE fkey_gui_id = 'gui' AND gui_layer_wms_id = 893);

INSERT into gui_gui_category VALUES ('template_openlayers', 2);
--
--
--
--Insert Template OpenLayers END


--
--
--
--Insert Template layout_with_map_and_toolbar
INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ('layout_with_map_and_toolbar','layout_with_map_and_toolbar','This application uses the jQuery UI Layout plugin to create a layout with columns and rows',1);
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_position',1,1,'jQuery UI position','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_upload',1,1,'','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../plugins/jq_upload.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','body',1,1,'body (obligatory)','','body','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','body','favicon','../img/favicon.png','favicon','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','body','popupcss','../css/popup.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','body','tablesortercss','../css/tablesorter.css','file css','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','body','use_load_message','true','show splash screen while the application is loading','php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','body','jq_ui_theme','../extensions/jquery-ui-1.7.2.custom/css/smoothness/jquery-ui-1.7.2.custom.css','UI Theme from Themeroller','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','body','includeWhileLoading','../include/mapbender_logo_splash.php','show splash screen while the application is loading','php_var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui',1,1,'jQuery UI core','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','jq_ui','css','../extensions/jquery-ui-1.8.1.custom/css/ui-lightness/jquery-ui-1.8.1.custom.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_layout',1,1,'This jQuery plugin can create a sophisticated pane layout ','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery.layout.all-1.2.0/jquery.layout.min.js','','','http://layout.jquery-dev.net');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_widget',1,1,'jQuery UI widget','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js','','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_datatables',1,1,'Includes the jQuery plugin datatables, use like this
$(selector).datatables(options)','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_datatables.js','../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','','http://www.datatables.net/');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','jq_datatables','defaultCss','../extensions/dataTables-1.5/media/css/demo_table_jui.css','','file/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','i18n',1,1,'Internationalization module, collects data from all elements and sends them to the server in a single POST request. The strings are translated via gettext only.','Internationalization','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_i18n.js','','','','http://www.mapbender.org/Gettext');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','toolbar',1,1,'This toolbar appends all its target elements to its container','Toolbar','div','','class=''mb-toolbar'' style=''background:url(../img/Mapbender_logo_and_text_200x50.png) no-repeat 10px 10px''',NULL ,NULL,NULL ,NULL,NULL ,'','','div','../plugins/mb_toolbar.js','','pan1,zoomFull, showCoords_div, featureInfoTunnel,measure_widget','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','toolbar','css','.mb-toolbar ul, .mb-toolbar li {
   display: inline;
}

.mb-toolbar ul {
   float: right;
}

.mb-toolbar li {
   margin:2px
}','','text/css');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','pane',2,1,'','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'height:100%;width:100%','','div','../plugins/mb_pane.js','','','jq_layout','http://layout.jquery-dev.net');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','pane','center','mapframe1','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','pane','css','../extensions/jquery.layout.all-1.2.0/jquery.layout.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','pane','east','','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','pane','layoutOptions','{
south__minSize:60
}','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','pane','north','toolbar','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','pane','south','statusBar','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','pane','west','treeGDE','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','statusBar',2,1,'','','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','','','','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','statusBar','displayTarget','statusBar','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_mouse',2,1,'jQuery UI mouse','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js','','jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','doubleclickZoom',2,1,'adds doubleclick zoom to map module (target).
Deactivates the browser contextmenu!!!','Doubleclick zoom','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','mod_doubleclickZoom.js','','mapframe1','','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','showCoords_div',2,1,'displays coodinates by onmouseover','Koordinaten anzeigen','img','../img/button_blue_red/coords_off.png','onmouseover = "mb_regButton(''init_mod_showCoords_div'')" ',NULL ,NULL,NULL ,NULL,1,'','','','mod_coords_div.php','','mapframe1','','http://www.mapbender.org/index.php/ShowCoords_div');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','showCoords_div','displayTarget','statusBar','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','zoomFull',2,1,'zoom to full extent button','gesamte Karte anzeigen','img','../img/button_blue_red/zoomFull_off.png','',NULL ,NULL,NULL ,NULL,2,'','','','mod_zoomFull.js','','mapframe1','','http://www.mapbender.org/index.php/ZoomFull');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','featureInfoTunnel',2,1,'FeatureInfoRequest with local path','Query','img','../img/button_blue_red/query_off.png','onmouseover = "mb_regButton(''init_featureInfoTunnel'')"',NULL ,NULL,NULL ,NULL,1,'','','','mod_featureInfoTunnel.php','popup.js','mapframe1','','http://www.mapbender.org/index.php/FeatureInfoTunnel');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','featureInfoTunnel','featureInfoLayerPopup','true','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','featureInfoTunnel','featureInfoPopupHeight','300','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','featureInfoTunnel','featureInfoPopupPosition','[100,100]','position of the featureInfoPopup','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','featureInfoTunnel','featureInfoPopupWidth','340','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','pan1',2,1,'pan','Pan','img','../img/button_blue_red/pan_off.png','',NULL ,NULL,NULL ,NULL,1,'','','','mod_pan.js','','mapframe1','','http://www.mapbender.org/index.php/Pan');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','measure_widget',2,1,'Measure','Measure distance','img','../img/button_blue_red/measure_off.png','',NULL ,NULL,NULL ,NULL,1,'','','','../plugins/mb_measure_widget.php','../widgets/w_measure.js,../extensions/RaphaelJS/raphael-1.4.7.min.js','mapframe1','jq_ui_dialog,jq_ui_widget','http://www.mapbender.org/index.php/Measure');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','lineStrokeDefault','#C9F','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','lineStrokeSnapped','#F30','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','lineStrokeWidthDefault','3','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','lineStrokeWidthSnapped','5','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','measurePointDiameter','7','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','opacity','0.4','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','pointFillDefault','#CCF','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','pointFillSnapped','#F90','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','polygonFillDefault','#FFF','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','polygonFillSnapped','#FC3','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','polygonStrokeWidthDefault','1','','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','measure_widget','polygonStrokeWidthSnapped','5','','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','treeGDE',2,1,'new treegde2 - directory tree, checkbox for visible, checkbox for querylayer
for more infos have a look at http://www.mapbender.org/index.php/TreeGDE2','Karten','div','','class="ui-widget"',NULL ,NULL,NULL ,NULL,NULL ,'visibility:visible;overflow:scroll','','div','../html/mod_treefolderPlain.php','jsTree.js','mapframe1','mapframe1','http://www.mapbender.org/index.php/TreeGde');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','menu','opacity_up,opacity_down,zoom,hide,wms_up,wms_down,layer_up,layer_down,remove','context menu elements','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','alerterror','true','alertbox for wms loading error','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','ficheckbox','true','checkbox for featureInfo requests','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','imagedir','../img/tree_new','image directory','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','showstatus','true','show status in folderimages','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','switchwms','true','enables/disables all layer of a wms','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','metadatalink','false','link for layer-metadata','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','wmsbuttons','false','wms management buttons','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','css','../css/treeGDE2.css','','file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','treeGDE','openfolder','0','initial open folder','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','mapframe1',3,1,'frame for a map','','div','','',NULL ,NULL,NULL ,NULL,2,'overflow:hidden;background-color:#ffffff','','div','../plugins/mb_map.js','../../lib/history.js,map_obj.js,map.js,wms.js,wfs_obj.js,initWmcObj.php','','','http://www.mapbender.org/index.php/Mapframe');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','mapframe1','skipWmsIfSrsNotSupported','0','if set to 1, it skips the WMS request if the current SRS is not supported by the WMS; if set to 0, the WMS is always queried. Default is 0, because of backwards compatibility','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','mapframe1','slippy','1','1 = Activates an animated, pseudo slippy map','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','mapframe1_mousewheelZoom',4,1,'adds mousewheel zoom to map module (target)','Mousewheel zoom','div','','',NULL ,NULL,NULL ,NULL,NULL ,'','','div','mod_mousewheelZoom.js','../extensions/jquery.mousewheel.min.js','mapframe1','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','mapframe1_mousewheelZoom','factor','2','The factor by which the map is zoomed on each mousewheel unit','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_droppable',4,1,'jQuery UI droppable','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js','','jq_ui,jq_ui_widget,jq_ui_mouse,jq_ui_draggable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_button',4,1,'jQuery UI button','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.button.min.js','','jq_ui,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','mapframe1_zoomBar',4,1,'zoom bar - slider to handle navigation, define zoom level with an element var','Zoom to scale','div','','',30,100,NULL ,200,100,'','','div','../plugins/mb_zoomBar.js','','mapframe1','mapframe1, jq_ui_slider','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','mapframe1_zoomBar','defaultLevel','1','define the default level for application start','var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('layout_with_map_and_toolbar','mapframe1_zoomBar','level','[2500,5000,10000,50000,100000,500000,1000000,3000000,5000000,10000000]','define an array of levels for the slider (element_var has to be defined)','var');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','mapframe1_navigation',4,1,'Adds navigation arrows on top of the map','Navigation','div','','',20,30,NULL ,NULL,100,'','','div','../plugins/mb_navigation.js','','mapframe1','mapframe1','http://www.mapbender.org/Navigation');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_slider',5,1,'slider from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.slider.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_draggable',5,1,'Draggable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','http://jqueryui.com/demos/draggable/');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_dialog',5,1,'Dialog from jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js','','jq_ui,jq_ui_widget,jq_ui_button,jq_ui_draggable,jq_ui_mouse,jq_ui_position,jq_ui_resizable','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','../plugins/jq_ui_resizable.js','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.resizable.min.js','','jq_ui,jq_ui_mouse,jq_ui_widget','');
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('layout_with_map_and_toolbar','jq_ui_tabs',5,1,'horizontal tabs from the jQuery UI framework','','','','',NULL ,NULL,NULL ,NULL,NULL ,'','','','','../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js','','jq_ui,jq_ui_widget','');

-------------
--germany
INSERT INTO gui_wms (fkey_gui_id,fkey_wms_id,gui_wms_position,gui_wms_mapformat,
gui_wms_featureinfoformat,gui_wms_exceptionformat,gui_wms_epsg,gui_wms_visible,gui_wms_opacity,gui_wms_sldurl) 
(SELECT 
'layout_with_map_and_toolbar', fkey_wms_id, 0, 
gui_wms_mapformat,gui_wms_featureinfoformat,
gui_wms_exceptionformat,gui_wms_epsg,1 as gui_wms_visible,gui_wms_opacity,gui_wms_sldurl
FROM gui_wms WHERE fkey_gui_id = 'gui' AND fkey_wms_id = 893);

INSERT INTO gui_layer (fkey_gui_id,fkey_layer_id,gui_layer_wms_id,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,gui_layer_maxscale,
gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title)
(SELECT 
'layout_with_map_and_toolbar', 
fkey_layer_id,gui_layer_wms_id ,gui_layer_status,gui_layer_selectable,
gui_layer_visible,gui_layer_queryable,gui_layer_querylayer,gui_layer_minscale,
gui_layer_maxscale,gui_layer_priority,gui_layer_style,gui_layer_wfs_featuretype,gui_layer_title
 FROM gui_layer WHERE fkey_gui_id = 'gui' AND gui_layer_wms_id = 893);

INSERT into gui_mb_user (fkey_gui_id , fkey_mb_user_id , mb_user_type) VALUES ('layout_with_map_and_toolbar',1,'owner');

INSERT into gui_gui_category VALUES ('layout_with_map_and_toolbar', 2);
--
--
--
--Insert Template layout_with_map_and_toolbar END
--

DELETE FROM gui_element where fkey_gui_id = 'admin1' AND e_id = 'CreateTreeGDE';


--
-- delete obsolete wms
--
Delete from wms where wms_id = 625 AND wms_title LIKE 'DM Solutions';
Delete from wms where wms_id = 836 AND wms_title LIKE 'NDOP (OGCConnector v1.1)';
Delete from wms where wms_id = 842 AND wms_title LIKE 'Tsunami_Diasaster_Data';
Delete from wms where wms_id = 882 AND wms_title LIKE 'Wahlen';
Delete from wms where wms_id = 824 AND wms_title LIKE 'Image Web Server';
Delete from wms where wms_id = 903 AND wms_title LIKE 'DNM 25';
Delete from wms where wms_id = 828 AND wms_title LIKE 'IDERIOJA Gobierno de La Rioja OGC WMS';
Delete from wms where wms_id = 328 AND wms_title LIKE 'NWSIB-online';
Delete from wms where wms_id = 671 AND wms_title LIKE 'Wasserschutzgebiete NRW';
Delete from wms where wms_id = 785 AND wms_title LIKE 'WMS Map Server';
Delete from wms where wms_id = 786 AND wms_title LIKE 'Luftbilder';
Delete from wms where wms_id = 615 AND wms_title LIKE 'ESRI Inc. Map Server';
Delete from wms where wms_id = 614 AND wms_title LIKE 'ESRI Inc. Map Server';
Delete from wms where wms_id = 645 AND wms_title LIKE 'Multi-Hazard Mapping Initiative WMS Map Server (HDM)';
Delete from wms where wms_id = 792 AND wms_title LIKE 'WMS-Thueringen';
Delete from wms where wms_id = 634 AND wms_title LIKE 'Location mapserver';
Delete from wms where wms_id = 840 AND wms_title LIKE 'Intergraph World Map';
Delete from wms where wms_id = 883 AND wms_title LIKE 'SICAD/SD-IMS V5.1-Luftbilder';
Delete from wms where wms_id = 902 AND wms_title LIKE 'DNM 100/2';
Delete from wms where wms_id = 843 AND wms_title LIKE 'WMS Map Server';
Delete from wms where wms_id = 904 AND wms_title LIKE 'DNM 250/2';

--
-- fee - changed colum datatype to text
--
ALTER TABLE wms ALTER COLUMN fees TYPE text;
ALTER TABLE wfs ALTER COLUMN fees TYPE text;

--
-- Insert German translation for coordsLookup
--
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('de', 'Coordinate lookup', 'Koordinatensuche');

--remove new layout application
DELETE FROM gui_mb_user WHERE fkey_gui_id = 'layout_with_map_and_toolbar';

--
-- changed position for OpenLayers selements
--
UPDATE gui_element SET e_pos = 2 where e_id LIKE 'ol_%';

--
--replace jquery ui minified folder
--resizeable
--
UPDATE gui_element SET e_mb_mod=replace(e_mb_mod,'/minified/','/') where e_id ilike 'jq_ui%' ;
UPDATE gui_element SET e_mb_mod=replace(e_mb_mod,'.min.js','.js') where e_id ilike 'jq_ui%' ;
