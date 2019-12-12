<?php
# $Id: mod_wfs.php 6885 2010-09-03 14:51:01Z christoph $
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>mod_wfs</title>
<script language='JavaScript' type='text/javascript'>
var wfs_conf = new Array();
var id = "<?php echo $_GET["id"];?>";
var reloaded = "<?php echo $_GET["reloaded"];?>";
var idIsEmpty = "<?php echo $_GET["idIsEmpty"];?>";
/*
function register(){
	var isReg = false;
	for(var i=0; i<parent.mb_InitFunctions.length; i++){
		if(parent.mb_InitFunctions[i] == (window.name+".fetchInf()")){
			isReg = true;
		}
	}
	if(isReg == false){
		parent.mb_registerInitFunctions(window.name+".fetchInf()");
	}
}
*/

var mapbenderInit = function () {
	if (id === "") {
		if (reloaded === "") {
			try {
				parent.Mapbender.modules.loadwmc.events.loaded.register(function (obj) {
					document.location.href = "../php/mod_wfs.php?e_id_css=wfs_conf" + 
						"&e_id=wfs_conf&elementID=wfs_conf&reloaded=1&" + 
						"guiID=<?php echo Mapbender::session()->get("mb_user_gui");?>&" +
						parent.Mapbender.sessionName + "=" + parent.Mapbender.sessionId;
				});
			}
			catch (exc) {
				new parent.Mapbender.Notice(exc);
			}
		}
		fetchInf();
	}
};

function register() {
	if (parent.Mapbender.events.init.done === true) {
		mapbenderInit();
	}
	else {
		parent.Mapbender.events.init.register(mapbenderInit);
	}
}

function fetchInf(){
	var wfs = new Array();
	var l;
	var ind = parent.getMapObjIndexByName('mapframe1');
	for(var i=0; i<parent.mb_mapObj[ind].wms.length; i++){
		for(var j=0; j<parent.mb_mapObj[ind].wms[i].objLayer.length; j++){
			l = parent.mb_mapObj[ind].wms[i].objLayer[j];			
			if(l.gui_layer_wfs_featuretype != ""){
				wfs[wfs.length] = l.gui_layer_wfs_featuretype; 
			}
		}	
	}	
	
	if (idIsEmpty === "") {
		document.location.href = "../php/mod_wfs.php?id=" + wfs.join(",") + "&idIsEmpty=1";
	}	
}
function get_wfs_conf(){
	return wfs_conf;
}
var iamready = false;
</script>
<?php

function myNl2br ($str) {
	return preg_replace('#\r?\n#', '\\n', $str);
}

echo "<script language='JavaScript' type='text/javascript'>";
if(isset($_REQUEST['id']) && $_REQUEST['id']!=""){
	$wfs = mb_split(",",$_REQUEST['id']);
	
	$con = db_connect($DBSERVER,$OWNER,$PW);
	db_select_db($DB,$con);
	
	for($i=0; $i<count($wfs); $i++){	
		
		/* wfs_conf */
		$sql = "SELECT * FROM wfs_conf ";
		$sql .= "JOIN wfs ON wfs_conf.fkey_wfs_id = wfs.wfs_id ";
		$sql .= "WHERE wfs_conf.wfs_conf_id = $1";
		
		$v = array($wfs[$i]);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
	
		if($row = db_fetch_array($res)){
			$wfs_id  = $row["fkey_wfs_id"];
			$featuretype_id  = $row["fkey_featuretype_id"];
			echo "var len = wfs_conf.length;";
			echo "wfs_conf[".$i."] = new Array();";
			echo "wfs_conf[".$i."]['wfs_conf_id']  = '".$row["wfs_conf_id"]."';";
			echo "wfs_conf[".$i."]['wfs_conf_abstract']  = '".$row["wfs_conf_abstract"]."';";
			echo "wfs_conf[".$i."]['g_label']  = '".$row["g_label"]."';";
			echo "wfs_conf[".$i."]['g_label_id']  = '".$row["g_label_id"]."';";
			echo "wfs_conf[".$i."]['g_style']  = \"".preg_replace("/\n/", "", preg_replace("/\r/", "", $row["g_style"]))."\";";
			echo "wfs_conf[".$i."]['g_button']  = '".$row["g_button"]."';";
			echo "wfs_conf[".$i."]['g_button_id']  = '".$row["g_button_id"]."';";
			echo "wfs_conf[".$i."]['g_buffer']  = '".$row["g_buffer"]."';";
			echo "wfs_conf[".$i."]['g_res_style']  = \"".preg_replace("/\n/", "", preg_replace("/\r/", "", $row["g_res_style"]))."\";";
			echo "wfs_conf[".$i."]['g_use_wzgraphics']  = '".$row["g_use_wzgraphics"]."';";
			echo "wfs_conf[".$i."]['fkey_featuretype_id']  = '".$row["fkey_featuretype_id"]."';";
			echo "wfs_conf[".$i."]['wfs_getfeature']  = '".$row["wfs_getfeature"]."';";
			echo "wfs_conf[".$i."]['wfs_describefeaturetype']  = '".$row["wfs_describefeaturetype"]."';";
			echo "wfs_conf[".$i."]['wfs_transaction']  = '".$row["wfs_transaction"]."';";
			echo "wfs_conf[".$i."]['wfs_conf_type']  = '".$row["wfs_conf_type"]."';";
			#wfs_describefeaturetype - wfs_describefeaturetype
			
		}else{die("wfs_conf data not available");}
		
		$sql = "SELECT * FROM wfs_featuretype_namespace";
		$sql .= " WHERE fkey_wfs_id = $1 AND fkey_featuretype_id = $2";
		$v = array($wfs_id,$featuretype_id);
		$t = array('i','i');
		$res = db_prep_query($sql,$v,$t);
		echo "wfs_conf[".$i."]['namespaces'] = new Array();";
		$counter = 0;
		while($row = db_fetch_array($res)){
			echo "wfs_conf[".$i."]['namespaces'][".$counter."] = new Array();";
			echo "wfs_conf[".$i."]['namespaces'][".$counter."]['name']  = '".$row["namespace"]."';";
			echo "wfs_conf[".$i."]['namespaces'][".$counter."]['location']  = '".$row["namespace_location"]."';";
			$counter++;
		}
		
		
		$sql = "SELECT * FROM wfs_featuretype ";
		$sql .= "WHERE fkey_wfs_id = $1 AND featuretype_id = $2";
		$v = array($wfs_id,$featuretype_id);
		$t = array('i','i');
		$res = db_prep_query($sql,$v,$t);
		if($row = db_fetch_array($res)){
			echo "wfs_conf[".$i."]['featuretype_name']  = '".$row["featuretype_name"]."';";
			echo "wfs_conf[".$i."]['featuretype_srs']  = '".$row["featuretype_srs"]."';";
		}else{die("wfs_featuretype data not available");}
		
		/* wfs_conf_element */
		$sql = "SELECT * FROM wfs_conf_element ";
		$sql .= "JOIN wfs_element ON wfs_conf_element.f_id = wfs_element.element_id ";
		$sql .= "WHERE wfs_conf_element.fkey_wfs_conf_id = $1";
		$sql .= " ORDER BY wfs_conf_element.f_respos";
		#$sql .= "AND wfs_conf_element.f_search = 1 ORDER BY wfs_conf_element.f_search;";
				
		$v = array($wfs[$i]);
		$t = array('i');
		$res = db_prep_query($sql,$v,$t);
		
		echo "wfs_conf[".$i."]['element']  = new Array();";
		$cnt = 0;
		
		while($row = db_fetch_array($res)){
			echo "wfs_conf[".$i."]['element'][".$cnt."] = new Array();";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_search'] = ".$row["f_search"].";";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_style_id'] = '".$row["f_style_id"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_toupper'] = '".$row["f_toupper"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_label'] = '".htmlentities($row["f_label"], ENT_QUOTES, "UTF-8")."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_label_id'] = '".$row["f_label_id"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_show'] = '".$row["f_show"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_respos'] = '".$row["f_respos"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_show_detail'] = '".$row["f_show_detail"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_detailpos'] = '".$row["f_detailpos"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['element_name'] = '".htmlentities($row["element_name"], ENT_QUOTES, "UTF-8")."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['element_type'] = '".htmlentities($row["element_type"], ENT_QUOTES, "UTF-8")."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_geom'] = '".$row["f_geom"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_gid'] = '".$row["f_gid"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_form_element_html'] = \"".(preg_replace("/\n/", "", preg_replace("/\r/", "", $row["f_form_element_html"])))."\";";
//			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_form_element_html'] = \"\";";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_edit'] = '".$row["f_edit"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_mandatory'] = '".$row["f_mandatory"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_auth_varname'] = '".htmlentities($row["f_auth_varname"], ENT_QUOTES, "UTF-8")."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_operator'] = '".$row["f_operator"]."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_helptext'] = '".myNl2br(htmlentities($row["f_helptext"], ENT_QUOTES, "UTF-8"))."';";
			echo "wfs_conf[".$i."]['element'][".$cnt."]['f_category_name'] = '".htmlentities($row["f_category_name"], ENT_QUOTES, "UTF-8")."';";
			$cnt++;
		}
		if($cnt == 0){die("wfs_conf data not available");}		
	}
	echo "iamready = true;";
}
?>
</script>
</head>
<body leftmargin='0' topmargin='10'  bgcolor='#ffffff' onload='register()'>
</body>
</html>
