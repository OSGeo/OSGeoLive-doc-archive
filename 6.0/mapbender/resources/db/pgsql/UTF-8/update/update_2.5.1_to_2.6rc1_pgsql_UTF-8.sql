UPDATE gui_element SET e_mb_mod = 'map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php' WHERE e_id = 'mapframe1';

--new fields for wfs_conf
ALTER TABLE wfs_conf_element ADD COLUMN f_detailpos int4 DEFAULT 0;
ALTER TABLE wfs_conf_element ADD COLUMN f_min_input int4 DEFAULT 0;


--new element vars for wfs_gazetteer
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'gazetteerWFS', 'showResultInPopup', '1', 'if value is 1 search results will be displayed in popup, otherwise in gazetteer div' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'gazetteerWFS', 'wfs_spatial_request_conf_filename', 'wfs_additional_spatial_search.conf', 'location and name of the WFS configuration file for spatialRequest' ,'php_var');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'gazetteerWFS', 'showResultInPopup', '1', 'if value is 1 search results will be displayed in popup, otherwise in gazetteer div' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'gazetteerWFS', 'wfs_spatial_request_conf_filename', 'wfs_additional_spatial_search.conf', 'location and name of the WFS configuration file for spatialRequest' ,'php_var');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'gazetteerWFS', 'showResultInPopup', '1', 'if value is 1 search results will be displayed in popup, otherwise in gazetteer div' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'gazetteerWFS', 'wfs_spatial_request_conf_filename', 'wfs_additional_spatial_search.conf', 'location and name of the WFS configuration file for spatialRequest' ,'php_var');

--new element vars for body for css style of popups and tablesorter
INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui', 'body', 'popupcss', '../css/popup.css', 'file css', 'file/css');
INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'body', 'popupcss', '../css/popup.css', 'file css', 'file/css');
INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui_digitize', 'body', 'popupcss', '../css/popup.css', 'file css', 'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'body', 'tablesortercss', '../css/tablesorter.css', 'file css' ,'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'body', 'tablesortercss', '../css/tablesorter.css', 'file css' ,'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'body', 'tablesortercss', '../css/tablesorter.css', 'file css' ,'file/css');

--add all used modules for gazetteerWFS
ALTER TABLE gui_element ALTER e_mb_mod TYPE varchar(100); 
UPDATE gui_element SET e_mb_mod = 'geometry.js,requestGeometryConstructor.js,popup.js,../extensions/jquery.tablesorter.js' WHERE e_id = 'gazetteerWFS';

-- update wfs configuration Mapbender User (ID: 1) to show new functionality of gazetteerWFS
UPDATE wfs_conf_element SET f_show_detail = 1, f_detailpos = 1, f_operator = 'rightside' WHERE wfs_conf_element_id = 2 AND fkey_wfs_conf_id = 1;
UPDATE wfs_conf_element SET f_show_detail = 1, f_detailpos = 2, f_min_input = 1, f_operator = 'bothside' WHERE wfs_conf_element_id = 3 AND fkey_wfs_conf_id = 1; 
UPDATE wfs_conf_element SET f_show_detail = 1, f_detailpos = 3, f_label = 'Organization:', f_label_id = 'd' WHERE wfs_conf_element_id = 5 AND fkey_wfs_conf_id = 1;
UPDATE wfs_conf_element SET f_show_detail = 1, f_detailpos = 4, f_label = 'URL:', f_label_id = 'd', f_form_element_html = '<a href=""></a>' WHERE wfs_conf_element_id = 6 AND fkey_wfs_conf_id = 1;
UPDATE wfs_conf_element SET f_search = 1, f_pos = 3, f_style_id = 'c', f_label_id = 'd', f_show_detail = 1, f_detailpos = 5, f_label = 'Usertype:', f_form_element_html = '<select id="usertype" name="usertype">
<option value="">...</option>
<option value="1">Company</option>
<option value="2">Administration</option>
<option value="3">University</option>
<option value="4">Individual</option>
</select>', f_operator = 'equal' WHERE wfs_conf_element_id = 4 AND fkey_wfs_conf_id = 1;

--add style class to element var text css of element digitize in gui_digitize
UPDATE gui_element_vars SET var_value = 
'
.digitizeGeometryList {position:absolute; top:50px; left:0px;}
.digitizeGeometryListItem {color:#000000; font-size:10px;}
body {font-family: Arial, Helvetica, sans-serif; font-size:12px; color:#ff00ff; background-color:#ffffff; margin-top: 0px; margin-left:0px;}
.button {height:18px; width:32px;}
' 
WHERE fkey_e_id = 'digitize' AND var_name = 'text css';

-- http://trac.osgeo.org/mapbender/ticket/336
UPDATE gui_element SET e_js_file = 'wfs.php', e_target='mapframe1,overview' WHERE e_id = 'wfs';


UPDATE gui_element SET e_attributes = 'onload="init()"' WHERE e_id = 'body' AND fkey_gui_id IN ('admin1', 'admin2_de', 'admin2_en', 'admin_de_services', 'admin_en_services');

-- new capabilities diff column for monitoring
ALTER TABLE mb_monitor ADD COLUMN caps_diff text;
ALTER TABLE mb_monitor ALTER COLUMN caps_diff SET DEFAULT ''::text;

--
-- scope
-- ticket #
--

-- remove event handlers, are now in the script
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'forward';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'back';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'zoomIn1';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'zoomOut1';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'measure';

-- remove event handlers, are now in the script
UPDATE gui_element SET e_content = '<div id="mbN" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;">
<img id="arrow_n" style="position:relative;top:0;left:0" src="../img/arrows/arrow_n.gif" width="15" height="10">
</div> 
<div id="mbNE" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;">
<img id="arrow_ne" style="position:relative;top:0;left:0" src="../img/arrows/arrow_ne.gif" width="10" height="10">
</div> 
<div id="mbE" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;">
<img id="arrow_e" style="position:relative;top:0;left:0" src="../img/arrows/arrow_e.gif" width="10" height="15">
</div> 
<div id="mbSE" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;">
<img id="arrow_se" style="position:relative;top:0;left:0" src="../img/arrows/arrow_se.gif" width="10" height="10">
</div> 
<div id="mbS" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;">
<img id="arrow_s" style="position:relative;top:0;left:0" src="../img/arrows/arrow_s.gif" width="15" height="10">
</div> 
<div id="mbSW" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;">
<img id="arrow_sw" style="position:relative;top:0;left:0" src="../img/arrows/arrow_sw.gif" width="10" height="10">
</div>
<div id="mbW" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;">
<img id="arrow_w" style="position:relative;top:0;left:0" src="../img/arrows/arrow_w.gif" width="10" height="15">
</div> 
<div id="mbNW" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#B8C1C7;">
<img id="arrow_nw" style="position:relative;top:0;left:0" src="../img/arrows/arrow_nw.gif" width="10" height="10">
</div>' WHERE e_id = 'navFrame';


UPDATE gui_element SET e_pos = 1, e_element = 'div', e_src = '', e_attributes = '', e_more_styles = 'overflow:hidden;', e_content = '<div id="markResult" name="maps" style ="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index:26"> </div>
<div id="mapframe1_maps" name="maps" style ="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index:2;"> </div>
<div id="highlight" style="position:absolute;top:-10px;left:-10px;width:14px;height:14px;z-index:3;visibility:visible"><img src="../img/redball.gif"/></div>
<div id="l_right" name="l_right" style="position:absolute;top:0px;left:0px;width:0px;height:0px;overflow:hidden;z-index:10;visibility:hidden;background-color:#ff0000;cursor: crosshair;"></div>
<div id="l_bottom"  name="l_bottom" style="position:absolute;top:0px;left:0px;width:0px;height:0px;overflow:hidden;z-index:11;visibility:hidden;background-color:#ff0000;cursor: crosshair;"></div>
<div id="l_left" name="l_left" style="position:absolute;top:0px;left:0px;width:0px;height:0px;overflow:hidden;z-index:12;visibility:hidden;background-color:#ff0000;cursor: crosshair;"></div>
<div id="l_top" name="l_top" style="position:absolute;top:0px;left:0px;width:0px;height:0px;overflow:hidden;z-index:13;visibility:hidden;background-color:#ff0000;cursor: crosshair;"></div>
<div id="sandclock" style="position:absolute; top:0px; left:0px; z-index:14;"></div>
<div id="scalebar" style="position:absolute; top:0px; left:0px; z-index:15;"></div>
<div id="measuring" style="position:absolute; top:0px; left:0px; z-index:16; font-size:10px"></div>
<div id="measure_display" style="position:absolute; top:0px; left:0px; z-index:17;"></div>
<div id="copyright" style="position:absolute; top:0px; left:0px; z-index:18;"></div>
<div id="measure_sub" style="position:absolute; top:0px; left:0px; z-index:19;"></div>
<div id="permanent" style="position:absolute;top:-10px;left:-10px;width:14px;height:14px;z-index:13;visibility:hidden"><img src="../img/redball.gif"/></div>
<div id="digitize_sub" style="position:absolute; top:0px; left:0px; z-index:24;"></div>
<div id="digitize_display" style="position:absolute; top:0px; left:0px; z-index:25;"></div>
<div id="um_title" name="um_title" style="font-family: Arial, Helvetica, sans-serif; DISPLAY:none; OVERFLOW:visible; POSITION:absolute; DISPLAY:none; BACKGROUND:#BEC1C4;border:1px solid black; z-index:98;"></div>
<div id="um_draw" name="um_draw" style="LEFT:0px;OVERFLOW:visible;POSITION:absolute;TOP:0px;z-index:99;"></div>
<img id="um_img" name="um_img" style ="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; border:0;z-index:100" src="../img/transparent.gif" useMap="#um">
<map name="um" id="um"></map>', e_closetag = 'div', e_js_file = 'mapnf.php' WHERE e_id = 'mapframe1';
UPDATE gui_element SET e_element = 'div', e_src = '', e_attributes = '', e_more_styles = 'overflow:hidden;', e_content = '<div id="overview_maps" style="position:absolute;left:0px;right:0px;"></div>', e_closetag = 'div', e_js_file = 'ovnf.php', e_target = 'mapframe1', e_requires = 'mapframe1' WHERE e_id = 'overview';
INSERT INTO gui_element_vars select fkey_gui_id,'overview' as fkey_e_id, 'overview_wms' as var_name, '0' as var_value, 'wms that shows up as overview' as context, 'var' as var_type from gui_element where e_id = 'overview';
UPDATE gui_element SET e_pos = 2, e_element = 'div', e_more_styles = 'visibility:visible;overflow:scroll', e_content = '', e_closetag = 'div', e_js_file = '../html/mod_treefolderPlain.php', e_mb_mod = 'jsTree.js', e_requires = 'mapframe1' WHERE e_id = 'treeGDE';
INSERT INTO gui_element_vars select fkey_gui_id,'body' as fkey_e_id, 'treeGDE_css' as var_name, '../css/treeGDE2.css' as var_value, 'cssfile for TreeGDE' as context, 'file/css' as var_type from gui_element where e_id = 'treeGDE';
UPDATE gui_element SET e_element = 'select', e_src = '', e_attributes = '', e_closetag = 'select', e_js_file = 'mod_changeEPSG.php' WHERE e_id = 'changeEPSG';

-- only update e_content when e_content is empty
UPDATE gui_element SET e_content = '<option value="">undefined</option>
<option value="EPSG:4326">EPSG:4326</option>
<option value="EPSG:31466">EPSG:31466</option>
<option value="EPSG:31467">EPSG:31467</option>
<option value="EPSG:31468">EPSG:31468</option>
<option value="EPSG:31469">EPSG:31469</option>' WHERE e_id = 'changeEPSG' and (e_content IS NULL or e_content = '');


DELETE FROM gui_element WHERE e_id = 'closePolygon';
DELETE FROM gui_element WHERE e_id = 'rubber';
DELETE FROM gui_element WHERE e_id = 'getArea';
DELETE FROM gui_element WHERE e_id = 'rubberExt';

UPDATE gui_element SET e_element = 'div', e_src = '', e_attributes = '', e_more_styles = 'overflow:hidden;', e_content = '', e_closetag = 'div', e_js_file = 'mod_zoomCoords.php', e_target = 'mapframe1,overview', e_requires = 'mapframe1' WHERE e_id = 'zoomCoords';

 
-- a demo splash screen for gui1
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'body', 'use_load_message', 'true', '' ,'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'body', 'includeWhileLoading', '../include/gui1_splash.php', '' ,'php_var');

UPDATE gui_element SET e_mb_mod = '../extensions/wz_jsgraphics.js,geometry.js' WHERE e_id = 'wfs';
 
INSERT INTO translations (locale, msgid, msgstr) VALUES ('de', 'Measure distance', 'Messen'); 

UPDATE gui_element SET e_attributes = 'onload="init()"' WHERE e_id = 'body' AND fkey_gui_id IN ('wms_africa', 'wms_australia', 'wms_europe', 'wms_gdi_de', 'wms_germany', 'wms_north_america', 'wms_worldwide');

-- gui: tab: increase the size of the frames onmouseover
UPDATE gui_element SET e_attributes = 'frameborder = "0" onmouseover="this.style.zIndex=300;this.style.width=350;" onmouseout="this.style.zIndex=2;this.style.width=200"',
e_more_styles = 'visibility:hidden; border: 1px solid #a19c8f;' WHERE e_id IN ('treeGDE','printPDF','legend','imprint','meetingPoint','gazetteerWFS') AND fkey_gui_id IN ('gui');

UPDATE gui_element SET e_more_styles = e_more_styles || ' background-color:#FFFFFF;' WHERE e_id IN ('treeGDE','imprint') AND fkey_gui_id IN ('gui');


-- gui_treegde - delete entries which are not needed
Delete from gui_treegde where fkey_gui_id IN ('gui','gui1','gui2','gui_digitize') and my_layer_title ='new';

-- new translation entries for portuguese
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Zoom out', 'Zoom -');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Back', 'Zoom previo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Forward', 'Zoom siguiente');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Coordinates', 'Mostrar coordinadas');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Zoom by rectangle', 'Zoom retángulo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Redraw', 'Refrescar');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Query', 'Procurar dados');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Logout', 'Terminar sessão');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'WMS preferences', 'Ajuste WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Adding WMS from filtered list', 'Adicionar WMS desde lista filtrada');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Set map center', 'Centrar');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Help', 'Ajuda');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Show WMS infos', 'Mostrar informação sobre WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Save workspace as web map context document', 'Guardar vista como arquivo Web Map Context');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Resize Mapsize', 'Modificar tamanho do mapa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Rubber', 'Apagar');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Get Area', 'Calcular area');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Close Polygon', 'Fechar polígono');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Move back to your GUI list', 'Volver a lista WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Legend', 'Legenda');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Print', 'Imprimir');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Imprint', 'Expediente');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Maps', 'Mapas');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Search', 'Procura');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Meetingpoint', 'Lugar de reunião, ');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Metadatasearch', 'Procura metadados');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Adding WMS', 'Adicionar WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Adding WMS from List', 'Adicionar WMS desde lista');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Info', 'Informação');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Change Projection', 'Trocar projeto');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Digitize', 'Captura');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Overview', 'Mapa de vição geral');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Drag Mapsize', 'Ampliar janela do mapa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Mapframe', 'Janela do mapa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Navigation Frame', 'Janela do navegação');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'scaleSelect', 'Selecionar escala');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Scale Text', 'Texto da escala');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Scalebar', 'Barra de escala');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Set Background', 'Pôr fondo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Zoom to Coordinates', 'Zoom pra coordinadas');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Change Password', 'Trocar senha');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Load a web map context document', 'Carregar documento do Web Map Context');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Logo', 'Logo');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Measure distance', 'Medir distância');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'languaje', 'Selecionar Linguajem');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'navFrame', 'Marco de navegacion');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'dragMapSize', 'Agrandar Mapa');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Pan', 'Desplazamento');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Display complete map', 'Zoom na Extensão Total');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('pt', 'Zoom in', 'Zoom +');

-- new translation entries for french
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Pan', 'Déplacer la sélection');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Display complete map', 'Afficher toute la carte');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Zoom in', 'Zoomer');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Zoom out', 'Dézoomer');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Back', 'Précédent');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Forward', 'Suivant');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Coordinates', 'Afficher les coordonnées');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Zoom by rectangle', 'Zoomer sur la sélection');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Redraw', 'Actualiser [Espace]');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Query', 'Interroger la base de données');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Logout', 'Déconnexion');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'WMS preferences', 'Configuration du WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Adding WMS from filtered list', 'Ajouter un WMS de la liste filtrée');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Set map center', 'Définir le centre de la carte');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Help', 'Aide');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Show WMS infos', 'Affichage des informations du WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Save workspace as web map context document', 'Sauvegarder la vue/l''espace de travail en tant que Web Map Context');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Resize Mapsize', 'Redimensionner la taille de la carte');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Rubber', 'Gomme');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Get Area', 'Calculer la superficie');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Close Polygon', 'Fermer le polygone');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Move back to your GUI list', 'Retour à votre liste GUI');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Legend', 'Légende');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Print', 'Imprimer');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Imprint', 'Envoyer / Imprint');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Maps', 'Cartes');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Search', 'Recherche');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Meetingpoint', 'Point de rencontre');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Metadatasearch', 'Recherche des métadonnées');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Adding WMS', 'Ajouter WMS');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Adding WMS from List', 'Ajouter WMS depuis la liste');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Info', 'Info');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Change Projection', 'Changer la projection');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Copyright', 'Copyright');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Digitize', 'Numériser');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Overview', 'Carte d''aperçu');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Drag Mapsize', 'Modifier la taille de la carte');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Mapframe', 'Fenêtre de la carte');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Navigation Frame', 'Fenêtre de navigation');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Scale Select', 'Sélection de l ''échelle');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Scale Text', 'Texte de l''échelle');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Scalebar', 'Echelle graphique');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Set Background', 'Sélectionner la carte d''arrière-plan');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Zoom to Coordinates', 'Zoomer aux coordonnées');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Change Password', 'Changer le mot de passe');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Load a web map context document', 'Charger un fichier Web Map Context');
INSERT INTO translations (locale, msgid, msgstr) VALUES ('fr', 'Logo', 'Logo');

-- entries espaniol
Update translations set msgstr='Seleccionar Idioma' where locale= 'es' and msgid = 'Set language';
Update translations set msgstr='Agrandar Mapa' where locale= 'es' and msgid = 'dragMapSize';
Update translations set msgstr='Marco de navegacion' where locale= 'es' and msgid = 'navFrame';
Update translations set msgstr='Escala de texto' where locale= 'es' and msgid = 'Scale Text';
Update translations set msgstr='Barra de escala' where locale= 'es' and msgid = 'Scalebar';
Update translations set msgstr='Referencia' where locale= 'es' and msgid = 'Legend';
Update translations set msgstr='Mostrar mapa completo' where locale= 'es' and msgid = 'Display complete map';

UPDATE gui_element SET e_mb_mod = 'geometry.js,requestGeometryConstructor.js,popup.js' WHERE e_id = 'setSpatialRequest'; 

--
-- building categories to sort the guis in the login.php
-- have a look at http://www.mapbender.org/GUI_Category
--

-- new tables for category handling 
CREATE TABLE gui_gui_category
(
  fkey_gui_id character varying(50),
  fkey_gui_category_id integer
);


CREATE TABLE gui_category
(
  category_id serial,
  category_name character varying(50),
  category_description character varying(255),
  CONSTRAINT pk_category_id PRIMARY KEY (category_id)
);

ALTER TABLE ONLY gui_gui_category
    ADD CONSTRAINT gui_gui_category_ibfk_2 FOREIGN KEY (fkey_gui_id) REFERENCES gui(gui_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY gui_gui_category
    ADD CONSTRAINT gui_gui_category_ibfk_1 FOREIGN KEY (fkey_gui_category_id) REFERENCES gui_category(category_id) ON UPDATE CASCADE ON DELETE CASCADE;

INSERT INTO gui_category (category_id, category_name, category_description) VALUES (1, 'Administration', 'Applications for administration');
INSERT INTO gui_category (category_id, category_name, category_description) VALUES (3, 'WMS Container', NULL);
INSERT INTO gui_category (category_id, category_name, category_description) VALUES (2, 'Mapbender Template Applications', 'Template Applications');

INSERT INTO gui_gui_category VALUES ('admin1',1);
INSERT INTO gui_gui_category VALUES('admin2_de',1);
INSERT INTO gui_gui_category VALUES('admin2_en',1);
INSERT INTO gui_gui_category VALUES('admin_de_services',1);
INSERT INTO gui_gui_category VALUES('admin_en_services',1);
INSERT INTO gui_gui_category VALUES ('gui',2);
INSERT INTO gui_gui_category VALUES('gui1',2);
INSERT INTO gui_gui_category VALUES('gui2',2);
INSERT INTO gui_gui_category VALUES('gui_digitize',2);
INSERT INTO gui_gui_category VALUES('wms_gdi_de',3);
INSERT INTO gui_gui_category VALUES('wms_germany',3);
INSERT INTO gui_gui_category VALUES('wms_europe',3);

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('gui_category', 'category_id'), (Select max(category_id) from gui_category), true);

-- admin2_de background
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin2_de','headline_GUI_Category',3,1,'Hintergrund GUI Kategorien verwalten','GUI Categories','div','','',5,685,193,66,2,'','GUI Kategorien verwalten','div','','','','','');

-- admin2_de - create a new category
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin2_de','createCategory',2,1,'create a gui category','Create a new category','a','','href = "../php/mod_createCategory.php?sessionID" target = "AdminFrame" ',8,708,140,20,5,'','GUI Kategorien anlegen','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin2_de', 'createCategory', 'cssfile', '../css/administration_alloc.css', '' ,'file/css');
-- admin2_de - add a gui to a category, remove it from a category
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin2_de','category_filteredGUI',2,1,'add Gui to Category','Add one user to serveral groups','a','','href = "../php/mod_category_filteredGUI.php?sessionID&e_id_css=filteredUser_filteredGroup" target = "AdminFrame" ',8,728,190,20,10,'','GUI zu Kategorie zuordnen','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin2_de', 'category_filteredGUI', 'cssfile', '../css/administration_alloc.css', 'css file for admin module' ,'file/css');

-- admin2_en background
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin2_en','headline_GUI_Category',3,1,'Hintergrund GUI Kategorien verwalten','GUI Categories','div','','',5,685,193,66,2,'','GUI Category Management','div','','','','','');

-- admin2_en - create a new category
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin2_en','createCategory',2,1,'create a gui category','Create a new category','a','','href = "../php/mod_createCategory.php?sessionID" target = "AdminFrame" ',8,708,140,20,5,'','create a GUI category','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin2_en', 'createCategory', 'cssfile', '../css/administration_alloc.css', '' ,'file/css');
-- admin2_en - add a gui to a category, remove it from a category
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin2_en','category_filteredGUI',2,1,'add Gui to Category','Add one user to serveral groups','a','','href = "../php/mod_category_filteredGUI.php?sessionID&e_id_css=filteredUser_filteredGroup" target = "AdminFrame" ',8,728,190,20,10,'','add GUI to Category','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin2_en', 'category_filteredGUI', 'cssfile', '../css/administration_alloc.css', 'css file for admin module' ,'file/css');

-- admin1 - create a new category
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin1','createCategory',2,1,'create a gui category','Create a new category','a','','href = "../php/mod_createCategory.php?sessionID" target = "AdminFrame" ',8,1030,140,20,5,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','create a GUI category','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin1', 'createCategory', 'cssfile', '../css/administration_alloc.css', '' ,'file/css');

-- admin1 - add a gui to a category, remove it from a category
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin1','category_filteredGUI',2,1,'add Gui to Category','Add one user to serveral groups','a','','href = "../php/mod_category_filteredGUI.php?sessionID&e_id_css=filteredUser_filteredGroup" target = "AdminFrame" ',8,1050,190,20,10,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','add GUI to Category','a','','','','AdminFrame','http://www.mapbender.org/GUI_Category');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('admin1', 'category_filteredGUI', 'cssfile', '../css/administration_alloc.css', 'css file for admin module' ,'file/css');


-- remove module addWMSfromfilteredList_ajax from every standard gui and set it new with new parameters and new element vars
DELETE FROM gui_element WHERE fkey_gui_id = 'gui' and e_id = 'addWMSfromfilteredList_ajax';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui','addWMSfromfilteredList_ajax',2,1,'add a WMS to the running application from a filtered list','Adding WMS from filtered list','img','../img/button_gray/add_filtered_list_off.png','onclick=''var addWmsFromFilteredListPopup = new mb_popup({title:"Add WMS from filtered list",url:"../javascripts/mod_addWMSfromfilteredList_ajax.php?sessionID",width:720, height:600,left:20, top:20});addWmsFromFilteredListPopup.show()''  onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");'' title="Adding WMS from filtered list"',490,10,24,24,1,'','','','','mod_addWMSgeneralFunctions.js,popup.js','treeGDE,mapframe1','loadData','http://www.mapbender.org/index.php/Add_WMS_from_filtered_list');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'addWMSfromfilteredList_ajax', 'cssfileAddWMS', '../css/addwms.css', '' ,'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'addWMSfromfilteredList_ajax', 'capabilitiesInput', '1', 'load wms by capabilities url' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'addWMSfromfilteredList_ajax', 'option_dball', '1', '1 enables option "load all configured wms from db"' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'addWMSfromfilteredList_ajax', 'option_dbgroup', '1', '1 enables option "load configured wms by group"' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'addWMSfromfilteredList_ajax', 'option_dbgui', '1', '1 enables option "load configured wms by gui"' ,'var');

DELETE FROM gui_element WHERE fkey_gui_id = 'gui1' and e_id = 'addWMSfromfilteredList_ajax';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','addWMSfromfilteredList_ajax',2,1,'add a WMS to the running application from a filtered list','Adding WMS from filtered list','img','../img/button_gray/add_filtered_list_off.png','onclick=''var addWmsFromFilteredListPopup = new mb_popup({title:"Add WMS from filtered list",url:"../javascripts/mod_addWMSfromfilteredList_ajax.php?sessionID",width:720, height:600,left:20, top:20});addWmsFromFilteredListPopup.show()''  onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");'' title="Adding WMS from filtered list"',620,60,24,24,1,'','','','','mod_addWMSgeneralFunctions.js,popup.js','treeGDE,mapframe1','loadData','http://www.mapbender.org/index.php/Add_WMS_from_filtered_list');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'addWMSfromfilteredList_ajax', 'cssfileAddWMS', '../css/addwms.css', '' ,'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'addWMSfromfilteredList_ajax', 'capabilitiesInput', '1', 'load wms by capabilities url' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'addWMSfromfilteredList_ajax', 'option_dball', '1', '1 enables option "load all configured wms from db"' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'addWMSfromfilteredList_ajax', 'option_dbgroup', '0', '1 enables option "load configured wms by group"' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui1', 'addWMSfromfilteredList_ajax', 'option_dbgui', '0', '1 enables option "load configured wms by gui"' ,'var');

DELETE FROM gui_element WHERE fkey_gui_id = 'gui_digitize' and e_id = 'addWMSfromfilteredList_ajax';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui_digitize','addWMSfromfilteredList_ajax',2,1,'add a WMS to the running application from a filtered list','Adding WMS from filtered list','img','../img/button_gray/add_filtered_list_off.png','onclick=''var addWmsFromFilteredListPopup = new mb_popup({title:"Add WMS from filtered list",url:"../javascripts/mod_addWMSfromfilteredList_ajax.php?sessionID",width:720, height:600,left:20, top:20});addWmsFromFilteredListPopup.show()''  onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");'' title="Adding WMS from filtered list"',490,10,24,24,1,'','','','','mod_addWMSgeneralFunctions.js,popup.js','treeGDE,mapframe1','loadData','http://www.mapbender.org/index.php/Add_WMS_from_filtered_list');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'addWMSfromfilteredList_ajax', 'cssfileAddWMS', '../css/addwms.css', '' ,'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'addWMSfromfilteredList_ajax', 'capabilitiesInput', '1', 'load wms by capabilities url' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'addWMSfromfilteredList_ajax', 'option_dball', '1', '1 enables option "load all configured wms from db"' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'addWMSfromfilteredList_ajax', 'option_dbgroup', '0', '1 enables option "load configured wms by group"' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'addWMSfromfilteredList_ajax', 'option_dbgui', '0', '1 enables option "load configured wms by gui"' ,'var');

DELETE FROM gui_element WHERE fkey_gui_id = 'gui2' and e_id = 'addWMSfromfilteredList_ajax';
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui2','addWMSfromfilteredList_ajax',2,1,'add a WMS to the running application from a filtered list','Adding WMS from filtered list','img','../img/button_blue/add_filtered_list_off.png','onclick=''var addWmsFromFilteredListPopup = new mb_popup({title:"Add WMS from filtered list",url:"../javascripts/mod_addWMSfromfilteredList_ajax.php?sessionID",width:720, height:600,left:20, top:20});addWmsFromFilteredListPopup.show()''  onmouseover=''this.src = this.src.replace(/_off/,"_over");''  onmouseout=''this.src = this.src.replace(/_over/, "_off");'' title="Adding WMS from filtered list"',556,40,28,28,1,'','','','','mod_addWMSgeneralFunctions.js,popup.js','treeGDE,mapframe1','loadData','http://www.mapbender.org/index.php/Add_WMS_from_filtered_list');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'addWMSfromfilteredList_ajax', 'cssfileAddWMS', '../css/addwms.css', '' ,'file/css');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'addWMSfromfilteredList_ajax', 'capabilitiesInput', '1', 'load wms by capabilities url' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'addWMSfromfilteredList_ajax', 'option_dball', '1', '1 enables option "load all configured wms from db"' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'addWMSfromfilteredList_ajax', 'option_dbgroup', '0', '1 enables option "load configured wms by group"' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'addWMSfromfilteredList_ajax', 'option_dbgui', '0', '1 enables option "load configured wms by gui"' ,'var');

-- set popup.js as required module for gui element loadwmc
UPDATE gui_element SET e_mb_mod = 'popup.js' WHERE e_id = 'loadwmc';

-- set popup.js as required module for gui element featureInfoTunnel
UPDATE gui_element SET e_mb_mod = 'popup.js' WHERE e_id = 'featureInfoTunnel';

-- set standard element vars for possibility to use popup in featureInfoTunnel; activated for gui_digitize
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'featureInfoTunnel', 'featureInfoLayerPopup', 'false', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'featureInfoTunnel', 'featureInfoPopupHeight', '200', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui', 'featureInfoTunnel', 'featureInfoPopupWidth', '270', '' ,'var');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'featureInfoTunnel', 'featureInfoLayerPopup', 'false', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'featureInfoTunnel', 'featureInfoPopupHeight', '200', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'featureInfoTunnel', 'featureInfoPopupWidth', '270', '' ,'var');

INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'featureInfoTunnel', 'featureInfoLayerPopup', 'true', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'featureInfoTunnel', 'featureInfoPopupHeight', '200', '' ,'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui_digitize', 'featureInfoTunnel', 'featureInfoPopupWidth', '270', '' ,'var');


-- increase size 
ALTER TABLE gui_element ALTER e_js_file TYPE varchar(255);
ALTER TABLE gui_element ALTER  e_mb_mod  TYPE varchar(255);
ALTER TABLE gui_element ALTER  e_requires  TYPE varchar(255);

-- changed some styles for gui2
UPDATE gui_element SET e_left = 225 WHERE fkey_gui_id = 'gui2' AND e_id = 'mapframe1';
UPDATE gui_element SET e_top = 250 WHERE fkey_gui_id = 'gui2' AND e_id = 'tabs';
UPDATE gui_element SET e_content = '<div id="mbN" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#799FEB;">
<img id="arrow_n" style="position:relative;top:0;left:0" src="../img/arrows/arrow_n.gif" width="15" height="10">
</div> 
<div id="mbNE" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#799FEB;">
<img id="arrow_ne" style="position:relative;top:0;left:0" src="../img/arrows/arrow_ne.gif" width="10" height="10">
</div> 
<div id="mbE" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#799FEB;">
<img id="arrow_e" style="position:relative;top:0;left:0" src="../img/arrows/arrow_e.gif" width="10" height="15">
</div> 
<div id="mbSE" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#799FEB;">
<img id="arrow_se" style="position:relative;top:0;left:0" src="../img/arrows/arrow_se.gif" width="10" height="10">
</div> 
<div id="mbS" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#799FEB;">
<img id="arrow_s" style="position:relative;top:0;left:0" src="../img/arrows/arrow_s.gif" width="15" height="10">
</div> 
<div id="mbSW" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#799FEB;">
<img id="arrow_sw" style="position:relative;top:0;left:0" src="../img/arrows/arrow_sw.gif" width="10" height="10">
</div>
<div id="mbW" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#799FEB;">
<img id="arrow_w" style="position:relative;top:0;left:0" src="../img/arrows/arrow_w.gif" width="10" height="15">
</div> 
<div id="mbNW" style="position:absolute;width:0;height:0;top:0;left:0;background-color:#799FEB;">
<img id="arrow_nw" style="position:relative;top:0;left:0" src="../img/arrows/arrow_nw.gif" width="10" height="10">
</div>' WHERE fkey_gui_id = 'gui2' AND e_id = 'navFrame';

-- layer 19011 and 19015 not queryable for gui2 for demo of tooltip 
UPDATE gui_layer SET gui_layer_queryable = 0, gui_layer_querylayer = 0 WHERE fkey_gui_id = 'gui2' AND fkey_layer_id = 19011 AND gui_layer_wms_id = 893; 
UPDATE gui_layer SET gui_layer_queryable = 0, gui_layer_querylayer = 0 WHERE fkey_gui_id = 'gui2' AND fkey_layer_id = 19015 AND gui_layer_wms_id = 893;

-- tooltip module in gui2 + required gui elements wfs and wfs conf
INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element,e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires,e_url) VALUES ('gui2','tooltip',1,1,'Tooltip demo modul','','div','','',1,1,1,1,NULL ,'visibility:hidden','','div','mod_tooltip.php','popup.js,geometry.js,wfsFilter.js','mapframe1','wfs,wfs_conf,featureInfoTunnel,popup','');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui2','wfs',2,1,'wfs request and result handling','','div','','',1,1,1,1,NULL ,'visibility:hidden','','div','wfs.php','../extensions/wz_jsgraphics.js,geometry.js','mapframe1,overview','','');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES('gui2', 'wfs', 'displayWfsResultList', '1', '' ,'var');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('gui2','wfs_conf',2,1,'get all wfs_conf-params','','iframe','../php/mod_wfs.php','frameborder = "0"',1,1,1,1,NULL ,'visibility:hidden','','iframe','','','','','');

-- element vars for tooltip
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_destinationFrame', '', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_noResultArray', E'["Kein Ergebnis.","<body onload=\"javascript:window.close()\">"]', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_styles', '.list_even{font-size:11px;color:red;}.list_uneven{font-size:11px;color:blue;}', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_styles_detail', '.list_even{font-size:11px;color:green;}.list_uneven{font-size:11px;color:blue;}', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_timeDelay', '1000', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'wfs_conf_filename', 'wfs_default.conf', '', 'php_var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_width', '270', '', 'var');
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) VALUES ('gui2', 'tooltip', 'tooltip_height', '200', '', 'var');

-- Update URL to Mapbender help
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/AddWMS' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'addWMS';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Back' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'back';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Copyright' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'copyright';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/DragMapSize' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'dragMapSize';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/DependentDiv' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'dependentDiv';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/FeatureInfo' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'featureInfo1';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/FeatureInfoRedirect' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'FeatureInfoRedirect';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/FeatureInfoTunnel' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'featureInfoTunnel';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Forward' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'forward';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/GazetteerWFS' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'gazetteerWFS';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Logout' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'logout';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Mapframe' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'mapframe1';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Metadata' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'metadata';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/NavFrame' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'navFrame';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Overview' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'overview';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Pan' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'pan1';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/Print' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'printPDF';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/SaveWMC' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'savewmc';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/SelArea1' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'selArea1';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/SetBackground' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'setBackground';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/ShowCoords_div' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'showCoords_div';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/TreeGde' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'treeGDE';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/WMS_preferences' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'WMS_preferences';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/ZoomFull' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'zoomFull';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/ZoomIn' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'zoomIn1';
UPDATE gui_element SET e_url = 'http://www.mapbender.org/index.php/ZoomOut' where fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND e_id= 'zoomOut1';

-- new language fr, es pt
Update gui_element_vars set var_value = 'de,en,bg,gr,nl,it,fr,es,pt' where fkey_gui_id = 'gui' AND fkey_e_id = 'switchLocale_noreload';

-- customisable tree in admin1
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('admin1','Customize Tree',0002,1,'Create a set of nested folders that contain the applications WMS','Customize Tree','a','','href = "../php/mod_customTree.php?sessionID" target="AdminFrame"',10,975,250,20,NULL ,'font-family: Arial, Helvetica, sans-serif; font-size : 12px; text-decoration : none; color: #808080;','CUSTOMIZE TREE','a','','','','AdminFrame','');


--Bugfix for treegde in gui (tabs wideout)
UPDATE gui_element SET e_more_styles='visibility:hidden; background-color:#ffffff;border: 1px solid #a19c8f;overflow:auto;' WHERE fkey_gui_id='gui' AND e_id='treeGDE';

--remove treeGDE-css statement from 'treeGDE' because its handled in 'body' element 
DELETE  from gui_element_vars WHERE fkey_gui_id IN ('gui','gui2','gui1','gui_digitize') AND fkey_e_id= 'treeGDE' AND var_name='cssfile';
