/**
 * Package: mb_metadata_wmc_select
 *
 * Description:
 *
 * Files:
 *
 * SQL:
 * 
 * Help:
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var $metadataSelect = $(this);
$metadataSelect.prepend("<img src='../img/indicator_wheel.gif'>");

var MetadataSelectApi = function (o) {
	var table = null;
	var that = this;

	var fnGetSelected = function (oTableLocal){
		var aReturn = [];
		var aTrs = oTableLocal.fnGetNodes();
		
		for ( var i=0 ; i<aTrs.length ; i++ ){
			if ( $(aTrs[i]).hasClass('row_selected') ){
				aReturn.push( aTrs[i] );
			}
		}
		return aReturn;
	};
	
	this.getData = function () {
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_wmc_server.php",
			method: "getWmc",
			parameters: {
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				$metadataSelect.find("img").remove();
				
				var aoColumns = [];
				for (var i in obj.header) {
					if (obj.header[i] === "WMC ID") {
						continue;
					}
					aoColumns.push({"sTitle": obj.header[i]});
				}

				// initialize datatables
				table = $metadataSelect.find("table").dataTable({
					"aoColumns": aoColumns,
					"bJQueryUI": true,
					"bAutoWidth": false
				});
				
				// add rows
				for (var j in obj.data) {
					var data = obj.data[j];
					var wmcId = data[0];
					data.shift();
					var index = table.fnAddData(data);
					var rowNode = table.fnGetNodes(index[0]);
					$(rowNode).data("wmcId", wmcId);
				}
				
				// make rows selectable
				$metadataSelect.find("tbody").click(function (e) {
					$(table.fnSettings().aoData).each(function (){
						$(this.nTr).removeClass('row_selected');
					});
					$(e.target.parentNode).addClass('row_selected');
					var selectedRow = fnGetSelected(table);
					$metadataSelect.fadeOut(function () {
						that.events.selected.trigger({
							wmcId: $(selectedRow).data("wmcId")
						});
						$metadataSelect.show();
					});
				});
				
				$metadataSelect.find(".cancelClickEvent").click (function (e) {
					if(jQuery.browser.msie) {
						e.cancelBubble = true;
					} else {
						e.stopPropagation();
					}
				});
			}
		});
		req.send();
	};
	
	this.events = {
		selected: new Mapbender.Event()
	};
};

$metadataSelect.mapbender(new MetadataSelectApi(options));

$metadataSelect.mapbender("getData");