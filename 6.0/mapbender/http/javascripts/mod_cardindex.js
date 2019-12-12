/*********configuration:****************************************************************/
var mod_cardindex_indexes	= new Array("tab1 ","tab2 ","tab3 ","tab4 ");
var mod_cardindex_id		= new Array("iframe1","iframe2", "iframe3","iframe4");
var mod_cardindex_dim		= new Array();
var mod_cardindex_icolor	= "#7BA5D6";	//background active tabs
var mod_cardindex_ficolor	= "#ff0000";	//font activ tabs
var mod_cardindex_bcolor	= "#ffffff";	//background inactive tabs
/***************************************************************************************/

mb_registerInitFunctions("mod_cardindex_init()");
function mod_cardindex_init(){
	mod_cardindex_dim["width"] = document.getElementById(mod_cardindex_id[0]).style.width;
	mod_cardindex_dim["height"] = document.getElementById(mod_cardindex_id[0]).style.height;
	var str = "<span  style='font-family: Arial, Helvetica, sans-serif;font-size:10px;cursor:pointer'>";
	for(var i=0; i<mod_cardindex_indexes.length; i++){
		str += "<span id='mod_cardindex"+i+"'onclick='mod_cardindex("+i+")' style='color:black'>"+mod_cardindex_indexes[i]+"</span>&nbsp;";
	}
	str += "</span>";
	writeTag("","cardindex",str);
	for(var i=0; i<mod_cardindex_indexes.length; i++){
		if(i == 0){
			document.getElementById("mod_cardindex" + i).style.color = mod_cardindex_ficolor;
			document.getElementById("mod_cardindex" + i).style.backgroundColor = mod_cardindex_icolor;
		}
		else{
			document.getElementById(mod_cardindex_id[i]).style.visibility = 'hidden';
			document.getElementById(mod_cardindex_id[i]).style.width = "1px";
			document.getElementById(mod_cardindex_id[i]).style.height = "1px";
		}
	}
}
function mod_cardindex(obj){
	for(var i=0; i<mod_cardindex_indexes.length; i++){
		if(obj != i){
			document.getElementById("mod_cardindex" + i).style.color = 'black';
			document.getElementById("mod_cardindex" + i).style.backgroundColor = mod_cardindex_bcolor;
			document.getElementById(mod_cardindex_id[i]).style.visibility = 'hidden';
			document.getElementById(mod_cardindex_id[i]).style.width = "1px";
			document.getElementById(mod_cardindex_id[i]).style.height = "1px";         
		}
		else if(obj == i){
			document.getElementById("mod_cardindex" + i).style.color = mod_cardindex_ficolor;
			document.getElementById("mod_cardindex" + i).style.backgroundColor = mod_cardindex_icolor;
			document.getElementById(mod_cardindex_id[i]).style.visibility = 'visible';
			document.getElementById(mod_cardindex_id[i]).style.width = mod_cardindex_dim["width"];
			document.getElementById(mod_cardindex_id[i]).style.height = mod_cardindex_dim["height"];
		}
	}
}