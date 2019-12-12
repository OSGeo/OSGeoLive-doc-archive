<?php
# $Id: mod_wfs_conf.php 2327 2009-02-27 16:23:54Z baudson $
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

require_once(dirname(__FILE__)."/../classes/class_wfs_conf.php");
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
?>
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';
?>
<style type="text/css">
<!--
body {
	background-color: #ffffff;
	font-family: Arial, Helvetica, sans-serif;
	font-size : 12px;
	color: #808080
}

div.helptext {
	display: none;
	position: absolute;
	float:right;
	padding: 0 5px;
	color: #000;
	background-color: #CCC;
	border: 1px solid #000;
}

div.helptext strong {
	display: block;
	margin: 0 -5px 5px -5px;
	padding: 2px 5px;
	font-weight: normal;
	color: #FFF;
	background-color: #666;
}

div.helptext textarea {
	width: 450px;
	height: 250px;
}

div.helptext input {
	float:left;
	display: block;
	margin: 5px auto;
}


<?php
	require_once "../css/jquery-ui-1.7.1.custom.css";
?>

-->
</style>
<title>WFS configuration</title>
<script language="JavaScript" type="text/javascript">
	var Mapbender = {};
<?php
echo "var global_mb_log_js = '".LOG_JS."';";
echo "var global_mb_log_level = '".LOG_LEVEL."';";
echo "var global_log_levels = '".LOG_LEVEL_LIST."';";
require_once(dirname(__FILE__) . "/../extensions/jquery-ui-1.8.1.custom/js/jquery-1.4.2.min.js");
require_once(dirname(__FILE__) . "/../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery-ui-1.8.1.custom.js");
require_once "../extensions/jqjson.js";
require_once "../../lib/ajax.js";
require_once "../../lib/exception.js";
require_once "mod_wfs_conf_interface.js";
?>

$.fn.wfsConfiguration = function (options) {
	return this.each(function () {
		var opt = {};
		if (typeof options === "object") {
			opt = options;
		}		
		opt.$container = $(this);
		var myConf = new WfsConfInterface(opt);
	});
}

$(function () {
	$("#wfs_conf_tabs").wfsConfiguration();
});

</script>
</head>
<body>
	<div id='wfs_conf_tabs'></div>
</body>
