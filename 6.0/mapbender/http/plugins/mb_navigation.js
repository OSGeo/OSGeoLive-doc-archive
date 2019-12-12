/**
 * Package: mb_navigation
 *
 * Description:
 * Adds navigation arrows on top of the map
 * 
 * Files:
 *  - http/plugins/mb_navigation.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','mb_navigation',
 * > 2,1,'Adds navigation arrows on top of the map','Navigation','div','','',
 * > NULL ,NULL ,NULL ,NULL ,NULL ,'','','div','../plugins/mb_navigation.js',
 * > '','mapframe1','mapframe1','http://www.mapbender.org/Navigation');
 *
 * Help:
 * http://www.mapbender.org/Navigation
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

var iconDim = 18;
var $this = $(this);

Mapbender.events.init.register(function () {
	options.$target.each(function () {
		var $target = $(this);
		var id = $target.attr("id");

		var positionArrow = function ($domElement, direction) {
			var top = parseInt($this.css("top"), 10);
			var left = parseInt($this.css("left"), 10);
			var iconOffsetTop = isNaN(top) ? 10 : top;
			var iconOffsetLeft = isNaN(left) ? 10 : left;
			switch (direction) {
				case "n":
					top = (
						iconOffsetTop
					) + "px";
					left = (iconOffsetLeft + iconDim/2) + "px";
					break;
				case "s":
					top = (iconOffsetTop + 2*iconDim) + "px";
					left = (iconOffsetLeft + iconDim/2) + "px";
					break;
				case "w":
					top = (iconOffsetTop + iconDim) + "px";
					left = (iconOffsetLeft) + "px"
					break;
				case "e":
					top = (iconOffsetTop + iconDim) + "px";
					left = (iconOffsetLeft + iconDim) + "px"
					break;
			}
			$domElement.css({
				top: top,
				left: left,
				position:"absolute",
				zIndex: $this.css("z-index")
			});
		};
		
		var directionArray = ["n", "e", "s", "w"];
		for (i in directionArray) {
			(function () {
				var dir = directionArray[i];
				var $arrow = $("<span title='" + dir.toUpperCase() + "' " + 
					"id='" + id + "_" + dir + "' class=" + 
					"'ui-icon ui-corner-all ui-state-default ui-icon-triangle-1-" + dir + 
					"'></span>"
				).mousedown(function (e) {
					$target.mapbender(function () {
						this.pan(dir);
					});
					return false;
				}).mouseover(function () {
					$(this).addClass("ui-state-hover");
					return true;
				}).mouseout(function () {
					$(this).removeClass("ui-state-hover");
					return true;
				}).css("cursor", "pointer")
				.appendTo($target);
				positionArrow($arrow, dir);
				
				$target.mapbender(function () {
					this.events.dimensionsChanged.register(function () {
						positionArrow($arrow, dir);
					});
				});
			})();
		}
	});
});
