<?php
//http://www.geoportal.rlp.de/mapbender/php/mod_getServiceDisclaimer.php?type=wms&id=1501
//include relevant scripts
//
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__) . "/../classes/class_user.php");
//require_once(dirname(__FILE__)."/../classes/class_administration.php"); //TODO: include some class which can do the db connects

//function to parse urls as links
function display_text($string) {
    $string = eregi_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=_blank>\\0</a>", $string);   
    $string = eregi_replace("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([0-9a-z](-?[0-9a-z])*\.)+[a-z]{2}([zmuvtg]|fo|me)?$", "<a href=\"mailto:\\0\" target=_blank>\\0</a>", $string);   
    $string = eregi_replace("\n", "<br>", $string);
    return $string;
}  

//initialize request parameters:
$type = "wms";
$id = 1;
$languageCode = "de";
$withHeader = false;
$asTable = false;
//parse request parameters
//
if (isset($_REQUEST["id"]) & $_REQUEST["id"] != "") {
	//validate to integer 
	$testMatch = $_REQUEST["id"];
	$pattern = '/^[\d]*$/';		
 	if (!preg_match($pattern,$testMatch)){ 
		echo 'id: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$id = (integer)$testMatch;
	$testMatch = NULL;	
}
//
if (isset($_REQUEST["type"]) & $_REQUEST["type"] != "") {
	//validate to wms, wfs
	$testMatch = $_REQUEST["type"];	
 	if (!($testMatch == 'wms' or $testMatch == 'wfs')){ 
		echo 'type: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$type = $testMatch;
	$testMatch = NULL;
}
//
if (isset($_REQUEST["languageCode"]) & $_REQUEST["languageCode"] != "") {
	//validate to wms, wfs
	$testMatch = $_REQUEST["languageCode"];	
 	if (!($testMatch == 'de' or $testMatch == 'en' or  $testMatch == 'fr')){ 
		echo 'type: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$languageCode = $testMatch;
	$testMatch = NULL;
}
//
if (isset($_REQUEST["withHeader"]) & $_REQUEST["withHeader"] != "") {
	//validate to wms, wfs
	$testMatch = $_REQUEST["withHeader"];	
 	if (!($testMatch == 'true' or $testMatch == 'false')){ 
		echo 'type: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	if ($testMatch == 'true'){ 
		$withHeader = true;		
 	} else {
		$withHeader = false;
	}
	$testMatch = NULL;
}
//
if (isset($_REQUEST["asTable"]) & $_REQUEST["asTable"] != "") {
	//validate to wms, wfs
	$testMatch = $_REQUEST["asTable"];	
 	if (!($testMatch == 'true' or $testMatch == 'false')){ 
		echo 'type: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	if ($testMatch == 'true'){ 
		$asTable = true;		
 	} else {
		$asTable = false;
	}
	$testMatch = NULL;
}


$htmlHeader = array();

switch($languageCode){
        		case 'de':
                	$htmlHeader['discHeader'] = 'Nutzungsbedingungen';
			$htmlHeader['discPrivacyHeader'] = 'Datenschutzhinweis';
			$htmlHeader['accessConstraintsHeader'] = 'Beschränkungen des öffentlichen Zugangs';
			$htmlHeader['feesHeader'] = 'Angaben zu Kosten/Gebühren/Lizenzen';
			$htmlHeader['licences'] = '<b>Lizenz:</b><br>';
			$htmlHeader['networkAccess'] = 'Der Dienst ist <b>nicht im Internet</b> sondern nur in ausgewählten  Netzwerken (z.B. Intranets) verfügbar. Genauere Angaben erhalten Sie ggf. im folgenden Abschnitt.<br>';
			$htmlHeader['logInformation'] = 'Die Zugriffe auf den Dienst werden vom Anbieter <b>nutzerbezogen</b> aufgezeichnet. Dies erfolgt entweder zur Abrechnung vertraglicher Vereinbarungen ';
			$htmlHeader['logInformation'] .= 'oder aufgrund gesetzlicher Vorgaben.<br><b>Wenn Sie hiermit nicht einverstanden sein sollten, nutzen Sie diesen Dienst nicht!</b><br>';
			$htmlHeader['logInformation'] .= 'Falls Sie weitere Fragen haben, kontaktieren Sie bitte den Anbieter unter ';
			$htmlHeader['priceInformation'][0] = 'Der Anbieter dieses Dienstes hat ein allgemeines Nutzungsentgelt von <b>';
			$htmlHeader['priceInformation'][1] = ' Cent pro Megapixel</b> ';
			$htmlHeader['priceInformation'][2] = ' für abgerufene Bildinformationen angegeben. Das Abrufen eines Kartenbildes in einer Standardauflösung von 600x400 Pixeln kostet dementsprechend <b>';
			$htmlHeader['priceInformation'][3] = ' Euro</b>. Angaben zu eventuell möglichen Rabatten erhalten Sie über ';
			$htmlHeader['noInformation'] = 'Es sind keine Informationen über Nutzungsbedingungen verfügbar!';


       			break;
        		case 'en':
			$htmlHeader['discHeader'] = 'Terms of use';
			$htmlHeader['discPrivacyHeader'] = 'Note on protection of privacy';
			$htmlHeader['accessConstraintsHeader'] = 'Constraints on public access';
			$htmlHeader['feesHeader'] = 'Information about costs/fees/licences';
			$htmlHeader['licences'] = '<b>Licence:</b><br>';
			$htmlHeader['networkAccess'] = 'This Service is <b>not available via www</b> but only in special networks. Possibly you get further information about the network availability in the following paragraph.<br>';
			$htmlHeader['logInformation'] = 'The access on this service is logged <b>user-related</b> by the provider. The logging is done to support automated settlement based on a contract ';
			$htmlHeader['logInformation'] .= 'or to fulfill legal standards.<br><b>If you do not agree on this - please don\'t use this service!</b><br>';
			$htmlHeader['logInformation'] .= 'If you have further questions, please contact the provider under ';
			$htmlHeader['priceInformation'][0] = 'The provider have defined a charge of <b>';
			$htmlHeader['priceInformation'][1] = ' (euro)cent per megapixel</b> ';
			$htmlHeader['priceInformation'][2] = ' for retrieved picture data. The retrieving of a typical map with a standardized resolution of 600x400 px will cost <b>';
			$htmlHeader['priceInformation'][3] = ' euro</b>. For information about possible discounts please contact ';
			$htmlHeader['noInformation'] = 'No informations about terms of use are available!';


           
        		break;
        		case 'fr':
			$htmlHeader['discHeader'] = 'Nutzungsbedingungen';
			$htmlHeader['discPrivacyHeader'] = 'Datenschutzhinweis';
			$htmlHeader['accessConstraintsHeader'] = 'Beschränkungen des öffentlichen Zugangs';
			$htmlHeader['feesHeader'] = 'Angaben zu Kosten/Gebühren/Lizenzen';
			$htmlHeader['licences'] = '<b>Lizenz:</b><br>';
			$htmlHeader['networkAccess'] = 'Der Dienst ist <b>nicht im Internet</b> sondern nur in ausgewählten  Netzwerken (z.B. Intranets) verfügbar. Genauere Angaben erhalten Sie ggf. im folgenden Abschnitt.<br>';
			$htmlHeader['logInformation'] = 'Die Zugriffe auf den Dienst werden vom Anbieter <b>nutzerbezogen</b> aufgezeichnet. Dies erfolgt entweder zur Abrechnung vertraglicher Vereinbarungen ';
			$htmlHeader['logInformation'] .= 'oder aufgrund gesetzlicher Vorgaben.<br><b>Wenn Sie hiermit nicht einverstanden sein sollten, nutzen Sie diesen Dienst nicht!</b><br>';
			$htmlHeader['logInformation'] .= 'Falls Sie weitere Fragen haben, kontaktieren Sie bitte den Anbieter unter ';
			$htmlHeader['priceInformation'][0] = 'Der Anbieter dieses Dienstes hat ein allgemeines Nutzungsentgelt von <b>';
			$htmlHeader['priceInformation'][1] = ' Cent pro Megapixel</b> ';
			$htmlHeader['priceInformation'][2] = ' für abgerufene Bildinformationen angegeben. Das Abrufen eines Kartenbildes in einer Standardauflösung von 600x400 Pixeln kostet dementsprechend <b>';
			$htmlHeader['priceInformation'][3] = ' Euro</b>. Angaben zu eventuell möglichen Rabatten erhalten Sie über ';
			$htmlHeader['noInformation'] = 'No informations about terms of use are available!';


       			break;
     			default:
			$htmlHeader['discHeader'] = 'Nutzungsbedingungen';
			$htmlHeader['discPrivacyHeader'] = 'Datenschutzhinweis';
			$htmlHeader['accessConstraintsHeader'] = 'Beschränkungen des öffentlichen Zugangs';
			$htmlHeader['feesHeader'] = 'Angaben zu Kosten/Gebühren/Lizenzen';
			$htmlHeader['licences'] = '<b>Lizenz:</b><br>';
			$htmlHeader['networkAccess'] = 'Der Dienst ist <b>nicht im Internet</b> sondern nur in ausgewählten  Netzwerken (z.B. Intranets) verfügbar. Genauere Angaben erhalten Sie ggf. im folgenden Abschnitt.<br>';
			$htmlHeader['logInformation'] = 'Die Zugriffe auf den Dienst werden vom Anbieter <b>nutzerbezogen</b> aufgezeichnet. Dies erfolgt entweder zur Abrechnung vertraglicher Vereinbarungen ';
			$htmlHeader['logInformation'] .= 'oder aufgrund gesetzlicher Vorgaben.<br><b>Wenn Sie hiermit nicht einverstanden sein sollten, nutzen Sie diesen Dienst nicht!</b><br>';
			$htmlHeader['logInformation'] .= 'Falls Sie weitere Fragen haben, kontaktieren Sie bitte den Anbieter unter ';
			$htmlHeader['priceInformation'][0] = 'Der Anbieter dieses Dienstes hat ein allgemeines Nutzungsentgelt von <b>';
			$htmlHeader['priceInformation'][1] = ' Cent pro Megapixel</b> ';
			$htmlHeader['priceInformation'][2] = ' für abgerufene Bildinformationen angegeben. Das Abrufen eines Kartenbildes in einer Standardauflösung von 600x400 Pixeln kostet dementsprechend <b>';
			$htmlHeader['priceInformation'][3] = ' Euro</b>. Angaben zu eventuell möglichen Rabatten erhalten Sie über ';
			$htmlHeader['noInformation'] = 'No informations about terms of use are available!';

		}	

//parameters:	type	:wms, wfs, ... string
//		id	:1234, 234, ... integer
//
//
//read information from database
//
//
if ($type == "wms") {
	$sql = "SELECT wms_id, wms.accessconstraints, wms.fees, wms.wms_network_access , wms.wms_pricevolume, wms.wms_proxylog, termsofuse.name,";
	$sql .= " termsofuse.termsofuse_id, termsofuse.symbollink, termsofuse.description,termsofuse.descriptionlink from wms LEFT OUTER JOIN";
	$sql .= "  wms_termsofuse ON  (wms.wms_id = wms_termsofuse.fkey_wms_id) LEFT OUTER JOIN termsofuse ON";
	$sql .= " (wms_termsofuse.fkey_termsofuse_id=termsofuse.termsofuse_id) where wms.wms_id = $1";
}
if ($type == "wfs") {
	$sql = "SELECT wfs_id, accessconstraints, fees, wfs_network_access , termsofuse.name,";
	$sql .= " termsofuse.termsofuse_id ,termsofuse.symbollink, termsofuse.description,termsofuse.descriptionlink from wfs LEFT OUTER JOIN";
	$sql .= "  wfs_termsofuse ON  (wfs.wfs_id = wfs_termsofuse.fkey_wfs_id) LEFT OUTER JOIN termsofuse ON";
	$sql .= " (wfs_termsofuse.fkey_termsofuse_id=termsofuse.termsofuse_id) where wfs.wfs_id = $1";	
}
$v = array();
$t = array();
array_push($t, "i");
array_push($v, $id);
$res = db_prep_query($sql,$v,$t);
$row = db_fetch_array($res);
if (!isset($row['wms_id'])) {
echo $type."-service with this id is not known!";
	die();
}
//get email adress of responsible person for service:
if ($type == "wms") {
	$sql = "SELECT mb_user_email FROM wms LEFT OUTER JOIN mb_user ON  (wms_owner = mb_user.mb_user_id) WHERE wms_id=$1";
}
if ($type == "wfs") {
	$sql = "SELECT mb_user_email FROM wfs LEFT OUTER JOIN mb_user ON  (wfs_owner = mb_user.mb_user_id) WHERE wfs_id=$1";
}
$v = array();
$t = array();

array_push($t, "i");
array_push($v, $id);

$res = db_prep_query($sql,$v,$t);

$rowOwner = db_fetch_array($res);




if ((isset($row[$type.'_proxylog']) & $row[$type.'_proxylog'] != 0) or strtoupper($row['accessconstraints']) != "NONE" or strtoupper($row['fees']) != "NONE" or isset($row['termsofuse_id']) or (isset($row[$type.'_network_access']) & $row[$type.'_network_access'] != 0)) {
	//generate text for json object if restrictions exists
	if ($withHeader) {
		echo "<h1>".$htmlHeader['discHeader']."</h1>";
	}
	if ($asTable) {
		$tableBegin =  "<table>\n";
		$t_a = "\t<tr>\n\t\t<th>\n\t\t\t";
		$t_b = "\n\t\t</th>\n\t\t<td>\n\t\t\t";
		$t_c = "\n\t\t</td>\n\t</tr>\n";
		$tableEnd = "</table>\n";
		echo $tableBegin;
		if (isset($row[$type.'_proxylog']) & $row[$type.'_proxylog'] != 0 )  {
			$discPrivacy = $htmlHeader['logInformation'];
			$discPrivacy .= "<a href=\"mailto:".$rowOwner['mb_user_email']."\">".$rowOwner['mb_user_email']."</a>";
			echo $t_a.$htmlHeader['discPrivacyHeader'].$t_b.$discPrivacy.$t_c;
		}
		if ((strtoupper($row['accessconstraints']) != "NONE" & (str_replace(" ", "", $row['accessconstraints']) != "")) or (isset($row[$type.'_network_access']) & $row[$type.'_network_access'] != 0) ) {
			$accessConstraintsHeader = $htmlHeader['accessConstraintsHeader'];
			if (isset($row[$type.'_network_access']) & $row[$type.'_network_access'] != 0) {
				$accessConstraints = $htmlHeader['networkAccess'];
			}
			else {
				$accessConstraints = "";
			}
			$accessConstraints .= display_text($row['accessconstraints']);
			echo $t_a.$htmlHeader['accessConstraintsHeader'].$t_b.$accessConstraints.$t_c;
		}
		if (isset($row['termsofuse_id']) or (strtoupper($row['fees']) != "NONE" & (str_replace(" ", "", $row['fees']) != "")) or ($type == "wms" & isset($row['wms_pricevolume']) & $row['wms_pricevolume'] != 0) ) {
			$feesPart = $t_a.$htmlHeader['feesHeader'].$t_b;
			if (isset($row['termsofuse_id'])) {
				$fees = $htmlHeader['licences'];
				#$fees .= $row['name']."<br>";
				$fees .= "<a href='".$row['descriptionlink']."' target=_blank><img style='border: none;' src='".$row['symbollink']."' ".$row['name']."></a><br>";
				$fees .= $row['description']."<br>";
				$feesPart .= $fees;
			} else {
				if (isset($row['fees']) & ((strtoupper($row['fees']) != 'NONE') or ($row['fees'] != ''))) {
					$fees = display_text($row['fees']);
					$feesPart .= $fees;
				}
			}
			if ($type == "wms" & isset($row['wms_pricevolume']) & $row['wms_pricevolume'] != 0) {
				$priceExample = (integer)$row['wms_pricevolume']*400*600/100000000;
				$priceInformation = $htmlHeader['priceInformation'][0].(integer)$row['wms_pricevolume'];
				$priceInformation .= $htmlHeader['priceInformation'][1].$htmlHeader['priceInformation'][2].$priceExample.$htmlHeader['priceInformation'][3]." <a href=\"mailto:".$rowOwner['mb_user_email']."\">".$rowOwner['mb_user_email']."</a><br>";	
				$feesPart .= "<br>".$priceInformation.$t_c;
			} else {
				$feesPart .= $t_c;
			}
		}
		echo $feesPart.$tableEnd;
	} else {
		//information is given in the standard way - not as a html table
		if (isset($row[$type.'_proxylog']) & $row[$type.'_proxylog'] != 0 )  {
			$discPrivacy = $htmlHeader['logInformation'];
			$discPrivacy .= "<a href=\"mailto:".$rowOwner['mb_user_email']."\">".$rowOwner['mb_user_email']."</a>";
			echo "<h2>".$htmlHeader['discPrivacyHeader']."</h2>";
			echo $discPrivacy."<br>";
		}
		if ((strtoupper($row['accessconstraints']) != "NONE" & (str_replace(" ", "", $row['accessconstraints']) != "")) or (isset($row[$type.'_network_access']) & $row[$type.'_network_access'] != 0) ) {
			$accessConstraintsHeader = $htmlHeader['accessConstraintsHeader'];
			if (isset($row[$type.'_network_access']) & $row[$type.'_network_access'] != 0) {
				$accessConstraints = $htmlHeader['networkAccess'];
			}
			else {
				$accessConstraints = "";
			}
			$accessConstraints .= display_text($row['accessconstraints']);
			echo "<h2>".$htmlHeader['accessConstraintsHeader']."</h2>";
			echo $accessConstraints."<br>";
		}
		if (isset($row['termsofuse_id']) or (strtoupper($row['fees']) != "NONE" & (str_replace(" ", "", $row['fees']) != "")) or ($type == "wms" & isset($row['wms_pricevolume']) & $row['wms_pricevolume'] != 0) ) {
			echo "<h2>".$htmlHeader['feesHeader']."</h2>";
			if (isset($row['termsofuse_id'])) {
				$fees = $htmlHeader['licences'];
				#$fees .= $row['name']."<br>";
				$fees .= "<a href='".$row['descriptionlink']."' target=_blank><img src='".$row['symbollink']."' ".$row['name']."></a><br>";
				$fees .= $row['description']."<br>";
				echo $fees."<br>";
			} else {
				if (isset($row['fees']) & ((strtoupper($row['fees']) != 'NONE') or ($row['fees'] != ''))) {
					$fees = display_text($row['fees']);
					echo $fees."<br>";
				}
			}
			if ($type == "wms" & isset($row['wms_pricevolume']) & $row['wms_pricevolume'] != 0) {
				$priceExample = (integer)$row['wms_pricevolume']*400*600/100000000;
				$priceInformation = $htmlHeader['priceInformation'][0].(integer)$row['wms_pricevolume'];
				$priceInformation .= $htmlHeader['priceInformation'][1].$htmlHeader['priceInformation'][2].$priceExample.$htmlHeader['priceInformation'][3]." <a href=\"mailto:".$rowOwner['mb_user_email']."\">".$rowOwner['mb_user_email']."</a><br>";
				echo $priceInformation."<br>";
			}
		}
	}
} else {
	/*//if nothing about restrictions is defined
	if ($asTable){
		echo $tableBegin;
		echo $t_a.$htmlHeader['noInformation'].$t_b.$t_c."<br>";
		echo $tableEnd;
	} else {
		echo $htmlHeader['noInformation']."<br>" ;
	}*/
	echo "";//since it is not neccessary to give information about nothing ;-)
}

?>
