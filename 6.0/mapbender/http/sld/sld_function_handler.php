<?php
# $Id: sld_function_handler.php 2412 2008-04-23 16:14:30Z christoph $
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

include_once(dirname(__FILE__)."/sld_config.php");
include_once(dirname(__FILE__)."/../../conf/mapbender.conf");

/**
 * This function saves the data into the database
 * @param string $data the content of the sld-document to be stored inside the database
 */
function saveSld($data)
{	
	$con = db_connect($DBSERVER,$OWNER,$PW);
	db_select_db($DB,$con);
	$sql = "UPDATE sld_user_layer SET sld_xml=$1 WHERE fkey_gui_id=$2 AND fkey_layer_id=$3 AND fkey_mb_user_id=$4";
	$v = array($data, $_SESSION["sld_gui_id"], $_SESSION["sld_layer_id"], $_SESSION["mb_user_id"]);
	$t = array('s', 's', 'i', 'i');
	$res = db_prep_query($sql,$v,$t);
}


if (isset($_REQUEST["function"]))
{
	//MAIN FUNCTIONS:
	if ($_REQUEST["function"] == "getdefaultsld")
	{
		$file = $mapfileUrl."VERSION=1.1.1&REQUEST=GetStyles&LAYERS=".urlencode($layer_name);
		$data = readSld($file);
		$data = char_encode($data);
		saveSld($data);
	}
	else if ($_REQUEST["function"] == "save")
	{
		$styledlayerdescriptor = new StyledLayerDescriptor();
		$styledlayerdescriptor->generateObjectFromPost();
		saveSld($styledlayerdescriptor->generateXml());
	}
	/* the function "getusersld" is called by the mapserver to get the user's sld
	else if ($_REQUEST["function"] == "getusersld")
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
			else if ( isset($_REQUEST["layer_names"]) && isset($_REQUEST["user_id"]) )
		{
			$layer_names = split(",", urldecode($_REQUEST["layer_names"]));
			
			$con = db_connect($DBSERVER,$OWNER,$PW);
			db_select_db($DB,$con);
			
			#$sld_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<StyledLayerDescriptor version=\"1.0.0\">\n";
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
	} */
	// sets whether the user wishes to use a sld for this layer
	else if ($_REQUEST["function"] == "usesld")
	{
		if ( isset($_REQUEST["use_sld"]) )
		{
			$con = db_connect($DBSERVER,$OWNER,$PW);
			db_select_db($DB,$con);
			$sql = "UPDATE sld_user_layer SET use_sld=$1 WHERE fkey_gui_id=$2 AND fkey_layer_id=$3 AND fkey_mb_user_id=$4";
			$v = array($_REQUEST["use_sld"], $_SESSION["sld_gui_id"], $_SESSION["sld_layer_id"], $_SESSION["mb_user_id"]);
			$t = array('i', 's', 'i', 'i');
			$res = db_prep_query($sql,$v,$t);
			
			# update gui_wms_sldurl
			if ($_REQUEST["use_sld"]=="1") {
				$sld_url = $_REQUEST["mb_sld_url"];
				$sql = "UPDATE gui_wms SET gui_wms_sldurl=$1 WHERE fkey_gui_id=$2 AND fkey_wms_id=$3";
				$v = array($sld_url, $_SESSION["sld_gui_id"], $_SESSION["sld_wms_id"]);
				$t = array('s', 's', 'i');
				$res = db_prep_query($sql,$v,$t); 
			}
		}
	}
	
	
	
	
	//MANIPULATE SLD FUNCTIONS - ADD & DELETE
	else if ($_REQUEST["function"] == "addrule")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->addRule();
		}
	}
	else if ($_REQUEST["function"] == "deleterule")
	{
		if ( isset($_REQUEST["id"]) && isset($_REQUEST["number"]) && isset($_SESSION["sld"]) )
		{
			$number = $_REQUEST["number"];
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->deleteRule($number);
		}
	}
	else if ($_REQUEST["function"] == "addsymbolizer")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"]))&&(isset($_REQUEST["symbolizer"])))
		{
			$symbolizer = $_REQUEST["symbolizer"];
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->addSymbolizer($symbolizer);
		}
	}
	else if ($_REQUEST["function"] == "deletesymbolizer")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) && isset($_REQUEST["number"]) )
		{
			$number = $_REQUEST["number"];
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->deleteSymbolizer($number);
		}
	}
	else if ($_REQUEST["function"] == "addcssparameter")
	{
		if (isset($_REQUEST["id"]) && isset($_SESSION["sld"]) && isset($_REQUEST["cssparameter"]))
		{
			$cssparameter = $_REQUEST["cssparameter"];
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->addCssParameter($cssparameter);
		}
	}
	else if ($_REQUEST["function"] == "deletecssparameter")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) && isset($_REQUEST["number"]) )
		{
			$number = $_REQUEST["number"];
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->deleteCssParameter($number);
		}
	}

	else if ($_REQUEST["function"] == "addlegendgraphic")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->legendgraphic = new LegendGraphic();
		}
	}
	else if ($_REQUEST["function"] == "deletelegendgraphic")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->legendgraphic = "";
		}
	}
	
	else if ($_REQUEST["function"] == "addcolormapentry")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->addColorMapEntry();
		}
	}
	else if ($_REQUEST["function"] == "deletecolormapentry")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) && isset($_REQUEST["number"]) )
		{
			$number = $_REQUEST["number"];
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->deleteColorMapEntry($number);
		}
	}

	else if ($_REQUEST["function"] == "addexternalgraphicormark")
	{
		if ((isset($_REQUEST["id"])) && (isset($_SESSION["sld"])) && isset($_REQUEST["externalgraphicormark"]))
		{
			$externalgraphicormark = $_REQUEST["externalgraphicormark"];
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->addExternalGraphicOrMark($externalgraphicormark);
		}
	}
	else if ($_REQUEST["function"] == "deleteexternalgraphicormark")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) && isset($_REQUEST["number"]) )
		{
			$number = $_REQUEST["number"];
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->deleteExternalGraphicOrMark($number);
		}
	}
	
	else if ($_REQUEST["function"] == "addgraphic")
	{
		if ((isset($_REQUEST["id"])) && (isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->graphic = new Graphic();
		}
	}
	else if ($_REQUEST["function"] == "deletegraphic")
	{
		if ((isset($_REQUEST["id"])) && (isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->graphic = "";
		}
	}
	
	else if ($_REQUEST["function"] == "addcolormap")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->colormap = new ColorMap();
		}
	}
	else if ($_REQUEST["function"] == "deletecolormap")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) )
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->colormap = "";
		}
	}
	
	else if ($_REQUEST["function"] == "addfont")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->font = new Font();
		}
	}
	else if ($_REQUEST["function"] == "deletefont")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) )
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->font = "";
		}
	}
	
	else if ($_REQUEST["function"] == "addlabelplacement")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->labelplacement = new LabelPlacement();
		}
	}
	else if ($_REQUEST["function"] == "deletelabelplacement")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) )
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->labelplacement = "";
		}
	}

	else if ($_REQUEST["function"] == "addhalo")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->halo = new Halo();
		}
	}
	else if ($_REQUEST["function"] == "deletehalo")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) )
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->halo = "";
		}
	}
	
	else if ($_REQUEST["function"] == "addfill")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->fill = new Fill();
		}
	}
	else if ($_REQUEST["function"] == "deletefill")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) )
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->fill = "";
		}
	}
	
	else if ($_REQUEST["function"] == "addstroke")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->stroke = new Stroke();
		}
	}
	else if ($_REQUEST["function"] == "deletestroke")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) )
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->stroke = "";
		}
	}
	
	else if ($_REQUEST["function"] == "addgraphicfill")
	{
		if ((isset($_REQUEST["id"]))&&(isset($_SESSION["sld"])))
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->graphicfill = new GraphicFill();
		}
	}
	else if ($_REQUEST["function"] == "deletegraphicfill")
	{
		if ( isset($_REQUEST["id"]) && isset($_SESSION["sld"]) )
		{
			$id = $_REQUEST["id"];
			$_SESSION["sld_objects"][$id]->graphicfill = "";
		}
	}
	//TODO:
	//graphicstroke???
	
	else
	{
		echo "function ".$_REQUEST["function"]." is not defined!";
	}
	
	
	
	if ( $_REQUEST["function"] != "getdefaultsld" && $_REQUEST["function"] != "save" && $_REQUEST["function"] != "getusersld")
	{
		//Create the new SLD XML
		saveSld($_SESSION["sld_objects"][0]->generateXml());
	}
	if ( $_REQUEST["function"] != "getusersld" )
	{
		//header("Location: ".$MAPBENDER_URL."/sld/".$SLD_MAIN);
		//is ist faster to leave away the http?
		//redirect to a local file or to a http ressource?
		header("Location: ".$SLD_MAIN);
	}
}
?>