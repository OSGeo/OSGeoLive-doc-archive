
/**
 * HTMLOptGroupElement - DOM Level 2
 */
HTMLOptGroupElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLOptGroupElement.prototype = new HTMLElement;
__extend__(HTMLOptGroupElement.prototype, {
    get disabled(){
        return this.getAttribute('disabled');
    },
    set disabled(value){
        this.setAttribute('disabled',value);
    },
    get label(){
        return this.getAttribute('label');
    },
    set label(value){
        this.setAttribute('label',value);
    },
});
	