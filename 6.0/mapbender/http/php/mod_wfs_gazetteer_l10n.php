<?php
#$Id: mod_insertWmcIntoDb.php 507 2006-11-20 10:55:57Z christoph $
#$Header: /cvsroot/mapbender/mapbender/http/javascripts/mod_insertWmcIntoDb.php,v 1.19 2006/03/09 14:02:42 uli_rothstein Exp $
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

// translates all string values in a tree It can find
function translateTree($tree)
{

  if(is_object($tree)){
    foreach($tree as $key => $value )
    {
      $tree->$key = translateTree($value);
    }
  }else if(is_array($tree)){
    foreach($tree as $key => $value )
    {
      $tree[$key] = translateTree($value);
    }
  }else if(is_string($tree)){
    $tree =  _mb($tree);
  }
  return $tree;
}

$ajaxResponse = new AjaxResponse($_POST);

switch ($ajaxResponse->getMethod()) {
	case "translateServiceData" :
		$msg_obj = $ajaxResponse->getParameter("data");
        
        $translated_obj = translateTree($msg_obj);
		
        $ajaxResponse->setSuccess(true);
		$ajaxResponse->setResult($translated_obj);
		break;
	default :
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage(_mb("An unknown error occured."));
		break;
}

$ajaxResponse->send();

?>