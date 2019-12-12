<?php
# $Id: DbUtils.php 2412 2008-04-23 16:14:30Z christoph $
# http://www.mapbender.org/index.php/SLD
# Copyright (C) 2002 CCGIS 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

require_once(dirname(__FILE__)."/../../../conf/mapbender.conf");
$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);

/**
 * This class wraps db functions of the sld implementation.
 *
 * @package sld_classes
 * @author Michael Schulz
 */
class DbUtils 
{
    /**
     * Read the layer_id from the DB.
     * 
     * @param <int> the wms_id
     * @param <string> the layer_name
     * @return <int> the layer_id
     */
	function getLayerIdFromLayerName($wms_id, $layer_name) {
		$sql = "SELECT * FROM layer WHERE fkey_wms_id = $1 AND layer_name = $2";
		$v = array($wms_id, $layer_name);
		$t = array('i', 's');
		$res = db_prep_query($sql,$v,$t);
		if ( db_fetch_row($res, 0) ) {
			return db_result($res, 0, "layer_id");
		} else {
			return false;
		}
	}
	
	/**
     * Check for a layer wfs-Conf.
     * 
     * @param <int> the layer_id
     * @param <string> the gui_id
     * @return <int> wfs_conf_id
     */
	function getLayerWfsConfId($gui_id, $layer_id) {
		$sql = "SELECT gui_layer_wfs_featuretype FROM gui_layer WHERE fkey_gui_id = $1 AND fkey_layer_id = $2";
		$v = array($gui_id, $layer_id);
		$t = array('s', 'i');
		$res = db_prep_query($sql,$v,$t);
		if ( db_fetch_row($res, 0) ) {
			return db_result($res, 0, "gui_layer_wfs_featuretype");
		} else {
			return false;
		}
	}

	/**
     * return layer wfs-FeatureType-id.
     * 
     * @param <int> the wfs_conf_id
     * @return <int> fkey_featuretype_id
     */
	function getWfsConfFeatureTypeId($wfs_conf_id) {
		$sql = "SELECT fkey_featuretype_id FROM wfs_conf WHERE wfs_conf_id = $1";
		$v = array($wfs_conf_id);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		if ( db_fetch_row($res, 0) ) {
			return db_result($res, 0, "fkey_featuretype_id");
		} else {
			return false;
		}
	}	
	
	/**
	 * create a getmap request for the map images
	 * 
	 * @param <string> gui_id
	 * @param <int> layer_id
	 * @param <int> wms_id
	 *
	 * @return <string> getmap URL
	 */
	function getPreviewMapUrl($gui_id, $layer_id, $wms_id) {
		$previewMapSrs = "";
		$sql = "select w.gui_wms_epsg as srs, e.minx, e.miny, e.maxx, e.maxy ";
		$sql .= "from gui_wms w, layer l, layer_epsg e ";
		$sql .= "where l.fkey_wms_id=w.fkey_wms_id and e.fkey_layer_id=l.layer_id and e.epsg=w.gui_wms_epsg and ";
		$sql .= "l.layer_parent = '' and w.fkey_gui_id = $1 and w.gui_wms_position=0";
		$v = array($gui_id);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		if ( $res ) {
			$row = db_fetch_array($res, 0); 
			$previewMapSrs .= "&SRS=".$row["srs"]."&BBOX=".$row["minx"].",".$row["miny"].",".$row["maxx"].",".$row["maxy"];
		} else {
			return false;
		}
		
		$sql = "select wms.wms_getmap as mapurl, layer.layer_name, wms.wms_version as version, w.gui_wms_mapformat as format ";
		$sql .= "from gui_wms w, gui_layer l, wms, layer ";
		$sql .= "where w.fkey_gui_id=l.fkey_gui_id and wms.wms_id = w.fkey_wms_id and l.fkey_layer_id = layer.layer_id and ";
		$sql .= "w.fkey_gui_id=$1 and l.fkey_layer_id=$2 and wms.wms_id = $3";
		$v = array($gui_id, $layer_id, $wms_id);
		$t = array('s', 'i', 'i');
		$res = db_prep_query($sql,$v,$t);
		$previewMapUrl = "";
		if ( $res ) {
			$row = db_fetch_array($res, 0); 
			$previewMapUrl .= $row["mapurl"]."SERVICE=WMS&REQUEST=GetMap&VERSION=".$row["version"];
			$previewMapUrl .= "&LAYERS=".$row["layer_name"]."&STYLES=";
			$previewMapUrl .= $previewMapSrs;
			$previewMapUrl .= "&FORMAT=".$row["format"];
			return $previewMapUrl;
		} else {
			return false;
		}
	}
}

?>