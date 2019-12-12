/**
 * Package: mb_metadata_layerTree
 *
 * Description:
 *
 * Files:
 *
 * SQL:
 * 
 * Help:
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

var $metadataLayerTree = $(this);

var MetadataLayerTreeApi = function (o) {
	var that = this;

	var instanceId = "choose";

	var createFolder = function (set) {
		return {
			data: set.attr.layer_title,
			attr: {
				data: $.toJSON(set.attr)
			},
			state: "closed",
			children: []
		};
	};
	
	var createLeaf = function (set) {
		return {
			attr: {
				data: $.toJSON(set.attr)
			},
			data: {
				title: set.attr.layer_title
			}
		};
	};
	
	var toJsTreeJson = function (nestedSets) {
		if (!nestedSets.length && nestedSets.length !== 0) {
			new Mapbender.Exception("Nested sets not an array.");
			return [];
		}
		var right = null;
		if (arguments.length === 2) {
			right = arguments[1];
		}
		var children = [];
		while (nestedSets.length > 0) {
			set = nestedSets.shift();
			if (typeof set.left != "number" || typeof set.right != "number") {
				new Mapbender.Exception("Left or right not set.");
				return [];
			}

			// is a different subtree, go back
			if (right !== null && right < set.right) {
				nestedSets.unshift(set);
				return children;
			}
			// is a leaf
			else if (set.right - set.left === 1) {
				children.push(createLeaf(set));
			}
			// is a folder
			else {
				var node = createFolder(set);
				var nodeChildren = toJsTreeJson(nestedSets, set.right);
				children.push($.extend(node, {
					children: nodeChildren
				}));
			}
		}
		return children;
	};

	var checkLayer = function () {
		$("#" + instanceId).find("li").each(function () {
			var metadata = $(this).metadata({
				type: "attr",
				name: "data"
			});
			if (metadata && metadata.layer_searchable) {
				$("#" + instanceId).jstree("check_node", this);
			}
		});
	};

	var initTree = function (nestedSets) {
		var jsTreeData = toJsTreeJson(nestedSets);
		
		jsTreeData[0].state = "open";
		
		$("#" + instanceId).jstree("destroy");
		
		$.jstree._themes = "../extensions/jsTree.v.1.0rc/themes/";
		
		$("#" + instanceId).empty().jstree({ 
			"json_data" : {
				"data" : jsTreeData
			},
            "checkbox" :{
                "check_recursively": false
            },
			"plugins" : [ "themes", "json_data", "ui", "checkbox" ]
		});
		
		$("#" + instanceId).bind("before.jstree", function (evt, data) {
			if (data.func === "change_state" && data.args[0].tagName.toUpperCase() === "A") {
				$("#" + instanceId).find("a").removeClass("jstree-clicked");
				$(data.args[0]).parent().children("a").addClass("jstree-clicked");
				var metadata = $(data.args[0]).parent().metadata({
					type: "attr",
					name: "data"
				});
				that.events.selected.trigger({
					"layer": metadata
				});
									
				evt.stopImmediatePropagation();
				return false;
			}
		});
		checkLayer();
	};

	this.serialize = function (callback) {
		var data = {
			wms: {
				"layer_searchable": []
			}
		};

		var $checked = $("#" + instanceId).jstree("get_checked");
		$checked.each(function () {
			var metadata = $(this).metadata({
				type: "attr",
				name: "data"
			});
			data.wms.layer_searchable.push(metadata.layer_id);
		});
		
		if ($.isFunction(callback)) {
			callback(data);
		}
		return data;
	};	

	this.events = {
		selected: new Mapbender.Event()
	};

	this.init = function (obj) {
		// get layer from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_server.php",
			method: "getLayerByWms",
			parameters: {
				"id": obj
			},
			callback: function (obj, result, message) {
				if (!result) {
					return;
				}
				initTree(obj.nestedSets);
			}
		});
		req.send();		
	};
};

$metadataLayerTree.mapbender(new MetadataLayerTreeApi(options));
