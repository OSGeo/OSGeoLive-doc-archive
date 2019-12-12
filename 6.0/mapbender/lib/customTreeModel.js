/**
 * @class A List object is an array of arbitrary objects with additional methods. 
 *
 * @constructor
 */
var List = function() {
	
	/**
	 * gets the number of elements in this {@link List}
	 *
	 * @returns number of elements in this {@link List}
	 * @type Integer
	 */
	this.count = function() {
		return this.list.length;
	};

	/**
	 * deletes the object at index i; -1 refers to the last object in this {@link List}
	 *
	 * @param {Integer} i index
	 */
	this.del = function(i){
		i = this.getIndex(i);
		if (i !== false) {
			for(var z = i; z < this.count() - 1; z++){
				this.list[z] = this.list[z+1];
			}
			this.list.length -= 1;
			return true;
		}
		return false;
	};

	/**
	 * empties this {@link List}
	 */
	this.empty = function() {
		while (this.list !== null && this.count() > 0) {
			this.list.pop();
		}
	};
	
	/**
	 * @param {Integer} i index
	 * @returns the object at index i; -1 refers to the last object in this {@link List}
	 * @type Integer or false
	 */
	this.get = function(i) {
		i = this.getIndex(i);
		if (i !== false) {return this.list[i];}
		return false;		
	};
	/**
	 * adds a reference to item to this {@link List}.
	 *
	 * @param {Object} item an object
	 */
	this.add = function(item) {
		var i = this.list.length;
		this.list[i] = item;
	};
	/**
	 * adds a copy of item to this {@link List}.
	 *
	 * @param {Object} item an object
	 */
	this.addCopy = function(item) {
		this.add(Mapbender.cloneObject(item));
	};
	/**
	 * attaches the {@link List} aList to this {@link List}
	 *
	 * @param {List} aList another list
	 */
	this.union = function(aList) {
		for (var i=0; i < aList.count(); i++) {this.addCopy(aList.get(i));}
	};
	/**
	 * checks if the index is valid and returns it if it is; if i == -1, the correct index is retrieved.
	 *
	 * @private
	 * @return Integer or false
	 * @type Integer
	 */
	this.getIndex = function(i){ 
		var len = this.list.length;
		if (i<0 && len + i > -1) {
			return len + i;			
		}
		else if (i > -1 && i < len){
			return i;
		}
		var e = new Mb_exception("class List: function getIndex: member index " + i + " is not valid");
		return false;
	};
	/**
	 * @returns a {String} representation of this List
	 * @type String
	 */
	this.toString = function(){
		var str = "";
		for (var i =0 ; i < this.count() ; i++){
			str += this.get(i).toString();
		}
		return str;	
	};	
	
	this.list = null;
};

/**
 * A tree 
 * 
 * Using adjacency model 
 * 
 * w/ nested sets output
 */
var CustomTree = function (options) {
	this.root = new CustomTreeNode (null);
	
	var that = this;
	
	this.hasChanged = false;
	
	this.toNestedSets = function () {
		var nodeArray = toNestedSetsNode.apply(this, [[], this.root, 1]);
		return nodeArray;
	};
	
	var toNestedSetsNode = function (nodeArray, node, nextLeft) {
		var left = nextLeft;
		var right;
		var newLeft = nextLeft + 1;
		var nodeCount = node.childNodeList.count();
		var wmsIdArray = [];

		var containsFolder = false;
		for (var i = 0; i < nodeCount; i++) {
			var currentChild = node.childNodeList.get(i);
			// ... recursion
			if (currentChild.isFolder) {
				nodeArray = toNestedSetsNode.apply(that, [nodeArray, currentChild, newLeft]);
				newLeft = nodeArray[-1 + nodeArray.length].right + 1;
				containsFolder = true;
			}
			else {
				wmsIdArray.push(currentChild.wmsId);
			}
		}		

		// node has NOT at least one child which is a folder
		if (containsFolder) {
			right = newLeft;
		}
		else {
			right = left + 1;
		}

		nodeArray.push({
			"left": left,
			"right": right,
			"name": node.name,
			"isFolder": node.isFolder,
			"wms": wmsIdArray.join(",")
		});
		return nodeArray;
	};

	/**
	 * Returns this tree as nested sets
	 */
	this.toString = function () {
		return $.toJSON(this.exportNestedSets());
	};

	this.exportNestedSets = function () {
		// initialising recursion
		return nodeArray = toNestedSetsNode.apply(this, [[], this.root, 1]);
	};

	/**
	 * Create this tree from nested sets
	 */
	this.fromString = function(str){
		var nodeArray = eval(str);
		this.importNestedSets(nodeArray);
	};

	this.importNestedSets = function (nodeArray) {
		if(nodeArray===undefined || nodeArray.length<1) {
			return false;
		}
		
		//numeric sort function
		var nodeSort = function(a,b){
			return a.left-b.left;
		}

		//sort nodes by their left
		nodeArray.sort(nodeSort);
		
		//apply root node
		var currentNode = this.root;
		currentNode.applyKeys(nodeArray[0]);

		var rights = [nodeArray[0].right];
		for (var i = 1; i < nodeArray.length; i++) {

			//finish all nodes that have no further childs
			while (rights[rights.length-1] < nodeArray[i].left){
				rights.pop();
				currentNode = currentNode.parentNode;
				
				//check if there is an error in data or we have muliple roots				
				if(currentNode == null)
					return false;
			}

			//insert new node
			var newNode = new CustomTreeNode(currentNode);
			newNode.isFolder = true;
			rights.push(nodeArray[i].right);
			newNode.applyKeys(nodeArray[i]);
			currentNode.append(newNode);

			// add WMS
			var wmsIdArray = nodeArray[i].wms;
			for (var j in wmsIdArray) {
				var newWmsNode = new CustomTreeNode(newNode);
				newWmsNode.wmsId = j;
				newWmsNode.name = wmsIdArray[j];
				newNode.append(newWmsNode);
			}
			
			//new node is the one that take next childs
			currentNode = newNode;
		}

		// add WMS for root
		var wmsIdArray = nodeArray[0].wms;
		for (var j in wmsIdArray) {
			var newWmsNode = new CustomTreeNode(this.root);
			newWmsNode.wmsId = j;
			newWmsNode.name = wmsIdArray[j];
			this.root.append(newWmsNode);
		}
		return true;
	}

	var toStringNode = function (nodeArray, node, nextLeft) {
		var left = nextLeft;
		var right;
		var newLeft = nextLeft + 1;
		var nodeCount = node.childNodeList.count();

		// node has children...
		if (nodeCount > 0) {
			for (var i = 0; i < nodeCount; i++) {
				var currentChild = node.childNodeList.get(i);
				// ... recursion
				nodeArray = toStringNode.apply(that, [nodeArray, currentChild, newLeft]);
				newLeft = nodeArray[-1 + nodeArray.length].right + 1;
			}		
			right = newLeft;
		}
		// node is a leaf...
		else {
			right = left + 1;
		}
		nodeArray.push({
			"left": left,
			"right": right,
			"name": node.name,
			"id": node.id
		});
		return nodeArray;
	};
	
	this.empty = function () {
		for (var i = -1 + this.root.childNodeList.count(); i >= 0; i--) {
			var currentChild = this.root.childNodeList.get(i);
			currentChild.remove();
		}
	};
	
	var addMissingWmsToCustomTree = function (anApplicationId, callback) {
		// get available WMS ...
		var queryObj = {
			command:"getWmsByApplication",
			parameters: {
				"applicationId": anApplicationId
			},
			"sessionName": Mapbender.sessionName ? Mapbender.sessionName : parent.Mapbender.sessionName,
			"sessionId": Mapbender.sessionId ? Mapbender.sessionId : parent.Mapbender.sessionId
		};				

		$.post("../php/mod_customTree_server.php", {
			queryObj:$.toJSON(queryObj)
		}, function (json, status) {
			var replyObj = typeof json == "string" ?
					eval('(' + json + ')') : json;

			var wmsIdArray = findAllWmsInTree();
			for (var index in replyObj.data.wmsArray) {
				var found = false;
				for (var j = 0; j < wmsIdArray.length; j++) {
					if (wmsIdArray[j] == index) {
						found = true;
						break;
					}
				}
				if (!found) {
					var wmsNode = new CustomTreeNode();
					wmsNode.name = replyObj.data.wmsArray[index];
					wmsNode.wmsId = index;
					that.root.append(wmsNode);
				}
			}

			that.hasChanged = false;
			callback();
		});
	};

	var findAllWmsInTree = function () {
		return findAllWmsInNode(that.root).sort();
	};

	findAllWmsInNode = function (aNode) {
		if (!aNode.isFolder) {
			return [aNode.wmsId];
		}
		var wmsIdArray = [];
		for (var i = 0; i < aNode.childNodeList.count(); i++) {
			var child = aNode.childNodeList.get(i);
			var newArray = findAllWmsInNode(child);
			wmsIdArray = wmsIdArray.concat(newArray);
		}
		return wmsIdArray;
	};


	var getCustomTreeByJson = function (json) {
		var replyObj = eval('(' + json + ')');

		that.root.name = "JSON";

		var nodeArray = replyObj.data.nodeArray;
		
		that.importNestedSets(nodeArray);

		displayMyTree = new customTreeController(that, {
			contextMenu: options.contextMenu ? true : false,
			droppable: options.droppable ? true : false,
			draggable: typeof options.draggable === "undefined" ? 
				false : options.draggable,
			id: options.id ? 
				options.id : "myTree" + ((new Date()).getTime()),
			skipRootNode: typeof options.skipRootNode === "undefined" ? 
				false : options.skipRootNode,
			leafBehaviour: typeof options.leafBehaviour === "undefined" ? 
				[] : options.leafBehaviour
		});
	};

	var getCustomTreeByApplication = function (applicationName) {
		that.appId = applicationName;
		
		// load a previously saved 
		// customized tree from the database
		var queryObj = {
			command:"getCustomTreeByApplication",
			parameters: {
				"applicationId":applicationName
			},
			"sessionName": Mapbender.sessionName ? Mapbender.sessionName : parent.Mapbender.sessionName,
			"sessionId": Mapbender.sessionId ? Mapbender.sessionId : parent.Mapbender.sessionId
		};				

		$.post("../php/mod_customTree_server.php", {
			queryObj:$.toJSON(queryObj)
		}, function (json, status) {
			var replyObj = typeof json == "string" ?
					eval('(' + json + ')') : json;

			that.root.name = "(" + applicationName + ")";

			var nodeArray = replyObj.data.nodeArray;
			
			that.importNestedSets(nodeArray);

			addMissingWmsToCustomTree(applicationName, function () {
				displayMyTree = new customTreeController(that, {
					contextMenu: options.contextMenu ? true : false,
					droppable: options.droppable ? true : false,
					draggable: typeof options.draggable === "undefined" ? 
						false : options.draggable,
					id: options.id ? 
						options.id : "myTree" + ((new Date()).getTime()),
					skipRootNode: typeof options.skipRootNode === "undefined" ? 
						false : options.skipRootNode,
					leafBehaviour: typeof options.leafBehaviour === "undefined" ? 
						[] : options.leafBehaviour
				});
			});

		});
	};

	if (typeof options === "object" && options.loadFromApplication) {
		getCustomTreeByApplication(options.loadFromApplication);
	}
	else if (typeof options === "object" && options.loadFromJSON) {
		getCustomTreeByJson(options.loadFromJSON);
	}
};

/**
 *  A list of nodes
 */
var CustomTreeChildList = function () {
	this.list = [];
};

CustomTreeChildList.prototype = new List();




/**
 * A single node
 * 
 * @param {Object} parentNode
 */
var CustomTreeNode = function (parentNode) {
	this.name;
	this.isFolder = false;
	this.parentNode = parentNode;
	this.childNodeList = new CustomTreeChildList();

	this.applyKeys = function(obj){
		this.name = obj.name;
	}

	this.afterMove = function () {
	};
	
	this.afterRemove = function () {
	};
	
	this.afterAddChild = function () {
	};

	this.afterSetName = function () {
	};

	this.setName = function (newName) {
		this.name = newName;
		this.afterSetName();
		this.hasChanged();
	};

	this.hasChanged = function () {
		
	};
	
	this.addChild = function () {
		this.childNodeList.add(new CustomTreeNode(this));

		var newChild = this.childNodeList.get(-1);
		newChild.name = "(new node)";
		newChild.isFolder = true;

		this.afterAddChild(newChild);
		this.hasChanged();
		return newChild;
	};
	
	this.getNumberOfChildren = function () {
		var cnt = this.childNodeList.count();
		var numOfChildren = cnt;
		if (cnt > 0) {
			for (var i = 0; i < cnt; i++) {
				numOfChildren += this.childNodeList.get(i).getNumberOfChildren();				
			}
		}
		return numOfChildren;
	};
	
	this.remove = function () {
		this.childNodeArray = [];
		this.parentNode.removeChild(this);
		this.parentNode = null;
		this.afterRemove();
		this.hasChanged();
	};
	
	this.removeChild = function (someNode) {
		for (var i = 0;  i < this.childNodeList.count(); i++) {
			var child = this.childNodeList.get(i);
			if (child == someNode) {
				this.childNodeList.del(i);
				break;
			}
		}
	};
	
	this.move = function (toParent) {
		this.parentNode.removeChild(this);
		toParent.append(this);
		this.afterMove();
		this.hasChanged();
	};
	
	this.append = function (someNode) {
		someNode.parentNode = this;
		this.childNodeList.add(someNode);
		this.isFolder = true;
//		this.afterAppend(someNode);
		this.hasChanged();
	};
	
	this.containsFolder = function () {
		for (var i = 0;  i < this.childNodeList.count(); i++) {
			var child = this.childNodeList.get(i);
			if (child.isFolder) {
				return true;
			}
		}
		return false;	
	};

	this.containsNonFolder = function () {
		var foundNonFolder = false;
		for (var i = 0;  i < this.childNodeList.count(); i++) {
			var child = this.childNodeList.get(i);
			if (!child.isFolder) {
				return true;
			}
			else {
				foundNonFolder = foundNonFolder || child.containsNonFolder();
			}
		}
		return foundNonFolder;	
	};
};
