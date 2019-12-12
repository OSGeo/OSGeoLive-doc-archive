
/*
 * Link between various interfaces, imagine a wizard
 */
$.widget("mb.wizard", {
	options: {
		fade: false,
		onClickLink: function () {
			return true;
		},
		onBeforeClickLink: function () {
			return true;
		}		
	},
	_navigate: function ($t, path, $a) {
		if (this.options.fade) {
			var that = this;
			$t.siblings("div,form,iframe").fadeOut(function () {
				$t.fadeIn(function () {
					that._trigger("to" + $t.attr("id").toLowerCase(), null, {});
					that._trigger("to", null, {});
					that.options.onClickLink(path, $a);

				});
			});
		}		
		else {
			$t.siblings("div,form,iframe").hide();
			$t.show();
			this._trigger("to" + $t.attr("id").toLowerCase(), null, {});
			this.options.onClickLink(path, $a);
		}
	},
	_getPath: function ($t) {
		var m = $t.metadata({
			type: "attr",
			name: "data"
		});
		var p = [];
		if (m && m.path) {
			p = m.path.split("/");
		}
		return p;
	},
	_clickHandler: function (e, $t, $a) {
		var p = this._getPath($t);
		this._navigate($t, p, $a);
		if (e) {
			e.preventDefault();
		}
	},
	to: function (link, e) {
		var href, $target, me;
		if (e === undefined) {
			$target = arguments[0];
			href = "#" + $target.attr("id");
			me = null;
			e = null;
		}
		else {
			href = $(link).attr("href");
			// IE returns the URL as absolute firefox as relative
			var stringparts = href.split('#');
			href = "#" + stringparts[stringparts.length -1];
			$target = $(href);

		}



		var found = false;
		var abort = false;
		var wizardInstance = this;
		$target.parents().each(function () {
			if (abort || found) {
				return;
			}
			var $currentElement = $(this);
			// not this target's wizard
			if (!$currentElement.data("wizard")) {
				return;
			}
			if ($currentElement.data("wizard") !== wizardInstance) {
				abort = true;
				return;
			}				
			// this target's wizard
			found = true;
			return;
		});
		if (abort || !found) {
			new Mapbender.Warning("not this target's wizard, or not found, aborting");
			return;
		}

		var proceed = link === undefined ? true : this.options.onBeforeClickLink(e, $(link));
		if (proceed === false) {
			new Mapbender.Warning(
				"Clicked link " + href + " in wizard " + 
				wizardInstance.element.attr("id") + " aborted!"
			);
			return;
		}
		
		new Mapbender.Warning(
			"Clicked link " + href + " in wizard " + 
			wizardInstance.element.attr("id")
		);
		this._clickHandler(e, $target, $(this));
	},
	_create: function () {
		var wizardInstance = this;
		var $wiz = wizardInstance.element;
		$wiz.data("wizard", wizardInstance);

		// true = bad performance
		this.options.fade = false;

		if (this.options.startWith) {
			var path = this._getPath(this.options.startWith);
			this._trigger("to" + this.options.startWith.attr("id").toLowerCase(), null, {});
			this._trigger("to", null, {});
			this.options.onClickLink(path);			
		}

		$("a.wizard").live("click", function (e) {
			wizardInstance.to(this, e);
		});
	}
});
