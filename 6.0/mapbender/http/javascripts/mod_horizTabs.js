/* 
* $Id:$
* COPYRIGHT: (C) 2001 by ccgis. This program is free software under the GNU General Public
* License (>=v2). Read the file gpl.txt that comes with Mapbender for details. 
*/
(function($){

/**
 * turns a div tag into a tab control
 * @return jQuery
 */
	$.fn.tabControl = function(options){
		defaults = {tab:true};
		return this.each(function(){
			this._tabSettings = $.extend(defaults, options)
			this.tabs = [];
			this.activeTab = false;
			//append div for tab buttons
			$(this).append("<div />")
		});
	};
	
/**
 * adds a tab to a tab control
 * @return jQuery
 */
	$.fn.addTab = function(options){
		return this.each(function(){
			//ensure function is called on a tabControl
			if(!this._tabSettings)
				return;
				
			//Add tab
			this.tabs.push(options);

			//add access button	for this tab and update height
			this.tab_height = parseInt($("div",this)
				.append("<span class=\"tabButton\" id=\"tabButton_"+options.id+"\">"+options.title+"</span> ")
				.height());
			 //bind click event to activate the tab
			 $("#tabButton_"+options.id,this)
			 	.bind("click",{i:this.tabs.length-1},function(event){
					$(this.parentNode.parentNode).activateTab(event.data.i);
				});

			//set element to right position
			$("#"+options.id).css({position:"absolute",
				left:parseInt(this.style.left, 10) + "px",
				top:(parseInt(this.style.top, 10)+this.tab_height) + "px",
				width:$(this).width() + "px",
				height:($(this).height()-this.tab_height) + "px",
				zIndex:parseInt(this.style.zIndex+1)})
			//and hide it
			.hide();
		});
	}
	
/**
 * activate tab i of the tab control
 * @param integer i number of tab to activate (0 to number of tabs -1)
 * @return jQuery
 */
	$.fn.activateTab = function(i){
		return this.each(function(){
			//ensure function is called on a tabControl
			if(!this._tabSettings)
				return;	

			//hide old tab
			if(this.activeTab!==false){
				$("#"+this.tabs[this.activeTab].id).hide();
				$("#tabButton_"+this.tabs[this.activeTab].id).removeClass("tabButtonActive").addClass("tabButton");
			}

			//move tab to right position
			$("#"+this.tabs[i].id).css({position:"absolute",
					left:parseInt(this.style.left),
					top:parseInt(this.style.top)+this.tab_height,width:$(this).width(),
					height:$(this).height()-this.tab_height,zIndex:parseInt(this.style.zIndex+1)})
			//and show it
			.show();
			
			//update button state
			$("#tabButton_"+this.tabs[i].id).removeClass("tabButton").addClass("tabButtonActive");
			this.activeTab=i;
		});
	}
})
(jQuery);

window.setTimeout(function(){
     $(".nested-tabs-fixup").activateTab(0);
},5000);

