var Draggable = {
	buttonParameters : {
		on:"../img/button_blink_red/pan_on.png",
		over:"../img/button_blink_red/pan_over.png",
		off:"../img/button_blink_red/pan_off.png",
		type:"toggle"
	},
	grid : [7,7],
	makeDraggable : function() {

		var $selection = $(".ui-selected");
		if ($selection.size() > 0) {

			// if elements have been selected, we want to drag 
			// them together, not individually. So we move these
			// elements into a new div tag, and make that tag 
			// draggable
			
			Draggable.moveSelectionToDiv();
			
			$(".div-draggable").draggable({
				grid:Draggable.grid
			});
		}
		else {
			$(".collection").children().draggable({
				grid:Draggable.grid
			});
		}
	},
	removeDraggable : function () {
		if ($(".ui-selected").size() > 0) {
			Draggable.moveSelectionFromDiv();			
		}
		$(".collection").children().draggable("destroy");
	},
	moveSelectionFromDiv : function () {
		var divX, divY;
		var $draggableDiv = $(".div-draggable");
		$draggableDiv.each(function() {
			divX = parseInt(this.style.left);
			divY = parseInt(this.style.top);
		});
		$draggableDiv.children().each(function() {
			var newX = parseInt(this.style.left) + divX;
			var newY = parseInt(this.style.top) + divY;
			this.style.left = newX + "px";
			this.style.top = newY + "px";
			$(this).removeClass("ui-selectee");
			$(this).removeClass("ui-selected");
			$(this).addClass("div-border");
			$clone = $(this).clone();
			$(".collection").append($clone);
		});
		$draggableDiv.remove();
	},
	moveSelectionToDiv : function () {
		// first, put all selected elements inside a 
		// single div tag; then, make that div tag 
		// draggable
		
		// the coordinates of the new div tag need
		// to be computed first
		var minX, minY;
		$(".ui-selected").each(function() {
			var currentLeft = parseInt(this.style.left);
			if ((!minX && minX !== 0) || currentLeft < minX) {
				minX = currentLeft;
			}
			var currentTop = parseInt(this.style.top);
			if ((!minY && minY !== 0) || currentTop < minY) {
				minY = currentTop;
			}
		});
		
		var $div = $("<div class='div-draggable'></div>");
		$div.css("position", "absolute");
		$div.css("top", minY);
		$div.css("left", minX);

		// to move the selected nodes, we clone them and attach
		// them to the new div. the old tags are removed 
		$(".ui-selected").each(function() {
			var newX = parseInt(this.style.left) - minX;
			var newY = parseInt(this.style.top) - minY;

			$clone = $(this).clone();
			$clone.css("position", "absolute");
			$clone.css("top", newY + "px");
			$clone.css("left", newX + "px");

			$div.append($clone);
			$(this).remove();
		});
		
		$div.appendTo($(".collection"));
	}
};
