var allSelected = false;

var ResultListSelectAll = function(data) { 
		
        var tr_rows = data.table.fnGetNodes();
        for(trindex in tr_rows) {
			if(allSelected){
				$(tr_rows[trindex]).removeClass('row_selected');
			}
			else{
				$(tr_rows[trindex]).addClass('row_selected');
			}
        }
		allSelected = allSelected ? false: true;
};

Mapbender.modules[options.target].addGlobalButton({"title": "alle/keine auswählen", "classes": "selectAll", "callback": ResultListSelectAll});
