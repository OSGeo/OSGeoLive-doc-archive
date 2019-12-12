(function ($) {
	$.fn.helpDialog = function () {
		$.fn.helpDialog.currentDialog = null;
		return this.each(function () {

			var $this = $(this);
			var isOpen = false;

			var obj = $this.metadata({
				type: 'attr',
				name: 'help'
			});
			var helptext = obj.text.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
			var label = obj.label;
			
			var dialog = $("<div>" + helptext + "</div>").attr({
				"title": label
			}).dialog({
				autoOpen: false,
				show: 'fold',
				hide: 'fold',
				position: [
					$this.position().left + $this.width() + 10,
					$this.position().top + 5
				],
				beforeclose: function () {
					isOpen = false;
					$.fn.helpDialog.currentDialog = null;
				}
			});

			$this.bind("click", function () {
				if (isOpen) {
					return;
				}
				if ($.fn.helpDialog.currentDialog !== null) {
					$.fn.helpDialog.currentDialog.dialog("close");
				}
				$.fn.helpDialog.currentDialog = dialog;
				dialog.dialog("open");
				isOpen = true;
			});
		});
	};
}(jQuery));
