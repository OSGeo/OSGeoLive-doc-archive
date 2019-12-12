var ResultGeometryListController = function(options){

	var me = this;
	
	// The WFSConfId this List is currently configured with
	var WFSConf = null;

	// our model
	var model = null;

	// a jquery object referencing the datatable we're using
	this.datatable = null;
	
	// a jquery object referencing the underlying html table we're using
	var table = table;//table;$("#"+opid).append("<table></table>");;

	// the columns that should be displayed
	this.columns = [];
	
	// the detail columns that should be displayed
	this.detailColumns = [];

	// array of buttondef objects
	this.rowbuttons = [];

	this.options = options;


	/*
	 * Method: addFeature
	 * Description: adds a Feature to the Model
	 * Parameters:
	 * feature - a geoJSON featuree
	*/
	this.addFeature = function(feature) {
		if (this.wfsConfId === null){
			// or throw an exception?
			return false;
		}
		var result = me.model.addFeature(feature);
	};

	/*
	 * Method: addFeatureCollection
	 * Description: adds a FeatureCollection to the Model
	 * Parameters:
	 * geoJSON - a geoJSON featureCollection
	*/
	this.addFeatureCollection = function(geoJSON){
		var result = me.model.addFeatureCollection(geoJSON);
	};

	/*
	 * Method: addFeatureCollection_callback
	 * Description: The callback received from the Model upon successfully adding a Feature
	 * Parameters:
	 * featureEntryCollection - an array of (index,featureCollection) tuples
	*/
	var addFeatureCollection_callback = function(featureEntryCollection){
	// featureEntryCollection is an array of {index: <index>, feature: <feature>}
	// where <index> is the index in the model, and feature a featuireCollection
		// only add feature.properties enabled by wfsConf
		for (featureIndex in featureEntryCollection.featureCollection){
			//create array from featureEntries.feature here, using  wfsconf
			modelIndex = featureEntryCollection.featureCollection[featureIndex].index;
			var rowData = [];
			for (ci in me.columns){
				if(me.model.getFeatureProperty(modelIndex,me.columns[ci].name) === false) {
					var propertyValue = "";
				}
				else {
					var propertyValue = me.model.getFeatureProperty(modelIndex,me.columns[ci].name);
				}
				var cellContent = '<span class="wfsFproperty">' + propertyValue + '</span>';
				rowData.push(cellContent);
			}

			for(bId in me.rowbuttons){
				var callback  = me.rowbuttons[bId].callback;
				var specialHtml = me.rowbuttons[bId].specialHtml ? me.rowbuttons[bId].specialHtml : "";
				var buttonClass = "rowbutton_" +  me.rowbuttons[bId].title.replace(' ','_');
				rowData.push('<input type="button" class="'+ buttonClass +'" value="'+ me.rowbuttons[bId].title +'"  />' + specialHtml);
			}

			// fnAddData returns an array of indices
			var index = me.datatable.fnAddData(rowData);
			
			// this is ok because we only added a single row
			var rowNode = me.datatable.fnGetNodes(index[0]);
			$(rowNode).data("modelindex",modelIndex);

		}
	};
	
	//deleting
	// from where is this called ?
	this.deleteFeature = function(DOMTableRow){
		var modelIndex = $.data(DOMTableRow,"modelIndex");
		me.model.deleteFeature(modelIndex);
	 };

	//called to config deletion was successfull
	this.deleteFeature_callback = function(featureEntry){
	};

	//updating

	// clear = reinitialize + setting WFSConf to same value again ?
	//misc
	this.setWFSconf = function(WFSConf) {
		if(WFSConf != this.WFSConf){
			this.WFSConf = WFSConf;
			this.reinitialize();
			this.events.wfsConfSet.trigger({
				wfsConf : WFSConf
			});
		}
	};

	var getColumns  = function(WfsConf){
		if (!WfsConf) { return []; }
		var currentWfsConf = WfsConf;
//		var labelArray = [];
		var labelArray = [null,null,null,null,null,null,null,null,
		                  null,null,null,null,null,null,null,null,
		                  null,null,null,null];
		var hasLabeledColumn = false;
		for (var j = 0 ; j < currentWfsConf.element.length ; j++) {
			if(currentWfsConf.element[j].f_show == 1 && currentWfsConf.element[j].f_label!==''){
				var labelPos = currentWfsConf.element[j].f_respos;
				labelArray[labelPos] = {
					name : currentWfsConf.element[j].element_name,
					label : currentWfsConf.element[j].f_label
				};
				hasLabeledColumn = true;
			}
		}
		resultArray = [];
		if (!hasLabeledColumn) {
			alert("The WFS configuration must have at least one element which has a label and a 'show' flag.");
		}
		for (index in labelArray){
			if (labelArray[index]  !== undefined && labelArray[index]  !== null){
				resultArray.push(labelArray[index]);
			}
		}
		return resultArray;
	};
	
	var getDetailColumns  = function(WfsConf){
		if (!WfsConf) { return []; }
		var currentWfsConf = WfsConf;
//		var labelArray = [];
		var labelArray = [null,null,null,null,null,null,null,null,
		                  null,null,null,null,null,null,null,null,
		                  null,null,null,null];
		for (var j = 0 ; j < currentWfsConf.element.length ; j++) {
			if(currentWfsConf.element[j].f_show_detail == 1) {
				var labelPos = currentWfsConf.element[j].f_detailpos;
				labelArray[labelPos] = {
					name : currentWfsConf.element[j].element_name,
					label : currentWfsConf.element[j].f_label,
					html : currentWfsConf.element[j].f_form_element_html
				};
			}
		}
		resultArray = [];
		for (index in labelArray){
			if (labelArray[index]  !== undefined && labelArray[index]  !== null){
				resultArray.push(labelArray[index]);
			}
		}
		return resultArray;
	};
	
	this.clear = function () {
		me.model.deleteFeatureCollection();	
	};

	var clear_callback = function(){
		if (me.datatable) {
			me.datatable.fnClearTable();
//			datatable.remove();
		}
	};


	/*
	 * Method: addRowButton
	 * Description: adds a Button to each row
	 * Parameters:
	 * buttondef: {Object} an object with  properties: "title" and  "callback", a function that gets the  feature that corresponds to the popup as it's argument
  */
	this.addRowButton = function(buttondef){
		this.rowbuttons.push(buttondef);
	}

	this.reinitialize = function(){
		
		//create theads, and variable "labels" according to wfsConf
		me.columns = getColumns(this.WFSConf);
		
		//detail columns
		me.detailColumns = getDetailColumns(this.WFSConf);
		
		//var buttonrow = $('#'+me.options.id +"buttonrow")
		$('#' + me.options.id +"_table_wrapper").remove()

		if(me.table != null){me.table.remove() }
		me.table = $('<table class="display" id="'+ me.options.id +'_table"><thead><tr></tr></thead><tbody></tbody></table>');
		$('#' + me.options.id).prepend(me.table);

		//apply columns to table
		//$("thead tr th",table).remove();
		theads = "";
		for(i in me.columns){
			theads += "<th>"+ me.columns[i].label +"</th>";
			
		}

    
		for (i in this.rowbuttons){ 
			theads += "<th></th>";
		}

		$("thead tr",me.table).append(theads);

		//
		// This block should optionally add the required elements in a table
		//var heads = $("<thead></thead>");
		//for (column in columns)
		//{
		//	heads.append($("<th>"+column+"</th>"));
		//}
		//table.append(heads);
		//table.append($("<tbody></tbody>"));
		if (me.datatable != null){ me.datatable.remove();}

		Mapbender.languageId;
		me.datatable = me.table.dataTable({  
			// see http://www.sprymedia.co.uk/dataTables/example_dom.html
			"sDom": '<i<"resultdatatable"t>p> ',
			"bJQueryUI": true,
			"oLanguage": {
				"sUrl":"../extensions/dataTables-1.5/lang/"+Mapbender.languageId +".txt"
			}	
		});
		$(".resultdatatable").css("clear","both");
		$(".resultdatatable table").css("width","95%");



	};
	

	// initialize new
	me.model = new ResultGeometryListModel();
	me.model.events.added.register(addFeatureCollection_callback);
	me.model.events.deleted.register();
	me.model.events.updated.register();
	me.model.events.cleared.register(clear_callback);

	
};
