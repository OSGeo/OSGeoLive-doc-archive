window.alert = function (str) {
	$("<div title='Hinweis'></div>").text(str).dialog({
// Workaround if dialogs have insufficient height in IE
//		open: function(){
//			$(this).css({
//				"height": "200px"
//			});
//		}
	});
};