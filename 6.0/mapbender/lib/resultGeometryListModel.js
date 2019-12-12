/**
 * Package: ResultGeometryListModel
 * 
 * Description:
 * A feature collection of a result geometry list.
 * 
 * Files:
 *  - lib/resultGeometryListModel.js
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

/**
 * Constructor: ResultGeometryListModel
 */
var ResultGeometryListModel = function () {
	var geomArray = new GeometryArray();

	/**
	 * Property: events
	 * 
	 * Description:
	 * added   -  the event is fired after features are added
	 * deleted -  the event is fired after features have been deleted
	 * updated -  the event is fired after features have been updated
	 * cleared -  the event is fired after all features have been deleted
	 */
	this.events = {
		added: new Mapbender.Event(),
		deleted: new Mapbender.Event(),
		updated:  new Mapbender.Event(),
		cleared: new Mapbender.Event()
	};

	/**
	 * Method: addFeatureCollection
	 * 
	 * Description:
	 * Add a feature collection to the geometry array
	 * 
	 * Parameters:
	 * geoJSON      - a feature collection as GeoJSON string
	 */
	this.addFeatureCollection = function (geoJSON) {
		var oldLength = geomArray.count();
		if (geomArray.importGeoJSON(geoJSON)) {
			var featureCollection = [];
			for (var i = oldLength; i < geomArray.count(); i++) {
				featureCollection.push({
					index: i,
					feature: geomArray.get(i)
				});
			}
			
			this.events.added.trigger({
				featureCollection: featureCollection
			});
			return true;
		}
		return false;
	};

	/**
	 * Method: addFeature
	 * 
	 * Description:
	 * Add a feature to the geometry array
	 * 
	 * Parameters:
	 * geoJSON      - a feature as GeoJSON string
	 */
	this.addFeature = function (geoJSON) {
		return this.addFeatureCollection(geoJSON);
	};
	
	/**
	 * Method: deleteFeatureCollection
	 * 
	 * Description:
	 * Delete the feature collection (= clear)
	 */
	this.deleteFeatureCollection = function () {
		geomArray = new GeometryArray();
		this.events.cleared.trigger({});
		return true;
	};
	
	/**
	 * Method: deleteFeature
	 * 
	 * Description:
	 * Delete a feature from the geometry array
	 * 
	 * Parameters:
	 * index      - an index of the geometry array
	 */
	this.deleteFeature = function (index) {
		if (geomArray.count() <= index) {
			return false;
		}
		var feature = Mapbender.cloneObject(this.get(index));
		this.del(index);
		this.events.deleted.trigger({
			index: index,
			feature: feature
		});
		return true;
	};
	
	/**
	 * Method: updateFeature
	 * 
	 * Description:
	 * Replace a feature in the geometry array
	 * 
	 * Parameters:
	 * index      - index of the feature that is replaced
	 * geoJSON    - a feature as GeoJSON string
	 */
	this.updateFeature = function (index, geoJson) {
		if (geomArray.count() <= index) {
			return false;
		}
		var newGeomArray = new GeometryArray();
		if (!newGeomArray.importGeoJSON(geoJSON)) {
			return false;
		}
		var oldFeature = geomArray.get(index);
		oldFeature = newGeomArray.get(0);
		this.events.updated.trigger({
			index: index,
			feature: oldFeature
		});
		return true;
	};
	
	/**
	 * Method: setFeatureProperty
	 * 
	 * Description:
	 * Set a property of a feature in the geometry array
	 * 
	 * Parameters:
	 * index      - index of the feature
	 * pName      - property name
	 * pValue     - new property value
	 */
	this.setFeatureProperty = function (index, pName, pValue) {
		if (geomArray.count() <= index) {
			return false;
		}
		geomArray.get(index).e.setElement(pName, pValue);
		this.events.updated.trigger({
			index: index,
			feature: geomArray.get(index)
		});
		return true;			
	};
	
	/**
	 * Method: getFeatureProperty
	 * 
	 * Description:
	 * Get a property of a feature in the geometry array
	 * 
	 * Parameters:
	 * index      - index of the feature
	 * pName      - property name
	 */	
	 this.getFeatureProperty = function (index, pName) {
		if (geomArray.count() <= index) {
			return null;
		}
		if(geomArray.get(index).e.getElementValueByName(pName) === false) {
			var featureProperty = "";
		}
		else {
			var featureProperty = geomArray.get(index).e.getElementValueByName(pName);
			
		}
		return featureProperty;
	};
	
	/**
	 * Method: getFeature
	 * 
	 * Description:
	 * Get a feature in the geometry array
	 * 
	 * Parameters:
	 * index      - index of the feature
	 */	
	 this.getFeature = function (index) {
		if (geomArray.count() <= index) {
			return null;
		}
		return geomArray.get(index);
	};

	
	this.toString = function(){
		return geomArray.toString();
	};
};
