/**
 * Package: ol_wms
 *
 * Description:
 * Loads configured WMS from the Mapbender database and loads them into OpenLayers
 * 
 * Files:
 *  - http/plugins/ol_wms.php
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>','ol_wms',2,1,
 * > 'Load configured WMS from Mapbender application settings into OpenLayers',
 * > '','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div',
 * > '../plugins/ol_wms.php','','ol','','http://www.mapbender.org/ol_wms');
 *
 * Help:
 * http://www.mapbender.org/ol_wms
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var ol_map = Mapbender.modules[options.target[0]];
ol_map.mapbenderEvents.mapInstantiated.register(function () {
	var ol_map = Mapbender.modules[options.target[0]];
<?php

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_wms.php");

$sql = "SELECT fkey_wms_id FROM gui_wms WHERE fkey_gui_id = $1 ORDER BY gui_wms_position";
$v = array(Mapbender::session()->get("mb_user_gui"));
$t = array('s');
$res = db_prep_query($sql,$v,$t);

$cnt=0;
while($row = db_fetch_array($res)){
	$mywms = new wms();
	$mywms->createObjFromDB(Mapbender::session()->get("mb_user_gui"),$row["fkey_wms_id"]);
	// create the first OL-layer as baselayer
	$isBaseLayer = ($cnt === 0) ? true : false;
	$mywms->createOlObjFromWMS( $isBaseLayer );
	$cnt++;
}
?>
	// fire the mapInstantiated event
	// often the controls will be listening to this event 
	ol_map.mapbenderEvents.layersAdded.trigger();
	// fire the mapInstantiated event
	ol_map.mapbenderEvents.mapReady.trigger();
});


