// events
// 	geojson:loaded  	- a georssfeed was loaded from a server and is now available: function(url,geojson)
// 	georss:error	- a georssfeed failed to load: function(url,error);

var originalI18nObject = {
	"labelLoadError" : "Could not load Document",
	"labelName":"Name",
	"labelUntitled":"Untitled",
	"labelUrlBox": "Paste URL here",
	"sProcessing":   "Processing...",
	"sLengthMenu":   "Show _MENU_ entries",
	"sZeroRecords":  "No matching records found",
	"sInfo":         "SLowing _START_ to _END_ of _TOTAL_ entries",
	"sInfoEmpty":    "Showing 0 to 0 of 0 entries",
	"sInfoFiltered": "(filtered from _MAX_ total entries)",
	"sInfoPostFix":  "",
	"sSearch":       "Search:",
	"sUrl":          "",
	"oPaginate": {
		"sFirst":    "First",
		"sPrevious": "Previous",
		"sNext":     "Next",
		"sLast":     "Last"
	 }


};

//var translatedI18nObject = Mapbender.cloneObject(originalI18nObject);
var translatedI18nObject = originalI18nObject;


var displayFeatures = {

	options: {
		url: "",
		position: 'right',
		autoOpen: true,
		autoDisplay: true
	},
	_feeds: {},
	_popup : null,
	
	_create: function(){
		var self = this, o = this.options;
		this.element.bind('click', function(e){
			var map = self.element.mapbender();
			var pos = map.getMousePosition(e);		
			var clickPoint =  map.convertPixelToReal(new Point(pos.x,pos.y));
			var feed = null;
			var requestGeometries = [];
			// This uses two methods to determine wether a clickposition is on a geometry
			// - Points are represented as icons, so we check if the click is on an icon
			// - Polygons don't have a dom Element when not using Rapheljs, so we go ask postgis
			// after that's finished the results are merged and displayed in a box
			var pointGeometries = {};	
			var g,h,nodes = null;
			$("*",self._popup).remove();
			var $tabs = $("<ul></ul>");
			for (var i in self._feeds){
				feed = self._feeds[i] ;
				if(!feed.display){ continue; }
				requestGeometries = [];

				for(var j = 0; j < feed.geomArray.count(); j++){
					g = feed.geomArray.get(j);
					h = feed.highlightArray[j];	
					nodes = h.getNodes();
					if(g.geomType == geomType.point){
						// we only add one point per highlight so we can assume there's only one node
						if(!nodes[0]){ continue;}
						var rect = nodes[0].getBoundingClientRect(); 
						if(e.clientX >= rect.left && e.clientX <= rect.right &&
						   e.clientY >= rect.top  && e.clientY <= rect.bottom){
							// we just need the keys to exist
							// theywill be merged with the ones coming from the
							// server
							pointGeometries[j] = true;
						}
						
					}else{
						requestGeometries.push(g.toText());
					}
				}
				var req = new Mapbender.Ajax.Request({
				url: "../php/intersection.php",
				method: "intersect",
				parameters: {
					clickPoint:	clickPoint.toText(),
					geometries: requestGeometries
					},
				callback: (function(geomArray,pointGeometries){ return function(result, success, message){
					if(!success){
						return;
					}
					// this is basically an onclick handler, !intersects means
					// the click didn't happen on the polygon
					$.extend(result.geometries,pointGeometries);
					if(!result.geometries || result.geometries.length <1){
						return;
					}

					
					// this iterates over an object where the keys are _not_ the incremential
					// basically a sparse array. therefore It cannot be used to count the entries in the object
					// this is why j is used
					for(i in result.geometries){
						var g = geomArray.get(i);
						title = g.e.getElementValueByName("title");
						name  = g.e.getElementValueByName("name");
						if(typeof(name) == "string"){
							title = name != "false" ? name : title;
							if (icon == "false"){
								g.e.setElement("Mapbender:icon","../img/marker/red.png");
							}
						}else{
							//sane browsers go here
							title = name != false ? name : title;
							if (icon === false){
								g.e.setElement("Mapbender:icon","../img/marker/red.png");
							}
						}
						description = g.e.getElementValueByName("description");
						$tabs.append('<li><a href="#rsspopup_'+ i +'">'+ title + '</a></li>');
						self._popup.append('<div id="rsspopup_'+ i +'"><h1>'+ title +'</h1><p>'+ description +'</p></h1>');
					
						if($tabs.children().size() > 1){
						}
						self._popup.dialog('open');
					}
			

					if($tabs.children().size() > 1){	
						var $tabcontainer = $("<div><div>");
						$tabcontainer.append($tabs);
						$tabcontainer.append($('div',self._popup));	
						$tabs.css("display","none");
						self._popup.append($tabcontainer);
						$tabcontainer.tabs();
						// -1 because we need it zero based later
						var tabcount = $tabcontainer.find(".ui-tabs-panel").size() -1;
						$tabcontainer.find(".ui-tabs-panel").each(function(i){

							var  $navbar = $("<div></div>");
							$(this).append($navbar);
							// add to first panel
							if(i == 0 ){
								var next = i+1;
								$navbar.append('<a style="float:right;" href="#" class="next-tab" rel="'+next+'">mehr</a>');
							}
							// add to all except the first panel
							if(i > 0 ){
								var prev = i-1;
								$navbar.append('<a href="#" class="prev-tab" rel="'+prev+'">zurück</a>');
							}
							// add to all except first and last panel
							if( tabcount  > i && i > 0  ){
								var next = i+1;
								$navbar.append('<a style="float:right;" href="#" class="next-tab" rel="'+next+'">vor</a>');
							}
						});
						$tabcontainer.find(".next-tab, .prev-tab").click(function(){
							$tabcontainer.tabs("select",$(this).attr("rel"));
							return false;
						});


					}
				

	

				};})(feed.geomArray, pointGeometries)
				});
				req.send();
				requestGeometries = [];
				pointGeometries = {};
			}
		});
		self.element.bind('georss:loaded',function(event,obj){
			if(o.autoOpen){
				self._display(obj);
			}
		});	
		self.element.bind('georss:error',function(event,message){
			alert(message);
		});
		
	},
	_init: function(){
		var self = this, o = this.options;
		this._popup = $('<div></div>').dialog({autoOpen : false, height: 500, width: 500});
		if(o.url){
			self._load(o.url);
		}
	},

	_load : function(url){
		var self = this, o = this.options;
		var epsg = $(self.element).mapbender().epsg;
		epsg = epsg.split(":")[1];
		if(self._feeds[o.url]){
			//not adding feed twice
			return;
		}
		$.ajax({ url: self._endpointURL,
			data: {url: o.url, targetEPSG: epsg},
			type: 'POST',
			dataType: "json",
			success : function(data,textStatus,xhr){
				if(!data){
					self.element.trigger('georss:error',"request returned no data");
				}
				else if(data.errorMessage){
					self.element.trigger('georss:error',data.errorMessage);
				}else{
					self._feeds[o.url] = {type:"geojson",data:data,url:o.url,display: o.autoDisplay};
					self.element.trigger('georss:loaded',{type:"geojson",data:data,url:o.url,display: o.autoDisplay});
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				self.element.trigger('georss:error',"Problem talking to server: " + errorThrown);
			}
		});

	},
	show : function(url){
		this._feeds[url].display = true;
		var $map = $(this.element).mapbender();
		var extent = $map.getExtentInfos();
		$map.calculateExtent(extent);
		$map.setMapRequest();
		$(this.element).mapbender().setMapRequest();
	},

	hide : function(url){
		this._feeds[url].display = false;
		var $map = $(this.element).mapbender();
		var extent = $map.getExtentInfos();
		$map.calculateExtent(extent);
		$map.setMapRequest();
	},
	
	remove: function(url){
		// nrgh there are still references to the highlightobjects somewhere!
		for(var i in this._feeds[url].highlightArray){
			this._feeds[url].highlightArray[i].clean();
		}
		delete(this._feeds[url]);
		var $map = $(this.element).mapbender();
		var extent = $map.getExtentInfos();
		$map.calculateExtent(extent);
		$map.setMapRequest();
	},

	_display : function(mapitem){
		var self = this, o = this.options;
		// getting the mapitem from the events bre
		var mapitem = self._feeds[mapitem.url];
		var geojson = mapitem.data;
		if(typeof(Mapbender) != "undefined"){
			var $map = $(self.element).mapbender();
			var markers = [];
			var title = "";

			if(geojson.features){
				// we always transform _from_ 4326 geoRSS and KML use this as their default
//				var projSrc = new Proj4js.Proj('EPSG:4326');
//				var projDest = new Proj4js.Proj($map.epsg);
				var markeroptions = {width: "19px", height: "34px"};
				var g = null;
				
				mapitem.geomArray = new GeometryArray();
				mapitem.highlightArray = [];
				mapitem.geomArray.importGeoJSON(geojson);
				for(var i =0; i < mapitem.geomArray.count(); i++){
					var h = new Highlight([self.element.attr('id')], "mapframe1_" + parseInt(Math.random()*100000,10),{
						"position":"absolute",
						"top": "0px",
						"left": "0px",
						"z-index": "80" },1);
					g = mapitem.geomArray.get(i);
					icon = g.e.getElementValueByName("Mapbender:icon");
					title = g.e.getElementValueByName("title");
					name = g.e.getElementValueByName("name");
					
					if(name != "false" && name !== false){
						title = name; // use for tooltip
					}			
					if(icon == "false" || icon === false){
						g.e.setElement("Mapbender:iconOffsetX", -10);
						g.e.setElement("Mapbender:iconOffsetY", -34);
						g.e.setElement("Mapbender:icon","../img/marker/red.png");
					}

					h.add(g);
					mapitem.highlightArray.push(h);
					title = "";
					name = "";
				}
				if(mapitem.display){
					for(var j in mapitem.highlightArray){
						mapitem.highlightArray[j].paint();
					}
				}else{
					for(var j in mapitem.highlightArray){
						mapitem.highlightArray[j].hide();
					}
				}
				self.element.mapbender().events.afterMapRequest.register(function () {
					if(mapitem.display){ 
						for(var i in mapitem.highlightArray){
							mapitem.highlightArray[i].paint();
						}
					}else{
						for(var i in mapitem.highlightArray){
							mapitem.highlightArray[i].hide();
						}
					}
				});

			}
		}

	}


};




var displayGeoRSS =  $.extend({},displayFeatures,{
	_endpointURL: "../php/geoRSSToGeoJSON.php",
	_eventNamespace : "georss"

});
$.widget('ui.georss',displayGeoRSS);


var displayKML = $.extend({},displayFeatures, {
	_endpointURL: "../php/kmlToGeoJSON.php",
	_eventNamespace : "kml"
});
$.widget('ui.kml',displayKML);


var displayGeoJSON = $.extend({},displayFeatures,{
	_endpointURL: "",
	_load: function(){
		var self = this, o = this.options;
		$.ajax({ url: o.url,
			type: "GET",
			dataType: "json",
			success : function(data,textStatus,xhr){
				self._display(data);
			}
		});

	}
});
$.widget('ui.geojson',displayKML);
