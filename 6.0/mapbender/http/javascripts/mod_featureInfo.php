<?php
# $Id: mod_featureInfo.php 8335 2012-04-27 12:09:17Z astrid_emde $
# http://www.mapbender.org/index.php/mod_featureInfo.php
# Copyright (C) 2002 CCGIS 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
include '../include/dyn_js.php';
//defaults for element vars
?>
var ignoreWms = typeof ignoreWms === "undefined" ? [] : ignoreWms;

if(typeof(featureInfoLayerPopup)==='undefined')
	var featureInfoLayerPopup = 'false';
if(typeof(featureInfoPopupHeight)==='undefined')
	var featureInfoPopupHeight = '200';
if(typeof(featureInfoPopupWidth)==='undefined')
	var featureInfoPopupWidth = '270';
if(typeof(featureInfoPopupPosition)==='undefined')
	var featureInfoPopupPosition = 'center';

var mod_featureInfo_elName = "<?php echo $e_id;?>";
var mod_featureInfo_frameName = "";
var mod_featureInfo_target = "<?php echo $e_target[0]; ?>";
var mod_featureInfo_mapObj = null;

var mod_featureInfo_img_on = new Image(); mod_featureInfo_img_on.src =  "<?php  echo preg_replace("/_off/","_on",$e_src);  ?>";
var mod_featureInfo_img_off = new Image(); mod_featureInfo_img_off.src ="<?php  echo $e_src;  ?>";
var mod_featureInfo_img_over = new Image(); mod_featureInfo_img_over.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";

eventInit.register(function () {
	mb_regButton(function init_featureInfo1(ind){
		mod_featureInfo_mapObj = getMapObjByName(mod_featureInfo_target);
	
		mb_button[ind] = document.getElementById(mod_featureInfo_elName);
		mb_button[ind].img_over = mod_featureInfo_img_over.src;
		mb_button[ind].img_on = mod_featureInfo_img_on.src;
		mb_button[ind].img_off = mod_featureInfo_img_off.src;
		mb_button[ind].status = 0;
		mb_button[ind].elName = mod_featureInfo_elName;
		mb_button[ind].fName = mod_featureInfo_frameName;
		mb_button[ind].go = function () {
                        if ($.extend(mod_featureInfo_mapObj).defaultTouch) {
                            $.extend(mod_featureInfo_mapObj).defaultTouch.deactivate();
                        }
			mod_featureInfo_click();
		};
		mb_button[ind].stop = function () {
			mod_featureInfo_disable();
                        if ($.extend(mod_featureInfo_mapObj).defaultTouch) {
                            $.extend(mod_featureInfo_mapObj).defaultTouch.activate();
                        }
		};
	});
});
function mod_featureInfo_click(){   
	var el = mod_featureInfo_mapObj.getDomElement();
	
	if (el) {
		$(el).bind("click", mod_featureInfo_event)
			.css("cursor", "help");
	}
}
function mod_featureInfo_disable(){
	var el = mod_featureInfo_mapObj.getDomElement();

	if (el) {
		$(el).unbind("click", mod_featureInfo_event)
			.css("cursor", "default");
	}
}
function mod_featureInfo_event(e){
	var point = mod_featureInfo_mapObj.getMousePosition(e);
	
	eventBeforeFeatureInfo.trigger({"fName":mod_featureInfo_target});
	
//TODO that code should go to featureInfo Redirect module
	if(document.getElementById("FeatureInfoRedirect")){
		//fill the frames
		for(var i=0; i<mod_featureInfo_mapObj.wms.length; i++){
			var req = mod_featureInfo_mapObj.wms[i].getFeatureInfoRequest(mod_featureInfo_mapObj, point);
			if(req)
				window.frames.FeatureInfoRedirect.document.getElementById(mod_featureInfo_mapObj.wms[i].wms_id).src = req;
		}
	}
	else{
		urls = mod_featureInfo_mapObj.getFeatureInfoRequests(point, ignoreWms);
		if(urls){
			for(var i=0;i<urls.length;i++){
				var cnt = i;
				if(featureInfoPopupPosition.length == 2 && !isNaN(featureInfoPopupPosition[0]) && !isNaN(featureInfoPopupPosition[1])) {
					var dialogPosition = [];
					dialogPosition[0] = featureInfoPopupPosition[0]+cnt*25;
					dialogPosition[1] = featureInfoPopupPosition[1]+cnt*25;
				}
				else {
					var dialogPosition = featureInfoPopupPosition;
				}
				if(featureInfoLayerPopup == 'true'){
					$("<div><iframe frameborder='0' height='100%' width='100%' id='featureInfo_"+ i + "' title='<?php echo _mb("Information");?>' src='" + urls[i] + "'></iframe></div>").dialog({
						bgiframe: true,
						autoOpen: true,
						modal: false,
						title: '<?php echo _mb("Information");?>',
						width:parseInt(featureInfoPopupWidth, 10),
						height:parseInt(featureInfoPopupHeight, 10),
						position:dialogPosition,
						buttons: {
							"Ok": function(){
								$(this).dialog('close').remove();
							}
						}
					}).parent().css({position:"fixed"});
				}
				else
					window.open(urls[i], "" , "width="+featureInfoPopupWidth+",height="+featureInfoPopupHeight+",scrollbars=yes,resizable=yes");
			}
		}
		else
			alert(unescape("Please select a layer! \n Bitte waehlen Sie eine Ebene zur Abfrage aus!"));
	}
	setFeatureInfoRequest(mod_featureInfo_target,point.x,point.y);
}
