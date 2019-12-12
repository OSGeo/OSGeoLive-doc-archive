Update gui_element set e_top = 10 where fkey_gui_id = 'gui' and e_id = 'setBackground';

-- new language fr, es pt
Update gui_element_vars set var_value = 'de,en,bg,gr,nl,hu,it,fr,es,pt' where fkey_gui_id = 'gui' AND fkey_e_id = 'switchLocale_noreload';


INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Pan', 'Nézetet mozgat');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Display complete map', 'Teljes nézet');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Zoom in', 'Nagyít');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Zoom out', 'Kicsnyít');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Back', 'Vissza');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Forward', 'Előre');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Coordinates', 'Koordináták kijelzése');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Zoom by rectangle', 'Kijelölt területre nagyít');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Redraw', 'Újrarajzol');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Query', 'Adatok lekérése');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Logout', 'Kijelentkezés');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'WMS preferences', 'WMS beállítások');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Adding WMS from filtered list', 'WMS hozzáadása szűrt listából');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Set map center', 'Nézet középpontja');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Help', 'Segítség');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Show WMS infos', 'WMS adatok megjelenítése');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Save workspace as web map context document', 'Nézet mentése Web Map Context formában');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Resize Mapsize', 'Térkép átméretezése');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Rubber', 'Törlés');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Get Area', 'Területszámítás');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Close Polygon', 'Sokszög bezárása');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Move back to your GUI list', 'Vissza a GUI listához');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Legend', 'Jelmagyarázat');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Print', 'Nyomtat');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Imprint', 'Impresszum');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Maps', 'Térképek');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Search', 'Keres');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Meetingpoint', 'Találkozási pont');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Metadatasearch', 'Metaadat keresés');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Adding WMS', 'WMS hozzáadása');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Adding WMS from List', 'WMS hozzáadása listából');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Info', 'Információ');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Change Projection', 'Más vetület választása');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Digitize', 'Digitalizálás');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Overview', 'Átnézeti térkép');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Drag Mapsize', 'Térkép átméretezése');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Mapframe', 'Térképablak');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Navigation Frame', 'Navigációs ablak');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Scale Select', 'Lépték választása');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Scale Text', 'Lépték megadása');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Scalebar', 'Aránymérték');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Set Background', 'Háttér beállítása');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Zoom to Coordinates', 'Ugrás adott koordinátákra');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Change Password', 'Jelszó módosítása');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Load a web map context document', 'Web Map Context dokumentum betöltése');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Logo', 'Logó');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Measure distance', 'Távolságmérés'); 

-- Bugfix for treegde in gui (tabs wideout)
UPDATE gui_element SET e_more_styles='visibility:hidden; background-color:#ffffff;border: 1px solid #a19c8f;overflow:auto;' WHERE fkey_gui_id='gui' AND e_id='treeGDE';

-- remove treeGDE-css statement from 'treeGDE' because its handled in 'body' element 
DELETE  from gui_element_vars WHERE fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND fkey_e_id= 'treeGDE' AND var_name='cssfile';

-- http://trac.osgeo.org/mapbender/ticket/442
DELETE FROM gui_element_vars WHERE fkey_gui_id = 'gui2' AND fkey_e_id = 'tooltip' AND var_name = 'tooltip_noResultArray';
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_noResultArray', E'["Kein Ergebnis.","<body onload=\'javascript:window.close()\'>"]', '', 'var');

-- new element vars for module tooltip to control whether user likes to have wfs getFeature request and/or whether user likes to have wms getfeatureInfo requests
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_disableWfs', '0', 'disable WFS getFeature Request', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_disableWms', '1', 'disable WMS getFeatureInfo Request', 'var');

-- update style of wfs_conf myPolygons (coloured buttons, background)
UPDATE wfs_conf SET g_style = 'body{
 font-family:Verdana,Arial,sans-serif;
 font-size: 12px;
 line-height:2;
 background-color:#CFD2D4;
}
.a{
 font-weight:bold;
}
.b{
 font-family:Verdana,Arial,sans-serif;
 font-size: 12px;
 font-weight: bold;
 width:40px;
 color: #FFFFFF;
 -moz-border-radius:5px;
 -khtml-border-radius:5px;
 background-color: #1874CD;
 border-color: #1874CD;
}
.d{
 color:#000000;
}
.hidden{
 visibility: hidden;
}
.buttonDelFilter{
 font-family:Verdana,Arial,sans-serif;
 font-size: 12px;
 font-weight: bold;
 width:40px;
 color:#FFFFFF;
 -moz-border-radius:5px;
 -khtml-border-radius:5px;
 background-color: #8B0000;
 border-color: #8B0000;
}
' WHERE wfs_conf_id = 2;



-- insert statement for switchWMS is in template Guis was missing.
-- http://trac.osgeo.org/mapbender/ticket/472
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'treeGDE', 'switchwms', 'true', 'enables/disables all layer of a wms' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'treeGDE', 'switchwms', 'true', 'enables/disables all layer of a wms' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'treeGDE', 'switchwms', 'true', 'enables/disables all layer of a wms' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'treeGDE', 'switchwms', 'true', 'enables/disables all layer of a wms' ,'var');


ALTER TABLE gui_element ALTER COLUMN e_pos SET DEFAULT 2;
ALTER TABLE gui_element ALTER COLUMN e_pos SET NOT NULL;

ALTER TABLE gui_element ALTER COLUMN e_public SET DEFAULT 1;
ALTER TABLE gui_element ALTER COLUMN e_public SET NOT NULL;

-- new columns for table mb_user (more information fields + validity date fields)
ALTER TABLE mb_user ADD COLUMN mb_user_realname varchar(100);
ALTER TABLE mb_user ADD COLUMN mb_user_street varchar(100);
ALTER TABLE mb_user ADD COLUMN mb_user_housenumber varchar(50);
ALTER TABLE mb_user ADD COLUMN mb_user_reference varchar(100);
ALTER TABLE mb_user ADD COLUMN mb_user_for_attention_of varchar(100);
ALTER TABLE mb_user ADD COLUMN mb_user_valid_from date;
ALTER TABLE mb_user ADD COLUMN mb_user_valid_to date;
ALTER TABLE mb_user ADD COLUMN mb_user_password_ticket varchar(100);

-- enable favicon per gui using element_var
-- http://trac.osgeo.org/mapbender/ticket/514
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'body', 'favicon', '../img/favicon.png', 'favicon' ,'php_var');

-- pt update entries for translations table
update translations set msgid = 'Set language' where msgid = 'languaje' and locale = 'pt';
update translations set msgstr = 'Selecionar Linguagem' where msgstr = 'Selecionar Linguajem' and locale = 'pt';
update translations set msgstr = 'Marco de navegação' where msgstr = 'Marco de navegacion' and locale = 'pt';
update translations set msgstr = 'Zoom seguinte' where msgid = 'Forward' and locale = 'pt';
update translations set msgstr = 'Zoom para coordinadas' where msgid = 'Zoom to Coordinates' and locale = 'pt';
update translations set msgid = 'Drag Mapsize' where msgid = 'dragMapSize' and locale = 'pt';
update translations set msgid = 'Navigation Frame' where msgid = 'navFrame' and locale = 'pt';
update translations set msgid = 'Scale Select' where msgid = 'scaleSelect' and locale = 'pt';

-- polish entries for translations table
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Pan', 'Przesuń');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Display complete map', 'Pokaż calą mapę');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Zoom in', 'Powiększ');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Zoom out', 'Pomniejsz');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Back', 'Wróć');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Forward', 'Do przodu');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Coordinates', 'Współrzędne');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Zoom by rectangle', 'Wybierz fragment mapy');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Redraw', 'Załaduj ponownie');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Query', 'Szukaj danych');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Logout', 'Wymelduj');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'WMS preferences', 'Ustawienia WMS');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Adding WMS from filtered list', 'Dodaj WMS z listy');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Set map center', 'Zaznacz środek mapy');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Help', 'Pomoc');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Show WMS infos', 'Informacje WMS');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Save workspace as web map context document', 'Zapisz widok jako web map context dokument');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Resize Mapsize', 'Zmień rozmiar mapy');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Rubber', 'Usuń szkic');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Get Area', 'Oblicz powierzchnię');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Close Polygon', 'Zamknij poligon');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Move back to your GUI list', 'Z powrotem do listy GUI');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Legend', 'Legenda');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Print', 'Drukuj');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Imprint', 'Imprint');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Maps', 'Mapy');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Search', 'Szukaj');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Meetingpoint', 'Miejsce spotkań');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Metadatasearch', 'Wyszukiwanie metadanych');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Adding WMS', 'Dodaj WMS');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Adding WMS from List', 'Dodaj WMS z listy');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Info', 'Informacja');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Change Projection', 'Zmień układ współrzędnych');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Digitize', 'Dygitalizacja');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Overview', 'Mapa przeglądowa');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Drag Mapsize', 'Powiększ');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Mapframe', 'Okno mapy');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Navigation Frame', 'Pasek narzędzi');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Scale Select', 'Wybierz skalę');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Scale Text', 'Wpisz skalę');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Scalebar', 'Podziałka');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Set Background', 'Wybierz mapę tematyczną jako tło');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Zoom to Coordinates', 'Powiększ według współrzędnych');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Change Password', 'Zmień hasło');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Load a web map context document', 'Załaduj web map context dokument');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Logo', 'Logo');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Measure distance', 'Zmierz odległość');
INSERT INTO translations (locale, msgid, msgstr ) VALUES ('pl', 'Set language', 'Wybierz język');


-- hungarian entry for translations table
INSERT INTO translations (locale, msgid, msgstr) VALUES ('hu', 'Set language', 'Másik nyelv...');

-- italian entry for translations table
INSERT INTO translations (locale, msgid, msgstr) VALUES ('it', 'Set language', 'Assegnare linguaggio'); 

-- dutch entry for translations table
INSERT INTO translations (locale, msgid, msgstr) VALUES ('nl', 'Set language', 'Taal instellen');

-- set window.focus() function for print button in gui1
UPDATE gui_element set e_attributes = 'onclick=''printWindow = window.open("../print/mod_printPDF.php?target=mapframe1&sessionID&conf=printPDF_b.conf","printWin","width=260, height=380, resizable=yes ");printWindow.focus();''  onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");''' where fkey_gui_id = 'gui1' and e_id = 'printPDF';

-- set position of background select box
UPDATE gui_element SET e_top = '210', e_left = '10' WHERE fkey_gui_id = 'gui' AND e_id = 'setBackground';

-- set background colour of gazetteer
UPDATE gui_element SET e_more_styles = 'visibility:hidden; background:#fff; border: 1px solid #a19c8f;' WHERE e_id = 'gazetteerWFS' AND fkey_gui_id IN ('gui');
