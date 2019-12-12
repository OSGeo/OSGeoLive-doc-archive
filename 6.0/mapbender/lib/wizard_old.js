
/*
 * Link between various interfaces, imagine a wizard
 */
$.fn.wizard = function (options) {
	var args = arguments;
	return this.each(function () {
		var getPath = function ($t) {
			var m = $t.metadata({
				type: "attr",
				name: "data"
			});
			var p = [];
			if (m && m.path) {
				p = m.path.split("/");
			}
			o.onClickLink(p);			
		};
		
		var navigate = function ($t) {
			if (o.fade) {
				$t.siblings("div form").fadeOut(function () {
					$t.fadeIn();
				});
			}		
			else {
				$t.siblings("div form").hide();
				$t.show();
			}
		};

		var clickHandler = function (e, $t) {
			getPath($t);
			navigate($t);
			if (e) {
				e.preventDefault();
			}
		};
	
		var to = function () {
			if (arguments.length === 2) {
				var $target = arguments[1];
				var href = "#" + $target.attr("id");
				var me = null;
				var e = null;
			}
			else {
				var href = $(this).attr("href");
				var $target = $(href);
				var me = this;
				var e = arguments[0];
			}
			var found = false;
			var abort = false;
			$target.parents().each(function () {
				if (abort || found) {
					return;
				}
				var $currentElement = $(this);
				// not this target's wizard
				if ($(this).data("isWizard") && this !== wizardInstance) {
					abort = true;
					return;
				}				
				// this target's wizard
				if ($(this).data("isWizard") && this === wizardInstance) {
					found = true;
					return;
				}				
			});
			if (abort || !found) {
				new Mapbender.Warning("not this target's wizard, or not found, aborting");
				return;
			}

			var proceed = me === null ? true : o.onBeforeClickLink(e, $(me));
			if (proceed === false) {
				new Mapbender.Warning("Clicked link " + href + " in wizard " + wizardInstance.id + " aborted!");
				return;
			}
			
			new Mapbender.Warning("Clicked link " + href + " in wizard " + wizardInstance.id);
			clickHandler(e, $target);
		};

		if (typeof options === "string") {
			if (!$(this).data("isWizard")) {
				return;
			}
			var wizardInstance = this;
			var o = $(this).data("wizardOptions");
			if (options === "to" && args.length === 2) {
				return to(null, args[1]);
			}
			return;
		}

		var wizardInstance = this;
		var $wiz = $(wizardInstance);
		$wiz.data("isWizard", true);

		var o = $.extend({
			fade: false,
			onClickLink: function () {
				return true;
			},
			onBeforeClickLink: function () {
				return true;
			}
		}, options || {});
		$wiz.data("wizardOptions", o);

		
		if (o.startWith) {
			getPath(o.startWith);
		}

		$("a.wizard").live("click", function (e) {
			to.apply($(this), [e]);
		});
	});
};
