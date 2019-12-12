var Save = {
	buttonParameters : {
		on:"../img/button_blink_red/wmc_save_on.png",
		over:"../img/button_blink_red/wmc_save_over.png",
		off:"../img/button_blink_red/wmc_save_off.png",
		type:"toggle"
	},
	updateDatabase : function (callback) {
		var data = [];
		$(".collection").children().each(function() {
			data.push({
				id:this.id,
				top:parseInt(this.style.top, 10),
				left:parseInt(this.style.left, 10),
				width:parseInt(this.style.width, 10),
				height:parseInt(this.style.height, 10)	
			});
		});
		var queryObj = {
			command:"update",
			parameters:{
				applicationId:editApplicationId,
				data:data	
			}
		};
		$.post("mod_editApplication_server.php", {
			queryObj:$.toJSON(queryObj)	
		}, function (json, status) {
			var replyObj = eval('(' + json + ')');
			alert(replyObj.success);
			callback();
		});
	}
};
