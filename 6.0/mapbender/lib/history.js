Mapbender.History = function () {
	var historyItemArray = [];
	var currentIndex = 0;

	this.events = {
		beforeAdd : new Mapbender.Event(),		
		beforeBack : new Mapbender.Event(),		
		beforeForward : new Mapbender.Event(),		
		afterBack : new Mapbender.Event(),		
		afterForward : new Mapbender.Event()
	};
	
	this.getCurrentIndex = function () {
		return currentIndex;	
	};
	
	this.count = function () {
		return historyItemArray.length;
	};
	this.addItem = function (obj) {
		if (typeof obj == "object" 
		&& obj.back && typeof obj.back === "function"
		&& obj.forward && typeof obj.forward === "function"
		) {
			this.events.beforeAdd.trigger();
			for (var i = currentIndex; i < historyItemArray.length; i++) {
				delete historyItemArray[i];
			}
			historyItemArray.length = currentIndex;
			historyItemArray.push({
				back: obj.back,
				forward: obj.forward,
				data: obj.data
			});
			return true;
		}
		return false;
	};
	
	this.back = function (obj) {
		if (currentIndex > 0) {
			this.events.beforeBack.trigger();
			currentIndex --;
			historyItemArray[currentIndex].back(obj);
			this.events.afterBack.trigger();
			return true;
		}
		return false;
	};
	
	this.forward = function (obj) {
		if (currentIndex < historyItemArray.length) {
			this.events.beforeForward.trigger();
			historyItemArray[currentIndex].forward(obj);
			currentIndex ++;
			this.events.afterForward.trigger();
			return true;
		}
		return false;
	};
};