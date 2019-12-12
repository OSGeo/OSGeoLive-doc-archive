/**
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

/**
 * @class Represents a div tag. May be located in any frame of Mapbender.
 * 
 * @deprecated
 * @constructor
 * @param aTagName {String} the name of the tag
 * @param aFrameName {String} the name of frame where the div tag is being created
 * @param aStyle {Object} an object containing a set of name value pairs, like
 *                        {position:absolute,top:30,z-Index:30}
 */
function DivTag (aTagName, aFrameName, aStyle, appendToThisDomElement) {
	/**
	 * @ignore
	 */
	this.exists = function () { 
		return (rootNode.getElementById(tagName)) ? true : false;
	};
	
	/**
	 * @ignore
	 */
	this.getTag = function() {
		return rootNode.getElementById(tagName);
	};
	
	/**
	 * @private
	 */
	var determineRootNode = function () {
		node = document;
		if (frameName !== "") {
			if (checkFrame()) {node = window.frames[frameName].document;}
			else {var e = new Mb_exception("frame "+frameName+" doesn't exist.");}
		}
		return node;	
	};
	
	/**
	 * @private
	 */
	var toCamelCase = function(aString) {
		var newString = "";
		for (var i = 0; i < aString.length; i++) {
			if (aString.substr(i,1) != "-") {
				newString += aString.substr(i,1); 
			}
			else {
				i++;
				newString += aString.substr(i,1).toUpperCase();
			}
		}
		return newString;
	};
	
	/**
	 * @private
	 */
	var setStyle = function () {
		if (that.exists()) {
			var node = rootNode.getElementById(tagName);
			node.setAttribute("style", "");
			
			for (var attr in tagStyle) {
				if (typeof(tagStyle[attr]) != "function" && typeof(tagStyle[attr]) != "object") {
					var evalString = "node.style."+toCamelCase(attr)+" = \"" + tagStyle[attr] + "\";"; 
					eval(evalString);				
				}
			}
		}
	};
	
	/**
	 * @private
	 */
	var create = function () {
		if (!that.exists()) {
			var divTag = rootNode.createElement("div");
			var divTagAppended;
			if (targetDomElement !== null) {
				divTagAppended = targetDomElement.appendChild(divTag);
			}
			else {
				divTagAppended = rootNode.getElementsByTagName("body")[0].appendChild(divTag);
			}
			divTag.id = tagName;
		}
		else {
			that.clean();
		}
		setStyle();
	};

	/**
	 * @private
	 */
	var checkFrame = function () {
		if (frameName !== "") {
			return (typeof(window.frames[frameName]) != 'undefined');
		}
		return true;
	};
	
	var that = this;
	var tagName = aTagName;
	var frameName = aFrameName;
	var rootNode = determineRootNode();
	var targetDomElement = appendToThisDomElement ? appendToThisDomElement : null;
	var tagStyle = aStyle;

	create();
}
/**
 * Writes a text into the div tag, while removing existing content.
 * 
 * @param {String} someText the text that is inserted into the tag.
 */
DivTag.prototype.write = function (someText) {
	if (this.exists()) {
		this.getTag().innerHTML = someText;
	}
};

/**
 * Deletes the div tag content.
 */
DivTag.prototype.clean = function () {
	this.write("");
};
