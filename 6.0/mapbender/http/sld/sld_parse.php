<?php
# $Id: sld_parse.php 2412 2008-04-23 16:14:30Z christoph $
# http://www.mapbender.org/index.php/SLD/sld_config.php
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
 * Parses the sld-documents and creates the object structure.
 * This file does the parsing of the sld-data.
 * For all sld-elements that cannot be converted to a trivial datatype
 * a corresponding object is created.
 *
 * Creates the sld_objects and the sld_parent $_SESSION variables.
 * sld_objects is an array that contains the objects.
 * sld_parent is an array that contains the index of the object's parent object in sld_objects.
 * These variables are used to easily access the objects.
 *
 * @see sld_classes.php contains the class definitions
 * @package sld_parse
 * @author Markus Krzyzanowski
 */

//require_once(dirname(__FILE__)."/../php/mb_validateSession.php");

/**
 *  Creates a empty SLD object up to the rule object
 * 
 *  @return StyledLayerDescriptor
 */
function createEmptySLD() {
	$styledlayerdescriptor = new StyledLayerDescriptor();
	$layer = new NamedLayer();
	$style = new UserStyle();
	$fts = new FeatureTypeStyle();
	$rule = new Rule();
	
	$fts->rules[] = $rule;
	$style->featuretypestyles[] = $fts;
	$layer->styles[] = $style;
	$layer->name = $_SESSION["sld_layer_name"];
	$styledlayerdescriptor->layers[] = $layer;
	$styledlayerdescriptor->version = "1.0.0";
	$styledlayerdescriptor->id = 0;
	$styledlayerdescriptor->parent = false;
	
	return $styledlayerdescriptor;
}

/**
 * Opens the file at the specified URL and returns the content.
 *
 * @param string $file URL of the file
 * @return string content of the file
 */
function readSld($file)
{
	if (!($fp = fopen($file, "rb")))
	{
		die("could not open XML input - ".$file);
	}
	
	$data = "";
	
	while(!feof($fp))
	{
		$data .= fread($fp, 1024);

	}
	return $data;
}

/**
 * Parses the data and creates the object structure.
 *
 * @param string $data data to be parsed
 * @return StyledLayerDesciptor root object of the sld-document
 */
function parseSld($data)
{
	$xml_parser = xml_parser_create();
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parse_into_struct($xml_parser, $data, $vals, $index);
	xml_parser_free($xml_parser);
	
	$styledlayerdescriptor;
	$parent = array();
	$objects = array();
	$parentactual = 0;
	
	for ($i=0; $i<count($vals); $i++)
	{
		$element = $vals[$i];
		$tag = $element["tag"];
		$tagname=strtoupper($tag);
		switch($tagname)
		{
			// uh, the server responded with an error, should be break or start with an empty sld?
			case "SERVICEEXCEPTIONREPORT":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = false;
					$parentactual = 0;
					/*
					$styledlayerdescriptor = new StyledLayerDescriptor();
					$styledlayerdescriptor->version = "1.0.0";
					//Experimental:
					*/
					$styledlayerdescriptor = createEmptySLD();
					$styledlayerdescriptor->id = 0;
					$styledlayerdescriptor->parent = false;
					//END Experimental
					$objects[] = &$styledlayerdescriptor;
					if ($element["type"] == "complete")
					{
						$parentactual = false;
					}
					$_SESSION["sld_objects"] = $objects;
					$_SESSION["sld_parent"] = $parent;
					return $styledlayerdescriptor;
				}				
			case "STYLEDLAYERDESCRIPTOR":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = false;
					$parentactual = 0;
					$styledlayerdescriptor = new StyledLayerDescriptor();
					$styledlayerdescriptor->version = $element["attributes"]["version"];
					//Experimental:
					$styledlayerdescriptor->id = 0;
					$styledlayerdescriptor->parent = false;
					//END Experimental
					$objects[] = &$styledlayerdescriptor;
					if ($element["type"] == "complete")
					{
						$parentactual = false;
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "NAMEDLAYER":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new NamedLayer();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->layers[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "NAME":
				if ($element["type"] == "complete")
				{
					//TODO - workaround suchen...!!!
					//echo "-------------\n";
					//echo $element["value"]."\n";
					//$element["value"] = str_replace("<","&lt;",$element["value"]);
					//$element["value"] = str_replace(">","&gt;",$element["value"]);
					$objects[$parentactual]->name = $element["value"];
				}
				break;
			case "TITLE":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->title = $element["value"];
				}break;
			case "ABSTRACT":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->abstract = $element["value"];
				}
				break;
			case "MINSCALEDENOMINATOR":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->minscaledenominator = $element["value"];
				}
				break;
			case "MAXSCALEDENOMINATOR":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->maxscaledenominator = $element["value"];
				}
				break;
			case "USERSTYLE":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new UserStyle();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->styles[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "FEATURETYPESTYLE":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new FeatureTypeStyle();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->featuretypestyles[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "RULE":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new Rule();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->rules[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "LINESYMBOLIZER":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new LineSymbolizer();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->symbolizers[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "POLYGONSYMBOLIZER":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new PolygonSymbolizer();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->symbolizers[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "POINTSYMBOLIZER":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new PointSymbolizer();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->symbolizers[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "TEXTSYMBOLIZER":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new TextSymbolizer();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->symbolizers[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "RASTERSYMBOLIZER":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new RasterSymbolizer();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->symbolizers[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "LEGENDGRAPHIC":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new LegendGraphic();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->legendgraphic = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
	//Commented out - Filters will be created through the creation of xml in the default case
	//		case "ELSEFILTER":
	//			if ($element["type"] == "complete")
	//			{
	//				$objects[$parentactual]->filter = new ElseFilter();
	//			}
	//			break;
	//		case "OGC:FILTER": //TODO
	//			if ($element["type"] == "complete")
	//			{
	//				//Create a new ElseFilter and write it to the parent object
	//				$objects[$parentactual]->filter = new ElseFilter();
	//			}
	//			break;
			case "GRAPHIC":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new Graphic();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					
					$objects[$parentactual]->graphic = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "EXTERNALGRAPHIC":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new ExternalGraphic();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->externalgraphicsormarks[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "ONLINERESOURCE":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->onlineresource = $element["attributes"]["xlink:href"];
				}
				break;
			case "FORMAT":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->format = $element["value"];
				}
				break;
			case "STROKE":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new Stroke();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->stroke = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "CSSPARAMETER":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->cssparameters[] = new CssParameter();
					//Experimental:
					$objects[$parentactual]->cssparameters[count($objects[$parentactual]->cssparameters)-1]->id = count($parent)-1;
					$objects[$parentactual]->cssparameters[count($objects[$parentactual]->cssparameters)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->cssparameters[count($objects[$parentactual]->cssparameters)-1]->name = $element["attributes"]["name"];
					$objects[$parentactual]->cssparameters[count($objects[$parentactual]->cssparameters)-1]->value = $element["value"];
				}
				break;
			case "GRAPHICFILL":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new GraphicFill();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					
					
					//print_r($objects[count($parent)-1]);
					
					$objects[$parentactual]->graphicfill = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "GRAPHICSTROKE":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new GraphicStroke();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->graphicstroke = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "FILL":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new Fill();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->fill = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
					}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "MARK":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new Mark();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->externalgraphicsormarks[] = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "WELLKNOWNNAME":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->wellknownname = $element["value"];
				}
				break;
			case "FONT":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new Font();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->font = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "LABELPLACEMENT":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new LabelPlacement();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->labelplacement = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "POINTPLACEMENT":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new PointPlacement();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->placement = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "ANCHORPOINT":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new AnchorPoint();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->anchorpoint = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "DISPLACEMENT":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new Displacement();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->displacement = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "HALO":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new Halo();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->halo = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "COLORMAP":
				if ($element["type"] == "open" || $element["type"] == "complete")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new ColorMap();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->colormap = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					if ($element["type"] == "complete")
					{
						$parentactual = $parent[$parentactual];
					}
				}
				else if($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "COLORMAPENTRY":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->colormapentries[] = new ColorMapEntry();
					//Experimental:
					$objects[$parentactual]->colormapentries[count($objects[$parentactual]->colormapentries)-1]->id = count($parent)-1;
					$objects[$parentactual]->colormapentries[count($objects[$parentactual]->colormapentries)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->colormapentries[count($objects[$parentactual]->colormapentries)-1]->color = $element["attributes"]["color"];
					$objects[$parentactual]->colormapentries[count($objects[$parentactual]->colormapentries)-1]->opacity = $element["attributes"]["opacity"];
					$objects[$parentactual]->colormapentries[count($objects[$parentactual]->colormapentries)-1]->quantity = $element["attributes"]["quantity"];
					$objects[$parentactual]->colormapentries[count($objects[$parentactual]->colormapentries)-1]->label = $element["attributes"]["label"];
				}
				break;
			case "LABEL":
				if ($element["type"] == "complete")
				{
					//Kl�ren, ob Label nur so vorkommen kann - TODO
					//$objects[$parentactual]->label = $element["value"];
					
					//use the new ParameterValue class to store the mixed content value
					//simple case no extra markup ...
					//the label is instanciated by TextSymbolizer
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new ParameterValue();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					$objects[count($parent)-1]->value = $element["value"];
					//END Experimental
					$objects[$parentactual]->label = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
					$parentactual = $parent[$parentactual];
				}				
				elseif ($element["type"] == "open")
				{
					$parent[] = $parentactual;
					$objects[count($parent)-1] = new ParameterValue();
					//Experimental:
					$objects[count($parent)-1]->id = count($parent)-1;
					$objects[count($parent)-1]->parent = $parentactual;
					//END Experimental
					$objects[$parentactual]->label = &$objects[count($parent)-1];
					$parentactual = count($parent)-1;
				}
				elseif ($element["type"] == "close")
				{
					$parentactual = $parent[$parentactual];
				}
				break;
			case "RADIUS":
				if ($element["type"] == "complete")
				{
					//Kl�ren, ob Radius nur so vorkommen kann - TODO
					$objects[$parentactual]->radius = $element["value"];
				}
				break;
			case "OPACITY":
				if ($element["type"] == "complete")
				{
					//Kl�ren, ob Opacity nur so vorkommen kann - TODO
					$objects[$parentactual]->opacity = $element["value"];
				}
				break;
			case "SIZE":
				if ($element["type"] == "complete")
				{
					//Kl�ren, ob Size nur so vorkommen kann - TODO
					$objects[$parentactual]->size = $element["value"];
				}
				break;
			case "ROTATION":
				if ($element["type"] == "complete")
				{
					//Kl�ren, ob Rotation nur so vorkommen kann - TODO
					$objects[$parentactual]->rotation = $element["value"];
				}
				break;
			case "ANCHORPOINTX":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->anchorpointx = $element["value"];
				}
				break;
			case "ANCHORPOINTY":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->anchorpointy = $element["value"];
				}
				break;
			case "DISPLACEMENTX":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->displacementx = $element["value"];
				}
				break;
			case "DISPLACEMENTY":
				if ($element["type"] == "complete")
				{
					$objects[$parentactual]->displacementy = $element["value"];
				}
				break;
			//TODO: Kl�ren mit sld:parameterValueType
			//bei den folgenden XML Elementen
			case "PROPERTYNAME":
				if ($element["type"] == "complete")
				{
					if ($temp == "") {
						$objects[$parentactual]->value = $element["value"];
						$parentactual = count($parent)-1;
					}
					else {
						die($temp);
					}
				}
				break;
			//END TODO
			//von den vorhergegangenen XML Elementen
			
			
			default:
					$temp = "";
					if ($element["type"] == "open")
					{
						$temp .= "<".$tag;
						if ($element["attributes"] != "")
						{
							$keys = array_keys($element["attributes"]);
							foreach ($keys as $key)
							{
								$temp .= " ".$key."=\"".$element["attributes"][$key]."\"";
							}
						}
						$temp .= ">";
					}
					if ($element["type"] == "close")
					{
						$temp .= "</".$tag.">";
					}
					if ($element["type"] == "complete")
					{
						$temp .= "<".$tag.">";
						$temp .= $element["value"];
						$temp .= "</".$tag.">";
					}
					//Wenn Parent vom Typ Rule, dann muss es ein Filter sein, also hinzuf�gen
					if (strtoupper(get_class($objects[$parentactual])) == "RULE")
					{
						$objects[$parentactual]->filter .= $temp;
						$temp = "";
					}
		}
	}
	
	
	//print_r($objects);
	
	
	$_SESSION["sld_objects"] = $objects;
	$_SESSION["sld_parent"] = $parent;
	
	return $styledlayerdescriptor;
}
?>
