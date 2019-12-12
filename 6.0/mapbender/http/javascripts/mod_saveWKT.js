var mod_saveWKT_img = new Image();
var mod_saveWKT_win = null;
mod_saveWKT_img.src = "../img/button_gray/save.gif";
eventAfterMeasure.register(function () {
	return mod_saveWKT();
});

function mod_saveWKT(){
	var str =  "<div style='position:absolute;top:75px' onmouseup='parent.mod_saveWKT_go()' ";
	str += "onmouseover='parent.mod_measure_timeout()' onmouseout='parent.mod_measure_go()'><img src='"+mod_saveWKT_img.src+"'></div>";
	return str;
}
function mod_saveWKT_go(){
	if(mod_saveWKT_win == null || mod_saveWKT_win.closed == true){
		mod_saveWKT_win =  window.open("../php/mod_saveWKT.php","mod_saveWKT_win","width=400, height=400, resizable=yes, dependent=yes");
	}
	else{
		mod_saveWKT_win.focus();
	}
}