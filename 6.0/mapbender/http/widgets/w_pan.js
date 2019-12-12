$.widget("mapbender.pan", {
	active: false,
	_startPos: null,
	_stopPos: null,
	_map: null,
	_panStart: function (e) {

		this._startPos = this._map.getMousePosition(e);
		this._stopPos = new Point(this._startPos);
		this.active = true;
		this.element.children().eq(0)
			.bind("mouseup", $.proxy(this, "_panStop"))
			.bind("mousemove", $.proxy(this, "_panMove"))
			.css("cursor", "move");		
	},
	_panMove: function (e) {
		if (!this.active) {
			return false;
		}
		this._stopPos = this._map.getMousePosition(e);
		var dif = this._stopPos.minus(this._startPos);
		this._map.moveMap(dif.x, dif.y);
		return false;		
	},
	_panStop: function (e) {
		if (!this.active) {
			return false;
		}
		if (!this._map) {
			return false;
		}
		this.active = false;
		var dif = this._stopPos.minus(this._startPos);
		var widthHeight = new Mapbender.Point(
			this._map.getWidth(),
			this._map.getHeight()
		);
		var center = widthHeight.times(0.5).minus(dif);
		var realCenter = this._map.convertPixelToReal(center);   
		this._map.moveMap();
		this._map.zoom(false, 1.0, realCenter);   
		this.element.children().eq(0)
			.unbind("mousemove", this._panMove)
			.unbind("mouseup", this._panStop)
			.css("cursor", "default");		
		return false;		
	},
	_init: function () {
	},
	_create: function () {
		// ":maps" is a Mapbender selector which 
		// checks if an element is a Mapbender map
		this.element = this.element.filter(":maps");

		if (!this.element.jquery || this.element.size() === 0) {
			$.error("This widget must be applied to a Mapbender map.");
		}
		
		this._map = this.element.mapbender();

		this.element.children().eq(0)
			.bind("mousedown", $.proxy(this, "_panStart"));		

	},
	destroy: function () {
		this.children().eq(0)
			.unbind("mousedown", this._panStart)
			.unbind("mousemove", this._panMove)
			.unbind("mouseup", this._panStop)
			.css("cursor", "default");		

		$.Widget.prototype.destroy.apply(this, arguments); // default destroy
	}
});
