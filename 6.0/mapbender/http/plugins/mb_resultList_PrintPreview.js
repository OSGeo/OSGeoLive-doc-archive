

var WFSPrintPreview = function(data) { 
	var geoObj = parent.$.parseJSON(data.geoJSON)
	var c = 0;
	var table = "";
	var rows = "";
	var showDetailColumnsArray = [];
	for (var i = 0 ; i < data.WFSConf.element.length ; i++) {
		if(data.WFSConf.element[i].f_show_detail == 1) {
			showDetailColumnsArray.push({
				name : data.WFSConf.element[i].element_name,
				label : data.WFSConf.element[i].f_label,
				position : data.WFSConf.element[i].f_detail_pos
			});
		}
	}
	
	var sortFunction = function (a, b) {
		if(typeof a.position != "number") {		
			return 1;
		}
		if(typeof b.position != "number") {		
			return -1;
		}
		return a.position < b.position;
	}
	
	showDetailColumnsArray.sort(sortFunction);
	
	var pageHtml = ""; 
	for (var j = 0; j < geoObj.features.length; j++){
		var feature = geoObj.features[j];
		var rows = "";
		for (elementName in feature.properties) {
			for (var k = 0 ; k < showDetailColumnsArray.length ; k++) {
				if(showDetailColumnsArray[k].name == elementName) {
					var cells = "<td>"+ showDetailColumnsArray[k].label + "</td>";
					cells += "<td>"+ feature.properties[elementName] + "</td>";
					rows += "<tr>"+cells+"</tr>";	
				}
			}
		}
		pageHtml += "<table><tbody>"+rows+"</tbody></table><br>";
	}	
	
	
/*	var selected = data.selectedRows;
	rows = "";
    for(c in selected){
		var feature = selected[c];
		var name = feature.e.getElementValueByName("firstname")
		rows += "<tr><td>"+ name +"</td></tr>";
	}
*/

	doc = "<html><head>" + options.pageCss + "</head><body>"+ pageHtml +"</body></html>";
	var popup = open("","_blank");
	popup.document.open("text/html");
	popup.document.write(doc);
	popup.document.close();
  };

Mapbender.modules[options.target].addGlobalButton({"title":"Alle Detailinfos drucken", "classes":"buttonMargin", "callback": WFSPrintPreview});
