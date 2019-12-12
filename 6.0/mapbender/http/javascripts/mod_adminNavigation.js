eventInit.register(function () {
	$("a").click(function () {
		$("a").each(function () {
			this.style.color = "#808080";
		});
		this.style.color = "#0000ff";
	});
});