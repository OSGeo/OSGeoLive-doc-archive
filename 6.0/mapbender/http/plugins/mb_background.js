/**
 * Package: mb_background
 *
 * Description:
 * A GoogleMaps like button set to select the current background map.
 * 
 * Files:
 *  - http/plugins/mb_background.js
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES ('<appId>',
 * > 'mb_background',1,1,'Set background WMS','Set background WMS','div','',
 * > '',NULL ,NULL ,NULL ,NULL ,NULL ,'','','div',
 * > '../plugins/mb_background.js','','mapframe1','','');
 * > 
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, 
 * > context, var_type) VALUES('test_background', 'body', 
 * > 'setBackgroundWmsCss', '.label-background-wms {
 * > border: 1px solid black;
 * > padding: 2px 10px 2px 10px;
 * > background-color: #fff;
 * > cursor: pointer;
 * > white-space: nowrap;
 * > }
 * > .label-background-wms-active {
 * > font-weight: bold;
 * > padding: 1px 9px 1px 9px;
 * > border: 2px solid black;
 * > }
 * > .container-background-wms {
 * > position: relative;
 * > z-index: 300;
 * > margin: 7px;
 * > text-align:right;
 * > }', '' ,'text/css');
 *
 * Help:
 * http://www.mapbender.org/<wiki site name>
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

var $background = $(this);

var BackgroundApi = function (o) {

	Mapbender.events.afterInit.register(function () {
		o.$target.each(function () {
			var $map = $(this);
			var map = $map.mapbender();
			
			var $bgRadio = $("<div />")
				.addClass("container-background-wms");

			var firstBackgroundWmsIsActivated = false;
			var numBackgroundWms = 0;
			
			// set first background wms to visibility = 2
			$(map.wms).each(function () {
				var wms = this;
				var isHidden = (wms.gui_wms_visible === 0) ? true : false;
				if (isHidden) {
					if (!firstBackgroundWmsIsActivated) {
						wms.gui_wms_visible = 2;
						firstBackgroundWmsIsActivated = true;
					}
					numBackgroundWms++;
				}
			});
			
			//workaround for overview
			if(Mapbender.modules['overview']) {
				Mapbender.modules['overview'].wms[0].gui_wms_visible = 1;
				setSingleMapRequest('overview',Mapbender.modules['overview'].wms[0].wms_id);
			}
			//workaround for first Maprequest
			setSingleMapRequest(o.target,map.wms[0].wms_id);

			// if less than two background wms are found, 
			// do not display buttons
			if (numBackgroundWms < 2) {
				return;
			}
			// display buttons in map
			$(map.wms).each(function () {
				var wms = this;
				var isHidden = (wms.gui_wms_visible === 0) ? true : false;
				var isVisible = (wms.gui_wms_visible === 2) ? true : false;
				if (!isHidden && !isVisible) {
					return;
				}
				$("<span />")
					.addClass("label-background-wms")
					.addClass(isVisible ? " label-background-wms-active" : "")
//					.text(wms.wms_title)
					.text(wms.objLayer[0].layer_currentTitle)
					.data("wms", wms)
					.mousedown(function (e) {
						wms.gui_wms_visible = 2;
						$(this)
							.addClass("label-background-wms-active")
							.siblings()
								.removeClass("label-background-wms-active")
								.each(function () {
									var wms = $(this).data("wms");
									wms.gui_wms_visible = 0;
								});
						map.zoom(true, 0.999);
						return false;
					}).appendTo($bgRadio);
			});
			$bgRadio.appendTo($map);

			map.events.afterMapRequest.register(function () {
				var scale = map.getScale();
				$bgRadio.children("span").each(function () {
					var $span = $(this);
					var wms = $span.data("wms");
					if (!wms) {
						return;
					}
					var layer = wms.objLayer[0];
					if (!layer) {
						return;
					}
					if (layer.gui_layer_maxscale < scale || layer.gui_layer_minscale > scale) {
						$span.addClass("label-background-wms-unavailable");
					}
					else {
						$span.removeClass("label-background-wms-unavailable");
					}
				});
			});
		});
	});
};

$background.mapbender(new BackgroundApi(options));
