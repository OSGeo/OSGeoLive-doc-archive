<?php
#http://localhost/mapbender/geoportal/mod_readOpenSearchResultsDetail.php?osid=1&...
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
#$con = db_connect(DBSERVER,OWNER,PW);
#db_select_db(DB,$con);
require_once(dirname(__FILE__)."/../classes/class_connector.php");
require_once dirname(__FILE__) . "/../../tools/wms_extent/extent_service.conf";
$languageCode = "de";
$layout = "tabs";
//get language parameter out of mapbender session if it is set else set default language to de_DE
if (isset($_SESSION['mb_lang']) && ($_SESSION['mb_lang']!='')) {
	$e = new mb_notice("mod_readOpenSearchResultsDetail.php: language found in session: ".$_SESSION['mb_lang']);
	$language = $_SESSION["mb_lang"];
	$langCode = explode("_", $language);
	$langCode = $langCode[0]; # Hopefully de or s.th. else
	$languageCode = $langCode; #overwrite the GET Parameter with the SESSION information
}

if (isset($_REQUEST["languageCode"]) & $_REQUEST["languageCode"] != "") {
	//validate to csv integer list
	$testMatch = $_REQUEST["languageCode"];
	if (!($testMatch == 'de' or $testMatch == 'fr' or $testMatch == 'en')){ 
		echo 'languageCode: <b>'.$testMatch.'</b> is not valid.<br/>'; 
		die(); 		
 	}
	$languageCode = $testMatch;
	$testMatch = NULL;
}

if(!isset($_REQUEST["osid"])) {
	echo "no opensearch id set";
	die();
} else {
	#if(isset($_REQUEST["mdtype"])&($_REQUEST["mdtype"]=='debug') ) {	
	#	echo "opensearch interface no.: ".$_REQUEST["osid"]." will be requested<br>";
	#}
}

if(!isset($_REQUEST["docuuid"])) {
	echo "No uuid of dataset given!";
	die();
} else {
	$docuuid = $_REQUEST["docuuid"];
}

function getExtentGraphic($layer_4326_box) {
		$rlp_4326_box = array(6.05,48.9,8.6,50.96);
		if ($layer_4326_box[0] <= $rlp_4326_box[0] || $layer_4326_box[2] >= $rlp_4326_box[2] || $layer_4326_box[1] <= $rlp_4326_box[1] || $layer_4326_box[3] >= $rlp_4326_box[3]) {
			if ($layer_4326_box[0] < $rlp_4326_box[0]) {
				$rlp_4326_box[0] = $layer_4326_box[0]; 
			}
			if ($layer_4326_box[2] > $rlp_4326_box[2]) {
				$rlp_4326_box[2] = $layer_4326_box[2]; 
			}
			if ($layer_4326_box[1] < $rlp_4326_box[1]) {
				$rlp_4326_box[1] = $layer_4326_box[1]; 
			}
			if ($layer_4326_box[3] > $rlp_4326_box[3]) {
				$rlp_4326_box[3] = $layer_4326_box[3]; 
			}

			$d_x = $rlp_4326_box[2] - $rlp_4326_box[0]; 
			$d_y = $rlp_4326_box[3] - $rlp_4326_box[1];
			
			$new_minx = $rlp_4326_box[0] - 0.05*($d_x);
			$new_maxx = $rlp_4326_box[2] + 0.05*($d_x);
			$new_miny = $rlp_4326_box[1] - 0.05*($d_y);
			$new_maxy = $rlp_4326_box[3] + 0.05*($d_y);

			if ($new_minx < -180) $rlp_4326_box[0] = -180; else $rlp_4326_box[0] = $new_minx;
			if ($new_maxx > 180) $rlp_4326_box[2] = 180; else $rlp_4326_box[2] = $new_maxx;
			if ($new_miny < -90) $rlp_4326_box[1] = -90; else $rlp_4326_box[1] = $new_miny;
			if ($new_maxy > 90) $rlp_4326_box[3] = 90; else $rlp_4326_box[3] = $new_maxy;
		}
		$getMapUrl = EXTENTSERVICEURL."VERSION=1.1.1&REQUEST=GetMap&SERVICE=WMS&LAYERS=".EXTENTSERVICELAYER."&STYLES=&SRS=EPSG:4326&BBOX=".$rlp_4326_box[0].",".$rlp_4326_box[1].",".$rlp_4326_box[2].",".$rlp_4326_box[3]."&WIDTH=120&HEIGHT=120&FORMAT=image/png&BGCOLOR=0xffffff&TRANSPARENT=TRUE&EXCEPTIONS=application/vnd.ogc.se_inimage&minx=".$layer_4326_box[0]."&miny=".$layer_4326_box[1]."&maxx=".$layer_4326_box[2]."&maxy=".$layer_4326_box[3];
		return $getMapUrl;
}



function display_text($string) {
    $string = eregi_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=_blank>\\0</a>", $string);   
    $string = eregi_replace("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([0-9a-z](-?[0-9a-z])*\.)+[a-z]{2}([zmuvtg]|fo|me)?$", "<a href=\"mailto:\\0\" target=_blank>\\0</a>", $string);   
    $string = eregi_replace("\n", "<br>", $string);
    return $string;
} 
 
function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
        return $uuid;
    }
}

//function to validate against the inspire validation service
function validateInspireMetadataFromData($iso19139Xml){
	$validatorUrl = 'http://www.inspire-geoportal.eu/INSPIREValidatorService/resources/validation/inspire';
	#$validatorUrl2 = 'http://localhost/mapbender/x_geoportal/log_requests.php'; //for debugging purposes
	//send inspire xml to validator and push the result to requesting user
	$validatorInterfaceObject = new connector();
	$validatorInterfaceObject->set('httpType','POST');
	$validatorInterfaceObject->set('httpContentType','multipart/form-data'); # maybe given automatically
	//first test with data from ram - doesn't function :-(
	$fields = array(
		'dataFile'=>urlencode($iso19139Xml)
		);
	//generate file identifier:
	$fileId = guid();
	//generate temporary file under tmp
	 if($h = fopen(TMPDIR."/".$fileId."iso19139_validate_tmp.xml","w")){
		if(!fwrite($h,$iso19139Xml)){
			$e = new mb_exception("geoportal/mod_readOpenSearchResultsDetail.php: cannot write to file: ".TMPDIR."iso19139_validate_tmp.xml");
		}
	fclose($h);
	}
	//send file as post like described under http://www.tecbrat.com/?itemid=13&catid=1
	$fields['dataFile']='@'.TMPDIR.'/'.$fileId.'iso19139_validate_tmp.xml';
	$postData = $fields;
	$validatorInterfaceObject->set('httpPostFieldsNumber',count($postData));
	$validatorInterfaceObject->set('curlSendCustomHeaders',false);
	$validatorInterfaceObject->set('httpPostData', $postData); #give an array
	$validatorInterfaceObject->load($validatorUrl);
	header("Content-type: text/html; charset=UTF-8");
	echo $validatorInterfaceObject->file;
	//delete file in tmp 
	//TODO - this normally done by a cronjob
	die();
}

//INSPIRE Mapping
$md_ident = array(
//Metadata Identifier - not neccessary?
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "unique resource identifier",
		inspiremandatory => "false",
		iso_name => "fileIdentifier",
		html => _mb("Metadata identifier"),
		value => "",
		category => "identification",
		description => _mb("A value uniquely identifying the resource. The value domain of this metadata element is a mandatory character string code, generally assigned by the data owner, and a character string namespace uniquely identifying the context of the identifier code (for example, the data owner).")
	),
//B 1.1
	array(	ibus => "rtitle",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "resource title",
		inspiremandatory => "true",
		iso_name => "type",
		html => _mb("Resource title"),
		value => "",
		category => "identification",
		description => _mb("This a characteristic, and often unique, name by which the resource is known. The value domain of this metadata element is free text.")
	),
//B 1.2
	array(	ibus => "abstract",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:abstract/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "resource abstract",
		inspiremandatory => "true",
		iso_name => "description",
		html => _mb("Resource abstract"),
		value => "",
		category => "identification",
		description => _mb("This is a brief narrative summary of the content of the resource.")
	),
//B 1.3
	array(	ibus => "rtype",
		iso19139 => "/gmd:MD_Metadata/gmd:hierarchyLevel/gmd:MD_ScopeCode/@codeListValue",
		iso19139explode => "false" ,
		inspire => "resource type",
		inspiremandatory => "true",
		iso_name => "type",
		html => _mb("Resource type"),
		value => "",
		category => "identification",
		description => _mb("This is the type of resource being described by the metadata.")
	),
//B 1.4
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:linkage/gmd:URL",
		iso19139explode => "false" ,
		inspire => "unique resource locator",
		inspiremandatory => "true",
		iso_name => "resourceLocator",
		html => _mb("Resource locator"),
		value => "",
		category => "identification",
		description => _mb("The resource locator defines the link(s) to the resource and/or the link to additional information about the resource. The value domain of this metadata element is a character string, commonly expressed as uniform resource locator (URL).")
	),
//B 1.5 - Identifier of dataset!
//Part 1 id
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:citation/gmd:CI_Citation/gmd:identifier/gmd:RS_Identifier/gmd:code/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "unique resource identifier namespace",
		inspiremandatory => "true",
		iso_name => "dataset id",
		html => _mb("Unique resource identifier id"),
		value => "",
		category => "identification",
		description => _mb("A value uniquely identifying the resource. The value domain of this metadata element is a mandatory character string code, generally assigned by the data owner.")
	),
//Part 2 - namespace
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:citation/gmd:CI_Citation/gmd:identifier/gmd:RS_Identifier/gmd:codeSpace/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "unique resource identifier namespace",
		inspiremandatory => "true",
		iso_name => "dataset namespace",
		html => _mb("Unique resource identifier namespace"),
		value => "",
		category => "identification",
		description => _mb("A character string namespace uniquely identifying the context of the identifier code (for example, the data owner)")
	),
//B 1.6 Coupled resource
//NOTICE: simplexml has problems with namespaced attributes! So we choose a alias for xlink:href which is xlinkhref and exchange this attributes from xml before parsing the xml!
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/srv:operatesOn/@xlinkhref",
		iso19139explode => "false" ,
		inspire => "coupled resource",
		inspiremandatory => "true",
		iso_name => "coupled resource",
		html => _mb("Coupled resource"),
		value => "",
		category => "identification",
		description => _mb("If the resource is a spatial data service, this metadata element identifies, where relevant, the target spatial data set(s) of the service through their unique resource identifiers (URI). The value domain of this metadata element is a mandatory character string code, generally assigned by the data owner, and a character string namespace uniquely identifying the context of the identifier code (for example, the data owner).")
	),
//B 1.7 Language dataset
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:language/gmd:LanguageCode/@codeListValue",
		iso19139explode => "false" ,
		inspire => "Language",
		inspiremandatory => "true",
		iso_name => "language",
		html => _mb("Language"),
		value => "",
		category => "identification",
		description => _mb("The language(s) used within the resource. The value domain of this metadata element is limited to the languages defined in ISO 639-2.")
	),
//B 2.1 Topic category
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory/gmd:MD_TopicCategoryCode",
		iso19139explode => "true" ,
		inspire => "topic category",
		inspiremandatory => "true",
		iso_name => "topic category",
		html => _mb("Topic category"),
		value => "",
		category => "classification",
		description => _mb("The topic category is a high-level classification scheme to assist in the grouping and topic-based search of available spatial data resources. The value domain of this metadata element is defined in Part D.2.")
	),
//B 2.2 Spatial data service type
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/srv:SV_ServiceIdentification/srv:serviceType/gco:LocalName",
		iso19139explode => "false" ,
		inspire => "spatial data service type",
		inspiremandatory => "true",
		iso_name => "topic category",
		html => _mb("Spatial data service type"),
		value => "",
		category => "classification",
		description => _mb("This is a classification to assist in the search of available spatial data services. A specific service shall be categorised in only one category. The value domain of this metadata element is defined in Part D.3.")
	),
//B3 Keyword
//B 3.1 keyword value dataset
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword/gco:CharacterString",
		iso19139explode => "true" ,
		inspire => "keyword value",
		inspiremandatory => "true",
		iso_name => "keywordValue",
		html => _mb("Keyword value"),
		value => "",
		category => "keyword",
		description => _mb("If the resource is a spatial data service, at least one keyword from Part D.4 shall be provided. If a resource is a spatial data set or spatial data set series, at least one keyword shall be provided from the general environmental multilingual thesaurus (GEMET) describing the relevant spatial data theme as defined in Annex I, II or III to Directive 2007/2/EC.")
	),
//B 4 Geographic Location
//B 4.1 Geographic bounding box
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/*/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/*/gco:Decimal",
		iso19139explode => "true" ,
		inspire => "geographic bounding box",
		inspiremandatory => "true",
		iso_name => "geographic bounding box",
		html => _mb("Geographic bounding box"),
		value => "",
		category => "location",
		description => _mb("This is the extent of the resource in the geographic space, given as a bounding box. The bounding box shall be expressed with westbound and eastbound longitudes, and southbound and northbound latitudes in decimal degrees, with a precision of at least two decimals.")
	),
//B 5. Temporal reference
//B 5.1 Temporal extent
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/gmd:extent/gmd:EX_Extent/gmd:temporalElement/gmd:EX_EX_TemporalExtent/gmd:extent/gml:TimePeriod/*",
		iso19139explode => "false" ,
		inspire => "temporal extent",
		inspiremandatory => "false",
		iso_name => "Temporal extent",
		html => _mb("Temporal extent"),
		value => "",
		category => "actuality",
		description => _mb("The temporal extent defines the time period covered by the content of the resource. This time period may be expressed as any of the following: - an individual date, - an interval of dates expressed through the starting date and end date of the interval, - a mix of individual dates and intervals of dates.")
	),
//B 5.2 Date of publication
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date[gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='publication']/gmd:date/*",
		iso19139explode => "false" ,
		inspire => "date of publication",
		inspiremandatory => "true",
		iso_name => "date of publication",
		html => _mb("Date of publication"),
		value => "",
		category => "actuality",
		description => _mb("This is the date of publication of the resource when available, or the date of entry into force. There may be more than one date of publication.")
	),
//B 5.3 Date of last revision
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date[gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='revision']/gmd:date/*",
		iso19139explode => "false" ,
		inspire => "date of last revision",
		inspiremandatory => "true",
		iso_name => "date of last revision",
		html => _mb("Date of last revision"),
		value => "",
		category => "actuality",
		description => _mb("This is the date of last revision of the resource, if the resource has been revised. There shall not be more than one date of last revision.")
	),
//B 5.4 Date of creation
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date[gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='creation']/gmd:date/*",
		iso19139explode => "false" ,
		inspire => "date of creation",
		inspiremandatory => "true",
		iso_name => "date of creation",
		html => _mb("Date of creation"),
		value => "",
		category => "actuality",
		description => _mb("This is the date of creation of the resource. There shall not be more than one date of creation.")
	),
//B 6. Quality and validity
//B 6.1 Lineage
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:lineage/gmd:LI_Lineage/gmd:statement/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "lineage",
		inspiremandatory => "true",
		iso_name => "lineage",
		html => _mb("Lineage"),
		value => "",
		category => "quality",
		description => _mb("This is a statement on process history and/or overall quality of the spatial data set. Where appropriate it may include a statement whether the data set has been validated or quality assured, whether it is the official version (if multiple versions exist), and whether it has legal validity.")
	),

//B 6.2 Spatial Resolution
//B 6.2.1 equivalent scale 
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:spatialResolution/gmd:MD_Resolution/gmd:equivalentScale/gmd:MD_RepresentativeFraction/gmd:denominator/gco:Integer",
		iso19139explode => "false" ,
		inspire => "equivalent scale",
		inspiremandatory => "true",
		iso_name => "equivalent scale",
		html => _mb("Equivalent scale"),
		value => "",
		category => "resolution",
		description => _mb("An equivalent scale is generally expressed as an integer value expressing the scale denominator.")
	),
//B 6.2.2 ground distance
//B 6.2.2.1 ground distance value
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:spatialResolution/gmd:MD_Resolution/gmd:distancegco:Distance",
		iso19139explode => "false" ,
		inspire => "ground distance value",
		inspiremandatory => "true",
		iso_name => "groundDistanceValue",
		html => _mb("Ground Distance Value"),
		value => "",
		category => "resolution",
		description => _mb("A resolution distance shall be expressed as a numerical value associated with a unit of length.")
	),
//B 6.2.2.1 ground distance value
//TODO maybe everytime m? or parse the href ...
//B 7. Conformity
//B 7.1. Specification
//B 7.1.1 Title
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_DomainConsistency/gmd:result/gmd:DQ_ConformanceResult/gmd:specification/gmd:CI_Citation/gmd:title/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "Specification title",
		inspiremandatory => "true",
		iso_name => "Specification title",
		html => _mb("Specification title"),
		value => "",
		category => "quality",
		description => _mb("This is a citation of the implementing rules adopted under Article 7(1) of Directive 2007/2/EC or other specification to which a particular resource conforms. A resource may conform to more than one implementing rules adopted under Article 7(1) of Directive 2007/2/EC or other specification. This citation shall include at least the title and a reference date (date of publication, date of last revision or of creation) of the implementing rules adopted under Article 7(1) of Directive 2007/2/EC or of the specification.")
	),
//B 7.1.2 Reference Date
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_DomainConsistency/gmd:result/gmd:DQ_ConformanceResult/gmd:specification/gmd:CI_Citation/gmd:date/gmd:CI_Date/gmd:date/*",
		iso19139explode => "false" ,
		inspire => "specification reference date",
		inspiremandatory => "true",
		iso_name => "Specification reference date",
		html => _mb("Specification reference date"),
		value => "",
		category => "quality",
		description => _mb("This is a citation of the implementing rules adopted under Article 7(1) of Directive 2007/2/EC or other specification to which a particular resource conforms. A resource may conform to more than one implementing rules adopted under Article 7(1) of Directive 2007/2/EC or other specification. This citation shall include at least the title and a reference date (date of publication, date of last revision or of creation) of the implementing rules adopted under Article 7(1) of Directive 2007/2/EC or of the specification.")
	),
//B 7.2. Degree
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_DomainConsistency/gmd:result/gmd:DQ_ConformanceResult/gmd:pass/gco:Boolean",
		iso19139explode => "false" ,
		inspire => "deegree of conformance",
		inspiremandatory => "true",
		iso_name => "Degree of conformance",
		html => _mb("Degree of conformance"),
		value => "",
		category => "quality",
		description => _mb("This is the degree of conformity of the resource to the implementing rules adopted under Article 7(1) of Directive 2007/2/EC or other specification. The value domain of this metadata element is defined in Part D.")
	),
//B 8. Constraints related to access and use
//B 8.1. Conditions applying to access and use
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:resourceConstraints/gmd:MD_Constraints/gmd:useLimitation/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "conditions applying to access and use",
		inspiremandatory => "true",
		iso_name => "conditions applying to access and use",
		html => _mb("Conditions applying to access and use"),
		value => "",
		category => "useconstraints",
		description => _mb("A set of conditions applying to access and use.")
	),
//B 8.2. Limitations on public access
//B 8.2.1 access constraints codelist
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:accessConstraints/gmd:MD_RestrictionCode/@codeListValue",
		iso19139explode => "false" ,
		inspire => "access constraints code",
		inspiremandatory => "true",
		iso_name => "access constraints code",
		html => _mb("Access constraints code"),
		value => "",
		category => "useconstraints",
		description => _mb("Code for access constraints")
	),

//B 8.2.2 other constraints
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:otherConstraints/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "other constraints",
		inspiremandatory => "true",
		iso_name => "other constraints",
		html => _mb("Other constraints"),
		value => "",
		category => "useconstraints",
		description => _mb("Other constraints")
	),
//B 9. Organisations responsible for the establishment, management, maintance and distribution of spatial data sets and services
//B 9.1. Responsible party
//B 9.1.1 Responsible party name
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "responsible party name",
		inspiremandatory => "true",
		iso_name => "responsible party name",
		html => _mb("Responsible party name"),
		value => "",
		category => "contact",
		description => _mb("The name of the organisation as free text.")
	),
//B 9.1.2 Responsible party email
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "responsible party email",
		inspiremandatory => "true",
		iso_name => "responsible party email",
		html => _mb("Responsible party email"),
		value => "",
		category => "contact",
		description => _mb("A contact e-mail address as a character string.")
	),
//B 9.2 Responsible party role
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:role/gmd:CI_RoleCode/@codeListValue",
		iso19139explode => "false" ,
		inspire => "responsible party role",
		inspiremandatory => "true",
		iso_name => "responsible party role",
		html => _mb("Responsible party role"),
		value => "",
		category => "contact",
		description => _mb("This is the role of the responsible organisation. The value domain of this metadata element is defined in Part D.")
	),
//TODO some more translations
//B 10 Metadata on metadata
//B 10.1. Metadata point of contact
//B 10.1.1 Metadata point of contact name
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:contact/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "metadata point of contact name",
		inspiremandatory => "true",
		iso_name => "Metadata point of contact name",
		html => _mb("Metadata point of contact name"),
		value => "",
		category => "contact",
		description => _mb("The name of the organisation as free text.")
	),
//B 10.1.2 Metadata point of contact email
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "metadata point of contact email",
		inspiremandatory => "true",
		iso_name => "Metadata point of contact email",
		html => _mb("Metadata point of contact email"),
		value => "",
		category => "contact",
		description => _mb("A contact e-mail address as a character string.")
	),
//B 10.2. Metadata date
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:dateStamp/*",
		iso19139explode => "false" ,
		inspire => "metadata date",
		inspiremandatory => "true",
		iso_name => "Metadata date",
		html => _mb("Metadata date"),
		value => "",
		category => "metadata",
		description => _mb("The date which specifies when the metadata record was created or updated. This date shall be expressed in conformity with ISO 8601.")
	),
//B 10.3. Metadata language
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:language/gmd:LanguageCode/@codeListValue",
		iso19139explode => "false" ,
		inspire => "metadata language",
		inspiremandatory => "true",
		iso_name => "Metadata language",
		html => _mb("Metadata language"),
		value => "",
		category => "metadata",
		description => _mb("This is the language in which the metadata elements are expressed. The value domain of this metadata element is limited to the official languages of the Community expressed in conformity with ISO 639-2.")
	),

//Additional Metadata Elements from the Data Specs
//Metadata required for interoperability
//TODO!!
/*
https://geo-ide.noaa.gov/wiki/index.php?title=ISO_Boilerplate
<gmd:referenceSystemInfo>
    <gmd:MD_ReferenceSystem>
        <gmd:referenceSystemIdentifier>
            <gmd:RS_Identifier>
                <gmd:authority>
                    <gmd:CI_Citation>
                        <gmd:title>
                            <gco:CharacterString>European Petroleum Survey Group (EPSG) Geodetic Parameter Registry</gco:CharacterString>
                        </gmd:title>
                        <gmd:date>
                            <gmd:CI_Date>
                                <gmd:date>
                                    <gco:Date>2008-11-12</gco:Date>
                                </gmd:date>
                                <gmd:dateType>
                                    <gmd:CI_DateTypeCode codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#CI_DateTypeCode" codeListValue="publication">publication</gmd:CI_DateTypeCode>
                                </gmd:dateType>
                            </gmd:CI_Date>
                        </gmd:date>
                        <gmd:citedResponsibleParty>
                            <gmd:CI_ResponsibleParty>
                                <gmd:organisationName>
                                    <gco:CharacterString>European Petroleum Survey Group</gco:CharacterString>
                                </gmd:organisationName>
                                <gmd:contactInfo>
                                    <gmd:CI_Contact>
                                        <gmd:onlineResource>
                                            <gmd:CI_OnlineResource>
                                                <gmd:linkage>
                                                    <gmd:URL>http://www.epsg-registry.org/</gmd:URL>
                                                </gmd:linkage>
                                            </gmd:CI_OnlineResource>
                                        </gmd:onlineResource>
                                    </gmd:CI_Contact>
                                </gmd:contactInfo>
                                <gmd:role gco:nilReason="missing"/>
                            </gmd:CI_ResponsibleParty>
                        </gmd:citedResponsibleParty>                            
                    </gmd:CI_Citation>
                </gmd:authority>
                <gmd:code>
                    <gco:CharacterString>urn:ogc:def:crs:EPSG:4326</gco:CharacterString>
                </gmd:code>
                <gmd:version>
                    <gco:CharacterString>6.18.3</gco:CharacterString>
                </gmd:version>
            </gmd:RS_Identifier>
        </gmd:referenceSystemIdentifier>
    </gmd:MD_ReferenceSystem>
</gmd:referenceSystemInfo>
*/
//1. Coordinate Reference System
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:RS_Identifier/gmd:code/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "coordinate reference system",
		inspiremandatory => "true",
		iso_name => "coordinate reference system",
		html => _mb("Coordinate reference system"),
		value => "",
		category => "dataspec",
		description => _mb("Description of the coordinate reference system(s) used in the data set.")
	),
/*
//2. Temporal Reference System - only mandatory if not the standard system!
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:language/gmd:LanguageCode/@codeListValue",
		iso19139explode => "false" ,
		inspire => "temporal reference system",
		inspiremandatory => "true",
		iso_name => "temporal reference system",
		html => _mb("Temporal reference system"),
		value => "",
		category => "dataspec",
		description => _mb("Description of the temporal reference system(s) used in the data set. This element is mandatory only if the spatial data set contains temporal information that does not refer to the default temporal reference system.")
	),
*/
//3. Encoding
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:MD_DataIdentification/gmd:distributionInfo/gmd:MD_Distribution/gmd:distributionFormat/gmd:MD_Format/gmd:name/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "encoding",
		inspiremandatory => "true",
		iso_name => "encoding",
		html => _mb("Encoding"),
		value => "",
		category => "dataspec",
		description => _mb("Description of the computer language construct(s) specifying the representation of data objects in a record, file, message, storage device or transmission channel.")
	),
/*
//4. Topological Consistency
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:language/gmd:LanguageCode/@codeListValue",
		iso19139explode => "false" ,
		inspire => "topological Consistency",
		inspiremandatory => "true",
		iso_name => "topological Consistency",
		html => _mb("Topological Consistency"),
		value => "",
		category => "dataspec",
		description => _mb("Correctness of the explicitly encoded topological characteristics of the data set as described by the scope. This element is mandatory only if the data set includes types from the Generic Network Model and does not assure centreline topology (connectivity of centrelines) for the network.")
	),
*/
//5. Character Encoding
/*<gmd:characterSet>
  <gmd:MD_CharacterSetCode
    codeList="http://www.isotc211.org/2005/resources/Codelist/gmxCodelists.xml#MD_CharacterSetCode"
    codeListValue="UTF8">UTF8</gmd:MD_CharacterSetCode>
</gmd:characterSet>
*/
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:characterSet/gmd:MD_CharacterSetCode/@codeListValue",
		iso19139explode => "false" ,
		inspire => "character Encoding",
		inspiremandatory => "true",
		iso_name => "character Encoding",
		html => _mb("Character encoding"),
		value => "",
		category => "dataspec",
		description => _mb("The character encoding used in the data set. This element is mandatory only if an encoding is used that is not based on UTF-8.")
	),


//Preview (36)
	array(	ibus => "t01_object.obj_id",
		iso19139 => "/gmd:MD_Metadata/gmd:identificationInfo/*/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileName/gco:CharacterString",
		iso19139explode => "false" ,
		inspire => "preview",
		inspiremandatory => "false",
		iso_name => "graphicOverview",
		html => _mb("Preview"),
		value => "",
		category => "metadata",
		description => _mb("Graphical overview of the resource.")
	)

);

#***get the information out of the mapbender-db
#get url to search interface (opensearch):
$sql_os = "SELECT * from gp_opensearch where os_id = $1";
#do db select
$v_os = array($_REQUEST["osid"]);
$t_os = array('i');
$res_os = db_prep_query($sql_os,$v_os,$t_os);
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
	$cnt_os++;
}
#give out count of interfaces to use
#echo "\nCount of registrated OpenSearch Interfaces: ".count($os_list)."\n";
#***
switch ($os_list[0] ['version']) {
    	case 2:
		#define new csw get record by id search like:
		#http://www.portalu.de/csw202?request=GetRecordById&service=CSW&version=2.0.2&Id=81FF8BB2-2753-4A95-8C1E-F78C19035780&ElementSetName=full
		$openSearchUrlDetail = $os_list[0] ['url'];
		//exchange opensearch with csw202?
		$openSearchUrlDetail = str_replace('opensearch/', 'csw202?', $openSearchUrlDetail);
		$url = $openSearchUrlDetail."request=GetRecordById&service=CSW&version=2.0.2&Id=".$docuuid."&ElementSetName=full";
	break;			
	default:
		#define opensearch detail url
		$openSearchUrlDetail=$os_list[0] ['url']."detail?";
		#get resultlists
		$url=$openSearchUrlDetail."plugid=".$_REQUEST['plugid']."&docid=".$_REQUEST['docid'];
	break;
}

#create connector object
$openSearchObject = new connector($url);
#get results
$openSearchDetail = $openSearchObject->file;


//solve problem with xlink namespace for href attributes:
$openSearchDetail = str_replace('xlink:href', 'xlinkhref', $openSearchDetail);
#http://forums.devshed.com/php-development-5/simplexml-namespace-attributes-problem-452278.html
#http://www.leftontheweb.com/message/A_small_SimpleXML_gotcha_with_namespaces


#$openSearchDetail = str_replace('xmlns=', 'ns=', $openSearchDetail);
$openSearchDetailXML=simplexml_load_string($openSearchDetail);
#extract objects to iso19139 elements
$openSearchDetailXML->registerXPathNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
$openSearchDetailXML->registerXPathNamespace("gml", "http://www.opengis.net/gml");
$openSearchDetailXML->registerXPathNamespace("gco", "http://www.isotc211.org/2005/gco");
$openSearchDetailXML->registerXPathNamespace("gmd", "http://www.isotc211.org/2005/gmd");
$openSearchDetailXML->registerXPathNamespace("gts", "http://www.isotc211.org/2005/gts");
$openSearchDetailXML->registerXPathNamespace("srv", "http://www.isotc211.org/2005/srv");
$openSearchDetailXML->registerXPathNamespace("xlink", "http://www.w3.org/1999/xlink");

//check if only iso19139 data is requested - if so - push the result automatically from the CSW getRecordById request to the user or the validator
if ($_REQUEST['mdtype']=='iso19139' && $_REQUEST['validate'] != 'true') {
	header("Content-type: application/xhtml+xml; charset=UTF-8");
	//delete scw entries from response file
	$MD_Metadata = str_replace('<csw:GetRecordByIdResponse xmlns:csw="http://www.opengis.net/cat/csw/2.0.2">', '', $openSearchDetail);
	$MD_Metadata = str_replace('</csw:GetRecordByIdResponse>', '', $MD_Metadata);
	echo $MD_Metadata;
	die();
}
if ($_REQUEST['mdtype']=='iso19139' && $_REQUEST['validate'] == 'true') {
	$MD_Metadata = str_replace('<csw:GetRecordByIdResponse xmlns:csw="http://www.opengis.net/cat/csw/2.0.2">', '', $openSearchDetail);
	$MD_Metadata = str_replace('</csw:GetRecordByIdResponse>', '', $MD_Metadata);
	validateInspireMetadataFromData($MD_Metadata);
}

$j=0;
switch ($os_list[0] ['version']) {
    	case 2:
//register namespaces: 
/* <gmd:MD_Metadata xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml" xmlns:gts="http://www.isotc211.org/2005/gts" xmlns:srv="http://www.isotc211.org/2005/srv" id="_ingrid-group_iplug-rp-udk-db_263">*/
		for($a = 0; $a < count($md_ident); $a++) {
			$resultOfXpath = $openSearchDetailXML->xpath('/csw:GetRecordByIdResponse'.$md_ident[$a]['iso19139']);
			for ($i = 0; $i < count($resultOfXpath); $i++) {
				$md_ident[$a]['value'] = $md_ident[$a]['value'].",".$resultOfXpath[$i];
			}
			$md_ident[$a]['value'] = ltrim($md_ident[$a]['value'],',');
		}
	break;
	default:
	foreach ( $openSearchDetailXML->channel->item->details->detail as $detail) { 
		$detail_key=(string)$detail->{'detail-key'};		#cast explicitly to string
		$detail_value=(string)$detail->{'detail-value'};
		$detail_array[$detail_key] = $detail_value;
		$detail_keys[$j]=$detail_key;	
		if(isset($_REQUEST["mdtype"])&($_REQUEST["mdtype"]=='debug') ) {
			if (in_array($detail_key, $ibus_names)) {
				echo "Key <b>".$detail_key."</b> exists in lookup table!<br>";
				$i++;
			}
		}
		$j++;			
	} 
	break;
}

//generate output for different parameters mdtype

switch ($_REQUEST["mdtype"]) {
	case "html":
		$html = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$languageCode.'">';
		$html .= '<body>';
		$metadataStr .= '<head>' . 
		'<title>'._mb("Metadata").'</title>' . 
		'<meta name="description" content="'._mb("Metadata").'" xml:lang="'.$languageCode.'" />'.
		'<meta name="keywords" content="'._mb("Metadata").'" xml:lang="'.$languageCode.'" />'	.	
		'<meta http-equiv="cache-control" content="no-cache">'.
		'<meta http-equiv="pragma" content="no-cache">'.
		'<meta http-equiv="expires" content="0">'.
		'<meta http-equiv="content-language" content="'.$languageCode.'" />'.
		'<meta http-equiv="content-style-type" content="text/css" />'.
		'<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">' . 	
		'</head>';
		$html .= $metadataStr;
		//define the javascripts to include
		$html .= '<link type="text/css" href="../css/metadata.css" rel="Stylesheet" />';
		if ($layout == 'tabs') {
			$html .= '<link type="text/css" href="../extensions/jquery-ui-1.8.1.custom/css/custom-theme/jquery-ui-1.8.5.custom.css" rel="Stylesheet" />';	
			$html .= '<script type="text/javascript" src="../extensions/jquery-ui-1.8.1.custom/js/jquery-1.4.2.min.js"></script>';
			$html .= '<script type="text/javascript" src="../extensions/jquery-ui-1.8.1.custom/js/jquery-ui-1.8.1.custom.min.js"></script>';
			//initialize tabs
			$html .= '<script type="text/javascript">';
			$html .= '$(function() {';
			$html .= '	$("#tabs").tabs();';
			$html .= '});';
			$html .= '</script>';
			//independently define the headers of the parts
			$html .= '<div class="demo">';
			$html .= '<div id="tabs">';
			$html .= '<ul>';
			$html .= 	'<li><a href="#tabs-1">'._mb("Overview").'</a></li>';
			$html .= 	'<li><a href="#tabs-2">'._mb("Properties").'</a></li>';
			$html .= 	'<li><a href="#tabs-3">'._mb("Contact").'</a></li>';
			$html .= 	'<li><a href="#tabs-4">'._mb("Terms of use").'</a></li>';
			$html .= 	'<li><a href="#tabs-5">'._mb("Quality").'</a></li>';
			$html .= 	'<li><a href="#tabs-6">'._mb("Interfaces").'</a></li>';
			$html .= '</ul>';
		}
		if ($layout == 'accordion') {
			$html .= '<link type="text/css" href="../extensions/jquery-ui-1.8.1.custom/css/custom-theme/jquery-ui-1.8.4.custom.css" rel="Stylesheet" />';	
			$html .= '<script type="text/javascript" src="../extensions/jquery-ui-1.8.1.custom/js/jquery-1.4.2.min.js"></script>';
			$html .= '<script type="text/javascript" src="../extensions/jquery-ui-1.8.1.custom/js/jquery-ui-1.8.1.custom.min.js"></script>';
			//define the javascript functions
			$html .= '<script type="text/javascript">';
			$html .= '	$(function() {';
			$html .= '		$("#accordion").accordion();';
			//$html .= '		$("#accordion").accordion({ autoHeight: false});';
			//$html .= '		$("#accordion").accordion({ autoHeight: false , clearStyle: true });';
			$html .= '	});';
			$html .= '	</script>';
			$html .= '<div class="demo">';
			$html .= '<div id="accordion">';
		}
		if ($layout == 'plain') {
			$html .= '<div class="demo">';
			$html .= '<div id="plain">';
		}
		//some placeholders
		$tableBegin =  "<table>\n";
		$t_a = "\t<tr>\n\t\t<th>\n\t\t\t";
		$t_b = "\n\t\t</th>\n\t\t<td>\n\t\t\t";
		$t_c = "\n\t\t</td>\n\t</tr>\n";
		$tableEnd = "</table>\n";
		//**************************overview part begin******************************
		//generate div tags for the content - the divs are defined in the array
		if ($layout == 'accordion') {
			$html .= '<h3><a href="#">'._mb("Overview").'</a></h3>';
			$html .= '<div style="height:300px">';
		}
		if ($layout == 'tabs') {
			$html .= '<div id="tabs-1">';
		}
		if ($layout == 'plain') {
			$html .= '<h3>'._mb("overview").'</h3>';
			$html .= '<div>';
		}
		$html .= '<p>';
		$html .= '<fieldset><legend>'._mb("Metadata").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[0]['html']."</b>: ".$t_b.$md_ident[0]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[31]['html']."</b>: ".$t_b.$md_ident[31]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[32]['html']."</b>: ".$t_b.$md_ident[32]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Identification").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[1]['html']."</b>: ".$t_b.$md_ident[1]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[2]['html']."</b>: ".$t_b.$md_ident[2]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[3]['html']."</b>: ".$t_b.$md_ident[3]['value'].$t_c;
		if ($md_ident[36]['value'] != "") {
			$html .= $t_a."<b>".$md_ident[36]['html']."</b>: ".$t_b."<img width=120 height=120 src = '".$md_ident[36]['value']."'>".$t_c;//preview
		}
		$html .= $t_a."<b>".$md_ident[5]['html']."</b>: ".$t_b.$md_ident[5]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[6]['html']."</b>: ".$t_b.$md_ident[6]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[26]['html']."</b>: ".$t_b.$md_ident[26]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[27]['html']."</b>: ".$t_b.$md_ident[27]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		
		$bbox = explode(',',$md_ident[12]['value']);

		if (count($bbox) == 4) {
			$wgs84Bbox = $bbox[0].",".$bbox[2].",".$bbox[1].",".$bbox[3];
			$getMapUrl = getExtentGraphic(explode(",", $wgs84Bbox));
			$html .= '<fieldset><legend>'._mb("Extent").'</legend>';
			if (defined('EXTENTSERVICEURL')) {
				$html .= "<img src='".$getMapUrl."'>";
			} else {
				$html .= _mb('Graphic unavailable');
			}
			$html .= '</fieldset>';
			
		}
		
		$html .= '<fieldset><legend>'._mb("Contact").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[26]['html']."</b>: ".$t_b.$md_ident[26]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[27]['html']."</b>: ".$t_b.$md_ident[27]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '</p>';
		$html .= '</div>';//element
		//***************************************************************************
		//**************************properties part begin******************************
		//generate div tags for the content - the divs are defined in the array
		if ($layout == 'accordion') {
			$html .= '<h3><a href="#">'._mb("Properties").'</a></h3>';
			$html .= '<div style="height:300px">';
		}
		if ($layout == 'tabs') {
			$html .= '<div id="tabs-2">';
		}
		if ($layout == 'plain') {
			$html .= '<h3>'._mb("Properties").'</h3>';
			$html .= '<div>';
		}
		$html .= '<p>';
		$html .= '<fieldset><legend>'._mb("Common").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[8]['html']."</b>: ".$t_b.$md_ident[8]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[9]['html']."</b>: ".$t_b.$md_ident[9]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[11]['html']."</b>: ".$t_b.$md_ident[11]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Geographic extent").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[33]['html']."</b>: ".$t_b.$md_ident[33]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[12]['html']."</b>: ".$t_b.$md_ident[12]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Temporal extent").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[13]['html']."</b>: ".$t_b.$md_ident[13]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[14]['html']."</b>: ".$t_b.$md_ident[14]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[15]['html']."</b>: ".$t_b.$md_ident[15]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[16]['html']."</b>: ".$t_b.$md_ident[16]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Format").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[34]['html']."</b>: ".$t_b.$md_ident[34]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[35]['html']."</b>: ".$t_b.$md_ident[35]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Service information").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[10]['html']."</b>: ".$t_b.$md_ident[10]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[7]['html']."</b>: ".$t_b.$md_ident[7]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '</p>';
		$html .= '</div>';//element
		//***************************************************************************
		//**************************contact part begin******************************
		//generate div tags for the content - the divs are defined in the array
		if ($layout == 'accordion') {
			$html .= '<h3><a href="#">'._mb("Properties").'</a></h3>';
			$html .= '<div style="height:300px">';
		}
		if ($layout == 'tabs') {
			$html .= '<div id="tabs-3">';
		}
		if ($layout == 'plain') {
			$html .= '<h3>'._mb("Properties").'</h3>';
			$html .= '<div>';
		}
		$html .= '<p>';
		$html .= '<fieldset><legend>'._mb("Data/Service provider").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[26]['html']."</b>: ".$t_b.$md_ident[26]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[28]['html']."</b>: ".$t_b.$md_ident[28]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[27]['html']."</b>: ".$t_b.$md_ident[27]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Metadata provider").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[29]['html']."</b>: ".$t_b.$md_ident[29]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[30]['html']."</b>: ".$t_b.$md_ident[30]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		
		$html .= '</p>';
		$html .= '</div>';//element
		//***************************************************************************
		//**************************terms of use part begin******************************
		//generate div tags for the content - the divs are defined in the array
		if ($layout == 'accordion') {
			$html .= '<h3><a href="#">'._mb("Terms of use").'</a></h3>';
			$html .= '<div style="height:300px">';
		}
		if ($layout == 'tabs') {
			$html .= '<div id="tabs-4">';
		}
		if ($layout == 'plain') {
			$html .= '<h3>'._mb("Terms of use").'</h3>';
			$html .= '<div>';
		}
		$html .= '<p>';
		$html .= '<fieldset><legend>'._mb("Conditions").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[23]['html']."</b>: ".$t_b.$md_ident[23]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Access constraints").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[24]['html']."</b>: ".$t_b.$md_ident[24]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[25]['html']."</b>: ".$t_b.$md_ident[25]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		
		$html .= '</p>';
		$html .= '</div>';//element
		//***************************************************************************
		//**************************quality part begin******************************
		//generate div tags for the content - the divs are defined in the array
		if ($layout == 'accordion') {
			$html .= '<h3><a href="#">'._mb("Quality").'</a></h3>';
			$html .= '<div style="height:300px">';
		}
		if ($layout == 'tabs') {
			$html .= '<div id="tabs-5">';
		}
		if ($layout == 'plain') {
			$html .= '<h3>'._mb("Quality").'</h3>';
			$html .= '<div>';
		}
		$html .= '<p>';
		$html .= '<fieldset><legend>'._mb("Lineage").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[17]['html']."</b>: ".$t_b.$md_ident[17]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Resolution").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[18]['html']."</b>: ".$t_b.$md_ident[18]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[19]['html']."</b>: ".$t_b.$md_ident[19]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		$html .= '<fieldset><legend>'._mb("Validity").'</legend>';
		$html .= $tableBegin;
		$html .= $t_a."<b>".$md_ident[20]['html']."</b>: ".$t_b.$md_ident[20]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[21]['html']."</b>: ".$t_b.$md_ident[21]['value'].$t_c;
		$html .= $t_a."<b>".$md_ident[22]['html']."</b>: ".$t_b.$md_ident[22]['value'].$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		
		$html .= '</p>';
		$html .= '</div>';//element
		//***************************************************************************
//**************************quality part begin******************************
		//generate div tags for the content - the divs are defined in the array
		if ($layout == 'accordion') {
			$html .= '<h3><a href="#">'._mb("Interfaces").'</a></h3>';
			$html .= '<div style="height:300px">';
		}
		if ($layout == 'tabs') {
			$html .= '<div id="tabs-6">';
		}
		if ($layout == 'plain') {
			$html .= '<h3>'._mb("Interfaces").'</h3>';
			$html .= '<div>';
		}
		$html .= '<p>';
		$html .= '<fieldset><legend>'._mb("Metadata").'</legend>';
		$html .= $tableBegin;
		//exchange mdtype html with iso19139
		$queryNew = str_replace("mdtype=html","mdtype=iso19139",$_SERVER['QUERY_STRING']);
		$html .= $t_a."<b>"._mb("ISO19139")."</b>: <a href='".$url."' target='_blank'>"._mb("Metadata")."</a><a href='".$_SERVER['PHP_SELF']."?".$queryNew."&validate=true' target='_blank'><img style='border: none;' src = '../img/misc/icn_inspire_validate.png' title='"._mb("INSPIRE Validator")."'></a>".$t_c;
		$html .= $tableEnd;
		$html .= '</fieldset>';
		
		$html .= '</p>';
		$html .= '</div>';//element
		//***************************************************************************
		$html .= '</div>'; //accordion
		$html .= '</div>'; //demo
		$html .= '</body>';
		$html .= '</html>';
		echo $html;
		die();
	break;
	case "inspire":
		echo "<a href='".$url."'>GetRecordById URL</a><br><br>";
		for($a = 0; $a < count($md_ident); $a++) {
			echo "<b>".$md_ident[$a]['html']."</b>: ".$md_ident[$a]['value']."<br><br>";
		}
		die();
	break;
	case "debug":
		echo "<a href='".$url."'>GetRecordById URL</a><br><br>";
		for($a = 0; $a < count($md_ident); $a++) {
			echo "<b>".$md_ident[$a]['html']."</b>: ".$md_ident[$a]['value']."<br><br>";
		}
		die();
	break;
	default:
		echo "<a href='".$url."'>GetRecordById URL</a><br><br>";
		for($a = 0; $a < count($md_ident); $a++) {
			echo "<b>".$md_ident[$a]['html']."</b>: ".$md_ident[$a]['value']."<br><br>";
		}
		die();
	break;
}

if ($_REQUEST['mdtype']=='debug'){
		echo "DEBUG Metadatenanzeige<br>";
		#define table
		echo "<html><table border=\"1\"><br>";
		echo "<tr>";
		#loop for each detail - tag - sometimes there are other tags in there - if one detail has more than one entry! - maybe this must be interpreted but later!
		foreach ($detail_keys as $detailkey) {
			if (in_array($detailkey, $ibus_names)==false){
				echo  "<td >".$detailkey."</td>";
				}
				else {
				echo "<td bgcolor=\"green\">".$md_ident[array_search($detailkey, $ibus_names)]['html']."(".$detailkey.")</td>";
				}
			#echo "</td>";
			echo "<td>";
			echo $detail_array[$detailkey];
			echo "</tr>";
		}
		echo "</table></html>";
}


if ($_REQUEST['mdtype']=='html'){
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
	<head>
		<title>GeoPortal Rheinland-Pfalz - Metadaten</title>
		<meta name="description" content="Metadaten" xml:lang="de" />
		<meta name="keywords" content="Metadaten" xml:lang="de" />		
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="expires" content="0">
		<meta http-equiv="content-language" content="de" />
		<meta http-equiv="content-style-type" content="text/css" />		
<?php
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
		<link rel="stylesheet" type="text/css" href="../../../portal/fileadmin/design/css/screen.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="../../../portal/fileadmin/design/css/print.css" media="print" />
	</head>
	<body id="top" class="popup">

	
	<div id="header_gray">
	<a href="javascript:window.print()">Drucken <img src="../../../portal/fileadmin/design/images/icon_print.gif" width="14" height="14" alt="" /></a>
	<a href="javascript:window.close()">Fenster schlie&szlig;en <img src="../../../portal/fileadmin/design/images/icon_close.gif" width="14" height="14" alt="" /></a>
	</div>
	<div id="header_redbottom"></div>
	<div id="header_red"></div>
	
	<div class="content">
<?php
	echo "<h1>Detailinformationen:</h1>";
	#define table
	echo "<html><table class='contenttable-0-wide'>";
	echo "<tr>";
	#loop for each detail - tag - sometimes there are other tags in there - if one detail has more than one entry! - maybe this must be interpreted but later!
	foreach ($detail_keys as $detailkey) {	
		if (in_array($detailkey, $ibus_names)==true){
			echo "<td>".$md_ident[array_search($detailkey, $ibus_names)]['html']."</td>";
			echo "<td>";
			echo display_text($detail_array[$detailkey]);
			echo "</td></tr>";
		}
	}
	echo "</table></html>";
}

if ($_REQUEST['mdtype']=='inspire') {
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
	<head>
		<title>GeoPortal Rheinland-Pfalz - Metadaten</title>
		<meta name="description" content="Metadaten" xml:lang="de" />
		<meta name="keywords" content="Metadaten" xml:lang="de" />		
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="expires" content="0">
		<meta http-equiv="content-language" content="de" />
		<meta http-equiv="content-style-type" content="text/css" />		
<?php
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
		<link rel="stylesheet" type="text/css" href="../../../portal/fileadmin/design/css/screen.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="../../../portal/fileadmin/design/css/print.css" media="print" />
	</head>
	<body id="top" class="popup">
	<div id="header_gray">
	<a href="javascript:window.print()">Drucken <img src="../../../portal/fileadmin/design/images/icon_print.gif" width="14" height="14" alt="" /></a>
	<a href="javascript:window.close()">Fenster schlie&szlig;en <img src="../../../portal/fileadmin/design/images/icon_close.gif" width="14" height="14" alt="" /></a>
	</div>
	<div id="header_redbottom"></div>
	<div id="header_red"></div>
	<div class="content">
<?php
	echo "<img border=\"0\" src=\"img/inspire_tr_100.png\" alt=\"INSPIRE Logo\"><h1>INSPIRE Metadaten:</h1>";
	#define table
	echo "<html><table class='contenttable-0-wide'>";
	echo "<tr>";
	#loop for each detail - tag - sometimes there are other tags in there - if one detail has more than one entry! - maybe this must be interpreted but later!
	foreach ($detail_keys as $detailkey) {
		if (in_array($detailkey, $ibus_names)==true){
			if ($md_ident[array_search($detailkey, $ibus_names)]['inspiremandatory']=='true') {
				echo "<td>".$md_ident[array_search($detailkey, $ibus_names)]['inspire']."</td>";
				echo "<td>";
				echo display_text($detail_array[$detailkey]);
				echo "</td></tr>";
			}
		}
	}
	echo "</table></html>";
	echo '<br><b>INSPIRE output not completly implemented!<b><br>';
}
?>
