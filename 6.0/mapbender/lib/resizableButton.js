var Resizable = {
	buttonParameters : {
		on:"../img/button_blink_red/select_rectangle_on.png",
		over:"../img/button_blink_red/select_rectangle_over.png",
		off:"../img/button_blink_red/select_rectangle_off.png",
		type:"toggle"
	},
	makeResizable : function () {
		var selector = ".collection > div";
		$all = $(selector);
		$all.resizable();	
	},
	removeResizable : function () {
		var selector = ".collection > div";
		$all = $(selector);
		$all.resizable("destroy");	
	}
};
