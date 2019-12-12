/**
 * Package: ol_wmsGetFeatureInfo
 *
 * Description:
 * 
 * 
 * Files:
 *  - http/plugins/ol_wmsGetFeatureInfo.js
 *
 * SQL:
 *
 * Help:
 * http://www.mapbender.org/ol_wmsGetFeatureInfo
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

var $featureInfo = $(this);

var WmsGetFeatureInfoApi = function () {
	var that = this;
	var ctrl;
	
	var activate = function (map, button) {
		button.activate();
		ctrl = new OpenLayers.Control.MapbenderWMSGetFeatureInfo({
			drillDown: true,
			eventListeners: {
                beforegetfeatureinfo: function () {
				    OpenLayers.ProxyHost = "../extensions/ext_featureInfoTunnel.php?url=";
                },
                getfeatureinfo: function(event) {
                    map.addPopup(new OpenLayers.Popup.FramedCloud(
                        "chicken", 
                        map.getLonLatFromPixel(event.xy),
                        null,
                        event.text,
                        null,
                        true
                    ));
                    deactivate(map, button);
                }
            }
		});
		map.addControl(ctrl);
		ctrl.activate();
	};
	
	var deactivate = function (map, button) {
		ctrl.deactivate();
		button.deactivate();
		map.removeControl(ctrl);
	};
		
	var init = function () {
		options.$target.each(function () {
			var map = $(this).mapbender();
			map.mapbenderEvents.layersAdded.register(function () {
				var button = new OpenLayers.Control.Button({
					displayClass: options.cssClass,
					trigger: function () {
						if (!this.active) {
							activate(map, button);
						}
						else {
							deactivate(map, button);
						}
					}
				});
				button.setMap(map);
				that.buttons = [button];
			});
		});
	};
	
	init();
};

$featureInfo.mapbender(new WmsGetFeatureInfoApi());