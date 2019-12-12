/**
 * Package: wmsTree
 *
 * Description:
 * A basic WMS layer switcher. You can turn entire WMS on or off by clicking an
 * icon, and additionally change the display order by dragging and dropping.
 *
 * Files:
 *  - ../plugins/mb_wmsTree.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment,
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height,
 * > e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod,
 * > e_target, e_requires, e_url) VALUES('<appId>','wmsTree',1,1,'','',
 * > 'div','','',1020,100,200,500,NULL ,'','','div',
 * > '../plugins/mb_wmsTree.js','','mapframe1','jq_ui_sortable','');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value,
 * > context, var_type) VALUES('<appId>', 'wmsTree', 'css',
 * > 'ul.wmstree-list {
 * >    list-style: none;
 * > }
 * > span.wmstree-tick {
 * >    float: right;
 * >    cursor: pointer;
 * > }
 * > li.wmstree-handle {
 * >    cursor: move;
 * > }', '' ,'text/css');
 * >
 * > INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value,
 * > context, var_type) VALUES('<appId>', 'wmsTree', 'liveUpdate', '1',
 * > '1 = change the wms order in the map while dragging, 0 = change after drop'
 * > ,'var');
 *
 * Help:
 * http://www.mapbender.org/<wiki site name>
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 *
 * Parameters:
 * css        - text/css
 * liveUpdate - *[optional]* 1 = change the wms order in the map while dragging,
 *					0 = change after drop (default)
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var $wmsTree = $(this).append("<ul class='wmstree-list'></ul>");

var WmsTreeApi = function (o) {
	var that = this;
	var map;
	var $list = $wmsTree.children("ul");
	var notSortable = (o.notSortable && typeof o.notSortable === "string") ?
		o.notSortable.split(",") : [];
	var reverseSortOrder = (o.reverseSortOrder &&
		(o.reverseSortOrder = "true"  || typeof o.reverseSortOrder === "number")) ?
		true : false;

	var updateAllTicks = function () {
		$list.find("span.wmstree-tick").each(function () {
			updateTick($(this));
		});
	};

	var updateTick = function ($span) {
		var wms = $span.parent().data("wms");
		if (typeof wms !== "object") {
			return;
		}
		var layer = wms.objLayer[0];
		var isVisible = layer.gui_layer_visible;

		var isInScale = false;
		for (var i = 1; i < wms.objLayer.length; i++) {
			if (wms.objLayer[i].checkScale(map)) {
				isInScale = true;
				break;
			}
		};
		if (!isInScale) {
			$span.parent().addClass("ui-state-disabled").end().css("cursor", "default");
		}
		else {
			$span.parent().removeClass("ui-state-disabled").end().css("cursor", "pointer");
		}

		var $tick = $span.find("img");
		$tick.attr("src", isVisible ? "../img/tick.png" : "../img/cross.png");
	};

	var toggleWmsVisibility = function () {
		var $row = $(this).parent();
		if ($row.hasClass("ui-state-disabled")) {
			return;
		}
		var wms = $row.data("wms");
		if (typeof wms !== "object") {
			return;
		}
		var layer = wms.objLayer[0];
		var isVisible = layer.gui_layer_visible;
		wms.handleLayer(layer.layer_name, "visible", !isVisible ? 1 : 0);
		map.setMapRequest();
	};

	var createRow = function (wms) {
		if (!wms || wms.gui_wms_visible !== 1 || wms.objLayer[0].gui_layer_selectable === 0) {
			return;
		}
		var name = wms.objLayer[0].layer_currentTitle;
		var html = "<li class='ui-state-default wmstree-handle'>" +
			"<span class='wmstree-label'>" + name + "</span>" +
			"<span class='wmstree-tick'><img " +
			"src='../img/tick.png' /></span></li>";
		var $row = $(html).data("wms", wms);

		// these rows will now be sortable
		var index = $.inArray(wms.objLayer[0].layer_name, notSortable);
		if (index !== -1) {
			$row.addClass("wmstree-notsortable");
		}
		else {
			$row.hover(function () {
				$(this).addClass("ui-state-hover");
			}, function () {
				$(this).removeClass("ui-state-hover");
			});
		}

		var $tick = $row.find("span.wmstree-tick");
		$tick.click(toggleWmsVisibility);
		updateTick($tick);
		return $row;
	};

	var initTree = function () {
		for (var i = 0; i < map.wms.length; i++) {
			(function () {
				var wms = map.wms[i];
				var $row = createRow(wms);
				if (reverseSortOrder) {
					$list.prepend($row);
				}
				else {
					$list.append($row);
				}
			})();
		}

		var oldIndex, newIndex;

		var rearrangeWms = function (ui) {
			ui.item.removeClass("ui-state-hover");

			var wms = ui.item.data("wms");
			if (typeof oldIndex === "number" &&
				typeof newIndex === "number" &&
				oldIndex === newIndex
			) {
				return;
			}

			// If steps > 0 the WMS is moved down in the list, hence the
			// third parameter to the move method (moveUp) is false, which
			// means that the WMS is moved down in the map object.
			// If reverseSortOrder is set, this effect is canceled out.
			var steps = (newIndex - oldIndex);
			for (var i = 0; i < Math.abs(steps); i++) {
				map.move(wms.wms_id, null, steps < 0);
				// A problem will occur if a WMS is not selectable:
				// it will not appear in the tree, but it is in the
				// map object. This will cause index trouble.
			}
			map.setMapRequest();
		};

		var getIndex = function (li) {
			if (reverseSortOrder) {
				return li.nextAll("li").length;
			}
			return li.prevAll("li").length;
		};

		var isPrevious = function (indexA, indexB) {
			if (reverseSortOrder) {
				if (indexB < indexA) {
					return true;
				}
				return false;
			}
			if (indexA < indexB) {
				return true;
			}
			return false;
		};

		var decrementIndex = function (index) {
			if (reverseSortOrder) {
				return index + 1;
			}
			return index - 1;
		};

		$list.sortable({
			axis: "y",
			items: "li:not(li.wmstree-notsortable)",
			containment: $wmsTree,
			start: function (event, ui) {
				oldIndex = getIndex(ui.item);
			},
			change: o.liveUpdate ? function (event, ui) {
				newIndex = getIndex(ui.placeholder);
				// if the item is previous to the placeholder,
				// do not take it into account
				if (isPrevious(getIndex(ui.item), newIndex)) {
					newIndex = decrementIndex(newIndex);
				}
				rearrangeWms(ui);
				oldIndex = newIndex;
			} : function () {},
			stop: !o.liveUpdate ? function (event, ui) {
				newIndex = getIndex(ui.item);
				rearrangeWms(ui);
			} : function () {}
		}).disableSelection();

	};

	Mapbender.events.init.register(function () {
		if (o.$target && o.$target.jquery) {
			map = o.$target.mapbender();
			map.events.afterMapRequest.register(function () {
				updateAllTicks();
			});
			Mapbender.events.afterLoadWms.register(function (obj) {
				if (obj && obj.wms) {
					var wms = obj.wms;
					var pos = map.getWmsIndexById(wms.wms_id);
					if (typeof pos !== "number") {
						return;
					}
					var $row = createRow(wms);
					// add row at correct position
					if (pos === map.wms.length - 1) {
						if (reverseSortOrder) {
							$list.prepend($row);
						}
						else {
							$list.append($row);
						}
					}
					else {
						new Mb_exception("Open issue: cannot add newly " +
							"inserted WMS to list if WMS is not put on top " +
							"of map.");
					}
				}
			});
			initTree();
		}
		else {
			new Mb_exception("No target given. WmsTree not available.");
		}
	});
};

$wmsTree.mapbender(new WmsTreeApi(options));