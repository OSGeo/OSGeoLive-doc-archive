/**
 * Package: Button
 *
 * Description:
 * Adds a clickable button to Mapbender
 * 
 * Files:
 *  - lib/button.js
 *
 * Help:
 * <none>
 *
 * Maintainer:
 * http://www.mapbender.org/User:Christoph_Baudson
 * 
 * Parameters:
 * options.over      - URL to img representing the "over" status
 * options.on        - URL to img representing the "on" status
 * options.off       - URL to img representing the "off" status (default)
 * options.go        - a function to be executed once the button is pressed
 * options.stop      - a function to be executed once the button is disabled
 * options.off       - URL to img representing the "off" status (default)
 * options.name      - the ID of the corresponding DOM element
 *
 * License:
 * Copyright (c) 2009, Open Source Geospatial Foundation
 * This program is dual licensed under the GNU General Public License 
 * and Simplified BSD license.  
 * http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt
 */


Mapbender.Button = function (options) {
	this.stop = function () {
		mb_disableThisButton(button.elName);
	};

	var button = mb_regButton(options);
};
 
var mb_button = [];

function mb_regButton_frame(wii, frameName, param){
	var ind = mb_button.length;
	mb_button[ind] = new mb_conButton(wii, ind);

	if (typeof wii === "object") {
		var options = wii;
		mb_button[ind] = options.domElement;
		mb_button[ind].img_over = options.over ? options.over : "";
		mb_button[ind].img_on = options.on ? options.on : "";
		mb_button[ind].img_off = options.off ? options.off : "";
		mb_button[ind].status = options.status ? options.status : 0;
		mb_button[ind].elName = options.name ? options.name : "";
		mb_button[ind].fName = options.frameName ? options.frameName : "";
		if (options.go && typeof options.go === "function") {
			mb_button[ind].go = options.go;
		}
		else {
			mb_button[ind].go = function () {
			};
		}
		if (options.stop && typeof options.stop === "function") {
			mb_button[ind].stop = options.stop;
		}
		else {
			mb_button[ind].stop = function () {
			};
		}
	}
	else if (typeof wii === "string") {
		var str = "";
		if (frameName !== null) {
			str += "window.frames['" + frameName + "'].";
		}
		str += wii + "(" + ind;
		if (param !== null) {
			str += "," + param;
		}
		str += ");";
		eval(str);
	}
	else if (typeof wii == "function") {
		if (param === null) {
			wii(ind);
		}
		else {
			wii(ind, param);
		}
	}

	mb_button[ind].prev = mb_button[ind].src;
	mb_button[ind].src = mb_button[ind].img_off;
	mb_button[ind].onmouseover = function () {
		mb_button_over(ind);
	};
	mb_button[ind].onmouseout = function(){
		mb_button_out(ind);
	};
	mb_button[ind].onclick = function(){
		mb_button_click(ind);
	};
	if (frameName === null) {
		mb_button[ind].frameName = "";
	}
	else {
		mb_button[ind].frameName = frameName;
	}
	return mb_button[ind];
}

function mb_regButton(wii){
	return mb_regButton_frame(wii, null, null);
}

function mb_conButton(wii, ind){
   return true;
}
function mb_button_over(ind){
   if(mb_button[ind].status === 0){
      mb_button[ind].prev = mb_button[ind].src;
      mb_button[ind].src = mb_button[ind].img_over;
   }
}
function mb_button_out(ind){
   if(mb_button[ind].status === 0){
   		mb_button[ind].src = mb_button[ind].img_off;
   }
}
function mb_button_click(ind){
   var mbStatus = mb_button[ind].status;
   if(mbStatus === 0){
      mb_disableButton(mb_button[ind].elName);
      mb_button[ind].prev = mb_button[ind].img_on;
      mb_button[ind].src = mb_button[ind].img_on;
      mb_button[ind].status = 1;
      if (mb_button[ind].frameName !== "") {
          window.frames[mb_button[ind].frameName].document.getElementById(mb_button[ind].elName).go();
      }
      else {
	      document.getElementById(mb_button[ind].elName).go();
      }
   }
   else{
      mb_button[ind].prev = mb_button[ind].img_off;
      mb_button[ind].src = mb_button[ind].img_off;
      mb_button[ind].status = 0;      
      if (mb_button[ind].frameName !== "") {
          window.frames[mb_button[ind].frameName].document.getElementById(mb_button[ind].elName).stop();
      }
      else {
	      document.getElementById(mb_button[ind].elName).stop();
      }
   }
}

function mb_enableButton (elName) {
	for (var i = 0; i < mb_button.length; i++) {
		if (mb_button[i].elName === elName && mb_button[i].status === 0) {
			mb_button_click(i);
			return;
		}
	}
}
function mb_disableButton(elName){
   for(var i=0; i<mb_button.length; i++){
      if(mb_button[i].elName != elName && mb_button[i].status == 1){
            mb_button[i].status = 0;
		      if (mb_button[i].frameName !== "") {
    	        window.frames[mb_button[i].frameName].document.getElementById(mb_button[i].elName).src = mb_button[i].img_off;
	            window.frames[mb_button[i].frameName].document.getElementById(mb_button[i].elName).stop();
		      }
		      else {
    	        document.getElementById(mb_button[i].elName).src = mb_button[i].img_off;
	            document.getElementById(mb_button[i].elName).stop();
		      }
            return true;
       
      }
   }
}
function mb_disableThisButton(elName){
   for(var i=0; i<mb_button.length; i++){
      if(mb_button[i].elName == elName && mb_button[i].status == 1){
      		//alert(mb_button[i].elName);
            mb_button[i].status = 0;
		      if (mb_button[i].frameName !== "") {
    	        window.frames[mb_button[i].frameName].document.getElementById(mb_button[i].elName).src = mb_button[i].img_off;
	            window.frames[mb_button[i].frameName].document.getElementById(mb_button[i].elName).stop();
		      }
		      else {
        	    document.getElementById(mb_button[i].elName).src = mb_button[i].img_off;
		        document.getElementById(mb_button[i].elName).stop();
		      }
            return true;
       
      }
   }
}
function updateButtonTooltips(obj, result, message) {
	if (!result) {
		alert(message);
		return;
	}
	var buttonArray = obj;
	// this one only changes those in the main frame
	var imageArray = document.getElementsByTagName("img");
	for (var i = 0; i < imageArray.length; i++) {
		for(var j=0; j<buttonArray.length; j++){
			if (imageArray[i].id == buttonArray[j].id) {
				document.getElementById(imageArray[i].id).title = buttonArray[j].title;
			}
		}
	}
}

function mb_localizeButtons(){
	var req = new Mapbender.Ajax.Request({
		url: "../php/mod_button_tooltips.php",
		callback: updateButtonTooltips
	});
	req.send();
}

Mapbender.events.localize.register(function () {
	mb_localizeButtons();
});

/*Mapbender.events.init.register(function () {
	mb_localizeButtons();
});*/

