var JQUERY_PATH = "../extensions/jquery-ui-1.8.1-custom/";

$.widget("mb.wfsGazetteer", {
	options: {
		geometry : null
	},

	_create: function () {
		var that = this;
		if (typeof this.options.wfsConf !== "object" && 
			this.options.wfsConf !== null
		) {
			this.destroy();
			return;
		}
		var c = this.options.wfsConf;
		for (var i = c.element.length - 1; i >= 0; i--) {
			var el = c.element[i];
			if (parseInt(el.f_search, 10) !== 1) {
				continue;
			}
			var id = $(this.element).attr('id') + "_" + el.element_name;
			
			var $formElement;
			if (el.f_form_element_html.match(/\<select/)) {
				$formElement = $(el.f_form_element_html).attr("id",  id);
			}
			else if (el.f_form_element_html.match(/checkbox/)) {
				$formElement = $(el.f_form_element_html).attr("id",  id);
			}
			else if (el.f_form_element_html.match(/datepicker/)) {
				$formElement = $("<input />", {
					type: "text",
					id: id,
					readonly: "readonly"
				}).datepicker({
					showOn: "button",
					buttonImage: JQUERY_PATH + 
						"development-bundle/demos/datepicker/images/calendar.gif",
					buttonImageOnly: true
				});
			}
			else {
				$formElement = $("<input />", {
					type: "text",
					id: id
				});
			}

			var $label = $("<label />", {
				"for": id,
				"css": {
					"display":"block"
				}
			}).text(el.f_label).append($formElement);

			$(".mb-ui-wfsGazetteer-form", this.element).prepend($label);
			$(".mb-ui-wfsGazetteer-status", this.element).show();
		

		}

		$(this.element).submit(function(e){
			// we never want the form to actually be submitted
			e.preventDefault();
			// get filters from field, and search	
			that.search({});
			return false;
		});
		
		$(this.element).bind('reset',function(e){
			that.clear.call(that,e);
		});
	},
	/*
 	*
 	* @param filter an object where each key specifies one field to filter by using it's value
 	*
 	*/
	search : function(filter) {
		$('.error',this.element).css('border','');
		$('.error',this.element).removeClass('error');
		this._status('Searching');
		
		if(!filter){ return; }
		var OGCFilter = null;
		for(var i = 0; i < filter.length; i++){
		}
			
		var element = null;
		var value = null;
		var srs = this.options.wfsConf.featuretype_srs;
		var ft = this.options.wfsConf.featuretype_name;
		var id = ""; 
		var OGCFilterExpression = "";
		var propertyConditions = [];
		var geometryConditions  = [];
		for(var i = 0; i < this.options.wfsConf.element.length; i++){
			el = this.options.wfsConf.element[i];
			id = $(this.element).attr('id') + "_" + el.element_name;

			var qname = ft.split(':');	
			nsPrefix = qname.length == 2 ? qname[0]+":" : "";
			elementQname = nsPrefix + el.element_name;
			value = $('#'+id).val() || "";
			
			// check if field is mandatory
			if(el.f_min_input > value.length){
				$('#'+id).addClass('error');
				$(".error",this.element).css('border','2px inset red');
				this._status('Errors in search');
				return;
			}
			if(value.length === 0){ continue;}

			value = el.f_toupper == 1 ? value.toUpperCase() : value;
		
			switch(el.f_operator){
				
				case 'greater_than':
					OGCFilterExpression  = "<ogc:PropertyIsGreaterThan>";
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname +"</ogc:PropertyName>";
					OGCFilterExpression += "<ogc:Literal>"+value+"</ogc:Literal>";	
					OGCFilterExpression += "</ogc:PropertyIsGreaterThan>";
				break;
				
				case 'greater_equal_than':
					OGCFilterExpression  = "<ogc:PropertyIsGreaterThanOrEqualTo>";
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname +"</ogc:PropertyName>";
					OGCFilterExpression += "<ogc:Literal>"+value+"</ogc:Literal>";	
					OGCFilterExpression += "</ogc:PropertyIsGreaterThanOrEqualTo>";
				break;
				
				case 'less_than':
					OGCFilterExpression  = "<ogc:PropertyIsLessThan>";
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname +"</ogc:PropertyName>";
					OGCFilterExpression += "<ogc:Literal>"+value+"</ogc:Literal>";	
					OGCFilterExpression += "</ogc:PropertyIsLessThan>";
				break;
				
				case 'less_equal_than':
					OGCFilterExpression  = "<ogc:PropertyIsLessThan>";
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname +"</ogc:PropertyName>";
					OGCFilterExpression += "<ogc:Literal>"+value+"</ogc:Literal>";	
					OGCFilterExpression += "</ogc:PropertyIsLessThan>";
				break;
				
				case 'equal':
					OGCFilterExpression  = "<ogc:PropertyIsEqualTo>";
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname +"</ogc:PropertyName>";
					OGCFilterExpression += "<ogc:Literal>"+value+"</ogc:Literal>";	
					OGCFilterExpression += "</ogc:PropertyIsEqualTo>";
				break;
			
				case 'leftside':
					OGCFilterExpression  = "<ogc:PropertyIsLike wildCard='*' singleChar='.' escape='!'>";
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname +"</ogc:PropertyName>";
					OGCFilterExpression += "<ogc:Literal>*"+value+"</ogc:Literal>";	
					OGCFilterExpression += "</ogc:PropertyIsLike>";
				break;
			
				case 'rightside':
					OGCFilterExpression  = "<ogc:PropertyIsLike wildCard='*' singleChar='.' escape='!'>";
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname +"</ogc:PropertyName>";
					OGCFilterExpression += "<ogc:Literal>"+value+"*</ogc:Literal>";	
					OGCFilterExpression += "</ogc:PropertyIsLike>";
				break;
				
				case 'bothside':
				default:
					OGCFilterExpression  = "<ogc:PropertyIsLike wildCard='*' singleChar='.' escape='!'>";
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname +"</ogc:PropertyName>";
					OGCFilterExpression += "<ogc:Literal>*"+value+"*</ogc:Literal>";	
					OGCFilterExpression += "</ogc:PropertyIsLike>";
				break;
				
				
			}

			propertyConditions.push(OGCFilterExpression);	
		}
		var OGCORFilter = propertyConditions.length == 1 ? propertyConditions[0] : "<And>"+ propertyConditions.join('') +"</And>"; 
		geometryConditions.push(OGCORFilter);


		

		for(var i = 0; i < this.options.wfsConf.element.length; i++){
			el = this.options.wfsConf.element[i];
			if(el.f_geom != 1 || this.options.geometry == null){ continue; }
			var geometry = this.options.geometry;
			var qname = ft.split(':');	
			nsPrefix = qname.length == 2 ? qname[0]+":" : "";
			elementQname = nsPrefix  +el.element_name;
			OGCFilterExpression = "";
			switch(geometry.geomType){

				case geomType.point:
					//var mapPos = makeRealWorld2mapPos("mapframe1",geometry.get(0).x, geometry.get(0).y);
					//mapPos = makeClickPos2RealWorldPos("mapframe1", geometry.get(0).x, geometry.get(0).y);
					
					var mapPos = [geometry.get(0).x,geometry.get(0).y];
					var buffer = 4;
					var mapPosXAddPix = mapPos[0] + buffer; 
					var mapPosYAddPix = mapPos[1] +buffer;
					var mapPosXRemovePix = mapPos[0] - buffer;
					var mapPosYRemovePix = mapPos[1] - buffer;
					var realWorld1 = makeClickPos2RealWorldPos("mapframe1",mapPosXRemovePix,mapPosYRemovePix);
					var realWorld2 = makeClickPos2RealWorldPos("mapframe1",mapPosXAddPix,mapPosYRemovePix);
					var realWorld3 = makeClickPos2RealWorldPos("mapframe1",mapPosXAddPix,mapPosYAddPix);
					var realWorld4 = makeClickPos2RealWorldPos("mapframe1",mapPosXRemovePix,mapPosYAddPix);
					OGCFilterExpression = "<Intersects><ogc:PropertyName>";
					OGCFilterExpression +=  elementQname;
					OGCFilterExpression += "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\"><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";	
					OGCFilterExpression += realWorld1[0] + "," + realWorld1[1] + " " + realWorld2[0] + "," + realWorld2[1] +  " ";
					OGCFilterExpression += realWorld3[0] + "," + realWorld3[1] + " " + realWorld4[0] + "," + realWorld4[1] + " " + realWorld1[0] + "," + realWorld1[1]; 
					OGCFilterExpression += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon></Intersects>";
				break;

				case geomType.line:

				break;

				case geomType.polygon:
					OGCFilterExpression += "<ogc:PropertyName>"+ elementQname  +"</ogc:PropertyName>";
					OGCFilterExpression += '<gml:Polygon srsName="'+srs + '"><gml:outerBoundaryIs><gml:LinearRing>';
					OGCFilterExpression += '<gml:coordinates>';
					for(var k=0; k<geometry.count(); k++){
						if(k>0)	{ OGCFilterExpression += " ";}
						OGCFilterExpression += geometry.get(k).x+","+geometry.get(k).y;
					}
					OGCFilterExpression += '</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon>';
					OGCFilterExpression ="<Intersects>" + OGCFilterExpression +"</Intersects>";
				break;
			}
			geometryConditions.push(OGCFilterExpression);
		}

		var OGCANDFilter = geometryConditions.length == 1 ? geometryConditions[0] : "<And>"+ geometryConditions.join('') +"</And>"; 
		OGCFilter = "<ogc:Filter>" + OGCANDFilter  +"</ogc:Filter>";
		
		var params = {
			command : "getSearchResults", 
			wfs_conf_id : this.options.wfsConf.wfs_conf_id, //?
			typename : this.options.wfsConf.featuretype_name,
			frame : "", 
			filter : OGCFilter, 
			backlink : ""
		};
		var that = this;
		mb_ajax_get("../php/mod_wfs_gazetteer_server.php", params, function(json,status){
			that._status('');
			that._trigger('receivefeaturecollection',null,{
				featureCollection:json,
				wfsConf: that.options.wfsConf});
			$(that.element).trigger('receivefeaturecollection',null,{
				featureCollection:json,
				wfsConf: that.options.wfsConf});
		});


	},
	_setOption: function(key,value){
		switch(key){
			case "geometry":
				var geomArray = new GeometryArray();
				geomArray.importGeoJSON(value);
				var geometry = geomArray.get(0).get(0);
				this.options.geometry = geometry;
				this._status('Spatial filter is set.');
			break;

		}
	},
	receivefeaturecollection: function(event,ui){
	},
	_status: function(message){
		$('.mb-ui-wfsGazetteer-status',$(this.element).parent()).html(message);
	},
	clear: function(){
		$('.error',this.element).css('border','');
		$('.error',this.element).removeClass('error');
		this._status('');
		this.options.geometry = null;
		collection = new GeometryArray();
        collection.addMember(Mapbender.geometryType.point);
        collection.get(-1).addGeometry();
        collection.getGeometry(-1, -1).addPoint(new Mapbender.Point(0,0));


	},
	destroy: function () {
	}
});
