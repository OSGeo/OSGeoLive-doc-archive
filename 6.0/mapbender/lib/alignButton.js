var Align = {
	top : {
		buttonParameters : {
			on:"../img/button_blink_red/up_on.png",
			over:"../img/button_blink_red/up_on.png",
			off:"../img/button_blink_red/up_off.png",
			type:"singular"
		},
		align : function () {
			if ($(".ui-selected").size() < 2) {
				return false;
			}
			var minY;
			$(".ui-selected").each(function() {
				var currentMinY = parseInt(this.style.top, 10);
				if ((!minY && minY !== 0 ) || currentMinY < minY) {
					minY = currentMinY;
				}
			});
			$(".ui-selected").each(function() {
				this.style.top = minY + "px";
			});
		}
	},
	left : {
		buttonParameters : {
			on:"../img/button_blink_red/back_on.png",
			over:"../img/button_blink_red/back_on.png",
			off:"../img/button_blink_red/back_off.png",
			type:"singular"
		},		
		align : function () {
			if ($(".ui-selected").size() < 2) {
				return false;
			}
			var minX;
			$(".ui-selected").each(function() {
				var currentMinX = parseInt(this.style.left, 10);
				if ((!minX && minX !== 0 ) || currentMinX < minX) {
					minX = currentMinX;
				}
			});
	
			$(".ui-selected").each(function() {
				this.style.left = minX + "px";
			});
		}
	},
	bottom : {
		buttonParameters : {
			on:"../img/button_blink_red/down_on.png",
			over:"../img/button_blink_red/down_on.png",
			off:"../img/button_blink_red/down_off.png",
			type:"singular"
		},
		align : function () {
			if ($(".ui-selected").size() < 2) {
				return false;
			}
			var maxY;
			$(".ui-selected").each(function() {
				var currentMaxY = parseInt(this.style.top, 10) + parseInt(this.style.height, 10);
				if ((!maxY && maxY !== 0) || currentMaxY > maxY) {
					maxY = currentMaxY;
				}
			});
	
			$(".ui-selected").each(function() {
				var newY = maxY - parseInt(this.style.height, 10);
				this.style.top = newY + "px";
			});
		}
	},
	right : {
		buttonParameters : {
			on:"../img/button_blink_red/forward_on.png",
			over:"../img/button_blink_red/forward_on.png",
			off:"../img/button_blink_red/forward_off.png",
			type:"singular"
		},
		align : function () {
			if ($(".ui-selected").size() < 2) {
				return false;
			}
			var maxX;
			$(".ui-selected").each(function() {
				var currentMaxX = parseInt(this.style.left, 10) + parseInt(this.style.width, 10);
				if ((!maxX && maxX !== 0) || currentMaxX > maxX) {
					maxX = currentMaxX;
				}
			});
	
			$(".ui-selected").each(function() {
				var newX = maxX - parseInt(this.style.width, 10);
				this.style.left = newX + "px";
			});
		}
	}
};