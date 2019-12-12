<?php
# $Id: mod_featuretypeMetadata.php 235 2007-09-20 verenadiewald $
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

function display_text($string) {
    $string = eregi_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=_blank>\\0</a>", $string);   
    $string = eregi_replace("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([0-9a-z](-?[0-9a-z])*\.)+[a-z]{2}([zmuvtg]|fo|me)?$", "<a href=\"mailto:\\0\" target=_blank>\\0</a>", $string);   
    $string = eregi_replace("\n", "<br>", $string);
    return $string;
}  

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
	<head>
		<title>WFS Metadata</title>
		<meta name="description" content="Metadata" xml:lang="en" />
		<meta name="keywords" content="Metadaten" xml:lang="en" />		
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="expires" content="0">
		<meta http-equiv="content-language" content="de" />
		<meta http-equiv="content-style-type" content="text/css" />	
		<link rel="stylesheet" type="text/css" href="../css/metadata.css" />		
<?php
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
	</head>
	<body id="top">

	

<?php
	$wfs_conf_id = $_GET['wfs_conf_id'];
	//for testing only
	#$wfs_conf_id = 1;
	
	$sql_id = "SELECT fkey_wfs_id, fkey_featuretype_id FROM wfs_conf WHERE wfs_conf_id = $1";
	$v_id = array($wfs_conf_id);
	$t_id = array('i');
	$res_id = db_prep_query($sql_id,$v_id,$t_id);
	$row_id = db_fetch_array($res_id);
	$wfs_id = $row_id['fkey_wfs_id'];
	$featuretype_id = $row_id['fkey_featuretype_id'];
		
	// retrieve the geometry type
	$sql_geom = "SELECT a.element_type AS geom_type ";
	$sql_geom .= "FROM wfs_element AS a, wfs_conf AS b, wfs_conf_element AS c ";
	$sql_geom .= "WHERE a.fkey_featuretype_id = b.fkey_featuretype_id AND b.wfs_conf_id = $1 ";
	$sql_geom .= "AND b.wfs_conf_id = c.fkey_wfs_conf_id AND c.f_geom = 1 AND c.f_id = a.element_id";
	$v_geom = array($wfs_conf_id);
	$t_geom = array('i');
	$res_geom = db_prep_query($sql_geom, $v_geom, $t_geom);
	$row_geom = db_fetch_array($res_geom);
	$geomType = $row_geom['geom_type'];

	$sql = "SELECT ";
	$sql .= "ft.featuretype_id, ft.featuretype_title, ft.featuretype_srs, ft.featuretype_abstract, ";
	$sql .= "wfs.wfs_title, wfs.wfs_abstract, wfs.wfs_id, wfs.fees, wfs.accessconstraints, wfs.individualname, ";
	$sql .= "wfs.positionname, wfs.providername, wfs.deliverypoint, wfs.city, wfs.wfs_timestamp, wfs.wfs_owner, ";
	$sql .= "wfs.country, wfs.postalcode, wfs.voice, wfs.facsimile, ";
	$sql .= "wfs.electronicmailaddress, wfs.wfs_getcapabilities ";
	$sql .= "FROM wfs, wfs_featuretype ft WHERE wfs.wfs_id = $1 AND ft.featuretype_id = $2 AND wfs.wfs_id = ft.fkey_wfs_id LIMIT 1";
	$v = array($wfs_id,$featuretype_id);
	$t = array('i','i');
	$res = db_prep_query($sql,$v,$t);
	echo db_error();
	$wfs = array();
	$row = db_fetch_array($res);
	
	$sql_dep = "SELECT mb_group_name FROM mb_group AS a, mb_user AS b, mb_user_mb_group AS c WHERE b.mb_user_id = $1  AND b.mb_user_id = c.fkey_mb_user_id AND c.fkey_mb_group_id = a.mb_group_id AND b.mb_user_department = a.mb_group_description LIMIT 1";
	$v_dep = array($row['wfs_owner']);
	$t_dep = array('i');
	$res_dep = db_prep_query($sql_dep, $v_dep, $t_dep);
	$row_dep = db_fetch_array($res_dep);
	
	$featuretype['ID'] = $featuretype_id;
	$featuretype['Titel'] = $row['featuretype_title'];
	$featuretype['Zusammenfassung'] = $row['featuretype_abstract'];
	$featuretype['Koordinatensysteme'] = $row['featuretype_srs'];
	$featuretype['Geometrietyp'] = $geomType;
	$featuretype['Capabilities-Dokument'] = $row['wfs_getcapabilities'];
	#$featuretype['Capabilities-Dokument'] = "<a href='".$row['wfs_getcapabilities']."' target=_blank>Capabilities-Dokument</a>";
	if ($row['wfs_timestamp']) {
		$layer['Datum der Registrierung'] = date("d.m.Y",$row['wfs_timestamp']); 
	}
	else {
		$layer['Datum der Registrierung'] = "Keine Angabe"; 
	}
	$featuretype['Registrierende Stelle'] = $row_dep['mb_group_name'];
	$featuretype['WFS ID'] = $row['wfs_id'];
	$featuretype['WFS Titel'] = $row['wfs_title'];
	$featuretype['WFS Zusammenfassung'] = $row['wfs_abstract'];
	$featuretype['Geb&uuml;hren'] = $row['fees'];
	$featuretype['Zugriffsbeschr&auml;nkung'] = $row['accessconstraints'];
	$featuretype['Ansprechpartner'] = $row['individualname'];
	$featuretype['Organisation'] = $row['providername'];
	$featuretype['Adresse'] = $row['deliverypoint'];
	$featuretype['Stadt'] = $row['city'];
	$featuretype['PLZ'] = $row['postalcode'];
	$featuretype['Telefon'] = $row['voice'];
	$featuretype['Fax'] = $row['facsimile'];
	$featuretype['E-Mail'] = $row['electronicmailaddress'];
	$featuretype['Land'] = $row['country'];
	
	echo "<table >\n";
	$t_a = "\t<tr>\n\t\t<th>\n\t\t\t";
	$t_b = "\n\t\t</th>\n\t\t<td>\n\t\t\t";
	$t_c = "\n\t\t</td>\n\t</tr>\n";

	$keys = array_keys($featuretype);
	for ($j=0; $j<count($featuretype); $j++) {
		echo $t_a . utf8_encode($keys[$j]) . $t_b . display_text($featuretype[$keys[$j]]) . $t_c;
	}
	
	echo "</td></tr></table>\n";
?>

	</body>
</html>
