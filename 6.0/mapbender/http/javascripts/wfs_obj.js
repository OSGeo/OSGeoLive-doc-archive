//$Id: wfs_obj.js 2413 2008-04-23 16:21:04Z christoph $
//$Header: /cvsroot/mapbender/mapbender/http/javascripts/wfs_obj.js,v 1.3 2005/09/13 14:38:11 bjoern_heuser Exp $
//global variables
var wfs = [];
var wfs_featuretype_count = 0;
var wfs_element_count = 0;
//list of all wms-objects
function add_wfs(
			wfs_id,
			wfs_version,
			wfs_title,
			wfs_abstract,
			wfs_getcapabilities,
			wfs_describefeaturetype){
					wfs[wfs.length] = new wfs_const( 
					wfs_id,
			      wfs_version,
			      wfs_title,
			      wfs_abstract,
			      wfs_getcapabilities,
			      wfs_describefeaturetype);
					//wfs_featuretype[wfs.length - 1] = [];
}
//the wms constructor
function wfs_const(  
			wfs_id,
			wfs_version,
			wfs_title,
			wfs_abstract,
			wfs_getcapabilities,
			wfs_describefeaturetype){
   
	this.wfs_id = wfs_id;
	this.wfs_version = wfs_version;
	this.wfs_title = wfs_title;
	this.wfs_abstract = wfs_abstract;
	this.wfs_getcapabilities = wfs_getcapabilities;
	this.wfs_describefeaturetype = wfs_describefeaturetype;

	this.wfs_featuretype = [];
   //alert(wfs_id + " , " +wfs_title + " , " +wfs_abstract + " , " +wfs_getcapabilities + " , " +wfs_describefeaturetype);
}
//featuretype
function wfs_add_featuretype(
			featuretype_name,
			featuretype_title,
			featuretype_srs,
			featuretype_geomtype){
                      
	      wfs[wfs.length-1].wfs_featuretype[wfs[wfs.length-1].wfs_featuretype.length] = new featuretype(
											featuretype_name,
											featuretype_title,
											featuretype_srs,
											featuretype_geomtype);
//alert(featuretype_name + " , " +featuretype_title + " , " +featuretype_srs + " , " +featuretype_geomtype);
}
function featuretype(
			featuretype_name,
			featuretype_title,
			featuretype_srs,
			featuretype_geomtype){
	this.featuretype_name = featuretype_name;
	this.featuretype_title = featuretype_title;
	this.featuretype_srs = featuretype_srs;
	this.featuretype_geomtype = featuretype_geomtype;
	this.element = [];
	wfs_featuretype_count++; 
}
//elements
function wfs_add_featuretype_element(element_name, element_type, element_count, featuretype_count){
	wfs[wfs.length-1].wfs_featuretype[featuretype_count].element[element_count] = [];
	wfs[wfs.length-1].wfs_featuretype[featuretype_count].element[element_count].name = element_name;
	wfs[wfs.length-1].wfs_featuretype[featuretype_count].element[element_count].type = element_type;
   //alert(element_name +" , "+element_type);
}
