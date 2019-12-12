/**
 * Package: overviewToggle
 *
 * Description:
 * shows and hides the overview module with a jQuery animation
 * 
 * Files:
 *  - http/javascripts/mod_overviewToggle.js
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<app_id>',
 * > 'overviewToggle',5,1,'','','div','','class="ui-widget-header ui-corner-all"',
 * > -1,-1,NULL ,NULL ,NULL ,
 * > 'display:none;height:24px;width:35px;vertical-align: middle;text-align:right',
 * > '<img style=''position:absolute;top:0px;left:0px'' src=''../img/ovtoggle.png'' /><span style=''margin-left: auto; margin-right: 0;'' class=''ui-icon ui-icon-triangle-1-e''></span>',
 * > 'div','../javascripts/mod_overviewToggle.js','','overview','','');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES ('<app_id>', 'body', 
 * > 'overviewToggle_css', '../css/dialog/jquery-ui-1.7.2.custom.css', 
 * > '' ,'file/css');
 *
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES ('<app_id>', 'overviewToggle', 
 * > 'initialOpen', 'false', 
 * > '' ,'var');
 * >
 * Help:
 * http://www.mapbender.org/OverviewToggle
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
options.initialOpen = options.initialOpen ? true : false;

var ovSwitchTarget = options.target[0];
var ovSwitchId = options.id;

var overview_visible = options.initialOpen;

//▶
//◀

eventInit.register(function () {

	var $ov = $("#" + ovSwitchTarget);
	var $this = $("#" + ovSwitchId);
	var $ovToggleButton = $("#" + ovSwitchId + " > span");
	var overviewInitialWidth = $ov.width();
	var overviewInitialHeight = $ov.height();
	var overviewInitialTop = parseInt($ov.css("top"), 10);
	var overviewLeft = parseInt($ov.css("left"), 10);
	var overviewInitialOuterWidth = $ov.outerWidth();
	
	$ov.css({
		display:"block",
		top: (overviewInitialTop + 26) + "px",
		borderWidth: "0px",
		borderStyle: "solid",
		borderColor: "#176798"
	});
	
	if (!overview_visible) {
		$ov.css({
			display:"none",	
			left: overviewLeft + "px"
		});
	} else {
			$("#" + ovSwitchId).css('width',overviewInitialWidth+'px');
			$this.addClass("ui-corner-top").removeClass("ui-corner-all");
			$ovToggleButton.removeClass("ui-icon-triangle-1-e").addClass("ui-icon-triangle-1-w");
	}
	
	$this.css({
		display:"inline-block",
		left: overviewLeft + "px",
		top: overviewInitialTop + "px",
		zIndex: $ov.css("zIndex")
	});
	
	$ovToggleButton.mouseover(function () {
		this.style.cursor = "pointer";
	}).mouseout(function () {
		this.style.cursor = "default";
	}).click(function(){
		if(overview_visible){
			//
			// Hide
			//
			$ov.animate({
				height: 0
			}, "fast", "linear", function () {
				$ov.css({
					display: "none",
					width: "0px",
					borderWidth: "0px",
					borderStyle: "solid",
					borderColor: "#176798"					
				});	
				$this.animate({
					width: 35
				}, "fast", "linear", function () {
					$this.removeClass("ui-corner-top").addClass("ui-corner-all");
					$ovToggleButton.addClass("ui-icon-triangle-1-e").removeClass("ui-icon-triangle-1-w");
				});
				overview_visible = false;
				options.initialOpen = false;
			});
		}
		else{
			//
			// Show
			//
			$this.animate({
				width: overviewInitialWidth
			}, "fast", "linear", function () {
				$ov.css({
					display: "block",
					width: overviewInitialWidth,
					height: 0,
					borderWidth: "1px",
					borderStyle: "solid",
					borderColor: "#176798"					
				});	
				$ov.animate({
					height: overviewInitialHeight
				}, "fast", "linear", function () {
					$this.addClass("ui-corner-top").removeClass("ui-corner-all");
					$ovToggleButton.removeClass("ui-icon-triangle-1-e").addClass("ui-icon-triangle-1-w");
				});
				overview_visible = true;
				options.initialOpen = true;
			});
		}
	});
});
