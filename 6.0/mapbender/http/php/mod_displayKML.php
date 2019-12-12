<?php
# $Id: class_wmc.php,v 1.31 2006/03/16 14:49:30 c_baudson Exp $
# http://www.mapbender.org/index.php/class_wmc.php
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

mb_internal_encoding("UTF-8");

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__) . "/../classes/class_kml_ows.php");

$kmlId = $_GET["kmlId"];

$kml = new KML();

$kmlDoc = $kml->getKmlDocumentFromDB($kmlId);

if ($kmlDoc) {
	header("Content-type: application/xhtml+xml; charset=" . CHARSET);
	echo $kmlDoc;
}
else {
	echo "You may not have the rights to access this document. Check your log file.";
}
?>