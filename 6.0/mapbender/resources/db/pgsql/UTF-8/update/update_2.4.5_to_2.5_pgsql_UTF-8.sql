--
-- database changes in version 2.5
--
-- notice language plpgsql has to be installed
-- 


-- How to install plpgsql? 
-- createlang plpgsql mapbender


INSERT INTO gui_mb_group (fkey_gui_id, fkey_mb_group_id, mb_group_type) VALUES ('admin_de_services', 20, '');
INSERT INTO gui_mb_group (fkey_gui_id, fkey_mb_group_id, mb_group_type) VALUES ('admin_en_services', 20, '');

--
-- table mb_monitor: new columns for mb_monitor
--
ALTER TABLE mb_monitor ADD COLUMN image int4;
ALTER TABLE mb_monitor ADD COLUMN map_url varchar(2048);



--
-- WFS database handling
-- enhancement of the datadase structure concerning WFS metadata (same solution like for WMS) 
--
ALTER table wfs add wfs_getcapabilities_doc text;
ALTER table wfs add wfs_upload_url character varying(255);
ALTER table wfs add fees character varying(255);
ALTER table wfs add accessconstraints text;
ALTER table wfs add individualname character varying(255);
ALTER table wfs add positionname character varying(255);
ALTER table wfs add providername character varying(255);
ALTER table wfs add city character varying(255);
ALTER table wfs add deliverypoint character varying(255);
ALTER table wfs add administrativearea character varying(255);
ALTER table wfs add postalcode character varying(255);
ALTER table wfs add voice character varying(255);
ALTER table wfs add facsimile character varying(255);
ALTER table wfs add electronicmailaddress character varying(255);
ALTER table wfs add wfs_mb_getcapabilities_doc text;
ALTER table wfs add wfs_owner integer;
ALTER table wfs add wfs_timestamp integer;
ALTER table wfs add country character varying(255);

ALTER TABLE wfs ALTER COLUMN wfs_title SET NOT NULL;
ALTER TABLE wfs ALTER COLUMN wfs_title SET DEFAULT ''::character varying;

ALTER TABLE wfs ALTER COLUMN wfs_version SET NOT NULL;
ALTER TABLE wfs ALTER COLUMN wfs_version SET DEFAULT ''::character varying;

ALTER TABLE wfs ALTER COLUMN wfs_getcapabilities SET NOT NULL;
ALTER TABLE wfs ALTER COLUMN wfs_getcapabilities SET DEFAULT ''::character varying;

alter table wfs_featuretype add column featuretype_searchable integer default 1;
alter table wfs_featuretype add column featuretype_abstract character varying(50);

-- grant root access to default wfs
UPDATE wfs SET wfs_owner = 1;

--
-- new table gui_wfs_conf
--
CREATE TABLE gui_wfs_conf (
    fkey_gui_id character varying(50) DEFAULT ''::character varying NOT NULL,
    fkey_wfs_conf_id integer DEFAULT 0 NOT NULL
);

ALTER TABLE ONLY gui_wfs_conf
    ADD CONSTRAINT pk_fkey_wfs_conf_id PRIMARY KEY (fkey_gui_id, fkey_wfs_conf_id);
   
ALTER  TABLE ONLY gui_wfs_conf
    ADD CONSTRAINT gui_wfs_conf_ibfk_1 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;
    
ALTER TABLE ONLY gui_wfs_conf
    ADD CONSTRAINT gui_wfs_conf_ibfk_2 FOREIGN KEY (fkey_wfs_conf_id) REFERENCES wfs_conf(wfs_conf_id) ON UPDATE CASCADE ON DELETE CASCADE;

-- insert wfs confs into gui digitize
INSERT INTO gui_wfs_conf (fkey_gui_id, fkey_wfs_conf_id) VALUES ('gui_digitize', 1);
INSERT INTO gui_wfs_conf (fkey_gui_id, fkey_wfs_conf_id) VALUES ('gui_digitize', 3);
INSERT INTO gui_wfs_conf (fkey_gui_id, fkey_wfs_conf_id) VALUES ('gui_digitize', 4);
INSERT INTO gui_wfs_conf (fkey_gui_id, fkey_wfs_conf_id) VALUES ('gui_digitize', 2);



--
-- new table wfs_featuretype_keyword: 
--
CREATE TABLE wfs_featuretype_keyword (
  fkey_featuretype_id integer NOT NULL,
  fkey_keyword_id integer NOT NULL
);

ALTER TABLE ONLY wfs_featuretype_keyword
    ADD CONSTRAINT fkey_keyword_id_fkey_featuretype_id FOREIGN KEY (fkey_keyword_id) REFERENCES keyword(keyword_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY wfs_featuretype_keyword
    ADD CONSTRAINT fkey_featuretype_id_fkey_keyword_id FOREIGN KEY (fkey_featuretype_id) REFERENCES wfs_featuretype(featuretype_id) ON UPDATE CASCADE ON DELETE CASCADE;

--
-- new table layer_load_count
--
CREATE TABLE layer_load_count (
 fkey_layer_id int4, 
 load_count int8
);


--
-- table wfs_conf
--
ALTER TABLE wfs_conf add COLUMN wfs_conf_description text;


--
-- table wfs_conf_element: change in WFS configuration: access to geometries may now be restricted
--
ALTER TABLE wfs_conf_element ADD COLUMN f_auth_varname VARCHAR(50);
ALTER TABLE wfs_conf_element ADD COLUMN f_show_detail int4;
ALTER TABLE wfs_conf_element ADD COLUMN f_operator VARCHAR(50);

ALTER TABLE ONLY wfs_conf
    ADD CONSTRAINT wfs_conf_ibfk_2 FOREIGN KEY (fkey_featuretype_id) REFERENCES wfs_featuretype(featuretype_id) ON UPDATE CASCADE ON DELETE CASCADE;

--
-- table gui_element: new element for table admin1 to edit wfs settings
--
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin1','WFS',2,1,'edit wfs settings','a','','href = "../javascripts/mod_wfs_client.html" target="AdminFrame"',10,1005,250,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','EDIT WFS','a','','','','AdminFrame','');


-- 
-- new table translation: new table for translations
-- 

CREATE TABLE translations
(
  trs_id serial PRIMARY KEY not null,
  locale varchar(8),
  msgid varchar(512),
  msgstr varchar(512)
);

CREATE INDEX msgid_idx ON translations(msgid);



 CREATE TRUSTED PROCEDURAL LANGUAGE 'plpgsql'
  HANDLER plpgsql_call_handler;
-- 
-- new function gettext for easy translations
-- 
CREATE OR REPLACE FUNCTION gettext(locale_arg "text", string "text")
RETURNS "varchar" AS
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
        RETURN trl.msgstr;
    END IF; 
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
  
--
-- new entries for table translations
--

INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Pan', 'Ausschnitt verschieben');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Display complete map', 'gesamte Karte anzeigen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Zoom in', 'In die Karte hineinzoomen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Zoom out', 'Aus der Karte herauszoomen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Back', 'Zurück');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Forward', 'Nach vorne');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Coordinates', 'Koordinaten anzeigen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Zoom by rectangle', 'Ausschnitt wählen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Redraw', 'Neu laden oder Tastatur: Leertaste');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Query', 'Datenabfrage');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Logout', 'Abmelden');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'WMS preferences', 'WMS Einstellungen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Adding WMS from filtered list', 'WMS von gefilteter Liste hinzufügen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Set map center', 'Kartenmittelpunkt setzen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Help', 'Hilfe');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Show WMS infos', 'Anzeige von WMS Informationen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Save workspace as web map context document', 'Ansicht als Web Map Context Dokument speichern');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Resize Mapsize', 'Bildschirmgröße anpassen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Rubber', 'Skizze löschen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Get Area', 'Fläche berechnen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Close Polygon', 'Polygon schliessen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Move back to your GUI list', 'Zurück zur GUI Liste');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Legend', 'Legende');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Print', 'Druck');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Imprint', 'Impressum');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Maps', 'Karten');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Search', 'Suche');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Meetingpoint', 'Treffpunkt');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Metadatasearch', 'Metadatensuche');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Adding WMS', 'WMS hinzufügen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Adding WMS from List', 'WMS aus Liste hinzufügen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Info', 'Info');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Change Projection', 'Projektion ändern');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Digitize', 'Digitalisierung');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Overview', 'Übersichtskarte');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Drag Mapsize', 'Karte vergrößern');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Mapframe', 'Kartenfenster');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Navigation Frame', 'Navigationsfenster');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Scale Select', 'Auswahl des Maßstabes');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Scale Text', 'Maßstab per Texteingabe');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Scalebar', 'Maßstabsleiste');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Set Background', 'Hintegrundkarte auswählen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Zoom to Coordinates', 'Zu den Koordinaten zoomen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Change Password', 'Passwort ändern');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Load a web map context document', 'laden eines Web Map Context Dokumentes');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de ', 'Logo', 'Logo');

INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Pan', 'Премести областта');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Display complete map', 'Покажи цялата карта');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Zoom in', 'Покажи картата в по-едър мащаб');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Zoom out', 'Покажи картата в по-дребен мащаб');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Back', 'Назад');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Forward', 'Напред');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Coordinates', 'Координати');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Zoom by rectangle', 'Увеличи картата в избраната област');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Redraw', 'Пречертай или от клавиатурата: празен интервал');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Query', 'Зявка към данните');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Logout', 'Изход на потребител');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'WMS preferences', 'WMS настройки');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Adding WMS from filtered list', 'Добави WMS от избран списък');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Set map center', 'Постави център на карта');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Help', 'Помощ');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Show WMS infos', 'Покажи WMS информация');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Save workspace as web map context document', 'Запази работния документ като Web Map Context документ');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Resize Mapsize', 'Напасни големината на картата');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Rubber', 'Изтрий');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Get Area', 'Изчисли площ');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Close Polygon', 'Затвори полигон');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Move back to your GUI list', 'Назад към списъка с интерфейси (GUI)');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Legend', 'Легенда');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Print', 'Печат');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Imprint', 'Авторско каре');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Maps', 'Карти');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Search', 'Търсене');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Meetingpoint', 'Място на среща');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Metadatasearch', 'Търсене по метаданни');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Adding WMS', 'Добави WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Adding WMS from List', 'Добави WMS от списък');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Info', 'Информация');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Change Projection', 'Промени проекция');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Copyright', 'Авторско право');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Digitize', 'Дигитализиране');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Overview','Обзорна карта');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Drag Mapsize', 'Промени размера на картата');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Mapframe', 'Рамка на картата');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Navigation Frame', 'Навигационна рамка');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Scale Select', 'Избор на мащаб');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Scale Text', 'Въведи мащаб');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Scalebar', 'Мащабна скала');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Set Background', 'Избери фоново изображение');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Zoom to Coordinates', 'Покажи картата около посочените координати');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Change Password', 'Промени парола');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Load a web map context document', 'Зареди Web Map Context документ');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Logo', 'Фирмен знак');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('bg', 'Measure distance', 'Измери разстояние');

INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Pan', 'Μετακίνηση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Display complete map', 'Επίδειξη Πλήρους Χάρτη');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Zoom in', 'Zoom In');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Zoom out', 'Zoom Out');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Back', 'Πίσω');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Forward', 'Πρόσθια');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Coordinates', 'Συντεταγμένες');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Zoom by rectangle', 'Ζουμ από ορθογώνιο');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Redraw', 'Απόσυρση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Query', 'Μέτρηση Εμβαδού');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Logout', 'Αποσύνδεση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'WMS preferences', 'Αλλάξτε τη διάταξη των WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Adding WMS from filtered list', 'Πρόσθεση WMS από το φιλτραρισμένο κατάλογο');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Set map center', 'Το καθορισμένο Κέντρο Χαρτών');
INSERT INTO translations (locale, msgid, msgstr) VALUES  ('gr', 'Help', 'Βοήθεια');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Save workspace as web map context document', 'Ansicht als Web Map Context Dokument speichern');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Rubber', 'Το λάστιχο- Τρίφτης');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Close Polygon', 'Το Κλειστό Πολύγωνο');
INSERT INTO translations (locale, msgid, msgstr) VALUES ( 'gr', 'Move back to your GUI list', 'Παρουσίαση του GUI Καταλόγου');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Print', 'Εκτύπωση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Maps', ' Χάρτες');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Adding WMS', 'Πρόσθεση WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Adding WMS from List', 'Πρόσθεση WMS από τον κατάλογο');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Navigation Frame', 'Navigationsfenster');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Load a web map context document', 'laden eines Web Map Context Dokumentes');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Measure distance', 'Μέτρηση Απόστασης');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Meetingpoint', 'Σημείο Συνάντησης');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Info', 'Πληροφορίες');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Digitize', 'Ψηφιοποίηση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Change Projection', 'Αλλαγή Προβολής');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Set Background', 'Επιλογή Φόντου');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Overview', 'Προεπισκόπιση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Change Password', 'Αλλαγή Συνθηματικού');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Scalebar', 'Κανόνας Κλίμακας');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr ', 'Logo', 'Λογότυπο');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Zoom to Coordinates', 'Εστίαση σε Συντεταγμένες');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Scale Text', 'Κείμενο Κλίμακας');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Show WMS infos', 'Προβολή WMS πληροφοριών');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Legend', 'Λεζάντα');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Search', 'Αναζήτηση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Resize Mapsize', 'Αλλαγή Μεγέθους Χάρτη');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Drag Mapsize', 'Αλλαγή Μεγέθους Χάρτη με Ολίσθηση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Mapframe', 'Πλαίσιο Χάρτη');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Get Area', 'Προβολή Περιοχής');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Imprint', 'Αποτύπωση');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Scale Select', 'Επιλογή Κλίμακας');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('gr', 'Metadatasearch', 'Αναζήτηση στα Metadata');

INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Pan', 'Selectie verschuiven');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Display complete map', 'Hele kaart tonen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Zoom in', 'Inzoomen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Zoom out', 'Uitzoomen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Back', 'Terug');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Forward', 'Voorwaarts');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Coordinates', 'Koordinaten tonen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Zoom by rectangle', 'Zoom rechthoek-selectie');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Redraw', 'Opfrissen of toetsenbord : spatiebalk');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Query', 'Data opvragen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Logout', 'Afmelden');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'WMS preferences', 'WMS instellingen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Adding WMS from filtered list', 'WMS van gefilterte lijst toevoegen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Set map center', 'Kaartmiddelpunt aangeven');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Help', 'Help');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Show WMS infos', 'Tonen van WMS Informatie');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Save workspace as web map context document', 'zicht als Web map context dokument bewaren');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Resize Mapsize', 'beeldschermgrote aanpassen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Rubber', 'schets verwerpen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Get Area', 'oppervlakte berekenen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Close Polygon', 'Polygoon sluiten');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Move back to your GUI list', 'Terug naar GUI lijst');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Legend', 'Legende');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Print', 'Drukken');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Imprint', 'Impressum');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Maps', 'Kaarten');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Search', 'Zoek');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Meetingpoint', 'Trefpunt');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Metadatasearch', 'Zoeken naar Metadata');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Adding WMS', 'WMS toevoegen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Adding WMS from List', 'WMS uit lijst toevoegen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Info', 'Informatie');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Change Projection', 'Projektie veranderen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Digitize', 'Digitaliseren');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Overview', 'overzichtskaart');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Drag Mapsize', 'Kaart vergroten');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Mapframe', 'Kaartvenster');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Navigation Frame', 'Navigeervenster');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Scale Select', 'Schaal selecteren');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Scale Text', 'Schaal via tekstingave');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Scalebar', 'Schaalbalk');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Set Background', 'Achtergrond selecteren');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Zoom to Coordinates', 'Naar koordinaten zoomen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Change Password', 'Paswoord wijzigen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Load a web map context document', 'Laad een Web Map Context Dokument');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Logo', 'Logo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Measure distance', 'Meet afstand');

INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Pan', 'Sposta dettaglio');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Display complete map', 'Mostra tutta la mappa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Zoom in', 'Ingrandisci');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Zoom out', 'Riduci');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Back', 'Indietro');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Forward', 'Avanti');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Coordinates', 'Mostra coordinate');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Zoom by rectangle', 'Seleziona dettaglio');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Redraw', 'Ridisegna oppure, da tastiera: spazio');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Query', 'Interroga');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Logout', 'Logout');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'WMS preferences', 'Impostazioni WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Adding WMS from filtered list', 'Aggiungi un WMS da una lista filtrata');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Set map center', 'Fissa il centro della mappa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Help', 'Aiuto');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Show WMS infos', 'Mostra informazioni sul WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Save workspace as web map context document', 'Salva vista come documento di Web Map Context');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Resize Mapsize', 'Adatta la dimensione della mappa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Rubber', 'Cancella bozza');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Get Area', 'Calcola area');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Close Polygon', 'Chiudi poligono');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Move back to your GUI list', 'Torna alla lista delle GUI');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Legend', 'Legenda');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Print', 'Stampa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Imprint', 'Colophon');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Maps', 'Mappe');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Search', 'Cerca');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Meetingpoint', 'Punti di incontro');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Metadatasearch', 'Ricerca metadati');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Adding WMS', 'Aggiungi WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Adding WMS from List', 'Aggiungi WMS da lista');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Info', 'Info');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Change Projection', 'Cambia proiezione');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Digitize', 'Digitalizza');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Overview', 'Mappa di insieme');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Drag Mapsize', 'Cambia dimensione mappa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Mapframe', 'Quadro della mappa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Navigation Frame', 'Quadro di navigazione');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Scale Select', 'Scelta della scala');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Scale Text', 'Scala dei testi');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Scalebar', 'Scala di riferimento');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Set Background', 'Seleziona mappa di sfondo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Zoom to Coordinates', 'Ingrandisci alle coordinate');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Change Password', 'Cambia password');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Load a web map context document', 'Carica un documento Web Map Context');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Logo', 'Logo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Measure distance', 'Misura distanza');

INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Pan', 'Desplazamiento');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Display complete map', 'Desplazamiento');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Zoom in', 'Zoom +');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Zoom out', 'Zoom -');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Back', 'Zoom previo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Forward', 'Zoom siguiente');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Coordinates', 'Mostrar coordenadas');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Zoom by rectangle', 'Zoom rectángulo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Redraw', 'Refrescar');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Query', 'Busqueda de datos');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Logout', 'Terminar');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'WMS preferences', 'Ajuste WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Adding WMS from filtered list', 'Anadir WMS desde lista filtrada');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Set map center', 'Centrar');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Help', 'Ayuda');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Show WMS infos', 'Mostrar información sobre WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Save workspace as web map context document', 'Guardar vista como fichero Web Map Context');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Resize Mapsize', 'Modificar el tamano de mapa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Rubber', 'Borrar');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Get Area', 'Calcular area');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Close Polygon', 'Cerrar polígono');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Move back to your GUI list', 'Volver a la lista WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Legend', 'Leyenda');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Print', 'Imprimir');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Imprint', 'Aviso legal');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Maps', 'Mapas');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Search', 'Busqueda');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Meetingpoint', 'Lugar de reunión');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Metadatasearch', 'Búsqueda de datos meta');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Adding WMS', 'Añadir WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Adding WMS from List', 'Añadir WMS desde lista');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Info', 'Informacion');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Change Projection', 'Cambiar projecto');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Digitize', 'Digitalización');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Overview', 'Mapa de visión general');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Drag Mapsize', 'Ampilar vista de mapa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Mapframe', 'Ventana de mapa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Navigation Frame', 'Ventana de navigacion');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Scale Select', 'Selecionar escala');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Scale Text', 'Teclar escala');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Scalebar', 'Fraja de escala');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Set Background', 'Poner el fondo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Zoom to Coordinates', 'Zoom en coordenadas');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Change Password', 'Cambiar clave');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Load a web map context document', 'Cargar un documento de Web Map Context');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Logo', 'Logo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Measure distance', 'Medir distancias');


INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Set language', 'Sprache auswählen');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('es', 'Set language', 'Selección del idioma');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Set language', 'Lingua');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Set language', 'Choisir une langue');

--
-- new table gui_kml
--
CREATE TABLE gui_kml (
    kml_id serial,
    fkey_mb_user_id integer NOT NULL,
    fkey_gui_id character varying(50) NOT NULL,
    kml_doc text NOT NULL,
    kml_name character varying(64),
    kml_description text,
    kml_timestamp integer NOT NULL
);

ALTER TABLE ONLY gui_kml
    ADD CONSTRAINT mb_gui_kml_pkey PRIMARY KEY (kml_id);

ALTER TABLE ONLY gui_kml
    ADD CONSTRAINT gui_kml_fkey_mb_user_id FOREIGN KEY (fkey_mb_user_id) REFERENCES mb_user(mb_user_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY gui_kml
    ADD CONSTRAINT gui_kml_id_fkey_gui_id  FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;

--
-- new sld_user_layer table
--
CREATE TABLE sld_user_layer (
    sld_user_layer_id serial NOT NULL,
    fkey_mb_user_id integer NOT NULL,
    fkey_layer_id integer NOT NULL,
    fkey_gui_id character varying,
    sld_xml text,
    use_sld smallint
);
ALTER TABLE sld_user_layer ADD CONSTRAINT pk_sld_user_layer PRIMARY KEY  (sld_user_layer_id);
--
-- Name: sld_user_layer_ibfk_1; Type: FK CONSTRAINT; Schema: public; Owner: admin
--
ALTER TABLE ONLY sld_user_layer
    ADD CONSTRAINT sld_user_layer_ibfk_1 FOREIGN KEY (fkey_mb_user_id) REFERENCES mb_user(mb_user_id) ON UPDATE CASCADE ON DELETE CASCADE;
--
-- Name: sld_user_layer_ibfk_2; Type: FK CONSTRAINT; Schema: public; Owner: admin
--
ALTER TABLE ONLY sld_user_layer
    ADD CONSTRAINT sld_user_layer_ibfk_2 FOREIGN KEY (fkey_layer_id) REFERENCES layer(layer_id) ON UPDATE CASCADE ON DELETE CASCADE;
--
-- Name: sld_user_layer_ibfk_3; Type: FK CONSTRAINT; Schema: public; Owner: admin
--
ALTER TABLE ONLY sld_user_layer
    ADD CONSTRAINT sld_user_layer_ibfk_3 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;
--
-- add sld related columns
--
alter table gui_wms add column gui_wms_sldurl character varying(255) DEFAULT ''::character varying NOT NULL;
alter table wms add column wms_supportsld boolean;
alter table wms add column wms_userlayer boolean;
alter table wms add column wms_userstyle boolean;
alter table wms add column wms_remotewfs boolean;   

--
-- opacity handeling
--
ALTER TABLE gui_wms ADD COLUMN gui_wms_opacity INT DEFAULT 100;


UPDATE gui_element SET e_mb_mod = 'mod_addWMSgeneralFunctions.js' WHERE e_id = 'addWMS';

ALTER TABLE gui_element ALTER COLUMN e_js_file TYPE character varying(255);
ALTER TABLE gui_element ALTER COLUMN e_mb_mod TYPE character varying(255);
ALTER TABLE wms ALTER COLUMN fees TYPE text;
ALTER TABLE wms RENAME COLUMN wms_mb_getcapabilies_doc TO wms_mb_getcapabilities_doc;

