var customTreeController = function (myTree, options) {
//	this.myTree = new CustomTree();
//	this.myTree.root.name = "My tree";

	var currentlyDraggedNode;

	this.options = options;
	
	this.options.draggable = typeof options.draggable === "undefined" ? 
		true : options.draggable;
	this.options.collapsed = typeof options.collapsed === "undefined" ? 
		true : options.collapsed;
	this.myTree = myTree;
	
	this.$root = $("<ul class='custom-tree-treeview custom-tree-filetree'></ul>");

	var that = this;
	
	// --------------- BEHAVIOUR (begin) ------------------
	
	var addLeafBehaviour = function ($domNode, treeNode) {
		$label = $domNode.children().eq(1);
		
		// enable context menu
		if (that.options.contextMenu) {
			addContextMenuToLeaf($domNode, treeNode);
		}

		// make leaves draggable
		if (that.options.draggable) {
			makeNodeDraggable($domNode, treeNode);
		}
		var additionalBehaviour = that.options.leafBehaviour;
		
		for (var i = 0; i < additionalBehaviour.length; i++) {
			var c = additionalBehaviour[i];
			var additionalHtml = "<" + c.openTag + ">";
			
			if (typeof c.leadingBlank === "boolean" && c.leadingBlank) {
				additionalHtml = "<span>&nbsp;</span>" + additionalHtml;
			}
			
			if (typeof c.content === "string" && c.content !== "") {
				additionalHtml += c.content;
			}
			if (typeof c.closeTag === "string" && c.closeTag !== "") {
				additionalHtml += "</" + c.closeTag + ">";
			}
			var $additionalButton = $(additionalHtml);

			if (typeof c.attr === "object") {
				$additionalButton.attr(c.attr);
			}

			if (typeof c.css === "object") {
				$additionalButton.css(c.css);
			}

			if (typeof c.behaviour === "object") {
				(function () {
					var currentBehaviour = c.behaviour;
					var $currentButton = $additionalButton;
					for (var j in currentBehaviour) {
						(function () {
							var beh = currentBehaviour[j];
							if (j === "click") {
								$currentButton[j](function(){
									beh({
										treeNode: treeNode,
										appId: that.myTree.appId,
										$domNode: $currentButton
									});
								});
								
							}
							else {
								beh({
									treeNode: treeNode,
									appId: that.myTree.appId,
									$domNode: $currentButton
								});
							}
							
						})();
					}
				})();
			}
			if (typeof c.after === "boolean" && c.after) {
				$domNode.children("span").append($additionalButton);
			}
			else {
				$domNode.children("span").prepend($additionalButton);
			}
		}
		
		treeNode.hasChanged = function () {
			that.myTree.hasChanged = true;
		};
	};
	
	var addFolderBehaviour = function ($domNode, treeNode, options) {
		$hitarea = $domNode.children().eq(0);
		$label = $domNode.children().eq(1);
		if (treeNode != that.myTree.root) {
			// enable folder closing and opening
			addOpenCloseBehaviour($hitarea, options);

			// enable context menu
			if (that.options.contextMenu) {
				addContextMenuToFolder($domNode, treeNode);
			}

			// make inner nodes draggable
			if (that.options.draggable) {
				makeNodeDraggable($domNode, treeNode);
			}
		}
		else {
			// enable context menu
			if (that.options.contextMenu) {
				addContextMenuToRoot($domNode, treeNode);
			}
		}		

		// make all folders droppable
		if (that.options.droppable) {
			makeNodeDroppable($label, treeNode);
		}

		treeNode.hasChanged = function () {
			that.myTree.hasChanged = true;
		};
	};


	var deleteNode = function (treeNode) {
		if (treeNode.containsNonFolder()) {
			alert("This folder contains a WMS. It may not be deleted. Move the WMS before deleting the folder.");
		}
		else {
			treeNode.remove();
		}
	};

	var editNode = function ($domNode, treeNode) {
		var newName = prompt('Name:', treeNode.name);
		if (newName) {
			treeNode.setName(newName);
		}	
	};
	
	var addNode = function ($domNode, treeNode) {
		var newName = prompt('Name:');
		if (newName) {
			var newChild = treeNode.addChild();
			newChild.setName(newName);
		}
	};

	//
	// CONTEXT MENU
	//
	var addContextMenuToLeaf = function ($domNode, treeNode) {
		$domNode.contextMenu('leafMenu', {
			onContextMenu: function(e) {
				return true;
			},
			onShowMenu: function(e, menu) {
				return menu;
			},
			bindings: {
				'deleteService': function () {
					deleteNode(treeNode);
				}
			}
		});
	}
	
	var addContextMenuToFolder = function ($domNode, treeNode) {
		$domNode.contextMenu('folderMenu', {
			onContextMenu: function(e){
				return true;
			},
			onShowMenu: function(e, menu){
				return menu;
			},
			bindings: {
				'addFolder': function(item){
					addNode($domNode, treeNode);
				},
				'deleteFolder': function(){
					deleteNode(treeNode);
				},
				'editFolder': function(){
					editNode($domNode, treeNode);
				}
			}
		});
	};
	
	var addContextMenuToRoot = function ($domNode, treeNode) {
		$domNode.contextMenu('rootMenu', {
			onContextMenu: function(e){
				return true;
			},
			onShowMenu: function(e, menu){
				return menu;
			},
			bindings: {
				'addFolder': function(item){
					addNode($domNode, treeNode);
				}
			}
		});
	};
	
	var openFolder = function ($node) {
		var $this = $node;
		if ($this.hasClass("custom-tree-expandable-hitarea")) {
			$this.removeClass("custom-tree-expandable-hitarea");
			$this.addClass("custom-tree-collapsable-hitarea");
		}
		if ($this.hasClass("custom-tree-lastExpandable-hitarea")) {
			$this.removeClass("custom-tree-lastExpandable-hitarea");
			$this.addClass("custom-tree-lastCollapsable-hitarea");
		}
		var $li = $node.parent();
		if ($li.hasClass("custom-tree-expandable")) {
			$li.removeClass("custom-tree-expandable");
			$li.addClass("custom-tree-collapsable");
		}
		if ($li.hasClass("custom-tree-lastExpandable")) {
			$li.removeClass("custom-tree-lastExpandable");
			$li.addClass("custom-tree-lastCollapsable");
		}
		$this.siblings("ul").show("fast");
	};

	var closeFolder = function ($node) {
		var $this = $node;
		if ($this.hasClass("custom-tree-collapsable-hitarea")) {
			$this.removeClass("custom-tree-collapsable-hitarea");
			$this.addClass("custom-tree-expandable-hitarea");
		}
		if ($this.hasClass("custom-tree-lastCollapsable-hitarea")) {
			$this.removeClass("custom-tree-lastCollapsable-hitarea");
			$this.addClass("custom-tree-lastExpandable-hitarea");
		}
		var $li = $node.parent();
		if ($li.hasClass("custom-tree-collapsable")) {
			$li.removeClass("custom-tree-collapsable");
			$li.addClass("custom-tree-expandable");
		}
		if ($li.hasClass("custom-tree-lastCollapsable")) {
			$li.removeClass("custom-tree-lastCollapsable");
			$li.addClass("custom-tree-lastExpandable");
		}
		$this.siblings("ul").hide("fast");
	};
	
	//
	// OPEN AND CLOSE BEHAVIOUR
	// 
	var addOpenCloseBehaviour = function ($domNode, options) {
		if (typeof options === "object" && options.collapsed) {
			$domNode.siblings("ul").css("display", "none");
			$domNode.toggle(function () {
				openFolder($(this));
			}, function () {
				closeFolder($(this));
			});		
		}
		else {
			$domNode.toggle(function () {
				closeFolder($(this));
			}, function () {
				openFolder($(this));
			});		
		}
	};
	
	var isFolder = function ($node) {
		if ($node.children("div.custom-tree-hitarea").size() > 0) {
			return true;
		}
		return false;
	};
	
	var makeNodeLast = function ($node) {
		if (isFolder($node)) {
			$node.addClass("custom-tree-last");
			if ($node.hasClass("custom-tree-collapsable")) {
				$node.addClass("custom-tree-lastCollapsable");
			}
			else if ($node.hasClass("custom-tree-expandable")) {
				$node.addClass("custom-tree-lastExpandable");
			}
			var $hitarea = $node.children("div:first");
			if ($hitarea.hasClass("custom-tree-collapsable-hitarea")) {
				$hitarea.addClass("custom-tree-lastCollapsable-hitarea");
			}			
			else if ($hitarea.hasClass("custom-tree-expandable-hitarea")) {
				$hitarea.addClass("custom-tree-lastExpandable-hitarea");
			}			
			return;
		}
		$node.addClass("custom-tree-last");
	};
	
	var makeNodeLastButOne = function ($node) {
		if (isFolder($node)) {
			$node.removeClass("custom-tree-last");
			if ($node.hasClass("custom-tree-lastCollapsable")) {
				$node.removeClass("custom-tree-lastCollapsable");
			}
			else if ($node.hasClass("custom-tree-lastExpandable")) {
				$node.removeClass("custom-tree-lastExpandable");
			}
			var $hitarea = $node.children("div:first");
			if ($hitarea.hasClass("custom-tree-lastCollapsable-hitarea")) {
				$hitarea.removeClass("custom-tree-lastCollapsable-hitarea");
			}			
			else if ($hitarea.hasClass("custom-tree-lastExpandable-hitarea")) {
				$hitarea.removeClass("custom-tree-lastExpandable-hitarea");
			}			
			return;
		}
		$node.removeClass("custom-tree-last");
	};
	
	//
	// DRAGGABLE AND DROPPABLE
	//
	var makeNodeDraggable = function ($domNode, treeNode) {
		$domNode.draggable({
			"helper": "clone",
			"start": function(){
				var $this = $(this);
				$(this).addClass("currently-dragging");
				currentlyDraggedNode = treeNode;
			}
		});
	};
	
	var makeNodeDroppable = function ($domNode, treeNode) {
		$domNode.droppable({
			"accept": function ($draggable) {
				var $invalidDroppables = $(".currently-dragging .treeNodeDrop");
				var $invalidDroppablesMinusThis = $invalidDroppables.not($domNode);

				if ($invalidDroppables.size() > $invalidDroppablesMinusThis.size()) {
					return false;
				}
				return true;
			},
			"tolerance": "pointer", 
			"drop": function (e, ui) {
				$toDomNode = $(this);
				$fromDomNode = $(ui.draggable);

				var parent1 = ui.draggable.parent().prev().get(0);
				var parent2 = this;
				// has node been inserted in the same branch?
				if (parent1 === parent2) {
					// the dragged node is the last of the branch
					if ($fromDomNode.hasClass("custom-tree-last")) {
						// do nothing
					}
					else {
						var $li = $toDomNode.next().children();
						if ($li.size() > 1) {
							var $oldLast = $li.eq($li.size() - 2);
							makeNodeLastButOne($oldLast);
						}
						$toDomNode.next().append($fromDomNode);
						makeNodeLast($fromDomNode);
					}
				}
				else {
	
					// the dragged node is the last of the branch
					if ($fromDomNode.hasClass("custom-tree-last")) {

						// make last but one node last
						$fromDomNode.removeClass("custom-tree-last");
						
						var $prev = $fromDomNode.prev("li");
						if ($prev.size() > 0) {
							makeNodeLast($prev);
						}
					}
					
					var $oldLast = $toDomNode.next().children("li:last");
					
					if ($oldLast.size() > 0) {
						makeNodeLastButOne($oldLast);
					}

					$toDomNode.next().append($fromDomNode);
					makeNodeLast($fromDomNode);

				}
				currentlyDraggedNode.afterMove = function () {
					$(".custom-tree-leaf").removeAttr("style");
					$("*").removeClass("currently-dragging");
				};
				currentlyDraggedNode.move(treeNode);
			}
		});		
	};
	
	// --------------- BEHAVIOUR (end) ------------------

	var createLeaf = function (treeNode, options) {
		
		//PLEASE NOTE: Workaround for IE (see more information here: http://dev.jqueryui.com/ticket/4333#comment:7)
		var $currentItem = $("<li></li>").mousedown(function(e) {
		    if($.browser.msie) {
		         e.stopPropagation();
		    }
		});

		var $currentIcon = $("<div></div>");
		var $currentLabel = $("<span class='custom-tree-file'>" + treeNode.name + "</span>");

		if (typeof options === "object" && options.last) {
			$currentItem.addClass("custom-tree-last");
		}

		$currentItem.append($currentIcon);
		$currentItem.append($currentLabel);
		addLeafBehaviour($currentItem, treeNode);

		treeNode.$domNode = $currentItem;
		treeNode.isFolder = false;

		treeNode.afterRemove = function () {
			$currentItem.remove();
		};

		return $currentItem;
	};
	
	var createFolder = function (treeNode, options) {
		var $currentItem;
		if (typeof options === "object" && options.collapsed) {
			$currentItem = $("<li class='custom-tree-expandable'></li>");
		}
		else {
			$currentItem = $("<li class='custom-tree-collapsable'></li>");
		}
		var $currentIcon = $("<div class='custom-tree-hitarea'></div>");
		var $currentLabel = $("<div class='treeNodeDrop'>" + 
			"<span class='custom-tree-folder'>" + treeNode.name + 
			"</span></div>");
		var $currentFolder = $("<ul></ul>");

		if (typeof options === "object") {
			if (options.collapsed) {
				if (options.last) {
					$currentItem.addClass("custom-tree-lastExpandable");
					$currentIcon.addClass("custom-tree-lastExpandable-hitarea");
				}
				$currentItem.addClass("custom-tree-expandable");
				$currentIcon.addClass("custom-tree-expandable-hitarea");
			}
			else {
				if (options.last) {
					$currentItem.addClass("custom-tree-lastCollapsable");
					$currentIcon.addClass("custom-tree-lastCollapsable-hitarea");
				}
				$currentItem.addClass("custom-tree-collapsable");
				$currentIcon.addClass("custom-tree-collapsable-hitarea");
			}
		}
		else {
			$currentItem.addClass("custom-tree-collapsable");
			$currentIcon.addClass("custom-tree-collapsable-hitarea");
		}
		
		$currentItem.append($currentIcon);
		$currentItem.append($currentLabel);
		$currentItem.append($currentFolder);
		treeNode.isFolder = true;

		addFolderBehaviour($currentItem, treeNode, options);

		treeNode.$domNode = $currentItem;

		treeNode.afterRemove = function () {
			$currentItem.remove();
		};

		treeNode.afterSetName = function () {
			$currentLabel.children().eq(0).html(treeNode.name);
		};

		treeNode.afterAddChild = function (newChild) {
			var $folder = $currentItem.children("ul");
			var size = $folder.children("li").size();
			if (size > 0) {
				makeNodeLastButOne($folder.children("li").eq(size - 1));
			}			
			$newNode = createFolder(newChild, {
				last: true
			});
			$folder.append($newNode);
		};

		treeNode.afterAppend = function (newChild) {
			var $folder = $currentItem.children("ul");
			var size = $folder.children("li").size();
			if (size > 0) {
				makeNodeLastButOne($folder.children("li").eq(size - 1));
			}			
			$newNode = createFolder(newChild, {
				last: true
			});
			$folder.append($newNode);
		};
		return $currentItem;
	};
	
	/**
	 *  A recursive function to draw the nodes of a tree and attach 
	 *  draggables and droppables
	 *  
	 * @param {Object} $domNode
	 * @param {Object} treeNode
	 */
	var drawNode = function ($domNode, treeNode, options) {
		var numOfChildren = treeNode.childNodeList.count();
		var $newNode;

		if (numOfChildren === 0 && treeNode != that.myTree.root && !treeNode.isFolder) {
			$newNode = createLeaf(treeNode, options);
		}
		else {
			$newNode = createFolder(treeNode, options);

			// visit the child nodes (depth first)
			var $folder = $newNode.children("ul");
			for (var i = 0; i < numOfChildren; i++) {
				var opt = {
					collapsed: that.options.collapsed
				};
				if (i === numOfChildren - 1) {
					opt.last = true;
				}
				drawNode.apply(that, [$folder, treeNode.childNodeList.get(i), opt]);
			}
		}		
		// attach node to tree
		$domNode.append($newNode);
	};

	$("#" + this.options.id).empty();
	$("#" + this.options.id).append(that.$root);
	if (options.skipRootNode) {
		var numOfChildren = that.myTree.root.childNodeList.count();
		for (var i = 0; i < numOfChildren; i++) {
			drawNode.apply(that, [that.$root, that.myTree.root.childNodeList.get(i), {
				last: numOfChildren - 1 === i ? true : false,
				collapsed: typeof that.options.collapsed !== "undefined" ? 
					that.options.collapsed : true
			}]);
		}
	}
	else {
		drawNode(that.$root, that.myTree.root, {
			last: true
		});
	}
};
