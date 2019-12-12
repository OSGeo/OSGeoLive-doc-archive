<?php
# $Id:
# add vendor-specific parameters to MapRequest and FeatureInfoRequests
# http://www.mapbender.org/index.php/mod_add_vendorspecific.php
#
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
?>
mb_registerVendorSpecific("mod_vs_init()");
function mod_vs_init(){   
   var re = "vendorspecific=<?php echo Mapbender::session()->get('mb_user_name') ?>";
   return re;
}

