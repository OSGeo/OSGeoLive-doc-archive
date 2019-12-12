<?php

#example for local request (aware of having configured curl or http connection in mapbender.conf):
#http://localhost/mapbender/geoportal/mod_readOpenSearchResults.php?q=ah4


#***things to be done first (globals, ...)
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../../conf/geoportal.conf");
#require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");
#require_once(dirname(__FILE__)."/../../conf/geoportal.conf");
$resdir = RESULT_DIR;
echo $resdir;
$con = db_connect(DBSERVER,OWNER,PW);
db_select_db(DB,$con);
function logit($text){
	 	if($h = fopen(dirname(__FILE__)."/../tmp/opensearch_log.txt","a")){
					$content = $text .chr(13).chr(10);
					if(!fwrite($h,$content)){
						#exit;
					}
					fclose($h);
				}	 	
	 }
#require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php"); 
#***
#test if script was requested over http or from cli
#if it came from cli, use output to tmp folder - > typo3 would find it and will show it in template, there should be an identifier from the gaz.php script which controls the different search moduls
#if it came as http request it should generate its own html window
#Maybe problematic: if requested from command-line, how would mapbender get the content? Should be tested.

#check if requested as cli
if (isset($argv[0])&isset($argv[1])){
	echo "\nthe script was invoked from commandline\n";
	$from_cli=true;
	#do something with the searchstring if needed
	#from cli no pagenumber will be given. Therefor everytime page number 1 will be requested
	$request_p = 1;
	$_REQUEST["q"] = $argv[2];//.$argv[3];//$searchPortaluFilter = $argv[3];
	$cli_id = $argv[1];
	
	echo "\nID: ".$argv[1]."\n";
	echo "\nSearchstring: ".$argv[2]."\n";
	}
	else
	{
		echo "\n<br>no commandline args set!\n<br>";
		$from_cli=false;
	}

#When script was not invoked from cli it should have been invoked per http - check the params
if (!$from_cli){
	#***Validation of GET Parameters
	#handle errors
	if(!isset($_REQUEST["q"]) ) {
		echo "No search string found! Please send a query!<br>";
		die();
		}
	if(!isset($_REQUEST["p"]) ) {
		$request_p=1;
		}
	else 
		{
		$request_p=$_REQUEST["p"];
		}

	if(!isset($_REQUEST["request_id"]) or $_REQUEST["request_id"]=='') {
		echo "<br> request_id is not set <br>";
		$requeststring="&request_id=from_http";
		$cli_id="from_http";
		}
	else
		{
		echo "<br>request_id is set<br>";
		$cli_id=$_REQUEST["request_id"];
		}
	echo "<br>Search string: <b>".$_REQUEST["q"]."</b> will be send<br>";
}

	






#***get the information out of the mapbender-db
#get urls to search interfaces (opensearch):
$sql_os = "SELECT * from gp_opensearch ORDER BY os_id";
#do db select
$res_os = db_query($sql_os);
#initialize count of search interfaces
$cnt_os = 0;
#initialize result array
$os_list=array(array());
#fill result array
while($row_os = db_fetch_array($res_os)){
	$os_list[$cnt_os] ['id']= $row_os["os_id"];
	$os_list[$cnt_os] ['name']= $row_os["os_name"];
	$os_list[$cnt_os] ['url']= $row_os["os_url"];
	$os_list[$cnt_os] ['h']= $row_os["os_h"];
	$os_list[$cnt_os] ['standardfilter']= $row_os["os_standard_filter"];
	$os_list[$cnt_os] ['version']= $row_os["os_version"];
#os_standard_filter
	$cnt_os++;
}

if (!$from_cli) {
#give out count of interfaces to use
	echo "\nCount of registrated OpenSearch Interfaces: ".count($os_list)."\n";
	#***
}

#get command_line args
#$cli_id="1234567-1234567-1234567-test";

#+++++++++++++++++++++++++++


#if the request came from http and the first request came from a commandline - add a get parameter to the following requests and change set the $cli_id 
#if(!isset($_REQUEST["request_id"]) ) {
#		$cli_id=$_REQUEST["request_id"];
#		$requeststring="&request_id=".$cli_id;
	#	}
	#else
	#	{
	#	$requeststring="";
	#	}


#+++++++++++++++++++++++++++++++++

#string to add to further requests:
$requeststring="&request_id=".$cli_id;
#***write xml with list of opensearch catalogs
#$from_cli=true;# for testing only
#if ($from_cli) {
	#write out xml 'is really no xml!' with opensearch-catalogs
if ($from_cli) {
echo "\nFolder to write to: ".$resdir."\n";
echo "\nFile to open: ".$resdir."/".$cli_id."_os.xml\n";
}
	if($os_catalogs_file_handle = fopen($resdir."/".$cli_id."_os.xml","w")){
		fwrite($os_catalogs_file_handle,"<interfaces>\n");
		for ($i_c = 0; $i_c < count($os_list); $i_c++) {
			$content = $os_list[$i_c] ['name'];
			fwrite($os_catalogs_file_handle,"<opensearchinterface>");
			fwrite($os_catalogs_file_handle,$content);
			fwrite($os_catalogs_file_handle,"</opensearchinterface>\n");
		}
		fwrite($os_catalogs_file_handle,"</interfaces>\n");
		fclose($os_catalogs_file_handle);
	}	
	else
	{
		if ($from_cli) {
			echo "\nCouldn't open file!\n";
		}
	}
#}
#$from_cli=false;# for testing only
#***
#***loop for things to do for each registrated search interface - only if the search should be done in all interfaces!
#use only one catalogue if a further page is requested
$start_cat=0;
$end_cat=count($os_list);
$cat=$_REQUEST["cat"];
if (isset($cat)){
$start_cat=(int)$cat;
$end_cat=(int)$cat+1;
}

for ($i_si = $start_cat; $i_si < $end_cat ; $i_si++) {
	
	$openSearchUrl[$i_si]=$os_list[$i_si] ['url'];
	$openSearchWrapperDetail="mod_readOpenSearchResultsDetail.php";
	#define opensearch search url and opensearch detail url
	$openSearchUrlSearch[$i_si]=$openSearchUrl[$i_si]."query?";
	$openSearchUrlDetail[$i_si]=$openSearchUrl[$i_si]."detail?";
	
	#get resultlists
	$url=$openSearchUrlSearch[$i_si]."q=".$_REQUEST["q"].$os_list[$i_si] ['standardfilter']."&h=".$os_list[$i_si] ['h']."&p=".$request_p;
	if (!$from_cli) {	
	echo "<br> url: ".$url."<br>";
	}
	else
	{
	#echo "url: ".$url."\n";
	}
	#$e = new mb_exception("metadataurl= ".$url);
	logit("metadataurl : ".$url);
	//$e = new mb_exception("metadataurl= ".$url);
	#create connector object
	$openSearchObject = new connector($url);
	#get results
	$openSearchResult = $openSearchObject->file;
	
	#save resultset in temporary folder identified by sessionid, katalog_id and page_id! Now there would be more tmp files than before! 
	#this has to be done in order to give the information to typo3
	#**************to be done!************************************
	#$e = new mb_exception('external xml : '.$openSearchResult);
	#parse result to simplexml 		
	$openSearchXml=simplexml_load_string($openSearchResult);
	#read out array with docids and plugids
	#read out number of results - there are two ways: with namespaces and without!:
	$n_results=$openSearchXml->channel->totalResults;
	if ($os_list[$i_si] ['version']=='2') {
		$opensearchElements=$openSearchXml->channel->children('http://a9.com/-/spec/opensearch/1.1/');
		$n_results=$opensearchElements->totalResults;
		
	}
	if (!isset($n_results)) {
		$n_results = 0;
	}
	//$e = new mb_exception("n_results(".$i_si.")= ".$n_results);	
	if ($from_cli) {	
		logit( "Number of Results in Catalogue ".$i_si.": ".$n_results."\n");
	}
	#calculate number of needed pages to show all results:
	$number_of_pages=ceil((real)$n_results/(real)$os_list[$i_si] ['h']);
	
	#do some debugging output
	#var_dump($openSearchXml);
	#show total results


#do a html output for showing results of the different opensearch catalogues
if (!$from_cli) {
	echo "<b>".$n_results."</b> Ergebnisse in Katalog <b>".$os_list[$i_si] ['name']."</b><br><br>";
	#show Pagenumbers
	if ((int)$request_p>1) {
	echo "<a href=\"mod_readOpenSearchResults.php?q=".$_REQUEST['q']."&p=".(string)((int)$request_p-1)."&cat=".$i_si.$requeststring."\"> Vorige Seite </a> ";
	}

	echo "Seite: <b>".$request_p."</b> von <b>".$number_of_pages."</b>";
	
	if ((int)$request_p < (int)$number_of_pages) {
	echo " <a href=\"mod_readOpenSearchResults.php?q=".$_REQUEST['q']."&p=".(string)((int)$request_p+1)."&cat=".$i_si.$requeststring."\"> Nächste Seite </a>";
	}
	
	echo "<br><br>";
}
else
{
#echo "Keine Blättermöglichkeit in CLI\n";
}


$from_cli=true; //- do this everytime
if ($from_cli) { #do these things if the request was done from the commandline - it is done by the central search function
	#generate the output for each page! Like: xyz_os1_1_10.xml = this means: searchid_os#catalogid_#page_#totalpages.xml
	#open the specific file for writing
	#number of the actual catalog:
	$catalog_number=(int)$i_si+1;
	logit($resdir."/".$cli_id."_os".$catalog_number."_".$request_p.".xml");
	if($os_catalogs_file_handle = fopen($resdir."/".$cli_id."_os".$catalog_number."_".$request_p.".xml","w")){
		fwrite($os_catalogs_file_handle,"<resultlist>\n");
		#logit("<resultlist>\n");
		fwrite($os_catalogs_file_handle,"<querystring>".urlencode($_REQUEST["q"])."</querystring>\n");
		#logit("<querystring>".urlencode($_REQUEST["q"])."</querystring>\n");
		fwrite($os_catalogs_file_handle,"<totalresults>".$n_results."</totalresults>\n");
		#logit("<totalresults>".$n_results."</totalresults>\n");
		fwrite($os_catalogs_file_handle,"<npages>".$number_of_pages."</npages>\n");
		#logit("<npages>".$number_of_pages."</npages>\n");
		fwrite($os_catalogs_file_handle,"<nresults>".(int)$os_list[$i_si] ['h']."</nresults>\n");
		//write rssurl only, if opensearch version not equal to 1		
		if ($os_list[$i_si] ['version']=='1') {
			fwrite($os_catalogs_file_handle,"<rssurl></rssurl>\n");
		}
		else
		{
			fwrite($os_catalogs_file_handle,"<rssurl>".urlencode($openSearchXml->channel->link)."</rssurl>\n");
		}
		#logit("<nresults>".(int)$os_list[$i_si] ['h']."</nresults>\n");
		#loop for single results in first list
		#problematic: if less than 10 results are in the list, let the loop run only nresults times
			
		if ($n_results < (int)$os_list[$i_si] ['h']) {
			$upperLimit = $n_results;
		}
		else
		{
			$upperLimit = (int)$os_list[$i_si] ['h'];
		}
 		for ($i=0; $i < $upperLimit; $i++) {
		
			#filter nur dann, wenn docid und plugid gesetzt! Nur notwendig, wenn alle abgefragt werden!
			#TODO: Get $plugid, $docid, $docuuid, $wms_url, $bbox out of result!!!
			#if ($os_list[$i_si] ['version']=='2') {
			#	$opensearchElements=$openSearchXml->channel->children('http://a9.com/-/spec/opensearch/1.1/');
			#}
			unset($wms_url);

			$link = $openSearchXml->channel->item[$i]->link;
			if ($os_list[$i_si] ['version']=='1') {
				$plugid = $openSearchXml->channel->item[$i]->plugid;
				$docid = $openSearchXml->channel->item[$i]->docid;
				#$link = urlencode($link);
				if (isset($openSearchXml->channel->item[$i]->{'wms-url'})) {
					$wms_url = $openSearchXml->channel->item[$i]->{'wms-url'};
					#adopt wms url to 1.3.0 - REQUEST has no VERSION set - set this to 1.1.1
					$wms_url = correctWmsUrl($wms_url);
					$wms_url = urlencode($wms_url);
				}
				$source = $openSearchXml->channel->item[$i]->source;
			}
			else {
				$ingridElements=$openSearchXml->channel->item[$i]->children('http://www.portalu.de/opensearch/extension/1.0');
				$link = urlencode($link);
				$plugid = $ingridElements->plugid;
				$source = $ingridElements->source;
				$docid = $ingridElements->docid;
				if (isset($ingridElements->{'wms-url'})) {
					$wms_url = $ingridElements->{'wms-url'};
					#adopt wms url to 1.3.0 - REQUEST has no VERSION set - set this to 1.1.1
					$wms_url = correctWmsUrl($wms_url);
					$wms_url = urlencode($wms_url);
				}
				$georssElements=$openSearchXml->channel->item[$i]->children('http://www.georss.org/georss');
				if (isset($georssElements->{'box'})) {
					$bbox = $ingridElements->{'box'};
				}
				else
				{
					$bbox = null;
				}
				$docuuid = $ingridElements->docuuid;
			}



			if (isset($plugid)&isset($docid)){
				
				#Do result XML output to file
				fwrite($os_catalogs_file_handle,"<result>\n");
				#Tags for catalogtitle and link to detailed information
				fwrite($os_catalogs_file_handle,"<catalogtitle>");
				fwrite($os_catalogs_file_handle,$source." (ID=".$docid.")");
				fwrite($os_catalogs_file_handle,"</catalogtitle>\n");
				fwrite($os_catalogs_file_handle,"<catalogtitlelink>");
				


				fwrite($os_catalogs_file_handle,urlencode($openSearchWrapperDetail."?osid=".$os_list[$i_si] ['id']."&plugid=".$plugid."&docid=".$docid."&docuuid=".$docuuid."&mdtype=html"));

				fwrite($os_catalogs_file_handle,"</catalogtitlelink>\n");
				#Tags for objecttitle and abstract
				fwrite($os_catalogs_file_handle,"<title>");
				fwrite($os_catalogs_file_handle,$openSearchXml->channel->item[$i]->title);
				fwrite($os_catalogs_file_handle,"</title>\n");
				fwrite($os_catalogs_file_handle,"<abstract>");
				fwrite($os_catalogs_file_handle,substr($openSearchXml->channel->item[$i]->description,0,250)."...");
				fwrite($os_catalogs_file_handle,"</abstract>\n");
				#Tag for link to original metadata view
				fwrite($os_catalogs_file_handle,"<urlmdorig>");
				if ($os_list[$i_si] ['version']=='1') {
					fwrite($os_catalogs_file_handle,urlencode($link));
				}
				else
				{
					fwrite($os_catalogs_file_handle,$link);
				}


				#fwrite($os_catalogs_file_handle,$link);




				fwrite($os_catalogs_file_handle,"</urlmdorig>\n");
				#if a wms resource is found, the url will be in the list
				if (isset($wms_url)){	
					fwrite($os_catalogs_file_handle,"<wmscapurl>");
					fwrite($os_catalogs_file_handle,$wms_url);
					fwrite($os_catalogs_file_handle,"</wmscapurl>\n");
					fwrite($os_catalogs_file_handle,"<mbaddurl>");
					fwrite($os_catalogs_file_handle,"testurl");
					fwrite($os_catalogs_file_handle,"</mbaddurl>\n");
				}
				else #add empty tags
				{
					fwrite($os_catalogs_file_handle,"<wmscapurl></wmscapurl>\n<mbaddurl></mbaddurl>\n");
					
				}
				if (isset($bbox)){	
					fwrite($os_catalogs_file_handle,"<georssurl>");
					$urlToId = $openSearchUrlSearch[$i_si]."q=t01_object.obj_id:".$docuuid.$os_list[$i_si] ['standardfilter']."&h=".$os_list[$i_si] ['h']."&p=".$request_p;
					fwrite($os_catalogs_file_handle,urlencode($urlToId));
					fwrite($os_catalogs_file_handle,"</georssurl>\n");
				}
				else
				{
					fwrite($os_catalogs_file_handle,"<georssurl>");
					fwrite($os_catalogs_file_handle,"</georssurl>\n");
				}
				#fwrite($os_catalogs_file_handle,"");
				fwrite($os_catalogs_file_handle,"<iso19139url>");
				fwrite($os_catalogs_file_handle,urlencode($openSearchWrapperDetail."?osid=".$os_list[$i_si] ['id']."&plugid=".$plugid."&docid=".$docid."&docuuid=".$docuuid."&mdtype=iso19139"));
				fwrite($os_catalogs_file_handle,"</iso19139url>\n");
				fwrite($os_catalogs_file_handle,"<inspireurl>");
				fwrite($os_catalogs_file_handle,urlencode($openSearchWrapperDetail."?osid=".$os_list[$i_si] ['id']."&plugid=".$plugid."&docid=".$docid."&docuuid=".$docuuid."&mdtype=iso19139&validate=true"));
				fwrite($os_catalogs_file_handle,"</inspireurl>\n");
				#end of resultlist
				fwrite($os_catalogs_file_handle,"</result>\n");
			}
	}	
	fwrite($os_catalogs_file_handle,"</resultlist>\n");
	#logit("</resultlist>\n");
	
	fclose($os_catalogs_file_handle);
}	
}

#$from_cli=false;

#do html version
if (!$from_cli) {
	for ($i=0; $i < (int)$os_list[$i_si] ['h']; $i++) {


		#filter nur dann, wenn docid und plugid gesetzt! Nur notwendig, wenn alle abgefragt werden!
		if (isset($plugid)&isset($docid)){
			echo("<a href=\"".$openSearchWrapperDetail."?osid=".$os_list[$i_si] ['id']."&plugid=".$plugid."&docid=".$docid."&mdtype=html\" target=\"_blank\" onclick='window.open(this.href,\"Details\",\"width=500,height=600,left=100,top=200,scrollbars=yes ,dependent=yes\"); return false'><b> ".$openSearchXml->channel->item[$i]->title."</b></a>");
			if (isset($wms_url)){
			echo("<a href=\"\"><img border=\"0\" alt=\"In Karte übernehmen\" src=\"img/map.png\"></a><br>");
			}
			else
			{
			echo "<br>";
			}
			echo("<b><i>".$source." (ID=".$docid.")</b></i><br>");

			echo("<i>".substr($openSearchXml->channel->item[$i]->description,0,250)." ...</i><br>");
			echo("<a href=\"".$link."\">Originäre Metadaten<a><br>");
			#if a wms resource is found, the url will be in the list
			if (isset($wms_url)){
				echo(" <a href=\"".urldecode($wms_url)."\">WMS GetCapabilities<a><br>");
				
			}
			echo("");
			echo("<b>Alternative Formate:</b><br><a href=\"".$openSearchWrapperDetail."?osid=".$os_list[$i_si] ['id']."&plugid=".$plugid."&docid=".$docid."&mdtype=iso19139\" onclick='window.open(this.href,\"Details ISO19139\",\"width=500,height=600,left=100,top=200,scrollbars=yes ,dependent=yes\"); return false' target=\"_blank\"><img border=\"0\" src=\"img/iso19139.png\" alt=\"ISO19139\"></a><a href=\"".$openSearchWrapperDetail."?osid=".$os_list[$i_si] ['id']."&plugid=".$plugid."&docid=".$docid."&mdtype=inspire\" onclick='window.open(this.href,\"Details INSPIRE\",\"width=500,height=600,left=100,top=200,scrollbars=yes ,dependent=yes\"); return false' target=\"_blank\"><img border=\"0\" alt=\"INSPIRE\" src=\"img/inspire_tr_36.png\"></a><br><br>");
		}

			#wenn AUSGABE IN DATEI ERWUENSCHT
			#logit($openSearchResult);
			#logit("test");
	}

}
}
function correctWmsUrl($wms_url) {
	$findme1   = "VERSION=";
	$findme2 = "version=";
	$pos1 = strpos($wms_url, $findme1);
	$pos2 = strpos($wms_url, $findme2);
	if ($pos1 === false && $pos2 === false) {
		$wms_url .= "&amp;VERSION=1.1.1";
	}
	//exchange &? with & and &amp; 
	$wms_url = str_replace('&?', '&', $wms_url);
	$wms_url = str_replace('&amp;?', '&', $wms_url);
	$wms_url = str_replace('&amp;', '&', $wms_url);
return $wms_url;
}










?>
