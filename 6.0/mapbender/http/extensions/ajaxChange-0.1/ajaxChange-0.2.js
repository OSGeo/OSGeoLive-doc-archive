/**
 * ajaxChange - a jQuery plugin that enhances the change event for select boxes
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Author: Christoph Baudson (christoph AT osgeo DOT org)
 * 
 * Contributors: Marc Jansen
 * 
 * Version: 0.2
 * 
 * Changelog:
 * - return jQuery object
 * - using jQuery utility function isFunction
 * - added author and contributors
 * 
 * Date: May 7th, 2010
 * 
 * 
 * This plugin enhances the change event for select boxes. 
 * It only has two features:
 * 
 * - disable: 	automatically disable the select box when the 
 * 				change event is triggered (for example, to avoid 
 * 				multiple AJAX request requests)
 * - undo: 		reset the selected index to its original value 
 * 				before the change event had been triggered (for 
 * 				example, if the result of an AJAX request reveals 
 * 				that the new selected index is invalid)
 * 
 * initialization:
 * 
 * $("select").ajaxChange(func [, options]);
 * 
 * func is a function to be executed on change
 * options is an object with two attributes
 * - boolean "disable" [true]
 * - boolean "undo" [true]
 * 
 * control:
 * 
 * $("select").ajaxChange("abort");
 * 
 * - if disable is set, the select box is enabled.
 * - if undo is set, the selectedIndex is set to the original value.
 * 
 * $("select").ajaxChange("done");
 * - if disable is set, the select box is enabled, otherwise "done" is irrelevant.
 * 
 * 
 */
(function ($) {
	$.fn.ajaxChange = function () {
		var args = arguments;
		return this.each(function () {
			if (this.tagName.toUpperCase() !== "SELECT") {
				return;
			}
			
			var $this = $(this);
	
			// initialization
			if (args.length >= 1 && $.isFunction(args[0])) {
				var f = args[0];
				
				var options = {};
				if (args.length >= 2 && typeof args[1] === "object") {
					options = args[1];
				}
				options = $.extend({
					disable: true,
					undo: true
				}, options);
				$this
					.data("ajaxChangeDisable", options.disable)
					.data("ajaxChangeUndo", options.undo)
					.data("ajaxChangeSelectedIndex", null)
					.change(function () {
						var $current = $(this);
						if ($current.data("ajaxChangeDisable") === true) {
							$current.attr("disabled", "disabled");
						}
						f.apply(this, arguments);
					})
					.mousedown(function () {
						var $current = $(this);
						if ($current.data("ajaxChangeSelectedIndex") === null
							&& $current.data("ajaxChangeUndo")
						) {
							$current.data("ajaxChangeSelectedIndex", this.selectedIndex);
						}
					});
			}
			// control
			else if (args.length >= 1 && typeof args[0] === "string") {
				var command = args[0];
				switch (command) {
					case "abort":
						if ($this.data("ajaxChangeDisable") === true) {
							$this.removeAttr("disabled");
						}
						if ($this.data("ajaxChangeUndo")) {
							this.selectedIndex = $this.data("ajaxChangeSelectedIndex");
							$this.data("ajaxChangeSelectedIndex", null);
						}
						break;
					case "done":
						if ($this.data("ajaxChangeDisable") === true) {
							$this.removeAttr("disabled");
						}
						if ($this.data("ajaxChangeUndo")) {
							$this.data("ajaxChangeSelectedIndex", null);
						}
						break;
				}
			}
		});
	};
}(jQuery));