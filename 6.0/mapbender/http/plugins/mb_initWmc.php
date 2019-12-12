<?php 
/**
 * Package: initWmc
 *
 * Description:
 * Mapbender is initialized with a WMC document given as parameter load_wmc_id 
 * 
 * Files:
 *  - http/plugins/mb_initWmc.php
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('<appId>','initWmc',
 * > 2,1,'init Wmc','initWmc','div','','',1,1,2,2,5,'','',
 * > 'div','../plugins/mb_initWmc.php','',
 * > 'mapframe1','','http://www.mapbender.org/index.php/InitWMC');
 * >
 * 
 * Help:
 * http://www.mapbender.org/InitWMC
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * http://www.mapbender.org/User:Verena_Diewald
 * 
 * 
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */
require_once(dirname(__FILE__) . "/../classes/class_wmc.php");

$wmcId = Mapbender::session()->get("mb_myWmc");
$wmcAction = Mapbender::session()->get("mb_myWmc_action");
?>
var myTarget = options.target ? options.target[0] : "mapframe1";
var myId = options ? options.id : "initWmc";

var InitWmcApi = function () {

	var that = this;
	this.events = {
		done: new Mapbender.Event()
	};
	this.init = function () {
		Mapbender.events.beforeInit.register(function () {
			<?php 
			function initWmcById ($wmcId,$wmcAction) {
				$wmc = new wmc();
			     
			    $wmc->createFromDb($wmcId);
			    
			    $updatedWMC = $wmc->updateUrlsFromDb();
			    $wmc->createFromXml($updatedWMC);
			    
			    $jsArray = $wmc->toJavaScript();
			    
				if ($jsArray) {
					$jsString = implode("", $jsArray);
					echo $jsString;
				}
				else {
					$e = new mb_exception("WMC could not be loaded.");
				}
			}
			if($wmcId != "") {
				initWmcById($wmcId,$wmcAction);
			}
			?>
			that.events.done.trigger({
				id: parseInt("<?php echo $wmcId;?>", 10),
				extensionData: restoredWmcExtensionData
			});
		});
		
	};
	Mapbender.events.initMaps.register(function () {
		that.init();
	});
};
$("#" + myId).mapbender(new InitWmcApi());

