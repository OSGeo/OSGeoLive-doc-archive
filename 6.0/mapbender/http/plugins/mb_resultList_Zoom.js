/**
 * Package: resultList_Zoom
 *
 * Description:
 * A zoom functionality for a mapbender result list
 * 
 * Files:
 *  - http/plugins/mb_resultList_Zoom.js
 *
 * SQL:
 * > INSERT INTO gui_element(fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, 
 * > e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, 
 * > e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) VALUES('<app_id>',
 * > 'resultList_Zoom',2,1,'zoom functionality for resultList','','div','','',NULL,NULL,NULL,NULL,NULL,
 * > '','','','../plugins/mb_resultList_Zoom.js','','resultList,mapframe1','',
 * > 'http://www.mapbender.org/ResultList'); 
 * > 
 *
 * Help:
 * http://www.mapbender.org/resultList_Zoom
 *
 * Maintainer:
 * http://www.mapbender.org/User:Verena_Diewald
 * 
 * Parameters:
 * 
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */

Mapbender.events.init.register(function(){
  Mapbender.modules[options.target[0]].rowclick.register(function(row){
    var me = Mapbender.modules[options.target[0]];
    var modelIndex = $(row).data("modelindex");
    var feature = me.model.getFeature(modelIndex);

    if (options.maxHighlightedPoints > 0 && feature.getTotalPointCount() > options.maxHighlightedPoints) {
      feature = feature.getBBox4();
    }
    
    var bbox = feature.getBBox();
    var bufferFloat = parseFloat(me.WFSConf.g_buffer);
    var buffer = new Point(bufferFloat,bufferFloat);
    bbox[0] = bbox[0].minus(buffer);
    bbox[1] = bbox[1].plus(buffer);

    var map = Mapbender.modules[options.target[1]];

    map.calculateExtent(
      new Mapbender.Extent(bbox[0], bbox[1])
    );
    map.setMapRequest();
  });
});
