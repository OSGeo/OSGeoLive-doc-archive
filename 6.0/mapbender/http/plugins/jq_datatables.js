/**
 * Package: jq_datatables
 *
 * Description:
 * 
 * Includes the jQuery plugin datatables, use like this
 * 
 * $(selector).datatables(options)
 * 
 * Files:
 *  - http/plugins/jq_datatables.js
 *  - http/extensions/dataTables-1.5/
 *  
 * SQL:
 * > INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, 
 * > e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, 
 * > e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, 
 * > e_mb_mod, e_target, e_requires, e_url) VALUES('gui1','jq_datatables',1,1,
 * > 'Includes the jQuery plugin datatables','','','','',NULL ,NULL ,NULL ,
 * > NULL ,NULL ,'','','','../plugins/jq_datatables.js',
 * > '../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js','','',
 * > 'http://www.datatables.net/');
 * > 
 * > INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, 
 * > var_value, context, var_type) VALUES('gui1', 'jq_datatables', 
 * > 'defaultCss', 
 * > '../extensions/dataTables-1.5/media/css/demo_table_jui.css', '' ,
 * > 'file/css');
 *
 * Help:
 * http://www.datatables.net/
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

return $(this).dataTable(options);

// TODO: i18n