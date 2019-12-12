/**
 * Package: mb_metadata_featuretypeTree
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

var $metadataFeaturetypeTree = $(this);

var MetadataFeaturetypeTreeApi = function (o) {
	var that = this;

	var instanceId = "choose";

	var createFolder = function (set) {
		return {
			attributes: {
				data: $.toJSON(set.attr)
			},
			data: set.attr.featuretype_name,
			state: "closed",
			children: []
		};
	};
	
	var createLeaf = function (set) {
		return {
			attributes: {
				data: $.toJSON(set.attr)
			},
			data: {
				title: set.attr.featuretype_name
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

	var initTree = function (nestedSets) {
		var jsTreeData = toJsTreeJson(nestedSets);
		jsTreeData[0].state = "open";
		
		var jsTree = $.tree.reference(instanceId);
		if (jsTree !== null) {
			jsTree.destroy();
		}
		
		$("#" + instanceId).tree({
			ui: {
//				theme_path: "../extensions/jsTree.v.0.9.9a2/themes/checkbox/style.css",
//				theme_name: "checkbox"
				theme_path: "../extensions/jsTree.v.0.9.9a2/themes/default/style.css",
				theme_name: "default"
			},
			plugins: {
//				checkbox: {			
//				}
			},
			data : { 
				type : "json",
				opts : {
					static : jsTreeData
				}
			},
			callback: {
				onselect: function (node, treeObj) {
					var data = $(node).metadata({
						type: "attr",
						name: "data"
					});
					that.events.selected.trigger({
						"featuretype": data
					});
				}
			}
		});
	};


	this.events = {
		selected: new Mapbender.Event()
	};

	this.init = function (obj) {
		// get featuretypes from server
		var req = new Mapbender.Ajax.Request({
			url: "../plugins/mb_metadata_wfs_server.php",
			method: "getFeaturetypeByWfs",
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

$metadataFeaturetypeTree.mapbender(new MetadataFeaturetypeTreeApi(options));
