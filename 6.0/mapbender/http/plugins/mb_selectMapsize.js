/**
 * Package: selectMapsize
 *
 * Description:
 * Select the map size from a pre defined settings, like large or small
 * 
 * Files:
 *  - http/plugins/mb_selectMapsize.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>','selectMapsize',
 * > 2,1,'change the size of the map with a selectbox','','select','','',
 * > 750,10,100,20,5,'','<option value="800,800">gross</option><option value="600,600">mittel</option><option value="500,500">klein</option>',
 * > 'select','../plugins/mb_selectMapsize.js','','mapframe1','','');
 *
 * Help:
 * http://www.mapbender.org/SelectMapsize
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

var mod_selectMapsize_target = options.target[0];

var $this = $(this).change(function () {
	mod_selectMapsize(this);
});

function mod_selectMapsize(obj){
	var map = Mapbender.modules[mod_selectMapsize_target];     
	var p = obj.value.split(",");
	var w = parseInt(p[0], 10) ;
	var h = parseInt(p[1], 10);
	map.setWidth(w);
	map.setHeight(h);

	var pos = map.convertPixelToReal(new Mapbender.Point(w,h));
	var coords = map.getExtent().split(",");
	var newExtent = new Mapbender.Extent(
		parseInt(coords[0], 10),
		pos.y,
		pos.x,
		parseInt(coords[3], 10)
	);
	map.calculateExtent(newExtent); 
	map.setMapRequest();
}

Mapbender.events.init.register(function () {
	// set value from select box
	mod_selectMapsize($this.get(0));
	
	// listen to dimensions of target
	// if change occurs, set select box value if possible
	var map = Mapbender.modules[mod_selectMapsize_target];     
	map.events.dimensionsChanged.register(function (obj) {
		$this.children("option").each(function () {

			var value = this.value.split(",");
			if (obj.width === parseInt(value[0], 10) 
				&& obj.height === parseInt(value[1], 10)) {
				this.selected = true;
			}
		});
	});
	
});
