/**
 * Package: mb_adminpopup
 *
 * Description:
 * 
 * 
 * 
 * 
 * 
 *
 * Files:
 *  - ../plugins/mb_adminpopup.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes,  
 * > e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target,
 * > e_requires, e_url) VALUES ('<gui_id>','mb_adminpopup',7,1,'adminpopup','adminpopup','div',
 * > '','',NULL ,NULL ,NULL ,NULL ,NULL ,
 * > '','','div','../plugins/mb_adminpopup.js','','','','');
 *
 * Maintainer:
 * http://www.mapbender.org/User:Karim_Malhas
 *
 * Parameters:
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License
 * and Simplified BSD license.
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

var $adminpopup = $(this);

var AdminPopup = function(o){
	// create dialog window
	$(this).dialog({
        	autoOpen: false,
        	position: 'center',
        	width: 300,
       		height: 300,
        	buttons: {
                	"Schliessen": function(){
                       		$adminpopup.dialog('close');
               		}
        	}
	});
	// push iframe AdminFrame into dialog
	$adminpopup.append($('#AdminFrame'));

	// show dialog onClick on anchor
	$('a').bind('click',function(){ $adminpopup.dialog('open')});
};


Mapbender.events.init.register(function(){
     $adminpopup.mapbender(new AdminPopup(options));
});

