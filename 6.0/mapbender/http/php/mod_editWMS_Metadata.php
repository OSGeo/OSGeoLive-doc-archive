<?php
# $Id: mod_editWMS_Metadata.php 6903 2010-09-05 09:32:38Z christoph $
# http://www.mapbender.org/index.php?title=Edit_WMS_Metadata
# Copyright (C) 2009 OSGeo
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

$e_id="EditWMSMetadata";
require_once(dirname(__FILE__)."/mb_validatePermission.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

include_once '../include/dyn_css.php';

$adm = new administration();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Edit WMS Metadata</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">   
<script language="JavaScript">
var dTarget;
function save() {
   document.forms[0].update_content.value=1;
   document.forms[0].submit();
}

function deletepreview(layer_id) {
	var url = String(document.location);
	url = url.substr(0, url.indexOf('?'));
	window.open(url+"?<?php echo $urlParameters; ?>&delete_preview=1&layer_id="+layer_id,'delete preview', 'height=50, width=150, dependent=yes');
	document.getElementById(layer_id+"_dp").style.display="none";
}

function pick_the_date(obj) {
    dTarget = obj;
	var datePickerParameters = "m=Jan_Feb_Mrz_Apr_Mai_Jun_Jul_Aug_Sep_Okt_Nov_Dez&d=Mo_Di_Mi_Do_Fr_Sa_So&t=heute";
	var datePickerStyle = "left=200,top=200,width=230,height=210,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0"
    var dp = window.open('../extensions/datepicker/datepicker.php?' + datePickerParameters,'dp',datePickerStyle);
    dp.focus();
    return false;
}
</script>
<link rel="stylesheet" type="text/css" href="../css/metadata.css" />
</head>
<body>
<?php

function guessTimestamp($timestr) {
	
     if (mb_strpos($timestr, '.')) {
        list($day, $month, $year) = explode(".", $timestr);
     }
     elseif (mb_strpos($timestr, '/')) {
        list($month, $day, $year) = explode("/", $timestr);
     }
     elseif (mb_strpos($timestr, '-')) {
        list($year, $month, $day) = explode("-", $timestr);
     }
     else {
     	$year = 0;
        $month = 0;
        $day = 0;
     }
     return mktime(0, 0, 0, $month, $day, intval($year));
}

#Update handling

if (isset($_REQUEST['update_content']) && $_REQUEST['update_content'] == true) {
	
	$update_wms_sql = "UPDATE wms SET "; 
	$update_wms_sql .= "wms_title = $1, wms_abstract = $2, fees = $3, ";
	$update_wms_sql .= "accessconstraints = $4, contactperson = $5, ";
	$update_wms_sql .= "contactposition = $6, contactorganization = $7, ";
	$update_wms_sql .= "address = $8, city = $9, stateorprovince = $10, ";
	$update_wms_sql .= "postcode = $11, country = $12, ";
	$update_wms_sql .= "contactvoicetelephone = $13, ";
	$update_wms_sql .= "contactfacsimiletelephone = $14, ";
	$update_wms_sql .= "contactelectronicmailaddress = $15 ";

	$v = array();
	array_push($v, $_REQUEST['wms_title_box']);
	array_push($v, $_REQUEST['wms_abstract_box']);
	array_push($v, $_REQUEST['fees_box']);
	array_push($v, $_REQUEST['accessconstraints_box']);
	array_push($v, $_REQUEST['contactperson_box']);
	array_push($v, $_REQUEST['contactposition_box']);
	array_push($v, $_REQUEST['contactorganization_box']);
	array_push($v, $_REQUEST['address_box']);
	array_push($v, $_REQUEST['city_box']);
	array_push($v, $_REQUEST['stateorprovince_box']);
	array_push($v, $_REQUEST['postcode_box']);
	array_push($v, $_REQUEST['country_box']);
	array_push($v, $_REQUEST['contactvoicetelephone_box']);
	array_push($v, $_REQUEST['contactfacsimiletelephone_box']);
	array_push($v, $_REQUEST['contactelectronicmailaddress_box']);
	$t = array("s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "s");

	if (isset($_REQUEST['wms_timestamp_box']) && $_REQUEST['wms_timestamp_box'] <> "") {
        $update_wms_sql .= ", wms_timestamp = $16 ";
		array_push($v, guessTimestamp($_REQUEST['wms_timestamp_box']));
		array_push($t, "s");

		$update_wms_sql .= "WHERE wms_id = $17";
	}
	else {
		$update_wms_sql .= "WHERE wms_id = $16";
	}
	array_push($v, 	$_REQUEST['wms_id']);
	array_push($t, "s");

    $res_update_wms_sql = db_prep_query($update_wms_sql, $v, $t);

    while(list($key,$val) = each($_REQUEST)) {
        if(preg_match("/___/", $key)) {
            $myKey = explode("___", $key);
            $layer_id = preg_replace("/L_/","",$myKey[0]);
            if($myKey[1]=="layer_abstract") {
				$layer_sql = "UPDATE layer SET layer_abstract = $1 ";
				$layer_sql .= "WHERE layer_id = $2 AND fkey_wms_id = $3";  
                $v = array($val, $layer_id, $_REQUEST['wms_id']);
                $t = array("s", "i", "s");
                $res_keyword_sql = db_prep_query($layer_sql, $v, $t);
            }
            if($myKey[1]=="layer_keywords") {
                #Get all keywords depending on the given layer after user modification
                $keywords  = explode(",",$val);
                #delete all blanks from the keywords list
                for ($j = 0; $j < count($keywords); $j++) {
                    $word = $keywords[$j];
                    $word = trim($word);
                    $keywords[$j] = $word;
                }
                #echo "1: Keywords eines Layers: id des Layers: ", $layer_id, ", �bergebener String: ", $val, ";<br>";
                #Get all keywords depending on this layer from database
                $keyword_sql = "SELECT keyword_id, keyword FROM keyword, layer_keyword, layer " .
                               "WHERE keyword.keyword_id = layer_keyword.fkey_keyword_id " .
                               "AND layer_keyword.fkey_layer_id = layer.layer_id " .
                               "AND layer.fkey_wms_id = $1 " .
                               "AND layer.layer_id = $2";
                
                $v = array($_REQUEST['wms_id'], $layer_id); 
                $t = array("s", "i");
                $res_keyword_sql = db_prep_query($keyword_sql, $v, $t);
                while($keyword_row = db_fetch_array($res_keyword_sql))
                {
                    $keyword = $keyword_row['keyword'];
                    $keyword_id = $keyword_row['keyword_id'];
                    #keyword has been deleted or has been modified
                    #keyword exists in database but not in user data
                    $index = -1;
                    #echo "1a: Abfrage ob DB Keywords in User Liste: Keyword: ", $keyword, ";<br>";
                    if(in_array($keyword, $keywords) == false)
                    {
                        #echo "1c: Keyword nicht in User Liste: Keyword: ", $keyword, ";<br>";
                        #Deleting reference to the keyword from the layer_keyword table.
                        $keyword_sql = "DELETE FROM layer_keyword " .
                                       "WHERE fkey_layer_id = $1 " .
                                       "AND fkey_keyword_id = $2";
                        $v = array($layer_id, $keyword_id);
                        $t = array("i", "i");
                        db_prep_query($keyword_sql, $v, $t);
                        #Checking, if the keyword is in use by any layer
                        $layer_sql = "SELECT * FROM layer_keyword " .
                                       "WHERE fkey_keyword_id = $1";
                        $v = array($keyword_id);
                        $t = array("i");
                        $res_layer_sql = db_prep_query($layer_sql, $v, $t);
                        if(!($row = db_fetch_array($res_layer_sql)))
                        {
                            #If keyword will not longer be in use, delete it from keyword table
                            $keyword_sql = "DELETE FROM keyword " .
                                           "WHERE keyword_id = $1";
                            $v = array($keyword_id);
                            $t = array("i");
                            db_prep_query($keyword_sql, $v, $t);
                        }
                    }
                    #Keyword exists in the database and in the user data
                    else
                    {
                        #echo "1d: Keyword ist in User Liste: Keyword: ", $keyword, ";<br>";
                        for($i = 0; $i < count($keywords); $i++)
                        {
                            #Delete keyword from the user data list, because the data
                            #have not to be updated within the database
                            if($keywords[$i] == $keyword)
                            {
                                $keywords[$i] = null;
                            }
                        }
                    }
                }
                #Inserting keyword, that are not existing in the database
                for($i = 0; $i < count($keywords); $i++)
                {
                    #echo "2: Alle Eintr�ge des Keyword arrays: Keyword: ", $keywords[$i], "; Index: ", $i, ";<br>";
                    if($keywords[$i] != null)
                    {
                        #echo "3: Eintr�ge ungleich null: Keyword: ", $keywords[$i], "; Index: ", $i, ";<nr>";
                        $keyword = trim($keywords[$i]);
                        #Check, if the keyword is exsiting in the database
                        $keyword_sql = "SELECT keyword_id FROM keyword " .
                                       "WHERE UPPER(keyword) = UPPER($1)";
                        $v = array($keyword);
                        $t = array("s");
                        $res_keyword_sql = db_prep_query($keyword_sql, $v, $t);
                        $keyword_row = db_fetch_array($res_keyword_sql);
                        #Keyword exists in the database
                        if($keyword_row != null)
                        {
                            $keyword_id = $keyword_row[0];
                            #echo "4: Keyword in Datenbank vorhanden: id des Keywords: ", $keyword_id, ";<br>";
                        }
                        #Keyword does not exist in the database
                        else
                        {
                            $keyword_sql = "INSERT INTO keyword (keyword) VALUES ($1)";
                            $v = array($keyword);
                            $t = array("s");
                            $res_keyword_sql = db_prep_query($keyword_sql, $v, $t);
                            
                            $keyword_sql = "SELECT keyword_id FROM keyword WHERE keyword = $1";
                            $v = array($keyword);
                            $t = array("s");
                            $res_keyword_sql = db_prep_query($keyword_sql, $v, $t);
                            $keyword_row = db_fetch_array($res_keyword_sql);
                            if($keyword_row != null)
                            {
                                $keyword_id = $keyword_row[0];
                                #echo "4: Keyword in der Datenbank nicht vorhanden: id des Keywords: ", $keyword_id, ";<br>";
                            }
                        }
                        #Inserting the reference between layer and keyword in the layer_keyword table
                        $keyword_sql = "INSERT INTO layer_keyword (fkey_layer_id, fkey_keyword_id) " .
                                       "VALUES ($1, $2)";
                        $v = array($layer_id, $keyword_id);
                        $t = array("s", "s");
                        $res_keyword_sql = db_prep_query($keyword_sql, $v, $t);
                    }
                }
                #Delete all elements from array
                unset($keywords);
            }
        }
    }
}
unset($update_content);

#delete preview

if(isset($_REQUEST['delete_preview']) && $_REQUEST['delete_preview']=='1'
	&& isset($_REQUEST['layer_id']))
{
    $preview_sql = "DELETE FROM layer_preview WHERE fkey_layer_id = $1";
    $v = array($_REQUEST['layer_id']);
    $t = array("s");
    $res_preview_sql = db_prep_query($preview_sql, $v, $t);
    die("Preview has been deleted!</body></html>");
}
?>
<form name='form1' action='<?php echo $self . "&show_wms_list=true"; ?>' method='post'>

<table border='0'>
<tr>
<td WIDTH="300" align="left">
<B>WMS Metadaten<B/>
<td/>
<td WIDTH="160">
<td/>
</tr>

<?php  

#Use select box to select a wms

if (isset($_REQUEST['show_wms_list']) && $_REQUEST['show_wms_list'] == true)
{

    #Querying information from wms data table 
    $wms_sql = "SELECT wms_id, wms_title FROM wms WHERE wms_owner = $1 ORDER BY wms_title";
    $v = array(Mapbender::session()->get("mb_user_id"));
    $t = array("i");
    $res_wms_sql = db_prep_query($wms_sql, $v, $t);
    #wms-selection

    $selectBox = "";
    while($row = db_fetch_array($res_wms_sql)) {
        if ($adm->getWmsPermission($row["wms_id"], Mapbender::session()->get("mb_user_id"))) {
	        $selectBox .= "<option value='".$row["wms_id"]."' ";
	        if(isset($_REQUEST['wmsList']) && $_REQUEST['wmsList'] == $row["wms_id"]) {
	            $selectBox .= "selected";
	        }
	        $selectBox .= "> ".$row["wms_title"]."</option>";
        }
    }
    
    if ($selectBox != "") {
	    echo "<tr><td>";
	    echo "<select size=6 name='wmsList' onchange='submit()'>".$selectBox."</select>";
	    echo "</td><td width='160px' align='right'>";
	    echo "<input type='button' class='sbutton' value='save' onclick='save()'>";
		echo "</td></tr>";
    }
    else {
    	echo "<div>no wms owner.</div>";
    	die;
    }

    if(isset($_REQUEST['wmsList']) == true && $_REQUEST['wmsList'] <>0)
    editWMSByWMSID ($_REQUEST['wmsList']);
}

echo "</table>";

//$wms_id;
function editWMSByWMSID($param_wms_id)
{
    global $wms_id;
    $wms_id = $param_wms_id;

}


if(isset($wms_id) == true && $wms_id <>0)
{ 
	$selected_wms_sql = "SELECT * FROM wms WHERE wms_id = $1";
	$v = array($wms_id);
	$t = array("s");
    $res_selected_wms_sql = db_prep_query($selected_wms_sql, $v, $t);
    $selected_row = db_fetch_array($res_selected_wms_sql);

?>
    
    <table border='0' class='table_top' >
    <tr>
    <td>WMS-Titel:<td><td/>
    <input type='text' name='wms_title_box' value='<?php echo $selected_row["wms_title"];?>' /><td/>

    <td>WMS- Abstract:<td><td/>
    <input type='text' name='wms_abstract_box' value='<?php echo htmlentities($selected_row["wms_abstract"],ENT_QUOTES,"UTF-8");?>' /><td/>

    <td >Fees:<td><td/>
    <input type='text' name='fees_box' value='<?php echo $selected_row["fees"]?>'/><td/>
    <tr/>
       
    <tr>
    <td>Access Constraints:<td><td/>
    <input type='text' name='accessconstraints_box' value='<?php echo $selected_row["accessconstraints"]?>'/><td/>

    <td>Contact Person:<td><td/>
    <input type='text' name='contactperson_box' value='<?php echo $selected_row["contactperson"]?>'/><td/>

    <td>Contact Position:<td><td/>
    <input type='text' name='contactposition_box' value='<?php echo $selected_row["contactposition"]?>'/><td/>
    <tr/>
    
    <tr>
    <td>Contact Organization:<td><td/>
    <input type='text' name='contactorganization_box' value='<?php echo $selected_row["contactorganization"]?>'/><td/>
 
    <td>Address:<td><td/>
    <input type='text' name='address_box' value='<?php echo $selected_row["address"]?>'/><td/>

    <td style="width:">City:<td><td/>
    <input type='text' name='city_box' value='<?php echo $selected_row["city"]?>'/><td/>
    <tr/>
    
    <tr>
    <td>State or Province:<td><td/>
    <input type='text' name='stateorprovince_box' value='<?php echo $selected_row["stateorprovince"]?>'/><td/>

    <td>Postcode:<td><td/>
    <input type='text' name='postcode_box' value='<?php echo $selected_row["postcode"]?>'/><td/>
    
    <td>Country:<td><td/>
    <input type='text' name='country_box' value='<?php echo $selected_row["country"]?>'/><td/>
    <tr/>
    
    <tr>
    <td>Telephone:<td><td/>
    <input type='text' name='contactvoicetelephone_box' 
        value='<?php echo $selected_row["contactvoicetelephone"]?>'/><td/>
    
    <td>Fax:<td><td/>
    <input type='text' name='contactfacsimiletelephone_box' value='<?php echo $selected_row["contactfacsimiletelephone"]?>'/><td/>
    
    <td>E-Mail:<td><td/>
    <input type='text' name='contactelectronicmailaddress_box' value='<?php echo $selected_row["contactelectronicmailaddress"]?>'/><td/>
    <tr/>
    <tr>
    <?php
  if (isset($selected_row["wms_timestamp"]) && $selected_row["wms_timestamp"] <> "") {
    $datum = date("d.m.Y",$selected_row["wms_timestamp"]);
  }
  else $datum = ""
?>
    <td>Date:<td><td/>
    <input type='text' name='wms_timestamp_box' value='<?php echo $datum?>' onClick='pick_the_date(document.form1.wms_timestamp_box)'/><td/>
    <tr/>
    
    <table class="table_layer">
    <tr><td>Nr.</td><td>Title</td><td>Abstract</td><td>Keywords</td><td></td></tr>

    
<?php
   
    $layer_sql = "SELECT * FROM layer WHERE layer.fkey_wms_id = $1" .
                 " ORDER BY layer_pos";
    $v = array($wms_id);
    $t = array("s");
    $res_layer_sql = db_prep_query($layer_sql, $v, $t);
    
    while($layer_row = db_fetch_array($res_layer_sql))
    {
    ?>
        <tr align='center'>
        <td><input type='text' size='1' name='L_<?php echo $layer_row['layer_id']?>___layer_nr' 
            value='<?php echo $layer_row['layer_pos']?>' readonly></td>
        <td><input type='text' size='15' name='L_<?php echo $layer_row['layer_id']?>___layer_title' 
            value='<?php echo $layer_row['layer_title']?>' readonly></td>
        <td><input type='text' size='42' name='L_<?php echo $layer_row['layer_id']?>___layer_abstract'
            value='<?php echo htmlentities($layer_row['layer_abstract'],ENT_QUOTES,"UTF-8")?>'>

    <?php
        $keyword_sql = "SELECT keyword FROM keyword, layer_keyword, layer " .
                       "WHERE keyword.keyword_id = layer_keyword.fkey_keyword_id " .
                       "AND layer_keyword.fkey_layer_id = layer.layer_id " .
                       "AND layer.fkey_wms_id = $1 " .
                       "AND layer.layer_id = $2";
        $v = array($wms_id, $layer_row['layer_id']);
        $t = array("s", "i");
        $res_keyword_sql = db_prep_query($keyword_sql, $v, $t);
        $keywordList = "";
        $seperator = "";
        while($keyword_row = db_fetch_array($res_keyword_sql))
        {
            if($keywordList != "")
            {    
                $seperator = ",";
            }
            $keywordList .= $seperator.$keyword_row["keyword"];
        }
        ?>
        <td><input type='text' size='42' name='L_<?php echo $layer_row['layer_id']?>___layer_keywords' 
             value='<?php echo $keywordList?>'>        
        </td>
        <td>
        <!--
        <input type="button" value='preview' onclick="window.open('../frames/index.php?&gui_id=layer_preview&layer_preview=1&portal_services=<?php echo $layer_row['layer_id'];?>', 'mini_mapbender', 'height=370, width=370, dependent=yes');">
        -->
        </td>
        <td>
<?php 
/*
    $preview_sql = "SELECT * FROM layer_preview " .
                   "WHERE fkey_layer_id = ".$layer_row['layer_id']."";
    $res_preview_sql = db_query($preview_sql);
	if(db_numrows($res_preview_sql)>0){?>
        <!--
        <input id="<?php echo $layer_row['layer_id'];?>_dp" type="button" value='delete preview' onclick="deletepreview('<?php echo $layer_row['layer_id'];?>');">
        -->
<?php }else{?>
		<!--
        <input id="<?php echo $layer_row['layer_id'];?>_dp" type="button" value='delete preview' style="display:none;" onclick="deletepreview('<?php echo $layer_row['layer_id'];?>');">
        -->
<?php }*/?>
        </td>
        </tr>
    <?php
    }

    ?>
    </table>
    <input type='hidden' name='update_content' value=''/>
    <input type='hidden' name='wms_id' value='<?php echo $wms_id ?>'/>
	<!--
    <input type="hidden" value='' name='delete_preview'>
    -->
    <input type="hidden" value='' name='layer_id'>
    </form>
    </body>
    </html>
<?php
}
?>
