/**
 * Package: tabs_vertical
 *
 * Description:
 * An accordion decorator, replaces the deprecated element "tabs"
 *
 * Enter the elements to be put in the accordion in its target field
 * (comma-separated list of element ids).
 *
 * Avoid styles in elements within the accordion.
 *
 * The overview won't work in the accordion, as it cannot be positioned
 * absolutely.
 *
 * The width of this module determines the accordion width, but the height
 * of the individual elements determines the height.
 *
 *
 * Files:
 *  - http/plugins/mb_accordion.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>',
 * > 'tabs_vertical',2,1,
 * > 'An accordion decorator, replaces the deprecated element tabs',
 * > '','div','','',10,130,300,NULL ,2,'','','div',
 * > '../plugins/../plugins/mb_tabs_vertical.js','','','jq_ui_accordion','');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<appId>', 'tabs_vertical', 
 * > 'active', '2', 'which tab to open on startup [1..n]' ,'var');
 *
 * Help:
 * 
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

$this = $(this);

for (var i = 0; i < options.target.length; i++) {
	$c = $("#" + options.target[i]);
	$this.append(
		$(
			"<h3><a id='header_" + options.id + "_" +	$c.attr("id") +
			"' href='#'>" +	Mapbender.modules[$c.attr("id")].currentTitle +
			"</a></h3>"
		)
	).append(
		$(
			"<div></div>"
		).append(
			$(
				"<div style='height:" + $c.css("height") +	"'></div>"
			).append($c)
		)
	);
}

var defaults = {
	collapsible: true,
	autoHeight: false,
	active: typeof options.active !== "number" ? 
		false : options.active - 1
};

var myOptions = $.extend(defaults, options.accordionOptions);

$this.accordion(myOptions);

var TabsVerticalApi = function (o) {
	this.setDimensions = function () {
		$this.accordion("resize");
	};
};

$this.mapbender(new TabsVerticalApi(options));
