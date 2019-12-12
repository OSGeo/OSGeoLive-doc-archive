/**
 * Package: jq_ui_resizable
 *
 * Description:
 * resizable from the jQuery UI framework
 * 
 * Files:
 *  - http/plugins/jq_ui_resizable.js
 *  - http/extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.resizable.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>',
 * > 'jq_ui_resizable',5,1,'Resizable from the jQuery UI framework','','',
 * > '','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','','../plugins/jq_ui_resizable.js',
 * > '../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.resizable.js','','jq_ui','http://docs.jquery.com/UI/Resizable');
 *
 * Help:
 * http://jqueryui.com/demos/resizable/
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
Mapbender.events.init.register(function () {
	options.$target.each(function () {
		var $currentTarget = $(this);
		var id = $currentTarget.attr("id");
		var $maps = $("#" + id + ":maps");

		
		$maps.resizable({
			handles: 'se',
			start: function () {
				var mapsContainerId = $maps.attr("id") + "_maps";
				$maps.children(":not(#" + mapsContainerId + ")").hide();
			},
			stop: function () {
				var id = $currentTarget.attr("id");
				$maps.mapbender(function () {
					
					var w = parseInt($currentTarget.css("width"), 10);
					var h = parseInt($currentTarget.css("height"), 10);
					
					var sw = this.convertPixelToReal(new Mapbender.Point(0, h));
					var ne = this.convertPixelToReal(new Mapbender.Point(w, 0));

					this.setWidth(w);
					this.setHeight(h);

					this.calculateExtent(new Extent(sw, ne));	
					this.setMapRequest();

					$maps.children().show();
				});
			}
		});
	});
});
