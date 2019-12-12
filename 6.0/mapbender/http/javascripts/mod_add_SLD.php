<?php
# $Id: mod_add_SLD.php 3681 2009-03-12 16:29:44Z christoph $
# add SLD-Parameter parameters to MapRequest
# http://www.mapbender.org/index.php/mod_add_vendorspecific.php
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

?>
var sldURL = "";
var mod_remove_layer_and_styles = "";
var removeLayerAndStylesAffectedWMSTitle = "deegree wms";
//var addSldAffectedWMSTitle = "deegree wms";
//var sldLocationUrl = "http://wms1.ccgis.de/mapbender_dev/tmp/";

var addSldAffectedWMSTitle = "Bonn Ergebnisse"; 
var sldLocationUrl = "http://localhost/bonn_orlando_svn/http/tmp/"; 

mod_remove_layer_and_styles += "if (sldURL != '' && (functionName == 'setMapRequest' || functionName == 'setSingleMapRequest') && mb_mapObj[i].wms[ii].wms_title == removeLayerAndStylesAffectedWMSTitle){";
mod_remove_layer_and_styles += "newMapURL = newMapURL.replace(/LAYERS=[^&]*&/, '');";
mod_remove_layer_and_styles += "newMapURL = newMapURL.replace(/STYLES=[^&]*&/, '');";
mod_remove_layer_and_styles += "}";

function mod_sld_init(wmsTitle, functionName) {
		//alert(sldURL + ' ' + functionName );
	if (sldURL != ''){
		if (functionName == 'setMapRequest' || functionName == 'setSingleMapRequest') {
			//Stadt Bonn: if (wmsTitle == 'deegree wms'){	
					
			if (wmsTitle == addSldAffectedWMSTitle){
				return 'SLD=' + sldURL;
			}
		}
	}

	return "";
}


mb_registerVendorSpecific(mod_remove_layer_and_styles);
mb_registerVendorSpecific("mod_sld_init(mb_mapObj[i].wms[ii].wms_title, functionName);");


function mod_set_sld(sldString){ 
	mb_ajax_post("../orlando/saveSLD.php", {'sld':sldString}, function (json, status) {
	var result = eval('('+json+')');
	sldURL = sldLocationUrl + result.filename;
	zoom("mapframe1", true,0.9999);
	});

}
