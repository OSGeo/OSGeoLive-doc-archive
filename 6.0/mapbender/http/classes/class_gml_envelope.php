<?php
# $Id: class_gml2.php 3099 2008-10-02 15:29:23Z nimix $
# http://www.mapbender.org/index.php/class_gml2.php
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

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_gml_polygon.php");

/**
 * 		Models a GML Envelope.
 * 
 *      Example:
 *      
 *      <gml:Envelope>
 *         <gml:lowerCorner>42.943 -71.032</gml:lowerCorner>
 *         <gml:upperCorner>43.039 -69.856</gml:upperCorner>
 *      </gml:Envelope>
 */
class GMLEnvelope extends GMLPolygon{

	public function parseEnvelope ($domNode) {
		$corner1 = $domNode->firstChild;
		$corner2 = $corner1->nextSibling;
		
		list($y1,$x1) = explode(' ',$corner1->nodeValue);
		list($y2,$x2) = explode(' ',$corner2->nodeValue);

		$this->addPoint($x1, $y1);
		$this->addPoint($x1, $y2);
		$this->addPoint($x2, $y2);
		$this->addPoint($x2, $y1);
		$this->addPoint($x1, $y1);
	}
}
?>