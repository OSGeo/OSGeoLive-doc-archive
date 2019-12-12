/**
 * Package: Sandclock
 *
 * Description:
 * Displays an arbitrary sandclock image on a map
 *
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, 
 * > e_comment, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closeTag, e_js_file, 
 * > e_mb_mod, e_target, e_requires) VALUES ('<gui_id>', 'sandclock', '0002', 
 * > '1', 'displays a sand clock while waiting for requests', 'div', '', 
 * > '', '', '', '', '', '', '', '', 'div', 'mod_sandclock.php', '', 
 * > 'mapframe1', '');
 * > 
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES ('<gui_id>', 'sandclock', 
 * > 'mod_sandclock_image', '../img/sandclock.gif', 
 * > 'define a sandclock-image', 'var');
 * >
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('<gui_id>', 'sandclock', 
 * > 'blockElement', '0', '' ,'var');
 * 
 * Help:
 * http://www.mapbender.org/Sandclock
 * 
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * mod_sandclock_img - *[optional]* the filename and path to the sandclock image
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
 
var Sandclock = function (options) {
	var that = this;

	//
	// check if target is set correctly
	for (var i in options.target) {
		if (!Mapbender.modules[options.target[i]]) {
			new Mb_exception(
				"Target " + options.target[i] + " not found by " + options.id
			);
		}
	}

	//
	// sandclock image
	//
	if (!options.mod_sandclock_image) {
		options.mod_sandclock_image = "../img/sandclock.gif";
	}

	var mod_sandclock_img = new Image();
	mod_sandclock_img.src = options.mod_sandclock_image;
	
	//
	// block the html element with a css shadow
	//
	if (typeof options.blockElement === "undefined") {
		options.blockElement = 0;
	}
	
	//
	// if target objects are maps, bind sandclock to its repaint event
	//
	for (var i in options.target) {
		(function () {
			var nodeId = options.target[i];
			if (typeof Mapbender.modules[nodeId].afterMapRequest === "object") {
				Mapbender.modules[nodeId].afterMapRequest.register(function (obj) {
					that.show(nodeId, {
						mapId: obj.myMapId,
						blockElement: options.blockElement
					});
				});
			}
		})();
	}

	var aktiv;
	
	/** 
	 * Method: show
	 *
	 * Displays the sandclock
	 */
	this.show = function (nodeId, arg) {
		var mapId = arg.mapId;

		var $clock = $("#" + nodeId + "_sandclock");
		
		if ($clock.size() === 0) {
			//create Box Elements
			$(
				"<div id='" + nodeId + "_sandclock'></div>"
			).css({
				position: "absolute",
				top: "0px",
				left: "0px",
				width: "100%",
				height: "100%",
				overflow: "hidden",
				zIndex: 1000,
				visibility: "visible"
			}).appendTo($("#" + nodeId));
		}
		$clock.css({
			zIndex: 1000
		}).html(
			"<img style='position:absolute; top:50%; left:50%' src='" + 
			mod_sandclock_img.src+"'>"
		);

		if (arg.blockElement) {
			$clock.addClass("ui-widget-overlay");
		}
	
		//
		// if mapId is not given, the sandclock has to be turned off manually
		// by calling hide(). Usually this is done in a callback 
		// function.
		//
		if (typeof mapId !== "undefined") {
			aktiv = setTimeout(function () {
				that.show(nodeId, {
					mapId: mapId,
					blockElement: arg.blockElement
				});
			},10);
			var mapIdArray = mapId.split(",");
			var complete = true;
			var map = Mapbender.modules[nodeId];
			
			for (var i = 0; i < mapIdArray.length && complete; i++) {
				var currentMapId = mapIdArray[i];
				var myDoc = map.getDomElement().ownerDocument;
				if(myDoc.getElementById(currentMapId) && 
					!myDoc.getElementById(currentMapId).complete) {
					complete = false;
				}
			}
			if (complete) {
				clearTimeout(aktiv);
				that.hide(nodeId, map);
			} 
		}
	};
	
	/**
	 * Method: hide
	 *
	 * Hides the sandclock
	 */
	this.hide = function (nodeId) {
		$("#" + nodeId + "_sandclock").css({
			zIndex: 1
		}).removeClass("ui-widget-overlay").empty();
	};
};

Mapbender.events.init.register(function () {
	$.extend(Mapbender.modules[options.id], new Sandclock(options));
});
