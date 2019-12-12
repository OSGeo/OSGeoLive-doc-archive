function mod_displayObj(){
	var tg = "";
	tg +="<head>";
	tg += "<title>Mapbender Info</title>";
	tg += "<style type='text/css'>";
	tg +="<!-- body{font-family:Verdana, Geneva, Arial, Helvetica, sans-serif;font-size:10pt}";
	tg +="hr{color:blue;}";
	tg +="table{border-width:1;font-size:10pt}";
	tg +="th{background-color:#F08080;text-align:left;}";
	tg +="tr{background-color:white;}";
	tg +=".tr_head{font-weight:bold;background-color:silver;}";
	tg +=".th_wfs{font-weight:bold;background-color:#BDBAF7;text-align:left;}";
	tg +="--></style></head>";

	var myWin = null;
	tg += "<body><h3 >Mapbender Metadata</h3>";
	tg += "<h4 >active maps</h4>";

	for(var i=0; i<mb_mapObj.length; i++){
		tg += "<h4 >frame: "+ mb_mapObj[i].elementName+"</h4>";
		tg += "<table border='1' width='98%' rules='rows'><tr>";
		tg += "<th width='200'>wms:</th><th>request</th></tr>";
		for(var ii=0; ii<mb_mapObj[i].wms.length; ii++){
			tg +="<tr><td width='200'>"+mb_mapObj[i].wms[ii].wms_title+"</td><td>";
			// if the mapURL is not defined or false, don't add a link
			if (mb_mapObj[i].mapURL[ii] == false || typeof(mb_mapObj[i].mapURL[ii]) == 'undefined' ){
				tg += mb_mapObj[i].mapURL[ii];
			}
			else{
				tg += "<a href='"+ mb_mapObj[i].mapURL[ii]+"' target='_blank'>"+ mb_mapObj[i].mapURL[ii]+"</a>";
			}
			tg +="</td></tr>";
		}
		tg += "</table><br>";
	}
	tg += "<hr>";
   
	for(var j=0; j<mb_mapObj.length; j++){   
		for(var cnt=0; cnt<mb_mapObj[j].wms.length;cnt++){
			tg += "<h4 >"+ mb_mapObj[j].frameName+" WMS: "+ cnt+ ": "+mb_mapObj[j].wms[cnt].wms_title+" ("+mb_mapObj[j].wms[cnt].wms_abstract+") </h4>";
			tg += "<table border='1' rules='rows'><tr>";
			tg += "<th >WMS nr:</th><th>" + cnt+ "</th></tr>";
			tg += "<tr><td>wms_id:</td><td>" + mb_mapObj[j].wms[cnt].wms_id + "</td></tr>";
			tg += "<tr><td>wms_version:</td><td>" + mb_mapObj[j].wms[cnt].wms_version + "</td></tr>";
			tg += "<tr><td>wms_title: </td><td>" + mb_mapObj[j].wms[cnt].wms_title + "</td></tr>";
			tg += "<tr><td>wms_abstract:</td><td>" + mb_mapObj[j].wms[cnt].wms_abstract + "</td></tr>";
			tg += "<tr><td>wms_getmap: </td><td>" + mb_mapObj[j].wms[cnt].wms_getmap + "</td></tr>";
			tg += "<tr><td>wms_getfeatureinfo: </td><td>" + mb_mapObj[j].wms[cnt].wms_getfeatureinfo + "</td></tr>";
			tg += "<tr><td>wms_getlegendurl: </td><td>" + mb_mapObj[j].wms[cnt].wms_getlegendurl + "</td></tr>";
			tg += "<tr><td>gui_wms_mapformat: </td><td>" + mb_mapObj[j].wms[cnt].gui_wms_mapformat + "</td></tr>";
			tg += "<tr><td>gui_wms_featureinfoformat: </td><td>" + mb_mapObj[j].wms[cnt].gui_wms_featureinfoformat + "</td></tr>";
			tg += "<tr><td>gui_wms_exceptionformat: </td><td>" + mb_mapObj[j].wms[cnt].gui_wms_exceptionformat + "</td></tr>";
			tg += "<tr><td>gui_wms_epsg: </td><td>" + mb_mapObj[j].wms[cnt].gui_wms_epsg + "</td></tr>";
			tg += "<tr><td>gui_wms_visible: </td><td>" + mb_mapObj[j].wms[cnt].gui_wms_visible + "</td></tr>" ;
            
	  
			tg += "<tr class='tr_head'><td>Data: </td><td>.</td></tr>";
			for(var i=0; i<mb_mapObj[j].wms[cnt].data_type.length; i++){
				tg += "<tr><td>" + mb_mapObj[j].wms[cnt].data_type[i] + "</td><td>" + mb_mapObj[j].wms[cnt].data_format[i] + "</td></tr>" ;
			}
			for(var i=0; i<mb_mapObj[j].wms[cnt].gui_epsg.length; i++){
				tg += "<tr class='tr_head'><td>epsg : </td><td>" + mb_mapObj[j].wms[cnt].gui_epsg[i] + "</td></tr>" ;
				tg += "<tr><td>minx : </td><td>" + mb_mapObj[j].wms[cnt].gui_minx[i] + "</td></tr>" ;
				tg += "<tr><td>miny : </td><td>" + mb_mapObj[j].wms[cnt].gui_miny[i] + "</td></tr>" ;
				tg += "<tr><td>maxx : </td><td>" + mb_mapObj[j].wms[cnt].gui_maxx[i] + "</td></tr>" ;
				tg += "<tr><td>maxy : </td><td>" + mb_mapObj[j].wms[cnt].gui_maxy[i]+"</td></tr>" ;
			}
	     
			tg += "</table><br>";
	     
			tg += "<table border='1' rules='rows'><tr class='tr_head'>";
			tg += "<td>layer_id</td>";
			tg += "<td>layer_uid</td>";
			tg += "<td>layer_pos</td>";
			tg += "<td>layer_parent</td>";
			tg += "<td>layer_name</td>";
			tg += "<td>layer_title</td>";
			tg += "<td>layer_dataurl</td>";
			tg += "<td>layer_queryable</td>";
			tg += "<td>layer_minscale</td>";
			tg += "<td>layer_maxscale</td>";
			tg += "<td>gui_layer_wms_id</td>";
			tg += "<td>gui_layer_status</td>";
			tg += "<td>gui_layer_selectable</td>";
			tg += "<td>gui_layer_visible</td>";
			tg += "<td>gui_layer_queryable</td>";
			tg += "<td>gui_layer_querylayer</td>";
			tg += "<td>gui_layer_minscale</td>";
			tg += "<td>gui_layer_maxscale</td>";
			tg += "<td>gui_layer_wfs_featuretype</td>";
			tg += "</tr>";
	          
			for(var i=0; i<mb_mapObj[j].wms[cnt].objLayer.length; i++){
				tg += "<tr>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_id + "</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_uid + "</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_pos + "</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_parent+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_name+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_title+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_dataurl_href+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_queryable+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_minscale+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].layer_maxscale+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_wms_id+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_status+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_selectable+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_visible+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_queryable+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_querylayer+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_minscale+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_maxscale+"</td>";
				tg += "<td>"+mb_mapObj[j].wms[cnt].objLayer[i].gui_layer_wfs_featuretype+"</td>";
				tg += "</tr>";
			}
			tg += "</table><br><br>";
		}
		tg += "<hr>";
	}

	if(wfs){
		for(var cnt=0; cnt<wfs.length;cnt++){
			tg += "<br><h4 style='font-color:blue'>WFS: "+ cnt+ ": "+wfs[cnt].wfs_title+" ("+wfs[cnt].wfs_abstract+") </h4>";
			tg+="<table border='1' rules='rows'><tr>";
    
			tg += "<th class='th_wfs' width='200'>WFS nr: </th><th class='th_wfs'>" + cnt+ "</th></tr>";
			tg += "<tr><td>wfs_id:</td><td> " + wfs[cnt].wfs_id + "</td></tr>";
			tg += "<tr><td>wfs_version: </td><td>" + wfs[cnt].wfs_version + "</td></tr>";
			tg += "<tr><td>wfs_title: </td><td>" + wfs[cnt].wfs_title + "</td></tr>";
			tg += "<tr><td>wfs_abstract:</td><td>" + wfs[cnt].wfs_abstract + "</td></tr>";
			tg += "<tr><td>wfs_getcapabilities: </td><td><a href='"+ wfs[cnt].wfs_getcapabilities + "&VERSION=1.0.0&request=getcapabilities' target='_blank'>" + wfs[cnt].wfs_getcapabilities + "</a></td></tr>";
			tg += "<tr><td>wfs_describefeaturetype:</td><td>" + wfs[cnt].wfs_describefeaturetype +  "</td></tr>";

			tg += "</table><br>";
     
			tg += "<table border='1' rules='rows' ><tr class='tr_head'>";
			tg += "<td width='200'>wfs_featuretype</td>";
			tg += "<td>featuretype_name</td></tr>";
         
			for(var i=0; i<wfs[cnt].wfs_featuretype.length; i++){
				tg += "<tr><td>"+i+"</td>";
				tg += "<td>" + wfs[cnt].wfs_featuretype[i].featuretype_name + "</td>";
				tg += "<tr>";
			}
			tg += "</table><br>";
		}

	}
	//
	tg += "<BR></body>";
	myWin = window.open("","myWin","");
	myWin.document.open("text/html");
	myWin.document.write(tg);
	myWin.document.close();
	myWin.focus();  
}