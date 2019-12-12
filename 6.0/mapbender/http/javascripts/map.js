/**
 * used in mod_box1, mod_dragMapSize, mod_pan
 */
var mb_start_x = 0;
var mb_start_y = 0;
var mb_end_x = 0;
var mb_end_y = 0;

/**
 * @deprecated
 */
function mb_execloadWmsSubFunctions(obj){
	eventAfterLoadWMS.trigger(obj);
}

function mb_execWfsReadSubFunctions(geom) { 	 
	for(var i=0; i<mb_WfsReadSubFunctions.length; i++){ 	 
		mb_WfsReadSubFunctions[i](geom); 	 
	} 	 
}

function mb_execWfsWriteSubFunctions() { 	 
	for(var i=0; i<mb_WfsWriteSubFunctions.length; i++){
		mb_WfsWriteSubFunctions[i](); 	 
	} 	 
}

function mb_setWmcExtensionData(anArray) {
	for (var i in anArray) {
		if (typeof(anArray[i]) != "undefined") {
			currentWmcExtensionData[i] = anArray[i];
		}
	}
}

function mb_getWmcExtensionData(arrayKey) {
	for (var i in restoredWmcExtensionData) {
		if (arrayKey == i) {
			return restoredWmcExtensionData[i];
		}
	}
	var e = new Mb_warning("mb_getWmcExtensionData: "+arrayKey+" not found. Maybe this GUI does not allow loading or saving WMC documents from/to the session");
	return null;
}

/**
 * @deprecated
 */
function mb_mapObjremoveWMS(objind,wmsind){
	new Mb_warning("The function mb_mapObjremoveWMS is deprecated.");
	return mb_mapObj[objind].removeWms(wmsind);
};

/**
 * @deprecated
 */
function setMapRequest(frameName){
	new Mb_warning("The function setMapRequest is deprecated.");
	var ind = getMapObjIndexByName(frameName);	
	return mb_mapObj[ind].setMapRequest();
}

/**
 * @deprecated
 */
function setSingleMapRequest(frameName,wms_id){
	new Mb_warning("The function setSingleMapRequest is deprecated.");
	var ind = getMapObjIndexByName(frameName);	
	return mb_mapObj[ind].setSingleMapRequest(wms_id);
}

/**
 * @deprecated
 */
function mb_restateLayers(frameName,wms_id){
	new Mb_warning("The function mb_restateLayers is deprecated.");
	var ind = getMapObjIndexByName(frameName);	
	mb_mapObj[ind].restateLayers(wms_id);
}

/**
 * @deprecated
 */
function mb_checkScale(frameName,mObj,wmsObj){
	new Mb_warning("The function mb_checkScale is deprecated.");
	return mb_mapObj[mObj].checkScale(wmsObj);
}

/**
 * @deprecated
 */
function setFeatureInfoRequest(fName,x,y, path) {
	new Mb_warning("The function setFeatureInfoRequest is deprecated.");

/*
	var functionName = 'setFeatureInfoRequest';
	var ts = mb_timestamp();
	eventBeforeFeatureInfo.trigger({"fName":fName});
	var cnt_fi = 0;
	for(i=0; i<mb_mapObj.length; i++){
		if(mb_mapObj[i].frameName == fName){
			for(var ii=0; ii<mb_mapObj[i].wms.length; ii++){
				var newfeatureInfoRequest = "";
				var requestParams = "";
				var validation = false;
				newfeatureInfoRequest += mb_mapObj[i].wms[ii].wms_getfeatureinfo;          
            	newfeatureInfoRequest += mb_getConjunctionCharacter(mb_mapObj[i].wms[ii].wms_getfeatureinfo);
            	
				if(mb_mapObj[i].wms[ii].wms_version == "1.0.0"){requestParams += "WMTVER="+mb_mapObj[i].wms[ii].wms_version+"&REQUEST=feature_info&";}
				if(mb_mapObj[i].wms[ii].wms_version != "1.0.0"){requestParams += "VERSION="+mb_mapObj[i].wms[ii].wms_version+"&REQUEST=GetFeatureInfo&SERVICE=WMS&";}
				requestParams += "SRS="+mb_mapObj[i].epsg+"&";
				requestParams += "BBOX="+mb_mapObj[i].extent.toString()+"&";
				requestParams += "WIDTH="+mb_mapObj[i].width+"&";
				requestParams += "HEIGHT="+mb_mapObj[i].height+"&";
				requestParams += "LAYERS="+mb_mapObj[i].layers[ii]+"&";
				requestParams += "STYLES="+mb_mapObj[i].styles[ii]+"&";
				requestParams += "FORMAT="+mb_mapObj[i].wms[ii].gui_wms_mapformat+"&";
				requestParams += "INFO_FORMAT="+mb_mapObj[i].wms[ii].gui_wms_featureinfoformat+"&";
				requestParams += "EXCEPTIONS=application/vnd.ogc.se_xml&";
				if(mb_feature_count > 0){             
					requestParams += "FEATURE_COUNT="+mb_feature_count+"&";
				}
				requestParams += "QUERY_LAYERS="+mb_mapObj[i].querylayers[ii]+"&";
				requestParams += "X=" + x  + "&";
				requestParams += "Y=" + y;
				
				if(mb_mapObj[i].querylayers[ii] !== "" && mb_mapObj[i].layers[ii] !== ""){
					validation = true;
				}
				//add vendor-specific
				for(var v=0; v < mb_vendorSpecific.length; v++){
					var vendorSpecificString = eval(mb_vendorSpecific[v]); 
					requestParams += "&" + vendorSpecificString; 
				}
				if(Mapbender.log && validation){
					var tmp = eval(Mapbender.log + "('" + newfeatureInfoRequest + requestParams + "','" + ts + "')");
				}
				if(document.getElementById("FeatureInfoRedirect") && validation){
					newfeatureInfoRequest += requestParams;
					if(path){
						window.frames.FeatureInfoRedirect.document.getElementById(mb_mapObj[i].wms[ii].wms_id).src = path + "?url=" + escape(newfeatureInfoRequest)+"&"+mb_nr;
					}
					else{
						window.frames.FeatureInfoRedirect.document.getElementById(mb_mapObj[i].wms[ii].wms_id).src = newfeatureInfoRequest;
					}
					cnt_fi++;
            	}
				else if(path && validation){
					newfeatureInfoRequest += requestParams;
					(function () {
						var currentRequest = newfeatureInfoRequest;
						mb_ajax_post(path, {'url':currentRequest},function(js_code,status){
							if(js_code){
								try{
									var p = new mb_popup({
										title:"Feature Info",
										url:path + "?url=" + escape(currentRequest)+"&"+mb_nr,
										width:600,
										height:500,
										top:200,
										left:600
									});
									p.show();
								}catch(e){
									window.open(path + "?url=" + escape(currentRequest)+"&"+mb_nr, "" , "width=300,height=400,scrollbars=yes,resizable=yes");
								}
							}
							else{
								var e = new Mb_exception("No featureInfo results.");
							}
						});
					}());
					cnt_fi++;
				}
				else if(validation){
					newfeatureInfoRequest += requestParams;
					try{
						var p = new mb_popup({
							title:"Feature Info",
							url:newfeatureInfoRequest,
							width:600,
							height:500,
							top:200,
							left:600
						});
						p.show();
					}
					catch(e){
						window.open(newfeatureInfoRequest, "" , "width=300,height=400,scrollbars=yes,resizable=yes");					
					}
					cnt_fi++;
				}     
			}
		}
	}
   	if(cnt_fi === 0){
		alert(unescape("Please select a layer! \n Bitte waehlen Sie eine Ebene zur Abfrage aus!"));
	}
*/
}

/**
 * @deprecated
 */
function zoom(frameName,in_, factor,x,y) {
	new Mb_warning("The function zoom is deprecated.");
	var obj = getMapObjByName(frameName);
	return obj.zoom(in_, factor, x, y);
}

/**
 * @deprecated
 */
function mb_panMap(frameName,dir){
	new Mb_warning("The function mb_panMap is deprecated.");
	var obj = getMapObjByName(frameName);
	return obj.pan(dir);
}

/**
 * @deprecated
 */
/*
function handleSelectedLayer (frameName, wms_title, layerName, type, status) {
	new Mb_warning("The function handleSelectedLayer is deprecated.");
	var obj = getMapObjByName(frameName);
	for (var ii = 0; ii < obj.wms.length; ii++) {
		if (obj.wms[ii].wms_title == wms_title) {
			obj.wms[ii].handleLayer(layerName, type, status);
            obj.restateLayers(obj.wms[ii].wms_id);
		}
	}
}
*/
/**
 * @deprecated
 */
function handleSelectedLayer_array(frameName, array_wms, array_layer, type, status){
	new Mb_warning("The function handleSelectedLayer_array is deprecated.");
	var obj = getMapObjByName(frameName);
	changedWms = [];
	for (var i = 0; i < array_wms.length; i++) {
		var wmsWillBeUpdated = false;
		for (var ii = 0; ii < obj.wms.length; ii++) {
			if (obj.wms[ii].wms_id == array_wms[i]) {
				obj.wms[ii].handleLayer(array_layer[i], type, status);
				obj.restateLayers(obj.wms[ii].wms_id);
				if (!wmsWillBeUpdated) {
					changedWms.push(obj.wms[ii].wms_id);
					wmsWillBeUpdated = true;
				}				
			}
		}
	}
	for (aWms in changedWms) {
		obj.setSingleMapRequest(aWms);
	}
}

/**
 * @deprecated
 */
function makeClickPos2RealWorldPos(frameName, myClickX, myClickY) {
	new Mb_warning("The function makeClickPos2RealWorldPos is deprecated.");
	var ind = getMapObjIndexByName(frameName);
	var newPoint = mb_mapObj[ind].convertPixelToReal(new Point(myClickX, myClickY));
	return [newPoint.x, newPoint.y]
}

/**
 * @deprecated
 */
function makeRealWorld2mapPos(frameName,rw_posx, rw_posy){
	new Mb_warning("The function makeRealWorld2mapPos is deprecated.");
	var ind = getMapObjIndexByName(frameName);
	var aPoint = mb_mapObj[ind].convertRealToPixel(new Point(rw_posx, rw_posy)); 
	return [aPoint.x, aPoint.y];
}

// function for object-identification 
function getMapObjIndexByName(elementName){
	for(var i=0; i<mb_mapObj.length; i++){
		if(mb_mapObj[i].elementName == elementName){
			return i;
		}
	}   
}
function getMapObjByName(elementName){
	for(var i=0; i<mb_mapObj.length; i++){
		if(mb_mapObj[i].elementName == elementName){
			return mb_mapObj[i];
		}
	}
	return false;
}

/**
 * @deprecated
 */
function getWMSIDByTitle(frameName,wms_title){
	new Mb_warning("The function getWMSIDByTitle is deprecated.");
	var ind = getMapObjIndexByName(frameName);
	return mb_mapObj[ind].getWmsIdByTitle(wms_title);
}

/**
 * @deprecated
 */
function getWMSIndexById(frameName,wms_id){
	new Mb_warning("The function getWMSIndexById is deprecated.");
	var ind = getMapObjIndexByName(frameName);
	return mb_mapObj[ind].getWmsIndexById(wms_id);
}

/**
 * @deprecated
 */
function mb_repaintScale(frameName, x, y, scale){
	new Mb_warning("The function mb_repaintScale is deprecated.");
	var ind = getMapObjIndexByName(frameName);
	return mb_mapObj[ind].repaintScale(x, y, scale);
}

/**
 * @deprecated
 */
function mb_repaint(frameName,minx,miny,maxx,maxy){
	new Mb_warning("The function mb_repaint is deprecated.");
	var ind = getMapObjIndexByName(frameName);
	mb_mapObj[ind].extent = new Mapbender.Extent(minx, miny, maxx, maxy);
	setMapRequest(frameName);
}

/**
 * @deprecated 
 */
function mb_getScale(frameName) {
	new Mb_warning("The function mb_getScale is deprecated.");
	var ind = getMapObjIndexByName(frameName);
	return mb_mapObj[ind].getScale();
}

/**
 * converts the extent of the mapobject so that the maximum	extent will be displayed {@link Map#calculateExtent}
 * use: mb_mapObj.calculateExtent
 * @deprecated
 * 
 */
function mb_calculateExtent(frameName,minx,miny,maxx,maxy){
  new Mb_warning("The function mb_calculateExtent is deprecated.");
  var map = getMapObjByName(frameName);
  var extent = new Mapbender.Extent(minx,miny,maxx,maxy);
  map.calculateExtent(extent);
}

function handleSelectedWms (map, wms_id, type, val) {
	var ind =  getMapObjIndexByName(map);
	var wms =  getWMSIndexById(map,wms_id);
	for (var i = 0; i < mb_mapObj[ind].wms[wms].objLayer.length; i++) {
	
		var layername =  mb_mapObj[ind].wms[wms].objLayer[i].layer_name;
		mb_mapObj[ind].wms[wms].handleLayer(layername,type,val.toString());
	}
	mb_restateLayers(map, wms_id);
}
