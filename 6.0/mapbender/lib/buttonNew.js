
/**
 * A group of buttons. 
 */
function ButtonGroup (nodeId) {
	var buttonArray = [];
	var node = "#" + nodeId;
	
	this.add = function (button) {
		if (!button.constructor == "Button") {
			return false;
		}
		if (button.type == "toggle") {
			for (var i = 0; i < buttonArray.length; i++) {
				var currentButton = buttonArray[i];
				currentButton.registerPush(button.triggerStop);
				button.registerPush(currentButton.triggerStop);
			}
		}
		else if (button.type == "singular") {
			button.registerPush(button.triggerStop);	
		}
		buttonArray.push(button);
		$(node).append(button.getNode());
	}
}
	
	
function Button (options) {
	//
	// API
	//
	this.registerStart = function (func) {
		start.register(func);
	};
	
	this.registerStop = function (func) {
		stop.register(func);
	};
	
	this.registerPush = function (func) {
		push.register(func);
	};
	
	this.registerRelease = function (func) {
		release.register(func);
	};

	this.triggerStart = function () {
		start.trigger();
	};
	
	this.triggerStop = function () {
		stop.trigger();
	};

	this.triggerPush = function () {
		push.trigger();
	};
	
	this.triggerRelease = function () {
		release.trigger();
	};

	this.getNode = function () {
		return $node;
	};
	
	//
	// constructor
	//
	/**
	 * Is triggered if the button is pushed, may
	 * also be triggered by other actions.
	 * Changes the button image source.
	 */
	var start = new MapbenderEvent();
	/**
	 * Is triggered if the button is released, may
	 * also be triggered by other actions.
	 * Changes the button image source.
	 */
	var stop = new MapbenderEvent();
	/**
	 * Is only called after the button has been 
	 * manually pushed by the user.
	 * Triggers "start".
	 */
	var push = new MapbenderEvent();

	/**
	 * Is only called after the button has been 
	 * manually released by the user.
	 * Triggers "stop".
	 */
	var release = new MapbenderEvent();
	
	start.register(function() {
		isOn = true;
		$node.attr("src", srcOn);
	});
	
	stop.register(function() {
		$node.attr("src", srcOff);
		isOn = false;
	});
	
	push.register(function() {
		start.trigger();
	});
	
	release.register(function() {
		stop.trigger();
	});
	
	var srcOn = (options.on) ? options.on : null;
	var srcOff = (options.off) ? options.off : null;
	var srcOver = (options.over) ? options.over : null;
	this.type = (options.type) ? options.type : "default";
		 
	var $node = $("<img style='padding:5px' src='" + srcOff + "'/>");
	var isOn = false;
	
	$node.click(function() {
		(!isOn) ? push.trigger() : release.trigger();	
	});
}

