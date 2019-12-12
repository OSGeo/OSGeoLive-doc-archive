OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;

var map, layer, select, hover, multi, control;
function init(){
  OpenLayers.ProxyHost= "../cgi-bin/proxy.cgi?url=";
  map = new OpenLayers.Map('map', {
    controls: [
	       new OpenLayers.Control.Navigation()
	       ]
	});
  layerLS = new OpenLayers.Layer.WMS(
				     "OL baselayer",
				     "http://labs.metacarta.com/wms/vmap0",
				     {layers: 'basic', format: 'image/png'}, {isBaseLayer:true}
				     );
  
  layer = new OpenLayers.Layer.WMS(
				   "States WMS/WFS",
				   "http://localhost:8082/geoserver/ows",
				   {layers: 'topp:states',transparent:'true', format: 'image/png'},
				   {isBaseLayer: false, buffer:1, singleTile:true}
				   );
  
  select = new OpenLayers.Layer.Vector("Selection", {styleMap:
      new OpenLayers.Style(OpenLayers.Feature.Vector.style["select"])
	});
  hover = new OpenLayers.Layer.Vector("Hover");
  multi = new OpenLayers.Layer.Vector("Multi", {styleMap:
      new OpenLayers.Style({
	fillColor:"red",
	    fillOpacity:0.4,
	    strokeColor:"red",
	    strokeOpacity:1,
	    strokeWidth:2
	    })
	});
  map.addLayers([layerLS, layer, select, hover, multi]);
  
  control = new OpenLayers.Control.GetFeature({
    protocol: OpenLayers.Protocol.WFS.fromWMSLayer(layer)
	});
  
  control.events.register("featureselected", this, function(e) {
      select.addFeatures([e.feature]);
      elist=$(".single-process:not(.ui-state-disabled)");
      for(var i=0;i<elist.length;i++)
         elist[i].style.display='block';
      if(hover.features.length>=1){
	slist=$(".multi-process:not(.ui-state-disabled)");
        for(var i=0;i<slist.length;i++)
	   slist[i].style.display='block';
      }
    });
  control.events.register("featureunselected", this, function(e) {
      select.removeFeatures([e.feature]);
    });
  control.events.register("hoverfeature", this, function(e) {
      hover.addFeatures([e.feature]);
    });
  control.events.register("outfeature", this, function(e) {
      hover.removeFeatures([e.feature]);
    });
  map.addControl(control);
  control.activate();
  map.zoomToExtent(new OpenLayers.Bounds(-140.444336,25.115234,-44.438477,50.580078));
}


function toggleControl(element) {
  for(key in mapControls) {
    alert(key);
    var control = mapControls[key];
    //alert ($(element).is('.ui-state-active'));
    if(element.name == key && $(element).is('.ui-state-active')) {
      control.activate();
    } else {
      control.deactivate();
    }
  }
}

function restartDisplay(){
  var tmp=[select,hover,multi];
  for(i=0;i<tmp.length;i++)
  if(tmp[i].features.length>=1)
        tmp[i].removeFeatures(tmp[i].features);
  slist=$(".single-process:not(.ui-state-disabled)");
  for(var i=0;i<slist.length;i++)
    slist[i].style.display='none';
  mlist=$(".multi-process:not(.ui-state-disabled)");
  for(var i=0;i<mlist.length;i++)
    mlist[i].style.display='none';
}

function singleProcessing(aProcess) {
  if (select.features.length == 0)
    return alert("No feature selected!");
  if(multi.features.length>=1)
  	multi.removeFeatures(multi.features);
  var url = '/zoo/?request=Execute&service=WPS&version=1.0.0&';
  if (aProcess == 'Buffer') {
    var dist = document.getElementById('bufferDist')?document.getElementById('bufferDist').value:'1';
    if (isNaN(dist))
return alert("Distance is not a Number!");
    url+='Identifier=Buffer&DataInputs=BufferDistance='+dist+'@datatype=interger;InputPolygon=Reference@xlink:href=';
  } else
    url += 'Identifier='+aProcess+'&DataInputs=InputPolygon=Reference@xlink:href=';
  var xlink = control.protocol.url +"?SERVICE=WFS&REQUEST=GetFeature&VERSION=1.0.0";
  xlink += '&typename='+control.protocol.featurePrefix;
  xlink += ':'+control.protocol.featureType;
  xlink += '&SRS='+control.protocol.srsName;
  xlink += '&FeatureID='+select.features[0].fid;
  url += encodeURIComponent(xlink);
  url += '&RawDataOutput=Result@mimeType=text/xml';
  
  var request = new OpenLayers.Request.XMLHttpRequest();
  request.open('GET',url,true);
  request.onreadystatechange = function() {
    if(request.readyState == OpenLayers.Request.XMLHttpRequest.DONE) {
      var GML = new OpenLayers.Format.GML();
      var features = GML.read(request.responseText);
      hover.removeFeatures(hover.features);
      hover.addFeatures(features);
      slist=$(".multi-process:not(.ui-state-disabled)");
      for(var i=0;i<slist.length;i++)
        slist[i].style.display='block';
    }
  }
  request.send();
}

function multiProcessing(aProcess) {
  if (select.features.length == 0 || hover.features.length == 0)
    return alert("No feature created!");
  var url = '/zoo/';
  var xlink = control.protocol.url +"?SERVICE=WFS&REQUEST=GetFeature&VERSION=1.0.0";
  xlink += '&typename='+control.protocol.featurePrefix;
  xlink += ':'+control.protocol.featureType;
  xlink += '&SRS='+control.protocol.srsName;
  xlink += '&FeatureID='+select.features[0].fid;
  var GeoJSON = new OpenLayers.Format.GeoJSON();
  try {
    var params = '<wps:Execute service="WPS" version="1.0.0" xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wps/1.0.0/../wpsExecute_request.xsd">';
    params += '<ows:Identifier>'+aProcess+'</ows:Identifier>';
    params += '<wps:DataInputs>';
    params += '<wps:Input>';
    params += '<ows:Identifier>InputEntity1</ows:Identifier>';
    params += '<wps:Reference xlink:href="'+xlink.replace(/&/gi,'&amp;')+'"/>';
    params += '</wps:Input>';
    params += '<wps:Input>';
    params += '<ows:Identifier>InputEntity2</ows:Identifier>';
    params += '<wps:Data>';
    params += '<wps:ComplexData mimeType="application/json"> '+GeoJSON.write(hover.features[0].geometry)+' </wps:ComplexData>';
    params += '</wps:Data>';
    params += '</wps:Input>';
    params += '</wps:DataInputs>';
    params += '<wps:ResponseForm>';
    params += '<wps:RawDataOutput mimeType="text/xml">';
    params += '<wps:Output asReference="false" mimeType="application/json">';
    params += '<ows:Identifier>Result</ows:Identifier>';
    params += '</wps:Output>';
    params += '</wps:RawDataOutput>';
    params += '</wps:ResponseForm>';
    params += '</wps:Execute>';
  } catch(e) {
    alert(e);
    return false;
  }
  var request = new OpenLayers.Request.XMLHttpRequest();
  request.open('POST',url,true);
  request.setRequestHeader('Content-Type','text/xml');
  request.onreadystatechange = function() {
    if(request.readyState == OpenLayers.Request.XMLHttpRequest.DONE) {
      var GML = new OpenLayers.Format.GML();
      var features = GML.read(request.responseText);
      multi.removeFeatures(multi.features);
      multi.addFeatures(features);
    }
  }
  request.send(params);
}

$(function(){
    $(".fg-button:not(.ui-state-disabled)")
      .hover(
	     function(){ 
	       $(this).addClass("ui-state-hover"); 
	     },
	     function(){ 
	       $(this).removeClass("ui-state-hover"); 
	     }
	     )
      .mousedown(function(){
          if( !$(this).is('.process') && !$(this).is('.refresh1')){
	  $(this).parents('.fg-buttonset-single:first').find(".fg-button.ui-state-active").removeClass("ui-state-active");
	  if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){ $(this).removeClass("ui-state-active"); }
	  else { $(this).addClass("ui-state-active"); }	
	  }
	})
      .mouseup(function(){
	  if(! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ){
	    $(this).removeClass("ui-state-active");
	  }
	});
  });	

$(document).ready(function(){    
    $('#btnPrev[title], #btnNext[title], a[title]').qtip({ 
      style: { 
	name: 'dark', 
	    tip: true,
	    border:{width:2,color:'#fe8701',radius:3},
	    width:{min:100}
	}
      });
    elist=$('.process');
    for(var i=0;i<elist.length;i++){
	elist[i].style.display='none';
    }
});
