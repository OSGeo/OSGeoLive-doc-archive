/**
 * Package: ol_panel
 *
 * Description:
 * 
 * 
 * Files:
 *  - http/plugins/ol_panel.js
 *
 * SQL:
 *
 * Help:
 * http://www.mapbender.org/ol_panel
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

var $panel = $(this);

var PanelApi = function () {
//	var panel = new OpenLayers.Control.Panel({
//		div: $panel.get(0)	
//	});

	var panel = new OpenLayers.Control.Panel({
//		div: $("<div></div>").css({
//			position: "absolute",
//			top: "30px",
//			left: "30px"
//		}).appendTo("body").get(0)
		div: $panel.get(0)
	});
	
	var map = options.$target.eq(0).mapbender();
	map.mapbenderEvents.mapReady.register(function() {
		var controls = options.controls.split(",");
		$(controls).each(function () {
			var $node = $("#" + this);
			if ($node.size() > 0) {
				var module = $node.mapbender();
				if (module.buttons) {
					panel.addControls(module.buttons);
				}
			}
		});
		map.addControl(panel);
		if (options.horizontal) {
			$(panel.div).children().each(function () {
				$(this).css("float", "left");
			});
		}
	});
};

$panel.mapbender(new PanelApi());