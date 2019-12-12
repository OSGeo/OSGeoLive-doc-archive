var Selectable = {
	buttonParameters : {
		on:"../img/button_blink_red/selArea_on.png",
		over:"../img/button_blink_red/selArea_over.png",
		off:"../img/button_blink_red/selArea_off.png",
		type:"toggle"
	},
	makeSelectable : function () {
		// if a selection has been made, remove it
		if ($(".ui-selected").size() > 0) {
			Selectable.removeSelection();
		}

		$all = $(".collection");
		$all.selectable({
			selecting:function() {
				$(".ui-selecting").removeClass("div-border");
			},
			unselecting:function() {
				$(".ui-selectee").addClass("div-border");
				$(".ui-selecting").removeClass("div-border");
			}
		});
	},
	removeSelection : function () {
		$(".ui-selected").addClass("div-border");
		$(".collection").selectable("destroy");
		$(".ui-selected").removeClass("ui-selected");
		$(".ui-selectee").removeClass("ui-selectee");
		$(".ui-selectable").removeClass("ui-selectable");
	}
};