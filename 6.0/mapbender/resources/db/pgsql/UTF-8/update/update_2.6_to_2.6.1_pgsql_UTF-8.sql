--
-- mapframe1 set e_mb_mod - sets background to white
--
UPDATE gui_element SET 
e_mb_mod = 'map_obj.js,map.js,wms.js,wfs_obj.js,initWms.php',
e_more_styles = 'overflow:hidden;background-color:white'
WHERE e_id = 'mapframe1';

--
-- wfs target mapframe1 and overview
-- 
UPDATE gui_element SET e_js_file = 'wfs.php', e_target='mapframe1' WHERE e_id = 'wfs';
UPDATE gui_element SET e_js_file = 'wfs.php', e_target='mapframe1,overview' WHERE e_id = 'wfs' 
AND fkey_gui_id IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'overview');


--
-- element wfs
-- 
UPDATE gui_element SET e_mb_mod = '../extensions/wz_jsgraphics.js,geometry.js' WHERE e_id = 'wfs';
 


--
-- update e_attributes for some elements - new definition
--
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'forward';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'back';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'zoomIn1';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'zoomOut1';
UPDATE gui_element SET e_attributes = '' WHERE e_id = 'measure';

--
-- delete deprecated elements
-- 
DELETE FROM gui_element WHERE e_id = 'closePolygon';
DELETE FROM gui_element WHERE e_id = 'rubber';
DELETE FROM gui_element WHERE e_id = 'getArea';
DELETE FROM gui_element WHERE e_id = 'rubberExt';

--
-- gazetteerWFS - add new elements var where needed (showResultInPopup)
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'gazetteerWFS', 'showResultInPopup', '1', 
'if value is 1 search results will be displayed in popup, otherwise in gazetteer div' ,'var' FROM gui_element 
WHERE gui_element.e_id = 'gazetteerWFS' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'showResultInPopup' AND fkey_e_id = 'gazetteerWFS'); 


-- gazetteerWFS - element var wfs_spatial_request_conf_filename for gui element gazetteerWFS
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'gazetteerWFS', 'wfs_spatial_request_conf_filename', 'wfs_additional_spatial_search.conf', 
'location and name of the WFS configuration file for spatialRequest' ,'php_var' FROM gui_element 
WHERE gui_element.e_id = 'gazetteerWFS' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'wfs_spatial_request_conf_filename' AND fkey_e_id = 'gazetteerWFS'); 

	
	
--
-- gazetteerWFS add tablesorter.js
--
UPDATE gui_element 
SET e_mb_mod = 'geometry.js,requestGeometryConstructor.js,popup.js,../extensions/jquery.tablesorter.js' 
WHERE e_id = 'gazetteerWFS';


--
-- element var popupcss for gui element body
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'body', 'popupcss', '../css/popup.css', 'file css', 'file/css' 
FROM gui_element WHERE gui_element.e_id = 'body' 
AND gui_element.fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element_vars 
WHERE var_name = 'popupcss' AND fkey_e_id = 'body') 
AND fkey_gui_id NOT LIKE 'wms_%' AND fkey_gui_id NOT LIKE 'admin%' ; 


--
-- element var tablesortercss for gui element body
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'body', 'tablesortercss', '../css/tablesorter.css', 'file css' ,'file/css' 
FROM gui_element WHERE gui_element.e_id = 'body' 
AND gui_element.fkey_gui_id NOT IN (SELECT fkey_gui_id FROM gui_element_vars 
WHERE var_name = 'tablesortercss' AND fkey_e_id = 'body') 
AND fkey_gui_id NOT LIKE 'wms_%' AND fkey_gui_id NOT LIKE 'admin%' ; 


--#style in gui_digitize
-- ---------------------
-- add style class to element var text css of element digitize in gui_digitize
UPDATE gui_element_vars 
SET var_value = 
'digitizeGeometryList {position:absolute; top:50px; left:0px;}
.digitizeGeometryListItem {color:#000000; font-size:10px;}
body {font-family: Arial, Helvetica, sans-serif; font-size:12px; color:#ff00ff; background-color:#ffffff; margin-top: 0px; margin-left:0px;}
.button {height:18px; width:32px;}' 
WHERE fkey_e_id = 'digitize' 
  AND var_name  = 'text css';


--
-- overview as div
--
-- element_var for overview_wms
-- wms=int from former overview definition and add it to the new element_var (example ../php/mod_mapOV.php?wms=22&sessionID)
INSERT INTO gui_element_vars 
select fkey_gui_id, 'overview' as fkey_e_id, 'overview_wms' as var_name, 
substr(e_src,strpos(e_src,'wms=')+4,strpos(e_src,'&')-strpos(e_src,'wms=')-4) as var_value, 
'wms that shows up as overview' as context, 
'var' as var_type from gui_element where e_id = 'overview' 
AND strpos(e_src,'wms=') > 0;

--
-- update overview as a div
--
UPDATE gui_element SET e_element = 'div', 
e_src = '', 
e_attributes = '', 
e_more_styles = 'overflow:hidden;', 
e_content = '<div id="overview_maps" style="position:absolute;left:0px;right:0px;"></div>', 
e_closetag = 'div', 
e_js_file = 'ovnf.php', 
e_target = 'mapframe1', 
e_requires = 'mapframe1' WHERE e_id = 'overview';


--
-- UPDATE treeGDE - div
-- 
--
-- treeGDE new element_var for treeGDE in body
--
-- css is now part of body and not treeGDE
UPDATE gui_element_vars set 
fkey_e_id ='body',
var_name = 'treeGDE_css'
where fkey_e_id = 'treeGDE'
and var_type = 'file/css';


--
-- treeGDE - div
-- 
UPDATE gui_element SET 
e_pos = 2, 
e_element = 'div', 
e_more_styles = 'visibility:visible;overflow:scroll', 
e_content = '', e_closetag = 'div', 
e_js_file = '../html/mod_treefolderPlain.php', 
e_mb_mod = 'jsTree.js', e_requires = 'mapframe1' WHERE e_id = 'treeGDE';


--
-- remove treeGDE-css statement from 'treeGDE' because its handled in 'body' element 
--
DELETE  from gui_element_vars WHERE fkey_e_id= 'treeGDE' AND var_name='cssfile';
-- End treeGDE


--
-- Update ChangeEPSG
--
-- Notice: EPSG-Codes are now defined in e_content and not in the file itself
--
UPDATE gui_element SET e_element = 'select', e_src = '', e_attributes = '', e_content = '<option value="">undefined</option>
<option value="EPSG:4326">EPSG:4326</option>
<option value="EPSG:31466">EPSG:31466</option>
<option value="EPSG:31467">EPSG:31467</option>
<option value="EPSG:31468">EPSG:31468</option>
<option value="EPSG:31469">EPSG:31469</option>', e_closetag = 'select', e_js_file = 'mod_changeEPSG.php' 
WHERE e_id = 'changeEPSG';


--
-- zoomCoords - new definition
--
UPDATE gui_element SET e_element = 'div', e_src = '',
e_attributes = '', 
e_more_styles = 'overflow:hidden;', 
e_content = '', e_closetag = 'div', 
e_js_file = 'mod_zoomCoords.php', 
e_target = 'mapframe1', 
e_requires = 'mapframe1' WHERE e_id = 'zoomCoords';

UPDATE gui_element SET e_element = 'div', e_src = '',
e_attributes = '', 
e_more_styles = 'overflow:hidden;', 
e_content = '', e_closetag = 'div', 
e_js_file = 'mod_zoomCoords.php', 
e_target = 'mapframe1,overview', 
e_requires = 'mapframe1' WHERE e_id = 'zoomCoords' 
AND fkey_gui_id IN (SELECT fkey_gui_id FROM gui_element WHERE e_id = 'overview');


-- 
-- splash screen - add splash to all application
--
INSERT INTO gui_element_vars 
SELECT gui_element.fkey_gui_id, 'body', 'use_load_message', 'true' as var_value,
'show splash screen while the application is loading' as context ,
'php_var' as var_type 
FROM gui_element WHERE gui_element.e_id = 'body' AND 
gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'use_load_message' AND fkey_e_id = 'body') 
AND fkey_gui_id NOT LIKE 'wms_%' AND fkey_gui_id NOT LIKE 'admin%' ; 

INSERT INTO gui_element_vars 
SELECT gui_element.fkey_gui_id, 'body', 'includeWhileLoading', 
'' as var_value,
'show splash screen while the application is loading' as context ,
'php_var' as var_type 
FROM gui_element WHERE gui_element.e_id = 'body' AND 
gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'includeWhileLoading' AND fkey_e_id = 'body') 
AND fkey_gui_id NOT LIKE 'wms_%' AND fkey_gui_id NOT LIKE 'admin%' ; 


--
-- gui: tab: increase the size of the frames onmouseover
--
UPDATE gui_element SET e_attributes = 'frameborder = "0" onmouseover="this.style.zIndex=300;this.style.width=350;" onmouseout="this.style.zIndex=2;this.style.width=200"',
e_more_styles = 'visibility:hidden; border: 1px solid #a19c8f;' 
WHERE e_id IN ('treeGDE','printPDF','legend','imprint','meetingPoint','gazetteerWFS') 
AND fkey_gui_id IN ('gui');
-- ## UPDATE gui_element SET e_more_styles = e_more_styles || ' background-color:#FFFFFF;' WHERE e_id IN ('treeGDE','imprint') AND fkey_gui_id IN ('gui');


--
-- setSpatialRequest
--
UPDATE gui_element SET e_mb_mod = 'geometry.js,requestGeometryConstructor.js,popup.js' WHERE e_id = 'setSpatialRequest'; 


-- element var cssfileAddWMS for gui element addWMSfromfilteredList_ajax
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'addWMSfromfilteredList_ajax', 'cssfileAddWMS', '../css/addwms.css', '' ,'file/css' 
FROM gui_element WHERE gui_element.e_id = 'addWMSfromfilteredList_ajax' AND 
gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'cssfileAddWMS' AND fkey_e_id = 'addWMSfromfilteredList_ajax'); 

--
-- element var capabilitiesInput for gui element addWMSfromfilteredList_ajax
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'addWMSfromfilteredList_ajax', 'capabilitiesInput', '1', 'load wms by capabilities url' ,'var' 
FROM gui_element WHERE gui_element.e_id = 'addWMSfromfilteredList_ajax' 
AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'capabilitiesInput' AND fkey_e_id = 'addWMSfromfilteredList_ajax'); 

--
-- element var option_dball for gui element addWMSfromfilteredList_ajax
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'addWMSfromfilteredList_ajax', 'option_dball', '1', '1 enables option "load all configured wms from db"' ,'var' 
FROM gui_element WHERE gui_element.e_id = 'addWMSfromfilteredList_ajax' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars 
WHERE var_name = 'option_dball' AND fkey_e_id = 'addWMSfromfilteredList_ajax'); 

--
-- element var option_dbgroup for gui element addWMSfromfilteredList_ajax
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'addWMSfromfilteredList_ajax', 'option_dbgroup', '1', '1 enables option "load configured wms by group"' ,'var' 
FROM gui_element WHERE gui_element.e_id = 'addWMSfromfilteredList_ajax' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'option_dbgroup' AND fkey_e_id = 'addWMSfromfilteredList_ajax'); 

--
-- element var option_dbgui for gui element addWMSfromfilteredList_ajax
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'addWMSfromfilteredList_ajax', 'option_dbgui', '1', '1 enables option "load configured wms by gui"' ,'var' 
FROM gui_element WHERE gui_element.e_id = 'addWMSfromfilteredList_ajax' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'option_dbgui' AND fkey_e_id = 'addWMSfromfilteredList_ajax'); 


--  
-- delete old element_vars for 'addWMSfromfilteredList_ajax' completely
DELETE FROM gui_element_vars
WHERE fkey_e_id = 'addWMSfromfilteredList_ajax'
and var_name NOT IN ('cssfileAddWMS','capabilitiesInput','option_dbgui','option_dball','option_dbgroup');


--
-- featureinfoTunnel - element var featureInfoPopupHeight for gui element featureInfoTunnel
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'featureInfoTunnel', 'featureInfoPopupHeight', '200', '' ,'var' 
FROM gui_element WHERE gui_element.e_id = 'featureInfoTunnel' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'featureInfoPopupHeight' AND fkey_e_id = 'featureInfoTunnel'); 

--
-- featureinfoTunnel - element var featureInfoPopupWidth for gui element featureInfoTunnel
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'featureInfoTunnel', 'featureInfoPopupWidth', '270', '' ,'var' 
FROM gui_element WHERE gui_element.e_id = 'featureInfoTunnel' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'featureInfoPopupWidth' AND fkey_e_id = 'featureInfoTunnel'); 

--
-- featureinfoTunnel
-- element var featureInfoLayerPopup for gui element featureInfoTunnel
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'featureInfoTunnel', 'featureInfoLayerPopup', 'false', '' ,'var' 
FROM gui_element WHERE gui_element.e_id = 'featureInfoTunnel' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'featureInfoLayerPopup' AND fkey_e_id = 'featureInfoTunnel');

--
-- element var tooltip_noResultArray for gui element tooltip
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'tooltip', 'tooltip_noResultArray', E'["Kein Ergebnis.","<body onload=\'javascript:window.close()\'>"]', '', 'var' FROM gui_element 
WHERE gui_element.e_id = 'tooltip' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'tooltip_noResultArray' AND fkey_e_id = 'tooltip'); 

--
-- element var tooltip_disableWfs for gui element tooltip
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'tooltip', 'tooltip_disableWfs', '0', 'disable WFS getFeature Request', 'var' 
FROM gui_element WHERE gui_element.e_id = 'tooltip' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'tooltip_disableWfs' AND fkey_e_id = 'tooltip'); 

--
-- element var tooltip_disableWms for gui element tooltip
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'tooltip', 'tooltip_disableWms', '1', 'disable WMS getFeatureInfo Request', 'var' 
FROM gui_element WHERE gui_element.e_id = 'tooltip' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'tooltip_disableWms' AND fkey_e_id = 'tooltip'); 


-- 
-- body add favicon - element var favicon for gui element body
--
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'body', 'favicon', '../img/favicon.png', 'favicon' ,'php_var' FROM gui_element 
WHERE gui_element.e_id = 'body' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'favicon' AND fkey_e_id = 'body'); 


-- element var switchwms for gui element treeGDE
INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) 
SELECT gui_element.fkey_gui_id, 'treeGDE', 'switchwms', 'true', 'enables/disables all layer of a wms' ,'var' 
FROM gui_element WHERE gui_element.e_id = 'treeGDE' AND gui_element.fkey_gui_id NOT IN 
(SELECT fkey_gui_id FROM gui_element_vars WHERE var_name = 'switchwms' AND fkey_e_id = 'treeGDE'); 


-- missing wz_jsgraphics in gazetteer
-- replacing obsolete tablesorter
UPDATE gui_element SET e_mb_mod = 'geometry.js,requestGeometryConstructor.js,popup.js,../extensions/wz_jsgraphics.js,../extensions/jquery.tablesorter.js' WHERE e_id = 'gazetteerWFS';

-- http://trac.osgeo.org/mapbender/ticket/590 toggle Module and measure
UPDATE gui_element set e_attributes ='onmouseover = "mb_regButton(''init_mod_measure'')"' where e_id='measure';

-- http://trac.osgeo.org/mapbender/ticket/632 
/*loadWMSinternal zu Admin-Guis hinzufügen*/
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin2_de','loadWMSinternal',2,0,'Capabilities hochladen interne Datei, nicht aktiv schalten!',	'','','','href = "../php/mod_loadwms.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','Capabilities hochladen interne Funktion','','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin_de_services','loadWMSinternal',2,0,'Capabilities hochladen interne Datei, nicht aktiv schalten!',	'','','','href = "../php/mod_loadwms.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','Capabilities hochladen interne Funktion','','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');
/*loadWMSinternal added to admin-guis*/
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin1','loadWMSinternal',2,0,'load Capabilities internal file, has not to be active!','','','','href = "../php/mod_loadwms.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','load capabilities internal function','','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin_en_services','loadWMSinternal',2,0,'load Capabilities internal file, has not to be active!','','','','href = "../php/mod_loadwms.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','load capabilities internal function','','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin2_en','loadWMSinternal',2,0,'load Capabilities internal file, has not to be active!','','','','href = "../php/mod_loadwms.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','load capabilities internal function','','','','','','http://www.mapbender.org/index.php/Add_new_maps_to_Mapbender');

/*loadCSWinternal zu Admin-Guis hinzufügen*/
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin2_de','loadCSWinternal',2,0,'CatalogCapabilities hochladen interne Datei, nicht aktiv schalten!',	'','','','href = "../php/mod_loadCatalog.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','Capabilities hochladen interne Funktion','','','','','','http://www.mapbender.org/index.php/newGUI');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin_de_services','loadCSWinternal',2,0,'CatalogCapabilities hochladen interne Datei, nicht aktiv schalten!',	'','','','href = "../php/mod_loadCatalog.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','Capabilities hochladen interne Funktion','','','','','','http://www.mapbender.org/index.php/newGUI');
/*loadCSWinternal added to admin-guis*/
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin1','loadCSWinternal',2,0,'load Catalog Capabilities internal file, has not to be active!','','','','href = "../php/mod_loadCatalog.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','load capabilities internal function','','','','','','http://www.mapbender.org/index.php/newGUI');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin_en_services','loadCSWinternal',2,0,'load Catalog Capabilities internal file, has not to be active!','','','','href = "../php/mod_loadCatalog.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','load capabilities internal function','','','','','','http://www.mapbender.org/index.php/newGUI');
INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content,	e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) 	VALUES('admin2_en','loadCSWinternal',2,0,'load Catalog Capabilities internal file, has not to be active!','','','','href = "../php/mod_loadCatalog.php"',NULL ,NULL ,NULL ,NULL ,NULL ,'','load capabilities internal function','','','','','','http://www.mapbender.org/index.php/newGUI');


