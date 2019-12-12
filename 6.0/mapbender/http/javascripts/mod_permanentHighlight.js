var mod_permanentHighlight_target = "mapframe1";
var mod_permanentHighlight_x = false;
var mod_permanentHighlight_y = false;
var mod_permanentHighlight_text = false;

mb_registerSubFunctions("mod_permanentHighlight_init()");
mb_registerPanSubElement("permanent");

function mod_permanentHighlight_init(){
	if(mod_permanentHighlight_x && mod_permanentHighlight_y){
		mb_permanentHighlight("mapframe1",parseFloat(mod_permanentHighlight_x),parseFloat(mod_permanentHighlight_y));
	}
}
function mb_permanentHighlight(frameName,x,y){
	var pos = makeRealWorld2mapPos(frameName,x, y);
	window.frames[frameName].document.getElementById('permanent').style.visibility = 'visible';
	//3373790 / 5938930
	if (mod_permanentHighlight_text){
		var tagSource = "";
	    tagSource += "<div style='z-index:4;position:absolute;left:"+(pos[0]-7)+"px;top:"+(pos[1]-7)+"px'>";
	    tagSource += "<img src='../img/redball.gif'>";
	    tagSource += "<span style='position:absolute;top:+12px;left:+12px;z-index:20;visibility:visible;background-color:white;color:red;font-family:Arial;'><nobr>";
	    tagSource += mod_permanentHighlight_text +"</nobr><span></div>";
	    writeTag(frameName, "permanent", tagSource);
	}else{
		mb_arrangeElement("","permanent",pos[0]-7, pos[1]-7);
	}
}
