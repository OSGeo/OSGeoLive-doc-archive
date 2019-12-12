/**
 * Package: List
 * 
 * Description:
 * A List object is an array of arbitrary objects with additional methods.
 * 
 * Files:
 *  - lib/list.js
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
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
