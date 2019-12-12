// check element vars
try{
	if (open_tab){
		open_tab = Number(open_tab);

		if (isNaN(open_tab)) {
			var e = new Mb_warning("mod_tab.js: tab_init: open_tab must be a number or an empty string.");
		}
	}
}
catch(e){
	var z = new Mb_warning("mod_tab.js: tab_init: open_tab is not set.");
	open_tab = "";
}

var tabs;

eventLocalize.register(function () {
	localizeTabs();
});

eventInit.register(function () {
	tab_init();
//	localizeTabs();

});

function localizeTabs() {
	mb_ajax_json("../php/mod_tab_messages.php", {
		"sessionName": Mapbender.sessionName,
		"sessionId": Mapbender.sessionId
        }, function(obj, status){
		tabs.setTitles(obj);
	});
}
 
function tab_init(){
	var tabNode = document.getElementById("tabs");
	var obj = tabNode.style;

	// generate a new tab array
	tabs = new VerticalTabArray(tab_style);

	// add the tabs from element vars
	for (var i = 0; i < tab_ids.length; i++){
		tabs.addTab(tab_ids[i], tab_prefix + tab_titles[i], tab_frameHeight[i]);
	}

	if (open_tab !== "") {
		tabs.openTab(tabs.get(open_tab).module);
	}
	$(tabNode).children("div").hover(function () {
		$(this).addClass("ui-state-hover");
	}, function () {
		$(this).removeClass("ui-state-hover");
	});

}

function tab_open(elementName) {
  // show the desired tab
	tabs.openTab(elementName);
}

/**
 * @class A single vertical tab
 * 
 * @constructor
 * @param {String} id the ID of the GUI element that will be displayed within the tab
 * @param {String} title the header of the tab
 * @param {Integer} frameHeight the height of the frame containing the GUI element
 * @param {Integer} tabWidth the width of a tab (NOT the frame)
 * @param {Integer} tabHeight the height of a tab (NOT the frame)
 * @param {String} tabStyle A string with a CSS (example: position:absolute;visibility:visible;border: 1px solid white;font-size:12;color:#343434;background-color:#CFD2D4;cursor:pointer;)
 * @param {Integer} number the index of the current tab in a {@link VerticalTabArray}.
 */
var VerticalTab = function (id, title, frameHeight, tabWidth, tabHeight, tabStyle, number) {
	
	/**
	 * Sets the attributes of the tabs DOM node.
	 * 
	 * @private
	 * @param {String} title the header of the tab
	 * @param {Integer} frameHeight the height of the frame containing the GUI element
	 * @param {Integer} tabWidth the width of a tab (NOT the frame)
	 * @param {Integer} tabHeight the height of a tab (NOT the frame)
	 * @param {String} tabStyle A string with a CSS (example: position:absolute;visibility:visible;border: 1px solid white;font-size:12;color:#343434;background-color:#CFD2D4;cursor:pointer;)
	 * @param {Integer} number the index of the current tab in a {@link VerticalTabArray}.
	 * 
	 */
	var setNodeAttributes = function(title, frameHeight, tabWidth, tabHeight, tabStyle, number) {
	
		node.id = "tabs_" + that.module;
		
		//set css class
		node.className = "verticalTabs ui-state-default";

		//mandatory style entries
		node.style.position = "absolute";
		node.style.width = parseInt(tabWidth, 10) + "px";
		node.style.height = parseInt(tabHeight, 10) + "px";
		node.style.top = parseInt(number * tabHeight, 10) + "px";
		
		$(node).click(function() {
			tabs.toggleTab(that.module);
		});

		// tab header
		node.innerHTML = title;
	};

	/**
	 * Returns the DOM node of this tab.
	 *
	 * @return the DOM node of this tab.
	 * @type DOMNode
	 */
	this.getNode = function() {
		return node;
	};
	
	/**
	 * The ID of the GUI element that will be displayed within the tab.
	 */
	this.module = id;

	/**
	 * The height of the frame containing the GUI element.
	 */
	this.height = frameHeight;
	
	/**
	 * While a tab is opened or closed, the value is false.
	 */
	this.animationFinished = true;

	/**
	 * The DOM node of this tab.
	 *
	 * @private
	 */
	var node = document.createElement("div");
	var that = this;
	
	setNodeAttributes(title, frameHeight, tabWidth, tabHeight, tabStyle, number);
};
	
/**
 * An array of vertical tabs, behaving like an accordion
 *
 * @extends List
 * @param {String} cssString A string with a CSS (example: position:absolute;visibility:visible;border: 1px solid white;font-size:12;color:#343434;background-color:#CFD2D4;cursor:pointer;)
 */	
var VerticalTabArray = function (cssString) {
	
	/**
	 * Adds a new tab to the Array.
	 *
	 * @param {String} id the ID of the GUI element that will be displayed within the tab
	 * @param {String} title the header of the tab
	 * @param {Integer} height the height of the frame containing the GUI element
	 */
	this.addTab = function(id, title, height) {
		var tab = new VerticalTab(id, title, height, tabWidth, tabHeight, tabStyle, this.count());
		this.add(tab);

		document.getElementById(id).style.visibility = 'hidden';
		document.getElementById(id).style.display = 'none';

		// append the new tab
		rootNode.appendChild(this.get(-1).getNode());
	};
	
	/**
	 * Removes a tab from the Array.
	 *
	 * @param {String} id the ID of the GUI element within the tab.
	 */
	this.delTab = function(id) {
		var index = getIndexById(id);
		
		if (index !== null) {
			// delete the DOM node
			rootNode.removeChild(this.get(index).getNode());

			// delete the tab
			this.del(index);
			
			// move the other tabs (below the removed tab) up
			for (var i = index; i < this.count(); i++) {
				var currentNode = this.get(i).getNode();
				
				// parseInt removes "px"
				var currentTop = parseInt(currentNode.style.top, 10);
				currentNode.style.top = currentTop - tabHeight;
			}			
		}
	};
		
	/**
	 * Opens a tab specified by the module Id.
	 *
	 * @param {String} id the ID of the GUI element within the tab.
	 */
	this.toggleTab = function(id) {
		$("#tabs_" + id).addClass("ui-state-active").siblings().removeClass("ui-state-active");

		// if no tab is currently opening or closing
		if (isAnimationFinished()) {
			for (var i=0; i < this.count(); i++) {
				hideFrame(this.get(i).module);
			}
			// if the opened tab is the active tab, close it
			if (id === activeTabId) {
				closeTab(activeTabId);
			}
			//otherwise
			else {
				// show the desired tab
		  	activeTabId = id;
				startAnimation("open");
			}
		}
		else {
			var e = new Mb_warning("mod_tab.js: could not activate tab, opening or closing in progress!");
		}
	};
	
	/**
	 * Sets the titles of each single tab after l10n
	 * 
	 * @param {Object} obj an array containing objects with id and title
	 */
	this.setTitles = function (obj) {
		for (var i = 0; i < this.count(); i++) {
			for(var j=0; j<obj.length; j++){
				if (this.get(i).module == obj[j].id) {
					this.get(i).getNode().innerHTML = tabPrefix + obj[j].title;
				}
			}
		}		
	}
	/**
	 * Returns the absolute coordinates of tab by the module ID
	 * 
	 * @param {String} id the ID of the GUI element within the tab.
	 * @return {String} String with "left,top,right,bottom"
	 */
	 
	 this.getCoords = function(id) {
	 	var coords=[];
	 	//get indixes
	 	if (activeTabId) {
			var indexOfOpeningTab = getIndexById(activeTabId);
		}
		var index = getIndexById(id);
	 	
	 	//left
	 	coords[0] = tabLeftOffset;
	 	//top
	 	coords[1] = tabTopOffset + index*tabHeight + (activeTabId&&indexOfOpeningTab<index?this.get(indexOfOpeningTab).height:0);
	 	//right
	 	coords[2] = coords[0] + tabWidth;
	 	//bottom
	 	coords[3] = coords[1] + (id==activeTabId?this.get(indexOfOpeningTab).height+tabHeight:tabHeight);
	 	
	 	return coords.join(",");
	 };

	/**
	 * Animated opening and closing of the tab with the given id.
	 * Needs to be public because it is called via "setInterval". 
	 *
	 * @param {String} openOrClose a string with the values "open" or "close".
	 */
	this.animate = function(openOrClose) {

		for (var i=0; i < this.count(); i++) {
			
			if (this.get(i).animationFinished === false) {

				//The 'top' position of the i-th tab after the animation
				var currentTabNewTop = i * tabHeight;
	
				if (openOrClose == 'open') {
					var indexOfOpeningTab = getIndexById(activeTabId);
					
					// move the lower tabs down by the height of the opening tab
					if (indexOfOpeningTab !== null && i > indexOfOpeningTab) {
						currentTabNewTop += this.get(indexOfOpeningTab).height;
					}
				}	
				//The current 'top' position of the i-th tab
				//(parseInt removes 'px')
				var currentTabCurrentTop = parseInt(this.get(i).getNode().style.top, 10);
				
				// animation is finished
				if (currentTabCurrentTop == currentTabNewTop) {
					this.get(i).animationFinished = true;
				}	
				// animation not yet finished, move the tab down
				else if (currentTabCurrentTop < currentTabNewTop) {
					var pixel = Math.min(pixelPerIteration, currentTabNewTop - currentTabCurrentTop);
					this.get(i).getNode().style.top = (currentTabCurrentTop + pixel) + "px";
				}
				// animation not yet finished, move the tab up
				else if (currentTabCurrentTop > currentTabNewTop) {
					var pixel = Math.min(pixelPerIteration, currentTabCurrentTop - currentTabNewTop);
					this.get(i).getNode().style.top = (currentTabCurrentTop - pixel) + "px";
				}
				else {
					var e = new Mb_exception("mod_tab.js: animate: unknown state for tab "+ i + " (currentTop: "+currentTabCurrentTop+", newTop:"+currentTabNewTop+")");
				}
			}
		}
		// check if the animation is finished
		if (isAnimationFinished()) {
			stopAnimation();
			if (openOrClose == "open") {
				showFrame(activeTabId);
			}
		}
	};

	/**
	 * Returns the index of the vertical tab with a given id 
	 *
	 * @private
	 * @param {String} id the ID of the GUI element within the tab.
	 */
	var getIndexById = function(id) {
		for (var i = 0; i < that.count(); i++) {
			if (that.get(i).module == id) {
				return i;
			}
		}
		var e = new Mb_exception("mod_tab.js: getIndexById: ID '"+id+"' not found.");
		return null;
	};
	
	/**
	 * Closes a tab.
	 * 
	 * @private
	 * @param {String} id the ID of the GUI element within the tab.
	 */
	var closeTab = function(id) {
		if (id !== null) {
			hideFrame(id);
			activeTabId = null;
			startAnimation("close");
		}		
	};
	
	/**
	 * Opens a tab.
	 *
	 * @public
	 * @param {String} id the ID of the GUI element within the tab.
	 */
	this.openTab = function(id) {
		if (id !== null && activeTabId != id) {
  		// if no tab is currently opening or closing
  		if (isAnimationFinished()) {
  			for (var i=0; i < this.count(); i++) {
  				hideFrame(this.get(i).module);
  			}
  		}
		activeTabId = id;
		$("#tabs_" + id).addClass("ui-state-active").siblings().removeClass("ui-state-active");
    	startAnimation("open");
		}
	};

	/**
	 * Hides a frame within a tab (before closing the tab).
	 *
	 * @private
	 * @param {String} id the ID of the GUI element within the tab.
	 */
	var hideFrame = function(id) {
		var index = getIndexById(id);
		if (index !== null) {
			var obj = document.getElementById(id);
			obj.style.visibility = 'hidden';
			//try to apply for childs of horizontal tabs
			try{
				if(obj.tabs)
					document.getElementById(obj.tabs[obj.activeTab].id).style.visibility = 'hidden';
		}
			catch(e){}
		}
	};
	
	/**
	 * Shows a frame within a tab (after opening the tab).
	 *
	 * @private
	 * @param {String} id the ID of the GUI element within the tab.
	 */
	var showFrame = function(id) {
		var index = getIndexById(id);
		if (index !== null) {
			var obj = document.getElementById(id);
			$(obj).addClass("ui-widget-content");
			var newpos = ((index+1) * tabHeight) + parseInt(tabTopOffset, 10);
			//try to apply for childs of horizontal tabs
			try{
				if(obj.tabs){
					activeTab = document.getElementById(obj.tabs[obj.activeTab].id).style;
					activeTab.visibility = 'visible';
					activeTab.top = ((newpos + 1) + obj.tab_height) + "px";
					activeTab.left = (tabLeftOffset) + "px";
					activeTab.width = tabWidth;
					activeTab.height = (parseInt(that.get(index).height, 10) - 2) - obj.tab_height;
				}
			}
			catch(e){
				new Mapbender.Exception(e.message);
			}
			obj=obj.style;
			obj.top = (newpos + 1) + "px";
			obj.left = (tabLeftOffset) + "px";
			obj.width = tabWidth + "px";
			obj.height = (parseInt(that.get(index).height, 10) - 2) + "px";
			obj.visibility = 'visible';
			obj.display = 'block';
		}
	};

	/**
	 * Starts the animation of the opening and closing tabs
	 *
	 * @private
	 * @param {String} openOrClose a string with the values "open" or "close".
	 */
	var startAnimation = function(openOrClose) {
		for (var i = 0; i < that.count(); i++) {
			that.get(i).animationFinished = false;
		}
		tabInterval = setInterval(function(){
			tabs.animate(openOrClose);
		}, 10);
	};
	
	/**
	 * Checks if the animation of the opening and closing tabs is finished.
	 *
	 * @private
	 */
	var isAnimationFinished = function() {
		for (var i = 0; i < that.count(); i ++) {
			if (that.get(i).animationFinished === false) {
				return false;
			}
		}
		return true;
	};

	/**
	 * Stops the animation of the opening and closing tabs
	 *
	 * @private
	 */
	var stopAnimation = function() {
		clearInterval(tabInterval);		
	};

	this.list = [];
	var that = this;

	/**
	 * The DOM node of the tab array.
	 */
	var rootNode = document.getElementById("tabs");

	/**
	 * The ID of the currently open tab. If no tab is open, the value is NULL
	 */
	var activeTabId = null;

	/**
	 * Number of pixel that a tab moves while opening or closing.
	 * @private
	 */
	var pixelPerIteration = 20;
	
	/**
	 * Used for the 'setInterval' call of 'this.animate'
	 */
	var tabInterval;


	var tabTopOffset = parseInt(rootNode.style.top, 10);
	var tabLeftOffset = parseInt(rootNode.style.left, 10);
	var tabWidth = parseInt(rootNode.style.width, 10);
	var tabHeight = parseInt(rootNode.style.height, 10);
	var tabStyle = cssString;

	var tabPrefix = tab_prefix || '';
	var styleObj = new StyleTag();
	styleObj.addClass("verticalTabs", tabStyle);
};

VerticalTabArray.prototype = new List();
