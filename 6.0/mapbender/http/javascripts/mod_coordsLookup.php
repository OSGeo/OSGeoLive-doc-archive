/**
 * Package: coordsLookup
 *
 * Description:
 * The user enters a coordinate tuple and selects the corresponding SRS 
 * from a select box. After submitting this form, Mapbender transforms
 * the coordinate tuple to the current SRS and zooms to the location.
 * 
 * Files:
 *  - http/javascripts/mod_coordsLookup.js
 *  - http/php/mod_coordsLookup_server.php
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<app_id>','coordsLookup',
 * > 10,1,'','Coordinate lookup','div','','',1000,0,NULL ,NULL ,NULL ,
 * > 'z-index:9999;','','div','mod_coordsLookup.php','',
 * > 'mapframe1','','');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES ('<app_id>', 'coordsLookup', 
 * > 'perimeters', '[50,200,1000,10000]', '' ,'var');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<app_id>', 'coordsLookup', 
 * > 'projections',
 * > 'EPSG:4326;Geographic Coordinates,
 * > EPSG:31466;Gauss-Krueger 2,EPSG:31467;Gaus-Krueger 3', '' ,
 * > 'php_var');
 *
 * Help:
 * http://www.mapbender.org/coordsLookup
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * perimeters		- Array of perimeters in m, like [50,200,1000,10000]
 * projections		- Array of EPSG names, like ['EPSG:31467','EPSG:31468']
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

//
// http://trac.osgeo.org/proj4js/wiki/UserGuide
//
// 3802000 / 5825000
//import some php vars to allow translation of objects in select boxes
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
var standingHighlight = null;
Mapbender.events.afterMapRequest.register( function(){
	if(standingHighlight){
		standingHighlight.paint();
	}
});
var CoordsLookup = function() {
	var that = this;
	if(
		typeof options.target      === 'undefined' || options.target.length      === 0 || 
		typeof options.perimeters  === 'undefined' || options.perimeters.length  === 0
	) {
	}

	this.buildForm = function() {
//		Container elements
		this.formContainer             = $(document.createElement('form')).attr({'id':'coords-lookup-form'}).appendTo('#' + options.id);
		this.coordsInputContainer      = $(document.createElement('p')).appendTo(this.formContainer);
		this.projectionSelectContainer = $(document.createElement('p')).appendTo(this.formContainer);
		this.perimeterSelectContainer  = $(document.createElement('p')).appendTo(this.formContainer);
		this.triggerButtonContainer    = $(document.createElement('p')).appendTo(this.formContainer);
//		Coordinates input with label
		this.coordsInputLabel          = $(document.createElement('label')).attr({'for':'coord-x'}).text('<?php echo _mb("coordinates");?>:').appendTo(this.coordsInputContainer);
		this.coordXInput               = $(document.createElement('input')).attr({'id':'coord-x','size':8}).appendTo(this.coordsInputContainer);
		this.coordYInput               = $(document.createElement('input')).attr({'id':'coord-y','size':8}).appendTo(this.coordsInputContainer);
		$(this.coordsInputLabel).after($(document.createElement('br')));
		$(this.coordXInput).after('&nbsp;/&nbsp;');
//		Projection select
		this.projectionSelect          = $(document.createElement('select')).attr({'id':'projection-select'}).appendTo(this.projectionSelectContainer);
//		Perimeter select
		this.perimeterSelect           = $(document.createElement('select')).attr({'id':'perimeter-select'}).appendTo(this.perimeterSelectContainer);		
//		Trigger button
		this.triggerButton             = $(document.createElement('input')).attr({'id':'trigger-button','type':'button','value':'<?php echo _mb("zoom to coordinates");?>'}).appendTo(this.triggerButtonContainer);		
	};
	
	this.initForm = function() {
//		Fill projection select with options
<?php
		for ($i=0; $i < count($projections); $i++){
			echo "$(this.projectionSelect).append('<option value=\"".$projectionsValue[$i]."\">".$projectionsName[$i]."</option>');\n";
		}
?>
		$(this.projectionSelect).prepend('<option value="Projektionssystem" selected=selected><?php echo _mb("Spatial Reference System");?></option>');

//		Fill perimeter select with options	
		for(var i = 0; i < options.perimeters.length; i++) {
			var optionValue = options.perimeters[i] + '';
			
			optionValue = (optionValue.length < 4) ? (optionValue + ' m') : optionValue.replace(/(\d+)(\d{3})/, '$1' + '$2' + ' m');
			
			$(this.perimeterSelect).append('<option value=' + optionValue + ' >' + optionValue + '</option>');
		}
		$(this.perimeterSelect).prepend('<option value="Umkreis" ><?php echo _mb("Perimeter: ");?></option>');
		
//		Set action for trigger button
		$(this.triggerButton).click(function() {
			Mapbender.modules[options.id].zoomToCoordinates();
		});
	};
	
	this.zoomToCoordinates = function() {
		this.coords = {};

		this.coords.x                = this.coordXInput.val().replace(',','.');
		this.coords.y                = this.coordYInput.val().replace(',','.');
		//check if deg/minutes/seconds have been inserted
		//validate 
		this.regexdms = /([0-9.]+)\°([0-9.]+)\'([0-9.]+)\'\'/;
		
		this.coords.sourceProjection = (this.projectionSelect.val()) ? 
			this.projectionSelect.val() : null;
		this.coords.targetProjection = Mapbender.modules[options.target].getSRS();
		this.coords.perimeter        = (this.perimeterSelect.val()) ? 
			parseFloat(this.perimeterSelect.val()) : null;
		//validate coordinates
		if(this.coords.x.length === 0 || isNaN(this.coords.x)) {
			this.regexdms.exec(this.coords.x);
			//alert(RegExp.$1 + ";" + RegExp.$2 + ";" + RegExp.$3);
			if (isNaN(parseFloat(RegExp.$1)) || isNaN(parseFloat(RegExp.$2)) || isNaN(parseFloat(RegExp.$3)))
			{
				alert('<?php echo _mb("Invalid X coordinate! Must be a float or a DMS value!");?>');
				return;
			} else {
				this.coords.x = parseFloat(RegExp.$1) + parseFloat(RegExp.$2) / 60.0 + parseFloat(RegExp.$3) / 3600.0;	
				//alert('this.coords.x');	
			}
		}
		if(this.coords.y.length === 0 || isNaN(this.coords.y)) {
			this.regexdms.exec(this.coords.y);
			//alert(RegExp.$1 + ";" + RegExp.$2 + ";" + RegExp.$3);
			if (isNaN(parseFloat(RegExp.$1)) || isNaN(parseFloat(RegExp.$2)) || isNaN(parseFloat(RegExp.$3)))
			{
				alert('<?php echo _mb("Invalid Y coordinate! Must be a float or a DMS value!");?>');
				return;
			} else {
				this.coords.y = parseFloat(RegExp.$1) + parseFloat(RegExp.$2 / 60.0) + parseFloat(RegExp.$3 / 3600.0);	
				//alert('this.coords.y');	
			}
		}
		
		if (this.coords.sourceProjection === null) {
			alert('<?php echo _mb("Invalid SRS!");?>');
			return;
		}

		//if(this.coords.sourceProjection && (this.coords.sourceProjection != this.coords.targetProjection)) {
		if(this.coords.sourceProjection) {
			this.transformProjection();
		}

	};
	
	this.transformProjection = function() {
		var parameters = {
			fromSrs: this.coords.sourceProjection,
			toSrs: this.coords.targetProjection
		};
		if (this.coords.perimeter === null) {
			parameters.x = this.coords.x;
			parameters.y = this.coords.y;
		}
		else {
			if (this.coords.sourceProjection == 'EPSG:4326') {
				this.R = 6371000.0;
				this.Pi = Math.PI;
				this.rho = 180.0/this.Pi;
				this.coords.perimeterlon = 0.0;
				this.coords.perimeterlat = 0.0;
				this.coords.perimeterlon = parseFloat(this.coords.perimeter) * this.rho / this.R;
				this.coords.perimeterlat = parseFloat(this.coords.perimeter) * this.rho / (this.R * Math.cos(parseFloat(this.coords.y) / this.rho));
				parameters.bbox = (
					parseFloat(this.coords.x) - parseFloat(this.coords.perimeterlon)
					) + "," + (
					parseFloat(this.coords.y) - parseFloat(this.coords.perimeterlat)
					) + "," + (
					parseFloat(this.coords.x) + parseFloat(this.coords.perimeterlon)
					) + "," + (
					parseFloat(this.coords.y) + parseFloat(this.coords.perimeterlat));
			}
			else {
			parameters.bbox = (
				parseFloat(this.coords.x) - parseFloat(this.coords.perimeter)
				) + "," + (
				parseFloat(this.coords.y) - parseFloat(this.coords.perimeter)
				) + "," + (
				parseFloat(this.coords.x) + parseFloat(this.coords.perimeter)
				) + "," + (
				parseFloat(this.coords.y) + parseFloat(this.coords.perimeter));
			}
		}
		var that = this;
		var req = new Mapbender.Ajax.Request({
			url: "../php/mod_coordsLookup_server.php",
			method: "transform",
			parameters: parameters,
			callback: function (obj, success, message) {
				if (!success) {
					new Mapbender.Exception(message);
					return;
				}
				
				var map = Mapbender.modules[options.target];
				
				if(standingHighlight !== null){ 
					standingHighlight.clean();
				}else{
					standingHighlight = new Highlight(
						[options.target],
						"standingHighlight", 
						{"position":"absolute", "top":"0px", "left":"0px", "z-index":100}, 
						2);
				}
				if (obj.points) {
					
					if (obj.points.length === 1) {
						var point = new Point(
							obj.points[0].x,
							obj.points[0].y
						) 
						map.setCenter(point);
					}
					else if (obj.points.length === 2) {
						var newExtent = new Extent(
							obj.points[0].x,
							obj.points[0].y,
							obj.points[1].x,
							obj.points[1].y
						);
						var point0 = new Point(obj.points[0].x,obj.points[0].y);
						var point1 = new Point(obj.points[1].x,obj.points[1].y);
						
						var x = point0.x + (point1.x - point0.x)/2;
						var y = point0.y + (point1.y - point0.y)/2;
						var point = new Point(x,y);
						map.calculateExtent(newExtent);
					}
					var ga = new GeometryArray();
					ga.importPoint({
						coordinates:[x,y,null]
					},that.coords.targetProjection)
					var m = ga.get(-1,-1);
					standingHighlight.add(m, "#ff0000");
					standingHighlight.paint();
					map.setMapRequest();
				}
			} 
		});
		req.send();
	};
	
	this.buildForm();
	this.initForm();

};

Mapbender.events.init.register(function() {
	Mapbender.modules[options.id] = $.extend(new CoordsLookup(),Mapbender.modules[options.id]);	
});
