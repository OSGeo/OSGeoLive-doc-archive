function WfsFilter () {
	
	this.operators = [
		{
			"operator":"==", 
			"wfsOpenTag":"PropertyIsEqualTo", 
			"wfsCloseTag":"PropertyIsEqualTo"
		},
		{
			"operator":">=", 
			"wfsOpenTag":"PropertyIsGreaterThanOrEqualTo",
			"wfsCloseTag":"PropertyIsGreaterThanOrEqualTo"
		},
		{
			"operator":"<=", 
			"wfsOpenTag":"PropertyIsLessThanOrEqualTo",
			"wfsCloseTag":"PropertyIsLessThanOrEqualTo"
		},
		{
			"operator":">>", 
			"wfsOpenTag":"PropertyIsGreaterThan",
			"wfsCloseTag":"PropertyIsGreaterThan"
		},
		{
			"operator":"<<", 
			"wfsOpenTag":"PropertyIsLessThan",
			"wfsCloseTag":"PropertyIsLessThan"
		},
		{
			"operator":"<>", 
			"wfsOpenTag":"PropertyIsNotEqualTo",
			"wfsCloseTag":"PropertyIsNotEqualTo"
		},
		{
			"operator":"LIKE", 
			"wfsOpenTag":"PropertyIsLike wildCard='*' singleChar='.' escape='!'",
			"wfsCloseTag":"PropertyIsLike"
		}
	];
	
	var conditionToString = function (condition, operator){
		var splitParam = condition.split(operator);
		var columnName = trim(splitParam[0]);
		var columnValue = trim(splitParam[1]);
	
/*
		if (operator == 'LIKE') {
			columnValue = "*"+ columnValue +"*";
		}
*/		
		for (var i = 0 ; i < that.operators.length ; i++) {
			if (that.operators[i].operator == operator){
	
				// add condition: Property
				var condString = '<'+that.operators[i].wfsOpenTag+'>' +
					'<PropertyName>'+columnName+'</PropertyName>' +
					'<Literal>'+columnValue+'</Literal>' +
					'</'+that.operators[i].wfsCloseTag+'>';
			
/*
				// add condition: Property is not null
					'<ogc:Not><ogc:PropertyIsNull>' +
					'<ogc:PropertyName>'+columnName+'</ogc:PropertyName>' +
					'</ogc:PropertyIsNull></ogc:Not>';
*/
				return condString;
	    	}
		}
		return "";
	};

	var getOperatorFromCondition = function (aCondition) {
		for (var j = 0; j < that.operators.length; j++) {
			if (aCondition.match(that.operators[j].operator)) {
				return that.operators[j].operator;
			}
		}
		return false;
	};
	
	/**
	 * parse the filter from the HTML form, 
	 * 
	 * @param {String} filter like "[usertype]<>3 AND [firstname]==Emil"
	 */ 
	this.parse = function (filter) {
		if (filter !== '') {
	
			filter = filter.replace(/\[/g,"");
			filter = filter.replace(/\]/g,"");
			var condArray = filter.split(' AND ');
			var wfsCond = [];
			for (var i = 0 ; i < condArray.length ; i++) {
				var currentOperator = getOperatorFromCondition(condArray[i]);
				if (!currentOperator) {
					return false;
				}
				wfsCond.push(conditionToString(condArray[i], currentOperator));
			}
			conditionArray = conditionArray.concat(wfsCond);
		}
		return true;
	};
	
	this.addSpatial = function (spatialRequestGeom, geometryColumn, filteroption, srs, target) {
		if(typeof(spatialRequestGeom) != "undefined"){
			var spatialRequestFilter = "";
			if(spatialRequestGeom.geomType == geomType.polygon){
				spatialRequestFilter += "<" + filteroption + "><ogc:PropertyName>" +
					geometryColumn + "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\">" + 
					"<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";
				for(var k=0; k<spatialRequestGeom.count(); k++){
					if (k > 0) {
						spatialRequestFilter += " ";
					}
					spatialRequestFilter += spatialRequestGeom.get(k).x+","+spatialRequestGeom.get(k).y;
				}
				spatialRequestFilter += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>";
				spatialRequestFilter += "</gml:Polygon></" + filteroption + ">";
			}	
			else if(spatialRequestGeom.geomType == geomType.line){
				var rectangle = [];
				rectangle = spatialRequestGeom.getBBox();
				
				spatialRequestFilter += "<" + filteroption + "><ogc:PropertyName>" +
					geometryColumn + "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\">" +
					"<gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>" +
					rectangle[0].x+","+rectangle[0].y + " " +
					rectangle[0].x+","+rectangle[1].y + " " +	
					rectangle[1].x+","+rectangle[1].y + " " +
					rectangle[1].x+","+rectangle[0].y + " " + 
					rectangle[0].x+","+rectangle[0].y +
					"</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs>" + 
					"</gml:Polygon></" + filteroption + ">";
			}
			else if(spatialRequestGeom.geomType == geomType.point){
				var tmp = spatialRequestGeom.get(0);
				var mapPos = makeRealWorld2mapPos(target,tmp.x, tmp.y);
				var buffer = mb_wfs_tolerance/2;
				var realWorld1 = makeClickPos2RealWorldPos(target,mapPos[0]-buffer,mapPos[1]-buffer);
				var realWorld2 = makeClickPos2RealWorldPos(target,mapPos[0]+buffer,mapPos[1]-buffer);
				var realWorld3 = makeClickPos2RealWorldPos(target,mapPos[0]+buffer,mapPos[1]+buffer);
				var realWorld4 = makeClickPos2RealWorldPos(target,mapPos[0]-buffer,mapPos[1]+buffer);
				spatialRequestFilter += "<Intersects><ogc:PropertyName>";
				spatialRequestFilter += geometryColumn;
				spatialRequestFilter += "</ogc:PropertyName><gml:Polygon srsName=\""+srs+"\"><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>";	
				spatialRequestFilter += realWorld1[0] + "," + realWorld1[1] + " " + realWorld2[0] + "," + realWorld2[1] +  " ";
				spatialRequestFilter += realWorld3[0] + "," + realWorld3[1] + " " + realWorld4[0] + "," + realWorld4[1] + " " + realWorld1[0] + "," + realWorld1[1]; 
				spatialRequestFilter += "</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon></Intersects>";
			}
/*
			spatialRequestFilter += "<ogc:Not><ogc:PropertyIsNull>";
            spatialRequestFilter += "<ogc:PropertyName>" + geometryColumn + "</ogc:PropertyName>";
       		spatialRequestFilter += "</ogc:PropertyIsNull></ogc:Not>";
*/
			conditionArray.push(spatialRequestFilter);
		}
	};
	
	this.addPreConfigured = function (propName, propValueArray, toUpper, operator) {
		var orConditions = "";
		for (var j=0; j < propValueArray.length; j++) {
			if (operator == 'greater_than' || operator == 'less_than' || operator == 'equal') {
				if(propValueArray[j]!=''){
					var tag;
					if (operator == 'greater_than') {
						tag = "PropertyIsGreaterThan";
					}
					else if (operator == 'less_than') {
						tag = "PropertyIsLessThan";
					}
					else if (operator == 'equal') {
						tag = "PropertyIsEqualTo";
					}
					
					orConditions += "<ogc:" + tag + ">";
					orConditions += "<ogc:PropertyName>" + propName + "</ogc:PropertyName>";
					orConditions += "<ogc:Literal>";
	
					if (toUpper == 1) {
						propValueArray[j] = propValueArray[j].toUpperCase();
					}
					
					orConditions += propValueArray[j] + "</ogc:Literal></ogc:" + tag + ">";
				}
			}
			else {
				var leftSide = "";
				var rightSide = "*";
				
				if (operator != 'rightside') {
					leftSide = "*";
				}
				orConditions += "<ogc:PropertyIsLike wildCard='*' singleChar='.' escape='!'>";
				orConditions += "<ogc:PropertyName>" + propName + "</ogc:PropertyName>";
				orConditions += "<ogc:Literal>" + leftSide;
				if (toUpper == 1){
					propValueArray[j] = propValueArray[j].toUpperCase();
				}
				orConditions += propValueArray[j] + rightSide;
				orConditions += "</ogc:Literal></ogc:PropertyIsLike>";
			}
/*
			orConditions += "<ogc:Not><ogc:PropertyIsNull>";
            orConditions += "<ogc:PropertyName>" + propName + "</ogc:PropertyName>";
       		orConditions += "</ogc:PropertyIsNull></ogc:Not>";
*/
		}
		if(propValueArray.length > 1){
			orConditions = "<Or>" + orConditions + "</Or>";
		}
		this.add(orConditions);
	};

	this.add = function (aFilterString) {
		conditionArray.push(aFilterString);
	};
	
	this.toString = function () {
		var str = "";
		str += "<ogc:Filter xmlns:ogc='http://www.opengis.net/ogc' ";
		str += "xmlns:gml='http://www.opengis.net/gml'>";
		if (conditionArray.length > 1) {
			str += "<And>" + conditionArray.join("") + "</And>";	
		}
		else {
			str += conditionArray.join("");	
		}
		str += "</ogc:Filter>";
		return str;
	};
	
	var conditionArray = [];
	var that = this;
}

