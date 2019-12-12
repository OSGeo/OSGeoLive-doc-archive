/* 
* $Id: popup.js 3097 2008-10-02 15:27:36Z nimix $
* COPYRIGHT: (C) 2002 by ccgis. This program is free software under the GNU General Public
* License (>=v2). Read the file gpl.txt that comes with Mapbender for details. 
*/
//http://www.mapbender.org/index.php/popup
var popup_count = 1;
var popup_top = 150;

/**
 * @class A class representing a popup window
 *
 * @constructor
 * @param {String/Object} title the title text of the popup or Options object
 * Elelemts of the options object (all optional):
 *  left: position left
 *  top: position top
 *  width: popup width
 *  height: popup height
 *  title: popup title
 *  frameName: name of the popups iframe if it loads a frame  (recommended to set destroy to false)
 *  opacity: opacity of the frame default:1
 *  moveOpacity: opacity while the user moves or resizes the frame default:0.8
 *  html:content html of the frame (not parsed if url not false)
 *  url:content url of the frame
 *  minWidth: minimum width of the popup if the user resizes it
 *  maxWidth: maximum width of the popup if the user resizes it
 *  minHeight: minimum height of the popup if the user resizes it
 *  maxHeight: maximum height of the popup if the user resizes it
 *  minTop: minimum top position if the user moves the popup default:"document"
 *  minLeft: minimum left position if the user moves the popup default:"document"
 *  maxRight: maximum right position if the user moves/resizes the popup 
 *  maxBottom: maximum bottom position if the user moves/resized the popup
 * 	style: additional styles for the popup window
 *  destroy: remove dom of popup if user closes it (don't use it for iframes with framename) default:true
 *  closeCallback: function that is called if the user closes the window
 *  resizeable: allow user to change the size default:true
 *  dragable: allow user to move the window default:true
 *  balloon: balloon popup from top, left (disables resizeable and dragable)
 *  modal: create modal popup default:false
 * 
 * @param {String} html the "body" of the popup, can also be "url:http://foo.de" to display a website
 * @param {Number} width width of the popup
 * @param {Number} height hight of the popup
 * @param {Number} posx left position of the popup
 * @param {Number} popy top posision of the popup
 * 
 */
function mb_popup(title,html,width,height,posx,posy,fName,opacity) {
	//get first free place
	var create_pos=popup_count;
	for(var i = 0; i < popup_count;i++)
		if(!document.getElementById("popup"+String(i))){
			create_pos=i;
			break;
		}

	this.id="popup"+String(++popup_top);
		
	//Set defaults
	defaults = {
		left:25*create_pos,
		top:25*create_pos,
		width:300,height:250,
		title:"Title",
		frameName:this.id,
		id:this.id
	};

	if(typeof(title)!='object'){
		this.options = defaults;
		
		//Set vars
		if(posx)this.options.left=posx;
		if(posy)this.options.top=posy;
		if(width)this.options.width=width;
		if(height)this.options.height=height;
		if(title)this.options.title=title;
		if(fName)this.options.frameName=fName;
		if(opacity)this.options.opacity;
		if(html){
			if(html.indexOf("url:")==0)
				this.options.url=html.substr(4);
			else
				this.options.html=html;
		}
	}
	else
		this.options=$.extend(defaults, title);

	this.id = this.options.id;
	
	popup_count++;
	
	//create dom popup
	$("body").append("<div style=\"display:none;z-index:"+popup_top+"\" id=\""+this.id+"\"></div>");
	this.div = $("#"+this.id).mbPopup(this.options);
}

/**
 * Shows the popup
 */
mb_popup.prototype.show = function(){
	if(!document.getElementById(this.id)){
		//(re)create dom popup
		$("body").append("<div style=\"display:none;z-index:"+popup_top+"\" id=\""+this.id+"\"></div>");
		this.div = $("#"+this.id).mbPopup(this.options);
	}
	if(!this.isVisible())
		$("#"+this.id).show();
}

/**
 * Hides the popup
 */
mb_popup.prototype.hide = function(){	
	$("#"+this.id).hide();
	$("#balloon_"+this.id).hide();
	$("#modal_mouse_catcher").remove();
}

/**
 * Hides the popup
 */
mb_popup.prototype.destroy = function(){	
	$("#"+this.id).remove();
	$("#balloon_"+this.id).remove();
	$("#modal_mouse_catcher").remove();
}

/**
 * sets the width of the popup window
 *
 * @param {Number} width new width of the popup  
 */
mb_popup.prototype.setWidth = function(width){
	var div=document.getElementById(this.id);
	if(div)div.style.width=width;
}

/**
 * sets the height of the popup window
 *
 * @param {Number} height new height of the popup  
 */
mb_popup.prototype.setHeight = function(height){
	var div=document.getElementById(this.id);
	if(div)div.style.height=height;
}

/**
 * sets the left position of the popup window
 *
 * @param {Number} left new left position of the popup  
 */
mb_popup.prototype.setLeft = function(left){
	var div=document.getElementById(this.id);
	if(div)div.style.left=left;
}

/**
 * sets the top position of the popup window
 *
 * @param {Number} top new top position of the popup  
 */
mb_popup.prototype.setTop = function(topp){
	var div=document.getElementById(this.id);
	if(div)div.style.top=topp + "px";
}

/**
 * sets the opacity of the popup window
 *
 * @param {Number} opacity new opacity value of the popup  
 */
mb_popup.prototype.setOpacity = function(opacityy){
	var div=document.getElementById(this.id);
	if(div)div.style.opacity=opacityy;
}

/**
 * sets the title of the popup window
 *
 * @param {String} title new title text of the popup  
 */
mb_popup.prototype.setTitle = function(title){
	$("#"+this.id+" h1").text(title);
}

/**
 * sets the html content of the popup window
 *
 * @param {String} html new html "body" of the popup  
 */
mb_popup.prototype.setHtml = function(htmll){
	this.options.url=null;
	this.options.html=htmll;
	$("#"+this.id+" .Content").html('<div class="scrollDiv">'+htmll+'</div>');
}
/**
 * behaves like document.open(); (clear popup)
 */
mb_popup.prototype.open = function(){
	oDoc = this.getDocument();
	if(oDoc!=null){
		return oDoc.open();
	}
	this.setHtml("");
}

/**
 * behaves like document.write(); (appends content to the popup)
 * 
 * @param {String} text or html to write into the document
 */
mb_popup.prototype.write = function(str){
	oDoc = this.getDocument();
	if(oDoc!=null){
		return oDoc.write(str);
	}
	this.setHtml(this.options.html+str);
}

/**
 * behaves like document.close(); (finish loading state if popup is an iframe)
 */
mb_popup.prototype.close = function(){
	oDoc = this.getDocument();
	if(oDoc!=null){
		return oDoc.close();
	}
}

/**
 * sets the url of the content
 *
 * @param {String} url new url of the popup  
 */
mb_popup.prototype.setUrl = function(url){
	this.options.url=url;
	this.options.html=null;
	
	oDoc = this.getDocument();
	if(oDoc){
		oDoc.location.href = url;
	}
	else{
		$("#"+this.id+" .Content").html('<iframe src="'+url+'"></iframe>');
	}
}

/**
 * reposition
 */
mb_popup.prototype.reposition =function(){
//TODO	
}

/**
 * gets the visible state of the popup window
 * @return visible state of the popup
 * @type Boolean
 */
mb_popup.prototype.isVisible = function(){
	return $("#"+this.id+":visible").length>0?true:false;
}

/**
 * (re) set Some Options of the popup Window and rerender it
 * @param options object that contains the new options
 */
mb_popup.prototype.set = function(options){
	this.destroy();
	this.options=$.extend(this.options, options);
	this.show();
}

/**
 * get the DOM document of the client iframe
 * @return DOM document or null if the popup doesn't contain an iframe
 */
mb_popup.prototype.getDocument = function(){
	iFrame = $("#"+this.id+" iframe")[0];
	try{
		//try to load contentWindow first since not every browser 
		//supports contentDocument (or gives the window instead)
		oDoc = iFrame.contentWindow || iFrame.contentDocument;
		if (oDoc.document) {
	        oDoc = oDoc.document;
    	}
    	return oDoc;
	}
	catch(e){return null;}
}

/**
 * get Scroll position
 */
function getScrollPos() {  
	var scrOf ={X:0,Y:0,wW:$(window).width(),dW:$().width(),wH:$(window).height(),dH:$().height()};  
	
	if( typeof( window.pageYOffset ) == 'number' ) {  
		//Netscape compliant  
		scrOf.Y = window.pageYOffset;  
		scrOf.X = window.pageXOffset;  
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {  
		//DOM compliant  
		scrOf.Y = document.body.scrollTop;  
		scrOf.X = document.body.scrollLeft;  
	} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {  
		//IE6 standards compliant mode  
		scrOf.Y = document.documentElement.scrollTop;  
		scrOf.X = document.documentElement.scrollLeft;  
	}
	return scrOf;  
}



(function($){
	$.fn.mbPopup = function(options){
		//default settings
		defaults = {
			left:0,top:0,width:300,height:250,
			title:"",frameName:"",
			opacity:1.0,moveOpacity:0.8,html:false,url:false,
			minWidth:false,maxWidth:false,
			minHeight:false,maxHeight:false,
			minTop:"document",minLeft:"document",
			maxRight:false,maxBottom:false,
			dragable:true,resizeable:true,balloon:false,
			style:null,destroy:true,modal:false,
			closeCallback:null
		};
		//override defaults
		settings=$.extend(defaults,options)
		this.settings = settings;
		
		//add Styles
		this.addClass("mbPopup");
		
		//append additions styles
		if(settings.style)
			this.css(settings.style);
		
		//automated settings for balloon popup
		if(settings.balloon){
			$("body").append("<div id='balloon_"+settings.id+"' class='balloonBL' style='visibility:hidden;'>");
			settings.resizeable = false;
			settings.dragable = false;
		}

		//calculate positioning (mainly needed for balloon style)
		pos = $.mbPopupFn.calcPositioning(settings, this);

		//set balloon arm position
		if(settings.balloon){
			$("#balloon_"+settings.id).attr("class", "balloon"+(pos.position>=2?"B":"T")+(pos.position%2?"R":"L"))
				.css({"z-index":(popup_top),visibility:"visible",left:String(pos.bX)+"px",top:String(pos.bY)+"px"});
			settings.left = pos.pX;
			settings.top = pos.pY;
		}
		
		//fix IE width and height by adding the padding values
		if($.browser.msie){
			settings.width = pos.pW;
			settings.height = pos.pH;
		}
		
		//Set dimensions
		this.css({top:String(settings.top)+"px",
			left:String(settings.left)+"px",
			width:String(settings.width)+"px",
			height:String(settings.height)+"px",
			opacity:settings.opacity
			});
		
		//Insert content
		if(settings.url)
			html = ('<iframe name="'+settings.frameName+'" src="'+settings.url+'"></iframe>');
		else
			html = ('<div class="scrollDiv">'+settings.html+'</div>');
	
		this.html('<div class="Title Drag"><h1>'+settings.title+'</h1></div><div class="Content">'+html+'</div><img src="../img/close_icon.png" class="Close" alt="close" />'+(settings.resizeable?'<div class="Resize" />':''));
	
		var data = {El:this,fY:settings.minTop,fX:settings.minLeft,tX:settings.maxRight,tY:settings.maxBottom,
			fW:settings.minWidth,tW:settings.maxWidth,fH:settings.minHeight,tH:settings.maxHeight,
			destroy:settings.destroy,opacity:settings.opacity,moveOpacity:settings.moveOpacity,close:settings.closeCallback}
		
		//Make window Dragable
		if(settings.dragable)
			$(".Drag", this).bind('mousedown',data,function(event){
				//set to top
				event.data.El.css("z-index",popup_top++);
				event.data.El.css('opacity',event.data.moveOpacity)
				//create helper div to steal mouse events
				$("body").append("<div style=\"position:absolute;top:0px;left:0px;width:"+$().width()+"px;height:"+$().height()+"px;z-index:"+(popup_top+2)+($.browser.msie?";background:url(../img/transparent.gif)":"")+"\" id=\"mouse_catcher\"></div>");
				$("iframe", event.data.El).hide();
							
				//Parse setiings of min and max position
				var data = $.mbPopupFn.parseDimensions(event);
				
				//bind mouse events to popup
				$().bind("mousemove",$.extend(data,{drag:true}),$.mbPopupFn.move).bind('mouseup',event.data,$.mbPopupFn.stop);
			});
		
		//Make Window resizable
		if(settings.resizeable)
			$(".Resize", this).bind('mousedown',data,function(event){
				//set to top
				event.data.El.css("z-index",popup_top++);
				event.data.El.css('opacity',event.data.moveOpacity)
				//create helper div to steal mouse events
				$("body").append("<div style=\"position:absolute;top:0px;left:0px;width:"+$().width()+"px;height:"+$().height()+"px;z-index:"+(popup_top+2)+($.browser.msie?";background:url(../img/transparent.gif)":"")+"\" id=\"mouse_catcher\"></div>");
				$("iframe", event.data.El).hide();
	
				//Parse setiings of min and max position
				var data = $.mbPopupFn.parseDimensions(event);
				
				//bind mouse events to popup
				$().bind("mousemove",$.extend(data,{drag:false}),$.mbPopupFn.move).bind('mouseup',event.data,$.mbPopupFn.stop);
			});
		
		//closeButton
		this.closePopup = function(event){
			if(event.data.close)
				event.data.close();
			$("#modal_mouse_catcher").remove();
			if(event.data.destroy){
				event.data.El.slideUp('slow', function(){$(this).remove();});
				$("#balloon_"+event.data.El.attr("id")).remove();
			}
			else{
				event.data.El.slideUp('slow');
				$("#balloon_"+event.data.El.attr("id")).fadeOut();
			}
		};
		$(".Close", this).bind('click',data, this.closePopup);
		
		//create div to make window modal
		if(settings.modal){
			$("body").append("<div style=\"position:absolute;top:0px;left:0px;width:"+$().width()+"px;height:"+$().height()+"px;z-index:"+(popup_top-1)+($.browser.msie?";background:url(../img/transparent.gif)":"")+"\" id=\"modal_mouse_catcher\"></div>");
			$("#modal_mouse_catcher").bind('click',data, this.closePopup);
		}

		//raise on click
		this.click(function(){this.style.zIndex=popup_top++;$("#balloon_"+this.id).css("z-index", popup_top++);});
		$("#balloon_"+settings.id).click(function(){$("#"+this.id.substr(8)).css("z-index", popup_top++);this.style.zIndex=popup_top++;});
		
		//raise top postition
		popup_top++;
	};
	//helper functions
	$.mbPopupFn = {
		//on mouse action
		move:function(event){
			//drag
			if(event.data.drag){
				var newPos = {X:event.data.X+event.pageX-event.data.pX,
					Y:event.data.Y+event.pageY-event.data.pY};
				if(event.data.fX!==false&&newPos.X<event.data.fX)
					newPos.X=event.data.fX;
				if(event.data.fY!==false&&newPos.Y<event.data.fY)
					newPos.Y=event.data.fY;
				if(event.data.tX!==false&&newPos.X>event.data.tX-event.data.W)
					newPos.X=event.data.tX-event.data.W;
				if(event.data.tY!==false&&newPos.Y>event.data.tY-event.data.H)
					newPos.Y=event.data.tY-event.data.H;
				
				event.data.El.css({left:newPos.X,top:newPos.Y});
				return;
			}
			//resize
			var newDim = {W:Math.max(event.pageX-event.data.pX+event.data.W,0),
				H:Math.max(event.pageY-event.data.pY+event.data.H,0)};
			if(event.data.fW!==false&&newDim.W<event.data.fW)
				newDim.W=event.data.fW;
			if(event.data.fH!==false&&newDim.H<event.data.fH)
				newDim.H=event.data.fH;
			if(event.data.tW!==false&&newDim.W>event.data.tW)
				newDim.W=event.data.tW;
			if(event.data.tH!==false&&newDim.H>event.data.tH)
				newDim.H=event.data.tH;
			if(event.data.tX!==false&&newDim.W+event.data.X>event.data.tX)
				newDim.W=event.data.tX-event.data.X;
			if(event.data.tY!==false&&newDim.H+event.data.Y>event.data.tY)
				newDim.H=event.data.tY-event.data.Y;	
						
			event.data.El.css({width:newDim.W,height:newDim.H});
			
		},
		stop:function(event){
			event.data.El.css('opacity',event.data.opacity)
			$("iframe", event.data.El).show();
			$().unbind('mousemove',$.mbPopupFn.move).unbind('mouseup',$.mbPopupFn.stop);
			$("#mouse_catcher").remove();
		},
		//parse move and resize dimensions
		parseDimensions:function(d){
			var dim = getScrollPos();
			var bd = {X:parseInt($(d.data.El).css("border-left-width"))+parseInt($(d.data.El).css("border-right-width")),
				Y:parseInt($(d.data.El).css("border-top-width"))+parseInt($(d.data.El).css("border-bottom-width"))};
			ret = {El:d.data.El,
				fX:(d.data.fX=="window"?dim.X:(d.data.fX=="document"?0:(d.data.fX<0?dim.dW-d.data.fX:d.data.fX))),
				fY:(d.data.fX=="window"?dim.Y:(d.data.fY=="document"?0:(d.data.fY<0?dim.dH-d.data.fY:d.data.fY))),
				tX:(d.data.tX=="window"?dim.X+dim.wW:(d.data.tX=="document"?dim.dW:(d.data.tX<0?dim.dW-d.data.tX:d.data.tX))),
				tY:(d.data.tY=="window"?dim.Y+dim.wH:(d.data.tY=="document"?dim.dH:(d.data.tH<0?dim.dH-d.data.tH:d.data.tY))),
				fW:(d.data.fW=="window"?dim.wW:(d.data.fW=="document"?dim.dW:(d.data.fW<0?dim.dW-d.data.fW:d.data.fW))),
				fH:(d.data.fH=="window"?dim.wH:(d.data.fH=="document"?dim.dH:(d.data.fH<0?dim.dH-d.data.fH:d.data.fH))),
				tW:(d.data.tW=="window"?dim.wW:(d.data.tW=="document"?dim.dW:(d.data.tW<0?dim.dW-d.data.tW:d.data.tW))),
				tH:(d.data.tH=="window"?dim.wH:(d.data.tH=="document"?dim.dH:(d.data.tH<0?dim.dH-d.data.tH:d.data.tH))),
				X:parseInt(d.data.El.css("left")),
				Y:parseInt(d.data.El.css("top")),
				W:parseInt(d.data.El.css("width")),
				H:parseInt(d.data.El.css("height")),
				pX:d.pageX,pY:d.pageY
			};
			if(ret.tX)ret.tX-=bd.X;
			if(ret.tY)ret.tY-=bd.Y;
			return ret;	
		},
		calcPositioning:function(settings, el){
			var dim = getScrollPos();
			
			pos = {};
			pos.pH = settings.height + (parseInt(el.css("padding-top"))+parseInt(el.css("padding-bottom")));
			pos.pW = settings.width + (parseInt(el.css("padding-left"))+parseInt(el.css("padding-right")));
			if(settings.balloon){
				pos.bH = $("#balloon_"+settings.id).height();
				pos.bW = $("#balloon_"+settings.id).width();
	
				pos.position = 0;
				
				if(settings.top-pos.pH-pos.bH-parseInt(el.css("border-top-width"))<dim.Y){
					//bubble on top
					pos.pY = settings.top+pos.bH-parseInt(el.css("border-top-width"));
				}
				else{
					//bubble on bottom
					pos.pY = settings.top-pos.pH-pos.bH+($.browser.msie?parseInt(el.css("border-bottom-width")):-parseInt(el.css("border-top-width")));
					pos.position+=2;
				}
				if(settings.left+pos.pW+parseInt(el.css("border-left-width"))+parseInt(el.css("border-right-width"))>(dim.X+dim.wW)){
					//bubble on right
					pos.pX = settings.left-parseInt(el.css("border-left-width"))-parseInt(el.css("border-right-width"))-pos.pW;
					pos.position++;
				}
				else{
					//bubble on left
					pos.pX = settings.left-parseInt(el.css("border-left-width"));
				}
				
				pos.bX=(settings.left-(pos.position%2?pos.bW:0));
				pos.bY=(settings.top-(pos.position>=2?pos.bH:0));
			}
			return pos;
		}
	};
	})
(jQuery);
