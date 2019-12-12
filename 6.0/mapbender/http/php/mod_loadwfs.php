<?php
# $Id: mod_loadwfs.php 3664 2009-03-10 16:40:45Z christoph $
# http://www.mapbender.org/index.php/Administration
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/mb_validateInput.php");
require_once(dirname(__FILE__)."/../classes/class_universal_wfs_factory.php"); 
require_once(dirname(__FILE__)."/../classes/class_gui.php"); 

echo "file: ".$_REQUEST["xml_file"];
echo "<br>-------------------------------<br>";

$guiList = mb_validateInput($_REQUEST["guiList"]);
$url = mb_validateInput($_REQUEST["xml_file"]);

$myWfsFactory = new UniversalWfsFactory();
$myWfs = $myWfsFactory->createFromUrl($url);      

if (is_null($myWfs)) {
	echo "WFS could not be uploaded.";
	die;
}
$myWfs->insertOrUpdate();

// link WFS to GUIs in $guiList
$guiArray = explode(",", $guiList);
foreach ($guiArray as $appName) {
	$currentApp = new gui($appName);	
	$currentApp->addWfs($myWfs);
}

echo $myWfs;
?>