/**
 * Package: changeEPSG
 *
 * Description:
 * Select a spatial reference system EPSG code. All maps are transformed to 
 * that system.
 * 
 * Files:
 *  - http/javascripts/mod_changeEPSG.php
 *  - http/php/mod_changeEPSG_server.php
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<gui_id>','changeEPSG',
 * > 2,1,'change EPSG, Postgres required, overview is targed for full extent',
 * > 'Change Projection','select','','',432,25,107,24,1,'',
 * > '','select',
 * > 'mod_changeEPSG.php','','overview','','');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<app_id>', 'changeEPSG', 
 * > 'projections',
 * > 'EPSG:4326;Geographic Coordinates,
 * > EPSG:31466;Gauss-Krueger 2,EPSG:31467;Gaus-Krueger 3', '' ,
 * > 'php_var');
 *
 * Help:
 * http://www.mapbender.org/ChangeEpsg
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
<?php
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
include '../include/dyn_php.php';
//generate array
$projections = explode(',',$projections);
$projectionsValue =  array();
$projectionsName = array();
for ($i=0; $i < count($projections); $i++){
	$projectionList = explode(';',$projections[$i]);
	if (count($projectionList) > 1) {
		$projectionsValue[$i] = $projectionList[0];
		$projectionsName[$i] = _mb($projectionList[1]);
	} else {
		$projectionsValue[$i] = $projectionList[0];
		$projectionsName[$i] = $projectionList[0];
	}
}
?>
(function ($) {
	$.fn.ajaxChange = function () {
		var args = arguments;
		return this.each(function () {
			if (this.tagName.toUpperCase() !== "SELECT") {
				return;
			}
			
			var $this = $(this);
	
			if (args.length >= 1 && $.isFunction(args[0])) {
				var f = args[0];
				
				var options = {};
				if (args.length >= 2 && typeof args[1] === "object") {
					options = args[1];
				}
				options = $.extend({
					disable: true,
					undo: true
				}, options);
				$this
					.data("ajaxChangeDisable", options.disable)
					.data("ajaxChangeUndo", options.undo)
					.data("ajaxChangeSelectedIndex", null)
					.change(function () {
						if ($this.data("ajaxChangeDisable") === true) {
							$this.attr("disabled", "disabled");
						}
						f.apply(this, arguments);
					})
					.mousedown(function () {
						if ($this.data("ajaxChangeSelectedIndex") === null
							&& $this.data("ajaxChangeUndo")
						) {
							$this.data("ajaxChangeSelectedIndex", this.selectedIndex);
						}
					});
			}
			// control
			else if (args.length >= 1 && typeof args[0] === "string") {
				var command = args[0];
				switch (command) {
					case "abort":
						if ($this.data("ajaxChangeDisable") === true) {
							$this.removeAttr("disabled");
						}
						if ($this.data("ajaxChangeUndo")) {
							this.selectedIndex = $this.data("ajaxChangeSelectedIndex");
							$this.data("ajaxChangeSelectedIndex", null);
						}
						break;
					case "done":
						if ($this.data("ajaxChangeDisable") === true) {
							$this.removeAttr("disabled");
						}
						if ($this.data("ajaxChangeUndo")) {
							$this.data("ajaxChangeSelectedIndex", null);
						}
						break;
				}
			}
		});
	};
}(jQuery));

var $changeEpsg = $(this);
$changeEpsg.ajaxChange(function () {

	var srsValue = this.value;
	if (srsValue === "") {
		$(this).ajaxChange("abort");
		return;
	}
	$changeEpsg.mapbender(function () {
		this.setSrs(srsValue);
	});
});

var ChangeEpsg = function () {
	
	var compileSrsArray = function () {

		var srsArray = [];
		var wmsArray = [];

		// this is kind of inconsistent...for WMS, only the new extent of
		// the FIRST main map is calculated
		var mainMap = $(":mainMaps").eq(0).mapbender();

		$("div:maps").mapbender(function () {
			srsArray.push({
				frameName: this.elementName,
				epsg: this.epsg,
				extent: this.extent.toString(),
				width: this.width,
				height: this.height
			});

			for (var i = 0; i < this.wms.length; i++) {
				var wms = this.wms[i];
				// unique entries only
				if ($.inArray(wms.wms_id, wmsArray) !== -1) {
					continue;
				}
				// only wms with bounding box in current SRS
				var ext = wms.getBoundingBoxBySrs(this.epsg);
				if (ext === null) {
					continue;
				}

				srsArray.push({
					wms: wms.wms_id,
					epsg: mainMap.epsg,
					extent: ext.toString(),
					width: mainMap.width,
					height: mainMap.height
				});
				wmsArray.push(wms.wms_id);
			}
		});
		return srsArray;
	};

	var setSrsCallback = function (obj, success, message) {
		if (!success) {
			$changeEpsg.ajaxChange("abort");
			new Mapbender.Exception(message);
			return;
		}
		$changeEpsg.ajaxChange("done");
		
		var newExtent = obj;
		var mapObjNames = [];
		var myTarget = options.target[0];
		var exists = false;


		var i, j;
		for (i = 0; i < newExtent.length; i++) {
			if (newExtent[i].frameName) {
				mapObjNames.push("#" + newExtent[i].frameName);
			}
		}

		for (i = 0; i < newExtent.length; i++) {
			if (newExtent[i].wms) {
				// global wms object is deprecated.
				// this loop can be removed once the
				// wms object has been removed.
				// redundant.
				
				for (j = 0; j < wms.length; j++) {
					if (wms[j].wms_id == newExtent[i].wms) {
						wms[j].setBoundingBoxBySrs(
							newExtent[i].newSrs, 
							new Extent(
								parseFloat(newExtent[i].minx), 
								parseFloat(newExtent[i].miny), 
								parseFloat(newExtent[i].maxx), 
								parseFloat(newExtent[i].maxy)
							)
						);
					}
				}
				
				$(mapObjNames.join(",")).mapbender(function(){
					for (j = 0; j < this.wms.length; j++) {
						if (this.wms[j].wms_id == newExtent[i].wms) {
							this.wms[j].setBoundingBoxBySrs(
								newExtent[i].newSrs, 
								new Extent(
									parseFloat(newExtent[i].minx), 
									parseFloat(newExtent[i].miny), 
									parseFloat(newExtent[i].maxx), 
									parseFloat(newExtent[i].maxy)
								)
							);
						}
						break;
					}
				});
			}			
			//
			// Overview map
			//
			if (newExtent[i].frameName === myTarget){
				var map = $("#" + myTarget).mapbender();
				
				for (var ii = 0; ii < map.mb_MapHistoryObj.length; ii++) {
					if (map.mb_MapHistoryObj[ii].epsg == newExtent[i].newSrs) {
						exists = ii;
						var goback = true;
					}
				}

				if (goback) {
					var extArray = map.mb_MapHistoryObj[exists].extent.toString().split(",");
					var newExt = new Extent(
						parseFloat(extArray[0]),
						parseFloat(extArray[1]),
						parseFloat(extArray[2]),
						parseFloat(extArray[3])
					);
					map.setSrs({
						srs: newExtent[i].newSrs,
						extent: newExt,
						displayWarning: false
					});
				}
				else{
					map.setSrs({
						srs: newExtent[i].newSrs,
						extent: new Extent(
							parseFloat(newExtent[i].minx),
							parseFloat(newExtent[i].miny),
							parseFloat(newExtent[i].maxx),
							parseFloat(newExtent[i].maxy)
						),
						displayWarning: false
					});
				}
			}
			//
			// Main maps
			//
			else {
				$("#" + newExtent[i].frameName).mapbender(function () {
					this.setSrs({
						srs: newExtent[i].newSrs,
						extent: new Extent(
							parseFloat(newExtent[i].minx),
							parseFloat(newExtent[i].miny),
							parseFloat(newExtent[i].maxx),
							parseFloat(newExtent[i].maxy)
						),
						displayWarning: false
					});
				});
			}
		}
		setTimeout(function () {
			$(":maps").mapbender(function () {
				this.setMapRequest();
			});
		}, 200);
	};

	this.setSrs = function (val) {
		var srsArray = compileSrsArray();
		$.ajaxSetup({async:false});
		var req = new Mapbender.Ajax.Request({
			method: "changeEpsg",
			url: "../php/mod_changeEPSG_server.php",
			callback: setSrsCallback,
			parameters:{
				srs: srsArray,
				newSrs: val
			}
		});
		req.send();
		$.ajaxSetup({async:true});
	};
	// initialization
	<?php
		for ($i=0; $i < count($projections); $i++){
			echo "\$changeEpsg.append('<option value=\"".$projectionsValue[$i]."\">".$projectionsName[$i]."</option>');\n";
		}
	?>

	// update epsg in select box after any map request
	Mapbender.events.init.register(function () {
		$("div:mainMaps").mapbender(function () {
			var map = this;
			map.events.afterMapRequest.register(function () {
				$changeEpsg.children("option").each(function () {
					if (this.value === map.epsg) {
						$(this).attr("selected", "selected");
					}
				});
			});
			
		});
	}); 

};

$changeEpsg.mapbender(new ChangeEpsg());
