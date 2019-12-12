<?php
# $Id: sld_function_getusersld.php 3408 2009-01-07 09:16:05Z christoph $
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

/**
 * This file realizes the function handling for the sld editor.
 * Requests from the sld_edit_form are forwarded to this page.
 * After processing the requested function this file forwards to the
 * sld_main.php.
 * Only if the request is to get the sld it will not forward to sld_main.php - 
 * in this case this file will return the requested sld-document instead.
 * 
 * @package sld_function_handler
 * @author Markus Krzyzanowski
 */

include_once(dirname(__FILE__)."/classes/StyledLayerDescriptor.php");
include_once(dirname(__FILE__)."/../../conf/mapbender.conf");
include_once(dirname(__FILE__)."/../../core/globalSettings.php");
include_once(dirname(__FILE__)."/sld_parse.php");

/**
 * This function saves the data into the database
 * @param string $data the content of the sld-document to be stored inside the database
 */

if (isset($_REQUEST["function"]))
{
	// the function "getusersld" is called by the mapserver to get the user's sld
	if ($_REQUEST["function"] == "getusersld")
	{
		if ( isset($_REQUEST["sld_layer_id"]) && isset($_REQUEST["sld_gui_id"]) && isset($_REQUEST["user_id"]) )
		{ //Used for the preview
			$con = db_connect($DBSERVER,$OWNER,$PW);
			db_select_db($DB,$con);
			$sql = "SELECT * FROM sld_user_layer WHERE fkey_gui_id=$1 AND fkey_layer_id=$2 AND fkey_mb_user_id=$3";
			$v = array($_REQUEST["sld_gui_id"], $_REQUEST["sld_layer_id"], $_REQUEST["user_id"]);
			$t = array('s', 'i', 'i');
			$res = db_prep_query($sql,$v,$t);			

			if ( db_fetch_row($res, 0) )
			{
				//forcesld is used for the preview image to force the sld
				if ( db_result($res, 0, "use_sld") == "1" || $_REQUEST["forcesld"] == "1" )
				{
					echo db_result($res, 0, "sld_xml");
				}
			}
		} //Used for mapbender integration - old deprecated
		else if ( isset($_REQUEST["sld_layer_names"]) && isset($_REQUEST["user_id"]) )
		{
			$layer_names = split(",", urldecode($_REQUEST["sld_layer_names"]));
			
			$con = db_connect($DBSERVER,$OWNER,$PW);
			db_select_db($DB,$con);
			
			/*$sld_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<StyledLayerDescriptor version=\"1.0.0\">\n";*/
			$sld_xml = "<StyledLayerDescriptor version=\"1.0.0\" xmlns=\"http://www.opengis.net/sld\" xmlns:ogc=\"http://www.opengis.net/ogc\">\n";
			
			foreach ($layer_names as $layer_name)
			{
				$sql = "SELECT * FROM layer WHERE layer_name=$1";
				$v = array($layer_name);
				$t = array('s');
				$res = db_prep_query($sql,$v,$t);				

				$layer_id = "";
				if ( db_fetch_row($res, 0) )
				{
					$layer_id = db_result($res, 0, "fkey_layer_id");
					$sql = "SELECT * FROM sld_user_layer WHERE fkey_layer_id=$1 AND fkey_mb_user_id=$2";
					$v = array($layer_id, $_REQUEST["user_id"]);
					$t = array('i', 'i');
					$res = db_prep_query($sql,$v,$t);
					
					if ( db_fetch_row($res, 0) )
					{
						if ( db_result($res, 0, "use_sld") == "1" )
						{
							$data = db_result($res, 0, "sld_xml");
							$styledlayerdescriptor = parseSld($data);
							$sld_xml .= $styledlayerdescriptor->layers[0]->generateXml(" ");
						}
					}
				}
			}
			$sld_xml .= "</StyledLayerDescriptor>";
			echo $sld_xml;
		} //Used for mapbender integration
		else if ( isset($_REQUEST["sld_wms_id"]) && isset($_REQUEST["sld_gui_id"]) )
		{
		
			$con = db_connect($DBSERVER,$OWNER,$PW);
			db_select_db($DB,$con);
			$sql = "SELECT fkey_layer_id FROM gui_layer WHERE fkey_gui_id=$1 AND gui_layer_wms_id=$2";
			$v = array($_REQUEST["sld_gui_id"], $_REQUEST["sld_wms_id"]);
			$t = array('s', 'i');
			$res = db_prep_query($sql,$v,$t);
			
			$sld_xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
			$sld_xml .= "<StyledLayerDescriptor version=\"1.0.0\" xmlns=\"http://www.opengis.net/sld\" xmlns:ogc=\"http://www.opengis.net/ogc\">\n";			

			while ($row = db_fetch_row($res))
			{
				$layer_id = $row[0];
				$sql = "SELECT * FROM sld_user_layer WHERE fkey_layer_id=$1 AND fkey_gui_id=$2";
				$v = array($layer_id, $_REQUEST["sld_gui_id"]);
				$t = array('i', 's');
				$res2 = db_prep_query($sql,$v,$t);

				if ( db_fetch_row($res2, 0) )
				{
					if ( db_result($res2, 0, "use_sld") == "1" )
					{
						$data = db_result($res2, 0, "sld_xml");
						$styledlayerdescriptor = parseSld($data);
						$sld_xml .= $styledlayerdescriptor->layers[0]->generateXml(" ");
					}
				}
			}
			$sld_xml .= "</StyledLayerDescriptor>";
			echo $sld_xml;
		}
	}
}
?>
