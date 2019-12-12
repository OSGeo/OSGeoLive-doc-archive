/**
 * Package: mb_highlight
 *
 * Description:
 * Displays geometries in maps.
 * 
 * Files:
 *  - http/plugins/mb_highlight.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','mb_highlight',
 * > 1,1,'','','div','','',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div',
 * > '../plugins/mb_highlight.js','','mapframe1','','');
 *
 * Help:
 * http://www.mapbender.org/<wiki site name>
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * inputs    - *[optional]* connections to other elements (JSON)
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var $highlight = $(this);

var HighlightApi = function () {
	var that = this;
	var h = [];
	
	this.change = function (geoJson) {
		// add features
		var g = new GeometryArray();
		g.importGeoJSON(geoJson, false);
		for (var i = 0; i < g.count(); i++) {
			var feature = g.get(i);
			$(h).each(function () {
				this.add(feature);
			});
		}
		// paint features
		$(h).each(function () {
			this.paint();
		});
		
	};
		
	this.add = function (geoJson) {
		this.clear();
		this.change(geoJson);

	};

	this.clear = function () {
		$(h).each(function () {
			this.clean();
		});		
	};

	Mapbender.events.init.register(function () {
		options.$target.each(function () {
			var map = $(this).mapbender();
			var id = map.elementName + "_" + parseInt(Math.random()*100000,10);
			h.push(new Highlight(
				[map.elementName],
				id,
				{
					"position": "absolute",
					"top": "0px",
					"left": "0px",
					"zIndex":60
				},
				1
			));
			map.events.afterMapRequest.register(function () {
				$(h).each(function () {
					this.paint();
				});
			});
		});
		
	});
};

$highlight.mapbender(new HighlightApi());
